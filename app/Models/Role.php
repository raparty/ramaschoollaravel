<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Role Model
 * 
 * Represents user roles in the RBAC system
 */
class Role extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'role_name',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the permissions for this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role',
            'permission_id',
            'role_name',
            'id'
        );
    }

    /**
     * Scope to get only active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission($module, $action)
    {
        return $this->permissions()
            ->where('module', $module)
            ->where('action', $action)
            ->exists();
    }

    /**
     * Get users with this role.
     * Note: users table uses 'role' column as string, not foreign key
     */
    public function users()
    {
        return User::where('role', $this->role_name)->get();
    }

    /**
     * Get count of users with this role.
     */
    public function getUsersCountAttribute()
    {
        // This queries the actual users table
        return DB::table('users')->where('role', $this->role_name)->count();
    }
}
