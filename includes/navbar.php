<?php
$currentPage = basename($_SERVER['PHP_SELF']); // get current file name
?>
<link rel="stylesheet" href="../assets/css/nav.css">

<nav>
    <?php if ($_SESSION['user']['role'] == 'Administrator') { ?>
        <a href="admin-dashboard.php" class="<?php echo ($currentPage=='admin-dashboard.php')?'active':''; ?>">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
        <a href="users.php" class="<?php echo ($currentPage=='users.php')?'active':''; ?>">
            <i class="fa-solid fa-users"></i> Manage Users
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
    <?php } elseif ($_SESSION['user']['role'] == 'Veterinarian') { ?>
        <a href="vet_dashboard.php" class="<?php echo ($currentPage=='vet_dashboard.php')?'active':''; ?>">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
        <a href="consultations.php" class="<?php echo ($currentPage=='consultations.php')?'active':''; ?>">
            <i class="fa-solid fa-stethoscope"></i> Consultations
        </a>
    <?php } elseif ($_SESSION['user']['role'] == 'PetOwner') { ?>
        <a href="owner_dashboard.php" class="<?php echo ($currentPage=='owner_dashboard.php')?'active':''; ?>">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
        <a href="pets.php" class="<?php echo ($currentPage=='pets.php')?'active':''; ?>">
            <i class="fa-solid fa-dog"></i> My Pets
        </a>
        <a href="appointments.php" class="<?php echo ($currentPage=='appointments.php')?'active':''; ?>">
            <i class="fa-solid fa-calendar-check"></i> My Appointments
        </a>
    <?php } ?>

    <!-- Common links -->
    <a href="appointments.php" class="<?php echo ($currentPage=='appointments.php')?'active':''; ?>">
        <i class="fa-solid fa-calendar-check"></i> Appointments
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
</nav>
