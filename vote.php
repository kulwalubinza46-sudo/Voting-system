<?php
session_start();
include('config/db.php');

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

if(isset($_POST['vote'])){
    $candidate_id = intval($_POST['candidate_id']);

    $check = mysqli_prepare($conn, "SELECT id FROM votes WHERE voter_id = ?");
    mysqli_stmt_bind_param($check, 'i', $user_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        echo "<script>alert('You have already voted');</script>";
    } else {
        $ins = mysqli_prepare($conn, "INSERT INTO votes (voter_id, candidate_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($ins, 'ii', $user_id, $candidate_id);
        if (mysqli_stmt_execute($ins)) {
            echo "<script>alert('Vote Submitted Successfully');</script>";
        } else {
            echo "<script>alert('Vote failed');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Vote</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Select Candidate</h2>

<?php

$candidates =
mysqli_query($conn,"SELECT * FROM candidates");

while($row=mysqli_fetch_assoc($candidates)){
?>

<form class="vote-form" data-candidate-name="<?php echo htmlspecialchars($row['full_name']); ?>" method="POST">

<h3><?php echo htmlspecialchars($row['full_name']); ?></h3>

<p>Sex: <?php echo htmlspecialchars($row['sex']); ?> — Age: <?php echo htmlspecialchars($row['age']); ?></p>

<input type="hidden" name="candidate_id" value="<?php echo $row['id']; ?>">

<button name="vote" type="submit">Vote</button>

</form>

<hr>

<?php } ?>
<script src="js/app.js"></script>
</body>
</html>
