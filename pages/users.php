<?php
session_start();
if (!isset($_SESSION['user']) || 
   ($_SESSION['user']['role'] != 'Administrator' && $_SESSION['user']['role'] != 'Receptionist')) {
    header("Location: login.php");
    exit;
}

include('../config/db.php');

$message = "";

// Handle Add User
if (isset($_POST['add_user'])) {
    $name = $_POST['full_name'];
    $username = $_POST['username'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $role = ($_SESSION['user']['role'] == 'Receptionist') ? 'PetOwner' : $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT * FROM users WHERE username=?");
    $check->bind_param("s", $username);
    $check->execute();
    $resultCheck = $check->get_result();

    if ($resultCheck->num_rows > 0) {
        $message = "Error: Username already exists. Please choose another.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, role, password, contact, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $username, $role, $password, $contact, $email);
        if ($stmt->execute()) {
            $message = "User added successfully.";
        } else {
            $message = "Error adding user.";
        }
    }
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = "User deleted successfully.";
}

// Handle Edit User
if (isset($_POST['edit_user'])) {
    $id = $_POST['user_id'];
    $name = $_POST['full_name'];
    $username = $_POST['username'];
    $role = ($_SESSION['user']['role'] == 'Receptionist') ? 'PetOwner' : $_POST['role'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    $check = $conn->prepare("SELECT * FROM users WHERE username=? AND user_id!=?");
    $check->bind_param("si", $username, $id);
    $check->execute();
    $resultCheck = $check->get_result();

    if ($resultCheck->num_rows > 0) {
        $message = "Error: Username already exists. Please choose another.";
    } else {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET full_name=?, username=?, role=?, contact=?, email=?, password=? WHERE user_id=?");
            $stmt->bind_param("ssssssi", $name, $username, $role, $contact, $email, $password, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name=?, username=?, role=?, contact=?, email=? WHERE user_id=?");
            $stmt->bind_param("sssssi", $name, $username, $role, $contact, $email, $id);
        }
        $stmt->execute();
        $message = "User updated successfully.";
    }
}

// Fetch Users with role-based filtering
if ($_SESSION['user']['role'] == 'Receptionist') {
    $result = $conn->query("SELECT * FROM users WHERE role='PetOwner'");
} else {
    $result = $conn->query("SELECT * FROM users");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - VMS</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h1>Manage Users</h1>
            <p><?php echo $_SESSION['user']['role']; ?> Panel</p>
        </div>
    </div>

    <?php if (!empty($message)) { ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

    <!-- Add User Form -->
    <div class="container">
        <div class="card">
            <form method="POST" action="users.php">
                <h2>Add New User</h2>
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="text" name="contact" placeholder="Contact" required>
                <input type="email" name="email" placeholder="Email" required>
                <?php if ($_SESSION['user']['role'] == 'Administrator') { ?>
                    <select name="role" required>
                        <option value="">Select Role</option>
                        <option value="Administrator">Administrator</option>
                        <option value="Veterinarian">Veterinarian</option>
                        <option value="Receptionist">Receptionist</option>
                        <option value="PetOwner">Pet Owner</option>
                    </select>
                <?php } else { ?>
                    <input type="hidden" name="role" value="PetOwner">
                <?php } ?>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" name="add_user" value="Add User">
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="container">
        <div class="card">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td><?php echo $row['contact']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td>
                        <?php if ($_SESSION['user']['role'] == 'Administrator') { ?>
                            <a class="edit" href="users.php?edit=<?php echo $row['user_id']; ?>">Edit</a> |
                            <a class="delete" href="users.php?delete=<?php echo $row['user_id']; ?>" onclick="return confirm('Delete this user?');">Delete</a>
                        <?php } elseif ($_SESSION['user']['role'] == 'Receptionist' && $row['role'] == 'PetOwner') { ?>
                            <a class="edit" href="users.php?edit=<?php echo $row['user_id']; ?>">Edit</a> |
                            <a class="delete" href="users.php?delete=<?php echo $row['user_id']; ?>" onclick="return confirm('Delete this user?');">Delete</a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <!-- Edit User Form -->
    <?php if (isset($_GET['edit'])) {
        $id = $_GET['edit'];
        $editResult = $conn->query("SELECT * FROM users WHERE user_id=$id");
        $user = $editResult->fetch_assoc();
    ?>
    <div class="container">
        <div class="card">
            <form method="POST" action="users.php">
                <h2>Edit User</h2>
                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
                <input type="text" name="contact" value="<?php echo $user['contact']; ?>" required>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

                <?php if ($_SESSION['user']['role'] == 'Administrator') { ?>
                    <select name="role" required>
                        <option value="Administrator" <?php if($user['role']=='Administrator') echo 'selected'; ?>>Administrator</option>
                        <option value="Veterinarian" <?php if($user['role']=='Veterinarian') echo 'selected'; ?>>Veterinarian</option>
                        <option value="Receptionist" <?php if($user['role']=='Receptionist') echo 'selected'; ?>>Receptionist</option>
                        <option value="PetOwner" <?php if($user['role']=='PetOwner') echo 'selected'; ?>>Pet Owner</option>
                    </select>
                <?php } else { ?>
                    <!-- Receptionists can only edit Pet Owners -->
                    <input type="hidden" name="role" value="PetOwner">
                <?php } ?>

                <input type="password" name="password" placeholder="Leave blank to keep current password">
                <input type="submit" name="edit_user" value="Update User">
            </form>
        </div>
    </div>
    <?php } // end if edit ?>

<?php include '../includes/footer.php'; ?>
</body>
</html>
