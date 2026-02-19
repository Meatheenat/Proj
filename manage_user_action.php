<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('config/db.php');

// ตรวจสอบความปลอดภัย
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

// --- เปลี่ยนบทบาท (POST) ---
if(isset($_POST['action']) && $_POST['action'] == 'update_role') {
    $target_user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);

    // ห้ามเปลี่ยนสิทธิ์ตัวเอง
    if($target_user_id == $_SESSION['user_id']) {
        echo "<script>alert('ห้ามเปลี่ยนบทบาทตัวเอง!'); window.location='admin_dashboard.php';</script>";
        exit();
    }

    $sql = "UPDATE users SET role = '$new_role' WHERE user_id = '$target_user_id'";
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('อัปเดตบทบาทสำเร็จ'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit();
}

// --- แบน/ปลดแบน (GET) ---
if(isset($_GET['action']) && $_GET['action'] == 'toggle_status') {
    $target_user_id = mysqli_real_escape_string($conn, $_GET['id']);
    $current_status = mysqli_real_escape_string($conn, $_GET['status']);
    
    if($target_user_id == $_SESSION['user_id']) {
        echo "<script>alert('ห้ามแบนตัวเอง!'); window.location='admin_dashboard.php';</script>";
        exit();
    }

    $new_status = ($current_status == 'active') ? 'banned' : 'active';
    $sql = "UPDATE users SET status = '$new_status' WHERE user_id = '$target_user_id'";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit();
}

header("Location: admin_dashboard.php");
exit();
?>