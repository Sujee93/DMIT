<?php
/**
 * Registers the "History Event" custom post type, its category taxonomy,
 * and the post-meta fields every event carries (date, source link,
 * guest-submitter info). This file only defines data shape — admin UI
 * lives in class-edz-admin.php, front-end queries in class-edz-rest.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDZ_MC_CPT_Handler {

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
			'name'                  => __( 'History Events', 'edz-memory-calendar' ),
			'singular_name'         => __( 'History Event', 'edz-memory-calendar' ),
			'add_new'               => __( 'Add New Event', 'edz-memory-calendar' ),
			'add_new_item'          => __( 'Add New History Event', 'edz-memory-calendar' ),
			'edit_item'             => __( 'Edit History Event', 'edz-memory-calendar' ),
			'new_item'              => __( 'New History Event', 'edz-memory-calendar' ),
			'view_item'             => __( 'View History Event', 'edz-memory-calendar' ),
			'search_items'          => __( 'Search History Events', 'edz-memory-calendar' ),
			'not_found'             => __( 'No history events found.', 'edz-memory-calendar' ),
			'not_found_in_trash'    => __( 'No history events found in Trash.', 'edz-memory-calendar' ),
			'all_items'             => __( 'All Events', 'edz-memory-calendar' ),
			'menu_name'             => __( 'Memory Calendar', 'edz-memory-calendar' ),
			'name_admin_bar'        => __( 'History Event', 'edz-memory-calendar' ),
		);

		register_post_type(
			EDZ_MC_CPT,
			array(
				'labels'             => $labels,
				'public'             => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'menu_icon'          => 'dashicons-calendar-alt',
				'menu_position'      => 20,
				'show_in_rest'       => true,
				'rest_base'          => 'edz-history-events',
				'has_archive'        => 'history-events',
				'rewrite'            => array( 'slug' => 'history-event' ),
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions', 'custom-fields' ),
				'taxonomies'         => array( EDZ_MC_TAXONOMY ),
			)
		);
	}

	public function register_taxonomy() {
		$labels = array(
			'name'          => __( 'Event Categories', 'edz-memory-calendar' ),
			'singular_name' => __( 'Event Category', 'edz-memory-calendar' ),
			'search_items'  => __( 'Search Categories', 'edz-memory-calendar' ),
			'all_items'     => __( 'All Categories', 'edz-memory-calendar' ),
			'edit_item'     => __( 'Edit Category', 'edz-memory-calendar' ),
			'update_item'   => __( 'Update Category', 'edz-memory-calendar' ),
			'add_new_item'  => __( 'Add New Category', 'edz-memory-calendar' ),
			'new_item_name' => __( 'New Category Name', 'edz-memory-calendar' ),
			'menu_name'     => __( 'Categories', 'edz-memory-calendar' ),
		);

		register_taxonomy(
			EDZ_MC_TAXONOMY,
			array( EDZ_MC_CPT ),
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
	 * _edz_event_date drives everything: the calendar groups events by
	 * the month+day portion of this field and ignores the year, so the
	 * same day can surface entries from many different years.
	 */
	public function register_meta() {
		register_post_meta(
			EDZ_MC_CPT,
			'_edz_event_date',
			array(
				'type'              => 'string',
				'description'       => __( 'The historical date of the event (YYYY-MM-DD).', 'edz-memory-calendar' ),
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( $this, 'sanitize_date' ),
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_post_meta(
			EDZ_MC_CPT,
			'_edz_source_link',
			array(
				'type'              => 'string',
				'description'       => __( 'Optional reference/source URL for the event.', 'edz-memory-calendar' ),
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
			EDZ_MC_CPT,
			'_edz_submitter_name',
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
			EDZ_MC_CPT,
			'_edz_submitter_email',
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
			EDZ_MC_CPT,
			'_edz_is_guest_submission',
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
