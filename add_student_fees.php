<?php
// 1. Force Error Reporting to stop the blank page
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

$conn = Database::connection(); // Get active DB connection

// 2. Logic to process payment
$msg = "";
if (isset($_POST['submit'])) {
    $reg_no = mysqli_real_escape_string($conn, $_POST['registration_no']);
    $amount = (float)$_POST['fees_amount'];
    $pending = (float)$_POST['pending_amount'];
    $term_id = (int)$_POST['fees_term'];

    if ($amount > 0 && $amount <= $pending) {
        $res_max = mysqli_query($conn, "SELECT MAX(id) as max_id FROM student_fees_detail");
        $max_row = mysqli_fetch_assoc($res_max);
        $receipt_no = "FEES-" . (($max_row['max_id'] ?? 0) + 1001);

        $sql_in = "INSERT INTO student_fees_detail(registration_no, reciept_no, fees_term, fees_amount, payment_date) 
                   VALUES ('$reg_no', '$receipt_no', '$term_id', '$amount', NOW())";
        
        if (mysqli_query($conn, $sql_in)) {
            echo "<script>window.location='fees_receipt.php?receipt_no=$receipt_no';</script>";
            exit;
        }
    } else {
        $msg = "<div style='color:red; font-weight:bold; margin-bottom:20px;'>Error: Amount exceeds pending balance or is zero.</div>";
    }
}

// 3. Fetch student from 'admissions' table
$reg_no = $_REQUEST['registration_no'] ?? '';
$student = null;
$pending_balance = 0;

if (!empty($reg_no)) {
    $safe_reg = mysqli_real_escape_string($conn, $reg_no);
    $sql = "SELECT a.*, c.class_name FROM admissions a 
            LEFT JOIN classes c ON a.class_id = c.id 
            WHERE a.reg_no = '$safe_reg' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $student = mysqli_fetch_assoc($result);

    if ($student) {
        // Calculate Total Paid
        $paid_res = mysqli_query($conn, "SELECT SUM(fees_amount) as paid FROM student_fees_detail WHERE registration_no = '$safe_reg'");
        $paid_row = mysqli_fetch_assoc($paid_res);
        $total_paid = (float)($paid_row['paid'] ?? 0);
        
        // Use 'admission_fee' as total package from admissions module
        $total_package = (float)($student['admission_fee'] ?? 0);
        $pending_balance = $total_package - $total_paid;
    }
}
?>

<div style="margin-left: 280px !important; padding: 40px !important; display: block !important;">
    <div class="page_title" style="margin-bottom: 25px;">
        <h3 style="color: #1c75bc; font-size: 26px;">Fee Collection Form</h3>
    </div>

    <?php echo $msg; ?>

    <?php if (!$student): ?>
        <div class="azure-card" style="padding: 30px; border: 1px solid #ffcc00; background: #fffdf2;">
            <strong>No student selected.</strong> Please search for a student first.
        </div>
    <?php else: ?>
        <div class="azure-card" style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 30px;">
            <form action="add_student_fees.php" method="post">
                <input type="hidden" name="registration_no" value="<?php echo htmlspecialchars($reg_no); ?>">
                <input type="hidden" name="pending_amount" value="<?php echo $pending_balance; ?>">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div>
                        <label style="font-weight:600; display:block; margin-bottom:5px;">Registration No</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($reg_no); ?>" readonly style="background:#f1f5f9;">
                    </div>
                    <div>
                        <label style="font-weight:600; display:block; margin-bottom:5px;">Student Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['student_name']); ?>" readonly style="background:#f1f5f9;">
                    </div>
                    <div>
                        <label style="font-weight:600; display:block; margin-bottom:5px;">Class</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?>" readonly style="background:#f1f5f9;">
                    </div>
                    <div>
                        <label style="font-weight:600; color:red; display:block; margin-bottom:5px;">Pending Balance</label>
                        <input type="text" class="form-control" value="<?php echo number_format($pending_balance, 2); ?>" readonly style="background:#fff5f5; border-color:#feb2b2; font-weight:700;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; border-top: 1px solid #eee; padding-top: 25px;">
                    <div class="form_group">
                        <label style="font-weight:600; display:block; margin-bottom:5px;">Fee Term <span style="color:red;">*</span></label>
                        <select name="fees_term" class="form-control" required>
                            <option value="">-- Select Term --</option>
                            <?php
                            $terms = mysqli_query($conn, "SELECT * FROM fees_term");
                            while ($t = mysqli_fetch_assoc($terms)) {
                                echo "<option value='{$t['id']}'>{$t['term_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form_group">
                        <label style="font-weight:600; display:block; margin-bottom:5px;">Amount to Pay <span style="color:red;">*</span></label>
                        <input type="number" name="fees_amount" step="0.01" class="form-control" placeholder="0.00" required>
                    </div>
                </div>

                <div style="margin-top: 40px; text-align: right;">
                    <button type="submit" name="submit" class="btn-fluent-primary" style="padding: 12px 40px;">Post Payment</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<style>
    .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
</style>

<?php require_once("includes/footer.php"); ?>
