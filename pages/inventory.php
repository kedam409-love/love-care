<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Handle new inventory item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $threshold = $_POST['threshold'];
    $unit_price = $_POST['unit_price'];

    $stmt = $conn->prepare("INSERT INTO inventory (item_name, category, quantity, threshold, unit_price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $item_name, $category, $quantity, $threshold, $unit_price);
    $stmt->execute();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_item'])) {
    $id = $_POST['item_id'];

    // For now, just a placeholder update (you can redirect to edit_item.php later)
    $stmt = $conn->prepare("UPDATE inventory SET item_name=item_name WHERE item_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_item'])) {
    $id = $_POST['item_id'];
    $stmt = $conn->prepare("DELETE FROM inventory WHERE item_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Handle PDF export
if (isset($_POST['export_pdf'])) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();

    $css_theme = file_get_contents(__DIR__ . '/../assets/css/theme.css');
    $mpdf->WriteHTML($css_theme, \Mpdf\HTMLParserMode::HEADER_CSS);

    // Centered logo watermark
    $mpdf->SetWatermarkImage(__DIR__ . '/../assets/logo.png', 0.15, 'F', [150,150]);
    $mpdf->showWatermarkImage = true;

    $html = '
    <div style="text-align:center;">
        <img src="../assets/logo.png" width="80" style="float:left;">
        <h2>Love Care Veterinary Clinic</h2>
        <p>Phone: +237 621726670 | Email: kedam409@gmail.com</p>
        <h3>Veterinary Management System - Inventory</h3>
    </div>
    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr style="background:#eee;">
                <th>ID</th><th>Item</th><th>Category</th><th>Quantity</th><th>Threshold</th><th>Unit Price</th>
            </tr>
        </thead>
        <tbody>';

    $result = $conn->query("SELECT * FROM inventory ORDER BY item_name ASC");
    while($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>'.$row['item_id'].'</td>
            <td>'.$row['item_name'].'</td>
            <td>'.$row['category'].'</td>
            <td>'.$row['quantity'].'</td>
            <td>'.$row['threshold'].'</td>
            <td>'.$row['unit_price'].' FCFA</td>
        </tr>';
    }

    $html .= '</tbody></table>';
    $mpdf->SetFooter('Page {PAGENO} of {nb} | Generated on '.date('Y-m-d H:i'));
    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->Output('inventory.pdf','I');
    exit;
}

// Fetch inventory for display
$result = $conn->query("SELECT * FROM inventory ORDER BY item_name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/res.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2>Inventory Management</h2>
    <form method="POST">
        <input type="text" name="item_name" placeholder="Item Name" required>
        <input type="text" name="category" placeholder="Category" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <input type="number" name="threshold" placeholder="Threshold" required>
        <input type="number" name="unit_price" placeholder="Unit Price (FCFA)" required>
        <input type="submit" name="add_item" value="Add Item" class="btn-green btn-small">
    </form>

    <div class="export-btn" style="margin:15px 0;">
        <form method="POST" action="inventory.php" style="display:inline;">
            <input type="submit" name="export_pdf" value="Export to PDF" class="btn-blue btn-small">
        </form>
    </div>
    
    <h2>Stock List</h2>
    <table class="table">
        <tr>
            <th>ID</th><th>Item</th><th>Category</th><th>Quantity</th><th>Threshold</th><th>Unit Price</th><th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr class="<?= ($row['quantity'] < $row['threshold']) ? 'low-stock' : '' ?>">
            <td><?= $row['item_id'] ?></td>
            <td><?= $row['item_name'] ?></td>
            <td><?= $row['category'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['threshold'] ?></td>
            <td><?= $row['unit_price'] ?> FCFA</td>
            <td>
                <!-- Update form -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="item_id" value="<?= $row['item_id'] ?>">
                    <input type="submit" name="update_item" value="Update" class="btn-blue btn-small">
                </form>

                <!-- Delete form -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="item_id" value="<?= $row['item_id'] ?>">
                    <input type="submit" name="delete_item" value="Delete" class="btn-green btn-small" onclick="return confirm('Delete this item?');">
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
