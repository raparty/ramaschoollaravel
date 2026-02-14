<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = Database::connection(); 

    // Sanitize Inputs
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $admission_date = mysqli_real_escape_string($conn, $_POST['admission_date']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group'] ?? '');
    $class_id = (int)$_POST['class_id'];
    $aadhaar_no = mysqli_real_escape_string($conn, $_POST['aadhaar_no']);
    $guardian_name = mysqli_real_escape_string($conn, $_POST['guardian_name']);
    $guardian_phone = mysqli_real_escape_string($conn, $_POST['guardian_phone']);
    $past_school = mysqli_real_escape_string($conn, $_POST['past_school_info']);
    
    // Generate Registration Number
    $year = date('Y');
    $count_query = mysqli_query($conn, "SELECT COUNT(id) as total FROM admissions");
    $count_row = mysqli_fetch_assoc($count_query);
    $next_id = ($count_row['total'] ?? 0) + 1;
    $reg_no = "ADM-" . $year . "-" . str_pad((string)$next_id, 3, '0', STR_PAD_LEFT);

    // File Handling Logic
    $photo_path = "";
    $doc_path = "";
    $upload_dir = "uploads/students/";

    if (!empty($_FILES['student_pic']['name'])) {
        $photo_path = $upload_dir . "photos/" . $reg_no . "_photo." . pathinfo($_FILES['student_pic']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['student_pic']['tmp_name'], $photo_path);
    }

    if (!empty($_FILES['aadhaar_doc']['name'])) {
        $doc_path = $upload_dir . "documents/" . $reg_no . "_aadhaar.pdf";
        move_uploaded_file($_FILES['aadhaar_doc']['tmp_name'], $doc_path);
    }

    $sql = "INSERT INTO admissions (reg_no, student_name, student_pic, dob, gender, blood_group, class_id, admission_date, aadhaar_no, aadhaar_doc_path, guardian_name, guardian_phone, past_school_info) 
            VALUES ('$reg_no', '$student_name', '$photo_path', '$dob', '$gender', '$blood_group', $class_id, '$admission_date', '$aadhaar_no', '$doc_path', '$guardian_name', '$guardian_phone', '$past_school')";

    if (mysqli_query($conn, $sql)) {
        header("Location: student_detail.php?msg=success&reg=" . $reg_no);
        exit;
    } else {
        die("Database Error: " . mysqli_error($conn));
    }
}
?>
