<?php
header("Content-Type: application/json");
include 'db.php'; // Ensure db.php correctly initializes $conn

$searchQuery = isset($_GET['query']) ? $_GET['query'] : '';

if ($searchQuery) {
    $sql = "SELECT title, image, video, steps, benefits, contents, 
                   COALESCE(uploaded_by, 'Unknown') AS uploaded_by 
            FROM recipes WHERE title LIKE ? OR contents LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%$searchQuery%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();

    $recipes = [];
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }

    echo json_encode($recipes);
} else {
    echo json_encode([]);
}

$conn->close();
?>
