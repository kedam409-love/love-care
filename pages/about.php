<?php
// Public About page — no login required
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>About - Veterinary Management System</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
        header { background: #006699; color: white; padding: 20px; text-align: center; }
        nav { background: #333; padding: 10px; text-align: center; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
        nav a:hover { color: #ffcc00; }
        .container { padding: 40px; max-width: 900px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        h2 { color: #006699; }
        p { line-height: 1.6; }
    </style>
</head>
<body>
    <header>
        <h1>About the Veterinary Management System</h1>
    </header>

    <nav>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <h2>Our Mission</h2>
        <p>
            The Veterinary Management System (VMS) streamlines daily operations in veterinary clinics —
            managing appointments, pet records, billing, and user roles efficiently.
        </p>

        <h2>Features</h2>
        <ul>
            <li>🐾 Pet registration and medical history tracking</li>
            <li>📅 Appointment scheduling and management</li>
            <li>💳 Billing and invoice generation</li>
            <li>👩‍💼 Role-based access for Administrators, Veterinarians, Receptionists, and Pet Owners</li>
        </ul>
    </div>
</body>
</html>
