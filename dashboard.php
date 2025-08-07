<?php
require_once 'config.php';
checkLogin();
require_once 'header.php';

$userRole = getUserRole();
$userId = $_SESSION['user_id'];

// Handle team request processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($userRole === 'admin' && isset($_POST['request_id'])) {
        // Admin processing request
        $request_id = intval($_POST['request_id']);
        $action = $_POST['action'];
        
        $stmt = $conn->prepare("SELECT player_id, team_id FROM team_requests WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $request = $result->fetch_assoc();
            $player_id = $request['player_id'];
            $team_id = $request['team_id'];
            
            if ($action == 'approve') {
                // Update request status
                $update = $conn->prepare("UPDATE team_requests SET status = ?, response_date = NOW() WHERE id = ?");
                $update->bind_param("ii", REQUEST_APPROVED, $request_id);
                $update->execute();
                
                // Assign player to team
                $assign = $conn->prepare("UPDATE players SET team_id = ? WHERE id = ?");
                $assign->bind_param("ii", $team_id, $player_id);
                $assign->execute();
                
                $_SESSION['success'] = "Request approved and player assigned to team.";
            } elseif ($action == 'reject') {
                $update = $conn->prepare("UPDATE team_requests SET status = ?, response_date = NOW() WHERE id = ?");
                $update->bind_param("ii", REQUEST_REJECTED, $request_id);
                $update->execute();
                
                $_SESSION['success'] = "Request rejected.";
            }
        } else {
            $_SESSION['error'] = "Invalid request.";
        }
    } elseif ($userRole === 'player' && isset($_POST['team_id'])) {
        // Player submitting request
        $stmt = $conn->prepare("SELECT id FROM players WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $player_id = $result->fetch_row()[0];
            $team_id = intval($_POST['team_id']);
            
            // Check if player already has a team
            $check_team = $conn->prepare("SELECT team_id FROM players WHERE id = ? AND team_id IS NOT NULL");
            $check_team->bind_param("i", $player_id);
            $check_team->execute();
            
            if ($check_team->get_result()->num_rows == 0) {
                // Check for existing pending request
                $check_request = $conn->prepare("SELECT id FROM team_requests 
                                               WHERE player_id = ? AND team_id = ? AND status = 0");
                $check_request->bind_param("ii", $player_id, $team_id);
                $check_request->execute();
                
                if ($check_request->get_result()->num_rows == 0) {
                    // Create new request
                    $insert = $conn->prepare("INSERT INTO team_requests (player_id, team_id) VALUES (?, ?)");
                    $insert->bind_param("ii", $player_id, $team_id);
                    
                    if ($insert->execute()) {
                        $_SESSION['success'] = "Team request submitted successfully!";
                    } else {
                        $_SESSION['error'] = "Error submitting request. Please try again.";
                    }
                } else {
                    $_SESSION['error'] = "You already have a pending request for this team.";
                }
            } else {
                $_SESSION['error'] = "You are already assigned to a team.";
            }
        } else {
            $_SESSION['error'] = "Player profile not found.";
        }
    }
    
    header("Location: dashboard.php");
    exit();
}
?>

<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1><?php echo ucfirst($userRole); ?> Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    </div>

    <!-- Display messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Admin Dashboard Content -->
    <?php if ($userRole === 'admin'): ?>
        <div class="admin-dashboard">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Players</h3>
                    <?php
                    $count = $conn->query("SELECT COUNT(*) FROM players")->fetch_row()[0];
                    ?>
                    <div class="stat-number"><?php echo $count; ?></div>
                    <a href="players.php" class="stat-link">View Players</a>
                </div>
                
                <div class="stat-card">
                    <h3>Total Staff</h3>
                    <?php
                    $count = $conn->query("SELECT COUNT(*) FROM staff")->fetch_row()[0];
                    ?>
                    <div class="stat-number"><?php echo $count; ?></div>
                    <a href="staff.php" class="stat-link">View Staff</a>
                </div>
                
                <div class="stat-card">
                    <h3>Upcoming Matches</h3>
                    <?php
                    $count = $conn->query("SELECT COUNT(*) FROM matches WHERE match_date > NOW()")->fetch_row()[0];
                    ?>
                    <div class="stat-number"><?php echo $count; ?></div>
                    <a href="matches.php" class="stat-link">View Matches</a>
                </div>
            </div>
            
            <!-- Team Requests Section -->
            <div class="team-requests-section">
                <h3><i class="fas fa-user-check"></i> Team Join Requests</h3>
                
                <?php
                $requests = $conn->query("SELECT r.*, p.name as player_name, t.name as team_name 
                                        FROM team_requests r
                                        JOIN players p ON r.player_id = p.id
                                        JOIN teams t ON r.team_id = t.id
                                        WHERE r.status = 0
                                        ORDER BY r.request_date DESC");
                
                if ($requests->num_rows > 0): ?>
                    <table class="admin-requests-table">
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Team</th>
                                <th>Request Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($req = $requests->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($req['player_name']); ?></td>
                                <td><?php echo htmlspecialchars($req['team_name']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($req['request_date'])); ?></td>
                                <td class="actions">
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No pending team requests.</p>
                <?php endif; ?>
            </div>
        </div>

    <!-- Player Dashboard Content -->
    <?php elseif ($userRole === 'player'): ?>
        <?php
        // Get player info
        $stmt = $conn->prepare("SELECT p.*, t.name as team_name 
                              FROM players p
                              LEFT JOIN teams t ON p.team_id = t.id
                              WHERE p.user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $player = $stmt->get_result()->fetch_assoc();
        ?>
        
        <div class="player-dashboard">
            <div class="player-info">
                <?php if ($player): ?>
                    <div class="player-card">
                        <div class="player-header">
                            <h3><?php echo htmlspecialchars($player['name']); ?></h3>
                            <?php if ($player['team_id']): ?>
                                <span class="jersey-number">#<?php echo htmlspecialchars($player['jersey_number']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="player-details">
                            <p><strong>Position:</strong> <?php echo htmlspecialchars($player['position']); ?></p>
                            <?php if ($player['team_id']): ?>
                                <p><strong>Team:</strong> <?php echo htmlspecialchars($player['team_name']); ?></p>
                            <?php else: ?>
                                <p><strong>Team:</strong> Not assigned</p>
                            <?php endif; ?>
                            <p><strong>Date of Birth:</strong> <?php echo date('F j, Y', strtotime($player['date_of_birth'])); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p>Your player profile is not complete. Please contact your coach.</p>
                <?php endif; ?>
            </div>

            <!-- Team Request Section -->
            <div class="request-team-section">
                <h3><i class="fas fa-users"></i> Team Requests</h3>
                
                <?php if ($player && !$player['team_id']): ?>
                    <form method="post" class="request-form">
                        <div class="form-group">
                            <label for="team_id">Select Team:</label>
                            <select name="team_id" id="team_id" required>
                                <option value="">-- Select Team --</option>
                                <?php
                                $teams = $conn->query("SELECT id, name FROM teams");
                                while ($team = $teams->fetch_assoc()): ?>
                                    <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Request to Join</button>
                    </form>
                <?php endif; ?>
                
                <div class="my-requests">
                    <h4>My Requests</h4>
                    <?php
                    if ($player) {
                        $requests = $conn->prepare("SELECT r.*, t.name as team_name 
                                                  FROM team_requests r
                                                  JOIN teams t ON r.team_id = t.id
                                                  WHERE r.player_id = ?
                                                  ORDER BY r.request_date DESC");
                        $requests->bind_param("i", $player['id']);
                        $requests->execute();
                        $requests_result = $requests->get_result();
                        
                        if ($requests_result->num_rows > 0): ?>
                            <table class="requests-table">
                                <thead>
                                    <tr>
                                        <th>Team</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($req = $requests_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($req['team_name']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($req['request_date'])); ?></td>
                                        <td>
                                            <?php 
                                            switch($req['status']) {
                                                case REQUEST_APPROVED: 
                                                    echo '<span class="badge badge-success">Approved</span>';
                                                    break;
                                                case REQUEST_REJECTED: 
                                                    echo '<span class="badge badge-danger">Rejected</span>';
                                                    break;
                                                default: 
                                                    echo '<span class="badge badge-warning">Pending</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No team requests submitted yet.</p>
                        <?php endif;
                    } ?>
                </div>
            </div>
        </div>

    <!-- Coach Dashboard Content -->
    <?php elseif ($userRole === 'coach'): ?>
        <div class="coach-dashboard">
            <h2><i class="fas fa-clipboard"></i> Coach Dashboard</h2>
            
            <div class="coach-teams">
                <h3>Your Teams</h3>
                <?php
                $stmt = $conn->prepare("SELECT * FROM teams WHERE coach_id = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0): ?>
                    <div class="team-list">
                        <?php while ($team = $result->fetch_assoc()): ?>
                            <div class="team-card">
                                <h4><?php echo htmlspecialchars($team['name']); ?></h4>
                                <p>Home Ground: <?php echo htmlspecialchars($team['home_ground']); ?></p>
                                <a href="team_details.php?id=<?php echo $team['id']; ?>" class="team-link">
                                    View Team Details
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>You are not assigned to any teams yet.</p>
                <?php endif; ?>
            </div>
        </div>

    <!-- Default Staff Dashboard -->
    <?php else: ?>
        <div class="staff-dashboard">
            <h2><i class="fas fa-user-tie"></i> Staff Dashboard</h2>
            <p>Welcome to the staff portal. Use the navigation menu to access different sections.</p>
        </div>
    <?php endif; ?>
</div>

<style>
    :root {
        --primary-color: #0066cc;
        --primary-light: #e6f2ff;
        --secondary-color: #ff6b00;
        --dark-color: #2c3e50;
        --light-color: #f8f9fa;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --border-radius: 8px;
    }
    
    .dashboard-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
    }
    
    .welcome-section {
        background-color: var(--primary-light);
        padding: 20px;
        border-radius: var(--border-radius);
        margin-bottom: 30px;
        border-left: 5px solid var(--primary-color);
    }
    
    .welcome-section h1 {
        color: var(--primary-color);
        margin-bottom: 5px;
    }
    
    /* Alert messages */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: var(--border-radius);
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }
    
    .stat-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        box-shadow: var(--shadow);
        text-align: center;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-card h3 {
        color: var(--dark-color);
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--primary-color);
        margin: 10px 0;
    }
    
    .stat-link {
        display: inline-block;
        margin-top: 10px;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
    }
    
    .stat-link:hover {
        text-decoration: underline;
    }
    
    /* Team Requests Section */
    .team-requests-section {
        background: white;
        padding: 20px;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        margin: 30px 0;
    }
    
    .admin-requests-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .admin-requests-table th, 
    .admin-requests-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    
    .admin-requests-table th {
        background-color: var(--primary-color);
        color: white;
    }
    
    .actions {
        white-space: nowrap;
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }
    
    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }
    
    .btn-success {
        background-color: var(--success-color);
        color: white;
    }
    
    .btn-danger {
        background-color: var(--danger-color);
        color: white;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }
    
    /* Player Dashboard Styles */
    .player-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        box-shadow: var(--shadow);
        margin: 20px 0;
    }
    
    .player-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .jersey-number {
        background-color: var(--primary-color);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .request-team-section {
        background: white;
        padding: 20px;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        margin: 30px 0;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .requests-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    
    .requests-table th, .requests-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    
    .requests-table th {
        background-color: #f5f5f5;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-success {
        background-color: #d4edda;
        color: #155724;
    }
    
    .badge-warning {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .badge-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    /* Coach Dashboard Styles */
    .team-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }
    
    .team-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        box-shadow: var(--shadow);
        border-top: 4px solid var(--primary-color);
    }
    
    .team-link {
        display: inline-block;
        margin-top: 15px;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
    }
    
    /* Font Awesome */
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
</style>

<?php require_once 'footer.php'; ?>