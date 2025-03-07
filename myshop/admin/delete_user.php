<?php
include '../config/db.php';

$type = $_GET['type']; 
$id = $_GET['id'];

$table = ($type === 'admin') ? 'admin' : 'user';

$sql = "DELETE FROM $table WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: manage_users.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();
?>
