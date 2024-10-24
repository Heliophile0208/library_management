<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <style>
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
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f4f9;
            margin: 0 auto;
            padding: 0px;
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
    <button onclick="openModal();">Xem Kho</button>

    <!-- Modal hiển thị dữ liệu Inventory -->
    <div id="inventoryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Inventory Transactions</h2>
            <table id="inventoryTable">
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
                        $result = $conn->query("SELECT * FROM Inventory_transaction");
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['TransactionID']}</td>
                                    <td>{$row['BookID']}</td>
                                    <td>{$row['Quantity']}</td>
                                    <td>{$row['TransactionType']}</td>
                                    <td>{$row['TransactionDate']}</td>
                                    <td>
                                        <button onclick=\"editTransaction({$row['TransactionID']})\">Sửa</button>
                                        <button onclick=\"deleteTransaction({$row['TransactionID']})\">Xóa</button>
                                    </td>
                                  </tr>";
                        }
                    ?>
                </tbody>
            </table>
            <button onclick="showAddForm()">Thêm</button>
        </div>
    </div>

    <script>
        // Hàm mở modal
        function openModal() {
            document.getElementById("inventoryModal").style.display = "block";
        }

        // Hàm đóng modal
        function closeModal() {
            document.getElementById("inventoryModal").style.display = "none";
        }

        // Hiển thị form thêm mới
        function showAddForm() {
            window.location.href = "add_transaction.php";
        }

        // Hàm sửa transaction
        function editTransaction(transactionId) {
            window.location.href = "edit_transaction.php?id=" + transactionId;
        }

        // Hàm xóa transaction
        function deleteTransaction(transactionId) {
            if (confirm("Bạn có chắc muốn xóa giao dịch này?")) {
                window.location.href = "delete_transaction.php?id=" + transactionId;
            }
        }
    </script>
</body>
</html>