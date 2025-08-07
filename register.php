<?php
require_once 'config.php';

$error = '';
$success = '';
$available_roles = ['manager', 'coach', 'player', 'staff'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $team_id = ($role == 'player') ? (int)$_POST['team_id'] : null;

    // Validate inputs
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email) || empty($role)) {
        $error = 'All fields are required';
    } elseif (!in_array($role, $available_roles)) {
        $error = 'Invalid role selected';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        try {
            $conn->begin_transaction();

            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $error = 'Username or email already exists';
                $conn->rollback();
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $hashed_password, $email, $role);
                
                if ($stmt->execute()) {
                    $user_id = $conn->insert_id;
                    
                    // Create role-specific profile
                    if ($role == 'player') {
                        // Validate team exists
                        $team_check = $conn->prepare("SELECT id FROM teams WHERE id = ?");
                        $team_check->bind_param("i", $team_id);
                        $team_check->execute();
                        
                        if ($team_check->get_result()->num_rows == 0) {
                            throw new Exception("Selected team does not exist");
                        }
                        
                        $profile_stmt = $conn->prepare("INSERT INTO players 
                            (user_id, name, position, team_id, jersey_number, date_of_birth, nationality) 
                            VALUES (?, ?, 'Player', ?, 0, CURDATE(), '')");
                        $profile_stmt->bind_param("isi", $user_id, $username, $team_id);
                    } 
                    elseif ($role == 'coach' || $role == 'staff') {
                        $profile_stmt = $conn->prepare("INSERT INTO staff 
                            (user_id, name, role, email, phone) 
                            VALUES (?, ?, ?, ?, '')");
                        $profile_stmt->bind_param("isss", $user_id, $username, $role, $email);
                    }
                    
                    if (isset($profile_stmt) && !$profile_stmt->execute()) {
                        throw new Exception("Failed to create profile");
                    }
                    
                    $conn->commit();
                    $success = 'Registration successful! You can now <a href="login.php">login</a>';
                } else {
                    throw new Exception("Registration failed");
                }
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}

require_once 'header.php';
?>

<div class="container">
    <h2>Register New Account</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php else: ?>
        <form method="post" action="register.php" id="registration-form">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password (min 8 characters):</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="role">Account Type:</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="">Select your role</option>
                    <?php foreach ($available_roles as $role_option): ?>
                        <option value="<?php echo $role_option; ?>" 
                            <?php echo ($_POST['role'] ?? '') == $role_option ? 'selected' : ''; ?>>
                            <?php echo ucfirst($role_option); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" id="team-selection" style="display: none;">
                <label for="team_id">Team:</label>
                <select id="team_id" name="team_id" class="form-control">
                    <?php
                    $teams = $conn->query("SELECT id, name FROM teams");
                    while ($team = $teams->fetch_assoc()): ?>
                        <option value="<?php echo $team['id']; ?>"
                            <?php echo ($_POST['team_id'] ?? '') == $team['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($team['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Register</button>
            <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
        </form>
        
        <script>
        document.getElementById('role').addEventListener('change', function() {
            document.getElementById('team-selection').style.display = 
                (this.value === 'player') ? 'block' : 'none';
        });
        // Trigger change event on page load if returning with errors
        document.getElementById('role').dispatchEvent(new Event('change'));
        </script>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>