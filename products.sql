-- SQL initialization for Product Inventory Module
-- Database: DEMOKIM

CREATE TABLE IF NOT EXISTS Products (
    productID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255)
);

-- Insert sample data
INSERT INTO Products (name, description, price, stock, image) VALUES
('Watercolor Set', '24-color professional watercolor set', 19.99, 20, 'img/watercolor.jpg'),
('3D Printer Filament', '1kg PLA filament for 3D printing', 24.50, 15, 'img/filament.jpg'),
('Sketchbook A4', '120gsm paper for drawing', 12.00, 25, 'img/sketchbook.jpg');
