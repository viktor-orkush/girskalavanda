<?php
/**
 * Custom Footer — Гірська Лаванда
 *
 * Overrides Astra's default footer with a branded hotel footer.
 *
 * @package Astra Child
 */

if (!defined('ABSPATH')) {
  exit;
}

$phone = get_theme_mod('gl_phone', '');
$phone_disp = get_theme_mod('gl_phone_display', $phone);
$instagram = get_theme_mod('gl_instagram', 'https://www.instagram.com/girska_lavandaa/');
$facebook = get_theme_mod('gl_facebook', '');
$telegram = get_theme_mod('gl_telegram', '');
$maps_url = get_theme_mod('gl_maps_url', 'https://www.google.com/maps/place/Комплекс+Гірська+Лаванда/@49.2191115,23.3509508,17z/data=!3m1!4b1!4m6!3m5!1s0x473a358d09cdeecf:0x65ba7a1199625777!8m2!3d49.2191115!4d23.3509508');

// Build social URLs
$telegram_url = '';
if ($telegram) {
  $telegram_url = str_starts_with($telegram, 'http')
    ? $telegram
    : 'https://t.me/' . ltrim($telegram, '@/');
}

$whatsapp_url = '';
$viber_url = '';
if ($phone) {
  $wa_phone = preg_replace('/[^0-9]/', '', $phone);
  $whatsapp_url = 'https://wa.me/' . $wa_phone;
  $viber_url = 'viber://contact?number=' . $wa_phone;
}
?>

<?php astra_content_bottom(); ?>
</div> <!-- ast-container -->
</div><!-- #content -->
<?php astra_content_after(); ?>

<!-- ================================================================
     CUSTOM FOOTER — Гірська Лаванда
     ================================================================ -->
<footer class="gl-footer" role="contentinfo" itemscope itemtype="https://schema.org/WPFooter">

  <!-- Decorative top border -->
  <div class="gl-footer__accent-bar"></div>

  <div class="gl-container">

    <!-- ── Main grid ── -->
    <div class="gl-footer__grid">

      <!-- Column 1: Brand -->
      <div class="gl-footer__col gl-footer__brand">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="gl-footer__logo">
          Гірська<span>Лаванда</span>
        </a>
        <p class="gl-footer__tagline">Комплекс відпочинку · Східниця · Карпати</p>
        <p class="gl-footer__desc">
          Затишний відпочинок серед карпатських сосен. Традиційна баня, гарячий чан просто неба та мальовничі гірські
          краєвиди.
        </p>


      </div>



      <!-- Column 3: Services -->
      <div class="gl-footer__col">
        <h4 class="gl-footer__heading">Послуги</h4>
        <ul class="gl-footer__links">
          <li><a href="<?php echo esc_url(home_url('/rooms/')); ?>">Проживання</a></li>
          <li><a href="<?php echo esc_url(home_url('/banya/')); ?>">Традиційна баня</a></li>
          <li><a href="<?php echo esc_url(home_url('/banya/')); ?>">Хамам</a></li>
          <li><a href="<?php echo esc_url(home_url('/chan/')); ?>">Гарячий чан</a></li>
        </ul>
      </div>

      <!-- Column 4: Contacts -->
      <div class="gl-footer__col">
        <h4 class="gl-footer__heading">Контакти</h4>
        <ul class="gl-footer__contacts">
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
              <circle cx="12" cy="10" r="3" />
            </svg>
            <a href="<?php echo esc_url($maps_url); ?>" target="_blank" rel="noopener">с. Східниця,
              Бориславська&nbsp;громада, Львівська&nbsp;обл.</a>
          </li>
          <?php if ($phone): ?>
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <path
                d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.5 11.61a19.79 19.79 0 01-3.07-8.67A2 2 0 012.42 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 8.9a16 16 0 006.15 6.15l1.27-.77a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z" />
            </svg>
            <a href="tel:<?php echo esc_attr($phone); ?>">
              <?php echo esc_html($phone_disp ?: $phone); ?>
            </a>
          </li>
          <?php
endif; ?>
        </ul>

        <?php if ($phone || $instagram || $telegram): ?>
        <div class="gl-footer__social">
          <?php if ($instagram): ?>
          <a href="<?php echo esc_url($instagram); ?>" class="gl-footer__social-link" target="_blank" rel="noopener"
            aria-label="Instagram" title="Instagram">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
              stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="2" width="20" height="20" rx="5" />
              <circle cx="12" cy="12" r="5" />
              <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none" />
            </svg>
          </a>
          <?php
  endif; ?>
          <?php if ($telegram_url): ?>
          <a href="<?php echo esc_url($telegram_url); ?>" class="gl-footer__social-link" target="_blank" rel="noopener"
            aria-label="Telegram" title="Telegram">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.492-1.302.48-.428-.013-1.252-.242-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z" />
            </svg>
          </a>
          <?php
  endif; ?>
          <?php if ($whatsapp_url): ?>
          <a href="<?php echo esc_url($whatsapp_url); ?>" class="gl-footer__social-link" target="_blank" rel="noopener"
            aria-label="WhatsApp" title="WhatsApp">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
            </svg>
          </a>
          <?php
  endif; ?>
          <?php if ($viber_url): ?>
          <a href="<?php echo esc_attr($viber_url); ?>" class="gl-footer__social-link" target="_blank" rel="noopener"
            aria-label="Viber" title="Viber">
            <svg width="18" height="18" viewBox="0 0 512 512" fill="currentColor">
              <path
                d="M436.4 125.1c-19.3-33-46.7-60.4-79.6-79.6-38.4-22.3-81.8-33.6-126.7-33.6-45 0-88.4 11.3-126.7 33.6-33 19.3-60.4 46.7-79.6 79.6C11.4 163.6 0 207 0 252c0 45 11.4 88.4 33.7 126.9 16.7 28.7 38.8 53.4 65.3 73.1l-10.8 32.3c-2.4 7.2.1 15 6 19.6 4.3 3.4 9.8 4.4 15 2.8l47-14.7c23.6 9 49.3 13.9 75.8 13.9 44.9 0 88.3-11.3 126.7-33.6 33-19.3 60.4-46.7 79.6-79.6C488.6 340.4 500 297 500 252c0-44.9-11.4-88.3-33.6-126.9zm-75.1 230.1c-6.8 6.7-18.7 12.1-34.9 15.6-5.8 1.3-10.2 1.6-15.5 1.6-42.3 0-97.1-23.3-132.8-59-35.7-35.7-59-90.5-59-132.8 0-5.3.3-9.7 1.6-15.5 3.5-16.2 8.9-28.1 15.6-34.9 8.8-8.8 19.9-13.3 32.7-13.3 5.4 0 10.6 1.4 15.2 4l28 16c6.4 3.6 11.1 9 14.2 16.1l11.1 26c3 7.1 3 14.8.1 21.8l-7.3 17c-2.8 6.5-1.9 14 2.5 19.6 5.5 6.9 12.3 14 20.3 21.2 7.2 6.5 14.3 13.3 21.3 18.8 5.6 4.4 13.1 5.3 19.6 2.5l17-7.3c7-3 14.7-3 21.8.1l26 11.1c7.1 3.1 12.5 7.8 16.1 14.2l16 28c2.6 4.6 4 9.8 4 15.2 0 12.8-4.5 24-13.3 32.8zM395 186.4c-4.4-23.7-22-41.2-45.7-45.7-5.9-1.1-11.4 2.8-12.5 8.7-1.1 5.9 2.8 11.4 8.7 12.5 17.6 3.3 30.6 16.4 33.9 33.9 1.1 5.9 6.6 9.8 12.5 8.7 5.9-1.1 9.8-6.6 8.7-12.5zm-33.8 16.2c-2.4-12.5-11.6-21.7-24.1-24.1-5.9-1.1-11.4 2.8-12.5 8.7-1.1 5.9 2.8 11.4 8.7 12.5 6.2 1.2 10.4 5.3 11.5 11.5 1.1 5.9 6.6 9.8 12.5 8.7 5.9-1.1 9.8-6.6 8.7-12.5zm47.7-14.7c-6.8-37.1-35.3-65.7-72.4-72.4-5.9-1.1-11.4 2.8-12.5 8.7-1.1 5.9 2.8 11.4 8.7 12.5 31.1 5.7 53.8 28.3 59.5 59.5 1.1 5.9 6.6 9.8 12.5 8.7 5.9-1-9.8-6.6-8.7-12.5z" />
            </svg>
          </a>
          <?php
  endif; ?>
        </div>
        <?php
endif; ?>
      </div>

    </div><!-- .gl-footer__grid -->

  </div><!-- .gl-container -->

  <!-- ── Bottom bar ── -->
  <div class="gl-footer__bottom">
    <div class="gl-container gl-footer__bottom-inner">
      <p class="gl-footer__copyright">
        &copy;
        <?php echo date('Y'); ?> Комплекс відпочинку Гірська Лаванда. Всі права захищені.
      </p>
      <p class="gl-footer__credits">
        <span>Східниця, Львівська область, Україна</span>
      </p>
    </div>
  </div>

</footer>

</div><!-- #page -->
<?php
astra_body_bottom();
wp_footer();
?>
</body>

</html>