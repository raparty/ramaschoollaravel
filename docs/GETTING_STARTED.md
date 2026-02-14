# Getting Started with Laravel School Management System

Welcome! This guide will help you get the Laravel School Management System up and running quickly.

## Prerequisites Checklist

Before you begin, make sure you have:

- [ ] PHP 8.1 or higher installed
- [ ] Composer installed
- [ ] A database (MySQL, PostgreSQL, or SQLite)
- [ ] Basic knowledge of Laravel (optional but helpful)

## Quick Start (5 Minutes)

### 1. Clone and Install

```bash
# Clone the repository
git clone https://github.com/raparty/ramaschoollaravel.git
cd ramaschoollaravel

# Install dependencies
composer install
```

### 2. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Set Up Database

**Option A: SQLite (Easiest for testing)**
```bash
# Create database file
touch database/database.sqlite

# Update .env file - set DB_CONNECTION=sqlite
# Then run migrations
php artisan migrate
```

**Option B: MySQL**
```bash
# Create database in MySQL
mysql -u root -p -e "CREATE DATABASE school_erp"

# Update .env with your database credentials:
# DB_CONNECTION=mysql
# DB_DATABASE=school_erp
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate
```

### 4. Create Storage Link

```bash
php artisan storage:link
```

### 5. Start the Server

```bash
php artisan serve
```

Open your browser and visit: **http://localhost:8000**

## First Steps After Installation

### 1. Create Admin User

```bash
php artisan tinker
```

Then in the Tinker console:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@school.com';
$user->password = bcrypt('password');
$user->save();
exit
```

### 2. Login

- URL: http://localhost:8000
- Email: admin@school.com
- Password: password

⚠️ **Important**: Change the default password after first login!

### 3. Initial Setup

After logging in, configure these in order:

1. **Settings** → School Information
   - Add school name, address, logo
   
2. **Settings** → Academic Session
   - Create academic year/session
   
3. **Classes & Subjects** → Classes
   - Add classes (e.g., Class 1, Class 2, etc.)
   
4. **Classes & Subjects** → Sections
   - Add sections for each class (e.g., A, B, C)
   
5. **Classes & Subjects** → Subjects
   - Add subjects (e.g., Mathematics, English, Science)

6. **Fee Management** → Fee Packages
   - Create fee packages with amounts

Now you're ready to:
- Add students
- Collect fees
- Manage library
- Record attendance
- And more!

## Common Tasks

### Adding a Student

1. Navigate to **Students** → **Add New Student**
2. Fill in the registration form
3. Upload student photo (optional)
4. Assign class and section
5. Click **Save**

### Collecting Fees

1. Go to **Fees** → **Collect Fee**
2. Search for student
3. Select fee package
4. Enter amount and payment method
5. Click **Collect** to generate receipt

### Recording Attendance

1. Go to **Attendance** → **Mark Attendance**
2. Select class, section, and date
3. Mark each student as Present/Absent/Late/Half-day/Leave
4. Click **Save Attendance**

## Need Help?

### Documentation

- **Complete Setup Guide**: [docs/SETUP.md](SETUP.md)
- **Quick Start Guide**: [docs/QUICK_START_GUIDE.md](QUICK_START_GUIDE.md)
- **Main README**: [README.md](../README.md)

### Troubleshooting

**Can't connect to database?**
- Check your `.env` database credentials
- Make sure MySQL/PostgreSQL service is running
- For SQLite, ensure `database/database.sqlite` file exists

**Getting 500 errors?**
- Set `APP_DEBUG=true` in `.env` temporarily
- Check `storage/logs/laravel.log` for errors
- Ensure storage folders have write permissions: `chmod -R 775 storage`

**CSS/JS not loading?**
- Run `php artisan storage:link`
- Clear cache: `php artisan cache:clear`

**"Class not found" errors?**
- Run `composer dump-autoload`

### Getting Support

- GitHub Issues: [Report a bug or request a feature](https://github.com/raparty/ramaschoollaravel/issues)
- Documentation: Check the `/docs` folder for detailed guides

## What's Next?

Once you've got the basics down:

1. **Explore all modules** - Check out Library, Staff, Exams, Transport
2. **Customize settings** - Adjust to your school's needs
3. **Add data** - Start entering real school data
4. **Generate reports** - Use the reporting features
5. **Contribute** - See [CONTRIBUTING.md](../CONTRIBUTING.md) to help improve the project

## Video Tutorials (Coming Soon)

We're working on video tutorials for:
- Installation walkthrough
- Complete feature overview
- Common workflows
- Tips and tricks

Stay tuned!

---

**Happy managing! 🎓**

For detailed documentation, see the main [README.md](../README.md) file.
