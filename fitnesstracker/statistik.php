<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'auth.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT type, SUM(duration) AS total_dauer
    FROM activities
    WHERE user_id = ?
    GROUP BY type
");
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
    <title>Statistik</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <h2>Deine Aktivitäten (gesamt)</h2>
    <canvas id="chart" width="450" height="150"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const daten = <?= json_encode($daten); ?>;
    const ctx = document.getElementById('chart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: daten.map(d => d.type),
            datasets: [{
                label: 'Minuten insgesamt',
                data: daten.map(d => d.total_dauer),
                backgroundColor: 'green',
                borderColor: 'white',
                borderWidth: 1
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