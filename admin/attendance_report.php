<?php
$conn = new mysqli("localhost", "root", "", "varad_academy");
$batchId = $_GET['batch_id'] ?? 1;

$dateFrom = $_GET['from'] ?? date('Y-m-01');
$dateTo = $_GET['to'] ?? date('Y-m-t');

// Sanitize inputs
$dateFrom = $conn->real_escape_string($dateFrom);
$dateTo = $conn->real_escape_string($dateTo);

$studentsResult = $conn->query("SELECT id, name FROM students WHERE batch_id = $batchId");

// Get unique class days between selected dates
$classDaysRes = $conn->query("SELECT DISTINCT date FROM attendance WHERE batch_id = $batchId AND date BETWEEN '$dateFrom' AND '$dateTo'");
$classDates = [];
while ($row = $classDaysRes->fetch_assoc()) {
    $classDates[] = $row['date'];
}
$totalClassDays = count($classDates);

$attendanceData = [];

while ($student = $studentsResult->fetch_assoc()) {
    $studentId = $student['id'];
    $presentDays = 0;

    // Count distinct dates where student was marked present
    $presentRes = $conn->query("SELECT DISTINCT date FROM attendance WHERE student_id = $studentId AND status = 'Present' AND date BETWEEN '$dateFrom' AND '$dateTo'");
    $presentDays = $presentRes->num_rows;

    $percent = $totalClassDays ? min(round(($presentDays / $totalClassDays) * 100), 100) : 0;

    $attendanceData[$studentId] = [
        'name' => $student['name'],
        'present' => $presentDays,
        'total' => $totalClassDays,
        'percent' => $percent
    ];
}

$currentMonthYear = date("F Y", strtotime($dateFrom));
$fromFormatted = date("d-m-Y", strtotime($dateFrom));
$toFormatted = date("d-m-Y", strtotime($dateTo));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report - <?= $currentMonthYear ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            margin: auto;
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
            margin-bottom: 10px;
        }

        h3 {
            color: #34495e;
            font-size: 20px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 20px;
            color: #555;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="date"] {
            padding: 8px 14px;
            margin: 0 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            padding: 10px 20px;
            margin: 0 8px;
            background-color: #2980b9;
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background-color: #1c6fa3;
        }

        .print-btn {
            float: right;
            margin-bottom: 20px;
        }

        .back-btn {
            float: left;
            background-color: #27ae60;
        }

        .back-btn:hover {
            background-color: #1e8e4d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            overflow-x: auto;
            display: block;
            white-space: nowrap;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 15px;
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

        .low-attendance {
            background-color: #ffcccc !important;
        }

        strong {
            color: #2980b9;
            font-weight: bold;
        }

        @media screen and (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }

            h3 {
                font-size: 18px;
            }

            .subtitle {
                font-size: 14px;
            }

            form {
                font-size: 14px;
            }

            button {
                padding: 8px 14px;
                font-size: 14px;
            }

            .back-btn, .print-btn {
                float: none;
                width: 100%;
                margin: 10px 0;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px;
            }
        }

        @media print {
            .print-btn, .back-btn, form {
                display: none;
            }

            .container {
                box-shadow: none;
                padding: 0;
            }

            body {
                background: white;
                padding: 0;
            }

            table {
                display: table;
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
        <h2>वरद अकादमी</h2>
        <h3>Attendance Report - <?= $currentMonthYear ?></h3>
        <div class="subtitle">From: <?= $fromFormatted ?> To: <?= $toFormatted ?></div>

        <form method="GET">
            <input type="hidden" name="batch_id" value="<?= $batchId ?>">
            <label>From:
                <input type="date" name="from" value="<?= $dateFrom ?>">
            </label>
            <label>To:
                <input type="date" name="to" value="<?= $dateTo ?>">
            </label>
            <button type="submit">Filter</button>
        </form>

        <button class="print-btn" onclick="window.print()">Print Report</button>
        <a href="dashboard.php"><button class="back-btn">Back to Dashboard</button></a>

        <table>
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th>Student</th>
                    <th>Present Days</th>
                    <th>Total Days</th>
                    <th>Attendance %</th>
                </tr>
            </thead>
            <tbody>
                <?php $sr = 1; foreach ($attendanceData as $data): ?>
                    <tr <?= $data['percent'] < 75 ? 'class="low-attendance"' : '' ?>>
                        <td><?= $sr++ ?></td>
                        <td><?= htmlspecialchars($data['name']) ?></td>
                        <td><?= $data['present'] ?></td>
                        <td><?= $data['total'] ?></td>
                        <td><strong><?= $data['percent'] ?>%</strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        document.getElementById('navToggle').addEventListener('click', function() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('active');
        });
    </script>
</body>
</html>