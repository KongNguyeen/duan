<?php
include '../config/db.php';

$id = $_GET['id'];

if (!isset($id) || !is_numeric($id)) {
    echo "ID không hợp lệ.";
    exit();
}

$sql = "DELETE FROM admin WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: manage_users.php');
        exit();
    } else {
        echo "Lỗi khi xóa: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Lỗi chuẩn bị câu lệnh: " . $conn->error;
}

$conn->close();
?>
