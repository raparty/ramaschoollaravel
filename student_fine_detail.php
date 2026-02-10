<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();
?>

<?php include_once("includes/library_setting_sidebar.php");?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding:15px 0 0 20px; color:#0078D4">Student Fine Details</h3>

            <div class="grid_12">
                <div class="btn_30_blue float-right">
                    <a href="entry_student_fine_detail.php"><span style="width:140px"> Individual Student Fine </span></a>				
                </div>
            </div>

            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top">
                        <h6>Fine Records</h6>
                    </div>
                    <div class="widget_content">
                        <table class="display data_tbl">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Book Name</th>
                                    <th>Book Number</th>
                                    <th>Fine Amount</th>
                                    <th>Session</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $session = mysqli_real_escape_string($conn, (string)$_SESSION['session']);
                                
                                // Querying fine details for the current active session
                                $sql_fine = "SELECT * FROM student_fine_detail WHERE session='$session' AND fine_amount > 0";
                                $res_fine = mysqli_query($conn, $sql_fine);

                                if ($res_fine && mysqli_num_rows($res_fine) > 0) {
                                    while($row = mysqli_fetch_assoc($res_fine)) {
                                        // LINK TO ADMISSIONS TABLE
                                        $reg_no = mysqli_real_escape_string($conn, (string)$row['registration_no']);
                                        $sql_std = "SELECT student_name, class_id FROM admissions WHERE reg_no = '$reg_no'";
                                        $res_std = mysqli_query($conn, $sql_std);
                                        $student = mysqli_fetch_assoc($res_std);

                                        // LINK TO BOOK_MANAGERS TABLE
                                        $book_no = mysqli_real_escape_string($conn, (string)$row['book_number']);
                                        $res_bk = mysqli_query($conn, "SELECT book_name FROM book_managers WHERE book_number = '$book_no'");
                                        $book = mysqli_fetch_assoc($res_bk);
                                ?>
                                <tr>
                                    <td class="center"><?php echo $i++; ?></td>
                                    <td class="center"><?php echo htmlspecialchars($student['student_name'] ?? 'Record Not Found'); ?></td>
                                    <td class="center"><?php echo htmlspecialchars($student['class_id'] ?? 'N/A'); ?></td>
                                    <td class="center"><?php echo htmlspecialchars($book['book_name'] ?? 'N/A'); ?></td>
                                    <td class="center"><code><?php echo htmlspecialchars($row['book_number']); ?></code></td>
                                    <td class="center" style="color:red; font-weight:bold;">₹<?php echo $row['fine_amount']; ?></td>
                                    <td class="center"><?php echo htmlspecialchars($row['session']); ?></td>
                                    <td class="center">
                                        <a href="library_edit_student_fine_detail.php?sid=<?php echo $row['id']; ?>" class="action-icons c-edit">Edit</a>
                                        <a href="library_delete_student_fine_detail.php?sid=<?php echo $row['id']; ?>" class="action-icons c-delete" onclick="return confirm('Delete this fine record?')">Delete</a>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='center'>No active fine records found in session $session.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once("includes/footer.php");?>
