<?php
session_start(); 


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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Tất cả các trường đều bắt buộc.";
    } elseif ($password !== $confirm_password) {
        $error = "Mật khẩu không khớp.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Định dạng email không hợp lệ.";
    } elseif (strlen($password) < 8) { 
        $error = "Mật khẩu phải dài ít nhất 8 ký tự.";
    } else {
        
        $sql = "SELECT * FROM user WHERE email = ?";
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("s", $email); 
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) { 
            $error = "Email đã được đăng ký.";
        } else {
            
            $sql = "INSERT INTO user (email, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql); 
            $stmt->bind_param("ss", $email, $password); 
            if ($stmt->execute()) { 
                $success = "Đăng ký thành công. <a href='login.php'>Đăng nhập tại đây</a>";
            } else {
                $error = "Lỗi: Không thể đăng ký.";
            }
        }
        $stmt->close(); 
    }
}

$conn->close(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/register.css">
    <title>Đăng ký</title>
</head>
<body>
    <div class="login">
        <img src="./images/login-bg.png" alt="login image" class="login__img">

        <form action="register.php" method="POST" class="container">
            <h1 class="login__title">Đăng ký</h1>

            <?php if ($error): ?>
                <p class="login__error"><?php echo htmlspecialchars($error); ?></p> <!-- Hiển thị thông báo lỗi nếu có -->
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="login__success"><?php echo $success; ?></p> <!-- Hiển thị thông báo thành công nếu có -->
            <?php endif; ?>

            <div class="login__content">
                <div class="login__box">
                    <i class="ri-user-3-line login__icon"></i>
                    <div class="login__box-input">
                        <input type="email" name="email" required class="login__input" id="register-email" placeholder=" ">
                        <label for="register-email" class="login__label">Email</label>
                    </div>
                </div>

                <div class="login__box">
                    <i class="ri-lock-2-line login__icon"></i>
                    <div class="login__box-input">
                        <input type="password" name="password" required class="login__input" id="register-pass" placeholder=" ">
                        <label for="register-pass" class="login__label">Mật khẩu</label>
                    </div>
                </div>

                <div class="login__box">
                    <i class="ri-lock-2-line login__icon"></i>
                    <div class="login__box-input">
                        <input type="password" name="confirm_password" required class="login__input" id="register-confirm-pass" placeholder=" ">
                        <label for="register-confirm-pass" class="login__label">Xác nhận mật khẩu</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="login__button">Đăng ký</button>

            <p class="login__register">
                Đã có tài khoản? <a href="login.php">Đăng nhập tại đây</a>
            </p>
        </form>
    </div>
</body>
</html>
