<?php
/**
 * Template Name: Головна сторінка
 * Description: Повна головна сторінка комплексу Гірська Лаванда
 */

get_header();
?>
<main id="main" class="gl-home-page">

  <!-- =====================================================================
       HERO
       ===================================================================== -->
  <?php
  $hero_image = get_theme_mod( 'gl_hero_image', '' );
  if ( ! $hero_image ) {
      $hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
  }
  $has_photo  = ! empty( $hero_image );
  ?>
  <section class="gl-hero" id="hero" <?php if ( $has_photo ) : ?> style="--gl-hero-bg: url('<?php echo esc_url( $hero_image ); ?>')"<?php endif; ?>>

    <!-- Фонове фото або градієнт -->
    <div class="gl-hero__bg"></div>

    <!-- Контент -->
    <div class="gl-hero__content">
      <div class="gl-hero__badge gl-animate gl-animate--blur">
        <span class="gl-hero__badge-dot"></span>
        Відкрито до бронювання
      </div>

      <?php
      $hero_title    = get_theme_mod( 'gl_hero_title', "Гірська Лаванда" );
      $hero_subtitle = get_theme_mod( 'gl_hero_subtitle', 'Заміський комплекс · Східниця · Карпати' );
      ?>
      <h1 class="gl-hero__title gl-text-reveal">
        <?php echo nl2br( esc_html( $hero_title ) ); ?>
      </h1>
      <p class="gl-hero__subtitle gl-animate gl-animate--blur gl-animate--delay-1"><?php echo esc_html( $hero_subtitle ); ?></p>
      <p class="gl-hero__desc gl-animate gl-animate--blur gl-animate--delay-2">Затишний відпочинок серед карпатських сосен. Традиційна баня, гарячий чан просто неба — все для справжнього відновлення</p>

      <div class="gl-hero__actions gl-animate gl-animate--blur gl-animate--delay-3">
        <a href="/rooms/" class="gl-btn gl-btn--primary">
          Забронювати номер
        </a>
        <a href="#rooms" class="gl-btn gl-btn--outline">
          Переглянути номери
        </a>
      </div>
    </div>

    <!-- Atmospheric fog particles -->
    <canvas class="gl-hero-fog" aria-hidden="true"></canvas>

    <!-- Scroll indicator -->
    <a href="#rooms" class="gl-hero__scroll" aria-label="Прокрутити донизу">
      <span class="gl-hero__scroll-line"></span>
    </a>

  </section>

  <!-- Marquee strip between hero and rooms -->
  <div class="gl-marquee-wrap" aria-hidden="true">
    <div class="gl-marquee-track">
      <span class="gl-marquee-text">СХІДНИЦЯ&nbsp;&nbsp;&bull;&nbsp;&nbsp;КАРПАТИ&nbsp;&nbsp;&bull;&nbsp;&nbsp;ВІДПОЧИНОК&nbsp;&nbsp;&bull;&nbsp;&nbsp;БАНЯ&nbsp;&nbsp;&bull;&nbsp;&nbsp;ЧИСТЕ ПОВІТРЯ&nbsp;&nbsp;&bull;&nbsp;&nbsp;ГАРЯЧИЙ ЧАН&nbsp;&nbsp;&bull;&nbsp;&nbsp;ПРИРОДА&nbsp;&nbsp;&bull;&nbsp;&nbsp;</span>
      <span class="gl-marquee-text" aria-hidden="true">СХІДНИЦЯ&nbsp;&nbsp;&bull;&nbsp;&nbsp;КАРПАТИ&nbsp;&nbsp;&bull;&nbsp;&nbsp;ВІДПОЧИНОК&nbsp;&nbsp;&bull;&nbsp;&nbsp;БАНЯ&nbsp;&nbsp;&bull;&nbsp;&nbsp;ЧИСТЕ ПОВІТРЯ&nbsp;&nbsp;&bull;&nbsp;&nbsp;ГАРЯЧИЙ ЧАН&nbsp;&nbsp;&bull;&nbsp;&nbsp;ПРИРОДА&nbsp;&nbsp;&bull;&nbsp;&nbsp;</span>
    </div>
  </div>

  <!-- =====================================================================
       ROOMS CAROUSEL
       ===================================================================== -->
  <section class="gl-section gl-rooms gl-section--white" id="rooms">
    <div class="gl-container">
      <div class="gl-rooms__header gl-animate gl-animate--blur">
        <span class="gl-section-label">Номери</span>
        <h2 class="gl-section-title gl-text-reveal">Оберіть свій номер</h2>
        <p class="gl-section-subtitle">Кожен номер — окрема атмосфера затишку і комфорту в серці Карпат</p>
      </div>

      <div class="gl-rooms__carousel" id="rooms-carousel">
        <div class="gl-rooms__viewport">
          <div class="gl-rooms__track">
        <?php
        // Отримати апартаменти з MotoPress Booking
        $rooms = get_posts( [
            'post_type'      => 'mphb_room_type',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );

        if ( ! empty( $rooms ) ) :
            foreach ( $rooms as $room ) :
                $thumb_id  = get_post_thumbnail_id( $room->ID );
                $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
                $price     = function_exists( 'glav_get_room_price' ) ? glav_get_room_price( $room->ID ) : 0;
                $capacity  = get_post_meta( $room->ID, 'mphb_adults_capacity', true );
                $size      = get_post_meta( $room->ID, 'mphb_size', true );
                $permalink = get_permalink( $room->ID );
                $excerpt   = wp_trim_words( get_post_field( 'post_excerpt', $room->ID ) ?: get_post_field( 'post_content', $room->ID ), 18 );
        ?>
            <article class="gl-room-card">
          <div class="gl-room-card__img">
            <?php if ( $thumb_url ) : ?>
              <img src="<?php echo esc_url( $thumb_url ); ?>"
                   alt="<?php echo esc_attr( get_the_title( $room->ID ) ); ?>"
                   loading="lazy" />
            <?php else : ?>
              <div class="gl-room-card__img-placeholder">🛏️</div>
            <?php endif; ?>
            <span class="gl-room-card__badge">Карпати</span>
          </div>
          <div class="gl-room-card__body">
            <h3 class="gl-room-card__name"><?php echo esc_html( get_the_title( $room->ID ) ); ?></h3>
            <?php if ( $excerpt ) : ?>
              <p class="gl-room-card__desc"><?php echo esc_html( $excerpt ); ?></p>
            <?php endif; ?>
            <div class="gl-room-card__features">
              <?php if ( $capacity ) : ?>
              <span class="gl-room-card__feature">
                <span class="gl-room-card__feature-icon">👥</span>
                <?php echo esc_html( $capacity ); ?> гостей
              </span>
              <?php endif; ?>
              <?php if ( $size ) : ?>
              <span class="gl-room-card__feature">
                <span class="gl-room-card__feature-icon">📐</span>
                <?php echo esc_html( $size ); ?> м²
              </span>
              <?php endif; ?>
              <span class="gl-room-card__feature">
                <span class="gl-room-card__feature-icon">🌲</span>
                Вид на ліс
              </span>
            </div>
            <div class="gl-room-card__footer">
              <div class="gl-room-card__price">
                <span class="gl-room-card__price-label">від</span>
                <span class="gl-room-card__price-value">
                  <?php echo $price ? esc_html( number_format( $price, 0, '.', ' ' ) ) . ' ₴' : 'за запитом'; ?>
                </span>
                <span class="gl-room-card__price-night">/ ніч</span>
              </div>
              <a href="<?php echo esc_url( $permalink ); ?>" class="gl-btn gl-btn--sm">
                Детальніше
              </a>
            </div>
          </div>
        </article>
        <?php
            endforeach;
        endif;
        ?>
          </div><!-- /.gl-rooms__track -->
        </div><!-- /.gl-rooms__viewport -->

        <button class="gl-rooms__btn gl-rooms__btn--prev" aria-label="Попередній номер">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <button class="gl-rooms__btn gl-rooms__btn--next" aria-label="Наступний номер">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
        <div class="gl-rooms__dots" id="rooms-dots"></div>
      </div><!-- /.gl-rooms__carousel -->
    </div>
  </section>

  <!-- =====================================================================
       BANYA & CHAN
       ===================================================================== -->
  <section class="gl-section gl-wellness" id="wellness">
    <div class="gl-container">
      <div class="gl-wellness__header gl-animate gl-animate--scale">
        <span class="gl-section-label">Оздоровлення</span>
        <h2 class="gl-section-title">Баня та Чан</h2>
        <p class="gl-section-subtitle">Справжнє карпатське оздоровлення — традиційна баня і чан просто неба</p>
      </div>

      <div class="gl-wellness__grid">

        <!-- БАНЯ -->
        <div class="gl-wellness-card gl-wellness-card--banya gl-animate gl-animate--left gl-animate--delay-1">
          <div class="gl-wellness-card__img">
            <?php
            $banya_page = get_page_by_path( 'banya' ) ?: get_page_by_path( 'bania' );
            $banya_img  = $banya_page ? get_the_post_thumbnail_url( $banya_page->ID, 'large' ) : '';
            if ( $banya_img ) : ?>
              <img src="<?php echo esc_url( $banya_img ); ?>" alt="Традиційна баня" loading="lazy" />
            <?php else : ?>
              <div class="gl-wellness-card__img-placeholder">🔥</div>
            <?php endif; ?>
          </div>
          <div class="gl-wellness-card__body">
            <div class="gl-wellness-card__icon">🧖</div>
            <h3 class="gl-wellness-card__title">Традиційна Баня</h3>
            <p class="gl-wellness-card__desc">Справжня дерев'яна баня з карпатського дуба. Жар, пара, аромат трав — відчуй справжнє очищення тіла і душі.</p>
            <div class="gl-wellness-card__features">
              <span class="gl-wellness-card__feature-tag">до 8 осіб</span>
              <span class="gl-wellness-card__feature-tag">від 2 годин</span>
              <span class="gl-wellness-card__feature-tag">Парна + міні-басейн</span>
              <span class="gl-wellness-card__feature-tag">Натуральні трави</span>
            </div>
            <div class="gl-wellness-card__price">
              <span class="gl-wellness-card__price-value">від 2 500 ₴</span>
              <span class="gl-wellness-card__price-unit">/ сеанс</span>
            </div>
            <div class="gl-wellness-card__actions">
              <a href="/banya/" class="gl-btn gl-btn--outline">Детальніше</a>
            </div>
          </div>
        </div>

        <!-- ХАМАМ -->
        <div class="gl-wellness-card gl-wellness-card--hamam gl-animate gl-animate--scale gl-animate--delay-2">
          <div class="gl-wellness-card__img">
            <?php
            $hamam_page = get_page_by_path( 'hamam' );
            $hamam_img  = $hamam_page ? get_the_post_thumbnail_url( $hamam_page->ID, 'large' ) : '';
            $final_hamam_img = $hamam_img ?: $banya_img;
            
            if ( $final_hamam_img ) : ?>
              <img src="<?php echo esc_url( $final_hamam_img ); ?>" alt="Хамам" loading="lazy" />
            <?php else : ?>
              <div class="gl-wellness-card__img-placeholder">🧖</div>
            <?php endif; ?>
          </div>
          <div class="gl-wellness-card__body">
            <div class="gl-wellness-card__icon">💨</div>
            <h3 class="gl-wellness-card__title">Хамам</h3>
            <p class="gl-wellness-card__desc">Турецька парна з м'якою вологою парою та комфортною температурою — ідеально для релаксації та глибокого очищення шкіри.</p>
            <div class="gl-wellness-card__features">
              <span class="gl-wellness-card__feature-tag">від 2 годин</span>
              <span class="gl-wellness-card__feature-tag">М'яка волога пара</span>
              <span class="gl-wellness-card__feature-tag">Релаксація</span>
            </div>
            <div class="gl-wellness-card__price">
              <span class="gl-wellness-card__price-value">від 3 000 ₴</span>
              <span class="gl-wellness-card__price-unit">/ сеанс</span>
            </div>
            <div class="gl-wellness-card__actions">
              <a href="/banya/" class="gl-btn gl-btn--outline">Детальніше</a>
            </div>
          </div>
        </div>

        <!-- ЧАН -->
        <div class="gl-wellness-card gl-wellness-card--chan gl-animate gl-animate--right gl-animate--delay-3">
          <div class="gl-wellness-card__img">
            <?php
            $chan_page = get_page_by_path( 'chan' );
            $chan_img  = $chan_page ? get_the_post_thumbnail_url( $chan_page->ID, 'large' ) : '';
            if ( $chan_img ) : ?>
              <img src="<?php echo esc_url( $chan_img ); ?>" alt="Гарячий чан" loading="lazy" />
            <?php else : ?>
              <div class="gl-wellness-card__img-placeholder">🌊</div>
            <?php endif; ?>
          </div>
          <div class="gl-wellness-card__body">
            <div class="gl-wellness-card__icon">🌿</div>
            <h3 class="gl-wellness-card__title">Гарячий Чан</h3>
            <p class="gl-wellness-card__desc">Дерев'яний чан з гарячою водою просто неба. Зіркове небо, свіже карпатське повітря та тепло природного вогню — незабутній досвід.</p>
            <div class="gl-wellness-card__features">
              <span class="gl-wellness-card__feature-tag">до 8 осіб</span>
              <span class="gl-wellness-card__feature-tag">від 2 годин</span>
              <span class="gl-wellness-card__feature-tag">Підігрів дровами</span>
              <span class="gl-wellness-card__feature-tag">Увесь рік</span>
            </div>
            <div class="gl-wellness-card__price">
              <span class="gl-wellness-card__price-value">від 2 500 ₴</span>
              <span class="gl-wellness-card__price-unit">/ сеанс</span>
            </div>
            <div class="gl-wellness-card__actions">
              <a href="/chan/" class="gl-btn gl-btn--outline">Детальніше</a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- =====================================================================
       ADVANTAGES — shortcode
       ===================================================================== -->
  <?php echo do_shortcode( '[gl_advantages]' ); ?>

  <!-- =====================================================================
       TESTIMONIALS — shortcode
       ===================================================================== -->
  <?php echo do_shortcode( '[gl_testimonials]' ); ?>

  <!-- =====================================================================
       GALLERY PREVIEW — shortcode
       ===================================================================== -->
  <?php echo do_shortcode( '[gl_gallery_preview]' ); ?>


  </main>


<?php
get_footer();
