<?php
declare(strict_types=1);

/**
 * ID 2.1: Add Fees Term
 * Fix: Absolute override of legacy grid to fix sidebar/search distortion
 */

require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

// 1. Logic for Form Submission
$msg = "";
if(isset($_POST['submit'])) {
    $term_name = trim((string)($_POST['term_name'] ?? ''));
    $term_name_safe = db_escape($term_name);
    
    $check_sql = "SELECT * FROM fees_term WHERE term_name='$term_name_safe'";
    $check_res = db_query($check_sql);
    
    if(db_num_rows($check_res) == 0 && !empty($term_name_safe)) {
        $insert_sql = "INSERT INTO fees_term(term_name) VALUES ('$term_name_safe')";
        db_query($insert_sql);
        header("Location:term_manager.php?msg=1");
        exit;
    } else {
        $msg = "<div style='color: #a94442; background-color: #f2dede; border: 1px solid #ebccd1; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>Error: Term already exists or name is empty.</div>";
    }
}
?>

<div style="margin-left: 280px !important; padding: 40px !important; display: block !important; position: relative !important; clear: both !important;">
    
    <div style="margin-bottom: 30px;">
        <h3 style="color: #1c75bc; font-size: 28px; font-weight: 400; margin: 0;">Add Fees Term</h3>
        <hr style="border: 0; border-top: 1px solid #eee; margin-top: 10px;">
    </div>

    <?php if($msg != "") echo $msg; ?>

    <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); max-width: 600px;">
        <form action="add_term.php" method="post">
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; margin-bottom: 10px; color: #333; font-size: 14px;">Term Name <span style="color:red;">*</span></label>
                <input name="term_name" type="text" placeholder="e.g. Quarter 1 / First Installment" required 
                       style="width: 100%; padding: 12px; border: 1px solid #ccd1d6; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                <small style="display: block; color: #6c757d; margin-top: 8px; font-size: 12px;">This name will appear on student fee statements.</small>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px; border-top: 1px solid #f1f1f1; padding-top: 25px;">
                <button type="submit" name="submit" class="btn-fluent-primary" 
                        style="background: #0078d4; color: white; border: none; padding: 12px 30px; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 14px;">
                    Save Term
                </button>
                <a href="term_manager.php" 
                   style="background: #f3f2f1; color: #333; text-decoration: none; padding: 12px 30px; border-radius: 4px; font-weight: 600; border: 1px solid #ccc; font-size: 14px; display: inline-block;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once("includes/footer.php"); ?>
