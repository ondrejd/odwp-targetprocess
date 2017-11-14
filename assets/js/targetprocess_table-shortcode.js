/**
 * @author Ondrej Donek <ondrejd@gmail.com>
 * @link https://github.com/ondrejd/odwp-targetprocess for the canonical source repository
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-targetprocess
 * @since 0.3
 * @todo Translate strings!
 */

jQuery( document ).ready( function( $ ) {
    tinymce.create( 'tinymce.plugins.odwptp_targetprocess_table', {
        init : function( ed, url) {
            ed.addCommand( 'odwptp_insert_targetprocess_table', function() {
                var defaults = {
                    take: 100,
                    title: ''
                };

                ed.windowManager.open( {
                    title: 'Insert Targetprocess table',
                    data: defaults,
                    body: [{
                        name: 'title',
                        type: 'textbox',
                        label: 'Title',
                        value: defaults.title,
                        onchange: function() { data.title = this.value(); }
                    }, {
                        name: 'take',
                        type: 'textbox',
                        label: 'Rows',
                        value: defaults.take,
                        onchange: function() { data.take = this.value(); }
                    }],
                    onSubmit: function(e) {
                        var html = '[targetprocess-table ';
                                tinymce.extend( defaults, e.data );

                        html += ' title="' + e.data.title + '"';
                        html += ' take="' + e.data.take + '"';
                        html += ']';

                        tinymce.execCommand( 'mceInsertContent', false, html );
                    }
                } );
            } );

            ed.addButton( 'odwptp_targetprocess_table', {
                title : 'Insert shortcode',
                cmd : 'odwptp_insert_targetprocess_table',
                image: url + '../../../assets/img/targetprocess-32x32.png'
            } );
        },//rows,title
    });

    tinymce.PluginManager.add( 'odwptp_targetprocess_table', tinymce.plugins.odwptp_targetprocess_table );
});
