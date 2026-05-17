<?php
include('../config/db.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name    = $conn->real_escape_string($_POST['name']);
    $email   = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);

    // Insert into messages table
    $sql = "INSERT INTO messages (name, email, message, created_at) 
            VALUES ('$name', '$email', '$message', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green; font-family:Segoe UI;'>Thank you, $name! Your message has been saved. We’ll get back to you soon.</p>";
        echo "<a href='contact.php'>Go back</a>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}

$conn->close();
?>
