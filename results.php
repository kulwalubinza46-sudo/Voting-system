<?php
include('config/db.php');

$sql = "
SELECT
candidates.id,
candidates.full_name,
candidates.sex,
candidates.age,
COUNT(votes.id) AS total_votes
FROM candidates
LEFT JOIN votes
ON candidates.id = votes.candidate_id
GROUP BY candidates.id
";

$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Results</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Election Results</h2>

<table border="1">
<tr>
<th>Candidate</th>
<th>Sex</th>
<th>Age</th>
<th>Votes</th>
</tr>

<?php
while($row=mysqli_fetch_assoc($result)){
?>

<tr>
<td><?php echo htmlspecialchars($row['full_name']); ?></td>
<td><?php echo htmlspecialchars($row['sex']); ?></td>
<td><?php echo htmlspecialchars($row['age']); ?></td>
<td><?php echo htmlspecialchars($row['total_votes']); ?></td>
</tr>

<?php } ?>

</table>
<script src="js/app.js"></script>
</body>
</html>
