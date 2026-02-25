<?php
// 1. เปิด Debug ทุกอย่าง
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('config/db.php');

// ตรวจสอบว่าถ้าส่งมาแล้ว Array ว่างจริง ให้เช็ค Error ของระบบ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
    die("Error: ข้อมูลที่ส่งมาใหญ่เกินกว่าที่ Server จะรับได้ (Check post_max_size ใน php.ini)");
}

// เช็คข้อมูลเบื้องต้น
if (!isset($_POST['book_name'])) {
    echo "หาตัวแปร book_name ไม่เจอ! <br> ข้อมูลที่ได้รับจากเครื่องมึง: <pre>";
    print_r($_POST); // ดูค่าตัวหนังสือ
    print_r($_FILES); // ดูค่ารูปภาพ
    echo "</pre>";
    die("จบการทำงาน: กรุณาเช็คว่าเลือกไฟล์รูปใหญ่เกินไปหรือไม่? หรือลองไม่เลือกรูปแล้วกดบันทึกดูครับ");
}

// --- ถ้าผ่านจุดข้างบนมาได้ แสดงว่าข้อมูลมาแล้ว ---
$book_name = mysqli_real_escape_string($conn, $_POST['book_name']);
$author = mysqli_real_escape_string($conn, $_POST['author']);
$duration = mysqli_real_escape_string($conn, $_POST['borrow_duration']);
$category = mysqli_real_escape_string($conn, $_POST['category']);
$description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

$image_name = NULL;

// จัดการรูปภาพ (เพิ่มการเช็คขนาดไฟล์)
if(isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
    if ($_FILES['book_image']['size'] > 2000000) { // เกิน 2MB
        echo "<script>alert('ไฟล์รูปใหญ่เกินไป! ห้ามเกิน 2MB'); window.history.back();</script>";
        exit();
    }
    
    $ext = strtolower(pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION));
    $new_name = "cover_" . time() . "_" . rand(100,999) . "." . $ext;
    $upload_dir = "assets/img/covers/";
    
    if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }

    if(move_uploaded_file($_FILES['book_image']['tmp_name'], $upload_dir . $new_name)) {
        $image_name = $new_name;
    }
}

// บันทึก
$sql = "INSERT INTO books (book_name, author, category, borrow_duration, book_image, description, status) 
        VALUES ('$book_name', '$author', '$category', '$duration', '$image_name', '$description', 'available')";

if(mysqli_query($conn, $sql)) {
    echo "<script>alert('สำเร็จ!'); window.location.href='admin_dashboard.php';</script>";
} else {
    echo "SQL Error: " . mysqli_error($conn);
}
?>