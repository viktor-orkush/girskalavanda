<?php
/**
 * page-contact.php — Сторінка «Контакти»
 * Автоматично застосовується до сторінки зі слагом "contact"
 */

get_header();

// === Contact info from Customizer ===
$phone = get_theme_mod('gl_phone', '');
$phone_disp = get_theme_mod('gl_phone_display', $phone);
$telegram_raw = get_theme_mod('gl_telegram', '');
$telegram_url = '';
if ($telegram_raw) {
  $telegram_url = str_starts_with($telegram_raw, 'http')
    ? $telegram_raw
    : 'https://t.me/' . ltrim($telegram_raw, '@/');
}
$whatsapp_url = $viber_url = '';
if ($phone) {
  $wa_phone = preg_replace('/[^0-9]/', '', $phone);
  $wa_msg = rawurlencode('Добрий день! Хочу забронювати апартаменти в Гірській Лаванді.');
  $whatsapp_url = 'https://wa.me/' . $wa_phone . '?text=' . $wa_msg;
  $viber_url = 'viber://contact?number=' . $wa_phone;
}
$instagram = get_theme_mod('gl_instagram', 'https://www.instagram.com/girska_lavandaa/');
$facebook = get_theme_mod('gl_facebook', 'https://facebook.com/girskalavanda');
$maps_url = get_theme_mod('gl_maps_url', GL_MAPS_URL_DEFAULT);
$maps_embed_url = get_theme_mod('gl_maps_embed_url', GL_MAPS_EMBED_DEFAULT);

// Instagram DM link
$instagram_dm = $instagram;
if ($instagram) {
  preg_match('/instagram\.com\/([^\/\?#]+)/i', $instagram, $m);
  $ig_user = isset($m[1]) ? trim($m[1], '/') : '';
  $instagram_dm = $ig_user ? 'https://ig.me/m/' . $ig_user : $instagram;
}

// === Hero image ===
$page_id = get_the_ID();
$hero_id = get_post_thumbnail_id($page_id);
$hero_url = $hero_id ? wp_get_attachment_image_url($hero_id, 'full') : '';
if (!$hero_url) {
  $uploads = wp_upload_dir();
  $hero_url = $uploads['baseurl'] . '/2025/07/L77A2868-Pano.jpg';
}

// === SVG Icons ===
$icon_phone = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.5 11.61a19.79 19.79 0 01-3.07-8.67A2 2 0 012.42 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 8.9a16 16 0 006.15 6.15l1.27-.77a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>';
$icon_mail = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
$icon_map = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>';
$icon_clock = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
$icon_car = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 16H9m10 0h3v-3.15a1 1 0 00-.84-.99L16 11l-2.7-3.6a1 1 0 00-.8-.4H5.24a1 1 0 00-.97.76L3 11H2a1 1 0 00-1 1v4h3"/><circle cx="6.5" cy="16.5" r="2.5"/><circle cx="16.5" cy="16.5" r="2.5"/></svg>';
$icon_nav = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>';
$icon_check = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7l3.5 3.5 6.5-6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>

<main id="main" class="gl-contact-page">

  <!-- ======================================================================
       HERO
       ====================================================================== -->
  <section class="gl-contact-hero" style="--contact-hero-bg: url('<?php echo esc_url($hero_url); ?>')">
    <div class="gl-contact-hero__overlay"></div>

    <div class="gl-contact-hero__content">
      <div class="gl-container">
        <p class="gl-contact-hero__label">Гірська Лаванда · Східниця</p>
        <h1 class="gl-contact-hero__title">Контакти апартаментів<br><em>Гірська Лаванда</em></h1>
        <p class="gl-contact-hero__subtitle">Ми завжди раді допомогти з бронюванням та відповісти на ваші запитання</p>

        <div class="gl-contact-hero__actions">
          <?php if ($phone): ?>
          <a href="tel:<?php echo esc_attr($phone); ?>" class="gl-btn gl-btn--gold">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              aria-hidden="true">
              <path
                d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.5 11.61a19.79 19.79 0 01-3.07-8.67A2 2 0 012.42 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 8.9a16 16 0 006.15 6.15l1.27-.77a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z" />
            </svg>
            Зателефонувати
          </a>
          <?php
endif; ?>
          <a href="#contact-info" class="gl-btn gl-btn--outline-white">
            Детальна інформація
          </a>
        </div>
      </div>
    </div>

    <!-- Contact strip — прикріплений до низу hero -->
    <div class="gl-hero-stats-bar gl-contact-hero__strip" id="contact-quick">
      <div class="gl-container">
        <div class="gl-contact-strip__grid">

          <?php if ($phone): ?>
          <a href="tel:<?php echo esc_attr($phone); ?>"
            class="gl-contact-strip__item gl-contact-strip__item--hero gl-animate">
            <div class="gl-contact-strip__icon">
              <?php echo $icon_phone; ?>
            </div>
            <div class="gl-contact-strip__text">
              <span class="gl-contact-strip__label">Телефон</span>
              <span class="gl-contact-strip__value">
                <?php echo esc_html($phone_disp ?: $phone); ?>
              </span>
            </div>
          </a>
          <?php
endif; ?>

          <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener"
            class="gl-contact-strip__item gl-contact-strip__item--hero gl-animate gl-animate--delay-1">
            <div class="gl-contact-strip__icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" />
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
              </svg>
            </div>
            <div class="gl-contact-strip__text">
              <span class="gl-contact-strip__label">Instagram</span>
              <span class="gl-contact-strip__value">@girska_lavandaa</span>
            </div>
          </a>

          <a href="<?php echo esc_url($maps_url); ?>" target="_blank" rel="noopener"
            class="gl-contact-strip__item gl-contact-strip__item--hero gl-animate gl-animate--delay-2">
            <div class="gl-contact-strip__icon">
              <?php echo $icon_map; ?>
            </div>
            <div class="gl-contact-strip__text">
              <span class="gl-contact-strip__label">Адреса</span>
              <span class="gl-contact-strip__value">пгт. Східниця, Львівська обл.</span>
            </div>
          </a>

          <div class="gl-contact-strip__item gl-contact-strip__item--hero gl-animate gl-animate--delay-3">
            <div class="gl-contact-strip__icon">
              <?php echo $icon_clock; ?>
            </div>
            <div class="gl-contact-strip__text">
              <span class="gl-contact-strip__label">Заселення / Виселення</span>
              <span class="gl-contact-strip__value">14:00 — 12:00</span>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Scroll indicator -->
    <a href="#contact-info" class="gl-contact-hero__scroll" aria-label="Прокрутити донизу">
      <span class="gl-contact-hero__scroll-line"></span>
    </a>
  </section>





  <!-- ======================================================================
       CONTACT INFO + MAP
       ====================================================================== -->
  <section class="gl-contact-main gl-section gl-section--white" id="contact-info">
    <div class="gl-container">

      <div class="gl-contact-main__header gl-animate gl-center">
        <span class="gl-section-label">Контактна інформація</span>
        <h2 class="gl-section-title">Як з нами зв'язатись</h2>
        <p class="gl-section-subtitle">Оберіть зручний спосіб зв'язку — ми відповімо якомога швидше</p>
      </div>

      <div class="gl-contact-main__grid">

        <!-- Left: Contact Details -->
        <div class="gl-contact-details gl-animate">

          <div class="gl-contact-card">
            <div class="gl-contact-card__icon">
              <?php echo $icon_phone; ?>
            </div>
            <div class="gl-contact-card__content">
              <h3 class="gl-contact-card__title">Телефон / Viber / WhatsApp</h3>
              <?php if ($phone): ?>
              <a href="tel:<?php echo esc_attr($phone); ?>"
                class="gl-contact-card__value gl-contact-card__value--phone">
                <?php echo esc_html($phone_disp ?: $phone); ?>
              </a>

              <div class="gl-contact-inline-channels">
                <?php if ($viber_url): ?>
                  <a class="gl-contact-inline-channel" href="<?php echo esc_url($viber_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Viber" style="--ch-color: #665CAC;">
                    <span class="gl-contact-inline-channel-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.398.002C9.473.028 5.331.344 3.014 2.467 1.294 4.177.518 6.77.399 9.932c-.12 3.163-.27 9.09 5.563 10.665l.004.002v2.458s-.038.99.613 1.195c.79.249 1.254-.508 2.01-1.318.413-.443.983-1.093 1.413-1.59 3.9.327 6.894-.422 7.234-.534.784-.258 5.22-.824 5.943-6.726.745-6.079-.354-9.917-2.347-11.65l-.002-.004c-.6-.574-2.986-2.239-8.523-2.393 0 0-.387-.02-.908-.013zM11.5 1.59c.455-.007.78.012.78.012 4.671.13 6.774 1.468 7.283 1.952 1.683 1.46 2.593 4.87 1.94 10.143-.608 4.958-4.194 5.29-4.876 5.514-.287.095-2.836.732-6.16.531 0 0-2.44 2.942-3.2 3.708-.12.12-.258.166-.352.144-.13-.03-.166-.18-.164-.396l.028-4.015c-4.917-1.327-4.623-6.327-4.523-8.976.1-2.648.727-4.88 2.178-6.316 1.958-1.795 5.612-2.076 7.345-2.3h-.001l.72.001zm.7 2.834c-.162 0-.294.132-.294.296a.295.295 0 0 0 .294.296c1.14.013 2.197.46 3.005 1.289.808.828 1.27 1.933 1.298 3.11a.295.295 0 0 0 .296.291h.004a.295.295 0 0 0 .291-.3c-.033-1.378-.575-2.673-1.525-3.647-.95-.974-2.19-1.518-3.5-1.535h-.068zM8.073 6.26c-.232-.009-.476.092-.655.303l-.002.002c-.342.37-.706.77-.727 1.225-.037.66.35 1.277.658 1.732l.024.034c.836 1.29 1.862 2.47 3.07 3.404l.016.013.013.015c.76.615 1.636 1.13 2.583 1.418l.004.002.03.012c.405.154.814.095 1.162-.092.348-.188.603-.495.735-.828.073-.182.058-.378-.06-.52-.426-.52-.946-.96-1.512-1.332-.28-.174-.598-.063-.754.063l-.496.457a.36.36 0 0 1-.39.063c-.558-.243-1.874-1.326-2.375-1.858a.357.357 0 0 1-.034-.413l.376-.55c.15-.218.196-.542.005-.81a9.68 9.68 0 0 0-1.171-1.299.535.535 0 0 0-.372-.152c-.043-.002-.085-.002-.128-.004zm4.534.466c-.163 0-.295.134-.293.297.01.895.377 1.748 1.024 2.395.648.648 1.497 1.01 2.392 1.025h.01a.295.295 0 0 0 .003-.59c-.715-.013-1.39-.3-1.907-.818-.518-.518-.81-1.199-.82-1.916a.296.296 0 0 0-.296-.293h-.113zm.095 1.563a.295.295 0 0 0-.28.31c.03.42.213.81.518 1.107.305.298.698.472 1.107.493a.295.295 0 0 0 .03-.59.988.988 0 0 1-.71-.315.988.988 0 0 1-.332-.71.295.295 0 0 0-.297-.291l-.035-.004z"/></svg></span>
                  </a>
                <?php endif; ?>
                <?php if ($telegram_url): ?>
                  <a class="gl-contact-inline-channel" href="<?php echo esc_url($telegram_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Telegram" style="--ch-color: #2AABEE;">
                    <span class="gl-contact-inline-channel-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg></span>
                  </a>
                <?php endif; ?>
                <?php if ($whatsapp_url): ?>
                  <a class="gl-contact-inline-channel" href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" style="--ch-color: #25D366;">
                    <span class="gl-contact-inline-channel-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg></span>
                  </a>
                <?php endif; ?>
              </div>

              <?php
else: ?>
              <p class="gl-contact-card__hint">Номер телефону налаштовується в Customizer</p>
              <?php
endif; ?>
            </div>
          </div>



          <div class="gl-contact-card">
            <div class="gl-contact-card__icon">
              <?php echo $icon_map; ?>
            </div>
            <div class="gl-contact-card__content">
              <h3 class="gl-contact-card__title">Адреса</h3>
              <p class="gl-contact-card__value">
                Львівська область, Дрогобицький район,<br>
                Східницька територіальна громада,<br>
                вулиця Шевченко, 162-Н
              </p>
              <p class="gl-contact-card__hint">GPS: 49.2192° N, 23.3509° E</p>
            </div>
          </div>

          <div class="gl-contact-card">
            <div class="gl-contact-card__icon" aria-hidden="true">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
            </div>
            <div class="gl-contact-card__content">
              <h3 class="gl-contact-card__title">Ми в соцмережах</h3>
              <div class="gl-contact-inline-channels">
                <?php if ($instagram): ?>
                  <a class="gl-contact-inline-channel" href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram" style="--ch-color: #E1306C;">
                    <span class="gl-contact-inline-channel-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg></span>
                  </a>
                <?php endif; ?>
                <?php if ($facebook): ?>
                  <a class="gl-contact-inline-channel" href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook" style="--ch-color: #1877F2;">
                    <span class="gl-contact-inline-channel-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></span>
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>

        </div>

        <!-- Right: Map -->
        <div class="gl-contact-map gl-animate gl-animate--delay-2">
          <div class="gl-contact-map__wrapper">
            <iframe
              src="<?php echo esc_url($maps_embed_url); ?>"
              loading="lazy" referrerpolicy="no-referrer-when-downgrade"
              title="Розташування комплексу Гірська Лаванда — Східниця"></iframe>
          </div>
          <a href="<?php echo esc_url($maps_url); ?>" class="gl-contact-map__link" target="_blank"
            rel="noopener noreferrer">
            <?php echo $icon_nav; ?>
            Відкрити в Google Maps
          </a>
        </div>

      </div>
    </div>
  </section>



  <!-- ======================================================================
       HOW TO GET HERE
       ====================================================================== -->
  <section class="gl-contact-directions gl-section gl-section--dark" id="directions">
    <div class="gl-container">

      <div class="gl-contact-directions__header gl-animate gl-center">
        <span class="gl-section-label">Маршрут</span>
        <h2 class="gl-section-title">Як доїхати до нас у Східницю</h2>
        <p class="gl-section-subtitle">Комплекс розташований у курортному містечку Східниця, поруч з Трускавцем</p>
      </div>

      <div class="gl-contact-directions__grid">

        <div class="gl-direction-card gl-animate">
          <div class="gl-direction-card__icon">
            <?php echo $icon_car; ?>
          </div>
          <h3 class="gl-direction-card__title">На автомобілі</h3>
          <ul class="gl-direction-card__list">
            <li><strong>Зі Львова</strong> — ~120 км, 2–2.5 години через Стрий — Борислав — Східниця</li>
            <li><strong>З Києва</strong> — ~650 км, 7–8 годин через Львів або Тернопіль</li>
            <li><strong>З Івано-Франківська</strong> — ~100 км, 1.5–2 години через Калуш — Долину</li>
          </ul>
          <p class="gl-direction-card__note">
            <?php echo $icon_check; ?> Безкоштовна парковка на території комплексу
          </p>
        </div>

        <div class="gl-direction-card gl-animate gl-animate--delay-1">
          <div class="gl-direction-card__icon">
            <?php echo $icon_nav; ?>
          </div>
          <h3 class="gl-direction-card__title">Громадським транспортом</h3>
          <ul class="gl-direction-card__list">
            <li><strong>Потяг до Стрия</strong> або Борислава, далі маршрутка до Східниці</li>
            <li><strong>Автобус зі Львова</strong> — прямий рейс до Східниці (3–4 рази на день)</li>
            <li><strong>BlaBlaCar</strong> — часто є пропозиції зі Львова та Івано-Франківська</li>
          </ul>
          <p class="gl-direction-card__note">
            <?php echo $icon_check; ?> Допоможемо організувати трансфер від вокзалу
          </p>
        </div>

      </div>
    </div>
  </section>


  <!-- ======================================================================
       FAQ
       ====================================================================== -->
  <section class="gl-contact-faq gl-section gl-section--white" id="faq">
    <div class="gl-container">

      <div class="gl-contact-faq__header gl-animate gl-center">
        <span class="gl-section-label">Питання та відповіді</span>
        <h2 class="gl-section-title">Часті запитання</h2>
        <p class="gl-section-subtitle">Відповіді на найпопулярніші запитання наших гостей</p>
      </div>

      <div class="gl-contact-faq__list gl-animate">

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Як забронювати апартаменти?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Забронювати апартаменти можна кількома способами: через наш <a href="/rooms/">сайт</a>, за телефоном
              <?php echo $phone ? ' <a href="tel:' . esc_attr($phone) . '">' . esc_html($phone_disp ?: $phone) . '</a>' : ''; ?>,
              або написавши нам у будь-який месенджер (Telegram, WhatsApp, Viber, Instagram Direct). Ми підтвердимо
              бронювання протягом кількох хвилин.
            </p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи можна заселитись раніше або виселитись пізніше?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так, за попередньою домовленістю. Ранній заїзд (від 11:00) та пізній виїзд (до 15:00) можливі за
              наявності вільних апартаментів. Зв'яжіться з нами заздалегідь для уточнення.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи є паркінг?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так, на території комплексу є безкоштовна охоронювана парковка для гостей. Місця не потрібно бронювати
              заздалегідь.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Як працює баня та чан?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Баня та чан бронюються окремо. Мінімальний сеанс — 2 години. Баня готується за 2-3 години до вашого
              приїзду. Детальніше на сторінках <a href="/banya/">Баня</a> та <a href="/chan/">Чан</a>.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи є Wi-Fi?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так, безкоштовний Wi-Fi доступний у всіх номерах та на території комплексу.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи можна з домашніми тваринами?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Будь ласка, зв'яжіться з нами для уточнення. Ми розглядаємо кожен запит індивідуально та намагаємось
              знайти рішення для всіх гостей.</p>
          </div>
        </details>

      </div>
    </div>
  </section>




  <!-- JSON-LD Schema: LocalBusiness & FAQPage -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "ApartmentComplex",
        "name": "Гірська Лаванда",
        "image": "<?php echo esc_url($hero_url); ?>",
        "url": "https://girskalavanda.com/",
        "telephone": "<?php echo esc_attr($phone); ?>",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "вулиця Шевченко, 162-Н",
          "addressLocality": "Східниця",
          "addressRegion": "Львівська область",
          "postalCode": "82391",
          "addressCountry": "UA"
        },
        "geo": {
          "@type": "GeoCoordinates",
          "latitude": 49.2192,
          "longitude": 23.3509
        }
      },
      {
        "@type": "FAQPage",
        "mainEntity": [
          {
            "@type": "Question",
            "name": "Як забронювати апартаменти?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Забронювати апартаменти можна через наш сайт, за телефоном, або написавши нам у месенджери (Telegram, WhatsApp, Viber, Instagram Direct)."
            }
          },
          {
            "@type": "Question",
            "name": "Чи можна заселитись раніше або виселитись пізніше?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Ранній заїзд (від 11:00) та пізній виїзд (до 15:00) можливі за наявності вільних апартаментів за попередньою домовленістю."
            }
          },
          {
            "@type": "Question",
            "name": "Чи є паркінг?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Так, на території комплексу є безкоштовна охоронювана парковка для гостей."
            }
          },
          {
            "@type": "Question",
            "name": "Як працює баня та чан?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Баня та чан бронюються окремо. Мінімальний сеанс — 2 години."
            }
          }
        ]
      }
    ]
  }
  </script>
</main>

<?php get_footer(); ?>