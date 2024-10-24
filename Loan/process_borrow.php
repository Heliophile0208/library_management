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

// Lấy thông tin từ form
$reader_id = $_POST['reader_id']; // Lấy thông tin ReaderID
$book_ids = $_POST['book_id']; // Mảng các BookID được chọn

// Lấy thông tin thẻ thư viện của người dùng
$sql = "SELECT CardRank FROM LibraryCards WHERE ReaderID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $reader_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$cardRank = $row['CardRank'] ?? 'Basic'; // Mặc định là Basic nếu không có thông tin

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
    echo "Bạn chỉ có thể mượn tối đa $maxBooks quyển sách. Bạn đã mượn $loaned_count quyển.";
} else {
    // Tiến hành ghi vào cơ sở dữ liệu thông tin mượn sách
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
$conn->close();
?>