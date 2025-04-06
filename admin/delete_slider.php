<?php
$conn = new mysqli("localhost", "root", "", "varad_academy");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Get the image path from the database
    $stmt = $conn->prepare("SELECT image_path FROM slider_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($img);
    $stmt->fetch();
    $stmt->close();

    if ($img) {
        $imagePath = "../images/" . $img;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Delete from database
        $deleteStmt = $conn->prepare("DELETE FROM slider_images WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        $deleteStmt->execute();
        $deleteStmt->close();
    }
}

header("Location: dashboard.php");
exit();
?>
