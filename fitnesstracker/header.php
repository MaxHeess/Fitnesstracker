<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
    <h1>Fitnesstracker</h1>
    <img src="images/logo.png" class="logo">

    <?php if (isset($_SESSION['user_id'])): ?>
        <nav>
            <a href="index.php">Dashboard</a> 
            <a href="profil.php">Profil</a> 
            <a href="bmi.php">BMI</a> 
            <a href="gewicht.php">Gewicht</a> 
            <a href="aktivitaet.php">Aktivität</a> 
            <a href="statistik.php">Statistik</a> 
            <a href="logout.php" class="logout-icon-link" title="Logout">
                <img src="images/logout.png" alt="Logout" class="logout-icon">
            </a>
        </nav>
    <?php endif; ?>

    <hr>
</header>