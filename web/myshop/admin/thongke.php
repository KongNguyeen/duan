<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "myshop";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql = "
    SELECT p.name AS product_name, SUM(od.quantity) AS total_quantity
    FROM order_details od
    JOIN products p ON od.product_id = p.id
    GROUP BY p.id
    ORDER BY total_quantity DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê sản phẩm</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/thongke.css">
</head>
<body>
<div class="sidebar">
    <h2>Admin Dashboard</h2>
    <a href="manage_product.php">Quản lý sản phẩm</a>
    <a href="manage_categories.php">Quản lý danh mục</a>
    <a href="manage_orders.php">Quản lý đơn hàng</a>
    <a href="manage_users.php" >Quản lý người dùng</a>
    <a href="thongke.php" >Thống kê sản phẩm</a>
    <a href="logout.php" class="btn btn-danger">Đăng xuất</a>
</div>

<div class="content">
    <h1>Thống kê sản phẩm được mua nhiều nhất</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Tổng số lượng</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_quantity']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">Không có dữ liệu.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>
