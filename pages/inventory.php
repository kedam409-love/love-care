<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Handle new inventory item
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $threshold = $_POST['threshold'];
    $unit_price = $_POST['unit_price'];

    $stmt = $conn->prepare("INSERT INTO inventory (item_name, category, quantity, threshold, unit_price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $item_name, $category, $quantity, $threshold, $unit_price);
    $stmt->execute();
}

// Fetch inventory
$result = $conn->query("SELECT * FROM inventory ORDER BY item_name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
        <input type="submit" value="Add Item">
    </form>

    <h3>Stock List</h3>
    <table class="table">
        <tr>
            <th>ID</th><th>Item</th><th>Category</th><th>Quantity</th><th>Threshold</th><th>Unit Price</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr class="<?= ($row['quantity'] < $row['threshold']) ? 'low-stock' : '' ?>">
            <td><?= $row['item_id'] ?></td>
            <td><?= $row['item_name'] ?></td>
            <td><?= $row['category'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['threshold'] ?></td>
            <td><?= $row['unit_price'] ?> FCFA</td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
