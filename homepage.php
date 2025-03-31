<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
echo "Welcome, " . $_SESSION['username'] . "! You are now logged in.";

// Connect to the database
$conn = new mysqli("localhost", "root", "", "getsetrecipe");

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch 5 random recipes
$sql = "SELECT id, title, image FROM recipes ORDER BY RAND() LIMIT 5";
$result = $conn->query($sql);

echo "<div class='recipe-cards-container'>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recipe_id = $row['id'];
        $recipe_title = $row['title'];
        $recipe_image = "recidata/" . $row['image']; // Correct relative path

        echo "<div class='recipe-card'>";
        echo "<a href='recipe.php?id=$recipe_id'>";
        echo "<img src='$recipe_image' alt='$recipe_title' class='recipe-image' onerror=\"this.onerror=null; this.src='recidata/uploads/default.png';\">";
        echo "<h3 class='recipe-title'>$recipe_title</h3>";
        echo "</a>";
        echo "</div>";
    }
} else {
    echo "<p>No recipes found.</p>";
}
echo "</div>";

$conn->close();
?>
