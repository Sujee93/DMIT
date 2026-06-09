// ZelClean Melbourne — Main JS

document.addEventListener('DOMContentLoaded', () => {

  // ---- Navbar scroll effect ----
  const navbar = document.querySelector('.navbar');
  const scrollHandler = () => {
    if (window.scrollY > 40) {
      navbar?.classList.add('scrolled');
    } else {
      navbar?.classList.remove('scrolled');
    }
  };
  window.addEventListener('scroll', scrollHandler, { passive: true });
  scrollHandler();

  // ---- Mobile menu toggle ----
  const hamburger = document.querySelector('.hamburger');
  const menuClose = document.querySelector('.menu-close');

  hamburger?.addEventListener('click', () => {
    document.body.classList.toggle('mobile-menu-open');
    const isOpen = document.body.classList.contains('mobile-menu-open');
    hamburger.setAttribute('aria-expanded', isOpen);
    // Swap icon
    hamburger.querySelectorAll('span').forEach((span, i) => {
      if (isOpen) {
        if (i === 0) span.style.transform = 'translateY(7px) rotate(45deg)';
        if (i === 1) span.style.opacity = '0';
        if (i === 2) span.style.transform = 'translateY(-7px) rotate(-45deg)';
      } else {
        span.style.transform = '';
        span.style.opacity = '';
      }
    });
  });

  // Close mobile menu on nav link click
  document.querySelectorAll('.navbar-nav a').forEach(link => {
    link.addEventListener('click', () => {
      document.body.classList.remove('mobile-menu-open');
      hamburger?.querySelectorAll('span').forEach(s => {
        s.style.transform = '';
        s.style.opacity = '';
      });
    });
  });

  // ---- Active nav link ----
  const currentPath = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.navbar-nav a').forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPath || (currentPath === '' && href === 'index.html')) {
      link.classList.add('active');
    }
  });

  // ---- Scroll reveal ----
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

  // ---- FAQ accordion ----
  document.querySelectorAll('.faq-question').forEach(btn => {
    btn.addEventListener('click', () => {
      const item = btn.closest('.faq-item');
      const isOpen = item.classList.contains('open');

      // Close all
      document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));

      if (!isOpen) item.classList.add('open');
    });
  });

  // ---- Counter animation ----
  const counters = document.querySelectorAll('.stat-number[data-target]');
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseInt(el.getAttribute('data-target'));
        const suffix = el.getAttribute('data-suffix') || '';
        const prefix = el.getAttribute('data-prefix') || '';
        const duration = 1800;
        const start = performance.now();

        const update = (now) => {
          const elapsed = now - start;
          const progress = Math.min(elapsed / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          el.textContent = prefix + Math.round(eased * target) + suffix;
          if (progress < 1) requestAnimationFrame(update);
        };

        requestAnimationFrame(update);
        counterObserver.unobserve(el);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(c => counterObserver.observe(c));

  // ---- Form handling ----
  document.querySelectorAll('form[data-form]').forEach(form => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      const btn = form.querySelector('[type="submit"]');
      const originalText = btn.textContent;

      btn.textContent = 'Sending...';
      btn.disabled = true;

      // Simulate submission (replace with real endpoint)
      setTimeout(() => {
        btn.textContent = '✓ Message Sent!';
        btn.style.background = '#27AE60';

        // Show success message
        const success = document.createElement('p');
        success.style.cssText = 'text-align:center;color:#27AE60;font-weight:600;font-size:.9rem;margin-top:12px;';
        success.textContent = "Thanks! We'll be in touch within 24 hours.";
        form.appendChild(success);

        setTimeout(() => {
          btn.textContent = originalText;
          btn.disabled = false;
          btn.style.background = '';
          form.reset();
          success.remove();
        }, 4000);
      }, 1200);
    });
  });

  // ---- Phone click tracking ----
  document.querySelectorAll('a[href^="tel:"]').forEach(link => {
    link.addEventListener('click', () => {
      if (typeof gtag !== 'undefined') {
        gtag('event', 'phone_click', { event_category: 'contact' });
      }
    });
  });

});
