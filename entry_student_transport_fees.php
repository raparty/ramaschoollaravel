<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");

$msg = "";
if(isset($_POST['submit']))
{
if($_POST['pending_amount']>=$_POST['fees_amount'])
{
FROM student_fees_detail where registration_no='".$_POST['registration_no']."' and fees_term='".$_POST['fees_term']."' and session='".$_SESSION['session']."'";
l1) or die("Error : " . db_error());
um=db_num_rows($res1);
um==0)
_no']!=""&&$_POST['fees_term']!=""&&$_POST['fees_amount']!="")
SERT INTO student_fees_detail(registration_no,fees_term,fees_amount,session) VALUES ('".$_POST['registration_no']."','".$_POST['fees_term']."','".$_POST['fees_amount']."','".$_SESSION['session']."')";
l3) or die("Error : " . db_error());
:fees_manager.php?msg=1");
:add_student_fees.php?error=2");
:add_student_fees.php?error=1");
:add_student_fees.php?error=3");
}
}
else
{
if($_GET['msg']==1)
{
"<div class='alert alert-success'>Student Fees Detail Added Successfully</div>";
}
if($_GET['msg']==2)
{
"<div class='alert alert-success'>Student Fees Detail Deleted Successfully</div>";
}
if($_GET['msg']==3)
{
"<div class='alert alert-success'>Student Fees Detail Updated Successfully</div>";
}
else if($_GET['error']==1)
{
"<div class='alert alert-danger'>Student Fees Detail Already Exists</div>";
}
else if($_GET['error']==2)
{
"<div class='alert alert-danger'>Please fill all detail</div>";
}
else if($_GET['error']==3)
{
"<div class='alert alert-danger'>Deposit fees amount is greater than pending amount.</div>";
}
}
?>

<div class="page_title">
<span class="title_icon"><span class="money_dollar"></span></span>
<h3>Add Student Transport Fees</h3>
</div>

<div id="container">
<div id="content">
tainer">
class="widget_wrap enterprise-card">
Student Bus Fees</h6>
tent">
$msg; } ?>
="add_student_transport_fees.php" method="post" class="p-4" enctype="multipart/form-data">
mb-4">
for="registration_no" class="form-label fw-bold">
t Registration Number <span class="text-danger">*</span>
put 
ame="registration_no" 
_no"

trol" 
ter SR Number"
Blur="getCheckreg('checkregno.php?registration_no='+this.value)" 
d-flex align-items-end justify-content-center">
 class="text-muted fw-bold">OR</span>
d-flex align-items-end">
sport_fees_searchby_name.php" class="btn-fluent-secondary w-100">
height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:6px;">
14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/>
 Name
class="my-4">
gap-2">
 type="submit" name="submit1" class="btn-fluent-primary">
height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:6px;">
16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z" fill="currentColor"/>
>
ager.php" class="btn-fluent-secondary">
height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:6px;">
11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="currentColor"/>
clude_once("includes/footer.php");?>
