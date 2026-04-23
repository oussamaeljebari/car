<?php
$conn = new mysqli("localhost","root","","stock_system");

if ($conn->connect_error) {
    die("Erreur connexion DB");
}

/* AJOUT */
if(isset($_POST['add'])){
    $imei = $_POST['imei'];
    $client_name = $_POST['client_name'];
    $technicien = $_POST['technicien'];
    $matricule = $_POST['matricule'];
    $accessoires = $_POST['accessoires'];
    $flash_status = $_POST['flash_status'];
    
    // تسجيل الوقت والتاريخ الحالي عند الضغط على Ajouter
    $current_date = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO clients 
        (imei, client_name, technicien, matricule, accessoires, flash_status, statut, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 'Stock', ?)");

    $stmt->bind_param("sssssss",
        $imei,
        $client_name,
        $technicien,
        $matricule,
        $accessoires,
        $flash_status,
        $current_date
    );

    $stmt->execute();
    header("Location: stock_clients.php");
    exit();
}

/* SUPPRESSION */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM clients WHERE id=$id");
    header("Location: stock_clients.php");
    exit();
}

/* CHANGEMENT STATUT */
if(isset($_GET['set_statut']) && isset($_GET['id'])){
    $id = intval($_GET['id']);
    $statut = $_GET['set_statut'];

    $stmt = $conn->prepare("UPDATE clients SET statut=? WHERE id=?");
    $stmt->bind_param("si",$statut,$id);
    $stmt->execute();

    header("Location: stock_clients.php");
    exit();
}

/* RECHERCHE */
$search = "";
if(isset($_GET['search']) && $_GET['search'] != ""){
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM clients 
        WHERE imei LIKE ? OR client_name LIKE ? OR technicien LIKE ? OR statut LIKE ?
        ORDER BY id DESC");
    $like = "%".$search."%";
    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM clients ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stock Clients</title>
    <style>
         .navbar {
    background: linear-gradient(90deg, #1e4fd8, #2563eb);
    padding: 12px 30px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    color: white;
    font-size: 20px;
    font-weight: bold;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 25px;
}

.nav-links a {
    text-decoration: none;
    color: white;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: 6px;
    transition: 0.3s;
}

.nav-links a:hover {
    background: rgba(255,255,255,0.2);
}

.nav-user {
    display: flex;
    align-items: center;
    gap: 15px;
    color: white;
}

        
        body { font-family: Arial, sans-serif; background: #f4f6f8; padding: 20px; margin: 0; }
        form { background: #fff; padding: 15px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select { padding: 8px; margin: 5px; width: 170px; border: 1px solid #ccc; border-radius: 4px; }
        
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; font-size: 13px; }
        th { background: #2563eb; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }

        .btn { padding: 6px 10px; border: none; cursor: pointer; border-radius: 4px; font-weight: bold; font-size: 11px; margin: 2px; }
        .btn-add { background: #16a34a; color: white; }
        .btn-search { background: #2563eb; color: white; }
        .install { background: #16a34a; color: white; }
        .stock { background: #2563eb; color: white; }
        .casa-flash { background: #9333ea; color: white; }
        .tech { background: #facc15; color: black; }
        .btn-delete { background: #dc2626; color: white; }

        /* Style dyal l-baht */
        .search-container {
            margin-bottom: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .search-container input { width: 300px; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <div class="logo">
             <img src="assets/sendatrack.png" alt="Sendatrack Logo" height="45">
             📦 Système professionnel de stock
        </div>

        <ul class="nav-links">
            <li><a href="devices.html">Type GPS</a></li>
            <li><a href="index.html">Stock</a></li>
            <li><a href="stock_clients.php">Stock Clients</a></li>
            <li><a href="#">Rapports</a></li>
        </ul>

        <div class="nav-user">
            <span>👤 Admin</span>
            <button class="logout-btn">Déconnexion</button>
        </div>

        <div class="nav-toggle" onclick="toggleMenu()">☰</div>
    </div>
</nav>

<h2 style="color:#2563eb;">Gestion des Clients</h2>

<form method="POST">
    <input type="text" name="imei" placeholder="IMEI" required>
    <input type="text" name="client_name" placeholder="Client" required>
    <input type="text" name="technicien" placeholder="Technicien">
    <input type="text" name="matricule" placeholder="Matricule">
    <input type="text" name="accessoires" placeholder="Accessoires">
    <select name="flash_status">
        <option value="Non">Flashé: Non</option>
        <option value="Oui">Flashé: Oui</option>
    </select>
    <button type="submit" name="add" class="btn btn-add">Ajouter Client</button>
</form>

<hr>

<div class="search-container">
    <form method="GET" style="box-shadow: none; padding: 0; margin: 0;">
        <input type="text" name="search" placeholder="Rechercher par IMEI, Client, Technicien..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-search">Rechercher</button>
        <?php if($search != ""): ?>
            <a href="stock_clients.php"><button type="button" class="btn" style="background:#6b7280; color:white;">Réinitialiser</button></a>
        <?php endif; ?>
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Date d'ajout</th> <th>IMEI</th>
        <th>Client</th>
        <th>Technicien</th>
        <th>Matricule</th>
        <th>Statut</th>
        <th>Action</th>
    </tr>

    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td style="color: #666; font-weight: bold;">
            <?php echo isset($row['created_at']) ? $row['created_at'] : date("Y-m-d H:i"); ?>
        </td>
        <td><?php echo $row['imei']; ?></td>
        <td><?php echo $row['client_name']; ?></td>
        <td><?php echo $row['technicien']; ?></td>
        <td><?php echo $row['matricule']; ?></td>
        <td style="font-weight:bold;"><?php echo $row['statut']; ?></td>
        <td>
            <a href="?set_statut=Installé&id=<?php echo $row['id']; ?>"><button class="btn install">Installé</button></a>
            <a href="?set_statut=Stock Tanger&id=<?php echo $row['id']; ?>"><button class="btn stock">Stock Tanger</button></a>
            <a href="?set_statut=Casa Flash&id=<?php echo $row['id']; ?>"><button class="btn casa-flash">Casa Flash</button></a>
            <a href="?set_statut=Technicien&id=<?php echo $row['id']; ?>"><button class="btn tech">Technicien</button></a>
            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Supprimer ?')"><button class="btn btn-delete">Supprimer</button></a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>