<?php
/**
 * Plugin Name:           AutomatorWP - WP PostRatings integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-wp-postratings-integration/
 * Description:           Connect AutomatorWP with WP PostRatings.
 * Version:               1.0.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wp-postratings-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.9
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WP_PostRatings
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_WP_PostRatings_Integration {

    /**
     * @var         AutomatorWP_WP_PostRatings_Integration $instance The one true AutomatorWP_WP_PostRatings_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_WP_PostRatings_Integration self::$instance The one true AutomatorWP_WP_PostRatings_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_WP_PostRatings_Integration();

            if( ! self::$instance->pro_installed() ) {

                self::$instance->constants();
                self::$instance->includes();
                self::$instance->load_textdomain();

            }

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'AUTOMATORWP_WP_POSTRATINGS_VER', '1.0.1' );

        // Plugin file
        define( 'AUTOMATORWP_WP_POSTRATINGS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WP_POSTRATINGS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WP_POSTRATINGS_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            // Triggers
            require_once AUTOMATORWP_WP_POSTRATINGS_DIR . 'includes/triggers/rate-post.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'wp_postratings', array(
            'label' => 'WP PostRatings',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wp-postratings.svg',
        ) );

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'AutomatorWP - WP PostRatings requires %s and %s in order to work. Please install and activate them.', 'automatorwp-wp-postratings-integration' ),
                        '<a href="https://wordpress.org/plugins/automatorwp/" target="_blank">AutomatorWP</a>',
                        '<a href="https://wordpress.org/plugins/wp-postratings/" target="_blank">WP PostRatings</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php elseif ( $this->pro_installed() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php echo __( 'You can uninstall AutomatorWP - WP PostRatings Integration because you already have the pro version installed and includes all the features of the free version.', 'automatorwp-wp-postratings-integration' ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! function_exists( 'postratings_init' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Check if the pro version of this integration is installed
     *
     * @since  1.0.0
     *
     * @return bool True if pro version installed
     */
    private function pro_installed() {

        if ( ! class_exists( 'AutomatorWP_WP_PostRatings' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {

        // Set filter for language directory
        $lang_dir = AUTOMATORWP_WP_POSTRATINGS_DIR . '/languages/';
        $lang_dir = apply_filters( 'automatorwp_wp_postratings_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'automatorwp-wp-postratings-integration' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'automatorwp-wp-postratings-integration', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/automatorwp-wp-postratings-integration/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/automatorwp-wp-postratings-integration/ folder
            load_textdomain( 'automatorwp-wp-postratings-integration', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/automatorwp-wp-postratings-integration/languages/ folder
            load_textdomain( 'automatorwp-wp-postratings-integration', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'automatorwp-wp-postratings-integration', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_WP_PostRatings_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_WP_PostRatings_Integration The one true AutomatorWP_WP_PostRatings_Integration
 */
function AutomatorWP_WP_PostRatings_Integration() {
    return AutomatorWP_WP_PostRatings_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_WP_PostRatings_Integration' );
