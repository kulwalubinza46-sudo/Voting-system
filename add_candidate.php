<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo 'Access denied';
    exit();
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name']);
    $sex = $_POST['sex'];
    $age = intval($_POST['age']);

    if ($name === '' || $sex === '' || $age <= 0) {
        $msg = 'All fields are required';
    } elseif ($age < 18) {
        $msg = 'Contestant must be at least 18 years old';
    } elseif (!preg_match('/^[A-Za-z ]+$/', $name)) {
        $msg = 'Name should contain only letters and spaces';
    } else {
        $name = ucwords(strtolower($name));
        $check = mysqli_prepare($conn, "SELECT id FROM candidates WHERE full_name = ?");
        mysqli_stmt_bind_param($check, 's', $name);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $msg = 'Contestant already exists';
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO candidates (full_name, sex, age) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssi', $name, $sex, $age);
            if (mysqli_stmt_execute($stmt)) {
                $msg = 'Candidate added';
            } else {
                $msg = 'Insert failed';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Candidate</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Add Contestant</h2>
    <?php if ($msg): ?>
        <p><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>
    <form class="validation-form" method="POST">
        <input type="text" name="full_name" placeholder="Full name" required>
        <select name="sex" required>
            <option value="">Select sex</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>
        <input type="number" name="age" placeholder="Age" required>
        <button type="submit">Add Contestant</button>
    </form>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</div>
<script src="js/app.js"></script>
</body>
</html>
