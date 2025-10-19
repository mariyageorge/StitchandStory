# StitchandStory

**A beginner-friendly crafting platform offering knitting, crochet, and cross-stitch kits with step-by-step tutorials.**

---

## 🧵 Features

- **User Registration & Login**: Secure authentication for users to create and manage accounts.
- **Product Catalog**: Browse various crafting kits, including knitting, crochet, and cross-stitch sets.
- **Shopping Cart**: Add, view, and modify items before checkout.
- **Order Management**: View order history and track current orders.
- **Payment Integration**: Seamless payment processing for kit purchases.
- **Admin Dashboard**: Manage products and orders efficiently.
- **Automatic Database Setup**: Database and tables are created automatically on first run.

---

## 🚀 Quick Start

### Prerequisites

- **PHP 7.4+** with MySQLi extension
- **MySQL 5.7+** or **MariaDB 10.2+**
- **Apache** or any PHP-compatible web server
- **XAMPP/WAMP/MAMP** (recommended for local development)

---

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/mariyageorge/StitchandStory.git
cd StitchandStory
```

### 2. Configure Database Connection

Edit the `Database.php` file (or `config.php` if separate) with your database credentials:

```php
private $host = 'localhost';
private $db_name = 'stitch_and_story';
private $username = 'root';
private $password = '';
```

**Note**: The database will be created automatically when you first run the application.

### 3. Set Up Web Server

#### Option A: Using XAMPP
1. Copy the project folder to `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux)
2. Start Apache and MySQL from XAMPP Control Panel
3. Access the application at `http://localhost/StitchandStory/`

#### Option B: Using PHP Built-in Server
```bash
cd StitchandStory
php -S localhost:8000
```
Access at `http://localhost:8000/`

### 4. First Run

When you first access the application:
- The database `stitch_and_story` will be created automatically
- All necessary tables will be created
- Sample products will be inserted for testing

---

## 🗄️ Database Schema

The application automatically creates the following tables:

### **users**
| Column | Type | Description |
|--------|------|-------------|
| user_id | INT (PK) | Auto-incrementing user ID |
| username | VARCHAR(100) | User's display name |
| email | VARCHAR(150) | User's email (unique) |
| password | VARCHAR(255) | Hashed password |
| created_at | TIMESTAMP | Account creation date |

### **products**
| Column | Type | Description |
|--------|------|-------------|
| product_id | INT (PK) | Auto-incrementing product ID |
| name | VARCHAR(150) | Product name |
| category | VARCHAR(100) | Product category |
| description | TEXT | Product description |
| price | DECIMAL(10,2) | Product price |
| image | VARCHAR(255) | Image filename |
| created_at | TIMESTAMP | Product creation date |

### **cart**
| Column | Type | Description |
|--------|------|-------------|
| cart_id | INT (PK) | Auto-incrementing cart ID |
| user_id | INT (FK) | References users table |
| product_id | INT (FK) | References products table |
| quantity | INT | Number of items |
| added_at | TIMESTAMP | Date added to cart |

### **payments**
| Column | Type | Description |
|--------|------|-------------|
| payment_id | INT (PK) | Auto-incrementing payment ID |
| user_id | INT (FK) | References users table |
| amount | DECIMAL(10,2) | Payment amount |
| transaction_id | VARCHAR(255) | Transaction identifier |
| payment_status | ENUM | success/failed/pending |
| payment_date | TIMESTAMP | Payment timestamp |

### **orders**
| Column | Type | Description |
|--------|------|-------------|
| order_id | INT (PK) | Auto-incrementing order ID |
| user_id | INT (FK) | References users table |
| payment_id | INT (FK) | References payments table |
| total_amount | DECIMAL(10,2) | Order total |
| delivery_address | TEXT | Shipping address |
| order_status | ENUM | pending/processing/shipped/delivered/cancelled |
| created_at | TIMESTAMP | Order creation date |

### **order_items**
| Column | Type | Description |
|--------|------|-------------|
| order_item_id | INT (PK) | Auto-incrementing item ID |
| order_id | INT (FK) | References orders table |
| product_id | INT (FK) | References products table |
| product_name | VARCHAR(150) | Product name snapshot |
| product_price | DECIMAL(10,2) | Price at time of order |
| quantity | INT | Number of items |
| subtotal | DECIMAL(10,2) | Line item total |

---

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL / MariaDB
- **Web Server**: Apache
- **Architecture**: MVC pattern with OOP

---

## 📁 Project Structure

```
StitchandStory/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
│   └── Database.php          # Database connection & setup
├── includes/
│   ├── header.php
│   └── footer.php
├── pages/
│   ├── index.php             # Home page
│   ├── products.php          # Product catalog
│   ├── cart.php              # Shopping cart
│   ├── checkout.php          # Checkout process
│   └── orders.php            # Order history
├── .htaccess                 # URL rewriting rules
└── README.md
```

---

## 🔐 Security Features

- **Password Hashing**: All passwords are hashed using PHP's `password_hash()`
- **Prepared Statements**: Protection against SQL injection
- **Foreign Key Constraints**: Database integrity enforcement
- **Session Management**: Secure user session handling

---


### Test User Accounts

Create test accounts through the registration page or manually insert into the database.

---

## 🐛 Troubleshooting

### Database Connection Issues

If you encounter connection errors:

1. Verify MySQL is running
2. Check credentials in `Database.php`
3. Ensure the MySQL user has CREATE DATABASE privileges
4. Check PHP MySQLi extension is enabled in `php.ini`

### Table Creation Failures

If tables aren't created automatically:

```sql
-- Run this manually in phpMyAdmin or MySQL CLI
CREATE DATABASE IF NOT EXISTS stitch_and_story;
USE stitch_and_story;
```

Then refresh the application page.

### Permission Errors

Ensure the web server has write permissions for:
- Upload directories
- Session storage
- Log files

---

## 🤝 Contributing

Contributions are welcome! Here's how you can help:

1. **Fork** the repository
2. **Create** a feature branch:
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. **Commit** your changes:
   ```bash
   git commit -m 'Add some amazing feature'
   ```
4. **Push** to the branch:
   ```bash
   git push origin feature/amazing-feature
   ```
5. **Open** a Pull Request

### Contribution Guidelines

- Follow PSR-12 coding standards
- Write clear commit messages
- Add comments to complex code
- Test your changes thoroughly
- Update documentation as needed

---


## 🙏 Acknowledgments

- Thanks to all contributors who have helped shape this project
- Inspired by the crafting community's passion for handmade goods
- Built with ❤️ for craft enthusiasts everywhere

---

## 🗺️ Roadmap

- [ ] Add wishlist functionality
- [ ] Implement product reviews and ratings
- [ ] Add advanced search and filtering
- [ ] Create mobile app version
- [ ] Integrate multiple payment gateways
- [ ] Add email notifications
- [ ] Implement inventory management
- [ ] Create tutorial video section

---

**Happy Crafting! 🧶✂️🎨**

---

*Made with love by the StitchandStory Team*

