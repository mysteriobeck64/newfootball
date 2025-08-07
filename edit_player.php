<?php 
include 'config.php';
checkLogin();
include 'header.php';

if (!isset($_GET['id'])) {
    header("Location: players.php");
    exit();
}

$player_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $team_id = $_POST['team_id'];
    $jersey_number = $_POST['jersey_number'];
    $date_of_birth = $_POST['date_of_birth'];
    $nationality = $_POST['nationality'];
    
    $stmt = $conn->prepare("UPDATE players SET name=?, position=?, team_id=?, jersey_number=?, date_of_birth=?, nationality=? WHERE id=?");
    $stmt->bind_param("ssiissi", $name, $position, $team_id, $jersey_number, $date_of_birth, $nationality, $player_id);
    
    if ($stmt->execute()) {
        header("Location: players.php");
        exit();
    } else {
        $error = "Error updating player: " . $conn->error;
    }
}

// Fetch player data
$player = $conn->query("SELECT * FROM players WHERE id = $player_id")->fetch_assoc();
if (!$player) {
    header("Location: players.php");
    exit();
}

// Fetch teams for dropdown
$teams = $conn->query("SELECT id, name FROM teams");
?>

<div class="container">
    <h2>Edit Player</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="edit_player.php?id=<?php echo $player_id; ?>">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $player['name']; ?>" required>
        
        <label for="position">Position:</label>
        <select id="position" name="position" required>
            <option value="Goalkeeper" <?php echo $player['position'] == 'Goalkeeper' ? 'selected' : ''; ?>>Goalkeeper</option>
            <option value="Defender" <?php echo $player['position'] == 'Defender' ? 'selected' : ''; ?>>Defender</option>
            <option value="Midfielder" <?php echo $player['position'] == 'Midfielder' ? 'selected' : ''; ?>>Midfielder</option>
            <option value="Forward" <?php echo $player['position'] == 'Forward' ? 'selected' : ''; ?>>Forward</option>
        </select>
        
        <label for="team_id">Team:</label>
        <select id="team_id" name="team_id" required>
            <?php while ($team = $teams->fetch_assoc()): ?>
                <option value="<?php echo $team['id']; ?>" <?php echo $team['id'] == $player['team_id'] ? 'selected' : ''; ?>>
                    <?php echo $team['name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        
        <label for="jersey_number">Jersey Number:</label>
        <input type="number" id="jersey_number" name="jersey_number" value="<?php echo $player['jersey_number']; ?>" min="1" max="99" required>
        
        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $player['date_of_birth']; ?>" required>
        
        <label for="nationality">Nationality:</label>
        <input type="text" id="nationality" name="nationality" value="<?php echo $player['nationality']; ?>" required>
        
        <input type="submit" value="Update Player">
    </form>
</div>

<?php include 'footer.php'; ?>