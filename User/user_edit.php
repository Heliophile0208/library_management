<?php
session_start(); 
if(!$_SESSION['username']){
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
    die("Kết nối thất bại: " . $conn->connect_error);
}

mysqli_set_charset($conn, 'utf8');

$user = null;
// Lấy danh sách người dùng
$usersQuery = "SELECT * FROM Users"; 

// Lấy giá trị ENUM cho User
$UserQuery = "SHOW COLUMNS FROM Users LIKE 'Role'";
$UserResult = $conn->query($UserQuery);
$UserOptions = [];

if ($UserResult->num_rows > 0) {
    $row = $UserResult->fetch_assoc();
    $enum = $row['Type'];

    // Lấy các giá trị ENUM
    preg_match_all("/'([^']+)'/", $enum, $matches);
    $UserOptions = $matches[1]; // Chứa các giá trị ENUM
}

// Kiểm tra nếu có UserID 
if (isset($_GET['UserID'])) {
    $UserID = $conn->real_escape_string($_GET['UserID']);
    $userQuery = "SELECT * FROM Users WHERE UserID = '$UserID'";
    $userResult = $conn->query($userQuery);
    
    // Kiểm tra 
    if ($userResult && $userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
    } else {
        echo "Người dùng không tồn tại.";
        exit;
    }
}

// cập nhật người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $Username = $conn->real_escape_string($_POST['Username']);
    $Password = $conn->real_escape_string($_POST['Password']);
    $Fullname = isset($_POST['Fullname']) && $_POST['Fullname'] !== '' ? $conn->real_escape_string($_POST['Fullname']) : NULL;
    $Role = $conn->real_escape_string($_POST['Role']);

    //  cập nhật Fullname là NULL nếu trống
    $updateQuery = "UPDATE Users SET Username = '$Username', Password = '$Password', 
                    Fullname = " . ($Fullname ? "'$Fullname'" : "NULL") . ", Role = '$Role' WHERE UserID = '$UserID'";
    
    if ($conn->query($updateQuery) === TRUE) {
        echo "<script>alert('Cập nhật user thành công!'); window.location.href='users.php';</script>";
    } else {
        echo "Lỗi cập nhật user: " . $conn->error;
    }
}

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Người Dùng</title>
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
        Library Management Dashboard
    </div>

    <div class="menu">
        <a href="../dashboard.php">Bảng Điều Khiển</a>
        <a href="../Book/books.php">Sách</a>
        <a class="active" href="../User/users.php">Người Dùng</a>
        <a href="../Loan/loans.php">Mượn/Trả Sách</a>
        <a href="../Book/inventory.php">Kho</a>
        <a href="../Report/reports.php">Báo Cáo</a>
        <a href="../logout.php">Đăng Xuất</a>
    </div>

    <div class="content">
        <h2>Sửa Người Dùng</h2>

        <?php if ($user): ?>
            <form method="POST" action="">

                <div class="form-group">
                    <label for="Username">User Name:</label>
                    <input type="text" id="Username" name="Username" value="<?php echo htmlspecialchars($user['Username'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="Password">Password:</label>
                    <input type="text" id="Password" name="Password" value="<?php echo htmlspecialchars($user['Password'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="Fullname">Full Name:</label>
                    <input type="text" id="Fullname" name="Fullname" value="<?php echo htmlspecialchars($user['Fullname'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="Role">Role:</label>
                    <select id="Role" name="Role" required>
                        <?php foreach ($UserOptions as $UserOption): ?>
                            <option value="<?php echo $UserOption; ?>" <?php echo $user['Role'] == $UserOption ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($UserOption); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: flex; align-items: center;"> <!-- Đặt form và nút trong một hàng -->
                    <button type="submit">Cập nhật</button>
                    <a href="users.php" class="back-button">Quay lại</a> <!-- Nút quay lại -->
                </div>
            </form>
        <?php else: ?>
            <p>Không tìm thấy thông tin người dùng.</p>
        
        <?php endif; ?>
    </div>
</body>
</html>