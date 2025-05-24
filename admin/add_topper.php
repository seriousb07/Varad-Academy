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

// Add Topper
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_topper'])) {
    $name = trim($_POST['name']);
    $total = (int)$_POST['total_marks'];
    $math = (int)$_POST['math_marks'];
    $year = (int)$_POST['batch_year'];

    if (empty($name) || $total <= 0 || $math <= 0 || $year <= 0 || !isset($_FILES['photo']['name'])) {
        echo "<script>alert('All fields are required!'); window.location.href='add_topper.php';</script>";
        exit();
    }

    $photoName = $_FILES['photo']['name'];
    $tmp = $_FILES['photo']['tmp_name'];
    $photoPath = "../images/" . basename($photoName);
    
    if (move_uploaded_file($tmp, $photoPath)) {
        $stmt = $conn->prepare("INSERT INTO toppers (name, total_marks, math_marks, batch_year, photo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiis", $name, $total, $math, $year, $photoName);
        if ($stmt->execute()) {
            echo "<script>alert('Topper added!'); window.location.href='add_topper.php';</script>";
        } else {
            echo "<script>alert('Error adding topper: " . $stmt->error . "'); window.location.href='add_topper.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error uploading photo!'); window.location.href='add_topper.php';</script>";
    }
}

// Delete Topper
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM toppers WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Topper deleted!'); window.location.href='add_topper.php';</script>";
    } else {
        echo "<script>alert('Error deleting topper: " . $stmt->error . "'); window.location.href='add_topper.php';</script>";
    }
    $stmt->close();
}

// Fetch all toppers
$stmt = $conn->prepare("SELECT * FROM toppers ORDER BY batch_year DESC");
$stmt->execute();
$toppers = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Topper | Varad Academy</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        /* Form and Table */
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
            color: #2c3e50;
        }

        .form-container input,
        .form-container button {
            width: 100%;
            padding: 12px;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-container button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #2980b9;
        }

        .back-btn {
            margin-top: 10px;
            text-align: center;
        }

        .back-btn a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        .back-btn a:hover {
            color: #2980b9;
        }

        .table-container {
            overflow-x: auto;
            margin: 0 auto;
            width: 95%;
            max-width: 1000px;
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

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

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
            display: inline-block;
            border-radius: 5px;
        }

        .edit-btn {
            background: #4caf50;
            color: white;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
        }

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
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .print-btn-container button:hover {
            background-color: #2980b9;
        }

        @media (max-width: 600px) {
            .form-container {
                margin: 80px 15px 30px;
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

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 20mm;
            }

            .navbar, .form-container, .print-btn-container, .btn-action {
                display: none;
            }

            .table-container {
                margin: 0;
                width: 100%;
            }

            table {
                box-shadow: none;
                border: 1px solid #000;
            }

            th, td {
                border: 1px solid #000;
                font-size: 12pt;
            }

            th {
                background-color: #3498db;
            }

            .print-header {
                text-align: center;
                margin-bottom: 20px;
            }

            .print-header img {
                height: 80px;
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

    <!-- Add Topper Form -->
    <div class="form-container">
        <div class="print-header">
           
            <h2>वरद अकादमी</h2>
        </div>
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
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><img src="../images/<?= htmlspecialchars($row['photo']) ?>" class="photo" alt=""></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['total_marks']) ?></td>
                    <td><?= htmlspecialchars($row['math_marks']) ?></td>
                    <td><?= htmlspecialchars($row['batch_year']) ?></td>
                    <td>
                        <a class="btn-action edit-btn" href="edit_topper.php?id=<?= $row['id'] ?>">Edit</a>
                        <a class="btn-action delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Scripts -->
    <script>
        // Mobile nav toggle
        document.querySelector('.nav-toggle').addEventListener('click', () => {
            document.querySelector('.nav-links').classList.toggle('active');
        });

        // Photo preview
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

        // Print table
        function printTable() {
            const tableHTML = document.getElementById("toppersTable").outerHTML;
            const win = window.open('', '', 'width=1000,height=700');
            win.document.write(`
                <html>
                <head>
                    <title>Print Toppers Table</title>
                    <style>
                        body { font-family: 'Segoe UI', Arial, sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { padding: 10px; border: 1px solid #000; text-align: center; }
                        th { background-color: #3498db; color: white; }
                        img { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
                    </style>
                </head>
                <body>
                    <div style="text-align:center; margin-bottom:20px;">
                        <img src="../images/logo.png" style="height:80px;" alt="Varad Academy Logo">
                    </div>
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
<?php $conn->close(); ?>