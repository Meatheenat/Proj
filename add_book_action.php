<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('config/db.php');

// 1. เช็คว่ามีข้อมูลส่งมาแบบ POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Error: มึงไม่ได้ส่งข้อมูลแบบ POST มานะ หรือเรียกไฟล์นี้ตรงๆ?");
}

// 2. เช็คว่าเจอตัวแปร book_name ไหม
if(isset($_POST['book_name'])) {
    
    $book_name = mysqli_real_escape_string($conn, $_POST['book_name']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $duration = mysqli_real_escape_string($conn, $_POST['borrow_duration']);
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? 'ทั่วไป');
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

    $image_name = NULL;

    // ระบบจัดการไฟล์รูปภาพ
    if(isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'webp');

        if(in_array($ext, $allowed)) {
            $new_name = "cover_" . time() . "_" . rand(1000, 9999) . "." . $ext;
            $upload_path = "assets/img/covers/";
            
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $target = $upload_path . $new_name;
            if(move_uploaded_file($_FILES['book_image']['tmp_name'], $target)) {
                $image_name = $new_name;
            }
        }
    }

    // 3. คำสั่ง SQL
    $sql = "INSERT INTO books (book_name, author, category, borrow_duration, book_image, description, status) 
            VALUES ('$book_name', '$author', '$category', '$duration', '$image_name', '$description', 'available')";

    if(mysqli_query($conn, $sql)) {
        echo "<script>
                alert('เพิ่มหนังสือสำเร็จ!'); 
                window.location.href='admin_dashboard.php';
              </script>";
        exit();
    } else {
        echo "SQL Error: " . mysqli_error($conn); // ถ้า SQL พังจะโชว์ตรงนี้
    }
} else {
    // ถ้าหา book_name ไม่เจอ จะแสดงค่าที่ได้รับมาทั้งหมดเพื่อตรวจสอบ
    echo "หาตัวแปร book_name ไม่เจอ! ข้อมูลที่ส่งมามีแค่นี้: <pre>";
    print_r($_POST);
    echo "</pre>";
    die("ตรวจสอบ name ในหน้า Admin Dashboard ด่วนเพื่อน!");
}
?>