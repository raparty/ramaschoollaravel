<?php
declare(strict_types=1);
require_once 'includes/bootstrap.php';

$error_msg = "";

/**
 * SECURE LOGIN LOGIC
 * This version removes the bypass and uses password_verify for security.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $conn = Database::connection();
    
    $username = mysqli_real_escape_string($conn, trim((string)$_POST['username']));
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // Fetch user including the RBAC role column
        $sql = "SELECT * FROM users WHERE user_id = '$username' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            /**
             * PASSWORD VERIFICATION
             * Checks the plain-text input against the secure hash in the database.
             */
            if (password_verify($password, $user['password'])) {
                
                // 1. Set Session Variables for RBAC permissions
                $_SESSION['user_id']  = $user['user_id'];
                $_SESSION['username'] = $user['full_name'];
                $_SESSION['role']     = $user['role']; // Powers has_access() in sidebar

                // 2. Regenerate ID to prevent session fixation
                session_regenerate_id(true);

                header("Location: dashboard.php");
                exit;
            } else {
                $error_msg = "Invalid username or password.";
            }
        } else {
            $error_msg = "Invalid username or password.";
        }
    } else {
        $error_msg = "Please enter both credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | School ERP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        :root {
            --fluent-azure: #0078D4;
            --bg-page: #F3F2F1;
        }
        body { 
            background: var(--bg-page); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card { 
            background: #fff; 
            padding: 40px; 
            border-radius: 4px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            width: 100%; 
            max-width: 400px; 
            border: 1px solid #EDEBE9;
        }
        .btn-azure { 
            background: var(--fluent-azure); 
            color: #fff; 
            border: none; 
            width: 100%; 
            padding: 12px; 
            border-radius: 2px; 
            font-weight: 600; 
            margin-top: 15px; 
        }
        .btn-azure:hover { background: #106EBE; color: #fff; }
        .form-control { border-radius: 2px; padding: 10px; }
        .form-label { font-weight: 600; color: #323130; font-size: 14px; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <div style="background: var(--fluent-azure); color: #fff; display: inline-block; padding: 8px 16px; border-radius: 2px; font-weight: 700; font-size: 20px; margin-bottom: 10px;">ERP</div>
        <h4 style="color: #323130; font-weight: 600;">Welcome back</h4>
        <p style="color: #605E5C; font-size: 14px;">Secure Enterprise Access Portal</p>
    </div>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger" style="font-size: 13px; border-radius: 2px;"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="index.php" method="post">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="User ID" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" name="login_submit" class="btn btn-azure">Sign In</button>
    </form>
    
    <div class="mt-4 text-center" style="font-size: 11px; color: #A19F9D; border-top: 1px solid #F3F2F1; padding-top: 20px; text-transform: uppercase; letter-spacing: 1px;">
        © <?php echo date('Y'); ?> School Management System
    </div>
</div>

</body>
</html>
