<?php 
// 1. เปิดโหมดดู Error เพื่อความชัวร์ (สไตล์ IT Support)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
include('config/db.php'); // เชื่อมต่อฐานข้อมูล

// 2. ระบบนับแจ้งเตือนหนังสือเกินกำหนด (ดึงข้อมูล Due Date)
$today = date('Y-m-d');
$user_id = $_SESSION['user_id'];
$sql_noti = "SELECT COUNT(*) as total FROM borrow_records 
             WHERE user_id = '$user_id' AND status = 'pending' AND due_date < '$today'";
$res_noti = mysqli_query($conn, $sql_noti);
$noti_count = ($res_noti) ? mysqli_fetch_assoc($res_noti)['total'] : 0;

// 3. Logic ค้นหาหนังสือ (รวมคอลัมน์ book_image ที่เพิ่มใหม่)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

$sql = "SELECT * FROM books WHERE 1=1";
if($search != ''){
    $sql .= " AND (book_name LIKE '%$search%' OR author LIKE '%$search%' OR category LIKE '%$search%')";
}
if($category != ''){ $sql .= " AND category = '$category'"; }
if($status != ''){ $sql .= " AND status = '$status'"; }
$sql .= " ORDER BY book_id DESC";
$result = mysqli_query($conn, $sql);

$cat_sql = "SELECT DISTINCT category FROM books WHERE category != ''";
$cat_result = mysqli_query($cat_sql);
?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการหนังสือ - Library System</title>
    
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
        /* --- นิยามตัวแปรสีให้ดำสนิททั้งหน้า --- */
        [data-bs-theme="light"] {
            --bg-page: #f8f9fa;
            --bg-card: #ffffff;
            --text-color: #212529;
        }
        [data-bs-theme="dark"] {
            --bg-page: #121212;
            --bg-card: #1e1e1e;
            --text-color: #f8f9fa;
        }

        body { 
            background-color: var(--bg-page) !important;
            color: var(--text-color) !important;
            font-family: 'Sarabun', sans-serif; 
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .navbar { 
            background-color: #212529 !important; 
            padding-top: 15px !important;
            padding-bottom: 15px !important;
        }

        .search-container {
            background-color: var(--bg-card) !important;
            color: var(--text-color) !important;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .book-card {
            background-color: var(--bg-card) !important;
            border: none !important;
            border-radius: 15px;
            transition: transform 0.3s ease;
            overflow: hidden; /* บังคับให้รูปไม่ล้นขอบการ์ด */
        }
        .book-card:hover {
            transform: translateY(-5px);
        }

        /* ส่วนควบคุมการแสดงผลรูปหน้าปก */
        .book-cover-container {
            height: 200px;
            overflow: hidden;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        [data-bs-theme="dark"] .book-cover-container {
            background: #2a2a2a;
        }

        .book-cover-img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* ทำให้รูปเต็มกรอบสวยๆ */
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

        <div class="dropdown">
            <button class="btn btn-outline-light btn-sm dropdown-toggle fw-bold px-3 rounded-pill" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['fullname']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li><a class="dropdown-item" href="history.php">ประวัติของฉัน</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">ออกจากระบบ</a></li>
            </ul>
        </div>
    </div>
  </div>
</nav>

<div class="container">
    
    <div class="search-container">
        <h5 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>ค้นหาหนังสือที่คุณต้องการ</h5>
        <form action="books.php" method="GET">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="ชื่อหนังสือ หรือ ผู้แต่ง..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">ทุกหมวดหมู่</option>
                        <?php while($cat = mysqli_fetch_assoc($cat_result)) { ?>
                            <option value="<?= $cat['category'] ?>" <?= ($category == $cat['category']) ? 'selected' : '' ?>><?= $cat['category'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">ค้นหา</button>
                </div>
            </div>
        </form>
    </div>

    <div class="row g-4 mb-5">
        <?php if(mysqli_num_rows($result) > 0) { 
            while($book = mysqli_fetch_assoc($result)) { 
                $is_avail = ($book['status'] == 'available');
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card book-card h-100 shadow-sm border-0">
                <div class="book-cover-container">
                    <?php if(!empty($book['book_image']) && file_exists("assets/img/covers/" . $book['book_image'])): ?>
                        <img src="assets/img/covers/<?php echo $book['book_image']; ?>" 
                             class="book-cover-img" alt="หน้าปก">
                    <?php else: ?>
                        <div class="text-center">
                            <i class="bi bi-book fs-1 text-muted"></i>
                            <div class="small text-muted opacity-50 mt-1">ไม่มีรูปหน้าปก</div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-body d-flex flex-column p-3">
                    <span class="badge <?= $is_avail ? 'bg-success' : 'bg-danger' ?> mb-2 align-self-start">
                        <?= $is_avail ? 'ว่าง' : 'ถูกยืม' ?>
                    </span>
                    <h6 class="card-title fw-bold text-truncate" title="<?= $book['book_name'] ?>">
                        <?= htmlspecialchars($book['book_name']) ?>
                    </h6>
                    <p class="card-text opacity-75 small mb-3"><?= htmlspecialchars($book['author']) ?></p>
                    <div class="mt-auto">
                        <?php if($is_avail) { ?>
                            <a href="borrow.php?id=<?= $book['book_id'] ?>" class="btn btn-primary btn-sm w-100 fw-bold rounded-pill shadow-sm">ยืมเล่มนี้</a>
                        <?php } else { ?>
                            <button class="btn btn-secondary btn-sm w-100 fw-bold rounded-pill opacity-50" disabled>ไม่ว่าง</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } } else { ?>
            <div class="col-12 text-center py-5 opacity-50">
                <i class="bi bi-search display-1 mb-3"></i>
                <h5>ไม่พบหนังสือที่ค้นหา</h5>
            </div>
        <?php } ?>
    </div>
</div>

<footer class="text-center py-5 opacity-50"><small>LibraryMobile System © 2026</small></footer>

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