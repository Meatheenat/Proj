<?php 
session_start(); 
// ถ้า Login อยู่แล้ว ให้เด้งไปหน้าแรก
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
    <title>ลืมรหัสผ่าน - ระบบยืมคืนหนังสือ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        /* จัด Layout ให้ฟอร์มอยู่ตรงกลางจอ เหมือนหน้า Register */
        .forgot-wrapper {
            min-height: calc(100vh - 56px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .forgot-card {
            width: 100%;
            max-width: 450px;
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

<div class="container forgot-wrapper">
    <div class="forgot-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-warning">ลืมรหัสผ่าน?</h2>
            <p class="text-muted small">กรุณากรอกอีเมลที่ใช้สมัครสมาชิก เพื่อค้นหาบัญชีของคุณ</p>
        </div>
        
        <form action="auth_action.php" method="POST">
            <div class="mb-4">
                <label for="email" class="form-label fw-semibold">อีเมล (Email)</label>
                <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="กรอกอีเมลของคุณ" required autofocus>
            </div>
            
            <button type="submit" name="forgot_password" class="btn btn-warning btn-lg w-100 mb-3 fw-bold text-dark">ตรวจสอบข้อมูล</button>
            
            <div class="text-center mt-3">
                <p class="mb-1 text-muted">นึกรหัสผ่านออกแล้ว? <a href="login.php" class="text-decoration-none fw-bold">เข้าสู่ระบบที่นี่</a></p>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>

</body>
</html>