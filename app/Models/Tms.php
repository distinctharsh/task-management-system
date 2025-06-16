<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tms extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'action_by',
        'action_type',
        'remarks',
        'previous_assigned_to',
        'new_assigned_to',
    ];

    protected $casts = [
        'action_type' => 'string',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }

    public function previousAssignedTo()
    {
        return $this->belongsTo(User::class, 'previous_assigned_to');
    }

    public function newAssignedTo()
    {
        return $this->belongsTo(User::class, 'new_assigned_to');
    }

    public function isCreated()
    {
        return $this->action_type === 'created';
    }

    public function isAssigned()
    {
        return $this->action_type === 'assigned';
    }

    public function isReassigned()
    {
        return $this->action_type === 'reassigned';
    }

    public function isEscalated()
    {
        return $this->action_type === 'escalated';
    }

    public function isRemarkAdded()
    {
        return $this->action_type === 'remark_added';
    }

    public function isResolved()
    {
        return $this->action_type === 'resolved';
    }

    public function isClosed()
    {
        return $this->action_type === 'closed';
    }
}
