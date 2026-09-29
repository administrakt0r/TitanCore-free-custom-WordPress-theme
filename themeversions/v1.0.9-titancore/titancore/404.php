<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header(); ?>

<main id="main-content" tabindex="-1" class="min-h-[70vh] bg-background flex flex-col items-center justify-center w-full z-10 relative px-6 py-16">
  <?php get_template_part( 'template-parts/background', 'grid' ); ?>
  <div class="text-center flex flex-col gap-5 max-w-md mx-auto relative z-10">
    <span class="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground border border-border shadow-sm"><?php echo titancore_get_icon('file-question', 'w-6 h-6'); ?></span>
    <h1 class="text-7xl md:text-8xl font-mono font-bold tracking-tighter text-primary">404</h1>
    <h2 class="text-xl font-semibold tracking-tight"><?php esc_html_e( 'Page not found', 'titancore' ); ?></h2>
    <p class="text-muted-foreground text-sm md:text-base leading-relaxed text-balance">
      <?php esc_html_e( 'Sorry, we couldn\'t find the page you\'re looking for. The page might have been moved, deleted, or you entered the wrong URL.', 'titancore' ); ?>
    </p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center mt-2">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary-foreground shadow hover:bg-primary/90 rounded-lg h-11 px-6">
        <?php esc_html_e( 'Back to Home', 'titancore' ); ?>
      </a>
      <a href="<?php echo esc_url( get_search_link() ); ?>" class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring bg-card border border-border hover:bg-accent hover:text-accent-foreground rounded-lg h-11 px-6">
        <?php esc_html_e( 'Search articles', 'titancore' ); ?>
      </a>
    </div>
    <div class="mt-4 w-full max-w-sm mx-auto">
      <?php get_search_form(); ?>
    </div>
  </div>

  <?php
  $categories = get_categories( array(
    'number'     => 6,
    'orderby'    => 'count',
    'order'      => 'DESC',
    'hide_empty' => true,
  ) );
  if ( ! empty( $categories ) ) : ?>
    <div class="relative z-10 w-full max-w-lg mx-auto mt-8 text-center">
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
  $recent = new WP_Query( array( 'posts_per_page' => 3, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
  if ( $recent->have_posts() ) : ?>
    <div class="relative z-10 w-full max-w-3xl mx-auto mt-10">
      <h3 class="text-xs font-semibold tracking-widest uppercase text-muted-foreground text-center mb-4"><?php esc_html_e( 'Recent Articles', 'titancore' ); ?></h3>
      <div class="grid sm:grid-cols-3 gap-3 text-left">
        <?php while ( $recent->have_posts() ) : $recent->the_post();
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
</main>

<?php
get_footer();
