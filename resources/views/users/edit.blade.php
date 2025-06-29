@extends('layouts.app')

@section('content')
@include('layouts.breadcrumbs', [
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => 'Edit', 'url' => null],
    ]
])

<div class="row">
    <div class="col-md-12 mb-4">
        <h2>Edit User: {{ $user->full_name }}</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                            id="username" name="username" value="{{ old('username', $user->username) }}" required autofocus>
                        @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                            id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" required>
                        @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control"
                            id="password_confirmation" name="password_confirmation">
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select @error('role_id') is-invalid @enderror"
                            id="role_id" name="role_id" required
                            onchange="document.getElementById('verticalBox').style.display = (this.options[this.selectedIndex].text.toLowerCase().includes('vm') || this.options[this.selectedIndex].text.toLowerCase().includes('nfo')) ? 'block' : 'none';"
                            {{ $user->id === auth()->user()->id ? 'disabled' : '' }}>
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($user->id === auth()->user()->id)
                        <div class="form-text">You cannot change your own role.</div>
                        @endif
                    </div>

                    <!-- Vertical -->
                    <div class="mb-3" id="verticalBox"
                        style="display: {{ (old('role_id', $user->role_id) && (App\Models\Role::find(old('role_id', $user->role_id))->slug == 'vm' || App\Models\Role::find(old('role_id', $user->role_id))->slug == 'nfo')) ? 'block' : 'none' }};">
                        <label for="vertical_id" class="form-label">Vertical</label>
                        <select name="vertical_id" class="form-control">
                            <option value="">Select Vertical</option>
                            @foreach($verticals as $vertical)
                            <option value="{{ $vertical->id }}" {{ old('vertical_id', $user->vertical_id) == $vertical->id ? 'selected' : '' }}>
                                {{ $vertical->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Created At</dt>
                    <dd class="col-sm-8">{{ $user->created_at->format('M d, Y H:i') }}</dd>

                    <dt class="col-sm-4">Last Updated</dt>
                    <dd class="col-sm-8">{{ $user->updated_at->format('M d, Y H:i') }}</dd>

                    <dt class="col-sm-4">Total Tickets</dt>
                    <dd class="col-sm-8">{{ $user->complaints->count() }}</dd>

                    <dt class="col-sm-4">Assigned Tasks</dt>
                    <dd class="col-sm-8">{{ $user->assignedComplaints->count() }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Role Information</h5>
            </div>
            <div class="card-body">
                <h6>User Role</h6>
                <ul class="list-unstyled mb-4">
                    <li>• Can create and manage their own tickets</li>
                    <li>• Can view and comment on assigned tickets</li>
                    <li>• Cannot access user management</li>
                </ul>

                <h6>Admin Role</h6>
                <ul class="list-unstyled mb-0">
                    <li>• Full access to all tickets</li>
                    <li>• Can manage user accounts</li>
                    <li>• Can assign tickets to users</li>
                    <li>• Can update ticket statuses</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection