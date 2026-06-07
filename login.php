<!DOCTYPE html>
<html>
<head>
    <title>Voting System Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h2>Voting System Login</h2>

<form class="validation-form" action="authentication.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
 </div>
 <script src="js/app.js"></script>

</body>
</html>
