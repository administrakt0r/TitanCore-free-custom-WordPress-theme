<?php
/**
 * Custom search form — accessible, 44px tap targets, i18n, consistent with enhancements.css
 *
 * Uses titancore text domain and reuses tc- icon helper for the submit button.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titancore_search_id    = 'search-field-' . esc_attr( uniqid() );
$titancore_search_query = get_search_query();
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Site search', 'titancore' ); ?>">
	<label for="<?php echo esc_attr( $titancore_search_id ); ?>">
		<span class="sr-only"><?php esc_html_e( 'Search', 'titancore' ); ?></span>
		<input type="search" id="<?php echo esc_attr( $titancore_search_id ); ?>" name="s" value="<?php echo esc_attr( $titancore_search_query ); ?>" placeholder="<?php esc_attr_e( 'Search articles...', 'titancore' ); ?>" required autocomplete="off" />
	</label>
	<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'titancore' ); ?>">
		<span aria-hidden="true" class="inline-flex items-center gap-1">
			<?php echo titancore_get_icon( 'search', 'w-4 h-4' ); ?>
			<span><?php esc_html_e( 'Search', 'titancore' ); ?></span>
		</span>
	</button>
</form>
