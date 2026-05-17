<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Fetch totals
$totalPets = $conn->query("SELECT COUNT(*) AS c FROM pets")->fetch_assoc()['c'];
$totalOwners = $conn->query("SELECT COUNT(*) AS c FROM owners")->fetch_assoc()['c'];
$totalAppointments = $conn->query("SELECT COUNT(*) AS c FROM appointments")->fetch_assoc()['c'];
$totalInvoices = $conn->query("SELECT COUNT(*) AS c FROM invoices")->fetch_assoc()['c'];
$totalRevenue = $conn->query("SELECT SUM(amount) AS s FROM invoices WHERE payment_status='Paid'")->fetch_assoc()['s'];

// Handle PDF export
if (isset($_POST['export_pdf'])) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();

    // Load CSS
    $css_theme = file_get_contents(__DIR__ . '/../assets/css/theme.css');
    $mpdf->WriteHTML($css_theme, \Mpdf\HTMLParserMode::HEADER_CSS);

    // Centered logo watermark
    $mpdf->SetWatermarkImage(__DIR__ . '/../assets/logo.png', 0.15, 'F', [20,40]);
    $mpdf->showWatermarkImage = true;

    // Build HTML
    $html = '
    <div style="text-align:center;">
        <img src="../assets/logo.png" width="80" style="float:left;">
        <h2>Veterinary Clinic Kumba</h2>
        <p>Phone: +237 6XX XXX XXX | Email: info@lovecarevms.com</p>
        <h3>Veterinary Management System - Reports</h3>
    </div>
    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <tr><th>Total Pets</th><td>'.$totalPets.'</td></tr>
        <tr><th>Total Owners</th><td>'.$totalOwners.'</td></tr>
        <tr><th>Total Appointments</th><td>'.$totalAppointments.'</td></tr>
        <tr><th>Total Invoices</th><td>'.$totalInvoices.'</td></tr>
        <tr><th>Total Revenue</th><td>'.$totalRevenue.' FCFA</td></tr>
    </table>';

    $mpdf->SetFooter('Page {PAGENO} of {nb} | Generated on '.date('Y-m-d H:i'));
    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->Output('reports.pdf','I');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Clinic Reports</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/res.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2>Clinic Reports</h2>
    <p><strong>Total Pets Registered:</strong> <?= $totalPets ?></p>
    <p><strong>Total Owners:</strong> <?= $totalOwners ?></p>
    <p><strong>Total Appointments:</strong> <?= $totalAppointments ?></p>
    <p><strong>Total Invoices:</strong> <?= $totalInvoices ?></p>
    <p><strong>Total Revenue:</strong> <?= $totalRevenue ?> FCFA</p>

    <h3>Export Data</h3>
    <form method="POST" action="reports.php" style="display:inline;">
        <input type="submit" name="export_pdf" value="Export to PDF">
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
