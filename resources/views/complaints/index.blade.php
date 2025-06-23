@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Tickets</h2>
            <a href="{{ route('complaints.create') }}" class="btn btn-primary">Create New Ticket</a>
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
                <!-- Search and Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('complaints.index') }}" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search by reference or description..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-outline-primary">Search</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('complaints.index') }}" class="d-flex">
                            <select name="status" class="form-select me-2">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                        {{ $status->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-secondary">Filter</button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <!-- <th>Subject</th> -->
                                <th>Network</th>
                                <th>Vertical</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created By</th>
                                <th>Assigned To</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($complaints as $complaint)
                            <tr>
                                <td>{{ $complaint->reference_number }}</td>
                                <!-- <td>{{ $complaint->subject }}</td> -->

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
                                <td>
                                    @if($complaint->assignedTo)
                                    {{ $complaint->assignedTo->full_name }}
                                    @else
                                    <span class="text-muted">Not Assigned</span>
                                    @endif
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

                                        @elseif(auth()->user()->isVM())
                                            @if($complaint->isPending() || $complaint->assigned_to === auth()->user()->id)
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#assignModal{{ $complaint->id }}">
                                                    Assign
                                                </button>
                                                @if($complaint->assigned_to === auth()->user()->id)
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
                                                        <button type="button" class="btn btn-sm btn-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#resolveModal{{ $complaint->id }}">
                                                            Resolve
                                                        </button>
                                                    @endif

                                                    @if($complaint->assigned_to === auth()->user()->id && !$complaint->isClosed())
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

                                                        <!-- Closed Checkbox -->
                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" type="checkbox" value="1" id="markClosed{{ $complaint->id }}" name="mark_closed">
                                                            <label class="form-check-label" for="markClosed{{ $complaint->id }}">
                                                                Mark as Closed
                                                            </label>
                                                        </div>

                                                        <!-- Status Dropdown -->
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

                                                        <!-- Remarks Textarea -->
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
                                                                <option value="{{ $manager->id }}"
                                                                    @if($manager->id == $complaint->assigned_by) selected @endif>
                                                                    {{ $manager->full_name }}
                                                                    @if($manager->id == $complaint->assigned_by) (Original Assigner) @endif
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
                                <td colspan="8" class="text-center">No tickets found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $complaints->links('vendor.pagination.custom') }} <!-- Your custom view -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to fetch assignable users
        async function fetchAssignableUsers(complaintId) {
            try {
                const response = await fetch(`/api/assignable-users?complaint_id=${complaintId}`);
                const users = await response.json();

                const select = document.querySelector(`#assignModal${complaintId} select[name="assigned_to"]`);
                select.innerHTML = '<option value="">Select User</option>';

                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.username} (${user.role.name.toUpperCase()})`;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error fetching assignable users:', error);
            }
        }

        // Add event listeners to all assign modals
        document.querySelectorAll('[id^="assignModal"]').forEach(modal => {
            const complaintId = modal.id.replace('assignModal', '');
            modal.addEventListener('show.bs.modal', () => {
                fetchAssignableUsers(complaintId);
            });
        });

        // Add event listeners to all resolve modals for the checkbox functionality
        document.querySelectorAll('[id^="resolveModal"]').forEach(modal => {
            const complaintId = modal.id.replace('resolveModal', '');
            const checkbox = modal.querySelector(`#markClosed${complaintId}`);
            const statusSelect = modal.querySelector(`#statusSelect${complaintId}`);

            if (checkbox && statusSelect) {
                checkbox.addEventListener('change', function() {
                    statusSelect.disabled = this.checked;
                    if (this.checked) {
                        // Optional: clear the selection or set to a default
                        // statusSelect.selectedIndex = 0; 
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection