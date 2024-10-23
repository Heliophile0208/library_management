<?php
// Bắt đầu session
session_start();

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
$inventoryQuery = "SELECT SUM(quantityinStock) AS total_inventory_books FROM Inventory";
$inventoryResult = $conn->query($inventoryQuery);
$inventoryRow = $inventoryResult->fetch_assoc();
$totalInventoryBooks = $inventoryRow['total_inventory_books'];

// Truy vấn số lượng sách đang được mượn
$borrowedBooksQuery = "SELECT COUNT(*) AS borrowed_books FROM Loans WHERE returndate IS NULL";
$borrowedBooksResult = $conn->query($borrowedBooksQuery);
$borrowedBooksRow = $borrowedBooksResult->fetch_assoc();
$borrowedBooks = $borrowedBooksRow['borrowed_books'];

// Tính tổng số lượng sách
$totalBooks = $totalInventoryBooks + $borrowedBooks;

// Truy vấn số lượng người dùng
$usersQuery = "SELECT COUNT(*) AS total_users FROM Users";
$usersResult = $conn->query($usersQuery);
$usersRow = $usersResult->fetch_assoc();
$totalUsers = $usersRow['total_users'];

// Truy vấn sách quá hạn
$overdueBooksQuery = "SELECT COUNT(*) AS overdue_books FROM Loans WHERE ReturnDate IS NULL AND DueDate < NOW()";
$overdueBooksResult = $conn->query($overdueBooksQuery);
$overdueBooksRow = $overdueBooksResult->fetch_assoc();
$overdueBooks = $overdueBooksRow['overdue_books'];

// Truy vấn số sách bị hư hỏng
$damagedBooksQuery = "SELECT COUNT(*) AS damaged_books FROM BookDamages WHERE DamageType = 'Damaged'";
$damagedBooksResult = $conn->query($damagedBooksQuery);
$damagedBooksRow = $damagedBooksResult->fetch_assoc();
$damagedBooks = $damagedBooksRow['damaged_books'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            font-weight: bold; 
        }
        .content {
            padding: 20px;
        }
        .welcome-message {
            text-align: center;
            font-size: 24px;
            margin: 50px 0;
            color: blue;
        }
        .stats-grid {
            display: flex;
            gap: 20px;
            margin-bottom:20px;
        }
        .stat-card {
            flex: 1;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .stat-card h4 {
            margin-bottom: 10px;
            font-size: 18px;
        }
        .stat-card p {
            font-size: 24px;
            font-weight: bold;
        }
        .notification-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            margin-top:20px;
            text-align:center;
        }
        .stat-card a {
            color: black;
            text-decoration: none; 
        }
        .stat-card a:hover {
            text-decoration: underline; 
            color:red;
        }
    </style>
</head>
<body>
    <div class="navbar">
        Library Management Dashboard
    </div>

    <div class="menu">
        <a href="#" class="active" onclick="showSection('dashboard', this)">Bảng Điều Khiển</a>
   
        <a href="../Book/books.php">Sách</a>
        <a href="../User/users.php">Người Dùng</a>
        <a href="../Loan/loans.php">Mượn/Trả Sách</a>
        <a href="../Book/inventory.php">Kho</a>
        <a href="../Report/reports.php">Báo Cáo</a>
        <a href="../logout.php">Đăng Xuất</a>
    </div>

    <div class="content">
        <div id="dashboard">
            <h2>Tổng quan</h2>
            <div class="welcome-message">
                Chào Mừng Bạn Đến Với Quản Lý Thư Viện
            </div>

          <a href="books.php">
          <div class="stats-grid">
                <div class="stat-card">
                    <h4>Tổng số sách</h4>
                    <p><?php echo $totalBooks; ?></a></p>
                </div>
         <a href="users.php">
                <div class="stat-card">
                    <h4>Người dùng</h4>
                    <p><?php echo $totalUsers; ?></a></p>
                </div>
          <a href="loans.php">
                <div class="stat-card">
                    <h4>Sách đang mượn</h4>
                    <p><?php echo $borrowedBooks; ?></a></p>
                </div>
          <a href="loans.php?status=overdue">
                <div class="stat-card">
                    <h4>Sách quá hạn</h4>
                    <p><?php echo $overdueBooks; ?></a></p>
                </div>
            </div>

            <div class="notification-card">
                <h3>Thông báo</h3>
                <p>Có <strong><?php echo $overdueBooks; ?></strong> sách quá hạn cần được xử lý.</p>
                <p>Có <strong><?php echo $damagedBooks; ?></strong> sách bị hư hỏng.</p>
            </div>
        </div>
    </div>

    <script>
        function showSection(section, element) {
            // Ẩn tất cả các phần khác
            document.getElementById("dashboard").style.display = "none";

            // Hiện phần tương ứng
            if (section === 'dashboard') {
                document.getElementById("dashboard").style.display = "block";
            }
           
            let menuLinks = document.querySelectorAll('.menu a');
            menuLinks.forEach(link => {
                link.classList.remove('active');
            });

            // Thêm lớp 'active' cho liên kết được nhấn
            element.classList.add('active');
        }

        // Hiển thị mặc định phần dashboard
        showSection('dashboard', document.querySelector('.menu a.active'));
    </script>
</body>
</html>