/**
 * Гірська Лаванда — Main JS
 * Sticky header, scroll animations, gallery lightbox, testimonials slider
 */
(function () {
  'use strict';

  /* =========================================================================
     STICKY HEADER
     ========================================================================= */
  const header = document.getElementById('masthead') || document.querySelector('.site-header');

  function onScroll() {
    if (!header) return;
    const scrolled = window.scrollY > 80;
    document.body.classList.toggle('gl-header-scrolled', scrolled);
    document.body.classList.toggle('gl-header-transparent', !scrolled);
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll(); // init

  /* =========================================================================
     SCROLL ANIMATIONS (Intersection Observer)
     ========================================================================= */
  const animatedEls = document.querySelectorAll('.gl-animate');

  if (animatedEls.length && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );
    animatedEls.forEach(function (el) { observer.observe(el); });
  } else {
    // Fallback — show all
    animatedEls.forEach(function (el) { el.classList.add('is-visible'); });
  }

  /* Hero background entrance animation */
  var heroBg = document.querySelector('.gl-hero__bg');
  if (heroBg) {
    heroBg.classList.add('gl-hero__bg--loaded');
  }

  /* =========================================================================
     TESTIMONIALS SLIDER
     ========================================================================= */
  var testimonialsTrack = document.querySelector('.gl-testimonials__track');
  var testimonialsBtnPrev = document.querySelector('.gl-testimonials__btn--prev');
  var testimonialsBtnNext = document.querySelector('.gl-testimonials__btn--next');
  var testimonialsDotsWrap = document.querySelector('.gl-testimonials__dots');

  if (testimonialsTrack) {
    var tCurrent = 0;
    var tCards = testimonialsTrack.querySelectorAll('.gl-testimonial-card');
    var tTotal = tCards.length;

    function tGetMax() {
      return Math.max(0, tTotal - (window.innerWidth < 768 ? 1 : window.innerWidth < 1024 ? 2 : 3));
    }

    function buildTestimonialDots() {
      if (!testimonialsDotsWrap) return;
      testimonialsDotsWrap.innerHTML = '';
      var max = tGetMax();
      for (var i = 0; i <= max; i++) {
        var dot = document.createElement('button');
        dot.className = 'gl-testimonials__dot' + (i === tCurrent ? ' is-active' : '');
        dot.setAttribute('aria-label', 'Відгук ' + (i + 1));
        testimonialsDotsWrap.appendChild(dot);
        (function (idx) {
          dot.addEventListener('click', function () { tGoTo(idx); });
        })(i);
      }
    }

    function tGoTo(idx) {
      var max = tGetMax();
      // Infinite loop
      if (idx < 0) idx = max;
      if (idx > max) idx = 0;
      tCurrent = idx;
      var cardWidth = tCards[0] ? tCards[0].offsetWidth : 0;
      testimonialsTrack.style.transform = 'translateX(-' + (tCurrent * (cardWidth + 28)) + 'px)';
      if (testimonialsDotsWrap) {
        testimonialsDotsWrap.querySelectorAll('.gl-testimonials__dot').forEach(function (d, i) {
          d.classList.toggle('is-active', i === tCurrent);
        });
      }
    }

    if (testimonialsBtnPrev) testimonialsBtnPrev.addEventListener('click', function () { tGoTo(tCurrent - 1); });
    if (testimonialsBtnNext) testimonialsBtnNext.addEventListener('click', function () { tGoTo(tCurrent + 1); });

    // Auto-play
    function tStartAutoplay() {
      return setInterval(function () { tGoTo(tCurrent + 1); }, 5000);
    }
    var tAutoplay = tStartAutoplay();
    testimonialsTrack.addEventListener('mouseenter', function () { clearInterval(tAutoplay); });
    testimonialsTrack.addEventListener('mouseleave', function () { tAutoplay = tStartAutoplay(); });

    // Touch swipe
    var tTouchStartX = 0;
    testimonialsTrack.addEventListener('touchstart', function (e) { tTouchStartX = e.touches[0].clientX; }, { passive: true });
    testimonialsTrack.addEventListener('touchend', function (e) {
      var diff = tTouchStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) tGoTo(diff > 0 ? tCurrent + 1 : tCurrent - 1);
    });

    // Resize handler
    var tResizeTimer;
    window.addEventListener('resize', function () {
      clearTimeout(tResizeTimer);
      tResizeTimer = setTimeout(function () {
        buildTestimonialDots();
        tGoTo(Math.min(tCurrent, tGetMax()));
      }, 200);
    }, { passive: true });

    buildTestimonialDots();
    tGoTo(0);
  }

  /* =========================================================================
     LIGHTBOX
     ========================================================================= */
  var lightbox     = document.querySelector('.gl-lightbox');
  var lightboxImg  = document.querySelector('.gl-lightbox__img');
  var lightboxClose= document.querySelector('.gl-lightbox__close');
  var lightboxPrev = document.querySelector('.gl-lightbox__prev');
  var lightboxNext = document.querySelector('.gl-lightbox__next');
  var lightboxCounter = document.querySelector('.gl-lightbox__counter');
  var galleryItems = [];
  var lightboxIdx  = 0;

  function updateCounter() {
    if (lightboxCounter && galleryItems.length) {
      lightboxCounter.textContent = (lightboxIdx + 1) + ' / ' + galleryItems.length;
    }
  }

  function openLightbox(items, idx) {
    galleryItems = items;
    lightboxIdx = idx;
    showLightboxImg();
    updateCounter();
    if (lightbox) {
      lightbox.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeLightbox() {
    if (lightbox) {
      lightbox.classList.remove('is-open');
      document.body.style.overflow = '';
    }
  }

  function showLightboxImg() {
    if (!lightboxImg || !galleryItems[lightboxIdx]) return;
    lightboxImg.src = galleryItems[lightboxIdx];
    updateCounter();
  }

  if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
  if (lightboxPrev) lightboxPrev.addEventListener('click', function () {
    lightboxIdx = (lightboxIdx - 1 + galleryItems.length) % galleryItems.length;
    showLightboxImg();
  });
  if (lightboxNext) lightboxNext.addEventListener('click', function () {
    lightboxIdx = (lightboxIdx + 1) % galleryItems.length;
    showLightboxImg();
  });
  if (lightbox) lightbox.addEventListener('click', function (e) { if (e.target === lightbox) closeLightbox(); });
  document.addEventListener('keydown', function (e) {
    if (!lightbox || !lightbox.classList.contains('is-open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft' && lightboxPrev) lightboxPrev.click();
    if (e.key === 'ArrowRight' && lightboxNext) lightboxNext.click();
  });

  // Gallery items (home page preview)
  var galleryEls = document.querySelectorAll('.gl-gallery__item[data-src]');
  if (galleryEls.length) {
    var srcs = Array.from(galleryEls).map(function (el) {
      return el.dataset.src;
    }).filter(Boolean);

    galleryEls.forEach(function (el, i) {
      el.style.cursor = 'zoom-in';
      el.addEventListener('click', function () { openLightbox(srcs, i); });
    });
  }

  // Gallery page — masonry items with lightbox
  var masonryItems = document.querySelectorAll('.gl-gallery-masonry__item[data-src]');
  if (masonryItems.length) {
    function getVisibleSrcs() {
      return Array.from(document.querySelectorAll('.gl-gallery-masonry__item[data-src]:not(.is-hidden)')).map(function (el) {
        return el.dataset.src;
      }).filter(Boolean);
    }

    masonryItems.forEach(function (item) {
      item.addEventListener('click', function () {
        var visibleSrcs = getVisibleSrcs();
        var thisSrc = item.dataset.src;
        var idx = visibleSrcs.indexOf(thisSrc);
        if (idx === -1) idx = 0;
        openLightbox(visibleSrcs, idx);
      });
    });
  }

  /* =========================================================================
     GALLERY PAGE — Filter Tabs
     ========================================================================= */
  var filterTabs = document.querySelectorAll('.gl-gallery-tab');
  var galleryGrid = document.getElementById('gallery-masonry');
  var galleryEmpty = document.getElementById('gallery-empty');

  if (filterTabs.length && galleryGrid) {
    filterTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var filter = tab.dataset.filter;

        // Update active tab
        filterTabs.forEach(function (t) {
          t.classList.remove('is-active');
          t.setAttribute('aria-selected', 'false');
        });
        tab.classList.add('is-active');
        tab.setAttribute('aria-selected', 'true');

        // Filter items
        var items = galleryGrid.querySelectorAll('.gl-gallery-masonry__item');
        var visibleCount = 0;

        items.forEach(function (item) {
          var itemCat = item.dataset.category;
          if (filter === 'all' || itemCat === filter) {
            item.classList.remove('is-hidden');
            visibleCount++;
          } else {
            item.classList.add('is-hidden');
          }
        });

        // Toggle empty state
        if (galleryEmpty) {
          galleryEmpty.hidden = visibleCount > 0;
        }
      });
    });
  }

  // Banya/Chan gallery items — lightbox trigger
  var banyaGalleryItems = document.querySelectorAll('.gl-banya-gallery__item.gl-lightbox-trigger');
  if (banyaGalleryItems.length) {
    var banyaSrcs = Array.from(banyaGalleryItems).map(function (el) {
      return el.dataset.src;
    }).filter(Boolean);

    banyaGalleryItems.forEach(function (el, i) {
      el.addEventListener('click', function (e) {
        e.preventDefault();
        openLightbox(banyaSrcs, i);
      });
    });
  }

  /* =========================================================================
     ROOM BENTO GALLERY — click opens lightbox carousel
     ========================================================================= */
  var bentoGrid = document.querySelector('.gl-room-bento');
  if (bentoGrid) {
    // Parse all full-size URLs from data attribute
    var bentoSrcs = [];
    try {
      bentoSrcs = JSON.parse(bentoGrid.dataset.gallery || '[]');
    } catch (e) { bentoSrcs = []; }

    // Each cell opens lightbox at its index
    var bentoCells = bentoGrid.querySelectorAll('.gl-room-bento__cell');
    bentoCells.forEach(function (cell) {
      cell.addEventListener('click', function (e) {
        // Don't trigger if clicking the back link area
        if (e.target.closest('.gl-room-bento__info a')) return;
        var idx = parseInt(cell.dataset.index, 10) || 0;
        if (bentoSrcs.length) openLightbox(bentoSrcs, idx);
      });
    });

    // "Show all" button opens lightbox at first image
    var showAllBtn = bentoGrid.querySelector('.gl-room-bento__show-all');
    if (showAllBtn) {
      showAllBtn.addEventListener('click', function () {
        if (bentoSrcs.length) openLightbox(bentoSrcs, 0);
      });
    }
  }

  /* =========================================================================
     SMOOTH SCROLL for anchor links
     ========================================================================= */
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var target = document.querySelector(this.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  /* =========================================================================
     HEADER: mark booking button
     ========================================================================= */
  document.querySelectorAll('.ast-nav-menu .menu-item > a, nav .menu-item > a').forEach(function (link) {
    if (/забронювати|book|бронюв/i.test(link.textContent)) {
      link.closest('.menu-item').classList.add('menu-item-book-now');
    }
  });

  /* =========================================================================
     MOBILE MENU — smooth animation helper
     Astra toggles display:none/block which breaks CSS transitions.
     We override with max-height animation via CSS class, and hook into
     Astra's toggle to add/remove body class.
     ========================================================================= */
  var mobileMenuContent = document.querySelector('#ast-mobile-header .ast-mobile-header-content');
  var mobileToggle = document.querySelector('#ast-mobile-header .ast-mobile-menu-trigger-minimal');

  if (mobileMenuContent && mobileToggle) {
    // Override Astra's display:none with our CSS animation approach
    // Force display:block always, control visibility via max-height + opacity
    mobileMenuContent.style.display = 'block';

    // Watch for Astra's class changes on body to detect menu open/close
    var menuObserver = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (mutation.attributeName === 'class') {
          var isOpen = document.body.classList.contains('ast-main-header-nav-open');
          if (isOpen) {
            mobileMenuContent.style.display = 'block';
          }
        }
      });
    });
    menuObserver.observe(document.body, { attributes: true });

    // Also ensure display is always block (Astra may re-set it)
    var contentObserver = new MutationObserver(function () {
      if (mobileMenuContent.style.display === 'none' && 
          document.body.classList.contains('ast-main-header-nav-open')) {
        mobileMenuContent.style.display = 'block';
      }
    });
    contentObserver.observe(mobileMenuContent, { attributes: true, attributeFilter: ['style'] });

    // Close menu on link click (for smooth UX)
    mobileMenuContent.querySelectorAll('.menu-link').forEach(function (link) {
      // Skip links with sub-menus (they toggle sub-menu instead)
      var parent = link.closest('.menu-item');
      if (parent && parent.classList.contains('menu-item-has-children')) return;

      link.addEventListener('click', function () {
        // Trigger Astra's close by clicking the toggle
        if (document.body.classList.contains('ast-main-header-nav-open')) {
          mobileToggle.click();
        }
      });
    });
  }

  /* =========================================================================
     SCROLL-TO-TOP BUTTON
     ========================================================================= */
  var scrollTopBtn = document.getElementById('gl-scroll-top');
  if (scrollTopBtn) {
    var progressCircle = scrollTopBtn.querySelector('.gl-scroll-top__progress-circle');
    var circumference = progressCircle ? 2 * Math.PI * 22 : 0; // r=22

    function updateScrollTop() {
      var scrollY = window.scrollY || window.pageYOffset;
      var docHeight = document.documentElement.scrollHeight - window.innerHeight;
      var scrollPercent = docHeight > 0 ? scrollY / docHeight : 0;

      // Show/hide button (appear after 400px scroll)
      if (scrollY > 400) {
        scrollTopBtn.classList.add('is-visible');
      } else {
        scrollTopBtn.classList.remove('is-visible');
      }

      // Update progress ring
      if (progressCircle && circumference) {
        var offset = circumference - (scrollPercent * circumference);
        progressCircle.style.strokeDashoffset = offset;
      }
    }

    window.addEventListener('scroll', updateScrollTop, { passive: true });
    updateScrollTop();

    scrollTopBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* =========================================================================
     ROOMS CAROUSEL
     ========================================================================= */
  var roomsCarousel = document.getElementById('rooms-carousel');
  var roomsTrack    = roomsCarousel ? roomsCarousel.querySelector('.gl-rooms__track') : null;
  var roomsDotsWrap = document.getElementById('rooms-dots');
  var roomsBtnPrev  = roomsCarousel ? roomsCarousel.querySelector('.gl-rooms__btn--prev') : null;
  var roomsBtnNext  = roomsCarousel ? roomsCarousel.querySelector('.gl-rooms__btn--next') : null;

  if (roomsTrack) {
    var roomCards   = roomsTrack.querySelectorAll('.gl-room-card');
    var roomTotal   = roomCards.length;
    var roomCurrent = 0;

    function getRoomsPerView() {
      return window.innerWidth < 768 ? 1 : window.innerWidth < 1024 ? 2 : 3;
    }

    function getRoomsMax() {
      return Math.max(0, roomTotal - getRoomsPerView());
    }

    function buildRoomDots() {
      if (!roomsDotsWrap) return;
      roomsDotsWrap.innerHTML = '';
      var max = getRoomsMax();
      for (var i = 0; i <= max; i++) {
        var dot = document.createElement('button');
        dot.className = 'gl-rooms__dot' + (i === roomCurrent ? ' is-active' : '');
        dot.setAttribute('aria-label', 'Слайд ' + (i + 1));
        roomsDotsWrap.appendChild(dot);
        (function (idx) {
          dot.addEventListener('click', function () { goToRoom(idx); });
        })(i);
      }
    }

    function goToRoom(idx) {
      roomCurrent = Math.max(0, Math.min(idx, getRoomsMax()));
      var cardWidth = roomCards[0] ? roomCards[0].offsetWidth : 0;
      roomsTrack.style.transform = 'translateX(-' + (roomCurrent * (cardWidth + 28)) + 'px)';

      if (roomsDotsWrap) {
        roomsDotsWrap.querySelectorAll('.gl-rooms__dot').forEach(function (d, i) {
          d.classList.toggle('is-active', i === roomCurrent);
        });
      }
      if (roomsBtnPrev) roomsBtnPrev.disabled = roomCurrent === 0;
      if (roomsBtnNext) roomsBtnNext.disabled = roomCurrent >= getRoomsMax();
    }

    if (roomsBtnPrev) roomsBtnPrev.addEventListener('click', function () { goToRoom(roomCurrent - 1); });
    if (roomsBtnNext) roomsBtnNext.addEventListener('click', function () { goToRoom(roomCurrent + 1); });

    var roomTouchX = 0;
    roomsTrack.addEventListener('touchstart', function (e) { roomTouchX = e.touches[0].clientX; }, { passive: true });
    roomsTrack.addEventListener('touchend', function (e) {
      var diff = roomTouchX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) goToRoom(diff > 0 ? roomCurrent + 1 : roomCurrent - 1);
    });

    var roomResizeTimer;
    window.addEventListener('resize', function () {
      clearTimeout(roomResizeTimer);
      roomResizeTimer = setTimeout(function () {
        buildRoomDots();
        goToRoom(Math.min(roomCurrent, getRoomsMax()));
      }, 200);
    }, { passive: true });

    buildRoomDots();
    goToRoom(0);
  }

  /* =========================================================================
     HERO BOKEH (warm light orbs — amber / gold / green palette)
     Disabled on mobile (< 768px) — saves ~15% CPU on low-end phones.
     Disabled when prefers-reduced-motion is set (accessibility).
     ========================================================================= */
  var fogCanvas = document.querySelector('.gl-hero-fog');
  var _reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var _isMobile      = window.innerWidth < 768;
  if (fogCanvas && fogCanvas.getContext && !_isMobile && !_reducedMotion) {
    var fogCtx = fogCanvas.getContext('2d');
    var bokehOrbs = [], fogRafId;

    // Warm Carpathian palette: gold, amber, soft green, warm white
    var BOKEH_COLORS = [
      [200, 169,  81],  // gold
      [220, 140,  40],  // amber
      [255, 220, 120],  // warm yellow
      [100, 170,  70],  // forest green
      [255, 255, 200],  // warm white
      [180, 120,  50],  // bronze
    ];

    function resizeFog() {
      fogCanvas.width  = fogCanvas.offsetWidth  || window.innerWidth;
      fogCanvas.height = fogCanvas.offsetHeight || window.innerHeight;
    }

    function makeOrb() {
      var col = BOKEH_COLORS[Math.floor(Math.random() * BOKEH_COLORS.length)];
      return {
        x:      Math.random() * fogCanvas.width,
        y:      Math.random() * fogCanvas.height,
        r:      Math.random() * 130 + 40,       // base radius 40–170px
        rAmp:   Math.random() * 20 + 5,         // pulse amplitude
        rFreq:  Math.random() * 0.0006 + 0.0003,// pulse speed
        a:      Math.random() * 0.13 + 0.05,    // opacity 5–18%
        vx:     (Math.random() - 0.5) * 0.18,
        vy:     (Math.random() - 0.5) * 0.12,
        col:    col,
        phase:  Math.random() * Math.PI * 2,    // pulse offset
      };
    }

    function initFog() {
      resizeFog();
      bokehOrbs = [];
      for (var i = 0; i < 22; i++) { bokehOrbs.push(makeOrb()); }
    }

    function drawFog() {
      fogCtx.clearRect(0, 0, fogCanvas.width, fogCanvas.height);
      fogCtx.globalCompositeOperation = 'screen';
      var now = performance.now();
      for (var i = 0; i < bokehOrbs.length; i++) {
        var p   = bokehOrbs[i];
        var rad = p.r + Math.sin(now * p.rFreq + p.phase) * p.rAmp;
        var g   = fogCtx.createRadialGradient(p.x, p.y, 0, p.x, p.y, rad);
        var c   = p.col;
        g.addColorStop(0,   'rgba(' + c[0] + ',' + c[1] + ',' + c[2] + ',' + (p.a * 0.9) + ')');
        g.addColorStop(0.4, 'rgba(' + c[0] + ',' + c[1] + ',' + c[2] + ',' + (p.a * 0.4) + ')');
        g.addColorStop(1,   'rgba(' + c[0] + ',' + c[1] + ',' + c[2] + ',0)');
        fogCtx.beginPath();
        fogCtx.arc(p.x, p.y, rad, 0, 6.283);
        fogCtx.fillStyle = g;
        fogCtx.fill();
        // drift
        p.x += p.vx;
        p.y += p.vy;
        // wrap around edges
        if (p.x < -rad * 2)  p.x = fogCanvas.width  + rad;
        if (p.x > fogCanvas.width  + rad * 2) p.x = -rad;
        if (p.y < -rad * 2)  p.y = fogCanvas.height + rad;
        if (p.y > fogCanvas.height + rad * 2) p.y = -rad;
      }
      fogCtx.globalCompositeOperation = 'source-over';
      fogRafId = requestAnimationFrame(drawFog);
    }

    window.addEventListener('resize', resizeFog, { passive: true });

    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        if (entries[0].isIntersecting) {
          if (!fogRafId) drawFog();
        } else {
          if (fogRafId) { cancelAnimationFrame(fogRafId); fogRafId = null; }
        }
      }).observe(fogCanvas);
    } else {
      drawFog();
    }

    initFog();
  }

  /* =========================================================================
     TEXT REVEAL (word-by-word mask animation)
     ========================================================================= */
  document.querySelectorAll('.gl-text-reveal').forEach(function (el) {
    var text = el.textContent.trim();
    el.setAttribute('aria-label', text);
    el.innerHTML = text.split(/\s+/).map(function (w) {
      return '<span class="gl-tr-w"><span class="gl-tr-i">' + w + '</span></span>';
    }).join(' ');

    if ('IntersectionObserver' in window) {
      var trObs = new IntersectionObserver(function (entries) {
        if (!entries[0].isIntersecting) return;
        el.querySelectorAll('.gl-tr-i').forEach(function (span, i) {
          span.style.transitionDelay = (i * 0.07) + 's';
          span.classList.add('is-visible');
        });
        trObs.unobserve(el);
      }, { threshold: 0.2 });
      trObs.observe(el);
    } else {
      el.querySelectorAll('.gl-tr-i').forEach(function (s) { s.classList.add('is-visible'); });
    }
  });

  /* =========================================================================
     CUSTOM CURSOR + MAGNETIC BUTTONS
     (desktop only — touch devices are excluded by CSS and matchMedia)
     ========================================================================= */
  if (window.matchMedia && window.matchMedia('(pointer: fine)').matches) {
    var glCursor    = document.createElement('div');
    var glCursorRing = document.createElement('div');
    glCursor.className     = 'gl-cursor';
    glCursorRing.className = 'gl-cursor-follower';
    document.body.appendChild(glCursor);
    document.body.appendChild(glCursorRing);
    document.body.classList.add('has-custom-cursor');

    var curX = -100, curY = -100, ringX = -100, ringY = -100;

    document.addEventListener('mousemove', function (e) {
      curX = e.clientX; curY = e.clientY;
      // Bug fix 3: CSS margin handles centering — just translate to exact cursor pos
      glCursor.style.transform = 'translate(' + curX + 'px,' + curY + 'px)';
    });

    (function animateRing() {
      ringX += (curX - ringX) * 0.13;
      ringY += (curY - ringY) * 0.13;
      // Bug fix 3: CSS margin handles centering
      glCursorRing.style.transform = 'translate(' + ringX + 'px,' + ringY + 'px)';
      requestAnimationFrame(animateRing);
    })();

    document.addEventListener('mouseleave', function () {
      glCursor.style.opacity = '0'; glCursorRing.style.opacity = '0';
    });
    document.addEventListener('mouseenter', function () {
      glCursor.style.opacity = '1'; glCursorRing.style.opacity = '1';
    });

    document.addEventListener('mouseover', function (e) {
      if (e.target.closest('a, button, [role="button"], label, input')) {
        glCursor.classList.add('is-active');
        glCursorRing.classList.add('is-active');
      }
    });
    // Bug fix 2: use relatedTarget to avoid flickering when moving between
    // child elements of a link (mouseout fires for every child traversal)
    document.addEventListener('mouseout', function (e) {
      var from = e.target.closest('a, button, [role="button"], label, input');
      var to   = e.relatedTarget ? e.relatedTarget.closest('a, button, [role="button"], label, input') : null;
      if (from && from !== to) {
        glCursor.classList.remove('is-active');
        glCursorRing.classList.remove('is-active');
      }
    });

    // Magnetic pull — all buttons (instant follow, elastic spring-back)
    document.querySelectorAll('.gl-btn').forEach(function (btn) {
      btn.addEventListener('mouseenter', function () {
        // Kill CSS transition for transform — button follows cursor instantly
        btn.style.transition = 'transform 0s linear';
      });

      btn.addEventListener('mousemove', function (e) {
        var r  = btn.getBoundingClientRect();
        var dx = (e.clientX - (r.left + r.width  / 2)) * 0.32;
        var dy = (e.clientY - (r.top  + r.height / 2)) * 0.32;
        btn.style.transform = 'translate(' + dx + 'px,' + dy + 'px) scale(1.04)';
        glCursor.classList.add('is-magnetic');
        glCursorRing.classList.add('is-magnetic');
      });

      btn.addEventListener('mouseleave', function () {
        // Elastic spring-back
        btn.style.transition = 'transform 0.55s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        btn.style.transform = '';
        setTimeout(function () { btn.style.transition = ''; }, 600);
        glCursor.classList.remove('is-magnetic');
        glCursorRing.classList.remove('is-magnetic');
      });
    });
  }

  /* =========================================================================
     LIQUID RIPPLE — click wave from cursor origin (all .gl-btn)
     ========================================================================= */
  document.querySelectorAll('.gl-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      var r    = btn.getBoundingClientRect();
      var size = Math.max(r.width, r.height) * 2.2;
      var x    = e.clientX - r.left - size / 2;
      var y    = e.clientY - r.top  - size / 2;

      var ripple = document.createElement('span');
      ripple.className = 'gl-ripple';
      ripple.style.cssText = 'width:' + size + 'px;height:' + size + 'px;top:' + y + 'px;left:' + x + 'px;';

      // Insert first so text DOM nodes paint on top
      btn.insertBefore(ripple, btn.firstChild);
      setTimeout(function () { ripple.remove(); }, 750);
    });
  });

  /* =========================================================================
     CONTACT FAB — toggle open/close
     ========================================================================= */
  var fabEl     = document.getElementById('gl-contact-fab');
  var fabToggle = document.getElementById('gl-contact-fab-toggle');
  if (fabEl && fabToggle) {
    fabToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      fabEl.classList.toggle('is-open');
    });
    document.addEventListener('click', function (e) {
      if (fabEl.classList.contains('is-open') && !fabEl.contains(e.target)) {
        fabEl.classList.remove('is-open');
      }
    });
    // Close on Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') fabEl.classList.remove('is-open');
    });
  }

})();
