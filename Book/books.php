<?php
session_start();

if (!isset($_SESSION['username'])) {
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
$search = '';
$booksQuery = "SELECT * FROM Books"; 

// Xử lý tìm kiếm sách
if (isset($_POST['submit_search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $booksQuery .= " WHERE Title LIKE '%$search%' OR AuthorId LIKE '%$search%' OR ISBN LIKE '%$search%' OR Language LIKE '%$search%'";
}

// Thực thi truy vấn
$booksResult = $conn->query($booksQuery);

// Kiểm tra kết quả truy vấn
if ($booksResult === FALSE) {
    echo "Lỗi truy vấn: " . $conn->error . "<br>";
}

// Xử lý xóa sách
if (isset($_POST['delete'])) {
    if (isset($_POST['BookID']) && !empty($_POST['BookID'])) {
        $bookIdToDelete = $_POST['BookID'];
        $deleteQuery = "DELETE FROM Books WHERE BookID = ?";
        $stmt = $conn->prepare($deleteQuery);
        
        if ($stmt) {
            $stmt->bind_param("i", $bookIdToDelete);
            if ($stmt->execute()) {
                echo "<script>alert('Xóa sách thành công!'); window.location.href = 'books.php';</script>";
                exit; 
            } else {
                echo "<script>alert('Lỗi khi xóa sách: " . $stmt->error . "');</script>"; // Sử dụng $stmt->error
            }
            $stmt->close();
        } else {
            echo "<script>alert('Lỗi chuẩn bị truy vấn xóa sách.');</script>";
        }
    } else {
        echo "<script>alert('Bạn chưa chọn sách để xóa.');</script>";
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
    <title>Quản lý Sách</title>
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
        button[type="submit"] {
            margin-right: 10px;
        }
        .form {
            display: flex;
            padding: 10px;
            margin: 10px;
        }
        input[type="text"], button, .editButton {
            padding: 10px;
            margin: 10px;
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

<!-- Thanh điều hướng -->
<div class="navbar">
    Library Management Dashboard
</div>


<div class="menu">
    <a href="../dashboard.php">Bảng Điều Khiển</a>
    <a class="active" href="../Book/books.php">Sách</a>
    <a href="../User/users.php">Người Dùng</a>
    <a href="../Loan/loans.php">Mượn/Trả Sách</a>
    <a href="../Book/inventory.php">Kho</a>
    <a href="../Report/reports.php">Báo Cáo</a>
    <a href="../logout.php">Đăng Xuất</a>
</div>

<h2>Quản lý Sách</h2>

<!-- Form Tìm kiếm Sách -->
<form method="POST" action="books.php" style="display: inline;">
    <input type="text" name="search" placeholder="Tìm kiếm sách..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit" name="submit_search">Tìm kiếm</button>
</form>

<!-- Form Thêm Sách -->
<form method="POST" action="book_add.php" style="display: inline;">
    <button type="submit">Thêm Sách</button>
</form>

<!-- Form Sửa và Xóa Sách -->
<form method="POST" action="books.php" style="display: inline;">
    <table>
        <thead>
            <tr>
                <th>Choose</th>
                <th>Book ID</th>
                <th>Name </th>
                <th>Language</th>
                <th>Publisher</th>
                <th>Quantity</th>
                <th>ISBN</th>
                <th>Status</th>
                <th>Author ID</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (isset($booksResult) && $booksResult->num_rows > 0) {
                while ($row = $booksResult->fetch_assoc()) {
                    echo "<tr>
                        <td><input type='radio' name='BookID' value='" . htmlspecialchars($row['BookID']) . "' required></td>
                        <td>" . htmlspecialchars($row['BookID']) . "</td>
                        <td>" . htmlspecialchars($row['Title']) . "</td>
                        <td>" . htmlspecialchars($row['Language']) . "</td>
                        <td>" . htmlspecialchars($row['Publisher']) . "</td>
                        <td>" . htmlspecialchars($row['Quantity']) . "</td>
                        <td>" . htmlspecialchars($row['ISBN']) . "</td>
                        <td>" . htmlspecialchars($row['Status']) . "</td>
                        <td>" . htmlspecialchars($row['AuthorID']) . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Không có sách nào.</td></tr>";
            } ?>
        </tbody>
    </table>

    <!-- Nút sửa sách -->
    <button type="button" class="editButton" onclick="setEditBookID();">Sửa Sách</button>
    
    <button type="submit" name="delete" onclick="return confirmDelete();">Xóa Sách</button>
</form>

<script>
    function setEditBookID() {
        const selectedRadio = document.querySelector('input[name="BookID"]:checked');
        if (selectedRadio) {
            const bookID = selectedRadio.value;
            window.location.href = `book_edit.php?BookID=${bookID}`; 
        } else {
            alert("Bạn chưa chọn sách để sửa.");
        }
    }

    function confirmDelete() {
        const selectedRadio = document.querySelector('input[name="BookID"]:checked');
        if (!selectedRadio) {
            alert("Bạn chưa chọn sách để xóa.");
            return false; // Ngăn không cho form gửi đi
        }
        return confirm('Bạn có chắc chắn muốn xóa sách này?');
    }
</script>

</body>
</html>