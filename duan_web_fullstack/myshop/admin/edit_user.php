<?php
include '../config/db.php';

$type = $_GET['type']; 
$id = $_GET['id'];

$table = ($type === 'admin') ? 'admin' : 'user';
$title = ($type === 'admin') ? 'Admin User' : 'Regular User';

$sql = "SELECT * FROM $table WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $registration_date = $_POST['registration_date'];
    $last_login = $_POST['last_login'];
    $status = $_POST['status'];
    $additional_info = $_POST['additional_info'];

    $sql = "UPDATE $table SET name = ?, email = ?, password = ?, phone = ?, address = ?, registration_date = ?, last_login = ?, status = ?, additional_info = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssssssi", $name, $email, $password, $phone, $address, $registration_date, $last_login, $status, $additional_info, $id);
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
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="content">
        <h1>Edit <?php echo htmlspecialchars($title); ?></h1>
        <form action="edit_user.php?type=<?php echo htmlspecialchars($type); ?>&id=<?php echo htmlspecialchars($id); ?>" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" value="<?php echo htmlspecialchars($user['password']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($user['address']); ?>">
            </div>
            <div class="form-group">
                <label for="registration_date">Registration Date:</label>
                <input type="date" name="registration_date" id="registration_date" value="<?php echo htmlspecialchars($user['registration_date']); ?>">
            </div>
            <div class="form-group">
                <label for="last_login">Last Login:</label>
                <input type="datetime-local" name="last_login" id="last_login" value="<?php echo htmlspecialchars($user['last_login']); ?>">
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <input type="text" name="status" id="status" value="<?php echo htmlspecialchars($user['status']); ?>">
            </div>
            <div class="form-group">
                <label for="additional_info">Additional Info:</label>
                <textarea name="additional_info" id="additional_info"><?php echo htmlspecialchars($user['additional_info']); ?></textarea>
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
