<?php
declare(strict_types=1);

/**
 * ID 1.1: Student Profile View
 * Comprehensive view of all fields from add_admission.php
 */
require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");

// 1. Data Retrieval Logic
$student_id = (int)($_GET['student_id'] ?? 0);

if ($student_id <= 0) {
    echo "<div class='alert alert-danger'>Invalid Student ID. <a href='student_detail.php'>Return to Directory</a></div>";
    require_once("includes/footer.php");
    exit;
}

// Optimized query to join class names
$sql = "SELECT a.*, c.class_name 
        FROM admissions a 
        LEFT JOIN classes c ON a.class_id = c.id 
        WHERE a.id = $student_id LIMIT 1";
$res = db_query($sql);
$row = db_fetch_array($res);

if (!$row) {
    echo "<div class='alert alert-danger'>Student record not found.</div>";
    require_once("includes/footer.php");
    exit;
}

$photo = !empty($row['student_pic']) ? $row['student_pic'] : 'assets/images/no-photo.png';
?>

<div class="grid_container">
    <div class="page_title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3>
            <span style="color: var(--app-muted); font-size: 14px; display: block; text-transform: uppercase;">Admissions / Directory /</span>
            <?php echo htmlspecialchars($row['student_name']); ?>
        </h3>
        <a href="student_detail.php" class="btn-outline-secondary">
            <svg viewBox="0 0 24 24" width="16" height="16" style="margin-right: 5px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
            Back to Directory
        </a>
    </div>

    <div class="row" style="display: flex; gap: 24px;">
        <div style="flex: 0 0 300px;">
            <div class="azure-card" style="text-align: center; padding: 30px;">
                <img src="<?php echo $photo; ?>" alt="Profile" style="width: 160px; height: 160px; border-radius: 12px; border: 1px solid var(--app-border); margin-bottom: 20px; object-fit: cover; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <h4 style="margin-bottom: 5px; color: var(--fluent-slate);"><?php echo htmlspecialchars($row['student_name']); ?></h4>
                <p class="fluent-badge-outline" style="display: inline-block; margin-bottom: 20px;"><?php echo htmlspecialchars($row['reg_no']); ?></p>
                
                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px;">
                    <a href="edit_admission.php?student_id=<?php echo $student_id; ?>" class="btn-fluent-primary">Edit Full Profile</a>
                    <a href="student_tc.php?registration_no=<?php echo $row['reg_no']; ?>" class="btn-outline-secondary" style="justify-content: center;">Generate TC</a>
                </div>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="azure-card">
                <div class="widget_top">
                    <h6 class="fluent-card-header">Comprehensive Student Dossier</h6>
                </div>
                <div class="widget_content" style="padding: 0;">
                    <table class="fluent-data-table">
                        <tr><th colspan="4" class="section-divider">Academic Placement</th></tr>
                        <tr>
                            <td class="label">Current Class</td>
                            <td class="value"><?php echo htmlspecialchars($row['class_name'] ?? 'N/A'); ?></td>
                            <td class="label">Admission Date</td>
                            <td class="value"><?php echo !empty($row['admission_date']) ? date('d-M-Y', strtotime($row['admission_date'])) : 'N/A'; ?></td>
                        </tr>

                        <tr><th colspan="4" class="section-divider">Personal Identity</th></tr>
                        <tr>
                            <td class="label">Date of Birth</td>
                            <td class="value"><?php echo !empty($row['dob']) ? date('d-M-Y', strtotime($row['dob'])) : 'N/A'; ?></td>
                            <td class="label">Gender</td>
                            <td class="value"><?php echo htmlspecialchars($row['gender']); ?></td>
                        </tr>
                        <tr>
                            <td class="label">Aadhaar Number</td>
                            <td class="value" style="letter-spacing: 1px;"><?php echo htmlspecialchars($row['aadhaar_no'] ?? 'N/A'); ?></td>
                            <td class="label">Blood Group</td>
                            <td class="value"><?php echo htmlspecialchars($row['blood_group'] ?? 'N/A'); ?></td>
                        </tr>

                        <tr><th colspan="4" class="section-divider">Guardian Information</th></tr>
                        <tr>
                            <td class="label">Guardian Name</td>
                            <td class="value"><?php echo htmlspecialchars($row['guardian_name'] ?? 'N/A'); ?></td>
                            <td class="label">Contact Number</td>
                            <td class="value"><?php echo htmlspecialchars($row['guardian_phone'] ?? 'N/A'); ?></td>
                        </tr>

                        <tr><th colspan="4" class="section-divider">Academic History</th></tr>
                        <tr>
                            <td class="label">Past School Details</td>
                            <td class="value" colspan="3" style="line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($row['past_school_info'] ?? 'No previous school data recorded.')); ?>
                            </td>
                        </tr>

                        <tr><th colspan="4" class="section-divider">Uploaded Documents</th></tr>
                        <tr>
                            <td class="label">Aadhaar Document</td>
                            <td class="value" colspan="3">
                                <?php if (!empty($row['aadhaar_doc_path'])): ?>
                                    <a href="<?php echo $row['aadhaar_doc_path']; ?>" target="_blank" class="btn-outline-secondary" style="display: inline-flex; padding: 4px 12px; font-size: 11px;">
                                        View Aadhaar PDF
                                    </a>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-style: italic;">No document uploaded.</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fluent-data-table { width: 100%; border-collapse: collapse; }
    .fluent-data-table td { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; }
    .fluent-data-table td.label { width: 20%; color: #64748b; font-size: 12px; font-weight: 600; background: #f8fafc; text-transform: uppercase; }
    .fluent-data-table td.value { width: 30%; color: #1e293b; font-weight: 500; font-size: 14px; }
    .section-divider { background: #eff6fc; color: var(--app-primary); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 10px 20px !important; text-align: left; }
    .fluent-badge-outline { background: #f0f7ff; color: var(--app-primary); padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 700; border: 1px solid #e0e7ff; }
</style>

<?php require_once("includes/footer.php"); ?>
