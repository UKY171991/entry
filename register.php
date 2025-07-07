<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "Registration successful. <a href='index.php'>Login Here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<form method="POST">
    <input type="text" name="username" required placeholder="Enter Username" class="border">
    <input type="password" name="password" required placeholder="Enter Password" class="border">
    <button type="submit">Register</button>
</form>
