<?php
/**
 * The template for displaying comments
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area mt-10">

	<?php
	if ( have_comments() ) :
		?>
		<h2 class="comments-title text-2xl font-semibold mb-6">
			<?php
			$titancore_comment_count = get_comments_number();
			if ( '1' === $titancore_comment_count ) {
				printf(
					/* translators: 1: title. */
					esc_html__( 'One thought on &ldquo;%1$s&rdquo;', 'titancore' ),
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			} else {
				printf(
					/* translators: 1: comment count number, 2: title. */
					esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $titancore_comment_count, 'comments title', 'titancore' ) ),
					number_format_i18n( $titancore_comment_count ),
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			}
			?>
		</h2>

		<ol class="comment-list space-y-6">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
                    'class'       => 'border-b border-border pb-6',
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation( array(
		    'prev_text' => __( 'Older Comments', 'titancore' ),
		    'next_text' => __( 'Newer Comments', 'titancore' ),
		));

		if ( ! comments_open() ) :
			?>
			<p class="no-comments mt-6 text-muted-foreground italic"><?php esc_html_e( 'Comments are closed.', 'titancore' ); ?></p>
			<?php
		endif;

	endif;

    $req = get_option( 'require_name_email' );
    $commenter = wp_get_current_commenter();

    $comment_args = array(
        'class_submit'  => 'inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 mt-4',
        'class_form'    => 'comment-form flex flex-col space-y-4 mt-8',
        'title_reply_class' => 'text-xl font-semibold',
        'comment_field' => '<p class="comment-form-comment flex flex-col"><label for="comment" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 mb-2">' . _x( 'Comment', 'noun', 'titancore' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"></textarea></p>',
        'fields' => array(
            'author' => '<p class="comment-form-author flex flex-col"><label for="author" class="text-sm font-medium mb-2">' . __( 'Name', 'titancore' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" maxlength="245" autocomplete="name" required="required" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" /></p>',
            'email'  => '<p class="comment-form-email flex flex-col"><label for="email" class="text-sm font-medium mb-2">' . __( 'Email', 'titancore' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label><input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" maxlength="100" autocomplete="email" required="required" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" /></p>',
            'url'    => '<p class="comment-form-url flex flex-col"><label for="url" class="text-sm font-medium mb-2">' . __( 'Website', 'titancore' ) . '</label><input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" maxlength="200" autocomplete="url" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" /></p>',
        )
    );

	comment_form( $comment_args );
	?>

</div><!-- #comments -->
