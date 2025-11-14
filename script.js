const player = document.getElementById("player");
const container = document.querySelector(".game-container");
const gameOverText = document.getElementById("gameOver");
const restartBtn = document.getElementById("restartBtn");
const scoreEl = document.getElementById("score");
const highScoreEl = document.getElementById("highScore");

let x = 180;
let y = 10;
const step = 50;
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
//highscore save
function gameOver() {
  gameRunning = false;
  gameOverText.style.display = "block";

  // Local high score for quick display
  if (score > highScore) {
    highScore = score;
    localStorage.setItem("spaceJamHighScore", highScore);
    highScoreEl.textContent = highScore;
  }

  // Save score to server
  fetch("save_score.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ score: score, level: "Level 1" })
  })
  .then(res => res.text())
  .then(msg => console.log(msg))
  .catch(err => console.error("Error saving score:", err));
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

 // Profile dropdown event
    const profileIcon = document.getElementById('profileIcon');
    const profileDropdown = document.getElementById('profileDropdown');

    profileIcon.addEventListener('click', () => {
      profileDropdown.style.display = profileDropdown.style.display === 'block' ? 'none' : 'block';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!profileIcon.contains(e.target) && !profileDropdown.contains(e.target)) {
        profileDropdown.style.display = 'none';
      }
    });


// Background Music Control
const bgMusic = document.getElementById("bgMusic");
const musicOn = document.getElementById("musicOn");
const musicOff = document.getElementById("musicOff");

let musicStarted = false;
let fadeInterval;

// Function to start music with fade-in
function startMusic() {
  if (!musicStarted) {
    bgMusic.volume = 0;       // start muted
    bgMusic.play().catch(err => console.log("Autoplay blocked:", err));
    fadeInterval = setInterval(() => {
      if (bgMusic.volume < 0.5) {
        bgMusic.volume = Math.min(bgMusic.volume + 0.05, 0.5);
      } else {
        clearInterval(fadeInterval);
      }
    }, 200);
    musicStarted = true;
    console.log("Music started");
  }
}

// Music On button
musicOn.addEventListener("click", () => {
  musicOn.classList.add("active");
  musicOff.classList.remove("active");
  startMusic();
  bgMusic.volume = 0.5;  // ensure volume is at 50%
  console.log("Music turned ON");
});

// Music Off button
musicOff.addEventListener("click", () => {
  musicOff.classList.add("active");
  musicOn.classList.remove("active");
  // Fade out before pause
  clearInterval(fadeInterval);
  fadeInterval = setInterval(() => {
    if (bgMusic.volume > 0.05) {
      bgMusic.volume -= 0.05;
    } else {
      bgMusic.pause();
      clearInterval(fadeInterval);
    }
  }, 200);
  console.log("Music turned OFF");
});


document.addEventListener("click", startMusic, { once: true });
document.addEventListener("keydown", startMusic, { once: true });

