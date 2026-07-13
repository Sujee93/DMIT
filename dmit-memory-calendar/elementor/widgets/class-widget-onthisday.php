<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Homepage "On This Day" widget — the dark minimal vertical timeline.
 * By default it always shows *today's* date (recalculated on every page
 * load), but editors can pin a fixed month/day for previews or evergreen
 * "on this day" landing pages.
 */
class DMIT_MC_Widget_On_This_Day extends Widget_Base {

	public function get_name() {
		return 'dmit-on-this-day';
	}

	public function get_title() {
		return __( 'On This Day', 'dmit-memory-calendar' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_categories() {
		return array( 'dmit-memory-calendar' );
	}

	public function get_keywords() {
		return array( 'timeline', 'history', 'on this day', 'memory' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array( 'label' => __( 'Timeline', 'dmit-memory-calendar' ) )
		);

		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Heading', 'dmit-memory-calendar' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'On This Day', 'dmit-memory-calendar' ),
			)
		);

		$this->add_control(
			'use_fixed_date',
			array(
				'label'     => __( 'Pin a specific date', 'dmit-memory-calendar' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'label_on'  => __( 'Fixed date', 'dmit-memory-calendar' ),
				'label_off' => __( "Always today", 'dmit-memory-calendar' ),
			)
		);

		$this->add_control(
			'fixed_month',
			array(
				'label'     => __( 'Month', 'dmit-memory-calendar' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 12,
				'condition' => array( 'use_fixed_date' => 'yes' ),
			)
		);

		$this->add_control(
			'fixed_day',
			array(
				'label'     => __( 'Day', 'dmit-memory-calendar' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 31,
				'condition' => array( 'use_fixed_date' => 'yes' ),
			)
		);

		$this->add_control(
			'empty_message',
			array(
				'label'   => __( 'Message when nothing happened this day', 'dmit-memory-calendar' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Nothing on record for today — check back another day.', 'dmit-memory-calendar' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			array(
				'label' => __( 'Colours', 'dmit-memory-calendar' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'year_color',
			array(
				'label'     => __( 'Year label colour', 'dmit-memory-calendar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F59E0B',
				'selectors' => array(
					'{{WRAPPER}} .rl-timeline' => '--rl-orange: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title colour', 'dmit-memory-calendar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F8FAFC',
				'selectors' => array(
					'{{WRAPPER}} .rl-timeline' => '--rl-title: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$month = '';
		$day   = '';
		if ( 'yes' === $settings['use_fixed_date'] ) {
			$month = $settings['fixed_month'];
			$day   = $settings['fixed_day'];
		}

		echo DMIT_MC_Render::timeline( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'heading'       => $settings['heading'],
				'empty_message' => $settings['empty_message'],
				'month'         => $month,
				'day'           => $day,
			)
		);
	}
}
