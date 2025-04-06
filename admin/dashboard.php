<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "varad_academy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetching data
$toppersResult = $conn->query("SELECT * FROM toppers");
$sliderResult = $conn->query("SELECT * FROM slider_images ORDER BY id DESC LIMIT 5");
$totalToppers = $conn->query("SELECT COUNT(*) AS total FROM toppers")->fetch_assoc()['total'];
$totalSlider = $conn->query("SELECT COUNT(*) AS total FROM slider_images")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard | Varad Academy</title>
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background-color: #f9f9f9;
    }

    .navbar {
      background: #fff;
      padding: 14px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar img {
      height: 60px;
    }

    .navbar a {
      color: #b30000;
      font-weight: bold;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .navbar a:hover {
      color: #8c0000;
    }

    .container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    h2, h3 {
      color: #b30000;
      text-align: center;
      margin-bottom: 20px;
    }

    .stats {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 30px;
      margin-bottom: 40px;
    }

    .stat-box {
      background: #ffeaea;
      color: #b30000;
      padding: 20px;
      border-radius: 10px;
      width: 220px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .stat-box h4 {
      font-size: 18px;
      margin-bottom: 10px;
    }

    .stat-box p {
      font-size: 24px;
      font-weight: bold;
    }

    .actions {
      text-align: center;
      margin-bottom: 40px;
    }

    .actions a {
      background-color: #b30000;
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      margin: 10px;
      border-radius: 6px;
      font-weight: 500;
      display: inline-block;
      transition: background-color 0.3s ease;
    }

    .actions a:hover {
      background-color: #8c0000;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 40px;
    }

    thead {
      background-color: #ffe5e5;
      color: #b30000;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }

    td img {
      height: 60px;
      width: 60px;
      object-fit: cover;
      border-radius: 5px;
    }

    .btn-edit, .btn-delete {
      padding: 6px 12px;
      font-size: 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      color: white;
      text-decoration: none;
      display: inline-block;
    }

    .btn-edit { background-color: #007bff; }
    .btn-delete { background-color: #dc3545; }

    .slider-section {
      margin-top: 40px;
    }

    .slider-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }

    .slider-item {
      position: relative;
    }

    .slider-item img {
      width: 200px;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .slider-item form {
      position: absolute;
      top: 5px;
      right: 5px;
    }

    .slider-item button {
      background: #dc3545;
      color: white;
      border: none;
      padding: 4px 8px;
      font-size: 12px;
      border-radius: 4px;
      cursor: pointer;
    }

    @media (max-width: 768px) {
      .stats { flex-direction: column; align-items: center; }
      .slider-container { flex-direction: column; }
      .actions a { display: block; margin: 10px auto; }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <img src="../images/logo.png" alt="Varad Academy Logo">
    <a href="logout.php">Logout</a>
  </div>

  <div class="container">
    <h2>Admin Dashboard</h2>

    <div class="stats">
      <div class="stat-box">
        <h4>Total Toppers</h4>
        <p><?= htmlspecialchars($totalToppers); ?></p>
      </div>
      <div class="stat-box">
        <h4>Total Slider Images</h4>
        <p><?= htmlspecialchars($totalSlider); ?></p>
      </div>
    </div>

    <div class="actions">
      <a href="add_topper.php">➕ Add Topper</a>
      <a href="upload_slider.php">🖼️ Upload Slider Image</a>
    </div>


    </div>
  </div>

</body>
</html>
