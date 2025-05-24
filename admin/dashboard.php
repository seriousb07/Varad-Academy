<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "varad_academy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$toppersResult = $conn->query("SELECT * FROM toppers");
$sliderResult = $conn->query("SELECT * FROM slider_images ORDER BY id DESC LIMIT 5");
$totalToppers = $conn->query("SELECT COUNT(*) AS total FROM toppers")->fetch_assoc()['total'];
$totalSlider = $conn->query("SELECT COUNT(*) AS total FROM slider_images")->fetch_assoc()['total'];
$batches = $conn->query("SELECT * FROM batches ORDER BY id DESC");
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
      background-color: #f4f6f8;
      color: #2C3E50;
    }

    .navbar {
      background: #ffffff;
      padding: 14px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar img {
      height: 60px;
    }

    .nav-links {
      display: flex;
      gap: 12px;
    }

    .nav-links a {
      color: #2C3E50;
      font-weight: bold;
      text-decoration: none;
      padding: 8px 16px;
      border: 1px solid #2C3E50;
      border-radius: 6px;
      transition: all 0.3s ease;
    }

    .nav-links a:hover {
      background-color: #2C3E50;
      color: #fff;
    }

    .menu-toggle {
      display: none;
      font-size: 28px;
      cursor: pointer;
      border: none;
      background: none;
      color: #2C3E50;
    }

    @media (max-width: 768px) {
      .nav-links {
        flex-direction: column;
        position: absolute;
        top: 70px;
        right: 0;
        background: white;
        width: 200px;
        border: 1px solid #ddd;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: none;
        padding: 10px;
      }

      .nav-links.active {
        display: flex;
      }

      .menu-toggle {
        display: block;
      }
    }

    .container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 30px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.05);
    }

    h2, h3 {
      color: #2C3E50;
      text-align: center;
      margin-bottom: 25px;
    }

    .stats {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 30px;
      margin-bottom: 40px;
    }

    .stat-box {
      background: #ecf0f1;
      color: #2C3E50;
      padding: 20px;
      border-radius: 10px;
      width: 220px;
      text-align: center;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .stat-box h4 {
      font-size: 18px;
      margin-bottom: 10px;
    }

    .stat-box p {
      font-size: 26px;
      font-weight: bold;
    }

    .actions {
      text-align: center;
      margin-bottom: 40px;
    }

    .actions a {
      background-color: #3498DB;
      color: white;
      text-decoration: none;
      padding: 12px 20px;
      margin: 10px;
      border-radius: 8px;
      font-weight: 500;
      display: inline-block;
      transition: background-color 0.3s ease;
    }

    .actions a:hover {
      background-color: #2980B9;
    }

    .batch-section {
      margin-top: 40px;
    }

    .batch-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }

    .batch-card {
      background: #f0f3f5;
      padding: 20px;
      border-radius: 12px;
      width: 250px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.07);
      text-align: center;
      color: #2C3E50;
    }

    .batch-card h4 {
      font-size: 20px;
      margin-bottom: 10px;
    }

    .batch-actions {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 15px;
    }

    .batch-actions a {
      background: #3498DB;
      color: white;
      padding: 10px;
      border-radius: 6px;
      font-size: 14px;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .batch-actions a:hover {
      background: #2980B9;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <img src="../images/logo.png" alt="Varad Academy Logo">
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    <div class="nav-links" id="navLinks">
      <a href="../index.php">🏠 Home</a>
      <a href="dashboard.php">📊 Dashboard</a>
      <a href="logout.php">🚪 Logout</a>
    </div>
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
      <a href="add_batch.php">📚 Create Batch</a>
    </div>

    <div class="batch-section">
      <h3>📘 All Batches</h3>
      <div class="batch-container">
        <?php while ($row = $batches->fetch_assoc()) { ?>
          <div class="batch-card">
            <h4><?= htmlspecialchars($row['name']) ?></h4>
            <div class="batch-actions">
              <a href="mark_attendance.php?batch_id=<?= $row['id'] ?>">📊 Mark Attendance</a>
              <a href="attendance_report.php?batch_id=<?= $row['id'] ?>">📄 View Attendance</a>
              <a href="edit_batch.php?batch_id=<?= $row['id'] ?>">✏️ Edit Batch</a>
              <a href="delete_batch.php?batch_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this batch?')">❌ Delete Batch</a>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>

  <script>
    function toggleMenu() {
      const menu = document.getElementById("navLinks");
      menu.classList.toggle("active");
    }
  </script>

</body>
</html>
