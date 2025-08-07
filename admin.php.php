<?php
require_once 'config.php';
checkLogin();
if ($_SESSION['user_role'] != 'admin') {
    header("Location: unauthorized.php");
    exit();
}
require_once 'header.php';
?>

<div class="container">
    <h2>Admin Dashboard</h2>
    
    <div class="team-requests">
        <h3>Pending Team Requests</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Player</th>
                    <th>Team</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $requests = $conn->query("SELECT r.*, p.name as player_name, t.name as team_name 
                                         FROM team_requests r
                                         JOIN players p ON r.player_id = p.id
                                         JOIN teams t ON r.team_id = t.id
                                         WHERE r.status = 'pending'");
                while($req = $requests->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($req['player_name']); ?></td>
                    <td><?php echo htmlspecialchars($req['team_name']); ?></td>
                    <td><?php echo date('M j, Y', strtotime($req['request_date'])); ?></td>
                    <td>
                        <a href="process_request.php?action=approve&id=<?php echo $req['id']; ?>" 
                           class="btn btn-success btn-sm">Approve</a>
                        <a href="process_request.php?action=reject&id=<?php echo $req['id']; ?>" 
                           class="btn btn-danger btn-sm">Reject</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>