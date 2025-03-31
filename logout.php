<?php
session_start();
session_destroy();
setcookie(session_name(), "", time() - 3600, "/"); // Remove session cookie

// Redirect to homepage
header("Location: homepage.html");
exit();
?>
