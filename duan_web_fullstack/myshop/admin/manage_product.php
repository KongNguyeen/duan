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
    die("Connection failed: " . $conn->connect_error);
}

// Cập nhật truy vấn SQL để bao gồm thông tin mô tả
$sql = "SELECT products.*, categories.name as category_name 
        FROM products 
        JOIN categories ON products.category_id = categories.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/manage_product.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Dashboard</h2>
    <a href="manage_product.php">Quản lý sản phẩm</a>
    <a href="manage_categories.php">Quản lý danh mục</a>
    <a href="manage_orders.php">Quản lý đơn hàng</a>
    <a href="manage_users.php">Quản lý người dùng</a>
    <a href="thongke.php">Thống kê sản phẩm</a>
    <a href="logout.php" class="btn btn-danger">Đăng xuất</a>
</div>

<div class="content">
    <h1>Quản lý sản phẩm</h1>
    <a href="add_product.php" class="btn btn-primary">Thêm sản phẩm mới</a>
    <hr>

    <table class="table">
        <thead>
            <tr>
                <th>Tên</th>
                <th>Giá</th>
                <th>Loại</th>
                <th>Số lượng</th> 
                <th>Hình ảnh</th>
                <th>Mô tả</th> 
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td> 
                    <td><img src="../images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" width="100"></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Chỉnh sửa</a>
                        <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>
