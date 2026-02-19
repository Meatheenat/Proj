<?php 
session_start(); 
// ตรวจสอบว่าถ้า Login อยู่แล้ว ให้เด้งกลับไปหน้าแรกทันที (UX ที่ดี)
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
    <title>เข้าสู่ระบบ - ระบบยืมคืนหนังสือ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        /* จัดตำแหน่งฟอร์มให้อยู่กึ่งกลางจอภาพเสมอ */
        .login-wrapper {
            min-height: calc(100vh - 56px); /* ความสูงหน้าจอ ลบ ความสูง Navbar */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 400px; /* ขนาดกำลังดีบนมือถือและแท็บเล็ต */
            padding: 2.5rem 2rem;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); /* เงาแบบนุ่มนวล */
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">LibraryMobile</a>
    <div class="ms-auto text-white">
        <a href='index.php' class='btn btn-outline-light btn-sm'>กลับหน้าแรก</a>
    </div>
  </div>
</nav>

<div class="container login-wrapper">
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">เข้าสู่ระบบ</h2>
            <p class="text-muted small">กรุณากรอกชื่อผู้ใช้งานและรหัสผ่าน</p>
        </div>
        
        <form action="auth_action.php" method="POST" id="loginForm">
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">ชื่อผู้ใช้งาน (Username)</label>
                <input type="text" name="username" id="username" class="form-control form-control-lg" placeholder="กรอกชื่อผู้ใช้งาน" required autofocus>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">รหัสผ่าน (Password)</label>
                <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="กรอกรหัสผ่าน" required>
            </div>
            
            <button type="submit" name="login" class="btn btn-primary btn-lg w-100 mb-3 fw-bold">เข้าสู่ระบบ</button>
            
            <div class="text-center mt-3">
                <p class="mb-1 text-muted">ยังไม่มีบัญชีใช่หรือไม่? <a href="register.php" class="text-decoration-none fw-bold">สมัครสมาชิกที่นี่</a></p>
                <a href="forgot_password.php" class="text-decoration-none text-danger small">ลืมรหัสผ่าน?</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>

</body>
</html>