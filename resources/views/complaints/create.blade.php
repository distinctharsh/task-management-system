@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Create New Ticket</h2>
            <a href="{{ route('complaints.index') }}" class="btn btn-secondary">Back to List</a>
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
                <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Network Type -->
                    <div class="mb-3">
                        <label for="network_type" class="form-label">Network Type *</label>
                        <select class="form-select @error('network_type') is-invalid @enderror" 
                                id="network_type" name="network_type" required>
                            <option value="">Select --</option>
                            <option value="fiber" {{ old('network_type') == 'fiber' ? 'selected' : '' }}>Fiber</option>
                            <option value="wireless" {{ old('network_type') == 'wireless' ? 'selected' : '' }}>Wireless</option>
                            <option value="copper" {{ old('network_type') == 'copper' ? 'selected' : '' }}>Copper</option>
                        </select>
                        @error('network_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Priority (Radio Buttons) -->
                    <div class="mb-3">
                        <label class="form-label">Priority *</label>
                        <div class="form-check">
                            <input class="form-check-input @error('priority') is-invalid @enderror" 
                                type="radio" name="priority" id="high" value="high" 
                                {{ old('priority') == 'high' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="high">High</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="priority" 
                                id="medium" value="medium" 
                                {{ old('priority') == 'medium' ? 'checked' : '' }}>
                            <label class="form-check-label" for="medium">Medium</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="priority" 
                                id="low" value="low" 
                                {{ old('priority') == 'low' ? 'checked' : '' }}>
                            <label class="form-check-label" for="low">Low</label>
                        </div>
                        @error('priority')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Complaint Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Complaint Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Verticals -->
                    <div class="mb-3">
                        <label for="vertical" class="form-label">Verticals *</label>
                        <select class="form-select @error('vertical') is-invalid @enderror" 
                                id="vertical" name="vertical" required>
                            <option value="">Select --</option>
                            <option value="it" {{ old('vertical') == 'it' ? 'selected' : '' }}>IT</option>
                            <option value="hr" {{ old('vertical') == 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="facilities" {{ old('vertical') == 'facilities' ? 'selected' : '' }}>Facilities</option>
                        </select>
                        @error('vertical')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- User Name -->
                    <div class="mb-3">
                        <label for="user_name" class="form-label">User Name *</label>
                        <input type="text" class="form-control @error('user_name') is-invalid @enderror" 
                            id="user_name" name="user_name" value="{{ old('user_name') }}" required>
                        @error('user_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label for="file" class="form-label">File Upload</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" 
                            id="file" name="file">
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Section -->
                    <div class="mb-3">
                        <label for="section" class="form-label">Section *</label>
                        <select class="form-select @error('section') is-invalid @enderror" 
                                id="section" name="section" required>
                            <option value="">Select --</option>
                            <option value="north" {{ old('section') == 'north' ? 'selected' : '' }}>North</option>
                            <option value="south" {{ old('section') == 'south' ? 'selected' : '' }}>South</option>
                            <option value="east" {{ old('section') == 'east' ? 'selected' : '' }}>East</option>
                        </select>
                        @error('section')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Intercom -->
                    <div class="mb-3">
                        <label for="intercom" class="form-label">Intercom *</label>
                        <input type="text" class="form-control @error('intercom') is-invalid @enderror" 
                            id="intercom" name="intercom" value="{{ old('intercom') }}" required>
                        @error('intercom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Submit Ticket</button>
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