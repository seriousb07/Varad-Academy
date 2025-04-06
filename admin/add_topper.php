<?php
$conn = new mysqli("localhost", "root", "", "varad_academy");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $total = $_POST['total_marks'];
    $math = $_POST['math_marks'];
    $year = $_POST['batch_year'];

    $photoName = $_FILES['photo']['name'];
    $tmp = $_FILES['photo']['tmp_name'];
    move_uploaded_file($tmp, "../images/$photoName");

    $sql = "INSERT INTO toppers (name, total_marks, math_marks, batch_year, photo)
            VALUES ('$name', '$total', '$math', '$year', '$photoName')";
    $conn->query($sql);
    echo "<script>alert('Topper added!');</script>";
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Name" required><br>
    <input type="number" name="total_marks" placeholder="Total Marks" required><br>
    <input type="number" name="math_marks" placeholder="Math Marks" required><br>
    <input type="text" name="batch_year" placeholder="Batch Year" required><br>
    <input type="file" name="photo" required><br>
    <button type="submit">Add Topper</button>
</form>
