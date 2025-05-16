<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'auth.php';

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM activities WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    header("Location: aktivitaet.php");
    exit;
}

if (isset($_GET['delete_all'])) {
    $stmt = $conn->prepare("DELETE FROM activities WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: aktivitaet.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $type = $_POST['type'];
    $duration = (int)$_POST['duration'];
    $calories = round($duration * 5.5);

    $stmt = $conn->prepare("INSERT INTO activities (user_id, date, type, duration, calories) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issii", $user_id, $date, $type, $duration, $calories);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT id, date, type, duration, calories FROM activities WHERE user_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$aktivitaeten = [];
while ($row = $result->fetch_assoc()) {
    $aktivitaeten[] = $row;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivität</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <h2>Aktivität erfassen</h2>
    <form method="POST">
        Datum: <input type="date" name="date" value="<?= date('Y-m-d') ?>" required><br>
        Sportart:
        <select name="type">
            <option value="Gehen">Gehen</option>
            <option value="Laufen">Laufen</option>
            <option value="Walking">Walking</option>
            <option value="Radfahren">Radfahren</option>
            <option value="Workout">Workout</option>
            <option value="Yoga">Yoga</option>
            <option value="HIIT">HIIT</option>
            <option value="Schwimmen">Schwimmen</option>
            <option value="Pilates">Pilates</option>
            <option value="Tennis">Tennis</option>
            <option value="Fußball">Fußball</option>
            <option value="Tanzen">Tanzen</option>
            <option value="Klettern">Klettern</option>
            <option value="Rudern">Rudern</option>
            <option value="Crosstrainer">Crosstrainer</option>
        </select><br>
        Dauer (Minuten): <input type="number" name="duration" required><br>
        <button type="submit">Speichern</button>
    </form>

    <form method="GET" onsubmit="return confirm('Willst du wirklich alle Aktivitäten löschen?');">
        <input type="hidden" name="delete_all" value="1">
        <button type="submit" style="background-color:red; color:white;">Alle Aktivitäten löschen</button>
    </form><br><br>

    <h2>Letzte Aktivitäten</h2>
    <table>
        <tr><th>Datum</th><th>Sportart</th><th>Dauer</th><th>Kalorien</th><th>Aktionen</th></tr>
        <?php foreach ($aktivitaeten as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['type']) ?></td>
                <td><?= htmlspecialchars($row['duration']) ?> Min</td>
                <td><?= htmlspecialchars($row['calories']) ?> kcal</td>
                <td>
                    <a href="?delete_id=<?= $row['id'] ?>" class="delete-link" onclick="return confirm('Eintrag wirklich löschen?');">Löschen</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table><br>

</body>
</html>