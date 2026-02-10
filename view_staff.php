<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/staff_setting_sidebar.php");

$conn = Database::connection();
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <div class="grid_12">
                <div style="float:right; margin-top:10px;">
                    <a href="add_new_staff_detail.php" class="btn_small btn_blue"><span>+ Add Staff</span></a>
                </div>
                <h3 style="padding:10px 0 0 20px; color:#0078D4">Employee Directory</h3>
            </div>
            
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_content">
                        <table class="display data_tbl">
                            <thead>
                                <tr>
                                    <th>Emp ID</th>
                                    <th>Full Name</th>
                                    <th>Department</th>
                                    <th>Category</th>
                                    <th>Job Title</th>
                                    <th>Photo</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $sql = "SELECT e.*, d.staff_department, c.staff_category 
                                    FROM staff_employee e
                                    LEFT JOIN staff_department d ON e.staff_department_id = d.staff_department_id
                                    LEFT JOIN staff_category c ON e.staff_cat_id = c.staff_cat_id
                                    ORDER BY e.staff_id DESC";
                            $res = mysqli_query($conn, $sql);
                            
                            while($row = mysqli_fetch_assoc($res)) {
                            ?>
                            <tr>
                                <td class="center"><code><?php echo htmlspecialchars($row['emp_id']); ?></code></td>
                                <td><strong><?php echo htmlspecialchars($row['first'] . " " . $row['last']); ?></strong></td>
                                <td class="center"><?php echo htmlspecialchars($row['staff_department'] ?? 'N/A'); ?></td>
                                <td class="center"><?php echo htmlspecialchars($row['staff_category'] ?? 'N/A'); ?></td>
                                <td class="center"><?php echo htmlspecialchars($row['job_title']); ?></td>
                                <td class="center">
                                    <img style="height:40px; width:40px; border-radius:4px; object-fit:cover;" 
                                         src="employee_image/<?php echo $row['image'] ?: 'no-photo.png'; ?>">
                                </td>
                                <td class="center">
                                    <a class="action-icons c-add" href="view_staff_employee.php?staff_id=<?php echo $row['staff_id'];?>" title="View Profile">View</a>
                                    <a class="action-icons c-edit" href="edit_staf_employee_detail.php?staff_id=<?php echo $row['staff_id']?>" title="Edit">Edit</a>
                                    <a class="action-icons c-delete" href="delete_staff.php?staff_id=<?php echo $row['staff_id'];?>" 
                                       onclick="return confirm('Delete employee record?')" title="Delete">Delete</a>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once("includes/footer.php"); ?>
