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
                <div class="row mb-3 align-items-center">
                    <div class="col-md-8">
                        <form method="GET" action="{{ route('complaints.index') }}" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Search by reference or description..." value="{{ request('search') }}">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                        {{ $status->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-primary">Search</button>
                        </form>
                    </div>
                    <div class="col-md-4 d-flex justify-content-end">
                        <div id="dtExportButtons"></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="complaintsTable" class="table table-hover">
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
                                <th>Assigned By</th>
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
                                <td colspan="10" class="text-center">No Ticket found.</td>
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
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/responsive.bootstrap5.min.js') }}"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('js/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ asset('js/jszip.min.js') }}"></script>
<script src="{{ asset('js/pdfmake.min.js') }}"></script>
<script src="{{ asset('js/vfs_fonts.js') }}"></script>
<script src="{{ asset('js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('js/buttons.print.min.js') }}"></script>
<script>
$(document).ready(function() {
    var table = $('#complaintsTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn btn-sm btn-outline-primary me-1'
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-sm btn-outline-success me-1'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-outline-success me-1'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-outline-danger me-1'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-sm btn-outline-secondary'
            }
        ]
    });
    // Move DataTable buttons to custom div
    table.buttons().container().appendTo('#dtExportButtons');
});
</script>
@endpush
@endsection