<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Permission Model
 * 
 * Represents permissions in the RBAC system
 */
class Permission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'permissions';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'module',
        'submodule',
        'action',
        'description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the roles that have this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_permissions',
            'permission_id',
            'role',
            'id',
            'role_name'
        );
    }

    /**
     * Scope to filter by module.
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Get formatted permission name.
     */
    public function getNameAttribute()
    {
        $name = ucfirst($this->action) . ' ' . ucfirst($this->module);
        if ($this->submodule) {
            $name .= ' (' . ucfirst($this->submodule) . ')';
        }
        return $name;
    }

    /**
     * Get all unique modules.
     */
    public static function getModules()
    {
        return static::select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');
    }
}
