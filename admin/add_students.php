<?php
session_start();
$conn = new mysqli("localhost", "root", "", "varad_academy");

$batchId = $_GET['batch_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['student_name'];
    $redirectToDashboard = isset($_POST['submit_and_redirect']);

    $stmt = $conn->prepare("INSERT INTO students (name, batch_id) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $batchId);
    $stmt->execute();

    if ($redirectToDashboard) {
        header("Location: dashboard.php");
        exit();
    }
}

$students = $conn->query("SELECT * FROM students WHERE batch_id = $batchId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Students to Batch</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e3f2fd, #ffffff);
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar fixed at top */
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

        /* Main content wrapper to center form vertically and horizontally */
        .content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding-top: 100px; /* To avoid navbar overlap */
            padding-bottom: 40px;
        }

        h2, h3 {
            color: #333;
            text-align: center;
            margin: 10px 0;
        }

        form {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin-bottom: 30px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input:focus {
            border-color: #007BFF;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        button {
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-btn {
            background-color: #6c757d;
        }

        .back-btn:hover {
            background-color: #495057;
        }

        ul {
            list-style-type: none;
            padding: 0;
            max-width: 400px;
            width: 100%;
        }

        li {
            background: #f8f9fa;
            margin: 8px 0;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 500;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        /* Mobile responsiveness */
        @media (max-width: 600px) {
            .content-wrapper {
                padding-top: 80px;
                padding-left: 10px;
                padding-right: 10px;
            }

            form {
                padding: 20px;
                margin-bottom: 20px;
            }

            h2, h3 {
                font-size: 20px;
            }

            input[type="text"] {
                font-size: 14px;
                padding: 10px;
            }

            button {
                font-size: 14px;
                padding: 12px;
            }

            .button-group {
                flex-direction: column;
            }

            ul {
                padding: 0;
                width: 100%;
            }

            li {
                font-size: 14px;
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>

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

<div class="content-wrapper">
    <h2>Add Students to Batch</h2>

    <form method="post">
        <input type="text" name="student_name" placeholder="Student Name" required />
        <div class="button-group">
            <button type="submit" name="submit_and_stay">Add Student</button>
            <button type="button" class="back-btn" onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
        </div>
    </form>

    <h3>Current Students</h3>
    <ul>
        <?php while ($row = $students->fetch_assoc()): ?>
            <li><?= htmlspecialchars($row['name']) ?></li>
        <?php endwhile; ?>
    </ul>
</div>

<script>
  // Mobile nav toggle script - only here, no inline onclick attribute
  document.querySelector('.nav-toggle').addEventListener('click', () => {
    document.querySelector('.nav-links').classList.toggle('active');
  });
</script>

</body>
</html>
