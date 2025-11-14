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

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <link rel="stylesheet" href="Styles/style.css">

  <style>
   
.profile-container {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.6rem;
  color: #0dcaf0;
  font-weight: 600;
  text-shadow: 0 0 8px rgba(13, 202, 240, 0.6);
  cursor: pointer;
  transition: all 0.3s ease;
}

.profile-container:hover {
  color: #ffc107; 
  text-shadow: 0 0 10px rgba(255, 193, 7, 0.7);
}

.profile-container i {
  font-size: 2rem;
  transition: transform 0.3s ease, color 0.3s ease;
}

.profile-container:hover i {
  transform: scale(1.1);
  color: #ffc107;
}

.profile-container span {
  font-size: 1.1rem;
  letter-spacing: 0.5px;
}


.profile-dropdown {
  display: none;
  position: absolute;
  top: 120%;
  right: 0;
  background-color: rgba(0, 20, 40, 0.95);
  border: 1px solid #0dcaf0;
  border-radius: 0.5rem;
  min-width: 140px;
  box-shadow: 0 0 15px rgba(13, 202, 240, 0.5);
  z-index: 100;
}

.profile-dropdown a {
  display: block;
  padding: 0.6rem 1rem;
  color: #0dcaf0;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.3s ease;
}

.profile-dropdown a:hover {
  background-color: #0dcaf0;
  color: #000;
  font-weight: 600;
}


 
    /*  Game Options Sidebar */
.options-container {
  position: absolute;
  top: 50%;
  left: 200px;
  transform: translateY(-50%);
  background-color: rgba(13, 13, 13, 0.9);
  border: 2px solid #0dcaf0;
  border-radius: 1rem;
  padding: 1.5rem;
  width: 230px; 
  height: 380px; 
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: stretch;
  gap: 1rem;
  box-shadow: 0 0 20px rgba(13, 202, 240, 0.4);
}

    .options-container h5 {
      font-weight: 600;
    }

    .btn-group .btn.active {
      background-color: var(--bs-success);
      color: #fff;
    }

.options-container button {
  width: 100%;
  padding: 10px;
  font-weight: 600;
}
  </style>
</head>

<body class="bg-dark text-light d-flex flex-column align-items-center vh-100">

  <!-- Header with profile -->
  <div class="d-flex w-100 justify-content-end align-items-center p-3">
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

  <div class="text-light fw-bold">
    Score: <span id="score">0</span> | High Score: <span id="highScore">0</span>
  </div>

  <!-- 🎛 Fixed Left-Side Game Options -->

<div class="options-container">
  <h5 class="text-info mb-3"><i class="fas fa-sliders-h me-2"></i>Game Options</h5>

  <!-- Sounds -->
 
<div class="mb-3">
  <h6 class="text-warning mb-2"><i class="fas fa-music me-2"></i>Music</h6>
  <div class="btn-group w-100" role="group" aria-label="Music Controls">
    <button id="musicOn" class="btn btn-outline-success active">On</button>
    <button id="musicOff" class="btn btn-outline-danger">Off</button>
  </div>
</div>


  <!-- Restart Button -->
  <button id="restartBtn" class="btn btn-outline-info">
    <i class="fas fa-rotate-right me-2"></i>Restart
  </button>

  <!-- Leaderboard Button -->
  <button id="leaderboardBtn" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#leaderboardModal">
    <i class="fas fa-trophy me-2"></i>Leaderboard
  </button>

  <!-- 🆕 Game Instructions Button -->
  <button id="instructionsBtn" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#instructionsModal">
    <i class="fas fa-info-circle me-2"></i>Instructions
  </button>
</div>

<!-- 🆕 Game Instructions Modal -->
<div class="modal fade" id="instructionsModal" tabindex="-1" aria-labelledby="instructionsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light border border-info shadow-lg">
      <div class="modal-header border-info">
        <h5 class="modal-title text-info fw-bold" id="instructionsModalLabel">
          <i class="fas fa-info-circle me-2"></i>Game Instructions
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <h6 class="text-warning fw-bold mb-2"><i class="fas fa-gamepad me-2"></i>Controls</h6>
        <ul>
          <li>⬅️ <b>Left Arrow</b> — Move Left</li>
          <li>➡️ <b>Right Arrow</b> — Move Right</li>
          <li>⬆️ <b>Up Arrow</b> — Move Up</li>
          <li>⬇️ <b>Down Arrow</b> — Move Down</li>
        </ul>

        <h6 class="text-warning fw-bold mb-2"><i class="fas fa-bullseye me-2"></i>Objective</h6>
        <p>Avoid the falling comets for as long as possible. The longer you survive, the higher your score!</p>

        <h6 class="text-warning fw-bold mb-2"><i class="fas fa-star me-2"></i>Scoring</h6>
        <ul>
          <li>🏆 Gain points every second you survive.</li>
          <li>⚡ Every 100 points, comet speed increases, making the game more challenging.</li>
          <li>🔥 Try to beat your high score!</li>
        </ul>
      </div>

      <div class="modal-footer border-info">
        <button type="button" class="btn btn-outline-info" data-bs-dismiss="modal">Got it!</button>
      </div>
    </div>
  </div>
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
            <tbody id="leaderboard-body">
              <tr><td colspan="3">Loading...</td></tr>
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

  <script>
  // 🏆 Fetch leaderboard
  function fetchLeaderboard() {
    fetch("fetch_leaderboard.php")
      .then(res => res.json())
      .then(data => {
        const tbody = document.getElementById("leaderboard-body");
        tbody.innerHTML = "";
        if (!data.length) {
          tbody.innerHTML = "<tr><td colspan='3'>No scores yet!</td></tr>";
          return;
        }
        data.forEach(player => {
          tbody.innerHTML += `
            <tr>
              <td>${player.rank}</td>
              <td>${player.username}</td>
              <td>${player.score}</td>
            </tr>`;
        });
      })
      .catch(err => {
        console.error(err);
        document.getElementById("leaderboard-body").innerHTML =
          "<tr><td colspan='3'>Error loading leaderboard</td></tr>";
      });
  }

  document.addEventListener("DOMContentLoaded", fetchLeaderboard);

  // 🔊 Sound Toggle
  const soundOn = document.getElementById("soundOn");
  const soundOff = document.getElementById("soundOff");
  let soundEnabled = true;

  soundOn.addEventListener("click", () => {
    soundEnabled = true;
    soundOn.classList.add("active");
    soundOff.classList.remove("active");
    console.log("Sound turned ON");
  });

  soundOff.addEventListener("click", () => {
    soundEnabled = false;
    soundOff.classList.add("active");
    soundOn.classList.remove("active");
    console.log("Sound turned OFF");
  });
  </script>

  <!--Music -->

<audio id="bgMusic" loop>
  <source src="Audio/bgmusic.mp3" type="audio/mpeg">
  Your browser does not support the audio element.
</audio>



</body>
</html>
