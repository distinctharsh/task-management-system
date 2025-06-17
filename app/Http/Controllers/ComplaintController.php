<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\ComplaintAction;
use App\Models\NetworkType;
use App\Models\Section;
use App\Models\Vertical;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    protected $statuses = [
        'pending' => 'Pending',
        'assigned' => 'Assigned',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'escalated' => 'Escalated',
        'closed' => 'Closed'
    ];

    public function __construct()
    {
        $this->middleware('auth')->except(['create', 'store', 'show']);
    }

    public function index()
    {
        $user = auth()->user();
        $query = Complaint::query()->with(['client', 'assignedTo', 'networkType', 'vertical']); // Added more relationships

        if ($user) {
            if ($user->isManager()) {
                $query->whereIn('status', ['pending', 'assigned', 'in_progress']);
            } elseif ($user->isVM()) {
                // VMs see all complaints - no filter needed
            } elseif ($user->isNFO()) {
                $query->where('assigned_to', $user->id)
                    ->orWhere('status', 'pending'); // Allow NFOs to see pending tickets they can claim
            } else {
                $query->where('client_id', $user->id);
            }
        }

        // Add search/sort functionality
        if (request()->has('search')) {
            $query->where('reference_number', 'like', '%' . request('search') . '%')
                ->orWhere('description', 'like', '%' . request('search') . '%');
        }

        if (request()->has('status')) {
            $query->where('status', request('status'));
        }

        $complaints = $query->latest()->paginate(10);

        return view('complaints.index', compact('complaints'));
    }

    public function create()
    {
        $networkTypes = NetworkType::all();
        $verticals = Vertical::all();
        $sections = Section::all();

        return view('complaints.create', compact('networkTypes', 'verticals', 'sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'network_type_id' => 'required|exists:network_types,id',
            'priority' => 'required|in:low,medium,high',
            'description' => 'required|string',
            'vertical_id' => 'required|exists:verticals,id',
            'user_name' => 'required|string|max:255',
            'file' => 'nullable|file|max:2048',
            'section_id' => 'required|exists:sections,id',
            'intercom' => 'required|string|max:255',
        ]);

        $complaint = Complaint::create([
            'reference_number' => 'CMP-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'client_id' => auth()->user()->id ?? 0,
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'pending',
            'network_type_id' => $validated['network_type_id'],
            'vertical_id' => $validated['vertical_id'],
            'section_id' => $validated['section_id'],
            'user_name' => $validated['user_name'],
            'file_path' => $request->hasFile('file') ? $request->file('file')->store('complaint_files', 'public') : null,
            'intercom' => $validated['intercom'],
            'network_type' => NetworkType::find($validated['network_type_id'])->name,
            'vertical' => Vertical::find($validated['vertical_id'])->name,
            'section' => Section::find($validated['section_id'])->name
        ]);

        // Create initial action record
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' => auth()->user()->id ?? 0,
            'action' => 'created',
            'description' => 'Complaint created',
            'changes' => json_encode($complaint->getChanges())
        ]);

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Complaint created successfully.');
    }

    public function edit(Complaint $complaint)
    {
        $this->authorize('update', $complaint);

        $networkTypes = NetworkType::all();
        $verticals = Vertical::all();
        $sections = Section::all();

        $complaint->load(['client', 'assignedTo']);

        return view('complaints.edit', compact('complaint', 'networkTypes', 'verticals', 'sections'));
    }



    public function update(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);

        $validated = $request->validate([
            'network_type_id' => 'required|exists:network_types,id',
            'description' => 'required|string',
            'vertical_id' => 'required|exists:verticals,id',
            'user_name' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
            'intercom' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,assigned,in_progress,resolved,closed',
            'file' => 'nullable|file|max:2048',
            'delete_file' => 'sometimes|boolean'
        ]);

        // Handle file deletion
        if ($request->input('delete_file') && $complaint->file_path) {
            Storage::delete($complaint->file_path);
            $validated['file_path'] = null;
        }

        // Handle file upload if new file is provided
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($complaint->file_path) {
                Storage::delete($complaint->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('complaint_files', 'public');
        }

        $complaint->update($validated);

        // Create action record
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' => auth()->user()->id ?? 0,
            'action' => 'updated',
            'description' => 'Complaint updated',
            'changes' => json_encode($complaint->getChanges())
        ]);

        return redirect()->route('complaints.index') // or another existing route
            ->with('success', 'Complaint updated successfully.');
    }



    public function assign(Request $request, Complaint $complaint)
    {
        $user = auth()->user();

        if (!$user->isManager() && !$user->isVM() && !$user->isNFO()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'description' => 'required|string'
        ]);

        $assignee = User::findOrFail($validated['assigned_to']);

        // Check if the assignee has the correct role based on the current user's role
        if ($user->isManager()) {
            if (!$assignee->isVM() && !$assignee->isNFO()) {
                abort(403, 'Managers can only assign to VMs or NFOs.');
            }
        } elseif ($user->isVM()) {
            if (!$assignee->isNFO() && $assignee->id !== $user->id) {
                abort(403, 'VMs can only self-assign or assign to NFOs.');
            }
        } elseif ($user->isNFO()) {
            if (!$assignee->isNFO() && !$assignee->isVM()) {
                abort(403, 'NFOs can only assign to other NFOs or VMs.');
            }
        }

        $complaint->update([
            'assigned_to' => $validated['assigned_to'],
            'status' => 'assigned'
        ]);

        // Create action record
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'action' => 'assigned',
            'description' => $validated['description']
        ]);

        return redirect()->route('complaints.index') // or another existing route
            ->with('success', 'Complaint updated successfully.');
    }

    public function resolve(Request $request, Complaint $complaint)
    {
        $user = auth()->user();

        if (!$user->isNFO()) {
            abort(403, 'Only NFOs can resolve complaints.');
        }

        if ($complaint->assigned_to !== $user->id) {
            abort(403, 'You can only resolve complaints assigned to you.');
        }

        $validated = $request->validate([
            'resolution' => 'required|string',
            'description' => 'required|string'
        ]);

        $complaint->update([
            'status' => 'resolved',
            'resolution' => $validated['resolution']
        ]);

        // Create action record
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'action' => 'resolved',
            'description' => $validated['description']
        ]);

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Complaint resolved successfully.');
    }

    public function revert(Request $request, Complaint $complaint)
    {
        $user = auth()->user();

        if (!$user->isVM()) {
            abort(403, 'Only VMs can revert complaints to managers.');
        }

        if ($complaint->assigned_to !== $user->id) {
            abort(403, 'You can only revert complaints assigned to you.');
        }

        $validated = $request->validate([
            'description' => 'required|string'
        ]);

        $complaint->update([
            'assigned_to' => null,
            'status' => 'pending'
        ]);

        // Create action record
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'action' => 'reverted',
            'description' => $validated['description']
        ]);

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Complaint reverted to manager successfully.');
    }

    public function getAssignableUsers(Request $request)
    {
        $user = auth()->user();
        $complaint = null;

        if ($request->has('complaint_id')) {
            $complaint = Complaint::find($request->complaint_id);
        }

        $assignableUsers = $user->getAssignableUsers();

        return response()->json($assignableUsers);
    }

    public function history(Request $request)
    {
        $query = \App\Models\Complaint::with(['actions' => function ($q) {
            $q->latest()->limit(1);
        }, 'actions.user']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('reference_number', 'like', "%$search%");
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->whereHas('actions', function ($q) use ($request) {
                $q->where('action', $request->action);
            });
        }

        // Filter by user
        if ($request->filled('by')) {
            $query->whereHas('actions.user', function ($q) use ($request) {
                $q->where('username', $request->by);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereHas('actions', function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from);
            });
        }
        if ($request->filled('date_to')) {
            $query->whereHas('actions', function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->date_to);
            });
        }

        $complaints = $query->latest()->paginate(10)->withQueryString();

        // For filter dropdowns
        $actionsList = \App\Models\ComplaintAction::select('action')->distinct()->pluck('action');
        $usersList = \App\Models\User::select('username')->distinct()->pluck('username');

        return view('complaints.history', compact('complaints', 'actionsList', 'usersList'));
    }


    public function show(Complaint $complaint)
    {
        $complaint->load(['client', 'assignedTo', 'actions.user', 'networkType', 'vertical', 'section']);

        return view('complaints.show', compact('complaint'));
    }
}
