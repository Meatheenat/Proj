<?php
// 1. เปิดโหมดดู Error เพื่อตรวจสอบจุดที่ติดขัด (IT Support Style)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('config/db.php');

// ตรวจสอบความปลอดภัย (ต้องเป็น Admin เท่านั้น)
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

if(isset($_POST['book_name'])) {
    // 2. รับค่าจากฟอร์ม (รวมถึง description ที่เพิ่มมาใหม่)
    $book_name = mysqli_real_escape_string($conn, $_POST['book_name']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $duration = mysqli_real_escape_string($conn, $_POST['borrow_duration']);
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? 'ทั่วไป');
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? ''); // เพิ่มส่วนนี้

    $image_name = NULL;

    // 3. ระบบจัดการไฟล์รูปภาพ
    if(isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'webp'); // กำหนดนามสกุลที่อนุญาต

        if(in_array($ext, $allowed)) {
            $new_name = "cover_" . time() . "_" . rand(1000, 9999) . "." . $ext;
            $upload_path = "assets/img/covers/";
            
            // สร้างโฟลเดอร์อัตโนมัติถ้าไม่มี
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $target = $upload_path . $new_name;

            if(move_uploaded_file($_FILES['book_image']['tmp_name'], $target)) {
                $image_name = $new_name;
            }
        }
    }

    // 4. บันทึกลงฐานข้อมูล (เพิ่มคอลัมน์ description เข้าไปใน Query)
    $sql = "INSERT INTO books (book_name, author, category, borrow_duration, book_image, description, status) 
            VALUES ('$book_name', '$author', '$category', '$duration', '$image_name', '$description', 'available')";

    if(mysqli_query($conn, $sql)) {
        // 5. แก้ไขหน้าค้าง: แจ้งเตือนแล้ว Redirect ทันที
        echo "<script>
                alert('เพิ่มหนังสือ \"$book_name\" สำเร็จ!'); 
                window.location.href='admin_dashboard.php';
              </script>";
        exit();
    } else {
        // กรณี SQL Error ให้แสดงแจ้งเตือนแทนหน้าขาว
        echo "<script>
                alert('เกิดข้อผิดพลาด: " . mysqli_error($conn) . "');
                window.history.back();
              </script>";
        exit();
    }
} else {
    // ถ้าไม่ได้มาจากฟอร์ม ให้ดีดกลับหน้าหลัก
    header("Location: admin_dashboard.php");
    exit();
}
?>