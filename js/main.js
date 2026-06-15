/* TamilNewsWire — shared interactions & GSAP animations */
(function () {
  "use strict";

  /* ---------- Mobile nav ---------- */
  var burger = document.getElementById("burger");
  var mobileNav = document.getElementById("mobileNav");
  if (burger && mobileNav) {
    burger.addEventListener("click", function () {
      var open = mobileNav.classList.toggle("is-open");
      burger.classList.toggle("is-open", open);
      burger.setAttribute("aria-expanded", open ? "true" : "false");
      document.body.style.overflow = open ? "hidden" : "";
    });
    mobileNav.addEventListener("click", function (e) {
      if (e.target === mobileNav || e.target.closest("a")) {
        mobileNav.classList.remove("is-open");
        burger.classList.remove("is-open");
        burger.setAttribute("aria-expanded", "false");
        document.body.style.overflow = "";
      }
    });
  }

  /* ---------- Reading progress (article page) ---------- */
  var progress = document.getElementById("readingProgress");
  if (progress) {
    var updateProgress = function () {
      var h = document.documentElement;
      var scrolled = h.scrollTop;
      var max = h.scrollHeight - h.clientHeight;
      progress.style.width = (max > 0 ? (scrolled / max) * 100 : 0) + "%";
    };
    window.addEventListener("scroll", updateProgress, { passive: true });
    window.addEventListener("resize", updateProgress);
    updateProgress();
  }

  /* ---------- GSAP ---------- */
  var hasGsap = typeof gsap !== "undefined";
  var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (!hasGsap || reduced) {
    // Fallback: make sure nothing stays hidden
    document.querySelectorAll(".gs-reveal, .gs-card, #featureCard, .snip").forEach(function (el) {
      el.style.opacity = 1;
    });
    return;
  }

  gsap.registerPlugin(ScrollTrigger);

  /* Breaking ticker — seamless marquee */
  var track = document.getElementById("tickerTrack");
  if (track) {
    track.innerHTML += track.innerHTML; // duplicate for loop
    var half = track.scrollWidth / 2;
    gsap.to(track, {
      x: -half,
      duration: Math.max(18, half / 60),
      ease: "none",
      repeat: -1,
      modifiers: {
        x: function (x) { return (parseFloat(x) % half) + "px"; }
      }
    });
  }

  /* Hero entrance */
  var heroTl = gsap.timeline({ defaults: { ease: "power3.out" } });
  var feature = document.getElementById("featureCard");
  if (feature) {
    heroTl
      .from(feature, { y: 50, opacity: 0, duration: 0.9 })
      .from(feature.querySelectorAll(".badge, h1, p, .feature-card__meta"), {
        y: 26, opacity: 0, duration: 0.7, stagger: 0.1, clearProps: "transform,opacity"
      }, "-=0.45")
      .from("#sideSnips > *", { x: 40, opacity: 0, duration: 0.6, stagger: 0.09, clearProps: "transform,opacity" }, "-=0.6");
  }
  var heroTitle = document.getElementById("heroTitle");
  if (heroTitle) {
    heroTl
      .from(".breadcrumb, .page-hero .kicker", { y: 18, opacity: 0, duration: 0.5, stagger: 0.08 })
      .from(heroTitle, { y: 34, opacity: 0, duration: 0.8 }, "-=0.25")
      .from(".page-hero p, .page-hero__actions", { y: 24, opacity: 0, duration: 0.6, stagger: 0.12 }, "-=0.45");
  }

  /* Section headers + generic reveals */
  gsap.utils.toArray(".gs-reveal").forEach(function (el) {
    gsap.from(el, {
      y: 36, opacity: 0, duration: 0.8, ease: "power3.out", clearProps: "transform,opacity",
      scrollTrigger: { trigger: el, start: "top 86%" }
    });
  });

  /* Card grids — staggered per container */
  gsap.utils.toArray(".news-grid, .events-teaser, .diaspora, .stats, .upcoming-list").forEach(function (grid) {
    var cards = grid.querySelectorAll(".gs-card, .up-event");
    if (!cards.length) return;
    gsap.from(cards, {
      y: 44, opacity: 0, duration: 0.7, ease: "power3.out", stagger: 0.1, clearProps: "transform,opacity",
      scrollTrigger: { trigger: grid, start: "top 84%" }
    });
  });

  /* Timeline items */
  gsap.utils.toArray(".timeline__item").forEach(function (item, i) {
    gsap.from(item, {
      y: 36, opacity: 0, duration: 0.7, delay: i * 0.05, ease: "power3.out", clearProps: "transform,opacity",
      scrollTrigger: { trigger: item, start: "top 88%" }
    });
  });

  /* Animated counters */
  gsap.utils.toArray("[data-count]").forEach(function (el) {
    var target = parseInt(el.getAttribute("data-count"), 10);
    var obj = { val: 0 };
    gsap.to(obj, {
      val: target, duration: 1.8, ease: "power2.out",
      scrollTrigger: { trigger: el, start: "top 88%" },
      onUpdate: function () {
        el.textContent = Math.round(obj.val).toLocaleString("en-US");
      }
    });
  });

  /* Calendar cells pop-in (events page) */
  var calGrid = document.getElementById("calGrid");
  if (calGrid) {
    // run after calendar.js renders (DOMContentLoaded scripts run in order; defer via rAF)
    requestAnimationFrame(function () {
      var cells = calGrid.querySelectorAll(".cal-day");
      if (!cells.length) return;
      gsap.from(cells, {
        opacity: 0, scale: 0.92, duration: 0.4, ease: "power2.out", clearProps: "transform,opacity",
        stagger: { each: 0.012, from: "start" },
        scrollTrigger: { trigger: calGrid, start: "top 85%" }
      });
    });
  }

  /* Subtle parallax on hero art */
  var art = document.querySelector(".feature-card__art svg");
  if (art) {
    gsap.to(art, {
      yPercent: 6, ease: "none",
      scrollTrigger: { trigger: ".hero", start: "top top", end: "bottom top", scrub: true }
    });
  }
})();
