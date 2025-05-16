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

$height_m = $user['height_cm'] / 100;

$stmt_weight = $conn->prepare("SELECT weight_kg FROM weights WHERE user_id = ? ORDER BY date DESC, id DESC LIMIT 1");
$stmt_weight->bind_param("i", $user_id);
$stmt_weight->execute();
$result_weight = $stmt_weight->get_result();
$gewicht = $result_weight->fetch_assoc()['weight_kg'] ?? null;

if ($gewicht && $height_m > 0) {
    $bmi = $gewicht / ($height_m * $height_m);

    $idealgewicht = ($user['gender'] == 'w')
        ? $user['height_cm'] - 100 - 10
        : $user['height_cm'] - 100;

    if ($bmi < 18.5) $bmi_klasse = "Untergewicht";
    elseif ($bmi < 25) $bmi_klasse = "Normalgewicht";
    elseif ($bmi < 30) $bmi_klasse = "Übergewicht";
    else $bmi_klasse = "Adipositas";
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMI</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <h2>BMI-Berechnung</h2>
    <?php if ($gewicht): ?>
        <p>Aktuelles Gewicht: <?= htmlspecialchars($gewicht) ?> kg</p>
        <p>BMI: <?= round($bmi, 1) ?> – <?= $bmi_klasse ?></p>
        <p>Idealgewicht (ungefähr): <?= $idealgewicht ?> kg</p>
    <?php else: ?>
        <p>Bitte zuerst ein Gewicht unter <a href="gewicht.php">Gewicht eintragen</a> hinzufügen.</p>
    <?php endif; ?>
</body>
</html>