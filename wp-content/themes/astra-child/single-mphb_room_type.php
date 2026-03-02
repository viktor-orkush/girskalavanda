<?php
/**
 * Single Room Type — Гірська Лаванда
 * Template for displaying individual MPHB room type pages
 */

get_header();

while ( have_posts() ) :
    the_post();

    $room_id  = get_the_ID();
    $adults   = (int) get_post_meta( $room_id, 'mphb_adults_capacity',   true );
    $children = (int) get_post_meta( $room_id, 'mphb_children_capacity', true );
    $size     = get_post_meta( $room_id, 'mphb_size', true );

    // Featured image
    $thumb_id  = get_post_thumbnail_id( $room_id );
    $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';

    // Gallery (MPHB stores as comma-separated attachment IDs)
    $gallery_str = get_post_meta( $room_id, 'mphb_gallery', true );
    $gallery_ids = $gallery_str
        ? array_filter( array_map( 'intval', explode( ',', $gallery_str ) ) )
        : [];

    // Build image list for lightbox
    $all_imgs = [];
    if ( $thumb_url ) $all_imgs[] = $thumb_url;
    foreach ( $gallery_ids as $gid ) {
        $url = wp_get_attachment_image_url( $gid, 'large' );
        if ( $url ) $all_imgs[] = $url;
    }

    $thumb1_url = isset( $gallery_ids[0] ) ? wp_get_attachment_image_url( $gallery_ids[0], 'medium_large' ) : '';
    $thumb2_url = isset( $gallery_ids[1] ) ? wp_get_attachment_image_url( $gallery_ids[1], 'medium_large' ) : '';
    $more_count = max( 0, count( $all_imgs ) - 3 );

    // Price, bed type, amenities via helpers in functions.php
    $price     = function_exists( 'glav_get_room_price' )     ? glav_get_room_price( $room_id )     : 0;
    $bed_type  = function_exists( 'glav_get_room_bed_type' )  ? glav_get_room_bed_type( $room_id )  : '';
    $amenities = function_exists( 'glav_get_room_amenities' ) ? glav_get_room_amenities( $room_id ) : [];

    // Archive / back link
    $back_url = get_post_type_archive_link( 'mphb_room_type' ) ?: home_url( '/#rooms' );
?>
<main id="main" class="gl-room-page">
  <div class="gl-container" style="padding-top:40px; padding-bottom:80px;">

    <!-- Back -->
    <a href="<?php echo esc_url( $back_url ); ?>" class="gl-btn gl-btn--outline gl-btn--sm" style="margin-bottom:32px;display:inline-flex;align-items:center;gap:8px;">
      ← Всі номери
    </a>

    <!-- ===================================================================
         GALLERY
         =================================================================== -->
    <div class="gl-room-page__gallery gl-animate">

      <div class="gl-room-page__gallery-main"
           <?php if ( $thumb_url ) : ?>data-src="<?php echo esc_url( $thumb_url ); ?>"<?php endif; ?>>
        <?php if ( $thumb_url ) : ?>
          <img src="<?php echo esc_url( $thumb_url ); ?>"
               alt="<?php the_title_attribute(); ?>"
               style="width:100%;height:100%;object-fit:cover;" />
        <?php else : ?>
          <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:80px;background:linear-gradient(135deg,#2D5A1A,#4a8c3f);">🏡</div>
        <?php endif; ?>
      </div>

      <?php if ( $thumb1_url ) : ?>
      <div class="gl-room-page__gallery-thumb" data-src="<?php echo esc_url( $thumb1_url ); ?>">
        <img src="<?php echo esc_url( $thumb1_url ); ?>" alt="" loading="lazy"
             style="width:100%;height:100%;object-fit:cover;" />
      </div>
      <?php endif; ?>

      <?php if ( $thumb2_url ) : ?>
      <div class="gl-room-page__gallery-thumb"
           <?php echo $more_count > 0 ? 'data-more="+' . $more_count . ' фото"' : ''; ?>
           data-src="<?php echo esc_url( $thumb2_url ); ?>">
        <img src="<?php echo esc_url( $thumb2_url ); ?>" alt="" loading="lazy"
             style="width:100%;height:100%;object-fit:cover;" />
      </div>
      <?php endif; ?>

    </div><!-- /.gl-room-page__gallery -->

    <!-- ===================================================================
         CONTENT + BOOKING SIDEBAR
         =================================================================== -->
    <div class="gl-room-page__content">

      <!-- Left column -->
      <div>

        <h1 class="gl-room-page__title gl-animate"><?php the_title(); ?></h1>

        <!-- Meta -->
        <div class="gl-room-page__meta gl-animate gl-animate--delay-1">

          <?php if ( $adults ) : ?>
          <span class="gl-room-page__meta-item">
            <span class="gl-room-page__meta-icon">👥</span>
            <?php
            if ( $children ) {
                printf( '%d дорослих + %d дітей', $adults, $children );
            } else {
                printf( '%d гостей', $adults );
            }
            ?>
          </span>
          <?php endif; ?>

          <?php if ( $size ) : ?>
          <span class="gl-room-page__meta-item">
            <span class="gl-room-page__meta-icon">📐</span>
            <?php echo esc_html( $size ); ?> м²
          </span>
          <?php endif; ?>

          <?php if ( $bed_type ) : ?>
          <span class="gl-room-page__meta-item">
            <span class="gl-room-page__meta-icon">🛏️</span>
            <?php echo esc_html( $bed_type ); ?>
          </span>
          <?php endif; ?>

          <span class="gl-room-page__meta-item">
            <span class="gl-room-page__meta-icon">🌲</span>
            Вид на карпатський ліс
          </span>

        </div><!-- /.gl-room-page__meta -->

        <!-- Description -->
        <?php if ( get_the_content() ) : ?>
        <div class="gl-room-page__desc gl-animate gl-animate--delay-2">
          <?php the_content(); ?>
        </div>
        <?php endif; ?>

        <!-- Amenities -->
        <?php if ( ! empty( $amenities ) ) : ?>
        <div class="gl-room-included gl-animate gl-animate--delay-3">
          <h3 class="gl-room-included__title">Що включено</h3>
          <ul class="gl-room-included__list">
            <?php foreach ( $amenities as $amenity ) : ?>
            <li><?php echo esc_html( $amenity ); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <!-- MPHB Attributes (якщо є) -->
        <?php
        $mphb_attributes = get_post_meta( $room_id, 'mphb_room_attributes', true );
        if ( ! empty( $mphb_attributes ) && function_exists( 'MPHB' ) ) :
        ?>
        <div class="gl-animate gl-animate--delay-4" style="margin-top:32px;">
          <?php echo do_shortcode( '[mphb_room_type_attributes]' ); ?>
        </div>
        <?php endif; ?>

      </div><!-- /left column -->

      <!-- ================================================================
           BOOKING SIDEBAR
           ================================================================ -->
      <aside class="gl-room-booking-box gl-animate gl-animate--delay-2">

        <div class="gl-room-booking-box__title">Бронювання</div>

        <?php if ( $price ) : ?>
        <div class="gl-room-booking-box__price">
          <span style="font-size:14px;color:var(--color-text-muted)">від</span>
          <span class="gl-room-booking-box__price-value">
            <?php echo esc_html( number_format( (float) $price, 0, '.', ' ' ) ); ?> ₴
          </span>
          <span class="gl-room-booking-box__price-unit">/ ніч</span>
        </div>
        <?php endif; ?>

        <?php if ( function_exists( 'MPHB' ) ) : ?>
          <?php
          // MPHB book button для цього типу номеру
          echo do_shortcode(
              '[mphb_book_button mphb_room_type_id="' . esc_attr( $room_id ) . '" button_text="Забронювати"]'
          );
          ?>
        <?php else : ?>
          <a href="<?php echo esc_url( home_url( '/#booking' ) ); ?>"
             class="gl-btn gl-btn--primary"
             style="width:100%;text-align:center;display:block;">
            📅 Забронювати
          </a>
        <?php endif; ?>

        <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--color-sand-dark);
                    font-size:13px;color:var(--color-text-muted);
                    display:flex;flex-direction:column;gap:8px;line-height:1.5;">
          <span>✓ Check-in від 14:00</span>
          <span>✓ Check-out до 12:00</span>
          <span>✓ Домашні сніданки</span>
          <span>✓ Безкоштовний паркінг</span>
          <span>✓ Безкоштовне скасування за 3 дні</span>
        </div>

        <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--color-sand-dark);">
          <?php
          $phone      = get_theme_mod( 'gl_phone', '' );
          $phone_disp = get_theme_mod( 'gl_phone_display', $phone );
          if ( $phone ) :
          ?>
          <p style="font-size:13px;color:var(--color-text-muted);margin:0 0 8px;">
            Або зв'яжіться з нами:
          </p>
          <a href="tel:<?php echo esc_attr( $phone ); ?>"
             style="font-size:16px;font-weight:700;color:var(--color-primary);text-decoration:none;display:block;">
            <?php echo esc_html( $phone_disp ?: $phone ); ?>
          </a>
          <?php endif; ?>
        </div>

      </aside><!-- /.gl-room-booking-box -->

    </div><!-- /.gl-room-page__content -->

  </div><!-- /.gl-container -->
</main>

<?php
endwhile;

get_footer();
