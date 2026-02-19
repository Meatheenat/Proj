<?php
session_start();
include('config/db.php');

if(isset($_POST['book_name'])) {
    $book_name = mysqli_real_escape_string($conn, $_POST['book_name']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $duration = mysqli_real_escape_string($conn, $_POST['borrow_duration']);
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? 'ทั่วไป');

    $image_name = NULL;

    // ระบบจัดการไฟล์รูปภาพ
    if(isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
        $ext = pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION);
        $new_name = "cover_" . time() . "." . $ext; // ตั้งชื่อใหม่กันชื่อซ้ำ
        $target = "assets/img/covers/" . $new_name;

        if(move_uploaded_file($_FILES['book_image']['tmp_name'], $target)) {
            $image_name = $new_name;
        }
    }

    $sql = "INSERT INTO books (book_name, author, category, borrow_duration, book_image, status) 
            VALUES ('$book_name', '$author', '$category', '$duration', '$image_name', 'available')";

    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('เพิ่มหนังสือสำเร็จ!'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>