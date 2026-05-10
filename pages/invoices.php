<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include('../config/db.php');

// Handle payment update
if (isset($_POST['update_payment'])) {
    $invoice_id = $_POST['invoice_id'];
    $status = $_POST['payment_status'];
    $date = ($status == 'Paid') ? date('Y-m-d') : NULL;

    $stmt = $conn->prepare("UPDATE invoices SET payment_status=?, payment_date=? WHERE invoice_id=?");
    $stmt->bind_param("ssi", $status, $date, $invoice_id);
    $stmt->execute();

    $message = "Invoice #$invoice_id updated successfully!";
    $msg_type = "success";
}

// Handle CSV export
if (isset($_POST['export_csv'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=invoices.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Invoice ID','Owner','Pet','Amount','Status','Payment Date']);
    $sql = "SELECT invoices.*, pets.pet_name, owners.owner_name 
            FROM invoices
            JOIN appointments ON invoices.appointment_id = appointments.appointment_id
            JOIN pets ON appointments.pet_id = pets.pet_id
            JOIN owners ON pets.owner_id = owners.owner_id";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['invoice_id'],
            $row['owner_name'],
            $row['pet_name'],
            $row['amount'],
            $row['payment_status'],
            $row['payment_date'] ? $row['payment_date'] : '---'
        ]);
    }
    fclose($output);
    exit;
}

// Handle PDF export with mPDF + CSS + Centered Watermark
if (isset($_POST['export_pdf'])) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();

    // Load CSS files
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
        <h2> Veterinary Clinic Kumba</h2>
        <p>Phone: +237 6XX XXX XXX | Email: info@lovecarevms.com</p>
        <h3>Veterinary Management System - Invoices</h3>
    </div>
    <table class="invoice-table" border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr style="background:#eee;">
                <th>ID</th>
                <th>Owner</th>
                <th>Pet</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>';

    $sql = "SELECT invoices.*, pets.pet_name, owners.owner_name 
            FROM invoices
            JOIN appointments ON invoices.appointment_id = appointments.appointment_id
            JOIN pets ON appointments.pet_id = pets.pet_id
            JOIN owners ON pets.owner_id = owners.owner_id";
    $result = $conn->query($sql);

    $totalAmount = 0;
    while($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>'.$row['invoice_id'].'</td>
            <td>'.$row['owner_name'].'</td>
            <td>'.$row['pet_name'].'</td>
            <td>'.$row['amount'].' FCFA</td>
            <td><span class="badge badge-'.strtolower($row['payment_status']).'">'.$row['payment_status'].'</span></td>
            <td>'.($row['payment_date'] ? $row['payment_date'] : '---').'</td>
        </tr>';
        $totalAmount += $row['amount'];
    }

    $html .= '<tr class="total-row">
        <td colspan="3">Total Amount</td>
        <td colspan="3">'.$totalAmount.' FCFA</td>
    </tr>';

    $html .= '</tbody></table>';

    // Footer
    $mpdf->SetFooter('Page {PAGENO} of {nb} | Generated on '.date('Y-m-d H:i'));

    // Output PDF
    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->Output('invoices.pdf','I');
    exit;
}

// Fetch invoices for display
$sql = "SELECT invoices.*, pets.pet_name, owners.owner_name 
        FROM invoices
        JOIN appointments ON invoices.appointment_id = appointments.appointment_id
        JOIN pets ON appointments.pet_id = pets.pet_id
        JOIN owners ON pets.owner_id = owners.owner_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Invoices - VMS</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
    <link rel="stylesheet" href="../assets/css/badges.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/navbar.php'); ?>

    <?php if(isset($message)): ?>
        <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="export-btn" style="text-align:center; margin:20px;">
        <form method="POST" action="invoices.php" style="display:inline;">
            <input type="submit" name="export_csv" value="Export to CSV">
        </form>
        <form method="POST" action="invoices.php" style="display:inline;">
            <input type="submit" name="export_pdf" value="Export to PDF">
        </form>
    </div>

    <table class="invoice-table">
        <tr>
            <th>Invoice ID</th>
            <th>Owner</th>
            <th>Pet</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Payment Date</th>
            <th>Action</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['invoice_id']; ?></td>
            <td><?php echo $row['owner_name']; ?></td>
            <td><?php echo $row['pet_name']; ?></td>
            <td><?php echo $row['amount']; ?> FCFA</td>
            <td>
                <span class="badge badge-<?php echo strtolower($row['payment_status']); ?>">
                    <?php echo $row['payment_status']; ?>
                </span>
            </td>
            <td><?php echo $row['payment_date'] ? $row['payment_date'] : '---'; ?></td>
            <td>
                <form method="POST" action="invoices.php">
                    <input type="hidden" name="invoice_id" value="<?php echo $row['invoice_id']; ?>">
                    <select name="payment_status">
                        <option value="Paid" <?php if($row['payment_status']=='Paid') echo 'selected'; ?>>Paid</option>
                        <option value="Pending" <?php if($row['payment_status']=='Pending') echo 'selected'; ?>>Pending</option>
                    </select>
                    <input type="submit" name="update_payment" value="Update">
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
