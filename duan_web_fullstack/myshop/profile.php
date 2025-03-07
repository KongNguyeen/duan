<?php
session_start(); 

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
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

$error = ''; 
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $additional_info = $_POST['additional_info'];

    
    $sql = "UPDATE user SET name = ?, phone = ?, address = ?, additional_info = ? WHERE email = ?";
    $stmt = $conn->prepare($sql); 
    if (!$stmt) {
        die("Prepare failed: " . $conn->error); 
    }
    $stmt->bind_param("sssss", $name, $phone, $address, $additional_info, $_SESSION['email']); 

    if ($stmt->execute()) {
        header("Location: profile.php?message=Hồ sơ đã được cập nhật thành công."); 
        exit();
    } else {
        $error = "Lỗi: Không thể cập nhật hồ sơ. " . $stmt->error; 
    }
    $stmt->close(); 
}


$email = $_SESSION['email'];
$sql = "SELECT * FROM user WHERE email = ?";
$stmt = $conn->prepare($sql); 
if (!$stmt) {
    die("Prepare failed: " . $conn->error); 
}
$stmt->bind_param("s", $email); 
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error); 
}
$result = $stmt->get_result(); 
if (!$result) {
    die("Get result failed: " . $stmt->error); 
}
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found."; 
    exit(); 
}

$order_sql = "
    SELECT o.id AS order_id, o.order_date, o.total, o.status, o.payment_method, o.address AS order_address, 
           od.product_id, od.quantity, od.price, p.name AS product_name, p.price AS product_price, p.image AS product_image
    FROM orders o
    JOIN order_details od ON o.id = od.order_id
    JOIN products p ON od.product_id = p.id
    WHERE o.user_email = ?
    ORDER BY o.order_date DESC
";
$order_stmt = $conn->prepare($order_sql); 
if (!$order_stmt) {
    die("Prepare failed: " . $conn->error); 
}
$order_stmt->bind_param("s", $email); 
if (!$order_stmt->execute()) {
    die("Execute failed: " . $order_stmt->error); 
}
$order_result = $order_stmt->get_result();
if (!$order_result) {
    die("Get result failed: " . $order_stmt->error);
}

$conn->close(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="./css/styles.css"> 
    <link rel="stylesheet" href="./css/profile.css"> 

    <style>
        body {
            font-family: 'Arial', sans-serif; 
            background-image: url('./images/1.jpg');
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;
            background-attachment: fixed; 
            color: #333; 
            margin: 0;
            padding: 20px;
            transition: background-color 0.3s, color 0.3s;
            margin-top: 5em; 
        }

        .profile-container .edit-form {
            display: none;
        }

        .profile-container.edit-mode .edit-form {
            display: block;
        }

        .profile-container .profile-info {
            display: block;
        }

        .profile-container.edit-mode .profile-info {
            display: none;
        }
        
        header {
    background-color: rgba(226, 20, 106, 0.9);
    padding: 3px 15px;
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: nowrap;
}

.logo {
    font-size: 1.2rem;
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.logo-img {
    height: 30px;
    margin-right: 8px;
}

.user-info {
    display: flex;
    align-items: center;
}

.profile-link, .login-link, .logout-link {
    color: white;
    text-decoration: none;
    margin-left: 10px;
    font-size: 0.9rem;
}

.profile-link:hover, .login-link:hover, .logout-link:hover {
    color: #ffcc00;
}

#cart-icon-link {
    display: flex;
    align-items: center;
}

#cart-icon {
    font-size: 1.5rem;
    cursor: pointer;
}

#cart-icon:hover {
    background: #171427;
    color: #fff;
    border-radius: 0.2rem;
    transition: background 0.3s, color 0.3s;
}



    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>
<?php include './lib/header.php'; ?>
<body>



    <section class="profile container profile-container">
        <h2 class="section-title">Thông tin của bạn</h2>

        <?php if ($error): ?>
            <p class="profile__error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <div class="profile-info">
            <p><strong>Tên người nhận:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <p><strong>Địa chỉ nhận:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            <p><strong>Trạng thái:</strong> <?php echo htmlspecialchars($user['status']); ?></p>
            <p><strong>Thông tin bổ sung:</strong> <?php echo htmlspecialchars($user['additional_info']); ?></p>
            <button class="profile__button" onclick="toggleEdit()">Chỉnh sửa thông tin</button>
        </div>

        <div class="edit-form">
            <form action="profile.php" method="POST">
                <div class="profile__field">
                    <label for="name">Tên người nhận:</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="profile__field">
                    <label for="phone">Số điện thoại:</label>
                    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>
                <div class="profile__field">
                    <label for="address">Địa chỉ nhận:</label>
                    <textarea name="address" id="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                <div class="profile__field">
                    <label for="additional_info">Thông tin bổ sung:</label>
                    <textarea name="additional_info" id="additional_info"><?php echo htmlspecialchars($user['additional_info']); ?></textarea>
                </div>
                <button type="submit" name="update" class="profile__button">Cập nhật thông tin</button>
                <button type="button" class="profile__button" onclick="toggleEdit()">Thoát</button>
            </form>
        </div>

        <h2 class="section-title">Đơn hàng đã đặt</h2>

        <?php if ($order_result->num_rows > 0): ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Ngày đặt hàng</th>
                        <th>Tổng cộng</th>
                        <th>Trạng thái</th>
                        <th>Phương thức thanh toán</th>
                        <th>Địa chỉ giao hàng</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Hình ảnh sản phẩm</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $order_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo number_format($order['total'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($order['order_address'])); ?></td>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                            <td><?php echo number_format($order['price'], 2); ?></td>
                            <td>
                                <?php if (!empty($order['product_image'])): ?>
                                    <img src="./images/<?php echo htmlspecialchars($order['product_image']); ?>" alt="<?php echo htmlspecialchars($order['product_name']); ?>">
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Không có đơn hàng nào.</p>
        <?php endif; ?>

    </section>
    <script src="./js/profile.js"></script>
    <?php include './lib/footer.php'; ?>

</body>
</html>
