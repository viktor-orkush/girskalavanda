<?php
/**
 * Astra Child Theme — Гірська Лаванда
 * functions.php — головний файл функцій
 */

// =============================================================================
// PERFORMANCE: LCP
// =============================================================================

/**
 * Preload the Hero Image for better LCP (Largest Contentful Paint).
 */
add_action( 'wp_head', 'glav_preload_hero_image', 1 );
function  glav_preload_hero_image() {
    $hero_image = get_theme_mod( 'gl_hero_image', '' );
    if ( is_front_page() && $hero_image ) {
        echo '<link rel="preload" as="image" href="' . esc_url( $hero_image ) . '" />';
    }
}

// =============================================================================
// ENQUEUE STYLES & SCRIPTS
// =============================================================================
add_action( 'wp_enqueue_scripts', 'glav_enqueue_assets' );
function glav_enqueue_assets() {
    // Parent theme (Astra)
    wp_enqueue_style(
        'astra-theme-css',
        get_template_directory_uri() . '/style.css'
    );

    // Child theme CSS (fonts included via style.css)
    wp_enqueue_style(
        'astra-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        [ 'astra-theme-css' ],
        wp_get_theme()->get( 'Version' )
    );

    // Main JS
    wp_enqueue_script(
        'gl-main-js',
        get_stylesheet_directory_uri() . '/assets/js/main.js',
        [],
        wp_get_theme()->get( 'Version' ),
        true // load in footer
    );
}

// =============================================================================
// BODY CLASSES
// =============================================================================
add_filter( 'body_class', 'glav_body_classes' );
function glav_body_classes( $classes ) {
    // Add page slug as class
    if ( is_page() ) {
        global $post;
        $classes[] = 'gl-page-' . $post->post_name;
    }
    return $classes;
}

// =============================================================================
// HIDE DEFAULT PAGE TITLE for custom templates
// =============================================================================
add_filter( 'astra_the_title_enabled', 'glav_hide_title_on_templates' );
function glav_hide_title_on_templates( $show ) {
    // Templates assigned via WP admin
    $hide_templates = [ 'page-home.php' ];
    foreach ( $hide_templates as $tpl ) {
        if ( is_page_template( $tpl ) ) {
            return false;
        }
    }
    // Slug-based templates (auto-applied by WP template hierarchy)
    $hide_slugs = [ 'banya', 'chan', 'contact', 'rooms', 'gallery' ];
    if ( is_page( $hide_slugs ) ) {
        return false;
    }
    return $show;
}

// =============================================================================
// FIX MOBILE MENU — use same curated nav as desktop
// =============================================================================
// Astra's mobile menu checks has_nav_menu('mobile_menu'). If no menu is assigned
// to that location, it falls back to wp_page_menu() listing ALL pages.
// Fix: sync the 'mobile_menu' location to use the same menu as 'primary'.
add_filter( 'theme_mod_nav_menu_locations', 'glav_sync_mobile_menu' );
function glav_sync_mobile_menu( $locations ) {
    if ( empty( $locations['mobile_menu'] ) && ! empty( $locations['primary'] ) ) {
        $locations['mobile_menu'] = $locations['primary'];
    }
    return $locations;
}

add_action( 'wp_head', 'glav_custom_hero_styles', 100 );
function glav_custom_hero_styles() {
    ?>
    <style id="glav-hero-fix">
        .gl-banya-hero {
            background-image: var(--banya-hero-bg) !important;
            background-position: center bottom !important;
            background-size: cover !important;
            background-repeat: no-repeat !important;
        }
        @media (max-width: 768px) {
            .gl-banya-hero {
                background-position: 50% 50% !important;
            }
        }
    </style>
    <?php
}

// =============================================================================
// PHONE NUMBER IN HEADER NAV
// =============================================================================
add_filter( 'wp_nav_menu_items', 'glav_add_phone_to_menu', 10, 2 );
function glav_add_phone_to_menu( $items, $args ) {
    // Add to primary and mobile menu
    if ( ! in_array( $args->theme_location, [ 'primary', 'mobile_menu' ], true ) ) {
        return $items;
    }

    $phone      = get_theme_mod( 'gl_phone', '' );
    $phone_disp = get_theme_mod( 'gl_phone_display', $phone );

    if ( $phone ) {
        $items .= '<li class="menu-item menu-item-phone">'
                 . '<a href="tel:' . esc_attr( $phone ) . '">'
                 . '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:5px"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.5 11.61a19.79 19.79 0 01-3.07-8.67A2 2 0 012.42 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 8.9a16 16 0 006.15 6.15l1.27-.77a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>'
                 . esc_html( $phone_disp ?: $phone )
                 . '</a></li>';
    }

    return $items;
}

// =============================================================================
// SHORTCODES
// =============================================================================

/**
 * [gl_advantages] — секція переваг комплексу
 */
add_shortcode( 'gl_advantages', 'glav_sc_advantages' );
function glav_sc_advantages( $atts ) {
    $advantages = [
        [ 'icon' => '🌲', 'title' => 'Карпатський ліс',    'desc' => 'Комплекс оточений вічнозеленими карпатськими соснами. Чисте гірське повітря просто за вашими вікнами' ],
        [ 'icon' => '🧖', 'title' => 'Баня та Чан',        'desc' => 'Традиційна дерев\'яна баня і гарячий чан просто неба — справжнє карпатське оздоровлення для тіла і душі' ],
        [ 'icon' => '🏔️', 'title' => 'Вид на гори',       'desc' => 'Мальовничі карпатські краєвиди і ліс прямо з вікна вашого номера — кожен ранок як на листівці' ],
        [ 'icon' => '🅿️', 'title' => 'Паркінг',           'desc' => 'Безкоштовна охоронювана парковка на території комплексу. Зручно для тих, хто приїжджає на авто' ],
        [ 'icon' => '💆', 'title' => 'Тиша і спокій',      'desc' => 'Ніяких клубів і гучних заходів. Тільки природа, відпочинок і атмосфера справжнього карпатського затишку' ],
    ];

    ob_start();
    ?>
    <section class="gl-section gl-advantages" id="advantages">
      <div class="gl-container">
        <div class="gl-advantages__header gl-animate gl-animate--blur">
          <span class="gl-section-label">Чому обирають нас</span>
          <h2 class="gl-section-title">Наші переваги</h2>
          <p class="gl-section-subtitle">Все для вашого ідеального відпочинку в серці Карпат</p>
        </div>
        <div class="gl-advantages__grid">
          <?php foreach ( $advantages as $i => $adv ) : ?>
          <div class="gl-advantage-item gl-animate gl-animate--scale gl-animate--delay-<?php echo min( $i + 1, 5 ); ?>">
            <div class="gl-advantage-item__icon" aria-hidden="true"><?php echo $adv['icon']; ?></div>
            <h3 class="gl-advantage-item__title"><?php echo esc_html( $adv['title'] ); ?></h3>
            <p class="gl-advantage-item__desc"><?php echo esc_html( $adv['desc'] ); ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * [gl_contacts] — секція контактів з картою
 */
add_shortcode( 'gl_contacts', 'glav_sc_contacts' );
function glav_sc_contacts( $atts ) {
    ob_start();
    ?>
    <section class="gl-section gl-contacts" id="contacts">
      <div class="gl-container">
        <div class="gl-contacts__grid">
          <div class="gl-contacts__info gl-animate">
            <span class="gl-section-label">Зв'яжіться з нами</span>
            <h2 class="gl-contacts__title">Контакти</h2>
            <p class="gl-contacts__subtitle">Ми знаходимось у мальовничому містечку Східниця — серці Карпат. Будемо раді вас прийняти!</p>

            <div class="gl-contacts__items">
              <div class="gl-contact-item">
                <div class="gl-contact-item__icon">📍</div>
                <div>
                  <div class="gl-contact-item__label">Адреса</div>
                  <div class="gl-contact-item__value">с. Східниця, Бориславська громада,<br>Львівська область, Україна</div>
                </div>
              </div>
              <?php
              $phone     = get_theme_mod( 'gl_phone', '' );
              $phone_disp= get_theme_mod( 'gl_phone_display', $phone );
              if ( $phone ) : ?>
              <div class="gl-contact-item">
                <div class="gl-contact-item__icon">📞</div>
                <div>
                  <div class="gl-contact-item__label">Телефон / Viber / WhatsApp</div>
                  <div class="gl-contact-item__value"><a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone_disp ?: $phone ); ?></a></div>
                </div>
              </div>
              <?php endif; ?>
              <div class="gl-contact-item">
                <div class="gl-contact-item__icon">✉️</div>
                <div>
                  <div class="gl-contact-item__label">Email</div>
                  <div class="gl-contact-item__value"><a href="mailto:info@girskalavanda.com">info@girskalavanda.com</a></div>
                </div>
              </div>
              <div class="gl-contact-item">
                <div class="gl-contact-item__icon">🕐</div>
                <div>
                  <div class="gl-contact-item__label">Заселення / виселення</div>
                  <div class="gl-contact-item__value">Check-in від 14:00 &nbsp;·&nbsp; Check-out до 12:00</div>
                </div>
              </div>
              <?php
              $instagram = get_theme_mod( 'gl_instagram', '' );
              $facebook  = get_theme_mod( 'gl_facebook', '' );
              if ( $instagram || $facebook ) : ?>
              <div class="gl-contact-item">
                <div class="gl-contact-item__icon">🌐</div>
                <div>
                  <div class="gl-contact-item__label">Соцмережі</div>
                  <div class="gl-contact-item__value">
                    <?php if ( $instagram ) : ?><a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
                    <?php if ( $instagram && $facebook ) : ?>&nbsp;·&nbsp;<?php endif; ?>
                    <?php if ( $facebook ) : ?><a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
                  </div>
                </div>
              </div>
              <?php endif; ?>
            </div>

            <a href="#booking" class="gl-btn gl-btn--primary">
              📅 Забронювати номер
            </a>
          </div>

          <div class="gl-contacts__map gl-animate gl-animate--delay-2">
            <!-- Карта: Гірська Лаванда, Східниця -->
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2606!2d23.35088!3d49.219197!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z0JPRltGA0YHRjNC60LAg0JvQsNCy0LDQvdC00LA!5e0!3m2!1suk!2sua!4v1"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Розташування комплексу Гірська Лаванда — Східниця"
            ></iframe>
          </div>
        </div>
      </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * [gl_gallery_preview] — превью галерея на головній
 */
add_shortcode( 'gl_gallery_preview', 'glav_sc_gallery_preview' );
function glav_sc_gallery_preview( $atts ) {
    // Отримати 5 медіафайлів (відповідає макету: 1 велике зліва + 2×2 справа)
    $images = get_posts( [
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => 5,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ] );

    ob_start();
    ?>
    <section class="gl-section gl-gallery gl-section--sand" id="gallery">
      <div class="gl-container">
        <div class="gl-gallery__header gl-animate gl-animate--blur">
          <span class="gl-section-label">Фото</span>
          <h2 class="gl-section-title">Галерея</h2>
          <p class="gl-section-subtitle">Поглянь на Гірську Лаванду очима гостей</p>
        </div>

        <div class="gl-gallery__grid gl-animate gl-animate--scale">
          <?php if ( ! empty( $images ) ) : ?>
            <?php foreach ( $images as $i => $img ) :
              $full = wp_get_attachment_image_url( $img->ID, 'full' );
            ?>
              <div class="gl-gallery__item" data-src="<?php echo esc_url( $full ); ?>">
                <?php echo wp_get_attachment_image( $img->ID, 'large', false, [ 'loading' => 'lazy' ] ); ?>
                <div class="gl-gallery__item-overlay">
                  <span class="gl-gallery__item-overlay-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <?php
            $placeholders = ['🏨', '🛏️', '🌲', '🧖', '🏔️', '🌸'];
            $gradients    = [
              'linear-gradient(135deg,#1C3A0E,#4a8c3f)',
              'linear-gradient(135deg,#5C1F00,#9B3A0E)',
              'linear-gradient(135deg,#2D5A1A,#4a8c3f)',
              'linear-gradient(135deg,#0E2147,#1A3D7C)',
              'linear-gradient(135deg,#1C3A0E,#2D5A1A)',
              'linear-gradient(135deg,#C8A951,#9B3A0E)',
            ];
            foreach ( $placeholders as $i => $ph ) : ?>
            <div class="gl-gallery__item">
              <div class="gl-gallery__placeholder" style="background:<?php echo $gradients[ $i ]; ?>; font-size: 48px;">
                <?php echo $ph; ?>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="gl-gallery__footer">
          <a href="/gallery" class="gl-btn gl-btn--outline-dark">Переглянути всі фото</a>
        </div>
      </div>
    </section>

    <!-- Lightbox -->
    <div class="gl-lightbox" role="dialog" aria-modal="true" aria-label="Фото галерея">
      <button class="gl-lightbox__close" aria-label="Закрити">✕</button>
      <button class="gl-lightbox__prev" aria-label="Попереднє фото">&#8592;</button>
      <img class="gl-lightbox__img" src="" alt="Фото галереї" />
      <button class="gl-lightbox__next" aria-label="Наступне фото">&#8594;</button>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * [gl_testimonials] — секція відгуків гостей
 */
add_shortcode( 'gl_testimonials', 'glav_sc_testimonials' );
function glav_sc_testimonials( $atts ) {
    $reviews = [
        [
            'name'   => 'Оксана М.',
            'date'   => 'Лютий 2026',
            'init'   => 'О',
            'stars'  => 5,
            'text'   => 'Надзвичайно затишне місце! Баня з чаном — це щось неймовірне після прогулянки у горах. Номер просторий, чисто, персонал уважний. Обовʼязково повернемося влітку.',
        ],
        [
            'name'   => 'Андрій та Юля',
            'date'   => 'Грудень 2025',
            'init'   => 'А',
            'stars'  => 5,
            'text'   => 'Провели тут новорічні свята — і не пожалкували. Карпатська природа, тиша, неймовірний хамам. Відчуваєш справжній відпочинок. Дякуємо команді за увагу до деталей!',
        ],
        [
            'name'   => 'Тетяна К.',
            'date'   => 'Жовтень 2025',
            'init'   => 'Т',
            'stars'  => 5,
            'text'   => 'Бронювала двоповерхові апартаменти для сімʼї. Дуже комфортно, є все необхідне. Вид на ліс з тераси — просто казка. Дітям дуже сподобалося, особливо гарячий чан під відкритим небом.',
        ],
        [
            'name'   => 'Роман Г.',
            'date'   => 'Серпень 2025',
            'init'   => 'Р',
            'stars'  => 5,
            'text'   => 'Ідеальне місце для відновлення сил. Повна тиша, чисте гірське повітря і справжня карпатська атмосфера. Традиційна баня з березовими віниками — окремий вид мистецтва.',
        ],
    ];

    ob_start();
    ?>
    <section class="gl-section gl-testimonials gl-section--dark" id="testimonials">
      <div class="gl-container">
        <div class="gl-testimonials__header gl-animate gl-animate--blur">
          <span class="gl-section-label">Відгуки</span>
          <h2 class="gl-section-title">Що кажуть гості</h2>
          <p class="gl-section-subtitle">Щирі враження тих, хто вже відпочив у Гірській Лаванді</p>
        </div>

        <div class="gl-testimonials__slider">
          <div class="gl-testimonials__track">
            <?php foreach ( $reviews as $r ) : ?>
            <div class="gl-testimonial-card gl-animate gl-animate--scale">
              <span class="gl-testimonial-card__quote">&ldquo;</span>
              <div class="gl-testimonial-card__stars"><?php echo str_repeat( '★', $r['stars'] ); ?></div>
              <p class="gl-testimonial-card__text"><?php echo esc_html( $r['text'] ); ?></p>
              <div class="gl-testimonial-card__author">
                <div class="gl-testimonial-card__avatar"><?php echo esc_html( $r['init'] ); ?></div>
                <div>
                  <div class="gl-testimonial-card__name"><?php echo esc_html( $r['name'] ); ?></div>
                  <div class="gl-testimonial-card__date"><?php echo esc_html( $r['date'] ); ?></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <nav class="gl-testimonials__nav" aria-label="Навігація відгуків">
          <button class="gl-testimonials__btn gl-testimonials__btn--prev" aria-label="Попередній відгук">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="gl-testimonials__dots">
            <?php foreach ( $reviews as $i => $r ) : ?>
            <button class="gl-testimonials__dot<?php echo $i === 0 ? ' is-active' : ''; ?>" aria-label="Відгук <?php echo $i + 1; ?>"></button>
            <?php endforeach; ?>
          </div>
          <button class="gl-testimonials__btn gl-testimonials__btn--next" aria-label="Наступний відгук">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </nav>
      </div>
    </section>
    <?php
    return ob_get_clean();
}

// =============================================================================
// THEME SUPPORT
// =============================================================================
add_action( 'after_setup_theme', 'glav_theme_support' );
function glav_theme_support() {
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption' ] );
}

// =============================================================================
// CUSTOMIZER — налаштування теми
// =============================================================================
add_action( 'customize_register', 'glav_customize_register' );
function glav_customize_register( $wp_customize ) {

    // Панель: Гірська Лаванда
    $wp_customize->add_panel( 'gl_panel', [
        'title'    => 'Гірська Лаванда',
        'priority' => 30,
    ] );

    // Секція: Hero
    $wp_customize->add_section( 'gl_hero', [
        'title' => 'Hero секція',
        'panel' => 'gl_panel',
    ] );

    // Hero фото
    $wp_customize->add_setting( 'gl_hero_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control(
        $wp_customize, 'gl_hero_image', [
            'label'   => 'Фонове фото Hero (рекомендовано 1920×1080)',
            'section' => 'gl_hero',
        ]
    ) );

    // Hero заголовок
    $wp_customize->add_setting( 'gl_hero_title', [
        'default'           => "Гірська\nЛаванда",
        'sanitize_callback' => 'sanitize_textarea_field',
    ] );
    $wp_customize->add_control( 'gl_hero_title', [
        'label'   => 'Заголовок Hero',
        'section' => 'gl_hero',
        'type'    => 'textarea',
    ] );

    // Hero підзаголовок
    $wp_customize->add_setting( 'gl_hero_subtitle', [
        'default'           => 'Заміський комплекс · Східниця · Карпати',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'gl_hero_subtitle', [
        'label'   => 'Підзаголовок Hero',
        'section' => 'gl_hero',
        'type'    => 'text',
    ] );

    // Секція: Контакти
    $wp_customize->add_section( 'gl_contacts', [
        'title' => 'Контакти',
        'panel' => 'gl_panel',
    ] );

    $wp_customize->add_setting( 'gl_phone', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'gl_phone', [
        'label'   => 'Телефон (у форматі +380XXXXXXXXX)',
        'section' => 'gl_contacts',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'gl_phone_display', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'gl_phone_display', [
        'label'   => 'Телефон для відображення',
        'section' => 'gl_contacts',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'gl_instagram', [
        'default'           => 'https://instagram.com/girskalavanda',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'gl_instagram', [
        'label'   => 'Instagram URL',
        'section' => 'gl_contacts',
        'type'    => 'url',
    ] );

    $wp_customize->add_setting( 'gl_facebook', [
        'default'           => 'https://facebook.com/girskalavanda',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'gl_facebook', [
        'label'   => 'Facebook URL',
        'section' => 'gl_contacts',
        'type'    => 'url',
    ] );

    $wp_customize->add_setting( 'gl_telegram', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'gl_telegram', [
        'label'       => 'Telegram (username без @ або повний URL)',
        'description' => 'Наприклад: girskalavanda або https://t.me/girskalavanda',
        'section'     => 'gl_contacts',
        'type'        => 'text',
    ] );

    $wp_customize->add_setting( 'gl_maps_url', [
        'default'           => 'https://maps.app.goo.gl/s5NUR41X67xDFT5f8',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'gl_maps_url', [
        'label'       => 'Google Maps посилання',
        'description' => 'Пряме посилання на Google Maps для готелю (використовується у футері та на сторінці контактів)',
        'section'     => 'gl_contacts',
        'type'        => 'url',
    ] );
}

// =============================================================================
// ROOM PAGE HELPERS (single-mphb_room_type.php)
// =============================================================================

/**
 * Get minimum price for a room type from its linked MPHB rate.
 */
function glav_get_room_price( $room_type_id ) {
    global $wpdb;

    // Знаходимо опублікований rate (mphb_rate) для цього room type
    $rate_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT pm.post_id FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             AND p.post_type = 'mphb_rate' AND p.post_status = 'publish'
         WHERE pm.meta_key = 'mphb_room_type_id' AND pm.meta_value = %d
         LIMIT 1",
        $room_type_id
    ) );

    if ( ! $rate_id ) return 0;

    // Ціна зберігається в mphb_season_prices як серіалізований масив:
    // [ 0 => [ 'season' => '...', 'price' => [ 'prices' => [ 0 => 2500 ] ] ] ]
    $season_prices = get_post_meta( (int) $rate_id, 'mphb_season_prices', true );

    if ( ! empty( $season_prices ) && is_array( $season_prices ) ) {
        $first = reset( $season_prices );
        if ( isset( $first['price']['prices'][0] ) ) {
            return (float) $first['price']['prices'][0];
        }
    }

    return 0;
}

/**
 * Get bed type string for a room type via MPHB taxonomy.
 */
function glav_get_room_bed_type( $room_type_id ) {
    // MPHB зберігає тип ліжка напряму в meta 'mphb_bed'
    $bed = get_post_meta( $room_type_id, 'mphb_bed', true );
    if ( $bed ) return $bed;

    // Запасний варіант — taxonomy mphb_bed_type
    $terms = get_the_terms( $room_type_id, 'mphb_bed_type' );
    if ( $terms && ! is_wp_error( $terms ) ) {
        return implode( ' / ', wp_list_pluck( $terms, 'name' ) );
    }
    return '';
}

/**
 * Get amenities list for a room type.
 * Priority: MPHB facilities taxonomy → smart defaults.
 */
function glav_get_room_amenities( $room_type_id ) {

    // 1. MPHB facilities taxonomy (mphb_room_type_facility)
    $facility_terms = get_the_terms( $room_type_id, 'mphb_room_type_facility' );
    if ( $facility_terms && ! is_wp_error( $facility_terms ) && count( $facility_terms ) > 0 ) {
        $list = wp_list_pluck( $facility_terms, 'name' );

        // Append room-specific extras not captured by taxonomy
        $title = mb_strtolower( get_the_title( $room_type_id ), 'UTF-8' );

        if ( str_contains( $title, 'двопов' ) ) {
            $list[] = 'Два поверхи';
        }
        if ( str_contains( $title, 'сімей' ) ) {
            $list[] = 'Місце для дітей';
        }
        // Baня і чан — shared hotel facilities
        $list[] = 'Баня та чан (спільне)';

        return array_unique( $list );
    }

    // 2. Smart defaults based on room title (fallback when no taxonomy set)
    $title = mb_strtolower( get_the_title( $room_type_id ), 'UTF-8' );

    $base = [
        'Безкоштовний WI-FI',
        'Великий LCD TV',
        'Набір рушників',
        'Санвузол',
        'Безкоштовний паркінг',
    ];

    if ( str_contains( $title, 'тераса' ) ) {
        $base[] = 'Власна тераса';
    }
    if ( str_contains( $title, 'сімей' ) ) {
        $base[] = 'Місце для дітей';
    }
    if ( str_contains( $title, 'двопов' ) ) {
        $base[] = 'Два поверхи';
        $base[] = 'Кухонна зона';
        $base[] = 'Холодильник';
        $base[] = 'Ортопедичний матрац';
    }
    $base[] = 'Баня та чан (спільне)';

    return $base;
}

// =============================================================================
// BOOKING CONTACT DATA HELPER
// =============================================================================
/**
 * Build contact data array for the booking-contact template part.
 *
 * @param string $wa_message  WhatsApp pre-filled message text.
 * @return array  Data ready for get_template_part( 'template-parts/booking-contact', null, $data ).
 */
function glav_get_contact_data( $wa_message = '' ) {
    $phone      = get_theme_mod( 'gl_phone', '' );
    $phone_disp = get_theme_mod( 'gl_phone_display', $phone );

    // Telegram
    $telegram_raw = get_theme_mod( 'gl_telegram', '' );
    $telegram_url = '';
    if ( $telegram_raw ) {
        $telegram_url = str_starts_with( $telegram_raw, 'http' )
            ? $telegram_raw
            : 'https://t.me/' . ltrim( $telegram_raw, '@/' );
    }

    // WhatsApp & Viber
    $whatsapp_url = '';
    $viber_url    = '';
    if ( $phone ) {
        $wa_phone     = preg_replace( '/[^0-9]/', '', $phone );
        $whatsapp_url = 'https://wa.me/' . $wa_phone;
        if ( $wa_message ) {
            $whatsapp_url .= '?text=' . rawurlencode( $wa_message );
        }
        $viber_url = 'viber://contact?number=' . $wa_phone;
    }

    // Instagram (fallback to hardcoded profile if theme_mod not set)
    $instagram_dm  = 'https://ig.me/m/girska_lavandaa';
    $instagram_raw = get_theme_mod( 'gl_instagram', '' );
    if ( $instagram_raw ) {
        preg_match( '/instagram\.com\/([^\/\?#]+)/i', $instagram_raw, $m );
        $ig_user      = isset( $m[1] ) ? trim( $m[1], '/' ) : '';
        $instagram_dm = $ig_user ? 'https://ig.me/m/' . $ig_user : $instagram_raw;
    }

    return [
        'phone'         => $phone,
        'phone_display' => $phone_disp,
        'telegram_url'  => $telegram_url,
        'whatsapp_url'  => $whatsapp_url,
        'viber_url'     => $viber_url,
        'instagram_dm'  => $instagram_dm,
    ];
}

// =============================================================================
// SCROLL-TO-TOP BUTTON
// =============================================================================

// Disable Astra's built-in scroll-to-top button (blue arrow) to avoid duplicate
add_filter( 'astra_get_option_scroll-to-top-enable', '__return_false' );

add_action( 'wp_footer', 'glav_scroll_to_top_button' );
function glav_scroll_to_top_button() {
    ?>
    <button class="gl-scroll-top" id="gl-scroll-top"
            aria-label="Прокрутити вгору"
            title="Вгору">
      <svg class="gl-scroll-top__progress" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50">
        <circle class="gl-scroll-top__progress-circle"
                cx="25" cy="25" r="22"
                stroke-dasharray="138.23"
                stroke-dashoffset="138.23"></circle>
      </svg>
      <span class="gl-scroll-top__icon" aria-hidden="true">&#8593;</span>
    </button>
    <?php
}
