<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Receptionist') {
    header("Location: login.php");
    exit;
}

include '../config/db.php'; // adjust path if needed

// Counts
$ownersCount = $conn->query("SELECT COUNT(*) AS total FROM owners")->fetch_assoc()['total'];
$petsCount = $conn->query("SELECT COUNT(*) AS total FROM pets")->fetch_assoc()['total'];
$appointmentsCount = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE appointment_date >= CURDATE()")->fetch_assoc()['total'];
$invoicesCount = $conn->query("SELECT COUNT(*) AS total FROM invoices WHERE payment_status='Pending'")->fetch_assoc()['total'];
$inventoryCount = $conn->query("SELECT COUNT(*) AS total FROM inventory WHERE quantity < threshold")->fetch_assoc()['total'];
$notificationsCount = $conn->query("SELECT COUNT(*) AS total FROM notifications WHERE status='Unread'")->fetch_assoc()['total'];

// Preview lists
$recentOwners = $conn->query("SELECT owner_name FROM owners ORDER BY owner_id DESC LIMIT 3");
$recentPets = $conn->query("SELECT pet_name, species FROM pets ORDER BY pet_id DESC LIMIT 3");
$nextAppointments = $conn->query("SELECT appointment_date, appointment_time, status FROM appointments WHERE appointment_date >= CURDATE() ORDER BY appointment_date ASC LIMIT 3");
$unpaidInvoices = $conn->query("SELECT invoice_id, amount, payment_status FROM invoices WHERE payment_status='Pending' ORDER BY invoice_id DESC LIMIT 3");
$lowStockItems = $conn->query("SELECT item_name, quantity, threshold FROM inventory WHERE quantity < threshold ORDER BY quantity ASC LIMIT 3");
$unreadNotifications = $conn->query("SELECT message, notification_date FROM notifications WHERE status='Unread' ORDER BY notification_date DESC LIMIT 3");

// Consultations preview
$recentConsultations = $conn->query("
    SELECT c.consultation_id, c.diagnosis, c.treatment, a.appointment_date,
           p.pet_name, u.full_name AS vet_name
    FROM consultations c
    JOIN appointments a ON c.appointment_id = a.appointment_id
    JOIN pets p ON a.pet_id = p.pet_id
    LEFT JOIN users u ON a.vet_id = u.user_id
    ORDER BY a.appointment_date DESC
    LIMIT 3
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receptionist Dashboard - VMS</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="stylesheet" href="../assets/css/badges.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h1><i class="fa-solid fa-gauge"></i> Receptionist Dashboard</h1>
            <p>Welcome, <?php echo $_SESSION['user']['full_name']; ?>! You can manage Pet Owners, Pets, Appointments, Billing, Inventory, Consultations, and Notifications.</p>
        </div>
    </div>

    <div class="container">
        <!-- Owners -->
        <div class="card">
            <h3><i class="fa-solid fa-user"></i> Pet Owners</h3>
            <p>Total Owners: <strong><?php echo $ownersCount; ?></strong></p>
            <ul>
                <?php while($row = $recentOwners->fetch_assoc()) { ?>
                    <li><?php echo $row['owner_name']; ?></li>
                <?php } ?>
            </ul>
            <a class="btn-blue" href="users.php">Go to Pet Owners</a>
        </div>

        <!-- Pets -->
        <div class="card">
            <h3><i class="fa-solid fa-dog"></i> Pets</h3>
            <p>Total Pets: <strong><?php echo $petsCount; ?></strong></p>
            <ul>
                <?php while($row = $recentPets->fetch_assoc()) { ?>
                    <li><?php echo $row['pet_name']; ?> (<?php echo $row['species']; ?>)</li>
                <?php } ?>
            </ul>
            <a class="btn-green" href="pets.php">Go to Pet Registration</a>
        </div>

        <!-- Appointments -->
        <div class="card">
            <h3><i class="fa-solid fa-calendar-check"></i> Appointments</h3>
            <p>Upcoming Appointments: <strong><?php echo $appointmentsCount; ?></strong></p>
            <ul>
                <?php while($row = $nextAppointments->fetch_assoc()) { ?>
                    <li>
                        <?php echo $row['appointment_date']; ?> at <?php echo $row['appointment_time']; ?>
                        <span class="badge badge-<?php echo strtolower($row['status']); ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </li>
                <?php } ?>
            </ul>
            <a class="btn-blue" href="appointments.php">Go to Appointments</a>
        </div>

        <!-- Invoices -->
        <div class="card">
            <h3><i class="fa-solid fa-file-invoice-dollar"></i> Billing</h3>
            <p>Unpaid Invoices: <strong><?php echo $invoicesCount; ?></strong></p>
            <ul>
                <?php while($row = $unpaidInvoices->fetch_assoc()) { ?>
                    <li>
                        #<?php echo $row['invoice_id']; ?> — <?php echo $row['amount']; ?> FCFA
                        <span class="badge badge-<?php echo strtolower($row['payment_status']); ?>">
                            <?php echo $row['payment_status']; ?>
                        </span>
                    </li>
                <?php } ?>
            </ul>
            <a class="btn-green" href="invoices.php">Go to Billing</a>
        </div>

        <!-- Inventory -->
        <div class="card">
            <h3><i class="fa-solid fa-boxes-stacked"></i> Inventory</h3>
            <p>Low Stock Items: <strong><?php echo $inventoryCount; ?></strong></p>
            <ul>
                <?php while($row = $lowStockItems->fetch_assoc()) { ?>
                    <li>
                        <?php echo $row['item_name']; ?> — Qty: <?php echo $row['quantity']; ?> (Threshold: <?php echo $row['threshold']; ?>)
                        <span class="badge badge-warning">Low</span>
                    </li>
                <?php } ?>
            </ul>
            <a class="btn-blue" href="inventory.php">Go to Inventory</a>
        </div>

        <!-- Consultations -->
        <div class="card">
            <h3><i class="fa-solid fa-stethoscope"></i> Consultations</h3>
            <p>Recent Consultations:</p>
            <ul>
                <?php while($row = $recentConsultations->fetch_assoc()) { ?>
                    <li>
                        <?php echo $row['pet_name']; ?> with <?php echo $row['vet_name']; ?> on <?php echo $row['appointment_date']; ?>
                        <br><small>Diagnosis: <?php echo $row['diagnosis']; ?> | Treatment: <?php echo $row['treatment']; ?></small>
                    </li>
                <?php } ?>
            </ul>
            <a class="btn-green" href="consultations.php">Go to Consultations</a>
        </div>

        <!-- Notifications -->
        <div class="card">
            <h3><i class="fa-solid fa-bell"></i> Notifications</h3>
            <p>Unread Notifications: <strong><?php echo $notificationsCount; ?></strong></p>
            <ul>
                <?php while($row = $unreadNotifications->fetch_assoc()) { ?>
                    <li>
                        <?php echo $row['message']; ?>
                        <span class="badge badge-info"><?php echo $row['notification_date']; ?></span>
                    </li>
                <?php } ?>
            </ul>
            <a class="btn-green" href="notifications.php">View Notifications</a>
        </div>
    </div>

    <div class="container">
        <div class="card" style="text-align:center;">
            <a href="reception_dashboard.php" class="btn-blue">
                <i class="fa-solid fa-gauge"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
