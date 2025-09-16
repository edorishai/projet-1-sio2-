<?php

session_start();
$pdo = new PDO('mysql:host=localhost;dbname=bibliotheque;charset=utf8', 'root', '');

// Récupère l'user_id connecté
$user_id = $_SESSION['user_id'] ?? null;

$reservations = [];
if ($user_id) {
    $sql = "SELECT r.id, l.titre, r.date_reservation, r.statut
            FROM reservations r
            JOIN livres l ON r.livre_id = l.id
            WHERE r.user_id = ?
            ORDER BY r.date_reservation DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des réservations</title>
    <link rel="stylesheet" href="css/historique.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Historique des réservations</h1>
        <table border="1" style="width:100%;text-align:center;">
            <tr>
                <th>ID</th>
                <th>Titre du livre</th>
                <th>Date de réservation</th>
                <th>Statut</th>
            </tr>
            <?php foreach ($reservations as $res): ?>
            <tr>
                <td><?= $res['id'] ?></td>
                <td><?= htmlspecialchars($res['titre']) ?></td>
                <td><?= $res['date_reservation'] ?></td>
                <td><?= $res['statut'] ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($reservations)): ?>
            <tr><td colspan="4">Aucune réservation trouvée.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>