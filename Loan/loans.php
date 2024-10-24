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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
mysqli_set_charset($conn, 'utf8');

// Xử lý tìm kiếm
$searchTerm = isset($_POST['search_term']) ? $_POST['search_term'] : '';

// Lấy danh sách mượn sách, có lọc nếu nhập vào ô tìm kiếm
$sql_loans = "SELECT * FROM Loans WHERE ReaderID LIKE '$searchTerm%' OR LoanID LIKE '$searchTerm%'";
$loans_result = $conn->query($sql_loans);

// Truy vấn danh sách sách để hiển thị trong modal mượn sách
$sql_books = "SELECT * FROM Books WHERE Quantity > 0";
$books_result = $conn->query($sql_books);

if (isset($_POST['reader_id']) && isset($_POST['book_id'])) {
    $reader_id = $_POST['reader_id']; // Lấy thông tin ReaderID
    $book_ids = $_POST['book_id']; // Mảng các BookID được chọn

    // Kiểm tra xem ReaderID có tồn tại trong bảng Readers không
    $sql_check_reader = "SELECT * FROM Readers WHERE ReaderID = ?";
    $stmt_check = $conn->prepare($sql_check_reader);
    $stmt_check->bind_param("s", $reader_id);
    $stmt_check->execute();
    $reader_result = $stmt_check->get_result();

    // Nếu ReaderID không tồn tại, chuyển hướng đến trang nhập thông tin mới
    if ($reader_result->num_rows === 0) {
        $_SESSION['error_message'] = "Reader ID không tồn tại. Vui lòng nhập thông tin mới.";
        header("Location: ../Book/add_reader.php?reader_id=" . urlencode($reader_id)); // Chuyển hướng với ReaderID
        exit();
    }
    // Lấy thông tin thẻ thư viện của người dùng
    $sql = "SELECT CardRank FROM LibraryCards WHERE ReaderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $reader_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $cardRank = $row['CardRank'] ?? null;

    // Kiểm tra số lượng sách được phép mượn và thời gian mượn theo hạng thẻ
    $maxBooks = 0;
    $loanDuration = 0; // Thời gian mượn bằng ngày

    switch ($cardRank) {
        case 'Basic':
            $maxBooks = 2;
            $loanDuration = 30; // 1 tháng
            break;
        case 'Silver':
            $maxBooks = 3;
            $loanDuration = 60; // 2 tháng
            break;
        case 'Gold':
            $maxBooks = 5;
            $loanDuration = 60; // 2 tháng
            break;
        case 'Platinum':
            $maxBooks = 10;
            $loanDuration = 90; // 3 tháng
            break;
        default:
            $maxBooks = 1;
            $loanDuration = 7; // 1 tuần
    }

    // Kiểm tra số lượng sách đã mượn của người dùng
    $sql = "SELECT COUNT(*) AS loaned_count FROM Loans WHERE ReaderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $reader_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $loaned_count = $result->fetch_assoc()['loaned_count'];

    // Kiểm tra tổng số sách đã mượn và sách mới mượn
    $total_books_requested = $loaned_count + count($book_ids);

    if ($total_books_requested > $maxBooks) {
        $_SESSION['error_message'] = "Bạn chỉ có thể mượn tối đa $maxBooks quyển sách. Bạn đã mượn $loaned_count quyển.";
    } else {
        $loanIds = []; // Mảng lưu trữ LoanID đã mượn
        foreach ($book_ids as $book_id) {
            $sql = "INSERT INTO Loans (ReaderID, BookID, LoanDate, DueDate) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY))";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $reader_id, $book_id, $loanDuration);
            $stmt->execute();
            // Lấy LoanID vừa được thêm vào
            $loanIds[] = $conn->insert_id;
        }
        // Chuyển hướng đến trang in phiếu mượn
        header("Location: print_receipt.php?loan_ids=" . implode(',', $loanIds));
        exit();
    }
    // Đóng kết nối
    $stmt->close();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loan_id'])) {
    $loan_id = $_POST['loan_id'];

    if ($_POST['action'] === 'delete') {
        $sql = "DELETE FROM Loans WHERE LoanID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $loan_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Xóa thành công.";
            header("Location: loans.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Có lỗi xảy ra khi xóa.";
            header("Location: loans.php");
            exit();
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['loan_id'])) {
    $loan_id = $_POST['loan_id'];
    $action = $_POST['action'];

    // Lấy thông tin sách từ LoanID
    $sql = "SELECT BookID, ReaderID, DueDate, ReturnDate FROM Loans WHERE LoanID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $loan = $result->fetch_assoc();

    if ($loan) {
        $book_id = $loan['BookID'];
        $reader_id = $loan['ReaderID'];
        $due_date = new DateTime($loan['DueDate']);
        $return_date = new DateTime();
        $days_late = $return_date > $due_date ? $return_date->diff($due_date)->days : 0;

        switch ($action) {
            case 'late_return':
                // Tính phí trả sách muộn
                $fee = $days_late * 5000; // Phí 5000 VNĐ/ngày

                // In ra số ngày trễ và phí
                var_dump($days_late, $fee); // Kiểm tra số ngày trễ và phí

                // Cập nhật ReturnDate
                $sql = "UPDATE Loans SET ReturnDate = NOW() WHERE LoanID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $loan_id);
                $stmt->execute();

                // Kiểm tra xem có cập nhật thành công không
                if ($stmt->affected_rows > 0) {
                    // Nếu trả sách thành công, thông báo phí muộn
                    header("Location: print_receipt_return.php?loan_id=$loan_id&late_fee=$fee"); // Chuyển hướng đến trang in với phí
                    exit();
                } else {
                    $_SESSION['error_message'] = "Có lỗi xảy ra khi trả sách.";
                }
                break;

            case 'compensation':
                // Lấy thông tin độc giả từ loan_id
                $sql = "SELECT ReaderID, BookID FROM Loans WHERE LoanID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $loan_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $loan_info = $result->fetch_assoc();

                if (!$loan_info) {
                    $_SESSION['error_message'] = "Không tìm thấy thông tin khoản mượn.";
                    header("Location: loans.php");
                    exit();
                }

                $reader_id = $loan_info['ReaderID'];
                $book_id = $loan_info['BookID'];

                // Lấy giá bồi thường từ bảng Prices
                $sql = "SELECT Price FROM Prices WHERE BookID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $book_id);
                $stmt->execute();
                $price_result = $stmt->get_result();
                $price_row = $price_result->fetch_assoc();

                // Gán phí bồi thường, nếu không tìm thấy giá thì dùng 50000
                $compensation_fee = $price_row['Price'] ?? 50000;

                // Cập nhật ngày trả sách
                $sql_loan = "UPDATE Loans SET ReturnDate = NOW() WHERE LoanID = ?";
                $stmt = $conn->prepare($sql_loan);
                $stmt->bind_param("i", $loan_id);

                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    // Kiểm tra trạng thái độc giả hiện tại
                    $sql_check_status = "SELECT Status FROM Readers WHERE ReaderID = ?";
                    $stmt_check = $conn->prepare($sql_check_status);
                    $stmt_check->bind_param("i", $reader_id);
                    $stmt_check->execute();
                    $result_check = $stmt_check->get_result();
                    $reader_info = $result_check->fetch_assoc();

                    // Cập nhật trạng thái độc giả thành 0 nếu chưa là 0
                    if ($reader_info && $reader_info['Status'] != 0) {
                        $sql_reader = "UPDATE Readers SET Status = 0 WHERE ReaderID = ?";
                        $stmt = $conn->prepare($sql_reader);
                        $stmt->bind_param("i", $reader_id);
                        $stmt->execute();
                        // Kiểm tra nếu cập nhật trạng thái thành công
                        if ($stmt->affected_rows > 0) {
                            $_SESSION['success_message'] = "Bồi thường thành công. Phí: " . number_format($compensation_fee, 0, ',', '.') . " VNĐ";
                        } else {
                            $_SESSION['success_message'] = "Bồi thường thành công, nhưng không cập nhật trạng thái độc giả.";
                        }
                    } else {
                        $_SESSION['success_message'] = "Bồi thường thành công. Phí: " . number_format($compensation_fee, 0, ',', '.') . " VNĐ";
                    }

                    // Chuyển đến trang in phiếu bồi thường
                    header("Location: print_compensation.php?loan_id=$loan_id&late_fee=$compensation_fee");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Không thể cập nhật ngày trả sách.";
                    header("Location: loans.php"); // Quay về loans.php nếu có lỗi
                    exit();
                }

            case 'blacklist':
                // Kiểm tra LoanID có tồn tại không
                $sql_check = "SELECT ReaderID FROM Loans WHERE LoanID = ?";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bind_param("i", $loan_id);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();

                if ($result_check->num_rows === 0) {
                    $_SESSION['error_message'] = "Loan ID không hợp lệ.";
                    header("Location: loans.php");
                    exit();
                }

                // Kiểm tra trạng thái độc giả
                $sql_status_check = "SELECT Status FROM Readers WHERE ReaderID = (SELECT ReaderID FROM Loans WHERE LoanID = ?)";
                $stmt_status_check = $conn->prepare($sql_status_check);
                $stmt_status_check->bind_param("i", $loan_id);
                $stmt_status_check->execute();
                $result_status_check = $stmt_status_check->get_result();

                if ($result_status_check->num_rows > 0) {
                    $current_status = $result_status_check->fetch_assoc()['Status'];
                    if ($current_status == 1) {
                        $_SESSION['error_message'] = "Độc giả đã ở trong danh sách đen.";
                        // Cập nhật ngày trả sách thành NULL nếu đã ở trong danh sách đen
                        $sql_loan = "UPDATE Loans SET ReturnDate = NULL WHERE LoanID = ?";
                        $stmt_loan = $conn->prepare($sql_loan);
                        $stmt_loan->bind_param("i", $loan_id);
                        $stmt_loan->execute();
                        header("Location: loans.php");
                        exit();
                    }
                } else {
                    $_SESSION['error_message'] = "Không tìm thấy độc giả.";
                    header("Location: loans.php");
                    exit();
                }

                // Cập nhật trạng thái Reader thành blacklisted
                $sql = "UPDATE Readers SET Status = 1 WHERE ReaderID = (SELECT ReaderID FROM Loans WHERE LoanID = ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $loan_id);

                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        // Cập nhật ngày trả sách thành NULL
                        $sql_loan = "UPDATE Loans SET ReturnDate = NULL WHERE LoanID = ?";
                        $stmt_loan = $conn->prepare($sql_loan);
                        $stmt_loan->bind_param("i", $loan_id);

                        if ($stmt_loan->execute()) {
                            $_SESSION['success_message'] = "Độc giả đã bị thêm vào danh sách đen.";
                        } else {
                            $_SESSION['error_message'] = "Lỗi khi cập nhật ngày trả sách: " . $stmt_loan->error;
                        }
                    } else {
                        $_SESSION['error_message'] = "Không có bản ghi nào được cập nhật.";
                    }
                } else {
                    $_SESSION['error_message'] = "Lỗi khi cập nhật trạng thái độc giả: " . $stmt->error;
                }

                header("Location: loans.php"); // Chuyển hướng về trang mượn sách
                exit();
        }
    }
    $stmt->close();
}

// Xử lý trả sách
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_book']) && isset($_POST['loan_id'])) {
    $loan_id = $_POST['loan_id'];

    // Cập nhật ReturnDate trong Loans
    $sql = "UPDATE Loans SET ReturnDate = NOW() WHERE LoanID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $loan_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Trả sách thành công.";
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi trả sách.";
    }

    // Đóng kết nối
    $stmt->close();
}

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mượn/Trả Sách</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0 auto;
            padding: 0;
        }

        h2 {
            padding: 20px;
            margin-bottom: -15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        input[type="text"] {
            width: 500px;
            padding: 10px;
            box-sizing: border-box;
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

        .close {
            float: right;
            font-size: 25px;
            cursor: pointer;
        }

        button {
            padding: 10px 15px;

            cursor: pointer;
            text-align: center;
            margin-right: 10px;
        }

        button:hover {
            background-color: #45a049;
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

        .selected-books {
            margin-top: 10px;
            padding: 10px;
        }

        .selected-books ul {
            list-style-type: none;
            padding: 10px;
        }

        .selected-books li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px;
            border: 1px solid #ddd;
            margin: 5px 0;
        }

        .remove-btn {
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
            padding: 5px 10px;
        }

        .remove-btn:hover {
            background-color: #d32f2f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .pagination {
            margin: 20px 0;
            text-align: center;
        }

        .pagination a {
            margin: 0 5px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .pagination a:hover {
            background-color: #45a049;
        }

        input[type="text"]#bookSearch {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[name="search_term"] {
            margin-left: 20px;
        }

        .book-selection {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }

        .book-item {
            margin: 10px 0;
        }

        .active {
            font-weight: bold;
            background-color: #007BFF;
            color: white;
        }

        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }
    </style>
    <script>
        function openModal() {
            document.getElementById("myModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        function filterBooks() {
            var input = document.getElementById('bookSearch');
            var filter = input.value.toLowerCase();
            var bookList = document.getElementById('bookList');
            var books = bookList.getElementsByClassName('book-item');

            for (var i = 0; i < books.length; i++) {
                var label = books[i].getElementsByTagName('label')[0];
                if (label) {
                    var txtValue = label.textContent || label.innerText;
                    books[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
                }
            }
        }
    </script>
</head>

<body>
    <div class="navbar">
        Library Management Dashboard
    </div>
    <div class="menu">
        <a href="../dashboard.php">Bảng Điều Khiển</a>
        <a href="../Book/books.php">Sách</a>
        <a href="../User/users.php">Người Dùng</a>
        <a class="active" href="loans.php">Mượn/Trả Sách</a>
        <a href="../Book/inventory.php">Kho</a>
        <a href="../Report/reports.php">Báo Cáo</a>
        <a href="../logout.php">Đăng Xuất</a>
    </div>
    <div class="container">
        <h2>Danh Sách Mượn Sách</h2>

        <!-- Hiển thị thông báo lỗi nếu có -->
        <?php if (isset($_SESSION['error_message'])): ?>

            <div style="color: red; margin: 10px;">
                <?php
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Hiển thị thông báo thành công nếu có -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="color: green; margin: 10px;">
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <form method="POST" action="">
                <input type="text" name="search_term" placeholder="Tìm kiếm theo LoanID hoặc ReaderID" value="<?php echo htmlspecialchars($searchTerm); ?>" />
                <button type="submit">Tìm kiếm</button>
                <button type="button" onclick="openModal()">Mượn Sách</button>
            </form>
        </div>

        <table>
            <tr>
                <th>Loan ID</th>
                <th>Reader ID</th>
            
                <th>Loan Date</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Actions</th>
                <th>Compention</th>
    </tr>
    <?php while ($row = $loans_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['LoanID']; ?></td>
            <td><?php echo $row['ReaderID']; ?></td>
                    <td><?php echo $row['LoanDate']; ?></td>
            <td><?php echo $row['DueDate']; ?></td>
            <td><?php echo $row['ReturnDate'] ? $row['ReturnDate'] : 'Chưa trả'; ?></td>
            <td>
                <button type="button" style="padding:10px;width:100px;" 
                        data-loan-id="<?php echo $row['LoanID']; ?>" 
                        onclick="openModalaction(this)">Hành Động</button>
            </td>
            <td>
                <?php if (!$row['ReturnDate']): ?>
                    <button type="button" style="padding:10px; width:100px;" onclick="openModal2('<?php echo $row['LoanID']; ?>')">Tác Vụ</button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<!-- Modal cho Hành Động -->
<div id="ActionsModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModalaction()">&times;</span>
        <h5>Chọn Hành Động</h5>
        <p>Bạn có chắc chắn muốn thực hiện hành động này?</p>
<div style=" display:flex"     >   
        <!-- Biểu mẫu xóa -->
        <form id="deleteForm" method="POST" action="loans.php">
            <input type="hidden" name="action" value="delete">
            <button type="button" onclick="confirmDelete()">Xoá</button>
        </form>

        <button type="button" onclick="editLoan(window.currentLoanId)">Sửa</button>
    </div></div>
</div>

<script>
// Mở modal cho hành động
function openModalaction(buttonElement) {
    const loanId = buttonElement.getAttribute('data-loan-id'); // Lấy LoanID từ data attribute
    document.getElementById("ActionsModal").style.display = "block";
    window.currentLoanId = loanId; // Lưu LoanID vào biến toàn cục
}

// Đóng modal
function closeModalaction() {
    document.getElementById("ActionsModal").style.display = "none";
}

// Xóa loan
function confirmDelete() {
    if (confirm("Bạn có chắc chắn muốn xoá không?")) {
        const form = document.getElementById('deleteForm');
        const loanIdInput = document.createElement('input');
        loanIdInput.type = 'hidden';
        loanIdInput.name = 'loan_id';
        loanIdInput.value = window.currentLoanId; // Sử dụng LoanID đã lưu
        form.appendChild(loanIdInput);
        form.submit(); // Gửi biểu mẫu xóa
    }
}

// Hàm sửa
function editLoan(loanId) {
    window.location.href = "edit_loan.php?loan_id=" + loanId; // Chuyển đến trang sửa
}

// Mở modal cho tác vụ
function openModal2(loanId) {
    // Logic cho modal tác vụ
}
</script>

        </table>
        <!-- Modal Bồi Thường/Trả Sách -->
        <div id="compensationModal" class="modal">
            <div class="modal-content">
                <span onclick="closeModal2()" style="cursor: pointer; float: right; font-size: 20px;">&times;</span>
                <h3>Xử Lý Bồi Thường/Trả Sách</h3>
                <form method="POST" action="">
                    <input type="hidden" name="loan_id" id="modal_loan_id" />
 <button type="submit" name="action" value="return_book">Trả Sách</button>
                    
                    <button type="submit" name="action" value="compensation">Bồi Thường</button>
                    <button type="submit" name="action" value="blacklist">Sổ Đen</button>

                </form>
            </div>
        </div>
        <script>
            function openModal2(loanId) {
                document.getElementById("modal_loan_id").value = loanId;
                document.getElementById("compensationModal").style.display = "flex";
            }
 function closeModal2() {
            document.getElementById("compensationModal").style.display = "none";
        }
        </script>
        <!-- Modal Mượn Sách -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span onclick="closeModal()" style="cursor: pointer; float: right; font-size: 20px;">&times;</span>
                <h3>Mượn Sách</h3>
                <form method="POST" action="">

                    <label for="reader_id">Reader ID:</label>
                    <input type="text" id="reader_id" name="reader_id" required />
                    <label for="bookSearch">Tìm sách:</label>
                    <input style="width:500px; margin-top:10px;margin-left:5px; border:1px soild black" type="text" id="bookSearch" onkeyup="filterBooks()" placeholder="Nhập tên sách..." />
                    <div id="bookList"></div>
 <div id="selectedList" style="margin-top: 20px; font-weight: bold; margin-bottom:20px;"></div>           
           
                   
 <div style="text-align:center" id="pagination"></div>
                    <div style="text-align:center">
                        <button style="margin-top:20px;" type="submit">Mượn</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
          const originalBooks = [
    <?php while ($book = $books_result->fetch_assoc()): ?> {
            id: "<?php echo $book['BookID']; ?>",
            title: "<?php echo $book['Title']; ?>"
        },
    <?php endwhile; ?>
];

let selectedBooks = []; // Mảng để lưu các sách đã chọn
const booksPerPage = 5;
let currentPage = 1;
let filteredBooks = [...originalBooks]; // Danh sách sách đã lọc, bắt đầu bằng danh sách gốc

// Hàm để render sách
function renderBooks() {
    const bookList = document.getElementById('bookList');
    bookList.innerHTML = '';

    const start = (currentPage - 1) * booksPerPage;
    const end = start + booksPerPage;
    const paginatedBooks = filteredBooks.slice(start, end);

    paginatedBooks.forEach(book => {
        const isChecked = selectedBooks.includes(book.id); // Kiểm tra sách có được chọn hay không
        const bookItem = document.createElement('div');
        bookItem.className = 'book-item';
        bookItem.innerHTML = `
            <input type="checkbox" id="book_${book.id}" name="book_id[]" value="${book.id}" onchange="updateSelectedBooks(this)" ${isChecked ? 'checked' : ''}>
            <label for="book_${book.id}">${book.title}</label>
        `;
        bookList.appendChild(bookItem);
    });

    updatePagination();
    renderSelectedBooks(); // Hiển thị sách đã chọn
}

// Hàm cập nhật sách đã chọn
function updateSelectedBooks(checkbox) {
    const bookId = checkbox.value;
    if (checkbox.checked) {
        if (!selectedBooks.includes(bookId)) {
            selectedBooks.push(bookId); // Thêm sách vào danh sách đã chọn
        }
    } else {
        selectedBooks = selectedBooks.filter(id => id !== bookId); // Xóa sách khỏi danh sách đã chọn
    }
    renderSelectedBooks(); // Cập nhật danh sách sách đã chọn
}

// Hàm phân trang
function updatePagination() {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    const totalPages = Math.ceil(filteredBooks.length / booksPerPage);

    // Nút Previous
    const prevButton = document.createElement('button');
    prevButton.innerText = 'Trước';
    prevButton.disabled = currentPage === 1;
    prevButton.onclick = () => {
        if (currentPage > 1) {
            currentPage--;
            renderBooks();
        }
    };
    pagination.appendChild(prevButton);

    // Nút cho từng trang
    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement('button');
        pageButton.innerText = i;
        pageButton.className = (i === currentPage) ? 'active' : '';
        pageButton.onclick = () => {
            currentPage = i;
            renderBooks();
        };
        pagination.appendChild(pageButton);
    }

    // Nút Next
    const nextButton = document.createElement('button');
    nextButton.innerText = 'Sau';
    nextButton.disabled = currentPage === totalPages;
    nextButton.onclick = () => {
        if (currentPage < totalPages) {
            currentPage++;
            renderBooks();
        }
    };
    pagination.appendChild(nextButton);
}

// Hàm tìm kiếm sách
function filterBooks() {
    const searchQuery = document.getElementById('bookSearch').value.toLowerCase();
    filteredBooks = originalBooks.filter(book => book.title.toLowerCase().includes(searchQuery));

    // Cập nhật danh sách sách để hiển thị
    currentPage = 1; // Reset trang hiện tại
    renderBooks(); // Render sách đã lọc
}

// Hàm hiển thị sách đã chọn
function renderSelectedBooks() {
    const selectedList = document.getElementById('selectedList');
    selectedList.innerHTML = ''; // Xóa danh sách hiện tại

    if (selectedBooks.length === 0) {
        selectedList.innerHTML = '<p>Không có sách nào được chọn.</p>';
        return;
    }

    selectedBooks.forEach(bookId => {
        const book = originalBooks.find(b => b.id === bookId);
        const selectedItem = document.createElement('div');
        selectedItem.className = 'selected-item';
        selectedItem.innerHTML = `
            ${book.title} <button style="padding:5px;color:red ; background-color:transparent;font-weight:bold, font-size:12px" onclick="removeSelectedBook('${bookId}')">x</button>
        `;
        selectedList.appendChild(selectedItem);
    });
}

// Hàm xóa sách đã chọn
function removeSelectedBook(bookId) {
    // Kiểm tra nếu bookId tồn tại trong mảng selectedBooks
    if (selectedBooks.includes(bookId)) {
        selectedBooks = selectedBooks.filter(id => id !== bookId); // Xóa ID sách khỏi mảng đã chọn
        const checkbox = document.getElementById(`book_${bookId}`);
        if (checkbox) {
            checkbox.checked = false; // Bỏ tick cho checkbox
        }
        renderSelectedBooks(); // Render lại danh sách sách đã chọn
    }
}
// Khởi động hiển thị sách
document.addEventListener('DOMContentLoaded', () => {
    renderBooks();
}); 
        </script>


        </form>
    </div>
    </div>
    </div>
</body>

</html>