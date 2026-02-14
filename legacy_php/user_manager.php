<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");

// SECURITY: Admin-only access
if (!has_access('dashboard', 'view') || ($_SESSION['role'] ?? '') !== 'Admin') {
    header("Location: dashboard.php");
    exit;
}

include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();
$msg = "";

// --- LOGIC SECTION ---

// 1. Handle Role Creation
if (isset($_POST['add_role'])) {
    $role_name = mysqli_real_escape_string($conn, trim($_POST['role_name']));
    mysqli_query($conn, "INSERT IGNORE INTO role_permissions (role, permission_id) SELECT '$role_name', id FROM permissions WHERE module='dashboard'");
    $msg = "<div class='alert alert-success'>Role '$role_name' created successfully.</div>";
}

// 2. Handle Permission Saving
if (isset($_POST['save_permissions'])) {
    $target_role = mysqli_real_escape_string($conn, $_POST['target_role']);
    $selected_perms = $_POST['perms'] ?? [];
    mysqli_query($conn, "DELETE FROM role_permissions WHERE role = '$target_role'");
    foreach ($selected_perms as $p_id) {
        $p_id = (int)$p_id;
        mysqli_query($conn, "INSERT INTO role_permissions (role, permission_id) VALUES ('$target_role', $p_id)");
    }
    $msg = "<div class='alert alert-success'>Permissions updated for $target_role.</div>";
}

// 3. Handle User Update/Reset
if (isset($_POST['update_user'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['edit_user_id']);
    $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);
    $new_pass = $_POST['new_password'] ?? '';
    
    mysqli_query($conn, "UPDATE users SET role = '$new_role' WHERE user_id = '$uid'");
    if (!empty($new_pass)) {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$hashed' WHERE user_id = '$uid'");
    }
    $msg = "<div class='alert alert-success'>User $uid updated.</div>";
}

// 4. Handle User Deletion
if (isset($_GET['delete_user'])) {
    $uid = mysqli_real_escape_string($conn, $_GET['delete_user']);
    // Prevent self-deletion
    if ($uid === $_SESSION['user_id']) {
        $msg = "<div class='alert alert-danger'>Security Error: You cannot delete your own account.</div>";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE user_id = '$uid'");
        $msg = "<div class='alert alert-success'>User $uid has been permanently removed.</div>";
    }
}
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <div class="grid_12">
                <h3 style="color:#0078D4; margin-bottom:20px;">Enterprise Access Control</h3>
                <?php echo $msg; ?>
                
                <ul class="nav nav-tabs mb-4" id="rbacTabs">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#staff">Staff Accounts</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#roles">Roles & Permissions</button>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="staff">
                    <div class="grid_12">
                        <div class="widget_wrap">
                            <div class="widget_top"><h6>User Management Console</h6></div>
                            <div class="widget_content">
                                <table class="display data_tbl">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Full Name</th>
                                            <th>Role</th>
                                            <th style="text-align:right;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $u_res = mysqli_query($conn, "SELECT * FROM users ORDER BY full_name ASC");
                                        while($u = mysqli_fetch_assoc($u_res)) {
                                            echo "<tr>
                                                <td><strong>{$u['user_id']}</strong></td>
                                                <td>{$u['full_name']}</td>
                                                <td><span class='badge bg-secondary'>{$u['role']}</span></td>
                                                <td style='text-align:right;'>
                                                    <button class='btn_small btn_gray' onclick=\"openEditModal('{$u['user_id']}', '{$u['role']}')\">Manage</button>
                                                    <a href='?delete_user={$u['user_id']}#staff' class='btn_small btn_orange' onclick=\"return confirm('Are you sure? This will permanently delete {$u['user_id']}.')\">Delete</a>
                                                </td>
                                            </tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="roles">
                    <div class="grid_4">
                        <div class="widget_wrap">
                            <div class="widget_top"><h6>Create Custom Role</h6></div>
                            <div class="widget_content" style="padding:20px;">
                                <form action="" method="post">
                                    <label>Role Name (e.g. Office Admin)</label>
                                    <input type="text" name="role_name" class="form-control mb-3" placeholder="Office Admin" required>
                                    <button type="submit" name="add_role" class="btn btn-primary w-100">Add New Role</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="grid_8">
                        <div class="widget_wrap">
                            <div class="widget_top"><h6>Permission Matrix</h6></div>
                            <div class="widget_content" style="padding:20px;">
                                <form action="" method="post">
                                    <select name="target_role" class="form-select mb-3" onchange="window.location.href='?role='+this.value+'#roles'">
                                        <option value="">-- Choose Role to Configure --</option>
                                        <?php
                                        $roles_res = mysqli_query($conn, "SELECT DISTINCT role FROM role_permissions");
                                        while($r = mysqli_fetch_assoc($roles_res)) {
                                            $sel = (isset($_GET['role']) && $_GET['role'] == $r['role']) ? 'selected' : '';
                                            echo "<option value='{$r['role']}' $sel>{$r['role']}</option>";
                                        }
                                        ?>
                                    </select>

                                    <?php if(isset($_GET['role'])): 
                                        $current_role = mysqli_real_escape_string($conn, $_GET['role']);
                                        $active_res = mysqli_query($conn, "SELECT permission_id FROM role_permissions WHERE role = '$current_role'");
                                        $active_ids = [];
                                        while($a = mysqli_fetch_assoc($active_res)) { $active_ids[] = (int)$a['permission_id']; }
                                    ?>
                                        <div style="max-height:400px; overflow-y:auto; border:1px solid #eee; padding:10px; margin-bottom:15px;">
                                            <table class="table table-sm">
                                                <?php
                                                $all_p = mysqli_query($conn, "SELECT * FROM permissions ORDER BY module ASC");
                                                while($p = mysqli_fetch_assoc($all_p)) {
                                                    $checked = in_array((int)$p['id'], $active_ids) ? 'checked' : '';
                                                    echo "<tr>
                                                        <td><strong>".ucfirst($p['module'])."</strong></td>
                                                        <td>".htmlspecialchars($p['description'])."</td>
                                                        <td style='text-align:right;'><input type='checkbox' name='perms[]' value='{$p['id']}' $checked></td>
                                                    </tr>";
                                                }
                                                ?>
                                            </table>
                                        </div>
                                        <button type="submit" name="save_permissions" class="btn btn-success w-100">Save Access for <?php echo $current_role; ?></button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="post" class="modal-content">
            <div class="modal-body">
                <input type="hidden" name="edit_user_id" id="edit_user_id">
                <h6>Manage User: <span id="display_uid" style="color:#0078D4;"></span></h6>
                <div class="mt-3">
                    <label>Assigned Access Role</label>
                    <select name="new_role" id="edit_role" class="form-select">
                        <?php
                        $roles_list = mysqli_query($conn, "SELECT DISTINCT role FROM role_permissions");
                        while($rl = mysqli_fetch_assoc($roles_list)) {
                            echo "<option value='{$rl['role']}'>{$rl['role']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mt-3">
                    <label>Reset Password (leave blank to keep current)</label>
                    <input type="password" name="new_password" class="form-control" placeholder="New Password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="update_user" class="btn btn-primary">Update Account</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(uid, role) {
    document.getElementById('edit_user_id').value = uid;
    document.getElementById('display_uid').innerText = uid;
    document.getElementById('edit_role').value = role;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// Ensure the Roles tab stays active when filtering permissions
if (window.location.hash === '#roles') {
    var triggerEl = document.querySelector('button[data-bs-target="#roles"]');
    bootstrap.Tab.getInstance(triggerEl).show();
}
</script>

<?php include_once("includes/footer.php"); ?>
