CREATE DATABASE IF NOT EXISTS property_listing_app
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE property_listing_app;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    buyer_id INT DEFAULT NULL,
    title VARCHAR(150) NOT NULL,
    property_type VARCHAR(30) NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    location VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    bedrooms INT DEFAULT NULL,
    bathrooms INT DEFAULT NULL,
    image VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Available',
    sold_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id),
    FOREIGN KEY (buyer_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
