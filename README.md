# 🏫 Laravel School Management System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-red.svg" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.1+-blue.svg" alt="PHP Version">
  <img src="https://img.shields.io/badge/Bootstrap-5.x-purple.svg" alt="Bootstrap Version">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

A comprehensive, modern school management system built with Laravel 10, featuring complete student, staff, fee, library, examination, and administrative management capabilities. Migrated from 278+ legacy PHP files to a modern MVC architecture with production-ready code quality.

## 📋 Table of Contents

- [Features](#-features)
- [Screenshots](#-screenshots)
- [System Requirements](#-system-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Database Setup](#-database-setup)
- [Usage](#-usage)
- [Module Overview](#-module-overview)
- [Architecture](#-architecture)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

### Core Modules (12 Complete Systems)

1. **🔐 Authentication & Access Control**
   - Secure user authentication system
   - Role-based access control (RBAC)
   - Permission management
   - Session management

2. **👨‍🎓 Student Admissions Management**
   - Complete student registration and enrollment
   - Photo upload and document management
   - Student profile management
   - Advanced search and filtering
   - Print-friendly student reports

3. **💰 Fee Management**
   - Flexible fee package configuration
   - Fee collection and receipt generation
   - Multiple payment methods (Cash, Cheque, Bank Transfer, Online, Card)
   - Pending fees tracking and reports
   - Term-wise fee management
   - Print-optimized receipts

4. **📚 Library Management**
   - Book catalog with categorization
   - Book issue and return system
   - Fine calculation for overdue books
   - Student borrowing history
   - Overdue book reports
   - Fine collection management

5. **👥 Staff Management**
   - Staff registration and profile management
   - Department and position hierarchy
   - Qualification tracking
   - Salary processing and slip generation
   - Staff attendance tracking
   - Bulk salary generation

6. **📝 Examination System**
   - Exam scheduling and management
   - Subject-wise mark entry
   - Automatic result generation
   - Grade calculation with ranking
   - Professional printable marksheets
   - Class performance statistics
   - Chart.js data visualization

7. **📊 Attendance Tracking**
   - Bulk attendance marking
   - Multiple status types (Present, Absent, Late, Half-day, Leave)
   - Student-wise attendance reports
   - Class-wise attendance reports
   - Monthly attendance summaries
   - CSV export functionality

8. **💼 Accounts Management**
   - Income and expense tracking
   - Category management (Income/Expense types)
   - Financial reports and analytics
   - Profit/loss calculations
   - Category-wise breakdown with percentages
   - Payment method tracking
   - CSV export and print optimization

9. **⚙️ Settings & Configuration**
   - System-wide settings management
   - Academic session management
   - School information and branding
   - Logo upload capability
   - Single active session enforcement

10. **🎓 Classes & Subjects**
    - Class hierarchy management (1-12)
    - Section management with capacity tracking
    - Subject catalog with type management
    - Class-subject assignment
    - Teacher mapping
    - Timetable generation with period allocation

11. **🚌 Transport Management**
    - Vehicle fleet management
    - Route planning with stop management
    - Student-route-vehicle assignments
    - Transport fee collection
    - Capacity tracking
    - Route-wise reports

12. **📈 Reports & Analytics**
    - Comprehensive dashboards (Main, Admin, Analytics)
    - Cross-module reporting
    - Student comprehensive reports
    - Academic performance analytics
    - Financial summaries
    - Data visualization with charts
    - CSV and PDF export functionality

### Technical Features

- ✅ **100% Type Hints** - Complete PHP type safety
- ✅ **Comprehensive PHPDoc** - Full documentation
- ✅ **PSR-12 Compliance** - Standard coding style
- ✅ **Eloquent Relationships** - Proper ORM usage
- ✅ **Database Transactions** - Data integrity
- ✅ **Form Validation** - Request validation classes
- ✅ **CSRF Protection** - Security built-in
- ✅ **Bootstrap 5** - Modern responsive design
- ✅ **Mobile-Friendly** - Responsive layouts
- ✅ **Print Optimization** - Professional print views

## 📸 Screenshots

### Dashboard
![Dashboard](screenshots/dashboard_fluent_ui.png)
*Main dashboard with key metrics and statistics*

### Student Admissions
![Admissions](screenshots/admission_fluent_ui.png)
*Student admission and enrollment interface*

### Fee Management
![Fee Manager](screenshots/fees_manager_fluent_ui.png)
*Fee collection and receipt generation*

## 💻 System Requirements

- **PHP**: >= 8.1
- **Composer**: Latest version
- **Database**: MySQL 5.7+ / PostgreSQL 9.6+ / SQLite 3.8+
- **Web Server**: Apache / Nginx
- **Node.js**: >= 16.x (for asset compilation)
- **Extensions**: 
  - OpenSSL PHP Extension
  - PDO PHP Extension
  - Mbstring PHP Extension
  - Tokenizer PHP Extension
  - XML PHP Extension
  - Ctype PHP Extension
  - JSON PHP Extension
  - BCMath PHP Extension

## 🚀 Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/raparty/ramaschoollaravel.git
cd ramaschoollaravel
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (optional, for asset compilation)
npm install
```

### Step 3: Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database

Edit the `.env` file with your database credentials:

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_erp
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**For SQLite (Development):**
```env
DB_CONNECTION=sqlite
# DB_DATABASE will be database/database.sqlite
```

### Step 5: Run Migrations

```bash
# Create database tables
php artisan migrate

# (Optional) Seed with sample data
php artisan db:seed
```

### Step 6: Create Storage Symlink

```bash
php artisan storage:link
```

### Step 7: Set Directory Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

### Step 8: Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## ⚙️ Configuration

### Application Settings

Edit `.env` file to configure:

```env
APP_NAME="School Management System"
APP_ENV=local  # Change to 'production' for live deployment
APP_DEBUG=true # Change to 'false' in production
APP_URL=http://localhost:8000
```

### File Uploads

The system stores uploaded files in:
- Student photos: `storage/app/public/students/`
- School logos: `storage/app/public/logos/`
- Documents: `storage/app/public/documents/`

Configure maximum upload size in `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 10M
```

### Mail Configuration

For sending emails (receipts, reports), configure in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@schoolsystem.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 🗄️ Database Setup

### Database Schema

The application uses a single comprehensive migration file that creates all necessary tables:

**Core Tables:**
- `schools` - School information and configuration
- `classes` - Academic class levels (1-12)
- `sections` - Class divisions
- `subjects` - Subject catalog
- `terms` - Academic terms/semesters

**Student Management:**
- `admissions` - Student registration and profiles
- `attendances` - Daily attendance records

**Fee Management:**
- `fee_packages` - Fee package definitions
- `student_fees` - Fee collection records
- `student_transport_fees` - Transport fee records

**Library:**
- `books` - Book catalog
- `book_categories` - Book categorization
- `book_issues` - Book borrowing records
- `library_fines` - Fine management

**Staff:**
- `staff` - Staff profiles
- `departments` - Staff departments
- `positions` - Staff positions
- `salaries` - Salary records
- `staff_attendances` - Staff attendance

**Examinations:**
- `exams` - Exam definitions
- `exam_subjects` - Exam-subject mappings
- `marks` - Student marks
- `results` - Calculated results
- `grades` - Grade definitions

**Accounts:**
- `account_categories` - Income/Expense categories
- `incomes` - Income records
- `expenses` - Expense records

**Transport:**
- `transport_routes` - Bus routes
- `transport_vehicles` - Fleet management

### Sample Data

To populate the database with sample data for testing:

```bash
php artisan db:seed
```

### Database Backup

Create regular backups:

```bash
# Using Laravel
php artisan db:backup

# Or using mysqldump (for MySQL)
mysqldump -u username -p school_erp > backup.sql
```

## 📖 Usage

### Default Login

After installation, access the system at `http://localhost:8000`

**Default Credentials:**
```
Email: admin@school.com
Password: password
```

> ⚠️ **Important**: Change the default password immediately after first login!

### Common Tasks

#### Adding a New Student
1. Navigate to **Students → Add New**
2. Fill in student details
3. Upload student photo (optional)
4. Assign class and section
5. Click **Save**

#### Collecting Fees
1. Navigate to **Fees → Collect Fee**
2. Search for student by name or registration number
3. Select fee package and term
4. Enter amount and payment method
5. Click **Collect** to generate receipt

#### Issuing Library Books
1. Navigate to **Library → Issue Book**
2. Search for student
3. Select book from catalog
4. Set due date
5. Click **Issue**

#### Recording Attendance
1. Navigate to **Attendance → Mark Attendance**
2. Select class and section
3. Choose date
4. Mark status for each student (Present/Absent/Late/Half-day/Leave)
5. Click **Save Attendance**

#### Entering Exam Marks
1. Navigate to **Examinations → Enter Marks**
2. Select exam and class
3. Enter marks for each student
4. System automatically calculates totals and percentages
5. Click **Save Marks**

#### Generating Reports
1. Navigate to **Reports**
2. Select report type
3. Set filters (date range, class, etc.)
4. Click **Generate**
5. Export as CSV or print

## 📦 Module Overview

### Application Structure

```
ramaschoollaravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # 19 Controllers
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── AdmissionController.php
│   │   │   ├── FeeController.php
│   │   │   ├── LibraryController.php
│   │   │   ├── StaffController.php
│   │   │   ├── ExamController.php
│   │   │   └── ... (12 more)
│   │   └── Requests/            # Form Validation Classes
│   ├── Models/                  # 25 Eloquent Models
│   │   ├── Admission.php
│   │   ├── Staff.php
│   │   ├── Book.php
│   │   └── ... (22 more)
│   └── Providers/
├── database/
│   ├── migrations/              # Database migrations
│   ├── seeders/                 # Database seeders
│   └── database.sqlite          # SQLite database (if used)
├── resources/
│   └── views/                   # 67+ Blade Templates
│       ├── layouts/             # Master layouts
│       ├── admissions/          # Student views
│       ├── fees/                # Fee views
│       ├── library/             # Library views
│       ├── staff/               # Staff views
│       ├── exams/               # Examination views
│       └── ... (10 more modules)
├── routes/
│   └── web.php                  # ~238 Routes
├── public/                      # Public assets
├── storage/                     # File storage
├── legacy_php/                  # Legacy PHP files (archived)
└── docs/                        # Documentation
```

### Statistics

- **Models**: 25 Eloquent models
- **Controllers**: 19 controllers with full CRUD
- **Form Requests**: 28 validation classes
- **Routes**: ~238 defined routes
- **Views**: 67+ Blade templates
- **Migrations**: Complete database schema
- **Code Quality**: 100% type hints, comprehensive PHPDoc

## 🏗️ Architecture

### Technology Stack

- **Framework**: Laravel 10.x
- **PHP**: 8.1+
- **Database**: MySQL / PostgreSQL / SQLite
- **Frontend**: Bootstrap 5, Blade Templates
- **Charts**: Chart.js for data visualization
- **Icons**: Bootstrap Icons
- **Forms**: Laravel Form Requests for validation

### Design Patterns

- **MVC Architecture**: Clean separation of concerns
- **Repository Pattern**: (Optional) for data access
- **Service Layer**: Business logic separation
- **Form Requests**: Validation abstraction
- **Eloquent ORM**: Database abstraction
- **Blade Components**: Reusable UI components

### Security Features

- ✅ CSRF Protection on all forms
- ✅ SQL Injection prevention via Eloquent ORM
- ✅ XSS Protection with Blade templating
- ✅ Password hashing with bcrypt
- ✅ Input validation and sanitization
- ✅ Middleware-based authentication
- ✅ Role-based access control

## 🔧 Development

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AdmissionTest.php
```

### Code Style

The project follows PSR-12 coding standards:

```bash
# Check code style
./vendor/bin/pint --test

# Fix code style issues
./vendor/bin/pint
```

### Asset Compilation

```bash
# Development mode
npm run dev

# Production build
npm run build

# Watch for changes
npm run watch
```

### Clearing Cache

```bash
# Clear all caches
php artisan optimize:clear

# Clear specific caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## 📝 API Documentation

The system includes RESTful routes for all modules. Example endpoints:

```
GET    /admissions              - List all students
POST   /admissions              - Create new student
GET    /admissions/{id}         - Show student details
PUT    /admissions/{id}         - Update student
DELETE /admissions/{id}         - Delete student
GET    /admissions/search       - Search students
```

For complete API documentation, run:
```bash
php artisan route:list
```

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines

- Follow PSR-12 coding standards
- Write comprehensive PHPDoc comments
- Include type hints for all parameters and return types
- Add tests for new features
- Update documentation as needed

## 🐛 Bug Reports

If you discover a bug, please open an issue with:
- Clear description of the issue
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots (if applicable)
- Environment details (PHP version, database, etc.)

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI powered by [Bootstrap 5](https://getbootstrap.com)
- Icons from [Bootstrap Icons](https://icons.getbootstrap.com)
- Charts by [Chart.js](https://www.chartjs.org)

## 📞 Support

For support and questions:
- GitHub Issues: [Create an issue](https://github.com/raparty/ramaschoollaravel/issues)
- Documentation: Check the `docs/` folder
- Legacy Reference: See `legacy_php/` for original implementation

## 🗺️ Roadmap

### Completed (82% Overall)
- ✅ All 12 major modules implemented
- ✅ 278+ legacy files migrated to Laravel
- ✅ Complete database schema
- ✅ Production-ready code quality

### Future Enhancements
- [ ] REST API for mobile apps
- [ ] Parent portal
- [ ] SMS notifications
- [ ] Email notifications
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] Multi-school support
- [ ] Online payment gateway integration
- [ ] Mobile application (iOS/Android)

---

**Last Updated**: February 14, 2026  
**Version**: 1.0.0  
**Laravel Version**: 10.x  
**PHP Version**: 8.1+

Made with ❤️ for educational institutions
