<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class EDZ_MC_Widget_Submit_Form extends Widget_Base {

	public function get_name() {
		return 'edz-submit-event-form';
	}

	public function get_title() {
		return __( 'Submit History Event Form', 'edz-memory-calendar' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return array( 'edz-memory-calendar' );
	}

	public function get_keywords() {
		return array( 'form', 'submit', 'history', 'events', 'memory' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array( 'label' => __( 'Form', 'edz-memory-calendar' ) )
		);

		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Heading', 'edz-memory-calendar' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Share a History Event', 'edz-memory-calendar' ),
			)
		);

		$this->add_control(
			'notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Submissions are held for editor review and only appear on the calendar once approved. Configure spam protection under History Events → Settings.', 'edz-memory-calendar' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
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
					'{{WRAPPER}} .edz-mc-submit-form-wrap' => '--edz-mc-accent: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		echo EDZ_MC_Render::submit_form( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'heading' => $settings['heading'],
			)
		);
	}
}
