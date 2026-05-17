<?php
session_start();
include('../config/db.php'); // your database connection file

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
       $sql = "SELECT user_id, username, full_name, role, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $_SESSION['user'] = [
            'id' => $row['user_id'],
            'username' => $row['username'],   // login credential
            'full_name' => $row['full_name'], // pulled from DB
            'role' => $row['role']
        ];

                // Redirect based on role
                switch ($row['role']) {
                    case 'Administrator':
                        header("Location: admin_dashboard.php");
                        break;
                    case 'Receptionist':
                        header("Location: reception_dashboard.php");
                        break;
                    case 'Veterinarian':
                        header("Location: vet_dashboard.php");
                        break;
                    case 'PetOwner':
                        header("Location: owner_dashboard.php");
                        break;
                    default:
                        header("Location: index.php");
                }
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "User not found!";
        }
    } else {
        $error = "Please enter both username and password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Love Care Veterinary Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/res.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: url('../assets/CD.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(6px);
            padding: 30px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .login-box h2 {
            text-align: center;
            color: #024363;
            margin-bottom: 20px;
        }
        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }
        .login-box button {
            width: 100%;
            padding: 12px;
            background: #00aaff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        .login-box button:hover {
            background: #0088cc;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2><i class="fa-solid fa-paw"></i> Login</h2>
        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit"><i class="fa-solid fa-right-to-bracket"></i> Login</button>
        </form>
    </div>
</body>
</html>
