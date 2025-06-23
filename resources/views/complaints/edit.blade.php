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

                    <!-- Row 1: Network Type and Priority -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="network_type_id" class="form-label">Network Type *</label>
                            <select class="form-select @error('network_type_id') is-invalid @enderror"
                                id="network_type_id" name="network_type_id" required>
                                <option value="">Select --</option>
                                @foreach($networkTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ old('network_type_id', $complaint->network_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('network_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
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
                    </div>

                    <!-- Description (Full Width) -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Complaint Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" rows="3" required>{{ old('description', $complaint->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Row 2: Vertical and Section -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="vertical_id" class="form-label">Vertical *</label>
                            <select class="form-select @error('vertical_id') is-invalid @enderror"
                                id="vertical_id" name="vertical_id" required>
                                <option value="">Select --</option>
                                @foreach($verticals as $vertical)
                                <option value="{{ $vertical->id }}"
                                    {{ old('vertical_id', $complaint->vertical_id) == $vertical->id ? 'selected' : '' }}>
                                    {{ $vertical->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('vertical_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="section_id" class="form-label">Section *</label>
                            <select class="form-select @error('section_id') is-invalid @enderror"
                                id="section_id" name="section_id" required>
                                <option value="">Select --</option>
                                @foreach($sections as $section)
                                <option value="{{ $section->id }}"
                                    {{ old('section_id', $complaint->section_id) == $section->id ? 'selected' : '' }}>
                                    {{ $section->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('section_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 3: User Name and Intercom -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user_name" class="form-label">User Name *</label>
                            <input type="text" class="form-control @error('user_name') is-invalid @enderror"
                                id="user_name" name="user_name" value="{{ old('user_name', $complaint->user_name) }}" required>
                            @error('user_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="intercom" class="form-label">Intercom *</label>
                            <input type="text" class="form-control @error('intercom') is-invalid @enderror"
                                id="intercom" name="intercom" value="{{ old('intercom', $complaint->intercom) }}" required>
                            @error('intercom')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- File Upload (Full Width) -->
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
                            <!-- <button type="button" class="btn btn-sm btn-danger ms-2" onclick="document.getElementById('delete_file').value = '1'">
                                Remove File
                            </button> -->
                            <input type="hidden" name="delete_file" id="delete_file" value="0">
                        </div>
                        @endif
                    </div>

                    <!-- Status (Full Width) -->
                    <div class="mb-3">
                        <label for="status_id" class="form-label">Status *</label>
                        <select class="form-select @error('status_id') is-invalid @enderror"
                            id="status_id" name="status_id" required>
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