<?php
include 'config.php';
checkLogin();

if (!isset($_GET['id'])) {
    header("Location: players.php");
    exit();
}

$player_id = $_GET['id'];

// Delete player
$stmt = $conn->prepare("DELETE FROM players WHERE id = ?");
$stmt->bind_param("i", $player_id);

if ($stmt->execute()) {
    header("Location: players.php?deleted=1");
} else {
    header("Location: players.php?error=1");
}

exit();
?>