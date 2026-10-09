<?php
/**
 * The template for displaying search results pages
 */

get_header(); ?>

<main id="main-content" tabindex="-1" class="min-h-screen bg-background relative">
  <?php get_template_part( 'template-parts/background', 'grid' ); ?>

  <div class="p-6 border-b border-border flex flex-col gap-6 min-h-[250px] justify-center relative z-10">
    <div class="max-w-7xl mx-auto w-full">
      <div class="flex flex-col gap-2">
        <h1 class="font-medium text-3xl md:text-4xl tracking-tighter">
          <?php
          /* translators: %s: search query. */
          printf( esc_html__( 'Search Results for: %s', 'titancore' ), '<span>' . esc_html( get_search_query( false ) ) . '</span>' );
          ?>
        </h1>
      </div>
    </div>
  </div>

  <div class="max-w-7xl mx-auto w-full px-6 lg:px-0">
    <?php if ( have_posts() ) : ?>
        <?php get_template_part( 'template-parts/loop', 'container' ); ?>
    <?php else : ?>
        <div class="py-16 text-center flex flex-col items-center justify-center space-y-4 border-x border-b border-border bg-card/50 px-6">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-muted border border-border text-muted-foreground"><?php echo titancore_get_icon('search-x', 'w-5 h-5'); ?></span>
            <h2 class="text-2xl font-medium tracking-tighter"><?php esc_html_e( 'No results found', 'titancore' ); ?></h2>
            <p class="text-muted-foreground max-w-md text-balance"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Try a broader query or browse categories.', 'titancore' ); ?></p>
            <div class="w-full max-w-sm pt-1"><?php get_search_form(); ?></div>
            <div class="flex flex-wrap gap-2 justify-center pt-1">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex h-10 items-center rounded-lg border border-border bg-background px-4 text-sm font-medium hover:bg-accent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"><?php esc_html_e( 'Browse home', 'titancore' ); ?></a>
                <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="inline-flex h-10 items-center rounded-lg bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"><?php esc_html_e( 'Latest articles', 'titancore' ); ?></a>
            </div>
        </div>
    <?php endif; ?>
  </div>
</main>

<?php
get_footer();
