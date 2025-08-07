<?php
include 'config.php';
checkLogin();

if (!isset($_GET['id'])) {
    header("Location: matches.php");
    exit();
}

$match_id = $_GET['id'];

// Delete match
$stmt = $conn->prepare("DELETE FROM matches WHERE id = ?");
$stmt->bind_param("i", $match_id);

if ($stmt->execute()) {
    header("Location: matches.php?deleted=1");
} else {
    header("Location: matches.php?error=1");
}

exit();
?>