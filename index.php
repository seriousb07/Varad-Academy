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

<div id="home" class="Section slider">
  <div class="slider-container" id="sliderContainer">
    <?php
      $conn = new mysqli("localhost", "root", "", "varad_academy");
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
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

<script>
  let slideIndex = 0;
  const slides = document.querySelectorAll('.slide');
  const dots = document.querySelectorAll('.dot');

  function showSlide(n) {
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    slides[n].classList.add('active');
    dots[n].classList.add('active');
    slideIndex = n;
  }

  if (slides.length > 0) {
    showSlide(0);
    setInterval(() => {
      slideIndex = (slideIndex + 1) % slides.length;
      showSlide(slideIndex);
    }, 4000);
  }
</script>

<div id="about" class="section">
  <h2>About Us</h2>
  <div class="founder-layout">
    <div class="founder-info">
      <h3>Founder: Mr. John Doe</h3>
      <p>Mr. John Doe is a visionary leader with over 15 years of experience in the education sector...</p>
    </div>
    <div class="founder-img">
      <img src="./images/card.png" alt="Founder Image">
    </div>
  </div>
</div>

<div id="toppers" class="section">
  <h2>Our Toppers</h2>
  <div style="display:flex; flex-wrap: wrap; justify-content:center;">
    <?php
      $res = $conn->query("SELECT * FROM toppers ORDER BY id DESC LIMIT 6");
      if ($res && $res->num_rows > 0) {
        while($row = $res->fetch_assoc()) {
          echo '<div class="topper-card">
                  <img src="images/'.$row['photo'].'" alt="'.$row['name'].'">
                  <h3>'.$row['name'].'</h3>
                  <p>Total: '.$row['total_marks'].'</p>
                  <b><p>Maths: '.$row['math_marks'].'</p></b>
                  <p>Batch: '.$row['batch_year'].'</p>
                </div>';
        }
      } else {
        echo '<p>No toppers available.</p>';
      }
    ?>
  </div>
  <center><a href="admin/view_more.php">View More</a></center>
</div>

<div id="map" class="section">
  <h2>Find Us</h2>
  <iframe src="https://www.google.com/maps/embed?pb=..." width="100%" height="600" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
</div>

<div id="contact" class="section">
  <h2>Contact Us</h2>
  <div class="contact-section" style="display:flex; flex-wrap: wrap; gap: 30px; align-items: center;">
    <div style="flex:1; text-align:center;">
      <video autoplay muted loop style="max-width:90%; height:auto; border-radius: 16px;">
        <source src="./images/Contact us.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
    <div style="flex:1;">
      <form class="contact-form" onsubmit="return sendWhatsApp()">
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

<div class="footer">
  <p>Varad Academy &copy; 2025. All Rights Reserved.</p>
</div>

<script>
  function toggleMenu() {
    document.getElementById("navbarLinks").classList.toggle("active");
  }

  function sendWhatsApp() {
    const name = document.getElementById('name').value;
    const mobile = document.getElementById('mobile').value;
    const village = document.getElementById('village').value;
    const cls = document.getElementById('class').value;
    const query = document.getElementById('query').value;
    const message = document.getElementById('message').value;
    const text = `Name: ${name}%0AMobile: ${mobile}%0AVillage: ${village}%0AClass: ${cls}%0AQuery: ${query}%0AMessage: ${message}`;
    const whatsappNumber = "91XXXXXXXXXX"; // Replace with your WhatsApp number
    window.open(`https://wa.me/${whatsappNumber}?text=${text}`, '_blank');
    return false;
  }
</script>

</body>
</html>
<?php $conn->close(); ?>
