<!doctype html>
<html <?php language_attributes(); ?> class="antialiased">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

    <?php
    $theme_mode = get_theme_mod( 'theme_color_mode', 'switch' );
    if ( $theme_mode === 'dark' ) {
        echo '<script>document.documentElement.classList.add("dark");</script>';
    } elseif ( $theme_mode === 'light' ) {
        echo '<script>document.documentElement.classList.remove("dark");</script>';
    } else {
        echo '<script>try{var d=document.documentElement.classList,t=localStorage.theme;t==="dark"||(!t&&window.matchMedia("(prefers-color-scheme: dark)").matches)?d.add("dark"):d.remove("dark")}catch(e){}</script>';
    }
    ?>
    <?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-background text-foreground' ); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'titancore' ); ?></a>

<?php
$sticky_class = get_theme_mod( 'sticky_header', true ) ? 'sticky top-0 z-20' : '';
$has_primary_menu = has_nav_menu( 'primary' );
$header_controls_classes = $has_primary_menu
	? 'flex flex-1 w-full justify-end md:justify-between items-center md:ml-6'
	: 'flex flex-1 w-full justify-end items-center';
?>

<header class="<?php echo esc_attr( $sticky_class ); ?> w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
  <div class="max-w-7xl mx-auto w-full flex h-14 items-center justify-between px-6">
    <div class="mr-4 flex min-w-0">
      <?php
      $show_text = get_theme_mod( 'nav_brand_text', true );
      if ( has_custom_logo() && ! $show_text ) {
          the_custom_logo();
      } else {
          ?>
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mr-6 flex items-center space-x-2 font-medium text-lg tracking-tighter h-8 rounded-md overflow-hidden min-w-0">
              <span class="text-xl font-bold tracking-tight truncate min-w-0"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
          </a>
          <?php
      }
      ?>
    </div>

    <div class="<?php echo esc_attr( $header_controls_classes ); ?>">
      <?php if ( $has_primary_menu ) : ?>
      <nav class="hidden md:flex items-center space-x-6 text-sm font-medium">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'flex items-center space-x-6 list-none m-0 p-0',
            'fallback_cb'    => false,
            'depth'          => 1,
        ) );
        ?>
      </nav>
      <?php endif; ?>
      
      <div class="flex items-center space-x-2 md:space-x-4">
        <?php if ( $theme_mode === 'switch' ) : ?>
        <button id="theme-toggle" type="button" class="relative inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring hover:bg-accent hover:text-accent-foreground h-9 w-9" aria-label="<?php esc_attr_e( 'Toggle theme', 'titancore' ); ?>">
            <?php echo titancore_get_icon('moon', 'h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0'); ?>
            <?php echo titancore_get_icon('sun', 'absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100'); ?>
            <span class="sr-only"><?php esc_html_e( 'Toggle theme', 'titancore' ); ?></span>
        </button>
        <?php endif; ?>

        <?php if ( $has_primary_menu ) : ?>
        <button
            id="mobile-menu-btn"
            type="button"
            class="md:hidden inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-9 w-9 focus:outline-none"
            aria-label="<?php esc_attr_e( 'Open navigation menu', 'titancore' ); ?>"
            aria-haspopup="true"
            aria-controls="mobile-menu"
            aria-expanded="false"
            data-open-label="<?php echo esc_attr__( 'Open navigation menu', 'titancore' ); ?>"
            data-close-label="<?php echo esc_attr__( 'Close navigation menu', 'titancore' ); ?>"
        >
            <?php echo titancore_get_icon('menu', 'w-5 h-5'); ?>
        </button>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php if ( $has_primary_menu ) : ?>
  <!-- Mobile Navigation Dropdown -->
  <div id="mobile-menu" class="hidden md:hidden border-t border-border/40 bg-background px-6 py-4" aria-hidden="true" tabindex="-1">
    <nav class="flex flex-col space-y-4 text-sm font-medium">
      <?php
      wp_nav_menu( array(
          'theme_location' => 'primary',
          'container'      => false,
          'menu_class'     => 'flex flex-col space-y-4 list-none m-0 p-0',
          'fallback_cb'    => false,
          'depth'          => 1,
      ) );
      ?>
    </nav>
  </div>
  <?php endif; ?>
  <?php
  $custom_header_code = titancore_get_custom_code_output( 'header' );
  if ( '' !== $custom_header_code ) {
      echo $custom_header_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  }
  ?>
</header>
