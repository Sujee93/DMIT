<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class EDZ_MC_Widget_Calendar extends Widget_Base {

	public function get_name() {
		return 'edz-memory-calendar';
	}

	public function get_title() {
		return __( 'Memory Calendar', 'edz-memory-calendar' );
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_categories() {
		return array( 'edz-memory-calendar' );
	}

	public function get_keywords() {
		return array( 'calendar', 'history', 'events', 'on this day', 'memory' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array( 'label' => __( 'Calendar', 'edz-memory-calendar' ) )
		);

		$this->add_control(
			'heading',
			array(
				'label'       => __( 'Heading', 'edz-memory-calendar' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Leave blank to hide', 'edz-memory-calendar' ),
			)
		);

		$this->add_control(
			'default_view',
			array(
				'label'   => __( 'Default view', 'edz-memory-calendar' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'calendar',
				'options' => array(
					'calendar' => __( 'Calendar', 'edz-memory-calendar' ),
					'list'     => __( 'List', 'edz-memory-calendar' ),
				),
			)
		);

		$this->add_control(
			'show_toggle',
			array(
				'label'        => __( 'Show calendar/list toggle', 'edz-memory-calendar' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Show', 'edz-memory-calendar' ),
				'label_off'    => __( 'Hide', 'edz-memory-calendar' ),
				'description'  => __( 'Off by default for a cleaner calendar-only view. Turn on if you want visitors to switch to a list right here (there is also a separate standalone "History Event List" widget).', 'edz-memory-calendar' ),
			)
		);

		$this->add_control(
			'category',
			array(
				'label'   => __( 'Filter list view by category', 'edz-memory-calendar' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => '',
				'options' => $this->get_category_options(),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			array(
				'label' => __( 'Colours', 'edz-memory-calendar' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => __( 'Accent colour', 'edz-memory-calendar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => EDZ_MC_Settings::get( 'accent_color', '#2563EB' ),
				'selectors' => array(
					'{{WRAPPER}} .edz-mc-calendar-widget' => '--edz-mc-accent: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'today_color',
			array(
				'label'     => __( '"Today" highlight', 'edz-memory-calendar' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => EDZ_MC_Settings::get( 'today_color', '#F59E0B' ),
				'selectors' => array(
					'{{WRAPPER}} .edz-mc-calendar-widget' => '--edz-mc-today: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function get_category_options() {
		$options = array( '' => __( 'All categories', 'edz-memory-calendar' ) );
		$terms   = get_terms( array( 'taxonomy' => EDZ_MC_TAXONOMY, 'hide_empty' => false ) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}
		return $options;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		echo EDZ_MC_Render::calendar( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'default_view' => $settings['default_view'],
				'show_toggle'  => 'yes' === $settings['show_toggle'],
				'category'     => $settings['category'],
				'heading'      => $settings['heading'],
			)
		);
	}
}
