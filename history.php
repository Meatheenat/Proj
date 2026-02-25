<?php 
// 1. เปิดโหมดดู Error (IT Support Style)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 
// ตรวจสอบการ Login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include('config/db.php');

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลประวัติการยืม-คืน
$sql = "SELECT br.*, b.book_id, b.book_name, b.author, b.category 
        FROM borrow_records br
        JOIN books b ON br.book_id = b.book_id
        WHERE br.user_id = '$user_id'
        ORDER BY br.borrow_id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการยืม-คืน - Library System</title>
    
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
        /* --- นิยามตัวแปรสีให้ดำสนิททั้งหน้าเหมือนหน้า Index --- */
        [data-bs-theme="light"] {
            --bg-page: #f8f9fa;
            --bg-card: #ffffff;
            --text-color: #212529;
            --table-border: #dee2e6;
        }

        [data-bs-theme="dark"] {
            --bg-page: #121212;
            --bg-card: #1e1e1e;
            --text-color: #f8f9fa;
            --table-border: #333333;
        }

        body { 
            background-color: var(--bg-page) !important;
            color: var(--text-color) !important;
            font-family: 'Sarabun', sans-serif;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .table-card {
            background-color: var(--bg-card) !important;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table {
            color: var(--text-color) !important;
            margin-bottom: 0;
        }

        .table thead th {
            background-color: rgba(0,0,0,0.05);
            border-bottom: 1px solid var(--table-border);
            padding: 1.2rem 1rem;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        [data-bs-theme="dark"] .table thead th {
            background-color: rgba(255,255,255,0.05);
        }

        .status-badge {
            font-weight: 600;
            padding: 0.6em 1.2em;
            border-radius: 30px;
            font-size: 0.75rem;
        }

        .btn-borrow-again {
            transition: all 0.3s ease;
            border-radius: 30px;
        }

        .btn-borrow-again:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-book-half me-2 text-primary"></i>LibraryMobile
    </a>
    
    <div class="ms-auto d-flex align-items-center">
        <button class="btn btn-link text-white me-3 p-0" id="themeToggle" type="button">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>

        <a href="index.php" class="btn btn-outline-light btn-sm fw-bold rounded-pill px-3">
            <i class="bi bi-house-door me-1"></i> กลับหน้าหลัก
        </a>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h2 class="fw-bold mb-2">ประวัติการยืม-คืนหนังสือ</h2>
            <p class="opacity-75">ตรวจสอบรายการหนังสือทั้งหมดที่คุณเคยทำรายการในระบบ</p>
        </div>
    </div>

    <div class="card table-card shadow-lg border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">ข้อมูลหนังสือ</th>
                            <th>วันที่ยืม</th>
                            <th>กำหนดคืน / วันที่คืน</th>
                            <th>สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                if($row['status'] == 'pending') {
                                    $status_label = '<span class="badge bg-warning text-dark status-badge shadow-sm"><i class="bi bi-clock-history me-1"></i>กำลังยืม</span>';
                                    $return_date = '<span class="opacity-50 small">- รอการส่งคืน -</span>';
                                    $action_btn = '<a href="return_book.php" class="btn btn-sm btn-primary fw-bold px-4 rounded-pill shadow-sm">ไปคืนหนังสือ</a>';
                                } else {
                                    $status_label = '<span class="badge bg-success status-badge shadow-sm text-white"><i class="bi bi-check-circle me-1"></i>คืนแล้ว</span>';
                                    $return_date = '<span class="fw-bold text-success">' . date('d/m/Y', strtotime($row['return_date'])) . '</span>';
                                    $action_btn = '<a href="borrow.php?id=' . $row['book_id'] . '" class="btn btn-sm btn-outline-primary fw-bold px-3 btn-borrow-again">
                                                    <i class="bi bi-arrow-repeat me-1"></i>ยืมอีกครั้ง</a>';
                                }
                        ?>
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="fw-bold fs-6 mb-1"><?php echo htmlspecialchars($row['book_name']); ?></div>
                                <div class="opacity-75 small"><i class="bi bi-person me-1"></i><?php echo htmlspecialchars($row['author']); ?></div>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($row['borrow_date'])); ?></td>
                            <td><?php echo $return_date; ?></td>
                            <td><?php echo $status_label; ?></td>
                            <td class="text-center"><?php echo $action_btn; ?></td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5'><i class='bi bi-inbox display-4 opacity-25'></i><p class='mt-3 opacity-50'>ยังไม่มีประวัติการทำรายการ</p></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4 text-center opacity-50 small">
        <i class="bi bi-info-circle me-1"></i> หากข้อมูลไม่ถูกต้อง โปรดติดต่อแผนก IT Support
    </div>
</div>

<footer class="text-center py-4 mt-auto opacity-50">
    <p class="small">LibraryMobile System © 2026</p>
</footer>



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