<?php
/**
 * Astra Child Theme — Гірська Лаванда
 * functions.php — головний файл функцій
 */

// Значення Google Maps за замовчуванням
define('GL_MAPS_URL_DEFAULT', 'https://www.google.com/maps?cid=7330305559505295223');
define('GL_MAPS_EMBED_DEFAULT', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2606!2d23.3509508!3d49.2191115!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x473a358d09cdeecf%3A0x65ba7a1199625777!2z0JrQvtC80L%2FQu9C10LrRgSDQk9GW0YDRgdGM0LrQsCDQm9Cw0LLQsNC90LTQsA!5e0!3m2!1suk!2sua!4v1744905600000');

// =============================================================================
// SEO — noindex тонкого контенту MPHB booking-сторінок
// =============================================================================

/**
 * Додає noindex до пошукових сторінок бронювання MPHB.
 * URL типу /?mphb_room_type_id=123&check-in=2026-04-18 є тонким контентом —
 * не потрібні в Google-індексі.
 */
add_filter( 'wp_robots', 'glav_noindex_mphb_booking_pages' );
function glav_noindex_mphb_booking_pages( $robots ) {
    // phpcs:disable WordPress.Security.NonceVerification.Recommended
    $has_booking_params = (
        isset( $_GET['mphb_room_type_id'] ) ||
        isset( $_GET['check-in'] )          ||
        isset( $_GET['check-out'] )         ||
        isset( $_GET['mphb_availability_search'] )
    );
    // phpcs:enable

    $booking_slugs = [
        'book-now', 'your-booking-detail', 'booking-confirmation',
        'booking-cancelled', 'booking-received', 'booking-error',
        'customer-cabinet', 'booking', 'checkout',
    ];

    if ( $has_booking_params || is_page( $booking_slugs ) ) {
        $robots['noindex'] = true;
        unset( $robots['max-snippet'], $robots['max-image-preview'], $robots['max-video-preview'] );
    }
    return $robots;
}

/**
 * Allow Google to show full text snippets and large image previews in SERP.
 * Only applied to indexable pages (skipped when noindex is already set).
 */
add_filter( 'wp_robots', 'glav_seo_robots_snippet_hints' );
function glav_seo_robots_snippet_hints( $robots ) {
    if ( ! isset( $robots['noindex'] ) ) {
        $robots['max-snippet']       = '-1';
        $robots['max-image-preview'] = 'large';
    }
    return $robots;
}

// =============================================================================
// PERFORMANCE: LCP
// =============================================================================

/**
 * Preload the Hero Image for better LCP (Largest Contentful Paint).
 * Outputs separate preload links for mobile (≤ 827px) and desktop,
 * both with fetchpriority="high" so the browser starts loading immediately.
 */
add_action( 'wp_head', 'glav_preload_hero_image', 1 );
function glav_preload_hero_image() {
    $hero_image = get_theme_mod( 'gl_hero_image', '' );
    if ( ! is_front_page() || ! $hero_image ) {
        return;
    }

    // Try to get attachment ID so we can serve a mobile-sized crop.
    $attachment_id = attachment_url_to_postid( $hero_image );

    if ( $attachment_id ) {
        $mobile_src = wp_get_attachment_image_url( $attachment_id, 'gl-hero-mobile' );
        $full_src   = wp_get_attachment_image_url( $attachment_id, 'full' ) ?: $hero_image;

        if ( $mobile_src ) {
            // Mobile: load the 828 × 1024 crop (saves ~1–3 MB on phones)
            echo '<link rel="preload" as="image" href="' . esc_url( $mobile_src ) . '" media="(max-width: 827px)" fetchpriority="high" />' . "\n";
            // Desktop: load the full-resolution image
            echo '<link rel="preload" as="image" href="' . esc_url( $full_src ) . '" media="(min-width: 828px)" fetchpriority="high" />' . "\n";
            return;
        }
    }

    // Fallback: single preload without responsive splitting
    echo '<link rel="preload" as="image" href="' . esc_url( $hero_image ) . '" fetchpriority="high" />' . "\n";
}

// =============================================================================
// SEO — META TAGS, CANONICAL, OPEN GRAPH, SCHEMA
// =============================================================================

/**
 * Canonical URL tag — prevents duplicate content from MPHB booking URL params.
 * Skipped if Yoast or RankMath is active.
 */
add_action( 'wp_head', 'glav_seo_canonical', 1 );
function glav_seo_canonical() {
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
        return;
    }
    if ( is_singular() ) {
        $canonical = get_permalink();
    } elseif ( is_front_page() || is_home() ) {
        $canonical = home_url( '/' );
    } else {
        return;
    }
    echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
}

/**
 * Custom Title Tags (SEO Optimization)
 * Skipped if Yoast or RankMath is active.
 */
add_filter( 'document_title_parts', 'glav_seo_document_title_parts', 10, 1 );
function glav_seo_document_title_parts( $title ) {
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
        return $title;
    }

    if ( is_front_page() || is_page_template( 'page-home.php' ) ) {
        $title['title']   = 'Комплекс Гірська Лаванда в Східниці';
        $title['tagline'] = 'Проживання, баня та чан у Карпатах';
    } elseif ( is_page( 'banya' ) ) {
        $title['title'] = 'Баня в Карпатах · Традиційна баня на дровах';
        $title['site']  = 'Гірська Лаванда, Східниця';
    } elseif ( is_page( 'chan' ) ) {
        $title['title'] = 'Гарячий чан в Східниці · Купання просто неба';
        $title['site']  = 'Гірська Лаванда';
    } elseif ( is_page( 'rooms' ) ) {
        $title['title'] = 'Номери в Карпатах · Оренда житла в Східниці';
        $title['site']  = 'Гірська Лаванда';
    } elseif ( is_page( 'gallery' ) ) {
        $title['title'] = 'Фотогалерея · Номери, баня та чан';
        $title['site']  = 'Гірська Лаванда, Східниця';
    } elseif ( is_page( 'contact' ) ) {
        $title['title'] = 'Контакти · Бронювання та адреса';
        $title['site']  = 'Гірська Лаванда, Східниця';
    } elseif ( is_singular( 'mphb_room_type' ) ) {
        $title['title'] = get_the_title() . ' · Оренда в Східниці';
        $title['site']  = 'Гірська Лаванда';
    }

    return $title;
}

/**
 * Custom Title Separator
 */
add_filter( 'document_title_separator', 'glav_seo_document_title_separator' );
function glav_seo_document_title_separator( $sep ) {
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
        return $sep;
    }
    return '—';
}

/**
 * Returns the SEO description for the current page/post.
 * Single source of truth — used by both meta description and OG tags.
 */
function glav_seo_get_description() {
    if ( is_front_page() || is_page_template( 'page-home.php' ) ) {
        return 'Комплекс Гірська Лаванда в Східниці — відпочинок та апартаменти в оренду в Карпатах, традиційна баня та гарячий чан. Бронювання онлайн.';
    }
    if ( is_page( 'banya' ) || is_page( 'chan' ) ) {
        return 'Приватна традиційна баня на дровах в Східниці — парна, хамам, міні-басейн. Від 2 500 ₴/сеанс. Бронювання онлайн.';
    }
    if ( is_page( 'rooms' ) ) {
        return 'Номери Гірська Лаванда в Східниці, Карпати — апартаменти та стандартні номери з видом на ліс. Онлайн бронювання.';
    }
    if ( is_page( 'gallery' ) ) {
        return 'Фотогалерея комплексу Гірська Лаванда в Східниці — номери, апартаменти, баня, чан та карпатські краєвиди.';
    }
    if ( is_page( 'contact' ) ) {
        return 'Контакти комплексу Гірська Лаванда — Східниця, Львівська область. Телефон, адреса, карта проїзду.';
    }
    if ( is_singular( 'mphb_room_type' ) ) {
        $id       = get_the_ID();
        $capacity = get_post_meta( $id, 'mphb_adults_capacity', true );
        $size     = get_post_meta( $id, 'mphb_size', true );
        $price    = glav_get_room_price( $id );
        $parts    = array_filter( [
            $capacity ? $capacity . ' гост.' : '',
            $size     ? $size . ' м²'        : '',
            $price    ? 'від ' . $price . ' ₴/ніч' : '',
        ] );
        return get_the_title() . ' — комплекс Гірська Лаванда, Східниця.' . ( $parts ? ' ' . implode( ', ', $parts ) . '.' : '' );
    }
    return get_bloginfo( 'description' );
}

/**
 * Meta description tag per page type.
 * Skipped if Yoast or RankMath is active.
 */
add_action( 'wp_head', 'glav_seo_meta_description', 1 );
function glav_seo_meta_description() {
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
        return;
    }
    $desc = glav_seo_get_description();
    if ( $desc ) {
        echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
    }
}

/**
 * Geo meta tags for local SEO (Bing, Yandex, directories).
 * Skipped if Yoast or RankMath is active.
 */
add_action( 'wp_head', 'glav_seo_geo_meta', 3 );
function glav_seo_geo_meta() {
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
        return;
    }
    echo '<meta name="geo.region"    content="UA-46" />' . "\n"; // Львівська обл.
    echo '<meta name="geo.placename" content="Східниця" />' . "\n";
    echo '<meta name="geo.position"  content="49.219197;23.35088" />' . "\n";
    echo '<meta name="ICBM"          content="49.219197, 23.35088" />' . "\n";
}

/**
 * Open Graph + Twitter Card meta tags.
 * Skipped if Yoast or RankMath is active.
 */
add_action( 'wp_head', 'glav_seo_og_tags', 5 );
function glav_seo_og_tags() {
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
        return;
    }

    $site_name   = get_bloginfo( 'name' );
    $default_img = get_theme_mod( 'gl_hero_image', '' );

    $title = wp_get_document_title();
    $url   = is_singular() ? get_permalink() : ( is_front_page() ? home_url( '/' ) : get_pagenum_link() );
    $type  = is_singular( 'mphb_room_type' ) ? 'article' : 'website';
    $image = $default_img;
    $desc  = glav_seo_get_description();

    if ( ( is_page( 'banya' ) || is_page( 'chan' ) || is_singular( 'mphb_room_type' ) ) && has_post_thumbnail() ) {
        $image = get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: $default_img;
    }

    ?>
<meta property="og:type" content="<?php echo esc_attr( $type ); ?>" />
<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>" />
<meta property="og:url" content="<?php echo esc_url( $url ); ?>" />
<meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>" />
<meta property="og:locale" content="uk_UA" />
<?php if ( $image ) : ?>
<meta property="og:image" content="<?php echo esc_url( $image ); ?>" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
<meta name="twitter:description" content="<?php echo esc_attr( $desc ); ?>" />
<?php if ( $image ) : ?>
<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>" />
<?php endif; ?>
    <?php
}

/**
 * LodgingBusiness JSON-LD schema — outputs on all pages.
 * Uses LodgingBusiness (parent of Hotel) so Google can surface hotel rich results.
 */
add_action( 'wp_head', 'glav_schema_lodging_business', 10 );
function glav_schema_lodging_business() {
    $phone      = get_theme_mod( 'gl_phone', '' );
    $instagram  = get_theme_mod( 'gl_instagram', '' );
    $facebook   = get_theme_mod( 'gl_facebook', '' );
    $maps_url   = get_theme_mod( 'gl_maps_url', '' );
    $hero_image = get_theme_mod( 'gl_hero_image', '' );

    $same_as = array_values( array_filter( [ $instagram, $facebook ] ) );

    $schema = [
        '@context'      => 'https://schema.org',
        '@type'         => 'LodgingBusiness',
        '@id'           => home_url( '/#hotel' ),
        'name'          => 'Гірська Лаванда',
        'alternateName' => 'Girska Lavanda',
        'description'   => 'Заміський комплекс у серці Карпат в Східниці. Проживання, традиційна баня на дровах, гарячий чан серед природи.',
        'url'           => home_url( '/' ),
        'address'       => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'с. Східниця',
            'addressLocality' => 'Східниця',
            'addressRegion'   => 'Львівська область',
            'addressCountry'  => 'UA',
            'postalCode'      => '82460',
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => 49.219197,
            'longitude' => 23.35088,
        ],
        'checkinTime'  => 'T14:00',
        'checkoutTime' => 'T12:00',
        'priceRange'   => '₴₴',
        'amenityFeature' => [
            [ '@type' => 'LocationFeatureSpecification', 'name' => 'Free WiFi',    'value' => true ],
            [ '@type' => 'LocationFeatureSpecification', 'name' => 'Free parking', 'value' => true ],
            [ '@type' => 'LocationFeatureSpecification', 'name' => 'Sauna',        'value' => true ],
            [ '@type' => 'LocationFeatureSpecification', 'name' => 'Hot tub',      'value' => true ],
        ],
    ];

    if ( $phone ) {
        $schema['contactPoint'] = [
            '@type'       => 'ContactPoint',
            'telephone'   => $phone,
            'contactType' => 'reservations',
            'areaServed'  => 'UA',
        ];
    }

    $schema['image'] = $hero_image ?: home_url( '/wp-content/uploads/2025/07/hero-hotel.jpg' );
    if ( $same_as ) {
        $schema['sameAs'] = $same_as;
    }
    if ( $maps_url ) {
        $schema['hasMap'] = $maps_url;
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/**
 * ApartmentUnit JSON-LD schema — only on single room type pages.
 */
add_action( 'wp_head', 'glav_schema_hotel_room', 10 );
function glav_schema_hotel_room() {
    if ( ! is_singular( 'mphb_room_type' ) ) {
        return;
    }

    $id       = get_the_ID();
    $title    = get_the_title();
    $url      = get_permalink();
    $image    = get_the_post_thumbnail_url( $id, 'full' );
    $capacity = (int) get_post_meta( $id, 'mphb_adults_capacity', true );
    $size     = (float) get_post_meta( $id, 'mphb_size', true );
    $price    = glav_get_room_price( $id );
    $amenities_raw = glav_get_room_amenities( $id );
    $excerpt  = get_the_excerpt() ?: wp_trim_words( get_the_content(), 30 );

    $schema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'ApartmentUnit',
        'name'             => $title,
        'url'              => $url,
        'description'      => $excerpt,
        'containedInPlace' => [ '@id' => home_url( '/#apartments' ) ],
    ];

    if ( $image ) {
        $schema['image'] = $image;
    }
    if ( $capacity ) {
        $schema['occupancy'] = [
            '@type'    => 'QuantitativeValue',
            'minValue' => 1,
            'maxValue' => $capacity,
        ];
    }
    if ( $size ) {
        $schema['floorSize'] = [
            '@type'    => 'QuantitativeValue',
            'value'    => $size,
            'unitCode' => 'MTK',
        ];
    }
    if ( $amenities_raw ) {
        $schema['amenityFeature'] = array_map( function( $name ) {
            return [ '@type' => 'LocationFeatureSpecification', 'name' => $name, 'value' => true ];
        }, $amenities_raw );
    }
    if ( $price ) {
        $schema['offers'] = [
            '@type'         => 'Offer',
            'price'         => $price,
            'priceCurrency' => 'UAH',
            'priceSpecification' => [
                '@type'         => 'UnitPriceSpecification',
                'price'         => $price,
                'priceCurrency' => 'UAH',
                'unitText'      => 'night',
            ],
        ];
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/**
 * Service + Offer JSON-LD schema — only on /banya/ page.
 */
add_action( 'wp_head', 'glav_schema_banya', 10 );
function glav_schema_banya() {
    if ( ! is_page( 'banya' ) && ! is_page( 'chan' ) ) {
        return;
    }

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Service',
        'name'        => 'Традиційна баня Гірська Лаванда',
        'description' => 'Приватна традиційна дерев\'яна баня на дровах в Східниці. Парна, хамам, міні-басейн, гарячий дерев\'яний чан під відкритим небом.',
        'provider'    => [ '@id' => home_url( '/#apartments' ) ],
        'areaServed'  => [ '@type' => 'City', 'name' => 'Східниця' ],
        'url'         => get_permalink(),
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name'  => 'Послуги бані',
            'itemListElement' => [
                [
                    '@type'       => 'Offer',
                    'itemOffered' => [
                        '@type'       => 'Service',
                        'name'        => 'Баня (традиційна парна)',
                        'description' => 'Традиційна дерев\'яна парна на дровах, до 8 осіб',
                    ],
                    'price'         => '2500',
                    'priceCurrency' => 'UAH',
                    'priceSpecification' => [
                        '@type'         => 'UnitPriceSpecification',
                        'price'         => '2500',
                        'priceCurrency' => 'UAH',
                        'unitText'      => 'session',
                        'minPrice'      => '2500',
                    ],
                ],
                [
                    '@type'       => 'Offer',
                    'itemOffered' => [
                        '@type'       => 'Service',
                        'name'        => 'Хамам',
                        'description' => 'Турецька парна з вологою парою',
                    ],
                    'price'         => '3000',
                    'priceCurrency' => 'UAH',
                ],
            ],
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/**
 * FAQPage JSON-LD schema — for /banya/ and /chan/ pages.
 */
add_action( 'wp_head', 'glav_schema_faq', 10 );
function glav_schema_faq() {
    $faqs = [];

    if ( is_page( 'banya' ) ) {
        $faqs = [
            [
                'q' => 'Скільки коштує баня в Карпатах (Східниці)?',
                'a' => 'Вартість оренди нашої приватної бані починається від 2 500 ₴ за сеанс (мінімум 2 години). У вартість входить традиційна парна на дровах, кімната відпочинку та закрита територія.'
            ],
            [
                'q' => 'Чи можна приїжджати в баню з дітьми?',
                'a' => 'Так, звичайно! Наша закрита територія цілком безпечна для дітей, а в кімнаті відпочинку є все необхідне для комфортного перебування всією сім\'єю.'
            ],
            [
                'q' => 'Скільки людей вміщує баня?',
                'a' => 'Парна та кімната відпочинку комфортно вміщують компанію до 8 осіб одночасно.'
            ],
            [
                'q' => 'Що входить у вартість оренди бані?',
                'a' => 'У вартість входить: парна на дровах, міні-басейн (купіль) з холодною водою, кімната відпочинку з телевізором та міні-кухнею, а також безкоштовний паркінг на закритій території.'
            ],
            [
                'q' => 'Чи працює баня взимку?',
                'a' => 'Так, наш комплекс відпочинку працює цілий рік — 365 днів на рік у будь-яку погоду.'
            ],
            [
                'q' => 'Чи можна у вас замовити хамам?',
                'a' => 'Так, поруч із традиційною парною є хамам. Це окрема додаткова послуга, вартість якої становить від 3 000 ₴ за сеанс.'
            ]
        ];
    } elseif ( is_page( 'chan' ) ) {
        $faqs = [
            [
                'q' => 'Скільки коштує чан у Карпатах (Східниці)?',
                'a' => 'Оренда гарячого чану просто неба коштує від 2 500 ₴ за сеанс (мінімум 2 години). У ціну також входить користування кімнатою відпочинку та закритою територією.'
            ],
            [
                'q' => 'Чи можна купатися в чані з дітьми?',
                'a' => 'Так, відпочинок у чані чудово підходить для сімей з дітьми. Вода нагрівається до комфортної і безпечної температури 38–42°C.'
            ],
            [
                'q' => 'Скільки людей вміщує чан?',
                'a' => 'Наш просторий чан розрахований на комфортний відпочинок компанії до 8 осіб.'
            ],
            [
                'q' => 'Що входить до оренди чану?',
                'a' => 'До вартості входить: закритий від сторонніх гарячий чан з підігрівом на дровах, кімната відпочинку з ТБ, міні-кухня, душ, роздягальня та безкоштовний паркінг.'
            ],
            [
                'q' => 'Чи працює чан узимку?',
                'a' => 'Так! Купання в гарячому чані під відкритим небом взимку в оточенні снігу — це один із найкращих видів релаксу в Карпатах.'
            ],
            [
                'q' => 'Чи можна додати в чан цілющі трави?',
                'a' => 'Звичайно. За вашим бажанням ми можемо додати цілющі карпатські трави для ароматерапії та кращого оздоровчого ефекту.'
            ]
        ];
    }

    if ( empty( $faqs ) ) {
        return;
    }

    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => []
    ];

    foreach ( $faqs as $faq ) {
        $schema['mainEntity'][] = [
            '@type'          => 'Question',
            'name'           => $faq['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $faq['a']
            ]
        ];
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/**
 * BreadcrumbList JSON-LD schema.
 */
add_action( 'wp_head', 'glav_schema_breadcrumb', 10 );
function glav_schema_breadcrumb() {
    $home = [ '@type' => 'ListItem', 'position' => 1, 'name' => 'Головна', 'item' => home_url( '/' ) ];

    if ( is_front_page() || is_home() ) {
        $items = [ $home ];
    } elseif ( is_page( 'rooms' ) ) {
        $items = [ $home, [ '@type' => 'ListItem', 'position' => 2, 'name' => 'Номери', 'item' => get_permalink() ] ];
    } elseif ( is_page( 'banya' ) || is_page( 'chan' ) ) {
        $items = [ $home, [ '@type' => 'ListItem', 'position' => 2, 'name' => 'Баня та Чан', 'item' => get_permalink() ] ];
    } elseif ( is_singular( 'mphb_room_type' ) ) {
        $rooms_page = get_page_by_path( 'rooms' );
        $rooms_url  = $rooms_page ? get_permalink( $rooms_page->ID ) : home_url( '/rooms/' );
        $items = [
            $home,
            [ '@type' => 'ListItem', 'position' => 2, 'name' => 'Номери', 'item' => $rooms_url ],
            [ '@type' => 'ListItem', 'position' => 3, 'name' => get_the_title(), 'item' => get_permalink() ],
        ];
    } else {
        return;
    }

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/**
 * ImageObject JSON-LD schema — helper to output multiple images.
 * @param array $images Array of arrays with keys: 'url', 'name', 'description', 'alt'
 */
function glav_render_image_schema( $images ) {
    if ( empty( $images ) ) {
        return;
    }

    $schema_list = [];
    foreach ( $images as $img ) {
        if ( empty( $img['url'] ) ) continue;
        
        $name = !empty( $img['name'] ) ? $img['name'] : ( !empty( $img['alt'] ) ? $img['alt'] : 'Фото Гірська Лаванда' );
        $desc = !empty( $img['description'] ) ? $img['description'] : $name;

        $schema_list[] = [
            '@context'       => 'https://schema.org',
            '@type'          => 'ImageObject',
            'contentUrl'     => $img['url'],
            'name'           => wp_strip_all_tags( $name ),
            'description'    => wp_strip_all_tags( $desc ),
            'contentLocation' => [
                '@type' => 'Place',
                'name'  => 'Східниця, Карпати',
                'geo'   => [
                    '@type'     => 'GeoCoordinates',
                    'latitude'  => 49.219197,
                    'longitude' => 23.35088,
                ]
            ],
            'creator' => [
                '@type' => 'Organization',
                'name'  => 'Гірська Лаванда'
            ]
        ];
    }
    
    if ( empty( $schema_list ) ) {
        return;
    }

    // Output directly when called (usually inside page templates, inline JSON-LD is valid and parsed by Google anywhere in HTML)
    $output_data = count( $schema_list ) === 1 ? $schema_list[0] : $schema_list;
    echo '<script type="application/ld+json">' . wp_json_encode( $output_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}


/**
 * Sitemap: remove internal MPHB post types and taxonomies from WP built-in sitemap.
 */
add_filter( 'wp_sitemaps_post_types', 'glav_sitemap_filter_post_types' );
function glav_sitemap_filter_post_types( $post_types ) {
    $remove = [ 'mphb_rate', 'mphb_season', 'mphb_room', 'mphb_booking', 'mphb_reserved_room', 'mphb_coupon', 'mphb_payment' ];
    foreach ( $remove as $type ) {
        unset( $post_types[ $type ] );
    }
    return $post_types;
}

add_filter( 'wp_sitemaps_taxonomies', 'glav_sitemap_filter_taxonomies' );
function glav_sitemap_filter_taxonomies( $taxonomies ) {
    $remove = [ 'mphb_room_type_category', 'mphb_room_type_facility', 'mphb_bed_type', 'mphb_season_rule' ];
    foreach ( $remove as $tax ) {
        unset( $taxonomies[ $tax ] );
    }
    return $taxonomies;
}

/**
 * Sitemap: remove users sub-sitemap (security — hides admin username).
 */
add_filter( 'wp_sitemaps_add_provider', 'glav_sitemap_remove_users_provider', 10, 2 );
function glav_sitemap_remove_users_provider( $provider, $name ) {
    if ( 'users' === $name ) {
        return false;
    }
    return $provider;
}

/**
 * Sitemap: exclude MPHB technical/utility pages from the pages sub-sitemap.
 * IDs: all auto-generated MPHB booking-flow pages + customer cabinet duplicates.
 */
add_filter( 'wp_sitemaps_posts_query_args', 'glav_sitemap_exclude_mphb_pages', 10, 2 );
function glav_sitemap_exclude_mphb_pages( $args, $post_type ) {
    if ( 'page' !== $post_type ) {
        return $args;
    }

    // Technical pages generated by MPHB and other utility pages not meant for indexing.
    $exclude_ids = [
        2176, // book-now
        2177, // your-booking-detail
        2178, // our-rooms (MPHB default, duplicate of /rooms/)
        2180, // Помешкання (MPHB)
        2181, // Перевірити наявність вільних місць (MPHB)
        2182, // Результати пошуку (MPHB)
        2183, // Скасування бронювання (MPHB)
        2184, // Підтвердження бронювання (MPHB)
        2185, // Бронювання підтверджене (MPHB child)
        2186, // Бронювання скасоване (MPHB child)
        2187, // Бронювання отримане (MPHB child)
        2188, // Помилка транзакції (MPHB child)
        2189, // Мій обліковий запис (MPHB)
        21,   // customer-cabinet
        2614, // hamam (hidden helper page)
    ];

    $args['post__not_in'] = array_merge(
        isset( $args['post__not_in'] ) ? $args['post__not_in'] : [],
        $exclude_ids
    );

    return $args;
}

/**
 * Sitemap: override lastmod for /banya/ and /chan/ to today's date
 * so Google re-crawls them after recent content updates (FAQ, schema).
 */
add_filter( 'wp_sitemaps_posts_entry', 'glav_sitemap_fix_lastmod', 10, 3 );
function glav_sitemap_fix_lastmod( $entry, $post, $post_type ) {
    if ( 'page' !== $post_type ) {
        return $entry;
    }

    $refresh_slugs = [ 'banya', 'chan' ];
    if ( in_array( $post->post_name, $refresh_slugs, true ) ) {
        $entry['lastmod'] = gmdate( 'c' ); // current UTC date in ISO 8601
    }

    return $entry;
}

// =============================================================================
// FONT PRELOAD — критичні шрифти для LCP
// =============================================================================
add_action( 'wp_head', 'glav_preload_fonts', 1 );
function glav_preload_fonts() {
    $base = get_stylesheet_directory_uri() . '/assets/fonts/';
    $fonts = [
        'cormorant-garamond-v16-latin_cyrillic-regular.woff2',
        'nunito-sans-v19-cyrillic.woff2',
        'nunito-sans-v19-latin.woff2',
    ];
    foreach ( $fonts as $font ) {
        echo '<link rel="preload" href="' . esc_url( $base . $font ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
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
    wp_script_add_data( 'gl-main-js', 'defer', true );
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
// HEADER NAV CUSTOMIZATIONS (Logo in middle, Phone at end)
// =============================================================================
add_filter( 'wp_nav_menu_items', 'glav_customize_header_menu', 10, 2 );
function  glav_customize_header_menu( $items, $args ) {
    // 1. Add Phone Number (Primary and Mobile)
    if ( in_array( $args->theme_location, [ 'primary', 'mobile_menu' ], true ) ) {
        $phone      = get_theme_mod( 'gl_phone', '' );
        $phone_disp = get_theme_mod( 'gl_phone_display', $phone );

        if ( $phone ) {
            $items .= '<li class="menu-item menu-item-phone">'
                     . '<a href="tel:' . esc_attr( $phone ) . '">'
                     . '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:5px"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.5 11.61a19.79 19.79 0 01-3.07-8.67A2 2 0 012.42 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 8.9a16 16 0 006.15 6.15l1.27-.77a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>'
                     . esc_html( $phone_disp ?: $phone )
                     . '</a></li>';
        }
    }

    // 2. Sort Mobile Menu items (same logical order as desktop, no branding logo)
    if ( in_array( $args->theme_location, [ 'mobile_menu', 'ast-mobile-menu', 'primary_mobile', 'off-canvas' ], true ) ) {
        // Split by TOP-LEVEL </li> only
        $depth      = 0;
        $current    = '';
        $menu_items = [];
        $tokens     = preg_split( '/(<li\b[^>]*>|<\/li>)/i', $items, -1, PREG_SPLIT_DELIM_CAPTURE );
        foreach ( $tokens as $token ) {
            if ( preg_match( '/^<li\b/i', $token ) ) {
                $depth++;
                $current .= $token;
            } elseif ( strcasecmp( $token, '</li>' ) === 0 ) {
                $depth--;
                $current .= '</li>';
                if ( $depth === 0 ) {
                    $menu_items[] = $current;
                    $current      = '';
                }
            } else {
                $current .= $token;
            }
        }

        // Remove "Home" / "Головна"
        $menu_items = array_values( array_filter( $menu_items, function ( $item ) {
            return stripos( $item, '>Головна<' ) === false && stripos( $item, '>Home<' ) === false;
        } ) );

        // Sort: Номери, Баня, Чан, Галерея, Контакти, Phone
        $mobile_order = [
            'rooms'    => 10, 'номери'   => 10,
            'banya'    => 20, 'баня'     => 20,
            'chan'     => 30, 'чан'      => 30,
            'gallery'  => 40, 'галерея'  => 40,
            'contact'  => 50, 'контакти' => 50,
            'phone'    => 60,
            'book'     => 70,
        ];

        usort( $menu_items, function ( $a, $b ) use ( $mobile_order ) {
            $a_score = 999;
            $b_score = 999;
            foreach ( $mobile_order as $key => $score ) {
                if ( stripos( $a, $key ) !== false ) { $a_score = min( $a_score, $score ); }
                if ( stripos( $b, $key ) !== false ) { $b_score = min( $b_score, $score ); }
            }
            return $a_score - $b_score;
        } );

        $items = implode( '', $menu_items );
    }

    // 3. Add Centered Logo (Primary Desktop Menu ONLY)
    if ( $args->theme_location === 'primary' ) {
        // Split by TOP-LEVEL </li> only — correctly handles nested sub-menus.
        // explode('</li>') breaks when items have children (sub-menu </li> tags inflate the count).
        $depth      = 0;
        $current    = '';
        $menu_items = [];
        $tokens     = preg_split( '/(<li\b[^>]*>|<\/li>)/i', $items, -1, PREG_SPLIT_DELIM_CAPTURE );
        foreach ( $tokens as $token ) {
            if ( preg_match( '/^<li\b/i', $token ) ) {
                $depth++;
                $current .= $token;
            } elseif ( strcasecmp( $token, '</li>' ) === 0 ) {
                $depth--;
                $current .= '</li>';
                if ( $depth === 0 ) {
                    $menu_items[] = $current;
                    $current      = '';
                }
            } else {
                $current .= $token;
            }
        }

        // Remove "Home" / "Головна"
        $menu_items = array_values( array_filter( $menu_items, function ( $item ) {
            return stripos( $item, '>Головна<' ) === false && stripos( $item, '>Home<' ) === false;
        } ) );

        // 3. Reorder items: [Rooms], [Banya], [Chan] | LOGO | [Gallery], [Contact], [Phone]
        $order_map = [
            'rooms'    => 10, 'номери'   => 10,
            'banya'    => 20, 'баня'     => 20,
            'chan'     => 30, 'чан'      => 30,
            'gallery'  => 40, 'галерея'  => 40,
            'contact'  => 50, 'контакти' => 50,
            'phone'    => 60,
            'book'     => 70,
        ];

        usort( $menu_items, function ( $a, $b ) use ( $order_map ) {
            $a_score = 999;
            $b_score = 999;
            foreach ( $order_map as $key => $score ) {
                if ( stripos( $a, $key ) !== false ) { $a_score = min( $a_score, $score ); }
                if ( stripos( $b, $key ) !== false ) { $b_score = min( $b_score, $score ); }
            }
            return $a_score - $b_score;
        } );

        // Find insertion point (middle of top-level items)
        // Use floor() to ensure that with an odd number of items (e.g. 5), 
        // the middle item (Gallery) moves to the right side of the logo.
        $count     = count( $menu_items );
        $insert_at = (int) floor( $count / 2 );

        // Branding Data
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        $logo_url = $custom_logo_id ? wp_get_attachment_image_url( $custom_logo_id, 'full' ) : '';
        $site_name = get_bloginfo( 'name' ); // e.g. "Гірська Лаванда"

        // Split name for "Logo in middle of name" effect
        $clean_name = str_replace( 'Комплекс ', '', $site_name );
        $name_parts = explode( ' ', $clean_name );
        $part1 = $name_parts[0] ?? '';
        $part2 = isset($name_parts[1]) ? implode(' ', array_slice($name_parts, 1)) : '';

        $logo_html = '<li class="menu-item menu-item-logo">'
                   . '<a href="' . home_url('/') . '" rel="home">'
                   . '<span class="gl-logo-word gl-logo-word--1">' . esc_html($part1) . '</span>'
                   . ($logo_url ? '<div class="gl-menu-logo-wrap"><img src="' . esc_url($logo_url) . '" alt="' . esc_attr($site_name) . '" class="gl-menu-logo"></div>' : '')
                   . '<span class="gl-logo-word gl-logo-word--2">' . esc_html($part2) . '</span>'
                   . '</a></li>';

        array_splice( $menu_items, $insert_at, 0, $logo_html );
        $items = implode( '', $menu_items );
    }

    return $items;
}

/**
 * 4. Filter Astra Logo to include "Word [Logo] Word" on Mobile (Header Bar)
 */
add_filter( 'astra_logo', 'glav_customize_mobile_header_logo', 10, 1 );
function glav_customize_mobile_header_logo( $html ) {
    // Only apply for mobile devices or tablet views
    if ( ! wp_is_mobile() ) {
        return $html;
    }
    
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo_url       = $custom_logo_id ? wp_get_attachment_image_url( $custom_logo_id, 'full' ) : '';
    $site_name      = get_bloginfo( 'name' );
    $clean_name     = str_replace( 'Комплекс ', '', $site_name );
    $name_parts     = explode( ' ', $clean_name );
    $part1          = $name_parts[0] ?? '';
    $part2          = isset( $name_parts[1] ) ? implode( ' ', array_slice( $name_parts, 1 ) ) : '';

    if ( ! $part1 && ! $part2 ) {
        return $html;
    }

    $branding_html = '<span class="gl-logo-word gl-logo-word--1">' . esc_html( $part1 ) . '</span>'
                  . ( $logo_url ? '<div class="gl-menu-logo-wrap"><img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $site_name ) . '" class="gl-menu-logo"></div>' : '' )
                  . '<span class="gl-logo-word gl-logo-word--2">' . esc_html( $part2 ) . '</span>';

    // Replace the content of the link in $html with our branding_html
    if ( preg_match( '/(<a\b[^>]*>)(.*)(<\/a>)/is', $html, $matches ) ) {
        return $matches[1] . $branding_html . $matches[3];
    }

    return $html;
}

// =============================================================================
// CONTACT INFO HELPER
// =============================================================================

/**
 * Повертає масив контактних даних з theme mods.
 * $wa_context — текст повідомлення для WhatsApp (напр. 'Хочу забронювати бані.')
 */
function glav_get_contact_info( $wa_context = 'Добрий день! Хочу дізнатися деталі.' ) {
    $phone       = get_theme_mod( 'gl_phone', '' );
    $phone_disp  = get_theme_mod( 'gl_phone_display', $phone );
    $telegram_raw = get_theme_mod( 'gl_telegram', '' );

    $telegram_url = '';
    if ( $telegram_raw ) {
        $telegram_url = str_starts_with( $telegram_raw, 'http' )
            ? $telegram_raw
            : 'https://t.me/' . ltrim( $telegram_raw, '@/' );
    }

    $whatsapp_url = $viber_url = '';
    if ( $phone ) {
        $wa_phone     = preg_replace( '/[^0-9]/', '', $phone );
        $whatsapp_url = 'https://wa.me/' . $wa_phone . '?text=' . rawurlencode( $wa_context );
        $viber_url    = 'viber://contact?number=' . $wa_phone;
    }

    $instagram_dm = 'https://www.instagram.com/girska_lavandaa/';
    $ig_raw = get_theme_mod( 'gl_instagram', '' );
    if ( $ig_raw ) {
        preg_match( '/instagram\.com\/([^\/\?#]+)/i', $ig_raw, $m );
        $ig_user      = isset( $m[1] ) ? trim( $m[1], '/' ) : '';
        $instagram_dm = $ig_user ? 'https://ig.me/m/' . $ig_user : $ig_raw;
    }

    return compact( 'phone', 'phone_disp', 'telegram_url', 'whatsapp_url', 'viber_url', 'instagram_dm' );
}

/**
 * Очищає transient галереї при оновленні медіафайлів.
 */
add_action( 'add_attachment', 'glav_clear_gallery_transients' );
add_action( 'edit_attachment', 'glav_clear_gallery_transients' );
add_action( 'delete_attachment', 'glav_clear_gallery_transients' );
function glav_clear_gallery_transients() {
    delete_transient( 'glav_gallery_preview_images' );
    delete_transient( 'glav_gallery_page_imgs' );
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
            <?php $maps_embed_url = get_theme_mod( 'gl_maps_embed_url', GL_MAPS_EMBED_DEFAULT ); ?>
            <iframe
              src="<?php echo esc_url( $maps_embed_url ); ?>"
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
    $images = get_transient( 'glav_gallery_preview_images' );

    if ( $images === false ) {
        // Отримати медіафайли (беремо більше, щоб відфільтрувати логотипи та кропи)
        $all_images = get_posts( [
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => 12, // беремо із запасом
            'orderby'        => 'date',
            'order'          => 'DESC',
        ] );

        $images = [];
        if ( ! empty( $all_images ) ) {
            foreach ( $all_images as $img ) {
                $filename = basename( get_attached_file( $img->ID ) );
                // Пропускаємо логотипи, кропи та занадто малі фото
                if ( stripos( $filename, 'logo' ) !== false || stripos( $filename, 'cropped' ) !== false ) {
                    continue;
                }
                $images[] = $img;
                if ( count( $images ) >= 5 ) break;
            }
        }

        set_transient( 'glav_gallery_preview_images', $images, DAY_IN_SECONDS );
    }

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
          <div class="gl-testimonials__dots"></div>
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

    // Hero images: mobile portrait crop (2× for 414px wide screens) and medium desktop.
    add_image_size( 'gl-hero-mobile', 828, 1024, true );
    add_image_size( 'gl-hero-medium', 1440, 900, false );
    // Room card thumbnails: uniform crop for carousel/list layouts.
    add_image_size( 'gl-room-card', 640, 480, true );
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
            'label'       => 'Фонове фото Hero (рекомендовано 1920×1080)',
            'description' => 'Горизонтальне фото для десктопу.',
            'section'     => 'gl_hero',
        ]
    ) );

    // Hero фото для мобільних
    $wp_customize->add_setting( 'gl_hero_image_mobile', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control(
        $wp_customize, 'gl_hero_image_mobile', [
            'label'       => 'Фонове фото Hero — мобільна версія',
            'description' => 'Вертикальне фото для смартфонів (рекомендовано 800×1200). Якщо не задане — використовується десктопне.',
            'section'     => 'gl_hero',
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
        'default'           => 'https://www.instagram.com/girska_lavandaa/',
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
        'default'           => GL_MAPS_URL_DEFAULT,
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'gl_maps_url', [
        'label'       => 'Google Maps посилання',
        'description' => 'Пряме посилання на Google Maps для апартаментів',
        'section'     => 'gl_contacts',
        'type'        => 'url',
    ] );

    $wp_customize->add_setting( 'gl_maps_embed_url', [
        'default'           => GL_MAPS_EMBED_DEFAULT,
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'gl_maps_embed_url', [
        'label'       => 'Google Maps Embed iframe URL',
        'description' => 'URL для iframe карти (src="..." атрибут з Google Maps)',
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
        // Baня і чан — shared facilities
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
// FLOATING CONTACT WIDGET (Chaty-style FAB)
// =============================================================================

// Disable Astra's built-in scroll-to-top button
add_filter( 'astra_get_option_scroll-to-top-enable', '__return_false' );

add_action( 'wp_footer', 'glav_floating_contact' );
function glav_floating_contact() {
    $data = glav_get_contact_data();

    $channels = [];

    if ( ! empty( $data['phone'] ) ) {
        $channels[] = [
            'url'   => 'tel:' . $data['phone'],
            'label' => 'Зателефонувати',
            'color' => '#03E78B',
            'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
        ];
    }

    if ( ! empty( $data['telegram_url'] ) ) {
        $channels[] = [
            'url'   => $data['telegram_url'],
            'label' => 'Telegram',
            'color' => '#2AABEE',
            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
        ];
    }

    if ( ! empty( $data['whatsapp_url'] ) ) {
        $channels[] = [
            'url'   => $data['whatsapp_url'],
            'label' => 'WhatsApp',
            'color' => '#25D366',
            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>',
        ];
    }

    if ( ! empty( $data['viber_url'] ) ) {
        $channels[] = [
            'url'   => $data['viber_url'],
            'label' => 'Viber',
            'color' => '#665CAC',
            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.398.002C9.473.028 5.331.344 3.014 2.467 1.294 4.177.518 6.77.399 9.932c-.12 3.163-.27 9.09 5.563 10.665l.004.002v2.458s-.038.99.613 1.195c.79.249 1.254-.508 2.01-1.318.413-.443.983-1.093 1.413-1.59 3.9.327 6.894-.422 7.234-.534.784-.258 5.22-.824 5.943-6.726.745-6.079-.354-9.917-2.347-11.65l-.002-.004c-.6-.574-2.986-2.239-8.523-2.393 0 0-.387-.02-.908-.013zM11.5 1.59c.455-.007.78.012.78.012 4.671.13 6.774 1.468 7.283 1.952 1.683 1.46 2.593 4.87 1.94 10.143-.608 4.958-4.194 5.29-4.876 5.514-.287.095-2.836.732-6.16.531 0 0-2.44 2.942-3.2 3.708-.12.12-.258.166-.352.144-.13-.03-.166-.18-.164-.396l.028-4.015c-4.917-1.327-4.623-6.327-4.523-8.976.1-2.648.727-4.88 2.178-6.316 1.958-1.795 5.612-2.076 7.345-2.3h-.001l.72.001zm.7 2.834c-.162 0-.294.132-.294.296a.295.295 0 0 0 .294.296c1.14.013 2.197.46 3.005 1.289.808.828 1.27 1.933 1.298 3.11a.295.295 0 0 0 .296.291h.004a.295.295 0 0 0 .291-.3c-.033-1.378-.575-2.673-1.525-3.647-.95-.974-2.19-1.518-3.5-1.535h-.068zM8.073 6.26c-.232-.009-.476.092-.655.303l-.002.002c-.342.37-.706.77-.727 1.225-.037.66.35 1.277.658 1.732l.024.034c.836 1.29 1.862 2.47 3.07 3.404l.016.013.013.015c.76.615 1.636 1.13 2.583 1.418l.004.002.03.012c.405.154.814.095 1.162-.092.348-.188.603-.495.735-.828.073-.182.058-.378-.06-.52-.426-.52-.946-.96-1.512-1.332-.28-.174-.598-.063-.754.063l-.496.457a.36.36 0 0 1-.39.063c-.558-.243-1.874-1.326-2.375-1.858a.357.357 0 0 1-.034-.413l.376-.55c.15-.218.196-.542.005-.81a9.68 9.68 0 0 0-1.171-1.299.535.535 0 0 0-.372-.152c-.043-.002-.085-.002-.128-.004zm4.534.466c-.163 0-.295.134-.293.297.01.895.377 1.748 1.024 2.395.648.648 1.497 1.01 2.392 1.025h.01a.295.295 0 0 0 .003-.59c-.715-.013-1.39-.3-1.907-.818-.518-.518-.81-1.199-.82-1.916a.296.296 0 0 0-.296-.293h-.113zm.095 1.563a.295.295 0 0 0-.28.31c.03.42.213.81.518 1.107.305.298.698.472 1.107.493a.295.295 0 0 0 .03-.59.988.988 0 0 1-.71-.315.988.988 0 0 1-.332-.71.295.295 0 0 0-.297-.291l-.035-.004z"/></svg>',
        ];
    }

    if ( ! empty( $data['instagram_dm'] ) ) {
        $channels[] = [
            'url'   => $data['instagram_dm'],
            'label' => 'Instagram',
            'color' => '#E1306C',
            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>',
        ];
    }

    if ( empty( $channels ) ) {
        return;
    }
    ?>
    <div class="gl-contact-fab" id="gl-contact-fab">
      <div class="gl-contact-fab__channels">
        <?php foreach ( $channels as $i => $ch ) : ?>
          <a class="gl-contact-fab__channel"
             href="<?php echo esc_url( $ch['url'] ); ?>"
             target="_blank" rel="noopener noreferrer"
             aria-label="<?php echo esc_attr( $ch['label'] ); ?>"
             style="--ch-color: <?php echo esc_attr( $ch['color'] ); ?>; --ch-i: <?php echo $i; ?>">
            <span class="gl-contact-fab__channel-icon"><?php echo $ch['icon']; ?></span>
            <span class="gl-contact-fab__channel-label"><?php echo esc_html( $ch['label'] ); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
      <button class="gl-contact-fab__toggle" id="gl-contact-fab-toggle"
              aria-label="Контакти" title="Зв'язатися з нами">
        <svg class="gl-contact-fab__icon-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <svg class="gl-contact-fab__icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <?php
}

// =============================================================================
// LAYOUT: FORCE FULL-WIDTH STRETCHED for Room Pages
// =============================================================================
/**
 * Forces Astra to use 'page-builder' (Full Width / Stretched) layout for single room types.
 * This natively removes the .ast-container max-width and padding constraints.
 */
add_filter( 'astra_get_content_layout', function( $layout ) {
    if ( is_singular( 'mphb_room_type' ) ) {
        return 'page-builder';
    }
    return $layout;
} );
