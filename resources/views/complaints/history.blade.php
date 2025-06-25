@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Ticket History</h4>
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="btn btn-secondary">Back</a>
                </div>
                <div class="card-body">
                    <form method="GET" action="" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Search reference..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="action" class="form-select">
                                    <option value="">All Actions</option>
                                    @foreach($actionsList as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="by" class="form-select">
                                    <option value="">All Recent Action By</option>
                                    @foreach($usersList as $user)
                                    <option value="{{ $user }}" {{ request('by') == $user ? 'selected' : '' }}>{{ $user }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-outline-primary w-100" type="submit">Filter</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ticket Ref</th>
                                    <th>Action</th>
                                    <th>Recent Action By</th>
                                    <th>Assigned To</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($complaints as $complaint)
                                @php $latestAction = $complaint->actions->first(); @endphp
                                <tr>
                                    <td>
                                        <a>
                                            {{ $complaint->reference_number }}
                                        </a>
                                    </td>
                                    <td>{{ $latestAction?->action ?? '-' }}</td>
                                    <td>{{ $latestAction?->user?->full_name ?? $latestAction?->user?->username ?? '-' }}</td>
                                    <td>
                                        @if($latestAction?->assigned_to)
                                            @php $assignedUser = \App\Models\User::find($latestAction->assigned_to); @endphp
                                            {{ $assignedUser ? $assignedUser->full_name : 'Unknown User' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $latestAction?->created_at ? $latestAction->created_at->format('M d, Y H:i') : '-' }}</td>
                                    <td>{{ $latestAction?->description ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $complaints->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection