<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     * Note: Using 'admin' table from legacy database
     *
     * @var string
     */
    protected $table = 'admin';

    /**
     * The attributes that are mass assignable.
     * Note: Database uses admin_user and admin_password columns
     * 
     * Security: 
     * - admin_password excluded to prevent password tampering via mass assignment
     * - role excluded to prevent privilege escalation attacks
     * - These fields exist in database but must be set explicitly, not via mass assignment
     * - Additional profile fields can be added here as needed (e.g., name, email, etc.)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_user',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'admin_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'admin_password' => 'hashed',
    ];

    /**
     * Get the name of the unique identifier for the user.
     * Note: Database uses 'admin_user' instead of 'email' or 'username'
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'admin_user';
    }

    /**
     * Get the password for the user.
     * Note: Database uses 'admin_password' instead of 'password'
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->admin_password;
    }

    /**
     * Check if the user is an administrator.
     */
    public function isAdmin()
    {
        // Since you are using the 'admin' table, everyone here is an admin
        return true;
    }

    /**
     * Get the user's role model.
     */
    public function roleModel()
    {
        // Note: users.role is a string, not a foreign key
        return Role::where('role_name', $this->role)->first();
    }

    /**
     * Get the user's role name.
     */
    public function getRoleNameAttribute()
    {
        return $this->role;
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($module, $action)
    {
        // Admins have all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Get role and check permissions
        $roleModel = $this->roleModel();
        if (!$roleModel) {
            return false;
        }

        return $roleModel->hasPermission($module, $action);
    }

    /**
     * Get all permissions for this user's role.
     */
    public function permissions()
    {
        $roleModel = $this->roleModel();
        if (!$roleModel) {
            return collect([]);
        }

        return $roleModel->permissions;
    }

    /**
     * Check if user can access a module.
     */
    public function canAccessModule($module)
    {
        return $this->permissions()
            ->where('module', $module)
            ->isNotEmpty();
    }
} // <--- THIS must be the ONLY brace at the end of the file.
