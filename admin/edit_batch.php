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

// Validate and sanitize batch_id
$batch_id = isset($_GET['batch_id']) ? (int)$_GET['batch_id'] : 0;
if ($batch_id <= 0) {
    die("Invalid batch ID!");
}

// Fetch batch data
$stmt = $conn->prepare("SELECT * FROM batches WHERE id = ?");
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Batch not found!");
}
$batch = $result->fetch_assoc();
$stmt->close();

// Fetch students from the batch
$students = [];
$stmt = $conn->prepare("SELECT * FROM students WHERE batch_id = ?");
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();

// Handle add student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $student_name = trim($_POST['student_name']);
    if (empty($student_name)) {
        $error = "Student name is required!";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (name, batch_id) VALUES (?, ?)");
        $stmt->bind_param("si", $student_name, $batch_id);
        if ($stmt->execute()) {
            $success = "Student added successfully!";
            header("Location: edit_batch.php?batch_id=$batch_id");
            exit();
        } else {
            $error = "Error adding student: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle update batch
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_batch']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $name = trim($_POST['name']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    if (empty($name) || empty($start_date) || empty($end_date)) {
        $error = "All fields are required!";
    } else {
        $stmt = $conn->prepare("UPDATE batches SET name = ?, start_date = ?, end_date = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $start_date, $end_date, $batch_id);
        if ($stmt->execute()) {
            $success = "Batch updated successfully!";
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error updating batch: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Batch - <?= htmlspecialchars($batch['name']) ?> | Varad Academy</title>
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

        h2, h3 {
            text-align: center;
            color: #2c3e50;
        }

        h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        h3 {
            font-size: 24px;
            margin: 30px 0 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f8f8f8;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        .student-list table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .student-list th, .student-list td {
            border: 1px solid #ccc;
            padding: 15px;
            text-align: left;
            font-size: 14px;
        }

        .student-list th {
            background-color: #3498db;
            color: white;
        }

        .student-list tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .delete-link, .edit-link {
            color: #e74c3c;
            text-decoration: none;
            margin-right: 10px;
        }

        .edit-link {
            color: #3498db;
        }

        .delete-link:hover {
            color: #c0392b;
        }

        .edit-link:hover {
            color: #2980b9;
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

        @media screen and (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 70px 10px 10px;
            }

            h2 {
                font-size: 24px;
            }

            h3 {
                font-size: 20px;
            }

            input[type="submit"] {
                font-size: 14px;
            }

            .student-list th, .student-list td {
                padding: 10px;
                font-size: 12px;
            }
        }

        @media print {
            body {
                background: white;
                padding: 20mm;
                margin: 0;
            }

            .navbar, input[type="submit"], .back-link, .delete-link, .edit-link {
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

            h3 {
                font-size: 18pt;
                margin: 10px 0;
            }

            .form-group {
                display: none;
            }

            .student-list table {
                width: 100%;
                border-collapse: collapse;
            }

            .student-list th, .student-list td {
                border: 1px solid #000;
                padding: 10px;
                font-size: 12pt;
            }

            .student-list th {
                background-color: #3498db;
                color: white;
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
          
            <h2>वरद अकादमी</h2>
            <h3>Edit Batch - <?= htmlspecialchars($batch['name']) ?></h3>
        </div>

        <?php if (isset($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <h2>Edit Batch</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-group">
                <label for="name">Batch Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($batch['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($batch['start_date']) ?>" required>
            </div>
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($batch['end_date']) ?>" required>
            </div>
            <div class="form-group">
                <input type="submit" name="update_batch" value="Update Batch">
            </div>
        </form>

        <h3>Add Student to Batch</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-group">
                <label for="student_name">Student Name</label>
                <input type="text" id="student_name" name="student_name" required>
            </div>
            <div class="form-group">
                <input type="submit" name="add_student" value="Add Student">
            </div>
        </form>

        <div class="student-list">
            <h3>Students in Batch</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <td>
                                <a href="edit_student.php?student_id=<?= $student['id'] ?>&batch_id=<?= $batch_id ?>" class="edit-link">Edit</a>
                                <a href="delete_student.php?student_id=<?= $student['id'] ?>&batch_id=<?= $batch_id ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

    <script>
        document.getElementById('navToggle').addEventListener('click', function() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('active');
        });
    </script>
</body>
</html>