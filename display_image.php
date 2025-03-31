<?php
$conn = new mysqli("localhost", "root", "", "getsetrecipe");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$sql = "SELECT image FROM recipes WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    if (!$row['image']) {
        die("No image data found for ID: $id");
    }

    header("Content-Type: image/jpeg");
    echo $row['image'];
} else {
    die("Image not found for ID: $id");
}

$conn->close();
?>
