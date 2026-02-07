<?php
declare(strict_types=1);
require_once("includes/bootstrap.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");
?>

<div class="page_title" style="background: transparent; box-shadow: none; border-bottom: 1px solid var(--app-border); border-radius: 0; margin-bottom: 30px;">
    <h3 style="font-weight: 400; color: var(--fluent-slate); font-size: 24px;">Student Admissions</h3>
</div>

<div class="grid_container">
    <div class="switch_bar">
        <ul style="display: flex; gap: 20px; list-style: none; padding: 0;">
            <li>
                <a href="add_admission.php" class="azure-modern-card" style="padding: 20px; min-height: 100px;">
                    <div class="azure-icon-wrapper" style="width: 40px; height: 40px; margin-bottom: 10px;">
                        <svg viewBox="0 0 24 24" style="width:20px; fill:var(--app-primary);"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    </div>
                    <span class="azure-label">New Admission</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="widget_wrap azure-card" style="margin-top: 30px;">
        <div class="widget_top">
            <h6 class="fluent-card-header">Recent Admissions</h6>
        </div>
        <div class="widget_content">
            <table class="display data_tbl fluent-table">
                <thead>
                    <tr>
                        <th class="center">Reg. No</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th class="center">Admission Date</th>
                        <th class="center">Status</th>
                        <th class="center">Action</th>
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
                        <td class="center"><span class="fluent-badge-outline" style="background: #ecfdf5; color: #065f46;">Confirmed</span></td>
                        <td class="center">
                            <div class="fluent-action-group">
                                <a href="edit_admission.php?student_id=<?php echo $row['id']; ?>" class="fluent-btn-icon" title="Edit Admission">
                                    <svg viewBox="0 0 24 24" width="18" height="18"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                </a>
                                <a href="view_student_detail.php?student_id=<?php echo $row['id']; ?>" class="fluent-btn-icon" title="View Profile">
                                    <svg viewBox="0 0 24 24" width="18" height="18"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .title_icon { display: none !important; } /* Remove blue box artifact */
    .fluent-action-group { display: flex; gap: 8px; justify-content: center; }
    .fluent-btn-icon { padding: 5px; border-radius: 4px; transition: 0.2s; display: flex; align-items: center; }
    .fluent-btn-icon:hover { background: #eff6fc; }
    .fluent-btn-icon svg { fill: var(--app-primary); }
</style>

<?php require_once("includes/footer.php"); ?>
