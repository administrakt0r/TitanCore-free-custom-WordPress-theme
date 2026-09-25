<?php
/**
 * Template part for displaying a message when no content is found.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_search_query = is_search();
$search_query    = get_search_query();
?>
<section class="py-12 md:py-16 text-center flex flex-col items-center justify-center border-x border-b border-border bg-card/50 px-6 space-y-6">
	<span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-muted border border-border text-muted-foreground" aria-hidden="true"><?php echo titancore_get_icon( 'inbox', 'w-6 h-6' ); ?></span>

	<div class="space-y-2 max-w-md">
		<h2 class="text-2xl md:text-3xl font-bold tracking-tight"><?php echo $is_search_query ? esc_html__( 'No results found', 'titancore' ) : esc_html__( 'Nothing found', 'titancore' ); ?></h2>
		<p class="text-muted-foreground text-sm md:text-base leading-relaxed text-balance">
			<?php
			if ( $is_search_query && ! empty( $search_query ) ) {
				/* translators: %s: Search term */
				printf( esc_html__( 'Sorry, we couldn\'t find any results matching "%s". Try adjusting your search term or explore popular topics below.', 'titancore' ), esc_html( $search_query ) );
			} else {
				esc_html_e( 'There are no posts matching your request. Try searching or browse our recent articles.', 'titancore' );
			}
			?>
		</p>
	</div>

	<div class="w-full max-w-sm pt-1">
		<?php get_search_form(); ?>
	</div>

	<?php
	$categories = get_categories( array(
		'number'     => 6,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'hide_empty' => true,
	) );
	if ( ! empty( $categories ) ) : ?>
		<div class="pt-2 w-full max-w-md">
			<span class="block text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-3"><?php esc_html_e( 'Popular Topics', 'titancore' ); ?></span>
			<div class="flex flex-wrap items-center justify-center gap-2">
				<?php foreach ( $categories as $category ) : ?>
					<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="tc-post-card__term hover:border-foreground transition-colors">
						<?php echo esc_html( $category->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php
	$recent_query = new WP_Query( array(
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );
	if ( $recent_query->have_posts() ) : ?>
		<div class="pt-6 border-t border-border/60 w-full max-w-3xl text-left">
			<h3 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground text-center mb-4"><?php esc_html_e( 'Recent Articles', 'titancore' ); ?></h3>
			<div class="grid sm:grid-cols-3 gap-3">
				<?php while ( $recent_query->have_posts() ) : $recent_query->the_post(); ?>
					<a href="<?php the_permalink(); ?>" class="group block rounded-lg border border-border bg-card p-4 hover:bg-accent transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
						<span class="block text-sm font-semibold leading-snug group-hover:text-foreground line-clamp-2"><?php the_title(); ?></span>
						<span class="block text-xs text-muted-foreground mt-2"><?php echo esc_html( get_the_date() ); ?></span>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="pt-2">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex h-10 items-center justify-center rounded-lg bg-primary px-5 text-sm font-medium text-primary-foreground hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
			<?php esc_html_e( 'Back to Home', 'titancore' ); ?>
		</a>
	</div>
</section>
