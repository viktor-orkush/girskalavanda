<?php
/**
 * page-contact.php — Сторінка «Контакти»
 * Автоматично застосовується до сторінки зі слагом "contact"
 */

get_header();

// === Contact info from Customizer ===
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
    $wa_msg       = rawurlencode( 'Добрий день! Хочу забронювати номер у готелі Гірська Лаванда.' );
    $whatsapp_url = 'https://wa.me/' . $wa_phone . '?text=' . $wa_msg;
    $viber_url    = 'viber://contact?number=' . $wa_phone;
}
$instagram = get_theme_mod( 'gl_instagram', 'https://www.instagram.com/girska_lavandaa/' );
$facebook  = get_theme_mod( 'gl_facebook', 'https://facebook.com/girskalavanda' );

// Instagram DM link
$instagram_dm = $instagram;
if ( $instagram ) {
    preg_match( '/instagram\.com\/([^\/\?#]+)/i', $instagram, $m );
    $ig_user      = isset( $m[1] ) ? trim( $m[1], '/' ) : '';
    $instagram_dm = $ig_user ? 'https://ig.me/m/' . $ig_user : $instagram;
}

// === Hero image ===
$page_id  = get_the_ID();
$hero_id  = get_post_thumbnail_id( $page_id );
$hero_url = $hero_id ? wp_get_attachment_image_url( $hero_id, 'full' ) : '';
if ( ! $hero_url ) {
    $uploads  = wp_upload_dir();
    $hero_url = $uploads['baseurl'] . '/2025/07/L77A2868-Pano.jpg';
}

// === SVG Icons ===
$icon_phone = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.5 11.61a19.79 19.79 0 01-3.07-8.67A2 2 0 012.42 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 8.9a16 16 0 006.15 6.15l1.27-.77a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>';
$icon_mail  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
$icon_map   = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>';
$icon_clock = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
$icon_car   = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 16H9m10 0h3v-3.15a1 1 0 00-.84-.99L16 11l-2.7-3.6a1 1 0 00-.8-.4H5.24a1 1 0 00-.97.76L3 11H2a1 1 0 00-1 1v4h3"/><circle cx="6.5" cy="16.5" r="2.5"/><circle cx="16.5" cy="16.5" r="2.5"/></svg>';
$icon_nav   = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>';
$icon_check = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7l3.5 3.5 6.5-6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>

<main id="main" class="gl-contact-page">

  <!-- ======================================================================
       HERO
       ====================================================================== -->
  <section class="gl-contact-hero"
           style="--contact-hero-bg: url('<?php echo esc_url( $hero_url ); ?>')">
    <div class="gl-contact-hero__overlay"></div>

    <div class="gl-contact-hero__content">
      <div class="gl-container">
        <p class="gl-contact-hero__label">Гірська Лаванда · Східниця</p>
        <h1 class="gl-contact-hero__title">Зв'яжіться<br><em>з нами</em></h1>
        <p class="gl-contact-hero__subtitle">Ми завжди раді допомогти з бронюванням та відповісти на ваші запитання</p>

        <div class="gl-contact-hero__actions">
          <?php if ( $phone ) : ?>
          <a href="tel:<?php echo esc_attr( $phone ); ?>" class="gl-btn gl-btn--gold">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.5 11.61a19.79 19.79 0 01-3.07-8.67A2 2 0 012.42 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 8.9a16 16 0 006.15 6.15l1.27-.77a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
            </svg>
            Зателефонувати
          </a>
          <?php endif; ?>
          <a href="#contact-info" class="gl-btn gl-btn--outline-white">
            Детальна інформація
          </a>
        </div>
      </div>
    </div>

    <!-- Scroll indicator -->
    <a href="#contact-quick" class="gl-contact-hero__scroll" aria-label="Прокрутити донизу">
      <span class="gl-contact-hero__scroll-line"></span>
    </a>
  </section>


  <!-- ======================================================================
       QUICK CONTACT STRIP
       ====================================================================== -->
  <section class="gl-contact-strip" id="contact-quick">
    <div class="gl-container">
      <div class="gl-contact-strip__grid">

        <?php if ( $phone ) : ?>
        <a href="tel:<?php echo esc_attr( $phone ); ?>" class="gl-contact-strip__item gl-animate">
          <div class="gl-contact-strip__icon"><?php echo $icon_phone; ?></div>
          <div class="gl-contact-strip__text">
            <span class="gl-contact-strip__label">Телефон</span>
            <span class="gl-contact-strip__value"><?php echo esc_html( $phone_disp ?: $phone ); ?></span>
          </div>
        </a>
        <?php endif; ?>

        <a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener" class="gl-contact-strip__item gl-animate gl-animate--delay-1">
          <div class="gl-contact-strip__icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </div>
          <div class="gl-contact-strip__text">
            <span class="gl-contact-strip__label">Instagram</span>
            <span class="gl-contact-strip__value">@girska_lavandaa</span>
          </div>
        </a>

        <div class="gl-contact-strip__item gl-animate gl-animate--delay-2">
          <div class="gl-contact-strip__icon"><?php echo $icon_map; ?></div>
          <div class="gl-contact-strip__text">
            <span class="gl-contact-strip__label">Адреса</span>
            <span class="gl-contact-strip__value">с. Східниця, Карпати</span>
          </div>
        </div>

        <div class="gl-contact-strip__item gl-animate gl-animate--delay-3">
          <div class="gl-contact-strip__icon"><?php echo $icon_clock; ?></div>
          <div class="gl-contact-strip__text">
            <span class="gl-contact-strip__label">Заселення / Виселення</span>
            <span class="gl-contact-strip__value">14:00 — 12:00</span>
          </div>
        </div>

      </div>
    </div>
  </section>


  <!-- ======================================================================
       CONTACT INFO + MAP
       ====================================================================== -->
  <section class="gl-contact-main gl-section gl-section--sand" id="contact-info">
    <div class="gl-container">

      <div class="gl-contact-main__header gl-animate">
        <span class="gl-section-label">Контактна інформація</span>
        <h2 class="gl-section-title">Як з нами зв'язатись</h2>
        <p class="gl-section-subtitle">Оберіть зручний спосіб зв'язку — ми відповімо якомога швидше</p>
      </div>

      <div class="gl-contact-main__grid">

        <!-- Left: Contact Details -->
        <div class="gl-contact-details gl-animate">

          <div class="gl-contact-card">
            <div class="gl-contact-card__icon"><?php echo $icon_phone; ?></div>
            <div class="gl-contact-card__content">
              <h3 class="gl-contact-card__title">Телефон / Viber / WhatsApp</h3>
              <?php if ( $phone ) : ?>
              <a href="tel:<?php echo esc_attr( $phone ); ?>" class="gl-contact-card__value gl-contact-card__value--phone">
                <?php echo esc_html( $phone_disp ?: $phone ); ?>
              </a>
              <p class="gl-contact-card__hint">Дзвоніть з 9:00 до 21:00</p>
              <?php else : ?>
              <p class="gl-contact-card__hint">Номер телефону налаштовується в Customizer</p>
              <?php endif; ?>
            </div>
          </div>

          <div class="gl-contact-card">
            <div class="gl-contact-card__icon"><?php echo $icon_mail; ?></div>
            <div class="gl-contact-card__content">
              <h3 class="gl-contact-card__title">Електронна пошта</h3>
              <a href="mailto:info@girskalavanda.com" class="gl-contact-card__value">
                info@girskalavanda.com
              </a>
              <p class="gl-contact-card__hint">Відповідаємо протягом кількох годин</p>
            </div>
          </div>

          <div class="gl-contact-card">
            <div class="gl-contact-card__icon"><?php echo $icon_map; ?></div>
            <div class="gl-contact-card__content">
              <h3 class="gl-contact-card__title">Адреса</h3>
              <p class="gl-contact-card__value">
                с. Східниця, Бориславська громада,<br>
                Львівська область, Україна
              </p>
              <p class="gl-contact-card__hint">GPS: 49.2192° N, 23.3509° E</p>
            </div>
          </div>

          <div class="gl-contact-card">
            <div class="gl-contact-card__icon"><?php echo $icon_clock; ?></div>
            <div class="gl-contact-card__content">
              <h3 class="gl-contact-card__title">Часи заселення</h3>
              <div class="gl-contact-card__times">
                <div class="gl-contact-card__time">
                  <span class="gl-contact-card__time-label">Check-in</span>
                  <span class="gl-contact-card__time-value">від 14:00</span>
                </div>
                <div class="gl-contact-card__time-divider"></div>
                <div class="gl-contact-card__time">
                  <span class="gl-contact-card__time-label">Check-out</span>
                  <span class="gl-contact-card__time-value">до 12:00</span>
                </div>
              </div>
              <p class="gl-contact-card__hint">Ранній заїзд та пізній виїзд — за домовленістю</p>
            </div>
          </div>

        </div>

        <!-- Right: Map -->
        <div class="gl-contact-map gl-animate gl-animate--delay-2">
          <div class="gl-contact-map__wrapper">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2606!2d23.35088!3d49.219197!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z0JPRltGA0YHRjNC60LAg0JvQsNCy0LDQvdC00LA!5e0!3m2!1suk!2sua!4v1"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Розташування готелю Гірська Лаванда — Східниця"
            ></iframe>
          </div>
          <a href="https://maps.app.goo.gl/s5NUR41X67xDFT5f8"
             class="gl-contact-map__link"
             target="_blank"
             rel="noopener noreferrer">
            <?php echo $icon_nav; ?>
            Відкрити в Google Maps
          </a>
        </div>

      </div>
    </div>
  </section>


  <!-- ======================================================================
       MESSENGERS — Зв'язок через месенджери
       ====================================================================== -->
  <section class="gl-contact-messengers gl-section gl-section--white">
    <div class="gl-container">

      <div class="gl-contact-messengers__header gl-animate">
        <span class="gl-section-label">Месенджери</span>
        <h2 class="gl-section-title">Напишіть нам</h2>
        <p class="gl-section-subtitle">Оберіть зручний месенджер — ми відповімо протягом кількох хвилин</p>
      </div>

      <div class="gl-contact-messengers__grid">

        <?php if ( $telegram_url ) : ?>
        <a href="<?php echo esc_url( $telegram_url ); ?>"
           class="gl-messenger-card gl-messenger-card--telegram gl-animate"
           target="_blank" rel="noopener noreferrer">
          <div class="gl-messenger-card__icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L8.375 14.7l-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.462c.537-.194 1.006.131.773.86z"/>
            </svg>
          </div>
          <h3 class="gl-messenger-card__title">Telegram</h3>
          <p class="gl-messenger-card__desc">Найшвидший спосіб зв'язку. Відповідаємо за кілька хвилин.</p>
          <span class="gl-messenger-card__action">Написати
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </span>
        </a>
        <?php endif; ?>

        <?php if ( $whatsapp_url ) : ?>
        <a href="<?php echo esc_url( $whatsapp_url ); ?>"
           class="gl-messenger-card gl-messenger-card--whatsapp gl-animate gl-animate--delay-1"
           target="_blank" rel="noopener noreferrer">
          <div class="gl-messenger-card__icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
          </div>
          <h3 class="gl-messenger-card__title">WhatsApp</h3>
          <p class="gl-messenger-card__desc">Напишіть або відправте голосове повідомлення.</p>
          <span class="gl-messenger-card__action">Написати
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </span>
        </a>
        <?php endif; ?>

        <?php if ( $viber_url ) : ?>
        <a href="<?php echo esc_url( $viber_url ); ?>"
           class="gl-messenger-card gl-messenger-card--viber gl-animate gl-animate--delay-2"
           target="_blank" rel="noopener noreferrer">
          <div class="gl-messenger-card__icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M11.4 0C8.64 0 3.12.96 1.08 6.72.24 9 0 11.16 0 12.72c0 1.32.12 3.72 1.2 5.76.12.24.24.48.36.72l-.48 3.24c-.12.72.48 1.32 1.2 1.08l3.12-1.08c1.44.84 3.12 1.44 5.4 1.56h.48c2.76 0 8.28-.96 10.32-6.72C22.44 15.12 24 12.12 24 12c0-2.88-1.2-5.88-3.6-8.04C18 1.56 14.88 0 11.4 0zm.12 2.16c3.12 0 5.76 1.08 7.68 3 1.92 1.8 2.88 4.2 2.88 6.72 0 .12-1.44 2.88-2.04 4.68-1.68 4.92-6.36 5.64-8.52 5.64h-.48c-2.04-.12-3.6-.72-4.92-1.56l-.36-.24-2.16.72.36-2.28-.36-.48c-.12-.24-.24-.36-.36-.6C2.64 16.44 2.4 14.28 2.4 12.72c0-1.32.24-3.24.96-5.16C5.04 3.12 9.6 2.16 11.52 2.16z"/>
            </svg>
          </div>
          <h3 class="gl-messenger-card__title">Viber</h3>
          <p class="gl-messenger-card__desc">Зручно для тих, хто звик до Viber.</p>
          <span class="gl-messenger-card__action">Написати
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </span>
        </a>
        <?php endif; ?>

        <a href="<?php echo esc_url( $instagram_dm ); ?>"
           class="gl-messenger-card gl-messenger-card--instagram gl-animate gl-animate--delay-3"
           target="_blank" rel="noopener noreferrer">
          <div class="gl-messenger-card__icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
            </svg>
          </div>
          <h3 class="gl-messenger-card__title">Instagram</h3>
          <p class="gl-messenger-card__desc">Слідкуйте за новинами та пишіть в Direct.</p>
          <span class="gl-messenger-card__action">Написати
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </span>
        </a>

      </div>
    </div>
  </section>


  <!-- ======================================================================
       HOW TO GET HERE
       ====================================================================== -->
  <section class="gl-contact-directions gl-section gl-section--sand" id="directions">
    <div class="gl-container">

      <div class="gl-contact-directions__header gl-animate">
        <span class="gl-section-label">Маршрут</span>
        <h2 class="gl-section-title">Як до нас дістатись</h2>
        <p class="gl-section-subtitle">Комплекс розташований у курортному містечку Східниця, в серці Карпат</p>
      </div>

      <div class="gl-contact-directions__grid">

        <div class="gl-direction-card gl-animate">
          <div class="gl-direction-card__icon"><?php echo $icon_car; ?></div>
          <h3 class="gl-direction-card__title">На автомобілі</h3>
          <ul class="gl-direction-card__list">
            <li><strong>Зі Львова</strong> — ~120 км, 2–2.5 години через Стрий — Борислав — Східниця</li>
            <li><strong>З Києва</strong> — ~650 км, 7–8 годин через Львів або Тернопіль</li>
            <li><strong>З Івано-Франківська</strong> — ~100 км, 1.5–2 години через Калуш — Долину</li>
          </ul>
          <p class="gl-direction-card__note"><?php echo $icon_check; ?> Безкоштовна парковка на території готелю</p>
        </div>

        <div class="gl-direction-card gl-animate gl-animate--delay-1">
          <div class="gl-direction-card__icon"><?php echo $icon_nav; ?></div>
          <h3 class="gl-direction-card__title">Громадським транспортом</h3>
          <ul class="gl-direction-card__list">
            <li><strong>Потяг до Стрия</strong> або Борислава, далі маршрутка до Східниці</li>
            <li><strong>Автобус зі Львова</strong> — прямий рейс до Східниці (3–4 рази на день)</li>
            <li><strong>BlaBlaCar</strong> — часто є пропозиції зі Львова та Івано-Франківська</li>
          </ul>
          <p class="gl-direction-card__note"><?php echo $icon_check; ?> Допоможемо організувати трансфер від вокзалу</p>
        </div>

      </div>
    </div>
  </section>


  <!-- ======================================================================
       FAQ
       ====================================================================== -->
  <section class="gl-contact-faq gl-section gl-section--white" id="faq">
    <div class="gl-container">

      <div class="gl-contact-faq__header gl-animate">
        <span class="gl-section-label">Питання та відповіді</span>
        <h2 class="gl-section-title">Часті запитання</h2>
        <p class="gl-section-subtitle">Відповіді на найпопулярніші запитання наших гостей</p>
      </div>

      <div class="gl-contact-faq__list gl-animate">

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Як забронювати номер?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Забронювати номер можна кількома способами: через наш <a href="/rooms/">сайт</a>, за телефоном<?php echo $phone ? ' <a href="tel:' . esc_attr($phone) . '">' . esc_html($phone_disp ?: $phone) . '</a>' : ''; ?>, або написавши нам у будь-який месенджер (Telegram, WhatsApp, Viber, Instagram Direct). Ми підтвердимо бронювання протягом кількох хвилин.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи можна заселитись раніше або виселитись пізніше?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так, за попередньою домовленістю. Ранній заїзд (від 11:00) та пізній виїзд (до 15:00) можливі за наявності вільних номерів. Зв'яжіться з нами заздалегідь для уточнення.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи є паркінг?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так, на території готелю є безкоштовна охоронювана парковка для гостей. Місця не потрібно бронювати заздалегідь.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Як працює баня та чан?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Баня та чан бронюються окремо. Мінімальний сеанс — 2 години. Баня готується за 2-3 години до вашого приїзду. Детальніше на сторінках <a href="/banya/">Баня</a> та <a href="/chan/">Чан</a>.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи є Wi-Fi?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так, безкоштовний Wi-Fi доступний у всіх номерах та на території готелю.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи можна з домашніми тваринами?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Будь ласка, зв'яжіться з нами для уточнення. Ми розглядаємо кожен запит індивідуально та намагаємось знайти рішення для всіх гостей.</p>
          </div>
        </details>

      </div>
    </div>
  </section>


  <!-- ======================================================================
       FINAL CTA
       ====================================================================== -->
  <section class="gl-contact-cta">
    <div class="gl-contact-cta__overlay"></div>
    <div class="gl-container">
      <div class="gl-contact-cta__content gl-animate">
        <span class="gl-section-label" style="color: var(--color-gold-light)">Бронювання</span>
        <h2 class="gl-contact-cta__title">Готові до відпочинку в Карпатах?</h2>
        <p class="gl-contact-cta__desc">Забронюйте номер у готелі Гірська Лаванда та подаруйте собі незабутній відпочинок серед карпатських сосен</p>
        <div class="gl-contact-cta__actions">
          <a href="/rooms/" class="gl-btn gl-btn--gold">Переглянути номери</a>
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
  </section>

</main>

<?php get_footer(); ?>
