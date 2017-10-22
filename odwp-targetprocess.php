<?php
/**
 * Plugin Name: odwp-targetprocess
 * Plugin URI: https://github.com/ondrejd/odwp-targetprocess
 * Description: Plugin that uses <a href="https://www.targetprocess.com/" target="blank">Targetprocess API</a> to publish <em>user stories</em> on your site.
 * Version: 0.2
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
            'wporg_settings_login_field',
            __( 'Jméno/email', 'odwptp' ),
            'odwptp_settings_login_field_cb',
            'general',
            'odwptp_settings_section'
        );

        add_settings_field(
            'wporg_settings_password_field',
            __( 'Heslo', 'odwptp' ),
            'odwptp_settings_password_field_cb',
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
<input type="text" name="odwptp_login" value="<?= isset( $val ) ? esc_attr( $val ) : '' ?>">
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
<input type="text" name="odwptp_password" value="<?= isset( $val ) ? esc_attr( $val ) : '' ?>">
<?php
    }
endif;


if ( ! function_exists( 'odwptp_print_admin_notice' ) ) :
    /**
     * Prints WP admin notice.
     * @param string $message
     * @param string $type (Optional.) Possible values: ["error", "warning", "success", "info"]. Defaultly "info".
     * @param boolean $dismissible (Optional.) Defaultly `TRUE`.
     * @since 0.1
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

        printf( '<div class="%s"><p>%s</p></div>', $classes, wp_kses( $message ) );
    }
endif;


if ( ! function_exists( 'odwptp_check_credentials' ) ) :
    /**
     * Checks Targetprocess credentials.
     * @return void
     * @since 0.1
     */
    function odwptp_check_credentials() {
        $login = get_option( 'odwptp_login' );
        $password = get_option( 'odwptp_password' );

        // Credentials are not set
        if( empty( $login ) || empty( $password ) ) {
            add_action( 'admin_notices', function() {
                odwptp_print_admin_notice( sprintf(
                    __( 'Pro správné použití pluginu <strong>odwp-targetprocess</strong> musíte nastavit přístupové údaje ke službě <strong>Targetprocess</strong> - viz. <a href="%s">Nastavení &gt; Obecné</a>.', 'odwptp' ),
                    admin_url( 'options-general.php#odwptp-settings' )
                ), 'warning' );
            } );
            return;
        }

        // Check the credentials
        $url = 'https://icthunter.tpondemand.com/api/v1/Context/';
        $ret = wp_remote_request( $url, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode( $login . ':' . $password ),
                'Accept' => 'application/json',
            ],
        ] );

        // Control request did not end well
        if ( ( $ret instanceof WP_Error ) ) {
            add_action( 'admin_notices', function() use ( $ret ) {
                $msg = '<ul>';

                foreach ( $ret->get_error_messages() as $err_raw ) {
                    $err = trim( $err_raw );

                    if ( ! empty( $err ) ) {
                        $msg .= '<li>' . $err . '</li>';
                    }
                }

                $msg = '</ul>';

                if ( $msg == '<ul></ul>' ) {
                    $msg = sprintf(
                        __( 'Při dotazu na server  <a href="%s" target="_blank">Targetprocess</a> nastala neznámá chyba - ujistěte se, že máte <a href="%s">nastavení</a> pluginu v pořádku.', 'odwptp' ),
                        'https://www.targetprocess.com/',
                        admin_url( 'options-general.php#odwptp-settings' )
                    );
                }

                odwptp_print_admin_notice( $msg, 'error' );
            } );
            return;
        }

        // Check if the response is correct
        if ( $ret['response']['code'] == 200 ) {
            // XXX Make this hidden by user preference (so show it just once if user wants it).
            /*$show = (bool) get_option( 'odwptp_show_connection_success' );
            if ( $show === false ) {
                return;
            }*/

            add_action( 'admin_notices', function() {
                odwptp_print_admin_notice( sprintf(
                    __( 'Údaje pro připojení se k Vašemu <a href="%s" target="_blank">Targetprocess</a> účtu jsou správné, nyní můžete umístit <em>targetprocess_shortcode</em> do Vašich příspěvků či stránek.', 'odwptp' ),
                    'https://www.targetprocess.com/'
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
