<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../config/db.php';

$sql = "SELECT * FROM categories";
$result = $conn->query($sql);

if (!$result) {
    die("Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/manage_categories.css">

</head>
<body>

<div class="sidebar">
    <h2>Admin Dashboard</h2>
    <a href="manage_product.php">Quản lý sản phẩm</a>
    <a href="manage_categories.php" class="active">Quản lý danh mục</a>
    <a href="manage_orders.php">Quản lý đơn hàng</a>
    <a href="manage_users.php">Quản lý người dùng</a>
    <a href="thongke.php">Thống kê sản phẩm</a>
    <a href="logout.php" class="btn btn-danger">Đăng xuất</a>
</div>

<div class="content">
    <h1>Quản lý danh mục</h1>
    <a href="add_category.php" class="btn btn-primary">Thêm danh mục mới</a>
    <hr>

    <?php if (isset($_GET['message'])): ?>
        <div class="alert"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>
                        <a href="edit_category.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Chỉnh sửa</a>
                        <a href="delete_category.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
