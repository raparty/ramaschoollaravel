<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");

// SECURITY: Only Admins can manage users
if (!has_access('dashboard', 'view') || ($_SESSION['role'] ?? '') !== 'Admin') {
    header("Location: dashboard.php");
    exit;
}

include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();
$msg = "";

// 1. HANDLE USER DELETION
if (isset($_GET['delete'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete']);
    if ($delete_id === $_SESSION['user_id']) {
        $msg = "<div class='alert alert-danger'>You cannot delete your own admin account.</div>";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE user_id = '$delete_id'");
        $msg = "<div class='alert alert-success'>User $delete_id has been removed.</div>";
    }
}

// 2. HANDLE ROLE & PASSWORD UPDATES
if (isset($_POST['update_user'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['edit_user_id']);
    $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);
    $new_pass = $_POST['new_password'] ?? '';
    
    // Update Role
    $sql = "UPDATE users SET role = '$new_role' WHERE user_id = '$uid'";
    mysqli_query($conn, $sql);

    // Update Password if provided
    if (!empty($new_pass)) {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$hashed_pass' WHERE user_id = '$uid'");
        $msg = "<div class='alert alert-success'>Account for $uid updated (Role & Password).</div>";
    } else {
        $msg = "<div class='alert alert-success'>Role for $uid updated to $new_role.</div>";
    }
}

// 3. HANDLE NEW USER CREATION
if (isset($_POST['create_user'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['user_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['full_name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    $sql = "INSERT INTO users (user_id, password, role, full_name) VALUES ('$uid', '$pass', '$role', '$fullname')";
    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='alert alert-success'>User $uid created successfully!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <div class="grid_12">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 style="color:#0078D4; margin:0;">Enterprise User Management</h3>
                    <button class="btn_small btn_blue" data-bs-toggle="collapse" data-bs-target="#addUserForm"><span>+ Add New User</span></button>
                </div>
                <?php echo $msg; ?>
            </div>

            <div class="grid_12 collapse" id="addUserForm">
                <div class="widget_wrap azure-card">
                    <div class="widget_top"><h6>Create Staff Credentials</h6></div>
                    <div class="widget_content">
                        <form action="" method="post" class="form_container left_label">
                            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:20px; padding:20px;">
                                <div>
                                    <label>User ID (Login)</label>
                                    <input name="user_id" type="text" class="form-control" required />
                                </div>
                                <div>
                                    <label>Full Name</label>
                                    <input name="full_name" type="text" class="form-control" required />
                                </div>
                                <div>
                                    <label>Initial Password</label>
                                    <input name="password" type="password" class="form-control" required />
                                </div>
                                <div>
                                    <label>Access Role</label>
                                    <select name="role" class="form-control" required>
                                        <option value="Teacher">Teacher</option>
                                        <option value="Office Manager">Office Manager</option>
                                        <option value="Librarian">Librarian</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>
                                <div style="display:flex; align-items:flex-end;">
                                    <button type="submit" name="create_user" class="btn_small btn_blue"><span>Register User</span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top"><h6>Directory & Access Control</h6></div>
                    <div class="widget_content">
                        <table class="display data_tbl">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Full Name</th>
                                    <th>Assigned Role</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = mysqli_query($conn, "SELECT * FROM users ORDER BY full_name ASC");
                                while ($u = mysqli_fetch_assoc($res)) {
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($u['user_id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $u['role'] === 'Admin' ? 'bg-primary' : 'bg-secondary'; ?>">
                                            <?php echo $u['role']; ?>
                                        </span>
                                    </td>
                                    <td><span style="color:green;">● Active</span></td>
                                    <td style="text-align:right;">
                                        <button class="btn_small btn_gray" onclick="openEditModal('<?php echo $u['user_id']; ?>', '<?php echo $u['role']; ?>')">Manage Account</button>
                                        <a href="?delete=<?php echo $u['user_id']; ?>" class="btn_small btn_orange" onclick="return confirm('Delete user <?php echo $u['user_id']; ?>?')">Delete</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="edit_user_id" id="edit_user_id">
                <p>Modifying Account: <strong id="display_uid" style="color:#0078D4;"></strong></p>
                <hr>
                <div class="mb-3">
                    <label class="form-label">Update Role</label>
                    <select name="new_role" id="edit_role" class="form-select">
                        <option value="Admin">Admin</option>
                        <option value="Office Manager">Office Manager</option>
                        <option value="Librarian">Librarian</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Student">Student</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reset Password (Leave blank to keep current)</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update_user" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(uid, role) {
    document.getElementById('edit_user_id').value = uid;
    document.getElementById('display_uid').innerText = uid;
    document.getElementById('edit_role').value = role;
    var myModal = new bootstrap.Modal(document.getElementById('editModal'));
    myModal.show();
}
</script>

<?php include_once("includes/footer.php"); ?>
