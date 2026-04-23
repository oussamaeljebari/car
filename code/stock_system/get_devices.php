<?php
include "db.php";
$r=$conn->query("
SELECT a.*, t.nom technicien
FROM appareils a
LEFT JOIN techniciens t ON t.id=a.technicien_id
WHERE a.supprime=0
ORDER BY date_ajout DESC
");
$data=[];
while($row=$r->fetch_assoc()) $data[]=$row;
echo json_encode($data);
