<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username   = "root";       
$password   = "";           
$dbname     = "spacejamdb";  

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit;
}

// ensure user is logged
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

//Reading JSON
$input = json_decode(file_get_contents("php://input"), true);
if (!$input || !isset($input['score'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$score = (int)$input['score'];
$level = $input['level'] ?? "N/A";


$stmt = $conn->prepare("
    INSERT INTO leaderboard (user_id, score, level, created_at) 
    VALUES (?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE 
        score = IF(VALUES(score) > score, VALUES(score), score),
        level = IF(VALUES(score) > score, VALUES(level), level),
        created_at = NOW()
");

// Ensure user_id is unique in DB
$stmt->bind_param("iis", $user_id, $score, $level);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Score saved']);
} else {
    echo json_encode(['success' => false, 'error' => 'DB error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>