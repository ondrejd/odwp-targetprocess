<?php
/**
 * Plugin Name: odwp-targetprocess
 * Plugin URI: https://github.com/ondrejd/odwp-targetprocess
 * Description: Plugin that uses <a href="https://www.targetprocess.com/" target="blank">Targetprocess API</a> to publish <em>user stories</em> on your site.
 * Version: 0.1
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
     * @uses register_setting
     * @uses add_settings_section
     * @uses add_settings_field
     */
    function odwptp_settings_init() {
        register_setting( 'general', 'odwptp_login', ['type' => 'string'] );
        register_setting( 'general', 'odwptp_password', ['type' => 'string'] );

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
            '<p>', '<a href="https://www.targetprocess.com/" target="_blank">', '</a>', '</p>'
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
