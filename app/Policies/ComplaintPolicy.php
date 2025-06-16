<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplaintPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user)
    {
        // Anyone can view the complaints list
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Complaint $complaint)
    {
        // Anyone can view a complaint
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(?User $user)
    {
        // Anyone can create a complaint
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(?User $user, Complaint $complaint)
    {
        if (!$user) {
            return false;
        }

        // Managers can update any complaint
        if ($user->isManager()) {
            return true;
        }

        // VMs can update complaints assigned to them
        if ($user->isVM() && $complaint->assigned_to === $user->id) {
            return true;
        }

        // NFOs can update complaints assigned to them
        if ($user->isNFO() && $complaint->assigned_to === $user->id) {
            return true;
        }

        // Regular users can only update their own complaints
        return $complaint->client_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(?User $user, Complaint $complaint)
    {
        // Only managers can delete complaints
        return $user && $user->isManager();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Complaint $complaint): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Complaint $complaint): bool
    {
        return false;
    }

    /**
     * Determine whether the user can assign the complaint.
     */
    public function assign(?User $user, Complaint $complaint)
    {
        if (!$user) {
            return false;
        }

        // Managers can assign to VMs or NFOs
        if ($user->isManager()) {
            return true;
        }

        // VMs can self-assign or assign to NFOs
        if ($user->isVM()) {
            return true;
        }

        // NFOs can assign to other NFOs or VMs
        if ($user->isNFO()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can resolve the complaint.
     */
    public function resolve(?User $user, Complaint $complaint)
    {
        if (!$user) {
            return false;
        }

        // Only NFOs can resolve complaints
        if (!$user->isNFO()) {
            return false;
        }

        // NFOs can only resolve complaints assigned to them
        return $complaint->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can escalate the complaint.
     */
    public function escalate(User $user, Complaint $complaint): bool
    {
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        if ($user->isVM() || $user->isNFO()) {
            return $complaint->assigned_to === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can revert the complaint.
     */
    public function revert(?User $user, Complaint $complaint)
    {
        if (!$user) {
            return false;
        }

        // Only VMs can revert complaints to managers
        if (!$user->isVM()) {
            return false;
        }

        // VMs can only revert complaints assigned to them
        return $complaint->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can update the complaint status.
     */
    public function updateStatus(User $user, Complaint $complaint): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}
