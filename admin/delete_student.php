<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Generate or verify CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// DB connection
$conn = new mysqli("localhost", "root", "", "varad_academy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize student_id and batch_id
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$batch_id = isset($_GET['batch_id']) ? (int)$_GET['batch_id'] : 0;
if ($student_id <= 0 || $batch_id <= 0) {
    die("Invalid student or batch ID!");
}

// Delete related attendance records first to avoid foreign key constraints
$stmt = $conn->prepare("DELETE FROM attendance WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->close();

// Delete student
$stmt = $conn->prepare("DELETE FROM students WHERE id = ? AND batch_id = ?");
$stmt->bind_param("ii", $student_id, $batch_id);
if ($stmt->execute()) {
    $success = "Student deleted successfully!";
    header("Location: edit_batch.php?batch_id=$batch_id");
    exit();
} else {
    $error = "Error deleting student: " . $stmt->error;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student | Varad Academy</title>
    <style>
        * {
            font-family: 'Segoe UI', sans-serif;
            box-sizing: border-box;
        }

        body {
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
        }

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
                z-index: 999;
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
            max-width: 1000px;
            margin: 80px auto 20px;
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 20px;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
            font-size: 16px;
        }

        .back-link:hover {
            color: #2980b9;
        }

        @media screen and (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 70px 10px 10px;
            }

            h2 {
                font-size: 24px;
            }
        }

        @media print {
            body {
                background: white;
                padding: 20mm;
                margin: 0;
            }

            .navbar, .back-link {
                display: none;
            }

            .container {
                box-shadow: none;
                padding: 0;
                margin: 0 auto;
                max-width: 100%;
            }

            .print-header {
                text-align: center;
                margin-bottom: 20px;
            }

            .print-header img {
                height: 80px;
                margin-bottom: 10px;
            }

            h2 {
                font-size: 24pt;
                margin: 0;
            }

            .message {
                display: none;
            }

            @page {
                margin: 20mm;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="../images/logo.png" alt="Varad Academy Logo" />
        <div class="nav-toggle" id="navToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="nav-links" id="navLinks">
            <a href="../index.php">🏠 Home</a>
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="print-header">
            <img src="../images/logo.png" alt="Varad Academy Logo" />
            <h2>वरद अकादमी</h2>
        </div>

        <h2>Delete Student</h2>
        <?php if (isset($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <a href="edit_batch.php?batch_id=<?= $batch_id ?>" class="back-link">← Back to Edit Batch</a>
    </div>

    <script>
        document.getElementById('navToggle').addEventListener('click', function() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('active');
        });
    </script>
</body>
</html>