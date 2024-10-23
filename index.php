<?php
// Bắt đầu session
session_start();

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

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = trim($_POST['username']);
    $inputPassword = $_POST['password'];

    // Kiểm tra xem username và password có tồn tại trong bảng Users không
    $sql = "SELECT * FROM Users WHERE Username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Kiểm tra mật khẩu
        if (password_verify($inputPassword, $row['Password'])) {
            // Lưu thông tin người dùng trong session
            $_SESSION['username'] = $row['Username'];
            echo "<div style='color:green; font-size:20px; font-weight:bold; text-align:center;'>Đăng nhập thành công!</div>";
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Invalid username or password.";
        }
    } else {
        $message = "Invalid username or password.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('QLThuVien/PNG/backgroundindex.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center; 
            justify-content: center;
        }
        .container {
            width: 300px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
            text-align:center;
        }
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .input-group input {
            width: 100%; 
            padding: 10px 40px; 
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; 
        }
        .input-group img {
            position: absolute;
            left: 10px; 
            top: 10px;
            width: 20px; 
            height: 20px; 
        }
        button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($message): ?>
            <div class="error-message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="input-group">
                <img src="QLThuVien/PNG/user.png" alt="Username Icon">
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <img src="QLThuVien/PNG/password.png" alt="Password Icon">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>