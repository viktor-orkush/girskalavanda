<?php
/**
 * Template Part: Ready for Vacation Section
 * Unified CTA section for the final part of pages.
 *
 * @param array $args {
 *     @type string $title      Optional title.
 *     @type string $subtitle   Optional subtitle.
 *     @type string $wa_msg     Pre-filled WhatsApp message.
 *     @type array  $extra_btn  Optional button [ 'url', 'label', 'class' ].
 * }
 */

if (!defined('ABSPATH'))
    exit;

$title = $args['title'] ?? 'Готові до відпочинку<br>в Карпатах?';
$subtitle = $args['subtitle'] ?? '';
$wa_msg = $args['wa_msg'] ?? 'Добрий день! Хочу забронювати номер.';
$extra_btn = $args['extra_btn'] ?? null;
$hide_contact = $args['hide_contact'] ?? false;

$contact = function_exists('glav_get_contact_data')
    ? glav_get_contact_data($wa_msg)
    : [];
?>

<section class="gl-section gl-section--white gl-ready-section" id="booking-section-ready">


    <div class="gl-container">
        <div class="gl-ready-section__content gl-animate gl-center">
            <span class="gl-section-label">Бронювання</span>
            <h2 class="gl-section-title">
                <?php echo $title; ?>
            </h2>

            <?php if ($subtitle): ?>
            <p class="gl-section-subtitle">
                <?php echo esc_html($subtitle); ?>
            </p>
            <?php
endif; ?>

            <?php if ($extra_btn): ?>
            <div class="gl-ready-section__actions">
                <a href="<?php echo esc_url($extra_btn['url']); ?>"
                    class="gl-btn <?php echo esc_attr($extra_btn['class'] ?? 'gl-btn--gold'); ?>">
                    <?php echo esc_html($extra_btn['label']); ?>
                </a>
            </div>
            <?php
endif; ?>

            <?php if (!$hide_contact): ?>
            <div class="gl-ready-section__contact">
                <?php get_template_part('template-parts/booking-contact', null, $contact); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>