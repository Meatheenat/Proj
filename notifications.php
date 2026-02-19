<?php
session_start();
// เรียกใช้ไฟล์เชื่อมต่อจากโฟลเดอร์ config ตามรูปของเพื่อน
include('config/db.php'); 

if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// ดึงรายการที่เลยกำหนด (due_date < วันนี้)
$sql = "SELECT br.*, b.book_name 
        FROM borrow_records br
        JOIN books b ON br.book_id = b.book_id
        WHERE br.user_id = '$user_id' 
        AND br.status = 'pending' 
        AND br.due_date < '$today'";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แจ้งเตือน - Library System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css"> 
</head>
<body>
    <div class="container mt-5">
        <h3 class="fw-bold mb-4 text-danger">รายการแจ้งเตือน</h3>
        <?php if(mysqli_num_rows($result) > 0) { ?>
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <div class="alert alert-danger shadow-sm mb-3">
                    <strong>เกินกำหนดคืน:</strong> <?php echo htmlspecialchars($row['book_name']); ?><br>
                    <small>ต้องคืนตั้งแต่วันที่: <?php echo date('d/m/Y', strtotime($row['due_date'])); ?></small>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p class="text-muted text-center">ไม่มีแจ้งเตือนในขณะนี้</p>
        <?php } ?>
        <a href="index.php" class="btn btn-primary">กลับหน้าหลัก</a>
    </div>
    <script src="assets/script.js"></script>
</body>
</html>