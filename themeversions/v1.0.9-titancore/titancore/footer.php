<footer class="site-footer bg-background border-t border-border">
  <div class="site-footer__inner max-w-7xl mx-auto px-6 py-8">
    <div class="site-footer__top">
      <div class="site-footer__brand">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-footer__title">
          <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
        </a>
        <?php if ( get_bloginfo( 'description' ) ) : ?>
          <p class="site-footer__description"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
        <?php endif; ?>
      </div>

      <?php if ( has_nav_menu( 'footer' ) ) : ?>
      <nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer navigation', 'titancore' ); ?>">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'footer',
            'container'      => false,
            'menu_class'     => 'flex flex-wrap gap-x-6 gap-y-2 list-none m-0 p-0 text-sm text-muted-foreground',
            'fallback_cb'    => false,
            'depth'          => 1,
        ) );
        ?>
      </nav>
      <?php endif; ?>
    </div>

    <?php if ( is_active_sidebar( 'footer-widgets' ) ) : ?>
      <div class="site-footer__widgets">
        <?php dynamic_sidebar( 'footer-widgets' ); ?>
      </div>
    <?php endif; ?>

    <div class="site-footer__bottom text-sm text-muted-foreground">
      <p class="m-0">
        &copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>.
        <?php esc_html_e( 'All rights reserved.', 'titancore' ); ?>
      </p>
      <p class="m-0 flex flex-wrap items-center gap-x-1">
        <?php
        printf(
            /* translators: %s: theme author link */
            esc_html__( 'Theme by %s', 'titancore' ),
            '<a href="https://administraktor.com" target="_blank" rel="noopener noreferrer" class="font-medium hover:underline text-foreground">' . esc_html__( 'administraktor.com', 'titancore' ) . '</a>'
        );
        ?>
        <span aria-hidden="true">&middot;</span>
        <a href="https://wpineu.com" title="<?php esc_attr_e( 'WordPress Hosting in Europe', 'titancore' ); ?>" target="_blank" rel="noopener noreferrer" class="font-medium hover:underline text-foreground"><?php esc_html_e( 'WPinEU.com', 'titancore' ); ?></a>
        <?php esc_html_e( '— WordPress Hosting in Europe', 'titancore' ); ?>
      </p>
      <a class="site-footer__back-top" href="#main-content"><?php esc_html_e( 'Back to top', 'titancore' ); ?></a>
    </div>
  </div>
  <?php
  $custom_footer_code = titancore_get_custom_code_output( 'footer' );
  if ( '' !== $custom_footer_code ) {
      echo $custom_footer_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  }
  ?>
</footer>

<?php wp_footer(); ?>
</body>
</html>
