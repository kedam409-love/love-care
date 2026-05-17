<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];
$message = "";

// Handle Add Pet
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_pet'])) {
    // PetOwner → force owner_id from session
    if ($role === 'PetOwner') {
        $owner_id = $user_id;
    } else {
        $owner_id = intval($_POST['owner_id']);
    }

    $pet_name = $_POST['pet_name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];

    $stmt = $conn->prepare("INSERT INTO pets (owner_id, pet_name, species, breed, age, gender) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssis", $owner_id, $pet_name, $species, $breed, $age, $gender);
    if ($stmt->execute()) {
        $message = "Pet registered successfully.";
    } else {
        $message = "Error registering pet.";
    }
}

// Handle Delete Pet
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM pets WHERE pet_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = "Pet deleted successfully.";
}

// Handle Edit Pet
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_pet'])) {
    $id = intval($_POST['pet_id']);
    $owner_id = intval($_POST['owner_id']);
    $pet_name = $_POST['pet_name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];

    $stmt = $conn->prepare("UPDATE pets SET owner_id=?, pet_name=?, species=?, breed=?, age=?, gender=? WHERE pet_id=?");
    $stmt->bind_param("isssisi", $owner_id, $pet_name, $species, $breed, $age, $gender, $id);
    $stmt->execute();
    $message = "Pet updated successfully.";
}

// Fetch pets based on role
if ($role === 'PetOwner') {
    $stmt = $conn->prepare("SELECT p.*, o.owner_name 
                            FROM pets p 
                            LEFT JOIN owners o ON p.owner_id=o.owner_id
                            WHERE p.owner_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

} elseif ($role === 'Veterinarian') {
    $stmt = $conn->prepare("SELECT DISTINCT p.*, o.owner_name 
                            FROM pets p 
                            LEFT JOIN owners o ON p.owner_id=o.owner_id
                            JOIN consultations c ON p.pet_id=c.pet_id
                            WHERE c.vet_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

} elseif ($role === 'Receptionist' || $role === 'Administrator') {
    $result = $conn->query("SELECT p.*, o.owner_name 
                            FROM pets p 
                            LEFT JOIN owners o ON p.owner_id=o.owner_id");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pet Management</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
    <link rel="stylesheet" href="../assets/css/badges.css">
    <link rel="stylesheet" href="../assets/css/res.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2><i class="fa-solid fa-dog"></i> Pet Management</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Add Pet Form -->
    <?php if ($role === 'Receptionist' || $role === 'Administrator' || $role === 'PetOwner'): ?>
    <form method="POST">
        <input type="hidden" name="add_pet" value="1">
        <?php if ($role === 'PetOwner'): ?>
            <!-- Owner ID auto-filled for PetOwner -->
            <input type="hidden" name="owner_id" value="<?= $user_id ?>">
        <?php else: ?>
            <!-- Receptionist/Admin can assign owner -->
            <input type="number" name="owner_id" placeholder="Owner ID" required>
        <?php endif; ?>
        <input type="text" name="pet_name" placeholder="Pet Name" required>
        <input type="text" name="species" placeholder="Species">
        <input type="text" name="breed" placeholder="Breed">
        <input type="number" name="age" placeholder="Age">
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
        <input type="submit" value="Add Pet" class="btn-green">
    </form>
    <?php endif; ?>

    <h3>Registered Pets</h3>
    <table class="table">
        <tr>
            <th>ID</th><th>Name</th><th>Species</th><th>Breed</th><th>Age</th><th>Gender</th><th>Owner</th><th>Action</th>
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
            <td>
                <?php if ($role === 'Receptionist' || $role === 'Administrator'): ?>
                    <a href="pet.php?edit=<?= $row['pet_id'] ?>" class="btn-blue">Edit</a> |
                    <a href="pet.php?delete=<?= $row['pet_id'] ?>" class="btn-red" onclick="return confirm('Delete this pet?');">Delete</a>
                <?php endif; ?>
                <a href="consultations.php?pet_id=<?= $row['pet_id'] ?>" class="btn-orange">Medical History</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- Edit Pet Form -->
<?php if (isset($_GET['edit']) && ($role === 'Receptionist' || $role === 'Administrator')): 
    $id = intval($_GET['edit']);
    $editResult = $conn->query("SELECT * FROM pets WHERE pet_id=$id");
    $pet = $editResult->fetch_assoc();
?>
<div class="container">
    <h3>Edit Pet</h3>
    <form method="POST">
        <input type="hidden" name="edit_pet" value="1">
        <input type="hidden" name="pet_id" value="<?= $pet['pet_id'] ?>">
        <input type="number" name="owner_id" value="<?= $pet['owner_id'] ?>" required>
        <input type="text" name="pet_name" value="<?= $pet['pet_name'] ?>" required>
        <input type="text" name="species" value="<?= $pet['species'] ?>">
        <input type="text" name="breed" value="<?= $pet['breed'] ?>">
        <input type="number" name="age" value="<?= $pet['age'] ?>">
        <select name="gender" required>
            <option value="Male" <?= ($pet['gender']=='Male')?'selected':''; ?>>Male</option>
            <option value="Female" <?= ($pet['gender']=='Female')?'selected':''; ?>>Female</option>
        </select>
        <input type="submit" value="Update Pet" class="btn-green">
    </form>
</div>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>
</body>
</html>
