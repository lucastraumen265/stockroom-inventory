# Stockroom: Inventory Monitoring System

Mobile-first office supplies inventory system for **BULSU COE 2**, built with PHP, MySQL, HTML, CSS and JavaScript.
Developed for CPE 304L Software Design (Final Laboratory Examination).

## Features
- Login and sign up with password rules and validation
- Role-based access (Admin and Staff)
- Inventory list with status badges, search, sorting, filters and pagination
- Restock, Issue, Adjust Stock and Add Item with live preview and a review step
- Stock Movements audit log (Admin only)
- Low-stock and out-of-stock Notifications
- Success and error feedback modals, logout confirmation

## Tech Stack
PHP 8+ (PDO), MySQL/MariaDB, HTML5, CSS3, vanilla JavaScript

## Setup (XAMPP / Laragon)
1. Copy all files into a folder, e.g. `htdocs/stockroom`.
2. Start **Apache** and **MySQL**.
3. Open phpMyAdmin, go to the **SQL** tab, paste the contents of `sqlcode.sql`, and click **Go**.
   (This creates the `stockroom_db` database, tables and sample data.)
4. Open `http://localhost/stockroom/index.php`.
5. If needed, edit the database settings in `db.php` (default: user `root`, no password).

## Demo Accounts
| Role  | Email                    | Password   |
|-------|--------------------------|------------|
| Admin | jasperadmin05@gmail.com  | `password` |
| Staff | jefstaff@gmail.com       | `password` |

New accounts created through Sign up are Staff by default.

## Permissions
| Feature                          | Admin | Staff |
|----------------------------------|:-----:|:-----:|
| View inventory and notifications | Yes   | Yes   |
| Issue items                      | Yes   | Yes   |
| Restock, Adjust stock, Add item  | Yes   | No    |
| Stock Movements log              | Yes   | No    |

## Project Structure
| File | Purpose |
|------|---------|
| `index.php`, `signup.php`, `logout.php` | Authentication |
| `dashboard.php` | Inventory list and stats |
| `restock.php`, `issue.php`, `adjust.php`, `add_item.php` | Stock operations |
| `movements.php` | Stock movement history |
| `notifications.php` | Low and out-of-stock alerts |
| `db.php`, `layout.php` | Database connection, helpers, shared layout |
| `style.css`, `app.js` | Styling and client-side behavior |
| `sqlcode.sql` | Database schema and seed data |

## Database
Tables: `users`, `categories`, `items`, `movements`.
Stock changes run inside a transaction and every change is recorded in `movements` with the before and after quantity.

## Authors
-Lorenzo, Jedrick 
-Manahan, Manuel
-Manalastas, Jef Edrielle
-Santos, Jasper Kim

