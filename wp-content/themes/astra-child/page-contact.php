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
  $wa_msg = rawurlencode('Добрий день! Хочу забронювати номер у готелі Гірська Лаванда.');
  $whatsapp_url = 'https://wa.me/' . $wa_phone . '?text=' . $wa_msg;
  $viber_url = 'viber://contact?number=' . $wa_phone;
}
$instagram = get_theme_mod('gl_instagram', 'https://www.instagram.com/girska_lavandaa/');
$facebook = get_theme_mod('gl_facebook', 'https://facebook.com/girskalavanda');
$maps_url = get_theme_mod('gl_maps_url', 'https://maps.app.goo.gl/s5NUR41X67xDFT5f8');

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
        <h1 class="gl-contact-hero__title">Зв'яжіться <em>з нами</em></h1>
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
              <span class="gl-contact-strip__value">с. Східниця, Львівська обл.</span>
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
                с. Східниця, Бориславська громада,<br>
                Львівська область, Україна
              </p>
              <p class="gl-contact-card__hint">GPS: 49.2192° N, 23.3509° E</p>
            </div>
          </div>



        </div>

        <!-- Right: Map -->
        <div class="gl-contact-map gl-animate gl-animate--delay-2">
          <div class="gl-contact-map__wrapper">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2606!2d23.35088!3d49.219197!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z0JPRltGA0YHRjNC60LAg0JvQsNCy0LDQvdC00LA!5e0!3m2!1suk!2sua!4v1"
              loading="lazy" referrerpolicy="no-referrer-when-downgrade"
              title="Розташування готелю Гірська Лаванда — Східниця"></iframe>
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
        <h2 class="gl-section-title">Як до нас дістатись</h2>
        <p class="gl-section-subtitle">Комплекс розташований у курортному містечку Східниця, в серці Карпат</p>
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
            <?php echo $icon_check; ?> Безкоштовна парковка на території готелю
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
            <span>Як забронювати номер?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Забронювати номер можна кількома способами: через наш <a href="/rooms/">сайт</a>, за телефоном
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
              наявності вільних номерів. Зв'яжіться з нами заздалегідь для уточнення.</p>
          </div>
        </details>

        <details class="gl-faq-item">
          <summary class="gl-faq-item__question">
            <span>Чи є паркінг?</span>
            <span class="gl-faq-item__toggle" aria-hidden="true"></span>
          </summary>
          <div class="gl-faq-item__answer">
            <p>Так, на території готелю є безкоштовна охоронювана парковка для гостей. Місця не потрібно бронювати
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
            <p>Так, безкоштовний Wi-Fi доступний у всіх номерах та на території готелю.</p>
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




</main>

<?php get_footer(); ?>