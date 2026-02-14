# School ERP System - Navigation Flow Table

This document maps the navigation relationships between files in the School ERP System, showing how different pages connect to each other through forms, redirects, and links.

## Table of Contents
1. [Authentication & Session Management](#authentication--session-management)
2. [Student Management](#student-management)
3. [Fees Management](#fees-management)
4. [Transport Management](#transport-management)
5. [Library Management](#library-management)
6. [Staff Management](#staff-management)
7. [Exam Management](#exam-management)
8. [Academic Settings](#academic-settings)
9. [Financial Management](#financial-management)
10. [Common Include Patterns](#common-include-patterns)

---

## Authentication & Session Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| index.php | login_process.php | Form POST | User authentication |
| login_process.php | dashboard.php | Redirect (header) | Successful login |
| login_process.php | index.php | Redirect (header) | Failed login |
| dashboard.php | session.php | Include | Session validation |
| logout.php | index.php | Redirect (header) | User logout |
| change_password.php | dashboard.php | Redirect (header) | Password updated |

---

## Student Management

### Student Admission Flow

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| admission.php | add_admission.php | Link (href) | Add new student |
| admission.php | searchby_name.php | Form GET | Search students |
| add_admission.php | admission_process.php | Form POST | Submit admission data |
| admission_process.php | student_detail.php | Redirect (header) | Show new student |
| admission_process.php | add_admission.php | Redirect (header) | Error handling |
| student_detail.php | view_student_detail.php | Link (href) | View full details |
| student_detail.php | edit_admission.php | Link (href) | Edit student |
| student_detail.php | delete_admission.php | Link (href) | Delete student |
| edit_admission.php | process_edit_admission.php | Form POST | Update student data |
| process_edit_admission.php | student_detail.php | Redirect (header) | Return to list |
| delete_admission.php | student_detail.php | Redirect (header) | Confirm deletion |

### RTE (Right to Education) Students

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| rte_admission.php | rte_student_detail.php | Form POST | Add RTE student |
| rte_student_detail.php | rte_view_student_detail.php | Link (href) | View RTE student |
| rte_student_detail.php | rte_edit_admission.php | Link (href) | Edit RTE student |
| rte_student_detail.php | rte_delete_admission.php | Link (href) | Delete RTE student |
| rte_edit_admission.php | rte_student_detail.php | Redirect (header) | Return after edit |
| rte_delete_admission.php | rte_student_detail.php | Redirect (header) | Confirm deletion |

### Student Transfer Certificate

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| student_tc.php | entry_student_tc.php | Link (href) | Issue TC |
| student_tc.php | student_tc_search_by_name.php | Form GET | Search for TC |
| entry_student_tc.php | student_tc_show.php | Form POST | Generate TC |
| student_tc_show.php | student_tc.php | Link (href) | Back to TC list |

---

## Fees Management

### Fee Collection

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| fees_manager.php | fees_searchby_name.php | Link (href) | Collect fees |
| fees_manager.php | fees_search_result.php | Form POST | Search fees |
| fees_searchby_name.php | entry_fees_reciept.php | Link (href) | Select student |
| entry_fees_reciept.php | fees_reciept.php | Form POST | Submit payment |
| fees_reciept.php | fees_reciept_byterm.php | Redirect (header) | Show receipt |
| fees_reciept_byterm.php | fees_manager.php | Link (href) | Return to manager |

### Fee Configuration

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| fees_package.php | add_fees_package.php | Link (href) | Add package |
| add_fees_package.php | fees_package.php | Redirect (header) | After adding |
| fees_package.php | edit_fees_package.php | Link (href) | Edit package |
| edit_fees_package.php | fees_package.php | Redirect (header) | After editing |
| fees_package.php | delete_fees_package.php | Link (href) | Delete package |
| delete_fees_package.php | fees_package.php | Redirect (header) | Confirm deletion |

### Student Fees Entry

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| entry_student_fees.php | add_student_fees.php | Form POST | Assign fees |
| add_student_fees.php | fees_manager.php | Redirect (header) | After assignment |
| student_pending_fees_detail.php | entry_student_pending_fees.php | Link (href) | Pay pending fees |
| entry_student_pending_fees.php | fees_manager.php | Redirect (header) | After payment |

### Fee Reports

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| student_fees_reports.php | student_fees_reports_pagination.php | Include | Pagination |
| fees_manager.php | print_daily_report.php | Link (href) | Print report |

---

## Transport Management

### Transport Fee Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| student_transport_fees.php | transport_fees_searchby_name.php | Link (href) | Collect fees |
| student_transport_fees.php | transport_fees_result.php | Form POST | Search results |
| transport_fees_searchby_name.php | entry_transport_fees_reciept.php | Link (href) | Select student |
| entry_transport_fees_reciept.php | transport_fees_reciept.php | Form POST | Submit payment |
| transport_fees_reciept.php | transport_fees_reciept_byterm.php | Redirect (header) | Show receipt |

### Transport Student Assignment

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| transport_student_detail.php | transport_searchby_name.php | Link (href) | Add to transport |
| transport_searchby_name.php | entry_transport_add_student.php | Link (href) | Select student |
| entry_transport_add_student.php | transport_add_student.php | Form POST | Assign transport |
| transport_add_student.php | transport_student_detail.php | Redirect (header) | Confirm assignment |
| transport_student_detail.php | transport_edit_student.php | Link (href) | Edit assignment |
| transport_edit_student.php | transport_student_detail.php | Redirect (header) | After editing |

### Transport Settings

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| transport_setting.php | transport_add_route.php | Link (href) | Add route |
| transport_add_route.php | transport_route_detail.php | Redirect (header) | After adding |
| transport_route_detail.php | transport_route_edit.php | Link (href) | Edit route |
| transport_route_edit.php | transport_route_detail.php | Redirect (header) | After editing |
| transport_setting.php | transport_add_vechile.php | Link (href) | Add vehicle |
| transport_add_vechile.php | transport_vechile_detail.php | Redirect (header) | After adding |
| transport_vechile_detail.php | transport_edit_vehicle.php | Link (href) | Edit vehicle |
| transport_edit_vehicle.php | transport_vechile_detail.php | Redirect (header) | After editing |

---

## Library Management

### Book Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| library_book_manager.php | library_add_book.php | Link (href) | Add new book |
| library_add_book.php | library_book_manager.php | Redirect (header) | After adding |
| library_book_manager.php | library_edit_book.php | Link (href) | Edit book |
| library_edit_book.php | library_book_manager.php | Redirect (header) | After editing |
| library_book_manager.php | library_delete_book.php | Link (href) | Delete book |
| library_delete_book.php | library_book_manager.php | Redirect (header) | Confirm deletion |

### Book Category Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| library_book_category.php | library_add_book_category.php | Link (href) | Add category |
| library_add_book_category.php | library_book_category.php | Redirect (header) | After adding |
| library_book_category.php | library_edit_book_category.php | Link (href) | Edit category |
| library_edit_book_category.php | library_book_category.php | Redirect (header) | After editing |
| library_book_category.php | library_delete_book_category.php | Link (href) | Delete category |
| library_delete_book_category.php | library_book_category.php | Redirect (header) | Confirm deletion |

### Book Issue & Return

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| library_student_books_manager.php | library_student_searchby_name.php | Link (href) | Issue book |
| library_student_searchby_name.php | library_entry_add_student_books.php | Link (href) | Select student |
| library_entry_add_student_books.php | library_add_student_books.php | Form POST | Issue book |
| library_add_student_books.php | library_student_books_manager.php | Redirect (header) | After issuing |
| library_student_books_manager.php | library_student_returnbook_searchby_name.php | Link (href) | Return book |
| library_student_returnbook_searchby_name.php | library_return_student_books_page.php | Link (href) | Select student |
| library_return_student_books_page.php | library_entry_student_return_books.php | Link (href) | Select book |
| library_entry_student_return_books.php | library_process_return.php | Form POST | Process return |
| library_process_return.php | library_student_books_manager.php | Redirect (header) | After return |

### Library Fine Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| library_fine_manager.php | library_add_fine.php | Link (href) | Add fine rule |
| library_add_fine.php | library_fine_manager.php | Redirect (header) | After adding |
| library_fine_manager.php | library_edit_fine.php | Link (href) | Edit fine rule |
| library_edit_fine.php | library_fine_manager.php | Redirect (header) | After editing |
| student_fine_detail.php | library_student_fine_entry.php | Link (href) | Pay fine |
| library_student_fine_entry.php | student_fine_detail1.php | Form POST | Submit payment |

---

## Staff Management

### Staff Details

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| view_staff.php | add_new_staff_detail.php | Link (href) | Add staff |
| add_new_staff_detail.php | view_staff.php | Redirect (header) | After adding |
| view_staff.php | view_staff_employee.php | Link (href) | View details |
| view_staff_employee.php | edit_staf_employee_detail.php | Link (href) | Edit staff |
| edit_staf_employee_detail.php | view_staff_employee.php | Redirect (header) | After editing |
| view_staff.php | delete_staff.php | Link (href) | Delete staff |
| delete_staff.php | view_staff.php | Redirect (header) | Confirm deletion |

### Staff Categories

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| staff_category.php | add_staff_category.php | Link (href) | Add category |
| add_staff_category.php | staff_category.php | Redirect (header) | After adding |
| staff_category.php | edit_staff_category.php | Link (href) | Edit category |
| edit_staff_category.php | view_staff_category.php | Redirect (header) | After editing |
| staff_category.php | delete_staff_category.php | Link (href) | Delete category |
| delete_staff_category.php | staff_category.php | Redirect (header) | Confirm deletion |

### Staff Departments

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| staff_department.php | add_staff_department.php | Link (href) | Add department |
| add_staff_department.php | staff_department.php | Redirect (header) | After adding |
| staff_department.php | edit_staff_department.php | Link (href) | Edit department |
| edit_staff_department.php | view_staff_department.php | Redirect (header) | After editing |
| staff_department.php | delete_staff_department.php | Link (href) | Delete department |
| delete_staff_department.php | staff_department.php | Redirect (header) | Confirm deletion |

### Staff Positions

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| staff_position.php | add_staff_position.php | Link (href) | Add position |
| add_staff_position.php | staff_position.php | Redirect (header) | After adding |
| staff_position.php | edit_staff_position.php | Link (href) | Edit position |
| edit_staff_position.php | view_staff_position.php | Redirect (header) | After editing |
| staff_position.php | delete_staff_position.php | Link (href) | Delete position |
| delete_staff_position.php | staff_position.php | Redirect (header) | Confirm deletion |

### Staff Qualifications

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| qualification.php | add_staff_qualification.php | Link (href) | Add qualification |
| add_staff_qualification.php | qualification.php | Redirect (header) | After adding |
| qualification.php | edit_staff_qualification.php | Link (href) | Edit qualification |
| edit_staff_qualification.php | view_staff_qualification.php | Redirect (header) | After editing |
| qualification.php | delete_staff_qualification.php | Link (href) | Delete qualification |
| delete_staff_qualification.php | qualification.php | Redirect (header) | Confirm deletion |

---

## Exam Management

### Maximum Marks Setup

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| exam_show_maximum_marks.php | exam_select_exam_term.php | Link (href) | Add max marks |
| exam_select_exam_term.php | entry_add_max_marks.php | Form POST | Select term |
| entry_add_max_marks.php | exam_add_maximum_marks.php | Form POST | Enter marks |
| exam_add_maximum_marks.php | exam_show_maximum_marks.php | Redirect (header) | After adding |
| exam_show_maximum_marks.php | exam_edit_maximum_marks.php | Link (href) | Edit max marks |
| exam_edit_maximum_marks.php | exam_show_maximum_marks.php | Redirect (header) | After editing |

### Student Marks Entry

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| exam_show_student_marks.php | exam_searchby_name.php | Link (href) | Add marks |
| exam_searchby_name.php | exam_marks_add_student.php | Link (href) | Select student |
| exam_marks_add_student.php | entry_exam_add_student_marks.php | Form POST | Select term |
| entry_exam_add_student_marks.php | exam_show_student_marks.php | Redirect (header) | After entry |

### Marksheet & Results

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| exam_final_marksheet.php | exam_marksheet_searchby_name.php | Link (href) | Search marksheet |
| exam_marksheet_searchby_name.php | exam_marksheet_student_selector.php | Link (href) | Select student |
| exam_marksheet_student_selector.php | entry_exam_marksheet.php | Form POST | Select term |
| entry_exam_marksheet.php | exam_final_marksheet.php | Redirect (header) | Show marksheet |
| exam_result.php | exam_final_marksheet.php | Link (href) | View marksheet |

### Exam Time Table

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| exam_date.php | exam_time_table_detail.php | Link (href) | View schedule |
| exam_time_table_detail.php | exam_edit_time_table.php | Link (href) | Edit schedule |
| exam_edit_time_table.php | exam_time_table_detail.php | Redirect (header) | After editing |

---

## Academic Settings

### Class Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| class.php | add_class.php | Link (href) | Add class |
| add_class.php | class.php | Redirect (header) | After adding |
| class.php | edit_class.php | Link (href) | Edit class |
| edit_class.php | class.php | Redirect (header) | After editing |
| class.php | delete_class.php | Link (href) | Delete class |
| delete_class.php | class.php | Redirect (header) | Confirm deletion |

### Section Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| section.php | add_section.php | Link (href) | Add section |
| add_section.php | section.php | Redirect (header) | After adding |
| section.php | edit_section.php | Link (href) | Edit section |
| edit_section.php | section.php | Redirect (header) | After editing |
| section.php | delete_section.php | Link (href) | Delete section |
| delete_section.php | section.php | Redirect (header) | Confirm deletion |

### Stream Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| stream.php | add_stream.php | Link (href) | Add stream |
| add_stream.php | stream.php | Redirect (header) | After adding |
| stream.php | edit_stream.php | Link (href) | Edit stream |
| edit_stream.php | stream.php | Redirect (header) | After editing |
| stream.php | delete_stream.php | Link (href) | Delete stream |
| delete_stream.php | stream.php | Redirect (header) | Confirm deletion |

### Subject Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| subject.php | add_subject.php | Link (href) | Add subject |
| add_subject.php | subject.php | Redirect (header) | After adding |
| subject.php | edit_subject.php | Link (href) | Edit subject |
| edit_subject.php | subject.php | Redirect (header) | After editing |
| subject.php | delete_subject.php | Link (href) | Delete subject |
| delete_subject.php | subject.php | Redirect (header) | Confirm deletion |

### Section Allocation

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| allocate_section.php | add_allocate_section.php | Link (href) | Allocate section |
| add_allocate_section.php | allocate_section.php | Redirect (header) | After allocation |
| allocate_section.php | edit_allocate_section.php | Link (href) | Edit allocation |
| edit_allocate_section.php | allocate_section.php | Redirect (header) | After editing |
| allocate_section.php | delete_allocate_section.php | Link (href) | Delete allocation |
| delete_allocate_section.php | allocate_section.php | Redirect (header) | Confirm deletion |

### Stream Allocation

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| allocate_stream.php | add_allocate_stream.php | Link (href) | Allocate stream |
| add_allocate_stream.php | allocate_stream.php | Redirect (header) | After allocation |
| allocate_stream.php | edit_allocate_stream.php | Link (href) | Edit allocation |
| edit_allocate_stream.php | allocate_stream.php | Redirect (header) | After editing |
| allocate_stream.php | delete_allocate_stream.php | Link (href) | Delete allocation |
| delete_allocate_stream.php | allocate_stream.php | Redirect (header) | Confirm deletion |

### Subject Allocation

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| allocate_subject.php | add_allocate_subject.php | Link (href) | Allocate subject |
| add_allocate_subject.php | allocate_subject.php | Redirect (header) | After allocation |
| allocate_subject.php | edit_allocate_subject.php | Link (href) | Edit allocation |
| edit_allocate_subject.php | allocate_subject.php | Redirect (header) | After editing |
| allocate_subject.php | delete_allocate_subject.php | Link (href) | Delete allocation |
| delete_allocate_subject.php | allocate_subject.php | Redirect (header) | Confirm deletion |

### Term Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| term_manager.php | add_term.php | Link (href) | Add term |
| add_term.php | term_manager.php | Redirect (header) | After adding |
| term_manager.php | edit_term.php | Link (href) | Edit term |
| edit_term.php | term_manager.php | Redirect (header) | After editing |
| term_manager.php | delete_term.php | Link (href) | Delete term |
| delete_term.php | term_manager.php | Redirect (header) | Confirm deletion |

---

## Financial Management

### Income Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| income_manager.php | add_income.php | Link (href) | Add income |
| add_income.php | income_manager.php | Redirect (header) | After adding |
| income_manager.php | edit_income.php | Link (href) | Edit income |
| edit_income.php | income_manager.php | Redirect (header) | After editing |
| income_manager.php | delete_income.php | Link (href) | Delete income |
| delete_income.php | income_manager.php | Redirect (header) | Confirm deletion |

### Expense Management

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| expense_manager.php | add_expense.php | Link (href) | Add expense |
| add_expense.php | expense_manager.php | Redirect (header) | After adding |
| expense_manager.php | edit_expense.php | Link (href) | Edit expense |
| edit_expense.php | expense_manager.php | Redirect (header) | After editing |
| expense_manager.php | delete_expense.php | Link (href) | Delete expense |
| delete_expense.php | expense_manager.php | Redirect (header) | Confirm deletion |

### Account Categories

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| account_category_manager.php | add_account_category_manager.php | Link (href) | Add category |
| add_account_category_manager.php | account_category_manager.php | Redirect (header) | After adding |
| account_category_manager.php | edit_account_category_manager.php | Link (href) | Edit category |
| edit_account_category_manager.php | account_category_manager.php | Redirect (header) | After editing |
| account_category_manager.php | delete_account_category_manager.php | Link (href) | Delete category |
| delete_account_category_manager.php | account_category_manager.php | Redirect (header) | Confirm deletion |

### Reports

| From File | Navigates to File | Navigation Method | Purpose |
|-----------|-------------------|-------------------|---------|
| account_report.php | entry_account_report.php | Form POST | Generate report |
| daily_report.php | print_daily_report.php | Link (href) | Print report |
| account_setting.php | account_report.php | Link (href) | View reports |

---

## Common Include Patterns

### Header & Navigation

All pages include these common files in this order:

| From File | Includes File | Purpose |
|-----------|---------------|---------|
| [Any Page] | includes/bootstrap.php | Session start, security headers, DB init |
| [Any Page] | includes/header.php | HTML header, auth guard, meta tags |
| [Any Page] | includes/sidebar.php | Navigation menu |
| [Any Page] | includes/footer.php | HTML footer, scripts |

### Pagination

Pages with data tables include pagination:

| From File | Includes File | Purpose |
|-----------|---------------|---------|
| student_detail_2.php | student_detail_pagination.php | Student list pagination |
| fees_manager.php | fees_manager_pagination.php | Fees records pagination |
| student_fees_reports.php | student_fees_reports_pagination.php | Fees reports pagination |
| student_transport_fees.php | student_transport_pending_fees_pagination.php | Transport fees pagination |
| rte_student_detail.php | rte_student_detail_pagination.php | RTE student pagination |
| income_manager.php | income_exp_pagination.php | Income/expense pagination |
| student_pending_fees_detail.php | student_pending_fees_pagination.php | Pending fees pagination |

### AJAX Files

Dynamic data loading via AJAX:

| From File | Calls File | Purpose |
|-----------|------------|---------|
| [Forms with Stream] | ajax_stream_code.php | Load stream options |
| [Forms with Sections] | ajax_stream_code1.php | Load section options |
| [Forms with Subjects] | ajax_stream_code2.php | Load subject options |
| [Fees Forms] | ajax_fees_code.php | Load fees data |
| [Transport Forms] | ajax_transport_fees_code.php | Load transport fees |
| [Transport Forms] | vehicle_ajax.php | Load vehicle data |
| [Admission Forms] | checkregno.php | Check registration number |
| [Admission Forms] | checkbookno.php | Check book number |
| [RTE Forms] | rte_checkregno.php | Check RTE reg number |

---

## Navigation Patterns Summary

### Common Patterns

1. **CRUD Pattern**: Most modules follow this flow:
   - List Page → Add Page → Process → Back to List
   - List Page → Edit Page → Process → Back to List
   - List Page → Delete Page → Back to List

2. **Entry-Process Pattern**: Data entry follows:
   - Entry Form → Processing Script → Confirmation/Receipt Page → Manager Page

3. **Search Pattern**: Search functionality follows:
   - Manager Page → Search Form → Search Results → Detail View

4. **Report Pattern**: Report generation follows:
   - Manager Page → Entry Form → Report Generation → Print View

### Navigation Methods Used

- **Form POST**: Data submission (90% of form actions)
- **Redirect (header)**: After processing (all CRUD operations)
- **Link (href)**: Direct navigation (view, edit, delete buttons)
- **Include**: Shared components (headers, footers, pagination)
- **AJAX**: Dynamic data loading (dropdowns, validation)

---

## Notes

- All redirects use PHP `header("Location: ...")` after processing
- Most forms use POST method for security
- Search forms typically use GET method
- All pages include authentication check via `includes/header.php`
- Database operations are centralized in `includes/database.php`
- Session management is handled by `includes/bootstrap.php`
