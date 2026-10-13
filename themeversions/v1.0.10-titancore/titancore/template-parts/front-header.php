<?php
/**
 * Shared intro header block for home/front-page templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="p-6 border-b border-border flex flex-col gap-6 min-h-[250px] justify-center relative z-10">
	<div class="max-w-7xl mx-auto w-full">
		<div class="flex flex-col gap-2">
			<h1 class="font-medium text-4xl md:text-5xl tracking-tighter">
				<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
			</h1>
			<p class="text-muted-foreground text-sm md:text-base lg:text-lg">
				<?php echo esc_html( get_bloginfo( 'description' ) ); ?>
			</p>
		</div>
	</div>

	<?php if ( has_nav_menu( 'secondary' ) ) : ?>
		<div class="max-w-7xl mx-auto w-full">
			<nav class="flex flex-wrap gap-2 items-center" aria-label="<?php esc_attr_e( 'Secondary tags or categories menu', 'titancore' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'secondary',
						'container'      => false,
						'menu_class'     => 'flex flex-wrap gap-2 items-center secondary-nav',
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
				?>
			</nav>
		</div>
	<?php else : ?>
		<?php
		$tag_limit       = get_theme_mod( 'tag_limit', 5 );
		$tags            = titancore_get_top_tags( $tag_limit );
		$published_total = titancore_get_published_posts_count();
		$posts_page_url  = titancore_get_posts_page_url();
		?>
		<?php if ( ! empty( $tags ) ) : ?>
			<div class="max-w-7xl mx-auto w-full">
				<div class="flex flex-wrap gap-2 items-center">
					<a href="<?php echo esc_url( $posts_page_url ); ?>" class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring h-8 rounded-md px-3 border border-border bg-accent text-accent-foreground">
						<?php esc_html_e( 'All', 'titancore' ); ?>
						<span class="ml-2 text-xs text-muted-foreground"><?php echo esc_html( $published_total ); ?></span>
					</a>
					<?php foreach ( $tags as $tag ) : ?>
						<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 rounded-md px-3 text-muted-foreground">
							<?php echo esc_html( $tag->name ); ?>
							<span class="ml-2 text-xs opacity-50"><?php echo esc_html( absint( $tag->count ) ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
