<?php
include 'db.php';

$nachricht = "";
$token = $_GET['token'] ?? null;
$token_valid = false;
$show_button = false;

if ($token) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $token_valid = true;

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_password"])) {
            $new_password_input = $_POST["new_password"];

            if (password_verify($new_password_input, $user['password'])) {
                $nachricht = "<p style='color: red;'>Das neue Passwort darf nicht gleich dem alten sein.</p>";
            } else {
                $new_password_hash = password_hash($new_password_input, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
                $stmt->bind_param("si", $new_password_hash, $user['id']);
                if ($stmt->execute()) {
                    $nachricht = "<p>Dein Passwort wurde erfolgreich geändert.</p><br>";
                    $token_valid = false;
                    $show_button = true;
                } else {
                    $nachricht = "Fehler beim Zurücksetzen des Passworts.";
                }
            }
        }
    } else {
        $nachricht = "Der Link ist ungültig oder abgelaufen.";
    }
} else {
    $nachricht = "Kein Token angegeben.";
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Passwort zurücksetzen</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<?php include 'header.php'; ?>

<h2>Passwort zurücksetzen</h2>

    <?php if ($nachricht): ?>
        <?= $nachricht ?>
        <?php if ($show_button): ?>
            <div>
                &nbsp;&nbsp;<a href='login.php'>Zurück zum Login</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($token_valid): ?>
        <form method="POST">
            <label>Neues Passwort:</label><br />
            <input type="password" name="new_password" required /><br /><br />
            <button type="submit">Passwort speichern</button>
        </form>
    <?php endif; ?>

</body>
</html>
