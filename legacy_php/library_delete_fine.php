<?php
declare(strict_types=1);

// 1. Core logic and database connection via modern bootstrap
require_once("includes/bootstrap.php");
ob_start();

$conn = Database::connection();
$sid = (int)($_GET['sid'] ?? 0);

if ($sid > 0) {
    // 2. Updated Table: 'library_fine_managers' instead of 'library_fine_manager'
    // 3. Updated Column: 'fine_id' is the verified primary key
    $delete_query = "DELETE FROM library_fine_managers WHERE fine_id = '$sid' LIMIT 1";
    
    if (mysqli_query($conn, $delete_query)) {
        // Redirect back to manager with msg=2 (Standard code for 'Deleted')
        header("Location: library_fine_manager.php?msg=2");
        exit;
    } else {
        // Log detailed error for admin while keeping user response clean
        error_log("Fine deletion failed for ID $sid: " . mysqli_error($conn));
        die("Database Error: Failed to remove fine record.");
    }
} else {
    // Security: Redirect if no valid ID is provided
    header("Location: library_fine_manager.php");
    exit;
}
?>
