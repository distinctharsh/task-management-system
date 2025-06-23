@extends('layouts.app')

@section('content')
<div class="container-xxl">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>User Management</h2>
                @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add User
                </a>
                @endif

            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('users.index') }}" class="mb-3">
        <div class="d-flex align-items-center gap-2">
            <label for="per_page" class="form-label mb-0">Show</label>
            <select name="per_page" id="per_page" class="form-select w-auto" onchange="this.form.submit()">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>All</option>
            </select>
            <span class="text-muted">users per page</span>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Role</th>
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

            @if($perPage !== 'all')
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                    </div>
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-muted mt-3">
                    Showing all {{ $users->count() }} users
                </div>
            @endif
        </div>
    </div>
</div>
@endsection