<?php
include 'config.php';
checkLogin();

if (!isset($_GET['id'])) {
    header("Location: staff.php");
    exit();
}

$staff_id = $_GET['id'];

// Delete staff
$stmt = $conn->prepare("DELETE FROM staff WHERE id = ?");
$stmt->bind_param("i", $staff_id);

if ($stmt->execute()) {
    header("Location: staff.php?deleted=1");
} else {
    header("Location: staff.php?error=1");
}

exit();
?>