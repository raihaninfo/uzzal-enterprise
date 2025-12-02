-- Database Schema for Uzzal Enterprise
-- Updated: 2025-12-02

CREATE DATABASE IF NOT EXISTS uzzal_enterprise;
USE uzzal_enterprise;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Will store hashed password
    business_name VARCHAR(100),
    role ENUM('admin', 'member') DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transactions Table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    source VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    category_id INT,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Dues Table
CREATE TABLE IF NOT EXISTS dues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    note TEXT,
    due_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_paid BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- MFS Accounts Table
CREATE TABLE IF NOT EXISTS mfs_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    provider ENUM('Bkash', 'Nagad', 'Rocket') NOT NULL,
    number VARCHAR(20) NOT NULL,
    balance DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- MFS Transactions Table
CREATE TABLE IF NOT EXISTS mfs_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mfs_account_id INT,
    user_id INT,
    type ENUM('in', 'out') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mfs_account_id) REFERENCES mfs_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Default Categories
INSERT IGNORE INTO categories (name) VALUES 
('General'), 
('Sale'), 
('Service'), 
('Food'), 
('Transport'), 
('Bill'), 
('Other');

-- Insert Default User (Admin)
-- Password: 123456 (Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
INSERT IGNORE INTO users (id, name, phone, password, business_name, role) VALUES 
(1, 'Uzzal Enterprise', '01727215472', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Uzzal Enterprise', 'admin');
