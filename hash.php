<?php
echo "Admin: " . password_hash("admin123", PASSWORD_DEFAULT) . "<br>";
echo "Vet: " . password_hash("vet123", PASSWORD_DEFAULT) . "<br>";
echo "Receptionist: " . password_hash("recept123", PASSWORD_DEFAULT) . "<br>";
echo "Owner: " . password_hash("owner123", PASSWORD_DEFAULT) . "<br>";
?>
