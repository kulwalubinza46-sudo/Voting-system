<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo 'Access denied';
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($username === '' || $password === '' || $role === '') {
        $error = 'All fields are required';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } elseif (!preg_match('/^[A-Za-z ]+$/', $username)) {
        $error = 'Username should contain only letters and spaces';
    } else {
        $username = ucwords(strtolower($username));
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = 'Username already exists';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insert, 'sss', $username, $hash, $role);
            if (mysqli_stmt_execute($insert)) {
                header('Location: view_voters.php');
                exit();
            } else {
                $error = 'Failed to create user';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Add User</h2>
    <?php if ($error): ?>
        <p class="form-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form class="validation-form" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="">Select role</option>
            <option value="voter">Voter</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Create User</button>
    </form>
    <p><a href="view_voters.php">Back to User List</a></p>
</div>
<script src="js/app.js"></script>
</body>
</html>
