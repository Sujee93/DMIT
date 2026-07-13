<?php
/**
 * Plugin Name:       Edz Memory Calendar
 * Plugin URI:        https://edzstudio.com/
 * Description:       "On this day" history calendar for WordPress + Elementor. Admins add dated history events from wp-admin, visitors can propose new events for moderation, and the front end shows a month calendar, a list view, and a homepage "on this day" timeline — all matched by month/day across every year on record.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Sujee
 * Author URI:        https://edzstudio.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       edz-memory-calendar
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'EDZ_MC_VERSION', '1.0.0' );
define( 'EDZ_MC_FILE', __FILE__ );
define( 'EDZ_MC_PATH', plugin_dir_path( __FILE__ ) );
define( 'EDZ_MC_URL', plugin_dir_url( __FILE__ ) );
define( 'EDZ_MC_CPT', 'edz_history_event' );
define( 'EDZ_MC_TAXONOMY', 'edz_event_category' );

/**
 * Core includes (safe to load on every request; each file just declares
 * classes/functions and hooks itself in via `init_hooks()` / bare `add_action`).
 */
require_once EDZ_MC_PATH . 'includes/class-edz-cpt.php';
require_once EDZ_MC_PATH . 'includes/class-edz-admin.php';
require_once EDZ_MC_PATH . 'includes/class-edz-settings.php';
require_once EDZ_MC_PATH . 'includes/class-edz-rest.php';
require_once EDZ_MC_PATH . 'includes/class-edz-submission.php';
require_once EDZ_MC_PATH . 'includes/class-edz-assets.php';
require_once EDZ_MC_PATH . 'includes/class-edz-render.php';
require_once EDZ_MC_PATH . 'includes/class-edz-shortcodes.php';

/**
 * Boots every plugin-side subsystem. Elementor's own widgets are only
 * registered once Elementor confirms it is active (see class-edz-elementor.php).
 */
final class EDZ_Memory_Calendar {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		EDZ_MC_CPT_Handler::instance();
		EDZ_MC_Admin::instance();
		EDZ_MC_Settings::instance();
		EDZ_MC_REST::instance();
		EDZ_MC_Submission::instance();
		EDZ_MC_Shortcodes::instance();
		EDZ_MC_Assets::instance();

		add_action( 'plugins_loaded', array( $this, 'maybe_load_elementor' ) );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'edz-memory-calendar', false, dirname( plugin_basename( EDZ_MC_FILE ) ) . '/languages' );
	}

	public function maybe_load_elementor() {
		if ( did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' ) ) {
			require_once EDZ_MC_PATH . 'elementor/class-edz-elementor.php';
			EDZ_MC_Elementor::instance();
		} else {
			add_action( 'elementor/loaded', function () {
				require_once EDZ_MC_PATH . 'elementor/class-edz-elementor.php';
				EDZ_MC_Elementor::instance();
			} );
		}
	}
}

function edz_memory_calendar() {
	return EDZ_Memory_Calendar::instance();
}
edz_memory_calendar();

/**
 * Activation: register the CPT/taxonomy immediately so the rewrite rules
 * flush correctly, then flush.
 */
function edz_mc_activate() {
	require_once EDZ_MC_PATH . 'includes/class-edz-cpt.php';
	EDZ_MC_CPT_Handler::instance()->register_post_type();
	EDZ_MC_CPT_Handler::instance()->register_taxonomy();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'edz_mc_activate' );

function edz_mc_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'edz_mc_deactivate' );
