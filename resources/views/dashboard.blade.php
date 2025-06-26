@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Dashboard</h4>
                    <div>
                        @if(auth()->user()->isManager() || auth()->user()->isVM())
                        <a href="{{ route('complaints.index') }}" class="btn btn-primary">View All Complaints</a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <!-- Welcome Message -->
                    <div class="alert alert-info">
                        Welcome back, {{ auth()->user()->name }}!
                        @if(auth()->user()->isManager())
                        As a Manager, you can view and assign all complaints.
                        @elseif(auth()->user()->isVM())
                        As a Vendor Manager, you can self-assign complaints and assign them to NFOs.
                        @elseif(auth()->user()->isNFO())
                        As a Network Field Officer, you can resolve complaints and reassign them.
                        @endif
                    </div>

                    <!-- Statistics (from controller variables) -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Total Complaints</h5>
                                    <h2 class="mb-0">{{ $totalComplaints }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Unassigned</h5>
                                    <h2 class="mb-0">{{ $unassignedComplaints }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Completed</h5>
                                    <h2 class="mb-0">{{ $completedComplaints }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Assign to Me</h5>
                                    <h2 class="mb-0">{{ $assignToMeComplaints }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Complaints -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Today's Complaints</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
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
                                                @forelse($todayComplaints as $complaint)
                                                    <tr>
                                                        <td>{{ $complaint->reference_number }}</td>
                                                        <td>{{ $complaint->networkType?->name ?? 'N/A' }}</td>
                                                        <td>{{ $complaint->vertical?->name ?? 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $complaint->status_color }}">
                                                                {{ $complaint->status?->name ?? 'Unknown' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $complaint->priority_color ?? 'secondary' }}">
                                                                {{ $complaint->priority ?? 'Unknown' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $complaint->client_id === 0 ? 'client' : ($complaint->client?->name ?? 'N/A') }}</td>
                                                        <td>{{ $complaint->assignedTo?->full_name ?? 'Not Assigned' }}</td>
                                                        <td>
                                                            @php $assignedBy = $complaint->assigned_by ? \App\Models\User::find($complaint->assigned_by) : null; @endphp
                                                            {{ $assignedBy?->full_name ?? 'N/A' }}
                                                        </td>
                                                        <td>{{ $complaint->created_at->format('Y-m-d H:i') }}</td>
                                                        <td>
                                                            <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-primary">View</a>
                                                            @auth
                                                                @if((auth()->user()->isManager() || auth()->user()->isVM()) && (!$complaint->assigned_to || $complaint->assigned_to == 0))
                                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal{{ $complaint->id }}">
                                                                        Assign
                                                                    </button>
                                                                @endif
                                                            @endauth
                                                        </td>
                                                    </tr>
                                                    @if((auth()->user()->isManager() || auth()->user()->isVM()) && (!$complaint->assigned_to || $complaint->assigned_to == 0))
                                                    <div class="modal fade" id="assignModal{{ $complaint->id }}" tabindex="-1" aria-labelledby="assignModalLabel{{ $complaint->id }}" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="{{ route('complaints.assign', $complaint) }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="assignModalLabel{{ $complaint->id }}">Assign Ticket {{ $complaint->reference_number }}</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label for="assigned_to{{ $complaint->id }}" class="form-label">Assign To</label>
                                                                            <select class="form-select" name="assigned_to" id="assigned_to{{ $complaint->id }}" required>
                                                                                <option value="">Select User</option>
                                                                                @foreach($complaint->assignableUsers as $user)
                                                                                    <option value="{{ $user->id }}">{{ $user->full_name }} ({{ strtoupper($user->role->name) }})</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="description{{ $complaint->id }}" class="form-label">Remarks</label>
                                                                            <textarea class="form-control" name="description" id="description{{ $complaint->id }}" rows="3" required></textarea>
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
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center">No complaints today.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/buttons.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/responsive.bootstrap5.min.css') }}">
@endsection

@section('scripts')
{{-- <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script> --}}  <!-- Removed duplicate jQuery -->
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
    $('#complaintsTable').DataTable({
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
                className: 'btn btn-sm btn-outline-primary'
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-sm btn-outline-success'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-outline-success'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-outline-danger'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-sm btn-outline-secondary'
            }
        ]
    });
});
</script>
@endsection

@stack('scripts')
</body>
</html>