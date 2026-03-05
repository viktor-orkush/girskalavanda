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

  /* =========================================================================
     HERO PARALLAX
     ========================================================================= */
  const heroBg = document.querySelector('.gl-hero__bg');
  if (heroBg) {
    heroBg.classList.add('gl-hero__bg--loaded');
    window.addEventListener('scroll', function () {
      const scrollY = window.scrollY;
      heroBg.style.transform = 'scale(1.05) translateY(' + (scrollY * 0.3) + 'px)';
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
    const perView = window.innerWidth < 768 ? 1 : window.innerWidth < 1024 ? 2 : 3;

    function getMax() {
      return Math.max(0, total - (window.innerWidth < 768 ? 1 : window.innerWidth < 1024 ? 2 : 3));
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
    var autoplay = setInterval(function () { goTo(current + 1 > getMax() ? 0 : current + 1); }, 5000);
    track.addEventListener('mouseenter', function () { clearInterval(autoplay); });

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

})();
