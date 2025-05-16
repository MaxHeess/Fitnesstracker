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
    $stmt = $conn->prepare("DELETE FROM weights WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    header("Location: gewicht.php");
    exit;
}

if (isset($_GET['delete_all'])) {
    $stmt = $conn->prepare("DELETE FROM weights WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: gewicht.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $weight = $_POST['weight'];
    $stmt = $conn->prepare("INSERT INTO weights (user_id, date, weight_kg) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $user_id, $date, $weight);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT id, date, weight_kg FROM weights WHERE user_id = ? ORDER BY date");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$daten = [];
while ($row = $result->fetch_assoc()) {
    $daten[] = $row;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gewicht</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <h2>Gewicht eintragen</h2>
    <form method="POST">
        Datum: <input type="date" name="date" value="<?= date('Y-m-d') ?>" required><br>
        Gewicht (kg): <input type="number" name="weight" step="0.1" required><br>
        <button type="submit">Speichern</button>
    </form>

    <form method="GET" onsubmit="return confirm('Willst du wirklich alle Einträge löschen?');">
        <input type="hidden" name="delete_all" value="1">
        <button type="submit" style="background-color:red; color:white;">Alle Gewichtseinträge löschen</button>
    </form><br><br>

    <h2>Bisherige Einträge</h2>
    <table>
        <tr><th>Datum</th><th>Gewicht (kg)</th><th>Aktion</th></tr>
        <?php foreach ($daten as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['weight_kg']) ?></td>
                <td>
                    <a href="?delete_id=<?= $row['id'] ?>" class="delete-link" onclick="return confirm('Eintrag wirklich löschen?');">Löschen</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table><br><br>

    <h2>Verlauf</h2>
    <canvas id="chart" width="450" height="150"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const daten = <?= json_encode($daten); ?>;
    const ctx = document.getElementById('chart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: daten.map(d => d.date),
            datasets: [{
                label: 'Gewicht (kg)',
                data: daten.map(d => d.weight_kg),
                borderColor: 'green',
                fill: false,
                tension: 0.2,
                pointRadius: 6,
                pointBackgroundColor: 'green' 
            }]
        },
        options: {
            scales: {
                x: {
                    ticks: { color: 'white' },
                    grid: {
                        color: 'white',
                        lineWidth: 0.5 
                    }
                },
                y: {
                    ticks: { color: 'white' },
                    grid: {
                        color: 'white',
                        lineWidth: 0.5  
                    }
                }
            },
            plugins: {
                legend: {
                    labels: { color: 'white' }
                }
            }
        }
    });
    </script>

</body>
</html>