<?php
require_once 'config.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['team_id'])) {
    $player = $conn->query("SELECT id FROM players WHERE user_id = ".$_SESSION['user_id'])->fetch_assoc();
    
    if ($player) {
        $team_id = (int)$_POST['team_id'];
        
        // Check if already in a team
        if ($player['team_id']) {
            $_SESSION['error'] = "You are already on a team!";
        } 
        // Check for existing pending request
        elseif ($conn->query("SELECT id FROM team_requests 
                            WHERE player_id = ".$player['id']." 
                            AND team_id = $team_id 
                            AND status = 'pending'")->num_rows > 0) {
            $_SESSION['error'] = "You already have a pending request for this team";
        } else {
            $conn->query("INSERT INTO team_requests (player_id, team_id) 
                         VALUES (".$player['id'].", $team_id)");
            $_SESSION['success'] = "Request submitted successfully!";
        }
    } else {
        $_SESSION['error'] = "Player profile not found";
    }
}

header("Location: players.php");
exit();
?>