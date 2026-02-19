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

$user_id = $_SESSION['user_id'];

// ==========================================
// ส่วนที่ 1: Logic การกดคืนหนังสือ (Process Return)
// ==========================================
if(isset($_GET['action']) && $_GET['action'] == 'return' && isset($_GET['borrow_id'])) {
    $borrow_id = mysqli_real_escape_string($conn, $_GET['borrow_id']);
    $book_id = mysqli_real_escape_string($conn, $_GET['book_id']);
    $return_date = date('Y-m-d');

    // 1. อัปเดตตารางบันทึกการยืม
    $update_record = "UPDATE borrow_records SET 
                      return_date = '$return_date', 
                      status = 'returned' 
                      WHERE borrow_id = '$borrow_id' AND user_id = '$user_id'";
    
    if(mysqli_query($conn, $update_record)) {
        // 2. อัปเดตสถานะหนังสือให้กลับมาว่าง
        $update_book = "UPDATE books SET status = 'available' WHERE book_id = '$book_id'";
        mysqli_query($conn, $update_book);

        echo "<script>alert('คืนหนังสือเรียบร้อยแล้ว!'); window.location='return_book.php';</script>";
        exit();
    }
}

// ==========================================
// ส่วนที่ 2: ดึงรายการหนังสือที่ User คนนี้ยังไม่ได้คืน
// ==========================================
$sql = "SELECT br.*, b.book_name, b.author 
        FROM borrow_records br
        JOIN books b ON br.book_id = b.book_id
        WHERE br.user_id = '$user_id' AND br.status = 'pending'
        ORDER BY br.borrow_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คืนหนังสือ - ระบบยืมคืนหนังสือ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-book-half me-2"></i>LibraryMobile</a>
    <div class="ms-auto">
        <a href="index.php" class="btn btn-outline-light btn-sm">กลับหน้าหลัก</a>
    </div>
  </div>
  <button class="btn btn-link text-white me-2" id="themeToggle" type="button">
    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
</button>
</nav>

<div class="container mt-4 mb-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">คืนหนังสือ</h2>
        <p class="text-muted">รายการหนังสือที่คุณกำลังยืมอยู่ในขณะนี้</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-journal-check fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($row['book_name']); ?></h6>
                                <small class="text-muted">ยืมเมื่อ: <?php echo date('d/m/Y', strtotime($row['borrow_date'])); ?></small>
                            </div>
                            <div class="ms-auto">
                                <a href="return_book.php?action=return&borrow_id=<?php echo $row['borrow_id']; ?>&book_id=<?php echo $row['book_id']; ?>" 
                                   class="btn btn-success fw-bold" 
                                   onclick="return confirm('ยืนยันการคืนหนังสือเล่มนี้ใช่หรือไม่?')">
                                   คืนหนังสือ
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 15px;">
                    <i class="bi bi-emoji-smile text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">คุณไม่มีหนังสือค้างส่งในขณะนี้</h5>
                    <a href="books.php" class="btn btn-primary mt-3">ไปยืมหนังสือเพิ่ม</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>