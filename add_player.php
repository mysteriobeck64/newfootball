<?php 
include 'config.php';
checkLogin();
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $team_id = $_POST['team_id'];
    $jersey_number = $_POST['jersey_number'];
    $date_of_birth = $_POST['date_of_birth'];
    $nationality = $_POST['nationality'];
    
    $stmt = $conn->prepare("INSERT INTO players (name, position, team_id, jersey_number, date_of_birth, nationality) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiss", $name, $position, $team_id, $jersey_number, $date_of_birth, $nationality);
    
    if ($stmt->execute()) {
        header("Location: players.php");
        exit();
    } else {
        $error = "Error adding player: " . $conn->error;
    }
}

// Fetch teams for dropdown
$teams = $conn->query("SELECT id, name FROM teams");
?>

<div class="container">
    <h2>Add New Player</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="add_player.php">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="position">Position:</label>
        <select id="position" name="position" required>
            <option value="Goalkeeper">Goalkeeper</option>
            <option value="Defender">Left Back</option>
            <option value="Defender">Center Back</option>
            <option value="Defender">Right Back</option>
            <option value="Midfielder">Left Midfielder</option>
            <option value="Midfielder">Center Midfielder</option>
            <option value="Midfielder">Right Midfielder</option>
            <option value="Forward">Left Winger</option>
            <option value="Forward">Center Forward</option>
            <option value="Forward">Right Winger</option>
        </select>
        
        <label for="team_id">Team:</label>
        <select id="team_id" name="team_id" required>
            <?php while ($team = $teams->fetch_assoc()): ?>
                <option value="<?php echo $team['id']; ?>"><?php echo $team['name']; ?></option>
            <?php endwhile; ?>
        </select>
        
        <label for="jersey_number">Jersey Number:</label>
        <input type="number" id="jersey_number" name="jersey_number" min="1" max="99" required>
        
        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" id="date_of_birth" name="date_of_birth" required>
        
        <label for="nationality">Nationality:</label>
        <input type="text" id="nationality" name="nationality" required>
        
        <input type="submit" value="Add Player">
    </form>
</div>

<?php include 'footer.php'; ?>