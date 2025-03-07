<?php
include '../config/db.php'; // Đảm bảo bạn đã kết nối cơ sở dữ liệu ở đây

$id = (int)$_GET['id']; // Kiểm tra và chuyển đổi id thành số nguyên

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $name = $conn->real_escape_string($_POST['name']);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category']; // Thay đổi thành số nguyên
    $quantity = (int)$_POST['quantity']; // Thêm dòng này để lấy số lượng
    $description = $conn->real_escape_string($_POST['description']); // Thêm dòng này để lấy mô tả
    $image = $_FILES['image']['name'];
    
    // Cập nhật hình ảnh nếu có
    if ($image) {
        $target = "../images/" . basename($image);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $sql = "UPDATE products SET name='$name', price='$price', category_id='$category_id', quantity='$quantity', description='$description', image='$image' WHERE id=$id";
        } else {
            echo "Failed to upload image.";
            exit();
        }
    } else {
        $sql = "UPDATE products SET name='$name', price='$price', category_id='$category_id', quantity='$quantity', description='$description' WHERE id=$id";
    }

    // Thực thi câu lệnh SQL
    if ($conn->query($sql) === TRUE) {
        header('Location: manage_product.php');
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Lấy thông tin sản phẩm để hiển thị trong form
$sql = "SELECT * FROM products WHERE id=$id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    echo "Product not found.";
    exit();
}

// Lấy danh sách các danh mục để hiển thị trong dropdown
$categories_sql = "SELECT * FROM categories";
$categories_result = $conn->query($categories_sql);
$categories = [];
if ($categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="content">
        <h1>Edit Product</h1>
        <form action="edit_product.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Tên sản phẩm:</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Giá:</label>
                <input type="number" name="price" id="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Loại hàng:</label>
                <select name="category" id="category" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php if ($product['category_id'] == $category['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Số lượng:</label>
                <input type="number" name="quantity" id="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Mô tả:</label>
                <textarea name="description" id="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea> <!-- Thêm trường mô tả -->
            </div>
            <div class="form-group">
                <label for="image">Hình ảnh sản phẩm:</label>
                <input type="file" name="image" id="image">
                <?php if ($product['image']): ?>
                    <img src="../images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 100px;">
                <?php endif; ?>
            </div>
            <button type="submit">Cập nhập sản phẩm</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
