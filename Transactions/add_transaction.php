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

// Biến thông báo
$message = '';

// Xử lý form khi người dùng gửi dữ liệu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'];
    $quantity = $_POST['quantity'];
    $transactionType = $_POST['transaction_type'];
    $transactionDate = !empty($_POST['transaction_date']) ? $_POST['transaction_date'] : date('Y-m-d'); // Gán ngày hiện tại nếu không nhập

    // Thêm giao dịch vào cơ sở dữ liệu
    $insertSql = "INSERT INTO Inventory_Transactions (BookID, Quantity, TransactionType, TransactionDate) 
                  VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("iiss", $bookId, $quantity, $transactionType, $transactionDate);

    if ($stmt->execute()) {
        $message = "Giao dịch mới đã được thêm thành công.";
    } else {
        $message = "Lỗi khi thêm giao dịch: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Transaction</title>
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
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 500px;
        }
        form label, form input {
            display: inline-block;
            margin-bottom: 10px;
            vertical-align: middle;
        }
        form label {
            width: 30%;
            text-align: left;
            padding-right: 10px;
        }
        form input {
            width: 60%;
            margin-left: 10px;
            padding: 10px;
        }
        form button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            width: 70%;
        }
        form button:hover {
            background-color: #45a049;
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

    <h2 style="text-align: center; margin-top: 20px;">Thêm Giao Dịch Mới</h2>
    <div style="text-align: center; margin-top: 10px; color: green; font-weight: bold;">
        <?php if (!empty($message)) echo $message; ?>
    </div>
    <form action="add_transaction.php" method="POST">
        <label for="book_id">Book ID:</label>
        <input type="text" name="book_id" required>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" required>

        <label for="transaction_type">Transaction Type:</label>
        <input type="text" name="transaction_type" required>

        <label for="transaction_date">Transaction Date:</label>
        <input type="date" name="transaction_date">

        <div style="text-align:center;">
            <button type="submit">Thêm Giao Dịch</button>
        </div>
    </form>
</body>
</html>

<?php $conn->close(); ?>