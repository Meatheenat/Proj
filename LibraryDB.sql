-- 1. สร้างฐานข้อมูลและเลือกใช้งาน
CREATE DATABASE IF NOT EXISTS LibraryDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE LibraryDB;

-- 2. ลบตารางเก่าทิ้งหากมีอยู่แล้ว (เพื่อป้องกัน Error เวลาทดสอบรันซ้ำ)
DROP TABLE IF EXISTS Transactions;
DROP TABLE IF EXISTS Books;
DROP TABLE IF EXISTS Users;

-- 3. สร้างตารางผู้ใช้งาน (Users)
CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'user'
);

-- 4. สร้างตารางหนังสือ (Books)
CREATE TABLE Books (
    book_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    status VARCHAR(20) DEFAULT 'available' 
);

-- 5. สร้างตารางประวัติการยืม-คืน (Transactions)
CREATE TABLE Transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    book_id INT,
    borrow_date DATE NOT NULL,
    return_date DATE,
    status VARCHAR(20) DEFAULT 'borrowing',
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE
);

-- 6. เพิ่มข้อมูลตัวอย่างลงตาราง Users (Admin 1 คน, User 2 คน)
INSERT INTO Users (username, password, name, role) VALUES
('admin01', '1234', 'ผู้ดูแลระบบ 1', 'admin'),
('user01', '1234', 'สมชาย ใจดี', 'user'),
('user02', '1234', 'สมหญิง รักเรียน', 'user');

-- 7. เพิ่มข้อมูลตัวอย่างลงตาราง Books (ว่าง 2 เล่ม, ถูกยืมอยู่ 1 เล่ม)
INSERT INTO Books (title, status) VALUES
('การเขียนโปรแกรม Flutter เบื้องต้น', 'available'),
('เรียนรู้ React Native ใน 24 ชั่วโมง', 'borrowed'),
('Database Design Concept', 'available');

-- 8. เพิ่มข้อมูลตัวอย่างลงตาราง Transactions (คืนแล้ว 2 รายการ, กำลังยืม 1 รายการ)
INSERT INTO Transactions (user_id, book_id, borrow_date, return_date, status) VALUES
(2, 1, '2026-02-01', '2026-02-05', 'returned'),
(3, 3, '2026-02-10', '2026-02-12', 'returned'),
(2, 2, '2026-02-15', NULL, 'borrowing');