<?php
declare(strict_types=1);

/**
 * ID 1.3: Process Edit Admission
 * Backend handler for updating student records
 */
require_once("includes/bootstrap.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = Database::connection(); 

    // 1. Capture and Sanitize Inputs
    $student_id = (int)$_POST['student_id'];
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $guardian_name = mysqli_real_escape_string($conn, $_POST['guardian_name']);
    $guardian_phone = mysqli_real_escape_string($conn, $_POST['guardian_phone']);
    $aadhaar_no = mysqli_real_escape_string($conn, $_POST['aadhaar_no']);

    // 2. Perform Update Query
    // Note: reg_no is excluded as it is read-only
    $sql = "UPDATE admissions SET 
                student_name = '$student_name',
                dob = '$dob',
                gender = '$gender',
                guardian_name = '$guardian_name',
                guardian_phone = '$guardian_phone',
                aadhaar_no = '$aadhaar_no'
            WHERE id = $student_id";

    if (mysqli_query($conn, $sql)) {
        // 3. Success Redirect
        header("Location: student_detail.php?msg=update_success");
        exit;
    } else {
        // Error handling for database failures
        die("Database Error: " . mysqli_error($conn));
    }
} else {
    // Security: Prevent direct browser access to this file
    header("Location: student_detail.php");
    exit;
}
?>
