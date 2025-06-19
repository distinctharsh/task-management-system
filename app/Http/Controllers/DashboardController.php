<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
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

            // Final data
            $data = [
                'totalComplaints' => (clone $baseQuery)->count(),
                'pendingComplaints' => (clone $baseQuery)->where('status', 'pending')->count(),
                'resolvedComplaints' => (clone $baseQuery)->where('status', 'resolved')->count(),
                'inProgressComplaints' => (clone $baseQuery)->where('status', 'in_progress')->count(),
                'recentComplaints' => (clone $baseQuery)->with(['client', 'networkType', 'vertical'])
                    ->latest()
                    ->take(5)
                    ->get(),
            ];

            return view('dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return view('dashboard', [
                'totalComplaints' => 0,
                'pendingComplaints' => 0,
                'resolvedComplaints' => 0,
                'inProgressComplaints' => 0,
                'recentComplaints' => collect()
            ])->with('error', 'There was an error loading the dashboard. Please try again.');
        }
    }



    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'in_progress' => 'info',
            'resolved' => 'success',
            'rejected' => 'danger'
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
