<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_appointment'])) {
    $pet_id = $_POST['pet_id'];
    $vet_id = $_POST['vet_id'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];

    if (!empty($pet_id) && !empty($vet_id) && !empty($date) && !empty($time)) {
        $stmt = $conn->prepare("INSERT INTO appointments (pet_id, vet_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'Scheduled')");
        $stmt->bind_param("iiss", $pet_id, $vet_id, $date, $time);
        $stmt->execute();
    }
}

// Handle PDF export
if (isset($_POST['export_pdf'])) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();

    // Load CSS
    $css_theme  = file_get_contents(__DIR__ . '/../assets/css/theme.css');
    $css_alerts = file_get_contents(__DIR__ . '/../assets/css/alerts.css');
    $css_badges = file_get_contents(__DIR__ . '/../assets/css/badges.css');
    $mpdf->WriteHTML($css_theme, \Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML($css_alerts, \Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML($css_badges, \Mpdf\HTMLParserMode::HEADER_CSS);

    // Centered logo watermark
    $mpdf->SetWatermarkImage(__DIR__ . '/../assets/logo.png', 0.15, 'F', [20,40]);
    $mpdf->showWatermarkImage = true;

    // Build HTML
    $html = '
    <div style="text-align:center;">
        <img src="../assets/logo.png" width="80" style="float:left;">
        <h2>Veterinary Clinic Kumba</h2>
        <p>Phone: +237 6XX XXX XXX | Email: info@lovecarevms.com</p>
        <h3>Veterinary Management System - Appointments</h3>
    </div>
    <table class="table" border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr style="background:#eee;">
                <th>ID</th><th>Pet</th><th>Veterinarian</th><th>Date</th><th>Time</th><th>Status</th>
            </tr>
        </thead>
        <tbody>';

    $result = $conn->query("SELECT a.*, p.pet_name, u.full_name AS vet_name 
                            FROM appointments a 
                            JOIN pets p ON a.pet_id=p.pet_id 
                            LEFT JOIN users u ON a.vet_id=u.user_id
                            ORDER BY a.appointment_date, a.appointment_time");

    while($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>'.$row['appointment_id'].'</td>
            <td>'.$row['pet_name'].'</td>
            <td>'.$row['vet_name'].'</td>
            <td>'.$row['appointment_date'].'</td>
            <td>'.$row['appointment_time'].'</td>
            <td><span class="badge badge-info">'.$row['status'].'</span></td>
        </tr>';
    }

    $html .= '</tbody></table>';

    // Footer
    $mpdf->SetFooter('Page {PAGENO} of {nb} | Generated on '.date('Y-m-d H:i'));

    // Output PDF
    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->Output('appointments.pdf','I');
    exit;
}

// Fetch appointments for display
$result = $conn->query("SELECT a.*, p.pet_name, u.full_name AS vet_name 
                        FROM appointments a 
                        JOIN pets p ON a.pet_id=p.pet_id 
                        LEFT JOIN users u ON a.vet_id=u.user_id
                        ORDER BY a.appointment_date, a.appointment_time");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Appointments - VMS</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
    <link rel="stylesheet" href="../assets/css/badges.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2>Appointment Scheduling</h2>

    <form method="POST" class="form-inline">
        <input type="number" name="pet_id" placeholder="Pet ID" required>
        <input type="number" name="vet_id" placeholder="Vet ID" required>
        <input type="date" name="appointment_date" required>
        <input type="time" name="appointment_time" required>
        <input type="submit" name="book_appointment" value="Book Appointment">
    </form>

    <div class="export-btn" style="margin:15px 0;">
        <form method="POST" action="appointments.php" style="display:inline;">
            <input type="submit" name="export_pdf" value="Export to PDF">
        </form>
    </div>

    <h3>Upcoming Appointments</h3>
    <table class="table">
        <tr>
            <th>ID</th><th>Pet</th><th>Veterinarian</th><th>Date</th><th>Time</th><th>Status</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['appointment_id'] ?></td>
            <td><?= $row['pet_name'] ?></td>
            <td><?= $row['vet_name'] ?></td>
            <td><?= $row['appointment_date'] ?></td>
            <td><?= $row['appointment_time'] ?></td>
            <td><span class="badge badge-info"><?= $row['status'] ?></span></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
