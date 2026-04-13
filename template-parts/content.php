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

$categories = get_the_category();
$reading_time = function_exists( 'titancore_get_estimated_reading_time' ) ? titancore_get_estimated_reading_time() : 1;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'tc-post-card group block relative before:absolute before:-left-0.5 before:top-0 before:z-10 before:h-screen before:w-px before:bg-border before:content-[\'\'] after:absolute after:-top-0.5 after:left-0 after:z-0 after:h-px after:w-screen after:bg-border after:content-[\'\'] md:border-r border-border border-b-0' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
    <a href="<?php echo esc_url( get_permalink() ); ?>" class="tc-post-card__media" aria-label="<?php echo esc_attr( sprintf( __( 'Read %s', 'titancore' ), wp_strip_all_tags( get_the_title() ) ) ); ?>">
        <?php the_post_thumbnail( 'titancore-card', $image_attributes ); ?>
    </a>
    <?php endif; ?>

    <div class="tc-post-card__body">
        <div class="tc-post-card__meta">
            <?php if ( ! empty( $categories ) ) : ?>
                <div class="tc-post-card__terms" aria-label="<?php esc_attr_e( 'Categories', 'titancore' ); ?>">
                    <?php foreach ( array_slice( $categories, 0, 2 ) as $category ) : ?>
                        <a class="tc-post-card__term" href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
                            <?php echo esc_html( $category->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                <?php echo esc_html( get_the_date() ); ?>
            </time>
        </div>

        <h2 class="tc-post-card__title">
            <a href="<?php echo esc_url( get_permalink() ); ?>">
                <?php the_title(); ?>
            </a>
        </h2>

        <p class="tc-post-card__excerpt">
            <?php
            if ( has_excerpt() ) {
                echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 24 ) );
            } else {
                echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), 24 ) );
            }
            ?>
        </p>

        <div class="tc-post-card__footer">
            <span>
                <?php
                printf(
                    /* translators: %s: estimated reading time in minutes. */
                    esc_html__( '%s min read', 'titancore' ),
                    esc_html( number_format_i18n( $reading_time ) )
                );
                ?>
            </span>
            <a href="<?php echo esc_url( get_permalink() ); ?>" class="tc-post-card__read-more">
                <?php esc_html_e( 'Read article', 'titancore' ); ?>
                <?php echo titancore_get_icon( 'chevron-right', 'w-4 h-4' ); ?>
            </a>
        </div>
    </div>
</article>
