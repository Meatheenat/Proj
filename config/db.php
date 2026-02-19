<?php
$host = "https://hosting.bncc.ac.th/";
$user = "s673190104";
$pass = "s673190104";
$dbname = "s673190104";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
?>