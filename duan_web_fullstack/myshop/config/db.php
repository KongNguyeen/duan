<?php


// Thông tin kết nối cơ sở dữ liệu
$servername = "localhost";  // Thay đổi nếu bạn sử dụng một máy chủ cơ sở dữ liệu khác
$username = "root";         // Thay đổi theo tên người dùng MySQL của bạn
$password = "";             // Thay đổi theo mật khẩu MySQL của bạn
$dbname = "myshop";         // Tên cơ sở dữ liệu

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
