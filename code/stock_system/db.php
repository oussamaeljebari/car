<?php
$conn = new mysqli("localhost", "root", "", "stock_system");
if ($conn->connect_error) {
    die("Erreur connexion base de données");
}
?>
