<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo 'Access denied';
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: view_voters.php');
    exit();
}

$userId = intval($_GET['id']);
$error = '';

$stmt = mysqli_prepare($conn, "SELECT username, role FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $username, $role);
if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: view_voters.php');
    exit();
}
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username']);
    $newPassword = $_POST['password'];
    $newRole = $_POST['role'];

    if ($newUsername === '' || $newRole === '') {
        $error = 'Username and role are required';
    } elseif (!preg_match('/^[A-Za-z ]+$/', $newUsername)) {
        $error = 'Username should contain only letters and spaces';
    } elseif ($newPassword !== '' && strlen($newPassword) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        $newUsername = ucwords(strtolower($newUsername));
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($check, 'si', $newUsername, $userId);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'Username already taken';
        } else {
            if ($newPassword !== '') {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $update = mysqli_prepare($conn, "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
                mysqli_stmt_bind_param($update, 'sssi', $newUsername, $hash, $newRole, $userId);
            } else {
                $update = mysqli_prepare($conn, "UPDATE users SET username = ?, role = ? WHERE id = ?");
                mysqli_stmt_bind_param($update, 'ssi', $newUsername, $newRole, $userId);
            }

            if (mysqli_stmt_execute($update)) {
                header('Location: view_voters.php');
                exit();
            } else {
                $error = 'Update failed';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Edit User</h2>
    <?php if ($error): ?>
        <p class="form-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form class="validation-form" method="POST">
        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Username" required>
        <input type="password" name="password" placeholder="New password (leave blank to keep current)">
        <select name="role" required>
            <option value="voter" <?php echo $role === 'voter' ? 'selected' : ''; ?>>Voter</option>
            <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
        <button type="submit">Update User</button>
    </form>
    <p><a href="view_voters.php">Back to User List</a></p>
</div>
<script src="js/app.js"></script>
</body>
</html>
