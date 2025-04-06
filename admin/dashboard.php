<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>

<h2>Admin Dashboard</h2>
<a href="add_topper.php">Add Topper</a><br>
<a href="upload_slider.php">Upload Slider Image</a><br>
<a href="logout.php">Logout</a>
