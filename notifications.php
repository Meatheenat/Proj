<?php
session_start();
// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูลตามโครงสร้างของเพื่อน
include('config/db.php'); 

if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// ดึงรายการที่เลยกำหนด (due_date < วันนี้)
$sql = "SELECT br.*, b.book_name 
        FROM borrow_records br
        JOIN books b ON br.book_id = b.book_id
        WHERE br.user_id = '$user_id' 
        AND br.status = 'pending' 
        AND br.due_date < '$today'
        ORDER BY br.due_date ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งเตือน - Library System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css"> 

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <style>
        body {
            background-color: var(--bg-page, #f8f9fa) !important;
            color: var(--text-color, #212529) !important;
            font-family: 'Sarabun', sans-serif;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        /* ปรับแต่งตัวแปรสีสำหรับหน้านี้โดยเฉพาะ */
        [data-bs-theme="dark"] {
            --bg-page: #121212;
            --bg-card: #1e1e1e;
            --text-color: #f8f9fa;
        }

        .noti-header {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(231, 74, 59, 0.3);
        }

        [data-bs-theme="dark"] .noti-header {
            background: linear-gradient(135deg, #1e1e1e 0%, #333 100%);
            border: 1px solid #444;
            box-shadow: none;
        }

        .noti-card {
            background-color: var(--bg-card, #ffffff) !important;
            border: none !important;
            border-radius: 15px;
            transition: transform 0.2s;
            border-left: 5px solid #e74a3b !important;
        }

        .noti-card:hover {
            transform: scale(1.02);
        }

        .empty-state {
            padding: 5rem 0;
            opacity: 0.6;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-book-half me-2 text-primary"></i>LibraryMobile
    </a>
    <div class="ms-auto d-flex align-items-center">
        <button class="btn btn-link text-white me-3 p-0" id="themeToggle" type="button" style="text-decoration: none;">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>
        <a href="index.php" class="btn btn-outline-light btn-sm fw-bold rounded-pill px-3">
            <i class="bi bi-house-door me-1"></i> กลับหน้าหลัก
        </a>
    </div>
  </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="noti-header text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-2"><i class="bi bi-bell-fill me-2"></i>ศูนย์การแจ้งเตือน</h2>
                <p class="mb-0 opacity-75">ตรวจสอบรายการหนังสือที่เลยกำหนดส่งคืน เพื่อหลีกเลี่ยงค่าปรับ</p>
            </div>
            <div class="col-md-4 text-center text-md-end d-none d-md-block">
                <i class="bi bi-exclamation-octagon" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <?php if(mysqli_num_rows($result) > 0) { ?>
                <h5 class="fw-bold mb-4 opacity-75">หนังสือที่ค้างส่ง (<?php echo mysqli_num_rows($result); ?> รายการ)</h5>
                
                <?php while($row = mysqli_fetch_assoc($result)) { 
                    // คำนวณจำนวนวันที่เลยกำหนด
                    $date1 = new DateTime($row['due_date']);
                    $date2 = new DateTime($today);
                    $diff = $date1->diff($date2);
                    $late_days = $diff->days;
                ?>
                    <div class="card noti-card shadow-sm mb-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-bold text-danger mb-1">
                                        <?php echo htmlspecialchars($row['book_name']); ?>
                                    </h5>
                                    <p class="mb-1 opacity-75">
                                        <i class="bi bi-calendar-x me-1"></i> กำหนดคืน: 
                                        <strong><?php echo date('d/m/Y', strtotime($row['due_date'])); ?></strong>
                                    </p>
                                    <span class="badge bg-danger rounded-pill">
                                        เลยกำหนดมาแล้ว <?php echo $late_days; ?> วัน
                                    </span>
                                </div>
                                <div class="text-end">
                                    <a href="return_book.php" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                        คืนตอนนี้
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                
            <?php } else { ?>
                <div class="empty-state text-center">
                    <i class="bi bi-check2-circle text-success" style="font-size: 5rem;"></i>
                    <h3 class="fw-bold mt-3">ทุกอย่างเรียบร้อยดี!</h3>
                    <p class="text-muted">คุณไม่มีหนังสือที่ค้างส่งเกินกำหนดในขณะนี้</p>
                    <a href="books.php" class="btn btn-outline-primary fw-bold mt-2 rounded-pill px-4">ค้นหาหนังสือใหม่</a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<footer class="text-center py-4 mt-auto opacity-50">
    <p class="small">LibraryMobile Notification System © 2026</p>
</footer>

[Image of a notification list UI on a mobile app with high contrast and clear alert badges]

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>

</body>
</html>