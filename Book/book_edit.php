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

// Khởi tạo biến để chứa thông tin sách
$book = null;

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
    $statusOptions = $matches[1]; // Chứa các giá trị ENUM
}

// Kiểm tra nếu có BookID được truyền qua URL
if (isset($_GET['BookID'])) {
    $bookID = $conn->real_escape_string($_GET['BookID']);
    
    // Truy vấn để lấy thông tin sách
    $bookQuery = "SELECT * FROM Books WHERE BookID = '$bookID'";
    $bookResult = $conn->query($bookQuery);
    
    // Kiểm tra kết quả truy vấn
    if ($bookResult && $bookResult->num_rows > 0) {
        $book = $bookResult->fetch_assoc();
    } else {
        echo "Sách không tồn tại.";
        exit; // Thoát nếu không tìm thấy sách
    }
}

// Xử lý cập nhật sách
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $title = $conn->real_escape_string($_POST['title']);
    $language = $conn->real_escape_string($_POST['language']);
    $publisher = $conn->real_escape_string($_POST['publisher']);
    $quantity = (int)$_POST['quantity'];
    $isbn = isset($_POST['isbn']) && $_POST['isbn'] !== '' ? $conn->real_escape_string($_POST['isbn']) : NULL; // Allow ISBN to be empty
    $authorId = (int)$_POST['authorId'];
    $genreId = (int)$_POST['genreId'];
    $supplierId = (int)$_POST['supplierId'];
    $status = $conn->real_escape_string($_POST['status']);

    // Truy vấn cập nhật
    $updateQuery = "UPDATE Books SET Title = '$title', Language = '$language', Publisher = '$publisher', 
                    Quantity = '$quantity', ISBN = " . ($isbn ? "'$isbn'" : "NULL") . ", AuthorID = '$authorId', 
                    GenreID = '$genreId', SupplierID = '$supplierId', Status = '$status' WHERE BookID = '$bookID'";
    
    if ($conn->query($updateQuery) === TRUE) {
        echo "<script>alert('Cập nhật sách thành công!'); window.location.href='books.php';</script>";
    } else {
        echo "Lỗi cập nhật sách: " . $conn->error;
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
    <title>Sửa Sách</title>
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
            background-color: #4CAF50; /* Màu nền giống nhau */
            color: white;
            border: none;
            cursor: pointer;
            text-align: center;
            margin-left:20px;
            margin-right:-10px;
        }
        button:hover, .back-button:hover {
            background-color: #45a049; /* Màu nền khi hover giống nhau */
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
        <h2>Sửa Sách</h2>

        <?php if ($book): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Tên Sách:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['Title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="language">Ngôn Ngữ:</label>
                    <input type="text" id="language" name="language" value="<?php echo htmlspecialchars($book['Language']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="publisher">Nhà Xuất Bản:</label>
                    <input type="text" id="publisher" name="publisher" value="<?php echo htmlspecialchars($book['Publisher']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Số Lượng:</label>
                    <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($book['Quantity']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="isbn">ISBN:</label>
                    <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($book['ISBN']); ?>"> <!-- Không cần required -->
                </div>

                <div class="form-group">
                    <label for="authorId">Tác Giả:</label>
                    <select id="authorId" name="authorId" required>
                        <?php 
                        // Reset result pointer and populate authors
                        $authorsResult->data_seek(0);
                        while ($author = $authorsResult->fetch_assoc()): ?>
                            <option value="<?php echo $author['AuthorID']; ?>" <?php echo $book['AuthorID'] == $author['AuthorID'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($author['Name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="genreId">Thể Loại:</label>
                    <select id="genreId" name="genreId" required>
                        <?php 
                        // Reset result pointer and populate genres
                        $gendersResult->data_seek(0);
                        while ($gender = $gendersResult->fetch_assoc()): ?>
                            <option value="<?php echo $gender['GenreID']; ?>" <?php echo $book['GenreID'] == $gender['GenreID'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($gender['GenreName']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="supplierId">Nhà Cung Cấp:</label>
                    <select id="supplierId" name="supplierId" required>
                        <?php 
                        $suppliersResult->data_seek(0);
                        while ($supplier = $suppliersResult->fetch_assoc()): ?>
                            <option value="<?php echo $supplier['SupplierID']; ?>" <?php echo $book['SupplierID'] == $supplier['SupplierID'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($supplier['Name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Trạng Thái:</label>
                    <select id="status" name="status" required>
                        <?php foreach ($statusOptions as $statusOption): ?>
                            <option value="<?php echo $statusOption; ?>" <?php echo $book['Status'] == $statusOption ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($statusOption); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                            </div>
           <div style="display: flex; align-items: center;">
                <button type="submit">Cật nhật</button>
                <a href="books.php" class="back-button">Quay lại</a> <!-- Nút quay lại -->
            </div>
            </form>
        <?php else: ?>
            <p>Không tìm thấy thông tin sách.</p>
        <?php endif; ?>
    </div>
</body>
</html>
                    