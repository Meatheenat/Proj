<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include('config/db.php'); 
date_default_timezone_set('Asia/Bangkok');

if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ไม่พบรหัสหนังสือที่ต้องการยืม'); window.location='books.php';</script>";
    exit();
}

$book_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

$sql_book = "SELECT * FROM books WHERE book_id = '$book_id'";
$result_book = mysqli_query($conn, $sql_book);

if(mysqli_num_rows($result_book) == 0) {
    echo "<script>alert('ไม่พบข้อมูลหนังสือในระบบ'); window.location='books.php';</script>";
    exit();
}

$book = mysqli_fetch_assoc($result_book);

if($book['status'] == 'borrowed') {
    echo "<script>alert('ขออภัย หนังสือเล่มนี้ถูกยืมไปแล้ว'); window.location='books.php';</script>";
    exit();
}

// ==========================================
// บันทึกการยืมเมื่อกดปุ่ม "ยืนยันการยืม"
// ==========================================
if(isset($_POST['confirm_borrow'])) {
    $borrow_date = date('Y-m-d');
    $duration = (int)$_POST['borrow_days']; 
    $due_date = date('Y-m-d', strtotime("+$duration days")); 
    
    $sql_insert = "INSERT INTO borrow_records (user_id, book_id, borrow_date, due_date, status) 
                   VALUES ('$user_id', '$book_id', '$borrow_date', '$due_date', 'pending')";
                   
    if(mysqli_query($conn, $sql_insert)) {
        mysqli_query($conn, "UPDATE books SET status = 'borrowed' WHERE book_id = '$book_id'");
        
        echo "<script>
                alert('ยืมสำเร็จ! กำหนดคืนวันที่: " . date('d/m/Y', strtotime($due_date)) . "'); 
                window.location='history.php';
              </script>";
        exit();
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันการยืม - Library System</title>
    
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
        /* --- นิยามตัวแปรสีสำหรับ Full Dark Mode --- */
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

        .borrow-wrapper { 
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
            overflow: hidden; 
        }

        .card-header-custom { 
            background: linear-gradient(135deg, #4e73df, #224abe); 
            color: white; 
            padding: 25px; 
            text-align: center; 
        }

        [data-bs-theme="dark"] .card-header-custom {
            background: linear-gradient(135deg, #1e1e1e, #333);
            border-bottom: 1px solid #444;
        }

        .form-select {
            background-color: var(--input-bg) !important;
            color: var(--text-color) !important;
            border-color: var(--input-border) !important;
        }

        .list-group-item {
            background-color: transparent !important;
            color: var(--text-color) !important;
            border-color: var(--input-border) !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="books.php">
        <i class="bi bi-arrow-left me-2"></i>กลับหน้ารายการ
    </a>
    
    <div class="ms-auto d-flex align-items-center">
        <button class="btn btn-link text-white me-3 p-0" id="themeToggle" type="button" style="text-decoration: none;">
            <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>
        <span class="text-white fw-bold d-none d-sm-inline">
            สวัสดี, <?php echo htmlspecialchars($_SESSION['fullname']); ?>
        </span>
    </div>
  </div>
</nav>

<div class="container borrow-wrapper">
    <div class="card" style="width: 100%; max-width: 500px;">
        <div class="card-header-custom">
            <i class="bi bi-journal-check mb-2" style="font-size: 3.5rem;"></i> 
            <h4 class="fw-bold mb-0">ยืนยันการทำรายการ</h4>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <h5 class="fw-bold mb-4 text-center opacity-75">รายละเอียดการยืมหนังสือ</h5>
            
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item px-0 py-3">
                    <div class="fw-bold text-muted small mb-1">ชื่อหนังสือ</div>
                    <span class="text-primary fw-bold fs-5"><?php echo htmlspecialchars($book['book_name']); ?></span>
                </li>
                <li class="list-group-item px-0 py-3">
                    <div class="fw-bold text-muted small mb-1">ชื่อผู้ยืม</div>
                    <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
                </li>
                <li class="list-group-item px-0 py-3 border-0">
                    <div class="fw-bold text-muted small mb-1">วันที่ทำรายการ</div>
                    <span class="text-success fw-bold"><?php echo date('d/m/Y'); ?></span> 
                </li>
            </ul>

            <form action="borrow.php?id=<?php echo $book['book_id']; ?>" method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold small opacity-75">
                        <i class="bi bi-calendar-event me-1"></i> เลือกจำนวนวันที่ต้องการยืม
                    </label>
                    <select name="borrow_days" class="form-select form-select-lg fw-bold" required>
                        <?php 
                        $durations = explode(',', $book['borrow_duration'] ?: '7,10,15,30'); 
                        foreach($durations as $day) {
                            $day = trim($day);
                            echo "<option value='$day'> ยืมได้ $day วัน </option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <a href="books.php" class="btn btn-outline-secondary btn-lg w-50 fw-bold rounded-pill">ยกเลิก</a>
                    <button type="submit" name="confirm_borrow" class="btn btn-primary btn-lg w-50 fw-bold shadow-sm rounded-pill">ยืนยันการยืม</button>
                </div>
            </form>
        </div>
    </div>
</div>

[Image of theme switcher logic flowchart showing checking local storage, applying data-bs-theme, and toggling icons]

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