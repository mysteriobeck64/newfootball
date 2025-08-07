<?php
require_once 'config.php';

$error = '';
$success = '';

// Check if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Check against database
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password (for admin, check direct match first for backward compatibility)
            if (($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) || 
                password_verify($password, $user['password'])) {
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
        $stmt->close();
    }
}

require_once 'header.php';
?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h2>Club Management Login</h2>
            <p>Access your football club dashboard</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        
        <form method="post" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-with-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-with-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                    </svg>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
            
            <div class="login-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                
            </div>
        </form>
    </div>
</div>

<style>
    :root {
        --primary-color: #0066cc;
        --primary-dark: #0055aa;
        --error-color: #e74c3c;
        --light-gray: #f5f5f5;
        --medium-gray: #e0e0e0;
        --dark-gray: #333;
        --white: #ffffff;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }
    
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
        background-color: var(--light-gray);
    }
    
    .login-card {
        background: var(--white);
        border-radius: 10px;
        box-shadow: var(--shadow);
        padding: 40px;
        width: 100%;
        max-width: 450px;
        transition: var(--transition);
    }
    
    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .login-header h2 {
        color: var(--primary-color);
        margin-bottom: 10px;
        font-size: 1.8rem;
    }
    
    .login-header p {
        color: var(--dark-gray);
        opacity: 0.8;
    }
    
    .alert {
        display: flex;
        align-items: center;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    
    .alert-error {
        background-color: rgba(231, 76, 60, 0.1);
        color: var(--error-color);
    }
    
    .alert svg {
        width: 20px;
        height: 20px;
        margin-right: 10px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--dark-gray);
    }
    
    .input-with-icon {
        position: relative;
        display: flex;
        align-items: center;
    }
    
    .input-with-icon svg {
        position: absolute;
        left: 15px;
        width: 20px;
        height: 20px;
        color: var(--primary-color);
    }
    
    .input-with-icon input {
        width: 100%;
        padding: 12px 15px 12px 45px;
        border: 1px solid var(--medium-gray);
        border-radius: 5px;
        font-size: 1rem;
        transition: var(--transition);
    }
    
    .input-with-icon input:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.2);
    }
    
    .login-btn {
        width: 100%;
        padding: 14px;
        background-color: var(--primary-color);
        color: var(--white);
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        margin-top: 10px;
    }
    
    .login-btn:hover {
        background-color: var(--primary-dark);
    }
    
    .login-footer {
        margin-top: 25px;
        text-align: center;
        font-size: 0.9rem;
        color: var(--dark-gray);
    }
    
    .login-footer a {
        color: var(--primary-color);
        text-decoration: none;
        transition: var(--transition);
    }
    
    .login-footer a:hover {
        text-decoration: underline;
    }
    
    .login-footer p {
        margin: 8px 0;
    }
    
    @media (max-width: 480px) {
        .login-card {
            padding: 30px 20px;
        }
    }
</style>

<?php require_once 'footer.php'; ?>