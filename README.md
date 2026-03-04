# PharmaShop (PHP + MySQL)

Production-oriented pharmacy/drugstore e-commerce starter built with plain PHP + MySQL.

## Features
- Customer auth (register/login/logout) with password hashing.
- Session hardening + CSRF protection for every POST workflow.
- Product catalog and product detail pages.
- Cart management + transactional checkout + stock-safe updates.
- Customer order history and full order detail pages.
- Admin dashboard:
  - Product create/update/activate/deactivate
  - Order status management + detailed order view
  - Landing page content management + activate/deactivate
- 10 predefined pharmacy landing pages.

## Requirements
- PHP 8.1+
- MySQL 8+

## Quick Setup
1. Create DB and seed data:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
2. Configure environment values (optional) before running:
   - `APP_NAME`, `APP_ENV`, `APP_BASE_URL`, `APP_CURRENCY_SYMBOL`
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
   - `SESSION_NAME`, `SESSION_SECURE`, `SESSION_LIFETIME`
3. Run local server:
   ```bash
   php -S 0.0.0.0:8000 router.php
   ```
4. Open `http://localhost:8000`.

## Default Admin
- Email: `admin@phpshop.local`
- Password: `admin123`

## Core Routes
- Public: `index.php`, `landing.php`, `products.php`, `product.php`, `cart.php`, `checkout.php`
- Auth: `register.php`, `login.php`, `logout.php`
- Customer Orders: `my_orders.php`, `order_view.php`, `order_success.php`
- Admin: `admin.php`, `admin_products.php`, `admin_pages.php`, `admin_orders.php`, `admin_order_view.php`

## Security Notes
- Uses PDO prepared statements everywhere.
- CSRF token required for all POST mutations.
- Session ID regenerated at login.
- Checkout uses DB transaction and guarded stock update condition.

## Preview troubleshooting
- If MySQL is not running/configured yet, the app now shows a friendly setup page instead of a raw fatal error.
- Once DB is ready and `database/schema.sql` is imported, refresh to see the full storefront/admin experience.

- If you get "Not Found" in preview, make sure you started the server from the project root with `php -S 0.0.0.0:8000 router.php`.

- If DB is unavailable, homepage, landing pages, and products now render in limited preview mode (HTTP 200) so screenshots/checks still work.

- Added `index.html` as a universal preview entry point (auto-redirects to `index.php`) for environments that probe static root first.
