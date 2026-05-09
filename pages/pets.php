<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $owner_id = $_POST['owner_id'];
    $pet_name = $_POST['pet_name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];

    $stmt = $conn->prepare("INSERT INTO pets (owner_id, pet_name, species, breed, age, gender) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssis", $owner_id, $pet_name, $species, $breed, $age, $gender);
    $stmt->execute();
}

// Fetch pets
$result = $conn->query("SELECT p.*, o.owner_name 
                        FROM pets p 
                        LEFT JOIN owners o ON p.owner_id=o.owner_id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pet Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2>Pet Management</h2>
    <form method="POST">
        <input type="number" name="owner_id" placeholder="Owner ID" required>
        <input type="text" name="pet_name" placeholder="Pet Name" required>
        <input type="text" name="species" placeholder="Species">
        <input type="text" name="breed" placeholder="Breed">
        <input type="number" name="age" placeholder="Age">
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
        <input type="submit" value="Add Pet">
    </form>

    <h3>Registered Pets</h3>
    <table class="table">
        <tr>
            <th>ID</th><th>Name</th><th>Species</th><th>Breed</th><th>Age</th><th>Gender</th><th>Owner</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['pet_id'] ?></td>
            <td><?= $row['pet_name'] ?></td>
            <td><?= $row['species'] ?></td>
            <td><?= $row['breed'] ?></td>
            <td><?= $row['age'] ?></td>
            <td><?= $row['gender'] ?></td>
            <td><?= $row['owner_name'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
