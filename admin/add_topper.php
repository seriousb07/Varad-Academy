<?php
$conn = new mysqli("localhost", "root", "", "varad_academy");

// Add Topper
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_topper'])) {
    $name = $_POST['name'];
    $total = $_POST['total_marks'];
    $math = $_POST['math_marks'];
    $year = $_POST['batch_year'];

    $photoName = $_FILES['photo']['name'];
    $tmp = $_FILES['photo']['tmp_name'];
    move_uploaded_file($tmp, "../images/$photoName");

    $sql = "INSERT INTO toppers (name, total_marks, math_marks, batch_year, photo)
            VALUES ('$name', '$total', '$math', '$year', '$photoName')";
    $conn->query($sql);
    echo "<script>alert('Topper added!'); window.location.href='add_topper.php';</script>";
}

// Delete Topper
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM toppers WHERE id = $id");
    echo "<script>alert('Topper deleted!'); window.location.href='add_topper.php';</script>";
}

// Fetch all toppers
$toppers = $conn->query("SELECT * FROM toppers ORDER BY batch_year DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Topper | Varad Academy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* All styles same as previously provided */
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #fff6f6;
    }
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
    .form-container {
      max-width: 500px;
      margin: 80px auto 40px;
      padding: 2rem;
      background: #ffffff;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
    }
    .form-container h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #b30000;
    }
    .form-container input {
      width: 100%;
      padding: 12px;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
    }
    .form-container button {
      width: 100%;
      padding: 12px;
      background-color: #b30000;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
    }
    .form-container button:hover { background-color: #8c0000; }
    .back-btn {
      margin-top: 10px;
      text-align: center;
    }
    .back-btn a {
      color: #b30000;
      text-decoration: none;
      font-weight: bold;
    }
    .table-container {
      overflow-x: auto;
      margin: 0 auto;
      width: 95%;
    }
    table {
      width: 100%;
      margin: 20px auto;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: center;
      font-size: 14px;
    }
    th { background-color: #b30000; color: white; }
    img.photo {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
    }
    .btn-action {
      padding: 5px 8px;
      margin: 2px;
      text-decoration: none;
      font-size: 12px;
      display: block;
      border-radius: 5px;
    }
    .edit-btn { background: #4caf50; color: white; }
    .delete-btn { background: #f44336; color: white; }
    .preview-container {
      text-align: center;
      margin-bottom: 1rem;
    }
    #photoPreview {
      max-width: 120px;
      max-height: 120px;
      border-radius: 50%;
      object-fit: cover;
      display: none;
      margin-top: 10px;
      border: 2px solid #ccc;
    }
    .print-btn-container {
      text-align: center;
      margin-bottom: 20px;
    }
    .print-btn-container button {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #b30000;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    @media (max-width: 600px) {
      .form-container {
        margin: 60px 15px 30px;
        padding: 1.2rem;
      }
      th, td {
        font-size: 12px;
        padding: 6px;
      }
      .btn-action {
        padding: 4px 6px;
        font-size: 11px;
      }
      .print-btn-container button {
        width: 100%;
        padding: 12px;
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
      <a href="login.php">Admin Login</a>
    </div>
  </div>

  <!-- Add Topper Form -->
  <div class="form-container">
    <h2>Add New Topper</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="name" placeholder="Name" required>
      <input type="number" name="total_marks" placeholder="Total Marks" required>
      <input type="number" name="math_marks" placeholder="Math Marks" required>
      <input type="text" name="batch_year" placeholder="Batch Year (e.g. 2024)" required>
      <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this)" required>
      <div class="preview-container">
        <img id="photoPreview" alt="Preview">
      </div>
      <button type="submit" name="add_topper">Add Topper</button>
    </form>
    <div class="back-btn">
      <a href="dashboard.php">← Go Back to Dashboard</a>
    </div>
  </div>

  <!-- Print Button -->
  <div class="print-btn-container">
    <button onclick="printTable()">🖨️ Print Toppers Table</button>
  </div>

  <!-- Toppers Table -->
  <div class="table-container">
    <table id="toppersTable">
      <tr>
        <th>ID</th>
        <th>Photo</th>
        <th>Name</th>
        <th>Total Marks</th>
        <th>Math Marks</th>
        <th>Batch Year</th>
        <th>Actions</th>
      </tr>
      <?php while ($row = $toppers->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><img src="../images/<?= $row['photo'] ?>" class="photo" alt=""></td>
          <td><?= $row['name'] ?></td>
          <td><?= $row['total_marks'] ?></td>
          <td><?= $row['math_marks'] ?></td>
          <td><?= $row['batch_year'] ?></td>
          <td>
            <a class="btn-action edit-btn" href="edit_topper.php?id=<?= $row['id'] ?>">Edit</a><br>
            <a class="btn-action delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <!-- Scripts -->
  <script>
    function toggleMenu() {
      const navbarLinks = document.getElementById('navbarLinks');
      navbarLinks.classList.toggle('active');
    }

    function previewPhoto(input) {
      const file = input.files[0];
      const preview = document.getElementById('photoPreview');
      if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'inline-block';
      } else {
        preview.src = '';
        preview.style.display = 'none';
      }
    }

    function printTable() {
      const tableHTML = document.getElementById("toppersTable").outerHTML;
      const win = window.open('', '', 'width=1000,height=700');
      win.document.write(`
        <html>
        <head>
          <title>Print Toppers Table</title>
          <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 10px; border: 1px solid #000; text-align: center; }
            th { background-color: #b30000; color: white; }
            img { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
          </style>
        </head>
        <body>
          <h2 style="text-align:center;">Varad Academy – Toppers List</h2>
          ${tableHTML}
        </body>
        </html>
      `);
      win.document.close();
      win.print();
    }
  </script>

</body>
</html>
