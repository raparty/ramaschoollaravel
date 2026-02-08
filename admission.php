<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
include_once("includes/header.php");
include_once("includes/sidebar.php");
?>

<div class="page_title" style="margin-bottom: 25px;">
    <h3 style="font-weight: 300; font-size: 28px; color: var(--fluent-slate);">Student Admissions</h3>
</div>

<div class="switch_bar" style="margin-bottom: 30px;">
    <ul style="display: flex; gap: 20px; list-style: none; padding: 0;">
        <li>
            <a href="add_admission.php" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; padding: 20px; background: #fff; border: 1px solid var(--app-border); border-radius: 8px; width: 150px;">
                <svg viewBox="0 0 24 24" style="width:32px; fill:var(--app-primary); margin-bottom: 10px;"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                <span style="color: var(--fluent-slate); font-weight: 600; font-size: 13px;">New Admission</span>
            </a>
        </li>
        <li>
            <a href="student_detail.php" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; padding: 20px; background: #fff; border: 1px solid var(--app-border); border-radius: 8px; width: 150px;">
                <svg viewBox="0 0 24 24" style="width:32px; fill:#64748b; margin-bottom: 10px;"><path d="M16 11c1.66 0 2.99-1.33 2.99-3S17.66 5 16 5s-3 1.33-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.33 2.99-3S9.66 5 8 5s-3 1.33-3 3 1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                <span style="color: var(--fluent-slate); font-weight: 600; font-size: 13px;">Student List</span>
            </a>
        </li>
    </ul>
</div>

<div class="grid_container">
    <div class="widget_wrap azure-card">
        <div class="widget_top">
            <h6 class="fluent-card-header">Recently Admitted Students</h6>
        </div>
        <div class="widget_content">
            <table class="display data_tbl fluent-table">
                <thead>
                    <tr>
                        <th style="width: 150px;">Reg. No</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th style="width: 150px;">Date Admitted</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sql = "SELECT a.id, a.reg_no, a.student_name, c.class_name, a.admission_date 
                            FROM admissions a 
                            JOIN classes c ON a.class_id = c.id 
                            ORDER BY a.id DESC LIMIT 10";
                    $res = db_query($sql);
                    while($row = db_fetch_array($res)) { ?>		
                    <tr>
                        <td class="center"><strong><?php echo $row['reg_no']; ?></strong></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td class="center"><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td class="center"><?php echo date('d-M-Y', strtotime($row['admission_date'])); ?></td>
                        <td class="center"><span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700;">CONFIRMED</span></td>
                        <td class="center">
                            <a href="view_student_detail.php?student_id=<?php echo $row['id']; ?>" class="btn-fluent-primary" style="padding: 4px 12px; font-size: 11px; text-decoration: none;">View</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once("includes/footer.php"); ?>
