<?php
$conn = new mysqli("localhost", "root", "", "varad_academy");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the photo filename from the database
    $get = $conn->query("SELECT photo FROM toppers WHERE id = $id");

    if ($get && $get->num_rows > 0) {
        $photo = $get->fetch_assoc()['photo'];

        // Delete the image from the server
        $imagePath = "../images/" . $photo;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Delete the topper from the database
        $conn->query("DELETE FROM toppers WHERE id = $id");
    }
}

header("Location: dashboard.php");
exit();
?>
