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
// Xử lý yêu cầu thay đổi trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
    }

    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_orders.php?message=Order status updated");
    exit();
}

// Lấy danh sách đơn hàng
$sql = "SELECT o.id, o.user_email, o.order_date, o.total, o.status, o.payment_method, o.address
        FROM orders o
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);

if ($result === false) {
    die("Lỗi truy vấn: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/manage_orders.css">

</head>
<body>

<div class="sidebar">
    <h2>Admin Dashboard</h2>
    <a href="manage_product.php">Quản lý sản phẩm</a>
    <a href="manage_categories.php">Quản lý danh mục</a>
    <a href="manage_orders.php" class="active">Quản lý đơn hàng</a>
    <a href="manage_users.php">Quản lý người dùng</a>
    <a href="thongke.php">Thống kê sản phẩm</a>
    <a href="logout.php" class="btn btn-danger">Đăng xuất</a>
</div>

<div class="content">
    <h1>Quản lý đơn hàng</h1>
    
    <?php if (isset($_GET['message'])): ?>
        <div class="alert"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Mã đơn hàng</th>
                <th>Email của người dùng</th>
                <th>Ngày đặt hàng</th>
                <th>Tổng cộng</th>
                <th>Phương thức thanh toán</th>
                <th>Địa chỉ giao hàng</th>
                <th>Tên sản phẩm</th>
                <th>Trạng thái</th> 
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                    <td><?php echo number_format($row['total'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['address'])); ?></td>
                    <td>
                        <?php
                        
                        $order_id = $row['id'];
                        $product_names = [];

                        $sql_products = "SELECT p.name FROM order_details od 
                                         JOIN products p ON od.product_id = p.id 
                                         WHERE od.order_id = ?";
                        $stmt_products = $conn->prepare($sql_products);
                        $stmt_products->bind_param("i", $order_id);
                        $stmt_products->execute();
                        $result_products = $stmt_products->get_result();

                        if ($result_products === false) {
                            die("Lỗi truy vấn sản phẩm: " . $stmt_products->error);
                        }

                        while ($product_row = $result_products->fetch_assoc()) {
                            $product_names[] = htmlspecialchars($product_row['name']);
                        }

                        $stmt_products->close();
                        echo implode(', ', $product_names);
                        ?>
                    </td>
                    <td>
                        <form method="POST" action="manage_orders.php" style="margin: 0;">
                            <select name="status" onchange="this.form.submit()">
                                <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Chưa giải quyết</option>
                                <option value="processing" <?php if ($row['status'] == 'processing') echo 'selected'; ?>>Đang Xử lý</option>
                                <option value="on delivery" <?php if ($row['status'] == 'on delivery') echo 'selected'; ?>>Đang giao hàng</option>
                                <option value="completed" <?php if ($row['status'] == 'completed') echo 'selected'; ?>>Hoàn thành</option>
                                <option value="cancelled" <?php if ($row['status'] == 'cancelled') echo 'selected'; ?>>Đã hủy</option>
                            </select>
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
