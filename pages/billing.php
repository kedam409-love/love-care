<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Handle invoice creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_invoice'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $amount = floatval($_POST['amount']);
    $payment_status = $_POST['payment_status'];
    $items = $_POST['items']; // format: "1:2,3:1" (item_id:qty)

    // Insert invoice
    $stmt = $conn->prepare("INSERT INTO invoices (appointment_id, amount, payment_status, payment_date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("ids", $appointment_id, $amount, $payment_status);
    if ($stmt->execute()) {
        $message = "Invoice generated successfully.";
    } else {
        $message = "Error generating invoice.";
    }

    // Deduct inventory items
    $item_pairs = explode(",", $items);
    foreach ($item_pairs as $pair) {
        $parts = explode(":", trim($pair));
        if (count($parts) == 2) {
            $item_id = intval($parts[0]);
            $qty = intval($parts[1]);

            $update = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE item_id=?");
            $update->bind_param("ii", $qty, $item_id);
            $update->execute();

            // Trigger notification if below threshold
            $check = $conn->query("SELECT * FROM inventory WHERE item_id=$item_id")->fetch_assoc();
            if ($check['quantity'] < $check['threshold']) {
                $msg = "Low stock alert: " . $check['item_name'] . " has only " . $check['quantity'] . " left.";
                $note = $conn->prepare("INSERT INTO notifications (message, status, notification_date) VALUES (?, 'Unread', NOW())");
                $note->bind_param("s", $msg);
                $note->execute();
            }
        }
    }
}

// Handle PDF export of all invoices
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

    // Watermark logo
    $mpdf->SetWatermarkImage(__DIR__ . '/../assets/logo.png', 0.15, 'F', [150,150]);
    $mpdf->showWatermarkImage = true;

    // Build PDF HTML
    $html = '
    <div style="text-align:center;">
        <img src="../assets/logo.png" width="80" style="float:left;">
        <h2>Love Care Veterinary Clinic</h2>
        <p>Phone: +237 6XX XXX XXX | Email: info@lovecarevms.com</p>
        <h3>Billing & Invoices Report</h3>
    </div>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>ID</th><th>Pet</th><th>Veterinarian</th><th>Amount</th><th>Status</th><th>Date</th>
            </tr>
        </thead>
        <tbody>';

    $invoices = $conn->query("SELECT i.*, p.pet_name, u.full_name AS vet_name 
                              FROM invoices i
                              JOIN appointments a ON i.appointment_id=a.appointment_id
                              JOIN pets p ON a.pet_id=p.pet_id
                              LEFT JOIN users u ON a.vet_id=u.user_id
                              ORDER BY i.payment_date DESC");
    while($row = $invoices->fetch_assoc()) {
        $html .= '<tr>
            <td>'.$row['invoice_id'].'</td>
            <td>'.$row['pet_name'].'</td>
            <td>'.$row['vet_name'].'</td>
            <td>'.$row['amount'].' FCFA</td>
            <td>'.$row['payment_status'].'</td>
            <td>'.$row['payment_date'].'</td>
        </tr>';
    }

    $html .= '</tbody></table>';

    $mpdf->SetFooter('Page {PAGENO} of {nb} | Generated on '.date('Y-m-d H:i'));
    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->Output('billing_report.pdf','I');
    exit;
}

// Handle single invoice PDF export
if (isset($_GET['invoice_pdf'])) {
    $invoice_id = intval($_GET['invoice_pdf']);
    require_once __DIR__ . '/../vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();

    $css_theme  = file_get_contents(__DIR__ . '/../assets/css/theme.css');
    $mpdf->WriteHTML($css_theme, \Mpdf\HTMLParserMode::HEADER_CSS);

    $mpdf->SetWatermarkImage(__DIR__ . '/../assets/logo.png', 0.15, 'F', [20,40]);
    $mpdf->showWatermarkImage = true;

    $stmt = $conn->prepare("SELECT i.*, p.pet_name, u.full_name AS vet_name, o.full_name AS owner_name
                            FROM invoices i
                            JOIN appointments a ON i.appointment_id=a.appointment_id
                            JOIN pets p ON a.pet_id=p.pet_id
                            JOIN users o ON p.owner_id=o.user_id
                            LEFT JOIN users u ON a.vet_id=u.user_id
                            WHERE i.invoice_id=?");
    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $invoice = $stmt->get_result()->fetch_assoc();

    $html = '
    <div style="text-align:center;">
        <img src="../assets/logo.png" width="80" style="float:left;">
        <h2>Veterinary Clinic Kumba</h2>
        <p>Phone:  +237 621726670 | Email: kedam409@gmail.com</p>
        <h3>Invoice #'.$invoice['invoice_id'].'</h3>
    </div>
    <hr>
    <p><strong>Pet:</strong> '.$invoice['pet_name'].'<br>
       <strong>Owner:</strong> '.$invoice['owner_name'].'<br>
       <strong>Veterinarian:</strong> '.$invoice['vet_name'].'<br>
       <strong>Amount:</strong> '.$invoice['amount'].' FCFA<br>
       <strong>Status:</strong> '.$invoice['payment_status'].'<br>
       <strong>Date:</strong> '.$invoice['payment_date'].'</p>';

    $mpdf->SetFooter('Page {PAGENO} of {nb} | Generated on '.date('Y-m-d H:i'));
    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->Output('invoice_'.$invoice_id.'.pdf','I');
    exit;
}

// Fetch invoices for display
$result = $conn->query("SELECT i.*, p.pet_name, u.full_name AS vet_name 
                        FROM invoices i
                        JOIN appointments a ON i.appointment_id=a.appointment_id
                        JOIN pets p ON a.pet_id=p.pet_id
                        LEFT JOIN users u ON a.vet_id=u.user_id
                        ORDER BY i.payment_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Billing & Invoices</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
    <link rel="stylesheet" href="../assets/css/badges.css">
    <link rel="stylesheet" href="../assets/css/res.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2><i class="fa-solid fa-file-invoice-dollar"></i> Billing & Invoices</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

        <!-- Add Invoice Form -->
    <form method="POST">
        <input type="hidden" name="add_invoice" value="1">
        <input type="number" name="appointment_id" placeholder="Appointment ID" required>
        <input type="number" name="amount" placeholder="Amount (FCFA)" required>
        <select name="payment_status" required>
            <option value="Paid">Paid</option>
            <option value="Pending">Pending</option>
        </select>
        <input type="text" name="items" placeholder="Inventory Items (e.g. 1:2,3:1)">
        <input type="submit" value="Generate Invoice" class="btn-green">
    </form>

    <h3>Invoice Records</h3>
    <table class="table">
        <tr>
            <th>ID</th><th>Pet</th><th>Veterinarian</th><th>Amount</th><th>Status</th><th>Date</th><th>Download</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['invoice_id'] ?></td>
            <td><?= $row['pet_name'] ?></td>
            <td><?= $row['vet_name'] ?></td>
            <td><?= $row['amount'] ?> FCFA</td>
            <td>
                <span class="badge badge-<?= strtolower($row['payment_status']); ?>">
                    <?= $row['payment_status'] ?>
                </span>
            </td>
            <td><?= $row['payment_date'] ?></td>
            <td>
                <a href="billing.php?invoice_pdf=<?= $row['invoice_id'] ?>" class="btn-blue">Download PDF</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Export all invoices to PDF -->
    <form method="POST" style="margin-top:15px;">
        <input type="submit" name="export_pdf" value="Export All Invoices (PDF)" class="btn-blue">
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
