<?php
// เปิดโหมดโชว์ Error ไว้ชั่วคราว
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('config/db.php');

// ==========================================
// 1. ระบบสมัครสมาชิก (Register)
// ==========================================
if(isset($_POST['register'])){
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if($password !== $confirm_password) {
        echo "<script>alert('รหัสผ่านไม่ตรงกัน กรุณาลองใหม่'); window.history.back();</script>";
        exit();
    }

    $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if(mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('ชื่อผู้ใช้งาน หรือ อีเมล นี้มีในระบบแล้ว'); window.history.back();</script>";
    } else {
        // เพิ่มคอลัมน์ status='active' เป็นค่าเริ่มต้น
        $sql = "INSERT INTO users (username, password, fullname, email, role, status) 
                VALUES ('$username', '$password', '$fullname', '$email', 'user', 'active')";
        
        if(mysqli_query($conn, $sql)){
            echo "<script>alert('สมัครสมาชิกสำเร็จ!'); window.location='login.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// ==========================================
// 2. ระบบเข้าสู่ระบบ (Login) - **อัปเกรดเช็ค BAN**
// ==========================================
if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        
        // --- ส่วนที่เพิ่มใหม่: เช็คสถานะการโดนแบน ---
        if($user['status'] == 'banned') {
            echo "<script>
                    alert('บัญชีของคุณถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแลระบบ'); 
                    window.location='login.php';
                  </script>";
            exit();
        }
        
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Username หรือ Password ไม่ถูกต้อง!'); window.location='login.php';</script>";
    }
}

// ==========================================
// 3. ระบบลืมรหัสผ่าน (Forgot Password)
// ==========================================
if(isset($_POST['forgot_password'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $user = mysqli_fetch_assoc($result);
        $found_password = $user['password'];
        echo "<script>alert('พบข้อมูล! รหัสผ่านคือ: $found_password'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('ไม่พบอีเมลนี้ในระบบ'); window.history.back();</script>";
    }
}
?>