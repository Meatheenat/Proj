<?php
session_start();
include('config/db.php');

// ตรวจสอบว่าเป็น Admin หรือไม่
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

// 1. กรณีเปลี่ยนบทบาท (Update Role)
if(isset($_POST['action']) && $_POST['action'] == 'update_role') {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);

    $sql = "UPDATE users SET role = '$new_role' WHERE user_id = '$user_id'";
    if(mysqli_query($conn, $sql)) {
        header("Location: admin_dashboard.php?success=role_updated");
    }
}

// 2. กรณีแบน/ปลดแบน (Toggle Status)
if(isset($_GET['action']) && $_GET['action'] == 'toggle_status') {
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);
    $current_status = mysqli_real_escape_string($conn, $_GET['status']);
    
    // สลับสถานะ
    $new_status = ($current_status == 'active') ? 'banned' : 'active';

    $sql = "UPDATE users SET status = '$new_status' WHERE user_id = '$user_id'";
    if(mysqli_query($conn, $sql)) {
        header("Location: admin_dashboard.php?success=status_changed");
    }
}
exit();
?>