<?php
declare(strict_types=1);

/**
 * ID 2.3: Add Student Fees
 * Fix: Table sync to 'admissions' and corrected CSS overlap
 */
require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

$conn = Database::connection();
$msg = "";
$registration_no = $_REQUEST['registration_no'] ?? '';

// 1. Process Fee Payment
if(isset($_POST['submit'])) {
    $reg_no = mysqli_real_escape_string($conn, $_POST['registration_no']);
    $amount = (float)$_POST['fees_amount'];
    $pending = (float)$_POST['pending_amount'];
    $term_id = (int)$_POST['fees_term'];

    if($pending >= $amount && $amount > 0) {
        // Receipt Generation Logic
        $res_max = mysqli_query($conn, "SELECT MAX(id) as max_id FROM student_fees_detail");
        $max_row = mysqli_fetch_assoc($res_max);
        $receipt_no = "FEES-" . ($max_row['max_id'] + 1001);

        $sql_in = "INSERT INTO student_fees_detail(registration_no, reciept_no, fees_term, fees_amount, payment_date) 
                   VALUES ('$reg_no', '$receipt_no', '$term_id', '$amount', NOW())";
        
        if(mysqli_query($conn, $sql_in)) {
            header("Location: fees_receipt.php?receipt_no=$receipt_no");
            exit;
        }
    } else {
        $msg = "<div class='alert-error'>Invalid amount: Deposit exceeds pending balance.</div>";
    }
}

// 2. Fetch Student Data from 'admissions' table
$student = null;
if(!empty($registration_no)) {
    $sql_s = "SELECT a.*, c.class_name 
              FROM admissions a 
              LEFT JOIN classes c ON a.class_id = c.id 
              WHERE a.reg_no = '".mysqli_real_escape_string($conn, $registration_no)."'";
    $student = mysqli_fetch_assoc(mysqli_query($conn, $sql_s));

    // Calculate Paid Fees
    $sql_p = "SELECT SUM(fees_amount) as paid FROM student_fees_detail WHERE registration_no = '".mysqli_real_escape_string($conn, $registration_no)."'";
    $paid_row = mysqli_fetch_assoc(mysqli_query($conn, $sql_p));
    $total_paid = (float)($paid_row['paid'] ?? 0);
    
    // Assuming 'admission_fee' field in admissions stores the total package amount
    $total_package = (float)($student['admission_fee'] ?? 0);
    $pending_balance = $total_package - $total_paid;
}
?>

<div style="margin-left: 280px !important; padding: 40px !important; display: block !important;">
    
    <div class="page_title" style="margin-bottom: 25px;">
        <h3 style="color: #1c75bc; font-size: 26px; font-weight: 400;">Fee Collection</h3>
    </div>

    <?php if($msg != "") echo $msg; ?>

    <div class="azure-card" style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 0; overflow: hidden; max-width: 900px;">
        <div style="background: #f8fafc; padding: 15px 25px; border-bottom: 1px solid #e2e8f0;">
            <h6 style="margin:0; color:#64748b;">RECORD NEW PAYMENT</h6>
        </div>

        <div style="padding: 30px;">
            <form action="add_student_fees.php" method="post">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
                    
                    <div class="form_group">
                        <label style="display:block; font-weight:600; margin-bottom:8px;">Registration No.</label>
                        <input name="registration_no" type="text" class="fluent-input" value="<?php echo htmlspecialchars($registration_no); ?>" readonly style="background:#f1f5f9;">
                    </div>

                    <div class="form_group">
                        <label style="display:block; font-weight:600; margin-bottom:8px;">Student Name</label>
                        <input type="text" class="fluent-input" value="<?php echo htmlspecialchars($student['student_name'] ?? ''); ?>" readonly style="background:#f1f5f9;">
                    </div>

                    <div class="form_group">
                        <label style="display:block; font-weight:600; margin-bottom:8px;">Class</label>
                        <input type="text" class="fluent-input" value="<?php echo htmlspecialchars($student['class_name'] ?? ''); ?>" readonly style="background:#f1f5f9;">
                    </div>

                    <div class="form_group">
                        <label style="display:block; font-weight:600; margin-bottom:8px;">Current Balance (Pending)</label>
                        <input name="pending_amount" type="text" class="fluent-input" value="<?php echo $pending_balance; ?>" readonly style="background: #fff5f5; border-color: #feb2b2; color: #c53030; font-weight: 700;">
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 25px;">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                    <div class="form_group">
                        <label style="display:block; font-weight:600; margin-bottom:8px;">Fees Term <span style="color:red;">*</span></label>
                        <select name="fees_term" class="fluent-input" required>
                            <option value="">- Select Term -</option>
                            <?php
                            $terms = mysqli_query($conn, "SELECT * FROM fees_term");
                            while($t = mysqli_fetch_assoc($terms)) {
                                echo "<option value='{$t['id']}'>{$t['term_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form_group">
                        <label style="display:block; font-weight:600; margin-bottom:8px;">Amount to Deposit <span style="color:red;">*</span></label>
                        <input name="fees_amount" type="number" step="0.01" class="fluent-input" placeholder="0.00" required style="border-color: #0078d4;">
                    </div>
                </div>

                <div style="margin-top: 40px; text-align: right; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    <button type="submit" name="submit" class="btn-fluent-primary" style="padding: 12px 40px;">Post Payment & Print Receipt</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .fluent-input { width: 100%; padding: 12px; border: 1px solid #ccd1d6; border-radius: 4px; box-sizing: border-box; }
    .alert-error { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; padding: 15px; border-radius: 4px; margin-bottom: 25px; font-weight: 600; }
</style>

<?php require_once("includes/footer.php"); ?>
