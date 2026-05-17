<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Veterinary Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: white;
        }

        /* Background slideshow with fade */
        body {
            position: relative;
            overflow-x: hidden;
        }
        .bg-slide {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            animation: fadeSlideshow 24s infinite;
        }
        .bg1 { background-image: url('assets/dog.jpg'); animation-delay: 0s; }
        .bg2 { background-image: url('assets/CAT.jpg'); animation-delay: 6s; }
        .bg3 { background-image: url('assets/cats.jpg'); animation-delay: 12s; }
        .bg4 { background-image: url('assets/Olp.webp'); animation-delay: 18s; }

        @keyframes fadeSlideshow {
            0%   { opacity: 0; }
            8%   { opacity: 1; }
            25%  { opacity: 1; }
            33%  { opacity: 0; }
            100% { opacity: 0; }
        }

        /* Overlay for readability */
        .overlay {
            background: rgba(0,0,0,0.6);
            min-height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 2;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 40px;
            background: rgba(4, 100, 148, 0.8);
        }
        header img.logo {
            height: 60px;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }
        nav a:hover {
            color: #ffcc00;
        }

        .hero {
            text-align: center;
            padding: 120px 20px;
        }
        .tagline {
            font-size: 22px;
            font-weight: bold;
            color: #ffcc00;
            text-shadow: 0 0 8px rgba(255, 204, 0, 0.8);
            opacity: 0;
            transform: translateY(20px);
            animation: fadeSlideGlow 2s ease-out forwards, pulseGlow 4s ease-in-out infinite;
            display: inline-block;
            cursor: pointer;
        }
        @keyframes fadeSlideGlow {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulseGlow {
            0% { text-shadow: 0 0 6px rgba(255, 204, 0, 0.6); }
            50% { text-shadow: 0 0 14px rgba(255, 204, 0, 1); }
            100% { text-shadow: 0 0 6px rgba(255, 204, 0, 0.6); }
        }

        .btn {
            background: #00aaff;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-size: 18px;
            transition: transform 0.2s ease-in-out;
            display: inline-block;
        }
        .btn:hover {
            animation: heartbeat 1s ease-in-out infinite;
            background: #0088cc;
        }
        @keyframes heartbeat {
            0% { transform: scale(1); }
            25% { transform: scale(1.05); }
            50% { transform: scale(1); }
            75% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .features {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 40px auto;
            max-width: 1000px;
        }
        .card {
            background: rgba(255,255,255,0.9);
            color: #333;
            border-radius: 8px;
            box-shadow: 0 0 10px #000;
            padding: 20px;
            margin: 15px;
            width: 220px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 0 15px rgba(0,170,255,0.6);
        }
        .card i {
            font-size: 40px;
            color: #00aaff;
            margin-bottom: 10px;
        }
        .mini-tagline {
            font-size: 14px;
            color: #00aaff;
            font-style: italic;
            margin-top: 8px;
        }

        footer {
            background: rgba(0,0,0,0.7);
            color: white;
            text-align: center;
            padding: 15px;
            position: relative;
            width: 100%;
            opacity: 0; /* hidden initially */
            transition: opacity 1s ease-in-out, transform 1s ease-in-out;
            transform: translateY(30px);
        }
        footer.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .features { flex-direction: column; align-items: center; }
            .card { width: 90%; }
        }
    </style>
</head>
<body>
    <!-- Background slides -->
    <div class="bg-slide bg1"></div>
    <div class="bg-slide bg2"></div>
    <div class="bg-slide bg3"></div>
    <div class="bg-slide bg4"></div>

    <div class="overlay">
        <header>
            <img src="assets/logo.png" alt="Clinic Logo" class="logo">
            <nav>
                <a href="pages/login.php">Login</a>
                <a href="pages/about.php">About</a>
                <a href="pages/contact.php">Contact</a>
            </nav>
        </header>

        <div class="hero">
           <h1>Love Care Veterinary System</h1>
            <p>Bringing pets, owners, and vets together in harmony</p>
            <p class="tagline">Trusted care for every pet 🐾</p>

            <a href="pages/login.php" class="btn">
                <i class="fa-solid fa-right-to-bracket"></i> Get Started
            </a>
        </div>

        <div class="features">
            <div class="card">
                <i class="fa-solid fa-calendar-check"></i>
                <h3>Appointments</h3>
                <p>Easily schedule and track visits.</p>
                <p class="mini-tagline">Never miss a visit 🐾</p>
            </div>
            <div class="card">
                <i class="fa-solid fa-dog"></i>
                <h3>Pet Records</h3>
                <p>Maintain detailed medical histories.</p>
                <p class="mini-tagline">Every detail matters 🐶</p>
            </div>
            <div class="card">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <h3>Billing</h3>
                <p>Generate invoices and process payments.</p>
                <p class="mini-tagline">Clear and simple 💳</p>
            </div>
            <div class="card">
                <i class="fa-solid fa-syringe"></i>
                <h3>Inventory</h3>
                <p>Monitor and manage stock levels.</p>
                <p class="mini-tagline">Stay stocked, stay ready 💉</p>
            </div>
        </div> <!-- end of features -->

      
        <!-- Footer -->
        <footer>
            &copy; <?php echo date("Y"); ?> Love Care Veterinary Clinic. 
            Contact us: kedam409@gmail.com | +237 621726670
        </footer>
    </div> <!-- end of overlay -->

    <script>
    // Fade in footer when scrolled into view
    document.addEventListener("scroll", function() {
        const footer = document.querySelector("footer");
        const rect = footer.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        if (rect.top < windowHeight) {
            footer.classList.add("visible");
        } else {
            footer.classList.remove("visible");
        }
    });
    </script>
</body>
</html>
