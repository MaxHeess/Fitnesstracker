<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'auth.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $age = $_POST['age'];
    $height = $_POST['height'];
    $gender = $_POST['gender'];

    $stmt_update = $conn->prepare("UPDATE users SET age = ?, height_cm = ?, gender = ? WHERE id = ?");
    $stmt_update->bind_param("dssi", $age, $height, $gender, $user_id);
    $stmt_update->execute();

    header("Location: profil.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <h2>Dein Profil</h2>
    <form method="POST">
        Name: <strong><?= htmlspecialchars($user['name']) ?></strong><br><br>
        Alter: <input type="number" name="age" value="<?= htmlspecialchars($user['age']) ?>"><br>
        Größe (cm): <input type="number" name="height" step="0.1" value="<?= htmlspecialchars($user['height_cm']) ?>"><br>
        Geschlecht:
        <select name="gender">
            <option value="m" <?= $user['gender'] == 'm' ? 'selected' : '' ?>>Männlich</option>
            <option value="w" <?= $user['gender'] == 'w' ? 'selected' : '' ?>>Weiblich</option>
            <option value="div" <?= $user['gender'] == 'div' ? 'selected' : '' ?>>Divers</option>
        </select><br>
        <button type="submit">Profil aktualisieren</button>
    </form>

</body>
</html>