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
if ($userId === $_SESSION['user_id']) {
    header('Location: view_voters.php');
    exit();
}

$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
header('Location: view_voters.php');
exit();
?>