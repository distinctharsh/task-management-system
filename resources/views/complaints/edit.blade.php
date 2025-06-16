@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Edit Ticket #{{ $complaint->reference_number }}</h2>
            <div>
                <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-secondary">Back to Details</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Ticket</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('complaints.update', $complaint) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Network Type -->
                    <div class="mb-3">
                        <label for="network_type" class="form-label">Network Type *</label>
                        <select class="form-select @error('network_type') is-invalid @enderror" 
                                id="network_type" name="network_type" required>
                            <option value="">Select --</option>
                            <option value="fiber" {{ old('network_type', $complaint->network_type) == 'fiber' ? 'selected' : '' }}>Fiber</option>
                            <option value="wireless" {{ old('network_type', $complaint->network_type) == 'wireless' ? 'selected' : '' }}>Wireless</option>
                            <option value="copper" {{ old('network_type', $complaint->network_type) == 'copper' ? 'selected' : '' }}>Copper</option>
                        </select>
                        @error('network_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority *</label>
                        <select class="form-select @error('priority') is-invalid @enderror" 
                                id="priority" name="priority" required>
                            <option value="low" {{ old('priority', $complaint->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $complaint->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $complaint->priority) == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Complaint Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" required>{{ old('description', $complaint->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vertical -->
                    <div class="mb-3">
                        <label for="vertical" class="form-label">Vertical *</label>
                        <select class="form-select @error('vertical') is-invalid @enderror" 
                                id="vertical" name="vertical" required>
                            <option value="">Select --</option>
                            <option value="it" {{ old('vertical', $complaint->vertical) == 'it' ? 'selected' : '' }}>IT</option>
                            <option value="hr" {{ old('vertical', $complaint->vertical) == 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="facilities" {{ old('vertical', $complaint->vertical) == 'facilities' ? 'selected' : '' }}>Facilities</option>
                        </select>
                        @error('vertical')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- User Name -->
                    <div class="mb-3">
                        <label for="user_name" class="form-label">User Name *</label>
                        <input type="text" class="form-control @error('user_name') is-invalid @enderror" 
                               id="user_name" name="user_name" value="{{ old('user_name', $complaint->user_name) }}" required>
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
                        @if($complaint->file_path)
                            <div class="mt-2">
                                <small>Current file: </small>
                                <a href="{{ Storage::url($complaint->file_path) }}" target="_blank">
                                    {{ basename($complaint->file_path) }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Section -->
                    <div class="mb-3">
                        <label for="section" class="form-label">Section *</label>
                        <select class="form-select @error('section') is-invalid @enderror" 
                                id="section" name="section" required>
                            <option value="">Select --</option>
                            <option value="north" {{ old('section', $complaint->section) == 'north' ? 'selected' : '' }}>North</option>
                            <option value="south" {{ old('section', $complaint->section) == 'south' ? 'selected' : '' }}>South</option>
                            <option value="east" {{ old('section', $complaint->section) == 'east' ? 'selected' : '' }}>East</option>
                        </select>
                        @error('section')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Intercom -->
                    <div class="mb-3">
                        <label for="intercom" class="form-label">Intercom *</label>
                        <input type="text" class="form-control @error('intercom') is-invalid @enderror" 
                               id="intercom" name="intercom" value="{{ old('intercom', $complaint->intercom) }}" required>
                        @error('intercom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="pending" {{ old('status', $complaint->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="assigned" {{ old('status', $complaint->status) == 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_progress" {{ old('status', $complaint->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ old('status', $complaint->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ old('status', $complaint->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update Ticket</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Ticket Details</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Reference</dt>
                    <dd class="col-sm-8">{{ $complaint->reference_number }}</dd>

                    <dt class="col-sm-4">Created By</dt>
                    <dd class="col-sm-8">{{ $complaint->client?->full_name ?? $complaint->user_name }}</dd>

                    <dt class="col-sm-4">Created At</dt>
                    <dd class="col-sm-8">{{ $complaint->created_at->format('M d, Y H:i') }}</dd>

                    <dt class="col-sm-4">Last Updated</dt>
                    <dd class="col-sm-8">{{ $complaint->updated_at->format('M d, Y H:i') }}</dd>

                    @if($complaint->assignedTo)
                        <dt class="col-sm-4">Assigned To</dt>
                        <dd class="col-sm-8">{{ $complaint->assignedTo->full_name }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection