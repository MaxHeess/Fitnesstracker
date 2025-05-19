<?php
include 'db.php';

$nachricht = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", time() + 3600);
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, $expires, $user['id']);
        $stmt->execute();

        $link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/passwort_zuruecksetzen.php?token=$token";
        $nachricht = "
            <p>Ein Link zum Zurücksetzen wurde erzeugt.<br><br> Er ist 1 Stunde gültig.</p>
            <p><a href=\"$link\" class=\"delete-link\">Hier klicken</a></p><br>";
    } else {
        $nachricht = "<p style='color: red;'>Benutzer nicht gefunden.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwort vergessen</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
    
<h2>Passwort vergessen</h2>

<?php if ($nachricht): ?>
    <p><?= $nachricht ?></p>
<?php endif; ?>

<form method="POST">
    Gib deinen Benutzernamen ein:<br>
    <input type="text" name="name" required><br><br>
    <button type="submit">Zurücksetzen-Link generieren</button><br>
    <button type="button" onclick="window.location.href='login.php'">Zurück zum Login</button>
</form>

</body>
</html>