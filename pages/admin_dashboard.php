<?php
session_start();
require __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Administrator') {
    header("Location: login.php");
    exit;
}

// Fetch statistics
$totalPets = $conn->query("SELECT COUNT(*) FROM pets")->fetch_row()[0];
$totalOwners = $conn->query("SELECT COUNT(*) FROM owners")->fetch_row()[0];
$totalAppointments = $conn->query("SELECT COUNT(*) FROM appointments")->fetch_row()[0];
$totalPayments = $conn->query("SELECT COALESCE(SUM(amount),0) FROM invoices")->fetch_row()[0];

// Low stock items
$lowStock = $conn->query("SELECT item_name, quantity, threshold 
                          FROM inventory 
                          WHERE quantity < threshold 
                          ORDER BY quantity ASC 
                          LIMIT 5");

// Recent consultations
$recentConsultations = $conn->query("
    SELECT c.consultation_id, c.diagnosis, c.treatment, a.appointment_date, 
           p.pet_name, u.full_name AS vet_name
    FROM consultations c
    JOIN appointments a ON c.appointment_id = a.appointment_id
    JOIN pets p ON a.pet_id = p.pet_id
    LEFT JOIN users u ON a.vet_id = u.user_id
    ORDER BY a.appointment_date DESC
    LIMIT 5
");

// Monthly appointments
$appointmentsData = $conn->query("
    SELECT MONTH(appointment_date) AS month, COUNT(*) AS total
    FROM appointments
    GROUP BY MONTH(appointment_date)
    ORDER BY month
");

// Monthly payments
$paymentsData = $conn->query("
    SELECT MONTH(payment_date) AS month, SUM(amount) AS total
    FROM invoices
    GROUP BY MONTH(payment_date)
    ORDER BY month
");

// Convert results into arrays
$appointmentsPerMonth = array_fill(1, 12, 0);
while($row = $appointmentsData->fetch_assoc()) {
    $appointmentsPerMonth[(int)$row['month']] = (int)$row['total'];
}

$paymentsPerMonth = array_fill(1, 12, 0);
while($row = $paymentsData->fetch_assoc()) {
    $paymentsPerMonth[(int)$row['month']] = (int)$row['total'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Love Care VMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
    <style>
        body { font-family:'Segoe UI', sans-serif; background:#f4f4f4; color:#333333; margin:0; }
        .container { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; padding:30px; }
        .card { background:#ffffff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); padding:20px; transition:transform 0.2s ease; }
        .card:hover { transform:translateY(-5px); }
        .card h3 { color:#001f3f; margin-top:0; }
        .icon { font-size:40px; color:#1e90ff; margin-bottom:10px; }
        ul { list-style:none; padding:0; }
        ul li { padding:5px 0; }
        .charts { display:flex; flex-wrap:wrap; justify-content:center; gap:30px; margin:30px; }
        canvas { background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); padding:20px; }
        .btn-blue { background:#1e90ff; color:white; padding:10px 20px; border:none; border-radius:6px; font-weight:bold; cursor:pointer; }
        .btn-blue:hover { background:#005cbf; }
        .btn-green { background:#27ae60; color:white; padding:10px 20px; border:none; border-radius:6px; font-weight:bold; cursor:pointer; }
        .btn-green:hover { background:#1e8449; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="card"><div class="icon"><i class="fa-solid fa-paw"></i></div><h3>Total Pets</h3><p><?php echo $totalPets; ?></p></div>
        <div class="card"><div class="icon"><i class="fa-solid fa-user"></i></div><h3>Total Owners</h3><p><?php echo $totalOwners; ?></p></div>
        <div class="card"><div class="icon"><i class="fa-solid fa-calendar-check"></i></div><h3>Total Appointments</h3><p><?php echo $totalAppointments; ?></p></div>
        <div class="card"><div class="icon"><i class="fa-solid fa-dollar-sign"></i></div><h3>Total Payments</h3><p><?php echo number_format($totalPayments,2); ?> FCFA</p></div>
    </div>

    <div class="container">
        <div class="card">
            <h3><i class="fa-solid fa-triangle-exclamation"></i> Low Stock Alerts</h3>
            <ul>
                <?php while($item = $lowStock->fetch_assoc()): ?>
                    <?php $class = ($item['quantity'] <= 2) ? 'alert-danger' : 'alert-warning'; ?>
                    <li class="<?php echo $class; ?>">
                        <?php echo $item['item_name']." (".$item['quantity'].")"; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <div class="card">
            <h3><i class="fa-solid fa-notes-medical"></i> Recent Consultations</h3>
            <ul>
                <?php while($c = $recentConsultations->fetch_assoc()): ?>
                    <li>
                        <?php echo $c['pet_name']." with ".$c['vet_name']." on ".$c['appointment_date']; ?>
                        <br><small>Diagnosis: <?php echo $c['diagnosis']; ?> | Treatment: <?php echo $c['treatment']; ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <div class="charts">
        <canvas id="appointmentsChart" width="400" height="300"></canvas>
        <canvas id="paymentsChart" width="400" height="300"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const appointmentsData = <?php echo json_encode(array_values($appointmentsPerMonth)); ?>;
        const paymentsData = <?php echo json_encode(array_values($paymentsPerMonth)); ?>;
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        new Chart(document.getElementById('appointmentsChart'), {
          type: 'bar',
          data: {
            labels: months,
            datasets: [{
              label: 'Appointments',
              data: appointmentsData,
              backgroundColor: 'rgba(30,144,255,0.7)',
              borderColor: '#001f3f',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { labels: { color: '#333333' } } },
            scales: {
              x: { ticks: { color: '#333333' } },
              y: { ticks: { color: '#333333' } }
            }
          }
        });

        new Chart(document.getElementById('paymentsChart'), {
          type: 'line',
          data: {
            labels: months,
            datasets: [{
              label: 'Payments (FCFA)',
              data: paymentsData,
              borderColor: '#27ae60',
              backgroundColor: 'rgba(39,174,96,0.2)',
              fill: true,
              tension: 0.3
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { labels: { color: '#333333' } } },
            scales: {
              x: { ticks: { color: '#333333' } },
              y: { ticks: { color: '#333333' } }
            }
          }
        });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
