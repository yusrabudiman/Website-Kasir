# POS System

A PHP-based Point of Sale system with inventory management, user authentication, and sales tracking.

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Node.js and npm (for frontend assets)
- Xampp Control Panel
## Installation

### 1. Clone the repository

```bash
htdocs
git clone https://github.com/yusrabudiman/website-kasir.git
cd website-kasir
```

### 2. Install PHP dependencies (vendor folder)

This project uses Composer to manage PHP dependencies. The vendor directory contains all the required PHP libraries.

```bash
# Install Composer if you don't have it
# Windows: https://getcomposer.org/Composer-Setup.exe
# Linux/Mac: curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer

# Install PHP dependencies
composer install
```

This will create a `vendor` directory with all the required PHP packages, including:
- Ramsey/UUID: For generating unique identifiers
- Any other PHP dependencies defined in composer.json

### 3. Install JavaScript dependencies (node_modules folder)

If the project uses npm packages for frontend assets (like Tailwind CSS, etc.):

```bash
# Install Node.js and npm if you don't have them
# Visit https://nodejs.org/ to download and install

# Install JS dependencies
npm install
```

This will create a `node_modules` directory containing all JavaScript libraries and tools used for frontend development.

### 4. Set up the database

```bash
# Create database tables
mysql -u root -p pos_db < database/schema.sql
```

### 5. Configure the environment

```bash
# Copy the example environment file
cp .env.example .env

# Edit the .env file with your database credentials
nano .env
```

### 6. Run the application

```bash
# If using PHP's built-in server for development
php -S localhost:8000

# If using Apache or Nginx, configure your virtual host to point to the project's root directory
# If using Xampp Control Panel, can you click start Apache and MySQL for run frontend and server
```

## Development

### Building frontend assets

If you make changes to JavaScript or CSS files:

```bash
# For development
npm run dev

# For production
npm run build
```

### Adding new dependencies

```bash
# Add PHP dependency
composer require vendor/package

# Add JavaScript dependency
npm install package-name --save
```

## Requirements

### Composer Packages
To install required packages, run the following commands:

```bash
composer require phpoffice/phpspreadsheet
```

This package is needed for:
- Modern Excel (XLSX) export in Audit Trail feature
- Spreadsheet generation and manipulation

## Troubleshooting

### Common issues with vendor directory

1. **Class not found errors**: If you encounter "Class not found" errors, try:
   ```bash
   composer dump-autoload
   ```

2. **Permission issues**: Make sure the `vendor` directory has appropriate permissions:
   ```bash
   chmod -R 755 vendor
   ```

### Common issues with node_modules

1. **Module not found errors**: If npm packages are missing:
   ```bash
   rm -rf node_modules
   npm install
   ```

2. **Outdated packages**: Update all packages to their latest versions:
   ```bash
   npm update
   ```

## More Documentation Pos System
- [Introduction Website-Kasir (POS System)](https://github.com/yusrabudiman/website-kasir/wiki/Introduction-Website-Kasir-(Point-of-Sale-System))
- [System Design (POS System)](https://github.com/yusrabudiman/website-kasir/wiki/System-Design-POS-Website-(Point-of-Sale-System))
- [Website Kasir (POS System) View Documentation](https://github.com/yusrabudiman/website-kasir/wiki/Website-Kasir-(Point-of-Sale-System)-View-Documentation)
