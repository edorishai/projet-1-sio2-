<?php

session_start();
$pdo = new PDO('mysql:host=localhost;dbname=bibliotheque;charset=utf8', 'root', '');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];

    $stmt = $pdo->query("SELECT password FROM admin LIMIT 1");
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['is_admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $message = "Mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="css/connexion_page.css">
</head>
<body>
    <div class="container">
        <h1>Connexion Admin</h1>
        <?php if ($message): ?>
            <p style="color:red"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="password" name="password" placeholder="Mot de passe" required><br><br>
            <button type="submit" class="btn btn-primary">Confirmer</button>
        </form>
    </div>
</body>
</html>