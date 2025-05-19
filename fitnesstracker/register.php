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
        <label>E-Mail:</label> <input type="email" name="email" required><br>
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
        $email = $_POST['email'];
        $age = $_POST['age'];
        $height = $_POST['height'];
        $gender = $_POST['gender'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM users WHERE name = ? OR email = ?");
        $check->bind_param("ss", $name, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            echo "<p style='color:red;'>Name oder E-Mail ist bereits vergeben.</p>";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, age, height_cm, gender, password) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssidss", $name, $email, $age, $height, $gender, $password);

                if ($stmt->execute()) {
                    echo "<p style='color:green;'>Registrierung erfolgreich. <br><br> <a href='login.php'>Jetzt einloggen</a></p>";
                } else {
                    echo "<p style='color:red;'>Fehler bei der Registrierung: " . $stmt->error . "</p>";
                }

                $stmt->close();
            } else {
                echo "<p style='color:red;'>Fehler bei der Vorbereitung: " . $conn->error . "</p>";
            }
        }

        $check->close();
    }
    ?>

</body>
</html>
