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
}

// Handle CSV export
if (isset($_POST['export_csv'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=invoices.csv');
    $output = fopen('php://output', 'w');

    // Column headers
    fputcsv($output, ['Invoice ID','Owner','Pet','Amount','Status','Payment Date']);

    // Fetch data
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

// Handle PDF export
if (isset($_POST['export_pdf'])) {
    require('../fpdf/fpdf.php'); // Make sure FPDF is installed in your project

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'Veterinary Management System - Invoices',0,1,'C');
    $pdf->Ln(10);

    // Table header
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(20,10,'ID',1);
    $pdf->Cell(40,10,'Owner',1);
    $pdf->Cell(30,10,'Pet',1);
    $pdf->Cell(30,10,'Amount',1);
    $pdf->Cell(30,10,'Status',1);
    $pdf->Cell(40,10,'Date',1);
    $pdf->Ln();

    // Fetch data
    $sql = "SELECT invoices.*, pets.pet_name, owners.owner_name 
            FROM invoices
            JOIN appointments ON invoices.appointment_id = appointments.appointment_id
            JOIN pets ON appointments.pet_id = pets.pet_id
            JOIN owners ON pets.owner_id = owners.owner_id";
    $result = $conn->query($sql);

    $pdf->SetFont('Arial','B',16);
    while($row = $result->fetch_assoc()) {
        $pdf->Cell(20,10,$row['invoice_id'],1);
        $pdf->Cell(40,10,$row['owner_name'],1);
        $pdf->Cell(30,10,$row['pet_name'],1);
        $pdf->Cell(30,10,$row['amount'].' FCFA',1);
        $pdf->Cell(30,10,$row['payment_status'],1);
        $pdf->Cell(40,10,$row['payment_date'] ? $row['payment_date'] : '---',1);
        $pdf->Ln();
    }

    $pdf->Output();
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
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
        header { background: green; color: white; padding: 20px; text-align: center; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #eee; }
        .paid { color: green; font-weight: bold; }
        .pending { color: red; font-weight: bold; }
        form { display: inline; }
        input[type=submit] { padding: 5px 10px; background: green; color: white; border: none; border-radius: 4px; cursor: pointer; }
        input[type=submit]:hover { background: darkgreen; }
        .export-btn { margin: 20px auto; text-align: center; }
    </style>
</head>
<body>
    <header>
        <h1>Invoices</h1>
        <p>Manage billing and payments</p>
    </header>

    <div class="export-btn">
        <form method="POST" action="invoices.php" style="display:inline;">
            <input type="submit" name="export_csv" value="Export to CSV">
        </form>
        <form method="POST" action="invoices.php" style="display:inline;">
            <input type="submit" name="export_pdf" value="Export to PDF">
        </form>
    </div>

    <table>
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
            <td class="<?php echo strtolower($row['payment_status']); ?>">
                <?php echo $row['payment_status']; ?>
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
</body>
</html>
