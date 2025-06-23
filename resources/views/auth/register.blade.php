@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create User</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                id="username" name="username" value="{{ old('username') }}" required autofocus>
                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                            @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control"
                                id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Role</label>
                            <select class="form-select @error('role_id') is-invalid @enderror"
                                id="role_id" name="role_id" required
                                onchange="document.getElementById('verticalBox').style.display = (this.options[this.selectedIndex].text.toLowerCase().includes('vm') || this.options[this.selectedIndex].text.toLowerCase().includes('nfo')) ? 'block' : 'none';">
                                <option value="">Select a role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Vertical -->
                        <div class="mb-3" id="verticalBox"
                            style="display: {{ (old('role_id') && (App\Models\Role::find(old('role_id'))->slug == 'vm' || App\Models\Role::find(old('role_id'))->slug == 'nfo')) ? 'block' : 'none' }};">
                            <label for="vertical_id" class="form-label">Vertical</label>
                            <select name="vertical_id" class="form-control">
                                <option value="">Select Vertical</option>
                                @foreach($verticals as $vertical)
                                <option value="{{ $vertical->id }}" {{ old('vertical_id') == $vertical->id ? 'selected' : '' }}>
                                    {{ $vertical->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Create
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <p class="mb-0">Already have an account?
                                <a href="{{ route('login') }}" class="text-decoration-none">Login</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection