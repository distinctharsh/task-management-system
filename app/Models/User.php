<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Complaint;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;


    protected $primaryKey = 'id';


    // Role constants
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_VM = 'vm';
    const ROLE_NFO = 'nfo';
    const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'full_name',
        'address',
        'role',
        'vertical_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'client_id');
    }

    public function assignedComplaints()
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }

    public function actions()
    {
        return $this->hasMany(Tms::class, 'action_by');
    }

    public function previousAssignments()
    {
        return $this->hasMany(Tms::class, 'previous_assigned_to');
    }

    public function newAssignments()
    {
        return $this->hasMany(Tms::class, 'new_assigned_to');
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is a manager
     */
    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    /**
     * Check if user is a VM
     */
    public function isVM(): bool
    {
        return $this->role === self::ROLE_VM;
    }

    /**
     * Check if user is an NFO
     */
    public function isNFO(): bool
    {
        return $this->role === self::ROLE_NFO;
    }

    /**
     * Check if user is a regular user
     */
    public function isRegularUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Get all complaints for the user based on their role
     */
    public function getComplaints()
    {
        if ($this->isAdmin() || $this->isManager()) {
            return Complaint::whereIn('status', ['pending', 'assigned', 'in_progress'])->get();
        }

        if ($this->isVM()) {
            return Complaint::all();
        }

        if ($this->isNFO()) {
            return Complaint::where('assigned_to', $this->id)->get();
        }

        return Complaint::where('client_id', $this->id)->get();
    }

    /**
     * Get users that can be assigned to complaints based on current user's role
     */
    public function getAssignableUsers()
    {
        $query = User::query();

        if ($this->isAdmin() || $this->isManager()) {
            // Admins and Managers can assign to VMs or NFOs
            $query->whereIn('role', [self::ROLE_VM, self::ROLE_NFO]);
        } elseif ($this->isVM()) {
            // VMs can self-assign or assign to NFOs
            $query->where(function ($q) {
                $q->where('id', $this->id)
                    ->orWhere('role', self::ROLE_NFO);
            });
        } elseif ($this->isNFO()) {
            // NFOs can assign to other NFOs or VMs
            $query->where(function ($q) {
                $q->where('role', self::ROLE_NFO)
                    ->orWhere('role', self::ROLE_VM);
            })->where('id', '!=', $this->id);
        }

        return $query->get(['id', 'username', 'email', 'role']);
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }
}
