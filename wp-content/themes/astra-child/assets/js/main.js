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
  var galleryItems = [];
  var lightboxIdx  = 0;

  function openLightbox(items, idx) {
    galleryItems = items;
    lightboxIdx = idx;
    showLightboxImg();
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

  // Gallery items
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

  // Room gallery
  var roomGalleryEls = document.querySelectorAll('.gl-room-page__gallery-main, .gl-room-page__gallery-thumb');
  if (roomGalleryEls.length) {
    var roomSrcs = Array.from(roomGalleryEls).map(function (el) {
      var img = el.querySelector('img');
      return img ? img.src : '';
    }).filter(Boolean);
    roomGalleryEls.forEach(function (el, i) {
      el.addEventListener('click', function () { openLightbox(roomSrcs, i); });
    });
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

})();
