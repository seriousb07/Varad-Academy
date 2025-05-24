<?php
// add_batch.php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "varad_academy");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];

    $stmt = $conn->prepare("INSERT INTO batches (name, start_date, end_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $start, $end);
    $stmt->execute();

    $batchId = $stmt->insert_id;
    header("Location: add_students.php?batch_id=" . $batchId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Batch</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e0eafc, #cfdef3);
            margin: 0;
            padding: 0;
            min-height: 100vh;
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

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 20px;
        }

        form {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin: 12px 0 5px;
            font-weight: 500;
            color: #444;
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            outline: none;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #5b9bd5;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background-color: #5b9bd5;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #407ec9;
        }

        a button {
            background-color: #f44336;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 10px;
                align-items: flex-start;
            }

            form {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }

            label {
                font-size: 14px;
            }

            input[type="text"],
            input[type="date"] {
                font-size: 14px;
                padding: 8px 10px;
            }

            button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <img src="../images/logo.png" alt="Varad Academy Logo">

    <div class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('active')">
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

<div class="container">
    <form method="post">
        <h2>Add New Batch</h2>
        <label>Batch Name:</label>
        <input type="text" name="name" required>

        <label>Start Date:</label>
        <input type="date" name="start_date" required>

        <label>End Date:</label>
        <input type="date" name="end_date" required>

        <button type="submit">Create Batch</button>

        <!-- Go Back Button -->
        <a href="dashboard.php" style="text-decoration: none;">
            <button type="button">Go Back</button>
        </a>
    </form>
</div>

</body>
</html>
