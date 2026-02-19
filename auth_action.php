<?php
// เปิดโหมดโชว์ Error ไว้ชั่วคราว เพื่อหาบั๊ก
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
    $email = mysqli_real_escape_string($conn, $_POST['email']); // รับค่า email ที่เพิ่มมาใหม่
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // เช็ครหัสผ่านให้ตรงกันอีกรอบที่ฝั่งหลังบ้าน (Backend)
    if($password !== $confirm_password) {
        echo "<script>alert('รหัสผ่านไม่ตรงกัน กรุณาลองใหม่'); window.history.back();</script>";
        exit();
    }

    // เช็คว่า Username หรือ Email นี้มีคนใช้หรือยัง?
    $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if(mysqli_num_rows($check_result) > 0) {
        echo "<script>
                alert('ชื่อผู้ใช้งาน หรือ อีเมล นี้มีในระบบแล้ว กรุณาใช้ข้อมูลอื่น'); 
                window.history.back();
              </script>";
    } else {
        // เพิ่มข้อมูลลงตาราง (อัปเดตคำสั่ง SQL ให้มีคอลัมน์ email)
        $sql = "INSERT INTO users (username, password, fullname, email, role) 
                VALUES ('$username', '$password', '$fullname', '$email', 'member')";
        
        if(mysqli_query($conn, $sql)){
            echo "<script>
                    alert('สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ'); 
                    window.location='login.php';
                  </script>";
        } else {
            // ถ้า SQL พัง จะแสดง Error แจ้งให้เรารู้
            echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($conn);
        }
    }
}

// ==========================================
// 2. ระบบเข้าสู่ระบบ (Login)
// ==========================================
if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: index.php");
        exit();
    } else {
        echo "<script>
                alert('Username หรือ Password ไม่ถูกต้อง!'); 
                window.location='login.php';
              </script>";
    }
}
// ==========================================
// 3. ระบบลืมรหัสผ่าน (Forgot Password)
// ==========================================
if(isset($_POST['forgot_password'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // ค้นหาว่ามีอีเมลนี้ในระบบหรือไม่
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $user = mysqli_fetch_assoc($result);
        $found_password = $user['password'];
        
        // ในระบบจริงควรส่งเข้าอีเมล แต่เพื่อความง่ายในการพัฒนา จะแสดงแจ้งเตือนให้เห็นเลย
        echo "<script>
                alert('พบข้อมูลของคุณ! รหัสผ่านของคุณคือ: $found_password'); 
                window.location='login.php';
              </script>";
    } else {
        echo "<script>
                alert('ไม่พบอีเมลนี้ในระบบ กรุณาตรวจสอบอีกครั้ง'); 
                window.history.back();
              </script>";
    }
}
?>