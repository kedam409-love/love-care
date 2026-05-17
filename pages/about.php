<?php
// Public About page — no login required
?>
<!DOCTYPE html>
<html>
<head>
    <title>About Us - Love Care Veterinary Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: url('../assets/cats.jpg') no-repeat center center/cover;
            color: #5a5a5a;
        }
        .overlay {
             background: rgba(255,255,255,0.6); /* lighter transparency */
             backdrop-filter: blur(6px);        /* frosted glass effect */
             min-height: 100vh;
             padding: 20px;
}

        header {
            background: #024363;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header img.logo {
            height: 50px;
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
        .about-section {
            max-width: 900px;
            margin: 40px auto;
            text-align: center;
        }
        .about-section h1 {
            color: #0483c3;
            margin-bottom: 20px;
        }
        .about-section p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .mission {
            background: rgba(2, 67, 99, 0.1);
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        footer {
            background: rgba(0,0,0,0.8);
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="overlay">
        <header>
            <img src="../assets/logo.png" alt="Clinic Logo" class="logo">
            <nav>
                <a href="../index.php">Home</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="login.php">Login</a>
            </nav>
        </header>

        <div class="about-section">
            <h1>About Love Care Veterinary Clinic</h1>
            <p>
                At Love Care Veterinary Clinic, we believe every pet deserves compassion, 
                quality healthcare, and a safe environment. Our Veterinary Management System 
                was designed to make it easier for clinics to provide excellent care while 
                staying organized and efficient.
            </p>
            <p>
                From scheduling appointments to maintaining detailed pet records, 
                our system ensures that veterinarians can focus on what matters most — 
                the health and happiness of your pets.
            </p>

            <div class="mission">
                <h2>Our Mission</h2>
                <p>
                    To empower veterinary professionals in Cameroon and beyond with 
                    modern tools that simplify management, improve patient care, 
                    and build stronger bonds between clinics and pet owners.
                </p>
            </div>
        </div>

        <footer>
            &copy; <?php echo date("Y"); ?> Love Care Veterinary Clinic. 
            Contact us: kedam409@gmail.com | +237 621726670
        </footer>
    </div>
</body>
</html>
