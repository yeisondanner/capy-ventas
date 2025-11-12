// Botón "Retry"
document.getElementById('retryBtn').addEventListener('click', () => {
  // Redirige al home o a donde prefieras
  window.location.href = '/';
});

// Si NO hay imagen de capibara, mostramos el SVG fallback.
// Si SÍ hay imagen (src válido), ocultamos el SVG.
(function manageCapy(){
  const capyImg = document.getElementById('capyImg');
  const capySvg = document.getElementById('capySvg');

  const hasImg = capyImg && capyImg.getAttribute('src') && capyImg.getAttribute('src').trim() !== '';
  if (hasImg) {
    capySvg.style.display = 'none';
  } else {
    capyImg.style.display = 'none';
  }
})();

// Si alguno de los conos no tiene src, se oculta automáticamente
Array.from(document.querySelectorAll('.cone')).forEach(el => {
  const s = el.getAttribute('src');
  if (!s || !s.trim()) el.style.display = 'none';
});
