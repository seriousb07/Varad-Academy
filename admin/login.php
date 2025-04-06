<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    if ($user == "varad#academy" && $pass == "varad#academy@2020") {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>alert('Invalid credentials');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login | Varad Academy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #fff6f6;
    }

    /* Navbar */
    .navbar {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      color: #333;
      padding: 12px 30px;
      position: sticky;
      top: 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 100;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      border-radius: 0 0 15px 15px;
      flex-wrap: wrap;
    }

    .navbar img {
      height: 60px;
    }

    .menu-toggle {
      display: none;
      cursor: pointer;
    }

    .menu-toggle svg {
      width: 30px;
      height: 30px;
      fill: #333;
    }

    .navbar-links {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .navbar-links a {
      color: #333;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .navbar-links a:hover {
      color: #b30000;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
      .menu-toggle {
        display: block;
      }

      .navbar-links {
        width: 100%;
        display: none;
        flex-direction: column;
        align-items: flex-start;
        background-color: #fff;
        padding: 10px 0;
      }

      .navbar-links.active {
        display: flex;
      }

      .navbar-links a {
        padding: 10px 20px;
        width: 100%;
        border-top: 1px solid #ddd;
      }
    }

    /* Login Form */
    .login-container {
      max-width: 400px;
      margin: 80px auto;
      padding: 2rem;
      background: #ffffff;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
    }

    .login-container h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #b30000;
    }

    .login-container input {
      width: 100%;
      padding: 12px;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
    }

    .login-container button {
      width: 100%;
      padding: 12px;
      background-color: #b30000;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }

    .login-container button:hover {
      background-color: #8c0000;
    }

    @media (max-width: 768px) {
      .navbar-links {
        display: none;
        flex-direction: column;
        width: 100%;
        margin-top: 10px;
      }

      .navbar-links.active {
        display: flex;
      }

      .menu-toggle {
        display: block;
      }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <img src="../images/logo name red.png" alt="Varad Academy Logo">
    <div class="menu-toggle" onclick="toggleMenu()">
      <svg viewBox="0 0 100 80" width="30" height="30">
        <rect width="100" height="10"></rect>
        <rect y="30" width="100" height="10"></rect>
        <rect y="60" width="100" height="10"></rect>
      </svg>
    </div>
    <div class="navbar-links" id="navbarLinks">
      <a href="#home">Home</a>
      <a href="#about">About</a>
      <a href="#toppers">Toppers</a>
      <a href="#contact">Contact</a>
      <a href="#map">Map</a>
      <a href="admin/login.php">Admin Login</a>
    </div>
  </div>

  <!-- Login Form -->
  <div class="login-container">
    <h2>Admin Login</h2>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>

  <!-- Script for mobile menu -->
  <script>
    function toggleMenu() {
      const navbarLinks = document.getElementById('navbarLinks');
      navbarLinks.classList.toggle('active');
    }
  </script>
</body>
</html>
