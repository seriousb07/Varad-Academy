<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    if ($user == "varad#academy" && $pass == "varad#academy@2020") {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
    } else {
        echo "<script>alert('Invalid credentials');</script>";
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>
