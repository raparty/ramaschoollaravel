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
                <span class="label">Categories</span>
            </a>
        </li>
        <li>
            <a href="library_book_manager.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
                <span class="label">Books Catalog</span>
            </a>
        </li>
        <li>
            <a href="library_student_books_manager.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                <span class="label">Issued Books</span>
            </a>
        </li>
        <li>
            <a href="library_fines_hub.php">
                <svg class="nav-icon-svg" viewBox="0 0 24 24"><path d="M12.5 12.5c1.38 0 2.5-1.12 2.5-2.5S13.88 7.5 12.5 7.5H6v-2h10V3H6c-.55 0-1 .45-1 1v4h7.5c.28 0 .5.22.5.5s-.22.5-.5.5H5v2h7.5c.28 0 .5.22.5.5s-.22.5-.5.5H5V13h3.28l4.34 6H16l-4.5-6h1z"/></svg>
                <span class="label">Fines Hub</span>
            </a>
        </li>
    </ul>
</div>
