<?php
/**
 * Admin notices and dashboard functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Welcome Notice
 */
function titancore_welcome_notice() {
    // Only show to users who can switch themes, and only once after activation
    if ( ! current_user_can( 'switch_themes' ) ) {
        return;
    }

    $theme_version = wp_get_theme()->get( 'Version' );
    $dismissed = get_option( 'titancore_welcome_dismissed', false );

    if ( $dismissed === $theme_version ) {
        return;
    }

    $settings_url = admin_url( 'customize.php' );
    $menus_url = admin_url( 'nav-menus.php' );

    ?>
    <div class="notice notice-info is-dismissible" id="titancore-welcome" style="border-left: 4px solid #18181b; padding: 0; margin-top: 20px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="background: #18181b; color: #fff; padding: 20px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h2 style="margin: 0 0 5px 0; color: #fff; font-size: 20px; font-weight: 600;"><?php esc_html_e( 'Welcome to TitanCore Theme!', 'titancore' ); ?></h2>
                <p style="margin: 0; color: rgba(255,255,255,0.8); font-size: 14px;"><?php esc_html_e( 'Thanks for choosing this ultra-clean, minimal theme. You are ready to build something great.', 'titancore' ); ?></p>
            </div>
            <a href="https://wpineu.com" target="_blank" style="background: #fff; color: #18181b; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 14px; transition: opacity 0.2s;">
                WordPress Hosting in Europe &rarr;
            </a>
        </div>
        <div style="padding: 20px; background: #fff;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h3 style="margin-top: 0;">Quick Links</h3>
                    <ul style="list-style-type: disc; margin-left: 20px;">
                        <li><a href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Configure Customizer Settings', 'titancore' ); ?></a> (Sticky Header, Colors, Frontpage Layout)</li>
                        <li><a href="<?php echo esc_url( $menus_url ); ?>"><?php esc_html_e( 'Set up your Primary/Secondary Menus', 'titancore' ); ?></a></li>
                    </ul>
                </div>
                <div style="background: #f4f4f5; padding: 15px; border-radius: 5px;">
                    <h3 style="margin-top: 0; display: flex; align-items: center; gap: 8px; color: #18181b;">
                        <?php echo titancore_get_icon('sun', 'w-5 h-5'); ?> WordPress Hosting in Europe
                    </h3>
                    <p style="margin-bottom: 10px; font-size: 13px;">Looking for fast, reliable WordPress hosting optimized for themes like TitanCore? Check out <a href="https://wpineu.com" target="_blank" style="font-weight: 600;">WPinEU.com</a> — WordPress Hosting in Europe.</p>
                </div>
            </div>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $(document).on('click', '#titancore-welcome .notice-dismiss', function() {
            $.post(ajaxurl, {
                action: 'titancore_dismiss_welcome',
                nonce: '<?php echo wp_create_nonce("dismiss_welcome"); ?>'
            });
        });
    });
    </script>
    <?php
}
add_action( 'admin_notices', 'titancore_welcome_notice' );

function titancore_dismiss_welcome_callback() {
    check_ajax_referer( 'dismiss_welcome', 'nonce' );
    update_option( 'titancore_welcome_dismissed', wp_get_theme()->get( 'Version' ) );
    wp_die();
}
add_action( 'wp_ajax_titancore_dismiss_welcome', 'titancore_dismiss_welcome_callback' );
