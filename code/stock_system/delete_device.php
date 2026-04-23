<?php
include "db.php";
$conn->query("UPDATE appareils SET supprime=1 WHERE id=".$_POST['id']);
