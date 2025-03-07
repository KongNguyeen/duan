<?php

session_start();


$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "myshop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? 1 : 0;

    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {
            $_SESSION['user_type'] = 'admin';
            $_SESSION['email'] = $email; 
            header("Location: admin/manage_product.php");
            exit();
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $sql = "SELECT * FROM user WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($password === $row['password']) {
                $_SESSION['user_type'] = 'user';
                $_SESSION['email'] = $email; 
                header("Location: index.php"); 
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/login.css">
    <title>Login</title>
</head>
<body>
    <div class="login">
        <img src="./images/login-bg.png" alt="login image" class="login__img">

        <form action="login.php" method="POST" class="container">
            <h1 class="login__title">Đăng nhập</h1>

            <?php if ($error): ?>
                <p class="login__error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <p class="login__success"><?php echo htmlspecialchars($_GET['success']); ?></p>
            <?php endif; ?>

            <div class="login__content">
                <div class="login__box">
                    <i class="ri-user-3-line login__icon"></i>
                    <div class="login__box-input">
                        <input type="email" name="email" required class="login__input" id="login-email" placeholder=" ">
                        <label for="login-email" class="login__label">Email</label>
                    </div>
                </div>

                <div class="login__box">
                    <i class="ri-lock-2-line login__icon"></i>
                    <div class="login__box-input">
                        <input type="password" name="password" required class="login__input" id="login-pass" placeholder=" ">
                        <label for="login-pass" class="login__label">Mật khẩu</label>
                        <i class="ri-eye-off-line login__eye" id="login-eye"></i>
                    </div>
                </div>
            </div>

            <div class="login__check">
                <div class="login__check-group">
                    <input type="checkbox" name="remember" class="login__check-input" id="login-check">
                    <label for="login-check" class="login__check-label">Remember me</label>
                </div>

                <a href="#" class="login__forgot">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="login__button">Đăng nhập</button>

            <p class="login__register">
                Don't have an account? <a href="register.php">Đăng ký</a>
            </p>
        </form>
    </div>
</body>
</html>
