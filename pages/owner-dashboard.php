<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'PetOwner') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pet Owner Dashboard - VMS</title>
    <style>
        body { font-family: Arial, sans-serif; background: #efe; margin: 0; }
        header { background: #009933; color: white; padding: 20px; text-align: center; }
        nav { background: #333; padding: 10px; text-align: center; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
        nav a:hover { color: #ffcc00; }
        .container { padding: 30px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 0 10px #ccc; padding: 20px; margin: 20px; display: inline-block; width: 250px; vertical-align: top; }
        .card h3 { margin-top: 0; color: #009933; }
    </style>
</head>
<body>
    <header>
        <h1>Pet Owner Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['user']['full_name']; ?>!</p>
    </header>

    <nav>
        <a href="pets.php">My Pets</a>
        <a href="appointments.php">Appointments</a>
        <a href="invoices.php">Invoices</a>
        <a href="notifications.php">Notifications</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <div class="card">
            <h3>My Pets</h3>
            <p>View and manage your pet records.</p>
            <a href="pets.php">Go to Pets</a>
        </div>

        <div class="card">
            <h3>Appointments</h3>
            <p>Check upcoming and past appointments.</p>
            <a href="appointments.php">Go to Appointments</a>
        </div>

        <div class="card">
            <h3>Invoices</h3>
            <p>View bills and payment status.</p>
            <a href="invoices.php">Go to Invoices</a>
        </div>
    </div>
</body>
</html>
