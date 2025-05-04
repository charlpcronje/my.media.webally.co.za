# Installation Guide

This guide provides detailed instructions for installing and configuring the Media Share platform.

## Prerequisites

Before beginning installation, ensure your system meets the following requirements:

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled
- Node.js 14.x or higher (for frontend development)
- Composer (for PHP dependencies)
- npm (for JavaScript dependencies)

## Server Configuration

### 1. Virtual Host Setup

The Media Share platform is designed to work with subdomain-based routing. Configure your web server with the following subdomains:

- `my.media.example.com` - Frontend application
- `api.media.example.com` - API endpoints
- `admin.media.example.com` - Admin panel

Replace `example.com` with your actual domain name.

#### Apache Virtual Host Configuration Example

```apache
# Frontend Virtual Host
<VirtualHost *:80>
    ServerName my.media.example.com
    DocumentRoot /var/www/html/my.media.example.com/frontend/dist
    
    <Directory /var/www/html/my.media.example.com/frontend/dist>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/media_frontend_error.log
    CustomLog ${APACHE_LOG_DIR}/media_frontend_access.log combined
</VirtualHost>

# API Virtual Host
<VirtualHost *:80>
    ServerName api.media.example.com
    DocumentRoot /var/www/html/my.media.example.com/backend/api
    
    <Directory /var/www/html/my.media.example.com/backend/api>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/media_api_error.log
    CustomLog ${APACHE_LOG_DIR}/media_api_access.log combined
</VirtualHost>

# Admin Virtual Host
<VirtualHost *:80>
    ServerName admin.media.example.com
    DocumentRoot /var/www/html/my.media.example.com/backend/admin
    
    <Directory /var/www/html/my.media.example.com/backend/admin>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/media_admin_error.log
    CustomLog ${APACHE_LOG_DIR}/media_admin_access.log combined
</VirtualHost>
```

Don't forget to enable the virtual hosts:

```bash
sudo a2ensite your-virtualhost-file
sudo systemctl reload apache2
```

### 2. SSL Configuration (Recommended)

For production environments, configure SSL certificates for secure connections:

```bash
# Install Certbot (if not already installed)
sudo apt-get install certbot python3-certbot-apache

# Generate certificates for all subdomains
sudo certbot --apache -d my.media.example.com -d api.media.example.com -d admin.media.example.com
```

## Application Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/media-share.git /var/www/html/my.media.example.com
cd /var/www/html/my.media.example.com
```

### 2. Backend Setup

#### Database Configuration

1. Create a new MySQL database:

```sql
CREATE DATABASE media_share CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'media_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON media_share.* TO 'media_user'@'localhost';
FLUSH PRIVILEGES;
```

2. Import the database schema:

```bash
mysql -u media_user -p media_share < backend/database_updates.sql
```

3. Configure database connection:

Create a copy of the config template and update it with your database credentials:

```bash
cp backend/config.template.php backend/config.php
```

Edit the `config.php` file to set your database connection parameters:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'media_share');
define('DB_USER', 'media_user');
define('DB_PASS', 'your_password');
```

#### Directory Setup

Create necessary directories and set proper permissions:

```bash
php backend/setup_media_dirs.php
```

### 3. Frontend Setup

Install dependencies and build the frontend application:

```bash
cd frontend
npm install
npm run build
```

## Post-Installation Steps

### 1. Create Admin User

The default admin user (username: `admin`, password: `admin123`) is created automatically when you run the database initialization script. It's strongly recommended to change this password immediately after installation.

### 2. Verify Installation

Visit the following URLs to verify that your installation is working correctly:

- Frontend: `https://my.media.example.com`
- Admin Panel: `https://admin.media.example.com` (login with admin/admin123)
- API (test endpoint): `https://api.media.example.com/media`

## Troubleshooting

If you encounter issues during installation, check the following:

### Common Issues

1. **404 Not Found errors**:
   - Ensure your virtual host configurations are correct
   - Verify that mod_rewrite is enabled: `sudo a2enmod rewrite`
   - Check that .htaccess files are being processed (AllowOverride All)

2. **Database connection errors**:
   - Verify database credentials in config.php
   - Ensure the MySQL user has proper permissions
   - Check that MySQL service is running

3. **Permission issues**:
   - Ensure the web server user (www-data) has write permissions to the uploads directories
   - Fix permissions if needed: `sudo chown -R www-data:www-data /var/www/html/my.media.example.com/media`

For more troubleshooting information, see the [Troubleshooting Guide](troubleshooting.md).