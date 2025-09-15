<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=bibliotheque;charset=utf8', 'root', '');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id']; // <-- AJOUTE CETTE LIGNE
        header('Location: livre.php');
        exit;
    } else {
        $message = "L'adresse email ou le mot de passe est incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
        <link rel="stylesheet" href="css/connexion_page.css">
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>
        <?php if ($message): ?>
            <p style="color:red"><?= $message ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="password" placeholder="Mot de passe" required><br><br>
            <button class="btn btn-primary" type="submit">Connexion</button>
        </form>
    </div>
</body>
</html>