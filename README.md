🥣 Pure Oats with Nuts — E-Commerce Website

A responsive and user-friendly e-commerce website developed using PHP, MySQL, HTML, CSS, and JavaScript for selling oats and related products online.

The project includes a customer-facing online store, shopping cart, checkout system, order management, admin dashboard, email notifications, shipping calculations, and essential store policy pages.

🚀 Features
🛍️ Customer Store
Responsive homepage
Product showcase
Oats product details
Add to cart functionality
Cart quantity management
Order checkout
Customer information collection
Customer email collection
Shipping location selection
City-based shipping charges
Free shipping threshold
Cash on Delivery availability for supported locations
Order confirmation page
WhatsApp/contact integration
Responsive design for desktop and mobile
🛒 Shopping Cart
Add products to cart
Update product quantity
Remove products
Cart total calculation
Shipping calculation
Order summary
Session-based cart management
💳 Checkout

The checkout system collects:

Customer name
Phone number
Email address
Shipping location
Complete delivery address
Order details

The system automatically calculates:

Product subtotal
Shipping charges
Final order total
📦 Order Management

Orders are stored in MySQL and can be managed through the admin panel.

Admin can:

View orders
View order details
Check customer information
Check ordered products
Check order totals
Update order status
Manage order workflow
Log out securely
📧 Email Notifications

The application supports Gmail SMTP using PHP sockets and OpenSSL.

Email functionality includes:

New order notification
Customer order status notification
Order status update confirmation
HTML + plain-text email messages

SMTP credentials are loaded through environment variables instead of being stored in source code.

🚚 Shipping System

Shipping charges can be configured according to city.

Current configured locations include:

Hyderabad
Karachi
Lahore
Islamabad
Rawalpindi
Multan
Faisalabad
Peshawar
Quetta
Other cities

A free-shipping minimum can also be configured.

📄 Store Policies

The project includes:

Privacy Policy
Return Policy
Shipping Policy
Terms & Conditions
🔍 SEO

The project includes:

robots.txt
sitemap.xml
SEO-friendly basic website structure
Responsive layout
Semantic page structure
🧰 Technologies Used
Frontend
HTML5
CSS3
JavaScript
Backend
PHP
MySQL
PHP Sessions
MySQLi
Email
Gmail SMTP
PHP OpenSSL
PHP socket connection
Server
Apache
XAMPP / LAMP / compatible PHP hosting
📁 Project Structure
oats-store/
│
├── admin/
│   ├── dashboard.php
│   ├── index.php
│   ├── logout.php
│   ├── order_detail.php
│   └── reset_password.php
│
├── config/
│   ├── db.php
│   └── email.php
│
├── database/
│   ├── migrate_customer_email.php
│   └── migrations/
│       └── 20261007_add_customer_email.sql
│
├── includes/
│   ├── header.php
│   └── footer.php
│
├── index.php
├── cart.php
├── checkout.php
├── order_success.php
│
├── privacy_policy.php
├── return_policy.php
├── shipping_policy.php
├── terms_conditions.php
│
├── EMAIL_SETUP.md
├── .htaccess
├── robots.txt
├── sitemap.xml
└── oats-product.jpg
⚙️ Installation
1. Clone the Repository
git clone https://github.com/YOUR-USERNAME/oats-store-php-mysql.git

Move into the project directory:

cd oats-store-php-mysql
2. Install XAMPP

Install XAMPP with:

Apache
MySQL
PHP
phpMyAdmin

Place the project inside:

C:\xampp\htdocs\

Example:

C:\xampp\htdocs\oats-store\
🗄️ Database Setup
1. Start XAMPP

Start:

Apache
MySQL
2. Open phpMyAdmin

Open:

http://localhost/phpmyadmin

Create a new database:

oats_store
3. Import Database

Import the project's database SQL file if provided with the deployment/database backup.

The application expects the database name:

oats_store
🔧 Database Configuration

Open:

config/db.php

Configure:

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "oats_store";

For a normal XAMPP installation, the default MySQL username is usually:

root

and the local password is commonly empty unless you configured one.

🌐 Run the Website

After starting Apache and MySQL, open:

http://localhost/oats-store/

Admin panel:

http://localhost/oats-store/admin/
📧 Email Configuration

The application uses Gmail SMTP.

Email credentials should NOT be placed directly inside the PHP source code.

Configure these environment variables:

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_APP_PASSWORD=your-google-app-password
SMTP_FROM_EMAIL=your-email@gmail.com
SMTP_FROM_NAME=Pure Oats with Nuts
Gmail App Password

You should:

Enable Google 2-Step Verification.
Create a Google App Password.
Use the generated App Password for SMTP_APP_PASSWORD.
Never use your normal Gmail password.
Never commit the App Password to GitHub.
🛠️ Customer Email Database Migration

If the existing database does not contain the customer email column, run the migration:

ALTER TABLE orders
    ADD COLUMN customer_email VARCHAR(254) NULL AFTER customer_name;

CREATE INDEX idx_orders_customer_email
ON orders (customer_email);

Alternatively, run the migration PHP script:

php database/migrate_customer_email.php
🔐 Security Notes

Before deploying this project to production:

Do not upload passwords to GitHub.
Do not upload Gmail App Passwords.
Use environment variables for SMTP credentials.
Disable PHP error display in production.
Use HTTPS.
Use a strong MySQL password.
Change default admin credentials.
Keep PHP and MySQL updated.
Restrict access to sensitive configuration files.
Validate and sanitize all user input.
Use prepared SQL statements.
Keep regular database backups.
⚠️ Production Configuration

The development configuration may display PHP errors.

Before production deployment, disable:

error_reporting(E_ALL);
ini_set('display_errors', 1);

Use server-side logging instead of displaying errors to customers.

📱 Responsive Design

The website is designed to work across:

Desktop
Laptop
Tablet
Mobile phones
📦 Main Modules
Module	Description
Store	Customer-facing online store
Cart	Shopping cart management
Checkout	Customer order submission
Orders	Order storage and processing
Admin	Order management dashboard
Email	Order and status notifications
Shipping	City-based shipping calculation
Policies	Store legal/policy pages
SEO	robots.txt and sitemap
🎯 Project Purpose

This project demonstrates how a complete small-scale e-commerce platform can be developed using core PHP and MySQL without relying on a CMS.

It can be used as:

E-commerce project
PHP/MySQL portfolio project
Online store starter project
Learning project
Small business website
Custom product-selling platform
👨‍💻 Developer

Sarfraz Ahmed

Founder & CEO — Code With Sheru

Skills Demonstrated
PHP
MySQL
HTML5
CSS3
JavaScript
Responsive Web Design
E-commerce Development
Admin Dashboard Development
Database Management
SMTP Integration
Order Management
SEO Fundamentals
📄 License

This project is provided for educational and development purposes.

Before using this project commercially, verify ownership of all source code, images, branding, product information, and third-party assets.

⭐ Support

If you find this project useful, consider giving the repository a ⭐ on GitHub.
