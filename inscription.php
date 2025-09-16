<?php

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=bibliotheque;charset=utf8', 'root', '');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Vérification si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $message = "Cet email existe déjà.";
    } else {
        // Insertion dans la base
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $password, $role]);
        header('Location: page_connexion.php');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="css/inscription.css">
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>
        <?php if ($message): ?>
            <p style="color:red"><?= $message ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="nom" placeholder="Nom" required><br><br>
            <input type="text" name="prenom" placeholder="Prénom" required><br><br>
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="password" placeholder="Mot de passe" required><br><br>
            <select name="role">
                <option value="etudiant">Étudiant</option>
                <option value="enseignant">Enseignant</option>
                <option value="admin">Admin</option>
            </select><br><br>
            <button class="btn btn-success" type="submit">Inscription</button>
        </form>
    </div>
</body>
</html>