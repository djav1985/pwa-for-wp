(function() {
  if (!('ontouchstart' in window)) return;
  let startY = 0;
  let triggered = false;
  const threshold = 80;

  const spinner = document.createElement('div');
  spinner.className = 'pwaforwp-ptr-spinner';
  document.addEventListener('DOMContentLoaded', function() {
    document.body.appendChild(spinner);
  });

  document.addEventListener('touchstart', function(e) {
    if (window.scrollY === 0) {
      startY = e.touches[0].clientY;
      triggered = false;
    } else {
      startY = 0;
    }
  }, {passive: true});

  document.addEventListener('touchmove', function(e) {
    if (!startY) return;
    const currentY = e.touches[0].clientY;
    if (currentY - startY > threshold && !triggered) {
      triggered = true;
      document.body.classList.add('pwaforwp-refreshing');
      window.location.reload();
    }
  }, {passive: true});
})();
