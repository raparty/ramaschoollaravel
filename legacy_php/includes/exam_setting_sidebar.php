<?php
/**
 * exam_setting_sidebar.php
 * Enterprise-Grade Professional Navigation Grid
 */
?>
<style>
    :root {
        --primary-blue: #0078D4;
        --text-dark: #334155;
        --border-light: #e2e8f0;
    }

    .switch_bar { padding: 20px 0; }

    .switch_bar ul {
        list-style: none; padding: 0; margin: 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 15px;
    }

    .switch_bar li { width: 100%; }

    /* The Professional Card */
    .switch_bar a {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        height: 110px; padding: 10px;
        background: #ffffff;
        border: 1px solid var(--border-light);
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    /* Enterprise Hover State */
    .switch_bar a:hover {
        border-color: var(--primary-blue);
        background: #f8fafc;
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(28, 117, 188, 0.1);
    }

    .switch_bar a:hover .nav-icon-svg { fill: var(--primary-blue); }
    .switch_bar a:hover .label { color: var(--primary-blue); }

    /* Modern SVG Icon Styling */
    .nav-icon-svg {
        width: 32px; height: 32px;
        fill: #605E5C; /* Slate grey default */
        margin-bottom: 12px;
        transition: fill 0.3s ease;
    }

    /* Label Styling */
    .switch_bar .label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-dark);
        text-transform: uppercase;
        text-align: center;
        letter-spacing: 0.8px;
    }
</style>

<div class="switch_bar">
    <ul>
        <li>
            <a href="exam_show_maximum_marks.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                <span class="label">Add Exam Marks</span>
            </a>
        </li>
        <li>
            <a href="exam_time_table_detail.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
                <span class="label">Exam Time Table</span>
            </a>
        </li>
        <li>
            <a href="exam_show_student_marks.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                <span class="label">Student Marks Detail</span>
            </a>
        </li>
        <li>
            <a href="entry_exam_marksheet.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                <span class="label">Exam Report Detail</span>
            </a>
        </li>
    </ul>
</div>
