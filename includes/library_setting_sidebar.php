<?php
/**
 * library_setting_sidebar.php
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
            <a href="library_book_category.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/></svg>
                <span class="label">Category Manager</span>
            </a>
        </li>
        <li>
            <a href="library_book_manager.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
                <span class="label">Books Manager</span>
            </a>
        </li>
        <li>
            <a href="library_fine_manager.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                <span class="label">Fine Manager</span>
            </a>
        </li>
        <li>
            <a href="library_student_books_manager.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                <span class="label">Student Books Manager</span>
            </a>
        </li>
        <li>
            <a href="library_entry_student_return_books.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
                <span class="label">Student Return Books</span>
            </a>
        </li>
        <li>
            <a href="student_fine_detail.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                <span class="label">Student Fine Detail</span>
            </a>
        </li>
    </ul>
</div>
