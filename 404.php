<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header(); ?>

<main id="main-content" tabindex="-1" class="min-h-[70vh] bg-background flex flex-col items-center justify-center w-full z-10 relative px-6 py-16">
  <?php get_template_part( 'template-parts/background', 'grid' ); ?>
  <div class="text-center flex flex-col gap-5 max-w-md mx-auto relative z-10">
    <span class="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-full bg-muted text-muted-foreground border border-border"><?php echo titancore_get_icon('file-question', 'w-6 h-6'); ?></span>
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
  $recent = new WP_Query( array( 'posts_per_page' => 3, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
  if ( $recent->have_posts() ) : ?>
    <div class="relative z-10 w-full max-w-3xl mx-auto mt-12">
      <h3 class="text-sm font-semibold tracking-widest uppercase text-muted-foreground text-center mb-4"><?php esc_html_e( 'Recent articles', 'titancore' ); ?></h3>
      <div class="grid sm:grid-cols-3 gap-3 text-left">
        <?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
          <a href="<?php the_permalink(); ?>" class="group block rounded-lg border border-border bg-card p-4 hover:bg-accent transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
            <span class="block text-sm font-semibold leading-snug group-hover:text-foreground line-clamp-2"><?php the_title(); ?></span>
            <span class="block text-xs text-muted-foreground mt-1"><?php echo esc_html( get_the_date() ); ?></span>
          </a>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    </div>
  <?php endif; ?>
</main>

<?php
get_footer();
