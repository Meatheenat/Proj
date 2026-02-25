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
-- เคลียร์ข้อมูลเก่า (ถ้าต้องการเริ่มใหม่)
-- DELETE FROM books;

INSERT INTO books (book_name, author, category, status, borrow_duration, description) VALUES
('PHP & MySQL Beginner', 'John Doe', 'Programming', 'available', '7,15,30', 'พื้นฐานการเขียนเว็บด้วย PHP และการจัดการฐานข้อมูล MySQL เหมาะสำหรับผู้เริ่มต้นที่ต้องการสร้างระบบยืมคืนหนังสือ'),
('UI/UX Design Mobile', 'Jane Smith', 'Design', 'available', '7,15,30', 'แนวคิดการออกแบบหน้าจอแอปพลิเคชันมือถือให้สวยงาม ใช้งานง่าย และตอบโจทย์ผู้ใช้งานยุคใหม่'),
('Database Systems', 'Robert Brown', 'Database', 'available', '7,15,30', 'ทฤษฎีและการออกแบบระบบฐานข้อมูลในระดับองค์กร ครอบคลุมเรื่อง Normalization และความปลอดภัยของข้อมูล'),
('เรียนรู้ PHP ใน 24 ชั่วโมง', 'สมชาย สายโค้ด', 'IT', 'available', '7,10,15', 'คู่มือเรียนเร็วสำหรับการเป็น Web Developer ภายใน 1 วัน เน้นการทำ Workshop จริง'),
('Deep Learning เบื้องต้น', 'ดร.มานะ เรียนดี', 'IT', 'available', '7,15', 'เจาะลึก AI และโครงข่ายประสาทเทียม เข้าใจกลไกการทำงานของปัญญาประดิษฐ์ในยุคปัจจุบัน'),
('Network Security สำหรับมือใหม่', 'วิชิต ปลอดภัย', 'IT', 'available', '7,15,30', 'พื้นฐานการป้องกันระบบเครือข่ายจากการโจมตี และการตั้งค่า Firewall เบื้องต้น'),
('ดาบพิฆาตอสูร เล่ม 1', 'Koyoharu Gotouge', 'Cartoon', 'available', '3,5,7', 'เรื่องราวการต่อสู้ของทันจิโร่เพื่อช่วยน้องสาวเนซึโกะที่กลายเป็นอสูร การเริ่มต้นของเสาหลักและหน่วยพิฆาตอสูร'),
('One Piece เล่ม 100', 'Eiichiro Oda', 'Cartoon', 'available', '3,5,7', 'การเดินทางของลูฟี่สู่การเป็นราชาโจรสลัด และการต่อสู้ครั้งใหญ่ที่เกาะโอนิกาชิมะ'),
('จิตวิทยาการสื่อสาร', 'นพ.สมเกียรติ', 'Psychology', 'available', '7,15,30', 'เทคนิคการคุยให้คนรักและประทับใจ เข้าใจภาษากายและจิตวิทยาเบื้องต้นของคู่สนทนา'),
('คิดแบบผู้นำ', 'John Maxwell', 'Psychology', 'available', '15,30', 'ปลุกพลังความเป็นผู้นำในตัวคุณ พัฒนาทักษะการตัดสินใจและการบริหารทีมงาน'),
('ความลับของจักรวาล', 'สตีเฟน ฮอว์กิง', 'Science', 'available', '7,15,30', 'ทำความเข้าใจเวลา หลุมดำ และต้นกำเนิดของเอกภพผ่านภาษาที่เข้าใจง่าย'),
('ประวัติศาสตร์ไทยฉบับย่อ', 'ไพโรจน์ บรรพบุรุษ', 'History', 'available', '15,30', 'เหตุการณ์สำคัญตั้งแต่อดีตจนถึงปัจจุบัน สรุปจบในเล่มเดียวเหมาะสำหรับนักเรียนนักศึกษา'),
('ทำอาหารง่ายๆ สไตล์เด็กหอ', 'แม่ครัวตัวน้อย', 'Cooking', 'available', '7,15', 'รวมเมนูจากไมโครเวฟและหม้อหุงข้าว ประหยัดเวลาและค่าใช้จ่ายแต่ยังได้สารอาหารครบถ้วน'),
('สรุปเข้ม Computer Science', 'อ.สมปอง', 'IT', 'available', '7,15', 'สรุปเนื้อหาสำคัญสำหรับนักศึกษาไอที ตั้งแต่ Algorithm ไปจนถึงการทำงานของ CPU'),
('SQL Expert Step by Step', 'ธีระพงษ์ ฐานข้อมูล', 'IT', 'available', '7,15,30', 'สอนเขียน Query ตั้งแต่พื้นฐาน SELECT จนถึงการทำ Store Procedure ระดับสูง'),
('วิถีแห่งเซน', 'Daisetz Suzuki', 'Philosophy', 'available', '15,30', 'ปรัชญาการใช้ชีวิตแบบสงบและเรียบง่าย ท่ามกลางความวุ่นวายของโลกปัจจุบัน'),
('สงครามโลกครั้งที่ 2', 'Winston Churchill', 'History', 'available', '15,30', 'บันทึกเหตุการณ์ประวัติศาสตร์ที่โลกไม่ลืม วิเคราะห์กลยุทธ์และผลกระทบที่เกิดขึ้นทั่วโลก'),
('การออกแบบ UI/UX', 'แอนนา ดีไซน์', 'IT', 'available', '7,15,30', 'เน้นการทำ Workshop ออกแบบแอปพลิเคชันจริงด้วยเครื่องมือ Figma และ Adobe XD'),
('Python for Data Science', 'Guido van Rossum', 'IT', 'available', '7,15,30', 'การวิเคราะห์ข้อมูลขนาดใหญ่และการทำ Data Visualization ด้วยภาษา Python'),
('Naruto: Boruto Next Gen', 'Masashi Kishimoto', 'Cartoon', 'available', '3,5,7', 'ตำนานบทใหม่ของรุ่นลูกนินจา เมื่อโลกนินจาต้องเผชิญกับเทคโนโลยีสมัยใหม่'),
('Jujutsu Kaisen Vol.0', 'Gege Akutami', 'Cartoon', 'available', '3,5,7', 'จุดเริ่มต้นของโรงเรียนไสยเวทย์ และเรื่องราวของ อคคทสึ ยูตะ'),
('วิธีการปฏิเสธคน', 'ซาซากิ ฟุมิโอะ', 'Psychology', 'available', '7,15', 'ใช้ชีวิตให้เบาลงด้วยการปฏิเสธอย่างมีศิลปะ โดยไม่ทำลายความสัมพันธ์'),
('Atomic Habits', 'James Clear', 'Psychology', 'available', '15,30', 'เปลี่ยนแปลงตัวเองวันละ 1% เพื่อผลลัพธ์ที่ยิ่งใหญ่ ผ่านการสร้างนิสัยเล็กๆ ที่ทำได้จริง'),
('Linux Server Administration', 'Linus Torvalds', 'IT', 'available', '7,15,30', 'คู่มือดูแลเซิร์ฟเวอร์ด้วยคำสั่ง Linux Command Line ตั้งแต่การติดตั้งไปจนถึงการรักษาความปลอดภัย'),
('Docker for Developers', 'Solomon Hykes', 'IT', 'available', '7,15,30', 'การทำ Containerization เพื่อให้การรันโปรเจกต์เหมือนกันในทุกสภาพแวดล้อม'),
('React Native Workshop', 'Mark Zuckerberg', 'IT', 'available', '7,15,30', 'สร้างแอป iOS และ Android ด้วยโค้ดชุดเดียวโดยใช้ JavaScript และ React'),
('Slam Dunk Re-edit', 'Takehiko Inoue', 'Cartoon', 'available', '3,5,7', 'การ์ตูนบาสเกตบอลในตำนานฉบับปรับปรุงเนื้อหาและภาพวาดใหม่'),
('Attack on Titan Final', 'Hajime Isayama', 'Cartoon', 'available', '3,5,7', 'บทสรุปของกำแพงและการต่อสู้เพื่ออิสรภาพที่แท้จริงของมนุษยชาติ'),
('เซเปียนส์ ประวัติย่อมนุษยชาติ', 'Yuval Noah Harari', 'History', 'available', '15,30', 'วิวัฒนาการของมนุษย์ตั้งแต่อดีตจนถึงยุคปัจจุบัน และสิ่งที่จะเกิดขึ้นในอนาคต'),
('สตาร์ทอัพสร้างได้', 'Eric Ries', 'Business', 'available', '7,15,30', 'คู่มือการทำธุรกิจยุคใหม่ด้วยแนวคิด Lean Startup'),
('พ่อรวยสอนลูก', 'Robert Kiyosaki', 'Business', 'available', '15,30', 'ความรู้เรื่องการเงินและการลงทุนที่โรงเรียนไม่เคยสอน เพื่ออิสรภาพทางการเงิน'),
('The Lean Startup', 'Eric Ries', 'Business', 'available', '15,30', 'สร้างธุรกิจที่เติบโตได้อย่างรวดเร็วและยั่งยืนในสภาวะที่มีความไม่แน่นอนสูง'),
('Zero to One', 'Peter Thiel', 'Business', 'available', '15,30', 'คิดแบบนักลงทุนระดับโลก สร้างสิ่งใหม่ที่ไม่เคยมีมาก่อนในโลกธุรกิจ'),
('JavaScript Modern ES6+', 'Brendan Eich', 'IT', 'available', '7,15,30', 'มาตรฐานใหม่ของภาษาจาวาสคริปต์ที่นักพัฒนาเว็บทุกคนต้องรู้'),
('Go Programming Guide', 'Google Team', 'IT', 'available', '7,15,30', 'ภาษาแห่งอนาคตที่เน้นความเร็ว ความเรียบง่าย และประสิทธิภาพในงาน Backend'),
('Kotlin for Android', 'JetBrains', 'IT', 'available', '7,15,30', 'ภาษาหลักที่ Google แนะนำให้ใช้พัฒนาแอปพลิเคชัน Android ยุคปัจจุบัน'),
('Blue Lock เล่ม 1', 'Muneyuki Kaneshiro', 'Cartoon', 'available', '3,5,7', 'โปรเจกต์สร้างสุดยอดกองหน้าทีมชาติญี่ปุ่นจากการคัดเลือกสุดโหด'),
('Spy x Family Vol.1', 'Tatsuya Endo', 'Cartoon', 'available', '3,5,7', 'ครอบครัวปลอมๆ ที่ประกอบด้วยจารชน นักฆ่า และเด็กพลังจิต ท่ามกลางภารกิจลับ'),
('การบริหารเวลาขั้นเทพ', 'Brian Tracy', 'Psychology', 'available', '7,15', 'เทคนิคการจัดลำดับความสำคัญของงานเพื่อให้บรรลุเป้าหมายในเวลาที่น้อยลง'),
('ศิลปะการอยู่ร่วมกับคนเฮงซวย', 'Robert Sutton', 'Psychology', 'available', '7,15', 'วิธีรับมือกับคนที่เป็นมลพิษในที่ทำงานโดยไม่ทำให้เสียสุขภาพจิต'),
('จักรวาลในเปลือกนัท', 'Stephen Hawking', 'Science', 'available', '7,15,30', 'อธิบายทฤษฎีควอนตัมฟิสิกส์และมิติที่ซ่อนอยู่แบบเข้าใจง่าย'),
('สรุปกฎหมายแพ่ง', 'ทนายสมพงษ์', 'Law', 'available', '15,30', 'สรุปมาตราสำคัญที่ต้องรู้ในชีวิตประจำวันเพื่อป้องกันการโดนเอาเปรียบ'),
('คู่มือซ่อมคอมพิวเตอร์', 'ช่างต้อม', 'IT', 'available', '7,15', 'วิธีแก้ปัญหา Hardware เบื้องต้นและการไล่หาอาการผิดปกติของเครื่องคอมพิวเตอร์'),
('Cyberpunk 2077 Lore', 'CD Projekt Red', 'Game', 'available', '7,15,30', 'เจาะลึกเบื้องหลังโลกอนาคตของ Night City และประวัติศาสตร์ของเหล่าตัวละคร'),
('Elden Ring Official Guide', 'FromSoftware', 'Game', 'available', '7,15,30', 'แผนที่และจุดซ่อนลับในดินแดนระว่างรอน พร้อมเทคนิคการสู้บอส'),
('Minecraft Survival Guide', 'Mojang', 'Game', 'available', '7,15', 'วิธีการเอาชีวิตรอด การคราฟไอเทมพื้นฐาน และการขุดหาแร่หายาก'),
('Genshin Impact Artbook', 'Hoyoverse', 'Game', 'available', '3,5,7', 'รวมภาพตัวละคร ฉากสวยๆ และคอนเซปต์อาร์ตจากการผจญภัยใน Teyvat'),
('Honkai Star Rail Journal', 'Hoyoverse', 'Game', 'available', '3,5,7', 'บันทึกการเดินทางข้ามดวงดาวและเรื่องราวของเหล่านักเดินทางอาสทราลเอ็กซ์เพรส'),
('League of Legends Universe', 'Riot Games', 'Game', 'available', '7,15,30', 'ตำนานและเรื่องราวของเหล่าแชมเปี้ยนในดินแดน Runeterra'),
('Grand Theft Auto History', 'Rockstar Games', 'Game', 'available', '7,15,30', 'เจาะลึกเบื้องหลังการพัฒนาซีรีส์เกมที่เปลี่ยนโลกของ Open World'),
('Call of Duty Tactics', 'Activision', 'Game', 'available', '7,15', 'กลยุทธ์การเล่นเป็นทีมและการเลือกใช้อาวุธในโหมด Multiplayer'),
('Sniper Elite Handbook', 'Rebellion', 'Game', 'available', '7,15', 'เทคนิคการเล็งยิงระยะไกลโดยคำนวณจากแรงลมและแรงโน้มถ่วง'),
('Assassin Creed Brotherhood', 'Ubisoft', 'Game', 'available', '7,15,30', 'การสร้างกองทัพนักฆ่าและประวัติศาสตร์ของ Ezio ในอิตาลี');