<?php
require_once 'config.php';
checkLogin();
if ($_SESSION['user_role'] != 'admin') {
    header("Location: unauthorized.php");
    exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $request_id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    $request = $conn->query("SELECT * FROM team_requests WHERE id = $request_id")->fetch_assoc();
    
    if ($request) {
        if ($action == 'approve') {
            // Update player's team
            $conn->query("UPDATE players SET team_id = ".$request['team_id']." 
                         WHERE id = ".$request['player_id']);
            
            // Update request status
            $conn->query("UPDATE team_requests 
                         SET status = 'approved', response_date = NOW() 
                         WHERE id = $request_id");
            
            $_SESSION['success'] = "Request approved and player added to team!";
        } 
        elseif ($action == 'reject') {
            $conn->query("UPDATE team_requests 
                         SET status = 'rejected', response_date = NOW() 
                         WHERE id = $request_id");
            $_SESSION['success'] = "Request rejected";
        }
    } else {
        $_SESSION['error'] = "Invalid request";
    }
}

header("Location: admin.php");
exit();
?>