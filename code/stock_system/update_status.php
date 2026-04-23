<?php
include "db.php";
$id=$_POST['id'];
$statut=$_POST['statut'];

if($statut=="Récupéré"){
$conn->query("UPDATE appareils SET statut='Récupéré',date_retour=NOW() WHERE id=$id");
}else{
$conn->query("UPDATE appareils SET statut='$statut' WHERE id=$id");
}
