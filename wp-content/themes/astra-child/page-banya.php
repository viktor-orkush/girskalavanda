<?php
/**
 * page-banya.php — Сторінка «Баня»
 * Автоматично застосовується до сторінки зі слагом "banya"
 */

get_header();

$page_id = get_the_ID();

// === Featured image ===
$hero_id = get_post_thumbnail_id($page_id);
$hero_url = $hero_id ? wp_get_attachment_image_url($hero_id, 'full') : '';
if (!$hero_url) {
  $uploads = wp_upload_dir();
  $hero_url = $uploads['baseurl'] . '/2025/07/chan.jpg';
}
$hero_alt    = $hero_id
  ? (get_post_meta($hero_id, '_wp_attachment_image_alt', true) ?: 'Баня на дровах у Східниці — Гірська Лаванда')
  : 'Баня на дровах у Східниці — Гірська Лаванда';
$hero_srcset = $hero_id ? wp_get_attachment_image_srcset($hero_id, 'full') : '';

// === Gallery — images attached to this page ===
$gallery_imgs = [];
$attachments = get_attached_media('image', $page_id);
foreach (array_slice((array)$attachments, 0, 8) as $att) {
  $lg = wp_get_attachment_image_url($att->ID, 'large');
  $full = wp_get_attachment_url($att->ID);
  if ($lg) {
    $gallery_imgs[] = [
      'lg' => $lg,
      'full' => $full,
      'alt' => get_post_meta($att->ID, '_wp_attachment_image_alt', true) ?: 'Баня',
    ];
  }
}
// Fallback gallery from uploads if no attachments
if (empty($gallery_imgs)) {
  $uploads_url = wp_upload_dir()['baseurl'];
  $fallback_imgs = [
    ['file' => '/2025/07/chan.jpg', 'alt' => 'Банний комплекс'],
    ['file' => '/2025/07/services-bg-1.jpg', 'alt' => 'Комплекс відпочинку'],
    ['file' => '/2025/07/about1.jpg', 'alt' => 'Гірська Лаванда'],
    ['file' => '/2025/07/L77A2868-Pano.jpg', 'alt' => 'Панорама Карпат'],
  ];
  foreach ($fallback_imgs as $img) {
    $gallery_imgs[] = [
      'lg' => $uploads_url . $img['file'],
      'full' => $uploads_url . $img['file'],
      'alt' => $img['alt'],
    ];
  }
}

// === JSON-LD Schema (Images, FAQ, Service) ===
if (function_exists('glav_render_image_schema')) {
  $schema_images = [];
  if ($hero_url) {
    $schema_images[] = [
      'url' => $hero_url,
      'alt' => $hero_alt,
      'name' => 'Баня на дровах у Східниці',
      'description' => 'Автентична приватна дерев\'яна баня з цілющою парою та купіллю — відпочинок у Східниці.',
    ];
  }
  $count = 0;
  foreach ($gallery_imgs as $gimg) {
    if ($count >= 3) break;
    $schema_images[] = [
      'url' => $gimg['full'],
      'alt' => $gimg['alt'],
      'name' => wp_strip_all_tags($gimg['alt']) . ' — Гірська Лаванда',
      'description' => 'Фото з банного комплексу Гірська Лаванда у Східниці.'
    ];
    $count++;
  }
  glav_render_image_schema($schema_images);
}

// FAQ Schema
$faq_schema = [
  "@context" => "https://schema.org",
  "@type" => "FAQPage",
  "mainEntity" => [
    [
      "@type" => "Question",
      "name" => "Скільки коштує баня у Східниці?",
      "acceptedAnswer" => [
        "@type" => "Answer",
        "text" => "Вартість оренди нашої приватної бані починається від 2 500 ₴ за сеанс (мінімум 2 години). У вартість входить парна на дровах, кімната відпочинку та закрита територія."
      ]
    ],
    [
      "@type" => "Question",
      "name" => "Чи можна приїжджати в баню з дітьми?",
      "acceptedAnswer" => [
        "@type" => "Answer",
        "text" => "Так, звичайно! Наша закрита територія цілком безпечна для дітей, а в кімнаті відпочинку є все необхідне для комфортного перебування всією сім'єю."
      ]
    ],
    [
      "@type" => "Question",
      "name" => "Скільки людей вміщує баня?",
      "acceptedAnswer" => [
        "@type" => "Answer",
        "text" => "Парна та кімната відпочинку комфортно вміщують компанію до 8 осіб одночасно."
      ]
    ],
    [
      "@type" => "Question",
      "name" => "Що входить у вартість оренди бані?",
      "acceptedAnswer" => [
        "@type" => "Answer",
        "text" => "У вартість входить: парна на дровах, міні-басейн (купіль) з холодною водою, кімната відпочинку з телевізором та міні-кухнею, а також безкоштовний паркінг на закритій території."
      ]
    ],
    [
      "@type" => "Question",
      "name" => "Чи працює баня взимку?",
      "acceptedAnswer" => [
        "@type" => "Answer",
        "text" => "Так, наш комплекс відпочинку працює цілий рік — 365 днів на рік у будь-яку погоду."
      ]
    ],
    [
      "@type" => "Question",
      "name" => "Чи можна у вас замовити хамам?",
      "acceptedAnswer" => [
        "@type" => "Answer",
        "text" => "Так, поруч із традиційною парною є хамам. Це окрема додаткова послуга, вартість якої становить від 3 000 ₴ за сеанс."
      ]
    ]
  ]
];

// Service Schema
$service_schema = [
  "@context" => "https://schema.org",
  "@type" => "Service",
  "name" => "Оренда приватної бані в Східниці",
  "provider" => [
    "@type" => "LocalBusiness",
    "name" => "Гірська Лаванда",
    "image" => $hero_url
  ],
  "areaServed" => [
    "@type" => "City",
    "name" => "Східниця"
  ],
  "description" => "Приватна баня на дровах з міні-басейном, хамамом та кімнатою відпочинку.",
  "offers" => [
    "@type" => "Offer",
    "price" => "2500.00",
    "priceCurrency" => "UAH",
    "description" => "Сеанс від 2 годин для компанії до 8 осіб"
  ]
];
?>
<script type="application/ld+json"><?php echo json_encode($faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
<script type="application/ld+json"><?php echo json_encode($service_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>

<?php

// === Contact info ===
$contact = glav_get_contact_info('Добрий день! Хочу забронювати баню.');
extract($contact);


// === SVG icons ===
$icon_fire = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-.5 15.5c-2.5 0-4.5-2-4.5-4.5 0-2.45 1.45-4.22 2.75-5.35C10.38 8.88 11 9.75 11 10.5c0 1.38-1 2.5-1 4 0 1.1.9 2 2 2s2-.9 2-2c0-1.5-1-2.62-1-4 0-.75.62-1.62 1.25-2.85C15.55 8.78 17 10.55 17 13c0 2.5-2 4.5-4.5 4.5z"/></svg>';
$icon_water = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L8.5 9.5C7 12.5 6 14.5 6 17a6 6 0 0012 0c0-2.5-1-4.5-2.5-7.5L12 2z"/></svg>';
$icon_leaf = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>';
$icon_sun = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
$icon_home = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>';
$icon_parking = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><path d="M9 17V7h4a3 3 0 010 6H9"/></svg>';
$icon_bbq = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11V9a8 8 0 0116 0v2H4zm0 0l-2 8h20l-2-8M12 19v3m-4-3l-1 3m10-3l1 3"/></svg>';
$icon_users = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>';
$icon_clock = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
$icon_star = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
$icon_lock = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>';

?>
<main id="main" class="gl-banya-page">

  <!-- ======================================================================
       HERO
       ====================================================================== -->
  <section class="gl-banya-hero">

    <img
      class="gl-banya-hero__bg"
      src="<?php echo esc_url($hero_url); ?>"
      <?php if ($hero_srcset): ?>srcset="<?php echo esc_attr($hero_srcset); ?>" sizes="100vw"<?php endif; ?>
      alt="<?php echo esc_attr($hero_alt); ?>"
      fetchpriority="high"
      decoding="async"
    >

    <div class="gl-banya-hero__content">
      <div class="gl-container">
        <p class="gl-banya-hero__label">Оздоровлення · Гірська Лаванда</p>
        <h1 class="gl-banya-hero__title">Баня на дровах <em>у Східниці</em></h1>
        <p class="gl-banya-hero__subtitle">На дровах · Приватна · Східниця</p>
        <p class="gl-banya-hero__desc">
          Справжня дерев'яна баня з карпатських порід дерева — жар, пара і аромат трав для справжнього відновлення тіла
          і духу.
        </p>
        <div class="gl-banya-hero__actions">
          <div class="gl-banya-hero__price">
            <span class="gl-banya-hero__price-from">від</span>
            <span class="gl-banya-hero__price-amount">2 500</span>
            <span class="gl-banya-hero__price-currency">₴</span>
            <span class="gl-banya-hero__price-unit">/ сеанс</span>
          </div>
          <a href="#booking-section-ready" class="gl-btn gl-btn--gold">Забронювати</a>
          <?php if ($phone): ?>
          <a href="tel:<?php echo esc_attr($phone); ?>" class="gl-btn gl-btn--outline-white" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(4px);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              aria-hidden="true">
              <path
                d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.5 11.61a19.79 19.79 0 01-3.07-8.67A2 2 0 012.42 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 8.9a16 16 0 006.15 6.15l1.27-.77a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z" />
            </svg>
            Зателефонувати
          </a>
          <?php
endif; ?>
        </div>
      </div>
    </div>

    <!-- Stats bar — прикріплений до низу hero -->
    <div class="gl-hero-stats-bar" id="banya-stats">
      <div class="gl-container">
        <div class="gl-hero-stats-grid">
          <div class="gl-hero-stat gl-animate">
            <span class="gl-hero-stat__num">до&nbsp;8</span>
            <span class="gl-hero-stat__label">осіб одночасно</span>
          </div>
          <div class="gl-hero-stat gl-animate gl-animate--delay-1">
            <span class="gl-hero-stat__num">2&nbsp;год</span>
            <span class="gl-hero-stat__label">мінімальний сеанс</span>
          </div>
          <div class="gl-hero-stat gl-animate gl-animate--delay-2">
            <span class="gl-hero-stat__num">120°C</span>
            <span class="gl-hero-stat__label">жар справжньої парної</span>
          </div>
          <div class="gl-hero-stat gl-animate gl-animate--delay-3">
            <span class="gl-hero-stat__num">365</span>
            <span class="gl-hero-stat__label">днів на рік</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll indicator -->
    <a href="#banya-about" class="gl-banya-hero__scroll" aria-label="Прокрутити донизу">
      <span class="gl-banya-hero__scroll-line"></span>
    </a>
  </section>


  <!-- ======================================================================
       ABOUT — описово з фото
       ====================================================================== -->
  <section class="gl-banya-about gl-section gl-section--white" id="banya-about">
    <div class="gl-container">
      <div class="gl-banya-about__grid">

        <div class="gl-banya-about__img-wrap gl-animate">
          <img
            class="gl-banya-about__img"
            src="<?php echo esc_url($hero_url); ?>"
            <?php if ($hero_srcset): ?>srcset="<?php echo esc_attr($hero_srcset); ?>" sizes="(max-width: 768px) 100vw, 50vw"<?php endif; ?>
            alt="<?php echo esc_attr($hero_alt); ?>"
            loading="lazy"
            decoding="async"
          >
          <div class="gl-banya-about__img-badge">
            <span class="gl-banya-about__img-badge-icon">🔥</span>
            <span>Справжня баня<br>на дровах</span>
          </div>
        </div>

        <div class="gl-banya-about__text gl-animate gl-animate--delay-1">
          <span class="gl-section-label">Про нашу баню</span>
          <h2 class="gl-banya-about__title">Комплекс для приватного відпочинку в Східниці</h2>
          <p class="gl-banya-about__desc">
            Наш банний комплекс — це закрита приватна територія з усім необхідним для повноцінного відпочинку.
            Баня на дровах, хамам, купальня, альтанка та мангальна зона — ідеальне поєднання для відновлення
            і радості.
          </p>
          <p class="gl-banya-about__desc">
            Баня топиться на натуральних дровах — так, як здавна робили в Карпатах. Жар чистий і м'який, пара ароматна —
            з карпатськими травами за бажанням.
          </p>

          <ul class="gl-banya-about__features">
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </span>
              Закрита приватна територія — тільки ваша компанія
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </span>
              Баня готується заздалегідь — приїжджаєте до вже гарячої парної
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </span>
              Парна + хамам + міні-басейн + кімната відпочинку + міні-кухня
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </span>
              Доступно цілий рік — у будь-яку погоду
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </span>
              Безкоштовний паркінг на закритій території
            </li>
          </ul>
        </div>

      </div>
    </div>
  </section>


  <!-- ======================================================================
       FEATURES — що входить
       ====================================================================== -->
  <section class="gl-banya-features gl-section gl-section--dark" id="banya-features">
    <div class="gl-container">
      <div class="gl-banya-features__header gl-center gl-animate">
        <span class="gl-section-label">Комплекс</span>
        <h2 class="gl-section-title">Що входить у вашу оренду</h2>
        <p class="gl-section-subtitle">Увесь комплекс — у вашому розпорядженні на весь час бронювання</p>
      </div>

      <div class="gl-banya-features__grid">

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-1">
          <div class="gl-banya-feature-card__icon gl-banya-feature-card__icon--fire">
            <?php echo $icon_fire; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Парна</h3>
          <p class="gl-banya-feature-card__desc">Справжня дерев'яна парна на дровах. Жар до 120°C, пара м'яка і
            ароматна.</p>
        </div>

        <div class="gl-banya-feature-card gl-banya-feature-card--optional gl-animate gl-animate--delay-2">
          <span class="gl-banya-feature-card__label">Додатково</span>
          <div class="gl-banya-feature-card__icon gl-banya-feature-card__icon--water">
            <?php echo $icon_water; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Хамам</h3>
          <p class="gl-banya-feature-card__desc">Турецька парна з м'якою вологою парою та комфортною температурою —
            ідеально для релаксації та очищення шкіри.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-3">
          <div class="gl-banya-feature-card__icon gl-banya-feature-card__icon--leaf">
            <?php echo $icon_water; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Міні-басейн</h3>
          <p class="gl-banya-feature-card__desc">Басейн з холодною водою для контрастного занурення — загартування і
            бадьорість після парної.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-1">
          <div class="gl-banya-feature-card__icon gl-banya-feature-card__icon--home">
            <?php echo $icon_home; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Кімната відпочинку</h3>
          <p class="gl-banya-feature-card__desc">Великий стіл для компанії, телевізор та зручні меблі — для відпочинку
            між заходами в парну.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-2">
          <div class="gl-banya-feature-card__icon gl-banya-feature-card__icon--bbq">
            <?php echo $icon_sun; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Міні-кухня</h3>
          <p class="gl-banya-feature-card__desc">Зручна кухня для приготування чаю, кави та легких перекусів — все під
            рукою.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-3">
          <div class="gl-banya-feature-card__icon gl-banya-feature-card__icon--lock">
            <?php echo $icon_lock; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Приватна територія</h3>
          <p class="gl-banya-feature-card__desc">Закрита, огороджена ділянка — тільки для вашої компанії. Паркінг
            включено.</p>
        </div>

      </div>
    </div>
  </section>


  <!-- ======================================================================
       GALLERY
       ====================================================================== -->
  <?php if (!empty($gallery_imgs)): ?>
  <section class="gl-banya-gallery gl-section gl-section--white" id="banya-gallery">
    <div class="gl-container">
      <div class="gl-banya-gallery__header gl-center gl-animate">
        <span class="gl-section-label">Фотогалерея</span>
        <h2 class="gl-section-title">Фото нашої бані у Східниці</h2>
      </div>
    </div>
    <div class="gl-banya-gallery__strip gl-animate gl-animate--delay-1">
      <?php foreach ($gallery_imgs as $gimg): ?>
      <a href="<?php echo esc_url($gimg['full']); ?>" class="gl-banya-gallery__item gl-lightbox-trigger"
        data-src="<?php echo esc_url($gimg['full']); ?>">
        <img src="<?php echo esc_url($gimg['lg']); ?>" alt="<?php echo esc_attr($gimg['alt']); ?>" loading="lazy" />
        <div class="gl-banya-gallery__item-overlay">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            aria-hidden="true">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
            <line x1="11" y1="8" x2="11" y2="14" />
            <line x1="8" y1="11" x2="14" y2="11" />
          </svg>
        </div>
      </a>
      <?php
  endforeach; ?>
    </div>
  </section>
  <?php
endif; ?>


  <!-- ======================================================================
       PRICES
       ====================================================================== -->
  <section class="gl-banya-prices gl-section" id="banya-prices">
    <div class="gl-container">
      <div class="gl-banya-prices__header gl-center gl-animate">
        <span class="gl-section-label">Вартість</span>
        <h2 class="gl-section-title">Ціна оренди бані та хамаму у Східниці</h2>
        <p class="gl-section-subtitle">Баня та хамам — окремі послуги. Ціна включає весь час оренди</p>
      </div>

      <div class="gl-banya-prices__grid">

        <div class="gl-banya-price-card gl-banya-price-card--main gl-animate gl-animate--delay-1">
          <div class="gl-banya-price-card__badge">Найпопулярніше</div>
          <div class="gl-banya-price-card__icon">🔥</div>
          <h3 class="gl-banya-price-card__title">Баня</h3>
          <p class="gl-banya-price-card__desc">Парна на дровах з купіллю, альтанкою та мангальною зоною</p>
          <div class="gl-banya-price-card__price">
            <span class="gl-banya-price-card__from">від</span>
            <span class="gl-banya-price-card__amount">2&nbsp;500</span>
            <span class="gl-banya-price-card__currency">₴</span>
            <span class="gl-banya-price-card__unit">/ сеанс</span>
          </div>
          <ul class="gl-banya-price-card__list">
            <li>Від 2 годин</li>
            <li>До 8 осіб</li>
            <li>Парна + міні-басейн</li>
            <li>Кімната відпочинку + ТВ</li>
            <li>Міні-кухня</li>
            <li>Паркінг безкоштовно</li>
          </ul>
          <a href="#booking-section-ready" class="gl-btn gl-btn--gold gl-banya-price-card__btn">Забронювати</a>
        </div>

        <div class="gl-banya-price-card gl-animate gl-animate--delay-2">
          <div class="gl-banya-price-card__icon">🧖</div>
          <h3 class="gl-banya-price-card__title">Хамам</h3>
          <p class="gl-banya-price-card__desc">Турецька парна з м'якою вологою парою — окрема послуга для глибокого
            розслаблення та очищення</p>
          <div class="gl-banya-price-card__price">
            <span class="gl-banya-price-card__from">від</span>
            <span class="gl-banya-price-card__amount">3&nbsp;000</span>
            <span class="gl-banya-price-card__currency">₴</span>
            <span class="gl-banya-price-card__unit">/ сеанс</span>
          </div>
          <ul class="gl-banya-price-card__list">
            <li>Від 2 годин</li>
            <li>М'яка волога пара</li>
            <li>Ідеально для релаксації</li>
            <li>Паркінг безкоштовно</li>
          </ul>
          <a href="#booking-section-ready" class="gl-btn gl-btn--gold gl-banya-price-card__btn">Забронювати</a>
        </div>

      </div>

      <p class="gl-banya-prices__note gl-animate gl-animate--delay-3">
        Точну ціну та доступність уточнюйте при бронюванні. Ціна може змінюватись залежно від кількості годин і сезону.
      </p>

    </div>
  </section>


  <!-- ======================================================================
       FAQ
       ====================================================================== -->
  <section class="gl-banya-faq gl-section gl-section--white" id="banya-faq">
    <div class="gl-container">
      <div class="gl-contact-faq__header gl-animate">
        <span class="gl-section-label">Часті питання</span>
        <h2 class="gl-section-title">Часті питання про баню на дровах</h2>
      </div>

      <div class="gl-contact-faq__list gl-animate gl-animate--delay-1" style="flex-direction: column;">
        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <h3 style="margin:0; font-size:inherit; font-weight:inherit;">Скільки коштує баня у Східниці?</h3>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Вартість оренди нашої приватної бані починається від 2 500 ₴ за сеанс (мінімум 2 години). У вартість входить парна на дровах, кімната відпочинку та закрита територія.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <h3 style="margin:0; font-size:inherit; font-weight:inherit;">Чи можна приїжджати в баню з дітьми?</h3>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так, звичайно! Наша закрита територія цілком безпечна для дітей, а в кімнаті відпочинку є все необхідне для комфортного перебування всією сім'єю.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <h3 style="margin:0; font-size:inherit; font-weight:inherit;">Скільки людей вміщує баня?</h3>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Парна та кімната відпочинку комфортно вміщують компанію до 8 осіб одночасно.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <h3 style="margin:0; font-size:inherit; font-weight:inherit;">Що входить у вартість оренди бані?</h3>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>У вартість входить: парна на дровах, міні-басейн (купіль) з холодною водою, кімната відпочинку з телевізором та міні-кухнею, а також безкоштовний паркінг на закритій території.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <h3 style="margin:0; font-size:inherit; font-weight:inherit;">Чи працює баня взимку?</h3>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так, наш комплекс відпочинку працює цілий рік — 365 днів на рік у будь-яку погоду.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <h3 style="margin:0; font-size:inherit; font-weight:inherit;">Чи можна у вас замовити хамам?</h3>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так, поруч із традиційною парною є хамам. Це окрема додаткова послуга, вартість якої становить від 3 000 ₴ за сеанс.</p>
          </div>
        </details>
      </div>
    </div>
  </section>

  <!-- ======================================================================
       ROOMS CROSS-LINK — Залишіться на ніч
       ====================================================================== -->
  <?php
  $banya_rooms = get_posts([
    'post_type'      => 'mphb_room_type',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
  ]);
  if (!empty($banya_rooms)): ?>
  <section class="gl-banya-rooms gl-section gl-section--sand" id="banya-rooms">
    <div class="gl-container">
      <div class="gl-banya-rooms__header gl-center gl-animate">
        <span class="gl-section-label">Комплекс Гірська Лаванда</span>
        <h2 class="gl-section-title">Залишіться на ніч</h2>
        <p class="gl-section-subtitle">Продовжте відпочинок після бані — оберіть затишний номер у Східниці</p>
      </div>
      <div class="gl-banya-rooms__grid">
        <?php foreach ($banya_rooms as $room):
          $thumb_url = get_the_post_thumbnail_url($room->ID, 'medium_large') ?: '';
          $capacity  = get_post_meta($room->ID, 'mphb_adults_capacity', true);
          $size      = get_post_meta($room->ID, 'mphb_size', true);
          $price     = function_exists('glav_get_room_price') ? glav_get_room_price($room->ID) : 0;
        ?>
        <a href="<?php echo esc_url(get_permalink($room->ID)); ?>" class="gl-banya-room-card gl-animate">
          <?php if ($thumb_url): ?>
          <div class="gl-banya-room-card__img">
            <img src="<?php echo esc_url($thumb_url); ?>"
                 alt="<?php echo esc_attr(get_the_title($room->ID)); ?>"
                 loading="lazy" decoding="async">
          </div>
          <?php endif; ?>
          <div class="gl-banya-room-card__body">
            <h3 class="gl-banya-room-card__title"><?php echo esc_html(get_the_title($room->ID)); ?></h3>
            <div class="gl-banya-room-card__meta">
              <?php if ($capacity): ?><span><?php echo esc_html($capacity); ?> гостей</span><?php endif; ?>
              <?php if ($size): ?><span><?php echo esc_html($size); ?> м²</span><?php endif; ?>
            </div>
            <?php if ($price): ?>
            <div class="gl-banya-room-card__price">від <?php echo number_format($price, 0, '.', ' '); ?> ₴ / ніч</div>
            <?php endif; ?>
            <span class="gl-banya-room-card__link">Переглянути номер →</span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <div class="gl-center gl-banya-rooms__cta gl-animate">
        <a href="<?php echo esc_url(home_url('/rooms/')); ?>" class="gl-btn gl-btn--outline-gold">Всі номери</a>
      </div>
    </div>
  </section>
  <?php endif; ?>


  <!-- ======================================================================
       BOOKING CTA
       ====================================================================== -->
  <?php get_template_part('template-parts/section-ready', null, [
  'title' => 'Забронюйте баню зараз',
  'subtitle' => 'Справжня дерев\'яна баня для вашого оздоровлення та релаксу',
  'wa_msg' => 'Добрий день! Хочу забронювати баню.'
]); ?>


</main>

<!-- Lightbox -->
<div class="gl-lightbox" role="dialog" aria-modal="true" aria-label="Перегляд фото">
  <button class="gl-lightbox__close" aria-label="Закрити">✕</button>
  <button class="gl-lightbox__prev" aria-label="Попереднє фото">&#8592;</button>
  <img class="gl-lightbox__img" src="" alt="Фото бані" />
  <button class="gl-lightbox__next" aria-label="Наступне фото">&#8594;</button>
</div>

<?php get_footer(); ?>