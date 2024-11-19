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

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

mysqli_set_charset($conn, 'utf8');

// Lọc theo tháng năm, BookID và InventoryID
$month_year = isset($_POST['month_year']) ? $_POST['month_year'] : '';
$book_id = isset($_POST['book_id']) ? $_POST['book_id'] : '';
$inventory_id = isset($_POST['inventory_id']) ? $_POST['inventory_id'] : '';

// Query để lấy dữ liệu Inventory với bộ lọc
$inventoryQuery = "SELECT * FROM Inventory WHERE 1=1";

if (!empty($month_year)) {
    $inventoryQuery .= " AND DATE_FORMAT(LastUpdated, '%Y-%m') = '$month_year'";
}
if (!empty($book_id)) {
    $inventoryQuery .= " AND BookID = '$book_id'";
}
if (!empty($inventory_id)) {
    $inventoryQuery .= " AND InventoryID = '$inventory_id'";
}

$inventoryResult = $conn->query($inventoryQuery);

// Query để lấy giao dịch liên quan đến inventory
$transactionQuery = "SELECT * FROM Inventory_Transactions WHERE 1=1";
if (!empty($book_id)) {
    $transactionQuery .= " AND BookID = '$book_id'";
}
$transactionResult = $conn->query($transactionQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
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
            margin: 20px;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form label {
            margin-right: 10px;
        }

        form input[type="month"], form input[type="text"] {
            padding: 5px;
            margin: 5px;
            font-size: 14px;
            width: 150px;
        }

        form button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        form button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td button {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }

        td button:hover {
            background-color: #d32f2f;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
        <a class="active" href="../Book/inventory.php">Kho</a>
        <a href="../Report/reports.php">Báo Cáo</a>
        <a href="../logout.php">Đăng Xuất</a>
    </div>

    <form action="inventory.php" method="POST">
        <label for="month_year">Tháng/Năm:</label>
        <input type="month" name="month_year" value="<?php echo htmlspecialchars($month_year); ?>">
        
        <label for="book_id">Book ID:</label>
        <input type="text" name="book_id" value="<?php echo htmlspecialchars($book_id); ?>" placeholder="Book ID">
        
        <label for="inventory_id">Inventory ID:</label>
        <input type="text" name="inventory_id" value="<?php echo htmlspecialchars($inventory_id); ?>" placeholder="Inventory ID">
        
        <button type="submit">Lọc</button>
    </form>

    <div style="display: flex; align-items: center;">
        <h2>Danh Sách Sách trong Kho</h2>
        <button style="height: 40px; margin-left: 20px; padding: 10px;" onclick="openModal()">Transactions</button>
    </div>
    <table>
        <thead>
            <tr>
                <th>Inventory ID</th>
                <th>Book ID</th>
<th>Reorder Level</th>

                <th>Quantity</th>
 
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($inventoryResult->num_rows > 0) {
                while ($row = $inventoryResult->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['InventoryID']}</td>
                            <td>{$row['BookID']}</td>
<td>{$row['ReorderLevel']}</td>
                            <td>{$row['QuantityInStock']}</td>
                            <td>{$row['LastUpdated']}</td>
                            <td>
                                <button onclick=\"editInventory({$row['InventoryID']})\">Sửa</button>
                                <button onclick=\"deleteInventory({$row['InventoryID']})\">Xóa</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Không có dữ liệu</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="inventoryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Inventory Transactions</h2>
            <table>
                <thead>
                    <tr>
                        <th>TransactionID</th>
                        <th>BookID</th>
                        <th>Quantity</th>
                        <th>TransactionType</th>
                        <th>TransactionDate</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $transactionResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['TransactionID']}</td>
                                <td>{$row['BookID']}</td>
                                <td>{$row['Quantity']}</td>
                                <td>{$row['TransactionType']}</td>
                                <td>{$row['TransactionDate']}</td>
                                <td>
                                    <button onclick=\"showAddTransactionForm({$row['TransactionID']})\">Thêm</button>
                                    <button onclick=\"editTransaction({$row['TransactionID']})\">Sửa</button>
                                    <button onclick=\"deleteTransaction({$row['TransactionID']})\">Xóa</button>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>


    <script>
        function openModal() {
         document.getElementById("inventoryModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("inventoryModal").style.display = "none";
        }

        // Hiển thị form thêm giao dịch
        function showAddTransactionForm() {
            window.location.href = "../Transactions/add_transaction.php";
        }

        // Hàm sửa giao dịch
        function editTransaction(transactionId) {
            window.location.href = "../Transactions/edit_transaction.php?id=" + transactionId;
        }

        // Hàm xóa giao dịch
        function deleteTransaction(transactionId) {
            if (confirm("Bạn có chắc muốn xóa giao dịch này?")) {
                window.location.href = "../Transactions/delete_transaction.php?id=" + transactionId;
            }
        }

        // Hàm sửa kho sách (cập nhật số lượng)
        function editInventory(inventoryId) {
            window.location.href = "../Book/edit_inventory.php?id=" + inventoryId;
        }

        function deleteInventory(inventoryId) {
            if (confirm("Bạn có chắc muốn xóa sách này khỏi kho?")) {
                window.location.href = "../Book/delete_inventory.php?id=" + inventoryId;
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>