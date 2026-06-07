<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo 'Access denied';
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM candidates ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Contestants</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Contestants</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Sex</th>
            <th>Age</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['sex']); ?></td>
            <td><?php echo htmlspecialchars($row['age']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</div>
<script src="js/app.js"></script>
</body>
</html>
