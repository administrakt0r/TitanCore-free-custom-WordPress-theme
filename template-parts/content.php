<?php
/**
 * Template part for displaying posts
 */

$image_sizes = titancore_get_image_sizes( 'grid-card' );
global $wp_query;
$is_first_visible_card = ( $wp_query instanceof WP_Query ) && ( 0 === (int) $wp_query->current_post ) && ! is_paged();
$image_loading = $is_first_visible_card ? 'eager' : 'lazy';
$image_attributes = array(
    'class' => 'object-cover transition-transform duration-300 group-hover:scale-105 w-full h-full absolute inset-0',
    'loading' => $image_loading,
    'decoding' => 'async',
    'sizes' => $image_sizes,
);

if ( $is_first_visible_card ) {
    $image_attributes['fetchpriority'] = 'high';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'group block relative before:absolute before:-left-0.5 before:top-0 before:z-10 before:h-screen before:w-px before:bg-border before:content-[\'\'] after:absolute after:-top-0.5 after:left-0 after:z-0 after:h-px after:w-screen after:bg-border after:content-[\'\'] md:border-r border-border border-b-0' ); ?>>
    <a href="<?php echo esc_url( get_permalink() ); ?>" class="flex flex-col h-full">
        <?php if ( has_post_thumbnail() ) : ?>
        <div class="relative w-full h-48 overflow-hidden">
            <?php the_post_thumbnail( 'medium_large', $image_attributes ); ?>
        </div>
        <?php endif; ?>

        <div class="p-6 flex flex-col gap-2">
            <h3 class="text-xl font-semibold text-card-foreground group-hover:underline underline-offset-4">
                <?php the_title(); ?>
            </h3>
            <div class="text-muted-foreground text-sm">
                <?php 
                if ( has_excerpt() ) {
                    echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 20 ) );
                } else {
                    echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), 20 ) );
                }
                ?>
            </div>
            <time class="block text-sm font-medium text-muted-foreground" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                <?php echo esc_html( get_the_date() ); ?>
            </time>
        </div>
    </a>
</article>
