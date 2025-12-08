
// ===============================
// BACKGROUND STARFIELD CANVAS + SHOOTING-CURSOR EFFECT
// ===============================
const canvas = document.getElementById('gameBackground');
const ctx = canvas.getContext('2d');

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;
canvas.style.cursor = 'none'; // hide default cursor over the canvas

window.addEventListener('resize', () => {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
});

// starfield
const stars = [];
for (let i = 0; i < 200; i++) {
  stars.push({
    x: Math.random() * canvas.width,
    y: Math.random() * canvas.height,
    size: Math.random() * 2,
    speed: Math.random() * 0.5 + 0.2
  });
}

// cursor trail particles
const particles = [];
let mouseX = canvas.width / 2;
let mouseY = canvas.height / 2;
let prevMouseX = mouseX;
let prevMouseY = mouseY;
let cursorVisible = true;

document.addEventListener('mousemove', (e) => {
  // track mouse relative to viewport (canvas is full-screen)
  prevMouseX = mouseX;
  prevMouseY = mouseY;
  mouseX = e.clientX;
  mouseY = e.clientY;

  // spawn particles when moving
  const dx = mouseX - prevMouseX;
  const dy = mouseY - prevMouseY;
  const speed = Math.hypot(dx, dy);

  const spawn = Math.min(6, Math.max(1, Math.floor(speed / 3)));
  for (let i = 0; i < spawn; i++) {
    particles.push({
      x: mouseX - dx * 0.3 + (Math.random() - 0.5) * 8,
      y: mouseY - dy * 0.3 + (Math.random() - 0.5) * 8,
      vx: -dx * 0.12 + (Math.random() - 0.5) * 1.4,
      vy: -dy * 0.12 + (Math.random() - 0.5) * 1.4,
      size: Math.random() * 2 + 0.6,
      life: Math.floor(18 + Math.random() * 22),
      maxLife: Math.floor(18 + Math.random() * 22),
      hue: 50 + Math.random() * 40 // warm color range
    });
  }
  cursorVisible = true;
});

canvas.addEventListener('mouseleave', () => { cursorVisible = false; });
canvas.addEventListener('mouseenter', () => { cursorVisible = true; });

// main animation (stars + particles + cursor head)
function animateStars() {
  // clear background (keep as black)
  ctx.globalCompositeOperation = 'source-over';
  ctx.fillStyle = 'black';
  ctx.fillRect(0, 0, canvas.width, canvas.height);

  // draw stars
  ctx.fillStyle = 'white';
  stars.forEach(star => {
    ctx.beginPath();
    ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2);
    ctx.fill();
    star.y += star.speed;
    if (star.y > canvas.height) {
      star.y = 0;
      star.x = Math.random() * canvas.width;
    }
  });

  // draw particles (trail) with additive blending for glow
  ctx.globalCompositeOperation = 'lighter';
  for (let i = particles.length - 1; i >= 0; i--) {
    const p = particles[i];
    const alpha = Math.max(0, p.life / p.maxLife);

    // color: warm/yellowish glow
    ctx.fillStyle = `rgba(255,${200 - (p.hue % 60)},${120},${0.6 * alpha})`;
    ctx.beginPath();
    ctx.arc(p.x, p.y, p.size * (0.7 + alpha * 0.6), 0, Math.PI * 2);
    ctx.fill();

    // subtle stretched tail - draw a thin rect in motion direction
    ctx.save();
    ctx.translate(p.x, p.y);
    const angle = Math.atan2(p.vy, p.vx);
    ctx.rotate(angle);
    ctx.fillStyle = `rgba(255,${180 - (p.hue % 40)},${100},${0.35 * alpha})`;
    ctx.fillRect(-p.size * 6, -p.size * 0.45, p.size * 6, p.size * 0.9);
    ctx.restore();

    // update
    p.x += p.vx;
    p.y += p.vy;
    p.vx *= 0.96;
    p.vy *= 0.96;
    p.life--;

    if (p.life <= 0) particles.splice(i, 1);
  }

  // draw cursor head (shooting-star)
  if (cursorVisible) {
    const grd = ctx.createRadialGradient(mouseX, mouseY, 0, mouseX, mouseY, 18);
    grd.addColorStop(0, 'rgba(255,255,220,1)');
    grd.addColorStop(0.2, 'rgba(255,220,120,0.9)');
    grd.addColorStop(0.5, 'rgba(255,160,70,0.6)');
    grd.addColorStop(1, 'rgba(255,120,20,0)');

    ctx.globalCompositeOperation = 'lighter';
    ctx.fillStyle = grd;
    ctx.beginPath();
    ctx.arc(mouseX, mouseY, 10, 0, Math.PI * 2);
    ctx.fill();

    // add a tiny star flares
    ctx.strokeStyle = 'rgba(255,255,200,0.9)';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(mouseX - 12, mouseY);
    ctx.lineTo(mouseX + 12, mouseY);
    ctx.moveTo(mouseX, mouseY - 12);
    ctx.lineTo(mouseX, mouseY + 12);
    ctx.stroke();
  }

  // restore default composite and loop
  ctx.globalCompositeOperation = 'source-over';
  requestAnimationFrame(animateStars);
}

animateStars();


// ===============================
// PROFILE DROPDOWN
// ===============================
const profileToggle = document.getElementById("profileToggle");
const profileDropdown = document.getElementById("profileDropdown");

profileToggle.addEventListener("click", () => {
  profileDropdown.style.display =
    profileDropdown.style.display === "block" ? "none" : "block";
});

document.addEventListener("click", (e) => {
  if (!profileToggle.contains(e.target))
    profileDropdown.style.display = "none";
});


// ===============================
// LEADERBOARD
// ===============================
function fetchLeaderboard() {
  fetch("fetch_leaderboard.php")
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById("leaderboard-body");
      tbody.innerHTML = "";
      if (!data.length) {
        tbody.innerHTML = "<tr><td colspan='3'>No scores yet!</td></tr>";
      } else {
        data.forEach(p => {
          tbody.innerHTML += `
            <tr><td>${p.rank}</td><td>${p.username}</td><td>${p.score}</td></tr>
          `;
        });
      }
    })
    .catch(() => {
      document.getElementById("leaderboard-body").innerHTML =
        "<tr><td colspan='3'>Error loading leaderboard</td></tr>";
    });
}
document.addEventListener("DOMContentLoaded", fetchLeaderboard);


// ===============================
// MUSIC TOGGLE
// ===============================
const musicOn = document.getElementById("musicOn");
const musicOff = document.getElementById("musicOff");
const bgMusic = document.getElementById("bgMusic");

musicOn.addEventListener("click", () => {
  musicOn.classList.add("active");
  musicOff.classList.remove("active");
  bgMusic.play();
});
musicOff.addEventListener("click", () => {
  musicOff.classList.add("active");
  musicOn.classList.remove("active");
  bgMusic.pause();
});


// ===============================
// NASA APOD PANEL + MODAL
// ===============================
const apodApiKey = "aGg4WRClHhmQhaYcVy1lcCy6JAoxWFDau6N18ZFO";
let apodData = {};

fetch(`https://api.nasa.gov/planetary/apod?api_key=${apodApiKey}`)
  .then(res => res.json())
  .then(data => {
    apodData = data;
    document.getElementById("apodImg").src = data.url;
    document.getElementById("apodImg").alt = data.title;
    document.getElementById("apodTitle").innerText = data.title;
    document.getElementById("apodDesc").innerText = data.explanation;
  })
  .catch(() => {
    document.getElementById("apodTitle").innerText = "Could not load APOD.";
    document.getElementById("apodDesc").innerText = "";
  });

document.getElementById("apodLearnMore").addEventListener("click", () => {
  document.getElementById("modalApodTitle").innerText = apodData.title;
  document.getElementById("modalApodImg").src = apodData.url;
  document.getElementById("modalApodDesc").innerText = apodData.explanation;

  new bootstrap.Modal(document.getElementById('apodModal')).show();
});


// ===============================
// NASA NEO SLIDER
// ===============================
const neoApiKey = apodApiKey;
let neoList = [];
let neoIndex = 0;

function loadNeoCard(i, direction = "right") {
  const card = document.getElementById("neoCard");

  card.classList.remove("show");
  card.classList.add(direction === "right" ? "hide-right" : "hide-left");

  setTimeout(() => {
    const neo = neoList[i];
    card.innerHTML = `
      <h6 class="text-warning fw-bold text-center">${neo.name}</h6>
      <p class="text-light small">
        <b>⚡ Speed:</b> ${neo.speed.toLocaleString()} km/h<br>
        <b>📏 Diameter:</b> ${neo.diameter} m<br>
        <b>🛑 Hazardous:</b>
        <span class="${neo.hazard ? "text-danger" : "text-success"} fw-bold">
          ${neo.hazard ? "YES" : "NO"}
        </span><br>
        <b>📅 Closest Approach:</b><br>${neo.date}
      </p>
    `;
    card.classList.remove("hide-right", "hide-left");
    setTimeout(() => card.classList.add("show"), 10);
  }, 300);
}

fetch(`https://api.nasa.gov/neo/rest/v1/feed?api_key=${neoApiKey}`)
  .then(res => res.json())
  .then(data => {
    const today = Object.keys(data.near_earth_objects)[0];
    neoList = data.near_earth_objects[today].map(obj => ({
      name: obj.name,
      speed: Number(obj.close_approach_data[0].relative_velocity.kilometers_per_hour),
      diameter: Math.floor(obj.estimated_diameter.meters.estimated_diameter_max),
      hazard: obj.is_potentially_hazardous_asteroid,
      date: obj.close_approach_data[0].close_approach_date_full
    }));
    loadNeoCard(neoIndex);
  })
  .catch(() => {
    document.getElementById("neoCard").innerHTML =
      `<p class="text-danger text-center">Failed to load NEO data.</p>`;
  });

document.getElementById("neoNext").addEventListener("click", () => {
  neoIndex = (neoIndex + 1) % neoList.length;
  loadNeoCard(neoIndex, "right");
});
document.getElementById("neoPrev").addEventListener("click", () => {
  neoIndex = (neoIndex - 1 + neoList.length) % neoList.length;
  loadNeoCard(neoIndex, "left");
});


// ===============================
// GAME ELEMENTS
// ===============================
const player = document.getElementById("player");
const container = document.querySelector(".game-container");

let x = 180, y = 10;
const step = 50;

let limitX, limitY;
let score = 0;
let level = 1;
let cometSpeed = 4;
let gameRunning = false;
let cometInterval = null;
let scoreInterval = null;
let activeComets = [];

let highScore = localStorage.getItem("spaceJamHighScore") || 0;
document.getElementById("highScore").textContent = highScore;

function updateLimits() {
  limitX = container.clientWidth - player.clientWidth;
  limitY = container.clientHeight - player.clientHeight;
}
updateLimits();


// ===============================
// PLAYER MOVEMENT
// ===============================
document.addEventListener("keydown", (e) => {
  if (!gameRunning) return;
  if (e.key === "ArrowLeft") x = Math.max(0, x - step);
  if (e.key === "ArrowRight") x = Math.min(limitX, x + step);
  if (e.key === "ArrowUp") y = Math.min(limitY, y + step);
  if (e.key === "ArrowDown") y = Math.max(0, y - step);

  player.style.left = `${x}px`;
  player.style.bottom = `${y}px`;
});


// ===============================
// GAME LOOP FUNCTIONS
// ===============================
function startGame() {
  resetGameState();
  gameRunning = true;
  startCometInterval();
  startScoreInterval();
}

function resetGameState() {
  x = 180;
  y = 10;
  player.style.left = `${x}px`;
  player.style.bottom = `${y}px`;

  score = 0;
  level = 1;
  cometSpeed = 4;
  highScoreNotified = false;

  document.getElementById("score").textContent = score;
  document.getElementById("level").textContent = level;

  clearComets();
  clearInterval(cometInterval);
  clearInterval(scoreInterval);
}

function startCometInterval() {
  cometInterval = setInterval(() => {
    if (gameRunning) createComet();
  }, 1500);
}

function startScoreInterval() {
  scoreInterval = setInterval(() => {
    if (!gameRunning) return;
    updateScore();
  }, 100);
}


document.getElementById("startBtn").addEventListener("click", () => {
  startGame();
});

// ===============================
// COMET CREATION
// ===============================
function createComet() {
  const obs = document.createElement("div");
  obs.classList.add("obstacle");
  obs.style.left = Math.floor(Math.random() * (container.clientWidth - 40)) + "px";
  container.appendChild(obs);

  let y = 0;
  const obj = { element: obs, y };
  activeComets.push(obj);

  obj._fallInterval = setInterval(() => {
    if (!gameRunning) return;

    y += cometSpeed;
    obs.style.top = y + "px";

    if (isColliding(player, obs)) {
      clearInterval(obj._fallInterval);
      gameOver();
    }

    if (y > container.clientHeight) {
      obs.remove();
      activeComets = activeComets.filter(c => c.element !== obs);
      clearInterval(obj._fallInterval);
    } else {
      obj.y = y;
    }
  }, 30);
}

function clearComets() {
  activeComets.forEach(c => {
    clearInterval(c._fallInterval);
    if (c.element) c.element.remove();
  });
  activeComets = [];
}

function isColliding(a, b) {
  const A = a.getBoundingClientRect();
  const B = b.getBoundingClientRect();
  return !(A.top > B.bottom || A.bottom < B.top || A.left > B.right || A.right < B.left);
}


// ===============================
// SCORE + LEVEL
// ===============================
let highScoreNotified = false;

function updateScore() {
  score++;
  document.getElementById("score").textContent = score;

  const newLevel = Math.floor(score / 100) + 1;
  if (newLevel > level) {
    level = newLevel;
    document.getElementById("level").textContent = level;
    cometSpeed += 2;
  }

  // Check if new high score during gameplay (only show notification once)
  if (score > highScore && !highScoreNotified) {
    highScoreNotified = true;
    highScore = score;
    localStorage.setItem("spaceJamHighScore", highScore);
    document.getElementById("highScore").textContent = highScore;
    
    // Display space-themed high score notification
    const notification = document.createElement("div");
    notification.innerHTML = `
      <div style="
        position: fixed;
        top: 20px;
        left: 20px;
        background: linear-gradient(135deg, #ffc107, #ff6b6b);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.8);
        z-index: 9999;
        font-weight: 600;
        text-align: center;
        animation: slideIn 0.5s ease-out, slideOut 0.5s ease-out 3.5s forwards;
      ">
        <p style="margin: 0; font-size: 1.2rem;">🚀 NEW HIGH SCORE! 🚀</p>
        <p style="margin: 0.5rem 0 0 0; font-size: 2rem;">${highScore}</p>
      </div>
      <style>
        @keyframes slideIn {
          from {
            transform: translateX(-400px);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }
        @keyframes slideOut {
          to {
            transform: translateX(-400px);
            opacity: 0;
          }
        }
      </style>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => notification.remove(), 4000);
  }
}

// ===============================
// GAME OVER + QUIZ
// ===============================
async function gameOver() {
  gameRunning = false;
  localStorage.setItem("cachedScore", score);

  if (score > highScore) {
    highScore = score;
    localStorage.setItem("spaceJamHighScore", highScore);
    document.getElementById("highScore").textContent = highScore;
  }

  fetch("save_score.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ score: score, level: level })
  });

  new bootstrap.Modal(document.getElementById("gameOverModal")).show();
  await loadBananaQuizIntoModal();
}

async function getBananaQuiz() {
  try {
    const res = await fetch("https://www.sanfoh.com/uob/banana/api/random");
    return await res.json();
  } catch {
    return { question: null, solution: 42 };
  }
}

async function loadBananaQuizIntoModal() {
  const qArea = document.getElementById("quizQuestionArea");
  const answerField = document.getElementById("answerField");
  const btn = document.getElementById("tryAgainBtn");

  qArea.innerHTML = `
    <div class="text-center">
      <div class="spinner-border text-warning"></div>
      <p class="mt-2">Loading...</p>
    </div>
  `;
  btn.disabled = true;

  const quiz = await getBananaQuiz();
  window.currentQuiz = quiz;

  if (quiz.question) {
    qArea.innerHTML = `
      <p class="text-warning fw-bold mb-3">Solve to continue:</p>
      <img src="${quiz.question}" class="img-fluid rounded border border-warning shadow">
    `;
  } else {
    qArea.innerHTML = `
      <p class="text-warning fw-bold mb-3">Quick Math!</p>
      <p class="fs-4 text-light">6 × 7 = ?</p>
    `;
  }

  btn.disabled = false;
  answerField.value = "";
  setTimeout(() => answerField.focus(), 300);
}


// ===============================
// QUIZ SUBMISSION HANDLER
// ===============================
document.getElementById("tryAgainBtn").addEventListener("click", () => {
  const ans = parseInt(document.getElementById("answerField").value.trim());
  const correct = parseInt(window.currentQuiz.solution);

  const modal = bootstrap.Modal.getInstance(document.getElementById("gameOverModal"));

  if (ans === correct) {
    // Display success message with space theme effect
    const qArea = document.getElementById("quizQuestionArea");
    
    qArea.innerHTML = `
      <div class="text-center">
        <p class="fs-1 mb-3" style="animation: pulse 0.5s infinite;">✨</p>
        <p class="text-success fw-bold fs-5">Excellent! Mission Approved!</p>
        <p class="text-info">Your rocket ignited and launched successfully 🚀</p>
        <p class="text-light mt-3">Resuming operations...</p>
      </div>
      <style>
        @keyframes pulse {
          0%, 100% { transform: scale(1); }
          50% { transform: scale(1.3); }
        }
        @keyframes glow {
          0%, 100% { text-shadow: 0 0 10px #00ff00; }
          50% { text-shadow: 0 0 20px #00ff00, 0 0 30px #00ff00; }
        }
      </style>
    `;
    
    setTimeout(() => {
      modal.hide();
      clearComets();
      resumeGame();
    }, 4000);
  } else {
    // Display wrong answer message with player name
    const qArea = document.getElementById("quizQuestionArea");
    const username = document.body.getAttribute("data-username") || "Pilot";
    
    qArea.innerHTML = `
      <div class="text-center">
        <p class="fs-2 mb-3">❌</p>
        <p class="text-danger fw-bold fs-5">Wrong answer mate!</p>
        <p class="text-warning">Your rocket crashed into an asteroid 🪨</p>
        <p class="text-light mt-3">Restarting mission...</p>
      </div>
    `;
    
    setTimeout(() => {
      modal.hide();
      localStorage.setItem("cachedScore", 0);
      startGame();
    }, 5000);
  }
});

// ===============================
// RESUME GAME
// ===============================
function resumeGame() {
  gameRunning = true;
  score = parseInt(localStorage.getItem("cachedScore")) || 0;
  document.getElementById("score").textContent = score;

  startScoreInterval();
  startCometInterval();
}


document.getElementById("restartBtn").addEventListener("click", () => {
  clearInterval(cometInterval);
  clearInterval(scoreInterval);
  clearComets();
  startGame();
});