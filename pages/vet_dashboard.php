<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Veterinarian') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Veterinarian Dashboard - VMS</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef; margin: 0; }
        header { background: #0066cc; color: white; padding: 20px; text-align: center; }
        nav { background: #333; padding: 10px; text-align: center; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
        nav a:hover { color: #ffcc00; }
        .container { padding: 30px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 0 10px #ccc; padding: 20px; margin: 20px; display: inline-block; width: 250px; vertical-align: top; }
        .card h3 { margin-top: 0; color: #0066cc; }
    </style>
</head>
<body>
    <header>
        <h1>Veterinarian Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['user']['full_name']; ?>!</p>
    </header>

    <nav>
        <a href="appointments.php">Appointments</a>
        <a href="consultations.php">Consultations</a>
        <a href="pets.php">Patient Records</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <div class="card">
            <h3>Upcoming Appointments</h3>
            <p>View and manage your schedule.</p>
            <a href="appointments.php">Go to Appointments</a>
        </div>

        <div class="card">
            <h3>Consultations</h3>
            <p>Record diagnoses and treatments.</p>
            <a href="consultations.php">Go to Consultations</a>
        </div>
    </div>
</body>
</html>
