-- Create database
CREATE DATABASE IF NOT EXISTS free_choise;
USE free_choise;

-- Users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    address TEXT,
    phone VARCHAR(20),
    is_admin BOOLEAN DEFAULT FALSE,
    is_banned BOOLEAN DEFAULT FALSE,
    ban_reason TEXT,
    ban_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Products table
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Create addresses table
CREATE TABLE addresses (
    address_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    zip_code VARCHAR(20) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Orders table
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    address_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cod', 'card') NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (address_id) REFERENCES addresses(address_id)
);

-- Order items table
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Cart table
CREATE TABLE cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Contact inquiries table
CREATE TABLE contact_inquiries (
    inquiry_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create admin_notifications table
CREATE TABLE admin_notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- User ban history table
CREATE TABLE user_bans (
    ban_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    reason TEXT NOT NULL,
    banned_by INT NOT NULL,
    ban_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ban_until TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (banned_by) REFERENCES users(user_id)
);

-- Order cancellation tracking table
CREATE TABLE order_cancellations (
    cancellation_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    cancelled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- Insert default categories
INSERT INTO categories (category_name, description) VALUES
('Clothes', 'Fashion and apparel items'),
('Watches', 'Luxury and casual watches'),
('Bands', 'Accessories and bands'),
('Kitchen Items', 'Kitchen appliances and utensils'),
('Shoes', 'Footwear for all occasions'),
('Skincare Items', 'Beauty and skincare products'),
('Soap', 'Personal hygiene and bathing products'),
('Perfumes', 'Fragrances and scented products'),
('Phone Accessories', 'Mobile phone cases, chargers, and accessories'),
('Electric Items', 'Electronic devices and appliances'),
('Other', 'Miscellaneous products and items');

-- Insert sample products for each category
-- Clothes
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(1, 'Classic White T-Shirt', 'Premium cotton t-shirt, comfortable fit', 19.99, 100, 'https://example.com/images/white-tshirt.jpg'),
(1, 'Blue Denim Jeans', 'Slim fit denim jeans, high quality material', 49.99, 50, 'https://example.com/images/blue-jeans.jpg'),
(1, 'Black Hoodie', 'Warm and comfortable hoodie, perfect for casual wear', 39.99, 75, 'https://example.com/images/black-hoodie.jpg'),
(1, 'Summer Dress', 'Light and flowy summer dress, floral pattern', 59.99, 30, 'https://example.com/images/summer-dress.jpg'),
(1, 'Formal Shirt', 'Business casual shirt, wrinkle-resistant', 45.99, 60, 'https://example.com/images/formal-shirt.jpg'),
(1, 'Winter Jacket', 'Warm winter jacket with multiple pockets', 89.99, 40, 'https://example.com/images/winter-jacket.jpg'),
(1, 'Yoga Pants', 'Stretchy and comfortable yoga pants', 34.99, 80, 'https://example.com/images/yoga-pants.jpg'),
(1, 'Polo Shirt', 'Classic polo shirt, various colors available', 29.99, 90, 'https://example.com/images/polo-shirt.jpg'),
(1, 'Leather Jacket', 'Genuine leather jacket, stylish design', 129.99, 25, 'https://example.com/images/leather-jacket.jpg'),
(1, 'Casual Shorts', 'Comfortable shorts for everyday wear', 24.99, 70, 'https://example.com/images/casual-shorts.jpg');

-- Watches
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(2, 'Classic Analog Watch', 'Elegant analog watch with leather strap', 199.99, 30, 'https://example.com/images/analog-watch.jpg'),
(2, 'Digital Sports Watch', 'Water-resistant sports watch with multiple features', 149.99, 40, 'https://example.com/images/sports-watch.jpg'),
(2, 'Luxury Gold Watch', 'Premium gold-plated watch with diamond markers', 599.99, 15, 'https://example.com/images/luxury-watch.jpg'),
(2, 'Smart Watch', 'Fitness tracking and notifications', 299.99, 35, 'https://example.com/images/smart-watch.jpg'),
(2, 'Minimalist Watch', 'Sleek design with minimalist face', 129.99, 45, 'https://example.com/images/minimalist-watch.jpg'),
(2, 'Chronograph Watch', 'Professional chronograph with multiple dials', 249.99, 25, 'https://example.com/images/chronograph-watch.jpg'),
(2, 'Vintage Watch', 'Retro design with leather strap', 179.99, 20, 'https://example.com/images/vintage-watch.jpg'),
(2, 'Diving Watch', 'Water-resistant up to 200m', 399.99, 15, 'https://example.com/images/diving-watch.jpg'),
(2, 'Automatic Watch', 'Self-winding mechanical watch', 349.99, 20, 'https://example.com/images/automatic-watch.jpg'),
(2, 'Kids Watch', 'Colorful and durable watch for children', 49.99, 50, 'https://example.com/images/kids-watch.jpg');

-- Bands
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(3, 'Leather Watch Band', 'Genuine leather watch strap', 29.99, 100, 'https://example.com/images/leather-band.jpg'),
(3, 'Metal Watch Band', 'Stainless steel watch band', 39.99, 80, 'https://example.com/images/metal-band.jpg'),
(3, 'Silicone Sports Band', 'Comfortable silicone band for sports watches', 19.99, 120, 'https://example.com/images/silicone-band.jpg'),
(3, 'Nylon NATO Band', 'Classic NATO style watch band', 14.99, 150, 'https://example.com/images/nato-band.jpg'),
(3, 'Milanese Mesh Band', 'Premium mesh watch band', 49.99, 60, 'https://example.com/images/mesh-band.jpg'),
(3, 'Rubber Dive Band', 'Professional diving watch band', 34.99, 70, 'https://example.com/images/dive-band.jpg'),
(3, 'Canvas Watch Band', 'Durable canvas watch strap', 24.99, 90, 'https://example.com/images/canvas-band.jpg'),
(3, 'Ceramic Watch Band', 'Modern ceramic watch band', 59.99, 40, 'https://example.com/images/ceramic-band.jpg'),
(3, 'Quick Release Band', 'Easy to change watch band', 29.99, 110, 'https://example.com/images/quick-release-band.jpg'),
(3, 'Premium Leather Band', 'Handcrafted leather watch band', 69.99, 50, 'https://example.com/images/premium-leather-band.jpg');

-- Kitchen Items
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(4, 'Non-stick Pan Set', '5-piece non-stick cookware set', 89.99, 40, 'https://example.com/images/pan-set.jpg'),
(4, 'Chef Knife Set', 'Professional 8-piece knife set', 129.99, 30, 'https://example.com/images/knife-set.jpg'),
(4, 'Food Processor', 'Multi-functional food processor', 149.99, 25, 'https://example.com/images/food-processor.jpg'),
(4, 'Coffee Maker', 'Programmable coffee maker', 79.99, 35, 'https://example.com/images/coffee-maker.jpg'),
(4, 'Blender', 'High-speed blender with multiple settings', 69.99, 45, 'https://example.com/images/blender.jpg'),
(4, 'Cutting Board Set', 'Bamboo cutting board set', 39.99, 60, 'https://example.com/images/cutting-board.jpg'),
(4, 'Mixing Bowl Set', 'Stainless steel mixing bowls', 49.99, 50, 'https://example.com/images/mixing-bowls.jpg'),
(4, 'Toaster', '4-slice toaster with multiple settings', 59.99, 40, 'https://example.com/images/toaster.jpg'),
(4, 'Pressure Cooker', 'Electric pressure cooker', 119.99, 30, 'https://example.com/images/pressure-cooker.jpg'),
(4, 'Utensil Set', 'Kitchen utensil set with holder', 34.99, 70, 'https://example.com/images/utensil-set.jpg');

-- Shoes
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(5, 'Running Shoes', 'Lightweight running shoes with cushioning', 89.99, 50, 'https://example.com/images/running-shoes.jpg'),
(5, 'Casual Sneakers', 'Comfortable everyday sneakers', 69.99, 60, 'https://example.com/images/casual-sneakers.jpg'),
(5, 'Formal Dress Shoes', 'Classic leather dress shoes', 129.99, 40, 'https://example.com/images/dress-shoes.jpg'),
(5, 'Hiking Boots', 'Waterproof hiking boots', 149.99, 30, 'https://example.com/images/hiking-boots.jpg'),
(5, 'Sandals', 'Comfortable summer sandals', 39.99, 80, 'https://example.com/images/sandals.jpg'),
(5, 'Sports Shoes', 'Multi-purpose sports shoes', 79.99, 45, 'https://example.com/images/sports-shoes.jpg'),
(5, 'Slip-on Shoes', 'Easy to wear slip-on shoes', 59.99, 55, 'https://example.com/images/slip-on-shoes.jpg'),
(5, 'Winter Boots', 'Warm and waterproof winter boots', 119.99, 35, 'https://example.com/images/winter-boots.jpg'),
(5, 'Dance Shoes', 'Professional dance shoes', 99.99, 25, 'https://example.com/images/dance-shoes.jpg'),
(5, 'Work Boots', 'Durable work boots with safety features', 159.99, 30, 'https://example.com/images/work-boots.jpg');

-- Skincare Items
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(6, 'Facial Cleanser', 'Gentle facial cleanser for all skin types', 24.99, 100, 'https://example.com/images/facial-cleanser.jpg'),
(6, 'Moisturizing Cream', 'Hydrating face cream', 29.99, 80, 'https://example.com/images/moisturizer.jpg'),
(6, 'Face Serum', 'Anti-aging face serum', 39.99, 60, 'https://example.com/images/face-serum.jpg'),
(6, 'Eye Cream', 'Reducing dark circles and puffiness', 34.99, 70, 'https://example.com/images/eye-cream.jpg'),
(6, 'Face Mask', 'Hydrating face mask', 19.99, 90, 'https://example.com/images/face-mask.jpg'),
(6, 'Sunscreen', 'Broad spectrum SPF 50 sunscreen', 29.99, 85, 'https://example.com/images/sunscreen.jpg'),
(6, 'Toner', 'Balancing facial toner', 22.99, 75, 'https://example.com/images/toner.jpg'),
(6, 'Exfoliating Scrub', 'Gentle facial exfoliator', 27.99, 65, 'https://example.com/images/exfoliator.jpg'),
(6, 'Night Cream', 'Repairing night cream', 32.99, 70, 'https://example.com/images/night-cream.jpg'),
(6, 'Face Oil', 'Nourishing face oil', 44.99, 50, 'https://example.com/images/face-oil.jpg');

-- Soap
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(7, 'Lavender Soap', 'Natural lavender scented soap', 4.99, 200, 'https://example.com/images/lavender-soap.jpg'),
(7, 'Charcoal Soap', 'Detoxifying charcoal soap', 5.99, 150, 'https://example.com/images/charcoal-soap.jpg'),
(7, 'Oatmeal Soap', 'Gentle exfoliating oatmeal soap', 4.99, 180, 'https://example.com/images/oatmeal-soap.jpg'),
(7, 'Honey Soap', 'Moisturizing honey soap', 5.49, 160, 'https://example.com/images/honey-soap.jpg'),
(7, 'Tea Tree Soap', 'Antibacterial tea tree soap', 5.99, 140, 'https://example.com/images/tea-tree-soap.jpg'),
(7, 'Rose Soap', 'Fragrant rose petal soap', 4.99, 170, 'https://example.com/images/rose-soap.jpg'),
(7, 'Coconut Soap', 'Moisturizing coconut soap', 5.49, 160, 'https://example.com/images/coconut-soap.jpg'),
(7, 'Aloe Vera Soap', 'Soothing aloe vera soap', 4.99, 180, 'https://example.com/images/aloe-soap.jpg'),
(7, 'Mint Soap', 'Refreshing mint soap', 4.99, 190, 'https://example.com/images/mint-soap.jpg'),
(7, 'Goat Milk Soap', 'Nourishing goat milk soap', 6.99, 130, 'https://example.com/images/goat-milk-soap.jpg');

-- Perfumes
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(8, 'Floral Perfume', 'Elegant floral fragrance', 89.99, 40, 'https://example.com/images/floral-perfume.jpg'),
(8, 'Woody Cologne', 'Masculine woody scent', 79.99, 45, 'https://example.com/images/woody-cologne.jpg'),
(8, 'Citrus Perfume', 'Fresh citrus fragrance', 69.99, 50, 'https://example.com/images/citrus-perfume.jpg'),
(8, 'Oriental Perfume', 'Exotic oriental scent', 99.99, 35, 'https://example.com/images/oriental-perfume.jpg'),
(8, 'Fresh Cologne', 'Light and fresh cologne', 59.99, 55, 'https://example.com/images/fresh-cologne.jpg'),
(8, 'Spicy Perfume', 'Warm spicy fragrance', 84.99, 40, 'https://example.com/images/spicy-perfume.jpg'),
(8, 'Aquatic Perfume', 'Ocean-inspired scent', 74.99, 45, 'https://example.com/images/aquatic-perfume.jpg'),
(8, 'Gourmand Perfume', 'Sweet gourmand fragrance', 89.99, 40, 'https://example.com/images/gourmand-perfume.jpg'),
(8, 'Green Perfume', 'Fresh green scent', 69.99, 50, 'https://example.com/images/green-perfume.jpg'),
(8, 'Fruity Perfume', 'Sweet fruity fragrance', 79.99, 45, 'https://example.com/images/fruity-perfume.jpg');

-- Phone Accessories
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(9, 'Phone Case', 'Durable phone case with protection', 19.99, 100, 'https://example.com/images/phone-case.jpg'),
(9, 'Wireless Charger', 'Fast wireless charging pad', 29.99, 80, 'https://example.com/images/wireless-charger.jpg'),
(9, 'Screen Protector', 'Tempered glass screen protector', 9.99, 150, 'https://example.com/images/screen-protector.jpg'),
(9, 'Power Bank', '10000mAh portable charger', 39.99, 60, 'https://example.com/images/power-bank.jpg'),
(9, 'Phone Stand', 'Adjustable phone stand', 14.99, 90, 'https://example.com/images/phone-stand.jpg'),
(9, 'Earphones', 'Wireless Bluetooth earphones', 49.99, 70, 'https://example.com/images/earphones.jpg'),
(9, 'Phone Grip', 'Phone grip and stand', 12.99, 120, 'https://example.com/images/phone-grip.jpg'),
(9, 'Car Mount', 'Universal car phone mount', 24.99, 80, 'https://example.com/images/car-mount.jpg'),
(9, 'Phone Lanyard', 'Adjustable phone lanyard', 8.99, 130, 'https://example.com/images/phone-lanyard.jpg'),
(9, 'Phone Ring Light', 'LED ring light for selfies', 19.99, 90, 'https://example.com/images/ring-light.jpg');

-- Electric Items
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(10, 'Bluetooth Speaker', 'Portable wireless speaker', 79.99, 50, 'https://example.com/images/bluetooth-speaker.jpg'),
(10, 'Smart Bulb', 'WiFi-enabled smart bulb', 24.99, 100, 'https://example.com/images/smart-bulb.jpg'),
(10, 'Electric Toothbrush', 'Rechargeable electric toothbrush', 89.99, 60, 'https://example.com/images/electric-toothbrush.jpg'),
(10, 'Hair Dryer', 'Professional hair dryer', 59.99, 70, 'https://example.com/images/hair-dryer.jpg'),
(10, 'Electric Shaver', 'Cordless electric shaver', 99.99, 45, 'https://example.com/images/electric-shaver.jpg'),
(10, 'Smart Plug', 'WiFi smart plug', 19.99, 120, 'https://example.com/images/smart-plug.jpg'),
(10, 'Electric Kettle', 'Stainless steel electric kettle', 49.99, 80, 'https://example.com/images/electric-kettle.jpg'),
(10, 'Massage Gun', 'Deep tissue massage gun', 149.99, 30, 'https://example.com/images/massage-gun.jpg'),
(10, 'Electric Blanket', 'Heated electric blanket', 69.99, 40, 'https://example.com/images/electric-blanket.jpg'),
(10, 'Smart Watch Charger', 'Universal smart watch charger', 29.99, 90, 'https://example.com/images/watch-charger.jpg');

-- Other Items
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(11, 'Travel Pillow', 'Memory foam travel pillow', 24.99, 80, 'https://example.com/images/travel-pillow.jpg'),
(11, 'Water Bottle', 'Insulated water bottle', 19.99, 100, 'https://example.com/images/water-bottle.jpg'),
(11, 'Backpack', 'Durable travel backpack', 49.99, 60, 'https://example.com/images/backpack.jpg'),
(11, 'Umbrella', 'Windproof travel umbrella', 29.99, 70, 'https://example.com/images/umbrella.jpg'),
(11, 'Sunglasses', 'UV protection sunglasses', 39.99, 50, 'https://example.com/images/sunglasses.jpg'),
(11, 'Wallet', 'Genuine leather wallet', 34.99, 65, 'https://example.com/images/wallet.jpg'),
(11, 'Keychain', 'Decorative keychain', 9.99, 120, 'https://example.com/images/keychain.jpg'),
(11, 'Notebook', 'Premium leather notebook', 19.99, 90, 'https://example.com/images/notebook.jpg'),
(11, 'Pen Set', 'Executive pen set', 29.99, 75, 'https://example.com/images/pen-set.jpg'),
(11, 'Desk Organizer', 'Multi-compartment desk organizer', 39.99, 55, 'https://example.com/images/desk-organizer.jpg');

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, is_admin) VALUES
('admin', 'admin@freechoise.com', '$2y$10$8K1p/a0dR1xqM1ZqK1p/a0dR1xqM1ZqK1p/a0dR1xqM1ZqK1p/a0dR1xq', TRUE); 