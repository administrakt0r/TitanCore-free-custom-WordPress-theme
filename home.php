<?php
/**
 * The home template file (Blog Index)
 */

get_header(); ?>

<main id="main-content" tabindex="-1" class="min-h-screen bg-background relative">
  <?php get_template_part( 'template-parts/background', 'grid' ); ?>
  <?php get_template_part( 'template-parts/front', 'header' ); ?>

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
