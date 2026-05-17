<?php
$currentPage = basename($_SERVER['PHP_SELF']); // get current file name
?>
<link rel="stylesheet" href="../assets/css/nav.css">

<nav class="navbar">
    <div class="nav-container">
        <div class="menu-toggle" id="menu-toggle">
            <i class="fa-solid fa-bars"></i>
        </div>
        <div class="nav-links" id="nav-links">
            <?php if ($_SESSION['user']['role'] == 'Administrator') { ?>
                <a href="admin_dashboard.php" class="<?php echo ($currentPage=='admin_dashboard.php')?'active':''; ?>">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                <a href="users.php" class="<?php echo ($currentPage=='users.php')?'active':''; ?>">
                    <i class="fa-solid fa-users"></i> Manage Users
                </a>
                <a href="inventory.php" class="<?php echo ($currentPage=='inventory.php')?'active':''; ?>">
                    <i class="fa-solid fa-boxes"></i> Inventory
                </a>
                <a href="view_messages.php" class="<?php echo ($currentPage=='view_messages.php')?'active':''; ?>">
                    <i class="fa-solid fa-envelope"></i> Messages
                </a>
                <a href="appointments.php" class="<?php echo ($currentPage=='appointments.php')?'active':''; ?>">
                    <i class="fa-solid fa-calendar-check"></i> Appointments
                </a>
                <a href="consultations.php" class="<?php echo ($currentPage=='consultations.php')?'active':''; ?>">
                    <i class="fa-solid fa-stethoscope"></i> Consultations
                </a>
                <a href="invoices.php" class="<?php echo ($currentPage=='invoices.php')?'active':''; ?>">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Invoices
                </a>
                <a href="reports.php" class="<?php echo ($currentPage=='reports.php')?'active':''; ?>">
                    <i class="fa-solid fa-chart-line"></i> Reports
                </a>
                <a href="notifications.php" class="<?php echo ($currentPage=='notifications.php')?'active':''; ?>">
                    <i class="fa-solid fa-bell"></i> Notifications
                </a>
                <a href="logout.php" class="<?php echo ($currentPage=='logout.php')?'active':''; ?>">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            <?php } elseif ($_SESSION['user']['role'] == 'Receptionist') { ?>
                <a href="reception_dashboard.php" class="<?php echo ($currentPage=='reception_dashboard.php')?'active':''; ?>">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                <a href="users.php" class="<?php echo ($currentPage=='users.php')?'active':''; ?>">
                    <i class="fa-solid fa-user"></i> Manage Pet Owners
                </a>
                <a href="pets.php" class="<?php echo ($currentPage=='pets.php')?'active':''; ?>">
                    <i class="fa-solid fa-dog"></i> Register Pets
                </a>
                <a href="appointments.php" class="<?php echo ($currentPage=='appointments.php')?'active':''; ?>">
                    <i class="fa-solid fa-calendar-check"></i> Appointments
                </a>
                <a href="invoices.php" class="<?php echo ($currentPage=='invoices.php')?'active':''; ?>">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Invoices
                </a>
                <a href="notifications.php" class="<?php echo ($currentPage=='notifications.php')?'active':''; ?>">
                    <i class="fa-solid fa-bell"></i> Notifications
                </a>
                <a href="logout.php" class="<?php echo ($currentPage=='logout.php')?'active':''; ?>">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            <?php } elseif ($_SESSION['user']['role'] == 'Veterinarian') { ?>
                <a href="vet_dashboard.php" class="<?php echo ($currentPage=='vet_dashboard.php')?'active':''; ?>">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                <a href="consultations.php" class="<?php echo ($currentPage=='consultations.php')?'active':''; ?>">
                    <i class="fa-solid fa-stethoscope"></i> Consultations
                </a>
                <a href="appointments.php" class="<?php echo ($currentPage=='appointments.php')?'active':''; ?>">
                    <i class="fa-solid fa-calendar-check"></i> Appointments
                </a>
                <a href="notifications.php" class="<?php echo ($currentPage=='notifications.php')?'active':''; ?>">
                    <i class="fa-solid fa-bell"></i> Notifications
                </a>
                <a href="logout.php" class="<?php echo ($currentPage=='logout.php')?'active':''; ?>">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            <?php } elseif ($_SESSION['user']['role'] == 'PetOwner') { ?>
                <a href="owner_dashboard.php" class="<?php echo ($currentPage=='owner_dashboard.php')?'active':''; ?>">
                    <i class="fa-solid fa-house-user"></i> Dashboard
                </a>
                <a href="pets.php" class="<?php echo ($currentPage=='pets.php')?'active':''; ?>">
                    <i class="fa-solid fa-dog"></i> My Pets
                </a>
                <a href="appointments.php" class="<?php echo ($currentPage=='appointments.php')?'active':''; ?>">
                    <i class="fa-solid fa-calendar-check"></i> My Appointments
                </a>
                <a href="invoices.php" class="<?php echo ($currentPage=='invoices.php')?'active':''; ?>">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Invoices
                </a>
                <a href="notifications.php" class="<?php echo ($currentPage=='notifications.php')?'active':''; ?>">
                    <i class="fa-solid fa-bell"></i> Notifications
                </a>
                <a href="logout.php" class="<?php echo ($currentPage=='logout.php')?'active':''; ?>">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            <?php } ?>
        </div>
    </div>
</nav>

<script>
    // Toggle menu on mobile
    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("nav-links").classList.toggle("active");
    });
</script>
