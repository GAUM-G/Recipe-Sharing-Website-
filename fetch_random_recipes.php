<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include "config.php"; // Database connection

$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($category) {
    $sql = "SELECT id, title, image, steps, benefits, contents FROM recipes WHERE category = ? ORDER BY RAND() LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id, title, image, steps, benefits, contents FROM recipes ORDER BY RAND() LIMIT 5";
    $result = $conn->query($sql);
}

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
