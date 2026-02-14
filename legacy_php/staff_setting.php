<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();

// Fetch quick stats for the dashboard
$total_staff = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM staff_employee"))['count'] ?? 0;
$total_depts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM staff_department"))['count'] ?? 0;
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding:10px 0 0 20px; color:#1c75bc">Staff Management Hub</h3>
            
            <?php include_once("includes/staff_setting_sidebar.php"); ?>

            <div class="grid_12">
                <div class="widget_wrap azure-card">
                    <div class="widget_top">
                        <h6 class="fluent-card-header">Staff & HR Command Center</h6>
                    </div>
                    <div class="widget_content" style="padding: 30px;">
                        <div class="row" style="display: flex; gap: 30px; margin-bottom: 30px;">
                            <div style="flex: 1; background: #f0f7ff; padding: 20px; border-radius: 12px; border: 1px solid #0078D4;">
                                <span style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Total Employees</span>
                                <h2 style="margin: 5px 0 0; color: #0078D4;"><?php echo $total_staff; ?></h2>
                            </div>
                            <div style="flex: 1; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                <span style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Departments</span>
                                <h2 style="margin: 5px 0 0; color: #334155;"><?php echo $total_depts; ?></h2>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <a href="view_staff.php" class="staff-hub-item">
                                <span class="icon">👥</span>
                                <div><strong>Employee Directory</strong><small>Manage full profiles</small></div>
                            </a>
                            <a href="view_staff_department.php" class="staff-hub-item">
                                <span class="icon">🏢</span>
                                <div><strong>Departments</strong><small>Academic, Admin, etc.</small></div>
                            </a>
                            <a href="view_staff_position.php" class="staff-hub-item">
                                <span class="icon">🎓</span>
                                <div><strong>Designations</strong><small>Job titles & roles</small></div>
                            </a>
                            <a href="view_staff_category.php" class="staff-hub-item">
                                <span class="icon">🏷️</span>
                                <div><strong>Staff Categories</strong><small>Teaching, Non-teaching</small></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.staff-hub-item {
    display: flex; align-items: center; gap: 15px; padding: 15px;
    background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;
    text-decoration: none; transition: 0.2s;
}
.staff-hub-item:hover { border-color: #0078D4; background: #f0f7ff; transform: translateY(-2px); }
.staff-hub-item .icon { font-size: 24px; }
.staff-hub-item strong { display: block; color: #0078D4; font-size: 14px; }
.staff-hub-item small { font-size: 11px; color: #64748b; }
</style>

<?php include_once("includes/footer.php"); ?>
