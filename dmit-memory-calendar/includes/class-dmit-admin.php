<?php
/**
 * wp-admin experience for History Events: the date/source meta box,
 * list-table columns (date, category, submitter), and a "Pending
 * Submissions" filter so moderators can find guest-submitted events fast.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DMIT_MC_Admin {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_' . DMIT_MC_CPT, array( $this, 'save_meta_box' ) );

		add_filter( 'manage_' . DMIT_MC_CPT . '_posts_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_' . DMIT_MC_CPT . '_posts_custom_column', array( $this, 'render_columns' ), 10, 2 );
		add_filter( 'manage_edit-' . DMIT_MC_CPT . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'sort_by_event_date' ) );

		add_action( 'restrict_manage_posts', array( $this, 'render_moderation_filter' ) );
		add_action( 'pre_get_posts', array( $this, 'apply_moderation_filter' ) );

		add_action( 'admin_notices', array( $this, 'pending_submissions_notice' ) );
	}

	public function add_meta_boxes() {
		add_meta_box(
			'dmit_mc_event_details',
			__( 'Event Details', 'dmit-memory-calendar' ),
			array( $this, 'render_meta_box' ),
			DMIT_MC_CPT,
			'side',
			'high'
		);

		add_meta_box(
			'dmit_mc_submitter_details',
			__( 'Submission Info', 'dmit-memory-calendar' ),
			array( $this, 'render_submitter_box' ),
			DMIT_MC_CPT,
			'side',
			'default'
		);
	}

	public function render_meta_box( $post ) {
		wp_nonce_field( 'dmit_mc_save_event_details', 'dmit_mc_event_details_nonce' );

		$date        = get_post_meta( $post->ID, '_dmit_event_date', true );
		$source_link = get_post_meta( $post->ID, '_dmit_source_link', true );
		?>
		<p>
			<label for="dmit_mc_event_date"><strong><?php esc_html_e( 'Event date', 'dmit-memory-calendar' ); ?></strong></label><br />
			<input type="date" id="dmit_mc_event_date" name="dmit_mc_event_date" value="<?php echo esc_attr( $date ); ?>" style="width:100%;" required />
			<span class="description"><?php esc_html_e( 'The calendar matches by month + day, so events from any past year appear on the same box every year.', 'dmit-memory-calendar' ); ?></span>
		</p>
		<p>
			<label for="dmit_mc_source_link"><strong><?php esc_html_e( 'Source / reference link', 'dmit-memory-calendar' ); ?></strong></label><br />
			<input type="url" id="dmit_mc_source_link" name="dmit_mc_source_link" value="<?php echo esc_attr( $source_link ); ?>" placeholder="https://" style="width:100%;" />
		</p>
		<?php
	}

	public function render_submitter_box( $post ) {
		$is_guest = get_post_meta( $post->ID, '_dmit_is_guest_submission', true );
		if ( ! $is_guest ) {
			echo '<p>' . esc_html__( 'Added directly by an editor/admin.', 'dmit-memory-calendar' ) . '</p>';
			return;
		}
		$name  = get_post_meta( $post->ID, '_dmit_submitter_name', true );
		$email = get_post_meta( $post->ID, '_dmit_submitter_email', true );
		?>
		<p><strong><?php esc_html_e( 'Submitted by a visitor', 'dmit-memory-calendar' ); ?></strong></p>
		<p>
			<?php esc_html_e( 'Name:', 'dmit-memory-calendar' ); ?> <?php echo esc_html( $name ? $name : __( '(not provided)', 'dmit-memory-calendar' ) ); ?><br />
			<?php esc_html_e( 'Email:', 'dmit-memory-calendar' ); ?> <?php echo $email ? esc_html( $email ) : esc_html__( '(not provided)', 'dmit-memory-calendar' ); ?>
		</p>
		<p class="description"><?php esc_html_e( 'Review the details, then Publish to approve or move to Trash to reject.', 'dmit-memory-calendar' ); ?></p>
		<?php
	}

	public function save_meta_box( $post_id ) {
		if ( ! isset( $_POST['dmit_mc_event_details_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dmit_mc_event_details_nonce'] ) ), 'dmit_mc_save_event_details' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['dmit_mc_event_date'] ) ) {
			$date = sanitize_text_field( wp_unslash( $_POST['dmit_mc_event_date'] ) );
			update_post_meta( $post_id, '_dmit_event_date', DMIT_MC_CPT_Handler::instance()->sanitize_date( $date ) );
		}

		if ( isset( $_POST['dmit_mc_source_link'] ) ) {
			update_post_meta( $post_id, '_dmit_source_link', esc_url_raw( wp_unslash( $_POST['dmit_mc_source_link'] ) ) );
		}
	}

	public function add_columns( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['dmit_event_date'] = __( 'Event Date', 'dmit-memory-calendar' );
				$new['dmit_submitter']  = __( 'Submitted By', 'dmit-memory-calendar' );
			}
		}
		return $new;
	}

	public function render_columns( $column, $post_id ) {
		if ( 'dmit_event_date' === $column ) {
			$date = get_post_meta( $post_id, '_dmit_event_date', true );
			echo $date ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) : '&#8212;';
		}

		if ( 'dmit_submitter' === $column ) {
			$is_guest = get_post_meta( $post_id, '_dmit_is_guest_submission', true );
			if ( $is_guest ) {
				$name = get_post_meta( $post_id, '_dmit_submitter_name', true );
				echo '<span style="color:#b32d2e;font-weight:600;">' . esc_html__( 'Visitor submission', 'dmit-memory-calendar' ) . '</span>';
				if ( $name ) {
					echo '<br /><small>' . esc_html( $name ) . '</small>';
				}
			} else {
				$author = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );
				echo esc_html( $author );
			}
		}
	}

	public function sortable_columns( $columns ) {
		$columns['dmit_event_date'] = 'dmit_event_date';
		return $columns;
	}

	public function sort_by_event_date( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( 'dmit_event_date' === $query->get( 'orderby' ) ) {
			$query->set( 'meta_key', '_dmit_event_date' );
			$query->set( 'orderby', 'meta_value' );
		}
	}

	public function render_moderation_filter( $post_type ) {
		if ( DMIT_MC_CPT !== $post_type ) {
			return;
		}
		$selected = isset( $_GET['dmit_moderation'] ) ? sanitize_text_field( wp_unslash( $_GET['dmit_moderation'] ) ) : '';
		?>
		<select name="dmit_moderation">
			<option value=""><?php esc_html_e( 'All submissions', 'dmit-memory-calendar' ); ?></option>
			<option value="guest" <?php selected( $selected, 'guest' ); ?>><?php esc_html_e( 'Visitor submissions only', 'dmit-memory-calendar' ); ?></option>
		</select>
		<?php
	}

	public function apply_moderation_filter( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( $query->get( 'post_type' ) !== DMIT_MC_CPT ) {
			return;
		}
		if ( isset( $_GET['dmit_moderation'] ) && 'guest' === $_GET['dmit_moderation'] ) {
			$query->set(
				'meta_query',
				array(
					array(
						'key'   => '_dmit_is_guest_submission',
						'value' => '1',
					),
				)
			);
		}
	}

	/**
	 * Nudges admins toward the moderation queue whenever guest submissions
	 * are waiting, without being noisy on every single wp-admin screen.
	 */
	public function pending_submissions_notice() {
		$screen = get_current_screen();
		if ( ! $screen || DMIT_MC_CPT !== $screen->post_type || 'edit' !== $screen->base ) {
			return;
		}

		$pending = get_posts(
			array(
				'post_type'      => DMIT_MC_CPT,
				'post_status'    => 'pending',
				'meta_key'       => '_dmit_is_guest_submission',
				'meta_value'     => '1',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		if ( empty( $pending ) ) {
			return;
		}

		$count = wp_count_posts( DMIT_MC_CPT );
		$url   = add_query_arg(
			array(
				'post_type'       => DMIT_MC_CPT,
				'post_status'     => 'pending',
				'dmit_moderation' => 'guest',
			),
			admin_url( 'edit.php' )
		);
		?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					/* translators: %d: number of pending visitor submissions, %s: link to filtered list */
					esc_html__( '%1$d visitor-submitted history event(s) are waiting for review. %2$s', 'dmit-memory-calendar' ),
					intval( $count->pending ),
					'<a href="' . esc_url( $url ) . '">' . esc_html__( 'Review pending submissions', 'dmit-memory-calendar' ) . '</a>'
				);
				?>
			</p>
		</div>
		<?php
	}
}
