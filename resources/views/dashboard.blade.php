@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Dashboard</h4>
                    <div>
                        @if(auth()->user()->isManager() || auth()->user()->isVM())
                            <a href="{{ route('complaints.index') }}" class="btn btn-primary">View All Complaints</a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <!-- Welcome Message -->
                    <div class="alert alert-info">
                        Welcome back, {{ auth()->user()->name }}!
                        @if(auth()->user()->isManager())
                            As a Manager, you can view and assign all complaints.
                        @elseif(auth()->user()->isVM())
                            As a Vendor Manager, you can self-assign complaints and assign them to NFOs.
                        @elseif(auth()->user()->isNFO())
                            As a Network Field Officer, you can resolve complaints and reassign them.
                        @endif
                    </div>

                    <!-- Statistics (from controller variables) -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Total Complaints</h5>
                                    <h2 class="mb-0">{{ $totalComplaints }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Complaints</h5>
                                    <h2 class="mb-0">{{ $pendingComplaints }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Resolved Complaints</h5>
                                    <h2 class="mb-0">{{ $resolvedComplaints }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">In Progress</h5>
                                    <h2 class="mb-0">{{ $inProgressComplaints }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Complaints (from controller variables) -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Recent Complaints</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Reference</th>
                                                    <th>Subject</th>
                                                    <th>Status</th>
                                                    <th>Priority</th>
                                                    <th>Created By</th>
                                                    <th>Created At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recentComplaints as $complaint)
                                                    <tr>
                                                        <td>{{ $complaint->reference_number }}</td>
                                                        <td>{{ $complaint->subject }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $complaint->status_color }}">
                                                                {{ $complaint->status }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $complaint->priority_color }}">
                                                                {{ $complaint->priority }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $complaint->client?->full_name ?? 'Guest User' }}</td>
                                                        <td>{{ $complaint->created_at->format('M d, Y H:i') }}</td>
                                                        <td>
                                                            <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-info">View</a>
                                                            @can('update', $complaint)
                                                                <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-sm btn-primary">Edit</a>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No complaints found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection 