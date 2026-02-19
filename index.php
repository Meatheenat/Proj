<?php 
session_start(); 

// สำคัญ: สำหรับหน้า index ต้องเช็คว่า "ถ้ายังไม่ได้ Login" ให้ไล่กลับไปหน้า login.php
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรก - ระบบยืมคืนหนังสือ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        /* สไตล์ Card กลางจอ อิงจาก login-card ของคุณ */
        .dashboard-wrapper {
            min-height: calc(100vh - 56px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .dashboard-card {
            width: 100%;
            max-width: 600px; /* ให้กว้างกว่าหน้า Login นิดหน่อย */
            padding: 3rem 2rem;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">LibraryMobile</a>
    <div class="ms-auto text-white d-flex align-items-center">
        <span class="me-3 d-none d-sm-inline">สวัสดี, <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
        <a href='logout.php' class='btn btn-outline-danger btn-sm fw-bold'>ออกจากระบบ</a>
    </div>
  </div>
</nav>

<div class="container dashboard-wrapper">
    <div class="dashboard-card text-center">
        <h1 class="fw-bold text-primary mb-3">ยินดีต้อนรับสู่ระบบห้องสมุด</h1>
        <p class="text-muted mb-5">คุณสามารถค้นหาหนังสือและทำรายการยืม-คืนได้ที่นี่</p>
        
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <a href="books.php" class="btn btn-primary btn-lg w-100 py-3 fw-bold">
                    📚 รายการหนังสือ
                </a>
            </div>
            <div class="col-12 col-md-6">
                <a href="history.php" class="btn btn-outline-secondary btn-lg w-100 py-3 fw-bold">
                    🕒 ประวัติการยืม-คืน
                </a>
            </div>
            
            <?php 
            // พิเศษ: ถ้าเป็น Admin ให้เห็นเมนูจัดการระบบด้วย
            if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { 
            ?>
            <div class="col-12 mt-3">
                <hr>
                <a href="admin_dashboard.php" class="btn btn-dark w-100 py-2 fw-bold">
                    ⚙️ จัดการระบบ (Admin)
                </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/script.js"></script>

</body>
</html>