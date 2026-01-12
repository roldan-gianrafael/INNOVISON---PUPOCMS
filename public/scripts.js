// scripts.js - small helpers for the landing page
// Handles clicks on role cards and keyboard activation

document.addEventListener('DOMContentLoaded', function(){
  // set footer year
  const y = document.getElementById('year'); if(y) y.textContent = new Date().getFullYear();

  // role card navigation
  document.querySelectorAll('.role-card').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.dataset.target;
      if(target) window.location.href = target;
    });

    // keyboard: Space/Enter
    btn.addEventListener('keydown', (e) => {
      if(e.key === 'Enter' || e.key === ' ') {
        e.preventDefault(); btn.click();
      }
    });
  });
});
