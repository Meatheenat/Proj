<?php
session_start();
// ถ้ามีการล็อกอินค้างไว้แล้ว ให้เด้งไปหน้า index.php ทันที
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Library System</title>
    
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
        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .login-wrapper {
            min-height: calc(100vh - 60px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        /* บังคับให้ Card เปลี่ยนสีตามธีมแม้ CSS หลักจะหาไม่เจอ */
        [data-bs-theme="dark"] body { background-color: #121212; color: #eee; }
        [data-bs-theme="dark"] .card { background-color: #1e1e1e; color: #eee; border-color: #333; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-book-half me-2"></i>LibraryMobile</a>
    <div class="ms-auto d-flex align-items-center">
        <button class="btn btn-link text-white me-2 p-0" id="themeToggle" type="button" style="text-decoration: none;">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>
        <a href='login.php' class='btn btn-outline-light btn-sm fw-bold px-3'>เข้าสู่ระบบ</a>
    </div>
  </div>
</nav>

<div class="login-wrapper">
    <div class="card shadow-lg border-0" style="width: 100%; max-width: 400px; border-radius: 20px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-person-circle text-primary" style="font-size: 3rem;"></i>
                <h3 class="fw-bold mt-2">เข้าสู่ระบบ</h3>
                <p class="text-muted small">กรุณากรอกข้อมูลเพื่อเข้าใช้งานระบบคลังพัสดุ</p>
            </div>

            <?php if(isset($_GET['error'])) { ?>
                <div class="alert alert-danger py-2 small text-center rounded-3">
                    <i class="bi bi-exclamation-triangle me-1"></i> ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
                </div>
            <?php } ?>

            <form action="auth_action.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" class="form-control form-control-lg" placeholder="Username" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold small">รหัสผ่าน (Password)</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="กรอกรหัสผ่าน" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 mb-3 fw-bold py-2 shadow-sm">เข้าสู่ระบบ</button>
                <div class="text-center small">
                    <a href="register.php" class="text-decoration-none fw-bold">สมัครสมาชิก</a>
                    <span class="text-muted mx-2">|</span>
                    <a href="forgot_password.php" class="text-decoration-none text-muted">ลืมรหัสผ่าน?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;

    // ฟังก์ชันจัดการสีไอคอน
    function updateIcon(theme) {
        if (theme === 'dark') {
            themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            themeIcon.style.color = '#ffc107'; 
        } else {
            themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
            themeIcon.style.color = '#ffffff';
        }
    }

    // เรียกใช้ตอนโหลดหน้า
    updateIcon(htmlElement.getAttribute('data-bs-theme'));

    // ทำงานเมื่อกดสลับธีม
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
            console.log("Theme switched to: " + newTheme);
        });
    }

    // ระบบเปิด-ปิดลูกตาดูรหัสผ่าน
    const toggleBtn = document.querySelector('.toggle-password');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    }
});
</script>

</body>
</html>