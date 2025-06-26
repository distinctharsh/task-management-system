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
    public function isUnassigned()
    {
        return $this->status && $this->status->name === 'unassigned';
    }

    public function isAssigned()
    {
        return $this->status && $this->status->name === 'assigned';
    }

    public function isPendingWithVendor()
    {
        return $this->status && $this->status->name === 'pending_with_vendor';
    }

    public function isPendingWithUser()
    {
        return $this->status && $this->status->name === 'pending_with_user';
    }

    public function isAssignToMe()
    {
        return $this->status && $this->status->name === 'assign_to_me';
    }

    public function isCompleted()
    {
        return $this->status && $this->status->name === 'completed';
    }

    public function isClosed()
    {
        return $this->status && $this->status->name === 'closed';
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
        return $this->isUnassigned() || $this->isAssignToMe();
    }

    public function canBeCompleted()
    {
        return $this->isAssigned() || $this->isPendingWithVendor() || $this->isPendingWithUser();
    }

    public function canBeClosed()
    {
        return $this->isCompleted();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function canUserComment($user)
    {
        if (!$user) return false;

        // Get all user_ids and assigned_to from assignment actions
        $actions = $this->actions()->whereIn('action', ['assigned', 'reassigned'])->get();
        $userIds = $actions->pluck('user_id')->toArray();
        $assignedToIds = $actions->pluck('assigned_to')->toArray();
        $relatedUserIds = array_unique(array_merge($userIds, $assignedToIds));

        return in_array($user->id, $relatedUserIds);
    }
}
