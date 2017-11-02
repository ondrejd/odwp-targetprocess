<?php
/**
 * Plugin Name: odwp-targetprocess
 * Plugin URI: https://github.com/ondrejd/odwp-targetprocess
 * Description: Plugin that uses <a href="https://www.targetprocess.com/" target="blank">Targetprocess API</a> to publish <em>user stories</em> on your site.
 * Version: 0.3
 * Author: Ondrej Donek
 * Author URI: https://ondrejd.com/
 * License: GPLv3
 * Requires at least: 4.7
 * Tested up to: 4.8.1
 * Tags: debug,log,development
 * Donate link: https://www.paypal.me/ondrejd
 *
 * Text Domain: odwptp
 * Domain Path: /languages/
 *
 * @author Ondrej Donek <ondrejd@gmail.com>
 * @link https://github.com/ondrejd/odwp-targetprocess for the canonical source repository
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-targetprocess
 * @since 0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'odwptp_settings_init' ) ) :
    /**
     * Initialize settings.
     * @return void
     * @since 0.1
     */
    function odwptp_settings_init() {
        register_setting( 'general', 'odwptp_login', ['type', 'string'] );
        register_setting( 'general', 'odwptp_password', ['type', 'string'] );
        register_setting( 'general', 'odwptp_url', ['type', 'string'] );

        add_settings_section(
            'odwptp_settings_section',
            sprintf(
                __( 'Přihlašovací údaje k Targetprocess %s', 'odwptp' ),
                '<img src="' . plugins_url( 'assets/img/targetprocess-32x32.png', __FILE__ ) . '" style="height: 1.6em; position: relative; top: 0.5em; width: 1.6em;">'
            ),
            'odwptp_settings_section_cb',
            'general'
        );

        add_settings_field(
            'odwptp_settings_login_field',
            __( 'Jméno/email', 'odwptp' ),
            'odwptp_settings_login_field_cb',
            'general',
            'odwptp_settings_section'
        );

        add_settings_field(
            'odwptp_settings_password_field',
            __( 'Heslo', 'odwptp' ),
            'odwptp_settings_password_field_cb',
            'general',
            'odwptp_settings_section'
        );

        add_settings_field(
            'odwptp_settings_url_field',
            __( 'URL', 'odwptp' ),
            'odwptp_settings_url_field_cb',
            'general',
            'odwptp_settings_section'
        );
    }
endif;

add_action( 'admin_init', 'odwptp_settings_init' );


if ( ! function_exists( 'odwptp_settings_section_cb' ) ) :
    /**
     * @internal Renders settings section.
     * @return void
     * @since 0.1
     */
    function odwptp_settings_section_cb() {
        printf(
            __( '%sZadejte své přihlašovací údaje ke službě %sTargetprocess%s.%s', 'odwptp' ),
            '<p id="odwptp-settings">',
            '<a href="https://www.targetprocess.com/" target="_blank">',
            '</a>',
            '</p>'
        );
?>
<p><?php  ?></p>
<?php
    }
endif;


if ( ! function_exists( 'odwptp_settings_login_field_cb' ) ) :
    /**
     * @internal Renders input field for "login" setting.
     * @return void
     * @since 0.1
     */
    function odwptp_settings_login_field_cb() {
        $val = get_option( 'odwptp_login' );
?>
<input class="regular-text" name="odwptp_login" type="text" value="<?= isset( $val ) ? esc_attr( $val ) : '' ?>">
<?php
    }
endif;


if ( ! function_exists( 'odwptp_settings_password_field_cb' ) ) :
    /**
     * @internal Renders input field for "password" setting.
     * @return void
     * @since 0.1
     */
    function odwptp_settings_password_field_cb() {
        $val = get_option( 'odwptp_password' );
?>
<input class="regular-text code" name="odwptp_password" type="text" value="<?= isset( $val ) ? esc_attr( $val ) : '' ?>">
<?php
    }
endif;


if ( ! function_exists( 'odwptp_settings_url_field_cb' ) ) :
    /**
     * @internal Renders input field for "url" setting.
     * @return void
     * @since 0.1
     */
    function odwptp_settings_url_field_cb() {
        $val = get_option( 'odwptp_url' );
?>
<input class="regular-text code" name="odwptp_url" placeholder="<?php _e( 'https://[yourdomain].tpondemand.com', 'odwptp' ) ?>" type="url" value="<?= isset( $val ) ? esc_attr( $val ) : '' ?>">
<p class="description"><?php printf( __( 'Zadejte URL serveru, který představuje váš přístupový bod k %sTargetprocess API%s.', 'odwptp' ), '<a href="#" target="_blank">', '</a>' ) ?></p>
<?php
    }
endif;


if ( ! function_exists( 'odwptp_check_connection_success_msg' ) ) :
    /**
     * Checks if user wants to hide connection success message forever.
     * @return void
     * @since 0.2
     */
    function odwptp_check_connection_success_msg() {
        if ( isset( $_GET['disable_odwptp_success_msg'] ) ) {
            add_option( 'odwptp_show_connection_success', 0 );
            update_option( 'odwptp_show_connection_success', 0 );
        }
    }
endif;

add_action( 'admin_init', 'odwptp_check_connection_success_msg' );


if ( ! function_exists( 'odwptp_print_admin_notice' ) ) :
    /**
     * Prints WP admin notice.
     * @param string $message
     * @param string $type (Optional.) Possible values: ["error", "warning", "success", "info"]. Defaultly "info".
     * @param boolean $dismissible (Optional.) Defaultly `TRUE`.
     * @since 0.2
     */
    function odwptp_print_admin_notice( $message, $type = 'info', $dismissible = true ) {
        $classes = 'notice';

        if ( ! in_array( $type, ['error', 'warning', 'success', 'info'] ) ) {
            $type = 'info';
        }

        $classes .= ' notice-' . $type;

        if ( $dismissible === true ) {
            $classes .= ' is-dismissible';
        }

        // XXX Use `wp_kses` to escape `$message`?
        printf( '<div class="%s"><p>%s</p></div>', $classes, $message );
    }
endif;


if ( ! function_exists( 'odwptp_call_targetprocess' ) ) :
    /**
     * Makes call to Targetprocess API.
     * @param string $call
     * @return void
     * @since 0.3
     */
    function odwptp_call_targetprocess( $call ) {
        $login = get_option( 'odwptp_login' );
        $password = get_option( 'odwptp_password' );
        $url = get_option( 'odwptp_url' );

        // Credentials are not set
        if( empty( $login ) || empty( $password ) || empty( $url ) ) {
            add_action( 'admin_notices', function() {
                odwptp_print_admin_notice( sprintf(
                    __( 'Pro správné použití pluginu <strong>odwp-targetprocess</strong> musíte nastavit přístupové údaje ke službě <strong>Targetprocess</strong> - viz. <a href="%s">Nastavení &gt; Obecné</a>.', 'odwptp' ),
                    admin_url( 'options-general.php#odwptp-settings' )
                ), 'warning' );
            } );
            return;
        }

        // Check the credentials
        $url = rtrim( $url, '/' ) . $call;
        $ret = wp_remote_request( $url, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode( $login . ':' . $password ),
                'Accept' => 'application/json',
            ],
        ] );

        return $ret;
    }
endif;


if ( ! function_exists( 'odwptp_check_credentials' ) ) :
    /**
     * Checks Targetprocess credentials.
     * @return void
     * @since 0.2
     */
    function odwptp_check_credentials() {
        $ret = odwptp_call_targetprocess( '/api/v1/Context/' );

        // Control request did not end well
        if ( ( $ret instanceof WP_Error ) ) {
            add_action( 'admin_notices', function() {
                odwptp_print_admin_notice( sprintf(
                    __( 'Při dotazu na server  <a href="%s" target="_blank">Targetprocess</a> nastala neznámá chyba - ujistěte se, že máte <a href="%s">nastavení</a> pluginu v pořádku.', 'odwptp' ),
                    'https://www.targetprocess.com/',
                    admin_url( 'options-general.php#odwptp-settings' )
                ), 'error' );
            }, 99 );
            return;
        }

        // Check if the response is correct
        if ( $ret['response']['code'] == 200 ) {
            // Make this hidden by user preference (so show it just once if user wants it).
            $show = (bool) get_option( 'odwptp_show_connection_success', true );

            if ( $show === false ) {
                return;
            }

            add_action( 'admin_notices', function() {
                try {
                    $screen = get_current_screen();
                    $current_url = admin_url( $screen->parent_file ) . '?disable_odwptp_success_msg=1';
                } catch ( Exception $e ) {
                    $current_url = admin_url( '?disable_odwptp_success_msg=1' );
                }

                odwptp_print_admin_notice( sprintf(
                    __( 'Údaje pro připojení se k Vašemu <a href="%s" target="_blank">Targetprocess</a> účtu jsou správné, nyní můžete umístit <em>targetprocess_shortcode</em> do Vašich příspěvků či stránek&hellip;%s', 'odwptp' ),
                    'https://www.targetprocess.com/',
                    '<br><br><a href="' . $current_url  . '">' . __( 'Nezobrazovat již tuto zprávu', 'odwptp' ) . '</a>'
                ), 'success' );
            } );
            return;
        }

        // Otherwise inform the user
        add_action( 'admin_notices', function() {
            odwptp_print_admin_notice( sprintf(
                __( 'Zdá se, že nastavení pluginu <strong>odwp-targetprocess</strong> neodpovídá Vašemu <a href="%s" target="_blank">Targetprocess</a> účtu - ujistěte se, že máte <a href="%s">nastavení</a> v pořádku.', 'odwptp' ),
                'https://www.targetprocess.com/',
                admin_url( 'options-general.php#odwptp-settings' )
            ), 'error' );
        } );
    }
endif;

add_action( 'admin_init', 'odwptp_check_credentials' );


if ( ! function_exists( 'odwptp_check_wp_http_block_external' ) ) :
    /**
     * Checks if constant WP_HTTP_BLOCK_EXTERNAL isn't set `true`
     * in `wp-config.php` file because it will block our requests.
     * @return void
     * @since 0.2
     */
    function odwptp_check_wp_http_block_external() {
        if ( ! defined( 'WP_HTTP_BLOCK_EXTERNAL' ) ) {
            return; // Everything is OK
        }

        if ( WP_HTTP_BLOCK_EXTERNAL !== true ) {
            return; // Same as before - OK
        }

        // Now we just display notice to user and leave it on him
        add_action( 'admin_notices', function() {
            odwptp_print_admin_notice( sprintf(
                __( 'Vaše nastavení v souboru <code>wp-config.php</code> může zapříčinit nefunkčnost pluginu <strong>odwp-targetprocess</strong>! Je třeba upravit nastavení hodnot konstant <code>WP_HTTP_BLOCK_EXTERNAL</code> a <code>WP_ACCESSIBLE_HOSTS</code> - více viz. <a href="%s" target="_blank">dokumentace</a>.', 'odwptp' ),
                'https://codex.wordpress.org/Editing_wp-config.php#Block_External_URL_Requests'
            ), 'error' );
        } );
    }
endif;

add_action( 'admin_init', 'odwptp_check_wp_http_block_external' );


if ( ! function_exists( 'odwptp_shortcode_add' ) ) :
    /**
     * Registers our shortcode "targetprocess-table" with displayed user stories.
     * @param array $atts
     * @return void
     * @since 0.3
     */
    function odwptp_shortcode_add( $atts ) {
        $a = shortcode_atts( [
            'rows'  => 100,
            'skip'  => 0,
            'title' => '',
        ], $atts );

        ob_start();
        $table = new ODWP_TP_Table( $a );
        $table->render();

        return ob_get_clean();
    }
endif;

add_shortcode( 'targetprocess-table', 'odwptp_shortcode_add' );


/**
 * @var string $odwptp_plugin_dir
 * @since 0.3
 */
$odwptp_plugin_dir = plugin_dir_path( __FILE__ );

// Load all classes
include( $odwptp_plugin_dir . 'src/ODWP_TP_UserStory.php' );
include( $odwptp_plugin_dir . 'src/ODWP_TP_Table.php' );


if ( ! function_exists( 'odwptp_shrotcode_button_init' ) ) :
    /**
     * Registers TinyMCE button for our shortcode.
     * @return void
     * @since 0.3
     */
    function odwptp_shrotcode_button_init() {
        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) && get_user_option( 'rich_editing' ) == 'true' ) {
            return;
        }

        add_filter( 'mce_external_plugins', 'odwptp_tinymce_external_plugins' );
        add_filter( 'mce_buttons', 'odwptp_add_tinymce_button' );
    }
endif;

add_action( 'admin_init', 'odwptp_shrotcode_button_init' );


if ( ! function_exists( 'odwptp_tinymce_external_plugins' ) ) :
    /**
     * @internal Adds our plugin into the TinyMCE.
     * @param array $plugins
     * @return array
     * @since 0.3
     */
    function odwptp_tinymce_external_plugins( $plugins ) {
        $plugins['odwptp_targetprocess_table'] = plugins_url( 'assets/js/targetprocess_table-shortcode.js', __FILE__ );
        return $plugins;
    }
endif;


if ( ! function_exists( 'odwptp_add_tinymce_button' ) ) :
    /**
     * @internal Adds TinyMCE button.
     * @param array $buttons
     * @return array
     * @since 0.3
     */
    function odwptp_add_tinymce_button( $buttons ) {
        //Add the button ID to the $button array
        $buttons[] = 'odwptp_targetprocess_table';
        return $buttons;
    }
endif;


if ( ! function_exists( 'odwptp_add_stylesheet' ) ) :
    /**
     * @internal Adds our stylesheet.
     * @return void
     * @since 0.3
     */
    function odwptp_add_stylesheet() {
        wp_enqueue_style( 'odwp-targetprocess', plugins_url( 'assets/css/public.css', __FILE__ ) );
    }
endif;

add_action( 'wp_head', 'odwptp_add_stylesheet' );


if ( ! function_exists( 'odwptp_xxx' ) ) :
    /**
     * ...
     * @return void
     * @since 0.1
     */
    function odwptp_xxx() {
        //...
    }
endif;
