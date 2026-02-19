-- 1. สร้าง Database
CREATE DATABASE IF NOT EXISTS s673190104 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE s673190104;

-- 2. สร้างตารางผู้ใช้งาน (Users)
CREATE TABLE users (
    user_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    role ENUM('admin', 'member') DEFAULT 'member'
) ENGINE=InnoDB;

-- 3. สร้างตารางหนังสือ (Books)
CREATE TABLE books (
    book_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    book_name VARCHAR(255) NOT NULL,
    author VARCHAR(100),
    category VARCHAR(50),
    status ENUM('available', 'borrowed') DEFAULT 'available'
) ENGINE=InnoDB;

-- 4. สร้างตารางการยืม-คืน (Borrow_Records)
CREATE TABLE borrow_records (
    borrow_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11),
    book_id INT(11),
    borrow_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    status ENUM('pending', 'returned') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
) ENGINE=InnoDB;

-- 5. เพิ่มข้อมูลตัวอย่าง (Seed Data)
-- ข้อมูลผู้ใช้งาน
INSERT INTO users (username, password, fullname, role) VALUES 
('admin01', '1234', 'สมชาย แอดมิน', 'admin'),
('member01', '1234', 'สมหญิง รักเรียน', 'member'),
('member02', '1234', 'นายขยัน อ่านเขียน', 'member');

-- ข้อมูลหนังสือ
INSERT INTO books (book_name, author, category) VALUES 
('PHP & MySQL Beginner', 'John Doe', 'Programming'),
('UI/UX Design Mobile', 'Jane Smith', 'Design'),
('Database Systems', 'Robert Brown', 'Database');

-- ข้อมูลการยืมตัวอย่าง
INSERT INTO borrow_records (user_id, book_id, borrow_date, status) VALUES 
(2, 1, '2026-02-18', 'pending'),
(3, 2, '2026-02-19', 'pending'),
(2, 3, '2026-02-15', 'returned');

ALTER TABLE users ADD email VARCHAR(100) NOT NULL AFTER fullname;
ALTER TABLE books 
ADD publish_year VARCHAR(4) NULL AFTER category,
ADD publisher VARCHAR(100) NULL AFTER publish_year;
-- เพิ่มคอลัมน์เก็บวันที่กำหนด (เช่น เก็บเป็นข้อความ "7,10,15,30")
ALTER TABLE books ADD COLUMN borrow_duration VARCHAR(50) DEFAULT '7,15,30';
-- เพิ่มคอลัมน์สถานะ (active = ปกติ, banned = ถูกระงับการใช้งาน)
ALTER TABLE users ADD COLUMN status ENUM('active', 'banned') DEFAULT 'active';