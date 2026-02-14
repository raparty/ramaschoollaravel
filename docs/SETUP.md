# Detailed Setup Guide

This guide provides comprehensive setup instructions for the Laravel School Management System.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation Steps](#installation-steps)
3. [Database Configuration](#database-configuration)
4. [Initial Data Setup](#initial-data-setup)
5. [Web Server Configuration](#web-server-configuration)
6. [Troubleshooting](#troubleshooting)

## Prerequisites

### Required Software

1. **PHP 8.1 or higher**
   ```bash
   # Check PHP version
   php -v
   ```

2. **Composer** (Dependency Manager for PHP)
   ```bash
   # Install Composer (if not installed)
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   
   # Verify installation
   composer --version
   ```

3. **Database Server** (Choose one)
   - MySQL 5.7+ or MariaDB 10.3+
   - PostgreSQL 9.6+
   - SQLite 3.8+ (for development)

4. **Node.js and NPM** (Optional, for frontend assets)
   ```bash
   # Check Node.js version
   node -v
   npm -v
   ```

### Required PHP Extensions

Ensure the following PHP extensions are installed:

```bash
# Check installed extensions
php -m

# Required extensions:
# - OpenSSL
# - PDO
# - Mbstring
# - Tokenizer
# - XML
# - Ctype
# - JSON
# - BCMath
# - Fileinfo
```

Install missing extensions (Ubuntu/Debian):
```bash
sudo apt-get install php8.1-cli php8.1-mbstring php8.1-xml php8.1-bcmath php8.1-mysql
```

## Installation Steps

### 1. Clone the Repository

```bash
# Clone via HTTPS
git clone https://github.com/raparty/ramaschoollaravel.git

# Or clone via SSH
git clone git@github.com:raparty/ramaschoollaravel.git

# Navigate to directory
cd ramaschoollaravel
```

### 2. Install PHP Dependencies

```bash
# Install all Composer dependencies
composer install

# For production (optimized):
composer install --optimize-autoloader --no-dev
```

### 3. Install JavaScript Dependencies (Optional)

```bash
# Install NPM packages
npm install

# Build assets for production
npm run build

# Or for development with hot reload
npm run dev
```

### 4. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Environment Variables

Edit the `.env` file with your settings:

```env
# Application
APP_NAME="School Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (see Database Configuration section)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_erp
DB_USERNAME=root
DB_PASSWORD=your_password

# Mail Configuration (optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

## Database Configuration

### Option 1: MySQL/MariaDB

1. **Create Database**
   ```sql
   # Login to MySQL
   mysql -u root -p
   
   # Create database
   CREATE DATABASE school_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   
   # Create user (optional)
   CREATE USER 'school_user'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT ALL PRIVILEGES ON school_erp.* TO 'school_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

2. **Update .env file**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=school_erp
   DB_USERNAME=school_user
   DB_PASSWORD=strong_password
   ```

3. **Run Migrations**
   ```bash
   php artisan migrate
   ```

### Option 2: PostgreSQL

1. **Create Database**
   ```bash
   # Login to PostgreSQL
   sudo -u postgres psql
   
   # Create database and user
   CREATE DATABASE school_erp;
   CREATE USER school_user WITH ENCRYPTED PASSWORD 'strong_password';
   GRANT ALL PRIVILEGES ON DATABASE school_erp TO school_user;
   ```

2. **Update .env file**
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=school_erp
   DB_USERNAME=school_user
   DB_PASSWORD=strong_password
   ```

3. **Run Migrations**
   ```bash
   php artisan migrate
   ```

### Option 3: SQLite (Development Only)

1. **Create Database File**
   ```bash
   # Create database directory if it doesn't exist
   mkdir -p database
   
   # Create empty database file
   touch database/database.sqlite
   ```

2. **Update .env file**
   ```env
   DB_CONNECTION=sqlite
   # Comment out or remove other DB_ variables
   ```

3. **Run Migrations**
   ```bash
   php artisan migrate
   ```

## Initial Data Setup

### 1. Run Database Migrations

```bash
# Run all migrations
php artisan migrate

# Run migrations with seeding
php artisan migrate --seed

# Rollback and re-run (development only)
php artisan migrate:fresh --seed
```

### 2. Create Admin User

Option A: Using Tinker (Interactive)
```bash
php artisan tinker

# In Tinker prompt:
>>> $user = new App\Models\User();
>>> $user->name = 'Admin';
>>> $user->email = 'admin@school.com';
>>> $user->password = bcrypt('password');
>>> $user->save();
>>> exit
```

Option B: Create a Seeder
```bash
# Create seeder
php artisan make:seeder AdminUserSeeder

# Edit database/seeders/AdminUserSeeder.php
# Then run:
php artisan db:seed --class=AdminUserSeeder
```

### 3. Create Storage Symlink

```bash
# Create symbolic link for public storage
php artisan storage:link

# Verify the link was created
ls -la public/storage
```

### 4. Set File Permissions

```bash
# For Linux/Mac
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# For specific user/group (replace www-data with your web server user)
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
```

## Web Server Configuration

### Development Server

```bash
# Start Laravel development server
php artisan serve

# Specify host and port
php artisan serve --host=0.0.0.0 --port=8080
```

Access the application at: `http://localhost:8000`

### Production Server - Apache

1. **Create Virtual Host**
   ```apache
   <VirtualHost *:80>
       ServerName school.example.com
       DocumentRoot /var/www/ramaschoollaravel/public
       
       <Directory /var/www/ramaschoollaravel/public>
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog ${APACHE_LOG_DIR}/school-error.log
       CustomLog ${APACHE_LOG_DIR}/school-access.log combined
   </VirtualHost>
   ```

2. **Enable Required Modules**
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

### Production Server - Nginx

1. **Create Server Block**
   ```nginx
   server {
       listen 80;
       server_name school.example.com;
       root /var/www/ramaschoollaravel/public;
       
       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";
       
       index index.php;
       
       charset utf-8;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }
       
       error_page 404 /index.php;
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
       
       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

2. **Restart Nginx**
   ```bash
   sudo nginx -t
   sudo systemctl restart nginx
   ```

## Troubleshooting

### Common Issues

#### 1. "Class not found" errors
```bash
# Clear and regenerate autoload files
composer dump-autoload
```

#### 2. Permission denied errors
```bash
# Fix storage permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

#### 3. Database connection errors
```bash
# Verify database credentials in .env
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

#### 4. Missing encryption key
```bash
# Generate new application key
php artisan key:generate
```

#### 5. 500 Internal Server Error
```bash
# Enable debug mode in .env
APP_DEBUG=true

# Check Laravel logs
tail -f storage/logs/laravel.log

# Check web server logs
# Apache: /var/log/apache2/error.log
# Nginx: /var/log/nginx/error.log
```

#### 6. CSS/JS not loading
```bash
# Clear cache
php artisan cache:clear
php artisan view:clear

# Rebuild assets
npm run build

# Verify storage link
php artisan storage:link
```

### Performance Optimization

For production environments:

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### Clear All Caches

```bash
# Clear everything
php artisan optimize:clear

# Or individually:
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Security Checklist

Before deploying to production:

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Change default admin password
- [ ] Set strong `APP_KEY`
- [ ] Configure HTTPS/SSL
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Disable directory listing
- [ ] Configure firewall rules
- [ ] Enable CSRF protection (enabled by default)
- [ ] Set up regular database backups
- [ ] Configure mail settings for notifications
- [ ] Review and update `.env` security settings

## Next Steps

After successful setup:

1. Login with admin credentials
2. Configure school information in Settings
3. Create academic sessions and terms
4. Set up classes, sections, and subjects
5. Add fee packages
6. Start adding students and staff
7. Configure library and transport settings

For detailed usage instructions, see the main [README.md](../README.md).

## Getting Help

- Documentation: Check the `/docs` folder
- Issues: [GitHub Issues](https://github.com/raparty/ramaschoollaravel/issues)
- Quick Start: See `docs/QUICK_START_GUIDE.md`
