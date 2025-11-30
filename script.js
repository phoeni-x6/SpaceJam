const player = document.getElementById("player");
const container = document.querySelector(".game-container");
const scoreEl = document.getElementById("score");
const highScoreEl = document.getElementById("highScore");
const levelEl = document.getElementById("level");

let x = 180;
let y = 10;
const step = 50;
const limitX = container.clientWidth - player.clientWidth;
const limitY = container.clientHeight - player.clientHeight;

let gameRunning = true;
let score = 0;
let level = 1;
let cometSpeed = 4;

// Load high score
let highScore = localStorage.getItem("spaceJamHighScore") || 0;
highScoreEl.textContent = highScore;

// Banana Quiz Global
let currentQuiz = null;

// INTERVALS
let cometInterval = null;
let scoreInterval = null;

// ACTIVE COMETS TRACKER
let activeComets = [];

// PLAYER CONTROLS
document.addEventListener("keydown", (e) => {
  if (!gameRunning) return;
  switch (e.key) {
    case "ArrowLeft": x = Math.max(0, x - step); break;
    case "ArrowRight": x = Math.min(limitX, x + step); break;
    case "ArrowUp": y = Math.min(limitY, y + step); break;
    case "ArrowDown": y = Math.max(0, y - step); break;
  }
  player.style.left = `${x}px`;
  player.style.bottom = `${y}px`;
});

// SPAWN COMETS
function createObstacle() {
  const obstacle = document.createElement("div");
  obstacle.classList.add("obstacle");
  obstacle.style.left = Math.floor(Math.random() * (container.clientWidth - 40)) + "px";
  container.appendChild(obstacle);

  let obstacleY = 0;
  activeComets.push({ element: obstacle, y: obstacleY });

  const fallInterval = setInterval(() => {
    if (!gameRunning) return; // pause movement
    obstacleY += cometSpeed;
    obstacle.style.top = obstacleY + "px";
    if (isColliding(player, obstacle)) gameOver();
    if (obstacleY > container.clientHeight) {
      obstacle.remove();
      activeComets = activeComets.filter(c => c.element !== obstacle);
      clearInterval(fallInterval);
    } else {
      // Update tracker
      const comet = activeComets.find(c => c.element === obstacle);
      if (comet) comet.y = obstacleY;
    }
  }, 30);
}

function isColliding(a, b) {
  const aRect = a.getBoundingClientRect();
  const bRect = b.getBoundingClientRect();
  return !(aRect.top > bRect.bottom || aRect.bottom < bRect.top || aRect.left > bRect.right || aRect.right < bRect.left);
}

// BANANA QUIZ API
async function getBananaQuiz() {
  try {
    const r = await fetch("https://www.sanfoh.com/uob/banana/api/random");
    const d = await r.json();
    currentQuiz = d;
    return d;
  } catch (e) {
    console.error("Banana API error:", e);
    return null;
  }
}

// GAME OVER
async function gameOver() {
  if (!gameRunning) return; // prevent multiple triggers

  gameRunning = false; // pause game

  // Save high score
  if (score > highScore) {
    highScore = score;
    localStorage.setItem("spaceJamHighScore", highScore);
    highScoreEl.textContent = highScore;
  }

  fetch("save_score.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ score: score, level: `Level ${level}` })
  })
  .then(res => res.json())
  .then(data => {
    if (!data.success) console.warn("Score save failed:", data.error);
  })
  .catch(err => console.error("Fetch error:", err));

  // Show modal
  const modal = new bootstrap.Modal(document.getElementById("gameOverModal"));
  modal.show();

  // Load quiz
  await loadBananaQuizIntoModal();
}

// Load quiz into modal
async function loadBananaQuizIntoModal() {
  const quizArea = document.getElementById("quizQuestionArea");
  const answerField = document.getElementById("answerField");
  const tryAgainBtn = document.getElementById("tryAgainBtn");

  answerField.value = "";
  tryAgainBtn.disabled = true;
  quizArea.innerHTML = `
    <div class="text-center">
      <div class="spinner-border text-warning" role="status"></div>
      <p class="mt-2">Loading math problem...</p>
    </div>
  `;

  const quiz = await getBananaQuiz();
  if (quiz && quiz.question && quiz.solution !== undefined) {
    quizArea.innerHTML = `
      <p class="text-warning fw-bold mb-3">Solve to continue:</p>
      <img src="${quiz.question}" alt="Math Problem"
           class="img-fluid rounded border border-warning shadow"
           style="max-height: 300px; max-width: 100%;">
    `;
    tryAgainBtn.disabled = false;
  } else {
    currentQuiz = { solution: 42 };
    quizArea.innerHTML = `
      <p class="text-warning fw-bold mb-3">Quick Math!</p>
      <p class="fs-4 text-light">6 × 7 = ?</p>
    `;
    tryAgainBtn.disabled = false;
  }

  setTimeout(() => answerField.focus(), 400);
}

// QUIZ ANSWER SUBMISSION
document.getElementById("tryAgainBtn").addEventListener("click", () => {
  const ans = document.getElementById("answerField").value.trim();
  if (!ans) return alert("Please enter the answer!");

  const userAnswer = parseInt(ans);
  const correctAnswer = parseInt(currentQuiz.solution);

  if (userAnswer === correctAnswer) {
    alert("Correct! Game continues from your current score!");
    bootstrap.Modal.getInstance(document.getElementById("gameOverModal")).hide();
    resumeGame();
  } else {
    alert(`Wrong! The answer was ${correctAnswer}. Try again.`);
    document.getElementById("answerField").value = "";
    document.getElementById("answerField").focus();
  }
});

// ENTER KEY SUPPORT
document.getElementById("answerField").addEventListener("keypress", (e) => {
  if (e.key === "Enter") document.getElementById("tryAgainBtn").click();
});

// RESUME GAME FUNCTION
function resumeGame() {
  gameRunning = true;

  // Resume comets that were active
  activeComets.forEach(cometObj => {
    const obstacle = cometObj.element;
    let obstacleY = cometObj.y;

    const fallInterval = setInterval(() => {
      if (!gameRunning) return;
      obstacleY += cometSpeed;
      obstacle.style.top = obstacleY + "px";
      if (isColliding(player, obstacle)) gameOver();
      if (obstacleY > container.clientHeight) {
        obstacle.remove();
        activeComets = activeComets.filter(c => c.element !== obstacle);
        clearInterval(fallInterval);
      } else {
        cometObj.y = obstacleY;
      }
    }, 30);
  });
}

// GAME LOOPS
cometInterval = setInterval(() => { if (gameRunning) createObstacle(); }, 1500);
scoreInterval = setInterval(() => {
  if (gameRunning) {
    score++;
    scoreEl.textContent = score;
    const newLevel = Math.floor(score / 100) + 1;
    if (newLevel > level) {
      level = newLevel;
      levelEl.textContent = level;
      cometSpeed += 2;
    }
  }
}, 100);
