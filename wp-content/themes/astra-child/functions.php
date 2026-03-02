<?php
/**
 * Astra Child Theme — Гірська Лаванда
 * functions.php — головний файл функцій
 */

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

    // Google Fonts
    wp_enqueue_style(
        'gl-google-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600&family=Nunito+Sans:wght@300;400;600;700;800&display=swap',
        [],
        null
    );

    // Child theme CSS
    wp_enqueue_style(
        'astra-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        [ 'astra-theme-css', 'gl-google-fonts' ],
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
    $hide_templates = [ 'page-home.php', 'page-room.php', 'page-sauna.php' ];
    foreach ( $hide_templates as $tpl ) {
        if ( is_page_template( $tpl ) ) {
            return false;
        }
    }
    return $show;
}

// =============================================================================
// SHORTCODES
// =============================================================================

/**
 * [gl_advantages] — секція переваг готелю
 */
add_shortcode( 'gl_advantages', 'glav_sc_advantages' );
function glav_sc_advantages( $atts ) {
    $advantages = [
        [ 'icon' => '🌲', 'title' => 'Карпатський ліс',    'desc' => 'Готель оточений вічнозеленими карпатськими соснами. Чисте гірське повітря просто за вашими вікнами' ],
        [ 'icon' => '🧖', 'title' => 'Баня та Чан',        'desc' => 'Традиційна дерев\'яна баня і гарячий чан просто неба — справжнє карпатське оздоровлення для тіла і душі' ],
        [ 'icon' => '🍳', 'title' => 'Домашні сніданки',   'desc' => 'Щоранку свіжоприготовані сніданки з натуральних місцевих продуктів. Як вдома, тільки краще' ],
        [ 'icon' => '🏔️', 'title' => 'Вид на гори',       'desc' => 'Мальовничі карпатські краєвиди і ліс прямо з вікна вашого номера — кожен ранок як на листівці' ],
        [ 'icon' => '🅿️', 'title' => 'Паркінг',           'desc' => 'Безкоштовна охоронювана парковка на території готелю. Зручно для тих, хто приїжджає на авто' ],
        [ 'icon' => '💆', 'title' => 'Тиша і спокій',      'desc' => 'Ніяких клубів і гучних заходів. Тільки природа, відпочинок і атмосфера справжнього карпатського затишку' ],
    ];

    ob_start();
    ?>
    <section class="gl-section gl-advantages" id="advantages">
      <div class="gl-container">
        <div class="gl-advantages__header gl-animate">
          <span class="gl-section-label">Чому обирають нас</span>
          <h2 class="gl-section-title">Наші переваги</h2>
          <p class="gl-section-subtitle">Все для вашого ідеального відпочинку в серці Карпат</p>
        </div>
        <div class="gl-advantages__grid">
          <?php foreach ( $advantages as $i => $adv ) : ?>
          <div class="gl-advantage-item gl-animate gl-animate--delay-<?php echo min( $i + 1, 5 ); ?>">
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
 * [gl_testimonials] — секція відгуків
 */
add_shortcode( 'gl_testimonials', 'glav_sc_testimonials' );
function glav_sc_testimonials( $atts ) {
    $reviews = [
        [
            'name'   => 'Олена Коваль',
            'date'   => 'Лютий 2026',
            'rating' => 5,
            'text'   => 'Неймовірно затишний готель! Баня була просто казковою — ми провели там увесь вечір. Природа навколо приголомшлива, персонал дуже привітний. Обов\'язково повернемося!',
            'avatar' => 'О',
        ],
        [
            'name'   => 'Максим Петренко',
            'date'   => 'Січень 2026',
            'rating' => 5,
            'text'   => 'Чудовий відпочинок! Чан на вулиці взимку — це щось незабутнє. Зоряне небо, пар від гарячої води, свіже карпатське повітря. Номер чистий і комфортний.',
            'avatar' => 'М',
        ],
        [
            'name'   => 'Наталія та Андрій',
            'date'   => 'Грудень 2025',
            'rating' => 5,
            'text'   => 'Відмінне місце для романтичного відпочинку. Тихо, спокійно, красива природа. Сніданки дуже смачні! Розміщення зручне і чисте. Рекомендуємо всім друзям.',
            'avatar' => 'Н',
        ],
        [
            'name'   => 'Іван Мельник',
            'date'   => 'Листопад 2025',
            'rating' => 5,
            'text'   => 'Приїхали сім\'єю на 3 дні. Діти в захваті від лісу навколо. Баня справді традиційна, не турецька. Господарі дуже привітні, підказали найкращі маршрути для прогулянок.',
            'avatar' => 'І',
        ],
    ];

    ob_start();
    ?>
    <section class="gl-section gl-testimonials gl-section--white" id="reviews">
      <div class="gl-container">
        <div class="gl-testimonials__header gl-animate">
          <span class="gl-section-label">Гості про нас</span>
          <h2 class="gl-section-title">Відгуки</h2>
          <p class="gl-section-subtitle">Понад 200+ задоволених гостей довіряють нам свій відпочинок</p>
        </div>

        <div class="gl-testimonials__slider gl-animate">
          <div class="gl-testimonials__track">
            <?php foreach ( $reviews as $review ) : ?>
            <div class="gl-testimonial-card">
              <span class="gl-testimonial-card__quote">"</span>
              <div class="gl-testimonial-card__stars">
                <?php echo str_repeat( '★', $review['rating'] ); ?>
              </div>
              <p class="gl-testimonial-card__text"><?php echo esc_html( $review['text'] ); ?></p>
              <div class="gl-testimonial-card__author">
                <div class="gl-testimonial-card__avatar"><?php echo esc_html( $review['avatar'] ); ?></div>
                <div>
                  <div class="gl-testimonial-card__name"><?php echo esc_html( $review['name'] ); ?></div>
                  <div class="gl-testimonial-card__date"><?php echo esc_html( $review['date'] ); ?></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="gl-testimonials__nav">
          <button class="gl-testimonials__btn gl-testimonials__btn--prev" aria-label="Попередній">&#8592;</button>
          <div class="gl-testimonials__dots">
            <?php for ( $i = 0; $i < count( $reviews ); $i++ ) : ?>
            <button class="gl-testimonials__dot <?php echo $i === 0 ? 'is-active' : ''; ?>" aria-label="Відгук <?php echo $i + 1; ?>"></button>
            <?php endfor; ?>
          </div>
          <button class="gl-testimonials__btn gl-testimonials__btn--next" aria-label="Наступний">&#8594;</button>
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
            <!-- Карта: Східниця, Львівська область -->
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d20851.55!2d23.5120!3d49.1180!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4730e9b000000001%3A0x1!2z0KHRhdGW0LTQvdC40YbRjywg0JvRjNCy0ZbQstGB0YzQutCwINC-0LHQu9Cw0YHRgtGMLCDQo9C60YDQsNGX0L3QsA!5e0!3m2!1suk!2sua!4v1"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Розташування готелю Гірська Лаванда — Східниця"
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
    // Отримати 6 останніх медіафайлів з uploads
    $images = get_posts( [
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => 6,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ] );

    ob_start();
    ?>
    <section class="gl-section gl-gallery gl-section--sand" id="gallery">
      <div class="gl-container">
        <div class="gl-gallery__header gl-animate">
          <span class="gl-section-label">Фото</span>
          <h2 class="gl-section-title">Галерея</h2>
          <p class="gl-section-subtitle">Поглянь на наш готель очима гостей</p>
        </div>

        <div class="gl-gallery__grid gl-animate">
          <?php if ( ! empty( $images ) ) : ?>
            <?php foreach ( $images as $i => $img ) :
              $src = wp_get_attachment_image_url( $img->ID, 'large' );
              $full= wp_get_attachment_image_url( $img->ID, 'full' );
            ?>
              <div class="gl-gallery__item" data-src="<?php echo esc_url( $full ); ?>">
                <img src="<?php echo esc_url( $src ); ?>"
                     alt="<?php echo esc_attr( $img->post_title ); ?>"
                     loading="lazy" />
                <div class="gl-gallery__item-overlay">
                  <span class="gl-gallery__item-overlay-icon">🔍</span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <?php
            $placeholders = ['🏨', '🛏️', '🌲', '🧖', '🏔️', '🌸'];
            $gradients    = [
              'linear-gradient(135deg,#1C3A0E,#4a8c3f)',
              'linear-gradient(135deg,#5C1F00,#9B3A0E)',
              'linear-gradient(135deg,#7B5EA7,#9B7FC7)',
              'linear-gradient(135deg,#0E2147,#1A3D7C)',
              'linear-gradient(135deg,#2D5A1A,#7B5EA7)',
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
        'default'           => 'Парк готель · Східниця · Карпати',
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
 * Повертає MPHB amenities якщо встановлені, інакше — дефолтний набір.
 */
function glav_get_room_amenities( $room_type_id ) {

    // 1. Намагаємось отримати з MPHB amenity post type
    $amenity_ids = get_post_meta( $room_type_id, 'mphb_amenities', true );
    if ( ! empty( $amenity_ids ) ) {
        return array_filter( array_map( 'get_the_title', (array) $amenity_ids ) );
    }

    // 2. Smart defaults на основі назви номеру
    $title = mb_strtolower( get_the_title( $room_type_id ), 'UTF-8' );

    $base = [
        'WiFi безкоштовний',
        'Власний санвузол',
        'Гаряча вода цілодобово',
        'Домашні сніданки',
        'Безкоштовний паркінг',
        'Карпатські краєвиди',
    ];

    if ( str_contains( $title, 'тераса' ) || str_contains( $title, 'апарт' ) ) {
        $base[] = 'Власна тераса';
        $base[] = 'Холодильник';
        $base[] = 'Телевізор';
    }
    if ( str_contains( $title, 'сімей' ) ) {
        $base[] = 'Місце для дітей';
        $base[] = 'Дитяче ліжко за запитом';
    }
    if ( str_contains( $title, 'двопов' ) ) {
        $base[] = 'Два поверхи';
        $base[] = 'Кухонна зона';
        $base[] = 'Кавоварка';
    }

    return $base;
}
