<?php
session_start();
// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
include('config/db.php');

// ==========================================
// 1. ระบบสมัครสมาชิก (Register)
// ==========================================
if(isset($_POST['register'])){
    // ป้องกัน SQL Injection ด้วย mysqli_real_escape_string
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);

    // เช็คก่อนว่า Username นี้มีคนใช้หรือยัง?
    $check_query = "SELECT * FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_query);

    if(mysqli_num_rows($check_result) > 0) {
        // ถ้ามีซ้ำ ให้แจ้งเตือนและกลับไปหน้าเดิม
        echo "<script>
                alert('ชื่อผู้ใช้งานนี้มีในระบบแล้ว กรุณาใช้ชื่ออื่น'); 
                window.history.back();
              </script>";
    } else {
        // ถ้าไม่ซ้ำ ให้บันทึกลงฐานข้อมูล
        $sql = "INSERT INTO users (username, password, fullname, role) VALUES ('$username', '$password', '$fullname', 'member')";
        
        if(mysqli_query($conn, $sql)){
            echo "<script>
                    alert('สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ'); 
                    window.location='login.php';
                  </script>";
        } else {
            // กรณี Query พัง จะแจ้ง Error ออกมาให้เห็น
            echo "<script>
                    alert('เกิดข้อผิดพลาด: " . mysqli_error($conn) . "'); 
                    window.history.back();
                  </script>";
        }
    }
}

// ==========================================
// 2. ระบบเข้าสู่ระบบ (Login)
// ==========================================
if(isset($_POST['login'])){
    // ป้องกัน SQL Injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // ค้นหาข้อมูลในตาราง users
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    
    // ถ้าเจอข้อมูล 1 รายการพอดี (Login ผ่าน)
    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        
        // เก็บข้อมูลลง Session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role']; // เก็บสถานะ admin/member เผื่อใช้แยกสิทธิ์
        
        // ส่งไปหน้าแรก
        header("Location: index.php");
        exit(); // ควรใส่ exit เสมอหลังจากสั่ง header
    } else {
        // ถ้า Login ไม่ผ่าน
        echo "<script>
                alert('Username หรือ Password ไม่ถูกต้อง!'); 
                window.location='login.php';
              </script>";
    }
}
?>