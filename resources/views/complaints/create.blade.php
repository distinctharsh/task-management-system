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
                <form action="{{ route('complaints.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" name="subject" value="{{ old('subject') }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                               id="location" name="location" value="{{ old('location') }}" required>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select @error('priority') is-invalid @enderror" 
                                id="priority" name="priority" required>
                            <option value="">Select Priority</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @guest
                        <div class="mb-3">
                            <label for="client_name" class="form-label">Your Name</label>
                            <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                                   id="client_name" name="client_name" value="{{ old('client_name') }}" required>
                            @error('client_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="client_email" class="form-label">Your Email</label>
                            <input type="email" class="form-control @error('client_email') is-invalid @enderror" 
                                   id="client_email" name="client_email" value="{{ old('client_email') }}" required>
                            @error('client_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="client_phone" class="form-label">Your Phone</label>
                            <input type="tel" class="form-control @error('client_phone') is-invalid @enderror" 
                                   id="client_phone" name="client_phone" value="{{ old('client_phone') }}" required>
                            @error('client_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endguest

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