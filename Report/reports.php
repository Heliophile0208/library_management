<?php
session_start();
if (!$_SESSION['username']) {
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
mysqli_set_charset($conn, 'utf8');
$sql_loans = "SELECT * FROM Loans";
$sql_books ="SELECT * FROM Books";

$loans_result = $conn->query($sql_loans);
$books_result = $conn->query($sql_books);
$conn->close(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo</title>
</head>
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
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

    .menu a:hover,
    .menu a.active {
        background-color: #45a049;
        font-weight: bold;
    }

    .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
        }

</style>


<body>
    <div class="navbar">
        Library Management Dashboard
    </div>
    <div class="menu">
        <a href="../dashboard.php">Bảng Điều Khiển</a>
        <a href="../Book/">Sách</a>
        <a href="../User/">Người Dùng</a>
        <a href="../Loan/loans.php">Mượn/Trả Sách</a>
        <a href="../Book/Inventory.php">Kho</a>
        <a class="active" href="../Report/reports.php">Báo Cáo</a>
        <a href="logout.php">Đăng Xuất</a>
    </div>
    <h2>Báo Cáo Mượn Sách</h2>
        <label for="filer">Lọc Theo Tháng</label>
        <!-- Lọc LoanDate theo tháng -->
        <input type="number" min="1" max="12" name="filter" value="<?php echo $loan_date['Month']?>" placeholder="Nhập tháng">




        <!-- Nút để mở modal -->
        <button style="margin-top:20px; padding:10px;" type="button" onclick="openModal();">
            Tạo Báo Cáo
        </button>
<script>
    function openModal() {
        document.getElementById('createReportModal').style.display = "flex";
    }
    function closeModal() {
        document.getElementById('createReportModal').style.display = "none";
    }
</script>
        <!-- Modal -->
         <div id="createReportModal" class="modal fade" role="dialog" class="modal">
                <div class="modal-content">
                    <span onclick="closeModal();" style="cursor: pointer; float: right; font-size: 20px;">&times;</span>
                    <h3>Chọn Loại Báo Cáo</h3>
                    <form action="reports.php" method="post">
                        <label for="report_type">Loại Báo Cáo:</label>
                        <select name="report_type" id="report_type">
                            <option value="monthly_loan_report">Monthly Summary</option>
                            <option value="lost_loan_report">Lost Books Report</option>
                            <option value="damaged_loan_report">Damaged Books Report</option>
                            <option value="new_avarial_report">New Arrivals</option>
                            <option value="overdue_report">Overdue Books Report</option>
                        </select>
                        <button type="submit" name="generate_report">Tạo Báo Cáo</button>
                    </form>
                </div>

         </div>


    </form>





</body>

</html>