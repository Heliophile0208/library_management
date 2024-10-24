<?php
session_start();
if (!isset($_SESSION['username'])) {
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

//  loan_id và late_fee đã được truyền
if (!isset($_GET['loan_id']) || !isset($_GET['late_fee'])) {
    $_SESSION['error_message'] = "Không tìm thấy thông tin hóa đơn.";
    header("Location: loans.php"); // Quay về trang mượn sách
    exit();
}

// Lấy loan_id và late_fee từ URL
$loan_id = $_GET['loan_id'];
$late_fee = $_GET['late_fee'];

//  khoản mượn
$sql = "SELECT l.LoanID, l.ReaderID, l.BookID, l.DueDate, l.ReturnDate, b.Title, r.Name 
        FROM Loans l 
        JOIN Books b ON l.BookID = b.BookID 
        JOIN Readers r ON l.ReaderID = r.ReaderID 
        WHERE l.LoanID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error_message'] = "Không tìm thấy thông tin mượn sách.";
    header("Location: loans.php");
    exit();
}

$loan_info = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Trả Sách</title>
    <style>
        body {
            font-family: 'Arial', sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #f4f4f4;
        }
        .receipt {
            max-width: 600px; 
            margin: 20px auto; 
            padding: 20px; 
            border: 1px solid #007bff;
            border-radius: 10px; 
            background: #ffffff; 
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center; 
            color: #007bff;
            font-size: 28px; 
            margin-bottom: 10px;
        }
        .library-header {
            display: flex;  
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px;
        }
        .logo img {
            width: 80px; 
        }
        .library-info {
            text-align: right;
            font-size: 14px;
            color: #6c757d;
        }
        .library-info h2 {
            margin: 0;
            font-size: 20px;
            color: #343a40;
        }
        .details {
            margin: 20px 0; 
            padding: 15px;
            border-radius: 5px;
            background-color: #f8f9fa; 
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .details p {
            margin: 5px 0; 
            font-size: 16px;
            color: #495057;
        }
        .details strong {
            color: #007bff;
        }
        .footer {
            text-align: center; 
            margin-top: 20px; 
            font-size: 14px; 
            color: #777;
        }
        .print-button {
            display: block; 
            margin: 20px auto; 
            padding: 10px 20px; 
            background-color: #007bff; 
            color: #fff; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
            text-align: center;
            transition: background-color 0.3s;
        }
        .print-button:hover {
            background-color: #0056b3;
        }

        /* Ẩn nút in khi in */
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="library-header">
            <div class="logo">
                <img src="../QLThuVien/PNG/logo.jpg" alt="Library Logo"> 
            </div>
            <div class="library-info">
                <h2>Thư Viện ABC</h2>
                <p>Địa chỉ: 123 Đường XYZ, Thành phố, Quốc gia</p>
                <p>Điện thoại: (012) 345-6789</p>
            </div>
        </div>
        <h1>Hóa Đơn Trả Sách</h1>
        <div class="details">
            <p><strong>Loan ID:</strong> <?php echo htmlspecialchars($loan_info['LoanID']); ?></p>
            <p><strong>Tên Độc Giả:</strong> <?php echo htmlspecialchars($loan_info['Name']); ?></p>
            <p><strong>Tiêu Đề Sách:</strong> <?php echo htmlspecialchars($loan_info['Title']); ?></p>
            <p><strong>Ngày Đến Hạn:</strong> <?php echo htmlspecialchars($loan_info['DueDate']); ?></p>
            <p><strong>Ngày Trả:</strong> <?php echo htmlspecialchars($loan_info['ReturnDate']); ?></p>
            <p><strong>Phí Trả Sách Muộn:</strong> <?php echo number_format($late_fee, 0, ',', '.'); ?> VNĐ</p>
        </div>
        <div class="footer">
            <p>Cảm ơn bạn đã sử dụng dịch vụ của thư viện!</p>
        </div>
        <button class="print-button" onclick="window.print()">In Hóa Đơn</button>
    </div>
</body>
</html>

<?php
// Đóng kết nối
$stmt->close();
$conn->close();
?>