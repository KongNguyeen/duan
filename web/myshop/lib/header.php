<header>
    <div class="nav container">
        <a href="index.php" class="logo">
            <img src="./images/logo.png" alt="Logo" class="logo-img" > Trang chủ
        </a>
        <a href="checkout.php" id="cart-icon-link">
            <i class='bx bx-shopping-bag' id="cart-icon"></i>
        </a>
        
        <div class="user-info">
            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'user'): ?>
                <p class="profile-link">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>!</p>
                <a href="profile.php" class="profile-link">Thông tin khách hàng</a>
                <a href="gioithieu.html" class="profile-link">Giới thiệu</a>
                <a href="index.php?logout=true" class="logout-link">Đăng xuất</a>
            <?php else: ?>
                <p>Welcome, Guest!</p>
                <a href="login.php" class="login-link">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</header>
