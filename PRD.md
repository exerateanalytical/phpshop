# Pharmacy & Drugstore PHP Shop - Product Requirements Document

## 1. Goal
Build a simple, reliable, MySQL + PHP pharmacy e-commerce script that includes:
- Authentication (customer + admin)
- Product catalog browsing
- Cart and checkout flow
- Order history
- Admin panel for managing products and 10 configurable landing pages

## 2. Target Users
- **Customers**: browse medicine and wellness products, purchase, and track orders.
- **Admin**: manage products, orders, and activate/deactivate landing pages.

## 3. Core Functional Requirements

### 3.1 Authentication
- User registration and login.
- Session-based authentication.
- Role-based access (`customer`, `admin`).

### 3.2 Landing Pages
- Exactly 10 pre-created landing pages.
- Each page has: title, slug, hero text, body copy, `is_active` toggle.
- Public users can only access active landing pages.
- Admin can activate/deactivate pages from admin panel.

### 3.3 Catalog
- Product list with name, category, price, stock, and status.
- Product detail page.
- Only active products appear publicly.

### 3.4 Cart & Checkout
- Add/remove/update product quantities in cart.
- Checkout creates order + order items.
- Stock reduction after successful checkout.
- Basic checkout fields: customer name, phone, address, payment method.

### 3.5 Orders
- Customer can see own order history.
- Admin can view all orders and update status (`pending`, `paid`, `shipped`, `cancelled`).

### 3.6 Admin Panel
- Dashboard summary (users/products/orders/pages).
- Manage product catalog (create/update/activate/deactivate).
- Manage landing page activation.
- Manage order statuses.

## 4. Non-Functional Requirements
- Keep implementation minimal and easy to deploy in shared hosting.
- PDO-based DB access with prepared statements.
- Basic input validation and output escaping.
- No framework dependency.

## 5. Database Requirements
- MySQL schema for users, products, landing_pages, carts/orders.
- Seed data includes admin account, sample products, and 10 landing pages.

## 6. Deliverables
- SQL schema and seed script.
- PHP source code for auth, storefront, admin, cart, checkout.
- Architecture document.
- Setup and run instructions in README.
