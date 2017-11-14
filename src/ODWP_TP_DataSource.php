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

if ( ! class_exists( 'ODWP_TP_DataSource' ) ) :

/**
 * Datasource for Targetprocess table.
 *
 * Examples of API calls:
 * <ul>
 *  <li><code>/api/v1/UserStories?take=20&skip=10</code></li>
 *  <li><code>/api/v1/UserStories?where=EntityState.Name eq 'In Progress'</code></li>
 * @since 0.3
 */
class ODWP_TP_DataSource {

    /**
     * @var int $skip
     * @since 0.3
     */
    protected $skip;

	/**
	 * @var int $take
	 * @since 0.3
	 */
	protected $take;

    /**
     * @var string $where
     * @since 0.3
     */
    protected $where;

    /**
     * Constructor.
     * @since 0.3
     */
    public function __construct() {
        $take_opts = ['default' => 15, 'min_range' => 3, 'max_range' => 100];
        $skip_opts = ['default' => 0, 'min_range' => 0];

        $this->take = filter_input( INPUT_GET, 'take', FILTER_VALIDATE_INT, $take_opts );
        $this->skip = filter_input( INPUT_GET, 'skip', FILTER_VALIDATE_INT, $skip_opts );
        $this->where = filter_input( INPUT_GET, 'where' );
    }

	/**
	 * Returns parameters of the datasource as an array.
	 * @return array
	 * @since 0.3
	 */
	public function get_params() {
		return [
			'skip' => $this->skip,
			'take' => $this->take,
			'where' => $this->where,
		];
	}

    /**
     * @return integer
     * @since 0.3
     */
    public function get_skip() {
        return (int) $this->skip;
    }

	/**
	 * @return integer
	 * @since 0.3
	 */
	public function get_take() {
		return (int) $this->take;
	}

	/**
	 * @return string
	 * @since 0.3
	 */
	public function get_where() {
		return $this->where;
	}

    /**
     * Returns URL of the datasource.
     * @return string
     * @since 0.3
     */
    public function get_url() {
        $base_url  = get_option( 'odwptp_url' );
        $url       = $base_url . '/api/v1/UserStories/';
        /*$params    = [];

        if ( $this->skip > 0 ) {
            $params['skip'] = $this->skip;
        }

	    if ( $this->take > 0 ) {
		    $params['take'] = $this->take;
	    }

        if ( ! empty( $this->where ) ) {
            $params['where'] = $this->where;
        }*/

	    $params    = $this->get_params();

	    if ( count( $params ) <= 0 ) {
            return $url;
        }

        $url .= '?';
        array_walk( $params, function( $key, $val ) use ( &$url ) {
            $url .= ( ( substr( $url, -1 ) == '?' ) ? '' : '&' ) . $key . '=' . $val;
        } );

        return $url;
    }

    /**
     * Makes call to Targetprocess API.
     * @return array|null|WP_Error
     * @since 0.3
     */
    public function get_data() {
        $login    = get_option( 'odwptp_login' );
        $password = get_option( 'odwptp_password' );
        $call_url = $this->get_url();

        // Credentials are not set
        if( empty( $login ) || empty( $password ) || empty( $call_url ) ) {
            add_action( 'admin_notices', function() {
                odwptp_print_admin_notice( sprintf(
                    __( 'Pro správné použití pluginu <strong>odwp-targetprocess</strong> musíte nastavit přístupové údaje ke službě <strong>Targetprocess</strong> - viz. <a href="%s">Nastavení &gt; Obecné</a>.', 'odwptp' ),
                    admin_url( 'options-general.php#odwptp-settings' )
                ), 'warning' );
            } );
            return;
        }

        // Check the credentials
        $ret = wp_remote_request( $call_url, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode( $login . ':' . $password ),
                'Accept' => 'application/json',
            ],
        ] );

        return $ret;
    }
}

endif;
