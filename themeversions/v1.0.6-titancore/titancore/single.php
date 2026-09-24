<?php
/**
 * The template for displaying all single posts
 */

get_header();

while ( have_posts() ) :
	the_post();
    $posts_page_url = titancore_get_posts_page_url();
    $categories = get_the_category();
    $reading_time = function_exists( 'titancore_get_estimated_reading_time' ) ? titancore_get_estimated_reading_time() : 1;
    $author_description = get_the_author_meta( 'description' );
    $related_ids = function_exists( 'titancore_get_related_posts' ) ? titancore_get_related_posts( get_the_ID(), 3 ) : array();
?>

<div class="tc-single min-h-screen bg-background relative">
  <?php get_template_part( 'template-parts/background', 'grid' ); ?>

  <div class="tc-single__header space-y-4 border-b border-border relative z-10">
    <div class="max-w-7xl mx-auto flex flex-col gap-6 p-6">
      <div class="flex flex-wrap items-center gap-2.5 gap-y-3 text-sm text-muted-foreground">

        <a href="<?php echo esc_url( $posts_page_url ); ?>" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 w-8 shrink-0" aria-label="<?php esc_attr_e( 'Back to all articles', 'titancore' ); ?>">
            <?php echo titancore_get_icon('arrow-left', 'w-4 h-4'); ?>
            <span class="sr-only"><?php esc_html_e( 'Back to all articles', 'titancore' ); ?></span>
        </a>

        <?php if ( ! empty( $categories ) ) : ?>
          <div class="tc-single__terms">
            <?php foreach ( array_slice( $categories, 0, 3 ) as $category ) : ?>
              <a class="tc-single__term" href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
                <?php echo esc_html( $category->name ); ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php
        $tags = get_the_tags();
        if ( $tags ) : ?>
          <div class="flex flex-wrap gap-2">
            <?php foreach ( array_slice( $tags, 0, 6 ) as $tag ) : ?>
              <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="inline-flex h-7 items-center rounded-full border bg-muted px-3 text-xs font-semibold text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                #<?php echo esc_html( $tag->name ); ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <span class="inline-flex items-center gap-2 font-medium">
            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
            <span aria-hidden="true" class="h-1 w-1 rounded-full bg-border"></span>
            <span><?php printf( esc_html__( '%s min read', 'titancore' ), esc_html( number_format_i18n( $reading_time ) ) ); ?></span>
        </span>
      </div>

      <?php if ( function_exists( 'titancore_breadcrumbs' ) ) titancore_breadcrumbs(); ?>

      <h1 class="tc-single__title text-4xl md:text-5xl lg:text-6xl font-medium tracking-tighter text-balance">
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
    <main id="main-content" tabindex="-1" class="tc-single__main w-full p-0 overflow-hidden">
      <?php if ( has_post_thumbnail() ) : ?>
        <div class="tc-single__featured relative w-full overflow-hidden border-y border-border/60 max-h-[34rem] aspect-[16/9] sm:aspect-[16/9]">
          <?php the_post_thumbnail( 'titancore-hero', array(
              'class' => 'object-cover w-full h-full absolute inset-0 text-transparent',
              'loading' => 'eager',
              'fetchpriority' => 'high',
              'decoding' => 'async',
              'sizes' => titancore_get_image_sizes( 'single-hero' ),
          ) ); ?>
        </div>
      <?php endif; ?>

      <?php if ( get_theme_mod( 'show_toc', true ) ) : ?>
      <div class="lg:hidden mx-6 mt-6 rounded-lg border border-border bg-card p-4">
        <details class="group">
          <summary class="flex cursor-pointer list-none items-center justify-between font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring rounded-md px-1 py-1 -mx-1">
            <span class="flex items-center gap-2"><?php echo titancore_get_icon('list-tree', 'w-4 h-4'); ?><?php esc_html_e( 'On this page', 'titancore' ); ?></span>
            <span class="text-muted-foreground transition-transform group-open:rotate-180"><?php echo titancore_get_icon('chevron-down', 'w-4 h-4'); ?></span>
          </summary>
          <div class="mt-3 border-t border-border pt-3 text-sm">
            <?php titancore_generate_toc(); ?>
          </div>
        </details>
      </div>
      <?php endif; ?>

      <div class="p-6 lg:p-10">
        <div class="tc-single__content prose dark:prose-invert max-w-none prose-headings:scroll-mt-8 prose-headings:font-semibold prose-a:no-underline prose-headings:tracking-tight prose-headings:text-balance prose-p:tracking-tight prose-p:text-balance prose-lg">
          <?php the_content(); ?>
        </div>

        <?php if ( false !== strpos( get_post_field( 'post_content', get_the_ID() ), '<!--nextpage-->' ) || get_previous_post() || get_next_post() ) : ?>
        <div class="tc-single__after-content">
          <?php
          wp_link_pages(
              array(
                  'before' => '<div class="page-links">',
                  'after'  => '</div>',
              )
          );

          the_post_navigation(
              array(
                  'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous', 'titancore' ) . '</span><span class="nav-title">%title</span>',
                  'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next', 'titancore' ) . '</span><span class="nav-title">%title</span>',
              )
          );
          ?>
        </div>
        <?php endif; ?>
      </div>

      <?php if ( ! empty( $related_ids ) ) : ?>
      <div class="tc-single__related border-t border-border px-6 lg:px-10 py-10 bg-card/40">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-semibold tracking-tight text-foreground"><?php esc_html_e( 'Related Articles', 'titancore' ); ?></h2>
          <a href="<?php echo esc_url( $posts_page_url ); ?>" class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-muted-foreground hover:text-foreground transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring rounded-sm px-1">
            <?php esc_html_e( 'View All', 'titancore' ); ?>
            <?php echo titancore_get_icon( 'chevron-right', 'w-3.5 h-3.5 ml-1' ); ?>
          </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php foreach ( $related_ids as $rel_id ) :
            $rel_reading_time = function_exists( 'titancore_get_estimated_reading_time' ) ? titancore_get_estimated_reading_time( $rel_id ) : 1;
            $rel_cats         = get_the_category( $rel_id );
            $rel_thumb        = get_the_post_thumbnail( $rel_id, 'titancore-card', array(
              'class'   => 'object-cover w-full h-full group-hover:scale-105 transition-transform duration-300',
              'loading' => 'lazy',
            ) );
          ?>
            <article class="tc-post-card group relative flex flex-col rounded-xl border border-border bg-card p-4 transition-all hover:border-foreground/40 hover:shadow-md focus-within:ring-2 focus-within:ring-ring">
              <?php if ( $rel_thumb ) : ?>
                <div class="aspect-[16/9] w-full overflow-hidden rounded-lg bg-muted mb-3.5 relative">
                  <?php echo $rel_thumb; ?>
                </div>
              <?php endif; ?>
              <div class="flex flex-col flex-grow justify-between space-y-3">
                <div class="space-y-1.5">
                  <?php if ( ! empty( $rel_cats ) ) : ?>
                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block">
                      <?php echo esc_html( $rel_cats[0]->name ); ?>
                    </span>
                  <?php endif; ?>
                  <h3 class="text-base font-semibold leading-snug tracking-tight text-foreground group-hover:text-primary transition-colors line-clamp-2">
                    <a href="<?php echo esc_url( get_permalink( $rel_id ) ); ?>" class="focus-visible:outline-none after:absolute after:inset-0">
                      <?php echo esc_html( get_the_title( $rel_id ) ); ?>
                    </a>
                  </h3>
                </div>
                <div class="flex items-center justify-between text-xs text-muted-foreground pt-2 border-t border-border/50">
                  <time datetime="<?php echo esc_attr( get_the_date( 'c', $rel_id ) ); ?>"><?php echo esc_html( get_the_date( '', $rel_id ) ); ?></time>
                  <span><?php printf( esc_html__( '%s min read', 'titancore' ), esc_html( number_format_i18n( $rel_reading_time ) ) ); ?></span>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

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

        <div class="tc-single__sidebar-card flex flex-col gap-4 border border-border rounded-lg p-6 bg-card">
          <div class="flex items-center gap-4">
            <?php echo get_avatar( get_the_author_meta( 'ID' ), 48, '', '', array( 'class' => 'rounded-full' ) ); ?>
            <div>
              <p class="font-medium"><?php echo esc_html( get_the_author() ); ?></p>
              <p class="text-sm text-muted-foreground"><?php esc_html_e( 'Author', 'titancore' ); ?></p>
            </div>
          </div>
          <?php if ( '' !== trim( $author_description ) ) : ?>
            <p class="text-sm text-muted-foreground"><?php echo esc_html( $author_description ); ?></p>
          <?php endif; ?>
        </div>

        <div class="tc-single__sidebar-card border border-border rounded-lg p-6 bg-card">
          <h3 class="font-medium mb-4"><?php esc_html_e( 'Table of Contents', 'titancore' ); ?></h3>
          <?php
            if ( get_theme_mod( 'show_toc', true ) ) {
                titancore_generate_toc();
            } else {
                echo '<p class="text-sm text-muted-foreground italic">' . esc_html__( 'Table of Contents is disabled in Customizer.', 'titancore' ) . '</p>';
            }
          ?>
        </div>

        <?php if ( ! empty( $related_ids ) ) : ?>
        <div class="tc-single__sidebar-card tc-single__sidebar-related border border-border rounded-lg p-6 bg-card space-y-4">
          <h3 class="font-medium text-base text-foreground tracking-tight flex items-center justify-between">
            <span><?php esc_html_e( 'Related Reading', 'titancore' ); ?></span>
            <span class="text-xs text-muted-foreground font-normal"><?php echo esc_html( count( $related_ids ) ); ?></span>
          </h3>
          <ul class="space-y-3.5 divide-y divide-border/60 -mb-1">
            <?php foreach ( $related_ids as $rel_id ) :
              $rel_reading_time = function_exists( 'titancore_get_estimated_reading_time' ) ? titancore_get_estimated_reading_time( $rel_id ) : 1;
              $rel_cats         = get_the_category( $rel_id );
            ?>
              <li class="pt-3.5 first:pt-0 space-y-1">
                <?php if ( ! empty( $rel_cats ) ) : ?>
                  <span class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground block">
                    <?php echo esc_html( $rel_cats[0]->name ); ?>
                  </span>
                <?php endif; ?>
                <a href="<?php echo esc_url( get_permalink( $rel_id ) ); ?>" class="text-sm font-medium text-foreground hover:text-primary transition-colors line-clamp-2 leading-snug block focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring rounded-sm">
                  <?php echo esc_html( get_the_title( $rel_id ) ); ?>
                </a>
                <span class="text-xs text-muted-foreground block pt-0.5">
                  <?php printf( esc_html__( '%s min read', 'titancore' ), esc_html( number_format_i18n( $rel_reading_time ) ) ); ?>
                </span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
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
