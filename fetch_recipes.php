<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow access from any origin
header("Access-Control-Allow-Methods: GET");

// Database connection
include "config.php"; // Ensure this file contains the correct DB credentials

// Fetch recipes from the database
$sql = "SELECT id, title, image, steps, benefits, contents FROM recipes ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $recipes = array();
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
    echo json_encode($recipes);
} else {
    echo json_encode(["message" => "No recipes found"]);
}

$conn->close();
?>
