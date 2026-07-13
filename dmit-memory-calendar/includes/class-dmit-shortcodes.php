<?php
/**
 * Plain shortcodes so the calendar, timeline, list and submission form
 * also work outside Elementor (Classic editor, Gutenberg, theme templates).
 * These call the exact same DMIT_MC_Render methods the Elementor widgets use.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DMIT_MC_Shortcodes {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'dmit_memory_calendar', array( $this, 'calendar' ) );
		add_shortcode( 'dmit_on_this_day', array( $this, 'on_this_day' ) );
		add_shortcode( 'dmit_event_list', array( $this, 'event_list' ) );
		add_shortcode( 'dmit_submit_event_form', array( $this, 'submit_form' ) );
	}

	public function calendar( $atts ) {
		$atts = shortcode_atts(
			array(
				'view'    => 'calendar',
				'toggle'  => 'yes',
				'category' => '',
				'heading' => '',
			),
			$atts,
			'dmit_memory_calendar'
		);

		return DMIT_MC_Render::calendar(
			array(
				'default_view' => 'list' === $atts['view'] ? 'list' : 'calendar',
				'show_toggle'  => 'no' !== $atts['toggle'],
				'category'     => sanitize_text_field( $atts['category'] ),
				'heading'      => sanitize_text_field( $atts['heading'] ),
			)
		);
	}

	public function on_this_day( $atts ) {
		$atts = shortcode_atts(
			array(
				'heading' => __( 'On This Day', 'dmit-memory-calendar' ),
				'month'   => '',
				'day'     => '',
			),
			$atts,
			'dmit_on_this_day'
		);

		return DMIT_MC_Render::timeline(
			array(
				'heading' => sanitize_text_field( $atts['heading'] ),
				'month'   => sanitize_text_field( $atts['month'] ),
				'day'     => sanitize_text_field( $atts['day'] ),
			)
		);
	}

	public function event_list( $atts ) {
		$atts = shortcode_atts(
			array(
				'category' => '',
				'per_page' => 12,
			),
			$atts,
			'dmit_event_list'
		);

		return DMIT_MC_Render::list_view(
			array(
				'category' => sanitize_text_field( $atts['category'] ),
				'per_page' => (int) $atts['per_page'],
			)
		);
	}

	public function submit_form( $atts ) {
		$atts = shortcode_atts(
			array(
				'heading' => __( 'Share a History Event', 'dmit-memory-calendar' ),
			),
			$atts,
			'dmit_submit_event_form'
		);

		return DMIT_MC_Render::submit_form(
			array(
				'heading' => sanitize_text_field( $atts['heading'] ),
			)
		);
	}
}
