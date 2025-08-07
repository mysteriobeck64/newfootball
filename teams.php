<?php 
include 'config.php';
checkLogin();
include 'header.php';

// Fetch teams from database
$result = $conn->query("SELECT * FROM teams");
?>

<div class="container">
    <h2>Teams Management</h2>
    
    <a href="add_team.php" class="btn btn-primary">Add New Team</a>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Coach</th>
            <th>Home Ground</th>
            <th>Founded</th>
            <th>Actions</th>
        </tr>
        
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['coach']; ?></td>
            <td><?php echo $row['home_ground']; ?></td>
            <td><?php echo $row['founded_year']; ?></td>
            <td>
                <a href="edit_team.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                <a href="delete_team.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include 'footer.php'; ?>