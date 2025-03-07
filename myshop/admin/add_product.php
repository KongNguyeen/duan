<?php
include '../config/db.php'; // Đảm bảo bạn đã kết nối cơ sở dữ liệu ở đây

// Lấy danh sách danh mục từ cơ sở dữ liệu
$categories = [];
$categorySql = "SELECT id, name FROM categories";
$categoryResult = $conn->query($categorySql);

if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Xử lý biểu mẫu khi người dùng gửi dữ liệu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Nếu nút "Cancel" được nhấp, chuyển hướng đến trang quản lý sản phẩm
    if (isset($_POST['cancel'])) {
        header('Location: manage_product.php');
        exit();
    }

    // Lấy dữ liệu từ biểu mẫu và kiểm tra sự tồn tại của các giá trị
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $categoryId = $_POST['category'];
    $quantity = (int)$_POST['quantity']; // Thêm dòng này để lấy số lượng
    $description = trim($_POST['description']); // Thêm dòng này để lấy mô tả
    $image = '';

    // Kiểm tra xem người dùng có chọn ảnh không
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target = realpath(__DIR__ . "/../images/") . "/" . $image;

        // Kiểm tra lỗi tải lên
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo "File upload error: " . $_FILES['image']['error'];
            exit();
        }

        // Di chuyển tệp tải lên đến thư mục đích
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            echo "Failed to upload image.";
            exit();
        }
    }

    // Chuẩn bị câu lệnh SQL để thêm sản phẩm
    $sql = "INSERT INTO products (name, price, category_id, quantity, description, image) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Nếu không có ảnh, đặt giá trị cho trường ảnh là NULL
        $image = empty($image) ? null : $image;
        $stmt->bind_param("sissss", $name, $price, $categoryId, $quantity, $description, $image);
        if ($stmt->execute()) {
            header('Location: manage_product.php');
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="content">
        <h1>Add New Product</h1>
        <form action="add_product.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" name="price" id="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select name="category" id="category" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" required></textarea> 
            </div>
            <div class="form-group">
                <label for="image">Product Image (optional):</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>
            <button type="submit">Add Product</button>
            <button type="submit" name="cancel" value="1">Cancel</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
