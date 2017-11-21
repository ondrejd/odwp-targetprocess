<?php
/**
 * @author Ondrej Donek <ondrejd@gmail.com>
 * @link https://github.com/ondrejd/odwp-targetprocess for the canonical source repository
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-targetprocess
 * @since 0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ODWP_TP_Table' ) ) :

/**
 * Class that renders table with Targetprocess user stories.
 * @since 0.3
 * @todo Finish this (ODWP_TP_Table)!
 */
class ODWP_TP_Table {
    const POSITION_BOTTOM = 'bottom';
    const POSITION_TOP    = 'top';

    /**
     * @var ODWP_TP_DataSource $ds
     * @since 0.3
     */
    protected $ds;

    /**
     * @var string $title
     * @since 0.3
     */
    protected $title;

    /**
     * @access private
     * @param array $stories Array of {@see ODWP_TP_UserStory}.
     * @since 0.3
     */
    private $stories = [];

    /**
     * Constructor.
     * @global string $odwptp_plugin_dir
     * @param array $args
     * @since 0.3
     */
    public function __construct( array $args ) {
        global $odwptp_plugin_dir;

        if ( isset( $args['title'] ) ) {
            $this->title = $args['title'];
            unset( $args[ 'title'] );
        }

        include( $odwptp_plugin_dir . 'src/ODWP_TP_DataSource.php' );
        $this->ds = new ODWP_TP_DataSource( $args );
    }

    /**
     * @internal Parses user stories.
     * @param array $response
     * @return array
     * @since 0.3
     */
    protected function parse_user_stories( $response ) {
        $json = json_decode( $response['body'] );
        $ret  = [];

        if ( ! is_object( $json ) ) {
            return $ret;
        }

        if ( ! property_exists( $json, 'Items' ) ) {
            return $ret;
        }

        foreach ( $json->Items as $_story ) {
            $role = '';
            $min_md_rate = '';
            $opt_md_rate = '';
            $contract_type = '';

            foreach ( $_story->CustomFields as $field ) {
                if ( ! is_object( $field ) ) {
                    continue;
                }

                switch ( $field->Name) {
                    case 'Role'            : $role          = $field->Value; break;
                    case 'Minimal MD rate' : $min_md_rate   = $field->Value; break;
                    case 'Optimal MD rate' : $opt_md_rate   = $field->Value; break;
                    case 'Contract type'   : $contract_type = $field->Value; break;
                }
            }

            $ret[] = new ODWP_TP_UserStory( [
                'id'            => $_story->Id,
                'role'          => $role,
                'tags'          => $_story->Tags,
                'min_md_rate'   => $min_md_rate,
                'opt_md_rate'   => $opt_md_rate,
                'contract_type' => $contract_type,
                'description'   => $_story->Description,
            ] );
        }

        return $ret;
    }

    /**
     * @return int Returns count of stories.
     * @since 0.3
     */
    public function get_count() {
        return count( $this->stories );
    }

	/**
     * @global WP $wp
	 * @return string Returns URL of previous set of items.
	 * @since 0.4
	 */
	public function get_prev_url() {
	    global $wp;

		if ( $this->is_start() ) {
			return '#';
        }

        $params = $this->ds->get_params();
		$params['skip'] = $params['skip'] - $params['take'];

        return home_url( add_query_arg( $params, $wp->request ) );
	}

	/**
	 * @global WP $wp
	 * @return string Returns URL of previous set of items.
	 * @since 0.4
	 */
	public function get_next_url() {
		global $wp;

		if ( $this->is_end() ) {
			return '#';
		}

		$params = $this->ds->get_params();
		$params['skip'] = $params['skip'] + $params['take'];

		return home_url( add_query_arg( $params, $wp->request ) );
	}

	/**
	 * @global WP $wp
	 * @return string Returns URL for sorting anchors.
	 * @since 0.4
	 */
	public function get_sort_url() {
	    global $wp;

		$key1 = $this->ds->get_orderby();
		$key2 = $this->ds->get_orderbydesc();

		/**
		 * @param mixed $param
		 * @return string Returns either. "ASC" or "DESC".
		 */
		$t = function( $param ) {
			//...
			$orderby = filter_input( INPUT_GET, 'orderby' );
			$orderbydesc = filter_input( INPUT_GET, 'orderbydesc' );
			//...
		};

	    //...
    }

    /**
     * @return array Array of {@see ODWP_TP_UserStory}.
     * @since 0.3 Returns stories.
     */
    public function get_stories() {
        return $this->stories;
    }

	/**
	 * Sets stories and other private properties.
	 * @param array $stories Array of {@see ODWP_TP_UserStory}.
	 * @since 0.3
	 */
	public function set_stories( array $stories ) {
		$this->stories  = $stories;
	}

	/**
	 * @return bool Returns TRUE if we are on the start of items list.
     * @since 0.4
	 */
    public function is_start() {
        return ( $this->ds->get_skip() === 0 );
    }

	/**
	 * @return bool Returns TRUE if we are on the end of items list.
     * @since 0.4
	 */
    public function is_end() {
        return ( $this->get_count() < $this->ds->get_take() );
    }

	/**
	 * Renders HTML of our table.
	 * @since 0.3
	 */
	public function render() {
		$ret = $this->ds->get_data();
		?>
        <div class="odwptp-targetprocess_table_cont">
			<?php if ( ( $ret instanceof WP_Error ) ) : ?>
                <p class="odwptp-connection_error"><?php
					printf(
						__( 'Při spojení se serverem <strong>Targetprocess</strong> nastala chyba - zkuste, prosím, obnovit stránku a pokud se situace nezlepší, kontaktujte %sadministrátora%s.', 'odwptp' ),
						'<a href="mailto:' . get_option( 'admin_email' ) . '">', '</a>'
					);
					?></p>
			<?php elseif ( $ret['response']['code'] != 200 ) : ?>
                <p class="odwptp-connection_error"><?php
					printf(
						__( 'Při spojení se serverem <strong>Targetprocess</strong> nastala chyba při spojení - zkuste, prosím, obnovit stránku a pokud se situace nezlepší, kontaktujte %sadministrátora%s.', 'odwptp' ),
						'<a href="mailto:' . get_option( 'admin_email' ) . '">', '</a>'
					);
					?></p>
			<?php else :
				$stories = $this->parse_user_stories( $ret );
				$this->set_stories( $stories );

				if ( $this->get_count() == 0 ) :
					?><p class="odwptp-connection_error"><?php
					printf(
						__( 'Při spojení se serverem <strong>Targetprocess</strong> nastala chyba při spojení - zkuste, prosím, obnovit stránku a pokud se situace nezlepší, kontaktujte %sadministrátora%s.', 'odwptp' ),
						'<a href="mailto:' . get_option( 'admin_email' ) . '">', '</a>'
					);
					?></p><?php
				endif;

				$this->render_table();
			endif; ?>
        </div>
		<?php
	}

    /**
     * @internal Renders table.
     * @since 0.3
     */
    protected function render_table() {
?>
<div class="targetprocess-table-cont">
    <?php if ( ! empty( $this->title ) ) :?>
    <h2><?=$this->title?></h2>
    <?php endif?>
    <div class="row">
        <?php $this->render_tablenav()?>
    </div>
    <table id="odwptp-targetprocess_table" class="targetprocess-table">
        <?php $this->render_thead() ?>
        <?php $this->render_tbody() ?>
    </table>
</div>
<?php
    }

    /**
     * @internal Renders table navigation.
     * @param string $position (Optional.)
     * @since 0.3
     */
    protected function render_tablenav( $position = self::POSITION_TOP ) {
?>
<div class="tablenav <?= $position ?>">
    <div class="tablenav-pages">
        <span class="displaying-num"><?php printf( __( 'Zobrazeno položek: %d', 'odwptp' ), $this->get_count() ) ?></span>
        <?php if ( ! ( $this->is_start() && $this->is_end() ) ) : ?>
        <span class="pagination-links">
            <?php if ( ! $this->is_start() ) : ?>
            <a class="prev-page" href="<?= $this->get_prev_url() ?>"><?php _e( 'Předchozí', 'odwptp' ) ?></a>
            <?php else : ?>
            <span class="prev-page"><?php _e( 'Předchozí', 'odwptp' ) ?></span>
            <?php endif ?>
            <?php if ( ! $this->is_end() ) : ?>
            <a class="next-page" href="<?= $this->get_next_url() ?>"><?php _e( 'Následující', 'odwptp' ) ?></a>
            <?php else : ?>
            <span class="next-page"><?php _e( 'Následující', 'odwptp' ) ?></span>
            <?php endif ?>
        </span>
        <?php endif ?>
    </div>
</div>
<?php
    }

    /**
     * @internal Renders thead element.
     * @param string $position (Optional.)
     * @since 0.3
     */
    protected function render_thead( $position = self::POSITION_TOP ) {
?>
    <thead class="thead-<?= $position ?>">
        <tr>
            <th class="column-id column-primary sorted desc" scope="col">
                <a href="<?= $this->get_sort_url( 'Id' )?>">
                    <span><?php _e( 'ID', 'odwptp' ) ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th class="column-role sortable" scope="col">
                <a href="<?= $this->get_sort_url( 'CustomFields.Role' )?>">
                    <span><?php _e( 'Role', 'odwptp' ) ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th class="column-tags sortable" scope="col">
                <a href="<?= $this->get_sort_url( 'Tags' )?>">
                    <span><?php _e( 'Tagy', 'odwptp' ) ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th class="column-min_md_rate sortable" scope="col">
                <a href="<?= $this->get_sort_url( 'CustomFields.Minimal MD rate' )?>">
                    <span><?php _e( 'Min. MD rate', 'odwptp' ) ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th class="column-opt_md_rate sortable" scope="col">
                <a href="<?= $this->get_sort_url( 'CustomFields.Optimal MD rate' )?>">
                    <span><?php _e( 'Opt. MD rate', 'odwptp' ) ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th class="column-contract_type sortable" scope="col">
                <a href="<?= $this->get_sort_url( 'CustomFields.Contract Type' )?>">
                    <span><?php _e( 'Typ kontraktu', 'odwptp' ) ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        </tr>
    </thead>
<?php
    }

    /**
     * @internal Renders tbody element.
     * @since 0.3
     */
    protected function render_tbody() {
?>
<tbody><?php
    foreach ( $this->stories as $story ) :
        $this->render_tbody_row( $story );
    endforeach;
?></tbody>
<?php
    }

    /**
     * @internal Renders tbody's tr element.
     * @param ODWP_TP_UserStory $story
     * @since 0.3
     */
    protected function render_tbody_row( ODWP_TP_UserStory $story ) {
?>
    <tr class="first-row-of-one" id="first-row-of-one-<?php echo $story->get_id()?>">
        <th class="column-id" scope="row"><a href="#"><?php echo $story->get_id() ?></a></th>
        <td><?php echo $story->get_role() ?></td>
        <td><?php echo $story->get_tags() ?></td>
        <td><?php echo $story->get_min_md_rate() ?></td>
        <td><?php echo $story->get_opt_md_rate() ?></td>
        <td><?php echo $story->get_contract_type() ?></td>
    </tr>
    <tr class="second-row-of-one hidden" id="second-row-of-one-<?php echo $story->get_id()?>">
        <td colspan="6"><?php echo $story->get_description( true )?></td>
    </tr>
<?php
    }
}

endif;
