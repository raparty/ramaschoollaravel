<?php
declare(strict_types=1);

require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();
$msg = "";

// 1. HANDLE FINE SUBMISSION
if (isset($_POST['submit'])) {
    $reg_no = mysqli_real_escape_string($conn, trim((string)$_POST['registration_no']));
    $book_no = mysqli_real_escape_string($conn, trim((string)$_POST['book_number']));
    $amount = mysqli_real_escape_string($conn, trim((string)$_POST['fine_amount']));
    $session = mysqli_real_escape_string($conn, (string)($_SESSION['session'] ?? ''));
    $date = date('Y-m-d');

    // Verify student exists in 'admissions' before allowing fine entry
    $check_std = mysqli_query($conn, "SELECT id FROM admissions WHERE reg_no = '$reg_no'");
    
    if (mysqli_num_rows($check_std) > 0) {
        // Insert into pluralized table 'student_fine_detail'
        $sql_ins = "INSERT INTO student_fine_detail (registration_no, book_number, fine_amount, session, date) 
                    VALUES ('$reg_no', '$book_no', '$amount', '$session', '$date')";
        
        if (mysqli_query($conn, $sql_ins)) {
            echo "<script>alert('Fine recorded successfully.'); window.location.href='student_fine_detail.php?msg=1';</script>";
            exit;
        } else {
            $msg = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Student Registration No. not found.</div>";
    }
}

// 2. FETCH STUDENT NAME (AJAX-like preview)
$reg_param = mysqli_real_escape_string($conn, (string)($_GET['reg_no'] ?? ''));
$student_name = "";
if ($reg_param) {
    $res_name = mysqli_query($conn, "SELECT student_name FROM admissions WHERE reg_no = '$reg_param'");
    if ($row_n = mysqli_fetch_assoc($res_name)) {
        $student_name = $row_n['student_name'];
    }
}
?>

<?php include_once("includes/library_setting_sidebar.php"); ?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <div class="grid_12">
                <div class="widget_wrap">
                    <h3 style="padding:20px 0 0 20px; color:#0078D4">Record Individual Student Fine</h3>
                    <div class="widget_content" style="padding: 25px;">
                        <?php if($msg != "") echo $msg; ?>
                        
                        <form action="" method="post" class="form_container left_label">
                            <ul>
                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Registration No. <span style="color:red;">*</span></label>
                                        <div class="form_input">
                                            <input name="registration_no" id="reg_no_input" type="text" value="<?php echo htmlspecialchars($reg_param); ?>" 
                                                   placeholder="e.g. ADM-2026-001" required style="width:250px;" 
                                                   onblur="if(this.value != '') window.location.href='?reg_no='+this.value;" />
                                            <span class="label_intro">Press TAB to verify student name</span>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Student Name</label>
                                        <div class="form_input">
                                            <input type="text" value="<?php echo htmlspecialchars($student_name); ?>" readonly 
                                                   placeholder="Auto-fills after verification" style="background:#f9f9f9; width:350px;" />
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Book Number <span style="color:red;">*</span></label>
                                        <div class="form_input">
                                            <input name="book_number" type="text" placeholder="e.g. LIB-502" required style="width:250px;" />
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title">Fine Amount (₹) <span style="color:red;">*</span></label>
                                        <div class="form_input">
                                            <input name="fine_amount" type="number" step="0.01" placeholder="0.00" required style="width:250px;" />
                                        </div>
                                    </div>
                                </li>

                                <li style="margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px;">
                                    <div class="form_input">
                                        <button type="submit" name="submit" class="btn_small btn_blue"><span>Save Fine Entry</span></button>
                                        <a href="student_fine_detail.php" class="btn_small btn_orange" style="margin-left:10px;"><span>Back to List</span></a>
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
