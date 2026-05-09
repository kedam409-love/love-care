<?php
include('../config/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        echo "Please enter both username and password!";
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;

            // Role-based redirects
            switch ($user['role']) {
                case 'Administrator':
                    header("Location: admin_dashboard.php");
                    break;
                case 'Veterinarian':
                    header("Location: vet_dashboard.php");
                    break;
                case 'Receptionist':
                    header("Location: reception_dashboard.php");
                    break;
                case 'PetOwner':
                    header("Location: owner-dashboard.php");
                    break;
                default:
                    header("Location: dashboard.php");
            }
            exit;
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Veterinary Management System</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .login-box { width: 300px; margin: 100px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        .login-box h2 { text-align: center; margin-bottom: 20px; }
        .login-box input[type=text], .login-box input[type=password] {
            width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ccc; border-radius: 4px;
        }
        .login-box input[type=submit] {
            width: 100%; padding: 10px; background: green; color: #fff; border: none; border-radius: 4px; cursor: pointer;
        }
        .login-box input[type=submit]:hover { background: darkgreen; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>VMS Login</h2>
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
