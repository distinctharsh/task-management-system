<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'client_id',
        'priority',
        'description',
        'user_name',
        'file_path',
        'intercom',
        'status_id',
        'assigned_to',
        'assigned_by',
        'resolution',
        'network_type_id',
        'vertical_id',
        'section_id',
    ];

    protected $casts = [
        'priority' => 'string',
    ];

    // protected $appends = ['status_color', 'priority_color', 'network_type', 'vertical', 'section'];
    protected $appends = ['status_color', 'priority_color'];


    // Relationships
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function actions()
    {
        return $this->hasMany(ComplaintAction::class)->orderBy('created_at', 'desc');
    }

    public function networkType()
    {
        return $this->belongsTo(NetworkType::class);
    }

    public function vertical()
    {
        return $this->belongsTo(Vertical::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // Status Check Methods
    public function isPending()
    {
        return $this->status && $this->status->name === 'pending';
    }

    public function isAssigned()
    {
        return $this->status && $this->status->name === 'assigned';
    }

    public function isInProgress()
    {
        return $this->status && $this->status->name === 'in_progress';
    }

    public function isEscalated()
    {
        return $this->status && $this->status->name === 'escalated';
    }

    public function isResolved()
    {
        return $this->status && $this->status->name === 'resolved';
    }

    public function isClosed()
    {
        return $this->status && $this->status->name === 'closed';
    }

    public function isReverted()
    {
        return $this->status && $this->status->name === 'reverted';
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return $this->status ? $this->status->color : 'secondary';
    }

    public function getPriorityColorAttribute()
    {
        return match ($this->priority) {
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'secondary',
        };
    }

    // Helper Methods
    public function canBeAssigned()
    {
        return $this->isPending() || $this->isAssigned();
    }

    public function canBeResolved()
    {
        return $this->isAssigned() || $this->isInProgress();
    }

    public function canBeClosed()
    {
        return $this->isResolved();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
