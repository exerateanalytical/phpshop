CREATE DATABASE IF NOT EXISTS phpshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phpshop;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS landing_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    hero_text VARCHAR(255) NOT NULL,
    body_text TEXT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_landing_pages_is_active (is_active)
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_products_is_active (is_active),
    INDEX idx_products_category (category)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(32) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    customer_name VARCHAR(150) NOT NULL,
    phone VARCHAR(60) NOT NULL,
    address TEXT NOT NULL,
    payment_method VARCHAR(60) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','paid','shipped','cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_orders_user_id (user_id),
    INDEX idx_orders_status (status)
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(190) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    line_total DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_order_items_order_id (order_id)
);

INSERT INTO users (name, email, password, role)
VALUES ('Admin', 'admin@phpshop.local', '$2y$12$DqCKwzsLIo8Oe69ntBa6puIqzImvNHCcra2aqOrkLXu/GEIxd2ZoG', 'admin')
ON DUPLICATE KEY UPDATE email = email;

INSERT INTO landing_pages (title, slug, hero_text, body_text, is_active) VALUES
('Pain Relief Essentials', 'pain-relief-essentials', 'Fast and trusted pain relief products', 'Shop tablets, gels, and relief kits picked by pharmacists.', 1),
('Cold & Flu Care', 'cold-flu-care', 'Stay strong through the season', 'Explore syrups, vitamins, and immune support bundles.', 1),
('Digestive Wellness', 'digestive-wellness', 'Healthy gut, healthy life', 'Get antacids, probiotics, and digestion support formulas.', 1),
('Heart Health Picks', 'heart-health-picks', 'Daily support for heart wellness', 'Browse doctor-recommended supplements and monitors.', 1),
('Diabetes Support', 'diabetes-support', 'Smart products for glucose management', 'Meters, strips, and lifestyle products in one place.', 1),
('Allergy Defense', 'allergy-defense', 'Breathe easy every day', 'Antihistamines and nasal care for seasonal comfort.', 1),
('First Aid Station', 'first-aid-station', 'Be ready for every emergency', 'Bandages, antiseptics, and complete first aid kits.', 1),
('Women Health Care', 'women-health-care', 'Complete support for women wellness', 'Multivitamins, hygiene, and personal care essentials.', 1),
('Kids Pharmacy Zone', 'kids-pharmacy-zone', 'Gentle care for children', 'Syrups, vitamins, and pediatric care products.', 1),
('Senior Care Corner', 'senior-care-corner', 'Comfort and support for seniors', 'Mobility, supplements, and daily health care solutions.', 1)
ON DUPLICATE KEY UPDATE slug = slug;

INSERT INTO products (name, category, description, price, stock, is_active) VALUES
('Paracetamol 500mg', 'Pain Relief', 'General pain and fever reducer.', 4.50, 200, 1),
('Ibuprofen 200mg', 'Pain Relief', 'Anti-inflammatory tablets.', 6.00, 150, 1),
('Vitamin C 1000mg', 'Vitamins', 'Immune system support.', 12.00, 100, 1),
('Digital Thermometer', 'Devices', 'Fast body temperature reading.', 18.00, 60, 1),
('Cough Syrup', 'Cold & Flu', 'Soothing formula for dry cough.', 8.50, 80, 1),
('Antacid Tablets', 'Digestive', 'Relief from acidity and indigestion.', 5.75, 130, 1)
ON DUPLICATE KEY UPDATE name = name;
