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

class DMIT_MC_Assets {

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

	public function register_assets() {
		// FullCalendar v6's "global" bundle injects its own base CSS at runtime — no separate stylesheet to enqueue.
		wp_register_script( 'dmit-mc-fullcalendar', DMIT_MC_URL . 'assets/lib/fullcalendar/fullcalendar.min.js', array(), DMIT_MC_VERSION, true );

		wp_register_style( 'dmit-mc-calendar', DMIT_MC_URL . 'assets/css/dmit-calendar.css', array(), DMIT_MC_VERSION );
		wp_register_script( 'dmit-mc-calendar', DMIT_MC_URL . 'assets/js/dmit-calendar.js', array( 'dmit-mc-fullcalendar' ), DMIT_MC_VERSION, true );

		wp_register_style( 'dmit-mc-timeline', DMIT_MC_URL . 'assets/css/dmit-timeline.css', array(), DMIT_MC_VERSION );
		wp_register_script( 'dmit-mc-timeline', DMIT_MC_URL . 'assets/js/dmit-timeline.js', array(), DMIT_MC_VERSION, true );

		wp_register_style( 'dmit-mc-list', DMIT_MC_URL . 'assets/css/dmit-list.css', array(), DMIT_MC_VERSION );
		wp_register_script( 'dmit-mc-list', DMIT_MC_URL . 'assets/js/dmit-list.js', array(), DMIT_MC_VERSION, true );

		wp_register_style( 'dmit-mc-submit-form', DMIT_MC_URL . 'assets/css/dmit-submit-form.css', array(), DMIT_MC_VERSION );
		wp_register_script( 'dmit-mc-submit-form', DMIT_MC_URL . 'assets/js/dmit-submit-form.js', array(), DMIT_MC_VERSION, true );

		if ( DMIT_MC_Settings::get( 'enable_recaptcha' ) && DMIT_MC_Settings::get( 'recaptcha_site_key' ) ) {
			wp_register_script(
				'dmit-mc-recaptcha',
				'https://www.google.com/recaptcha/api.js?render=' . rawurlencode( DMIT_MC_Settings::get( 'recaptcha_site_key' ) ),
				array(),
				null,
				true
			);
		}
	}

	private function localize_common( $handle ) {
		wp_localize_script(
			$handle,
			'dmitMC',
			array(
				'restUrl'       => esc_url_raw( rest_url( DMIT_MC_REST::NAMESPACE_ ) ),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'submitNonce'   => wp_create_nonce( 'dmit_mc_submit_event' ),
				'accentColor'   => DMIT_MC_Settings::get( 'accent_color', '#2563EB' ),
				'todayColor'    => DMIT_MC_Settings::get( 'today_color', '#F59E0B' ),
				'recaptchaSiteKey' => DMIT_MC_Settings::get( 'enable_recaptcha' ) ? DMIT_MC_Settings::get( 'recaptcha_site_key' ) : '',
				'monthNames'    => $this->i18n_month_names(),
				'dayNamesShort' => $this->i18n_day_names(),
				'locale'        => get_locale(),
				'i18n'          => array(
					'noEvents'      => __( 'No events on this day.', 'dmit-memory-calendar' ),
					'pickAnother'   => __( 'Choose another date to explore.', 'dmit-memory-calendar' ),
					'listView'      => __( 'List view', 'dmit-memory-calendar' ),
					'calendarView'  => __( 'Calendar view', 'dmit-memory-calendar' ),
					'loading'       => __( 'Loading…', 'dmit-memory-calendar' ),
					'today'         => __( 'Today', 'dmit-memory-calendar' ),
					'readMore'      => __( 'Read more', 'dmit-memory-calendar' ),
					'source'        => __( 'Source', 'dmit-memory-calendar' ),
					'submitError'   => __( 'Something went wrong. Please try again.', 'dmit-memory-calendar' ),
					'submitSuccess' => __( 'Thank you! Your event has been submitted and is awaiting review.', 'dmit-memory-calendar' ),
					'noResults'     => __( 'No events found.', 'dmit-memory-calendar' ),
					'prev'          => __( 'Previous', 'dmit-memory-calendar' ),
					'next'          => __( 'Next', 'dmit-memory-calendar' ),
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
		wp_enqueue_style( 'dmit-mc-calendar' );
		wp_enqueue_script( 'dmit-mc-fullcalendar' );
		wp_enqueue_script( 'dmit-mc-calendar' );
		$this->localize_common( 'dmit-mc-calendar' );
	}

	public function enqueue_timeline_assets() {
		wp_enqueue_style( 'dmit-mc-timeline' );
		wp_enqueue_script( 'dmit-mc-timeline' );
		$this->localize_common( 'dmit-mc-timeline' );
	}

	public function enqueue_list_assets() {
		wp_enqueue_style( 'dmit-mc-list' );
		wp_enqueue_script( 'dmit-mc-list' );
		$this->localize_common( 'dmit-mc-list' );
	}

	public function enqueue_submit_form_assets() {
		wp_enqueue_style( 'dmit-mc-submit-form' );
		wp_enqueue_script( 'dmit-mc-submit-form' );
		$this->localize_common( 'dmit-mc-submit-form' );
		if ( wp_script_is( 'dmit-mc-recaptcha', 'registered' ) ) {
			wp_enqueue_script( 'dmit-mc-recaptcha' );
		}
	}
}
