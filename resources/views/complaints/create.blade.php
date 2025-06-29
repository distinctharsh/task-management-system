@extends('layouts.app')

@section('content')
@include('layouts.breadcrumbs', [
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Tickets', 'url' => route('complaints.index')],
        ['label' => 'Create', 'url' => null],
    ]
])

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Create Ticket</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ticket Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" class="ticket-create-form">
                    @csrf

                    <!-- First Row - User Name and Intercom -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user_name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('user_name') is-invalid @enderror"
                                id="user_name" name="user_name" value="{{ old('user_name') }}" required>
                            @error('user_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="intercom" class="form-label">Intercom</label>
                            <input type="number" class="form-control @error('intercom') is-invalid @enderror"
                                id="intercom" name="intercom" value="{{ old('intercom') }}" required
                                min="100" max="999" maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 3)">
                            <small class="form-text text-muted">Enter 3-digit intercom number (e.g., 123)</small>
                            @error('intercom')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Second Row - Network Type and Priority -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="network_type_id" class="form-label">Issue Type</label>
                            <select class="form-select @error('network_type_id') is-invalid @enderror"
                                id="network_type_id" name="network_type_id" required>
                                <option value="">Select --</option>
                                @foreach($networkTypes as $type)
                                <option value="{{ $type->id }}" {{ old('network_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('network_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="section_id" class="form-label">Section</label>
                            <select class="form-select @error('section_id') is-invalid @enderror"
                                id="section_id" name="section_id" required>
                                <option value="">Select --</option>
                                @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                    {{ $section->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('section_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <!-- Third Row - Vertical and Section -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="vertical_id" class="form-label">Vertical</label>
                            <select class="form-select @error('vertical_id') is-invalid @enderror"
                                id="vertical_id" name="vertical_id" required>
                                <option value="">Select --</option>
                                @foreach($verticals as $vertical)
                                <option value="{{ $vertical->id }}" {{ old('vertical_id') == $vertical->id ? 'selected' : '' }}>
                                    {{ $vertical->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('vertical_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Priority</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="high" value="high" {{ old('priority') == 'high' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="high">High</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="medium" value="medium" {{ old('priority') == 'medium' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="medium">Medium</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="low" value="low" {{ old('priority') == 'low' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="low">Low</label>
                                </div>
                            </div>
                            @error('priority')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Complaint Description (Full Width) -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Ticket Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- File Upload (Full Width) -->
                    <div class="mb-4">
                        <label for="file" class="form-label">File Upload</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                            id="file" name="file">
                        @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Max file size: 2MB</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary ticket-create-submit-btn">
                            <span class="btn-text">Submit Ticket</span>
                            <span class="btn-loading d-none">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Submitting...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Information</h5>
            </div>
            <div class="card-body">
                <p class="mb-3">Please provide all the required information to create a new ticket. Our team will review your ticket and take appropriate action.</p>

                <h6 class="mb-2">Priority Levels:</h6>
                <ul class="list-unstyled mb-3">
                    <li class="mb-2">
                        <span class="badge bg-success">Low</span>
                        - Non-urgent issues that can be addressed within 5-7 business days
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-warning">Medium</span>
                        - Issues that need attention within 2-3 business days
                    </li>
                    <li>
                        <span class="badge bg-danger">High</span>
                        - Urgent issues that require immediate attention
                    </li>
                </ul>

                <h6 class="mb-2">What happens next?</h6>
                <ol class="mb-0">
                    <li class="mb-2">Your ticket will be assigned a unique reference number</li>
                    <li class="mb-2">A manager will review and assign it to the appropriate team</li>
                    <li class="mb-2">You'll receive updates on the status of your ticket</li>
                    <li>You can track your ticket using the reference number</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle ticket creation form submission to prevent double-clicking
        const ticketCreateForm = document.querySelector('.ticket-create-form');
        if (ticketCreateForm) {
            ticketCreateForm.addEventListener('submit', function(e) {
                const submitBtn = ticketCreateForm.querySelector('.ticket-create-submit-btn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                // Disable button and show loading state
                submitBtn.disabled = true;
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');
                
                // Re-enable button after 10 seconds as a fallback (in case of errors)
                setTimeout(function() {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        btnText.classList.remove('d-none');
                        btnLoading.classList.add('d-none');
                    }
                }, 10000);
            });
        }
    });
</script>

<style>
.ticket-create-submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.ticket-create-submit-btn .btn-loading {
    display: inline-flex;
    align-items: center;
}

.ticket-create-submit-btn .spinner-border {
    width: 1rem;
    height: 1rem;
}
</style>
@endpush