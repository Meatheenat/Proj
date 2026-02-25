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
ALTER TABLE users MODIFY COLUMN role VARCHAR(20);
UPDATE users SET role = 'user' WHERE role != 'admin' OR role IS NULL;
ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin') NOT NULL DEFAULT 'user';
ALTER TABLE books ADD COLUMN book_image VARCHAR(255) DEFAULT NULL;
ALTER TABLE books ADD COLUMN description TEXT DEFAULT NULL;
INSERT INTO books (book_name, author, category, status, book_image) VALUES
('เรียนรู้ PHP ใน 24 ชั่วโมง', 'สมชาย สายโค้ด', 'IT', 'available', NULL),
('Deep Learning เบื้องต้น', 'ดร.มานะ เรียนดี', 'IT', 'available', NULL),
('Network Security สำหรับมือใหม่', 'วิชิต ปลอดภัย', 'IT', 'available', NULL),
('ดาบพิฆาตอสูร เล่ม 1', 'Koyoharu Gotouge', 'Cartoon', 'available', NULL),
('One Piece เล่ม 100', 'Eiichiro Oda', 'Cartoon', 'available', NULL),
('จิตวิทยาการสื่อสาร', 'นพ.สมเกียรติ', 'Psychology', 'available', NULL),
('คิดแบบผู้นำ', 'John Maxwell', 'Psychology', 'available', NULL),
('ความลับของจักรวาล', 'สตีเฟน ฮอว์กิง', 'Science', 'available', NULL),
('ประวัติศาสตร์ไทยฉบับย่อ', 'ไพโรจน์ บรรพบุรุษ', 'History', 'available', NULL),
('ทำอาหารง่ายๆ สไตล์เด็กหอ', 'แม่ครัวตัวน้อย', 'Cooking', 'available', NULL),
('สรุปเข้ม Computer Science', 'อ.สมปอง', 'IT', 'available', NULL),
('SQL Expert Step by Step', 'ธีระพงษ์ ฐานข้อมูล', 'IT', 'available', NULL),
('วิถีแห่งเซน', 'Daisetz Suzuki', 'Philosophy', 'available', NULL),
('สงครามโลกครั้งที่ 2', 'Winston Churchill', 'History', 'available', NULL),
('การออกแบบ UI/UX', 'แอนนา ดีไซน์', 'IT', 'available', NULL),
('Python for Data Science', 'Guido van Rossum', 'IT', 'available', NULL),
('Naruto: Boruto Next Gen', 'Masashi Kishimoto', 'Cartoon', 'available', NULL),
('Jujutsu Kaisen Vol.0', 'Gege Akutami', 'Cartoon', 'available', NULL),
('วิธีการปฏิเสธคน', 'ซาซากิ ฟุมิโอะ', 'Psychology', 'available', NULL),
('Atomic Habits', 'James Clear', 'Psychology', 'available', NULL),
('Linux Server Administration', 'Linus Torvalds', 'IT', 'available', NULL),
('Docker for Developers', 'Solomon Hykes', 'IT', 'available', NULL),
('React Native Workshop', 'Mark Zuckerberg', 'IT', 'available', NULL),
('Slam Dunk Re-edit', 'Takehiko Inoue', 'Cartoon', 'available', NULL),
('Attack on Titan Final', 'Hajime Isayama', 'Cartoon', 'available', NULL),
('เซเปียนส์ ประวัติย่อมนุษยชาติ', 'Yuval Noah Harari', 'History', 'available', NULL),
('สตาร์ทอัพสร้างได้', 'Eric Ries', 'Business', 'available', NULL),
('พ่อรวยสอนลูก', 'Robert Kiyosaki', 'Business', 'available', NULL),
('The Lean Startup', 'Eric Ries', 'Business', 'available', NULL),
('Zero to One', 'Peter Thiel', 'Business', 'available', NULL),
('JavaScript Modern ES6+', 'Brendan Eich', 'IT', 'available', NULL),
('Go Programming Guide', 'Google Team', 'IT', 'available', NULL),
('Kotlin for Android', 'JetBrains', 'IT', 'available', NULL),
('Blue Lock เล่ม 1', 'Muneyuki Kaneshiro', 'Cartoon', 'available', NULL),
('Spy x Family Vol.1', 'Tatsuya Endo', 'Cartoon', 'available', NULL),
('การบริหารเวลาขั้นเทพ', 'Brian Tracy', 'Psychology', 'available', NULL),
('ศิลปะการอยู่ร่วมกับคนเฮงซวย', 'Robert Sutton', 'Psychology', 'available', NULL),
('จักรวาลในเปลือกนัท', 'Stephen Hawking', 'Science', 'available', NULL),
('สรุปกฎหมายแพ่ง', 'ทนายสมพงษ์', 'Law', 'available', NULL),
('คู่มือซ่อมคอมพิวเตอร์', 'ช่างต้อม', 'IT', 'available', NULL),
('Cyberpunk 2077 Lore', 'CD Projekt Red', 'Game', 'available', NULL),
('Elden Ring Official Guide', 'FromSoftware', 'Game', 'available', NULL),
('Minecraft Survival Guide', 'Mojang', 'Game', 'available', NULL),
('Genshin Impact Artbook', 'Hoyoverse', 'Game', 'available', NULL),
('Honkai Star Rail Journal', 'Hoyoverse', 'Game', 'available', NULL),
('League of Legends Universe', 'Riot Games', 'Game', 'available', NULL),
('Grand Theft Auto History', 'Rockstar Games', 'Game', 'available', NULL),
('Call of Duty Tactics', 'Activision', 'Game', 'available', NULL),
('Sniper Elite Handbook', 'Rebellion', 'Game', 'available', NULL),
('Assassin Creed Brotherhood', 'Ubisoft', 'Game', 'available', NULL);