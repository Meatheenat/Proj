<?php
// ตั้งค่าสำหรับการเชื่อมต่อบนโฮสติ้งจริง (Hosting)
$host = "localhost"; // ใช้ localhost เพราะ Database รันบนเครื่องเดียวกับ Web Server
$user = "s673190104";
$pass = "s673190104";
$dbname = "s673190104";

// สร้างการเชื่อมต่อ
$conn = mysqli_connect($host, $user, $pass, $dbname);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ตั้งค่าให้รองรับภาษาไทย (UTF-8)
mysqli_set_charset($conn, "utf8");
?>