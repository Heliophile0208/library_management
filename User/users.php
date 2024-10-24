<?php
session_start();
if (!$_SESSION['username']) {
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
$search = '';

// Lấy danh sách người dùng
$usersQuery = "SELECT * FROM Users"; 

// Xử lý tìm kiếm người dùng
if (isset($_POST['submit_search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $usersQuery .= " WHERE Username LIKE '%$search%' OR FullName LIKE '%$search%' OR Role LIKE '%$search%'";
}
$usersResult = $conn->query($usersQuery);

// Kiểm tra kết quả truy vấn
if ($usersResult === FALSE) {
    echo "Lỗi truy vấn: " . $conn->error . "<br>";
}

// Xử lý xóa người dùng
if (isset($_POST['delete'])) {
    if (isset($_POST['UserID']) && !empty($_POST['UserID'])) {
        $UserIDToDelete = $_POST['UserID'];
        $deleteQuery = "DELETE FROM Users WHERE UserID = ?";
        $stmt = $conn->prepare($deleteQuery);
        
        if ($stmt) {
            $stmt->bind_param("i", $UserIDToDelete);
            if ($stmt->execute()) {
                echo "<script>alert('Xóa user thành công!'); window.location.href = 'users.php';</script>";
                exit; // Dừng script sau khi chuyển hướng
            } else {
                echo "<script>alert('Lỗi khi xóa user: " . $conn->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Lỗi chuẩn bị truy vấn xóa user.');</script>";
        }
    } else {
        echo "<script>alert('Bạn chưa chọn user để xóa.');</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
        h2 {
            padding: 20px;
            margin-bottom: -15px;
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
        input[type="text"], button {
            padding: 10px;
            margin: 10px;
        }
        button[type="submit"] {
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="navbar">
        Library Management Dashboard
    </div>
  <div class="menu">
    <a href="../dashboard.php">Bảng Điều Khiển</a>
    <a  href="../Book/books.php">Sách</a>
    <a class="active" href="../User/users.php">Người Dùng</a>
    <a href="../Loan/loans.php">Mượn/Trả Sách</a>
    <a href="../Book/inventory.php">Kho</a>
    <a href="../Report/reports.php">Báo Cáo</a>
    <a href="../logout.php">Đăng Xuất</a>
</div>
    <h2>Quản lý Người dùng</h2>

    <!-- Form Tìm kiếm User -->
    <form method="POST" action="users.php" style="display: inline;">
        <input type="text" name="search" placeholder="Tìm kiếm user..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" name="submit_search">Tìm kiếm</button>
    </form>

    <!-- Form Thêm User -->
    <form method="POST" action="user_add.php" style="display: inline;">
        <button type="submit">Thêm User</button>
    </form>

    <form method="post" action="users.php" style="display: inline;">
        <table>
            <thead>
                <tr>
                    <th>Chọn</th>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Password</th>
                    <th>Full Name</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($usersResult) && $usersResult->num_rows > 0) {
                    while ($row = $usersResult->fetch_assoc()) {
                        echo "<tr>
                            <td><input type='radio' name='UserID' value='" . htmlspecialchars($row['UserID']) . "' required></td>
                            <td>" . htmlspecialchars($row['UserID'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['Username'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['Password'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['FullName'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['Role'] ?? '') . "</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Không Có User nào.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Nút sửa user -->
        <button type="button" class="editButton" onclick="setEditUser();">Sửa User</button>
        <button type="submit" name="delete" onclick="return confirmDelete();">Xóa User</button>
    </form>

    <script>
        function setEditUser() {
            const selectedRadio = document.querySelector('input[name="UserID"]:checked');
            if (selectedRadio) {
                const UserID = selectedRadio.value;
                window.location.href = `user_edit.php?UserID=${UserID}`; 
            } else {
                alert("Bạn chưa chọn user để sửa.");
            }
        }

        function confirmDelete() {
            const selectedRadio = document.querySelector('input[name="UserID"]:checked');
            if (!selectedRadio) {
                alert("Bạn chưa chọn user để xóa.");
                return false; // Ngăn không cho form gửi đi
            }
            return confirm('Bạn có chắc chắn muốn xóa user này?');
        }
    </script>
</body>
</html>