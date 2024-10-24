<?php
// Bắt đầu session
session_start();
if (!$_SESSION['username']) {
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
    die("Connection failed: " . $conn->connect_error);
}

// Lấy danh sách tác giả
$authorsQuery = "SELECT AuthorID, Name FROM Authors";
$authorsResult = $conn->query($authorsQuery);

// Lấy danh sách nhà cung cấp
$suppliersQuery = "SELECT SupplierID, Name FROM Suppliers";
$suppliersResult = $conn->query($suppliersQuery);

// Lấy danh sách thể loại
$gendersQuery = "SELECT GenreID, GenreName FROM Genre";
$gendersResult = $conn->query($gendersQuery);

// Lấy giá trị ENUM cho Status
$statusQuery = "SHOW COLUMNS FROM Books LIKE 'Status'";
$statusResult = $conn->query($statusQuery);
$statusOptions = [];

if ($statusResult->num_rows > 0) {
    $row = $statusResult->fetch_assoc();
    $enum = $row['Type'];

    // Lấy các giá trị ENUM
    preg_match_all("/'([^']+)'/", $enum, $matches);
    $statusOptions = $matches[1];
}

// Xử lý thêm sách **chỉ khi có yêu cầu POST**
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $language = isset($_POST['language']) ? trim($_POST['language']) : '';
    $publisher = isset($_POST['publisher']) ? trim($_POST['publisher']) : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $isbn = isset($_POST['isbn']) ? trim($_POST['isbn']) : '';
    $authorId = isset($_POST['authorId']) ? (int)$_POST['authorId'] : 0;
    $bookID = isset($_POST['BookID']) ? trim($_POST['BookID']) : '';
    $genreId = isset($_POST['genreId']) ? (int)$_POST['genreId'] : 0;
    $supplierID = isset($_POST['SupplierID']) ? (int)$_POST['SupplierID'] : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    // Kiểm tra các trường thông tin hợp lệ
    if (!empty($title) && !empty($language) && !empty($publisher) && $quantity > 0 && !empty($isbn) && $authorId > 0 && $genreId > 0 && $supplierID > 0 && in_array($status, $statusOptions)) {
        
        // Kiểm tra trùng mã sách
        $checkQuery = "SELECT * FROM Books WHERE BookID = '$bookID'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('Mã sách đã tồn tại. Vui lòng nhập mã sách khác.');</script>";
        } else {
            // Truy vấn thêm sách
            $insertQuery = "INSERT INTO Books (BookID, Title, Language, Publisher, Quantity, ISBN, AuthorID, GenreID, SupplierID, Status) 
                            VALUES ('$bookID', '$title', '$language', '$publisher', '$quantity', '$isbn', '$authorId', '$genreId', '$supplierID', '$status')";

            if ($conn->query($insertQuery) === TRUE) {
                echo "<script>alert('Thêm sách thành công!'); window.location.href='books.php';</script>";
            } else {
                echo "Lỗi thêm sách: " . $conn->error;
            }
        }
    } else {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin và trạng thái hợp lệ.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sách Mới</title>
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

        .menu a:hover,
        .menu a.active {
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

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button,
        .back-button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            text-align: center;
            margin-right: 10px;
        }

        button:hover,
        .back-button:hover {
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
        <a href="../User/users.php">Người Dùng</a>
        <a href="../Loan/loans.php">Mượn/Trả Sách</a>
        <a href="../Book/inventory.php">Kho</a>
        <a href="../Report/reports.php">Báo Cáo</a>
        <a href="../logout.php">Đăng Xuất</a>
    </div>

    <div class="content">
        <h2>Thêm Sách Mới</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="BookID">Mã Sách:</label>
                <input type="text" id="BookID" name="BookID" required>
            </div>
            <div class="form-group">
                <label for="title">Tên Sách:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="language">Ngôn Ngữ:</label>
                <input type="text" id="language" name="language" required>
            </div>
            <div class="form-group">
                <label for="publisher">Nhà Xuất Bản:</label>
                <input type="text" id="publisher" name="publisher" required>
            </div>
            <div class="form-group">
                <label for="quantity">Số Lượng:</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="isbn">ISBN:</label>
                <input type="text" id="isbn" name="isbn" required>
            </div>
            <div class="form-group">
                <label for="authorId">Tác Giả:</label>
                <select id="authorId" name="authorId" required>
                    <?php while ($author = $authorsResult->fetch_assoc()) : ?>
                        <option value="<?php echo $author['AuthorID']; ?>"><?php echo htmlspecialchars($author['Name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="genreId">Thể Loại:</label>
                <select id="genreId" name="genreId" required>
                    <?php while ($gender = $gendersResult->fetch_assoc()) : ?>
                        <option value="<?php echo $gender['GenreID']; ?>"><?php echo htmlspecialchars($gender['GenreName']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="SupplierID">Nhà Cung Cấp:</label>
                <select id="SupplierID" name="SupplierID" required>

                    <?php while ($supplier = $suppliersResult->fetch_assoc()) : ?>

<option value="<?php echo $supplier['SupplierID']; ?>"><?php echo htmlspecialchars($supplier['Name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Trạng Thái:</label>
                <select id="status" name="status" required>
                    <?php foreach ($statusOptions as $statusOption) : ?>
                        <option value="<?php echo htmlspecialchars($statusOption); ?>"><?php echo htmlspecialchars($statusOption); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; align-items: center;">
                <button type="submit">Thêm</button>
                <a href="books.php" class="back-button">Quay lại</a> <!-- Nút quay lại -->
            </div>
        </form>
    </div>
</body>
</html>