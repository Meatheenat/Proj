<?php 
session_start(); 
// ถ้า Login อยู่แล้ว ให้เด้งไปหน้าแรกเลย
if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - ระบบยืมคืนหนังสือ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        /* จัด Layout ให้อยู่ตรงกลาง คลุมโทนเดียวกับหน้า Login */
        .register-wrapper {
            min-height: calc(100vh - 56px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-card {
            width: 100%;
            max-width: 500px; /* ขยายกว้างขึ้นนิดนึงเพื่อรองรับฟิลด์ที่เยอะขึ้น */
            padding: 2.5rem 2rem;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">LibraryMobile</a>
    <div class="ms-auto text-white">
        <a href='login.php' class='btn btn-outline-light btn-sm'>กลับหน้าเข้าสู่ระบบ</a>
    </div>
  </div>
</nav>

<div class="container register-wrapper">
    <div class="register-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-success">สมัครสมาชิก</h2>
            <p class="text-muted small">กรอกข้อมูลให้ครบถ้วนเพื่อสร้างบัญชีใหม่</p>
        </div>
        
        <form action="auth_action.php" method="POST" id="registerForm">
            <div class="mb-3">
                <label for="fullname" class="form-label fw-semibold">ชื่อ-นามสกุล (Full Name)</label>
                <input type="text" name="fullname" id="fullname" class="form-control" placeholder="ชื่อ นามสกุล" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">อีเมล (Email)</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="example@email.com" required>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">ชื่อผู้ใช้งาน (Username)</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="ตั้งชื่อผู้ใช้งาน" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label fw-semibold">รหัสผ่าน (Password)</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="รหัสผ่าน" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="confirm_password" class="form-label fw-semibold">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="รหัสผ่านอีกครั้ง" required>
                </div>
            </div>
            
            <button type="submit" name="register" class="btn btn-success btn-lg w-100 mb-3 fw-bold">ยืนยันการสมัครสมาชิก</button>
            
            <div class="text-center mt-3">
                <p class="mb-1 text-muted">มีบัญชีอยู่แล้วใช่หรือไม่? <a href="login.php" class="text-decoration-none fw-bold">เข้าสู่ระบบที่นี่</a></p>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>

<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        var pwd = document.getElementById('password').value;
        var cpwd = document.getElementById('confirm_password').value;
        if(pwd !== cpwd) {
            alert('รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน กรุณาตรวจสอบอีกครั้ง!');
            e.preventDefault(); // เบรกไม่ให้ฟอร์มเด้งไปหน้า auth_action.php
        }
    });
</script>

</body>
</html>