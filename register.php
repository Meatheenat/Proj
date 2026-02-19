<?php 
session_start(); 
// ถ้า Login อยู่แล้ว ให้เด้งไปหน้าแรกเลย
if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - Library System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css">

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
    
    <style>
        /* --- นิยามตัวแปรสีให้เหมือนหน้า Login --- */
        [data-bs-theme="light"] {
            --bg-page: #f8f9fa;
            --bg-card: #ffffff;
            --text-color: #212529;
            --input-bg: #ffffff;
            --input-border: #dee2e6;
        }

        [data-bs-theme="dark"] {
            --bg-page: #121212;
            --bg-card: #1e1e1e;
            --text-color: #f8f9fa;
            --input-bg: #2b2b2b;
            --input-border: #444444;
        }

        body {
            background-color: var(--bg-page) !important;
            color: var(--text-color) !important;
            font-family: 'Sarabun', sans-serif;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .register-wrapper {
            min-height: calc(100vh - 56px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background-color: var(--bg-card) !important;
            color: var(--text-color) !important;
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
        }

        .form-control {
            background-color: var(--input-bg) !important;
            color: var(--text-color) !important;
            border-color: var(--input-border) !important;
        }

        .form-control::placeholder {
            color: #888;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-book-half me-2"></i>LibraryMobile
    </a>
    <div class="ms-auto d-flex align-items-center">
        <button class="btn btn-link text-white me-2 p-0" id="themeToggle" type="button" style="text-decoration: none;">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>
        <a href='login.php' class='btn btn-outline-light btn-sm fw-bold px-3'>เข้าสู่ระบบ</a>
    </div>
  </div>
</nav>

<div class="container register-wrapper">
    <div class="card shadow-lg" style="width: 100%; max-width: 550px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-person-plus-fill text-success" style="font-size: 3.5rem;"></i>
                <h3 class="fw-bold mt-2">สมัครสมาชิก</h3>
                <p class="opacity-75 small text-muted">กรอกข้อมูลให้ครบถ้วนเพื่อสร้างบัญชีห้องสมุดใหม่</p>
            </div>
            
            <form action="auth_action.php" method="POST" id="registerForm">
                <div class="mb-3">
                    <label for="fullname" class="form-label fw-bold small">ชื่อ-นามสกุล (Full Name)</label>
                    <input type="text" name="fullname" id="fullname" class="form-control form-control-lg" placeholder="ชื่อ นามสกุล" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold small">อีเมล (Email)</label>
                    <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="example@email.com" required>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label fw-bold small">ชื่อผู้ใช้งาน (Username)</label>
                    <input type="text" name="username" id="username" class="form-control form-control-lg" placeholder="ตั้งชื่อผู้ใช้งาน" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label fw-bold small">รหัสผ่าน</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="รหัสผ่าน" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="confirm_password" class="form-label fw-bold small">ยืนยันรหัสผ่าน</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control form-control-lg" placeholder="ยืนยันรหัส" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="register" class="btn btn-success btn-lg w-100 mb-3 fw-bold py-2 shadow-sm">ยืนยันการสมัครสมาชิก</button>
                
                <div class="text-center mt-3 small">
                    <p class="mb-0 opacity-75">มีบัญชีอยู่แล้วใช่หรือไม่? <a href="login.php" class="text-decoration-none fw-bold text-primary">เข้าสู่ระบบที่นี่</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. ระบบสลับธีม
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;

    function updateIcon(theme) {
        if (theme === 'dark') {
            themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            themeIcon.style.color = '#ffc107'; 
        } else {
            themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
            themeIcon.style.color = '#ffffff';
        }
    }

    updateIcon(htmlElement.getAttribute('data-bs-theme'));

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }

    // 2. ระบบเปิด-ปิดลูกตา (คุมทั้ง 2 ช่อง)
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const inputField = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (inputField.type === 'password') {
                inputField.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                inputField.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    });

    // 3. ตรวจสอบรหัสผ่านตรงกัน
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const pwd = document.getElementById('password').value;
            const cpwd = document.getElementById('confirm_password').value;
            if(pwd !== cpwd) {
                alert('รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน กรุณาตรวจสอบอีกครั้ง!');
                e.preventDefault();
            }
        });
    }
});
</script>

</body>
</html>