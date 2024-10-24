<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Xử lý khi người dùng gửi biểu mẫu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ biểu mẫu
    $reader_id = $_POST['reader_id'];
    $name = $_POST['name'];
    $reader_type = $_POST['reader_type'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    // Kết nối đến cơ sở dữ liệu
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library_management";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    mysqli_set_charset($conn, 'utf8');

    // Thêm độc giả mới vào cơ sở dữ liệu
    $sql = "INSERT INTO Readers (ReaderID, Name, ReaderType, Phone, Email, Address, Status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $reader_id, $name, $reader_type, $phone, $email, $address, $status);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Thêm độc giả thành công!";
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra: " . $stmt->error;
    }

    // Đóng kết nối
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Độc Giả Mới</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="address"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px; /* Thêm khoảng cách giữa các nút */
        }
        button:hover {
            background-color: #45a049;
        }
        .back-button {
            background-color: #007BFF; /* Màu cho nút quay lại */
        }
        .back-button:hover {
            background-color: #0056b3; /* Màu khi di chuột qua nút quay lại */
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .success-message {
            color: green;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thêm Độc Giả Mới</h2>

        <!-- Hiển thị thông báo lỗi nếu có -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Hiển thị thông báo thành công nếu có -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="reader_id">Reader ID:</label>
                <input type="text" id="reader_id" name="reader_id" required />
            </div>
            <div class="form-group">
                <label for="name">Tên:</label>
                <input type="text" id="name" name="name" required />
            </div>
            <div class="form-group">
                <label for="reader_type">Loại Độc Giả:</label>
                <select id="reader_type" name="reader_type" required>
                    <option value="Regular">Thường</option>
                    <option value="Student">Sinh Viên</option>
                    <option value="Faculty">Giảng Viên</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone">Điện Thoại:</label>
                <input type="tel" id="phone" name="phone" required />
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required />
            </div>
            <div class="form-group">
                <label for="address">Địa Chỉ:</label>
                <input type="text" id="address" name="address" required />
            </div>
            <div class="form-group">
                <label for="status">Trạng Thái:</label>
                <select id="status" name="status" required>
                    <option value="Active">Hoạt Động</option>
                    <option value="Inactive">Ngừng Hoạt Động</option>
                </select>
            </div>
            <button type="submit">Thêm Độc Giả</button>
            <button type="button" class="back-button" onclick="window.location.href='../Loan/loans.php'">Quay Lại</button>
        </form>
    </div>
</body>
</html>