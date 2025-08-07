<?php 
include 'config.php';
checkLogin();
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $match_date = $_POST['match_date'];
    $home_team_id = $_POST['home_team_id'];
    $away_team_id = $_POST['away_team_id'];
    $venue = $_POST['venue'];
    $home_team_score = $_POST['home_team_score'];
    $away_team_score = $_POST['away_team_score'];
    
    $stmt = $conn->prepare("INSERT INTO matches (match_date, home_team_id, away_team_id, venue, home_team_score, away_team_score) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siisii", $match_date, $home_team_id, $away_team_id, $venue, $home_team_score, $away_team_score);
    
    if ($stmt->execute()) {
        header("Location: matches.php");
        exit();
    } else {
        $error = "Error adding match: " . $conn->error;
    }
}

// Fetch teams for dropdown
$teams = $conn->query("SELECT id, name FROM teams");
?>

<div class="container">
    <h2>Add New Match</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="add_match.php">
        <label for="match_date">Match Date:</label>
        <input type="datetime-local" id="match_date" name="match_date" required>
        
        <label for="home_team_id">Home Team:</label>
        <select id="home_team_id" name="home_team_id" required>
            <?php while ($team = $teams->fetch_assoc()): ?>
                <option value="<?php echo $team['id']; ?>"><?php echo $team['name']; ?></option>
            <?php endwhile; ?>
        </select>
        
        <label for="away_team_id">Away Team:</label>
        <select id="away_team_id" name="away_team_id" required>
            <?php 
            // Reset pointer to start
            $teams->data_seek(0);
            while ($team = $teams->fetch_assoc()): ?>
                <option value="<?php echo $team['id']; ?>"><?php echo $team['name']; ?></option>
            <?php endwhile; ?>
        </select>
        
        <label for="venue">Venue:</label>
        <input type="text" id="venue" name="venue" required>
        
        <label for="home_team_score">Home Team Score:</label>
        <input type="number" id="home_team_score" name="home_team_score" min="0">
        
        <label for="away_team_score">Away Team Score:</label>
        <input type="number" id="away_team_score" name="away_team_score" min="0">
        
        <input type="submit" value="Add Match">
    </form>
</div>

<?php include 'footer.php'; ?>