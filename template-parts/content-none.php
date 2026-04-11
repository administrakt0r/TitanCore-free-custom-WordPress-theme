<?php
/**
 * Template part for displaying a message when no content is found.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="py-20 text-center flex flex-col items-center justify-center space-y-4 border-x border-b border-border">
	<h2 class="text-2xl font-medium tracking-tighter"><?php esc_html_e( 'Nothing found', 'titancore' ); ?></h2>
	<p class="text-muted-foreground"><?php esc_html_e( 'Try searching with a different term.', 'titancore' ); ?></p>
	<?php get_search_form(); ?>
</section>
