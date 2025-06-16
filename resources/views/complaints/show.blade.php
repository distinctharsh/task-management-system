@extends('layouts.app')

@section('content')
<div class="container-xxl">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold mb-1">
                Ticket: <span class="text-primary">{{ $complaint->reference_number }}</span>
                <span class="badge bg-{{ $complaint->status_color }} ms-2">{{ ucfirst($complaint->status) }}</span>
            </h2>
            <div class="mb-2">
                <span class="badge bg-secondary me-2">Priority:
                    @php
                        $priorityColors = [
                            'high' => 'danger',
                            'medium' => 'warning',
                            'low' => 'success'
                        ];
                        $priority = strtolower($complaint->priority);
                    @endphp
                    <span class="badge bg-{{ $priorityColors[$priority] ?? 'secondary' }} ms-1">{{ ucfirst($complaint->priority) }}</span>
                </span>
                <span class="badge bg-info me-2">Subject: {{ $complaint->subject }}</span>
                <span class="badge bg-light text-dark">Location: {{ $complaint->location }}</span>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary me-2 mb-2">Back to List</a>
            @auth
                @if(auth()->user()->isManager() || auth()->user()->isVM())
                    @if($complaint->status === 'pending')
                        <button type="button" class="btn btn-primary me-2 mb-2" data-bs-toggle="modal" data-bs-target="#assignModal">
                            <i class="bi bi-person-plus"></i> Assign
                        </button>
                    @endif
                @endif
                @if(auth()->user()->isVM() && $complaint->assigned_to === auth()->id())
                    <button type="button" class="btn btn-warning me-2 mb-2" data-bs-toggle="modal" data-bs-target="#revertModal">
                        <i class="bi bi-arrow-counterclockwise"></i> Revert to Manager
                    </button>
                @endif
                @if(auth()->user()->isNFO() && $complaint->assigned_to === auth()->id())
                    <button type="button" class="btn btn-success me-2 mb-2" data-bs-toggle="modal" data-bs-target="#resolveModal">
                        <i class="bi bi-check-circle"></i> Resolve
                    </button>
                    <button type="button" class="btn btn-primary me-2 mb-2" data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="bi bi-person-plus"></i> Reassign
                    </button>
                @endif
            @endauth
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Complaint Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Complaint Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold">Created By:</p>
                            <p class="mb-2">
                                @if ($complaint->client && $complaint->client_id != 0)
                                    <i class="bi bi-person-circle"></i> {{ $complaint->client->full_name }}
                                @else
                                    <span class="text-muted">Guest User</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold">Created At:</p>
                            <p class="mb-2"><i class="bi bi-calendar"></i> {{ $complaint->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p class="mb-1 fw-semibold">Description:</p>
                            <div class="alert alert-secondary">{{ $complaint->description }}</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold">Last Updated:</p>
                            <p class="mb-2"><i class="bi bi-clock-history"></i> {{ $complaint->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold">Status:</p>
                            <p><span class="badge bg-{{ $complaint->status_color }}">{{ ucfirst($complaint->status) }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status History Timeline -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Status History</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline list-unstyled">
                        @foreach($complaint->actions as $action)
                            <li class="mb-4 position-relative ps-4">
                                <span class="position-absolute top-0 start-0 translate-middle p-2 bg-{{ $action->action === 'resolved' ? 'success' : ($action->action === 'reverted' ? 'warning' : 'primary') }} border border-light rounded-circle" style="        margin-top: 11px;"></span>
                                <div class="ms-3">
                                    <h6 class="mb-1">{{ ucfirst($action->action) }}</h6>
                                    <div class="text-muted small mb-1">
                                        <i class="bi bi-person"></i>
                                        @if ($action->user && $action->user_id != 0)
                                            {{ $action->user->full_name }}
                                        @else
                                            Guest User
                                        @endif
                                        &nbsp;|&nbsp;
                                        <i class="bi bi-clock"></i> {{ $action->created_at->format('M d, Y H:i') }}
                                    </div>
                                    <div>{{ $action->description }}</div>
                                    @if($action->action === 'resolved' && $action->resolution)
                                        <div class="mt-2">
                                            <strong>Resolution:</strong>
                                            <div class="alert alert-success mb-0">{{ $action->resolution }}</div>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Assigned User Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Assigned To</h5>
                </div>
                <div class="card-body">
                    @if($complaint->assignedTo)
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; font-size: 2rem;">
                                {{ substr($complaint->assignedTo->full_name, 0, 1) }}
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-1">{{ $complaint->assignedTo->full_name }}</h6>
                                <p class="text-muted mb-0">{{ ucfirst($complaint->assignedTo->role) }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Not assigned yet</p>
                    @endif
                </div>
            </div>

            <!-- Comments Card -->
            <!-- <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Comments</h5>
                </div>
                <div class="card-body">
                    @auth
                        <form action="{{ route('complaints.comment', $complaint) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <textarea name="comment" class="form-control" rows="3" placeholder="Add a comment..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Comment</button>
                        </form>
                    @endauth

                    <div class="comments">
                        @forelse($complaint->comments ?? [] as $comment)
                            <div class="comment mb-3 p-2 border rounded bg-light">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 1rem;">
                                        {{ substr($comment->user->full_name, 0, 1) }}
                                    </div>
                                    <div class="ms-2">
                                        <strong>{{ $comment->user->full_name }}</strong>
                                        <span class="text-muted small">&nbsp;{{ $comment->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                                <div>{{ $comment->comment }}</div>
                            </div>
                        @empty
                            <p class="text-muted">No comments yet.</p>
                        @endforelse
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('complaints.assign', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Ticket</h5>
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
                        <label for="description" class="form-label">Description</label>
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
<div class="modal fade" id="resolveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('complaints.resolve', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Resolve Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="resolution" class="form-label">Resolution</label>
                        <textarea class="form-control" name="resolution" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
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
<div class="modal fade" id="revertModal" tabindex="-1">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    async function fetchAssignableUsers() {
        console.log('Assign modal opened, fetching users...');
        try {
            const response = await fetch(`/api/assignable-users?complaint_id={{ $complaint->id }}`);
            const users = await response.json();
            const select = document.querySelector('#assignModal select[name="assigned_to"]');
            if (!select) return;
            select.innerHTML = '<option value="">Select User</option>';
            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.username} (${user.role.toUpperCase()})`;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error fetching assignable users:', error);
        }
    }

    // Try Bootstrap modal event
    const assignModal = document.getElementById('assignModal');
    if (assignModal) {
        assignModal.addEventListener('show.bs.modal', fetchAssignableUsers);
    }

    // Fallback: Bind to Assign button click
    document.querySelectorAll('button[data-bs-target="#assignModal"]').forEach(btn => {
        btn.addEventListener('click', fetchAssignableUsers);
    });
});
</script>
@endpush

@push('styles')
<style>
.timeline {
    border-left: 3px solid #dee2e6;
    margin-left: 1.5rem;
    padding-left: 1.5rem;
}
.timeline-item {
    position: relative;
}
.timeline-item .timeline-marker {
    position: absolute;
    left: -2.1rem;
    top: 0.3rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: #0d6efd;
    border: 2px solid #fff;
    z-index: 1;
}
</style>
@endpush 