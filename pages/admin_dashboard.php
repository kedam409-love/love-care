<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Administrator') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - VMS</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
        header { background: green; color: white; padding: 20px; text-align: center; }
        nav { background: #333; padding: 10px; text-align: center; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
        nav a:hover { color: #ffcc00; }
        .container { padding: 30px; }
        .card {
            background: #fff; border-radius: 8px; box-shadow: 0 0 10px #ccc;
            padding: 20px; margin: 20px; display: inline-block; width: 250px; vertical-align: top;
        }
        .card h3 { margin-top: 0; color: green; }
        footer { background: #333; color: white; text-align: center; padding: 15px; margin-top: 40px; }
    </style>
</head>
<body>
    <header>
        <h1>Administrator Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['user']['full_name']; ?>!</p>
    </header>

    <nav>
        <a href="pets.php">Pets</a>
        <a href="appointments.php">Appointments</a>
        <a href="consultations.php">Consultations</a>
        <a href="inventory.php">Inventory</a>
        <a href="invoices.php">Invoices</a>
        <a href="reports.php">Reports</a>
        <a href="notifications.php">Notifications</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <div class="card">
            <h3>Manage Users</h3>
            <p>Add, edit, or remove system users.</p>
            <a href="users.php">Go to Users</a>
        </div>

        <div class="card">
            <h3>System Reports</h3>
            <p>View clinic performance and activity reports.</p>
            <a href="reports.php">View Reports</a>
        </div>

        <div class="card">
            <h3>Inventory Alerts</h3>
            <p>Monitor stock levels and low‑stock notifications.</p>
            <a href="inventory.php">Check Inventory</a>
        </div>

        <div class="card">
            <h3>Appointments</h3>
            <p>Oversee all scheduled consultations.</p>
            <a href="appointments.php">Manage Appointments</a>
        </div>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> Veterinary Management System. All rights reserved.
    </footer>
</body>
</html>
