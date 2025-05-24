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

// Delete Slider Image
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
        $deleteStmt->close();
    } else {
        $error = "❌ Image not found!";
    }
    $stmt->close();
}

// Upload Slider Image
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["image"])) {
    $targetDir = "Uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedTypes)) {
        $error = "❌ Only JPG, JPEG, PNG, and GIF files are allowed.";
    } elseif ($_FILES["image"]["size"] > $maxFileSize) {
        $error = "❌ File size exceeds 5MB limit.";
    } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        $stmt = $conn->prepare("INSERT INTO slider_images (image_path) VALUES (?)");
        $stmt->bind_param("s", $targetFilePath);
        if ($stmt->execute()) {
            $success = "✅ Image uploaded successfully!";
        } else {
            $error = "❌ Database error occurred!";
        }
        $stmt->close();
    } else {
        $error = "❌ Failed to upload image!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Slider | Varad Academy</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #f4f6f8;
        }

        /* Navbar */
        .navbar {
            background: #ffffff;
            padding: 14px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar img {
            height: 60px;
        }

        .nav-toggle {
            display: none;
            cursor: pointer;
            flex-direction: column;
            justify-content: space-between;
            height: 22px;
        }

        .nav-toggle span {
            height: 3px;
            width: 25px;
            background: #2C3E50;
            border-radius: 2px;
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

        @media (max-width: 768px) {
            .nav-toggle {
                display: flex;
            }

            .nav-links {
                display: none;
                flex-direction: column;
                background: #fff;
                position: absolute;
                top: 70px;
                right: 30px;
                border: 1px solid #ccc;
                border-radius: 10px;
                padding: 10px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links a {
                padding: 10px;
                width: 150px;
                text-align: right;
            }
        }

        /* Container and Form */
        .container {
            max-width: 700px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }

        h2, h3 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f7f7f7;
        }

        button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 14px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        .msg {
            text-align: center;
            font-weight: 600;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
        }

        .success {
            color: #155724;
            background-color: #d4edda;
        }

        .error {
            color: #721c24;
            background-color: #f8d7da;
        }

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
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }

        a.back:hover {
            color: #2980b9;
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
            background-color: #e74c3c;
            padding: 8px 16px;
            display: inline-block;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 10px;
            transition: background 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 40px 0;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .container {
                margin: 80px 15px;
                padding: 20px;
            }

            .gallery-item {
                width: 100%;
                max-width: 300px;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 20mm;
            }

            .navbar, .form-group, button, a.back, .delete-btn {
                display: none;
            }

            .container {
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .print-header {
                text-align: center;
                margin-bottom: 20px;
            }

            .print-header img {
                height: 80px;
            }

            h3 {
                font-size: 18pt;
                margin: 10px 0;
            }

            .gallery {
                display: block;
            }

            .gallery-item {
                width: 100%;
                margin-bottom: 20px;
            }

            .gallery-item img {
                max-width: 100%;
                box-shadow: none;
                border: 1px solid #000;
            }

            @page {
                margin: 20mm;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <img src="../images/logo.png" alt="Varad Academy Logo" />
        <div class="nav-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="nav-links">
            <a href="../index.php">🏠 Home</a>
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="print-header">
           
            <h2>वरद अकादमी</h2>
        </div>
        <h2>Upload New Slider Image</h2>

        <?php if (isset($success)): ?>
            <div class="msg success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="msg error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

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

        <hr>

        <h3>Uploaded Slider Images</h3>

        <div class="gallery">
            <?php
            $stmt = $conn->prepare("SELECT * FROM slider_images ORDER BY id DESC");
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <div class="gallery-item">
                        <img src="' . htmlspecialchars($row['image_path']) . '" alt="Slider Image">
                        <a href="?delete=' . $row['id'] . '" class="delete-btn" onclick="return confirm(\'Are you sure?\')">🗑️ Delete</a>
                    </div>';
                }
            } else {
                echo "<p style='text-align:center;'>No slider images uploaded yet.</p>";
            }
            $stmt->close();
            ?>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Mobile nav toggle
        document.querySelector('.nav-toggle').addEventListener('click', () => {
            document.querySelector('.nav-links').classList.toggle('active');
        });

        // Image preview
        function previewImage(event) {
            const previewContainer = document.getElementById('preview-container');
            const previewImg = document.getElementById('preview-img');
            if (event.target.files[0]) {
                previewContainer.style.display = 'block';
                previewImg.src = URL.createObjectURL(event.target.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>