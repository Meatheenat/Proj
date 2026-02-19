<?php
session_start();
include('config/db.php');

// สมัครสมาชิก
if(isset($_POST['register'])){
    $username = $_POST['username'];
    $password = $_POST['password']; // ควรใช้ password_hash() ในงานจริง
    $fullname = $_POST['fullname'];

    $sql = "INSERT INTO users (username, password, fullname, role) VALUES ('$username', '$password', '$fullname', 'member')";
    if(mysqli_query($conn, $sql)){
        echo "<script>alert('สมัครสมาชิกสำเร็จ!'); window.location='login.php';</script>";
    }
}

// เข้าสู่ระบบ
if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['fullname'] = $user['fullname'];
        header("Location: index.php");
    } else {
        echo "<script>alert('Username หรือ Password ไม่ถูกต้อง'); window.location='login.php';</script>";
    }
}
?>