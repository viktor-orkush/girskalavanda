<?php
/**
 * page-rooms.php — Сторінка «Номери»
 * Автоматично застосовується до сторінки зі слагом "rooms"
 */

get_header();

$page_id = get_the_ID();

// === Hero image — fallback to first room's featured image ===
$hero_id = get_post_thumbnail_id($page_id);
$hero_url = $hero_id ? wp_get_attachment_image_url($hero_id, 'full') : '';
if (!$hero_url) {
  // Try using first room's image as fallback
  $first_room = get_posts([
    'post_type' => 'mphb_room_type',
    'posts_per_page' => 1,
    'post_status' => 'publish',
    'orderby' => 'menu_order',
    'order' => 'ASC',
  ]);
  if (!empty($first_room)) {
    $hero_url = get_the_post_thumbnail_url($first_room[0]->ID, 'full') ?: '';
  }
}
if (!$hero_url) {
  $uploads = wp_upload_dir();
  $hero_url = $uploads['baseurl'] . '/2025/07/L77A2868-Pano.jpg';
}

// === Contact info ===
$phone = get_theme_mod('gl_phone', '');
$phone_disp = get_theme_mod('gl_phone_display', $phone);

// === Get all room types ===
$rooms = get_posts([
  'post_type' => 'mphb_room_type',
  'posts_per_page' => -1,
  'post_status' => 'publish',
  'orderby' => 'menu_order',
  'order' => 'ASC',
]);

// Count totals for stats
$room_count = count($rooms);
$min_price = PHP_INT_MAX;
$max_guests = 0;
foreach ($rooms as $r) {
  $p = function_exists('glav_get_room_price') ? glav_get_room_price($r->ID) : 0;
  if ($p > 0 && $p < $min_price)
    $min_price = $p;
  $adults = (int)get_post_meta($r->ID, 'mphb_adults_capacity', true);
  $kids = (int)get_post_meta($r->ID, 'mphb_children_capacity', true);
  if (($adults + $kids) > $max_guests)
    $max_guests = $adults + $kids;
}
if ($min_price === PHP_INT_MAX)
  $min_price = 0;

// === Contact info for booking ===
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
  $whatsapp_url = 'https://wa.me/' . $wa_phone . '?text=' . rawurlencode('Добрий день! Хочу забронювати номер.');
  $viber_url = 'viber://contact?number=' . $wa_phone;
}
$instagram_dm = '';
$ig_raw = get_theme_mod('gl_instagram', '');
if ($ig_raw) {
  preg_match('/instagram\.com\/([^\/\?#]+)/i', $ig_raw, $m);
  $ig_user = isset($m[1]) ? trim($m[1], '/') : '';
  $instagram_dm = $ig_user ? 'https://ig.me/m/' . $ig_user : $ig_raw;
}
?>
<?php
// === Prepare Schema.org ItemList ===
$schema_items = [];
foreach ($rooms as $index => $r) {
  $p = function_exists('glav_get_room_price') ? glav_get_room_price($r->ID) : 0;
  $schema_items[] = [
    '@type' => 'ListItem',
    'position' => $index + 1,
    'item' => [
      '@type' => 'Apartment',
      'url' => get_permalink($r->ID),
      'name' => get_the_title($r->ID),
      'image' => get_the_post_thumbnail_url($r->ID, 'full') ?: '',
      'offers' => [
        '@type' => 'Offer',
        'price' => $p,
        'priceCurrency' => 'UAH'
      ]
    ]
  ];
}
$schema_json = wp_json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'ItemList',
  'name' => 'Апартаменти та Номери у Східниці — Гірська Лаванда',
  'itemListElement' => $schema_items
]);
?>
<main id="main" class="gl-rooms-page">
  <script type="application/ld+json"><?php echo $schema_json; ?></script>

  <!-- ======================================================================
       HERO
       ====================================================================== -->
  <section class="gl-rooms-hero" style="--rooms-hero-bg: url('<?php echo esc_url($hero_url); ?>')">
    <div class="gl-rooms-hero__overlay"></div>

    <div class="gl-rooms-hero__content">
      <div class="gl-container">
        <p class="gl-rooms-hero__label">Розміщення · Комплекс Гірська Лаванда</p>
        <h1 class="gl-rooms-hero__title">Наші <em>Апартаменти</em> у Східниці</h1>
        <p class="gl-rooms-hero__subtitle">Затишні апартаменти та номери серед Східницьких сосен — від компактних до розкішних двоповерхових з терасою</p>
        <div class="gl-rooms-hero__actions">
          <a href="#rooms-list" class="gl-btn gl-btn--gold">Переглянути номери</a>
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
    <div class="gl-hero-stats-bar" id="rooms-stats">
      <div class="gl-container">
        <div class="gl-hero-stats-grid">
          <div class="gl-hero-stat gl-animate">
            <span class="gl-hero-stat__num">
              <?php echo $room_count; ?>
            </span>
            <span class="gl-hero-stat__label">
              <?php echo $room_count <= 4 ? 'типи номерів' : 'типів номерів'; ?>
            </span>
          </div>
          <div class="gl-hero-stat gl-animate gl-animate--delay-1">
            <span class="gl-hero-stat__num">до&nbsp;
              <?php echo $max_guests; ?>
            </span>
            <span class="gl-hero-stat__label">гостей у номері</span>
          </div>
          <div class="gl-hero-stat gl-animate gl-animate--delay-2">
            <span class="gl-hero-stat__num">від&nbsp;
              <?php echo $min_price ? number_format($min_price, 0, '.', ' ') : '—'; ?>
            </span>
            <span class="gl-hero-stat__label">грн за ніч</span>
          </div>
          <div class="gl-hero-stat gl-animate gl-animate--delay-3">
            <span class="gl-hero-stat__num">365</span>
            <span class="gl-hero-stat__label">днів на рік</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll indicator -->
    <a href="#rooms-list" class="gl-rooms-hero__scroll" aria-label="Прокрутити до списку номерів">
      <span class="gl-rooms-hero__scroll-line"></span>
    </a>
  </section>


  <!-- ======================================================================
       ROOM TYPES LISTING
       ====================================================================== -->
  <section class="gl-section gl-rooms-listing gl-section--white" id="rooms-list">
    <div class="gl-container">
      <div class="gl-rooms-listing__header gl-animate">
        <span class="gl-section-label">Варіанти розміщення</span>
        <h2 class="gl-section-title">Доступні номери та апартаменти в Гірська Лаванда</h2>
        <p class="gl-section-subtitle">Кожен варіант — окрема атмосфера затишку і комфорту в серці Східниці</p>
      </div>

      <?php if (!empty($rooms)): ?>
      <div class="gl-rooms-listing__list">
        <?php foreach ($rooms as $i => $room):
    $room_id = $room->ID;
    $thumb_id = get_post_thumbnail_id($room_id);
    $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';
    $price = function_exists('glav_get_room_price') ? glav_get_room_price($room_id) : 0;
    $adults = (int)get_post_meta($room_id, 'mphb_adults_capacity', true);
    $children = (int)get_post_meta($room_id, 'mphb_children_capacity', true);
    $size = get_post_meta($room_id, 'mphb_size', true);
    $bed_type = function_exists('glav_get_room_bed_type') ? glav_get_room_bed_type($room_id) : '';
    $view = get_post_meta($room_id, 'mphb_view', true);
    $permalink = get_permalink($room_id);
    $amenities = function_exists('glav_get_room_amenities') ? glav_get_room_amenities($room_id) : [];
    $excerpt = get_post_field('post_excerpt', $room_id);
    if (!$excerpt) {
      $excerpt = wp_trim_words(get_post_field('post_content', $room_id), 30);
    }

    // Gallery images
    $gallery_str = get_post_meta($room_id, 'mphb_gallery', true);
    $gallery_ids = $gallery_str ? array_filter(array_map('intval', explode(',', $gallery_str))) : [];
    $gallery_count = count($gallery_ids) + ($thumb_id ? 1 : 0);

    // Room-specific WhatsApp message
    $room_title = get_the_title($room_id);
    $room_wa_url = $phone ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $phone) . '?text=' . rawurlencode('Добрий день! Цікавить номер «' . $room_title . '».') : '';

    $is_reversed = $i % 2 !== 0;
?>
        <article class="gl-rooms-card gl-animate <?php echo $is_reversed ? 'gl-rooms-card--reversed' : ''; ?>">

          <!-- Image -->
          <div class="gl-rooms-card__media">
            <?php if ($thumb_id): ?>
            <a href="<?php echo esc_url($permalink); ?>" class="gl-rooms-card__img-link">
              <?php echo wp_get_attachment_image( $thumb_id, 'large', false, [
                  'alt'     => esc_attr( get_the_title( $room_id ) ),
                  'loading' => 'lazy',
                  'sizes'   => '(max-width: 767px) 100vw, (max-width: 1023px) 100vw, 50vw',
              ] ); ?>
              <div class="gl-rooms-card__img-overlay">
                <span class="gl-rooms-card__img-overlay-text">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                  </svg>
                  <?php echo $gallery_count > 1 ? $gallery_count . ' фото' : 'Відкрити'; ?>
                </span>
              </div>
            </a>
            <?php
    else: ?>
            <div class="gl-rooms-card__img-placeholder">
              <span>🛏️</span>
            </div>
            <?php
    endif; ?>
            <span class="gl-rooms-card__tag">Номер</span>
          </div>

          <!-- Content -->
          <div class="gl-rooms-card__content">
            <h3 class="gl-rooms-card__title">
              <a href="<?php echo esc_url($permalink); ?>">
                <?php echo esc_html(get_the_title($room_id)); ?>
              </a>
            </h3>

            <?php if ($excerpt): ?>
            <p class="gl-rooms-card__desc">
              <?php echo esc_html($excerpt); ?>
            </p>
            <?php
    endif; ?>

            <!-- Specs pills -->
            <div class="gl-rooms-card__specs">
              <?php if ($adults): ?>
              <span class="gl-rooms-card__spec">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <?php echo $adults; ?>
                <?php echo $children ? '+' . $children : ''; ?> гостей
              </span>
              <?php
    endif; ?>
              <?php if ($size): ?>
              <span class="gl-rooms-card__spec">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="3" width="18" height="18" rx="1" />
                  <path d="M9 3v18M15 3v18M3 9h18M3 15h18" />
                </svg>
                <?php echo esc_html($size); ?> м²
              </span>
              <?php
    endif; ?>
              <?php if ($bed_type): ?>
              <span class="gl-rooms-card__spec">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M2 4v16M2 8h20a2 2 0 0 1 2 2v10M2 12h20M22 12v8" />
                  <path d="M6 12V10a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2" />
                </svg>
                <?php echo esc_html($bed_type); ?>
              </span>
              <?php
    endif; ?>
              <?php if ($view): ?>
              <span class="gl-rooms-card__spec">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
                <?php echo esc_html(ucfirst($view)); ?>
              </span>
              <?php
    endif; ?>
            </div>

            <!-- Amenities -->
            <?php if (!empty($amenities)): ?>
            <div class="gl-rooms-card__amenities">
              <?php foreach (array_slice($amenities, 0, 6) as $amenity): ?>
              <span class="gl-rooms-card__amenity">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
                <?php echo esc_html($amenity); ?>
              </span>
              <?php
      endforeach; ?>
              <?php if (count($amenities) > 6): ?>
              <span class="gl-rooms-card__amenity gl-rooms-card__amenity--more">
                +
                <?php echo count($amenities) - 6; ?> ще
              </span>
              <?php
      endif; ?>
            </div>
            <?php
    endif; ?>

            <!-- Price + CTA -->
            <div class="gl-rooms-card__footer">
              <div class="gl-rooms-card__price">
                <span class="gl-rooms-card__price-label">від</span>
                <span class="gl-rooms-card__price-value">
                  <?php echo $price ? esc_html(number_format($price, 0, '.', ' ')) . ' ₴' : 'за запитом'; ?>
                </span>
                <span class="gl-rooms-card__price-per">/ ніч</span>
              </div>
              <div class="gl-rooms-card__actions">
                <a href="<?php echo esc_url($permalink); ?>"
                  class="gl-btn gl-btn--outline-gold gl-btn--sm">Детальніше</a>
                <?php if ($phone): ?>
                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"
                  class="gl-btn gl-btn--primary gl-btn--sm" title="Зателефонувати">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
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
        </article>
        <?php
  endforeach; ?>
      </div>

      <?php
else: ?>
      <!-- Fallback if no rooms configured -->
      <div class="gl-rooms-listing__empty">
        <p>Номери ще не налаштовані. Зв'яжіться з нами для бронювання!</p>
        <?php if ($phone): ?>
        <a href="tel:<?php echo esc_attr($phone); ?>" class="gl-btn gl-btn--primary">
          <?php echo esc_html($phone_disp ?: $phone); ?>
        </a>
        <?php
  endif; ?>
      </div>
      <?php
endif; ?>
    </div>
  </section>






</main>

<?php
get_footer();