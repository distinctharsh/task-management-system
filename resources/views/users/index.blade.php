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

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection