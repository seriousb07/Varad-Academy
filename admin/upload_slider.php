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

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("SELECT image_path FROM slider_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($imagePath);
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $deleteStmt = $conn->prepare("DELETE FROM slider_images WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        if ($deleteStmt->execute()) {
            $success = "✅ Image deleted successfully!";
        } else {
            $error = "❌ Failed to delete image from database!";
        }
    } else {
        $error = "❌ Image not found!";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["image"])) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $stmt = $conn->prepare("INSERT INTO slider_images (image_path) VALUES (?)");
            $stmt->bind_param("s", $targetFilePath);
            if ($stmt->execute()) {
                $success = "✅ Image uploaded successfully!";
            } else {
                $error = "❌ Database error occurred!";
            }
        } else {
            $error = "❌ Failed to upload image!";
        }
    } else {
        $error = "❌ Only JPG, JPEG, PNG, and GIF files are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Slider | Varad Academy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #f2f2f2, #f9f9f9);
      margin: 0;
      padding: 0;
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

    .container {
      max-width: 700px;
      margin: 60px auto;
      padding: 30px;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }

    h2, h3 {
      text-align: center;
      color: #b30000;
      margin-bottom: 25px;
    }

    .form-group {
      margin-bottom: 25px;
    }

    label {
      font-weight: 600;
      display: block;
      margin-bottom: 10px;
    }

    input[type="file"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: #f7f7f7;
    }

    button {
      background-color: #b30000;
      color: #fff;
      border: none;
      padding: 14px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      transition: 0.3s ease;
    }

    button:hover {
      background-color: #8c0000;
    }

    .msg {
      text-align: center;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .success { color: green; }
    .error { color: red; }

    .preview {
      text-align: center;
      margin-top: 20px;
    }

    .preview img {
      max-width: 100%;
      height: auto;
      border-radius: 12px;
      margin-top: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    a.back {
      display: block;
      text-align: center;
      margin-top: 25px;
      color: #b30000;
      text-decoration: none;
      font-weight: 600;
    }

    a.back:hover {
      color: #8c0000;
    }

    .gallery {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      margin-top: 30px;
    }

    .gallery-item {
      text-align: center;
      width: 220px;
    }

    .gallery-item img {
      width: 100%;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .delete-btn {
      color: white;
      background-color: #b30000;
      padding: 8px 16px;
      display: inline-block;
      border-radius: 8px;
      text-decoration: none;
      margin-top: 10px;
      transition: 0.3s ease;
    }

    .delete-btn:hover {
      background-color: #8c0000;
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

<div class="container">
  <h2>Upload New Slider Image</h2>

  <?php if (isset($success)) echo "<div class='msg success'>$success</div>"; ?>
  <?php if (isset($error)) echo "<div class='msg error'>$error</div>"; ?>

  <form action="" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label for="image">Choose Image File:</label>
      <input type="file" name="image" id="image" accept="image/*" required onchange="previewImage(event)">
    </div>

    <div class="preview" id="preview-container" style="display: none;">
      <strong>Image Preview:</strong><br>
      <img id="preview-img" src="" alt="Preview">
    </div>

    <button type="submit">📤 Upload Image</button>
  </form>

  <a class="back" href="dashboard.php">← Go Back to Dashboard</a>

  <hr style="margin: 40px 0;">

  <h3>Uploaded Slider Images</h3>

  <div class="gallery">
    <?php
    $result = $conn->query("SELECT * FROM slider_images ORDER BY id DESC");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '
            <div class="gallery-item">
                <img src="' . $row['image_path'] . '" alt="Slider Image">
                <a href="?delete=' . $row['id'] . '" class="delete-btn">🗑️ Delete</a>
            </div>';
        }
    } else {
        echo "<p style='text-align:center;'>No slider images uploaded yet.</p>";
    }
    ?>
  </div>
</div>

<script>
  function previewImage(event) {
    const previewContainer = document.getElementById('preview-container');
    const previewImg = document.getElementById('preview-img');
    previewContainer.style.display = 'block';
    previewImg.src = URL.createObjectURL(event.target.files[0]);
  }

  function toggleMenu() {
    const navbarLinks = document.getElementById("navbarLinks");
    navbarLinks.classList.toggle("active");
  }
</script>

</body>
</html>
