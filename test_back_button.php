<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'Test User';
include_once("includes/header.php");
?>
<div class="app-content">
    <div id="container" style="padding: 40px;">
        <h2>Back Button Test</h2>
        <p>Click the back button in the header to test fallback behavior.</p>
        <a href="dashboard.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #0078D4; color: white; text-decoration: none; border-radius: 4px;">Go to Dashboard</a>
    </div>
</div>
<?php include_once("includes/footer.php"); ?>
