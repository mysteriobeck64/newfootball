<?php 
include 'config.php';
checkLogin();
include 'header.php';

// Fetch players from database
$result = $conn->query("SELECT * FROM players");
?>

<div class="container">
    <h2>Players Management</h2>
    
    <a href="add_player.php" class="btn btn-primary">Add New Player</a>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Position</th>
            <th>Team</th>
            <th>Jersey Number</th>
            <th>Actions</th>
        </tr>
        
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['position']; ?></td>
            <td><?php echo $row['team_id']; ?></td>
            <td><?php echo $row['jersey_number']; ?></td>
            <td>
                <a href="edit_player.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                <a href="delete_player.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include 'footer.php'; ?>