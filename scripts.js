document.addEventListener('DOMContentLoaded', () => {
  const themeToggle = document.getElementById('toggleTheme');
  const navToggleBtn = document.querySelector('.nav-toggle');
  const navLinks = document.querySelector('.nav-links');
  const body = document.body;

  // Dark/light mode with persistence in localStorage
  const savedTheme = localStorage.getItem('theme');
  function applyTheme(theme) {
    if (theme === 'dark-mode') {
      body.classList.add('dark-mode');
      body.classList.remove('light-mode');
      themeToggle.textContent = 'Light Mode';
    } else {
      body.classList.add('light-mode');
      body.classList.remove('dark-mode');
      themeToggle.textContent = 'Dark Mode';
    }
  }
  applyTheme(savedTheme || 'light-mode');

  themeToggle.addEventListener('click', () => {
    if(body.classList.contains('light-mode')) {
      applyTheme('dark-mode');
      localStorage.setItem('theme', 'dark-mode');
    } else {
      applyTheme('light-mode');
      localStorage.setItem('theme', 'light-mode');
    }
  });

  // Navigation toggle
  navToggleBtn.addEventListener('click', () => {
    navToggleBtn.classList.toggle('active');
    navLinks.classList.toggle('show');
  });

  // Close nav links dropdown on outside click
  document.addEventListener('click', event => {
    if (!navLinks.contains(event.target) && !navToggleBtn.contains(event.target)) {
      navLinks.classList.remove('show');
      navToggleBtn.classList.remove('active');
    }
  });

  // Multi-step registration form (simple implementation)
  const steps = document.querySelectorAll('.step');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const submitBtn = document.querySelector('.btn-submit');
  const progressBar = document.getElementById('progressBar');
  let currentStep = 0;

  if(steps.length > 0) {
    showStep(currentStep);

    nextBtn.addEventListener('click', () => {
      if(validateStep()) {
        currentStep++;
        if(currentStep >= steps.length) currentStep = steps.length - 1;
        showStep(currentStep);
      }
    });

    prevBtn.addEventListener('click', () => {
      currentStep--;
      if(currentStep < 0) currentStep = 0;
      showStep(currentStep);
    });

    function showStep(n) {
      steps.forEach((step, idx) => {
        step.classList.toggle('active', idx === n);
      });
      prevBtn.disabled = (n === 0);
      if (n === steps.length - 1) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'block';
      } else {
        nextBtn.style.display = 'inline-block';
        submitBtn.style.display = 'none';
      }
      if(progressBar) {
        progressBar.style.width = ((n + 1) / steps.length) * 100 + '%';
      }
    }

    function validateStep() {
      let valid = true;
      const inputs = steps[currentStep].querySelectorAll('input, select');
      for(let input of inputs) {
        if(input.hasAttribute('required') && !input.value) {
          alert('Tafadhali jaza: ' + input.previousElementSibling.textContent);
          valid = false;
          break;
        }
      }
      return valid;
    }
  }
});
