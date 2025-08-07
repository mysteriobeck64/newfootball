<?php
include 'config.php';
checkLogin();

if (!isset($_GET['id'])) {
    header("Location: teams.php");
    exit();
}

$team_id = $_GET['id'];

// Delete team
$stmt = $conn->prepare("DELETE FROM teams WHERE id = ?");
$stmt->bind_param("i", $team_id);

if ($stmt->execute()) {
    header("Location: teams.php?deleted=1");
} else {
    header("Location: teams.php?error=1");
}

exit();
?>