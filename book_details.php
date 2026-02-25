<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start(); 

if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit(); }
include('config/db.php');

if(!isset($_GET['id'])) { header("Location: books.php"); exit(); }

$book_id = mysqli_real_escape_string($conn, $_GET['id']);

// ดึงข้อมูลหนังสือ
$sql = "SELECT * FROM books WHERE book_id = '$book_id'";
$result = mysqli_query($conn, $sql);
$book = mysqli_fetch_assoc($result);

if(!$book) { echo "ไม่พบข้อมูลหนังสือ"; exit(); }

$is_avail = ($book['status'] == 'available');
?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $book['book_name']; ?> - Library System</title>
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
        [data-bs-theme="light"] { --bg-page: #f8f9fa; --bg-card: #ffffff; --text-color: #212529; }
        [data-bs-theme="dark"] { --bg-page: #121212; --bg-card: #1e1e1e; --text-color: #f8f9fa; }
        body { background-color: var(--bg-page) !important; color: var(--text-color) !important; transition: all 0.3s ease; }
        .details-card { background-color: var(--bg-card) !important; border-radius: 20px; border: none; overflow: hidden; }
        .img-detail { width: 100%; max-height: 500px; object-fit: contain; background: #2a2a2a; padding: 20px; border-radius: 15px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-arrow-left me-2"></i>กลับหน้าหลัก</a>
    <button class="btn btn-link text-white ms-auto me-3 p-0" id="themeToggle" type="button"><i class="bi bi-moon-stars-fill" id="themeIcon"></i></button>
  </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="card details-card shadow-lg p-4 p-md-5">
        <div class="row g-5">
            <div class="col-md-5 text-center">
                <?php if(!empty($book['book_image']) && file_exists("assets/img/covers/" . $book['book_image'])): ?>
                    <img src="assets/img/covers/<?php echo $book['book_image']; ?>" class="img-detail shadow" alt="หน้าปก">
                <?php else: ?>
                    <div class="img-detail d-flex align-items-center justify-content-center">
                        <i class="bi bi-book display-1 opacity-25"></i>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-7">
                <span class="badge <?php echo $is_avail ? 'bg-success' : 'bg-danger'; ?> rounded-pill px-3 mb-3">
                    <?php echo $is_avail ? 'ว่าง (ยืมได้)' : 'ถูกยืมแล้ว'; ?>
                </span>
                <h1 class="fw-bold mb-2"><?php echo htmlspecialchars($book['book_name']); ?></h1>
                <p class="fs-5 opacity-75 mb-4">โดย <?php echo htmlspecialchars($book['author']); ?></p>
                
                <hr class="opacity-10 mb-4">
                
                <div class="row mb-4">
                    <div class="col-6 col-sm-4">
                        <small class="text-muted d-block">หมวดหมู่</small>
                        <span class="fw-bold"><?php echo htmlspecialchars($book['category']); ?></span>
                    </div>
                    <div class="col-6 col-sm-4">
                        <small class="text-muted d-block">ระยะเวลายืม</small>
                        <span class="fw-bold text-primary"><?php echo $book['borrow_duration']; ?> วัน</span>
                    </div>
                </div>

                <div class="mb-5">
                    <h5 class="fw-bold mb-3"><i class="bi bi-card-text me-2"></i>เรื่องย่อ / ตัวอย่างข้อมูล</h5>
                    <p class="opacity-75" style="line-height: 1.8;">
                        <?php echo !empty($book['description']) ? nl2br(htmlspecialchars($book['description'])) : "ไม่มีรายละเอียดเพิ่มเติมสำหรับหนังสือเล่มนี้"; ?>
                    </p>
                </div>

                <div class="d-grid gap-2 d-md-flex">
                    <?php if($is_avail): ?>
                        <a href="borrow.php?id=<?php echo $book['book_id']; ?>" class="btn btn-primary btn-lg px-5 fw-bold rounded-pill shadow">ยืมเดี๋ยวนี้</a>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg px-5 fw-bold rounded-pill" disabled>ขณะนี้ไม่ว่าง</button>
                    <?php endif; ?>
                    <a href="books.php" class="btn btn-outline-secondary btn-lg px-4 rounded-pill">กลับหน้ารายการ</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // สคริปต์สลับธีม (เหมือนหน้าอื่น)
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;

    function updateIcon(theme) {
        if (theme === 'dark') { themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill'); themeIcon.style.color = '#ffc107'; } 
        else { themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill'); themeIcon.style.color = '#ffffff'; }
    }
    updateIcon(htmlElement.getAttribute('data-bs-theme'));

    themeToggle.addEventListener('click', function() {
        const newTheme = htmlElement.getAttribute('data-bs-theme') === 'light' ? 'dark' : 'light';
        htmlElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateIcon(newTheme);
    });
</script>
</body>
</html>