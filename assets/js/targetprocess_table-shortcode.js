/**
 * @author Ondrej Donek <ondrejd@gmail.com>
 * @link https://github.com/ondrejd/odwp-targetprocess for the canonical source repository
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-targetprocess
 * @since 0.1
 */

jQuery( document ).ready( function( $ ) {
    tinymce.create( 'tinymce.plugins.odwptp_targetprocess_table', {
        init : function( ed, url) {
            ed.addCommand( 'odwptp_insert_targetprocess_table', function() {
                var content =  '[targetprocess-table]';
                tinymce.execCommand( 'mceInsertContent', false, content );
            });

            ed.addButton( 'odwptp_targetprocess_table', {
                title : 'Insert shortcode',
                cmd : 'odwptp_insert_targetprocess_table',
                image: url + '../../../assets/img/targetprocess-32x32.png'
            } );
        },
    });

    tinymce.PluginManager.add( 'odwptp_targetprocess_table', tinymce.plugins.odwptp_targetprocess_table );
});
