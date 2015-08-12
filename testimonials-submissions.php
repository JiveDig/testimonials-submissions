<?php
/**
 * Plugin Name: Testimonials - Submissions
 * Plugin URI: http://thestizmedia.com
 * Description: Front-end posting of Testimonials by WooThemes via CMB2
 * Version: 1.0.0
 * Author: Mike Hemberger
 * Author URI: http://thestizmedia.com
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: tbws
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin and class main setup
if ( ! class_exists( 'Testimonials_Submissions' ) ) {

	final class Testimonials_Submissions {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of Testimonials_Submissions exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * Main Instance
		 *
		 * Ensures that only one instance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 *
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Testimonials_Submissions ) ) {
				self::$instance = new Testimonials_Submissions;
				self::$instance->setup_globals();
				self::$instance->includes();
			}

			return self::$instance;
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 * @see Testimonials_Submissions::init()
		 * @see Testimonials_Submissions::activation()
		 */
		private function __construct() {
			self::$instance = $this;

			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Globals
		 *
		 * @since 1.0
		 * @return void
		 */
		private function setup_globals() {
			// Plugin Folder Path
			if ( ! defined( 'TESTIMONIALS_SUBMISSIONS_POST_VERSION' ) ) {
				define( 'TESTIMONIALS_SUBMISSIONS_POST_VERSION', '1.0.0' );
			}
			// Plugin Folder Path
			if ( ! defined( 'TESTIMONIALS_SUBMISSIONS_POST_TITLE' ) ) {
				define( 'TESTIMONIALS_SUBMISSIONS_POST_TITLE', 'Testimonials - Submissions' );
			}
			// Plugin Folder Path
			if ( ! defined( 'TESTIMONIALS_SUBMISSIONS_POST_DIR' ) ) {
				define( 'TESTIMONIALS_SUBMISSIONS_POST_DIR', plugin_dir_path( __FILE__ ) );
			}
			// Plugin Folder URL
			if ( ! defined( 'TESTIMONIALS_SUBMISSIONS_POST_URL' ) ) {
				define( 'TESTIMONIALS_SUBMISSIONS_POST_URL', plugin_dir_url( __FILE__ ) );
			}
			// Plugin Root File
			if ( ! defined( 'TESTIMONIALS_SUBMISSIONS_POST_FILE' ) ) {
				define( 'TESTIMONIALS_SUBMISSIONS_POST_FILE', __FILE__ );
			}
		}

		/**
		 * Function fired on init
		 *
		 * This function is called on WordPress 'init'. It's triggered from the
		 * constructor function.
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @uses Testimonials_Submissions::load_textdomain()
		 *
		 * @return void
		 */
		public function init() {
			// If we need this later
		}

		/**
		 * Includes
		 *
		 * @since 1.0
		 * @access private
		 * @return void
		 */
		private function includes() {
			require_once( TESTIMONIALS_SUBMISSIONS_POST_DIR . '/includes/shortcode.php' );
		}

	}

	/**
	 * Bootstrap CMB2
	 * No need to check versions or if CMB2 is already loaded... the init file does that already!
	 *
	 * Check to see if CMB2 exists, and either bootstrap it or add a notice that it is missing
	 */
	if ( file_exists( dirname( __FILE__ ) . '/includes/CMB2/init.php' ) ) {
		require_once 'includes/CMB2/init.php';
	} else {
		add_action( 'admin_notices', 'tbws_woothemes_missing_cmb2' );
	}

	/**
	 * Check if Testimonials by WooThemes is installed, if not show an error
	 * If plugin loaded then load our class
	 *
	 * @since 1.0
	 *
	 * @see Testimonials_Submissions::get_instance()
	 *
	 * @return object Returns an instance of the main class
	 */
	add_action( 'plugins_loaded', 'tbws_woothemes_testimonials_load' );
	function tbws_woothemes_testimonials_load() {

	    if ( ! class_exists( 'Woothemes_Testimonials' ) ) {
			add_action( 'admin_notices', 'tbws_testimonials_missing_woothemes_testimonials' );
		} else {
	        return Testimonials_Submissions::get_instance();
	    }
	}

} // Testimonials_Submissions

/**
 * Add an error notice to the dashboard if CMB2 is not activated
 *
 * @return void
 */
function tbws_woothemes_missing_cmb2() {
?>
	<div class="error">
		<p><?php _e( 'tbws - Testimonials plugin is missing CMB2!', 'tbws' ); ?></p>
	</div>
<?php
}

/**
 * Add an error notice to the dashboard if Thrive - Core is not activated
 *
 * @return void
 */
function tbws_testimonials_missing_woothemes_testimonials() {
?>
	<div class="error">
		<p><?php _e( 'Testimonials - Submissions requires Testimonials by WooThemes to be installed and active.', 'tbws' ); ?></p>
	</div>
<?php
}
