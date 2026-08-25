<?php
/**
 * The template for displaying archive pages
 */

get_header(); ?>

<main id="main-content" tabindex="-1" class="min-h-screen bg-background relative">
  <?php get_template_part( 'template-parts/background', 'grid' ); ?>

  <div class="p-6 border-b border-border flex flex-col gap-6 min-h-[250px] justify-center relative z-10">
    <div class="max-w-7xl mx-auto w-full">
      <div class="flex flex-col gap-2">
        <h1 class="font-medium text-4xl md:text-5xl tracking-tighter">
          <?php the_archive_title(); ?>
        </h1>
        <div class="text-muted-foreground text-sm md:text-base lg:text-lg">
          <?php the_archive_description(); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="max-w-7xl mx-auto w-full px-6 lg:px-0">
    <?php if ( have_posts() ) : ?>
        <?php get_template_part( 'template-parts/loop', 'container' ); ?>
    <?php else : ?>
        <div class="py-16 text-center flex flex-col items-center justify-center space-y-4 px-6 border-x border-b border-border bg-card/50">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-muted border border-border text-muted-foreground"><?php echo titancore_get_icon('inbox', 'w-5 h-5'); ?></span>
            <h2 class="text-2xl font-medium tracking-tighter"><?php esc_html_e( 'No posts found', 'titancore' ); ?></h2>
            <p class="text-muted-foreground max-w-md text-balance"><?php esc_html_e( 'It seems we can\'t find what you\'re looking for. Try a search or browse recent articles.', 'titancore' ); ?></p>
            <div class="w-full max-w-sm pt-2"><?php get_search_form(); ?></div>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex h-11 items-center rounded-lg bg-primary px-6 text-sm font-medium text-primary-foreground hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"><?php esc_html_e( 'Browse all articles', 'titancore' ); ?></a>
        </div>
    <?php endif; ?>
  </div>
</main>

<?php
get_footer();
