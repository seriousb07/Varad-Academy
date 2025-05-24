<?php
session_start();
$conn = new mysqli("localhost", "root", "", "varad_academy");

$batchId = $_GET['batch_id'] ?? 0;
$date = $_GET['date'] ?? date('Y-m-d'); // default to today's date

// Fetch the batch name from the database
$batchResult = $conn->query("SELECT name FROM batches WHERE id = $batchId");
$batch = $batchResult->fetch_assoc();
$batchName = $batch['name'] ?? 'Unknown Batch';

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_attendance'])) {
    $date = $_POST['attendance_date'];

    // Delete previous attendance for that day and batch
    $conn->query("DELETE FROM attendance WHERE batch_id = $batchId AND date = '$date'");

    // Insert updated attendance
    if (!empty($_POST['students'])) {
        foreach ($_POST['students'] as $student_id) {
            $stmt = $conn->prepare("INSERT INTO attendance (student_id, batch_id, date) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $student_id, $batchId, $date);
            $stmt->execute();
        }
    }

    echo "<script>alert('Attendance updated successfully!'); window.location.href='dashboard.php';</script>";
    exit;
}

// Fetch students
$students = $conn->query("SELECT * FROM students WHERE batch_id = $batchId");

// Get attendance data for the selected date
$attendanceResult = $conn->query("SELECT student_id FROM attendance WHERE batch_id = $batchId AND date = '$date'");
$markedAttendance = [];
while ($row = $attendanceResult->fetch_assoc()) {
    $markedAttendance[] = $row['student_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mark Attendance</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #e3f2fd, #fff);
      padding: 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
      margin: 0;
      min-height: 100vh;
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

    h2 {
      margin-top: 90px; /* to avoid navbar overlap */
      margin-bottom: 20px;
      color: #333;
    }

    form {
      background: white;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      width: 100%;
      margin-bottom: 20px;
    }

    label, input[type="date"], .top-label {
      display: block;
      margin-bottom: 15px;
      font-size: 16px;
    }

    input[type="date"] {
      padding: 10px;
      width: 100%;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 15px;
    }

    ul {
      list-style-type: none;
      padding: 0;
      margin-bottom: 20px;
      max-height: 250px;
      overflow-y: auto;
      border: 1px solid #ddd;
      border-radius: 6px;
    }

    li {
      padding: 10px 15px;
      border-bottom: 1px solid #eee;
      display: flex;
      align-items: center;
    }

    li:last-child {
      border-bottom: none;
    }

    .checkbox-label {
      margin-left: 8px;
      font-size: 15px;
    }

    .top-label {
      font-weight: 600;
      margin-bottom: 10px;
    }

    button {
      background-color: #5b9bd5;
      color: white;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #407ec9;
    }

    #selectAll {
      margin-right: 8px;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
      body {
        padding: 20px;
      }

      form {
        padding: 20px;
      }

      h2 {
        font-size: 22px;
      }

      label, input[type="date"] {
        font-size: 14px;
      }

      input[type="date"] {
        padding: 8px;
      }

      .checkbox-label {
        font-size: 14px;
      }

      button {
        font-size: 14px;
        padding: 10px;
      }

      ul {
        max-height: 200px;
        margin-bottom: 15px;
      }

      li {
        font-size: 14px;
        padding: 8px 10px;
      }
    }

    /* Extra small screen */
    @media (max-width: 480px) {
      form {
        padding: 15px;
      }

      h2 {
        font-size: 20px;
      }

      button {
        font-size: 12px;
        padding: 8px;
      }

      label, .checkbox-label {
        font-size: 12px;
      }

      input[type="date"] {
        padding: 6px;
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

<h2>Mark Attendance - Batch <?= htmlspecialchars($batchName); ?></h2>

<!-- Date selector (uses GET to reload the page with selected date) -->
<form method="get" style="margin-bottom: 20px;">
  <input type="hidden" name="batch_id" value="<?= htmlspecialchars($batchId); ?>">
  <label class="top-label">Select Date for Attendance:</label>
  <input type="date" name="date" value="<?= htmlspecialchars($date); ?>" onchange="this.form.submit();">
</form>

<!-- Attendance form (uses POST) -->
<form method="post">
  <input type="hidden" name="attendance_date" value="<?= htmlspecialchars($date); ?>">

  <label><input type="checkbox" id="selectAll"> Select All Students</label>

  <ul>
    <?php while ($student = $students->fetch_assoc()): ?>
      <li>
        <input 
          type="checkbox" 
          name="students[]" 
          value="<?= $student['id']; ?>" 
          class="studentCheckbox"
          <?= in_array($student['id'], $markedAttendance) ? 'checked' : ''; ?>
        >
        <span class="checkbox-label"><?= htmlspecialchars($student['name']); ?></span>
      </li>
    <?php endwhile; ?>
  </ul>

  <button type="submit" name="submit_attendance">Submit Attendance</button>
</form>

<script>
  // Toggle nav menu on mobile
  const navToggle = document.getElementById('navToggle');
  const navLinks = document.getElementById('navLinks');

  navToggle.addEventListener('click', () => {
    navLinks.classList.toggle('active');
  });

  // Select/Deselect all students checkbox
  document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.studentCheckbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
  });
</script>

</body>
</html>
