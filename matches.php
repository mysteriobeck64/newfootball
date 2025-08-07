<?php 
include 'config.php';
checkLogin();
include 'header.php';

// Fetch matches from database with team names
$result = $conn->query("
    SELECT m.*, t1.name as home_team_name, t2.name as away_team_name 
    FROM matches m
    JOIN teams t1 ON m.home_team_id = t1.id
    JOIN teams t2 ON m.away_team_id = t2.id
    ORDER BY m.match_date DESC
");
?>

<div class="container">
    <h2>Matches Management</h2>
    
    <a href="add_match.php" class="btn btn-primary">Add New Match</a>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Home Team</th>
            <th>Away Team</th>
            <th>Venue</th>
            <th>Result</th>
            <th>Actions</th>
        </tr>
        
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['match_date']; ?></td>
            <td><?php echo $row['home_team_name']; ?></td>
            <td><?php echo $row['away_team_name']; ?></td>
            <td><?php echo $row['venue']; ?></td>
            <td><?php echo $row['home_team_score'] . ' - ' . $row['away_team_score']; ?></td>
            <td>
                <a href="edit_match.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                <a href="delete_match.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include 'footer.php'; ?>