<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Veterinary Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background: green;
            color: white;
            padding: 20px;
            text-align: center;
        }
        nav {
            background: #333;
            padding: 10px;
            text-align: center;
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
        .container {
            padding: 40px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            margin: 10px;
            background: green;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background: darkgreen;
        }
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>Veterinary Management System</h1>
        <p>Welcome to your clinic’s digital assistant</p>
    </header>

    <nav>
        <a href="pages/login.php">Login</a>
        <a href="pages/about.php">About</a>
        <a href="pages/contact.php">Contact</a>
    </nav>

    <div class="container">
        <h2>Manage your clinic with ease</h2>
        <p>Track pets, appointments, consultations, invoices, and inventory all in one place.</p>
        <a href="pages/login.php" class="btn">Get Started</a>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> Veterinary Management System. All rights reserved.
    </footer>
</body>
</html>
