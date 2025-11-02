<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spacejamdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT u.username, l.score
        FROM leaderboard l
        JOIN users u ON l.user_id = u.id
        ORDER BY l.score DESC
        LIMIT 10";

$result = $conn->query($sql);
$leaderboard = [];
$rank = 1;

while ($row = $result->fetch_assoc()) {
  $leaderboard[] = [
    'rank' => $rank++,
    'username' => $row['username'],
    'score' => $row['score']
  ];
}

header('Content-Type: application/json');
echo json_encode($leaderboard);

$conn->close();
?>
