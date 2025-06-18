@extends('layouts.app')

@section('content')
<div class="container-xxl">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold mb-1">
                Ticket: <span class="text-primary">{{ $complaint->reference_number }}</span>
                <span class="badge bg-{{ $complaint->status_color }} ms-2">
                    {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
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
            <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary me-2 mb-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            @auth
            @include('complaints.partials.action-buttons')
            @endauth
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <!-- Complaint Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Complaint Details</h5>
                    <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
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
                                    {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
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
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('complaints.modals.assign')
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

        // Fetch assignable users for modal
        async function fetchAssignableUsers() {
            try {
                const response = await fetch(`/api/assignable-users?complaint_id={{ $complaint->id }}`);
                const users = await response.json();
                const select = document.querySelector('#assignModal select[name="assigned_to"]');

                if (select) {
                    select.innerHTML = '<option value="">Select User</option>';
                    users.forEach(user => {
                        const option = new Option(
                            `${user.full_name} (${user.role.toUpperCase()})`,
                            user.id
                        );
                        select.add(option);
                    });
                }
            } catch (error) {
                console.error('Error fetching assignable users:', error);
                alert('Failed to load assignable users. Please try again.');
            }
        }

        // Set up modal event listeners
        const assignModal = document.getElementById('assignModal');
        if (assignModal) {
            assignModal.addEventListener('show.bs.modal', fetchAssignableUsers);
        }
    });
</script>
@endpush


@endsection