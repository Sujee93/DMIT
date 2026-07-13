<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class DMIT_MC_Widget_List extends Widget_Base {

	public function get_name() {
		return 'dmit-event-list';
	}

	public function get_title() {
		return __( 'History Event List', 'dmit-memory-calendar' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return array( 'dmit-memory-calendar' );
	}

	public function get_keywords() {
		return array( 'list', 'history', 'events', 'memory' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array( 'label' => __( 'List', 'dmit-memory-calendar' ) )
		);

		$this->add_control(
			'category',
			array(
				'label'   => __( 'Category', 'dmit-memory-calendar' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => '',
				'options' => $this->get_category_options(),
			)
		);

		$this->add_control(
			'show_filters',
			array(
				'label'     => __( 'Show search & category filters', 'dmit-memory-calendar' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Show', 'dmit-memory-calendar' ),
				'label_off' => __( 'Hide', 'dmit-memory-calendar' ),
			)
		);

		$this->add_control(
			'per_page',
			array(
				'label'   => __( 'Events per page', 'dmit-memory-calendar' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 50,
				'default' => 12,
			)
		);

		$this->end_controls_section();
	}

	private function get_category_options() {
		$options = array( '' => __( 'All categories', 'dmit-memory-calendar' ) );
		$terms   = get_terms( array( 'taxonomy' => DMIT_MC_TAXONOMY, 'hide_empty' => false ) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}
		return $options;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		echo DMIT_MC_Render::list_view( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'category'     => $settings['category'],
				'show_filters' => 'yes' === $settings['show_filters'],
				'per_page'     => (int) $settings['per_page'],
			)
		);
	}
}
