<?php
/**
 * The template for displaying all pages
 */

get_header();

while ( have_posts() ) :
	the_post();
?>

<div class="min-h-screen bg-background relative">
  <?php get_template_part( 'template-parts/background', 'grid' ); ?>

  <div class="space-y-4 border-b border-border relative z-10">
    <div class="max-w-7xl mx-auto flex flex-col gap-6 p-6">
      <?php if ( function_exists( 'titancore_breadcrumbs' ) ) titancore_breadcrumbs(); ?>
      <h1 class="text-4xl md:text-5xl lg:text-5xl font-medium tracking-tighter text-balance mt-4">
        <?php the_title(); ?>
      </h1>
    </div>
  </div>

  <div class="flex divide-x divide-border relative max-w-7xl mx-auto px-4 md:px-0 z-10">
    <div class="absolute max-w-7xl mx-auto left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] lg:w-full h-full border-x border-border p-0 pointer-events-none"></div>
    <main id="main-content" tabindex="-1" class="w-full p-0 overflow-hidden">
      <?php if ( has_post_thumbnail() ) : ?>
        <div class="relative w-full h-[400px] overflow-hidden object-cover border border-transparent">
          <?php the_post_thumbnail( 'full', array(
              'class' => 'object-cover w-full h-full absolute inset-0 text-transparent',
              'loading' => 'eager',
              'fetchpriority' => 'high',
              'decoding' => 'async',
              'sizes' => titancore_get_image_sizes( 'page-hero' ),
          ) ); ?>
        </div>
      <?php endif; ?>

      <div class="p-6 lg:p-10">
        <div class="prose dark:prose-invert max-w-none prose-headings:scroll-mt-8 prose-headings:font-semibold prose-a:no-underline prose-headings:tracking-tight prose-headings:text-balance prose-p:tracking-tight prose-p:text-balance prose-lg">
          <?php the_content(); ?>
        </div>
      </div>
      
      <?php
        if ( comments_open() || get_comments_number() ) :
            echo '<div class="px-6 lg:px-10 pb-10">';
            comments_template();
            echo '</div>';
        endif;
      ?>
    </main>
  </div>
</div>

<?php
endwhile;

get_footer();
