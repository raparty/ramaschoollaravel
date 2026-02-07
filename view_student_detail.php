<?php
declare(strict_types=1);

/**
 * ID 1.1: Student Profile View
 * Group A: Admissions Management
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
    <div class="page_title" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>
            <span style="color: var(--app-muted); font-size: 14px; display: block; text-transform: uppercase;">Admissions / Student Directory /</span>
            Student Profile
        </h3>
        <a href="student_detail.php" class="btn-outline-secondary">
            <svg viewBox="0 0 24 24" width="16" height="16" style="margin-right: 5px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
            Back to Directory
        </a>
    </div>

    <div class="row" style="display: flex; gap: 24px; margin-top: 20px;">
        <div style="flex: 0 0 300px;">
            <div class="azure-card" style="text-align: center; padding: 30px;">
                <img src="<?php echo $photo; ?>" alt="Profile" style="width: 150px; height: 150px; border-radius: 50%; border: 4px solid #f0f7ff; margin-bottom: 20px; object-fit: cover;">
                <h4 style="margin-bottom: 5px;"><?php echo htmlspecialchars($row['student_name']); ?></h4>
                <p class="fluent-badge-outline" style="display: inline-block; margin-bottom: 20px;"><?php echo htmlspecialchars($row['reg_no']); ?></p>
                
                <hr style="border: 0; border-top: 1px solid var(--app-border); margin: 20px 0;">
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="edit_admission.php?student_id=<?php echo $student_id; ?>" class="btn-fluent-primary">Edit Profile</a>
                    <a href="print_id_card.php?student_id=<?php echo $student_id; ?>" class="btn-outline-secondary">Print ID Card</a>
                </div>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="azure-card">
                <div class="widget_top">
                    <h6 class="fluent-card-header">Academic & Personal Details</h6>
                </div>
                <div class="widget_content" style="padding: 20px;">
                    <table class="fluent-data-table">
                        <tr>
                            <td class="label">Current Class:</td>
                            <td class="value"><?php echo htmlspecialchars($row['class_name'] ?? 'N/A'); ?></td>
                            <td class="label">Admission Date:</td>
                            <td class="value"><?php echo date('d-M-Y', strtotime($row['admission_date'])); ?></td>
                        </tr>
                        <tr>
                            <td class="label">Gender:</td>
                            <td class="value"><?php echo $row['gender']; ?></td>
                            <td class="label">Date of Birth:</td>
                            <td class="value"><?php echo !empty($row['dob']) ? date('d-M-Y', strtotime($row['dob'])) : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td class="label">Father's Name:</td>
                            <td class="value"><?php echo htmlspecialchars($row['father_name'] ?? 'N/A'); ?></td>
                            <td class="label">Mobile:</td>
                            <td class="value"><?php echo htmlspecialchars($row['mobile_no'] ?? 'N/A'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Profile specific Data Table */
    .fluent-data-table { width: 100%; border-collapse: collapse; }
    .fluent-data-table td { padding: 12px 15px; border-bottom: 1px solid #f3f4f6; }
    .fluent-data-table td.label { width: 20%; color: var(--app-muted); font-size: 13px; font-weight: 500; background: #fafbfc; }
    .fluent-data-table td.value { width: 30%; color: var(--fluent-slate); font-weight: 600; }
    
    /* Reuse badge from directory */
    .fluent-badge-outline { background: #f0f7ff; color: var(--app-primary); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
</style>

<?php require_once("includes/footer.php"); ?>
