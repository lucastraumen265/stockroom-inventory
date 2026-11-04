CREATE DATABASE IF NOT EXISTS stockroom_db CHARACTER SET utf8mb4;
USE stockroom_db;
DROP TABLE IF EXISTS movements; DROP TABLE IF EXISTS items; DROP TABLE IF EXISTS categories; DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50) UNIQUE NOT NULL);
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    category_id INT,
    quantity INT DEFAULT 0,
    threshold INT DEFAULT 10,
    unit VARCHAR(20) DEFAULT 'packs',
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);
CREATE TABLE movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    type ENUM('restock','issue','adjust') NOT NULL,
    quantity_change INT NOT NULL,
    qty_before INT NOT NULL,
    qty_after INT NOT NULL,
    recipient VARCHAR(100), purpose VARCHAR(255), supplier VARCHAR(100), location VARCHAR(100),
    reference VARCHAR(100), reason VARCHAR(100), notes TEXT, received_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Login password for both accounts: password
INSERT INTO users (name, email, password_hash, role) VALUES
('Jasper Santos', 'jasperadmin05@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Jef Manalastas', 'jefstaff@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff');

INSERT INTO categories (name) VALUES ('Office essentials'),('Writing Supplies'),('Paper Products'),('Ink & Toner'),('Filing & Storage'),('Desk Accessories'),('Cleaning Supplies');

INSERT INTO items (item_code, name, description, category_id, quantity, threshold, unit) VALUES
('PEN-1001','Ballpoint pens','Black ink · Box of 24',2,1240,120,'pens'),
('HLT-2001','Highlighter set','4 colors · Pack of 12',2,24,30,'sets'),
('PPR-3001','A4 printer paper','80 gsm · Ream of 500',3,186,30,'reams'),
('STP-4001','Stapler refills','5,000 staples · Pack of 6',1,0,10,'packs'),
('MRK-5001','Whiteboard markers','Fine tip · Pack of 6',2,62,10,'packs'),
('NTS-6001','Sticky notes','3 × 3 in · Pack of 12',1,340,50,'packs'),
('FLD-7001','Filing folders','Letter size · Pack of 25',5,18,25,'packs'),
('TAP-8001','Tape dispenser','Desktop · 1 dispenser',6,225,25,'dispensers'),
('PIC-6002','Printer Ink (Cyan)','Cyan · Bottle',4,15,5,'bottles'),
('PIK-8002','Printer Ink (Black)','Black · Bottle',4,15,5,'bottles');

INSERT INTO movements (item_id, user_id, type, quantity_change, qty_before, qty_after, location, reference, supplier, received_date) VALUES
(1,1,'restock',100,1140,1240,'Supply closet','PO-2026-079','Office Depot',CURDATE());
INSERT INTO movements (item_id, user_id, type, quantity_change, qty_before, qty_after, recipient, purpose) VALUES
(2,2,'issue',-4,28,24,'COE 2 Office','Office document review'),
(3,2,'issue',-35,221,186,'Registrar','Printing of forms');
