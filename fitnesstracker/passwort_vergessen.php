<?php
include 'db.php';

$nachricht = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
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

        $to = $user['email'];
        $subject_raw = "Passwort zurücksetzen";
        $subject = "=?UTF-8?B?" . base64_encode($subject_raw) . "?=";

        $message = "Hallo " . $user['name'] . ",\n\n\n";
        $message .= "Klicke auf den folgenden Link, um dein Passwort zurückzusetzen:\n$link\n\n";
        $message .= "Der Link ist 1 Stunde gültig.\n\n\n";
        $message .= "Viele Grüße\n\n";  
        $message .= "Dein MotionLog-Team";

        $headers = "From: info@maxheess.de\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

mail($to, $subject, $message, $headers);

        $nachricht = "<p>Ein Link zum Zurücksetzen wurde an deine E-Mail-Adresse gesendet.<br><br> Er ist 1 Stunde gültig.</p>";
    } else {
        $nachricht = "<p style='color: red;'>E-Mail-Adresse nicht gefunden.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Passwort vergessen</title>
    <link rel="icon" href="images/logo.png" type="image/png" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>

    <?php include 'header.php'; ?>

    <h2>Passwort vergessen</h2>

    <?php if ($nachricht): ?>
        <p><?= $nachricht ?></p>
    <?php endif; ?>

    <form method="POST">
        Gib deine E-Mail-Adresse ein:<br>
        <input type="email" name="email" required><br><br>
        <button type="submit">Zurücksetzen-Link generieren</button><br>
        <button type="button" onclick="window.location.href='login.php'">Zurück zum Login</button>
    </form>

</body>
</html>
