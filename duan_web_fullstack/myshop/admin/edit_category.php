<?php
include '../config/db.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];

    $sql = "UPDATE categories SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            header('Location: manage_categories.php');
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    $sql = "SELECT * FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="content">
        <h1>Edit Category</h1>
        <form action="edit_category.php?id=<?php echo $id; ?>" method="post">
            <div class="form-group">
                <label for="name">Category Name:</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
            </div>
            <button type="submit">Update Category</button>
        </form>
    </div>
</body>
</html>
