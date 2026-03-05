<?php
/**
 * page-chan.php — Сторінка «Чан»
 * Автоматично застосовується до сторінки зі слагом "chan"
 */

get_header();

$page_id = get_the_ID();

// === Featured image ===
$hero_id  = get_post_thumbnail_id( $page_id );
$hero_url = $hero_id ? wp_get_attachment_image_url( $hero_id, 'full' ) : '';
if ( ! $hero_url ) {
    $uploads  = wp_upload_dir();
    $hero_url = $uploads['baseurl'] . '/2025/07/chan.jpg';
}

// === Gallery — images attached to this page ===
$gallery_imgs = [];
$attachments  = get_attached_media( 'image', $page_id );
foreach ( array_slice( (array) $attachments, 0, 8 ) as $att ) {
    $lg   = wp_get_attachment_image_url( $att->ID, 'large' );
    $full = wp_get_attachment_url( $att->ID );
    if ( $lg ) {
        $gallery_imgs[] = [
            'lg'   => $lg,
            'full' => $full,
            'alt'  => get_post_meta( $att->ID, '_wp_attachment_image_alt', true ) ?: 'Чан',
        ];
    }
}
// Fallback gallery
if ( empty( $gallery_imgs ) ) {
    $uploads_url   = wp_upload_dir()['baseurl'];
    $fallback_imgs = [
        [ 'file' => '/2025/07/chan.jpg',          'alt' => 'Чан на відкритому повітрі' ],
        [ 'file' => '/2025/07/L77A2868-Pano.jpg', 'alt' => 'Панорама Карпат' ],
        [ 'file' => '/2025/07/about1.jpg',        'alt' => 'Гірська Лаванда' ],
        [ 'file' => '/2025/07/services-bg-1.jpg',  'alt' => 'Комплекс відпочинку' ],
    ];
    foreach ( $fallback_imgs as $img ) {
        $gallery_imgs[] = [
            'lg'   => $uploads_url . $img['file'],
            'full' => $uploads_url . $img['file'],
            'alt'  => $img['alt'],
        ];
    }
}

// === Contact info ===
$phone        = get_theme_mod( 'gl_phone', '' );
$phone_disp   = get_theme_mod( 'gl_phone_display', $phone );
$telegram_raw = get_theme_mod( 'gl_telegram', '' );
$telegram_url = '';
if ( $telegram_raw ) {
    $telegram_url = str_starts_with( $telegram_raw, 'http' )
        ? $telegram_raw
        : 'https://t.me/' . ltrim( $telegram_raw, '@/' );
}
$whatsapp_url = $viber_url = '';
if ( $phone ) {
    $wa_phone     = preg_replace( '/[^0-9]/', '', $phone );
    $wa_msg       = rawurlencode( 'Добрий день! Хочу забронювати чан.' );
    $whatsapp_url = 'https://wa.me/' . $wa_phone . '?text=' . $wa_msg;
    $viber_url    = 'viber://contact?number=' . $wa_phone;
}
$instagram_dm = 'https://www.instagram.com/girska_lavandaa';
$ig_raw       = get_theme_mod( 'gl_instagram', '' );
if ( $ig_raw ) {
    preg_match( '/instagram\.com\/([^\/\?#]+)/i', $ig_raw, $m );
    $ig_user      = isset( $m[1] ) ? trim( $m[1], '/' ) : '';
    $instagram_dm = $ig_user ? 'https://ig.me/m/' . $ig_user : $ig_raw;
}

// === SVG icons ===
$icon_fire     = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-.5 15.5c-2.5 0-4.5-2-4.5-4.5 0-2.45 1.45-4.22 2.75-5.35C10.38 8.88 11 9.75 11 10.5c0 1.38-1 2.5-1 4 0 1.1.9 2 2 2s2-.9 2-2c0-1.5-1-2.62-1-4 0-.75.62-1.62 1.25-2.85C15.55 8.78 17 10.55 17 13c0 2.5-2 4.5-4.5 4.5z"/></svg>';
$icon_water    = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L8.5 9.5C7 12.5 6 14.5 6 17a6 6 0 0012 0c0-2.5-1-4.5-2.5-7.5L12 2z"/></svg>';
$icon_star     = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
$icon_leaf     = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>';
$icon_sun      = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
$icon_lock     = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>';
$icon_thermo   = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.76V3.5a2.5 2.5 0 00-5 0v11.26a4.5 4.5 0 105 0z"/></svg>';

?>
<main id="main" class="gl-banya-page">

  <!-- ======================================================================
       HERO
       ====================================================================== -->
  <section class="gl-banya-hero"
           style="--banya-hero-bg: url('<?php echo esc_url( $hero_url ); ?>')">
    <div class="gl-banya-hero__overlay"></div>

    <div class="gl-banya-hero__content">
      <div class="gl-container">
        <p class="gl-banya-hero__label">Оздоровлення · Гірська Лаванда</p>
        <h1 class="gl-banya-hero__title">Гарячий<br><em>Чан</em></h1>
        <p class="gl-banya-hero__subtitle">На дровах · Під відкритим небом · Східниця, Карпати</p>
        <p class="gl-banya-hero__desc">
          Дерев'яний чан з гарячою водою просто неба — зіркове небо, свіже карпатське повітря та тепло природного вогню. Незабутній досвід для тіла і душі.
        </p>
        <div class="gl-banya-hero__actions">
          <div class="gl-banya-hero__price">
            <span class="gl-banya-hero__price-from">від</span>
            <span class="gl-banya-hero__price-amount">2 500</span>
            <span class="gl-banya-hero__price-currency">₴</span>
            <span class="gl-banya-hero__price-unit">/ сеанс</span>
          </div>
          <a href="#chan-booking" class="gl-btn gl-btn--gold">Забронювати</a>
          <?php if ( $phone ) : ?>
          <a href="tel:<?php echo esc_attr( $phone ); ?>" class="gl-btn gl-btn--outline-white">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.5 11.61a19.79 19.79 0 01-3.07-8.67A2 2 0 012.42 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 8.9a16 16 0 006.15 6.15l1.27-.77a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
            </svg>
            Зателефонувати
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Scroll indicator -->
    <a href="#chan-stats" class="gl-banya-hero__scroll" aria-label="Прокрутити донизу">
      <span class="gl-banya-hero__scroll-line"></span>
    </a>
  </section>


  <!-- ======================================================================
       STATS STRIP
       ====================================================================== -->
  <section class="gl-banya-stats" id="chan-stats">
    <div class="gl-container">
      <div class="gl-banya-stats__grid">
        <div class="gl-banya-stat gl-animate">
          <span class="gl-banya-stat__num">до&nbsp;8</span>
          <span class="gl-banya-stat__label">осіб одночасно</span>
        </div>
        <div class="gl-banya-stat gl-animate gl-animate--delay-1">
          <span class="gl-banya-stat__num">від&nbsp;2&nbsp;год</span>
          <span class="gl-banya-stat__label">мінімальний сеанс</span>
        </div>
        <div class="gl-banya-stat gl-animate gl-animate--delay-2">
          <span class="gl-banya-stat__num">38–42°C</span>
          <span class="gl-banya-stat__label">температура води</span>
        </div>
        <div class="gl-banya-stat gl-animate gl-animate--delay-3">
          <span class="gl-banya-stat__num">365</span>
          <span class="gl-banya-stat__label">днів на рік</span>
        </div>
      </div>
    </div>
  </section>


  <!-- ======================================================================
       ABOUT — описово з фото
       ====================================================================== -->
  <section class="gl-banya-about gl-section gl-section--sand" id="chan-about">
    <div class="gl-container">
      <div class="gl-banya-about__grid">

        <div class="gl-banya-about__img-wrap gl-animate">
          <div class="gl-banya-about__img"
               style="background-image: url('<?php echo esc_url( $hero_url ); ?>')">
          </div>
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
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
              Натуральний підігрів на дровах — без хімії та електрики
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
              Місткість до 8 осіб — ідеально для компанії чи родини
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
              Цілорічно — зимовий чан особливо атмосферний
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
              Можна додати карпатські трави для ароматерапії
            </li>
            <li class="gl-banya-about__feature">
              <span class="gl-banya-about__feature-check" aria-hidden="true">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
  <section class="gl-banya-features gl-section gl-section--white" id="chan-benefits">
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
          <p class="gl-banya-feature-card__desc">Натуральний підігрів карпатськими дровами — жодної хімії. Традиційний спосіб, який зберігає всі корисні властивості.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-2">
          <div class="gl-banya-feature-card__icon gl-banya-feature-card__icon--water">
            <?php echo $icon_star; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Під зоряним небом</h3>
          <p class="gl-banya-feature-card__desc">Чан розташований просто неба. Вдень — панорама карпатського лісу. Ввечері — зоряне небо та магічна атмосфера.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-3">
          <div class="gl-banya-feature-card__icon" style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));">
            <?php echo $icon_leaf; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Цілющі трави</h3>
          <p class="gl-banya-feature-card__desc">За бажанням додаємо карпатські трави — ялівець, м'яту, чебрець. Ароматерапія та оздоровчий ефект у комплексі.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-1">
          <div class="gl-banya-feature-card__icon" style="background: linear-gradient(135deg, var(--color-accent), var(--color-accent-light));">
            <?php echo $icon_lock; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Приватна зона</h3>
          <p class="gl-banya-feature-card__desc">Закрита територія тільки для вашої компанії. Ніяких сторонніх — повний комфорт і приватність.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-2">
          <div class="gl-banya-feature-card__icon" style="background: linear-gradient(135deg, #5A3A1A, #9B6B3A);">
            <?php echo $icon_sun; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Увесь рік</h3>
          <p class="gl-banya-feature-card__desc">Влітку чан — прохолодна купіль після спеки. Взимку — гаряча ванна під снігопадом. Кожен сезон — своя магія.</p>
        </div>

        <div class="gl-banya-feature-card gl-animate gl-animate--delay-3">
          <div class="gl-banya-feature-card__icon" style="background: linear-gradient(135deg, #0E4D7B, #1A7DC0);">
            <?php echo $icon_water; ?>
          </div>
          <h3 class="gl-banya-feature-card__title">Оздоровчий ефект</h3>
          <p class="gl-banya-feature-card__desc">Гаряча вода покращує кровообіг, знімає напругу м'язів, заспокоює нервову систему та зміцнює імунітет.</p>
        </div>

      </div>
    </div>
  </section>


  <!-- ======================================================================
       GALLERY
       ====================================================================== -->
  <?php if ( ! empty( $gallery_imgs ) ) : ?>
  <section class="gl-banya-gallery gl-section gl-section--sand" id="chan-gallery">
    <div class="gl-container">
      <div class="gl-banya-gallery__header gl-center gl-animate">
        <span class="gl-section-label">Фотогалерея</span>
        <h2 class="gl-section-title">Наш чан</h2>
      </div>
    </div>
    <div class="gl-banya-gallery__strip gl-animate gl-animate--delay-1">
      <?php foreach ( $gallery_imgs as $gimg ) : ?>
      <a href="<?php echo esc_url( $gimg['full'] ); ?>"
         class="gl-banya-gallery__item gl-lightbox-trigger"
         data-src="<?php echo esc_url( $gimg['full'] ); ?>">
        <img src="<?php echo esc_url( $gimg['lg'] ); ?>"
             alt="<?php echo esc_attr( $gimg['alt'] ); ?>"
             loading="lazy" />
        <div class="gl-banya-gallery__item-overlay">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            <line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
          </svg>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>


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
          <p class="gl-banya-price-card__desc">Гарячий чан на відкритому повітрі — ідеальний спосіб відпочити та оздоровитись</p>
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
          <a href="#chan-booking" class="gl-btn gl-btn--gold gl-banya-price-card__btn">Забронювати</a>
        </div>

      </div>

      <p class="gl-banya-prices__note gl-animate gl-animate--delay-3">
        Точну ціну та доступність уточнюйте при бронюванні. Ціна може змінюватись залежно від кількості годин і сезону.
      </p>

    </div>
  </section>


  <!-- ======================================================================
       BOOKING CTA
       ====================================================================== -->
  <section class="gl-banya-booking" id="chan-booking">
    <div class="gl-container">
      <div class="gl-banya-booking__inner gl-animate">

        <div class="gl-banya-booking__text">
          <span class="gl-section-label gl-banya-booking__label">Бронювання</span>
          <h2 class="gl-banya-booking__title">Забронюйте чан зараз</h2>
          <p class="gl-banya-booking__desc">
            Напишіть нам у месенджер або зателефонуйте — підберемо зручний час і підготуємо чан до вашого приїзду.
          </p>

          <div class="gl-banya-booking__details">
            <div class="gl-banya-booking__detail">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              Чан готується <strong>1–2 години</strong> до вашого приїзду
            </div>
            <div class="gl-banya-booking__detail">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
              Місткість <strong>до 8 осіб</strong> одночасно
            </div>
            <div class="gl-banya-booking__detail">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="8" x2="8" y2="8"/><line x1="16" y1="12" x2="8" y2="12"/><line x1="10" y1="16" x2="8" y2="16"/></svg>
              Скасування <strong>безкоштовне</strong> за 24 год
            </div>
          </div>
        </div>

        <div class="gl-banya-booking__actions">

          <?php
          $contact_data = glav_get_contact_data( 'Добрий день! Хочу забронювати чан.' );
          get_template_part( 'template-parts/booking-contact', null, $contact_data );
          ?>

        </div>
      </div>
    </div>
  </section>

</main>

<!-- Lightbox -->
<div class="gl-lightbox" role="dialog" aria-modal="true" aria-label="Перегляд фото">
  <button class="gl-lightbox__close" aria-label="Закрити">✕</button>
  <button class="gl-lightbox__prev" aria-label="Попереднє фото">&#8592;</button>
  <img class="gl-lightbox__img" src="" alt="Фото чану" />
  <button class="gl-lightbox__next" aria-label="Наступне фото">&#8594;</button>
</div>

<?php get_footer(); ?>
