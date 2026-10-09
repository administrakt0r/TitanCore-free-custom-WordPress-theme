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
        <?php get_template_part( 'template-parts/content', 'none' ); ?>
    <?php endif; ?>
  </div>
</main>

<?php
get_footer();
