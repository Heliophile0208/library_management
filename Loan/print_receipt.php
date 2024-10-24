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

// Lấy LoanID từ URL
$loan_ids = isset($_GET['loan_ids']) ? explode(',', $_GET['loan_ids']) : [];

if (empty($loan_ids)) {
    echo "Không có phiếu mượn nào để in.";
    exit();
}

// Truy vấn thông tin các phiếu mượn
$loan_data = [];
foreach ($loan_ids as $loan_id) {
    $sql = "SELECT * FROM Loans WHERE LoanID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $loan_data[] = $result->fetch_assoc();
    }
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiếu Mượn Sách</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .receipt {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #007bff;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
        }
        .library-header {
            display: flex;  
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px;
        }
        .logo {
            
            width:500px;
        }
        .library-header img {
            max-width: 100px;
        }
        .library-info {
            text-align: right;
        }
        .library-info h1 {
            margin: 5px 0;
            font-size: 24px;
            color: #343a40;
        }
        .library-info p {
            margin: 0;
            font-size: 14px;
            color: #6c757d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .print-button {
            display: block;
            margin: 20px auto;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
        }
        .print-button:hover {
            background-color: #0056b3;
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
                <h1>Thư Viện ABC</h1>
                <p>Địa chỉ: 123 Đường XYZ, Thành phố, Quốc gia</p>
                <p>Điện thoại: (012) 345-6789</p>
            </div>
        </div>
        <h2>Phiếu Mượn Sách</h2>
        <table>
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Reader ID</th>
                    <th>Book ID</th>
                    <th>Loan Date</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($loan_data as $loan): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($loan['LoanID']); ?></td>
                        <td><?php echo htmlspecialchars($loan['ReaderID']); ?></td>
                        <td><?php echo htmlspecialchars($loan['BookID']); ?></td>
                        <td><?php echo htmlspecialchars($loan['LoanDate']); ?></td>
                        <td><?php echo htmlspecialchars($loan['DueDate']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="footer">
            <p>Cảm ơn bạn đã đến với thư viện của chúng tôi!</p>
            <p>Hẹn gặp lại bạn vào lần sau!</p>
        </div>
        <button class="print-button" onclick="window.print()">In Phiếu Mượn</button>
    </div>
</body>
</html>