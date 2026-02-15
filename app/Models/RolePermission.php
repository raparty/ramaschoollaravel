<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * RolePermission Model
 * 
 * Pivot model for role-permission relationships
 */
class RolePermission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'role_permissions';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'role',
        'permission_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the role.
     */
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role', 'role_name');
    }

    /**
     * Get the permission.
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
