<?php 
include 'config.php';
checkLogin();
include 'header.php';

if (!isset($_GET['id'])) {
    header("Location: teams.php");
    exit();
}

$team_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $coach = $_POST['coach'];
    $home_ground = $_POST['home_ground'];
    $founded_year = $_POST['founded_year'];
    
    $stmt = $conn->prepare("UPDATE teams SET name=?, coach=?, home_ground=?, founded_year=? WHERE id=?");
    $stmt->bind_param("sssii", $name, $coach, $home_ground, $founded_year, $team_id);
    
    if ($stmt->execute()) {
        header("Location: teams.php");
        exit();
    } else {
        $error = "Error updating team: " . $conn->error;
    }
}

// Fetch team data
$team = $conn->query("SELECT * FROM teams WHERE id = $team_id")->fetch_assoc();
if (!$team) {
    header("Location: teams.php");
    exit();
}
?>

<div class="container">
    <h2>Edit Team</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="edit_team.php?id=<?php echo $team_id; ?>">
        <label for="name">Team Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $team['name']; ?>" required>
        
        <label for="coach">Coach:</label>
        <input type="text" id="coach" name="coach" value="<?php echo $team['coach']; ?>" required>
        
        <label for="home_ground">Home Ground:</label>
        <input type="text" id="home_ground" name="home_ground" value="<?php echo $team['home_ground']; ?>" required>
        
        <label for="founded_year">Founded Year:</label>
        <input type="number" id="founded_year" name="founded_year" value="<?php echo $team['founded_year']; ?>" min="1800" max="<?php echo date('Y'); ?>" required>
        
        <input type="submit" value="Update Team">
    </form>
</div>

<?php include 'footer.php'; ?>