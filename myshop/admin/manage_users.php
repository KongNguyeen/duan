<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include '../config/db.php';
$sql_admin = "SELECT * FROM admin";
$result_admin = $conn->query($sql_admin);
$sql_user = "SELECT * FROM user";
$result_user = $conn->query($sql_user);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/manage_users.css">
</head>
<body>
<div class="sidebar">
    <h2>Admin Dashboard</h2>
    <a href="manage_product.php">Quản lý sản phẩm</a>
    <a href="manage_categories.php">Quản lý danh mục</a>
    <a href="manage_orders.php">Quản lý đơn hàng</a>
    <a href="manage_users.php" class="active">Quản lý người dùng</a>
    <a href="thongke.php">Thống kê sản phẩm</a>
    <a href="logout.php" class="btn btn-danger">Đăng xuất</a>
</div>
<div class="content">
    <h1>Quản lý người dùng</h1>
    <div>
        <a href="add_admin.php" class="btn btn-primary">Thêm Quản trị viên mới</a>
    </div>
    <h2>Người dùng quản trị</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_admin->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <a href="edit_admin.php?type=admin&id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Chỉnh sửa</a>
                        <a href="delete_admin.php?type=admin&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <h2>Người dùng thường xuyên</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Registration Date</th>
                <th>Last Login</th>
                <th>Status</th>
                <th>Additional Info</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_user->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo htmlspecialchars($row['registration_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['last_login']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['additional_info']); ?></td>
                    <td>
                        <a href="edit_user.php?type=user&id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Chỉnh sửa</a>
                        <a href="delete_user.php?type=user&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php $conn->close(); ?>
