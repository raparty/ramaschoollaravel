<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/staff_setting_sidebar.php");

$conn = Database::connection();
$get_id = (int)($_GET['staff_id'] ?? 0);

if ($get_id <= 0) {
    echo "<script>window.location.href='view_staff.php';</script>";
    exit;
}

// Optimized query to pull employee data and join related table names
$sql = "SELECT e.*, d.staff_department, c.staff_category, p.staff_position, q.staff_qualification 
        FROM staff_employee e
        LEFT JOIN staff_department d ON e.staff_department_id = d.staff_department_id
        LEFT JOIN staff_category c ON e.staff_cat_id = c.staff_cat_id
        LEFT JOIN staff_position p ON e.staff_pos_id = p.staff_pos_id
        LEFT JOIN staff_qualification q ON e.staff_qualification_id = q.staff_qualification_id
        WHERE e.staff_id = $get_id";

$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);

if (!$row) {
    echo "<div class='alert alert-error'>Employee record not found.</div>";
    include_once("includes/footer.php");
    exit;
}
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <div class="grid_12">
                <h3 style="padding:20px 0 10px 20px; color:#0078D4; border-bottom:1px solid #e2e8f0;">
                    Staff Profile: <?php echo htmlspecialchars($row['first'] . " " . $row['last']); ?>
                </h3>
            </div>

            <div class="grid_4">
                <div class="widget_wrap azure-card" style="text-align: center; padding: 25px;">
                    <img src="employee_image/<?php echo $row['image'] ?: 'no-photo.png'; ?>" 
                         style="width: 180px; height: 180px; border-radius: 12px; border: 1px solid #ddd; object-fit: cover; margin-bottom: 15px;">
                    <h4 style="color:#334155; margin-bottom: 5px;"><?php echo htmlspecialchars($row['first'] . " " . $row['last']); ?></h4>
                    <p style="color:#0078D4; font-weight: 700;"><?php echo htmlspecialchars($row['emp_id']); ?></p>
                    <div style="margin-top: 15px;">
                        <a href="edit_staf_employee_detail.php?staff_id=<?php echo $get_id; ?>" class="btn_small btn_blue">Edit Profile</a>
                    </div>
                </div>
            </div>

            <div class="grid_8">
                <div class="widget_wrap azure-card">
                    <div class="widget_top"><h6 class="fluent-card-header">General & Professional Details</h6></div>
                    <div class="widget_content" style="padding: 0;">
                        <table class="fluent-data-table">
                            <tr>
                                <td class="label">Email Address</td>
                                <td class="value"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="label">Gender</td>
                                <td class="value"><?php echo ucfirst(htmlspecialchars($row['gender'])); ?></td>
                            </tr>
                            <tr>
                                <td class="label">Department</td>
                                <td class="value"><?php echo htmlspecialchars($row['staff_department'] ?? 'N/A'); ?></td>
                                <td class="label">Qualification</td>
                                <td class="value"><?php echo htmlspecialchars($row['staff_qualification'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="label">Job Title</td>
                                <td class="value"><?php echo htmlspecialchars($row['job_title']); ?></td>
                                <td class="label">Experience</td>
                                <td class="value"><?php echo htmlspecialchars($row['exp']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="widget_wrap azure-card" style="margin-top: 20px;">
                    <div class="widget_top"><h6 class="fluent-card-header">Personal Information</h6></div>
                    <div class="widget_content" style="padding: 0;">
                        <table class="fluent-data-table">
                            <tr>
                                <td class="label">Father's Name</td>
                                <td class="value"><?php echo htmlspecialchars($row['father_name'] ?? 'N/A'); ?></td>
                                <td class="label">Mother's Name</td>
                                <td class="value"><?php echo htmlspecialchars($row['mother_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="label">Marital Status</td>
                                <td class="value"><?php echo htmlspecialchars($row['marritial_status']); ?></td>
                                <td class="label">Blood Group</td>
                                <td class="value" style="color:#d32f2f; font-weight:700;"><?php echo htmlspecialchars($row['blood_group'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="label">Nationality</td>
                                <td class="value"><?php echo htmlspecialchars($row['nationality'] ?? 'N/A'); ?></td>
                                <td class="label">Current Address</td>
                                <td class="value"><?php echo htmlspecialchars($row['address1']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fluent-data-table { width: 100%; border-collapse: collapse; }
    .fluent-data-table td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
    .fluent-data-table td.label { width: 25%; color: #64748b; font-weight: 600; background: #f8fafc; text-transform: uppercase; font-size: 11px; }
    .fluent-data-table td.value { width: 25%; color: #1e293b; font-weight: 500; }
</style>

<?php include_once("includes/footer.php"); ?>
