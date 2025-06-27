<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();

            // Base query builder (dynamic banayenge role ke hisaab se)
            $baseQuery = Complaint::query();

            // Manager: no filter
            if ($user->isManager()) {
                // No need to modify $baseQuery
            }

            // VM: Filter by vertical_id
            elseif ($user->isVM()) {
                $baseQuery->where('vertical_id', $user->vertical_id);
            }

            // NFO: Filter by vertical_id + assigned_to = user id
            elseif ($user->isNFO()) {
                $baseQuery->where('vertical_id', $user->vertical_id)
                    ->where('assigned_to', $user->id);
            }

            // Client or Others: Filter by client_id
            else {
                $baseQuery->where('client_id', $user->id);
            }

            // Get status IDs from the Status table
            $statusIds = Status::whereIn('name', [
                'unassigned',
                'assigned',
                'pending_with_vendor',
                'pending_with_user',
                'assign_to_me',
                'completed',
                'closed'
            ])->pluck('id', 'name');

            // Final data
            $todayComplaints = (clone $baseQuery)->with(['client', 'networkType', 'vertical', 'status', 'assignedTo'])->whereDate('created_at', today())->get();
            foreach ($todayComplaints as $complaint) {
                $complaint->assignableUsers = $user->getAssignableUsers($complaint);
            }
            $data = [
                'totalComplaints' => (clone $baseQuery)->count(),
                'unassignedComplaints' => (clone $baseQuery)->where('status_id', $statusIds->get('unassigned'))->count(),
                'assignedComplaints' => (clone $baseQuery)->where('status_id', $statusIds->get('assigned'))->count(),
                'pendingWithVendorComplaints' => (clone $baseQuery)->where('status_id', $statusIds->get('pending_with_vendor'))->count(),
                'pendingWithUserComplaints' => (clone $baseQuery)->where('status_id', $statusIds->get('pending_with_user'))->count(),
                'assignToMeComplaints' => (clone $baseQuery)->where('assigned_to', $user->id)->count(),
                'completedComplaints' => (clone $baseQuery)->where('status_id', $statusIds->get('completed'))->count(),
                'closedComplaints' => (clone $baseQuery)->where('status_id', $statusIds->get('closed'))->count(),
                'todayComplaints' => $todayComplaints,
                'unassignedStatusId' => $statusIds->get('unassigned'),
                'completedStatusId' => $statusIds->get('completed'),
                'assignToMeStatusId' => null,
            ];
            
            // Remove the old recentComplaints section from the view
            return view('dashboard', $data)->with('error', null);

            return view('dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return view('dashboard', [
                'totalComplaints' => 0,
                'unassignedComplaints' => 0,
                'assignedComplaints' => 0,
                'pendingWithVendorComplaints' => 0,
                'pendingWithUserComplaints' => 0,
                'assignToMeComplaints' => 0,
                'completedComplaints' => 0,
                'closedComplaints' => 0,
                'todayComplaints' => collect(),
                'unassignedStatusId' => null,
                'completedStatusId' => null,
                'assignToMeStatusId' => null,
            ])->with('error', 'There was an error loading the dashboard. Please try again.');
        }
    }

    public function getStatusColorAttribute()
    {
        return [
            'unassigned' => 'warning',
            'assigned' => 'info',
            'pending_with_vendor' => 'primary',
            'pending_with_user' => 'primary',
            'assign_to_me' => 'info',
            'completed' => 'success',
            'closed' => 'secondary',
        ][$this->status] ?? 'secondary';
    }

    public function getPriorityColorAttribute()
    {
        return [
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger'
        ][$this->priority] ?? 'secondary';
    }
}
