<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>voting Dashboard</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="dashboard">
    <h1>Voting Dashboard</h1>
    <?php if (!empty($_SESSION['username'])): ?>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    <?php endif; ?>

    <nav class="dashboard-nav">
        <a class="nav-link" href="vote.php">Vote Now</a>
        <a class="nav-link" href="results.php">View Results</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a class="nav-link" href="add_candidate.php">Add Contestant</a>
            <a class="nav-link" href="view_candidates.php">View Contestants</a>
            <a class="nav-link" href="view_voters.php">Manage Users</a>
            <a class="nav-link" href="view_votes.php">View Votes</a>
        <?php endif; ?>
        <a class="nav-link logout" href="logout.php">Logout</a>
    </nav>
</div>
</body>
</html>
