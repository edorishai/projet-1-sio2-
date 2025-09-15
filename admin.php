<?php


session_start();
$pdo = new PDO('mysql:host=localhost;dbname=bibliotheque;charset=utf8', 'root', '');

// Vérifie si l'admin est connecté
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: admin_connexion.php');
    exit;
}

// Actions : supprimer, augmenter/diminuer quantité, changer statut
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $livre_id = $_POST['livre_id'];
        $pdo->prepare("DELETE FROM livres WHERE id = ?")->execute([$livre_id]);
        $pdo->prepare("DELETE FROM quantite WHERE livre_id = ?")->execute([$livre_id]);
    }
    if (isset($_POST['increase'])) {
        $livre_id = $_POST['livre_id'];
        $pdo->prepare("UPDATE quantite SET quantite = quantite + 1 WHERE livre_id = ?")->execute([$livre_id]);
    }
    if (isset($_POST['decrease'])) {
        $livre_id = $_POST['livre_id'];
        $pdo->prepare("UPDATE quantite SET quantite = GREATEST(quantite - 1, 0) WHERE livre_id = ?")->execute([$livre_id]);
    }
    if (isset($_POST['change_statut'])) {
        $livre_id = $_POST['livre_id'];
        $statut = $_POST['statut'];
        $pdo->prepare("UPDATE quantite SET statut = ? WHERE livre_id = ?")->execute([$statut, $livre_id]);
    }
}

// Récupère tous les livres
$sql = "SELECT l.*, q.quantite, q.statut FROM livres l LEFT JOIN quantite q ON l.id = q.livre_id";
$stmt = $pdo->query($sql);
$livres = $stmt->fetchAll();

include 'header.php';
?>
<head>
    <meta charset="UTF-8">
    <title>Gestion des livres</title>
    <link rel="stylesheet" href="css/livre.css">
</head>
<div class="container">
    <h1>Gestion des livres</h1>
    <div class="livres-list">
        <?php foreach ($livres as $livre): ?>
            <div class="livre-card">
                <?php if (!empty($livre['image'])): ?>
                    <img src="<?= htmlspecialchars($livre['image']) ?>" alt="Couverture" class="livre-img">
                <?php endif; ?>
                <h2><?= htmlspecialchars($livre['titre']) ?></h2>
                <p><strong>Auteur :</strong> <?= htmlspecialchars($livre['auteur']) ?></p>
                <p><strong>Date de publication :</strong> <?= htmlspecialchars($livre['date_publication']) ?></p>
                <p><strong>Genre :</strong> <?= htmlspecialchars($livre['genre']) ?></p>
                <p><strong>Quantité :</strong> <?= $livre['quantite'] ?? 0 ?></p>
                <p><strong>Statut :</strong> <?= $livre['statut'] ?? 'Libre' ?></p>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="livre_id" value="<?= $livre['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Supprimer ce livre ?')">Supprimer</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="livre_id" value="<?= $livre['id'] ?>">
                    <button type="submit" name="increase" class="btn btn-success">+ Quantité</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="livre_id" value="<?= $livre['id'] ?>">
                    <button type="submit" name="decrease" class="btn btn-warning">- Quantité</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="livre_id" value="<?= $livre['id'] ?>">
                    <select name="statut">
                        <option value="Libre" <?= ($livre['statut'] ?? '') === 'Libre' ? 'selected' : '' ?>>Libre</option>
                        <option value="Réservé" <?= ($livre['statut'] ?? '') === 'Réservé' ? 'selected' : '' ?>>Réservé</option>
                        <option value="En attente" <?= ($livre['statut'] ?? '') === 'En attente' ? 'selected' : '' ?>>En attente</option>
                    </select>
                    <button type="submit" name="change_statut" class="btn btn-primary">Changer statut</button>
                </form>
            </div>
        <?php endforeach; ?>
        <?php if (empty($livres)): ?>
            <p>Aucun livre trouvé.</p>
        <?php endif; ?>
    </div>
</div>