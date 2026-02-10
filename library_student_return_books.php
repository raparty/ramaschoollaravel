<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$conn = Database::connection();

// Support both 'registration_no' and 'reg_no' from URL
$reg_no = mysqli_real_escape_string($conn, trim((string)($_GET['registration_no'] ?? $_GET['reg_no'] ?? '')));

// 1. Fetch Student Name from the primary 'admissions' table
$student_name = "Unknown Student";
if ($reg_no) {
    $sql_std = "SELECT student_name FROM admissions WHERE reg_no = '$reg_no'";
    $res_std = mysqli_query($conn, $sql_std);
    if ($row_std = mysqli_fetch_assoc($res_std)) {
        $student_name = $row_std['student_name'];
    }
}
?>

<div id="container">
    <div id="content">
        <div class="grid_container">
            <h3 style="padding:15px 0 0 20px; color:#0078D4">Student Return Books Detail</h3>
            
            <div class="grid_12">
                <div class="widget_wrap">
                    <div class="widget_top">
                        <h6>Currently Issued Books for: <?php echo htmlspecialchars($student_name); ?> (<?php echo htmlspecialchars($reg_no); ?>)</h6>
                    </div>
                    <div class="widget_content">
                        <table class="display data_tbl">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Student Name</th>
                                    <th>Book Name</th>
                                    <th>Book Number</th>
                                    <th>Issue Date</th>
                                    <th>Session</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                // 2. Querying the verified plural table 'student_books_details'
                                // Filter by booking_status = '1' (Still Issued)
                                $sql = "SELECT * FROM student_books_details 
                                        WHERE registration_no = '$reg_no' 
                                        AND booking_status = '1' 
                                        ORDER BY id DESC";
                                
                                $res = mysqli_query($conn, $sql);

                                if ($res && mysqli_num_rows($res) > 0) {
                                    while($row = mysqli_fetch_assoc($res)) {
                                        // 3. Nested lookup to 'book_managers' to get the actual Book Name
                                        $book_num = mysqli_real_escape_string($conn, (string)$row['book_number']);
                                        $bn_query = "SELECT book_name FROM book_managers WHERE book_number = '$book_num' LIMIT 1";
                                        $bn_res = mysqli_query($conn, $bn_query);
                                        $b_data = mysqli_fetch_assoc($bn_res);
                                ?>
                                <tr>
                                    <td class="center"><?php echo $i++; ?></td>
                                    <td class="center"><strong><?php echo htmlspecialchars($student_name); ?></strong></td>
                                    <td class="center" style="color:#1c75bc; font-weight:600;">
                                        <?php echo htmlspecialchars($b_data['book_name'] ?? 'Book Not in Catalog'); ?>
                                    </td>
                                    <td class="center"><code><?php echo htmlspecialchars($row['book_number']); ?></code></td>
                                    <td class="center"><?php echo date('d-M-Y', strtotime($row['issue_date'])); ?></td>
                                    <td class="center"><?php echo htmlspecialchars($row['session']); ?></td>
                                    <td class="center">
                                        <a href="library_process_return.php?id=<?php echo $row['id']; ?>" class="btn_small btn_blue" onclick="return confirm('Process return for this book?')">
                                            <span>Return</span>
                                        </a>
                                    </td>
                                </tr>
                                <?php } } else { ?>
                                    <tr>
                                        <td colspan="7" class="center" style="padding:40px;">
                                            <div style="color:#d32f2f; font-weight:bold;">No active issued books found.</div>
                                            <small>Verify if the book was already returned or issued under a different Registration No.</small>
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
