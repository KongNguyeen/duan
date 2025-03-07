<?php
include '../config/db.php';
$id = $_GET['id'];

$sql = "SELECT image FROM products WHERE id=$id";
$result = $conn->query($sql);
$product = $result->fetch_assoc();
$image = $product['image'];
$target = "../images/" . $image;

$sql = "DELETE FROM products WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    if (file_exists($target)) {
        unlink($target);
    }
    header('Location: manage_product.php');
    exit();
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
