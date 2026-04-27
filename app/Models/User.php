<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'branch_id',
        'employee_id',
        'name',
        'email',
        'password',
        'profile_picture',
        'user_type_id',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'user_branch');
    }

    /**
     * Returns the branch IDs this user can access.
     * Superadmin → all branches. Admin with assigned branches → those branches.
     * Otherwise → just their own branch_id.
     */
    public function accessibleBranchIds(): array
    {
        $superRoles = config('rbac.super_roles', []);
        $roleName = optional($this->userType)->name ?? '';

        if (in_array($roleName, $superRoles)) {
            return []; // empty = no filter = all branches
        }

        $assigned = $this->branches()->pluck('branches.id')->toArray();
        if (! empty($assigned)) {
            return $assigned;
        }

        return $this->branch_id ? [$this->branch_id] : [];
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'cashier_id');
    }
}
