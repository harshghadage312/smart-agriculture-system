-- Smart Agriculture Advisory and Marketplace System
-- ⚠️ This script safely drops and recreates all tables

CREATE DATABASE IF NOT EXISTS smart_agri;
USE smart_agri;

-- Drop tables in correct order (child tables first)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS pests;
DROP TABLE IF EXISTS weather_advisory;
DROP TABLE IF EXISTS fertilizers;
DROP TABLE IF EXISTS crops;
DROP TABLE IF EXISTS users;

-- ============================================================
-- USERS TABLE
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    role ENUM('farmer','buyer','admin') DEFAULT 'farmer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- CROPS TABLE
-- ============================================================
CREATE TABLE crops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    soil_type VARCHAR(50) NOT NULL,
    season VARCHAR(50) NOT NULL,
    water_level ENUM('Low','Medium','High') NOT NULL,
    crop_name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT 'https://via.placeholder.com/200?text=Crop'
);

-- ============================================================
-- FERTILIZERS TABLE
-- ============================================================
CREATE TABLE fertilizers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    crop_name VARCHAR(100) NOT NULL,
    nutrient_status VARCHAR(100) NOT NULL,
    fertilizer_name VARCHAR(100) NOT NULL,
    dosage VARCHAR(100),
    instructions TEXT
);

-- ============================================================
-- WEATHER ADVISORY TABLE
-- ============================================================
CREATE TABLE weather_advisory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    season VARCHAR(50) NOT NULL,
    rainfall ENUM('Low','Medium','High') NOT NULL,
    advisory TEXT NOT NULL,
    suitable_crops VARCHAR(255)
);

-- ============================================================
-- PESTS TABLE
-- ============================================================
CREATE TABLE pests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    crop_name VARCHAR(100) NOT NULL,
    symptoms TEXT NOT NULL,
    pest_name VARCHAR(100) NOT NULL,
    treatment TEXT NOT NULL,
    prevention TEXT
);

-- ============================================================
-- PRODUCTS TABLE (Marketplace)
-- ============================================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    quantity INT DEFAULT 1,
    unit VARCHAR(20) DEFAULT 'kg',
    image VARCHAR(255) DEFAULT 'https://via.placeholder.com/200?text=Product',
    status ENUM('available','sold_out') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ============================================================
-- CART TABLE
-- ============================================================
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ============================================================
-- ORDERS TABLE
-- ============================================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    address TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Placed','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Placed',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ============================================================
-- ORDER ITEMS TABLE
-- ============================================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Admin User (password: admin123)
INSERT INTO users (name, email, password, phone, role) VALUES
('Admin', 'admin@agri.com', MD5('admin123'), '9999999999', 'admin');

-- Farmer Users (password: farmer123)
INSERT INTO users (name, email, password, phone, role) VALUES
('Ramesh Patil', 'ramesh@gmail.com', MD5('farmer123'), '9876543210', 'farmer'),
('Sunita Devi', 'sunita@gmail.com', MD5('farmer123'), '9876543211', 'farmer');

-- Buyer User (password: buyer123)
INSERT INTO users (name, email, password, phone, role) VALUES
('Amit Sharma', 'amit@gmail.com', MD5('buyer123'), '9876543212', 'buyer');

-- Crops Data
INSERT INTO crops (soil_type, season, water_level, crop_name, description) VALUES
('Black Soil', 'Kharif', 'High', 'Cotton', 'Cotton thrives in black soil with good moisture.'),
('Black Soil', 'Rabi', 'Medium', 'Wheat', 'Wheat is a major rabi crop in black soil regions.'),
('Red Soil', 'Kharif', 'Medium', 'Groundnut', 'Groundnut grows well in red sandy soil.'),
('Red Soil', 'Rabi', 'Low', 'Millets', 'Millets are drought resistant and grow in red soil.'),
('Alluvial Soil', 'Kharif', 'High', 'Rice', 'Rice requires high water and alluvial soil.'),
('Alluvial Soil', 'Rabi', 'Medium', 'Mustard', 'Mustard grows well in alluvial soil during rabi.'),
('Sandy Soil', 'Summer', 'Low', 'Watermelon', 'Watermelon grows well in sandy soil.'),
('Loamy Soil', 'Kharif', 'Medium', 'Maize', 'Maize grows best in well-drained loamy soil.'),
('Loamy Soil', 'Rabi', 'Medium', 'Potato', 'Potato requires loamy soil with good drainage.'),
('Clay Soil', 'Kharif', 'High', 'Sugarcane', 'Sugarcane needs water-retaining clay soil.');

-- Fertilizers Data
INSERT INTO fertilizers (crop_name, nutrient_status, fertilizer_name, dosage, instructions) VALUES
('Cotton', 'Low Nitrogen', 'Urea (46% N)', '25 kg/acre', 'Apply before sowing and once after 30 days.'),
('Cotton', 'Low Phosphorus', 'Single Super Phosphate', '50 kg/acre', 'Apply at the time of sowing.'),
('Wheat', 'Low Nitrogen', 'DAP (Di-Ammonium Phosphate)', '50 kg/acre', 'Apply at sowing time.'),
('Rice', 'Low Potassium', 'Muriate of Potash (MOP)', '20 kg/acre', 'Apply before transplanting.'),
('Maize', 'Low Nitrogen', 'Urea (46% N)', '30 kg/acre', 'Split into 2-3 applications.'),
('Groundnut', 'Low Calcium', 'Gypsum', '100 kg/acre', 'Apply at flowering stage.'),
('Sugarcane', 'Low Nitrogen', 'Ammonium Sulfate', '40 kg/acre', 'Apply in 3 split doses.'),
('Potato', 'Low Phosphorus', 'NPK 10:26:26', '50 kg/acre', 'Mix into soil before planting.');

-- Weather Advisory Data
INSERT INTO weather_advisory (season, rainfall, advisory, suitable_crops) VALUES
('Kharif', 'High', 'Heavy rains expected. Ensure proper drainage in fields. Ideal for water-intensive crops.', 'Rice, Sugarcane, Cotton'),
('Kharif', 'Medium', 'Moderate rainfall. Good season for most kharif crops. Monitor for fungal diseases.', 'Maize, Groundnut, Cotton'),
('Kharif', 'Low', 'Drought conditions. Use drought-resistant varieties. Consider drip irrigation.', 'Millets, Sorghum, Pulses'),
('Rabi', 'High', 'Higher than normal winter rains. Good for rabi crops but watch for waterlogging.', 'Wheat, Mustard, Chickpea'),
('Rabi', 'Medium', 'Normal rabi season. Ideal for most winter crops. Irrigation required.', 'Wheat, Barley, Peas'),
('Rabi', 'Low', 'Dry winter. Irrigation is essential. Choose water-efficient crops.', 'Mustard, Lentils, Chickpea'),
('Summer', 'Low', 'Hot and dry summer. Focus on heat-resistant crops with drip irrigation.', 'Watermelon, Cucumber, Okra'),
('Summer', 'Medium', 'Moderate summer showers. Good for summer vegetables.', 'Tomato, Brinjal, Bitter Gourd');

-- Pest & Disease Data
INSERT INTO pests (crop_name, symptoms, pest_name, treatment, prevention) VALUES
('Cotton', 'White sticky substance on leaves, yellowing', 'Whitefly', 'Spray Imidacloprid 0.3ml/L water', 'Use yellow sticky traps, avoid water stress'),
('Cotton', 'Holes in bolls, damaged squares', 'Bollworm', 'Apply Spinosad or Chlorpyrifos', 'Use pheromone traps, deep plow after harvest'),
('Rice', 'Yellow-orange discoloration, stunted growth', 'Brown Plant Hopper', 'Spray Buprofezin 25 SC', 'Avoid excess nitrogen, maintain water level'),
('Rice', 'Water-soaked lesions on leaves turning brown', 'Blast Disease', 'Apply Tricyclazole 75 WP', 'Use resistant varieties, avoid dense planting'),
('Wheat', 'Orange/brown pustules on leaves', 'Rust (Brown Rust)', 'Spray Propiconazole 25 EC', 'Use resistant varieties, early sowing'),
('Maize', 'Holes in stems, dead heart symptom', 'Stem Borer', 'Apply Carbofuran 3G granules', 'Destroy crop residues, use light traps'),
('Tomato', 'Brown spots on leaves, fruit rot', 'Early Blight', 'Spray Mancozeb 75 WP', 'Crop rotation, remove infected leaves'),
('Potato', 'Dark brown lesions on leaves and tubers', 'Late Blight', 'Apply Metalaxyl + Mancozeb', 'Use certified seed, avoid overhead irrigation'),
('Groundnut', 'Yellowing, mosaic pattern on leaves', 'Leaf Spot', 'Spray Chlorothalonil 75 WP', 'Use treated seeds, remove infected plants');

-- Sample Products (Marketplace)
INSERT INTO products (user_id, product_name, description, price, quantity, unit, status) VALUES
(2, 'Fresh Cotton', 'High quality cotton, freshly harvested from black soil farm.', 45.00, 500, 'kg', 'available'),
(2, 'Organic Wheat', 'Chemical-free organic wheat, Rabi 2024 harvest.', 28.00, 1000, 'kg', 'available'),
(3, 'Brown Rice', 'Unpolished brown rice, rich in nutrients.', 55.00, 300, 'kg', 'available'),
(3, 'Fresh Groundnut', 'Sun-dried groundnuts, ready for oil extraction.', 65.00, 200, 'kg', 'available'),
(2, 'Sugarcane Jaggery', 'Pure natural jaggery made from organic sugarcane.', 80.00, 100, 'kg', 'available');
