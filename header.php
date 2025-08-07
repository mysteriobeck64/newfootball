<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football Club Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<style>
.hd{
color:white;
}
</style>
    <header>
        <h1 class="hd">Football Club Management System</h1>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="players.php">Players</a></li>
                <li><a href="staff.php">Staff</a></li>
                <li><a href="teams.php">Teams</a></li>

                <li><a href="matches.php">Matches</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
<li><a href="register.php">Register</a></li>

            <?php endif; ?>
        </ul>
    </nav>