<?php
session_start();
include('config/db.php');

$book_id = $_GET['id'];
$sql = "SELECT * FROM books WHERE book_id = '$book_id'";
$res = mysqli_query($conn, $sql);
$book = mysqli_fetch_assoc($res);
?>
<form action="edit_book_action.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
    <input type="text" name="book_name" value="<?= htmlspecialchars($book['book_name']) ?>" class="form-control" required>
    <button type="submit" class="btn btn-warning w-100">บันทึกการแก้ไข</button>
</form>