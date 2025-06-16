<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\ComplaintAction;
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
        $query = Complaint::query();

        if ($user) {
            if ($user->isManager()) {
                // Managers can see all complaints
                $query->whereIn('status', ['pending', 'assigned', 'in_progress']);
            } elseif ($user->isVM()) {
                // VMs can see all complaints
                // No additional where clause needed
            } elseif ($user->isNFO()) {
                // NFOs can see complaints assigned to them
                $query->where('assigned_to', $user->id);
            } else {
                // Regular users can only see their own complaints
                $query->where('client_id', $user->id);
            }
        }

        $complaints = $query->with(['client', 'assignedTo'])
            ->latest()
            ->paginate(10);

        return view('complaints.index', compact('complaints'));
    }

    public function create()
    {
        return view('complaints.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'network_type' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'description' => 'required|string',
            'vertical' => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'file' => 'nullable|file|max:2048', // 2MB max
            'section' => 'required|string|max:255',
            'intercom' => 'required|string|max:255',
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('complaint_files');
        }

        $complaint = Complaint::create([
            'reference_number' => 'CMP-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'client_id' => auth()->user()->id ?? 0,
            'network_type' => $validated['network_type'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'vertical' => $validated['vertical'],
            'user_name' => $validated['user_name'],
            'file_path' => $filePath,
            'section' => $validated['section'],
            'intercom' => $validated['intercom'],
            'status' => 'pending'
        ]);

        // Create initial action record
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' => auth()->user()->id ?? 0,
            'action' => 'created',
            'description' => 'Complaint created'
        ]);

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Complaint created successfully.');
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['client', 'assignedTo', 'actions.user']);
        return view('complaints.show', compact('complaint'));
    }

    public function edit(Complaint $complaint)
    {
        $this->authorize('update', $complaint);
        $complaint->load(['client', 'assignedTo']);
        return view('complaints.edit', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        \Log::info("Update method triggered for complaint #{$complaint->id}");
        \Log::info("Request data:", $request->all());

        $this->authorize('update', $complaint);

        $validated = $request->validate([
            'network_type' => 'required|string|max:255',
            'description' => 'required|string',
            'vertical' => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'intercom' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,assigned,in_progress,resolved,closed',
            'file' => 'nullable|file|max:2048'
        ]);


        // Handle file upload if new file is provided
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($complaint->file_path) {
                Storage::delete($complaint->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('complaint_files');
        }
        $complaint->update($validated);

        // Create action record
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' =>  auth()->user()->id ?? 0,
            'action' => 'updated',
            'description' => 'Complaint updated'
        ]);

        return redirect()->route('complaints.show', $complaint)
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

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Complaint assigned successfully.');
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
}
