@extends('layouts.app')

@section('content')
@include('layouts.breadcrumbs', [
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Tickets', 'url' => null],
    ]
])
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Tickets</h2>
            {{-- <a href="{{ route('complaints.create') }}" class="btn btn-primary">Create New Ticket</a> --}}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Tickets</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <form method="GET" action="{{ route('complaints.index') }}" class="mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                {{-- <label for="per_page" class="form-label mb-0">Show</label> --}}
                                <select name="per_page" id="per_page" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                                </select>
                                {{-- <span class="text-muted ms-1">entries per page</span> --}}
                            </div>
                            <div class="col-auto">
                                <select name="status[]" id="status-filter" class="form-select" multiple onchange="this.form.submit()">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" {{ collect(request('status'))->contains($status->id) ? 'selected' : '' }}>{{ $status->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col ms-auto">
                                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}" onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>
                    <table id="complaintsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Network</th>
                                <th>Vertical</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created By</th>
                                <th>Assigned To</th>
                                <th>Assigned By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($complaints as $complaint)
                            <tr>
                                <td>{{ $complaint->reference_number }}</td>
                                <td>{{ $complaint->networkType->name ?? 'N/A' }}</td>
                                <td>{{ $complaint->vertical->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $complaint->status_color }}">
                                        {{ $complaint->status->display_name ?? 'Unknown' }}
                                    </span>
                                </td>
                               <td>
                                    <span class="badge bg-{{ $complaint->priority_color }}">
                                        {{ ucfirst($complaint->priority) }}
                                    </span>
                                </td>

                                <td>{{ $complaint->client?->full_name ?? 'Guest User' }}</td>
                                <td>{{ $complaint->assignedTo?->full_name ?? 'Not Assigned' }}</td>
                                <td>
                                    @php $assignedBy = $complaint->assigned_by ? \App\Models\User::find($complaint->assigned_by) : null; @endphp
                                    {{ $assignedBy?->full_name ?? 'N/A' }}
                                </td>
                                <td>{{ $complaint->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-info">View</a>

                                        @auth
                                        @if(auth()->user()->isManager())
                                        {{-- @if($complaint->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#assignModal{{ $complaint->id }}">
                                        Assign
                                        </button>
                                        @endif --}}

                                        @if(auth()->user()->isManager())
                                        @if($complaint->status->name != 'completed' && $complaint->status->name != 'closed')
                                        <button type="button" class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#assignModal{{ $complaint->id }}">
                                            @if($complaint->assigned_to)
                                            Reassign
                                            @else
                                            Assign
                                            @endif
                                        </button>
                                        @endif
                                        @endif

                                        @elseif(auth()->user()->isVM())
                                            @if(($complaint->isUnassigned() || $complaint->assigned_to === auth()->user()->id) && $complaint->status->name != 'completed' && $complaint->status->name != 'closed')
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#assignModal{{ $complaint->id }}">
                                                    Assign
                                                </button>
                                                @if($complaint->assigned_to === auth()->user()->id && $complaint->status->name != 'completed' && $complaint->status->name != 'closed')
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#revertModal{{ $complaint->id }}">
                                                        Revert
                                                    </button>
                                                @endif
                                            @endif
                                            @elseif(auth()->user()->isNFO())
                                                @if($complaint->assigned_to === auth()->user()->id)
                                                    @if($complaint->assigned_to === auth()->user()->id && !$complaint->isClosed())
                                                        {{--
                                                        @if($complaint->assigned_to === auth()->user()->id && !$complaint->isClosed())
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#resolveModal{{ $complaint->id }}">
                                                                Resolve
                                                            </button>
                                                        @endif
                                                        --}}
                                                    @endif

                                                    @if($complaint->assigned_to === auth()->user()->id && !$complaint->isCompleted() && !$complaint->isClosed())
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#assignModal{{ $complaint->id }}">
                                                            Reassign
                                                        </button>
                                                    @endif

                                                @endif
                                            @endif
                                        @endauth
                                    </div>

                                    <!-- Assign Modal -->
                                    <div class="modal fade" id="assignModal{{ $complaint->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('complaints.assign', $complaint) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Assign Complaint</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="assigned_to" class="form-label">Assign To</label>
                                                            <select class="form-select" name="assigned_to" required>
                                                                <option value="">Select User</option>
                                                                @foreach($complaint->assignableUsers as $user)
                                                                    <option value="{{ $user->id }}">{{ $user->full_name }} ({{ strtoupper($user->role->name) }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="description" class="form-label">Remarks</label>
                                                            <textarea class="form-control" name="description" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Assign</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Resolve Modal -->
                                    <div class="modal fade" id="resolveModal{{ $complaint->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('complaints.resolve', $complaint) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Resolve Ticket</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" type="checkbox" value="1" id="markClosed{{ $complaint->id }}" name="mark_closed">
                                                            <label class="form-check-label" for="markClosed{{ $complaint->id }}">
                                                                Mark as Closed
                                                            </label>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="status_id" class="form-label">Status *</label>
                                                            <select class="form-select @error('status_id') is-invalid @enderror"
                                                                id="statusSelect{{ $complaint->id }}" name="status_id" required>
                                                                @foreach($statuses as $status)
                                                                    <option value="{{ $status->id }}" {{ old('status_id', $complaint->status_id) == $status->id ? 'selected' : '' }}>
                                                                        {{ $status->display_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('status_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="description" class="form-label">Remarks / Solution *</label>
                                                            <textarea class="form-control" name="description" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Resolve</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Revert Modal -->
                                    <div class="modal fade" id="revertModal{{ $complaint->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('complaints.revert', $complaint) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Revert to Manager</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="assigned_to" class="form-label">Revert to Manager</label>
                                                            <select class="form-select" name="assigned_to" required>
                                                                <option value="">Select Manager</option>
                                                                @foreach($managers as $manager)
                                                                <option value="{{ $manager->id }}" @if($manager->id == $complaint->assigned_by) selected @endif>
                                                                    {{ $manager->full_name }}@if($manager->id == $complaint->assigned_by) (Original Assigner)@endif
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="description" class="form-label">Reason for Reverting</label>
                                                            <textarea class="form-control" name="description" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning">Revert</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No Ticket found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $complaints instanceof \Illuminate\Pagination\LengthAwarePaginator ? $complaints->links('vendor.pagination.custom') : '' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Tom Select for status filter
    const statusSelect = new TomSelect('#status-filter', {
        plugins: ['remove_button'],
        placeholder: 'Filter by Status',
        persist: false,
        create: false
    });

    // Handle status parameter from dashboard links
    const urlParams = new URLSearchParams(window.location.search);
    const statusParam = urlParams.get('status');
    const assignedToMeParam = urlParams.get('assigned_to_me');
    
    if (statusParam && statusParam !== '') {
        // Set the status filter value
        statusSelect.setValue(statusParam);
        
        // Update the URL to use the proper status[] format for the form
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('status');
        newUrl.searchParams.append('status[]', statusParam);
        
        // Replace the URL without reloading the page
        window.history.replaceState({}, '', newUrl);
    }

    // Handle assigned_to_me parameter from dashboard
    if (assignedToMeParam === '1') {
        // Try to select 'assign_to_me' in the status filter (by string or by id)
        let found = false;
        // Try string value first
        if (statusSelect.options['assign_to_me']) {
            statusSelect.setValue('assign_to_me');
            found = true;
        } else {
            // Try to find the option with display name 'Assign To Me' and select its value
            const selectEl = document.getElementById('status-filter');
            if (selectEl) {
                for (let i = 0; i < selectEl.options.length; i++) {
                    if (selectEl.options[i].text.toLowerCase().includes('assign to me')) {
                        statusSelect.setValue(selectEl.options[i].value);
                        found = true;
                        break;
                    }
                }
            }
        }
        // Add a visual indicator that we're showing "Assigned to Me" complaints
        const cardHeader = document.querySelector('.card-header');
        if (cardHeader && found) {
            const indicator = document.createElement('div');
            indicator.className = 'alert alert-info alert-sm mb-0';
            indicator.innerHTML = '<i class="bi bi-person-check"></i> Showing complaints assigned to you';
            cardHeader.appendChild(indicator);
        }
    }
});
</script>
@endpush