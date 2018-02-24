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
	 * @var string $orderby
	 * @since 0.4
	 */
    protected $orderby;

	/**
	 * @var string $orderby
	 * @since 0.4
	 */
	protected $orderbydesc;

    /**
     * Constructor.
     * @param array $args
     * @since 0.3
     */
    public function __construct( array $args ) {
        $this->take = isset( $args['take'] )  ? (int) $args['take'] : 15;
        $this->skip = isset( $args['skip'] )  ? (int) $args['skip'] : 0;
        $this->where = isset( $args['where'] )  ? $args['where'] : '';
	    $this->orderby = isset( $args['orderby'] )  ? $args['orderby'] : '';
	    $this->orderbydesc = isset( $args['orderbydesc'] )  ? $args['orderbydesc'] : '';
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
	 * @return string
	 * @since 0.4
	 */
	public function get_orderby() {
		return $this->orderby;
	}

	/**
	 * @return string
	 * @since 0.4
	 */
	public function get_orderbydesc() {
		return $this->orderbydesc;
	}

    /**
     * Returns URL of the datasource.
     * @return string
     * @since 0.3
     */
    public function get_url() {
        $base_url = get_option( 'odwptp_url' );
        $url      = $base_url . '/api/v1/UserStories/';
	    $url     .= '?take=' . $this->take . '&skip=' . $this->skip . '&where=' . $this->where;

	    if ( ! empty( $this->orderby ) ) {
		    $url .= '&orderby=' . $this->orderby;
	    }

	    if ( ! empty( $this->orderbydesc ) ) {
		    $url .= '&orderbydesc=' . $this->orderbydesc;
	    }

        return $url;
    }

    /**
     * Makes call to Targetprocess API.
     * @return array|WP_Error
     * @since 0.3
     */
    public function get_data() {
        $login    = get_option( 'odwptp_login' );
        $password = get_option( 'odwptp_password' );
        $call_url = $this->get_url();

        // Credentials are not set
        if( empty( $login ) || empty( $password ) || empty( $call_url ) ) {
            return new WP_Error( 'odwptp_err_credentials_not_set', sprintf(
                __( 'Pro správné použití pluginu <strong>odwp-targetprocess</strong> musíte nastavit přístupové údaje ke službě <strong>Targetprocess</strong> - viz. <a href="%s">Nastavení &gt; Obecné</a>.', 'odwptp' ),
                admin_url( 'options-general.php#odwptp-settings' )
            ) );
        }

        // Check the credentials
        $ret = wp_remote_request( $call_url, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode( $login . ':' . $password ),
                'Accept' => 'application/json',
            ],
        ] );

        if ( ( $ret instanceof WP_Error ) || $ret['response']['code'] != 200 ) {
            return new WP_Error( 'odwptp_bad_api_response', sprintf(
                __( '%sPři spojení se serverem <strong>Targetprocess</strong> nastala chyba při spojení - zkuste, prosím, obnovit stránku a pokud se situace nezlepší, kontaktujte %sadministrátora%s.%s', 'odwptp' ),
                '<p class="odwptp-connection_error">',
                '<a href="mailto:' . get_option( 'admin_email' ) . '">',
                '</a>', '</p>'
            ) );
        }

        return $ret;
    }
}

endif;
