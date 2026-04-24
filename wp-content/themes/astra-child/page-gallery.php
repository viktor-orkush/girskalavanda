<?php
/**
 * page-gallery.php — Сторінка «Галерея»
 * Автоматично застосовується до сторінки зі слагом "gallery"
 */

get_header();

$page_id = get_the_ID();

// === Featured image ===
$hero_id = get_post_thumbnail_id($page_id);
$hero_url = $hero_id ? wp_get_attachment_image_url($hero_id, 'full') : '';
if (!$hero_url) {
  $uploads = wp_upload_dir();
  $hero_url = $uploads['baseurl'] . '/2025/07/L77A2868-Pano.jpg';
}

// === Collect gallery images ===
// Priority: images attached to gallery page → all recent images
$gallery_imgs = get_transient( 'glav_gallery_page_imgs_v2' );

if ( $gallery_imgs === false ) :
$gallery_imgs = [];

// 1. Images attached to this page
$attachments = get_attached_media('image', $page_id);
foreach ((array)$attachments as $att) {
  $lg = wp_get_attachment_image_url($att->ID, 'large');
  $full = wp_get_attachment_url($att->ID);
  $alt = get_post_meta($att->ID, '_wp_attachment_image_alt', true) ?: $att->post_title;
  if ($lg) {
    $gallery_imgs[] = [
      'id' => $att->ID,
      'lg' => $lg,
      'full' => $full,
      'alt' => $alt,
      'title' => $att->post_title,
    ];
  }
}

// 2. Pull images from room types (always)
$rooms = get_posts([
    'post_type' => 'mphb_room_type',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'menu_order',
    'order' => 'ASC',
  ]);
  foreach ($rooms as $room) {
    $thumb_id = get_post_thumbnail_id($room->ID);
    if ($thumb_id && !in_array($thumb_id, array_column($gallery_imgs, 'id'), true)) {
      $lg = wp_get_attachment_image_url($thumb_id, 'large');
      $full = wp_get_attachment_url($thumb_id);
      if ($lg) {
        $gallery_imgs[] = [
          'id' => $thumb_id,
          'lg' => $lg,
          'full' => $full,
          'alt' => get_post_meta($thumb_id, '_wp_attachment_image_alt', true) ?: get_the_title($room->ID),
          'title' => get_the_title($room->ID),
          'category' => 'rooms',
        ];
      }
    }
    // Also pull gallery images from room posts
    $room_gallery = get_post_meta($room->ID, 'mphb_gallery', true);
    if (!empty($room_gallery) && is_string($room_gallery)) {
      $room_gallery_ids = explode(',', $room_gallery);
      foreach ($room_gallery_ids as $gid) {
        $gid = (int)trim($gid);
        if ($gid && !in_array($gid, array_column($gallery_imgs, 'id'), true)) {
          $lg = wp_get_attachment_image_url($gid, 'large');
          $full = wp_get_attachment_url($gid);
          if ($lg) {
            $gallery_imgs[] = [
              'id' => $gid,
              'lg' => $lg,
              'full' => $full,
              'alt' => get_post_meta($gid, '_wp_attachment_image_alt', true) ?: get_the_title($room->ID),
              'title' => get_the_title($room->ID),
              'category' => 'rooms',
            ];
          }
        }
      }
    }
  }

// 3. Pull from banya / chan pages
$service_pages = ['banya' => 'wellness', 'chan' => 'wellness'];
foreach ($service_pages as $slug => $cat) {
  $page_obj = get_page_by_path($slug);
  if (!$page_obj)
    continue;
  $thumb_id = get_post_thumbnail_id($page_obj->ID);
  if ($thumb_id && !in_array($thumb_id, array_column($gallery_imgs, 'id'), true)) {
    $lg = wp_get_attachment_image_url($thumb_id, 'large');
    $full = wp_get_attachment_url($thumb_id);
    if ($lg) {
      $gallery_imgs[] = [
        'id' => $thumb_id,
        'lg' => $lg,
        'full' => $full,
        'alt' => get_post_meta($thumb_id, '_wp_attachment_image_alt', true) ?: ucfirst($slug),
        'title' => get_the_title($page_obj->ID),
        'category' => $cat,
      ];
    }
  }
  // Attached media
  $att_media = get_attached_media('image', $page_obj->ID);
  foreach (array_slice((array)$att_media, 0, 4) as $att) {
    if (in_array($att->ID, array_column($gallery_imgs, 'id'), true))
      continue;
    $lg = wp_get_attachment_image_url($att->ID, 'large');
    $full = wp_get_attachment_url($att->ID);
    if ($lg) {
      $gallery_imgs[] = [
        'id' => $att->ID,
        'lg' => $lg,
        'full' => $full,
        'alt' => get_post_meta($att->ID, '_wp_attachment_image_alt', true) ?: ucfirst($slug),
        'title' => $att->post_title,
        'category' => $cat,
      ];
    }
  }
}

// 4. Fill with recent media library images if still lacking (optional)
if (count($gallery_imgs) < 12) {
  $existing_ids = array_column($gallery_imgs, 'id');
  $recent = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 20,
    'orderby' => 'date',
    'order' => 'DESC',
    'post__not_in' => $existing_ids ?: [0],
    'post_status' => 'inherit',
  ]);
  foreach ($recent as $att) {
    // Skip tiny thumbnails and avatars
    $meta = wp_get_attachment_metadata($att->ID);
    if (!$meta || (isset($meta['width']) && $meta['width'] < 400))
      continue;

    $lg = wp_get_attachment_image_url($att->ID, 'large');
    $full = wp_get_attachment_url($att->ID);
    if ($lg) {
      // Auto-categorize based on filename
      $fname = strtolower(basename($full));
      $auto_cat = 'complex';
      if (preg_match('/room|номер|standart|apart|suite/i', $fname)) {
        $auto_cat = 'rooms';
      }
      elseif (preg_match('/banya|bania|ban|hamam|парна/i', $fname)) {
        $auto_cat = 'wellness';
      }
      elseif (preg_match('/chan|чан/i', $fname)) {
        $auto_cat = 'wellness';
      }
      elseif (preg_match('/pano|mountain|forest|nature|карпат|ліс/i', $fname)) {
        $auto_cat = 'nature';
      }

      $gallery_imgs[] = [
        'id' => $att->ID,
        'lg' => $lg,
        'full' => $full,
        'alt' => get_post_meta($att->ID, '_wp_attachment_image_alt', true) ?: $att->post_title,
        'title' => $att->post_title,
        'category' => isset($gallery_imgs[count($gallery_imgs) - 1]['category'])
        ? $auto_cat : $auto_cat,
      ];
    }
    if (count($gallery_imgs) >= 24)
      break;
  }
}

// Ensure all items have a category
foreach ($gallery_imgs as &$img) {
  if (empty($img['category'])) {
    $img['category'] = 'complex';
  }
}
unset($img);

set_transient( 'glav_gallery_page_imgs_v2', $gallery_imgs, DAY_IN_SECONDS );
endif; // end transient cache block

// === Category definitions ===
$categories = [
  'all' => ['label' => 'Всі фото', 'icon' => '📷'],
  'complex' => ['label' => 'Комплекс', 'icon' => '🏨'],
  'rooms' => ['label' => 'Номери', 'icon' => '🛏️'],
  'wellness' => ['label' => 'Баня та Чан', 'icon' => '🧖'],
  'nature' => ['label' => 'Природа', 'icon' => '🌲'],
];

// Count photos per category
$cat_counts = ['all' => count($gallery_imgs)];
foreach ($gallery_imgs as $img) {
  $c = $img['category'];
  $cat_counts[$c] = ($cat_counts[$c] ?? 0) + 1;
}

// Remove empty categories
foreach ($categories as $key => $cat) {
  if ($key !== 'all' && empty($cat_counts[$key])) {
    unset($categories[$key]);
  }
}

// === Contact info for CTA ===
$phone = get_theme_mod('gl_phone', '');
$phone_disp = get_theme_mod('gl_phone_display', $phone);
?>
<main id="main" class="gl-gallery-page">

  <!-- ======================================================================
       HERO
       ====================================================================== -->
  <section class="gl-gallery-hero" style="--gallery-hero-bg: url('<?php echo esc_url($hero_url); ?>')">
    <div class="gl-gallery-hero__overlay"></div>

    <div class="gl-gallery-hero__content">
      <div class="gl-container">
        <p class="gl-gallery-hero__label">Фотогалерея · Гірська Лаванда</p>
        <h1 class="gl-gallery-hero__title">Наша <em>Галерея</em></h1>
        <p class="gl-gallery-hero__subtitle">Поглянь на комплекс, номери, баню і карпатську природу очима наших гостей
        </p>
      </div>
    </div>

    <!-- Filter tabs strip — прикріплений до низу hero -->
    <div class="gl-hero-stats-bar gl-gallery-hero__filters-bar">
      <div class="gl-container">
        <div class="gl-gallery-filters__tabs gl-gallery-filters__tabs--hero" role="tablist"
          aria-label="Фільтр фотографій">
          <?php foreach ($categories as $key => $cat): ?>
          <button class="gl-gallery-tab gl-gallery-tab--hero <?php echo $key === 'all' ? 'is-active' : ''; ?>"
            role="tab" aria-selected="<?php echo $key === 'all' ? 'true' : 'false'; ?>"
            data-filter="<?php echo esc_attr($key); ?>">
            <span class="gl-gallery-tab__icon">
              <?php echo $cat['icon']; ?>
            </span>
            <span class="gl-gallery-tab__label">
              <?php echo esc_html($cat['label']); ?>
            </span>
            <span class="gl-gallery-tab__count">
              <?php echo $cat_counts[$key] ?? 0; ?>
            </span>
          </button>
          <?php
endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Scroll indicator -->
    <a href="#gallery-content" class="gl-gallery-hero__scroll" aria-label="Прокрутити донизу">
      <span class="gl-gallery-hero__scroll-line"></span>
    </a>
  </section>


  <!-- anchor for scroll -->
  <div id="gallery-content"></div>


  <!-- ======================================================================
       GALLERY GRID
       ====================================================================== -->
  <section class="gl-gallery-main gl-section gl-section--sand">
    <div class="gl-container">

      <div class="gl-gallery-masonry" id="gallery-masonry">
        <?php if (!empty($gallery_imgs)): ?>
        <?php foreach ($gallery_imgs as $i => $img):
    // Determine grid span for visual variety
    $span_class = '';
    if ($i === 0) {
      $span_class = 'gl-gallery-masonry__item--wide';
    }
    elseif ($i % 7 === 3) {
      $span_class = 'gl-gallery-masonry__item--tall';
    }
    elseif ($i % 11 === 6) {
      $span_class = 'gl-gallery-masonry__item--wide';
    }
?>
        <div class="gl-gallery-masonry__item <?php echo $span_class; ?>"
          data-category="<?php echo esc_attr($img['category']); ?>" data-src="<?php echo esc_url($img['full']); ?>"
          data-index="<?php echo $i; ?>">
          <img src="<?php echo esc_url($img['lg']); ?>" alt="<?php echo esc_attr($img['alt']); ?>"
            loading="<?php echo $i < 6 ? 'eager' : 'lazy'; ?>" />
          <div class="gl-gallery-masonry__overlay">
            <div class="gl-gallery-masonry__overlay-content">
              <svg class="gl-gallery-masonry__zoom-icon" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                aria-hidden="true">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                <line x1="11" y1="8" x2="11" y2="14" />
                <line x1="8" y1="11" x2="14" y2="11" />
              </svg>
              <?php if (!empty($img['title'])): ?>
              <span class="gl-gallery-masonry__caption">
                <?php echo esc_html($img['title']); ?>
              </span>
              <?php
    endif; ?>
            </div>
          </div>
        </div>
        <?php
  endforeach; ?>
        <?php
else: ?>
        <!-- Fallback placeholders -->
        <?php
  $placeholders = [
    ['icon' => '🏨', 'title' => 'Комплекс', 'gradient' => 'linear-gradient(135deg,#1C3A0E,#4a8c3f)', 'cat' => 'complex'],
    ['icon' => '🛏️', 'title' => 'Номери', 'gradient' => 'linear-gradient(135deg,#5C1F00,#9B3A0E)', 'cat' => 'rooms'],
    ['icon' => '🌲', 'title' => 'Природа', 'gradient' => 'linear-gradient(135deg,#2D5A1A,#4a8c3f)', 'cat' => 'nature'],
    ['icon' => '🧖', 'title' => 'Баня', 'gradient' => 'linear-gradient(135deg,#0E2147,#1A3D7C)', 'cat' => 'wellness'],
    ['icon' => '🏔️', 'title' => 'Карпати', 'gradient' => 'linear-gradient(135deg,#1C3A0E,#2D5A1A)', 'cat' => 'nature'],
    ['icon' => '🌸', 'title' => 'Лаванда', 'gradient' => 'linear-gradient(135deg,#C8A951,#9B3A0E)', 'cat' => 'complex'],
    ['icon' => '🔥', 'title' => 'Чан', 'gradient' => 'linear-gradient(135deg,#7B2500,#C0440E)', 'cat' => 'wellness'],
    ['icon' => '🏡', 'title' => 'Тераса', 'gradient' => 'linear-gradient(135deg,#1C3A0E,#C8A951)', 'cat' => 'rooms'],
  ];
  foreach ($placeholders as $i => $ph):
    $span_class = $i === 0 ? 'gl-gallery-masonry__item--wide' : ($i === 3 ? 'gl-gallery-masonry__item--tall' : '');
?>
        <div class="gl-gallery-masonry__item <?php echo $span_class; ?>"
          data-category="<?php echo esc_attr($ph['cat']); ?>">
          <div class="gl-gallery-masonry__placeholder" style="background:<?php echo $ph['gradient']; ?>">
            <span class="gl-gallery-masonry__placeholder-icon">
              <?php echo $ph['icon']; ?>
            </span>
            <span class="gl-gallery-masonry__placeholder-title">
              <?php echo esc_html($ph['title']); ?>
            </span>
          </div>
        </div>
        <?php
  endforeach; ?>
        <?php
endif; ?>
      </div>

      <!-- Empty state for filtered results -->
      <div class="gl-gallery-empty" id="gallery-empty" hidden>
        <div class="gl-gallery-empty__icon">📷</div>
        <p class="gl-gallery-empty__text">Фото в цій категорії скоро з'являться</p>
      </div>

    </div>
  </section>


  <!-- ======================================================================
       CTA SECTION
       ====================================================================== -->
  <?php get_template_part('template-parts/section-ready', null, [
  'title' => 'Хочете побачити це наживо?',
  'subtitle' => 'Забронюйте номер у Гірській Лаванді та насолоджуйтесь краєвидами Карпат, затишком та оздоровленням',
  'extra_btn' => [
    'url' => '/rooms/',
    'label' => 'Переглянути номери',
    'class' => 'gl-btn--gold'
  ],
  'hide_contact' => true,
]); ?>


</main>

<!-- Lightbox -->
<div class="gl-lightbox" role="dialog" aria-modal="true" aria-label="Перегляд фото">
  <button class="gl-lightbox__close" aria-label="Закрити">✕</button>
  <button class="gl-lightbox__prev" aria-label="Попереднє фото">&#8592;</button>
  <img class="gl-lightbox__img" src="" alt="Фото галереї" />
  <button class="gl-lightbox__next" aria-label="Наступне фото">&#8594;</button>
  <div class="gl-lightbox__counter"></div>
</div>

<?php get_footer(); ?>