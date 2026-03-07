<div align="center">

<img src="https://capsule-render.vercel.app/api?type=waving&color=0:1a6b68,100:c9a84c&height=200&section=header&text=RamaSchool%20Laravel&fontSize=52&fontColor=ffffff&fontAlignY=38&desc=Modern%20School%20Management%20System&descAlignY=58&descSize=18&descColor=ffffffcc" width="100%"/>

<br/>

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-22c55e?style=for-the-badge)](LICENSE)

<br/>

> 🏫 A comprehensive, production-ready school ERP — migrated from **278+ legacy PHP files** into a clean **Laravel 10 MVC architecture**.

<br/>

| 📦 25 Models | 🎮 19 Controllers | 🛣️ 238+ Routes | 🖼️ 67+ Views | 🧩 12 Modules |
|:---:|:---:|:---:|:---:|:---:|

<br/>

</div>

---

## 📋 Table of Contents

- [✨ Features](#-features)
- [🛠️ Tech Stack](#️-tech-stack)
- [💻 System Requirements](#-system-requirements)
- [🚀 Installation](#-installation)
- [⚙️ Configuration](#️-configuration)
- [🗄️ Database Schema](#️-database-schema)
- [📖 Usage](#-usage)
- [🔒 Security](#-security)
- [🧪 Testing](#-testing)
- [🗺️ Roadmap](#️-roadmap)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)

---

## ✨ Features

### 🧩 12 Complete Management Modules

| # | Module | Description |
|---|--------|-------------|
| 🔐 | **Auth & Access Control** | Secure login, role-based permissions, session management |
| 👨‍🎓 | **Student Admissions** | Registration, enrollment, photo uploads, document management |
| 💰 | **Fee Management** | Flexible packages, receipt generation, multi-payment support |
| 📚 | **Library** | Book catalog, issue/return, overdue fines, borrowing history |
| 👥 | **Staff Management** | Profiles, departments, salary processing & attendance |
| 📝 | **Examinations** | Scheduling, mark entry, grade calc, printable marksheets |
| 📊 | **Attendance** | Bulk marking, monthly summaries, CSV export |
| 💼 | **Accounts** | Income/expense tracking, profit/loss, financial analytics |
| ⚙️ | **Settings** | School branding, academic sessions, system configuration |
| 🎓 | **Classes & Subjects** | Hierarchy management, teacher mapping, timetables |
| 🚌 | **Transport** | Fleet management, route planning, transport fee collection |
| 🏠 | **Hostel** | Room allocation, student assignments, hostel fee tracking |

### ⚡ Technical Highlights

```
✅ 100% PHP Type Hints          ✅ Comprehensive PHPDoc
✅ PSR-12 Compliant             ✅ Eloquent ORM + Relationships
✅ Database Transactions         ✅ 28 Form Request Classes
✅ CSRF Protection               ✅ Bootstrap 5 Responsive UI
✅ Chart.js Visualizations       ✅ Print-Optimized Views
✅ CSV & PDF Export              ✅ Role-Based Access Control
```

---

## 🛠️ Tech Stack

<div align="center">

| Layer | Technology |
|-------|-----------|
| **Framework** | ![Laravel](https://img.shields.io/badge/Laravel_10-FF2D20?style=flat-square&logo=laravel&logoColor=white) |
| **Language** | ![PHP](https://img.shields.io/badge/PHP_8.1+-777BB4?style=flat-square&logo=php&logoColor=white) |
| **Database** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) ![SQLite](https://img.shields.io/badge/SQLite-003B57?style=flat-square&logo=sqlite&logoColor=white) |
| **Frontend** | ![Bootstrap](https://img.shields.io/badge/Bootstrap_5-7952B3?style=flat-square&logo=bootstrap&logoColor=white) ![Blade](https://img.shields.io/badge/Blade_Templates-FF2D20?style=flat-square&logo=laravel&logoColor=white) |
| **Charts** | ![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=flat-square&logo=chartdotjs&logoColor=white) |
| **Build Tool** | ![Vite](https://img.shields.io/badge/Vite-646CFF?style=flat-square&logo=vite&logoColor=white) |
| **Code Style** | ![PSR-12](https://img.shields.io/badge/PSR--12-Compliant-22c55e?style=flat-square) |

</div>

---

## 💻 System Requirements

| Requirement | Minimum Version |
|-------------|----------------|
| 🐘 PHP | `>= 8.1` |
| 🎵 Composer | Latest |
| 🟢 Node.js | `>= 16.x` |
| 🗄️ MySQL | `>= 5.7` or PostgreSQL `9.6+` or SQLite `3.8+` |
| 🌐 Web Server | Apache / Nginx |

**Required PHP Extensions:** `OpenSSL` · `PDO` · `Mbstring` · `Tokenizer` · `XML` · `Ctype` · `JSON` · `BCMath`

---

## 🚀 Installation

### Step 1 — Clone the Repository

```bash
git clone https://github.com/raparty/ramaschoollaravel.git
cd ramaschoollaravel
```

### Step 2 — Install Dependencies

```bash
composer install
npm install
```

### Step 3 — Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials:

```env
# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_erp
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Or SQLite for local dev
# DB_CONNECTION=sqlite
```

### Step 4 — Run Migrations & Seeders

```bash
php artisan migrate

# Optional: load sample data
php artisan db:seed
```

### Step 5 — Link Storage & Set Permissions

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

### Step 6 — Build Assets & Start Server

```bash
npm run build
php artisan serve
```

> 🌐 Open **http://localhost:8000** in your browser.

> ⚠️ **Default login:** `admin@school.com` / `password` — **change this immediately after first login!**

---

## ⚙️ Configuration

### 📁 File Upload Paths

| Type | Path |
|------|------|
| 🖼️ Student photos | `storage/app/public/students/` |
| 🏫 School logos | `storage/app/public/logos/` |
| 📄 Documents | `storage/app/public/documents/` |

Set adequate upload limits in `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 10M
```

### 📧 Mail Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@school.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🗄️ Database Schema

<details>
<summary><b>⚙️ School Config</b></summary>

`schools` · `classes` · `sections` · `subjects` · `terms`

</details>

<details>
<summary><b>👨‍🎓 Students</b></summary>

`admissions` · `attendances`

</details>

<details>
<summary><b>💰 Fees</b></summary>

`fee_packages` · `student_fees` · `student_transport_fees`

</details>

<details>
<summary><b>📚 Library</b></summary>

`books` · `book_categories` · `book_issues` · `library_fines`

</details>

<details>
<summary><b>👥 Staff</b></summary>

`staff` · `departments` · `positions` · `salaries` · `staff_attendances`

</details>

<details>
<summary><b>📝 Examinations</b></summary>

`exams` · `exam_subjects` · `marks` · `results` · `grades`

</details>

<details>
<summary><b>💼 Accounts</b></summary>

`account_categories` · `incomes` · `expenses`

</details>

<details>
<summary><b>🚌 Transport & 🏠 Hostel</b></summary>

`transport_routes` · `transport_vehicles` · `hostels` · `hostel_rooms` · `hostel_allocations`

</details>

> 📘 Full schema documentation → [DB_SCHEMA_REFERENCE.md](./DB_SCHEMA_REFERENCE.md)

---

## 📖 Usage

<details>
<summary><b>👨‍🎓 Add a New Student</b></summary>

1. Go to **Students → Add New**
2. Fill in student details and upload photo
3. Assign class and section
4. Click **Save**

</details>

<details>
<summary><b>💰 Collect Fees</b></summary>

1. Go to **Fees → Collect Fee**
2. Search for student by name or registration number
3. Select fee package, term, payment method
4. Click **Collect** → receipt auto-generated

</details>

<details>
<summary><b>📚 Issue a Library Book</b></summary>

1. Go to **Library → Issue Book**
2. Search for student, select book, set due date
3. Click **Issue**

</details>

<details>
<summary><b>📊 Mark Attendance</b></summary>

1. Go to **Attendance → Mark Attendance**
2. Select class, section, and date
3. Mark each student: `Present` / `Absent` / `Late` / `Half-day` / `Leave`
4. Click **Save Attendance**

</details>

<details>
<summary><b>📝 Enter Exam Marks</b></summary>

1. Go to **Examinations → Enter Marks**
2. Select exam and class
3. Enter marks — totals & percentages calculated automatically
4. Click **Save Marks**

</details>

<details>
<summary><b>📈 Generate Reports</b></summary>

1. Go to **Reports**, pick report type and filters
2. Click **Generate**
3. Export as CSV or print

</details>

---

## 🔒 Security

| Feature | Status |
|---------|--------|
| CSRF Protection on all forms | ✅ |
| SQL Injection prevention via Eloquent ORM | ✅ |
| XSS protection via Blade templating | ✅ |
| Bcrypt password hashing | ✅ |
| Input validation via Form Request classes | ✅ |
| Middleware-based authentication | ✅ |
| Role-Based Access Control (RBAC) | ✅ |

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run a specific test
php artisan test tests/Feature/AdmissionTest.php

# Check code style (PSR-12)
./vendor/bin/pint --test

# Fix code style
./vendor/bin/pint

# Clear all caches
php artisan optimize:clear
```

---

## 🗺️ Roadmap

- [ ] 📱 REST API for mobile apps
- [ ] 👨‍👩‍👧 Parent portal & communication hub
- [ ] 💬 SMS & email notifications
- [ ] 💳 Online payment gateway integration
- [ ] 🌐 Multi-language support
- [ ] 🏢 Multi-school (SaaS) support
- [ ] 📲 Native mobile app (iOS & Android)

---

## 🤝 Contributing

Contributions are welcome! Please follow PSR-12 standards and include tests with your PR.

```bash
git checkout -b feature/YourFeature
git commit -m 'Add YourFeature'
git push origin feature/YourFeature
# Then open a Pull Request
```

See [CONTRIBUTING.md](./CONTRIBUTING.md) for full guidelines.

---

## 📄 License

This project is open-source software licensed under the [MIT License](./LICENSE).

---

<div align="center">

<img src="https://capsule-render.vercel.app/api?type=waving&color=0:c9a84c,100:1a6b68&height=100&section=footer" width="100%"/>

**Made with ❤️ for educational institutions**

⭐ **Star this repo if you find it useful!**

[![GitHub stars](https://img.shields.io/github/stars/raparty/ramaschoollaravel?style=social)](https://github.com/raparty/ramaschoollaravel/stargazers)

</div>
