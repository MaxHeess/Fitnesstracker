<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <form method="POST">
        <label>Name:</label> <input type="text" name="name" required><br>
        <label>Alter:</label> <input type="number" name="age" required><br>
        <label>Größe (cm):</label> <input type="number" name="height" step="0.1" required><br>
        <label>Geschlecht:</label>
        <select name="gender">
            <option value="m">Männlich</option>
            <option value="w">Weiblich</option>
            <option value="div">Divers</option>
        </select><br>
        <label>Passwort:</label> <input type="password" name="password" required><br>
        <button type="submit">Registrieren</button><br>
        <button type="button" onclick="window.location.href='index.php'">Zurück</button>
    </form>

    <?php
    include 'db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $age = $_POST['age'];
        $height = $_POST['height'];
        $gender = $_POST['gender'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, age, height_cm, gender, password) VALUES (?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            echo "<p style='color:red;'>Fehler bei der Vorbereitung: " . $conn->error . "</p>";
        } else {
            $stmt->bind_param("sidss", $name, $age, $height, $gender, $password);
            
            if ($stmt->execute()) {
                echo "<p style='color:green;'>Registrierung erfolgreich. <br><br> <a href='login.php'>Jetzt einloggen</a></p>";
            } else {
                echo "<p style='color:red;'>Fehler bei der Registrierung: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }
    }
    ?>

</body>
</html>