<?php

$pdo = new PDO('mysql:host=localhost;dbname=bibliotheque;charset=utf8', 'root', '');

// Récupération des filtres
$genre = $_GET['genre'] ?? '';
$auteur = $_GET['auteur'] ?? '';
$date = $_GET['date'] ?? '';

// Construction de la requête
$sql = "SELECT * FROM livres WHERE 1";
$params = [];

if ($genre) {
    $sql .= " AND genre = ?";
    $params[] = $genre;
}
if ($auteur) {
    $sql .= " AND auteur = ?";
    $params[] = $auteur;
}
if ($date) {
    $sql .= " AND date_publication = ?";
    $params[] = $date;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$livres = $stmt->fetchAll();

// Récupérer les genres et auteurs pour les filtres
$genres = $pdo->query("SELECT DISTINCT genre FROM livres")->fetchAll(PDO::FETCH_COLUMN);
$auteurs = $pdo->query("SELECT DISTINCT auteur FROM livres")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des livres</title>
    <link rel="stylesheet" href="css/livre.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Livres disponibles</h1>
        <form method="get" class="filtre-form">
            <select name="genre">
                <option value="">Tous les genres</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>" <?= $genre == $g ? 'selected' : '' ?>><?= htmlspecialchars($g) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="auteur">
                <option value="">Tous les auteurs</option>
                <?php foreach ($auteurs as $a): ?>
                    <option value="<?= htmlspecialchars($a) ?>" <?= $auteur == $a ? 'selected' : '' ?>><?= htmlspecialchars($a) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>

        <div class="livres-list">
            <?php foreach ($livres as $livre): ?>
                <?php
                    // Récupère la quantité pour ce livre
                    $stmtQ = $pdo->prepare("SELECT quantite FROM quantite WHERE livre_id = ?");
                    $stmtQ->execute([$livre['id']]);
                    $quantite = $stmtQ->fetchColumn();
                    if ($quantite === false) $quantite = 0;

                    // Détermine le statut
                    $statut = ($quantite > 0) ? 'Libre' : 'Réservé';
                    $disabled = ($quantite == 0) ? 'disabled' : '';
                ?>
                <div class="livre-card">
                    <!-- Ajoute l'image ici si tu veux : <img src=<?= htmlspecialchars($livre['image']) ?>" alt="Couverture" class="livre-img"> -->
                    <h2><?= htmlspecialchars($livre['titre']) ?></h2>
                    <p><strong>Auteur :</strong> <?= htmlspecialchars($livre['auteur']) ?></p>
                    <p><strong>Date de publication :</strong> <?= htmlspecialchars($livre['date_publication']) ?></p>
                    <p><strong>Genre :</strong> <?= htmlspecialchars($livre['genre']) ?></p>
                    <p><strong>Quantité :</strong> <?= $quantite ?></p>
                    <p><strong>Statut :</strong> <?= $statut ?></p>
                    <form action="reservation.php" method="get">
                        <input type="hidden" name="livre_id" value="<?= $livre['id'] ?>">
                        <button type="submit" class="btn btn-success" <?= $disabled ?>>Réserver</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <?php if (empty($livres)): ?>
                <p>Aucun livre trouvé.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>