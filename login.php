<?php
session_start();
include "db.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $username = trim($_POST['username']); 
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo "Username and password cannot be empty!";
        exit();
    }

    $sql = "SELECT id, password FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id']; 
            $_SESSION['username'] = $username; 
            echo "success"; 
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "User not found!";
    }

    $stmt->close();
    $conn->close();
}
?>
