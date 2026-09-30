<?php
/**
 * Template part for displaying a unified post grid and pagination
 */

?>
<div class="tc-post-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 relative overflow-hidden border-x border-border border-b">
    <?php
    while ( have_posts() ) :
        the_post();
        get_template_part( 'template-parts/content', get_post_type() );
    endwhile;
    ?>
</div>

<div class="py-10">
    <?php
    the_posts_pagination( array(
        'mid_size'  => 2,
        'prev_text' => titancore_get_icon('arrow-left', 'w-4 h-4 mr-2') . __( 'Previous', 'titancore' ),
        'next_text' => __( 'Next', 'titancore' ) . titancore_get_icon('chevron-right', 'w-4 h-4 ml-2'),
    ) );
    ?>
</div>
