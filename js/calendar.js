/* TamilNewsWire — Community Event Calendar */
(function () {
  "use strict";

  var TODAY = new Date(2026, 5, 11); // demo "today": 11 June 2026

  var MONTHS_TA = ["ஜனவரி", "பெப்ரவரி", "மார்ச்", "ஏப்ரல்", "மே", "ஜூன்",
    "ஜூலை", "ஓகஸ்ட்", "செப்டெம்பர்", "ஒக்டோபர்", "நவம்பர்", "டிசம்பர்"];
  var MONTHS_EN = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE",
    "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];
  var DAYS_TA = ["ஞாயிறு", "திங்கள்", "செவ்வாய்", "புதன்", "வியாழன்", "வெள்ளி", "சனி"];
  var DAYS_TA_SHORT = ["ஞாயி", "திங்", "செவ்", "புத", "வியா", "வெள்", "சனி"];

  var REGION_LABEL = {
    canada: "🇨🇦 கனடா", uk: "🇬🇧 ஐக்கிய இராச்சியம்", australia: "🇦🇺 அவுஸ்திரேலியா",
    europe: "🇪🇺 ஐரோப்பா", srilanka: "🇱🇰 இலங்கை"
  };

  /* Sample moderated events (date: YYYY-MM-DD) */
  var EVENTS = [
    { date: "2026-06-13", title: "முத்தமிழ் விழா 2026 — இசை, நடனம், நாடகம்", org: "ரொறன்ரோ தமிழ்ச் சங்கம்", city: "ரொறன்ரோ", region: "canada", cat: "கலாசாரம்", time: "மாலை 5:00" },
    { date: "2026-06-13", title: "தமிழ் மரபுத் திங்கள் — குடும்ப தின நிகழ்வு", org: "ஸ்கார்பரோ சமூக மையம்", city: "ஸ்கார்பரோ", region: "canada", cat: "சமூகம்", time: "காலை 10:00" },
    { date: "2026-06-14", title: "பரதநாட்டிய அரங்கேற்றம் — செல்வி அபிராமி", org: "லண்டன் நாட்டியாலயா", city: "லண்டன்", region: "uk", cat: "நடனம்", time: "மாலை 4:30" },
    { date: "2026-06-17", title: "தமிழ் மொழிப் பாடசாலை ஆண்டு பரிசளிப்பு விழா", org: "பாரிஸ் தமிழ்க் கல்விக் கழகம்", city: "பாரிஸ்", region: "europe", cat: "கல்வி", time: "மாலை 6:00" },
    { date: "2026-06-20", title: "கர்நாடக இசை மாலை — யுவ கலைஞர்கள் அரங்கம்", org: "சிட்னி தமிழ் இசைக் கழகம்", city: "சிட்னி", region: "australia", cat: "இசை", time: "மாலை 7:00" },
    { date: "2026-06-21", title: "சைவ உணவுத் திருவிழா & சந்தை", org: "சூரிச் தமிழ் ஒன்றியம்", city: "சூரிச்", region: "europe", cat: "சமூகம்", time: "காலை 11:00" },
    { date: "2026-06-21", title: "யோகா & தியான தினம் — இலவச பயிற்சி", org: "மெல்பேர்ண் இந்து மன்றம்", city: "மெல்பேர்ண்", region: "australia", cat: "சமயம்", time: "காலை 7:30" },
    { date: "2026-06-26", title: "யாழ் இசை நுட்பப் பட்டறை — மாணவர்களுக்கான வகுப்பு", org: "யாழ் கலை அகாடமி", city: "யாழ்ப்பாணம்", region: "srilanka", cat: "இசை", time: "பிற்பகல் 2:00" },
    { date: "2026-06-27", title: "இளையோர் தமிழ்ப் பேச்சுப் போட்டி — இறுதிச் சுற்று", org: "மெல்பேர்ண் தமிழ்ச் சங்கம்", city: "மெல்பேர்ண்", region: "australia", cat: "கல்வி", time: "காலை 9:30" },
    { date: "2026-06-28", title: "கிரிக்கெட் லீக் இறுதிப் போட்டி & குடும்ப விழா", org: "லண்டன் தமிழ் விளையாட்டுக் கழகம்", city: "லண்டன்", region: "uk", cat: "விளையாட்டு", time: "காலை 10:00" },
    { date: "2026-07-04", title: "கோடைக் கலை முகாம் — சிறுவர்களுக்கான பதிவு", org: "மொன்றியால் தமிழ் மன்றம்", city: "மொன்றியால்", region: "canada", cat: "கலாசாரம்", time: "காலை 9:00" },
    { date: "2026-07-11", title: "ஆடி அமாவாசை சிறப்பு வழிபாடு", org: "ஈலிங் முருகன் ஆலயம்", city: "லண்டன்", region: "uk", cat: "சமயம்", time: "காலை 6:00" },
    { date: "2026-07-18", title: "புலம்பெயர் எழுத்தாளர் சந்திப்பு & நூல் வெளியீடு", org: "கனடா தமிழ் இலக்கிய வட்டம்", city: "ரொறன்ரோ", region: "canada", cat: "கலாசாரம்", time: "மாலை 5:30" }
  ];

  var state = {
    year: TODAY.getFullYear(),
    month: TODAY.getMonth(),       // 0-based
    region: "all",
    selected: iso(TODAY)
  };

  function iso(d) {
    return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate());
  }
  function pad(n) { return (n < 10 ? "0" : "") + n; }

  function eventsOn(dateStr) {
    return EVENTS.filter(function (e) {
      return e.date === dateStr && (state.region === "all" || e.region === state.region);
    });
  }

  /* ---------- Calendar grid ---------- */
  var grid = document.getElementById("calGrid");
  var monthTa = document.getElementById("calMonthTa");
  var monthEn = document.getElementById("calMonthEn");

  function renderCalendar() {
    monthTa.textContent = MONTHS_TA[state.month] + " " + state.year;
    monthEn.textContent = MONTHS_EN[state.month] + " " + state.year;

    var first = new Date(state.year, state.month, 1);
    var startDow = first.getDay();
    var daysInMonth = new Date(state.year, state.month + 1, 0).getDate();
    var daysPrev = new Date(state.year, state.month, 0).getDate();
    var todayStr = iso(TODAY);

    var cells = [];
    var totalCells = Math.ceil((startDow + daysInMonth) / 7) * 7;

    for (var i = 0; i < totalCells; i++) {
      var dayNum, inMonth = true, d;
      if (i < startDow) {
        dayNum = daysPrev - startDow + 1 + i; inMonth = false;
        d = new Date(state.year, state.month - 1, dayNum);
      } else if (i >= startDow + daysInMonth) {
        dayNum = i - (startDow + daysInMonth) + 1; inMonth = false;
        d = new Date(state.year, state.month + 1, dayNum);
      } else {
        dayNum = i - startDow + 1;
        d = new Date(state.year, state.month, dayNum);
      }
      var dStr = iso(d);
      var evts = inMonth ? eventsOn(dStr) : [];
      var cls = "cal-day";
      if (!inMonth) cls += " cal-day--out";
      if (dStr === todayStr) cls += " cal-day--today";
      if (evts.length) cls += " cal-day--event";
      if (dStr === state.selected && inMonth) cls += " is-selected";

      var pills = "";
      if (evts.length) {
        pills = '<div class="cal-day__pills">' +
          evts.slice(0, 2).map(function (e, idx) {
            return '<span class="cal-pill' + (idx === 1 ? " cal-pill--alt" : "") + '">' + esc(e.title) + "</span>";
          }).join("") +
          (evts.length > 2 ? '<span class="cal-pill cal-pill--alt">+' + (evts.length - 2) + " மேலும்</span>" : "") +
          "</div>" +
          '<div class="cal-day__dots">' + evts.slice(0, 3).map(function () { return "<i></i>"; }).join("") + "</div>";
      }

      cells.push(
        '<button type="button" class="' + cls + '" data-date="' + dStr + '"' +
        (inMonth ? "" : " tabindex=\"-1\"") +
        ' aria-label="' + dStr + (evts.length ? ", " + evts.length + " நிகழ்வுகள்" : "") + '">' +
        '<span class="n">' + d.getDate() + "</span>" + pills +
        "</button>"
      );
    }
    grid.innerHTML = cells.join("");
  }

  function esc(s) {
    return s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }

  /* ---------- Day panel ---------- */
  var panelDate = document.getElementById("panelDate");
  var panelTitle = document.getElementById("panelTitle");
  var panelCount = document.getElementById("panelCount");
  var panelList = document.getElementById("panelList");

  function renderPanel() {
    var parts = state.selected.split("-");
    var d = new Date(+parts[0], +parts[1] - 1, +parts[2]);
    panelDate.textContent = d.getDate();
    panelTitle.textContent = MONTHS_TA[d.getMonth()] + " " + d.getDate() + ", " + DAYS_TA[d.getDay()];

    var evts = eventsOn(state.selected);
    panelCount.textContent = evts.length
      ? evts.length + " நிகழ்வு(கள்) பதிவாகியுள்ளன"
      : "இந்த நாளில் நிகழ்வுகள் இல்லை";

    if (!evts.length) {
      panelList.innerHTML = '<div class="day-panel__empty">இந்த நாளுக்குப் பதிவான நிகழ்வுகள் இல்லை.<br/>வேறு நாளைத் தேர்ந்தெடுக்கவும்.</div>';
      return;
    }
    panelList.innerHTML = evts.map(function (e) {
      return '<article class="day-event">' +
        "<h4>" + esc(e.title) + "</h4>" +
        '<div class="day-event__meta">' +
        "<span>🕓 " + esc(e.time) + "</span>" +
        "<span>📍 " + esc(e.city) + "</span>" +
        "<span>🏛 " + esc(e.org) + "</span>" +
        "</div>" +
        '<span class="badge badge--blue">' + esc(e.cat) + "</span>" +
        "</article>";
    }).join("");

    if (typeof gsap !== "undefined") {
      gsap.from(panelList.children, { y: 14, opacity: 0, duration: 0.35, stagger: 0.06, ease: "power2.out", clearProps: "all" });
    }
  }

  /* ---------- Upcoming list ---------- */
  var upList = document.getElementById("upcomingList");
  var upCount = document.getElementById("upcomingCount");

  function renderUpcoming() {
    var horizon = new Date(TODAY); horizon.setDate(horizon.getDate() + 40);
    var items = EVENTS.filter(function (e) {
      var d = new Date(e.date + "T00:00:00");
      return d >= TODAY && d <= horizon && (state.region === "all" || e.region === state.region);
    }).sort(function (a, b) { return a.date < b.date ? -1 : 1; });

    upCount.textContent = items.length + " நிகழ்வுகள் காட்டப்படுகின்றன";

    if (!items.length) {
      upList.innerHTML = '<div class="day-panel__empty" style="grid-column:1/-1">தேர்ந்த பிராந்தியத்தில் வரவிருக்கும் நிகழ்வுகள் இல்லை.</div>';
      return;
    }

    var pin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z"/><circle cx="12" cy="10" r="2.6"/></svg>';
    var clock = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>';
    var check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>';

    upList.innerHTML = items.map(function (e) {
      var p = e.date.split("-");
      var d = new Date(+p[0], +p[1] - 1, +p[2]);
      return '<article class="up-event">' +
        '<div class="up-event__date">' +
        '<span class="d">' + d.getDate() + "</span>" +
        '<span class="m">' + MONTHS_TA[d.getMonth()] + "</span>" +
        '<span class="w">' + DAYS_TA_SHORT[d.getDay()] + "</span>" +
        "</div>" +
        '<div class="up-event__body">' +
        "<h3><a href=\"#calendar\" data-goto=\"" + e.date + '">' + esc(e.title) + "</a></h3>" +
        '<div class="up-event__meta">' +
        "<span>" + clock + esc(e.time) + "</span>" +
        "<span>" + pin + esc(e.city) + " · " + (REGION_LABEL[e.region] || "") + "</span>" +
        "</div>" +
        '<div class="up-event__foot">' +
        '<span class="badge badge--blue">' + esc(e.cat) + "</span>" +
        '<span class="verified">' + check + " சரிபார்க்கப்பட்டது</span>" +
        "</div></div></article>";
    }).join("");
  }

  /* ---------- Interactions ---------- */
  grid.addEventListener("click", function (e) {
    var cell = e.target.closest(".cal-day");
    if (!cell || cell.classList.contains("cal-day--out")) return;
    state.selected = cell.getAttribute("data-date");
    renderCalendar();
    renderPanel();
  });

  upList.addEventListener("click", function (e) {
    var link = e.target.closest("[data-goto]");
    if (!link) return;
    var dateStr = link.getAttribute("data-goto");
    var p = dateStr.split("-");
    state.year = +p[0];
    state.month = +p[1] - 1;
    state.selected = dateStr;
    renderCalendar();
    renderPanel();
  });

  document.getElementById("calPrev").addEventListener("click", function () { shiftMonth(-1); });
  document.getElementById("calNext").addEventListener("click", function () { shiftMonth(1); });

  function shiftMonth(delta) {
    state.month += delta;
    if (state.month < 0) { state.month = 11; state.year--; }
    if (state.month > 11) { state.month = 0; state.year++; }
    renderCalendar();
    if (typeof gsap !== "undefined") {
      gsap.from(grid.children, { opacity: 0, scale: 0.94, duration: 0.3, stagger: 0.008, ease: "power2.out", clearProps: "all" });
    }
  }

  /* Region filters */
  var filterBar = document.getElementById("filterBar");
  filterBar.addEventListener("click", function (e) {
    var chip = e.target.closest(".chip");
    if (!chip) return;
    filterBar.querySelectorAll(".chip").forEach(function (c) { c.classList.remove("is-active"); });
    chip.classList.add("is-active");
    state.region = chip.getAttribute("data-region");
    renderCalendar();
    renderPanel();
    renderUpcoming();
  });

  /* ---------- Init ---------- */
  renderCalendar();
  renderPanel();
  renderUpcoming();
})();
