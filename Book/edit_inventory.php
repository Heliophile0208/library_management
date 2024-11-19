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
$message = '';
mysqli_set_charset($conn, 'utf8');

// Lấy ID kho sách từ URL
$inventoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($inventoryId == 0) {
    echo "Invalid inventory ID.";
    exit();
}

// Lấy dữ liệu kho sách từ cơ sở dữ liệu
$sql = "SELECT * FROM Inventory WHERE InventoryID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $inventoryId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Inventory not found.";
    exit();
}

$row = $result->fetch_assoc();

// Lưu giá trị cũ để hiển thị trên form
$bookId = $row['BookID'];
$quantityInStock = $row['QuantityInStock'];
$reorderLevel = $row['ReorderLevel'];
$lastUpdated = $row['LastUpdated'];

// Xử lý form khi người dùng gửi dữ liệu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'];
    $quantityInStock = $_POST['quantity_in_stock'];
    $reorderLevel = $_POST['reorder_level'];

    // Lấy ngày cập nhật nếu không có
    $lastUpdated = date("Y-m-d H:i:s");

    // Cập nhật thông tin kho sách trong cơ sở dữ liệu
    $updateSql = "UPDATE Inventory 
                  SET BookID = ?, QuantityInStock = ?, ReorderLevel = ?, LastUpdated = ?
                  WHERE InventoryID = ?";

    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("iiisi", $bookId, $quantityInStock, $reorderLevel, $lastUpdated, $inventoryId);

    if ($stmt->execute()) {
        $message = "Kho sách đã được cập nhật thành công.";
    } else {
        $message = "Lỗi khi cập nhật kho sách: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Inventory</title>
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
        .menu a:hover, a.active {
            background-color: #45a049;
            font-weight: bold;
        }
form {
    margin: 40px auto;
    padding: 30px;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    width: 500px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

form label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
}

form input {
    padding: 10px;
    margin-bottom: 20px;
    font-size: 14px;
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

form input:disabled {
    background-color: #f2f2f2;
}

form button {
    padding: 12px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    width: 100%;
    transition: background-color 0.3s;
}

form button:hover {
    background-color: #45a049;
}

form button:active {
    background-color: #388e3c;
}

input[type="text"], input[type="number"] {
    padding: 10px;
    margin: 8px 0;
    font-size: 14px;
    width: 100%;
    border-radius: 5px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

input[type="number"]:focus, input[type="text"]:focus {
    border-color: #4CAF50;
}

input[type="text"]:disabled {
    background-color: #f0f0f0;
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

    <h2 style="text-align: center; margin-top: 20px;">Chỉnh Sửa Kho Sách</h2>
<div style="text-align: center; margin-top: 10px; color: green; font-weight: bold;">
    <?php if (!empty($message)) echo $message; ?>
</div>
    <form action="edit_inventory.php?id=<?php echo $inventoryId; ?>" method="POST">
        <label for="book_id">Book ID:</label>
        <input type="text" name="book_id" value="<?php echo $bookId; ?>" required>

        <label for="quantity_in_stock">Quantity In Stock:</label>
        <input type="number" name="quantity_in_stock" value="<?php echo $quantityInStock; ?>" required>

        <label for="reorder_level">Reorder Level:</label>
        <input type="number" name="reorder_level" value="<?php echo $reorderLevel; ?>" required>

        <label for="last_updated">Last Updated:</label>
        <input type="text" name="last_updated" value="<?php echo $lastUpdated; ?>" disabled>

        <button type="submit">Cập Nhật Kho</button>
    </form>
</body>
</html>

<?php $conn->close(); ?>