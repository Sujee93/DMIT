<?php
/**
 * Plugin Name:       DMIT Memory Calendar
 * Plugin URI:        https://github.com/sujee93/dmit
 * Description:       "On this day" history calendar for WordPress + Elementor. Admins add dated history events from wp-admin, visitors can propose new events for moderation, and the front end shows a month calendar, a list view, and a homepage "on this day" timeline — all matched by month/day across every year on record.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            DMIT
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       dmit-memory-calendar
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'DMIT_MC_VERSION', '1.0.0' );
define( 'DMIT_MC_FILE', __FILE__ );
define( 'DMIT_MC_PATH', plugin_dir_path( __FILE__ ) );
define( 'DMIT_MC_URL', plugin_dir_url( __FILE__ ) );
define( 'DMIT_MC_CPT', 'dmit_history_event' );
define( 'DMIT_MC_TAXONOMY', 'dmit_event_category' );

/**
 * Core includes (safe to load on every request; each file just declares
 * classes/functions and hooks itself in via `init_hooks()` / bare `add_action`).
 */
require_once DMIT_MC_PATH . 'includes/class-dmit-cpt.php';
require_once DMIT_MC_PATH . 'includes/class-dmit-admin.php';
require_once DMIT_MC_PATH . 'includes/class-dmit-settings.php';
require_once DMIT_MC_PATH . 'includes/class-dmit-rest.php';
require_once DMIT_MC_PATH . 'includes/class-dmit-submission.php';
require_once DMIT_MC_PATH . 'includes/class-dmit-assets.php';
require_once DMIT_MC_PATH . 'includes/class-dmit-render.php';
require_once DMIT_MC_PATH . 'includes/class-dmit-shortcodes.php';

/**
 * Boots every plugin-side subsystem. Elementor's own widgets are only
 * registered once Elementor confirms it is active (see class-dmit-elementor.php).
 */
final class DMIT_Memory_Calendar {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		DMIT_MC_CPT_Handler::instance();
		DMIT_MC_Admin::instance();
		DMIT_MC_Settings::instance();
		DMIT_MC_REST::instance();
		DMIT_MC_Submission::instance();
		DMIT_MC_Shortcodes::instance();
		DMIT_MC_Assets::instance();

		add_action( 'plugins_loaded', array( $this, 'maybe_load_elementor' ) );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'dmit-memory-calendar', false, dirname( plugin_basename( DMIT_MC_FILE ) ) . '/languages' );
	}

	public function maybe_load_elementor() {
		if ( did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' ) ) {
			require_once DMIT_MC_PATH . 'elementor/class-dmit-elementor.php';
			DMIT_MC_Elementor::instance();
		} else {
			add_action( 'elementor/loaded', function () {
				require_once DMIT_MC_PATH . 'elementor/class-dmit-elementor.php';
				DMIT_MC_Elementor::instance();
			} );
		}
	}
}

function dmit_memory_calendar() {
	return DMIT_Memory_Calendar::instance();
}
dmit_memory_calendar();

/**
 * Activation: register the CPT/taxonomy immediately so the rewrite rules
 * flush correctly, then flush.
 */
function dmit_mc_activate() {
	require_once DMIT_MC_PATH . 'includes/class-dmit-cpt.php';
	DMIT_MC_CPT_Handler::instance()->register_post_type();
	DMIT_MC_CPT_Handler::instance()->register_taxonomy();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'dmit_mc_activate' );

function dmit_mc_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'dmit_mc_deactivate' );
