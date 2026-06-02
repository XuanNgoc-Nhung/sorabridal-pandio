(function () {
  'use strict';

  /* ─── Scroll Reveal (bidirectional) ─────────────────── */
  const srEls = document.querySelectorAll('.sr');

  // stagger index within siblings
  srEls.forEach(el => {
    const raw = Array.from(el.classList).find(c => c.match(/^d\d$/));
    if (raw) {
      const n = parseInt(raw.replace('d',''));
      el.style.transitionDelay = (n * 0.07) + 's';
    }
  });

  const srObs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      const el = entry.target;
      if (entry.isIntersecting) {
        el.classList.add('visible');
        el.classList.remove('hidden-below');
      } else {
        if (el.classList.contains('sr-once') && el.classList.contains('visible')) {
          return;
        }
        // Determine if element is above viewport (user scrolled past) → fade down
        const rect = entry.boundingClientRect;
        if (rect.top < 0) {
          el.classList.add('hidden-below');
          el.classList.remove('visible');
        } else {
          // Element is below viewport (not yet reached) → reset to fade-up state
          el.classList.remove('visible');
          el.classList.remove('hidden-below');
        }
      }
    });
  }, {
    threshold: 0.12,
    rootMargin: '0px 0px -40px 0px'
  });

  srEls.forEach(el => srObs.observe(el));

  /* ─── Header scroll shadow ──────────────────────────── */
  const header = document.getElementById('mainHeader');
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });
  }

  /* ─── Mobile Nav ─────────────────────────────────────── */
  const menuToggle = document.getElementById('menuToggle');
  const navLinks   = document.getElementById('navLinks');
  if (menuToggle && navLinks) {
    const menuIcon = menuToggle.querySelector('i');

    menuToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      navLinks.classList.toggle('open');
      menuIcon.className = navLinks.classList.contains('open') ? 'fas fa-times' : 'fas fa-bars';
    });

    navLinks.querySelectorAll('.dropdown > a').forEach(a => {
      a.addEventListener('click', e => {
        if (window.innerWidth > 768) return;
        e.preventDefault();
        a.parentElement.classList.toggle('open');
      });
    });

    document.addEventListener('click', e => {
      if (!navLinks.contains(e.target) && !menuToggle.contains(e.target)) {
        navLinks.classList.remove('open');
        menuIcon.className = 'fas fa-bars';
      }
    });
  }

  /* ─── Tabs ───────────────────────────────────────────── */
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const sec = btn.closest('section');
      sec.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      sec.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
      document.getElementById(btn.dataset.tab).classList.add('active');
    });
  });

  /* ─── Home Banner Slider (trang chủ) ─────────────────── */
  const bannerTrack = document.getElementById('homeBannerTrack');
  const bannerDots  = document.getElementById('homeBannerDots');
  const bannerPrev  = document.getElementById('homeBannerPrev');
  const bannerNext  = document.getElementById('homeBannerNext');
  const bannerSlider = document.getElementById('homeBannerSlider');

  if (bannerTrack && bannerDots && bannerPrev && bannerNext && bannerSlider) {
    const bannerSlides = bannerTrack.querySelectorAll('.home-banner-slide');
    const bannerTotal  = bannerSlides.length;
    let bannerCur      = 0;
    let bannerTimer;
    const BANNER_AUTO_MS = 5000;
    const bannerReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (bannerReducedMotion) bannerSlider.classList.add('is-reduced-motion');

    function bannerActivateSlide(idx) {
      bannerSlides.forEach((slide, i) => {
        const active = i === idx;
        slide.classList.toggle('is-active', active);
        if (active && !bannerReducedMotion) {
          slide.querySelectorAll('.home-banner-part').forEach(part => {
            part.style.animation = 'none';
            void part.offsetHeight;
            part.style.animation = '';
          });
          const img = slide.querySelector('.home-banner-media img');
          if (img) {
            img.style.animation = 'none';
            void img.offsetHeight;
            img.style.animation = '';
          }
        }
      });
    }

    bannerSlides.forEach((_, i) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'home-banner-dot' + (i === 0 ? ' active' : '');
      dot.setAttribute('role', 'tab');
      dot.setAttribute('aria-label', 'Banner ' + (i + 1));
      dot.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
      dot.addEventListener('click', () => bannerGoTo(i, true));
      bannerDots.appendChild(dot);
    });

    function bannerUpdateDots() {
      bannerDots.querySelectorAll('.home-banner-dot').forEach((d, i) => {
        const active = i === bannerCur;
        d.classList.toggle('active', active);
        d.setAttribute('aria-selected', active ? 'true' : 'false');
      });
    }

    function bannerGoTo(idx, userTriggered) {
      if (bannerTotal < 1) return;
      bannerCur = ((idx % bannerTotal) + bannerTotal) % bannerTotal;
      bannerTrack.style.transform = 'translateX(-' + (bannerCur * 100) + '%)';
      bannerActivateSlide(bannerCur);
      bannerUpdateDots();
      if (userTriggered) bannerRestartAuto();
    }

    bannerPrev.addEventListener('click', () => bannerGoTo(bannerCur - 1, true));
    bannerNext.addEventListener('click', () => bannerGoTo(bannerCur + 1, true));

    function bannerStartAuto() {
      if (bannerReducedMotion || bannerTotal < 2) return;
      bannerTimer = setInterval(() => bannerGoTo(bannerCur + 1, false), BANNER_AUTO_MS);
    }

    function bannerStopAuto() {
      clearInterval(bannerTimer);
    }

    function bannerRestartAuto() {
      bannerStopAuto();
      bannerStartAuto();
    }

    bannerSlider.addEventListener('mouseenter', bannerStopAuto);
    bannerSlider.addEventListener('mouseleave', bannerStartAuto);
    bannerSlider.addEventListener('focusin', bannerStopAuto);
    bannerSlider.addEventListener('focusout', (e) => {
      if (!bannerSlider.contains(e.relatedTarget)) bannerStartAuto();
    });

    let bannerTx = 0;
    bannerTrack.addEventListener('touchstart', e => {
      bannerTx = e.touches[0].clientX;
    }, { passive: true });
    bannerTrack.addEventListener('touchend', e => {
      const dx = e.changedTouches[0].clientX - bannerTx;
      if (Math.abs(dx) > 50) bannerGoTo(bannerCur + (dx < 0 ? 1 : -1), true);
    }, { passive: true });

    if (bannerTotal > 0) bannerStartAuto();
  }

  /* ─── Outer Collection Carousel (trang chủ) ──────────── */
  const outerTrack = document.getElementById('outerTrack');
  const dotsWrap   = document.getElementById('outerDots');
  const outerPrev  = document.getElementById('outerPrev');
  const outerNext  = document.getElementById('outerNext');
  if (outerTrack && dotsWrap && outerPrev && outerNext) {
    const outerSlides = outerTrack.querySelectorAll('.col-slide');
    const total       = outerSlides.length;
    let outerCur      = 0;
    let autoTimer;

    outerSlides.forEach((_, i) => {
      const d = document.createElement('div');
      d.className = 'odot' + (i === 0 ? ' active' : '');
      d.addEventListener('click', () => outerGoTo(i));
      dotsWrap.appendChild(d);
    });

    function outerUpdateDots() {
      dotsWrap.querySelectorAll('.odot').forEach((d, i) => d.classList.toggle('active', i === outerCur));
    }

    function outerGoTo(idx) {
      if (total < 1) return;
      outerCur = ((idx % total) + total) % total;
      outerTrack.style.transform = `translateX(-${outerCur * 100}%)`;
      outerUpdateDots();
    }

    outerPrev.addEventListener('click', () => outerGoTo(outerCur - 1));
    outerNext.addEventListener('click', () => outerGoTo(outerCur + 1));

    function startAuto() { autoTimer = setInterval(() => outerGoTo(outerCur + 1), 7500); }
    function stopAuto()  { clearInterval(autoTimer); }

    const outerWrap = outerTrack.closest('.outer-track-wrap');
    if (outerWrap) {
      outerWrap.addEventListener('mouseenter', stopAuto);
      outerWrap.addEventListener('mouseleave', startAuto);
    }
    if (total > 0) startAuto();

    let tx = 0;
    outerTrack.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
    outerTrack.addEventListener('touchend',   e => {
      const dx = e.changedTouches[0].clientX - tx;
      if (Math.abs(dx) > 50) outerGoTo(outerCur + (dx < 0 ? 1 : -1));
    }, { passive: true });
  }

  /* ─── Inner Image Carousels ──────────────────────────── */
  const innerStates = {};

  const INNER_MOBILE_BP = 768;

  function getInnerGap() {
    return window.innerWidth < INNER_MOBILE_BP ? 8 : 16;
  }

  function getInnerVisCount() {
    return 3;
  }

  function initInner(id) {
    const track = document.getElementById('it-' + id);
    if (!track) return;
    const slides  = track.querySelectorAll('.in-slide');
    const wrap    = document.getElementById('iw-' + id);
    const total   = slides.length;
    const vis     = getInnerVisCount();
    const max     = Math.max(0, total - vis);
    innerStates[id] = { cur: Math.min(innerStates[id]?.cur ?? 0, max), vis, max, track };

    updateInner(id);
  }

  function getInnerSlideW(id) {
    const track = document.getElementById('it-' + id);
    const wrap  = track.parentElement;
    const vis   = getInnerVisCount();
    const gap   = getInnerGap();
    return (wrap.offsetWidth - (vis - 1) * gap) / vis;
  }

  function updateInner(id) {
    const state = innerStates[id];
    if (!state) return;
    const gap = getInnerGap();
    const w = getInnerSlideW(id);
    state.track.style.transform = `translateX(-${state.cur * (w + gap)}px)`;
  }

  function innerGoTo(id, idx) {
    const state = innerStates[id];
    if (!state) return;
    state.cur = Math.max(0, Math.min(idx, state.max));
    updateInner(id);
  }

  // Init all inner carousels
  for (let i = 0; i < 4; i++) initInner(i);

  // Nav buttons
  document.querySelectorAll('.in-nav').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      const id  = parseInt(btn.dataset.id);
      const dir = parseInt(btn.dataset.dir);
      innerGoTo(id, (innerStates[id]?.cur || 0) + dir);
    });
  });

  // Recalc on resize
  window.addEventListener('resize', () => {
    for (let i = 0; i < 4; i++) initInner(i);
  });

  /* ─── Video Modal (trang chủ) ─────────────────────────── */
  const playBtn    = document.getElementById('playBtn');
  const videoModal = document.getElementById('videoModal');
  const modalClose = document.getElementById('modalClose');
  const videoFrame = document.getElementById('videoFrame');
  const videoId    = 'dQw4w9WgXcQ';

  if (playBtn && videoModal && modalClose && videoFrame) {
    playBtn.addEventListener('click', () => {
      videoFrame.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
      videoModal.classList.add('open');
      document.body.style.overflow = 'hidden';
    });

    function closeModal() {
      videoModal.classList.remove('open');
      videoFrame.src = '';
      document.body.style.overflow = '';
    }

    modalClose.addEventListener('click', closeModal);
    videoModal.addEventListener('click', e => { if (e.target === videoModal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
  }

  /* ─── Before/After Slider ───────────────────────────── */
  function initBASlider(wrapId, afterId, handleId) {
    const wrap   = document.getElementById(wrapId);
    const after  = document.getElementById(afterId);
    const handle = document.getElementById(handleId);
    if (!wrap || !after || !handle) return;
    let dragging = false;

    function setPos(x) {
      const rect = wrap.getBoundingClientRect();
      let pct = ((x - rect.left) / rect.width) * 100;
      pct = Math.max(2, Math.min(98, pct));
      after.style.clipPath = `inset(0 ${100 - pct}% 0 0)`;
      handle.style.left = pct + '%';
    }

    wrap.addEventListener('mousedown', e => { dragging = true; setPos(e.clientX); });
    window.addEventListener('mousemove', e => { if (dragging) setPos(e.clientX); });
    window.addEventListener('mouseup', () => { dragging = false; });

    wrap.addEventListener('touchstart', e => { dragging = true; setPos(e.touches[0].clientX); }, { passive: true });
    window.addEventListener('touchmove', e => { if (dragging) setPos(e.touches[0].clientX); }, { passive: true });
    window.addEventListener('touchend', () => { dragging = false; });
  }

  initBASlider('ba1', 'ba1-after', 'ba1-handle');
  initBASlider('ba2', 'ba2-after', 'ba2-handle');
  initBASlider('ba3', 'ba3-after', 'ba3-handle');
  initBASlider('ba4', 'ba4-after', 'ba4-handle');

  /* ─── Animate.css on scroll (WOW-style) ─────────────── */
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('.reveal-anim').forEach(el => {
    if (reduceMotion) {
      el.style.opacity = '1';
      return;
    }
    const name = el.dataset.animate || 'fadeInUp';
    const ioAnim = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        if (el.classList.contains('anim-show')) return;
        el.classList.add('animate__animated', 'animate__' + name, 'anim-show');
        if (el.classList.contains('reveal-once')) {
          el.addEventListener('animationend', () => {
            el.style.opacity = '1';
          }, { once: true });
        }
        ioAnim.unobserve(el);
      });
    }, { threshold: 0.14, rootMargin: '0px 0px -32px 0px' });
    ioAnim.observe(el);
  });

  /* ─── Count-up numbers ──────────────────────────────── */
  function formatCountValue(val, format, decimals) {
    if (format === 'k') {
      if (val < 1) return '0';
      const k = val / 1000;
      const s = k.toFixed(1).replace(/\.0$/, '');
      return s + 'K';
    }
    if (format === 'm') {
      const m = val / 1e6;
      if (m < 0.05) return '0';
      const s = m.toFixed(1).replace(/\.0$/, '');
      return s + 'M';
    }
    if (decimals > 0) return val.toFixed(decimals);
    return String(Math.round(val));
  }

  function runCountUp(el) {
    const target = parseFloat(el.dataset.count);
    if (Number.isNaN(target)) return;
    const duration = (parseFloat(el.dataset.duration) || 1.5) * 1000;
    const format = el.dataset.format || 'number';
    const decimals = parseInt(el.dataset.decimals || '0', 10);
    const suffix = el.dataset.suffix || '';
    const prefix = el.dataset.prefix || '';
    const start = performance.now();

    function easeOutQuart(t) {
      return 1 - Math.pow(1 - t, 4);
    }

    function frame(now) {
      const t = Math.min(1, (now - start) / duration);
      const eased = easeOutQuart(t);
      const cur = target * eased;
      el.textContent = prefix + formatCountValue(cur, format, decimals) + suffix;
      if (t < 1) {
        requestAnimationFrame(frame);
      } else {
        el.textContent = prefix + formatCountValue(target, format, decimals) + suffix;
      }
    }

    if (reduceMotion) {
      el.textContent = prefix + formatCountValue(target, format, decimals) + suffix;
      return;
    }
    requestAnimationFrame(frame);
  }

  document.querySelectorAll('.count-up').forEach(el => {
    const ioCount = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        if (el.dataset.countDone === '1') return;
        el.dataset.countDone = '1';
        runCountUp(el);
        ioCount.unobserve(el);
      });
    }, { threshold: 0.2, rootMargin: '0px' });
    ioCount.observe(el);
  });

  /* ─── Typing line (hero) ────────────────────────────── */
  const typedLine = document.querySelector('.hero-typed-line');
  const typingEl = typedLine && typedLine.querySelector('.typing-text');
  if (typingEl) {
    const full = typingEl.dataset.typingText || '';
    const speed = parseInt(typingEl.dataset.typingSpeed || '40', 10);
    if (reduceMotion) {
      typingEl.textContent = full;
    } else {
      typingEl.textContent = '';
      let started = false;
      function typeNext(i) {
        if (i > full.length) return;
        typingEl.textContent = full.slice(0, i);
        if (i < full.length) setTimeout(() => typeNext(i + 1), speed);
      }
      const tIo = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (!entry.isIntersecting || started) return;
          started = true;
          typeNext(0);
          tIo.disconnect();
        });
      }, { threshold: 0.15 });
      tIo.observe(typedLine);
    }
  }

})();

/* ─── Subpage routing ───────────────────────────────── */
function openPage(id) {
  document.querySelectorAll('.page-overlay').forEach(p => p.classList.remove('open'));
  const page = document.getElementById(id);
  if (page) {
    page.classList.add('open');
    page.scrollTop = 0;
    document.body.style.overflow = 'hidden';
  }
  // close mobile nav
  const navLinks = document.getElementById('navLinks');
  if (navLinks) navLinks.classList.remove('open');
}

function closePage(id) {
  const page = document.getElementById(id);
  if (page) page.classList.remove('open');
  document.body.style.overflow = '';
}

/* ─── FAQ accordion ─────────────────────────────────── */
function toggleFaq(el) {
  const item = el.parentElement;
  item.classList.toggle('open');
}
