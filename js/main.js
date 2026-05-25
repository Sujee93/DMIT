/* ===========================
   DMIT – Main JavaScript
   =========================== */

/* ---- Nav scroll shadow ---- */
(function () {
  var nav = document.getElementById('mainNav');
  if (!nav) return;
  function onScroll() {
    if (window.scrollY > 20) {
      nav.classList.add('scrolled');
    } else {
      nav.classList.remove('scrolled');
    }
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
})();

/* ---- Mobile menu toggle ---- */
(function () {
  var hamburger = document.querySelector('.nav-hamburger');
  var mobileNav = document.querySelector('.nav-mobile');
  if (!hamburger || !mobileNav) return;
  hamburger.addEventListener('click', function () {
    mobileNav.classList.toggle('open');
    hamburger.classList.toggle('active');
  });
  // Close on link click
  mobileNav.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', function () {
      mobileNav.classList.remove('open');
      hamburger.classList.remove('active');
    });
  });
})();

/* ---- FAQ Accordion ---- */
(function () {
  var items = document.querySelectorAll('.faq-item');
  items.forEach(function (item) {
    var question = item.querySelector('.faq-question');
    var toggle = item.querySelector('.faq-toggle');
    if (!question) return;
    question.addEventListener('click', function () {
      var isOpen = item.classList.contains('open');
      // Close all
      items.forEach(function (i) {
        i.classList.remove('open');
        var t = i.querySelector('.faq-toggle');
        if (t) t.textContent = '+';
      });
      // Open clicked if it was closed
      if (!isOpen) {
        item.classList.add('open');
        if (toggle) toggle.textContent = '×';
      }
    });
  });
})();

/* ---- Curriculum Accordion (course detail) ---- */
(function () {
  var headers = document.querySelectorAll('.curriculum-header');
  headers.forEach(function (header) {
    header.addEventListener('click', function () {
      var body = header.nextElementSibling;
      if (!body) return;
      body.classList.toggle('open');
      var arrow = header.querySelector('.curr-arrow');
      if (arrow) arrow.textContent = body.classList.contains('open') ? '▲' : '▼';
    });
  });
})();

/* ---- Scroll Animations (IntersectionObserver) ---- */
(function () {
  var els = document.querySelectorAll('.fade-in');
  if (!els.length) return;
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  els.forEach(function (el) { observer.observe(el); });
})();

/* ---- Stats Counter Animation ---- */
(function () {
  var counters = document.querySelectorAll('[data-count]');
  if (!counters.length) return;
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      var el = entry.target;
      var target = parseInt(el.getAttribute('data-count'), 10);
      var suffix = el.getAttribute('data-suffix') || '';
      var duration = 1800;
      var start = performance.now();
      function update(now) {
        var elapsed = now - start;
        var progress = Math.min(elapsed / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.floor(eased * target) + suffix;
        if (progress < 1) requestAnimationFrame(update);
      }
      requestAnimationFrame(update);
      observer.unobserve(el);
    });
  }, { threshold: 0.3 });
  counters.forEach(function (el) { observer.observe(el); });
})();

/* ---- Category horizontal scroll ---- */
(function () {
  var scroll = document.querySelector('.categories-scroll');
  var btnPrev = document.querySelector('.cat-scroll-btn[data-dir="prev"]');
  var btnNext = document.querySelector('.cat-scroll-btn[data-dir="next"]');
  if (!scroll) return;
  var step = 180;
  if (btnPrev) {
    btnPrev.addEventListener('click', function () {
      scroll.scrollBy({ left: -step, behavior: 'smooth' });
    });
  }
  if (btnNext) {
    btnNext.addEventListener('click', function () {
      scroll.scrollBy({ left: step, behavior: 'smooth' });
    });
  }
})();

/* ---- Filter pills (courses page) ---- */
(function () {
  var pills = document.querySelectorAll('.filter-pill');
  pills.forEach(function (pill) {
    pill.addEventListener('click', function () {
      pills.forEach(function (p) { p.classList.remove('active'); });
      pill.classList.add('active');
    });
  });
})();

/* ---- Testimonials prev/next (no-op cosmetic) ---- */
(function () {
  var btnPrev = document.querySelector('.testi-btn[data-dir="prev"]');
  var btnNext = document.querySelector('.testi-btn[data-dir="next"]');
  // Future enhancement: carousel logic
})();
