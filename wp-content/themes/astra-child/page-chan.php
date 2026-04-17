<?php
/**
 * page-chan.php — Сторінка «Чан»
 * Автоматично застосовується до сторінки зі слагом "chan"
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

// === About section image (відмінне від hero) ===
$about_img_id = get_post_meta($page_id, '_chan_about_image_id', true);
$about_url = $about_img_id ? wp_get_attachment_image_url($about_img_id, 'full') : '';
if (!$about_url) {
  $uploads = wp_upload_dir();
  $about_url = $uploads['baseurl'] . '/2026/04/фото-чана.webp';
}

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
      'alt' => get_post_meta($att->ID, '_wp_attachment_image_alt', true) ?: 'Чан',
    ];
  }
}
// Fallback gallery
if (empty($gallery_imgs)) {
  $uploads_url = wp_upload_dir()['baseurl'];
  $fallback_imgs = [
    ['file' => '/2025/07/chan.jpg', 'alt' => 'Чан на відкритому повітрі'],
    ['file' => '/2025/07/L77A2868-Pano.jpg', 'alt' => 'Панорама Карпат'],
    ['file' => '/2025/07/about1.jpg', 'alt' => 'Гірська Лаванда'],
    ['file' => '/2025/07/services-bg-1.jpg', 'alt' => 'Комплекс відпочинку'],
  ];
  foreach ($fallback_imgs as $img) {
    $gallery_imgs[] = [
      'lg' => $uploads_url . $img['file'],
      'full' => $uploads_url . $img['file'],
      'alt' => $img['alt'],
    ];
  }
}

// === ImageObject JSON-LD Schema ===
if (function_exists('glav_render_image_schema')) {
  $schema_images = [];
  if ($hero_url) {
    $schema_images[] = [
      'url' => $hero_url,
      'name' => 'Гарячий Чан просто неба у Східниці',
      'description' => 'Карпатський чан на дровах з цілющими травами під відкритим небом.',
    ];
  }
  $count = 0;
  foreach ($gallery_imgs as $gimg) {
    if ($count >= 3) break;
    $schema_images[] = [
      'url' => $gimg['full'],
      'alt' => $gimg['alt'],
      'name' => wp_strip_all_tags($gimg['alt']) . ' — Гірська Лаванда',
      'description' => 'Фото гарячого чану в комплексі відпочинку Гірська Лаванда у Східниці.'
    ];
    $count++;
  }
  glav_render_image_schema($schema_images);
}

// === Contact info ===
$contact = glav_get_contact_info('Добрий день! Хочу забронювати чан.');
extract($contact);

// === SVG icons ===
$icon_fire = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-.5 15.5c-2.5 0-4.5-2-4.5-4.5 0-2.45 1.45-4.22 2.75-5.35C10.38 8.88 11 9.75 11 10.5c0 1.38-1 2.5-1 4 0 1.1.9 2 2 2s2-.9 2-2c0-1.5-1-2.62-1-4 0-.75.62-1.62 1.25-2.85C15.55 8.78 17 10.55 17 13c0 2.5-2 4.5-4.5 4.5z"/></svg>';
$icon_water = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L8.5 9.5C7 12.5 6 14.5 6 17a6 6 0 0012 0c0-2.5-1-4.5-2.5-7.5L12 2z"/></svg>';
$icon_star = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
$icon_leaf = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>';
$icon_sun = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
$icon_lock = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>';
$icon_thermo = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.76V3.5a2.5 2.5 0 00-5 0v11.26a4.5 4.5 0 105 0z"/></svg>';

?>
<main id="main" class="gl-banya-page">

  <!-- ======================================================================
       HERO
       ====================================================================== -->
  <section class="gl-banya-hero" style="--banya-hero-bg: url('<?php echo esc_url($hero_url); ?>')">

    <div class="gl-banya-hero__content">
      <div class="gl-container">
        <p class="gl-banya-hero__label">Оздоровлення · Гірська Лаванда</p>
        <h1 class="gl-banya-hero__title">Гарячий<em>Чан</em></h1>
        <p class="gl-banya-hero__subtitle">На дровах · Під відкритим небом · Східниця, Карпати</p>
        <p class="gl-banya-hero__desc">
          Дерев'яний чан з гарячою водою просто неба — зіркове небо, свіже карпатське повітря та тепло природного вогню.
          Незабутній досвід для тіла і душі.
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
          <a href="tel:<?php echo esc_attr($phone); ?>" class="gl-btn gl-btn--outline-white">
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
    <div class="gl-hero-stats-bar" id="chan-stats">
      <div class="gl-container">
        <div class="gl-hero-stats-grid">
          <div class="gl-hero-stat gl-animate">
            <span class="gl-hero-stat__num">до&nbsp;8</span>
            <span class="gl-hero-stat__label">осіб одночасно</span>
          </div>
          <div class="gl-hero-stat gl-animate gl-animate--delay-1">
            <span class="gl-hero-stat__num">від 2&nbsp;год</span>
            <span class="gl-hero-stat__label">мінімальний сеанс</span>
          </div>
          <div class="gl-hero-stat gl-animate gl-animate--delay-2">
            <span class="gl-hero-stat__num">38–42°C</span>
            <span class="gl-hero-stat__label">температура води</span>
          </div>
          <div class="gl-hero-stat gl-animate gl-animate--delay-3">
            <span class="gl-hero-stat__num">365</span>
            <span class="gl-hero-stat__label">днів на рік</span>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ======================================================================
       ABOUT — описово з фото
       ====================================================================== -->
  <section class="gl-banya-about gl-section gl-section--white" id="chan-about">
    <div class="gl-container">
      <div class="gl-banya-about__grid">

        <div class="gl-banya-about__img-wrap gl-animate">
          <img
            src="<?php echo esc_url($about_url); ?>"
            alt="Гарячий чан — Гірська Лаванда"
            class="gl-banya-about__img gl-banya-about__img--full"
            loading="lazy"
            decoding="async">
          <div class="gl-banya-about__img-badge">
            <span class="gl-banya-about__img-badge-icon">🌊</span>
            <span>Гарячий чан<br>під відкритим небом</span>
          </div>
        </div>

        <div class="gl-banya-about__text gl-animate gl-animate--delay-1">
          <span class="gl-section-label">Про наш чан</span>
          <h2 class="gl-banya-about__title">Автентичний карпатський чан в Східниці</h2>
          <p class="gl-banya-about__desc">
            Карпатський чан — це великий дерев'яний резервуар з гарячою водою просто неба,
            підігрітий натуральними дровами. Традиція, яка здавна дарувала здоров'я та
            відновлювала сили мешканцям Карпат.
          </p>
          <p class="gl-banya-about__desc">
            Вода нагрівається до комфортних 38–42°C. Ви занурюєтесь у гарячу воду, навколо —
            вічнозелені карпатські сосни, чисте гірське повітря та тиша. Взимку — пар над водою
            і зоряне небо. Влітку — прохолодна свіжість після гарячої купелі.
          </p>

          <ul class="gl-banya-about__features">
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </span>
              Натуральний підігрів на дровах — без хімії та електрики
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </span>
              Місткість до 8 осіб — ідеально для компанії чи родини
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </span>
              Цілорічно — зимовий чан особливо атмосферний
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </span>
              Можна додати карпатські трави для ароматерапії
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </span>
              Приватна територія — тільки ваша компанія
            </li>
          </ul>
        </div>

      </div>
    </div>
  </section>


  <!-- ======================================================================
       BENEFITS — переваги чану
       ====================================================================== -->
  <section class="gl-banya-features gl-section gl-section--dark" id="chan-benefits">
    <div class="gl-container">
      <div class="gl-banya-features__header gl-center gl-animate">
        <span class="gl-section-label">Переваги</span>
        <h2 class="gl-section-title">Чому варто спробувати чан</h2>
        <p class="gl-section-subtitle">Користь для здоров'я та незабутні враження</p>
      </div>

      <div class="gl-banya-features__grid">

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-1">
          <div class="gl-banya-feature-card__icon gl-banya-feature-card__icon--fire">
            <?php echo $icon_fire; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Підігрів дровами</h3>
          <p class="gl-banya-feature-card__desc">Натуральний підігрів карпатськими дровами — жодної хімії. Традиційний
            спосіб, який зберігає всі корисні властивості.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-2">
          <div class="gl-banya-feature-card__icon gl-banya-feature-card__icon--water">
            <?php echo $icon_star; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Під зоряним небом</h3>
          <p class="gl-banya-feature-card__desc">Чан розташований просто неба. Вдень — панорама карпатського лісу.
            Ввечері — зоряне небо та магічна атмосфера.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-3">
          <div class="gl-banya-feature-card__icon"
            style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));">
            <?php echo $icon_leaf; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Цілющі трави</h3>
          <p class="gl-banya-feature-card__desc">За бажанням додаємо карпатські трави — ялівець, м'яту, чебрець.
            Ароматерапія та оздоровчий ефект у комплексі.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-1">
          <div class="gl-banya-feature-card__icon"
            style="background: linear-gradient(135deg, var(--color-accent), var(--color-accent-light));">
            <?php echo $icon_lock; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Приватна зона</h3>
          <p class="gl-banya-feature-card__desc">Закрита територія тільки для вашої компанії. Ніяких сторонніх — повний
            комфорт і приватність.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-2">
          <div class="gl-banya-feature-card__icon" style="background: linear-gradient(135deg, #5A3A1A, #9B6B3A);">
            <?php echo $icon_sun; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Увесь рік</h3>
          <p class="gl-banya-feature-card__desc">Влітку чан — прохолодна купіль після спеки. Взимку — гаряча ванна під
            снігопадом. Кожен сезон — своя магія.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-3">
          <div class="gl-banya-feature-card__icon" style="background: linear-gradient(135deg, #0E4D7B, #1A7DC0);">
            <?php echo $icon_water; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Оздоровчий ефект</h3>
          <p class="gl-banya-feature-card__desc">Гаряча вода покращує кровообіг, знімає напругу м'язів, заспокоює
            нервову систему та зміцнює імунітет.</p>
        </div>

      </div>
    </div>
  </section>


  <!-- ======================================================================
       GALLERY
       ====================================================================== -->
  <?php if (!empty($gallery_imgs)): ?>
  <section class="gl-banya-gallery gl-section gl-section--white" id="chan-gallery">
    <div class="gl-container">
      <div class="gl-banya-gallery__header gl-center gl-animate">
        <span class="gl-section-label">Фотогалерея</span>
        <h2 class="gl-section-title">Наш чан</h2>
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
  <section class="gl-banya-prices gl-section" id="chan-prices">
    <div class="gl-container">
      <div class="gl-banya-prices__header gl-center gl-animate">
        <span class="gl-section-label">Вартість</span>
        <h2 class="gl-section-title">Ціни та умови</h2>
        <p class="gl-section-subtitle">Оберіть варіант, що підходить саме вам</p>
      </div>

      <div class="gl-banya-prices__grid gl-banya-prices__grid--single">

        <div class="gl-banya-price-card gl-banya-price-card--main gl-animate gl-animate--delay-1">
          <div class="gl-banya-price-card__icon">🌊</div>
          <h3 class="gl-banya-price-card__title">Чан</h3>
          <p class="gl-banya-price-card__desc">Гарячий чан на відкритому повітрі — ідеальний спосіб відпочити та
            оздоровитись</p>
          <div class="gl-banya-price-card__price">
            <span class="gl-banya-price-card__from">від</span>
            <span class="gl-banya-price-card__amount">2&nbsp;500</span>
            <span class="gl-banya-price-card__currency">₴</span>
            <span class="gl-banya-price-card__unit">/ сеанс</span>
          </div>
          <ul class="gl-banya-price-card__list">
            <li>Від 2 годин</li>
            <li>До 8 осіб</li>
            <li>Підігрів дровами</li>
            <li>Кімната відпочинку + ТВ</li>
            <li>Міні-кухня</li>
            <li>Душ · Роздягальня · Туалет</li>
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
  <section class="gl-banya-faq gl-section gl-section--white" id="chan-faq">
    <div class="gl-container">
      <div class="gl-contact-faq__header gl-animate">
        <span class="gl-section-label">Часті питання</span>
        <h2 class="gl-section-title">Відповіді на ваші запитання</h2>
      </div>

      <div class="gl-contact-faq__list gl-animate gl-animate--delay-1" style="flex-direction: column;">
        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Скільки коштує чан у Карпатах (Східниці)?</span>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Оренда гарячого чану просто неба коштує від 2 500 ₴ за сеанс (мінімум 2 години). У ціну також входить користування кімнатою відпочинку та закритою територією.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи можна купатися в чані з дітьми?</span>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так, відпочинок у чані чудово підходить для сімей з дітьми. Вода нагрівається до комфортної і безпечної температури 38–42°C.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Скільки людей вміщує чан?</span>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Наш просторий чан розрахований на комфортний відпочинок компанії до 8 осіб.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Що входить до оренди чану?</span>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>До вартості входить: закритий від сторонніх гарячий чан з підігрівом на дровах, кімната відпочинку з ТБ, міні-кухня, душ, роздягальня та безкоштовний паркінг.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи працює чан узимку?</span>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так! Купання в гарячому чані під відкритим небом взимку в оточенні снігу — це один із найкращих видів релаксу в Карпатах.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи можна додати в чан цілющі трави?</span>
            <span class="gl-faq-item__toggle"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Звичайно. За вашим бажанням ми можемо додати цілющі карпатські трави для ароматерапії та кращого оздоровчого ефекту.</p>
          </div>
        </details>
      </div>
    </div>
  </section>

  <!-- ======================================================================
       ROOMS — cross-link
       ====================================================================== -->
  <?php
  $chan_rooms = get_posts([
    'post_type'      => 'mphb_room_type',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
  ]);
  if (!empty($chan_rooms)): ?>
  <section class="gl-banya-rooms gl-section gl-section--sand" id="chan-rooms">
    <div class="gl-container">
      <div class="gl-banya-rooms__header gl-center gl-animate">
        <span class="gl-section-label">Комплекс Гірська Лаванда</span>
        <h2 class="gl-section-title">Залишіться на ніч</h2>
        <p class="gl-section-subtitle">Продовжте відпочинок після чану — оберіть затишний номер серед карпатських сосен</p>
      </div>
      <div class="gl-banya-rooms__grid">
        <?php foreach ($chan_rooms as $room):
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
  'title' => 'Забронюйте чан зараз',
  'subtitle' => 'Гарячий чан просто неба з видом на гори — справжня карпатська екзотика',
  'wa_msg' => 'Добрий день! Хочу забронювати чан.'
]); ?>


</main>

<!-- Lightbox -->
<div class="gl-lightbox" role="dialog" aria-modal="true" aria-label="Перегляд фото">
  <button class="gl-lightbox__close" aria-label="Закрити">✕</button>
  <button class="gl-lightbox__prev" aria-label="Попереднє фото">&#8592;</button>
  <img class="gl-lightbox__img" src="" alt="Фото чану" />
  <button class="gl-lightbox__next" aria-label="Наступне фото">&#8594;</button>
</div>

<?php get_footer(); ?>