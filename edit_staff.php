<?php 
include 'config.php';
checkLogin();
include 'header.php';

if (!isset($_GET['id'])) {
    header("Location: staff.php");
    exit();
}

$staff_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    $stmt = $conn->prepare("UPDATE staff SET name=?, role=?, email=?, phone=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $role, $email, $phone, $staff_id);
    
    if ($stmt->execute()) {
        header("Location: staff.php");
        exit();
    } else {
        $error = "Error updating staff: " . $conn->error;
    }
}

// Fetch staff data
$staff = $conn->query("SELECT * FROM staff WHERE id = $staff_id")->fetch_assoc();
if (!$staff) {
    header("Location: staff.php");
    exit();
}
?>

<div class="container">
    <h2>Edit Staff</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="edit_staff.php?id=<?php echo $staff_id; ?>">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $staff['name']; ?>" required>
        
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="Manager" <?php echo $staff['role'] == 'Manager' ? 'selected' : ''; ?>>Manager</option>
            <option value="Coach" <?php echo $staff['role'] == 'Coach' ? 'selected' : ''; ?>>Coach</option>
            <option value="Physiotherapist" <?php echo $staff['role'] == 'Physiotherapist' ? 'selected' : ''; ?>>Physiotherapist</option>
            <option value="Scout" <?php echo $staff['role'] == 'Scout' ? 'selected' : ''; ?>>Scout</option>
            <option value="Administrator" <?php echo $staff['role'] == 'Administrator' ? 'selected' : ''; ?>>Administrator</option>
        </select>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $staff['email']; ?>" required>
        
        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?php echo $staff['phone']; ?>" required>
        
        <input type="submit" value="Update Staff">
    </form>
</div>

<?php include 'footer.php'; ?>