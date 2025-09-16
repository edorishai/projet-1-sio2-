<?php

session_start();
$pdo = new PDO('mysql:host=localhost;dbname=bibliotheque;charset=utf8', 'root', '');
$message = '';

$livre_id = $_GET['livre_id'] ?? null;
$titre = '';
if ($livre_id) {
    $stmt = $pdo->prepare("SELECT titre FROM livres WHERE id = ?");
    $stmt->execute([$livre_id]);
    $titre = $stmt->fetchColumn();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $livre_id = $_POST['livre_id'];
    $titre = $_POST['titre'];

    $user_id = $_SESSION['user_id'] ?? null;

    $stmtQ = $pdo->prepare("SELECT quantite FROM quantite WHERE livre_id = ?");
    $stmtQ->execute([$livre_id]);
    $quantite = $stmtQ->fetchColumn();

    if (!$user_id) {
        $message = "Vous n'êtes pas connecté.";
    } elseif ($quantite <= 0) {
        $message = "Ce livre n'est plus disponible.";
    } else {
        $stmtU = $pdo->prepare("UPDATE quantite SET quantite = quantite - 1 WHERE livre_id = ?");
        $stmtU->execute([$livre_id]);

        $stmtR = $pdo->prepare("INSERT INTO reservations (user_id, livre_id) VALUES (?, ?)");
        $stmtR->execute([$user_id, $livre_id]);

        $message = "Réservation confirmée pour '$titre' !";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver un livre</title>
    <link rel="stylesheet" href="css/reservation.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Réserver un livre</h1>
        <?php if ($message): ?>
            <p style="color:green"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <?php if ($livre_id): ?>
            <form method="post">
                <input type="hidden" name="livre_id" value="<?= $livre_id ?>">
                <input type="hidden" name="titre" value="<?= htmlspecialchars($titre) ?>">
                <label>Nom :</label><br>
                <input type="text" name="nom" required><br><br>
                <label>Prénom :</label><br>
                <input type="text" name="prenom" required><br><br>
                <label>Titre du livre :</label><br>
                <input type="text" name="titre_affiche" value="<?= htmlspecialchars($titre) ?>" readonly><br><br>
                <button type="submit" class="btn btn-success">Confirmer</button>
            </form>
        <?php elseif (!$livre_id): ?>
            <p>Aucun livre sélectionné.</p>
        <?php endif; ?>
    </div>
</body>
</html>