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

// loan_id và late_fee đã được truyền
if (!isset($_GET['loan_id']) || !isset($_GET['late_fee'])) {
    $_SESSION['error_message'] = "Không tìm thấy thông tin hóa đơn.";
    header("Location: loans.php"); // Quay về trang mượn sách
    exit();
}

// Lấy loan_id và late_fee từ URL
$loan_id = $_GET['loan_id'];
$late_fee = $_GET['late_fee'];

//  khoản mượn
$sql = "SELECT l.LoanID, l.ReaderID, l.BookID, b.Title, r.Name, l.ReturnDate
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
    <title>Phiếu Bồi Thường</title>
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
        <h1>Phiếu Bồi Thường</h1>
        <div class="details">
            <p><strong>Reader ID:</strong> <?php echo htmlspecialchars($loan_info['ReaderID']); ?></p>
            <p><strong>Tên Độc Giả:</strong> <?php echo htmlspecialchars($loan_info['Name']); ?></p>
            <p><strong>Loan ID:</strong> <?php echo htmlspecialchars($loan_info['LoanID']); ?></p>
            <p><strong>Tiêu Đề Sách:</strong> <?php echo htmlspecialchars($loan_info['Title']); ?></p>
            <p><strong>Ngày Trả:</strong> <?php echo date('d/m/Y'); ?></p>
            <p><strong>Phí Bồi Thường:</strong> <?php echo number_format($late_fee, 0, ',', '.'); ?> VNĐ</p>
        </div>
        <div class="footer">
            <p>Cảm ơn bạn đã sử dụng dịch vụ của thư viện!</p>
        </div>
        <button class="print-button" onclick="window.print()">In Phiếu</button>
    </div>
</body>
</html>

<?php
// Đóng kết nối
$stmt->close();
$conn->close();
?>