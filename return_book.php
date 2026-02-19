<?php 
// 1. เปิดโหมดดู Error เพื่อการตรวจสอบ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include('config/db.php'); // เชื่อมต่อฐานข้อมูลตามโครงสร้างไฟล์
date_default_timezone_set('Asia/Bangkok');

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// 2. ระบบนับแจ้งเตือนหนังสือเกินกำหนด (สำหรับ Navbar)
$sql_noti = "SELECT COUNT(*) as total FROM borrow_records 
             WHERE user_id = '$user_id' AND status = 'pending' AND due_date < '$today'";
$res_noti = mysqli_query($conn, $sql_noti);
$noti_count = ($res_noti) ? mysqli_fetch_assoc($res_noti)['total'] : 0;

// ==========================================
// ส่วนที่ 1: Logic การคืนหนังสือ
// ==========================================
if(isset($_GET['action']) && $_GET['action'] == 'return' && isset($_GET['borrow_id'])) {
    $borrow_id = mysqli_real_escape_string($conn, $_GET['borrow_id']);
    $book_id = mysqli_real_escape_string($conn, $_GET['book_id']);
    $return_date = date('Y-m-d');

    // อัปเดตสถานะเป็นคืนแล้ว
    $update_record = "UPDATE borrow_records SET return_date = '$return_date', status = 'returned' 
                      WHERE borrow_id = '$borrow_id' AND user_id = '$user_id'";
    
    if(mysqli_query($conn, $update_record)) {
        mysqli_query($conn, "UPDATE books SET status = 'available' WHERE book_id = '$book_id'");
        echo "<script>alert('คืนหนังสือเรียบร้อยแล้ว!'); window.location='return_book.php';</script>";
        exit();
    }
}

// ==========================================
// ส่วนที่ 2: ดึงรายการหนังสือที่ยังไม่คืน
// ==========================================
$sql = "SELECT br.*, b.book_name, b.author 
        FROM borrow_records br
        JOIN books b ON br.book_id = b.book_id
        WHERE br.user_id = '$user_id' AND br.status = 'pending'
        ORDER BY br.due_date ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คืนหนังสือ - Library System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css"> 

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <style>
        /* --- นิยามตัวแปรสี Full Dark Mode --- */
        [data-bs-theme="light"] {
            --bg-page: #f8f9fa;
            --bg-card: #ffffff;
            --text-color: #212529;
            --border-color: #dee2e6;
        }
        [data-bs-theme="dark"] {
            --bg-page: #121212;
            --bg-card: #1e1e1e;
            --text-color: #f8f9fa;
            --border-color: #333333;
        }

        body { 
            background-color: var(--bg-page) !important;
            color: var(--text-color) !important;
            font-family: 'Sarabun', sans-serif; 
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .navbar { background-color: #212529 !important; }

        .return-card {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .return-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .status-icon {
            width: 50px; height: 50px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-book-half me-2 text-primary"></i>LibraryMobile
    </a>
    
    <div class="ms-auto d-flex align-items-center">
        <a href="notifications.php" class="btn btn-link text-white position-relative me-3 p-0">
            <i class="bi bi-bell-fill fs-5"></i>
            <?php if($noti_count > 0) { ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                    <?php echo $noti_count; ?>
                </span>
            <?php } ?>
        </a>

        <button class="btn btn-link text-white me-3 p-0" id="themeToggle" type="button">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>

        <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold">กลับหน้าหลัก</a>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">คืนหนังสือ</h2>
        <p class="opacity-75">รายการหนังสือที่คุณกำลังยืมอยู่ในขณะนี้</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    $is_overdue = ($row['due_date'] < $today);
                ?>
                    <div class="card return-card mb-3 shadow-sm border-0">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="status-icon <?= $is_overdue ? 'bg-danger text-white' : 'bg-primary text-white' ?> flex-shrink-0">
                                <i class="bi <?= $is_overdue ? 'bi-exclamation-triangle' : 'bi-journal-check' ?> fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-4">
                                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($row['book_name']); ?></h5>
                                <div class="small opacity-75">
                                    <span class="me-3"><i class="bi bi-calendar-event me-1"></i> ยืมเมื่อ: <?= date('d/m/Y', strtotime($row['borrow_date'])) ?></span>
                                    <span class="<?= $is_overdue ? 'text-danger fw-bold' : '' ?>">
                                        <i class="bi bi-clock-history me-1"></i> กำหนดคืน: <?= date('d/m/Y', strtotime($row['due_date'])) ?>
                                        <?= $is_overdue ? '(เกินกำหนด)' : '' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="ms-auto">
                                <a href="return_book.php?action=return&borrow_id=<?php echo $row['borrow_id']; ?>&book_id=<?php echo $row['book_id']; ?>" 
                                   class="btn <?= $is_overdue ? 'btn-danger' : 'btn-success' ?> fw-bold rounded-pill px-4 shadow-sm" 
                                   onclick="return confirm('ยืนยันการคืนหนังสือเล่มนี้?')">
                                   คืนเล่มนี้
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 opacity-50">
                    <i class="bi bi-check2-circle display-1 text-success mb-3"></i>
                    <h4>คุณไม่มีหนังสือค้างส่งในขณะนี้</h4>
                    <a href="books.php" class="btn btn-primary mt-3 rounded-pill px-4 fw-bold">ไปยืมหนังสือเพิ่ม</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="text-center py-4 mt-auto opacity-50"><p class="small">LibraryMobile System © 2026</p></footer>

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
            const newTheme = (currentTheme === 'light') ? 'dark' : 'light';
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    }
});
</script>
</body>
</html>