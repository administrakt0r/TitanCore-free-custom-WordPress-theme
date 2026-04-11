<?php
/**
 * The template for displaying all single posts
 */

get_header();

while ( have_posts() ) :
	the_post();
    $posts_page_url = titancore_get_posts_page_url();
?>

<div class="min-h-screen bg-background relative">
  <?php get_template_part( 'template-parts/background', 'grid' ); ?>

  <div class="space-y-4 border-b border-border relative z-10">
    <div class="max-w-7xl mx-auto flex flex-col gap-6 p-6">
      <div class="flex flex-wrap items-center gap-3 gap-y-5 text-sm text-muted-foreground">
        
        <a href="<?php echo esc_url( $posts_page_url ); ?>" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-6 w-6">
            <?php echo titancore_get_icon('arrow-left', 'w-4 h-4'); ?>
            <span class="sr-only"><?php esc_html_e( 'Back to all articles', 'titancore' ); ?></span>
        </a>

        <?php
        $tags = get_the_tags();
        if ( $tags ) : ?>
          <div class="flex flex-wrap gap-3 text-muted-foreground">
            <?php foreach ( $tags as $tag ) : ?>
              <span class="h-6 w-fit px-3 text-sm font-medium bg-muted text-muted-foreground rounded-md border flex items-center justify-center">
                <?php echo esc_html( $tag->name ); ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <time class="font-medium text-muted-foreground" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
            <?php echo esc_html( get_the_date() ); ?>
        </time>
      </div>
      
      <?php if ( function_exists( 'titancore_breadcrumbs' ) ) titancore_breadcrumbs(); ?>
      
      <h1 class="text-4xl md:text-5xl lg:text-6xl font-medium tracking-tighter text-balance">
        <?php the_title(); ?>
      </h1>

      <?php if ( has_excerpt() ) : ?>
        <p class="text-muted-foreground max-w-4xl md:text-lg md:text-balance">
          <?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?>
        </p>
      <?php endif; ?>
    </div>
  </div>

  <div class="flex divide-x divide-border relative max-w-7xl mx-auto px-4 md:px-0 z-10">
    <div class="absolute max-w-7xl mx-auto left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] lg:w-full h-full border-x border-border p-0 pointer-events-none"></div>
    <main id="main-content" tabindex="-1" class="w-full p-0 overflow-hidden">
      <?php if ( has_post_thumbnail() ) : ?>
        <div class="relative w-full h-[500px] overflow-hidden object-cover border border-transparent">
          <?php the_post_thumbnail( 'full', array(
              'class' => 'object-cover w-full h-full absolute inset-0 text-transparent',
              'loading' => 'eager',
              'fetchpriority' => 'high',
              'decoding' => 'async',
              'sizes' => titancore_get_image_sizes( 'single-hero' ),
          ) ); ?>
        </div>
      <?php endif; ?>

      <div class="p-6 lg:p-10">
        <div class="prose dark:prose-invert max-w-none prose-headings:scroll-mt-8 prose-headings:font-semibold prose-a:no-underline prose-headings:tracking-tight prose-headings:text-balance prose-p:tracking-tight prose-p:text-balance prose-lg">
          <?php the_content(); ?>
        </div>
      </div>
      
      <!-- WP Comments Could Go Here -->
      <?php
        if ( comments_open() || get_comments_number() ) :
            echo '<div class="px-6 lg:px-10 pb-10">';
            comments_template();
            echo '</div>';
        endif;
      ?>
    </main>

    <aside class="hidden lg:block w-[350px] flex-shrink-0 p-6 lg:p-10 bg-muted/60 dark:bg-muted/20">
      <div class="sticky top-20 space-y-8">
        
        <!-- Author Card -->
        <div class="flex flex-col gap-4 border border-border rounded-lg p-6 bg-card">
          <div class="flex items-center gap-4">
            <?php echo get_avatar( get_the_author_meta( 'ID' ), 48, '', '', array( 'class' => 'rounded-full' ) ); ?>
            <div>
              <p class="font-medium"><?php echo esc_html( get_the_author() ); ?></p>
              <p class="text-sm text-muted-foreground"><?php esc_html_e( 'Author', 'titancore' ); ?></p>
            </div>
          </div>
          <p class="text-sm text-muted-foreground"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
        </div>

        <div class="border border-border rounded-lg p-6 bg-card">
          <h3 class="font-medium mb-4"><?php esc_html_e( 'Table of Contents', 'titancore' ); ?></h3>
          <?php
            if ( get_theme_mod( 'show_toc', true ) ) {
                titancore_generate_toc();
            } else {
                echo '<p class="text-sm text-muted-foreground italic">' . esc_html__( 'Table of Contents is disabled in Customizer.', 'titancore' ) . '</p>';
            }
          ?>
        </div>
      </div>
    </aside>
  </div>
</div>

<?php
if ( ! function_exists( 'titancore_has_external_seo_plugin' ) || ! titancore_has_external_seo_plugin() ) {
    // Output Article schema only when no dedicated SEO plugin is handling it.
    $article_description = has_excerpt()
        ? get_the_excerpt()
        : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '' );

    $article_schema = array(
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        'headline'         => wp_strip_all_tags( get_the_title() ),
        'description'      => wp_strip_all_tags( $article_description ),
        'url'              => esc_url_raw( get_permalink() ),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id'   => esc_url_raw( get_permalink() ),
        ),
        'datePublished'    => get_the_date( 'c' ),
        'dateModified'     => get_the_modified_date( 'c' ),
        'author'           => array(
            '@type' => 'Person',
            'name'  => wp_strip_all_tags( get_the_author() ),
        ),
    );
    if ( has_post_thumbnail() ) {
        $article_schema['image'] = esc_url_raw( get_the_post_thumbnail_url( get_the_ID(), 'full' ) );
    }
    echo '<script type="application/ld+json">' . wp_json_encode( $article_schema ) . '</script>';
}

endwhile; // End of the loop.

get_footer();
