<?php 
include 'config.php';
checkLogin();
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    $stmt = $conn->prepare("INSERT INTO staff (name, role, email, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $role, $email, $phone);
    
    if ($stmt->execute()) {
        header("Location: staff.php");
        exit();
    } else {
        $error = "Error adding staff: " . $conn->error;
    }
}
?>

<div class="container">
    <h2>Add New Staff</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="add_staff.php">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="Manager">Manager</option>
            <option value="Coach">Coach</option>
            <option value="Physiotherapist">Physiotherapist</option>
            <option value="Scout">Scout</option>
            <option value="Administrator">Administrator</option>
        </select>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" required>
        
        <input type="submit" value="Add Staff">
    </form>
</div>

<?php include 'footer.php'; ?>