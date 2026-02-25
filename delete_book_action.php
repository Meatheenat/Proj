<?php
session_start();
include('config/db.php');

if(isset($_GET['id']) && $_SESSION['role'] === 'admin') {
    $book_id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. เช็คสถานะเล่มนี้อีกครั้งเพื่อความชัวร์
    $check_sql = "SELECT status, book_image FROM books WHERE book_id = '$book_id'";
    $res = mysqli_query($conn, $check_sql);
    $book = mysqli_fetch_assoc($res);

    if($book['status'] === 'available') {
        // 2. ลบรูปหน้าปกออกจากเครื่อง (ถ้ามี)
        if($book['book_image'] && file_exists("assets/img/covers/" . $book['book_image'])) {
            unlink("assets/img/covers/" . $book['book_image']);
        }

        // 3. ลบข้อมูลจากฐานข้อมูล
        $del_sql = "DELETE FROM books WHERE book_id = '$book_id'";
        if(mysqli_query($conn, $del_sql)) {
            echo "<script>alert('ลบหนังสือเรียบร้อยแล้ว'); window.location='admin_dashboard.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('ไม่สามารถลบได้ เนื่องจากหนังสือถูกยืมอยู่!'); window.location='admin_dashboard.php';</script>";
    }
}
?>