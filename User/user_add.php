<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "library_management"; 

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy giá trị ENUM cho Role
$RoleQuery = "SHOW COLUMNS FROM Users LIKE 'Role'";
$RoleResult = $conn->query($RoleQuery);
$RoleOptions = [];
if ($RoleResult->num_rows > 0) {
    $row = $RoleResult->fetch_assoc();
    $enum = $row['Type'];

    // Lấy các giá trị ENUM
    preg_match_all("/'([^']+)'/", $enum, $matches);
    $RoleOptions = $matches[1]; 
}

// Xử lý thêm người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    $newUserID = trim($_POST['userid'] ?? '');
    $newUsername = trim($_POST['username'] ?? '');
    $newPlainPassword = trim($_POST['password'] ?? '');
    $fullName = trim($_POST['fullname'] ?? '');
    $role = trim($_POST['role'] ?? '');

    // Mã hóa mật khẩu
    $hashedPassword = password_hash($newPlainPassword, PASSWORD_DEFAULT);

    // Câu lệnh SQL để thêm người dùng
    $sql = "INSERT INTO Users (UserID, Username, Password, FullName, Role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $newUserID, $newUsername, $hashedPassword, $fullName, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Người dùng đã được thêm thành công!');</script>";
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Thư Viện</title>
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        button, .back-button {
            padding: 10px 15px;
            background-color: #4CAF50; 
            color: white;
            border: none;
            cursor: pointer;
            text-align: center;
            margin-right: 10px; 
        }
        button:hover, .back-button:hover {
            background-color: #45a049; 
        }
        .back-button {
            display: inline-block;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        Bảng Điều Khiển Quản Lý Thư Viện
    </div>

    <div class="menu">
        <a href="../dashboard.php">Bảng Điều Khiển</a>
        <a href="../Book/books.php">Sách</a>
        <a href="../User/users.php">Người Dùng</a>
        <a href="../Loan/loans.php">Mượn/Trả Sách</a>
        <a href="../Book/inventory.php">Kho</a>
        <a href="../Report/reports.php">Báo Cáo</a>
        <a href="../logout.php">Đăng Xuất</a>
    </div>

    <div class="content">
        <h2>Thêm Người Dùng</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_user">
            <div class="form-group">
                <label for="userid">Mã Người Dùng:</label>
                <input type="text" id="userid" name="userid" required>
            </div>
            <div class="form-group">
                <label for="username">Tên Người Dùng:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật Khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="fullname">Họ Tên:</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div class="form-group">
                <label for="role">Vai Trò:</label>
                <select id="role" name="role" required>
                    <option value="">Chọn vai trò</option>
                    <?php foreach ($RoleOptions as $roleOption): ?>
                        <option value="<?php echo htmlspecialchars($roleOption); ?>"><?php echo htmlspecialchars($roleOption); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
           <div style="display: flex; align-items: center;">
                    <button type="submit">Thêm</button>
                    <a href="users.php" class="back-button">Quay lại</a> <!-- Nút quay lại -->
                </div>
        </form>
    </div>
</body>
</html>