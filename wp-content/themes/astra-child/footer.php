<?php
/**
 * Custom Footer — Гірська Лаванда
 *
 * Overrides Astra's default footer with a branded hotel footer.
 *
 * @package Astra Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Gather contact data
$phone      = get_theme_mod( 'gl_phone', '' );
$phone_disp = get_theme_mod( 'gl_phone_display', $phone );
$instagram  = get_theme_mod( 'gl_instagram', '' );
$facebook   = get_theme_mod( 'gl_facebook', '' );
$telegram   = get_theme_mod( 'gl_telegram', '' );

// Build social URLs
$telegram_url = '';
if ( $telegram ) {
	$telegram_url = str_starts_with( $telegram, 'http' )
		? $telegram
		: 'https://t.me/' . ltrim( $telegram, '@/' );
}

$whatsapp_url = '';
$viber_url    = '';
if ( $phone ) {
	$wa_phone     = preg_replace( '/[^0-9]/', '', $phone );
	$whatsapp_url = 'https://wa.me/' . $wa_phone;
	$viber_url    = 'viber://contact?number=' . $wa_phone;
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
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="gl-footer__logo">
          Гірська<span>Лаванда</span>
        </a>
        <p class="gl-footer__tagline">Парк готель · Східниця · Карпати</p>
        <p class="gl-footer__desc">
          Затишний відпочинок серед карпатських сосен. Традиційна баня, гарячий чан просто неба та мальовничі гірські краєвиди.
        </p>

        <?php if ( $phone || $instagram || $telegram ) : ?>
        <div class="gl-footer__social">
          <?php if ( $instagram ) : ?>
            <a href="<?php echo esc_url( $instagram ); ?>" class="gl-footer__social-link" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg>
            </a>
          <?php endif; ?>
          <?php if ( $telegram_url ) : ?>
            <a href="<?php echo esc_url( $telegram_url ); ?>" class="gl-footer__social-link" target="_blank" rel="noopener" aria-label="Telegram" title="Telegram">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.492-1.302.48-.428-.013-1.252-.242-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ( $whatsapp_url ) : ?>
            <a href="<?php echo esc_url( $whatsapp_url ); ?>" class="gl-footer__social-link" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ( $viber_url ) : ?>
            <a href="<?php echo esc_attr( $viber_url ); ?>" class="gl-footer__social-link" target="_blank" rel="noopener" aria-label="Viber" title="Viber">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M11.398.002C9.47.028 5.196.344 3.005 2.394.84 4.463.135 7.442.017 11.183c-.117 3.743-.263 10.768 6.586 12.65l.007.003h.006l-.006 2.896s-.044.554.341.667c.096.028.192.022.299-.058.276-.218 3.958-3.265 3.958-3.265l.109-.076c2.298.264 4.079-.072 4.282-.14.466-.154 3.099-.489 3.526-3.99.44-3.616.258-5.915-.144-7.707l-.003-.01c-.258-1.149-.736-2.04-1.4-2.72.018-.043-.223-.467-.223-.467C15.662.656 12.616 0 11.398.002zm.212 1.847c1.164.022 3.596.57 5.028 2.426l.001.001.18.273C17.416 5.069 17.76 5.8 17.962 6.744c.342 1.519.493 3.53.105 6.735-.344 2.816-2.388 3.013-2.77 3.14-.16.053-1.657.33-3.573.145l-.09-.012-1.784 1.493c-.16.132-.35.153-.35-.08v-1.644c-.002-.112-.076-.163-.076-.163C3.3 15.199 3.474 9.585 3.56 6.67l-.001.004c.01-.3.024-.598.047-.89.129-1.82.54-3.147 1.546-4.14C6.685 3.047 8.858 2.362 9.937 2.154c.395-.078.97-.268 1.673-.305zm-.048 2.31c-.17.004-.34.02-.512.052C10.042 4.434 8.397 4.99 7.38 6.006c-.714.712-1.04 1.61-1.11 2.83-.015.2-.023.399-.03.604-.063 2.254-.07 5.775 4.597 6.726l.022.002v.776l1.052-.903c.157-.132.35-.155.537-.127 1.613.156 2.896-.075 3.03-.112.234-.082 1.588-.22 1.84-2.263.32-2.592.217-4.246-.065-5.503-.145-.645-.393-1.179-.76-1.59l-.045-.056C15.363 5.262 13.7 4.546 12.607 4.296c-.356-.07-.705-.126-1.045-.138zm.153 1.475c.094 0 .188.016.283.048.003.001.23.087.473.205.504.247.91.581 1.175.97.31.455.305.915.29 1.093-.03.337-.267.563-.56.525-.282-.035-.442-.312-.462-.59-.013-.185-.067-.384-.24-.637a2.67 2.67 0 00-.735-.618c-.113-.06-.293-.143-.503-.089-.249.064-.335-.19-.34-.347-.007-.176.083-.432.393-.525.069-.022.15-.034.226-.035zm-2.69.88c.155-.004.32.063.46.218l.005.004c.157.17.32.353.432.568.172.331 0 .679-.245.795l-.168.098c-.19.116-.32.347-.24.614.304 1.02.82 1.93 1.6 2.675.33.315.824.586 1.22.816.188.109.397.06.553-.07l.12-.118c.275-.268.615-.284.867-.093.258.196.509.413.742.647.295.293.103.665-.101.805l-.005.003-.125.094a1.97 1.97 0 01-.992.427c-.371.046-.758-.054-1.108-.196-1.677-.68-3.025-1.828-3.984-3.41-.358-.592-.6-1.26-.577-1.98.015-.452.191-.862.528-1.165.115-.104.139-.124.297-.23.081-.055.178-.1.28-.132.053-.015.106-.025.16-.027l-.001-.001c.027-.002.054-.003.08-.003zm4.575 1.076c.069 0 .142.01.22.036.267.09.38.326.35.564-.012.088-.008.22.073.478.06.192.156.346.32.469.103.078.27.148.444.063.265-.129.534.065.59.302.06.25-.094.5-.348.582-.173.058-.385.027-.517-.014C14.02 12.918 13.44 12.195 13.2 11.597c-.067-.165-.12-.337-.111-.516.013-.255.162-.479.42-.49h.09z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ( $facebook ) : ?>
            <a href="<?php echo esc_url( $facebook ); ?>" class="gl-footer__social-link" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Column 2: Navigation -->
      <div class="gl-footer__col">
        <h4 class="gl-footer__heading">Навігація</h4>
        <ul class="gl-footer__links">
          <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Головна</a></li>
          <li><a href="<?php echo esc_url( home_url( '/rooms/' ) ); ?>">Номери</a></li>
          <li><a href="<?php echo esc_url( home_url( '/banya/' ) ); ?>">Баня</a></li>
          <li><a href="<?php echo esc_url( home_url( '/chan/' ) ); ?>">Чан</a></li>
          <li><a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>">Галерея</a></li>
          <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Контакти</a></li>
        </ul>
      </div>

      <!-- Column 3: Services -->
      <div class="gl-footer__col">
        <h4 class="gl-footer__heading">Послуги</h4>
        <ul class="gl-footer__links">
          <li><a href="<?php echo esc_url( home_url( '/rooms/' ) ); ?>">Проживання</a></li>
          <li><a href="<?php echo esc_url( home_url( '/banya/' ) ); ?>">Традиційна баня</a></li>
          <li><a href="<?php echo esc_url( home_url( '/banya/' ) ); ?>">Хамам</a></li>
          <li><a href="<?php echo esc_url( home_url( '/chan/' ) ); ?>">Гарячий чан</a></li>
          <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Безкоштовний паркінг</a></li>
        </ul>
      </div>

      <!-- Column 4: Contacts -->
      <div class="gl-footer__col">
        <h4 class="gl-footer__heading">Контакти</h4>
        <ul class="gl-footer__contacts">
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>с. Східниця, Бориславська&nbsp;громада, Львівська&nbsp;обл.</span>
          </li>
          <?php if ( $phone ) : ?>
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.5 11.61a19.79 19.79 0 01-3.07-8.67A2 2 0 012.42 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 8.9a16 16 0 006.15 6.15l1.27-.77a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
            <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone_disp ?: $phone ); ?></a>
          </li>
          <?php endif; ?>
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <a href="mailto:info@girskalavanda.com">info@girskalavanda.com</a>
          </li>
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Check-in: 14:00 · Check-out: 12:00</span>
          </li>
        </ul>
      </div>

    </div><!-- .gl-footer__grid -->

  </div><!-- .gl-container -->

  <!-- ── Bottom bar ── -->
  <div class="gl-footer__bottom">
    <div class="gl-container gl-footer__bottom-inner">
      <p class="gl-footer__copyright">
        &copy; <?php echo date( 'Y' ); ?> Готель Гірська Лаванда. Всі права захищені.
      </p>
      <p class="gl-footer__credits">
        <span>Східниця, Карпати, Україна</span>
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
