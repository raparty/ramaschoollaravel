<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php"); 
?>

<div class="page_title">
    <div class="top_search">
        <form action="" method="post">
            <ul id="search_box">
                <li><input name="search" type="text" class="search_input" placeholder="Search by name or Reg No..."></li>
                <li><input type="submit" value="Search" class="search_btn"></li>
            </ul>
        </form>
    </div>
</div>

<?php include_once("includes/library_setting_sidebar.php");?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding-left:20px; color:#0078D4">Student Fine Detail</h3>
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top"><h6>Active Fine Records</h6></div>
                    <div class="widget_content">
                        <table class="display data_tbl">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Fine Amount</th>
                                    <th>Session</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $conn = Database::connection();
                            $session = mysqli_real_escape_string($conn, (string)($_SESSION['session'] ?? ''));
                            
                            // Query: Shows all positive fines, prioritizing current session
                            $sql = "SELECT * FROM student_fine_detail WHERE fine_amount > 0 ORDER BY (session = '$session') DESC, id DESC";
                            $res = mysqli_query($conn, $sql);
                            
                            $i = 1;
                            while($row = mysqli_fetch_assoc($res)) {
                                $reg_no = mysqli_real_escape_string($conn, $row['registration_no']);
                                $sql_std = "SELECT student_name, class_id FROM admissions WHERE reg_no = '$reg_no'";
                                $student_info = mysqli_fetch_assoc(mysqli_query($conn, $sql_std));
                                
                                $class_id = (int)($student_info['class_id'] ?? 0);
                                $sql_class = "SELECT class_name FROM classes WHERE id = '$class_id'";
                                $class_data = mysqli_fetch_assoc(mysqli_query($conn, $sql_class));
                            ?>
                            <tr>
                                <td class="center"><?php echo $i++; ?></td>
                                <td class="center"><strong><?php echo htmlspecialchars($student_info['student_name'] ?? 'Not Found'); ?></strong></td>
                                <td class="center"><?php echo htmlspecialchars($class_data['class_name'] ?? 'N/A'); ?></td>
                                <td class="center" style="color: #d32f2f; font-weight: bold;">₹ <?php echo number_format((float)$row['fine_amount'], 2); ?></td>
                                <td class="center"><?php echo htmlspecialchars($row['session'] ?: 'Unassigned'); ?></td>
                                <td class="center">
                                    <span><a class="action-icons c-edit" href="library_edit_student_fine_detail.php?sid=<?php echo $row['id']; ?>" title="Edit">Edit</a></span>
                                    <span><a class="action-icons c-delete" href="library_delete_student_fine_detail.php?sid=<?php echo $row['id']; ?>" title="Delete" onClick="return confirm('Delete fine?')">Delete</a></span>
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
<?php include_once("includes/footer.php");?>
