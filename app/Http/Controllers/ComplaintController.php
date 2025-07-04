<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\ComplaintAction;
use App\Models\NetworkType;
use App\Models\Section;
use App\Models\Vertical;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['create', 'store', 'show', 'history', 'track', 'lookup']);
    }

    public function index()
    {
        $user = Auth::user();

        $query = Complaint::query()->with(['client', 'assignedTo', 'networkType', 'vertical', 'status']);

        $status = (array) request('status'); // Always cast to array

        if ($user) {
            if ($user->isManager()) {
                if (empty($status)) {
                    // Only restrict if no status filter is applied
                    $activeStatusIds = Status::whereIn('name', [
                        'unassigned',
                        'assigned',
                        'pending_with_vendor',
                        'pending_with_user',
                        'assign_to_me',
                        'completed'
                    ])->pluck('id');
                    $query->whereIn('status_id', $activeStatusIds);
                }
            } elseif ($user->isVM()) {
                $query->where('vertical_id', $user->vertical_id);
            } elseif ($user->isNFO()) {
                $query->where('assigned_to', $user->id);
            } else {
                $query->where('client_id', $user->id);
            }
        }

        // Search functionality
        if (request()->has('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('vertical', function ($v) use ($search) {
                        $v->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if (!empty($status)) {
            $assignToMeStatusId = \App\Models\Status::where('name', 'assign_to_me')->value('id');
            if (in_array('assign_to_me', $status) || ($assignToMeStatusId && in_array($assignToMeStatusId, $status))) {
                $query->where('assigned_to', auth()->user()->id);
            } else {
                $query->whereIn('status_id', $status);
            }
        }

        // Assign to me filter (from dashboard)
        if (request('assigned_to_me') == '1') {
            $query->where('assigned_to', $user->id);
        }

        $managers = User::whereHas('role', function ($q) {
            $q->where('slug', 'manager');
        })->get();
        $statuses = Status::active()->ordered()->get();

        $perPage = request('per_page', 10);
        if ($perPage === 'all') {
            $complaints = $query->latest()->get();
        } else {
            $complaints = $query->latest()->paginate((int) $perPage)->appends(request()->query());
        }

        foreach ($complaints as $complaint) {
            $complaint->assignableUsers = $user->getAssignableUsers($complaint);
        }

        return view('complaints.index', compact('complaints', 'managers', 'statuses', 'perPage'));
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

        // Get unassigned status
        $unassignedStatus = Status::where('name', 'unassigned')->first();

        // 🔢 Generate CMP-YYYYMMDD### reference number
        $date = Carbon::now()->format('Ymd');
        $complaintsToday = Complaint::whereDate('created_at', Carbon::today())->count();
        $referenceNumber = 'CMP-' . $date . str_pad($complaintsToday + 1, 3, '0', STR_PAD_LEFT);

        $complaint = Complaint::create([
            'reference_number' => $referenceNumber,
            'client_id' => Auth::user()->id ?? 0,
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status_id' => $unassignedStatus->id,
            'network_type_id' => $validated['network_type_id'],
            'vertical_id' => $validated['vertical_id'],
            'section_id' => $validated['section_id'],
            'user_name' => $validated['user_name'],
            'file_path' => $request->hasFile('file') ? $request->file('file')->store('complaint_files', 'public') : null,
            'intercom' => $validated['intercom'],
            'network_type' => NetworkType::find($validated['network_type_id'])->name,
            'vertical' => Vertical::find($validated['vertical_id'])->name,
            'section' => Section::find($validated['section_id'])->name,
            'created_at' => Carbon::now()->setTimezone(config('app.timezone')),
            'updated_at' => Carbon::now()->setTimezone(config('app.timezone')),
        ]);

        // Create initial action record
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' => Auth::user()->id ?? 0,
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
        $statuses = Status::active()->ordered()->get();

        $complaint->load(['client', 'assignedTo', 'status']);

        return view('complaints.edit', compact('complaint', 'networkTypes', 'verticals', 'sections', 'statuses'));
    }



    public function update(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);

        // If this is a close request (only status_id and description are present)
        if ($request->has('status_id') && $request->has('description') && count($request->all()) <= 5) { // 5: _method, _token, status_id, description, (optionally assigned_to)
            $validated = $request->validate([
                'status_id' => 'required|exists:statuses,id',
                'description' => 'required|string',
            ]);

            $complaint->update([
                'status_id' => $validated['status_id'],
            ]);

            // Create action record
            ComplaintAction::create([
                'complaint_id' => $complaint->id,
                'user_id' => Auth::user()->id ?? 0,
                'action' => 'closed',
                'description' => $validated['description'],
            ]);

            return redirect()->route('complaints.index')
                ->with('success', 'Complaint closed successfully.');
        }

        // Default: full update (edit page)
        $validated = $request->validate([
            'network_type_id' => 'required|exists:network_types,id',
            'description' => 'required|string',
            'vertical_id' => 'required|exists:verticals,id',
            'user_name' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
            'intercom' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'status_id' => 'required|exists:statuses,id',
            'file' => 'nullable|file|max:2048',
            'delete_file' => 'sometimes|boolean',
            'assigned_to' => 'nullable|exists:users,id',
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

        // Check if assigned_to is being changed
        if (isset($validated['assigned_to']) && $validated['assigned_to'] != $complaint->assigned_to) {
            $validated['assigned_by'] = Auth::user()->id ?? 0;
        }

        $complaint->update($validated);

        // Create action record
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' => Auth::user()->id ?? 0,
            'action' => 'updated',
            'description' => 'Complaint updated',
            'changes' => json_encode($complaint->getChanges())
        ]);

        return redirect()->route('complaints.index') // or another existing route
            ->with('success', 'Complaint updated successfully.');
    }



    public function assign(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

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

        // Get assigned status
        $assignedStatus = Status::where('name', 'assigned')->first();

        $complaint->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => Auth::user()->id ?? 0,
            'status_id' => $assignedStatus->id
        ]);

        // Create action record
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'assigned_to' => $validated['assigned_to'],
            'action' => 'assigned',
            'description' => $validated['description']
        ]);

        return redirect()->route('complaints.index') // or another existing route
            ->with('success', 'Complaint updated successfully.');
    }

    public function resolve(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        if (!$user->isNFO()) {
            abort(403, 'Only NFOs can resolve complaints.');
        }

        if ($complaint->assigned_to !== $user->id) {
            abort(403, 'You can only resolve complaints assigned to you.');
        }

        $validated = $request->validate([
            'description' => 'required|string',
            'status_id' => 'nullable|exists:statuses,id',
            'mark_closed' => 'nullable|boolean'
        ]);

        // Determine final status
        if ($request->has('mark_closed')) {
            $finalStatus = Status::where('name', 'closed')->first();
        } else {
            $finalStatus = Status::find($validated['status_id']);
        }

        $complaint->update([
            'status_id' => $finalStatus->id,
            'resolution' => $validated['description'], // ✅ always store what user typed
        ]);

        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'action' => $finalStatus->name,
            'description' => $validated['description']
        ]);

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Complaint ' . $finalStatus->name . ' successfully.');
    }


    public function revert(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        if (!$user->isVM()) {
            abort(403, 'Only VMs can revert complaints to managers.');
        }

        if ($complaint->assigned_to !== $user->id) {
            abort(403, 'You can only revert complaints assigned to you.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'description' => 'required|string'
        ]);

        // Get reverted status
        $revertedStatus = Status::where('name', 'assign_to_me')->first();

        $complaint->update([
            'assigned_to' => $validated['assigned_to'],
            'status_id' => $revertedStatus->id,
            'assigned_by' => $user->id
        ]);

        // Create action record
        ComplaintAction::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'assigned_to' => $validated['assigned_to'],
            'action' => 'reverted',
            'description' => $validated['description']
        ]);

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Complaint reverted to manager successfully.');
    }

    public function getAssignableUsers(Request $request)
    {
        $user = Auth::user();
        $complaint = null;

        if ($request->has('complaint_id')) {
            $complaint = Complaint::find($request->complaint_id);
        }

        $assignableUsers = $user->getAssignableUsers($complaint); // Pass complaint

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
        $complaint->load(['client', 'assignedTo', 'actions.user', 'networkType', 'vertical', 'section', 'status']);

        // Statuses for assigned user to update
        $statusOptions = \App\Models\Status::whereIn('name', [
            'pending_with_vendor',
            'pending_with_user',
            'in_progress',
            'completed'
        ])->ordered()->get();

        // Show close/assign for manager (or VM if assigned to NFO) when status is completed
        $showCloseOrAssign = false;
        $user = Auth::user();
        if ($complaint->isCompleted()) {
            if (($user && $user->isManager()) || ($user && $user->isVM() && $complaint->assignedTo && $complaint->assignedTo->isNFO())) {
                $showCloseOrAssign = true;
            }
        }
        $closeStatus = \App\Models\Status::where('name', 'closed')->first();

        return view('complaints.show', compact('complaint', 'statusOptions', 'showCloseOrAssign', 'closeStatus'));
    }

    public function comment(Request $request, Complaint $complaint)
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
            'status_id' => 'nullable|exists:statuses,id',
        ]);

        // Add comment
        $complaint->comments()->create([
            'user_id' => Auth::user()->id ?? 0,
            'comment' => $request->comment,
        ]);

        // If status_id is present and different, update status and add to history
        if ($request->filled('status_id') && $complaint->status_id != $request->status_id) {
            $oldStatus = $complaint->status_id;
            $complaint->update(['status_id' => $request->status_id]);

            // Create action/history
            \App\Models\ComplaintAction::create([
                'complaint_id' => $complaint->id,
                'user_id' => Auth::user()->id ?? 0,
                'action' => \App\Models\Status::find($request->status_id)->name,
                'description' => $request->comment,
            ]);
        }

        return redirect()->back()->with('success', 'Comment added successfully.');
    }

    /**
     * Track a complaint by reference number (for guests).
     */
    public function track(Request $request)
    {
        $ref = $request->input('reference_number');
        $complaint = \App\Models\Complaint::where('reference_number', $ref)->first();
        if ($complaint) {
            return redirect()->route('complaints.show', $complaint->id);
        } else {
            return back()->with('error', 'Complaint not found');
        }
    }

    /**
     * AJAX lookup for complaint by reference number.
     */
    public function lookup(Request $request)
    {
        $ref = $request->input('reference_number');
        \Log::info('Looking up complaint', ['ref' => $ref]);
        $complaint = \App\Models\Complaint::with(['client', 'networkType', 'vertical', 'status'])
            ->whereRaw('LOWER(reference_number) = ?', [strtolower($ref)])
            ->first();
        if ($complaint) {
            return response()->json([
                'success' => true,
                'complaint' => [
                    'reference_number' => $complaint->reference_number,
                    'name' => $complaint->client?->full_name ?? $complaint->user_name ?? 'Guest',
                    'intercom' => $complaint->intercom,
                    'status' => $complaint->status?->display_name ?? 'Unknown',
                    'status_color' => $complaint->status_color,
                    'priority' => ucfirst($complaint->priority),
                    'priority_color' => $complaint->priority_color,
                    'created_at' => $complaint->created_at->format('M d, Y H:i'),
                    'description' => $complaint->description,
                    'network' => $complaint->networkType?->name ?? 'N/A',
                    'section' => $complaint->section?->name ?? 'N/A',
                    'vertical' => $complaint->vertical?->name ?? 'N/A',
                ]
            ]);
        } else {
            return response()->json(['success' => false, 'error' => 'Complaint not found.'], 404);
        }
    }
}
