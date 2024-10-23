<?php
session_start();
if (!$_SESSION['username']) {
    header("Location: index.php");
    exit();
}

// Kết nối đến cơ sở dữ liệu
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "library_management"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Truy vấn tổng số sách trong kho
$sql_kho = "SELECT SUM(quantityinStock) AS total_inventory_books FROM Inventory";
$kq_kho = $conn->query($sql_kho);
$dong_kho = $kq_kho->fetch_assoc();
$tongsach_kho = $dong_kho['total_inventory_books'];

// Truy vấn số lượng sách đang được mượn
$sql_muon = "SELECT COUNT(*) AS borrowed_books FROM Loans WHERE returndate IS NULL";
$kq_muon = $conn->query($sql_muon);
$dong_muon = $kq_muon->fetch_assoc();
$sachmuon = $dong_muon['borrowed_books'];

// Tính tổng số lượng sách
$tongsach = $tongsach_kho + $sachmuon;

// Truy vấn số lượng người dùng
$nguoidung = "SELECT COUNT(*) AS total_users FROM Users";
$kq_nguoidung = $conn->query($nguoidung);
$dong_nguoidung = $kq_nguoidung->fetch_assoc();
$tong_nguoidung = $dong_nguoidung['total_users'];

// Truy vấn sách quá hạn
$sql_sachquahan = "SELECT COUNT(*) AS overdue_books FROM Loans WHERE returndate IS NULL AND duedate < NOW()";
$kq_sachquahan = $conn->query($sql_sachquahan);
$sodong = $kq_sachquahan->fetch_assoc();
$sachquahan = $sodong['overdue_books'];

$conn->close();

// Trả về dữ liệu dưới dạng JSON
echo json_encode([
    'total_books' => $tongsach,
    'total_users' => $tong_nguoidung,
    'borrowed_books' => $sachmuon,
    'overdue_books' => $sachquahan
]);
?>
