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

if ( ! class_exists( 'ODWP_TP_UserStory' ) ) :

/**
 * Class representing single user story.
 * @since 0.3
 */
class ODWP_TP_UserStory {

    /**
     * @var int $id
     * @since 0.3
     */
    protected $id;

    /**
     * @var string $role
     * @since 0.3
     */
    protected $role;

    /**
     * @var string $tags
     * @since 0.3
     */
    protected $tags;

    /**
     * @var string $min_md_rate
     * @since 0.3
     */
    protected $min_md_rate;

    /**
     * @var string $opt_md_rate
     * @since 0.3
     */
    protected $opt_md_rate;

    /**
     * @var string $contract_type
     * @since 0.3
     */
    protected $contract_type;

    /**
     * @var string $description
     * @since 0.3
     */
    protected $description;

    /**
     * Constructor.
     * @param array $args
     * @since 0.3
     */
    public function __construct( $args = [] ) {
        if ( isset( $args['id'] ) ) {
            $this->id = (int) $args['id'];
        }

        if ( isset( $args['role'] ) ) {
            $this->role = $args['role'];
        }

        if ( isset( $args['tags'] ) ) {
            $this->tags = $args['tags'];
        }

        if ( isset( $args['min_md_rate'] ) ) {
            $this->min_md_rate = $args['min_md_rate'];
        }

        if ( isset( $args['contract_type'] ) ) {
            $this->contract_type = $args['contract_type'];
        }

        if ( isset( $args['opt_md_rate'] ) ) {
            $this->opt_md_rate = $args['opt_md_rate'];
        }

        if ( isset( $args['description'] ) ) {
            $this->description = $args['description'];
        }
    }

    /**
     * @return int Id of the user story.
     * @since 0.3
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * @return string Role of the user story.
     * @since 0.3
     */
    public function get_role() {
        return $this->role;
    }

    /**
     * @return string Tags of the user story.
     * @since 0.3
     */
    public function get_tags() {
        return $this->tags;
    }

    /**
     * @return string Minimal MD rate of the user story.
     * @since 0.3
     */
    public function get_min_md_rate() {
        return $this->min_md_rate;
    }

    /**
     * @return string Optimal MD rate of the user story.
     * @since 0.3
     */
    public function get_opt_md_rate() {
        return $this->opt_md_rate;
    }

    /**
     * @return string Contract type of the user story.
     * @since 0.3
     */
    public function get_contract_type() {
        return $this->contract_type;
    }

    /**
     * @return string Description of the user story.
     * @since 0.3
     */
    public function get_description() {
        return $this->description;
    }
}

endif;
