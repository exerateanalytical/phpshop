# Architecture Overview

## Stack
- PHP 8.x (no framework)
- MySQL 8.x
- Session-based auth/cart state

## Layout
- `config.php`: app and DB config (env-driven where available).
- `database/schema.sql`: full schema + seed data.
- `includes/functions.php`: global helpers (session, CSRF, escaping, money, order number).
- `includes/db.php`: PDO connection singleton.
- `includes/auth.php`: role guards.
- Public pages + admin pages in project root for simple shared-host deployment.

## Core Flows
1. **Auth**
   - Register -> hash password -> insert user.
   - Login -> verify hash -> regenerate session ID -> store role.
2. **Storefront**
   - Active landing pages listed on homepage.
   - Active products visible publicly.
3. **Checkout**
   - Cart item and stock validation.
   - Transaction creates order + order items.
   - Stock decremented with guarded condition (`stock >= qty`).
   - Rollback on conflicts.
4. **Admin**
   - Product CRUD-lite (create/update/status).
   - Landing page content + active status management.
   - Order status update and full order detail viewing.

## Production Hardening Included
- CSRF checks for every POST route.
- HttpOnly/SameSite session cookie settings.
- Strong validation for registration and checkout fields.
- SQL indexes on major lookup columns.
