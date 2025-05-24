<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Varad Academy</title>
  <link rel="stylesheet" href="./style.css" />
</head>
<body>

<?php
  $conn = new mysqli("localhost", "root", "", "varad_academy");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
?>

<!-- Navbar -->
<div class="navbar">
  <img src="../varad_academy/images/logo name red.png" alt="Varad Academy Logo">
  <div class="menu-toggle" onclick="toggleMenu()">
    <svg viewBox="0 0 100 80" width="30" height="30">
      <rect width="100" height="10"></rect>
      <rect y="30" width="100" height="10"></rect>
      <rect y="60" width="100" height="10"></rect>
    </svg>
  </div>
  <div class="navbar-links" id="navbarLinks">
    <a href="#home">Home</a>
    <a href="#about">About</a>
    <a href="#toppers">Toppers</a>
    <a href="#contact">Contact</a>
    <a href="#map">Map</a>
    <a href="admin/login.php">Admin Login</a>
  </div>
</div>

<!-- Home Slider -->
<div id="home" class="Section slider">
  <div class="slider-container" id="sliderContainer">
    <?php
      $result = $conn->query("SELECT * FROM slider_images");
      $index = 0;
      if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo '<img src="admin/' . $row['image_path'] . '" class="slide">';
          $index++;
        }
      } else {
        echo '<p>No slider images available.</p>';
      }
    ?>
  </div>

  <div class="dots">
    <?php for ($i = 0; $i < $index; $i++) echo "<span class='dot' onclick='showSlide($i)' id='dot$i'></span>"; ?>
  </div>
</div>

<!-- About Section -->
<div id="about" class="section" style="padding: 50px 20px;">
  <h2 style="text-align:center;">About Us</h2>
  <div class="founder-layout" style="display:flex; flex-wrap: wrap; align-items: center; justify-content: center;">
    <div class="founder-info" style="flex: 1; min-width: 300px; padding: 20px;">
      <h3>Founder: Mr. John Doe</h3>
      <p>Mr. John Doe is a visionary leader with over 15 years of experience in the education sector...</p>
    </div>
    <div class="founder-img" style="flex: 1; text-align:center;">
      <img src="./images/card.png" alt="Founder Image" style="max-width: 100%; border-radius: 10px;">
    </div>
  </div>
</div>

<!-- Toppers Section -->
<div id="toppers" class="section" style="padding: 50px 20px;">
  <h2 style="text-align:center;">Our Toppers</h2>
  <div style="display:flex; flex-wrap: wrap; justify-content:center; gap: 20px;">
    <?php
      $res = $conn->query("SELECT * FROM toppers ORDER BY id DESC LIMIT 6");
      if ($res && $res->num_rows > 0) {
        while($row = $res->fetch_assoc()) {
          echo '<div class="topper-card" style="border: 1px solid #ccc; border-radius:10px; padding: 15px; width:200px; text-align:center;">
                  <img src="images/'.$row['photo'].'" alt="'.$row['name'].'" style="width:100%; border-radius:8px;">
                  <h3>'.$row['name'].'</h3>
                  <p>Total: '.$row['total_marks'].'</p>
                  <p><b>Maths: '.$row['math_marks'].'</b></p>
                  <p>Batch: '.$row['batch_year'].'</p>
                </div>';
        }
      } else {
        echo '<p>No toppers available.</p>';
      }
    ?>
  </div>
  
</div>

<!-- Map Section -->
<div id="map" class="section" style="padding: 50px 20px;">
  <h2 style="text-align:center;">Find Us</h2>
  <iframe 
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3764.485093766085!2d75.21678637481385!3d19.34813728191332!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb6926dcb64c73%3A0x627ccf015ae884a3!2sVarad%20Academy!5e0!3m2!1sen!2sin!4v1745080581605!5m2!1sen!2sin" 
    allowfullscreen 
    loading="lazy" 
    referrerpolicy="no-referrer-when-downgrade"
    style="width: 100%; height: 450px; border: 0; border-radius: 10px;">
  </iframe>
</div>

<!-- Contact Section -->
<div id="contact" class="section" style="padding: 50px 20px;">
  <h2 style="text-align:center;">Contact Us</h2>
  <div class="contact-section" style="display:flex; flex-wrap: wrap; gap: 30px; align-items: center;">
    <div style="flex:1; text-align:center;">
      <video autoplay muted loop style="max-width:90%; height:auto; border-radius: 16px;">
        <source src="./images/Contact us.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
    <div style="flex:1;">
      <form class="contact-form" onsubmit="return sendWhatsApp()" style="display: flex; flex-direction: column; gap: 10px;">
        <input type="text" id="name" placeholder="Name" required>
        <input type="text" id="mobile" placeholder="Mobile Number" required>
        <input type="text" id="village" placeholder="Village">
        <select id="class">
          <option value="">Select Class</option>
          <option value="5">5th</option>
          <option value="6">6th</option>
          <option value="7">7th</option>
          <option value="8">8th</option>
          <option value="9">9th</option>
          <option value="10">10th</option>
        </select>
        <select id="query">
          <option>Maths Tuition</option>
          <option>Batch Timings</option>
          <option>Fees Inquiry</option>
          <option>Other</option>
        </select>
        <textarea id="message" placeholder="Your Message"></textarea>
        <button type="submit">Send via WhatsApp</button>
      </form>
    </div>
  </div>
</div>

<!-- Footer -->
<div class="footer" style="text-align:center; padding: 20px; background-color:#222; color:#fff; font-family: Arial, sans-serif;">
  <p style="margin: 0 0 10px 0; font-size: 16px;">Varad Academy &copy; 2025. All Rights Reserved.</p>
  <p style="margin: 0; font-size: 14px;">
    Designed and developed by 
    <a href="https://www.linkedin.com/in/shreyashbhagwat07/" target="_blank" style="color:#4db8ff; text-decoration:none;">
      Shreyash Bhagwat
    </a>
    &nbsp;|&nbsp;
    <a href="mailto:shreyashbhagwat0709@gmail.com" style="color:#4db8ff; text-decoration:none;">
      shreyashbhagwat0709@gmail.com
    </a>
  </p>
</div>



<!-- Scripts -->
<script>
  function toggleMenu() {
    document.getElementById("navbarLinks").classList.toggle("active");
  }

  function showSlide(n) {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    if (slides[n]) slides[n].classList.add('active');
    if (dots[n]) dots[n].classList.add('active');
    slideIndex = n;
  }

  let slideIndex = 0;
  const slides = document.querySelectorAll('.slide');
  if (slides.length > 0) {
    showSlide(0);
    setInterval(() => {
      slideIndex = (slideIndex + 1) % slides.length;
      showSlide(slideIndex);
    }, 4000);
  }

function sendWhatsApp() {
    const name = document.getElementById('name').value;
    const mobile = document.getElementById('mobile').value;
    const village = document.getElementById('village').value;
    const cls = document.getElementById('class').value;
    const query = document.getElementById('query').value;
    const message = document.getElementById('message').value;

    const text = `Name: ${name}%0AMobile: ${mobile}%0AVillage: ${village}%0AClass: ${cls}%0AQuery: ${query}%0AMessage: ${message}`;
    const whatsappNumber = "919834088278"; // Updated number
    window.open(`https://wa.me/${whatsappNumber}?text=${text}`, '_blank');
    return false;
}

</script>

<?php $conn->close(); ?>
</body>
</html>
