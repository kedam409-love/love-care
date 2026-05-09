<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Receptionist') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receptionist Dashboard - VMS</title>
    <style>
        body { font-family: Arial, sans-serif; background: #ffe; margin: 0; }
        header { background: #cc6600; color: white; padding: 20px; text-align: center; }
        nav { background: #333; padding: 10px; text-align: center; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
        nav a:hover { color: #ffcc00; }
        .container { padding: 30px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 0 10px #ccc; padding: 20px; margin: 20px; display: inline-block; width: 250px; vertical-align: top; }
        .card h3 { margin-top: 0; color: #cc6600; }
    </style>
</head>
<body>
    <header>
        <h1>Receptionist Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['user']['full_name']; ?>! You can manage Pet Owners, Pets, Appointments, and Billing.</p>
    </header>

    <nav>
        <a href="users.php">Manage Pet Owners</a>
        <a href="appointments.php">Schedule Appointments</a>
        <a href="pets.php">Register Pets</a>
        <a href="invoices.php">Billing</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <div class="card">
            <h3>Register Pet Owners</h3>
            <p>Add new Pet Owners to the system.</p>
            <a href="users.php">Go to Pet Owners</a>
        </div>

        <div class="card">
            <h3>Register New Pet</h3>
            <p>Add pet details for new clients.</p>
            <a href="pets.php">Go to Pet Registration</a>
        </div>

        <div class="card">
            <h3>Manage Appointments</h3>
            <p>Book and update appointments.</p>
            <a href="appointments.php">Go to Appointments</a>
        </div>

        <div class="card">
            <h3>Billing</h3>
            <p>Handle invoices and payments.</p>
            <a href="invoices.php">Go to Billing</a>
        </div>
    </div>
</body>
</html>
