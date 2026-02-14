<?php
declare(strict_types=1);

/**
 * delete_staff.php
 * Handles secure deletion of staff records and associated media assets.
 */

require_once("includes/bootstrap.php");

$conn = Database::connection();

// 1. Capture and Sanitize the ID
$staff_id = (int)($_GET['staff_id'] ?? 0);

if ($staff_id > 0) {
    // 2. Fetch the image filename before deleting the record
    $select_sql = "SELECT image FROM staff_employee WHERE staff_id = $staff_id";
    $result = mysqli_query($conn, $select_sql);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $image_name = $row['image'];
        $image_path = "employee_image/" . $image_name;

        // 3. Remove physical file if it exists and is not the default
        if (!empty($image_name) && $image_name !== "no-photo.png" && file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // 4. Delete the database record
    $delete_sql = "DELETE FROM staff_employee WHERE staff_id = $staff_id";
    
    if (mysqli_query($conn, $delete_sql)) {
        // Redirect with success message
        header("Location: view_staff.php?msg=delete_success");
        exit;
    } else {
        // Redirect with error message
        header("Location: view_staff.php?msg=delete_error&err=" . urlencode(mysqli_error($conn)));
        exit;
    }
} else {
    // Redirect if no valid ID provided
    header("Location: view_staff.php");
    exit;
}
