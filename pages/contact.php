<?php
// Public Contact page — no login required
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Us - Love Care Veterinary Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: url('../assets/cats.jpg') no-repeat center center/cover;
            color: #333;
        }
        .overlay {
            background: rgba(255,255,255,0.6); /* semi-transparent */
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
        .contact-section {
            max-width: 800px;
            margin: 40px auto;
            text-align: center;
        }
        .contact-section h1 {
            color: #0483c3;
            margin-bottom: 20px;
        }
        .contact-section p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }
        input, textarea {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }
        button {
            background: #00aaff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #0088cc;
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

        <div class="contact-section">
            <h1>Contact Love Care Veterinary Clinic</h1>
            <p>We’d love to hear from you! Please reach out with any questions, feedback, or appointment requests.</p>

            <form action="send_message.php" method="post">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
                <button type="submit"><i class="fa-solid fa-paper-plane"></i> Send Message</button>
            </form>
        </div>

        <footer>
            &copy; <?php echo date("Y"); ?> Love Care Veterinary Clinic. 
            Contact us: kedam409@gmail.com | +237 621726670
        </footer>
    </div>
</body>
</html>
