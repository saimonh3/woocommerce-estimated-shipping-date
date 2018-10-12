<?php
/**
* Plugin Name: WooCommerce Estimated Shipping Date
* Description: A simple WooCommerce based plugin to show the estimated shipping date on the product, cart, checkout page
* Author: Mohammed Saimon
* Author URI: https://saimonsplugins.com
* Version: 3.0.2
* Tested up to: 4.9.8
* Requires PHP: 5.6
* Text Domain: wcesd
* License: GPLv2 or later
**/

if ( ! defined( 'WPINC' ) ) exit;

/**
 * Woocommerce_Estimated_Shipping_Date Class
 */
final class Woocommerce_Estimated_Shipping_Date {
	protected $version = '3.0.2';
	private static $instance;

	/**
	 * Constructor method
	 */
	public function __construct() {
		$this->define_constants();
		$this->init_hooks();

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		$this->includes();
	}

	/**
	 * Define all the constants
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'WC_ESD', plugin_dir_path( __FILE__ ) );
		define( 'WC_ESD_INC', plugin_dir_path( __FILE__ ) . 'includes' );
		define( 'WC_ESD_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/' );
	}

	/**
	 * Init hooks
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'load_text_domain' ) );
	}

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function load_text_domain() {
        load_plugin_textdomain( 'wcesd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

	/**
	 * Include all the classes
	 *
	 * @return void
	 */
	public function includes() {

		require_once WC_ESD_INC . '/trait-helper-methods.php';

		if ( $this->request( 'admin' ) ) {
			require_once WC_ESD_INC . '/admin/class-settings.php';
			require_once WC_ESD_INC . '/admin/class-product-settings.php';

			if ( $this->has_dokan() ) {
				require_once WC_ESD_INC . '/admin/class-dokan-settings.php';
			}

		}

		if ( $this->request( 'public' ) ) {
			require_once WC_ESD_INC . '/public/class-public.php';

			if ( $this->has_dokan() ) {
				require_once WC_ESD_INC . '/public/class-dokan.php';
			}
		}
	}

	/**
	 * Check if dokan is installed
	 *
	 * @return boolean
	 */
	public function has_dokan() {
		if ( ! function_exists( 'dokan' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get request type
	 *
	 * @param  string $type
	 *
	 * @return boolean
	 */
	public function request( $type ) {
		$request = array(
			'admin'  => is_admin(),
			'public' => ! is_admin() && ! wp_doing_ajax()
		);

		return isset( $request[$type] ) ? $request[$type] : '';
	}

	/**
	 * Get instance
	 *
	 * @return object
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Plugin activate method
	 *
	 * @return void
	 */
	public function activate() {
		if ( ! function_exists( 'WC' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins( plugin_basename( __FILE__ ) );

			wp_die( '<div class="error"><p>' . sprintf( __( '<b>WC Estimated Shipping Date</b> requires %sWooCommerce%s to be installed & activated!', 'dokan-lite' ), '<a target="_blank" href="https://wordpress.org/plugins/woocommerce/">', '</a>' ) . '</p></div>' );
		}
	}

	/**
	 * Plugin deactivate method
	 *
	 * @return void
	 */
	public function deactivate() {
		//
	}

}

add_action( 'plugins_loaded', array( 'Woocommerce_Estimated_Shipping_Date', 'init' ) );


