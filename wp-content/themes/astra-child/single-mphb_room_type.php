<?php
/**
 * Single Room Type — Гірська Лаванда
 * Template v3.0 — fixed content filtering, reordered sections, calendar at bottom
 *
 * Fixes:
 *  - Removes duplicate gallery (WP editor gallery blocks filtered out; hero strip uses MPHB meta gallery)
 *  - Removes [mphb_room_type_attributes] from post content (we render it separately, styled)
 *  - New order: Amenities → Description → Attributes → (aside) Booking
 *  - Availability calendar moved to the very bottom, styled in site theme
 */

get_header();

while ( have_posts() ) :
    the_post();

    $room_id  = get_the_ID();
    $adults   = (int) get_post_meta( $room_id, 'mphb_adults_capacity',   true );
    $children = (int) get_post_meta( $room_id, 'mphb_children_capacity', true );
    $size     = get_post_meta( $room_id, 'mphb_size', true );

    // === Images ===
    $thumb_id  = get_post_thumbnail_id( $room_id );
    $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'full' ) : '';

    // Gallery from MPHB meta (comma-separated IDs)
    $gallery_str = get_post_meta( $room_id, 'mphb_gallery', true );
    $gallery_ids = $gallery_str
        ? array_filter( array_map( 'intval', explode( ',', $gallery_str ) ) )
        : [];

    $all_imgs = [];
    if ( $thumb_id ) {
        $all_imgs[] = [
            'id'  => $thumb_id,
            'url' => $thumb_url,
            'sm'  => wp_get_attachment_image_url( $thumb_id, 'medium' ) ?: $thumb_url,
        ];
    }
    foreach ( $gallery_ids as $gid ) {
        $url = wp_get_attachment_image_url( $gid, 'full' );
        if ( $url ) {
            $all_imgs[] = [
                'id'  => $gid,
                'url' => $url,
                'sm'  => wp_get_attachment_image_url( $gid, 'medium' ) ?: $url,
            ];
        }
    }

    $hero_url  = ! empty( $all_imgs ) ? $all_imgs[0]['url'] : '';
    $strip_max = 5;

    // === Room meta ===
    $price     = function_exists( 'glav_get_room_price' )     ? glav_get_room_price( $room_id )     : 0;
    $bed_type  = function_exists( 'glav_get_room_bed_type' )  ? glav_get_room_bed_type( $room_id )  : '';
    $amenities = function_exists( 'glav_get_room_amenities' ) ? glav_get_room_amenities( $room_id ) : [];

    $phone      = get_theme_mod( 'gl_phone', '' );
    $phone_disp = get_theme_mod( 'gl_phone_display', $phone );

    // === Booking URL (MPHB checkout page or fallback) ===
    $booking_url = '';
    if ( function_exists( 'MPHB' ) ) {
        $checkout_id = MPHB()->settings()->pages()->getCheckoutPageId();
        if ( $checkout_id ) {
            $booking_url = add_query_arg( 'mphb_room_type_id', $room_id, get_permalink( $checkout_id ) );
        }
    }
    if ( ! $booking_url ) {
        $booking_url = home_url( '/?mphb_room_type_id=' . $room_id );
    }

    // === Social contact URLs ===
    $telegram_raw = get_theme_mod( 'gl_telegram', '' );
    $telegram_url = '';
    if ( $telegram_raw ) {
        if ( str_starts_with( $telegram_raw, 'http' ) ) {
            $telegram_url = $telegram_raw;
        } else {
            $telegram_url = 'https://t.me/' . ltrim( $telegram_raw, '@/' );
        }
    }

    $whatsapp_url = '';
    $viber_url    = '';
    if ( $phone ) {
        $wa_phone     = preg_replace( '/[^0-9]/', '', $phone );
        $wa_msg       = rawurlencode( 'Добрий день! Хочу забронювати «' . get_the_title() . '».' );
        $whatsapp_url = 'https://wa.me/' . $wa_phone . '?text=' . $wa_msg;
        $viber_url    = 'viber://contact?number=' . $wa_phone;
    }

    $instagram_dm  = '';
    $instagram_raw = get_theme_mod( 'gl_instagram', '' );
    if ( $instagram_raw ) {
        preg_match( '/instagram\.com\/([^\/\?#]+)/i', $instagram_raw, $ig_m );
        $ig_user      = isset( $ig_m[1] ) ? trim( $ig_m[1], '/' ) : '';
        $instagram_dm = $ig_user ? 'https://ig.me/m/' . $ig_user : $instagram_raw;
    }
    if ( ! $instagram_dm ) {
        $instagram_dm = 'https://www.instagram.com/girska_lavandaa';
    }

    // === Filter post content ===
    // Remove WP gallery / image blocks (they duplicate the hero gallery)
    // Remove MPHB shortcodes (rendered separately below)
    $raw_content = get_the_content( null, false );
    $raw_content = preg_replace( '/<!--\s*wp:gallery[^>]*>[\s\S]*?<!--\s*\/wp:gallery\s*-->/i',     '', $raw_content );
    $raw_content = preg_replace( '/<!--\s*wp:image[^>]*>[\s\S]*?<!--\s*\/wp:image\s*-->/i',         '', $raw_content );
    $raw_content = preg_replace( '/<!--\s*wp:file[^>]*>[\s\S]*?<!--\s*\/wp:file\s*-->/i',           '', $raw_content );
    $raw_content = preg_replace( '/<!--\s*wp:media-text[^>]*>[\s\S]*?<!--\s*\/wp:media-text\s*-->/i', '', $raw_content );
    $raw_content = preg_replace( '/\[mphb_[^\]]*\]/i', '', $raw_content ); // remove all MPHB shortcodes
    $raw_content = trim( $raw_content );
    // Use wpautop+do_shortcode instead of apply_filters('the_content') to avoid
    // MPHB's the_content hook injecting a second calendar/booking form.
    $desc_html   = $raw_content ? wpautop( do_shortcode( $raw_content ) ) : '';
    // Strip any remaining <figure>/<img> that slipped through block filtering
    $desc_html   = preg_replace( '/<figure\b[^>]*>[\s\S]*?<\/figure>/i', '', $desc_html );
    $desc_html   = preg_replace( '/<img\b[^>]*\/?>/i', '', $desc_html );

    // === Room view from meta ===
    $view = get_post_meta( $room_id, 'mphb_view', true );

    // === MPHB facilities from taxonomy ===
    $facility_terms  = get_the_terms( $room_id, 'mphb_room_type_facility' );
    $mphb_facilities = ( $facility_terms && ! is_wp_error( $facility_terms ) )
        ? wp_list_pluck( $facility_terms, 'name' )
        : [];

    // === Specs grid data ===
    $specs = [];
    if ( $size ) {
        $specs[] = [
            'value' => $size . ' м²',
            'label' => 'Площа',
            'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="1"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg>',
        ];
    }
    $cap_str = $adults ? $adults . ( $children ? '+' . $children : '' ) : '';
    if ( $cap_str ) {
        $specs[] = [
            'value' => $cap_str . ' ' . ( $adults <= 1 ? 'гість' : ( $adults <= 4 ? 'гості' : 'гостей' ) ),
            'label' => 'Місткість',
            'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        ];
    }
    if ( $bed_type ) {
        $specs[] = [
            'value' => $bed_type,
            'label' => 'Тип ліжка',
            'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 4v16M2 8h20a2 2 0 0 1 2 2v10M2 12h20M22 12v8"/><path d="M6 12V10a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2"/></svg>',
        ];
    }
    if ( $view ) {
        $specs[] = [
            'value' => ucfirst( $view ),
            'label' => 'Вид',
            'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M12 2v4M4 15c2-1 4-2 8-2s6 1 8 2"/></svg>',
        ];
    }
?>
<main id="main" class="gl-room-single">

  <!-- ==========================================================================
       HERO — bento gallery with overlaid info
       ========================================================================== -->
  <?php
    // Prepare gallery: main + up to 4 side images
    $main_img  = ! empty( $all_imgs[0] ) ? $all_imgs[0] : null;
    $side_imgs = array_slice( $all_imgs, 1, 4 );
    $extra_count = max( 0, count( $all_imgs ) - 5 );
    $all_srcs_json = wp_json_encode( array_map( function( $i ) { return $i['url']; }, $all_imgs ) );
  ?>
  <section class="gl-room-hero-v2 gl-room-hero-v2--fullwidth">

    <!-- Full-width hero image -->
    <div class="gl-room-bento" data-gallery='<?php echo esc_attr( $all_srcs_json ); ?>'>

      <!-- Main (large) image — full width & height -->
      <?php if ( $main_img ) : ?>
      <div class="gl-room-bento__main gl-room-bento__cell" data-index="0">
        <img src="<?php echo esc_url( $main_img['url'] ); ?>"
             alt="<?php echo esc_attr( get_the_title() ); ?>"
             loading="eager" />
        <div class="gl-room-bento__main-overlay"></div>

          <!-- Info overlaid on main image -->
          <div class="gl-room-bento__info">
            <p class="gl-room-bento__label">Гірська Лаванда · Номер</p>
            <h1 class="gl-room-bento__title"><?php the_title(); ?></h1>

            <div class="gl-room-bento__meta">
              <?php if ( $adults ) : ?>
              <span class="gl-room-bento__pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <?php echo $adults; ?><?php echo $children ? '+' . $children : ''; ?> гостей
              </span>
              <?php endif; ?>
              <?php if ( $size ) : ?>
              <span class="gl-room-bento__pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg>
                <?php echo esc_html( $size ); ?> м²
              </span>
              <?php endif; ?>
              <?php if ( $bed_type ) : ?>
              <span class="gl-room-bento__pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16M2 8h20a2 2 0 0 1 2 2v10M2 12h20M22 12v8"/><path d="M6 12V10a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2"/></svg>
                <?php echo esc_html( $bed_type ); ?>
              </span>
              <?php endif; ?>
              <?php if ( $view ) : ?>
              <span class="gl-room-bento__pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 6s4-5 11-5 11 5 11 5-4 5-11 5S1 6 1 6z"/><circle cx="12" cy="6" r="3"/></svg>
                <?php echo esc_html( $view ); ?>
              </span>
              <?php endif; ?>
            </div>

            <?php if ( $price > 0 ) : ?>
            <div class="gl-room-bento__price">
              <span class="gl-room-bento__price-from">від</span>
              <span class="gl-room-bento__price-value"><?php echo number_format( $price, 0, '', ' ' ); ?> ₴</span>
              <span class="gl-room-bento__price-per">/ ніч</span>
            </div>
            <?php endif; ?>
          </div>
        <!-- Gallery thumbnails overlaid on main image (bottom-right) -->
        <?php if ( ! empty( $side_imgs ) ) : ?>
        <div class="gl-room-bento__gallery-overlay">
          <?php foreach ( $side_imgs as $si => $simg ) : ?>
          <div class="gl-room-bento__side gl-room-bento__cell" data-index="<?php echo $si + 1; ?>">
            <img src="<?php echo esc_url( $simg['sm'] ); ?>"
                 alt="<?php echo esc_attr( get_the_title() ); ?> — фото <?php echo $si + 2; ?>"
                 loading="eager" />
            <div class="gl-room-bento__zoom">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><path d="M11 8v6M8 11h6"/>
              </svg>
            </div>
            <?php if ( $si === count( $side_imgs ) - 1 && $extra_count > 0 ) : ?>
            <div class="gl-room-bento__more">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
              +<?php echo $extra_count; ?> фото
            </div>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>

          <!-- "All photos" button -->
          <?php if ( count( $all_imgs ) > 1 ) : ?>
          <button class="gl-room-bento__show-all" type="button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Всі фото (<?php echo count( $all_imgs ); ?>)
          </button>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        </div>
        <?php endif; ?>

    </div>
  </section>


  <!-- ==========================================================================
       BODY: two-column grid (content + booking sidebar)
       Порядок: 1. Що включено  2. Про номер  3. Характеристики | aside Booking
       ========================================================================== -->
  <section class="gl-room-body">
    <div class="gl-container gl-room-body__grid">

      <!-- ───── LEFT: details ───── -->
      <div class="gl-room-body__main">

        <!-- 1. DESCRIPTION — "Про номер" -->
        <?php if ( $desc_html ) : ?>
        <div class="gl-room-body__section gl-animate">
          <h2 class="gl-room-body__section-title">Про номер</h2>
          <div class="gl-room-body__desc">
            <?php echo $desc_html; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- 2. SPECS GRID — "Характеристики" (PHP meta, not shortcode) -->
        <?php if ( ! empty( $specs ) ) : ?>
        <div class="gl-room-body__section gl-animate gl-animate--delay-1">
          <h2 class="gl-room-body__section-title">Характеристики</h2>
          <div class="gl-room-specs">
            <?php foreach ( $specs as $spec ) : ?>
            <div class="gl-room-spec">
              <div class="gl-room-spec__icon" aria-hidden="true">
                <?php echo $spec['icon']; ?>
              </div>
              <span class="gl-room-spec__value"><?php echo esc_html( $spec['value'] ); ?></span>
              <span class="gl-room-spec__label"><?php echo esc_html( $spec['label'] ); ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- 3. AMENITIES — "Що включено" (MPHB facilities + smart defaults) -->
        <?php if ( ! empty( $amenities ) ) : ?>
        <div class="gl-room-body__section gl-animate gl-animate--delay-2">
          <h2 class="gl-room-body__section-title">Що включено</h2>
          <ul class="gl-room-amenities gl-room-amenities--pills">
            <?php foreach ( $amenities as $amenity ) : ?>
            <li class="gl-room-amenities__item">
              <span class="gl-room-amenities__check" aria-hidden="true">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                  <path d="M1.5 5L4 7.5L8.5 2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <?php echo esc_html( $amenity ); ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

      </div><!-- /main -->

      <!-- ───── RIGHT: booking sidebar (sticky) ───── -->
      <aside class="gl-room-booking gl-animate gl-animate--delay-1">
        <div class="gl-room-booking__inner">

          <?php
          $contact_data = glav_get_contact_data( 'Добрий день! Хочу забронювати «' . get_the_title() . '».' );
          get_template_part( 'template-parts/booking-contact', null, $contact_data );
          ?>

          <div class="gl-room-booking__details">
            <div class="gl-room-booking__detail-item">
              <span class="gl-room-booking__detail-icon">🕐</span>
              <span>Check-in від <strong>14:00</strong></span>
            </div>
            <div class="gl-room-booking__detail-item">
              <span class="gl-room-booking__detail-icon">🕛</span>
              <span>Check-out до <strong>12:00</strong></span>
            </div>
            <div class="gl-room-booking__detail-item">
              <span class="gl-room-booking__detail-icon">🅿️</span>
              <span>Безкоштовний паркінг</span>
            </div>
            <div class="gl-room-booking__detail-item">
              <span class="gl-room-booking__detail-icon">✅</span>
              <span>Безкоштовне скасування за 3 дні</span>
            </div>
          </div>

        </div><!-- /.gl-room-booking__inner -->
      </aside>

    </div><!-- /.gl-room-body__grid -->
  </section><!-- /body -->


  <!-- ==========================================================================
       CALENDAR — availability, at the very bottom
       ========================================================================== -->
  <?php if ( function_exists( 'MPHB' ) ) : ?>
  <section class="gl-room-calendar">
    <div class="gl-container">

      <div class="gl-room-calendar__header gl-animate">
        <span class="gl-section-label">Доступність</span>
        <h2 class="gl-room-calendar__title">Вільні дати</h2>
        <p class="gl-room-calendar__subtitle">
          Перевірте наявність вільних місць та оберіть зручні дати для заїзду
        </p>
      </div>

      <div class="gl-room-calendar__widget gl-animate gl-animate--delay-1">
        <?php echo do_shortcode( '[mphb_availability_calendar room_type_id="' . esc_attr( $room_id ) . '"]' ); ?>
      </div>

      <div class="gl-room-calendar__legend gl-animate gl-animate--delay-2">
        <div class="gl-room-calendar__legend-item">
          <span class="gl-room-calendar__legend-dot gl-room-calendar__legend-dot--available"></span>
          Вільно
        </div>
        <div class="gl-room-calendar__legend-item">
          <span class="gl-room-calendar__legend-dot gl-room-calendar__legend-dot--unavailable"></span>
          Зайнято
        </div>
        <div class="gl-room-calendar__legend-item">
          <span class="gl-room-calendar__legend-dot gl-room-calendar__legend-dot--today"></span>
          Сьогодні
        </div>
        <div class="gl-room-calendar__legend-item">
          <span class="gl-room-calendar__legend-dot gl-room-calendar__legend-dot--checkin"></span>
          Заїзд / Виїзд
        </div>
      </div>

      <div class="gl-room-calendar__cta gl-animate gl-animate--delay-3">
        <p class="gl-room-calendar__cta-label">Написати для бронювання:</p>
        <div class="gl-room-calendar__social gl-room-calendar__social--circles">
          <?php if ( $telegram_url ) : ?>
          <a href="<?php echo esc_url( $telegram_url ); ?>"
             class="gl-room-calendar__circle gl-room-calendar__circle--telegram"
             target="_blank" rel="noopener noreferrer" aria-label="Telegram" title="Telegram">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L8.375 14.7l-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.462c.537-.194 1.006.131.773.86z"/>
            </svg>
          </a>
          <?php endif; ?>
          <?php if ( $whatsapp_url ) : ?>
          <a href="<?php echo esc_url( $whatsapp_url ); ?>"
             class="gl-room-calendar__circle gl-room-calendar__circle--whatsapp"
             target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" title="WhatsApp">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
          </a>
          <?php endif; ?>
          <?php if ( $viber_url ) : ?>
          <a href="<?php echo esc_url( $viber_url ); ?>"
             class="gl-room-calendar__circle gl-room-calendar__circle--viber"
             aria-label="Viber" title="Viber">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M11.4 0C8.64 0 3.12.96 1.08 6.72.24 9 0 11.16 0 12.72c0 1.32.12 3.72 1.2 5.76.12.24.24.48.36.72l-.48 3.24c-.12.72.48 1.32 1.2 1.08l3.12-1.08c1.44.84 3.12 1.44 5.4 1.56h.48c2.76 0 8.28-.96 10.32-6.72C22.44 15.12 24 12.12 24 12c0-2.88-1.2-5.88-3.6-8.04C18 1.56 14.88 0 11.4 0zm.12 2.16c3.12 0 5.76 1.08 7.68 3 1.92 1.8 2.88 4.2 2.88 6.72 0 .12-1.44 2.88-2.04 4.68-1.68 4.92-6.36 5.64-8.52 5.64h-.48c-2.04-.12-3.6-.72-4.92-1.56l-.36-.24-2.16.72.36-2.28-.36-.48c-.12-.24-.24-.36-.36-.6C2.64 16.44 2.4 14.28 2.4 12.72c0-1.32.24-3.24.96-5.16C5.04 3.12 9.6 2.16 11.52 2.16zm-1.44 3.6c-.36-.12-.6-.12-.84 0-.24.12-.36.24-.6.36-.24.12-.84.84-.84 2.16 0 1.32.96 2.52 1.08 2.76.12.24 1.92 2.88 4.56 3.96 2.64 1.08 2.64.72 3.12.72.48 0 1.44-.6 1.68-1.2.24-.6.24-1.08.12-1.2-.12-.12-.36-.24-.84-.48-.48-.24-2.64-1.2-2.88-1.32-.24-.12-.36-.12-.48.12-.12.24-.6.84-.84 1.08-.12.12-.24.12-.48 0-.24-.12-1.08-.36-2.04-1.2-.72-.6-1.2-1.44-1.32-1.68-.12-.24 0-.36.12-.48.12-.12.24-.24.36-.36.12-.12.12-.24.24-.36.12-.12.12-.24 0-.36-.12-.12-.48-1.2-.72-1.68-.12-.24-.24-.24-.36-.24zm5.64 1.44c-.48 0-.6.36-.6.36s-.36 2.04 1.2 3.48c1.56 1.44 3.24 1.2 3.24 1.2s.48-.12.36-.6l-.36-.84c-.12-.36-.48-.24-.48-.24s-.96.12-1.92-.72c-.96-.84-.84-1.8-.84-1.8s.12-.48-.24-.6l-.36-.24z"/>
            </svg>
          </a>
          <?php endif; ?>
          <?php if ( $instagram_dm ) : ?>
          <a href="<?php echo esc_url( $instagram_dm ); ?>"
             class="gl-room-calendar__circle gl-room-calendar__circle--instagram"
             target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
            </svg>
          </a>
          <?php endif; ?>
        </div>

        <?php if ( $phone ) : ?>
        <p class="gl-room-calendar__phone-label">Або зателефонуйте:</p>
        <a href="tel:<?php echo esc_attr( $phone ); ?>"
           class="gl-room-calendar__call-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1.003 1.003 0 011.01-.24c1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.1.31.03.66-.25.94l-2.2 2.28z"/>
          </svg>
          <?php echo esc_html( $phone_disp ); ?>
        </a>
        <?php endif; ?>
      </div>

    </div>
  </section>
  <?php endif; ?>


</main>

<!-- Lightbox -->
<div class="gl-lightbox" role="dialog" aria-modal="true" aria-label="Перегляд фото">
  <button class="gl-lightbox__close" aria-label="Закрити">✕</button>
  <button class="gl-lightbox__prev" aria-label="Попереднє фото">&#8592;</button>
  <img class="gl-lightbox__img" src="" alt="Фото номеру" />
  <button class="gl-lightbox__next" aria-label="Наступне фото">&#8594;</button>
  <span class="gl-lightbox__counter"></span>
</div>

<?php
endwhile;

get_footer();
