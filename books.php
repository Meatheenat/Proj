<?php 
// เปิดโหมดโชว์ Error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
include('config/db.php');

// ==========================================
// ระบบค้นหาและตัวกรอง (Search & Filter Logic)
// ==========================================
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// สร้างคำสั่ง SQL พื้นฐาน
$sql = "SELECT * FROM books WHERE 1=1";

// 1. ค้นหาแบบกว้าง (ชื่อ, ผู้แต่ง, หมวดหมู่, ปีพิมพ์, สำนักพิมพ์)
if($search != ''){
    $sql .= " AND (book_name LIKE '%$search%' 
              OR author LIKE '%$search%' 
              OR category LIKE '%$search%'
              OR publisher LIKE '%$search%'
              OR publish_year LIKE '%$search%')";
}

// 2. กรองตามหมวดหมู่
if($category != ''){
    $sql .= " AND category = '$category'";
}

// 3. กรองตามสถานะ (ว่าง / ถูกยืม)
if($status != ''){
    $sql .= " AND status = '$status'";
}

$sql .= " ORDER BY book_id DESC"; // เรียงเล่มใหม่ล่าสุดขึ้นก่อน
$result = mysqli_query($conn, $sql);

// ดึงรายชื่อหมวดหมู่ทั้งหมดในระบบ เพื่อมาทำตัวกรอง (Dropdown) แบบไม่ซ้ำกัน
$cat_sql = "SELECT DISTINCT category FROM books WHERE category IS NOT NULL AND category != ''";
$cat_result = mysqli_query($conn, $cat_sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการหนังสือทั้งหมด - ระบบยืมคืนหนังสือ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .book-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            border: none;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .book-cover-placeholder {
            height: 180px;
            background: linear-gradient(135deg, #1cc88a, #13855c);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        /* แต่งฟอร์มค้นหาให้ดูพรีเมียม */
        .search-box {
            background-color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-top: -30px; /* ดึงให้ลอยทับ Navbar นิดๆ */
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark pb-5"> <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-arrow-left me-2"></i>กลับหน้าแรก</a>
    <div class="ms-auto text-white">
        <span class="d-none d-sm-inline">สวัสดี, <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
    </div>
  </div>
  <button class="btn btn-link text-white me-2" id="themeToggle" type="button">
    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
</button>
</nav>

<div class="container mb-5">
    
    <div class="search-box mb-5">
        <form action="books.php" method="GET">
            <div class="row g-2">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="ค้นหา ชื่อหนังสือ, ผู้แต่ง, สำนักพิมพ์, ปี..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                
                <div class="col-6 col-md-3">
                    <select name="category" class="form-select">
                        <option value="">-- ทุกหมวดหมู่ --</option>
                        <?php while($cat = mysqli_fetch_assoc($cat_result)) { ?>
                            <option value="<?php echo $cat['category']; ?>" <?php if($category == $cat['category']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($cat['category']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- ทุกสถานะ --</option>
                        <option value="available" <?php if($status == 'available') echo 'selected'; ?>>ว่าง (ยืมได้)</option>
                        <option value="borrowed" <?php if($status == 'borrowed') echo 'selected'; ?>>ถูกยืมแล้ว</option>
                    </select>
                </div>
                
                <div class="col-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">ค้นหา</button>
                    <?php if($search != '' || $category != '' || $status != '') { ?>
                        <a href="books.php" class="btn btn-outline-danger" title="ล้างการค้นหา"><i class="bi bi-x-lg"></i></a>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">ผลการค้นหา</h4>
        <span class="text-muted">พบหนังสือ <?php echo mysqli_num_rows($result); ?> เล่ม</span>
    </div>

    <div class="row g-4">
        <?php 
        if(mysqli_num_rows($result) > 0) {
            while($book = mysqli_fetch_assoc($result)) {
                $status_text = ($book['status'] == 'available') ? 'ว่าง (ยืมได้)' : 'ถูกยืมแล้ว';
                $status_color = ($book['status'] == 'available') ? 'bg-success' : 'bg-danger';
        ?>
        
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card book-card h-100 shadow-sm border-0">
                <div class="book-cover-placeholder">
                    <i class="bi bi-book"></i>
                </div>
                <div class="card-body d-flex flex-column">
                    <span class="badge <?php echo $status_color; ?> mb-2 align-self-start"><?php echo $status_text; ?></span>
                    <h6 class="card-title fw-bold text-truncate" title="<?php echo htmlspecialchars($book['book_name']); ?>">
                        <?php echo htmlspecialchars($book['book_name']); ?>
                    </h6>
                    <p class="card-text text-muted small mb-1"><i class="bi bi-person me-1"></i><?php echo htmlspecialchars($book['author']); ?></p>
                    
                    <p class="card-text text-muted small mb-3">
                        <i class="bi bi-tags me-1"></i><?php echo htmlspecialchars($book['category']); ?><br>
                        <?php if(!empty($book['publish_year'])) { echo "<i class='bi bi-calendar3 me-1'></i>ปี: " . htmlspecialchars($book['publish_year']) . "<br>"; } ?>
                        <?php if(!empty($book['publisher'])) { echo "<i class='bi bi-building me-1'></i>" . htmlspecialchars($book['publisher']); } ?>
                    </p>
                    
                    <div class="mt-auto">
                        <?php if($book['status'] == 'available') { ?>
                            <a href="borrow.php?id=<?php echo $book['book_id']; ?>" class="btn btn-outline-primary btn-sm w-100 fw-bold">ขอยืมเล่มนี้</a>
                        <?php } else { ?>
                            <button class="btn btn-secondary btn-sm w-100 fw-bold" disabled>ไม่ว่าง</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <?php 
            }
        } else {
            echo "<div class='col-12 text-center py-5'>
                    <i class='bi bi-search text-muted' style='font-size: 3rem;'></i>
                    <p class='text-muted mt-3 fs-5'>ไม่พบข้อมูลหนังสือที่คุณค้นหา</p>
                    <a href='books.php' class='btn btn-outline-primary'>ดูหนังสือทั้งหมด</a>
                  </div>";
        }
        ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>