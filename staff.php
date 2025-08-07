<?php 
include 'config.php';
checkLogin();
include 'header.php';

// Fetch staff from database
$result = $conn->query("SELECT * FROM staff");
?>

<div class="container">
    <h2>Staff Management</h2>
    
    <a href="add_staff.php" class="btn btn-primary">Add New Staff</a>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Role</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
        
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['role']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td>
                <a href="edit_staff.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                <a href="delete_staff.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include 'footer.php'; ?>