<?php
// 1. เปิด Error Reporting เพื่อดูว่าบรรทัดไหนพัง (ถ้ายัง 500 อีกมันจะฟ้องจุด)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('config/db.php'); // ตรวจสอบว่า Path ถูกต้องตามโครงสร้างโปรเจกต์

// 2. ตรวจสอบสิทธิ์ (ต้องเป็น Admin เท่านั้นถึงจะทำรายการนี้ได้)
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

// 3. จัดการเปลี่ยนบทบาท (POST จาก Select Dropdown)
if(isset($_POST['action']) && $_POST['action'] == 'update_role') {
    $target_user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);

    // ป้องกันการเปลี่ยนบทบาทตัวเอง (Safety Check)
    if($target_user_id == $_SESSION['user_id']) {
        echo "<script>alert('คุณไม่สามารถเปลี่ยนบทบาทของตัวเองได้!'); window.location='admin_dashboard.php';</script>";
        exit();
    }

    $sql = "UPDATE users SET role = '$new_role' WHERE user_id = '$target_user_id'";
    
    if(mysqli_query($conn, $sql)) {
        // ถ้าเปลี่ยนสิทธิ์สำเร็จ ให้เด้งกลับหน้าเดิมพร้อมข้อความสำเร็จ
        echo "<script>alert('อัปเดตบทบาทเป็น " . strtoupper($new_role) . " เรียบร้อยแล้ว'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "Error Updating Role: " . mysqli_error($conn);
    }
    exit();
}

// 4. จัดการแบน/ปลดแบน (GET จากปุ่ม)
if(isset($_GET['action']) && $_GET['action'] == 'toggle_status') {
    $target_user_id = mysqli_real_escape_string($conn, $_GET['id']);
    $current_status = mysqli_real_escape_string($conn, $_GET['status']);
    
    // สลับสถานะ
    $new_status = ($current_status == 'active') ? 'banned' : 'active';

    $sql = "UPDATE users SET status = '$new_status' WHERE user_id = '$target_user_id'";
    
    if(mysqli_query($conn, $sql)) {
        echo "<script>window.location='admin_dashboard.php';</script>";
    } else {
        echo "Error Changing Status: " . mysqli_error($conn);
    }
    exit();
}

// ถ้าไม่มี Action อะไรเลย ให้ส่งกลับ
header("Location: admin_dashboard.php");
exit();
?>