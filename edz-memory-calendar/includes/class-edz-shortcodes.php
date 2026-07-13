<?php
/**
 * Plain shortcodes so the calendar, timeline, list and submission form
 * also work outside Elementor (Classic editor, Gutenberg, theme templates).
 * These call the exact same EDZ_MC_Render methods the Elementor widgets use.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDZ_MC_Shortcodes {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'edz_memory_calendar', array( $this, 'calendar' ) );
		add_shortcode( 'edz_on_this_day', array( $this, 'on_this_day' ) );
		add_shortcode( 'edz_event_list', array( $this, 'event_list' ) );
		add_shortcode( 'edz_submit_event_form', array( $this, 'submit_form' ) );
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
			'edz_memory_calendar'
		);

		return EDZ_MC_Render::calendar(
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
				'heading' => __( 'On This Day', 'edz-memory-calendar' ),
				'month'   => '',
				'day'     => '',
			),
			$atts,
			'edz_on_this_day'
		);

		return EDZ_MC_Render::timeline(
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
			'edz_event_list'
		);

		return EDZ_MC_Render::list_view(
			array(
				'category' => sanitize_text_field( $atts['category'] ),
				'per_page' => (int) $atts['per_page'],
			)
		);
	}

	public function submit_form( $atts ) {
		$atts = shortcode_atts(
			array(
				'heading' => __( 'Share a History Event', 'edz-memory-calendar' ),
			),
			$atts,
			'edz_submit_event_form'
		);

		return EDZ_MC_Render::submit_form(
			array(
				'heading' => sanitize_text_field( $atts['heading'] ),
			)
		);
	}
}
