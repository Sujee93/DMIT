<?php
/**
 * Public REST routes powering the front end. Every route reads only
 * `post_status = publish` events, matched on the month/day portion of
 * `_edz_event_date` while ignoring the year — that's what lets a single
 * calendar day surface entries from many different years, and what lets
 * the "On This Day" widget find everything that ever happened on today's
 * date regardless of when it was published.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDZ_MC_REST {

	const NAMESPACE_ = 'edz-mc/v1';

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route(
			self::NAMESPACE_,
			'/month',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_month' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'month' => array(
						'required'          => true,
						'validate_callback' => array( $this, 'validate_month' ),
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE_,
			'/day',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_day' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'month' => array( 'required' => true, 'validate_callback' => array( $this, 'validate_month' ) ),
					'day'   => array( 'required' => true, 'validate_callback' => array( $this, 'validate_day' ) ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE_,
			'/on-this-day',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_on_this_day' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			self::NAMESPACE_,
			'/list',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_list' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function validate_month( $value ) {
		$value = (int) $value;
		return $value >= 1 && $value <= 12;
	}

	public function validate_day( $value ) {
		$value = (int) $value;
		return $value >= 1 && $value <= 31;
	}

	/**
	 * All events whose _edz_event_date falls in the given month (any
	 * year), grouped by day-of-month for the calendar grid.
	 */
	public function get_month( $request ) {
		$month = (int) $request->get_param( 'month' );

		$posts = get_posts( $this->base_query_args( array(
			'meta_query' => array(
				array(
					'key'     => '_edz_event_date',
					'value'   => sprintf( '^[0-9]{4}-%02d-[0-9]{2}$', $month ),
					'compare' => 'REGEXP',
				),
			),
		) ) );

		$days = array();
		foreach ( $posts as $post ) {
			$event = $this->format_event( $post );
			$day   = (int) gmdate( 'j', strtotime( $event['date'] ) );
			if ( ! isset( $days[ $day ] ) ) {
				$days[ $day ] = array();
			}
			$days[ $day ][] = $event;
		}

		return rest_ensure_response( array( 'month' => $month, 'days' => $days ) );
	}

	public function get_day( $request ) {
		$month = (int) $request->get_param( 'month' );
		$day   = (int) $request->get_param( 'day' );

		return rest_ensure_response( $this->query_month_day( $month, $day ) );
	}

	/**
	 * Defaults to the site's current date (in the WordPress timezone) so
	 * the homepage widget can call it with no params.
	 */
	public function get_on_this_day( $request ) {
		$month = $request->get_param( 'month' );
		$day   = $request->get_param( 'day' );

		if ( ! $month || ! $day ) {
			$now   = current_datetime();
			$month = (int) $now->format( 'n' );
			$day   = (int) $now->format( 'j' );
		}

		$events = $this->query_month_day( (int) $month, (int) $day );

		// Chronological, oldest year first — reads like a timeline.
		usort( $events, function ( $a, $b ) {
			return $a['year'] <=> $b['year'];
		} );

		return rest_ensure_response( array(
			'month'  => (int) $month,
			'day'    => (int) $day,
			'events' => $events,
		) );
	}

	private function query_month_day( $month, $day ) {
		$posts = get_posts( $this->base_query_args( array(
			'meta_query' => array(
				array(
					'key'     => '_edz_event_date',
					'value'   => sprintf( '^[0-9]{4}-%02d-%02d$', $month, $day ),
					'compare' => 'REGEXP',
				),
			),
		) ) );

		return array_map( array( $this, 'format_event' ), $posts );
	}

	/**
	 * Paginated, filterable list (category + free-text search across
	 * title/year) for the list-view toggle and the standalone list widget.
	 */
	public function get_list( $request ) {
		$page     = max( 1, (int) $request->get_param( 'page' ) );
		$per_page = min( 50, max( 1, (int) ( $request->get_param( 'per_page' ) ?: 12 ) ) );
		$category = sanitize_text_field( (string) $request->get_param( 'category' ) );
		$search   = sanitize_text_field( (string) $request->get_param( 'search' ) );

		$args = $this->base_query_args( array(
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'meta_key'       => '_edz_event_date',
			'orderby'        => 'meta_value',
			'order'          => 'DESC',
		) );

		if ( $category ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => EDZ_MC_TAXONOMY,
					'field'    => 'slug',
					'terms'    => $category,
				),
			);
		}

		if ( $search ) {
			$args['s'] = $search;
		}

		$query = new WP_Query( $args );

		return rest_ensure_response( array(
			'page'        => $page,
			'per_page'    => $per_page,
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
			'events'      => array_map( array( $this, 'format_event' ), $query->posts ),
		) );
	}

	private function base_query_args( $overrides = array() ) {
		return array_merge(
			array(
				'post_type'      => EDZ_MC_CPT,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'no_found_rows'  => true,
			),
			$overrides
		);
	}

	private function format_event( $post ) {
		$date  = get_post_meta( $post->ID, '_edz_event_date', true );
		$terms = get_the_terms( $post->ID, EDZ_MC_TAXONOMY );
		$terms = is_array( $terms ) ? wp_list_pluck( $terms, 'name', 'slug' ) : array();

		return array(
			'id'          => $post->ID,
			'title'       => get_the_title( $post ),
			'excerpt'     => wp_strip_all_tags( get_the_excerpt( $post ) ),
			'description' => apply_filters( 'the_content', $post->post_content ),
			'date'        => $date,
			'year'        => $date ? (int) gmdate( 'Y', strtotime( $date ) ) : null,
			'month'       => $date ? (int) gmdate( 'n', strtotime( $date ) ) : null,
			'day'         => $date ? (int) gmdate( 'j', strtotime( $date ) ) : null,
			'permalink'   => get_permalink( $post ),
			'thumbnail'   => get_the_post_thumbnail_url( $post, 'medium' ),
			'source_link' => get_post_meta( $post->ID, '_edz_source_link', true ),
			'categories'  => $terms,
		);
	}
}
