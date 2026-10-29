
# Invoice and Budget Management System

A web-based financial management application built with Laravel. It helps manage customers, suppliers, products, sales invoices, purchase invoices, budgets, expenses, and financial reports.

## 1. Features

- Admin authentication and login
- Dashboard with financial summaries and charts
- Customer management (Create, Read, Update, Delete)
- Supplier management
- Product management
- Sales invoice management
- Purchase invoice management
- Invoice status and outstanding dues tracking
- Budget management
- Expense management
- Search and filtering
- Financial reports and CSV export
- Currency exchange rates using the Frankfurter API

## 2. Technology Stack

- PHP 8.2+
- Laravel 12
- MySQL
- Blade templates
- Bootstrap
- Composer
- Node.js and npm for frontend assets, if required
- Frankfurter API for currency exchange rates

## 3. Prerequisites

Install the following software:

1. PHP 8.2 or a compatible version
2. Composer
3. MySQL
4. Git
5. Node.js and npm, if required by the frontend build

On Windows, WAMP can be used to provide PHP and MySQL.

Verify the installations:

```bash
php -v
composer --version
git --version
node -v
npm -v
```

## 4. Clone the Repository

Open a terminal and run:

```bash
git https://github.com/subbiahsundar20/invoice-budget
cd invoice-budget
```

## 5. Install Dependencies

Install Laravel's PHP dependencies:

```bash
composer install
```

If the project uses npm-managed frontend assets, install and build them:

```bash
npm install
npm run build
```

## 6. Configure the Environment

Create a local environment configuration file.

Windows Command Prompt:

```cmd
copy .env.example .env
```

PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

Open `.env` and configure the application and database settings.

Example configuration:

```dotenv
APP_NAME="Invoice and Budget Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoice_budget
DB_USERNAME=root
DB_PASSWORD=
```

Adjust the username, and password according to your local MySQL installation.


## 7. Create the Database

1. Start Apache and MySQL using WAMP, if using WAMP.
2. Open phpMyAdmin or your preferred MySQL client.
3. Create a database named `invoice_budget`, or use the database name configured in `.env`.
4. Confirm that the database credentials in `.env` match your local MySQL configuration.

## 8. Run Database Migrations

Run the migrations to create the application tables:

```bash
php artisan migrate
```

If Laravel asks to create the database, confirm only if you intend to use the configured database and have verified its name.

## 9. Seed the Admin Account

The project includes an admin seeder if the `AdminUserSeeder` class is present.

Run:

```bash
php artisan db:seed --class=AdminUserSeeder
```

This executes the admin seeder and creates the initial admin account according to the seeder implementation.

Before running this command, verify the seeder file at:

`database/seeders/AdminUserSeeder.php`

Check the initial admin email, password-generation logic, and whether the seeder can be run more than once safely.

currently admin credentieals:
username:admin@example.com
password:Admin@12345

Use the initial credentials supplied by the project owner or reviewer. If the seeder defines a default password, communicate it securely to the reviewer rather than publishing it in a public repository.

## 10. Start the Application

Run the Laravel development server:

```bash
php artisan serve
```

Open the URL shown in the terminal. The default URL is:

http://127.0.0.1:8000

Navigate to the application's login page and sign in using the admin account created by the seeder.

## 11. Currency Exchange Rate API

The dashboard uses the Frankfurter API to retrieve reference exchange rates.

API endpoint:

https://api.frankfurter.dev/v2/rates?base=USD&quotes=INR,EUR,GBP

Internet access is required to retrieve exchange-rate data.

The application caches currency data to reduce repeated API requests.

If the API request fails because of SSL certificate verification on a local PHP installation, configure PHP/cURL with a valid CA certificate bundle. Do not disable SSL certificate verification.

After changing the relevant PHP configuration, restart the affected PHP or web-server process if necessary.
