<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_management";

// Kết nối tới cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

mysqli_set_charset($conn, 'utf8');

// Lấy ID kho sách từ URL
$inventoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($inventoryId == 0) {
    echo "Invalid inventory ID.";
    exit();
}

// Kiểm tra nếu kho sách tồn tại
$sql = "SELECT * FROM Inventory WHERE InventoryID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $inventoryId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Kho sách không tồn tại.";
    exit();
}

// Xử lý khi người dùng xác nhận xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deleteSql = "DELETE FROM Inventory WHERE InventoryID = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $inventoryId);

    if ($deleteStmt->execute()) {
        header("Location: ../Book/inventory.php?message=InventoryDeleted"); // Chuyển hướng sau khi xóa thành công
        exit();
    } else {
        echo "Lỗi khi xóa kho sách: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Kho</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 16px;
        }
        .menu {
            display: flex;
            justify-content: center;
            background-color: #4CAF50;
        }
        .menu a {
            padding: 15px 20px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            transition: background-color 0.3s;
        }
        .menu a:hover, .menu a.active {
            background-color: #45a049;
        }
        .container {
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 500px;
            text-align: center;
        }
        .container h2 {
            margin-bottom: 20px;
        }
        .container button {
            padding: 10px 15px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }
        .container button:hover {
            background-color: #d32f2f;
        }
        .container a {
            text-decoration: none;
            color: #333;
            padding: 10px 15px;
            background-color: #ddd;
            border-radius: 5px;
        }
        .container a:hover {
            background-color: #ccc;
        }
    </style>
</head>
<body>
    <div class="navbar">Library Management Dashboard</div>
    <div class="menu">
        <a href="../dashboard.php">Bảng Điều Khiển</a>
        <a href="../Book/books.php">Sách</a>
        <a href="../User/users.php">Người Dùng</a>
        <a href="../Loan/loans.php">Mượn/Trả Sách</a>
        <a href="../Book/inventory.php">Kho</a>
        <a href="../Report/reports.php">Báo Cáo</a>
        <a href="../logout.php">Đăng Xuất</a>
    </div>

    <div class="container">
        <h2>Xác Nhận Xóa Kho</h2>
        <p>Bạn có chắc chắn muốn xóa kho sách với ID <strong><?php echo $inventoryId; ?></strong>?</p>
        <form action="delete_inventory.php?id=<?php echo $inventoryId; ?>" method="POST">
            <button type="submit">Xóa</button>
            <a href="../Book/inventory.php">Hủy</a>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>