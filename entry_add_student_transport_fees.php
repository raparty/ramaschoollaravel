<?php
declare(strict_types=1);

/**
 * ID 2.4: Add Student Bus Fees
 * Fix: Fluid Layout to resolve fixed-width container distortion
 */

require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

// 1. Process Logic
if(isset($_POST['entry_submit'])){
    $_SESSION['class_id'] = $_POST['class_id'];
    $_SESSION['stream_id'] = $_POST['stream'];
    echo "<script>window.location='add_student_transport_fees.php';</script>";
    exit;
}
?>

<style>
    /* Kill any legacy fixed widths that cause centering/overlap */
    #container, #content, .grid_container, .grid_12, .widget_wrap { 
        width: 100% !important; 
        max-width: none !important;
        margin: 0 !important; 
        padding: 0 !important; 
        float: none !important;
        display: block !important;
    }

    /* Modern Fluid Wrapper starting after the sidebar */
    .fluid-layout-engine {
        margin-left: 270px !important; 
        padding: 40px !important;
        background-color: #f8f9fa;
        min-height: 100vh;
        box-sizing: border-box !important;
    }

    .modern-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 40px;
        max-width: 900px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .form-flex {
        display: flex;
        gap: 25px;
        margin-bottom: 30px;
    }

    .form-col { flex: 1; }

    label { font-weight: 600; display: block; margin-bottom: 10px; color: #334155; font-size: 14px; }
    
    select { 
        width: 100%; 
        padding: 12px; 
        border: 1px solid #cbd5e1; 
        border-radius: 6px; 
        background-color: #fff;
        font-size: 14px;
        color: #1e293b;
    }

    .btn-save { background: #0078d4; color: white; padding: 12px 35px; border-radius: 6px; border: none; font-weight: 600; cursor: pointer; }
    .btn-back { background: #f1f5f9; color: #475569; padding: 12px 35px; border-radius: 6px; border: 1px solid #e2e8f0; text-decoration: none; font-weight: 600; display: inline-block; }
</style>

<div class="fluid-layout-engine">
    
    <div style="margin-bottom: 40px;">
        <?php include_once("includes/fees_setting_sidebar.php"); ?>
    </div>

    <div style="margin-bottom: 30px;">
        <h3 style="color: #1e293b; font-size: 28px; font-weight: 600; margin: 0;">Add Student Bus Fees</h3>
        <p style="color: #64748b; margin-top: 5px;">Configure transportation requirements for the current session.</p>
    </div>

    <div class="modern-card">
        <form action="entry_add_student_transport_fees.php" method="post">
            <div class="form-flex">
                
                <div class="form-col">
                    <label>Target Class <span style="color:red;">*</span></label>
                    <select name="class_id" required onchange="getForm('ajax_stream_code1.php?class_id='+this.value)">
                        <option value="">- Select Class -</option>
                        <?php
                        $res = db_query("SELECT * FROM class");
                        while($row = db_fetch_array($res)) {
                            echo "<option value='{$row['class_id']}'>".htmlspecialchars($row['class_name'])."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-col" id="stream_code">
                    <label>Academic Stream</label>
                    <select name="stream">
                        <option value="">- Select Stream (Optional) -</option>
                    </select>
                </div>

            </div>

            <div style="margin-top: 35px; padding-top: 25px; border-top: 1px solid #f1f5f9; display: flex; gap: 15px;">
                <button type="submit" name="entry_submit" class="btn-save">Continue</button>
                <a href="transport_student_detail.php" class="btn-back">Back to List</a>
            </div>
        </form>
    </div>
</div>

<?php require_once("includes/footer.php"); ?>
