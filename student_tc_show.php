<?php
// 1. Error Reporting
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once("includes/bootstrap.php");

// 2. Database Connection
$conn = Database::connection(); 
if (!$conn) {
    die("Database connection failed.");
}

// 3. Capture POST Data
$reg_no = $_POST['registration_no'] ?? '';
$removal_date = $_POST['removal_date'] ?? date('Y-m-d');
$removal_cause = $_POST['removal_cause'] ?? 'Course Completion';
$conduct = $_POST['conduct'] ?? 'Good';

if (empty($reg_no)) {
    die("Error: No registration number received.");
}

// 4. Fetch Student Details (admissions table)
$safe_reg = mysqli_real_escape_string($conn, $reg_no);
$sql = "SELECT a.*, c.class_name 
        FROM admissions a 
        LEFT JOIN classes c ON a.class_id = c.id 
        WHERE a.reg_no = '$safe_reg' LIMIT 1";

$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Error: Student record not found for Reg No: " . htmlspecialchars($reg_no));
}

// 5. FIXED: Fail-Safe School Details
// We check if table exists first; if not, we use defaults to prevent the Fatal Error
$school = ['school_name' => 'Your School Name', 'school_address' => 'Your School Address'];
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'school_detail'");
if (mysqli_num_rows($check_table) > 0) {
    $school_res = mysqli_query($conn, "SELECT * FROM school_detail LIMIT 1");
    if ($school_data = mysqli_fetch_assoc($school_res)) {
        $school = $school_data;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TC_<?php echo htmlspecialchars($reg_no); ?></title>
    <style>
        body { font-family: 'Times New Roman', serif; background: #525659; margin: 0; padding: 40px; display: flex; justify-content: center; }
        .certificate-paper { 
            background: white; width: 210mm; min-height: 297mm; padding: 50px; 
            box-sizing: border-box; border: 15px double #333; box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        .header { text-align: center; border-bottom: 3px solid #333; padding-bottom: 15px; margin-bottom: 40px; }
        .school-name { font-size: 36px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .tc-heading { font-size: 28px; text-decoration: underline; margin: 25px 0; font-weight: bold; }
        .content-table { width: 100%; border-collapse: collapse; }
        .content-table td { padding: 18px 5px; font-size: 19px; line-height: 1.5; border-bottom: 1px dotted #ccc; }
        .label { width: 45%; }
        .value { font-weight: bold; text-transform: uppercase; }
        .footer-section { margin-top: 100px; display: flex; justify-content: space-between; text-align: center; }
        .sig-box { width: 200px; border-top: 2px solid #000; padding-top: 8px; font-weight: bold; }
        @media print {
            body { background: none; padding: 0; }
            .certificate-paper { box-shadow: none; border: 10px double #000; margin: 0; width: 100%; }
            .print-btn { display: none; }
        }
        .print-btn {
            position: fixed; top: 20px; right: 20px; background: #0078d4; color: white;
            padding: 15px 30px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">PRINT / SAVE AS PDF</button>

    <div class="certificate-paper">
        <div class="header">
            <h1 class="school-name"><?php echo htmlspecialchars($school['school_name']); ?></h1>
            <p style="font-size: 16px; margin: 10px 0;"><?php echo htmlspecialchars($school['school_address']); ?></p>
            <div class="tc-heading">TRANSFER CERTIFICATE</div>
        </div>

        <table class="content-table">
            <tr><td class="label">Admission / Scholar No:</td><td class="value"><?php echo htmlspecialchars($student['reg_no']); ?></td></tr>
            <tr><td class="label">Name of the Pupil:</td><td class="value"><?php echo htmlspecialchars($student['student_name']); ?></td></tr>
            <tr><td class="label">Father's / Guardian's Name:</td><td class="value"><?php echo htmlspecialchars($student['guardian_name'] ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Date of Birth:</td><td class="value"><?php echo date('d-M-Y', strtotime($student['dob'])); ?></td></tr>
            <tr><td class="label">Date of Admission:</td><td class="value"><?php echo date('d-M-Y', strtotime($student['admission_date'])); ?></td></tr>
            <tr><td class="label">Class Last Studied:</td><td class="value"><?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?></td></tr>
            <tr><td class="label">Date of Removal from Rolls:</td><td class="value"><?php echo date('d-M-Y', strtotime($removal_date)); ?></td></tr>
            <tr><td class="label">Reason for Leaving:</td><td class="value"><?php echo htmlspecialchars($removal_cause); ?></td></tr>
            <tr><td class="label">Conduct and Character:</td><td class="value"><?php echo htmlspecialchars($conduct); ?></td></tr>
        </table>

        <div style="margin-top: 60px; font-size: 18px; font-style: italic;">
            Certified that the above information is in accordance with the School Records.
        </div>

        <div class="footer-section">
            <div class="sig-box">Prepared By</div>
            <div class="sig-box">Office Clerk</div>
            <div class="sig-box">Principal Signature</div>
        </div>
    </div>
</body>
</html>
