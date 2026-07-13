<?php
/**
 * Handles the public "submit a history event" form: honeypot + optional
 * reCAPTCHA v3 + a light per-IP rate limit, then always inserts the post
 * as `pending` — nothing a visitor submits ever goes live without an
 * editor approving it in wp-admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DMIT_MC_Submission {

	const HONEYPOT_FIELD = 'dmit_mc_website';
	const RATE_LIMIT      = 5; // submissions
	const RATE_WINDOW     = HOUR_IN_SECONDS;

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_ajax_dmit_mc_submit_event', array( $this, 'handle_submission' ) );
		add_action( 'wp_ajax_nopriv_dmit_mc_submit_event', array( $this, 'handle_submission' ) );
	}

	public function handle_submission() {
		check_ajax_referer( 'dmit_mc_submit_event', 'nonce' );

		if ( DMIT_MC_Settings::get( 'require_login_to_submit' ) && ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Please log in to submit a history event.', 'dmit-memory-calendar' ) ), 401 );
		}

		// Honeypot: a real visitor never fills this hidden field in.
		if ( ! empty( $_POST[ self::HONEYPOT_FIELD ] ) ) {
			wp_send_json_success( array( 'message' => __( 'Thank you! Your event has been submitted and is awaiting review.', 'dmit-memory-calendar' ) ) );
		}

		if ( ! $this->check_rate_limit() ) {
			wp_send_json_error( array( 'message' => __( 'You have submitted several events recently. Please try again later.', 'dmit-memory-calendar' ) ), 429 );
		}

		if ( DMIT_MC_Settings::get( 'enable_recaptcha' ) && ! $this->verify_recaptcha() ) {
			wp_send_json_error( array( 'message' => __( 'Spam check failed. Please try again.', 'dmit-memory-calendar' ) ), 400 );
		}

		$title       = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$event_date  = isset( $_POST['event_date'] ) ? sanitize_text_field( wp_unslash( $_POST['event_date'] ) ) : '';
		$description = isset( $_POST['description'] ) ? wp_kses_post( wp_unslash( $_POST['description'] ) ) : '';
		$source_link = isset( $_POST['source_link'] ) ? esc_url_raw( wp_unslash( $_POST['source_link'] ) ) : '';
		$category    = isset( $_POST['category'] ) ? sanitize_key( wp_unslash( $_POST['category'] ) ) : '';
		$sub_name    = isset( $_POST['submitter_name'] ) ? sanitize_text_field( wp_unslash( $_POST['submitter_name'] ) ) : '';
		$sub_email   = isset( $_POST['submitter_email'] ) ? sanitize_email( wp_unslash( $_POST['submitter_email'] ) ) : '';

		$errors = array();
		if ( '' === $title ) {
			$errors[] = __( 'A title is required.', 'dmit-memory-calendar' );
		}
		$event_date = DMIT_MC_CPT_Handler::instance()->sanitize_date( $event_date );
		if ( '' === $event_date ) {
			$errors[] = __( 'A valid event date is required.', 'dmit-memory-calendar' );
		}
		if ( strtotime( $event_date ) > current_time( 'timestamp' ) ) {
			$errors[] = __( 'The event date cannot be in the future.', 'dmit-memory-calendar' );
		}
		if ( '' === $description ) {
			$errors[] = __( 'A short description is required.', 'dmit-memory-calendar' );
		}
		if ( ! is_user_logged_in() && '' === $sub_email ) {
			$errors[] = __( 'Please provide an email address so we can follow up if needed.', 'dmit-memory-calendar' );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( array( 'message' => implode( ' ', $errors ) ), 400 );
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => DMIT_MC_CPT,
				'post_status'  => 'pending',
				'post_title'   => $title,
				'post_content' => $description,
				'post_author'  => get_current_user_id(),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( array( 'message' => $post_id->get_error_message() ), 500 );
		}

		update_post_meta( $post_id, '_dmit_event_date', $event_date );
		update_post_meta( $post_id, '_dmit_is_guest_submission', 1 );
		if ( $source_link ) {
			update_post_meta( $post_id, '_dmit_source_link', $source_link );
		}
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			update_post_meta( $post_id, '_dmit_submitter_name', $user->display_name );
			update_post_meta( $post_id, '_dmit_submitter_email', $user->user_email );
		} else {
			update_post_meta( $post_id, '_dmit_submitter_name', $sub_name );
			update_post_meta( $post_id, '_dmit_submitter_email', $sub_email );
		}

		if ( $category && term_exists( $category, DMIT_MC_TAXONOMY ) ) {
			wp_set_object_terms( $post_id, array( $category ), DMIT_MC_TAXONOMY );
		}

		$this->maybe_attach_image( $post_id );
		$this->notify_admin( $post_id, $title );

		wp_send_json_success( array( 'message' => __( 'Thank you! Your event has been submitted and is awaiting review.', 'dmit-memory-calendar' ) ) );
	}

	private function check_rate_limit() {
		$ip  = $this->get_ip();
		$key = 'dmit_mc_rl_' . md5( $ip );
		$count = (int) get_transient( $key );
		if ( $count >= self::RATE_LIMIT ) {
			return false;
		}
		set_transient( $key, $count + 1, self::RATE_WINDOW );
		return true;
	}

	private function get_ip() {
		return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
	}

	private function verify_recaptcha() {
		$token = isset( $_POST['recaptcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_token'] ) ) : '';
		$secret = DMIT_MC_Settings::get( 'recaptcha_secret_key' );
		if ( ! $token || ! $secret ) {
			return false;
		}

		$response = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'body'    => array(
					'secret'   => $secret,
					'response' => $token,
					'remoteip' => $this->get_ip(),
				),
				'timeout' => 10,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		return ! empty( $body['success'] ) && ( ! isset( $body['score'] ) || $body['score'] >= 0.5 );
	}

	private function maybe_attach_image( $post_id ) {
		if ( empty( $_FILES['featured_image'] ) || empty( $_FILES['featured_image']['name'] ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$allowed = array( 'jpg|jpeg|jpe' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif' );

		add_filter( 'upload_mimes', function ( $mimes ) use ( $allowed ) {
			return array_merge( $mimes, $allowed );
		} );

		$attachment_id = media_handle_upload( 'featured_image', $post_id );

		if ( ! is_wp_error( $attachment_id ) ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	private function notify_admin( $post_id, $title ) {
		$to = DMIT_MC_Settings::get( 'notification_email' ) ?: get_option( 'admin_email' );
		if ( ! $to ) {
			return;
		}

		$edit_link = admin_url( 'post.php?post=' . $post_id . '&action=edit' );

		$subject = sprintf(
			/* translators: %s: site name */
			__( '[%s] New history event awaiting review', 'dmit-memory-calendar' ),
			get_bloginfo( 'name' )
		);

		$message = sprintf(
			/* translators: 1: event title, 2: edit link */
			__( "A visitor submitted a new history event: \"%1\$s\".\n\nReview it here: %2\$s", 'dmit-memory-calendar' ),
			$title,
			$edit_link
		);

		wp_mail( $to, $subject, $message );
	}
}
