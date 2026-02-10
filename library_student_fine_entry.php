<?php
// 1. DO NOT add session_start() here if it is already in bootstrap.php
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/library_setting_sidebar.php");

$conn = Database::connection();
$msg = "";

// 2. CHECK IF TABLE EXISTS BEFORE RUNNING
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'student_fine_detail'");
$table_exists = (mysqli_num_rows($table_check) > 0);

if (!$table_exists) {
    // If student_fine_detail is missing, try checking for 'student_fine_details'
    $alt_check = mysqli_query($conn, "SHOW TABLES LIKE 'student_fine_details'");
    if (mysqli_num_rows($alt_check) > 0) {
        $active_table = "student_fine_details";
        $table_exists = true;
    } else {
        $msg = "<div style='color:red; background:#fee; padding:15px; border:1px solid red; margin-bottom:20px;'>
                    <strong>CRITICAL ERROR:</strong> Neither 'student_fine_detail' nor 'student_fine_details' exists in the database. 
                    Please check your table names.
                </div>";
    }
} else {
    $active_table = "student_fine_detail";
}

// 3. DATA PROCESSING
if (isset($_POST['save_fine']) && $table_exists) {
    $reg_no = mysqli_real_escape_string($conn, trim((string)$_POST['registration_no']));
    $book_no = mysqli_real_escape_string($conn, trim((string)$_POST['book_number']));
    $amount = (float)$_POST['fine_amount'];
    
    // Safety check for session variable
    $session = mysqli_real_escape_string($conn, (string)($_SESSION['session'] ?? '2025-2026'));

    // Verify Student
    $sql_check = "SELECT student_name FROM admissions WHERE reg_no = '$reg_no' LIMIT 1";
    $res_check = mysqli_query($conn, $sql_check);

    if ($res_check && mysqli_num_rows($res_check) > 0) {
        $student = mysqli_fetch_assoc($res_check);
        
        // Insert using the dynamically found table name
        $sql_ins = "INSERT INTO $active_table (registration_no, book_number, fine_amount, session, date) 
                    VALUES ('$reg_no', '$book_no', '$amount', '$session', NOW())";
        
        if (mysqli_query($conn, $sql_ins)) {
            $msg = "<div style='color:green; background:#e6fffa; padding:15px; border:1px solid green; margin-bottom:20px;'>
                        <strong>Success!</strong> Fine recorded for <strong>" . $student['student_name'] . "</strong>.
                    </div>";
        } else {
            $msg = "<div style='color:red;'>SQL Error: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $msg = "<div style='color:red; padding:15px; border:1px solid red;'>Error: Student ID '$reg_no' not found in Admissions.</div>";
    }
}
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding:15px 0 0 20px; color:#0078D4">Library Fine Management</h3>
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_content" style="padding: 25px;">
                        <?php if($msg != "") echo $msg; ?>
                        
                        <form action="" method="post" class="form_container left_label">
                            <ul>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">S.R. Number (Reg No)</label>
                                        <div class="form_input">
                                            <input name="registration_no" type="text" required style="width:300px;" />
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Book Number</label>
                                        <div class="form_input">
                                            <input name="book_number" type="text" required style="width:300px;" />
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Fine Amount (₹)</label>
                                        <div class="form_input">
                                            <input name="fine_amount" type="number" step="0.01" required style="width:300px;" />
                                        </div>
                                    </div>
                                </li>
                                <li style="margin-top: 20px;">
                                    <div class="form_input">
                                        <button type="submit" name="save_fine" class="btn_small btn_blue"><span>Save Fine Record</span></button>
                                        <a href="student_fine_detail.php" class="btn_small btn_orange" style="margin-left:10px;"><span>View Records</span></a>
                                    </div>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("includes/footer.php"); ?>
