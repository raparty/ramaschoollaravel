<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();

// 1. Capture the 'registration_no' passed from view_student_detail.php
$reg_no_param = mysqli_real_escape_string($conn, $_GET['registration_no'] ?? '');

if (empty($reg_no_param)) {
    echo "<div class='alert alert-danger'>Error: No Registration Number provided.</div>";
    include_once("includes/footer.php");
    exit;
}

// 2. Query 'admissions' using the correct column name 'reg_no'
$sql = "SELECT a.*, c.class_name 
        FROM admissions a 
        LEFT JOIN classes c ON a.class_id = c.id 
        WHERE a.reg_no = '$reg_no_param' LIMIT 1";

$res = db_query($sql);
$row = db_fetch_array($res);

if (!$row) {
    echo "<div class='alert alert-danger'>Error: Student with Reg No [$reg_no_param] not found in Admissions.</div>";
    include_once("includes/footer.php");
    exit;
}
?>

<div id="container">
    <div class="grid_container">
        <div class="widget_wrap azure-card">
            <div class="widget_top">
                <h6 class="fluent-card-header">Transfer Certificate (TC) Generation</h6>
            </div>
            <div class="widget_content" style="padding: 40px; background: #fff;">
                <div id="printableTC" style="border: 5px double #333; padding: 30px; line-height: 2;">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h2 style="margin:0;">SCHOOL LEAVING CERTIFICATE</h2>
                        <p style="margin:0;">(Transfer Certificate)</p>
                    </div>

                    <p>This is to certify that <strong><?php echo htmlspecialchars($row['student_name']); ?></strong> 
                    Registration No: <strong><?php echo htmlspecialchars($row['reg_no']); ?></strong> 
                    son/daughter of <strong><?php echo htmlspecialchars($row['guardian_name']); ?></strong> 
                    was a student of this institution.</p>

                    <p>He/She was admitted to Class <strong><?php echo htmlspecialchars($row['class_name'] ?? 'N/A'); ?></strong> 
                    on date <strong><?php echo date('d-M-Y', strtotime($row['admission_date'])); ?></strong>.</p>
                    
                    <p>His/Her Date of Birth according to the Admission Register is 
                    <strong><?php echo date('d-M-Y', strtotime($row['dob'])); ?></strong>.</p>

                    <div style="margin-top: 50px; display: flex; justify-content: space-between;">
                        <span>Date: <?php echo date('d-M-Y'); ?></span>
                        <span style="border-top: 1px solid #000; padding-top: 5px; min-width: 150px; text-align: center;">Principal Signature</span>
                    </div>
                </div>

                <div style="margin-top: 25px; text-align: right;" class="no-print">
                    <button onclick="window.print();" class="btn-fluent-primary">Print Certificate</button>
                    <a href="view_student_detail.php?student_id=<?php echo $row['id']; ?>" class="btn-outline-secondary" style="margin-left:10px;">Back to Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, #sidebar, #header, .page_title { display: none !important; }
    #container { margin: 0; padding: 0; }
    .azure-card { border: none !important; box-shadow: none !important; }
}
</style>

<?php include_once("includes/footer.php"); ?>
