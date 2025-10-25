<?php
session_start();
include 'config.php';

// if user not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Space Jam Rocket Game 🚀</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome for profile icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <link rel="stylesheet" href="Styles/style.css">

  <style>
    /* Profile dropdown styling */
    .profile-container {
      position: relative;
      display: inline-block;
      cursor: pointer;
    }

    .profile-dropdown {
      display: none;
      position: absolute;
      top: 120%;
      right: 0;
      background-color: #343a40;
      border: 1px solid #0dcaf0;
      border-radius: 0.5rem;
      min-width: 120px;
      z-index: 100;
    }

    .profile-dropdown a {
      display: block;
      padding: 0.5rem 1rem;
      color: #f8f9fa;
      text-decoration: none;
    }

    .profile-dropdown a:hover {
      background-color: #0dcaf0;
      color: #000;
    }

    .profile-container:hover span {
      text-decoration: underline;
    }
  </style>
</head>

<body class="bg-dark text-light d-flex flex-column align-items-center vh-100">

  <div class="d-flex w-100 justify-content-between align-items-center p-3">
    <div>
      <button id="restartBtn" class="btn btn-outline-info me-2">Restart</button>
      <!-- Leaderboard Button -->
      <button id="leaderboardBtn" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#leaderboardModal">
        <i class="fas fa-trophy me-1"></i> Leaderboard
      </button>
    </div>

    <div class="text-light fw-bold">
      Score: <span id="score">0</span> | High Score: <span id="highScore">0</span>
    </div>

    <!-- Profile Icon -->
    <div class="profile-container text-light">
      <i id="profileIcon" class="fas fa-user-circle fa-2x"></i>
      <span class="ms-2"><?= htmlspecialchars($username) ?></span>
      <div id="profileDropdown" class="profile-dropdown">
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>

  <!-- Game Title -->
  <div class="text-center mb-3">
    <h1 class="fw-bold text-info">🚀 Space Jam Rocket Game</h1>
    <p class="text-secondary">Use Arrow Keys to Dodge the Comets ☄️</p>
  </div>

  <!-- Game Container -->
  <div class="game-container border border-info rounded-4 shadow-lg p-2">
    <div id="player"></div>
    <h2 id="gameOver" class="text-danger fw-bold text-center">💥 Game Over! Press Restart</h2>
  </div>

  <div class="text-light fw-bold mt-3">
    | Level: <span id="level">1</span> |
  </div>

  <!-- Leaderboard Modal -->
  <div class="modal fade" id="leaderboardModal" tabindex="-1" aria-labelledby="leaderboardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-dark text-light border border-warning">
        <div class="modal-header">
          <h5 class="modal-title text-warning"><i class="fas fa-trophy me-2"></i>Leaderboard</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <table class="table table-dark table-hover table-bordered text-center align-middle">
            <thead class="table-warning text-dark">
              <tr>
                <th scope="col">🏅 Rank</th>
                <th scope="col">Username</th>
                <th scope="col">High Score</th>
              </tr>
            </thead>
            <tbody>
              <!-- Hardcoded leaderboard values -->
              <tr><td>1</td><td>RocketMaster</td><td>9800</td></tr>
              <tr><td>2</td><td>StarPilot</td><td>9200</td></tr>
              <tr><td>3</td><td>CometCrusher</td><td>8700</td></tr>
              <tr><td>4</td><td>AstroAce</td><td>8200</td></tr>
              <tr><td>5</td><td>GalaxyRider</td><td>7800</td></tr>
              <tr><td>6</td><td>MoonRacer</td><td>7400</td></tr>
              <tr><td>7</td><td>SkyBlazer</td><td>7100</td></tr>
              <tr><td>8</td><td>NovaKing</td><td>6900</td></tr>
              <tr><td>9</td><td>SpaceDrifter</td><td>6500</td></tr>
              <tr><td>10</td><td>CosmoChaser</td><td>6000</td></tr>
            </tbody>
          </table>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-warning" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom JS -->
  <script src="script.js"></script>

</body>
</html>
