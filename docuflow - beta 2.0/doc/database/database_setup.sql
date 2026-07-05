-- DocuFlow Database Schema
-- Run this SQL script to create the database structure
-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'group') DEFAULT 'group',
    group_name INT NOT NULL,
    active ENUM('0', '1', '3') DEFAULT '3',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Documents table
CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    national_number VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    passport VARCHAR(50) NOT NULL,
    contact VARCHAR(255) NOT NULL,
    status ENUM(
        'new',
        'w-resv',
        'on-resv',
        'enough',
        'pending-delivery',
        'delivered'
    ) DEFAULT 'new',
    iban VARCHAR(50),
    notes TEXT,
    passport_image VARCHAR(255),
    nn_image VARCHAR(255),
    visibility ENUM('private', 'department', 'public') DEFAULT 'public',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Activity log table
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    document_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE
    SET
        NULL
);

-- Insert default admin user (password: Admin@123)
-- Note: Change this password in production!
INSERT INTO
    users (name, email, password, role, group_name, active)
VALUES
    (
        'Admin User',
        'admin@docuflow.com',
        '$2y$10$qnOt90jGL/LmoHohg0atD.LXB3RiLs0PcLR0GfDs4yzNFHlPqG2u6',
        'admin',
        1,
        '0'
    );

-- Create indexes for better performance
CREATE INDEX idx_documents_user_id ON documents(user_id);

CREATE INDEX idx_documents_status ON documents(status);

CREATE INDEX idx_activity_user_id ON activity_log(user_id);

CREATE INDEX idx_activity_created_at ON activity_log(created_at);