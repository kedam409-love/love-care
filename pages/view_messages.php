<?php
session_start();
include('../config/db.php');

// Handle actions (delete / mark read/unread)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'delete') {
        $conn->query("DELETE FROM messages WHERE id=$id");
    } elseif ($_GET['action'] === 'read') {
        $conn->query("UPDATE messages SET status='Read' WHERE id=$id");
    } elseif ($_GET['action'] === 'unread') {
        $conn->query("UPDATE messages SET status='Unread' WHERE id=$id");
    }
    header("Location: view_messages.php"); // refresh page
    exit();
}

// Fetch messages
$sql = "SELECT id, name, email, message, created_at, status 
        FROM messages ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Messages - Love Care Veterinary Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/res.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: url('../assets/cats.jpg') no-repeat center center/cover;
            color: #333;
        }
        .overlay {
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(6px);
            min-height: 100vh;
            padding: 20px;
        }
        header {
            background: #024363;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header img.logo {
            height: 50px;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }
        nav a:hover {
            color: #ffcc00;
        }
        h1 {
            text-align: center;
            color: #0483c3;
            margin: 30px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255,255,255,0.9);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        th {
            background: #00aaff;
            color: white;
        }
        tr:hover {
            background: rgba(0,170,255,0.1);
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            font-size: 14px;
        }
        .actions a.delete { color: red; }
        .actions a.read { color: green; }
        .actions a.unread { color: orange; }
        footer {
            background: rgba(0,0,0,0.8);
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="overlay">
        <header>
            <img src="../assets/logo.png" alt="Clinic Logo" class="logo">
            <nav>
                <a href="../index.php">Home</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="login.php">Login</a>
            </nav>
        </header>

        <h1>Contact Messages</h1>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo $row['status'] ?? 'Unread'; ?></td>
                        <td class="actions">
                            <a href="view_messages.php?action=read&id=<?php echo $row['id']; ?>" class="read"><i class="fa-solid fa-envelope-open"></i> Read</a>
                            <a href="view_messages.php?action=unread&id=<?php echo $row['id']; ?>" class="unread"><i class="fa-solid fa-envelope"></i> Unread</a>
                            <a href="view_messages.php?action=delete&id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Delete this message?');"><i class="fa-solid fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p style="text-align:center;">No messages found.</p>
        <?php endif; ?>

        <footer>
            &copy; <?php echo date("Y"); ?> Love Care Veterinary Clinic. 
            Contact us: kedam409@gmail.com | +237 621726670
        </footer>
    </div>
</body>
</html>
<?php
$conn->close();
?>
