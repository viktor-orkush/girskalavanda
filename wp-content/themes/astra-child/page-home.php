<?php
/**
 * Template Name: Головна сторінка
 * Description: Повна головна сторінка готелю Гірська Лаванда
 */

get_header();
?>
<main id="main" class="gl-home-page">

  <!-- =====================================================================
       HERO
       ===================================================================== -->
  <?php
  $hero_image = get_theme_mod( 'gl_hero_image', '' );
  $has_photo  = ! empty( $hero_image );
  ?>
  <section class="gl-hero gl-hero--with-booking" id="hero">

    <!-- Фонове фото або градієнт -->
    <div class="gl-hero__bg"<?php if ( $has_photo ) : ?> style="background-image: url('<?php echo esc_url( $hero_image ); ?>')"<?php endif; ?>></div>

    <!-- Контент -->
    <div class="gl-hero__content">
      <div class="gl-hero__badge">
        <span class="gl-hero__badge-dot"></span>
        Відкрито до бронювання
      </div>

      <?php
      $hero_title    = get_theme_mod( 'gl_hero_title', "Гірська\nЛаванда" );
      $hero_subtitle = get_theme_mod( 'gl_hero_subtitle', 'Парк готель · Східниця · Карпати' );
      $title_parts   = explode( "\n", $hero_title );
      ?>
      <h1 class="gl-hero__title">
        <?php echo implode( '<br>', array_map( 'esc_html', $title_parts ) ); ?>
      </h1>
      <p class="gl-hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
      <p class="gl-hero__desc">Затишний відпочинок серед карпатських сосен. Традиційна баня, гарячий чан просто неба, домашні сніданки — все для справжнього відновлення</p>

      <div class="gl-hero__actions">
        <a href="/rooms/" class="gl-btn gl-btn--primary">
          Забронювати номер
        </a>
        <a href="#rooms" class="gl-btn gl-btn--outline">
          Переглянути номери
        </a>
      </div>
    </div>

    <!-- Stats -->
    <div class="gl-hero__stats">
      <div class="gl-hero__stat">
        <div class="gl-hero__stat-number">4</div>
        <div class="gl-hero__stat-label">Номери</div>
      </div>
      <div class="gl-hero__stat">
        <div class="gl-hero__stat-number">★ 5.0</div>
        <div class="gl-hero__stat-label">Рейтинг</div>
      </div>
      <div class="gl-hero__stat">
        <div class="gl-hero__stat-number">3+</div>
        <div class="gl-hero__stat-label">Роки роботи</div>
      </div>
    </div>

    <!-- Scroll indicator -->
    <a href="#rooms" class="gl-hero__scroll" aria-label="Прокрутити донизу">
      <span class="gl-hero__scroll-line"></span>
      <span>Scroll</span>
    </a>

    <!-- Booking strip з формою перевірки доступності -->
    <div class="gl-hero__booking">
      <div class="gl-hero__booking-inner">
        <span class="gl-hero__booking-label">Перевірити доступність</span>
        <?php if ( function_exists( 'MPHB' ) ) : ?>
          <?php echo do_shortcode( '[mphb_check_form]' ); ?>
        <?php else : ?>
          <a href="/rooms/" class="gl-btn gl-btn--primary" style="margin-left:auto;">Переглянути номери та ціни →</a>
        <?php endif; ?>
      </div>
    </div>

  </section>

  <!-- =====================================================================
       ROOMS
       ===================================================================== -->
  <section class="gl-section gl-rooms gl-section--white" id="rooms">
    <div class="gl-container">
      <div class="gl-rooms__header gl-animate">
        <span class="gl-section-label">Номери</span>
        <h2 class="gl-section-title">Оберіть свій номер</h2>
        <p class="gl-section-subtitle">Кожен номер — окрема атмосфера затишку і комфорту в серці Карпат</p>
      </div>

      <div class="gl-rooms__grid">
        <?php
        // Отримати кімнати з MotoPress Hotel Booking
        $rooms = get_posts( [
            'post_type'      => 'mphb_room_type',
            'posts_per_page' => 4,
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );

        if ( ! empty( $rooms ) ) :
            foreach ( $rooms as $i => $room ) :
                $thumb_id  = get_post_thumbnail_id( $room->ID );
                $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
                $price     = function_exists( 'glav_get_room_price' ) ? glav_get_room_price( $room->ID ) : 0;
                $capacity  = get_post_meta( $room->ID, 'mphb_adults_capacity', true );
                $size      = get_post_meta( $room->ID, 'mphb_size', true );
                $permalink = get_permalink( $room->ID );
                $excerpt   = wp_trim_words( get_post_field( 'post_excerpt', $room->ID ) ?: get_post_field( 'post_content', $room->ID ), 18 );
        ?>
        <article class="gl-room-card gl-animate gl-animate--delay-<?php echo min( $i + 1, 5 ); ?>">
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
              <a href="<?php echo esc_url( $permalink ); ?>" class="gl-btn gl-btn--accent gl-btn--sm">
                Детальніше
              </a>
            </div>
          </div>
        </article>
        <?php
            endforeach;
        else :
            // Fallback якщо MotoPress не налаштований
            $fallback_rooms = [
                [ 'name' => 'Стандартний двомісний номер',        'desc' => 'Затишний номер з двома окремими ліжками і видом на карпатський ліс. Ідеальний для двох.',                               'price' => '800',   'capacity' => 2, 'children' => 0, 'size' => 15, 'icon' => '🛏️' ],
                [ 'name' => 'Двомісні апартаменти з терасою',     'desc' => 'Просторі апартаменти з власною терасою і панорамним видом на гори. Двоспальне ліжко + диван.', 'price' => '1 400', 'capacity' => 2, 'children' => 0, 'size' => 25, 'icon' => '🏡' ],
                [ 'name' => 'Сімейні апартаменти',                'desc' => 'Ідеальний варіант для сімей з дітьми. Двоспальне ліжко + розкладний диван у просторому номері.',                         'price' => '1 500', 'capacity' => 4, 'children' => 2, 'size' => 35, 'icon' => '👨‍👩‍👧' ],
                [ 'name' => 'Двоповерхові апартаменти з терасою', 'desc' => 'Розкішні двоповерхові апартаменти з власною терасою — максимум простору і приватності в Карпатах.',                     'price' => '2 500', 'capacity' => 4, 'children' => 0, 'size' => 70, 'icon' => '🏠' ],
            ];
            foreach ( $fallback_rooms as $i => $room ) :
        ?>
        <article class="gl-room-card gl-animate gl-animate--delay-<?php echo min( $i + 1, 5 ); ?>">
          <div class="gl-room-card__img">
            <div class="gl-room-card__img-placeholder"><?php echo $room['icon']; ?></div>
            <span class="gl-room-card__badge">Карпати</span>
          </div>
          <div class="gl-room-card__body">
            <h3 class="gl-room-card__name"><?php echo esc_html( $room['name'] ); ?></h3>
            <p class="gl-room-card__desc"><?php echo esc_html( $room['desc'] ); ?></p>
            <div class="gl-room-card__features">
              <span class="gl-room-card__feature"><span class="gl-room-card__feature-icon">👥</span>
                <?php echo $room['capacity']; ?> гост.<?php if ( ! empty( $room['children'] ) ) : ?> + <?php echo $room['children']; ?> діт.<?php endif; ?>
              </span>
              <span class="gl-room-card__feature"><span class="gl-room-card__feature-icon">📐</span> <?php echo $room['size']; ?> м²</span>
              <span class="gl-room-card__feature"><span class="gl-room-card__feature-icon">🌲</span> Вид на ліс</span>
            </div>
            <div class="gl-room-card__footer">
              <div class="gl-room-card__price">
                <span class="gl-room-card__price-label">від</span>
                <span class="gl-room-card__price-value"><?php echo $room['price']; ?> ₴</span>
                <span class="gl-room-card__price-night">/ ніч</span>
              </div>
              <a href="/rooms/" class="gl-btn gl-btn--accent gl-btn--sm">Детальніше</a>
            </div>
          </div>
        </article>
        <?php
            endforeach;
        endif;
        ?>
      </div>
    </div>
  </section>

  <!-- =====================================================================
       BANYA & CHAN
       ===================================================================== -->
  <section class="gl-section gl-wellness" id="wellness">
    <div class="gl-container">
      <div class="gl-wellness__header gl-animate">
        <span class="gl-section-label">Оздоровлення</span>
        <h2 class="gl-section-title">Баня та Чан</h2>
        <p class="gl-section-subtitle">Справжнє карпатське оздоровлення — традиційна баня і чан просто неба</p>
      </div>

      <div class="gl-wellness__grid">

        <!-- БАНЯ -->
        <div class="gl-wellness-card gl-wellness-card--banya gl-animate gl-animate--delay-1">
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
              <span class="gl-wellness-card__feature-tag">до 6 осіб</span>
              <span class="gl-wellness-card__feature-tag">2 години</span>
              <span class="gl-wellness-card__feature-tag">Парна + купель</span>
              <span class="gl-wellness-card__feature-tag">Натуральні трави</span>
            </div>
            <div class="gl-wellness-card__price">
              <span class="gl-wellness-card__price-value">від 800 ₴</span>
              <span class="gl-wellness-card__price-unit">/ сеанс</span>
            </div>
            <a href="/banya/" class="gl-btn gl-btn--primary">Забронювати бані</a>
          </div>
        </div>

        <!-- ЧАН -->
        <div class="gl-wellness-card gl-wellness-card--chan gl-animate gl-animate--delay-2">
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
              <span class="gl-wellness-card__feature-tag">1–2 години</span>
              <span class="gl-wellness-card__feature-tag">Підігрів дровами</span>
              <span class="gl-wellness-card__feature-tag">Увесь рік</span>
            </div>
            <div class="gl-wellness-card__price">
              <span class="gl-wellness-card__price-value">від 600 ₴</span>
              <span class="gl-wellness-card__price-unit">/ сеанс</span>
            </div>
            <a href="/chan/" class="gl-btn gl-btn--primary">Забронювати чан</a>
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

  <!-- =====================================================================
       CONTACTS — shortcode
       ===================================================================== -->
  <?php echo do_shortcode( '[gl_contacts]' ); ?>

</main>

<?php
get_footer();
