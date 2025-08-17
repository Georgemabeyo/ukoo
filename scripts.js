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
$(document).on('click keypress', '.member', function(e) {
    if(e.type === 'keypress' && ![13,32].includes(e.which)) return;
    if($(e.target).hasClass('view-children-btn')) return;
    const id = $(this).data('id');
    const container = $('#children-' + id);
    if(container.is(':visible')){
        container.slideUp(200);
        $(this).attr('aria-expanded', 'false');
        container.attr('aria-hidden', 'true');
    } else {
        if(container.children().length === 0){
            $.get('load_children.php', {parent_id: id}, function(data){
                container.html(data).slideDown(200);
                $(this).attr('aria-expanded', 'true');
                container.attr('aria-hidden', 'false');
            }.bind(this));
        } else {
            container.slideDown(200);
            $(this).attr('aria-expanded', 'true');
            container.attr('aria-hidden', 'false');
        }
    }
});

$(document).on('click', '.view-children-btn', function(e){
    e.stopPropagation();
    const parentId = $(this).data('parent');
    $.get('view_member.php', {id: parentId}, function(data){
        // Display in modal instead of alert (implement modal in HTML)
        alert(data);
    });
});

