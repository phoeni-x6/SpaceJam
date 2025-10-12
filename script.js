const player = document.getElementById("player");
const container = document.querySelector(".game-container");
const gameOverText = document.getElementById("gameOver");
const restartBtn = document.getElementById("restartBtn");
const scoreEl = document.getElementById("score");
const highScoreEl = document.getElementById("highScore");

let x = 180;
let y = 10;
const step = 10;
const limitX = container.clientWidth - player.clientWidth;
const limitY = container.clientHeight - player.clientHeight;
let gameRunning = true;

let score = 0;

// Load high score from localStorage
let highScore = localStorage.getItem("spaceJamHighScore") || 0;
highScoreEl.textContent = highScore;

// Player controls
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

// Spawn comets
function createObstacle() {
  const obstacle = document.createElement("div");
  obstacle.classList.add("obstacle");
  obstacle.style.left = Math.floor(Math.random() * (container.clientWidth - 40)) + "px";
  container.appendChild(obstacle);

  let obstacleY = 0;
  const fallSpeed = 4;

  const fallInterval = setInterval(() => {
    if (!gameRunning) {
      clearInterval(fallInterval);
      obstacle.remove();
      return;
    }
  
    obstacleY += cometSpeed;
    obstacle.style.top = obstacleY + "px";
  
    if (isColliding(player, obstacle)) gameOver();
  
    if (obstacleY > container.clientHeight) {
      obstacle.remove();
      clearInterval(fallInterval);
    }
  }, 30);
  
}

// Collision detection
function isColliding(a, b) {
  const aRect = a.getBoundingClientRect();
  const bRect = b.getBoundingClientRect();
  return !(
    aRect.top > bRect.bottom ||
    aRect.bottom < bRect.top ||
    aRect.left > bRect.right ||
    aRect.right < bRect.left
  );
}

// Game Over
function gameOver() {
  gameRunning = false;
  gameOverText.style.display = "block";

  
  if (score > highScore) {
    highScore = score;
    localStorage.setItem("spaceJamHighScore", highScore);
    highScoreEl.textContent = highScore;
  }
}

// Restart 
restartBtn.addEventListener("click", () => {
  window.location.reload();
});

// Spawn comets every 1.5s
setInterval(() => {
  if (gameRunning) createObstacle();
}, 1500);

//  increment 
setInterval(() => {
    if (gameRunning) {
      score++;
      scoreEl.textContent = score;
  
      //increse on 100 points and comet speed also iiincrease 
      const newLevel = Math.floor(score / 100) + 1;
      if (newLevel > level) {
        level = newLevel;
        levelEl.textContent = level;
        cometSpeed += 2; 
      }
    }
  }, 100);
  

//levels
const levelEl = document.getElementById("level"); 
let level = 1; 
let cometSpeed = 4; 
