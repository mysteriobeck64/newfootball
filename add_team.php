<?php 
include 'config.php';
checkLogin();
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $coach = $_POST['coach'];
    $home_ground = $_POST['home_ground'];
    $founded_year = $_POST['founded_year'];
    
    $stmt = $conn->prepare("INSERT INTO teams (name, coach, home_ground, founded_year) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $coach, $home_ground, $founded_year);
    
    if ($stmt->execute()) {
        header("Location: teams.php");
        exit();
    } else {
        $error = "Error adding team: " . $conn->error;
    }
}
?>

<div class="container">
    <h2>Add New Team</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="add_team.php">
        <label for="name">Team Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="coach">Coach:</label>
        <input type="text" id="coach" name="coach" required>
        
        <label for="home_ground">Home Ground:</label>
        <input type="text" id="home_ground" name="home_ground" required>
        
        <label for="founded_year">Founded Year:</label>
        <input type="number" id="founded_year" name="founded_year" min="1800" max="<?php echo date('Y'); ?>" required>
        
        <input type="submit" value="Add Team">
    </form>
</div>

<?php include 'footer.php'; ?>