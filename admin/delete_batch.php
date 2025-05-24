<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "varad_academy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if batch_id is provided
if (isset($_GET['batch_id'])) {
    $batch_id = $_GET['batch_id'];

    // First, delete attendance records associated with the batch
    $delete_attendance_query = "DELETE FROM attendance WHERE batch_id = ?";
    $stmt = $conn->prepare($delete_attendance_query);
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();

    // Now, delete the batch itself
    $delete_batch_query = "DELETE FROM batches WHERE id = ?";
    $stmt = $conn->prepare($delete_batch_query);
    $stmt->bind_param("i", $batch_id);

    if ($stmt->execute()) {
        echo "Batch and associated attendance records deleted successfully!";
        header("Location: dashboard.php"); // Redirect to the dashboard after deletion
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Batch ID not specified!";
}
?>
