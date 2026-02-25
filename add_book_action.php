<?php
// 1. ตั้งค่าให้สคริปต์รันได้ไม่จำกัดเวลา (ป้องกันการค้างเวลาอัปโหลดไฟล์ใหญ่)
set_time_limit(0); 
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('config/db.php');

// ตรวจสอบว่าถ้าส่งมาแล้ว Array ว่าง (ส่วนใหญ่เป็นเพราะไฟล์ใหญ่เกินค่าที่ Server รับได้ใน .htaccess)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
    die("Error: ไฟล์ใหญ่เกินกว่าที่โฮสต์ของมรึงจะรับได้! (ไปแก้เลขใน .htaccess ให้สูงขึ้นครับ)");
}

if (!isset($_POST['book_name'])) {
    die("จบการทำงาน: ไม่ได้รับข้อมูลจากฟอร์ม");
}

$book_name = mysqli_real_escape_string($conn, $_POST['book_name']);
$author = mysqli_real_escape_string($conn, $_POST['author']);
$duration = mysqli_real_escape_string($conn, $_POST['borrow_duration']);
$category = mysqli_real_escape_string($conn, $_POST['category']);
$description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

$image_name = NULL;

// 2. ส่วนจัดการรูปภาพ (เอาตัวดัก 2MB ออกแล้ว!)
if(isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
    
    // หมายเหตุ: ผมเอาเงื่อนไข if size > 2000000 ออกให้ตามที่มรึงต้องการแล้วนะเพื่อน
    
    $ext = strtolower(pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION));
    $new_name = "cover_" . time() . "_" . rand(100,999) . "." . $ext;
    $upload_dir = "assets/img/covers/";
    
    if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }

    if(move_uploaded_file($_FILES['book_image']['tmp_name'], $upload_dir . $new_name)) {
        $image_name = $new_name;
    }
}

// 3. บันทึกลงฐานข้อมูล
$sql = "INSERT INTO books (book_name, author, category, borrow_duration, book_image, description, status) 
        VALUES ('$book_name', '$author', '$category', '$duration', '$image_name', '$description', 'available')";

if(mysqli_query($conn, $sql)) {
    echo "<script>alert('เพิ่มหนังสือเรียบร้อย!'); window.location.href='admin_dashboard.php';</script>";
} else {
    echo "SQL Error: " . mysqli_error($conn);
}
?>