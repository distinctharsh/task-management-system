@extends('layouts.app')

@section('content')
@include('layouts.breadcrumbs', [
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Users', 'url' => null],
    ]
])
<div class="container-xxl">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>User Management</h2>
                @if(auth()->user()->isManager())
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="bi bi-plus-lg"></i> Add User
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="">
                <table id="usersTable" class="table table-hover w-100">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Vertical</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->full_name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>
                                <span class="badge 
                                    @switch($user->role->slug ?? '')
                                        @case('admin') bg-danger @break
                                        @case('manager') bg-primary @break
                                        @case('vm') bg-info @break
                                        @case('nfo') bg-warning text-dark @break
                                        @default bg-secondary
                                    @endswitch
                                ">
                                    {{ $user->role->name ?? 'No Role' }}
                                </span>
                            </td>
                            <td>{{ $user->vertical ? $user->vertical->name : '-' }}</td>
                            <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">Edit</a>
                                    @if($user->id !== auth()->user()->id && $user->role->slug !== 'manager')
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- DataTables handles pagination/info -->
        </div>
    </div>
</div>

@if(auth()->user()->isManager())
<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createUserModalLabel">Create User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('users.store') }}" id="createUserForm">
          @csrf
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required autofocus>
            @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
            @error('full_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
          </div>
          <div class="mb-3">
            <label for="role_id" class="form-label">Role</label>
            <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required
                onchange="document.getElementById('verticalBox').style.display = (this.options[this.selectedIndex].text.toLowerCase().includes('vm') || this.options[this.selectedIndex].text.toLowerCase().includes('nfo')) ? 'block' : 'none';">
              <option value="">Select a role</option>
              @foreach($roles as $role)
                @if($role->slug !== 'admin' && $role->slug !== 'client')
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endif
              @endforeach
            </select>
            @error('role_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3" id="verticalBox"
            style="display: {{ (old('role_id') && (App\Models\Role::find(old('role_id'))->slug == 'vm' || App\Models\Role::find(old('role_id'))->slug == 'nfo')) ? 'block' : 'none' }};">
            <label for="vertical_id" class="form-label">Vertical</label>
            <select name="vertical_id" class="form-control">
              <option value="">Select Vertical</option>
              @foreach($verticals as $vertical)
                <option value="{{ $vertical->id }}" {{ old('vertical_id') == $vertical->id ? 'selected' : '' }}>{{ $vertical->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary" id="registerSubmitBtn">
              <span id="registerBtnText">Create</span>
              <span id="registerBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
          </div>
        </form>
        @if($errors->any())
        <div class="alert alert-danger mt-3">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">

@endpush
@push('scripts')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#usersTable').DataTable();
});
</script>
@endpush