<?php
include "db.php";
$stmt=$conn->prepare(
"INSERT INTO appareils (imei,nom_client,type_appareil,technicien_id)
VALUES (?,?,?,?)");
$stmt->bind_param("sssi",
$_POST['imei'],$_POST['nom_client'],
$_POST['type_appareil'],$_POST['technicien_id']);
$stmt->execute();
