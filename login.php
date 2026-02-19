<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Library System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 400px; padding: 20px; border-radius: 15px; }
        .navbar { position: fixed; top: 0; width: 100%; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">LibraryMobile</a>
    <div class="ms-auto d-flex align-items-center">
        <button class="btn btn-link p-0 me-3" id="themeToggle" type="button">
            <i class="bi bi-moon-stars-fill text-white" id="themeIcon"></i>
        </button>
        <a href='login.php' class='btn btn-outline-light btn-sm'>เข้าสู่ระบบ</a>
    </div>
  </div>
</nav>

<div class="container d-flex justify-content-center">
    <div class="card login-card">
        <div class="card-body">
            <h3 class="text-center mb-4">เข้าสู่ระบบ</h3>
            <form action="auth_action.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">รหัสผ่าน (Password)</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="กรอกรหัสผ่าน" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 mb-3">Login</button>
                <div class="text-center">
                    <a href="register.php" class="text-decoration-none">สมัครสมาชิก</a> | 
                    <a href="forgot_password.php" class="text-decoration-none text-muted">ลืมรหัสผ่าน?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>

</body>
</html>