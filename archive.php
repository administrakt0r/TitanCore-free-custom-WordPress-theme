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
        <div class="py-20 text-center flex flex-col items-center justify-center space-y-4">
            <h2 class="text-2xl font-medium tracking-tighter"><?php esc_html_e( 'No posts found', 'titancore' ); ?></h2>
            <p class="text-muted-foreground"><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for.', 'titancore' ); ?></p>
        </div>
    <?php endif; ?>
  </div>
</main>

<?php
get_footer();
