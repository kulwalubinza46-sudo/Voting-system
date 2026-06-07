<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "voting_system";

// Connect to MySQL server (no database selected yet)
$conn = mysqli_connect($servername, $username, $password);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
if (!mysqli_select_db($conn, $database)) {
    $createDbSql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (!mysqli_query($conn, $createDbSql)) {
        die("Database creation failed: " . mysqli_error($conn));
    }
    mysqli_select_db($conn, $database);
}

// Create required tables if they don't exist
$usersSql = "CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) DEFAULT 'voter'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $usersSql);

$candidatesSql = "CREATE TABLE IF NOT EXISTS candidates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(255),
  sex VARCHAR(20),
  age INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn,$candidatesSql);

// Add missing columns to candidates table if it already existed
$checkSex = mysqli_query($conn, "SHOW COLUMNS FROM candidates LIKE 'sex'");
if (mysqli_num_rows($checkSex) === 0) {
    mysqli_query($conn, "ALTER TABLE candidates ADD COLUMN sex VARCHAR(20)");
}
$checkAge = mysqli_query($conn, "SHOW COLUMNS FROM candidates LIKE 'age'");
if (mysqli_num_rows($checkAge) === 0) {
    mysqli_query($conn, "ALTER TABLE candidates ADD COLUMN age INT");
}

// Ensure candidate names are unique
$indexCandidates = mysqli_query($conn, "SHOW INDEX FROM candidates WHERE Column_name = 'full_name' AND Non_unique = 0");
if (mysqli_num_rows($indexCandidates) === 0) {
    mysqli_query($conn, "DELETE c1 FROM candidates c1 INNER JOIN candidates c2 ON c1.full_name = c2.full_name AND c1.id > c2.id");
    mysqli_query($conn, "ALTER TABLE candidates ADD UNIQUE INDEX unique_candidate_name (full_name)");
}

$votesSql = "CREATE TABLE IF NOT EXISTS votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  voter_id INT,
  candidate_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (voter_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $votesSql);

$voteIndex = mysqli_query($conn, "SHOW INDEX FROM votes WHERE Column_name = 'voter_id' AND Non_unique = 0");
if (mysqli_num_rows($voteIndex) === 0) {
    // Remove duplicate votes for the same voter before adding the unique constraint
    mysqli_query($conn, "DELETE v1 FROM votes v1 INNER JOIN votes v2 ON v1.voter_id = v2.voter_id AND v1.id > v2.id");
    mysqli_query($conn, "ALTER TABLE votes ADD UNIQUE INDEX unique_voter_vote (voter_id)");
}

// Insert a default admin if users table is empty
$res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users");
if ($res) {
    $row = mysqli_fetch_assoc($res);
    if (isset($row['cnt']) && $row['cnt'] == 0) {
        $adminUsername = 'admin';
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $role = 'admin';
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username,password,role) VALUES (?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sss', $adminUsername, $adminPass, $role);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

?>

