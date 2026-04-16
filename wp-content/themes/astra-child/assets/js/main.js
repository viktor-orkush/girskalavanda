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

  function updateHeaderHeight() {
    var h = header ? header.offsetHeight : 0;
    document.documentElement.style.setProperty('--gl-header-height', h + 'px');
  }

  updateHeaderHeight();
  window.addEventListener('resize', updateHeaderHeight, { passive: true });

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
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            // Stagger siblings that appear together
            var el = entry.target;
            var siblings = el.parentElement.querySelectorAll('.gl-animate:not(.is-visible)');
            var staggerBase = 0;
            siblings.forEach(function (sib) {
              if (sib.getBoundingClientRect().top < window.innerHeight) {
                if (!sib.style.transitionDelay && !sib.className.match(/gl-animate--delay-/)) {
                  sib.style.transitionDelay = staggerBase + 'ms';
                  staggerBase += 80;
                }
                sib.classList.add('is-visible');
                observer.unobserve(sib);
              }
            });
            el.classList.add('is-visible');
            observer.unobserve(el);
          }
        });
      },
      { threshold: 0.05, rootMargin: '0px 0px -20px 0px' }
    );
    animatedEls.forEach(function (el) { observer.observe(el); });

    // Safety net: reveal any still-hidden elements after 3s
    setTimeout(function () {
      document.querySelectorAll('.gl-animate:not(.is-visible)').forEach(function (el) {
        el.classList.add('is-visible');
      });
    }, 3000);
  } else {
    // Fallback — show all
    animatedEls.forEach(function (el) { el.classList.add('is-visible'); });
  }

  /* =========================================================================
     HERO PARALLAX (Optimized with requestAnimationFrame)
     ========================================================================= */
  const heroBg = document.querySelector('.gl-hero__bg');
  if (heroBg && window.innerWidth > 768) {
    heroBg.classList.add('gl-hero__bg--loaded');
    
    let scrollY = 0;
    let ticking = false;

    function updateParallax() {
      heroBg.style.transform = 'scale(1.05) translateY(' + (scrollY * 0.3) + 'px)';
      ticking = false;
    }

    window.addEventListener('scroll', function () {
      scrollY = window.scrollY;
      if (!ticking) {
        window.requestAnimationFrame(updateParallax);
        ticking = true;
      }
    }, { passive: true });
  }

  /* =========================================================================
     TESTIMONIALS SLIDER
     ========================================================================= */
  const track = document.querySelector('.gl-testimonials__track');
  const dots  = document.querySelectorAll('.gl-testimonials__dot');
  const btnPrev = document.querySelector('.gl-testimonials__btn--prev');
  const btnNext = document.querySelector('.gl-testimonials__btn--next');

  if (track) {
    let current = 0;
    const cards = track.querySelectorAll('.gl-testimonial-card');
    const total = cards.length;

    function getMax() {
      return Math.max(0, total - 1); // always 1 card per step — all dots reachable
    }

    function goTo(idx) {
      current = Math.max(0, Math.min(idx, getMax()));
      const cardWidth = cards[0] ? cards[0].offsetWidth : 0;
      const gap = 28;
      track.style.transform = 'translateX(-' + (current * (cardWidth + gap)) + 'px)';
      dots.forEach(function (d, i) { d.classList.toggle('is-active', i === current); });
    }

    if (btnPrev) btnPrev.addEventListener('click', function () { goTo(current - 1); });
    if (btnNext) btnNext.addEventListener('click', function () { goTo(current + 1); });
    dots.forEach(function (d, i) { d.addEventListener('click', function () { goTo(i); }); });

    // Auto-play
    function startAutoplay() {
      autoplay = setInterval(function () { goTo(current + 1 > getMax() ? 0 : current + 1); }, 5000);
    }
    var autoplay;
    startAutoplay();
    track.addEventListener('mouseenter', function () { clearInterval(autoplay); });
    track.addEventListener('mouseleave', startAutoplay);

    // Touch swipe
    var touchStartX = 0;
    track.addEventListener('touchstart', function (e) { touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', function (e) {
      var diff = touchStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) goTo(diff > 0 ? current + 1 : current - 1);
    });

    goTo(0);
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
  var galleryEls = document.querySelectorAll('.gl-gallery__item[data-src], .gl-gallery__item img');
  if (galleryEls.length) {
    var srcs = Array.from(galleryEls).map(function (el) {
      return el.dataset.src || (el.tagName === 'IMG' ? el.src : (el.querySelector('img') ? el.querySelector('img').src : ''));
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
     MOBILE MENU — CSS-driven animation with Astra override
     We force display:block and let CSS handle visibility via max-height/opacity.
     Astra uses body class `ast-main-header-nav-open` to toggle menu state.
     ========================================================================= */
  var mobileMenuContent = document.querySelector('#ast-mobile-header .ast-mobile-header-content, .ast-mobile-header-content');
  var mobileToggle = document.querySelector('#ast-mobile-header .menu-toggle, .ast-mobile-menu-trigger-minimal, .main-header-menu-toggle');

  if (mobileMenuContent && mobileToggle) {
    // Force display:block so CSS can control visibility via max-height + opacity
    mobileMenuContent.style.display = 'block';

    // Astra may reset display:none — keep overriding it
    var contentObserver = new MutationObserver(function () {
      if (mobileMenuContent.style.display !== 'block') {
        mobileMenuContent.style.display = 'block';
      }
    });
    contentObserver.observe(mobileMenuContent, { attributes: true, attributeFilter: ['style'] });

    // Close menu on link click (smooth UX — navigate then close)
    mobileMenuContent.querySelectorAll('a.menu-link, a[href]').forEach(function (link) {
      var parent = link.closest('.menu-item');
      // Skip sub-menu toggle parents (they expand sub-menu)
      if (parent && parent.classList.contains('menu-item-has-children') && !link.closest('.sub-menu')) return;

      link.addEventListener('click', function () {
        if (document.body.classList.contains('ast-main-header-nav-open')) {
          mobileToggle.click();
        }
      });
    });

    // Close on overlay click / scroll lock
    document.addEventListener('click', function (e) {
      if (!document.body.classList.contains('ast-main-header-nav-open')) return;
      // If click is outside menu and outside toggle button
      var header = document.querySelector('#ast-mobile-header, .ast-mobile-header-wrap');
      if (header && !header.contains(e.target)) {
        mobileToggle.click();
      }
    });
  }

  /* =========================================================================
     ROOMS CAROUSEL
     ========================================================================= */
  var roomsTrack   = document.querySelector('.gl-rooms__track');
  var roomsBtnPrev = document.querySelector('.gl-rooms__btn--prev');
  var roomsBtnNext = document.querySelector('.gl-rooms__btn--next');
  var roomsDotsEl  = document.getElementById('rooms-dots');

  if (roomsTrack) {
    var roomsCards   = roomsTrack.querySelectorAll('.gl-room-card');
    var roomsTotal   = roomsCards.length;
    var roomsCurrent = 0;
    var roomsDots    = [];

    function getRoomsPerView() {
      if (window.innerWidth >= 1024) return 3;
      if (window.innerWidth >= 640)  return 2;
      return 1;
    }

    function getRoomsMax() {
      return Math.max(0, roomsTotal - getRoomsPerView());
    }

    function roomsGoTo(idx) {
      roomsCurrent = Math.max(0, Math.min(idx, getRoomsMax()));
      var gap       = 28;
      var viewport  = roomsTrack.parentElement;
      var perView   = getRoomsPerView();
      var cardWidth = (viewport.offsetWidth - gap * (perView - 1)) / perView;
      roomsTrack.style.transform = 'translateX(-' + (roomsCurrent * (cardWidth + gap)) + 'px)';

      roomsDots.forEach(function (d, i) {
        d.classList.toggle('is-active', i === roomsCurrent);
      });

      if (roomsBtnPrev) roomsBtnPrev.disabled = roomsCurrent === 0;
      if (roomsBtnNext) roomsBtnNext.disabled = roomsCurrent >= getRoomsMax();
    }

    // Generate dots
    if (roomsDotsEl) {
      for (var ri = 0; ri < roomsTotal; ri++) {
        var dot = document.createElement('button');
        dot.className = 'gl-rooms__dot';
        dot.setAttribute('aria-label', 'Номер ' + (ri + 1));
        (function (idx) {
          dot.addEventListener('click', function () { roomsGoTo(idx); });
        }(ri));
        roomsDotsEl.appendChild(dot);
        roomsDots.push(dot);
      }
    }

    if (roomsBtnPrev) roomsBtnPrev.addEventListener('click', function () { roomsGoTo(roomsCurrent - 1); });
    if (roomsBtnNext) roomsBtnNext.addEventListener('click', function () { roomsGoTo(roomsCurrent + 1); });

    // Touch swipe
    var roomsTouchX = 0;
    roomsTrack.addEventListener('touchstart', function (e) {
      roomsTouchX = e.touches[0].clientX;
    }, { passive: true });
    roomsTrack.addEventListener('touchend', function (e) {
      var diff = roomsTouchX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) roomsGoTo(diff > 0 ? roomsCurrent + 1 : roomsCurrent - 1);
    });

    // Recalc offset on resize
    window.addEventListener('resize', function () {
      roomsGoTo(Math.min(roomsCurrent, getRoomsMax()));
    }, { passive: true });

    roomsGoTo(0);
  }

  /* =========================================================================
     FLOATING CONTACT WIDGET (FAB)
     ========================================================================= */
  var contactFab = document.getElementById('gl-contact-fab');
  if (contactFab) {
    var fabToggle = document.getElementById('gl-contact-fab-toggle');

    fabToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      contactFab.classList.toggle('is-open');
    });

    document.addEventListener('click', function (e) {
      if (!contactFab.contains(e.target)) {
        contactFab.classList.remove('is-open');
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        contactFab.classList.remove('is-open');
      }
    });
  }

})();
