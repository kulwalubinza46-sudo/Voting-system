<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo 'Access denied';
    exit();
}

$result = mysqli_query($conn, "SELECT id, username, role FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Voters</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Manage Users</h2>
    <p><a class="nav-link" href="add_user.php">Add User</a></p>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['role']); ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
                <?php if ($row['id'] != $_SESSION['user_id']): ?>
                    | <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this user?');">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</div>
<script src="js/app.js"></script>
</body>
</html>
