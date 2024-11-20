Quản Lý Thư Viện

Giới Thiệu

Dự án Quản Lý Thư Viện là một hệ thống quản lý sách, bạn đọc, và các giao dịch mượn/trả sách. Hệ thống được xây dựng bằng PHP và JavaScript, với MySQL làm cơ sở dữ liệu.

Tính Năng Chính

	1.	Quản lý sách: Thêm, sửa, xóa và tìm kiếm thông tin sách.
	2.	Quản lý bạn đọc: Lưu trữ thông tin và lịch sử mượn trả sách của bạn đọc.
	3.	Quản lý mượn/trả: Theo dõi trạng thái mượn trả sách, hỗ trợ thông báo (nếu cần).

Công Nghệ Sử Dụng

	•	PHP: Xử lý logic backend và kết nối cơ sở dữ liệu.
	•	JavaScript: Tương tác giao diện người dùng.
	•	MySQL: Lưu trữ dữ liệu sách, bạn đọc và lịch sử giao dịch.
	•	HTML/CSS: Tạo giao diện người dùng.

Cách Cài Đặt

	1.	Yêu Cầu Hệ Thống
	•	PHP >= 7.4
	•	MySQL >= 5.7
	•	Apache hoặc Nginx
	2.	Cài Đặt Dự Án
a. Clone dự án:

git clone <link_github>
cd library-management

b. Cài đặt cơ sở dữ liệu:
	•	Tạo cơ sở dữ liệu MySQL với lệnh:

CREATE DATABASE library_management;


	•	Import file database/library.sql:

mysql -u <username> -p library_management < database/library.sql


c. Cấu hình kết nối cơ sở dữ liệu trong file src/config.php:

<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'library_management');
?>

d. Chạy ứng dụng:

php -S localhost:8000

e. Truy cập ứng dụng tại: http://localhost:8000
