<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['number']);
    $email = mysqli_real_escape_string($conn, $_POST['gmail']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if username already exists
    $check_username = "SELECT * FROM users WHERE username = '$username'";
    $result_username = $conn->query($check_username);
    if ($result_username->num_rows > 0) {
        echo "<script>alert('Username already exists. Please choose a different one.'); window.history.back();</script>";
        exit();
    }

    // Check if phone number already exists
    $check_phone = "SELECT * FROM users WHERE phone = '$phone'";
    $result_phone = $conn->query($check_phone);
    if ($result_phone->num_rows > 0) {
        echo "<script>alert('Phone number already in use. Please use a different one.'); window.history.back();</script>";
        exit();
    }
    
    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result_email = $conn->query($check_email);
    if ($result_email->num_rows > 0) {
        echo "<script>alert('Email already in use. Please use a different one.'); window.history.back();</script>";
        exit();
    }

    // Debugging: Check if values are received
    echo "Received Data: <br>";
    echo "Name: $name <br>";
    echo "Username: $username <br>";
    echo "Phone: $phone <br>";
    echo "Email: $email <br>";
    echo "Password (Hashed): $password <br>";

    // Insert into database
    $sql = "INSERT INTO users (name, username, phone, email, password) VALUES ('$name', '$username', '$phone', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Sign up successful!'); window.location.href = 'homepage.html';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>