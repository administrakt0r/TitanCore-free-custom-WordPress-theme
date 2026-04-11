<?php
/**
 * Template part for displaying the background grid pattern
 */
$grid_color = get_theme_mod( 'grid_pattern_color', '#6b7280' );
$grid_opacity = get_theme_mod( 'grid_pattern_opacity', 0.4 ); // Increased default visibility
?>
<!-- FlickeringGrid placeholder styling -->
<div class="absolute top-0 left-0 z-0 w-full h-[200px] [mask-image:linear-gradient(to_top,transparent_25%,black_95%)]">
  <div class="absolute top-0 left-0 size-full" style="background-image: radial-gradient(<?php echo esc_attr( $grid_color ); ?> 1px, transparent 1px); background-size: 10px 10px; opacity: <?php echo esc_attr( $grid_opacity ); ?>;"></div>
</div>
