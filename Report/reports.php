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
    die("Kết nối thất bại: " . $conn->connect_error);
}

mysqli_set_charset($conn, 'utf8');

$loans_data = [];
$damages_data = [];
$missing_books_data = [];
$overdue_books_data = [];
$report_title = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_report'])) {
    $report_type = $_POST['report_type'];
    $selected_month = $_POST['month'] ?? '';

    switch ($report_type) {
        case "monthly_loan_report":
            if (!empty($selected_month)) {
                // Tách năm và tháng từ input
                $year = date('Y', strtotime($selected_month));
                $month = date('m', strtotime($selected_month));
                
                // Lọc dữ liệu từ bảng Loans
                $sql_loans = "
                    SELECT LoanID, BookID, LoanDate, ReturnDate, DueDate 
                    FROM Loans WHERE YEAR(LoanDate) = $year AND MONTH(LoanDate) = $month
                ";
                $result_loans = $conn->query($sql_loans);
                $loans_data = $result_loans->fetch_all(MYSQLI_ASSOC);
                
                // Lọc dữ liệu từ bảng BookDamages cho sách hỏng và sách mất
                $sql_damages = "
                    SELECT DamageID, BookID, DamageType, Description, ReportedAt
                    FROM BookDamages WHERE YEAR(ReportedAt) = $year AND MONTH(ReportedAt) = $month
                ";
                $result_damages = $conn->query($sql_damages);
                $damages_data = $result_damages->fetch_all(MYSQLI_ASSOC);

                // Lọc dữ liệu từ bảng Inventory_Transactions
                $sql_inventory = "
                    SELECT TransactionID, BookID, Quantity, TransactionType, TransactionDate
                    FROM Inventory_Transactions WHERE YEAR(TransactionDate) = $year AND MONTH(TransactionDate) = $month
                ";
                $result_inventory = $conn->query($sql_inventory);
                $inventory_data = $result_inventory->fetch_all(MYSQLI_ASSOC);

                $report_title = "Báo Cáo Tổng Quan Cho Tháng " . date('m/Y', strtotime($selected_month));
            } else {
                $loans_data = [];
                $damages_data = [];
                $inventory_data = [];
                $report_title = "Báo Cáo Tổng Quan Theo Tháng - Tháng không hợp lệ!";
            }
            break;

        case "missing_books_report":
            // Lọc dữ liệu sách mất (DamageType = 'Lost')
            $sql_missing_books = "
                SELECT DamageID, BookID, Description, ReportedAt
                FROM BookDamages
                WHERE DamageType = 'Lost'
            ";
            $result_missing_books = $conn->query($sql_missing_books);
            $missing_books_data = $result_missing_books->fetch_all(MYSQLI_ASSOC);
            $report_title = "Báo Cáo Sách Mất";
            break;

        case "overdue_books_report":
            // Lọc dữ liệu sách hỏng quá hạn
            $sql_overdue_books = "
                SELECT LoanID, BookID, LoanDate, DueDate, ReturnDate
                FROM Loans
                WHERE ReturnDate IS NULL AND DueDate < NOW()
            ";
            $result_overdue_books = $conn->query($sql_overdue_books);
            $overdue_books_data = $result_overdue_books->fetch_all(MYSQLI_ASSOC);
            $report_title = "Báo Cáo Sách Quá Hạn";
            break;

        default:
            $report_title = "Loại Báo Cáo Không Xác Định";
            break;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
 <title><?php echo isset($report_title) ? $report_title : "Tạo Báo Cáo"; ?></title>
    <style>

            body {
            font-family: Arial, Helvetica, sans-serif;
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
        .menu a:hover, a.active {
            background-color: #45a049;
            font-weight: bold;
        }

        h2 {
            text-align: center;
            padding: 20px;
        }

        h3 {
            text-align: center;
            padding-top: 20px;
            color: black;
        }  
        h4 {
            text-align: center;
            padding: 20px;
            color: #4CAF50;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            text-align: left;
            background-color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }

        td {
            text-align: center;
        }

        table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tbody tr:hover {
            background-color: #ddd;
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

        .menu a:hover {
            background-color: #45a049;
            font-weight: bold;
        }

        form {
            text-align: center;
            margin: 20px auto;
        }

        select, button {
            padding: 10px;
            font-size: 16px;
            margin: 10px;
        }

        #month_input {
            display: none;
            margin-top: 10px;
        }

        #month_input label, #month_input input {
            font-size: 16px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            text-align:center
        }

        button:hover {
            background-color: #45a049;
        }

        /* CSS cho in ấn */
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-section, #printable-section * {
                visibility: visible;
            }
            #printable-section {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

        }

        @media (max-width: 768px) {
            table {
                width: 100%;
            }

            th, td {
                font-size: 14px;
                padding: 8px;
            }

            .menu a {
                font-size: 16px;
            }

            form {
                padding: 10px;
            }
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
        <a class="active" href="../Report/reports.php">Báo Cáo</a>
        <a href="../logout.php">Đăng Xuất</a>
    </div>
<h1 style="text-align:center"> Báo Cáo Thường Trực </h1>

   
  <form action="reports.php" method="post">
    <label for="report_type">Chọn Loại Báo Cáo:</label>
    <select name="report_type" id="report_type" onchange="toggleMonthInput()">
        <option value="" disabled <?php echo !isset($_POST['report_type']) ? 'selected' : ''; ?>>Chọn loại báo cáo</option>
        <option value="monthly_loan_report" <?php echo isset($_POST['report_type']) && $_POST['report_type'] == 'monthly_loan_report' ? 'selected' : ''; ?>>Tổng Quan Theo Tháng</option>
        <option value="missing_books_report" <?php echo isset($_POST['report_type']) && $_POST['report_type'] == 'missing_books_report' ? 'selected' : ''; ?>>Báo Cáo Sách Mất</option>
        <option value="overdue_books_report" <?php echo isset($_POST['report_type']) && $_POST['report_type'] == 'overdue_books_report' ? 'selected' : ''; ?>>Báo Cáo Sách Quá Hạn</option>
    </select>

  
  <div id="month_input" style="display: none;">
        <label for="month">Chọn Tháng:</label>
        <input type="month" id="month" name="month" min="2023-01" max="2024-12" value="<?php echo isset($_POST['month']) ? $_POST['month'] : ''; ?>" />
    </div>

    <button type="submit" name="generate_report">Tạo Báo Cáo</button>
</form>
<?php if (isset($report_title)) : ?>
    <div id="printable-section">
        
         <h3><?php echo isset($report_title) ? $report_title : "Tạo Báo Cáo"; ?></h3>



        <!-- Báo cáo mượn sách theo tháng -->
        <?php if ($report_type == "monthly_loan_report" && !empty($loans_data)) : ?>
            <h4>Báo Cáo Mượn Sách</h4>
            <table>
                <thead>
                    <tr>
                        <th>LoanID</th>
                        <th>BookID</th>
                        <th>Ngày Mượn</th>
                        <th>Ngày Trả</th>
                        <th>Ngày Hạn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans_data as $loan) : ?>
                        <tr>
                            <td><?php echo $loan['LoanID']; ?></td>
                            <td><?php echo $loan['BookID']; ?></td>
                            <td><?php echo $loan['LoanDate']; ?></td>
                            <td><?php echo $loan['ReturnDate']; ?></td>
                            <td><?php echo $loan['DueDate']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

<!-- Báo cáo giao dịch kho -->
<?php if ($report_type == "monthly_loan_report" && !empty($inventory_data)) : ?>
    <h4>Báo Cáo Giao Dịch Kho</h4>
    <table>
        <thead>
            <tr>
                <th>TransactionID</th>
                <th>BookID</th>
                <th>Số Lượng</th>
                <th>Loại Giao Dịch</th>
                <th>Ngày Giao Dịch</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inventory_data as $transaction) : ?>
                <tr>
                    <td><?php echo $transaction['TransactionID']; ?></td>
                    <td><?php echo $transaction['BookID']; ?></td>
                    <td><?php echo $transaction['Quantity']; ?></td>
                    <td><?php echo $transaction['TransactionType']; ?></td>
                    <td><?php echo $transaction['TransactionDate']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

        <!-- Báo cáo sách hỏng -->
        <?php if ($report_type == "monthly_loan_report" && !empty($damages_data)) : ?>
            <h4>Báo Cáo Sách Hỏng</h4>
            <table>
                <thead>
                    <tr>
                        <th>DamageID</th>
                        <th>BookID</th>
                        <th>Loại Hỏng</th>
                        <th>Mô Tả</th>
                        <th>Ngày Báo Cáo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($damages_data as $damage) : ?>
                        <tr>
                            <td><?php echo $damage['DamageID']; ?></td>
                            <td><?php echo $damage['BookID']; ?></td>
                            <td><?php echo $damage['DamageType']; ?></td>
                            <td><?php echo $damage['Description']; ?></td>
                            <td><?php echo $damage['ReportedAt']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Báo cáo sách mất -->
        <?php if ($report_type == "missing_books_report" && !empty($missing_books_data)) : ?>

            
            <table>
                <thead>
                    <tr>
                        <th>DamageID</th>
                        <th>BookID</th>
                        <th>Mô Tả</th>
                        <th>Ngày Báo Cáo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($missing_books_data as $missing) : ?>
                        <tr>
                            <td><?php echo $missing['DamageID']; ?></td>
                            <td><?php echo $missing['BookID']; ?></td>
                            <td><?php echo $missing['Description']; ?></td>
                            <td><?php echo $missing['ReportedAt']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Báo cáo sách quá hạn -->
        <?php if ($report_type == "overdue_books_report" && !empty($overdue_books_data)) : ?>
            
            <table>
                <thead>
                    <tr>
                        <th>LoanID</th>
                        <th>BookID</th>
                        <th>Ngày Mượn</th>
                        <th>Ngày Hạn</th>
                        <th>Ngày Trả</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($overdue_books_data as $overdue) : ?>
                        <tr>
                            <td><?php echo $overdue['LoanID']; ?></td>
                            <td><?php echo $overdue['BookID']; ?></td>
                            <td><?php echo $overdue['LoanDate']; ?></td>
                            <td><?php echo $overdue['DueDate']; ?></td>
                            <td><?php echo $overdue['ReturnDate']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

<div style="text-align:center" id="print">

    <button  onclick="window.print()">In Báo Cáo</button>


</div>
<?php endif; ?>


<script>
    
    // Hàm ẩn/hiện input tháng dựa trên loại báo cáo
function toggleMonthInput() {
    const reportType = document.getElementById('report_type').value;
    const monthInput = document.getElementById('month_input');
    if (reportType === 'monthly_loan_report') {
        monthInput.style.display = 'block';
    } else {
        monthInput.style.display = 'none';
    }
}

// Kiểm tra lại trạng thái của báo cáo khi trang được tải lại
window.onload = function() {
    toggleMonthInput();  // Kiểm tra xem có phải báo cáo theo tháng hay không

    };

</script>
</body>
</html>

