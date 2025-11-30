<?php
session_start();
include 'config.php';

// Redirect to login if not logged in
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

<!-- Custom CSS -->
<link rel="stylesheet" href="Styles/style.css">

<style>
/* --- Profile --- */
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
.profile-container:hover { color: #ffc107; text-shadow: 0 0 10px rgba(255,193,7,0.7); }
.profile-container i { font-size: 2rem; transition: transform 0.3s ease, color 0.3s ease; }
.profile-container:hover i { transform: scale(1.1); color: #ffc107; }
.profile-container span { font-size: 1.1rem; letter-spacing: 0.5px; }
.profile-dropdown {
  display: none;
  position: absolute;
  top: 120%;
  right: 0;
  background-color: rgba(0,20,40,0.95);
  border: 1px solid #0dcaf0;
  border-radius: 0.5rem;
  min-width: 140px;
  box-shadow: 0 0 15px rgba(13,202,240,0.5);
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
.profile-dropdown a:hover { background-color: #0dcaf0; color: #000; font-weight: 600; }

/* --- Game Options Sidebar --- */
.options-container {
  position: absolute;
  top: 50%;
  left: 200px;
  transform: translateY(-50%);
  background-color: rgba(13,13,13,0.9);
  border: 2px solid #0dcaf0;
  border-radius: 1rem;
  padding: 1.5rem;
  width: 230px;
  height: 380px;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  box-shadow: 0 0 20px rgba(13,202,240,0.4);

#apodPanel {
  width: 300px;
  height: 300px;
}
#apodPanel img {
  width: 100%;
  height: 120px; /* Image takes top part */
  object-fit: cover;
}
#apodDesc {
  font-size: 0.8rem;
  line-height: 1rem;
}
#apodPanel {
  width: 300px;
  height: 300px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
#apodPanel img {
  width: 100%;
  height: 120px;
  object-fit: cover;
}
#apodDesc {
  font-size: 0.8rem;
  line-height: 1rem;
  flex-grow: 1;
}
#apodLearnMore {
  margin-top: auto;
}


}
.options-container h5 { font-weight: 600; }
.options-container button { width: 100%; padding: 10px; font-weight: 600; }
.btn-group .btn.active { background-color: var(--bs-success); color: #fff; }
</style>
</head>
<canvas id="gameBackground"></canvas>

<style>
  #gameBackground {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: -1; /* behind your game */
    background: black;
  }
</style>

<script>
const canvas = document.getElementById('gameBackground');
const ctx = canvas.getContext('2d');

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

const stars = [];
for(let i = 0; i < 200; i++){
  stars.push({
    x: Math.random() * canvas.width,
    y: Math.random() * canvas.height,
    size: Math.random() * 2,
    speed: Math.random() * 0.5 + 0.2
  });
}

function animateStars(){
  ctx.fillStyle = 'black';
  ctx.fillRect(0, 0, canvas.width, canvas.height);
  
  ctx.fillStyle = 'white';
  stars.forEach(star => {
    ctx.beginPath();
    ctx.arc(star.x, star.y, star.size, 0, Math.PI*2);
    ctx.fill();
    
    star.y += star.speed;
    if(star.y > canvas.height) star.y = 0;
  });
  
  requestAnimationFrame(animateStars);
}

animateStars();
</script>

<body class="bg-dark text-light d-flex flex-column align-items-center vh-100">

<!-- Header -->
<div class="d-flex w-100 justify-content-end align-items-center p-3">
  <div class="profile-container" id="profileToggle">
    <i class="fas fa-user-circle"></i>
    <span><?= htmlspecialchars($username) ?></span>
    <div class="profile-dropdown" id="profileDropdown">
      <a href="logout.php">Logout</a>
    </div>
  </div>
</div>

<!-- Title -->
<div class="text-center mb-3">
  <h1 class="fw-bold text-info">Space Jam Rocket Game</h1>
  <p class="text-secondary">Use Arrow Keys to Dodge the Comets </p>
</div>

<!-- Game Container -->
<div class="game-container border border-info rounded-4 shadow-lg p-2">
  <div id="player"></div>
  <h2 id="gameOver" style="display:none;"></h2>
</div>

<div class="text-light fw-bold mt-3">| Level: <span id="level">1</span> |</div>
<div class="text-light fw-bold">Score: <span id="score">0</span> | High Score: <span id="highScore">0</span></div>

<!-- Options Sidebar -->
<div class="options-container">
  <h5 class="text-info mb-3"><i class="fas fa-sliders-h me-2"></i>Game Options</h5>

  <!-- Music -->
  <div class="mb-3">
    <h6 class="text-warning mb-2"><i class="fas fa-music me-2"></i>Music</h6>
    <div class="btn-group w-100" role="group" aria-label="Music Controls">
      <button id="musicOn" class="btn btn-outline-success active">On</button>
      <button id="musicOff" class="btn btn-outline-danger">Off</button>
    </div>
  </div>

  <button id="restartBtn" class="btn btn-outline-info"><i class="fas fa-rotate-right me-2"></i>Restart</button>
  <button id="leaderboardBtn" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#leaderboardModal">
    <i class="fas fa-trophy me-2"></i>Leaderboard
  </button>
  <button id="instructionsBtn" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#instructionsModal">
    <i class="fas fa-info-circle me-2"></i>Instructions
  </button>
</div>

<!-- APOD Square Panel -->
<div id="apodPanel" class="position-absolute top-50 end-0 translate-middle-y bg-dark border border-info rounded-4 shadow-lg p-3 me-3 d-flex flex-column align-items-center justify-content-start" style="width: 300px; height: 300px; overflow:hidden;">
  <h6 class="text-info fw-bold mb-1 text-center"><i class="fas fa-star me-1"></i>Daily NASA Space Fact</h6>
  <img id="apodImg" src="" alt="APOD Image" class="img-fluid rounded mb-2" style="width: 100%; height: 120px; object-fit: cover;" />
  <h6 id="apodTitle" class="text-warning fw-bold small text-center mb-1"></h6>
  <p id="apodDesc" class="text-light small text-center" style="overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical;"></p>
  <button id="apodLearnMore" class="btn btn-outline-info btn-sm mt-auto w-100">Learn More</button>
</div>



<!-- APOD  Modal -->
<div class="modal fade" id="apodModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark text-light border border-info shadow-lg">
      <div class="modal-header border-info">
        <h5 class="modal-title text-info fw-bold" id="modalApodTitle"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalApodImg" src="" alt="APOD Full Image" class="img-fluid rounded mb-3" />
        <p id="modalApodDesc" class="text-light"></p>
      </div>
      <div class="modal-footer border-info">
        <button type="button" class="btn btn-outline-info" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- NEO Slider -->
<div class="neo-wrapper position-absolute top-50 end-0 translate-middle-y me-3 mt-5">
  <h6 class="text-info fw-bold mb-2 text-center">
    <i class="fas fa-meteor me-1"></i>Near Earth Objects
  </h6>

  <div class="neo-slider d-flex align-items-center">

    <!-- Left Arrow -->
    <button id="neoPrev" class="btn btn-outline-info btn-sm me-2">
      <i class="fas fa-chevron-left"></i>
    </button>

    <!-- Card Container -->
    <div id="neoCard" class="neo-card bg-dark border border-info rounded-4 shadow-lg p-3"
         style="width: 300px; height: 260px; overflow:hidden;">
      <h5 class="text-warning small text-center mb-2">Loading...</h5>
    </div>

    <!-- Right Arrow -->
    <button id="neoNext" class="btn btn-outline-info btn-sm ms-2">
      <i class="fas fa-chevron-right"></i>
    </button>

  </div>
</div>






<!-- Instructions Modal -->
<div class="modal fade" id="instructionsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light border border-info shadow-lg">
      <div class="modal-header border-info">
        <h5 class="modal-title text-info fw-bold"><i class="fas fa-info-circle me-2"></i>Game Instructions</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
          <li>⚡ Every 100 points, comet speed increases.</li>
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
<div class="modal fade" id="leaderboardModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark text-light border border-warning">
      <div class="modal-header">
        <h5 class="modal-title text-warning"><i class="fas fa-trophy me-2"></i>Leaderboard</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
          <tbody id="leaderboard-body"><tr><td colspan="3">Loading...</td></tr></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-warning" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Game Over Quiz Modal -->
<div class="modal fade" id="gameOverModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light border border-danger shadow-lg">
      <div class="modal-header border-danger">
        <h5 class="modal-title text-danger fw-bold">Game Over!</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <div id="quizQuestionArea" class="mb-4">
          <p class="text-warning">Loading math problem...</p>
        </div>
        <input type="number" id="answerField" 
               class="form-control text-center mb-3 fs-5 py-3 bg-dark border-warning text-light"
               placeholder="Enter answer..." inputmode="numeric" autocomplete="off" />
        <button id="tryAgainBtn" class="btn btn-outline-danger fw-bold w-100 py-3" disabled>
          Submit Answer
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Audio -->
<audio id="bgMusic" loop>
  <source src="Audio/bgmusic.mp3" type="audio/mpeg">
</audio>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="script.js"></script>

<script>
// Profile Dropdown Toggle
const profileToggle = document.getElementById("profileToggle");
const profileDropdown = document.getElementById("profileDropdown");
profileToggle.addEventListener("click", () => {
  profileDropdown.style.display = profileDropdown.style.display === "block" ? "none" : "block";
});
document.addEventListener("click", (e) => {
  if (!profileToggle.contains(e.target)) profileDropdown.style.display = "none";
});

// Leaderboard
function fetchLeaderboard() {
  fetch("fetch_leaderboard.php")
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById("leaderboard-body");
      tbody.innerHTML = "";
      if (!data.length) tbody.innerHTML = "<tr><td colspan='3'>No scores yet!</td></tr>";
      else data.forEach(p => {
        tbody.innerHTML += `<tr><td>${p.rank}</td><td>${p.username}</td><td>${p.score}</td></tr>`;
      });
    })
    .catch(err => {
      console.error(err);
      document.getElementById("leaderboard-body").innerHTML =
        "<tr><td colspan='3'>Error loading leaderboard</td></tr>";
    });
}
document.addEventListener("DOMContentLoaded", fetchLeaderboard);

// Music Toggle
const musicOn = document.getElementById("musicOn");
const musicOff = document.getElementById("musicOff");
const bgMusic = document.getElementById("bgMusic");
let musicEnabled = true;

musicOn.addEventListener("click", () => {
  musicEnabled = true;
  musicOn.classList.add("active");
  musicOff.classList.remove("active");
  bgMusic.play();
});
musicOff.addEventListener("click", () => {
  musicEnabled = false;
  musicOff.classList.add("active");
  musicOn.classList.remove("active");
  bgMusic.pause();
});

// Game Over Quiz Submit
document.getElementById("tryAgainBtn").addEventListener("click", () => {
  const ans = document.getElementById("answerField").value.trim();
  if (!ans) { alert("Please enter an answer!"); return; }
  console.log("Answer submitted:", ans);
  window.location.reload();
});

// NASA APOD Fetch
const apodApiKey = "aGg4WRClHhmQhaYcVy1lcCy6JAoxWFDau6N18ZFO"; 
const apodUrl = `https://api.nasa.gov/planetary/apod?api_key=${apodApiKey}`;

let apodData = {};

fetch(apodUrl)
  .then(res => res.json())
  .then(data => {
    apodData = data; // store full data for modal
    // Update square panel
    document.getElementById("apodImg").src = data.url;
    document.getElementById("apodImg").alt = data.title;
    document.getElementById("apodTitle").innerText = data.title;
    document.getElementById("apodDesc").innerText = data.explanation;
  })
  .catch(err => {
    console.error("Error fetching APOD:", err);
    document.getElementById("apodTitle").innerText = "Could not load APOD.";
    document.getElementById("apodDesc").innerText = "";
  });

// Learn More Button — open modal
const learnMoreBtn = document.getElementById("apodLearnMore");
learnMoreBtn.addEventListener("click", () => {
  document.getElementById("modalApodTitle").innerText = apodData.title;
  document.getElementById("modalApodImg").src = apodData.url;
  document.getElementById("modalApodImg").alt = apodData.title;
  document.getElementById("modalApodDesc").innerText = apodData.explanation;
  
  // Show Bootstrap modal
  const apodModal = new bootstrap.Modal(document.getElementById('apodModal'));
  apodModal.show();
});


// NEO
const neoApiKey = "aGg4WRClHhmQhaYcVy1lcCy6JAoxWFDau6N18ZFO"; 
const neoUrl = `https://api.nasa.gov/neo/rest/v1/feed?api_key=${neoApiKey}`;

let neoList = [];
let neoIndex = 0;

function loadNeoCard(index, direction = "right") {
  const card = document.getElementById("neoCard");

  // Animate out
  card.classList.remove("show");
  card.classList.add(direction === "right" ? "hide-right" : "hide-left");

  setTimeout(() => {
    const neo = neoList[index];

    card.innerHTML = `
      <h6 class="text-warning fw-bold text-center">${neo.name}</h6>
      <p class="text-light small">
        <b>⚡ Speed:</b> ${neo.speed.toLocaleString()} km/h <br>
        <b>📏 Diameter:</b> ${neo.diameter} m <br>
        <b>🛑 Hazardous:</b> 
        <span class="${neo.hazard ? "text-danger" : "text-success"} fw-bold">
          ${neo.hazard ? "YES" : "NO"}
        </span> <br>
        <b>📅 Closest Approach:</b><br>${neo.date}
      </p>
    `;

    // Reset animation classes
    card.classList.remove("hide-right", "hide-left");

    setTimeout(() => card.classList.add("show"), 10);
  }, 300);
}

fetch(neoUrl)
  .then(res => res.json())
  .then(data => {
    const nearEarthObjects = data.near_earth_objects;
    const today = Object.keys(nearEarthObjects)[0];

    neoList = nearEarthObjects[today].map(obj => ({
      name: obj.name,
      speed: parseFloat(obj.close_approach_data[0].relative_velocity.kilometers_per_hour),
      diameter: Math.floor(obj.estimated_diameter.meters.estimated_diameter_max),
      hazard: obj.is_potentially_hazardous_asteroid,
      date: obj.close_approach_data[0].close_approach_date_full
    }));

    // Load first card
    loadNeoCard(neoIndex);
  })
  .catch(err => {
    document.getElementById("neoCard").innerHTML =
      `<p class="text-danger text-center small">Failed to load NEO data.</p>`;
  });


// Slider Arrows
document.getElementById("neoNext").addEventListener("click", () => {
  neoIndex = (neoIndex + 1) % neoList.length;
  loadNeoCard(neoIndex, "right");
});

document.getElementById("neoPrev").addEventListener("click", () => {
  neoIndex = (neoIndex - 1 + neoList.length) % neoList.length;
  loadNeoCard(neoIndex, "left");
});


</script>

</body>
</html>
