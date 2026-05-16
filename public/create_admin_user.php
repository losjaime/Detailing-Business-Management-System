<?php
require_once __DIR__ . '/../src/models/DataLayer.php';

$dataLayer = new DataLayer();

$username = 'admin';
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$conn = $dataLayer->getConnection();
$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

if ($stmt) {
    $stmt->bind_param("ss", $username, $hashedPassword);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Admin user created successfully.";
    } else {
        echo "Failed to create admin user. User may already exist.";
    }

    $stmt->close();
} else {
    echo "Statement preparation failed.";
}
?>
