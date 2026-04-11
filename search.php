<?php
/**
 * The template for displaying search results pages
 */

get_header(); ?>

<main id="main-content" tabindex="-1" class="min-h-screen bg-background relative">
  <div class="absolute top-0 left-0 z-0 w-full h-[200px] [mask-image:linear-gradient(to_top,transparent_25%,black_95%)]">
    <div class="absolute top-0 left-0 size-full" style="background-image: radial-gradient(#6B7280 1px, transparent 1px); background-size: 10px 10px; opacity: 0.2;"></div>
  </div>

  <div class="p-6 border-b border-border flex flex-col gap-6 min-h-[200px] justify-center relative z-10">
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
        <div class="py-20 text-center flex flex-col items-center justify-center space-y-4 border-x border-b border-border">
            <h2 class="text-2xl font-medium tracking-tighter"><?php esc_html_e( 'No results found', 'titancore' ); ?></h2>
            <p class="text-muted-foreground"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'titancore' ); ?></p>
            <?php get_search_form(); ?>
        </div>
    <?php endif; ?>
  </div>
</main>

<?php
get_footer();
