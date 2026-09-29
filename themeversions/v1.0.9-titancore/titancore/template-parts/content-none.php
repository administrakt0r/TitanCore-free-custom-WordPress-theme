<?php
/**
 * Template part for displaying a message when no content is found.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_search_query = is_search();
$search_query    = get_search_query();
$icon_name       = $is_search_query ? 'search-x' : 'inbox';
?>
<section class="py-12 md:py-16 text-center flex flex-col items-center justify-center border-x border-b border-border bg-card/50 px-6 space-y-6">
	<span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-muted border border-border text-muted-foreground shadow-sm" aria-hidden="true">
		<?php echo titancore_get_icon( $icon_name, 'w-6 h-6' ); ?>
	</span>

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
		<div class="pt-2 w-full max-w-lg">
			<span class="block text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-3"><?php esc_html_e( 'Popular Topics', 'titancore' ); ?></span>
			<div class="flex flex-wrap items-center justify-center gap-2">
				<?php foreach ( $categories as $category ) : ?>
					<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="tc-empty-topic-chip inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-md border border-border bg-accent/60 hover:bg-accent hover:border-foreground/40 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
						<span><?php echo esc_html( $category->name ); ?></span>
						<span class="tc-empty-topic-count px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-background border border-border/80 text-muted-foreground"><?php echo esc_html( $category->count ); ?></span>
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
				<?php while ( $recent_query->have_posts() ) : $recent_query->the_post();
					$reading_time = function_exists( 'titancore_get_estimated_reading_time' ) ? titancore_get_estimated_reading_time( get_the_ID() ) : 0;
					?>
					<a href="<?php the_permalink(); ?>" class="tc-empty-recovery-card group block rounded-lg border border-border bg-card p-4 hover:bg-accent transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
						<span class="block text-sm font-semibold leading-snug group-hover:text-foreground line-clamp-2"><?php the_title(); ?></span>
						<div class="flex items-center gap-2 text-xs text-muted-foreground mt-3 pt-2 border-t border-border/40">
							<span><?php echo esc_html( get_the_date() ); ?></span>
							<?php if ( $reading_time > 0 ) : ?>
								<span class="text-border">•</span>
								<span><?php printf( esc_html__( '%d min read', 'titancore' ), (int) $reading_time ); ?></span>
							<?php endif; ?>
						</div>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="pt-2">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex h-10 items-center justify-center rounded-lg bg-primary px-5 text-sm font-medium text-primary-foreground hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-colors">
			<?php esc_html_e( 'Back to Home', 'titancore' ); ?>
		</a>
	</div>
</section>
