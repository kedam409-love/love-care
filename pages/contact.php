<?php
// Public Contact page — no login required
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact - Veterinary Management System</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
        header { background: #006699; color: white; padding: 20px; text-align: center; }
        nav { background: #333; padding: 10px; text-align: center; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
        nav a:hover { color: #ffcc00; }
        .container { padding: 40px; max-width: 900px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        h2 { color: #006699; }
        form { margin-top: 20px; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        input[type=submit] { background: #006699; color: white; border: none; cursor: pointer; }
        input[type=submit]:hover { background: #004466; }
        .message { margin-top: 20px; font-weight: bold; color: green; }
    </style>
</head>
<body>
    <header>
        <h1>Contact Us</h1>
    </header>

    <nav>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <h2>Get in Touch</h2>
        <p>
            Have questions about the Veterinary Management System? Need support or want to share feedback? 
            Fill out the form below or reach us directly at <strong>muzylovelois@gmail.com</strong>.
        </p>

        <form method="POST" action="contact.php">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" rows="6" placeholder="Your Message" required></textarea>
            <input type="submit" name="send" value="Send Message">
        </form>

        <?php
        if (isset($_POST['send'])) {
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            echo "<p class='message'>Thank you, $name! We’ll get back to you at $email.</p>";
        }
        ?>
    </div>
</body>
</html>
