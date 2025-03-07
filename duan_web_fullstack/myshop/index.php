<?php
session_start();

if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'user') {
        echo json_encode(['success' => false, 'message' => 'Người dùng chưa đăng nhập']);
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

    $product_id = intval($_POST['product_id']);
    $user_email = $_SESSION['email'];
    $quantity = 1;

    $sql = "SELECT * FROM cart WHERE user_email = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $user_email, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_email = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $user_email, $product_id);
    } else {
        $sql = "INSERT INTO cart (user_email, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sii', $user_email, $product_id, $quantity);
    }
    $stmt->execute();

    $stmt->close();
    $conn->close();

    echo json_encode(['success' => true]);
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

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/index.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        .background {
            background-image: url("./images/1.jpg"); 
            background-position: center; 
            background-size: cover; 
            background-repeat: no-repeat;
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100vw; 
            height: 100vh;
            z-index: -1; 
        }
        h1{
            text-align: center;
        }
        .product-box {
    position: relative;
    background: #fff;
    border-radius: 8px; 
    overflow: hidden; 
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column; 
    align-items: center; 
    padding: 1rem; 
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-box:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.product-img {
    width: 88%;
    height: auto; 
    margin-bottom: 0.5rem;
    transition: transform 0.3s;
}

.product-img:hover {
    transform: scale(1.1);
}

.product-title {
    font-size: 1.1rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    text-align: center;
}

.price {
    font-weight: 500;
    text-align: center; 
}

    </style>
</head>
<body>
    <div class="background"></div>

    <?php include './lib/header.php'; ?>

    <section class="shop container">
        <h1>Sản phẩm</h1>
        <div class="shop-content">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-box">';
                    echo '<img src="./images/' . htmlspecialchars($row["image"]) . '" alt="" class="product-img">';
                    echo '<h2 class="product-title">' . htmlspecialchars($row["name"]) . '</h2>';
                    echo '<span class="price">$' . htmlspecialchars($row["price"]) . '</span>';
                    echo '<button class="btn-buy" data-id="' . htmlspecialchars($row["id"]) . '">Add to Cart</button>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products available.</p>';
            }
            $conn->close();
            ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="./js/index.js"></script>
    <?php include './lib/footer.php'; ?>

</body>
</html>
