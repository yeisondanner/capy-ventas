// ==================== VISOR DEL ASTRONAUTA ====================
function drawVisor() {
  const canvas = document.getElementById('visor');
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  ctx.beginPath();
  ctx.moveTo(5, 45);
  ctx.bezierCurveTo(15, 64, 45, 64, 55, 45);
  ctx.lineTo(55, 20);
  ctx.bezierCurveTo(55, 15, 50, 10, 45, 10);
  ctx.lineTo(15, 10);
  ctx.bezierCurveTo(15, 10, 5, 10, 5, 20);
  ctx.lineTo(5, 45);

  ctx.fillStyle = '#2f3640';
  ctx.strokeStyle = '#f5f6fa';
  ctx.fill();
  ctx.stroke();
}

// ==================== ANIMACIÓN DEL CORDÓN ====================
function initCordAnimation() {
  const cordCanvas = document.getElementById('cord');
  if (!cordCanvas) return;

  const ctx = cordCanvas.getContext('2d');

  let y1 = 160;
  let y2 = 100;
  let y3 = 100;

  let y1Forward = true;
  let y2Forward = false;
  let y3Forward = true;

  function animate() {
    requestAnimationFrame(animate);

    ctx.clearRect(0, 0, cordCanvas.width, cordCanvas.height);

    ctx.beginPath();
    ctx.moveTo(130, 170);
    ctx.bezierCurveTo(250, y1, 345, y2, 400, y3);

    ctx.strokeStyle = 'white';
    ctx.lineWidth = 8;
    ctx.stroke();

    // Control de dirección de movimiento
    if (y1 === 100) y1Forward = true;
    if (y1 === 300) y1Forward = false;
    if (y2 === 100) y2Forward = true;
    if (y2 === 310) y2Forward = false;
    if (y3 === 100) y3Forward = true;
    if (y3 === 317) y3Forward = false;

    // Movimiento ondulado
    y1Forward ? y1++ : y1--;
    y2Forward ? y2++ : y2--;
    y3Forward ? y3++ : y3--;
  }

  animate();
}

// ==================== EJECUCIÓN ====================
document.addEventListener('DOMContentLoaded', function () {
  drawVisor();
  initCordAnimation();
});
