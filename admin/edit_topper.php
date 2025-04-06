<?php
$conn = new mysqli("localhost", "root", "", "varad_academy");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM toppers WHERE id = $id");
    $row = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $total = $_POST['total_marks'];
        $math = $_POST['math_marks'];
        $year = $_POST['batch_year'];

        if ($_FILES['photo']['name']) {
            $photoName = $_FILES['photo']['name'];
            $tmp = $_FILES['photo']['tmp_name'];
            move_uploaded_file($tmp, "../images/$photoName");

            $updateQuery = "UPDATE toppers 
                            SET name='$name', total_marks='$total', math_marks='$math', batch_year='$year', photo='$photoName' 
                            WHERE id=$id";
        } else {
            $updateQuery = "UPDATE toppers 
                            SET name='$name', total_marks='$total', math_marks='$math', batch_year='$year' 
                            WHERE id=$id";
        }

        $conn->query($updateQuery);
        echo "<script>alert('Topper updated successfully!'); window.location.href='add_topper.php';</script>";
    }
} else {
    echo "Invalid request.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Topper</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
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
      margin: 80px auto;
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
      transition: background 0.3s;
    }

    .form-container button:hover {
      background-color: #8c0000;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 10px;
      text-decoration: none;
      color: #333;
    }

    .back-link:hover {
      color: #b30000;
    }

    .photo-preview {
      text-align: center;
      margin-bottom: 1rem;
    }

    .photo-preview img {
      max-width: 100px;
      height: auto;
      border-radius: 10px;
      border: 1px solid #ccc;
    }

    label {
      font-weight: 500;
      margin-bottom: 5px;
      display: inline-block;
    }
  </style>
</head>
<body>

<div class="navbar">
    <img src="../images/logo name red.png" alt="Varad Academy Logo">
    <div class="menu-toggle" onclick="toggleMenu()">
      <svg viewBox="0 0 100 80">
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

<!-- ✅ Edit Form -->
<div class="form-container">
  <h2>Edit Topper</h2>
  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Name" value="<?= $row['name'] ?>" required>
    <input type="number" name="total_marks" placeholder="Total Marks" value="<?= $row['total_marks'] ?>" required>
    <input type="number" name="math_marks" placeholder="Math Marks" value="<?= $row['math_marks'] ?>" required>
    <input type="text" name="batch_year" placeholder="Batch Year (e.g. 2024)" value="<?= $row['batch_year'] ?>" required>
    
    <div class="photo-preview">
      <label>Current Photo:</label><br>
      <img src="../images/<?= $row['photo'] ?>" alt="Current Topper Photo">
    </div>

    <label>Change Photo (optional):</label>
    <input type="file" name="photo">
    
    <button type="submit">Update Topper</button>
  </form>
  <a href="add_topper.php" class="back-link">← Back to Dashboard</a>
</div>

<!-- 🔁 Toggle Script -->
<script>
  function toggleMenu() {
    const nav = document.getElementById('navbarLinks');
    nav.classList.toggle('active');
  }
</script>

</body>
</html>
