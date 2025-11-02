<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "spacejamdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  echo "Not logged in";
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$score = intval($data['score']);
$level = $data['level'] ?? "N/A";

$user_id = $_SESSION['user_id'];

// Check if user already has a record
$sql = "SELECT score FROM leaderboard WHERE user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $existing = $result->fetch_assoc();
  if ($score > $existing['score']) {
    $conn->query("UPDATE leaderboard SET score = $score, level = '$level', created_at = NOW() WHERE user_id = $user_id");
  }
} else {
  $conn->query("INSERT INTO leaderboard (user_id, score, level) VALUES ($user_id, $score, '$level')");
}

echo "Score saved successfully";
$conn->close();
?>
