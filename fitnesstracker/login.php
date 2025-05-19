<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['timeout'])) {
    $timeout_message = "Deine Sitzung wurde wegen Inaktivität beendet. Bitte logge dich erneut ein.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['last_activity'] = time();
        header("Location: index.php");
        exit();
    } else {
        $error = "Login fehlgeschlagen.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitnesstracker</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <?php if (isset($timeout_message)): ?>
        <p style="color: red;"><?= htmlspecialchars($timeout_message) ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        Name: <input type="text" name="name"><br>
        Passwort: <input type="password" name="password"><br>
        <a href="passwort_vergessen.php" class="delete-link">Passwort vergessen?</a><br><br>
        <button type="submit">Einloggen</button><br>
        Neu hier?<br><br>
        <button type="button" onclick="window.location.href='register.php'">Registrieren</button>
    </form>

</body>
</html>
