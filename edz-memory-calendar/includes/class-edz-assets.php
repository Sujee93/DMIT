<?php
/**
 * Registers every script/style the plugin ships, but never enqueues them
 * globally — shortcodes and Elementor widgets call the small `enqueue_*()`
 * helpers below only on pages that actually use them, so sites that embed
 * just the homepage timeline don't also pay for FullCalendar's JS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDZ_MC_Assets {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Uses the file's last-modified time as the query-string version
	 * instead of the static plugin version, so every edit automatically
	 * busts browser/caching-plugin caches without needing a manual
	 * version bump.
	 */
	private function ver( $relative_path ) {
		$path = EDZ_MC_PATH . $relative_path;
		return file_exists( $path ) ? (string) filemtime( $path ) : EDZ_MC_VERSION;
	}

	public function register_assets() {
		// FullCalendar v6's "global" bundle injects its own base CSS at runtime — no separate stylesheet to enqueue.
		wp_register_script( 'edz-mc-fullcalendar', EDZ_MC_URL . 'assets/lib/fullcalendar/fullcalendar.min.js', array(), $this->ver( 'assets/lib/fullcalendar/fullcalendar.min.js' ), true );

		wp_register_style( 'edz-mc-calendar', EDZ_MC_URL . 'assets/css/edz-calendar.css', array(), $this->ver( 'assets/css/edz-calendar.css' ) );
		wp_register_script( 'edz-mc-calendar', EDZ_MC_URL . 'assets/js/edz-calendar.js', array( 'edz-mc-fullcalendar' ), $this->ver( 'assets/js/edz-calendar.js' ), true );

		wp_register_style( 'edz-mc-timeline', EDZ_MC_URL . 'assets/css/edz-timeline.css', array(), $this->ver( 'assets/css/edz-timeline.css' ) );
		wp_register_script( 'edz-mc-timeline', EDZ_MC_URL . 'assets/js/edz-timeline.js', array(), $this->ver( 'assets/js/edz-timeline.js' ), true );

		wp_register_style( 'edz-mc-list', EDZ_MC_URL . 'assets/css/edz-list.css', array(), $this->ver( 'assets/css/edz-list.css' ) );
		wp_register_script( 'edz-mc-list', EDZ_MC_URL . 'assets/js/edz-list.js', array(), $this->ver( 'assets/js/edz-list.js' ), true );

		wp_register_style( 'edz-mc-submit-form', EDZ_MC_URL . 'assets/css/edz-submit-form.css', array(), $this->ver( 'assets/css/edz-submit-form.css' ) );
		wp_register_script( 'edz-mc-submit-form', EDZ_MC_URL . 'assets/js/edz-submit-form.js', array(), $this->ver( 'assets/js/edz-submit-form.js' ), true );

		if ( EDZ_MC_Settings::get( 'enable_recaptcha' ) && EDZ_MC_Settings::get( 'recaptcha_site_key' ) ) {
			wp_register_script(
				'edz-mc-recaptcha',
				'https://www.google.com/recaptcha/api.js?render=' . rawurlencode( EDZ_MC_Settings::get( 'recaptcha_site_key' ) ),
				array(),
				null,
				true
			);
		}
	}

	private function localize_common( $handle ) {
		wp_localize_script(
			$handle,
			'edzMC',
			array(
				'restUrl'       => esc_url_raw( rest_url( EDZ_MC_REST::NAMESPACE_ ) ),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'submitNonce'   => wp_create_nonce( 'edz_mc_submit_event' ),
				'accentColor'   => EDZ_MC_Settings::get( 'accent_color', '#2563EB' ),
				'todayColor'    => EDZ_MC_Settings::get( 'today_color', '#F59E0B' ),
				'recaptchaSiteKey' => EDZ_MC_Settings::get( 'enable_recaptcha' ) ? EDZ_MC_Settings::get( 'recaptcha_site_key' ) : '',
				'monthNames'    => $this->i18n_month_names(),
				'dayNamesShort' => $this->i18n_day_names(),
				'locale'        => get_locale(),
				'i18n'          => array(
					'noEvents'      => __( 'No events on this day.', 'edz-memory-calendar' ),
					'pickAnother'   => __( 'Choose another date to explore.', 'edz-memory-calendar' ),
					'listView'      => __( 'List view', 'edz-memory-calendar' ),
					'calendarView'  => __( 'Calendar view', 'edz-memory-calendar' ),
					'loading'       => __( 'Loading…', 'edz-memory-calendar' ),
					'today'         => __( 'Today', 'edz-memory-calendar' ),
					'source'        => __( 'Source', 'edz-memory-calendar' ),
					'submitError'   => __( 'Something went wrong. Please try again.', 'edz-memory-calendar' ),
					'submitSuccess' => __( 'Thank you! Your event has been submitted and is awaiting review.', 'edz-memory-calendar' ),
					'noResults'     => __( 'No events found.', 'edz-memory-calendar' ),
					'prev'          => __( 'Previous', 'edz-memory-calendar' ),
					'next'          => __( 'Next', 'edz-memory-calendar' ),
					/* translators: used as-is when a day has exactly one event */
					'oneEvent'      => __( '1 event', 'edz-memory-calendar' ),
					/* translators: %d is replaced client-side with the event count */
					'manyEvents'    => __( '%d events', 'edz-memory-calendar' ),
				),
			)
		);
	}

	private function i18n_month_names() {
		global $wp_locale;
		return array_values( $wp_locale->month );
	}

	private function i18n_day_names() {
		global $wp_locale;
		return array_values( $wp_locale->weekday_abbrev );
	}

	public function enqueue_calendar_assets() {
		wp_enqueue_style( 'edz-mc-calendar' );
		wp_enqueue_script( 'edz-mc-fullcalendar' );
		wp_enqueue_script( 'edz-mc-calendar' );
		$this->localize_common( 'edz-mc-calendar' );
	}

	public function enqueue_timeline_assets() {
		wp_enqueue_style( 'edz-mc-timeline' );
		wp_enqueue_script( 'edz-mc-timeline' );
		$this->localize_common( 'edz-mc-timeline' );
	}

	public function enqueue_list_assets() {
		wp_enqueue_style( 'edz-mc-list' );
		wp_enqueue_script( 'edz-mc-list' );
		$this->localize_common( 'edz-mc-list' );
	}

	public function enqueue_submit_form_assets() {
		wp_enqueue_style( 'edz-mc-submit-form' );
		wp_enqueue_script( 'edz-mc-submit-form' );
		$this->localize_common( 'edz-mc-submit-form' );
		if ( wp_script_is( 'edz-mc-recaptcha', 'registered' ) ) {
			wp_enqueue_script( 'edz-mc-recaptcha' );
		}
	}
}
