<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'client_id',
        'network_type',
        'priority',
        'description',
        'vertical',
        'user_name',
        'file_path',
        'section',
        'intercom',
        'status',
        'assigned_to',
        'resolution'
    ];

    protected $casts = [
        'priority' => 'string',
        'status' => 'string',
        'network_type' => 'string',
        'vertical' => 'string',
    ];

    

    protected $appends = ['status_color', 'priority_color'];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function actions()
    {
        return $this->hasMany(ComplaintAction::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isAssigned()
    {
        return $this->status === 'assigned';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isEscalated()
    {
        return $this->status === 'escalated';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'assigned' => 'info',
            'in_progress' => 'primary',
            'escalated' => 'danger',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary',
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'secondary',
        };
    }
}
