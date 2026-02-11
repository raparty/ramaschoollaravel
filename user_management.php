<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");

// Only admins can access user management
RBAC::requirePermission('dashboard', 'view');
if (RBAC::getUserRole() !== 'Admin') {
    header('Location: access_denied.php');
    exit;
}

// Handle form submissions
$message = '';
$error = '';

// Add new user
if (isset($_POST['add_user'])) {
    $user_id = trim($_POST['user_id']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    $full_name = trim($_POST['full_name']);
    $contact_no = trim($_POST['contact_no']);
    
    if (!empty($user_id) && !empty($password) && !empty($role) && !empty($full_name)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (user_id, password, role, full_name, contact_no, created_at) 
                  VALUES ('" . db_escape($user_id) . "', 
                          '" . db_escape($hashed_password) . "', 
                          '" . db_escape($role) . "', 
                          '" . db_escape($full_name) . "', 
                          '" . db_escape($contact_no) . "', 
                          NOW())";
        
        if (db_query($query)) {
            $message = "User created successfully!";
        } else {
            $error = "Error creating user: " . db_error();
        }
    } else {
        $error = "Please fill all required fields";
    }
}

// Update user
if (isset($_POST['update_user'])) {
    $id = (int)$_POST['user_id'];
    $role = $_POST['role'];
    $full_name = trim($_POST['full_name']);
    $contact_no = trim($_POST['contact_no']);
    $new_password = trim($_POST['new_password']);
    
    $query = "UPDATE users SET 
              role = '" . db_escape($role) . "',
              full_name = '" . db_escape($full_name) . "',
              contact_no = '" . db_escape($contact_no) . "'";
    
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query .= ", password = '" . db_escape($hashed_password) . "'";
    }
    
    $query .= " WHERE id = " . $id;
    
    if (db_query($query)) {
        $message = "User updated successfully!";
    } else {
        $error = "Error updating user: " . db_error();
    }
}

// Delete user
if (isset($_POST['delete_user'])) {
    $id = (int)$_POST['user_id'];
    
    // Don't allow deleting yourself
    if ($id != $_SESSION['user_id']) {
        $query = "DELETE FROM users WHERE id = " . $id;
        if (db_query($query)) {
            $message = "User deleted successfully!";
        } else {
            $error = "Error deleting user: " . db_error();
        }
    } else {
        $error = "You cannot delete your own account!";
    }
}

// Get all users
$usersQuery = "SELECT * FROM users ORDER BY 
               CASE role 
                   WHEN 'Admin' THEN 1 
                   WHEN 'Office Manager' THEN 2 
                   WHEN 'Librarian' THEN 3
                   WHEN 'Teacher' THEN 4 
                   WHEN 'Student' THEN 5 
               END, full_name";
$usersResult = db_query($usersQuery);

require_once("includes/header.php");
require_once("includes/sidebar.php");
?>

<div class="page_title">
    <h3>User Management</h3>
</div>

<div id="container">
    <div id="content">
        <div class="grid_container">
            
            <?php if ($message): ?>
            <div class="grid_12">
                <div style="padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="grid_12">
                <div style="padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Add New User Form -->
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top">
                        <span class="h_icon enterprise-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </span>
                        <h4 class="heading">Add New User</h4>
                    </div>
                    <div class="widget_content" style="padding: 30px;">
                        <form method="POST" class="row" style="gap: 20px;">
                            <div class="col-md-6">
                                <label class="form-label">Username (Login ID) *</label>
                                <input type="text" name="user_id" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_no" class="form-control">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Role *</label>
                                <select name="role" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <option value="Admin">Admin - Full System Access</option>
                                    <option value="Office Manager">Office Manager - Transport, Fees, Accounts</option>
                                    <option value="Librarian">Librarian - Library Operations</option>
                                    <option value="Teacher">Teacher - Academic Only</option>
                                    <option value="Student">Student - View Personal Records</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12" style="margin-top: 20px;">
                                <button type="submit" name="add_user" class="btn-fluent-primary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 6px;">
                                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                    </svg>
                                    Add User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Users List -->
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top">
                        <span class="h_icon enterprise-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                        </span>
                        <h4 class="heading">All Users</h4>
                    </div>
                    <div class="widget_content" style="padding: 20px;">
                        <div style="overflow-x: auto;">
                            <table class="table table-striped" style="width: 100%; border-collapse: collapse;">
                                <thead style="background: var(--fluent-white-softer);">
                                    <tr>
                                        <th style="padding: 15px; text-align: left; border-bottom: 2px solid var(--app-border);">Username</th>
                                        <th style="padding: 15px; text-align: left; border-bottom: 2px solid var(--app-border);">Full Name</th>
                                        <th style="padding: 15px; text-align: left; border-bottom: 2px solid var(--app-border);">Role</th>
                                        <th style="padding: 15px; text-align: left; border-bottom: 2px solid var(--app-border);">Contact</th>
                                        <th style="padding: 15px; text-align: center; border-bottom: 2px solid var(--app-border);">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = db_fetch_assoc($usersResult)): ?>
                                    <tr style="border-bottom: 1px solid var(--app-border);">
                                        <td style="padding: 15px;">
                                            <strong><?php echo htmlspecialchars($user['user_id']); ?></strong>
                                        </td>
                                        <td style="padding: 15px;">
                                            <?php echo htmlspecialchars($user['full_name']); ?>
                                        </td>
                                        <td style="padding: 15px;">
                                            <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 13px; <?php
                                                echo match($user['role']) {
                                                    'Admin' => 'background: #FFE5E5; color: #FF6B6B;',
                                                    'Office Manager' => 'background: #FFF3E0; color: #FFA726;',
                                                    'Librarian' => 'background: #E3F2FD; color: #29B6F6;',
                                                    'Teacher' => 'background: #E3F2FD; color: #0078D4;',
                                                    'Student' => 'background: #E8F5E9; color: #4CAF50;',
                                                    default => 'background: #F5F5F5; color: #666;'
                                                };
                                            ?>">
                                                <?php echo htmlspecialchars($user['role']); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 15px;">
                                            <?php echo htmlspecialchars($user['contact_no'] ?? 'N/A'); ?>
                                        </td>
                                        <td style="padding: 15px; text-align: center;">
                                            <button onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['user_id'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['role'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['contact_no'] ?? '', ENT_QUOTES); ?>')" 
                                                    class="btn-fluent-secondary" style="padding: 6px 12px; font-size: 13px; margin-right: 5px;">
                                                Edit
                                            </button>
                                            
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="delete_user" class="btn-fluent-secondary" 
                                                        style="padding: 6px 12px; font-size: 13px; background: #f44336; border-color: #f44336;">
                                                    Delete
                                                </button>
                                            </form>
                                            <?php else: ?>
                                            <span style="font-size: 12px; color: #999;">(Current User)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; padding: 30px; max-width: 600px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <h3 style="margin: 0 0 20px; color: var(--fluent-slate);">Edit User</h3>
        <form method="POST" id="editForm">
            <input type="hidden" name="user_id" id="edit_user_id">
            
            <div style="margin-bottom: 15px;">
                <label class="form-label">Username (Cannot be changed)</label>
                <input type="text" id="edit_username" class="form-control" disabled>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label class="form-label">Full Name *</label>
                <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_no" id="edit_contact_no" class="form-control">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label class="form-label">Role *</label>
                <select name="role" id="edit_role" class="form-control" required>
                    <option value="Admin">Admin - Full System Access</option>
                    <option value="Office Manager">Office Manager - Transport, Fees, Accounts</option>
                    <option value="Librarian">Librarian - Library Operations</option>
                    <option value="Teacher">Teacher - Academic Only</option>
                    <option value="Student">Student - View Personal Records</option>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label class="form-label">New Password (leave blank to keep current)</label>
                <input type="password" name="new_password" class="form-control">
            </div>
            
            <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeEditModal()" class="btn-fluent-secondary">Cancel</button>
                <button type="submit" name="update_user" class="btn-fluent-primary">Update User</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(id, username, role, fullName, contact) {
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_full_name').value = fullName;
    document.getElementById('edit_contact_no').value = contact;
    document.getElementById('edit_role').value = role;
    
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modal on outside click
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>

<style>
.enterprise-card-icon {
    color: #0078D4;
}
.form-label {
    font-weight: 600;
    color: var(--fluent-slate);
    margin-bottom: 8px;
    display: block;
}
.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--app-border);
    border-radius: 4px;
    font-size: 14px;
}
.row {
    display: flex;
    flex-wrap: wrap;
    margin: -10px;
}
.col-md-6 {
    flex: 0 0 50%;
    padding: 10px;
}
.col-md-12 {
    flex: 0 0 100%;
    padding: 10px;
}
@media (max-width: 768px) {
    .col-md-6 {
        flex: 0 0 100%;
    }
}
</style>

<?php require_once("includes/footer.php"); ?>
