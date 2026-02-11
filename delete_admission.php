<?php
declare(strict_types=1); 
require_once("includes/bootstrap.php"); // Use bootstrap for session and DB integrity

// RBAC: Check if user has permission to delete admissions
RBAC::requirePermission('admission', 'delete');

ob_start();

// Get the ID from the URL 'sid' parameter used in student_detail.php
$sid = (int)($_GET['sid'] ?? 0);

if ($sid > 0) {
    $conn = Database::connection();
    
    // 1. Corrected Table: 'admissions' instead of 'student_info'
    // 2. Corrected Column: 'id' instead of 'student_id'
    $delete_query = "DELETE FROM admissions WHERE id = '$sid' LIMIT 1";
    
    if (mysqli_query($conn, $delete_query)) {
        // Redirect with msg=2 (Standard code for 'Deleted')
        header("Location: student_detail.php?msg=2");
        exit;
    } else {
        die("Database Error: " . mysqli_error($conn));
    }
} else {
    header("Location: student_detail.php");
    exit;
}
?>
