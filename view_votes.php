<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo 'Access denied';
    exit();
}

$sql = "SELECT votes.id AS vote_id, users.username AS voter, candidates.full_name AS candidate, votes.created_at
        FROM votes
        LEFT JOIN users ON votes.voter_id = users.id
        LEFT JOIN candidates ON votes.candidate_id = candidates.id
        ORDER BY votes.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Votes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Vote Records</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Vote ID</th>
            <th>Voter</th>
            <th>Candidate</th>
            <th>Time</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['vote_id']); ?></td>
            <td><?php echo htmlspecialchars($row['voter']); ?></td>
            <td><?php echo htmlspecialchars($row['candidate']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</div>
<script src="js/app.js"></script>
</body>
</html>
