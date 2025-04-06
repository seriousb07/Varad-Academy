<?php
$conn = new mysqli("localhost", "root", "", "varad_academy");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $image = $_FILES['slider_image']['name'];
    $tmp = $_FILES['slider_image']['tmp_name'];
    move_uploaded_file($tmp, "../images/$image");

    $sql = "INSERT INTO slider_images (image_path) VALUES ('$image')";
    $conn->query($sql);
    echo "<script>alert('Slider image added!');</script>";
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="slider_image" required><br>
    <button type="submit">Upload</button>
</form>
