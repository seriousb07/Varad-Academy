<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Varad Academy</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f5f5;
    }

    .navbar {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      color: #333;
      padding: 12px 30px;
      position: sticky;
      top: 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 100;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      
      flex-wrap: wrap;
    }

    .navbar img {
      height: 60px;
    }

    .menu-toggle {
      display: none;
      cursor: pointer;
    }

    .menu-toggle svg {
      width: 30px;
      height: 30px;
      fill: #333;
    }

    .navbar-links {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .navbar-links a {
      color: #333;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .navbar-links a:hover {
      color: #b30000;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
      .menu-toggle {
        display: block;
      }

      .navbar-links {
        width: 100%;
        display: none;
        flex-direction: column;
        align-items: flex-start;
        background-color: #fff;
        padding: 10px 0;
      }

      .navbar-links.active {
        display: flex;
      }

      .navbar-links a {
        padding: 10px 20px;
        width: 100%;
        border-top: 1px solid #ddd;
      }
    }

    .section {
    
      margin: 20px;
      padding: 20px;
      background: #fff;
    }

    .slider {
        position: relative;
  width: 100%;
  max-width: 100%;
  height: 80vh; /* ⬅️ Reduce this from 80vh to 50vh or any suitable value */
  overflow: hidden;
  margin: auto;
}

.slider img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* or 'contain' if you want no cropping */
  display: none;
}

    .slider img.active {
      display: block;
    }

    .dots {
      text-align: center;
      position: absolute;
      bottom: 20px;
      width: 100%;
      display: flex;
      justify-content: center;
      gap: 8px;
    }

    .dot {
      height: 10px;
      width: 24px;
      background-color: #d3d3d3;
      border-radius: 20px;
      transition: all 0.3s ease;
      cursor: pointer;
      opacity: 0.7;
    }

    .dot:hover,
    .dot.active {
      background-color: rgb(227, 64, 88);
      width: 32px;
      opacity: 1;
      box-shadow: 0 0 8px rgba(230, 0, 35, 0.4);
    }

    .topper-card {
      border: 1px solid #ccc;
      padding: 15px;
      margin: 10px;
      border-radius: 15px;
      width: 230px;
      text-align: center;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .topper-card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 10px;
    }

    .footer {
      background: #b30000;
      color: white;
      text-align: center;
      padding: 20px;
      border-radius: 15px 15px 0 0;
    }

    .contact-form input,
    .contact-form select,
    .contact-form textarea {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 16px;
    }

    .contact-form button {
      background-color: rgb(7, 158, 67);
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }

    .contact-form button:hover {
      background-color: #990000;
    }

    .about-layout {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      align-items: center;
    }

    .founder-img {
      width: 300px;
      border-radius: 15px;
    }

    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar img {
        height: 60px;
        margin-bottom: 10px;
      }

      .menu-toggle {
        display: block;
      }

      .navbar-links {
        display: none;
        width: 100%;
      }

      .navbar-links.active {
        display: flex;
      }

      .slider {
        height: 50vh;
      }

      .about-layout,
      .contact-section {
        flex-direction: column;
      }

      video {
        max-width: 100% !important;
      }
    }
  </style>
</head>
<body>

<div class="navbar">
    <img src="./images/logo name red.png" alt="Varad Academy Logo">
    <div class="menu-toggle" onclick="toggleMenu()">
      <svg viewBox="0 0 100 80">
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
  <?php
    $conn = new mysqli("localhost", "root", "", "varad_academy");
    $result = $conn->query("SELECT * FROM slider_images");
    $index = 0;
    while($row = $result->fetch_assoc()) {
      echo '<img src="admin/' . $row['image_path'] . '" class="slide ' . ($index==0 ? 'active' : '') . '">';
      $index++;
    }
  ?>
  <div class="dots">
    <?php for ($i = 0; $i < $index; $i++) echo "<span class='dot' onclick='showSlide($i)'></span>"; ?>
  </div>
</div>


  <div id="about" class="section">
    <h2>About Us</h2>
    <div class="about-layout">
      <img src="images/founder.jpg" alt="Founder" class="founder-img">
      <div>
        <p>At Varad Academy, we are committed to delivering quality education with a focus on mathematics for students from 5th to 10th standard. Our mission is to build strong foundational skills through concept clarity and personalized attention.</p>
        <h3>Founder: Mr. Varad Patil</h3>
        <p>Mr. Varad Patil is a passionate educator with 10+ years of experience in teaching mathematics. His approach to teaching focuses on clarity, student engagement, and practical problem-solving.</p>
      </div>
    </div>
  </div>

  <div id="toppers" class="section">
    <h2>Our Toppers</h2>
    <div style="display:flex; flex-wrap: wrap; justify-content:center;">
      <?php
        $res = $conn->query("SELECT * FROM toppers ORDER BY id DESC LIMIT 6");
        while($row = $res->fetch_assoc()) {
          echo '<div class="topper-card">
                  <img src="images/'.$row['photo'].'">
                  <h3>'.$row['name'].'</h3>
                  <p>Total: '.$row['total_marks'].'</p>
                  <p>Maths: '.$row['math_marks'].'</p>
                  <p>Batch: '.$row['batch_year'].'</p>
                </div>';
        }
      ?>
    </div>
    <center><a href="admin/view_more.php">View More</a></center>
  </div>

  <div id="map" class="section">
    <h2>Find Us</h2>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3764.485093766085!2d75.21678637481385!3d19.34813728191332!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb6926dcb64c73%3A0x627ccf015ae884a3!2sVarad%20Academy!5e0!3m2!1sen!2sin!4v1743926361349!5m2!1sen!2sin" width="100%" height="600" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
    // Hamburger toggle
    function toggleMenu() {
      document.getElementById("navbarLinks").classList.toggle("active");
    }

    // Image Slider
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

    setInterval(() => {
      slideIndex = (slideIndex + 1) % slides.length;
      showSlide(slideIndex);
    }, 4000);

    function sendWhatsApp() {
      let name = document.getElementById('name').value;
      let mobile = document.getElementById('mobile').value;
      let village = document.getElementById('village').value;
      let query = document.getElementById('query').value;
      let message = document.getElementById('message').value;
      let cls = document.getElementById('class').value;
      let text = `Name: ${name}%0AMobile: ${mobile}%0AVillage: ${village}%0AClass: ${cls}%0AQuery: ${query}%0AMessage: ${message}`;
      window.open(`https://wa.me/91XXXXXXXXXX?text=${text}`, '_blank'); // Replace with real number
      return false;
    }
  </script>
</body>
</html>
