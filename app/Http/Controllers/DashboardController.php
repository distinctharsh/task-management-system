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
            
            // Initialize variables with default values
            $data = [
                'totalComplaints' => 0,
                'pendingComplaints' => 0,
                'resolvedComplaints' => 0,
                'inProgressComplaints' => 0,
                'recentComplaints' => collect()
            ];

            if ($user->isManager()) {
                $data['totalComplaints'] = Complaint::count();
                $data['pendingComplaints'] = Complaint::where('status', 'pending')->count();
                $data['resolvedComplaints'] = Complaint::where('status', 'resolved')->count();
                $data['inProgressComplaints'] = Complaint::where('status', 'in_progress')->count();
                $data['recentComplaints'] = Complaint::with('client')
                    ->latest()
                    ->take(5)
                    ->get();
            } else {
                $data['totalComplaints'] = Complaint::where('client_id', $user->id)->count();
                $data['pendingComplaints'] = Complaint::where('client_id', $user->id)
                    ->where('status', 'pending')
                    ->count();
                $data['resolvedComplaints'] = Complaint::where('client_id', $user->id)
                    ->where('status', 'resolved')
                    ->count();
                $data['inProgressComplaints'] = Complaint::where('client_id', $user->id)
                    ->where('status', 'in_progress')
                    ->count();
                $data['recentComplaints'] = Complaint::with('client')
                    ->where('client_id', $user->id)
                    ->latest()
                    ->take(5)
                    ->get();
            }

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
