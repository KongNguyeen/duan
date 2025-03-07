<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['email'];

$servername = "localhost";
$username = "root"; 
$password = "";    
$dbname = "myshop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$user_info = [];
$sql = "SELECT name, phone, address FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
}

$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $user_info = $row;
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $quantity = intval($quantity);
            if ($quantity > 0) {
                $sql = "UPDATE cart SET quantity = ? WHERE user_email = ? AND product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isi", $quantity, $user_email, $product_id);
                $stmt->execute();
                $stmt->close();
            } else {
                $sql = "DELETE FROM cart WHERE user_email = ? AND product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $user_email, $product_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    } elseif (isset($_POST['remove_item'])) {
        $product_id = $_POST['remove_item'];
        $sql = "DELETE FROM cart WHERE user_email = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $user_email, $product_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['checkout'])) {
        $payment_method = $_POST['payment_method'];
        $conn->begin_transaction();
        
        try {
            $total = 0;
            $cart_items = [];
            $sql = "SELECT p.id, p.name, p.price, c.quantity
                    FROM cart c
                    JOIN products p ON c.product_id = p.id
                    WHERE c.user_email = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
            }

            $stmt->bind_param("s", $user_email);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $cart_items[] = $row;
                $total += $row['price'] * $row['quantity'];
            }
            $stmt->close();

            if (count($cart_items) === 0) {
                throw new Exception("Your cart is empty.");
            }

            // Thêm đơn hàng vào bảng orders
            $sql = "INSERT INTO orders (user_email, total, name, phone, address, payment_method) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
            }

            $stmt->bind_param("sdssss", $user_email, $total, $user_info['name'], $user_info['phone'], $user_info['address'], $payment_method);
            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();

            // Thêm chi tiết đơn hàng vào bảng order_details
            $sql = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
            }

            foreach ($cart_items as $item) {
                $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
                $stmt->execute();
            }
            $stmt->close();

            // Xóa giỏ hàng của người dùng
            $sql = "DELETE FROM cart WHERE user_email = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
            }

            $stmt->bind_param("s", $user_email);
            $stmt->execute();
            $stmt->close();

            $conn->commit();

            $message = "Order placed successfully!";

        } catch (Exception $e) {
            $conn->rollback();
            $message = "Error: " . $e->getMessage();
        }

        $conn->close();
        header("Location: checkout.php?message=" . urlencode($message));
        exit();
    }
}

$sql = "SELECT p.id, p.name, p.price, p.image, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_email = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
}

$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/checkout.css">

    <link rel="stylesheet" href="./css/checkout.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>


.background {
    background-image: url("./images/1.jpg");
    background-size: cover;
    background-position: center;
}

.giua {
    margin: 2rem auto;
    max-width: 800px;
    padding: 1rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}


header {
    background-color: rgba(226, 20, 106, 0.9);
    padding: 5px 10px;
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
}

.logo {
    font-size: 1.1rem;
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.logo-img {
    height: 25px;
    margin-right: 6px;
}

.user-info {
    display: flex;
    align-items: center;
}

.profile-link, .login-link, .logout-link {
    color: white;
    text-decoration: none;
    margin-left: 8px;
    font-size: 0.85rem;
}

.profile-link:hover, .login-link:hover, .logout-link:hover {
    color: #ffcc00;
}

#cart-icon-link {
    display: flex;
    align-items: center;
}

#cart-icon {
    font-size: 1.4rem;
    cursor: pointer;
}

#cart-icon:hover {
    background: #171427;
    color: #fff;
    border-radius: 0.2rem;
    transition: background 0.3s, color 0.3s;
}


h1 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #e2146a;
}

form {
    display: flex;
    flex-direction: column;
}

.cart-box {
    display: flex;
    border-bottom: 1px solid #ddd;
    padding: 1rem 0;
    align-items: center;
}

.cart-img {
    width: 100px;
    height: auto;
    margin-right: 1rem;
}

.cart-info {
    flex: 1;
}

.cart-info h3 {
    margin: 0;
    font-size: 1.2rem;
}

.cart-info p {
    margin: 0.5rem 0;
}

input[type="number"] {
    width: 60px;
    padding: 0.25rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn-bu {
    background-color: #e2146a;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    margin: 0.5rem 0;
    transition: background-color 0.3s, transform 0.3s;
}

.btn-bu:hover {
    background-color: #d1146a;
    transform: scale(1.02);
}

.total {
    font-size: 1.2rem;
    margin: 1rem 0;
}

.total-price {
    font-weight: bold;
}

select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin: 0.5rem 0;
}

.message {
    padding: 1rem;
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    margin: 1rem 0;
}

.message {
    padding: 1rem;
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
    margin: 1rem 0;
}

    </style>
</head>
<body>
<div class="background">
<?php include './lib/header.php'; ?>
    <div class="giua">
        <h1>Checkout</h1>
        <form method="post" action="">
            <h1>Thông tin giỏ hàng</h1>
            <?php if (!empty($cart_items)): ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-box">
                        <img src="./images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-img">
                        <div class="cart-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                            <p>
                                Quantity: 
                                <input type="number" name="quantity[<?php echo htmlspecialchars($item['id']); ?>]" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="0">
                            </p>
                            <button type="submit" name="remove_item" value="<?php echo htmlspecialchars($item['id']); ?>" class="btn-bu">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="total">
                    Total: <span class="total-price">$<?php echo number_format(array_sum(array_map(function($item) {
                        return $item['price'] * $item['quantity'];
                    }, $cart_items)), 2); ?></span>
                </div>
                <button type="submit" name="update_cart" class="btn-bu">Update Cart</button>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>

            <h1>Payment Method</h1>
            <select name="payment_method" required>
                <option value="Credit Card">Thẻ tín dụng</option>
                <option value="PayPal">PayPal</option>
                <option value="Bank Transfer">Chuyển khoản ngân hàng</option>
                <option value="Cash on Delivery">Thanh toán khi nhận hàng</option>
            </select>
            
            <button type="submit" name="checkout" class="btn-bu">Place Order</button>
        </form>
        
        <?php if (isset($_GET['message'])): ?>
            <div class="message">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="./js/checkout.js"></script>

</body>
</html>
