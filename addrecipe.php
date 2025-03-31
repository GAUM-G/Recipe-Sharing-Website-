<?php
include 'db.php';
session_start(); // Start session to get the logged-in user

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $steps = $_POST['steps'];
    $benefits = $_POST['benefits'];
    $contents = $_POST['contents'];
    $uploaded_by = isset($_SESSION['username']) ? $_SESSION['username'] : 'Anonymous'; // Default to Anonymous if session not set

    // Ensure the upload directory exists
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Handle Image Upload
    $imagePath = NULL;
    if (!empty($_FILES["image"]["name"])) {
        $imageName = time() . "_" . basename($_FILES["image"]["name"]); // Unique filename
        $imagePath = $target_dir . $imageName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    // Handle Video Upload
    $videoPath = NULL;
    if (!empty($_FILES["video"]["name"])) {
        $videoName = time() . "_" . basename($_FILES["video"]["name"]); // Unique filename
        $videoPath = $target_dir . $videoName;
        move_uploaded_file($_FILES["video"]["tmp_name"], $videoPath);
    }

    // Insert into Database (Stores only relative paths)
    $stmt = $conn->prepare("INSERT INTO recipes (title, category, image, video, steps, benefits, contents, uploaded_by) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $title, $category, $imagePath, $videoPath, $steps, $benefits, $contents, $uploaded_by);
    
    if ($stmt->execute()) {
        echo "Recipe added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
