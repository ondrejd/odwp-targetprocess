/**
 * Script for our shortcode's table.
 *
 * @author Ondrej Donek <ondrejd@gmail.com>
 * @link https://github.com/ondrejd/odwp-targetprocess for the canonical source repository
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-targetprocess
 * @since 0.4
 */
jQuery( document ).ready( function() {
    jQuery(".first-row-of-one").click( function( event ) {
        var secondRow = jQuery( this ).next( ".second-row-of-one" );
        if ( secondRow.hasClass( "hidden" ) ) {
            secondRow.removeClass( "hidden" );
        } else {
            secondRow.addClass( "hidden" );
        }
    } );
} );