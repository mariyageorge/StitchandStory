# StitchandStory

**A beginner-friendly crafting platform offering knitting, crochet, and cross-stitch kits with step-by-step tutorials.**

---

## 🧵 Features

- **User Registration & Login**: Secure authentication for users to create and manage accounts.
- **Product Catalog**: Display of various crafting kits, including knitting, crochet, and cross-stitch sets.
- **Shopping Cart**: Functionality to add, view, and modify items before checkout.
- **Order Management**: Users can view their order history and track current orders.
- **Payment Integration**: Seamless payment processing for kit purchases.
- **Admin Dashboard**: Admins can manage products, view orders, and update site content.

---

## 🚀 Deployment

To deploy the **StitchandStory** platform locally:

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/mariyageorge/StitchandStory.git
   cd StitchandStory
````

2. **Set Up the Database**:

   * Import the provided `database.sql` file into your MySQL database.

     ```sql
     CREATE DATABASE stitchandstory;
     USE stitchandstory;
     SOURCE database.sql;
     ```

   * Ensure the `orders` table is correctly configured as per the `fix_orders_table.sql` script.

3. **Configure Database Connection**:

   * Edit `config.php` to include your database credentials:

     ```php
     <?php
     define('DB_SERVER', 'localhost');
     define('DB_USERNAME', 'your_username');
     define('DB_PASSWORD', 'your_password');
     define('DB_DATABASE', 'stitchandstory');
     ?>
     ```

4. **Set Up the Web Server**:

   * Place the project files in your web server's root directory (e.g., `htdocs` for XAMPP).
   * Ensure `.htaccess` is properly configured for URL rewriting.

5. **Access the Application**:

   * Open your browser and navigate to `http://localhost/StitchandStory/`.

---

## 📦 Tech Stack

* **Frontend**: HTML, CSS, JavaScript
* **Backend**: PHP
* **Database**: MySQL
* **Web Server**: Apache

---

## 🛠️ Setup Instructions

1. **Install XAMPP/WAMP**: Download and install XAMPP or WAMP for a local development environment.
2. **Start Services**: Launch Apache and MySQL services.
3. **Import Database**: Use phpMyAdmin to import `database.sql`.
4. **Configure PHP Files**: Ensure all PHP files have the correct paths and permissions.
5. **Test Application**: Navigate to `http://localhost/StitchandStory/` to verify the setup.


* [FIX_DATABASE_ERROR.md](FIX_DATABASE_ERROR.md): Instructions to resolve common database issues.
* [PAYMENT_FORM_UPDATE.md](PAYMENT_FORM_UPDATE.md): Guide to updating payment forms.

