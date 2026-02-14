# Phase 2: Authentication & RBAC - Implementation Files

This directory contains the Laravel authentication and RBAC implementation files for Phase 2 of the migration.

## Files Included

### Controllers
- **AuthController.php** - Handles login, logout, and dashboard
  - `showLogin()` - Display login form
  - `login()` - Process login with validation
  - `logout()` - Process logout and clear session
  - `dashboard()` - Show dashboard based on user role

### Middleware
- **RoleMiddleware.php** - Role-based access control
  - Checks if user has required role (Admin, Teacher, Staff, Student)
  - Admin bypasses all role checks

- **PermissionMiddleware.php** - Permission-based access control
  - Checks specific module and action permissions
  - Uses the role_permissions table

### Providers
- **AuthServiceProvider.php** - Authorization gates and policies
  - Defines gates for all modules (students, fees, library, etc.)
  - Implements before() hook for Admin superuser access

### Views
- **login.blade.php** - Modern login page with Fluent Design
  - CSRF protection
  - Validation error display
  - Remember me functionality
  - Responsive design

## Installation Instructions

When Laravel is set up, copy these files to their respective locations:

```bash
# Copy Controller
cp phase2_auth/AuthController.php laravel-app/app/Http/Controllers/Auth/

# Copy Middleware
cp phase2_auth/RoleMiddleware.php laravel-app/app/Http/Middleware/
cp phase2_auth/PermissionMiddleware.php laravel-app/app/Http/Middleware/

# Copy Provider
cp phase2_auth/AuthServiceProvider.php laravel-app/app/Providers/

# Copy View
mkdir -p laravel-app/resources/views/auth
cp phase2_auth/login.blade.php laravel-app/resources/views/auth/
```

## Configuration Required

### 1. Register Middleware in `app/Http/Kernel.php`

```php
protected $middlewareAliases = [
    // ... existing middleware
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'permission' => \App\Http\Middleware\PermissionMiddleware::class,
];
```

### 2. Update Routes in `routes/web.php`

```php
use App\Http\Controllers\Auth\AuthController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Example: Routes with role middleware
    Route::middleware(['role:Admin,Teacher'])->group(function () {
        Route::resource('admissions', AdmissionController::class);
    });
    
    // Example: Routes with permission middleware
    Route::middleware(['permission:students,view'])->group(function () {
        Route::get('/students', [StudentController::class, 'index']);
    });
});
```

### 3. Configure Auth Guard in `config/auth.php`

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],
```

### 4. Update User Model

Ensure the User model in `app/Models/User.php` has:
- `getAuthIdentifierName()` method returning 'user_id'
- `hasPermission()` method for checking permissions
- Role helper methods (isAdmin(), isTeacher(), etc.)

## Testing

### Test Login
1. Navigate to `/login`
2. Enter valid credentials
3. Should redirect to dashboard with user info

### Test Logout
1. Click logout button
2. Should redirect to login with success message

### Test Role-Based Access
1. Login as different roles
2. Try accessing role-restricted routes
3. Should see 403 error if unauthorized

### Test Permission Gates
1. Use @can directive in Blade templates
2. Test Gate::allows() in controllers
3. Verify permissions work correctly

## Security Features

✅ **CSRF Protection** - Automatic via @csrf directive
✅ **Session Regeneration** - Prevents session fixation
✅ **Password Verification** - Uses password_verify()
✅ **Remember Me** - Persistent authentication
✅ **Audit Logging** - Logs login/logout events
✅ **Role-Based Access** - Multiple role checking
✅ **Permission-Based Access** - Fine-grained control

## Next Steps

After implementing Phase 2:
1. Test authentication thoroughly
2. Verify RBAC works correctly  
3. Update existing procedural code to use Laravel auth
4. Move to Phase 3: Student Module Migration

## Notes

- Admin role has superuser access (bypasses all checks)
- User model uses `user_id` field instead of email for authentication
- All middleware properly handles unauthenticated users
- Authorization gates use the existing `role_permissions` table
