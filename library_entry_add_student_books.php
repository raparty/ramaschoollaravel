<?php
declare(strict_types=1);

require_once("includes/bootstrap.php");

$conn = Database::connection();
$msg = "";

// Fetch student details if registration number is provided
$registration_no = $_REQUEST['registration_no'] ?? $_SESSION['registration_no'] ?? '';
$student = null;

if ($registration_no) {
    $_SESSION['registration_no'] = $registration_no;
    $reg_safe = mysqli_real_escape_string($conn, (string)$registration_no);
    $sql_std = "SELECT s.*, c.class_name FROM student_info s 
                LEFT JOIN classes c ON s.class_id = c.id 
                WHERE s.registration_no='$reg_safe'";
    $res_std = mysqli_query($conn, $sql_std);
    $student = mysqli_fetch_assoc($res_std);
}

// Handle form submission (before headers)
if (isset($_POST['submit'])) {
    $reg_no = mysqli_real_escape_string($conn, (string)$_POST['registration_no']);
    $book_no = mysqli_real_escape_string($conn, (string)$_POST['book_number']);
    $issue_dt = date('Y-m-d', strtotime((string)$_POST['issue_date']));
    $session = mysqli_real_escape_string($conn, (string)$_SESSION['session']);

    // Check if book is already issued
    $check_sql = "SELECT * FROM student_books_details 
                  WHERE book_number='$book_no' AND booking_status='1'";
    $check_res = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_res) > 0) {
        $msg = "<div class='alert alert-danger'>This book is already issued to another student</div>";
    } else {
        $sql_ins = "INSERT INTO student_books_details (registration_no, book_number, issue_date, booking_status, session) 
                    VALUES ('$reg_no', '$book_no', '$issue_dt', '1', '$session')";
        
        if (mysqli_query($conn, $sql_ins)) {
            header("Location: library_student_books_manager.php?msg=1");
            exit;
        } else {
            $msg = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Handle success/error messages from redirects
if (isset($_GET['msg']) && $_GET['msg']==1) {
    $msg = "<div class='alert alert-success'>Student Book Issued Successfully</div>";
}
if (isset($_GET['msg']) && $_GET['msg']==2) {
    $msg = "<div class='alert alert-success'>Book Record Deleted Successfully</div>";
}
if (isset($_GET['msg']) && $_GET['msg']==3) {
    $msg = "<div class='alert alert-success'>Book Record Updated Successfully</div>";
}
if (isset($_GET['error']) && $_GET['error']==1) {
    $msg = "<div class='alert alert-danger'>Book is already issued</div>";
}
if (isset($_GET['error']) && $_GET['error']==2) {
    $msg = "<div class='alert alert-danger'>Please fill all required fields</div>";
}

include_once("includes/header.php");
include_once("includes/sidebar.php");
include_once("includes/library_setting_sidebar.php");
?>

<div class="page_title">
	<h3>Library Management</h3>
</div>

<div id="container">
	<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap enterprise-card">
					<div class="widget_top">
						<h6>Issue Book to Student</h6>
					</div>
					<div class="widget_content">
						<?php if($msg!=""){echo $msg; } ?>
						
						<form action="library_entry_add_student_books.php" method="post" class="p-4">
							<div class="row mb-4">
								<div class="col-md-5">
									<label for="registration_no" class="form-label fw-bold">
										S.R. Number <span class="text-danger">*</span>
									</label>
									<input 
										name="registration_no" 
										id="registration_no"
										type="text" 
										class="form-control" 
										placeholder="Enter Registration Number"
										value="<?php echo htmlspecialchars((string)$registration_no); ?>"
										onBlur="window.location.href='library_entry_add_student_books.php?registration_no='+encodeURIComponent(this.value)"
										required
									/>
								</div>
								<div class="col-md-2 d-flex align-items-end justify-content-center">
									<span class="text-muted fw-bold">OR</span>
								</div>
								<div class="col-md-5 d-flex align-items-end">
									<a href="library_student_searchby_name.php" class="btn-fluent-secondary w-100">
										<svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:6px;">
											<path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/>
										</svg>
										Search by Name
									</a>
								</div>
							</div>
							
							<?php if ($student): ?>
							<div class="row mb-4">
								<div class="col-md-6">
									<label class="form-label fw-bold">Student Name</label>
									<input type="text" class="form-control" value="<?php echo htmlspecialchars($student['name'] ?? 'N/A'); ?>" readonly style="background:#f1f5f9;">
								</div>
								<div class="col-md-6">
									<label class="form-label fw-bold">Class</label>
									<input type="text" class="form-control" value="<?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?>" readonly style="background:#f1f5f9;">
								</div>
							</div>
							
							<hr class="my-4">
							
							<div class="row mb-4">
								<div class="col-md-6">
									<label for="book_number" class="form-label fw-bold">
										Book Number <span class="text-danger">*</span>
									</label>
									<input 
										name="book_number" 
										id="book_number"
										type="text" 
										class="form-control" 
										placeholder="e.g. BK-101"
										required
									/>
								</div>
								<div class="col-md-6">
									<label for="issue_date" class="form-label fw-bold">
										Issue Date <span class="text-danger">*</span>
									</label>
									<input 
										name="issue_date" 
										id="issue_date"
										type="text" 
										class="form-control datepicker" 
										value="<?php echo date('m/d/Y'); ?>"
										required
									/>
								</div>
							</div>
							
							<hr class="my-4">
							
							<div class="d-flex gap-2">
								<button type="submit" name="submit" class="btn-fluent-primary">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:6px;">
										<path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z" fill="currentColor"/>
									</svg>
									Issue Book
								</button>
								<a href="library_student_books_manager.php" class="btn-fluent-secondary">
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:6px;">
										<path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="currentColor"/>
									</svg>
									Back
								</a>
							</div>
							<?php else: ?>
							<div class="alert alert-info">
								<strong>Please enter a registration number</strong> or search for a student to issue a book.
							</div>
							<?php endif; ?>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include_once("includes/footer.php");?>
