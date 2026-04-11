<?php
/**
 * Front page template with preset-driven layouts.
 */

get_header();

$preset = get_theme_mod( 'frontpage_preset', 'blog' );
$posts_per_page = max( 1, absint( get_theme_mod( 'home_post_limit', 10 ) ) );
$paged = max( 1, absint( get_query_var( 'paged' ) ), absint( get_query_var( 'page' ) ) );
$base_query_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
);

$pagination_base = str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) );
$render_pagination = static function ( $query ) use ( $paged, $pagination_base ) {
	if ( ! ( $query instanceof WP_Query ) || $query->max_num_pages < 2 ) {
		return;
	}

	$links = paginate_links(
		array(
			'base'      => $pagination_base,
			'format'    => '',
			'current'   => $paged,
			'total'     => (int) $query->max_num_pages,
			'mid_size'  => 2,
			'prev_text' => __( 'Previous', 'titancore' ),
			'next_text' => __( 'Next', 'titancore' ),
			'type'      => 'plain',
		)
	);

	if ( empty( $links ) ) {
		return;
	}

	echo '<div class="py-10">';
	echo '<nav class="pagination" aria-label="' . esc_attr__( 'Posts', 'titancore' ) . '">';
	echo '<div class="nav-links">' . wp_kses_post( $links ) . '</div>';
	echo '</nav>';
	echo '</div>';
};
?>

<main id="main-content" tabindex="-1" class="min-h-screen bg-background relative">
	<?php get_template_part( 'template-parts/background', 'grid' ); ?>
	<?php get_template_part( 'template-parts/front', 'header' ); ?>

	<div class="max-w-7xl mx-auto w-full px-6 lg:px-0 mt-6 box-border">
		<?php if ( 'news' === $preset ) : ?>
			<?php
			$headline_query = new WP_Query(
				array_merge(
					$base_query_args,
					array(
						'posts_per_page' => 5,
						'no_found_rows'  => true,
					)
				)
			);

			$headline_posts = is_array( $headline_query->posts ) ? $headline_query->posts : array();
			$hero_post = $headline_posts[0] ?? null;
			$trending_posts = array_slice( $headline_posts, 1, 4 );
			$excluded_ids = array_map( 'absint', wp_list_pluck( $headline_posts, 'ID' ) );

			$grid_query = new WP_Query(
				array_merge(
					$base_query_args,
					array(
						'posts_per_page' => $posts_per_page,
						'paged'          => $paged,
						'post__not_in'   => $excluded_ids,
					)
				)
			);
			?>
			<section class="tc-preset-news">
				<div class="tc-news-layout">
					<div class="tc-news-main">
						<?php if ( $hero_post instanceof WP_Post ) : ?>
							<?php
							global $post;
							$post = $hero_post;
							setup_postdata( $post );
							?>
								<article class="tc-featured-card">
									<a class="tc-featured-media" href="<?php echo esc_url( get_permalink() ); ?>">
										<?php if ( has_post_thumbnail() ) : ?>
											<?php
											the_post_thumbnail(
												'large',
												array(
													'class'         => 'tc-featured-image',
													'loading'       => 'eager',
													'fetchpriority' => 'high',
													'decoding'      => 'async',
													'sizes'         => titancore_get_image_sizes( 'news-hero' ),
												)
											);
											?>
										<?php else : ?>
											<span class="tc-image-fallback" aria-hidden="true"></span>
										<?php endif; ?>
									</a>
									<div class="tc-featured-content">
										<?php
										$hero_categories = get_the_category();
										if ( ! empty( $hero_categories ) ) :
											?>
											<span class="tc-category-pill"><?php echo esc_html( $hero_categories[0]->name ); ?></span>
										<?php endif; ?>

										<h2 class="tc-featured-title">
											<a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a>
										</h2>
										<p class="tc-featured-excerpt">
											<?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 38 ) ); ?>
										</p>
										<div class="tc-meta-row">
											<span><?php echo esc_html( get_the_author() ); ?></span>
											<span class="tc-meta-dot">&bull;</span>
											<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
										</div>
									</div>
								</article>
						<?php else : ?>
							<p class="tc-empty-note"><?php esc_html_e( 'No featured article available.', 'titancore' ); ?></p>
						<?php endif; ?>
					</div>

					<aside class="tc-news-sidebar">
						<h3 class="tc-sidebar-title"><?php esc_html_e( 'Trending Now', 'titancore' ); ?></h3>
						<?php if ( ! empty( $trending_posts ) ) : ?>
							<ol class="tc-trending-list">
								<?php
								$rank = 0;
								foreach ( $trending_posts as $trending_post ) :
									$rank++;
									$post = $trending_post;
									setup_postdata( $post );
									?>
									<li class="tc-trending-item">
										<span class="tc-rank"><?php echo esc_html( sprintf( '%02d', $rank ) ); ?></span>
										<div class="tc-trending-content">
											<h4 class="tc-trending-title">
												<a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a>
											</h4>
											<time class="tc-trending-time" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
										</div>
									</li>
								<?php endforeach; ?>
							</ol>
						<?php else : ?>
							<p class="tc-empty-note"><?php esc_html_e( 'No trending articles yet.', 'titancore' ); ?></p>
						<?php endif; ?>
					</aside>
				</div>

				<?php if ( $grid_query->have_posts() ) : ?>
					<div class="tc-news-grid">
						<?php while ( $grid_query->have_posts() ) : ?>
							<?php $grid_query->the_post(); ?>
							<article class="tc-post-card">
								<a class="tc-post-media" href="<?php echo esc_url( get_permalink() ); ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php
										the_post_thumbnail(
											'medium_large',
											array(
												'class'    => 'tc-post-image',
												'loading'  => 'lazy',
												'decoding' => 'async',
												'sizes'    => titancore_get_image_sizes( 'grid-card' ),
											)
										);
										?>
									<?php else : ?>
										<span class="tc-image-fallback" aria-hidden="true"></span>
									<?php endif; ?>
								</a>
								<div class="tc-post-body">
									<?php
									$grid_categories = get_the_category();
									if ( ! empty( $grid_categories ) ) :
										?>
										<span class="tc-card-category"><?php echo esc_html( $grid_categories[0]->name ); ?></span>
									<?php endif; ?>
									<h3 class="tc-post-title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
									<p class="tc-post-excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 22 ) ); ?></p>
									<time class="tc-post-time" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								</div>
							</article>
						<?php endwhile; ?>
					</div>
					<?php $render_pagination( $grid_query ); ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/content', 'none' ); ?>
				<?php endif; ?>
			</section>
			<?php wp_reset_postdata(); ?>

		<?php elseif ( 'magazine' === $preset ) : ?>
			<?php
			$featured_query = new WP_Query(
				array_merge(
					$base_query_args,
					array(
						'posts_per_page' => 2,
						'no_found_rows'  => true,
					)
				)
			);

			$featured_ids = array_map( 'absint', wp_list_pluck( $featured_query->posts, 'ID' ) );

			$list_query = new WP_Query(
				array_merge(
					$base_query_args,
					array(
						'posts_per_page' => $posts_per_page,
						'paged'          => $paged,
						'post__not_in'   => $featured_ids,
					)
				)
			);
			?>
			<section class="tc-preset-magazine">
				<?php if ( $featured_query->have_posts() ) : ?>
					<div class="tc-magazine-featured-grid">
						<?php
						$feature_rank = 0;
						while ( $featured_query->have_posts() ) :
							$feature_rank++;
							$featured_query->the_post();
							$feature_image_attrs = array(
								'class'    => 'tc-mag-featured-image',
								'loading'  => ( 1 === $feature_rank ) ? 'eager' : 'lazy',
								'decoding' => 'async',
								'sizes'    => titancore_get_image_sizes( 'magazine-hero' ),
							);
							if ( 1 === $feature_rank ) {
								$feature_image_attrs['fetchpriority'] = 'high';
							}
							?>
							<article class="tc-mag-featured-card">
								<a class="tc-mag-featured-media" href="<?php echo esc_url( get_permalink() ); ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php
										the_post_thumbnail(
											'large',
											$feature_image_attrs
										);
										?>
									<?php else : ?>
										<span class="tc-image-fallback" aria-hidden="true"></span>
									<?php endif; ?>
									<span class="tc-mag-gradient" aria-hidden="true"></span>
								</a>
								<div class="tc-mag-featured-content">
									<?php
									$feature_categories = get_the_category();
									if ( ! empty( $feature_categories ) ) :
										?>
										<span class="tc-category-pill tc-category-pill--on-dark"><?php echo esc_html( $feature_categories[0]->name ); ?></span>
									<?php endif; ?>
									<h2 class="tc-mag-featured-title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h2>
									<div class="tc-mag-featured-meta">
										<span><?php echo esc_html( get_the_author() ); ?></span>
										<span class="tc-meta-dot">&bull;</span>
										<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
									</div>
								</div>
							</article>
						<?php endwhile; ?>
					</div>
				<?php endif; ?>

				<?php if ( $list_query->have_posts() ) : ?>
					<div class="tc-magazine-list">
						<?php while ( $list_query->have_posts() ) : ?>
							<?php $list_query->the_post(); ?>
							<article class="tc-mag-row-card">
								<a class="tc-mag-row-media" href="<?php echo esc_url( get_permalink() ); ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php
										the_post_thumbnail(
											'medium_large',
											array(
												'class'    => 'tc-mag-row-image',
												'loading'  => 'lazy',
												'decoding' => 'async',
												'sizes'    => titancore_get_image_sizes( 'magazine-row' ),
											)
										);
										?>
									<?php else : ?>
										<span class="tc-image-fallback" aria-hidden="true"></span>
									<?php endif; ?>
								</a>
								<div class="tc-mag-row-content">
									<?php
									$list_categories = get_the_category();
									if ( ! empty( $list_categories ) ) :
										?>
										<span class="tc-card-category"><?php echo esc_html( $list_categories[0]->name ); ?></span>
									<?php endif; ?>
									<h3 class="tc-mag-row-title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
									<p class="tc-mag-row-excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 26 ) ); ?></p>
									<div class="tc-meta-row">
										<span><?php echo esc_html( get_the_author() ); ?></span>
										<span class="tc-meta-dot">&bull;</span>
										<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
									</div>
								</div>
							</article>
						<?php endwhile; ?>
					</div>
					<?php $render_pagination( $list_query ); ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/content', 'none' ); ?>
				<?php endif; ?>
			</section>
			<?php wp_reset_postdata(); ?>

		<?php else : ?>
			<?php
			$blog_query = new WP_Query(
				array_merge(
					$base_query_args,
					array(
						'posts_per_page' => $posts_per_page,
						'paged'          => $paged,
					)
				)
			);
			?>
			<?php if ( $blog_query->have_posts() ) : ?>
				<section class="tc-preset-blog">
					<div class="tc-news-grid">
						<?php $blog_rank = 0; ?>
						<?php while ( $blog_query->have_posts() ) : ?>
							<?php $blog_query->the_post(); ?>
							<?php $blog_rank++; ?>
							<?php
							$blog_image_attrs = array(
								'class'    => 'tc-post-image',
								'loading'  => ( 1 === $blog_rank ) ? 'eager' : 'lazy',
								'decoding' => 'async',
								'sizes'    => titancore_get_image_sizes( 'grid-card' ),
							);
							if ( 1 === $blog_rank ) {
								$blog_image_attrs['fetchpriority'] = 'high';
							}
							?>
							<article class="tc-post-card">
								<a class="tc-post-media" href="<?php echo esc_url( get_permalink() ); ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php
										the_post_thumbnail(
											'medium_large',
											$blog_image_attrs
										);
										?>
									<?php else : ?>
										<span class="tc-image-fallback" aria-hidden="true"></span>
									<?php endif; ?>
								</a>
								<div class="tc-post-body">
									<?php
									$grid_categories = get_the_category();
									if ( ! empty( $grid_categories ) ) :
										?>
										<span class="tc-card-category"><?php echo esc_html( $grid_categories[0]->name ); ?></span>
									<?php endif; ?>
									<h3 class="tc-post-title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
									<p class="tc-post-excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 22 ) ); ?></p>
									<time class="tc-post-time" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								</div>
							</article>
						<?php endwhile; ?>
					</div>
				</section>
				<?php $render_pagination( $blog_query ); ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
