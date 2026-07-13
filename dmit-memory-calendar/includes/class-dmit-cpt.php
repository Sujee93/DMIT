<?php
/**
 * Registers the "History Event" custom post type, its category taxonomy,
 * and the post-meta fields every event carries (date, source link,
 * guest-submitter info). This file only defines data shape — admin UI
 * lives in class-dmit-admin.php, front-end queries in class-dmit-rest.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DMIT_MC_CPT_Handler {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'init', array( $this, 'register_meta' ) );
	}

	public function register_post_type() {
		$labels = array(
			'name'                  => __( 'History Events', 'dmit-memory-calendar' ),
			'singular_name'         => __( 'History Event', 'dmit-memory-calendar' ),
			'add_new'               => __( 'Add New Event', 'dmit-memory-calendar' ),
			'add_new_item'          => __( 'Add New History Event', 'dmit-memory-calendar' ),
			'edit_item'             => __( 'Edit History Event', 'dmit-memory-calendar' ),
			'new_item'              => __( 'New History Event', 'dmit-memory-calendar' ),
			'view_item'             => __( 'View History Event', 'dmit-memory-calendar' ),
			'search_items'          => __( 'Search History Events', 'dmit-memory-calendar' ),
			'not_found'             => __( 'No history events found.', 'dmit-memory-calendar' ),
			'not_found_in_trash'    => __( 'No history events found in Trash.', 'dmit-memory-calendar' ),
			'all_items'             => __( 'All Events', 'dmit-memory-calendar' ),
			'menu_name'             => __( 'Memory Calendar', 'dmit-memory-calendar' ),
			'name_admin_bar'        => __( 'History Event', 'dmit-memory-calendar' ),
		);

		register_post_type(
			DMIT_MC_CPT,
			array(
				'labels'             => $labels,
				'public'             => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'menu_icon'          => 'dashicons-calendar-alt',
				'menu_position'      => 20,
				'show_in_rest'       => true,
				'rest_base'          => 'dmit-history-events',
				'has_archive'        => 'history-events',
				'rewrite'            => array( 'slug' => 'history-event' ),
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions', 'custom-fields' ),
				'taxonomies'         => array( DMIT_MC_TAXONOMY ),
			)
		);
	}

	public function register_taxonomy() {
		$labels = array(
			'name'          => __( 'Event Categories', 'dmit-memory-calendar' ),
			'singular_name' => __( 'Event Category', 'dmit-memory-calendar' ),
			'search_items'  => __( 'Search Categories', 'dmit-memory-calendar' ),
			'all_items'     => __( 'All Categories', 'dmit-memory-calendar' ),
			'edit_item'     => __( 'Edit Category', 'dmit-memory-calendar' ),
			'update_item'   => __( 'Update Category', 'dmit-memory-calendar' ),
			'add_new_item'  => __( 'Add New Category', 'dmit-memory-calendar' ),
			'new_item_name' => __( 'New Category Name', 'dmit-memory-calendar' ),
			'menu_name'     => __( 'Categories', 'dmit-memory-calendar' ),
		);

		register_taxonomy(
			DMIT_MC_TAXONOMY,
			array( DMIT_MC_CPT ),
			array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'history-event-category' ),
			)
		);
	}

	/**
	 * _dmit_event_date drives everything: the calendar groups events by
	 * the month+day portion of this field and ignores the year, so the
	 * same day can surface entries from many different years.
	 */
	public function register_meta() {
		register_post_meta(
			DMIT_MC_CPT,
			'_dmit_event_date',
			array(
				'type'              => 'string',
				'description'       => __( 'The historical date of the event (YYYY-MM-DD).', 'dmit-memory-calendar' ),
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_date' ),
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_post_meta(
			DMIT_MC_CPT,
			'_dmit_source_link',
			array(
				'type'              => 'string',
				'description'       => __( 'Optional reference/source URL for the event.', 'dmit-memory-calendar' ),
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'esc_url_raw',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		// Guest-submission metadata. Not exposed via REST (contains an email address).
		register_post_meta(
			DMIT_MC_CPT,
			'_dmit_submitter_name',
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => false,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_post_meta(
			DMIT_MC_CPT,
			'_dmit_submitter_email',
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => false,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_post_meta(
			DMIT_MC_CPT,
			'_dmit_is_guest_submission',
			array(
				'type'          => 'boolean',
				'single'        => true,
				'show_in_rest'  => false,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	/**
	 * Accepts Y-m-d and normalises anything parseable to that shape;
	 * returns '' rather than throwing so the meta box can flag the error.
	 */
	public function sanitize_date( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}
		$timestamp = strtotime( $value );
		if ( false === $timestamp ) {
			return '';
		}
		return gmdate( 'Y-m-d', $timestamp );
	}
}
