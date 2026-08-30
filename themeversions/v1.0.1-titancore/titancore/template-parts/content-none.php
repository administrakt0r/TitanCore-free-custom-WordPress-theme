<?php
/**
 * Template part for displaying a message when no content is found.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="py-16 text-center flex flex-col items-center justify-center space-y-4 border-x border-b border-border bg-card/50 px-6">
	<span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-muted border border-border text-muted-foreground" aria-hidden="true"><?php echo titancore_get_icon( 'inbox', 'w-5 h-5' ); ?></span>
	<h2 class="text-2xl font-medium tracking-tighter"><?php esc_html_e( 'Nothing found', 'titancore' ); ?></h2>
	<p class="text-muted-foreground max-w-md text-balance"><?php esc_html_e( 'There are no posts to display yet. Try a search or browse the latest articles.', 'titancore' ); ?></p>
	<div class="w-full max-w-sm pt-1"><?php get_search_form(); ?></div>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex h-11 items-center justify-center rounded-lg bg-primary px-6 text-sm font-medium text-primary-foreground hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"><?php esc_html_e( 'Browse all articles', 'titancore' ); ?></a>
</section>
