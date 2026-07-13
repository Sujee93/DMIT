<?php
/**
 * Registers the "Memory Calendar" Elementor widget category and the four
 * widgets themselves. Only loaded once Elementor is confirmed active
 * (see maybe_load_elementor() in the main plugin file).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DMIT_MC_Elementor {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'dmit-memory-calendar',
			array(
				'title' => __( 'Memory Calendar', 'dmit-memory-calendar' ),
				'icon'  => 'eicon-calendar',
			)
		);
	}

	public function register_widgets( $widgets_manager ) {
		require_once DMIT_MC_PATH . 'elementor/widgets/class-widget-calendar.php';
		require_once DMIT_MC_PATH . 'elementor/widgets/class-widget-onthisday.php';
		require_once DMIT_MC_PATH . 'elementor/widgets/class-widget-list.php';
		require_once DMIT_MC_PATH . 'elementor/widgets/class-widget-submit-form.php';

		$widgets_manager->register( new \DMIT_MC_Widget_Calendar() );
		$widgets_manager->register( new \DMIT_MC_Widget_On_This_Day() );
		$widgets_manager->register( new \DMIT_MC_Widget_List() );
		$widgets_manager->register( new \DMIT_MC_Widget_Submit_Form() );
	}
}
