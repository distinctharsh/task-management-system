@extends('layouts.app')

@section('content')
<div class="container-xxl">
    @guest
    {{-- <div class="alert alert-info d-flex justify-content-between align-items-center">
        <div>
            <strong>Guest Notice:</strong> You are viewing ticket details as a guest. For more actions, please log in.
        </div>
        <button class="btn btn-outline-primary btn-sm" onclick="openSearchModal()">Search Another Ticket</button>
    </div> --}}
    <script>
    function openSearchModal() {
        if (window.bootstrap && document.getElementById('searchTicketModal')) {
            var modal = new bootstrap.Modal(document.getElementById('searchTicketModal'));
            modal.show();
        } else {
            window.location.href = '/';
        }
    }
    </script>
    @endguest
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            @php
                $displayStatus = $complaint->status->display_name ?? 'Unknown';
                if (
                    ($complaint->status->name ?? null) === 'assign_to_me'
                    && auth()->check()
                    && $complaint->assigned_to == auth()->id()
                ) {
                    $displayStatus = 'Assign to Me';
                } elseif (($complaint->status->name ?? null) === 'assign_to_me') {
                    $displayStatus = 'Assigned';
                }
            @endphp
            <h2 class="fw-bold mb-1">
                Ticket: <span class="text-primary">{{ $complaint->reference_number }}</span>
                <span class="badge bg-{{ $complaint->status_color }} ms-2">
                    {{ $displayStatus }}
                </span>
            </h2>
            <div class="mb-2">
                <span class="badge bg-secondary me-2">Priority:
                    <span class="badge bg-{{ $complaint->priority_color }} ms-1">
                        {{ ucfirst($complaint->priority) }}
                    </span>
                </span>
                <span class="badge bg-info me-2">
                    <i class="bi bi-hdd-network"></i> {{ ucfirst($complaint->networkType->name ?? 'N/A') }}
                </span>
                <span class="badge bg-light text-dark">
                    <i class="bi bi-layers"></i> {{ ucfirst($complaint->vertical->name ?? 'N/A') }}
                </span>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            @include('layouts.breadcrumbs', [
                'breadcrumbs' => [
                    ['label' => 'Home', 'url' => route('home')],
                    ['label' => 'Tickets', 'url' => route('complaints.index')],
                    ['label' => 'Show', 'url' => null],
                ]
            ])
            {{-- @auth
            @include('complaints.partials.action-buttons')
            @endauth --}}
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <!-- Complaint Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ticket Details</h5>
                    @auth
                                                            @if(auth()->user()->isManager())
                    <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>

                        @endif
                                                        @endauth
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold"><i class="bi bi-person"></i> Created By:</p>
                            <p class="mb-3 ps-3">
                                @if ($complaint->client)
                                {{ $complaint->client->full_name }}
                                <small class="text-muted d-block">({{ $complaint->client->email }})</small>
                                @else
                                {{ $complaint->user_name }} <span class="badge bg-secondary">Guest</span>
                                @endif
                            </p>

                            <p class="mb-1 fw-semibold"><i class="bi bi-hdd-network"></i> Network Type:</p>
                            <p class="mb-3 ps-3">{{ ucfirst($complaint->networkType->name ?? 'N/A') }}</p>

                            <p class="mb-1 fw-semibold"><i class="bi bi-geo-alt"></i> Section:</p>
                            <p class="mb-3 ps-3">{{ ucfirst($complaint->section->name ?? 'N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold"><i class="bi bi-calendar"></i> Created At:</p>
                            <p class="mb-3 ps-3">
                                {{ $complaint->created_at->format('M d, Y h:i A') }}
                                <small class="text-muted d-block">({{ $complaint->created_at->diffForHumans() }} ({{ $complaint->created_at->format('h:i A') }}) )</small>
                            </p>

                            <p class="mb-1 fw-semibold"><i class="bi bi-layers"></i> Vertical:</p>
                            <p class="mb-3 ps-3">{{ ucfirst($complaint->vertical->name ?? 'N/A' ) }}</p>

                            <p class="mb-1 fw-semibold"><i class="bi bi-telephone"></i> Intercom:</p>
                            <p class="mb-3 ps-3">{{ $complaint->intercom }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1 fw-semibold"><i class="bi bi-card-text"></i> Description:</p>
                        <div class="alert alert-light border ps-3">{{ $complaint->description }}</div>
                    </div>

                    @if($complaint->file_path)
                    <div class="mb-3">
                        <p class="mb-1 fw-semibold"><i class="bi bi-paperclip"></i> Attachment:</p>
                        <div class="d-flex align-items-center">
                            <a href="{{ Storage::url($complaint->file_path) }}" target="_blank"
                                class="btn btn-sm btn-outline-primary me-2">
                                <i class="bi bi-download"></i> Download
                            </a>
                            <span class="text-muted small">
                                {{ basename($complaint->file_path) }}
                            </span>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold"><i class="bi bi-clock-history"></i> Last Updated:</p>
                            <p class="ps-3">
                                {{ $complaint->updated_at->format('M d, Y H:i') }}
                                <small class="text-muted d-block">({{ $complaint->updated_at->diffForHumans() }} ({{ $complaint->updated_at->format('h:i A') }}) )</small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 fw-semibold"><i class="bi bi-info-circle"></i> Status:</p>
                            <p class="ps-3">
                                <span class="badge bg-{{ $complaint->status_color }}">
                                    {{ $displayStatus }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            @include('complaints.partials.timeline')
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Assignment Card -->
            @include('complaints.partials.assignment-card')

            <!-- Comments Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Comments
                        <span class="text-muted small ms-2"></span>
                    </h5>
                </div>
                <div class="card-body">
                    @auth
                        @php $isManager = auth()->user() && auth()->user()->isManager(); @endphp
                        @if($complaint->isClosed())
                            <textarea class="form-control" rows="3" placeholder="Comments are disabled for this ticket as it is closed." disabled></textarea>
                            <button class="btn btn-primary mt-2" disabled>Add Comment</button>
                        @elseif($complaint->isCompleted() && $isManager)
                            <form action="{{ route('complaints.update', $complaint) }}" method="POST" class="mb-4">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="close_status_id" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status_id" id="close_status_id" class="form-select" required>
                                        <option value="{{ $closeStatus->id }}">Closed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <textarea name="description" class="form-control" rows="2" placeholder="Remarks (required)" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-success close-submit-btn">
                                    <span class="btn-text">Close</span>
                                    <span class="btn-loading d-none">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Closing...
                                    </span>
                                </button>
                                <a href="#" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#assignModal{{ $complaint->id }}">Assign</a>
                            </form>
                        @elseif($complaint->isCompleted() && !$isManager)
                            <textarea class="form-control" rows="3" placeholder="Comments are disabled for this ticket as it is completed." disabled></textarea>
                            <button class="btn btn-primary mt-2" disabled>Add Comment</button>
                        @elseif($complaint->canUserComment(auth()->user()))
                            <form action="{{ route('complaints.comment', $complaint) }}" method="POST" class="mb-4 comment-form" id="commentForm{{ $complaint->id }}">
                                @csrf
                                {{-- Debugging --}}
                                {{-- <div>auth id: {{ auth()->id() }}, assigned_to: {{ $complaint->assigned_to }}, isManager: {{ auth()->user() && auth()->user()->isManager() ? 'yes' : 'no' }}</div> --}}
                                @if(auth()->check() && $complaint->assigned_to && auth()->user()->id == $complaint->assigned_to && !$isManager && !$complaint->isCompleted())
                                    <div class="mb-3">
                                        <label for="status_id" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status_id" id="status_id" class="form-select" required>
                                            <option value="">Select status</option>
                                            @foreach($statusOptions as $status)
                                                <option value="{{ $status->id }}">{{ $status->display_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <textarea name="comment" class="form-control" rows="3" placeholder="Add a comment..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary comment-submit-btn" data-complaint-id="{{ $complaint->id }}">
                                    <span class="btn-text">Add Comment</span>
                                    <span class="btn-loading d-none">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Submitting...
                                    </span>
                                </button>
                            </form>
                        @else
                            <textarea class="form-control" rows="3" placeholder="You are not allowed to comment on this ticket." disabled></textarea>
                            <button class="btn btn-primary mt-2" disabled>Add Comment</button>
                        @endif
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
            </div>

            <!-- Quick Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5"><i class="bi bi-exclamation-triangle"></i> Priority:</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-{{ $complaint->priority_color }}">
                                {{ ucfirst($complaint->priority) }}
                            </span>
                        </dd>

                        <dt class="col-sm-5"><i class="bi bi-hdd-network"></i> Network:</dt>
                        <dd class="col-sm-7">{{ ucfirst( $complaint->networkType->name ?? 'N/A' ) }}</dd>

                        <dt class="col-sm-5"><i class="bi bi-layers"></i> Vertical:</dt>
                        <dd class="col-sm-7">{{ ucfirst($complaint->vertical->name ?? 'N/A' ) }}</dd>

                        <dt class="col-sm-5"><i class="bi bi-geo-alt"></i> Section:</dt>
                        <dd class="col-sm-7">{{ ucfirst( $complaint->section->name ?? 'N/A' ) }}</dd>

                        <dt class="col-sm-5"><i class="bi bi-telephone"></i> Intercom:</dt>
                        <dd class="col-sm-7">{{ $complaint->intercom }}</dd>

                        <dt class="col-sm-5"><i class="bi bi-calendar"></i> Created:</dt>
                        <dd class="col-sm-7">
                            {{ $complaint->created_at->diffForHumans() }} ({{ $complaint->created_at->format('h:i A') }})
                        </dd>
                    </dl>
                </div>
            </div>

            @if($complaint->isCompleted() && (
                (auth()->user() && auth()->user()->isManager()) ||
                (auth()->user() && auth()->user()->isVM() && $complaint->assignedTo && $complaint->assignedTo->isNFO())
            ))
                <form action="{{ route('complaints.update', $complaint) }}" method="POST" class="mb-4">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="close_status_id" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status_id" id="close_status_id" class="form-select" required>
                            <option value="{{ $closeStatus->id }}">Closed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <textarea name="description" class="form-control" rows="2" placeholder="Remarks (required)" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Close</button>
                    <a href="#" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#assignModal{{ $complaint->id }}">Assign</a>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Assign Modal (inline) -->
<div class="modal fade" id="assignModal{{ $complaint->id }}" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('complaints.assign', $complaint) }}" method="POST" class="assign-form">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Assign Ticket</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="assigned_to{{ $complaint->id }}" class="form-label">Assign To</label>
            <select class="form-select" name="assigned_to" id="assigned_to{{ $complaint->id }}" required>
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
          <button type="submit" class="btn btn-primary assign-submit-btn">
            <span class="btn-text">Assign</span>
            <span class="btn-loading d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Assigning...
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@include('complaints.modals.resolve')
@include('complaints.modals.revert')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Handle comment form submission to prevent double-clicking
        const commentForms = document.querySelectorAll('.comment-form');
        commentForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('.comment-submit-btn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                // Disable button and show loading state
                submitBtn.disabled = true;
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');
                
                // Re-enable button after 5 seconds as a fallback (in case of errors)
                setTimeout(function() {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        btnText.classList.remove('d-none');
                        btnLoading.classList.add('d-none');
                    }
                }, 5000);
            });
        });

        // Handle close form submission to prevent double-clicking
        const closeButtons = document.querySelectorAll('.close-submit-btn');
        closeButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                const btnText = btn.querySelector('.btn-text');
                const btnLoading = btn.querySelector('.btn-loading');
                
                // Disable button and show loading state
                btn.disabled = true;
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');
                
                // Re-enable button after 5 seconds as a fallback (in case of errors)
                setTimeout(function() {
                    if (btn.disabled) {
                        btn.disabled = false;
                        btnText.classList.remove('d-none');
                        btnLoading.classList.add('d-none');
                    }
                }, 5000);
            });
        });

        // Handle assign form submission to prevent double-clicking
        const assignForms = document.querySelectorAll('.assign-form');
        assignForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('.assign-submit-btn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                // Disable button and show loading state
                submitBtn.disabled = true;
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');
                
                // Re-enable button after 5 seconds as a fallback (in case of errors)
                setTimeout(function() {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        btnText.classList.remove('d-none');
                        btnLoading.classList.add('d-none');
                    }
                }, 5000);
            });
        });

        // Handle resolve form submission to prevent double-clicking
        const resolveForms = document.querySelectorAll('.resolve-form');
        resolveForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('.resolve-submit-btn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                // Disable button and show loading state
                submitBtn.disabled = true;
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');
                
                // Re-enable button after 5 seconds as a fallback (in case of errors)
                setTimeout(function() {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        btnText.classList.remove('d-none');
                        btnLoading.classList.add('d-none');
                    }
                }, 5000);
            });
        });

        // Handle revert form submission to prevent double-clicking
        const revertForms = document.querySelectorAll('.revert-form');
        revertForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('.revert-submit-btn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                // Disable button and show loading state
                submitBtn.disabled = true;
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');
                
                // Re-enable button after 5 seconds as a fallback (in case of errors)
                setTimeout(function() {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        btnText.classList.remove('d-none');
                        btnLoading.classList.add('d-none');
                    }
                }, 5000);
            });
        });

        // Fetch assignable users for modal
        async function fetchAssignableUsers() {
            try {
                const response = await fetch(`/api/assignable-users?complaint_id={{ $complaint->id }}`);
                const users = await response.json();
                const select = document.querySelector('#assignModal{{ $complaint->id }} select[name="assigned_to"]');
                if (select) {
                    select.innerHTML = '<option value="">Select User</option>';
                    users.forEach(user => {
                        const option = new Option(
                            `${user.full_name} (${user.role.name.toUpperCase()})`,
                            user.id
                        );
                        select.add(option);
                    });
                }
            } catch (error) {
                console.error('Error fetching assignable users:', error);
            }
        }
        // Set up modal event listeners
        const assignModal = document.getElementById('assignModal{{ $complaint->id }}');
        if (assignModal) {
            assignModal.addEventListener('show.bs.modal', fetchAssignableUsers);
        }
    });
</script>

<style>
.comment-submit-btn:disabled,
.close-submit-btn:disabled,
.assign-submit-btn:disabled,
.resolve-submit-btn:disabled,
.revert-submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.comment-submit-btn .btn-loading,
.close-submit-btn .btn-loading,
.assign-submit-btn .btn-loading,
.resolve-submit-btn .btn-loading,
.revert-submit-btn .btn-loading {
    display: inline-flex;
    align-items: center;
}

.comment-submit-btn .spinner-border,
.close-submit-btn .spinner-border,
.assign-submit-btn .spinner-border,
.resolve-submit-btn .spinner-border,
.revert-submit-btn .spinner-border {
    width: 1rem;
    height: 1rem;
}
</style>
@endpush


@endsection