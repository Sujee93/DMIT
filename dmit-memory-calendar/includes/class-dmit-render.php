<?php
/**
 * Shared HTML builders used by both the plain shortcodes and the
 * Elementor widgets, so the two entry points never drift out of sync.
 * Each render_*() call enqueues only the assets it needs and prints a
 * container `<div>` with `data-*` attributes; the matching JS file in
 * assets/js/ reads those attributes on DOMContentLoaded and hydrates it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DMIT_MC_Render {

	private static $uid = 0;

	private static function next_id( $prefix ) {
		self::$uid++;
		return $prefix . '-' . self::$uid;
	}

	public static function calendar( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'default_view' => 'calendar', // calendar|list
			'show_toggle'  => true,
			'category'     => '',
			'heading'      => '',
		) );

		DMIT_MC_Assets::instance()->enqueue_calendar_assets();
		DMIT_MC_Assets::instance()->enqueue_list_assets();

		$id = self::next_id( 'dmit-mc-calendar' );

		ob_start();
		?>
		<div
			id="<?php echo esc_attr( $id ); ?>"
			class="dmit-mc-calendar-widget"
			data-default-view="<?php echo esc_attr( $args['default_view'] ); ?>"
			data-show-toggle="<?php echo esc_attr( $args['show_toggle'] ? '1' : '0' ); ?>"
			data-category="<?php echo esc_attr( $args['category'] ); ?>"
		>
			<?php if ( $args['heading'] ) : ?>
				<h2 class="dmit-mc-widget-heading"><?php echo esc_html( $args['heading'] ); ?></h2>
			<?php endif; ?>

			<div class="dmit-mc-calendar-toolbar">
				<?php if ( $args['show_toggle'] ) : ?>
					<div class="dmit-mc-view-toggle" role="tablist">
						<button type="button" class="dmit-mc-view-btn is-active" data-view="calendar" role="tab"><?php esc_html_e( 'Calendar', 'dmit-memory-calendar' ); ?></button>
						<button type="button" class="dmit-mc-view-btn" data-view="list" role="tab"><?php esc_html_e( 'List', 'dmit-memory-calendar' ); ?></button>
					</div>
				<?php endif; ?>
			</div>

			<div class="dmit-mc-calendar-layout">
				<div class="dmit-mc-calendar-pane">
					<div class="dmit-mc-fc-root"></div>
				</div>
				<aside class="dmit-mc-day-panel" aria-live="polite">
					<div class="dmit-mc-day-panel-inner">
						<p class="dmit-mc-day-panel-placeholder"><?php esc_html_e( 'Select a day to see what happened.', 'dmit-memory-calendar' ); ?></p>
					</div>
				</aside>
			</div>

			<div class="dmit-mc-list-pane" hidden>
				<?php echo self::list_markup( array( 'category' => $args['category'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function timeline( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'heading'       => __( 'On This Day', 'dmit-memory-calendar' ),
			'empty_message' => __( 'Nothing on record for today — check back another day.', 'dmit-memory-calendar' ),
			'month'         => '',
			'day'           => '',
		) );

		DMIT_MC_Assets::instance()->enqueue_timeline_assets();

		$id = self::next_id( 'dmit-mc-timeline' );

		ob_start();
		?>
		<div class="dmit-mc-timeline-wrap">
			<?php if ( $args['heading'] ) : ?>
				<h2 class="dmit-mc-widget-heading dmit-mc-timeline-heading"><?php echo esc_html( $args['heading'] ); ?></h2>
			<?php endif; ?>
			<div
				id="<?php echo esc_attr( $id ); ?>"
				class="rl-timeline"
				data-month="<?php echo esc_attr( $args['month'] ); ?>"
				data-day="<?php echo esc_attr( $args['day'] ); ?>"
				data-empty-message="<?php echo esc_attr( $args['empty_message'] ); ?>"
			></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function list_view( $args = array() ) {
		DMIT_MC_Assets::instance()->enqueue_list_assets();
		return self::list_markup( $args );
	}

	private static function list_markup( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'category'     => '',
			'show_filters' => true,
			'per_page'     => 12,
		) );

		$id         = self::next_id( 'dmit-mc-list' );
		$categories = get_terms( array( 'taxonomy' => DMIT_MC_TAXONOMY, 'hide_empty' => true ) );

		ob_start();
		?>
		<div
			id="<?php echo esc_attr( $id ); ?>"
			class="dmit-mc-list-widget"
			data-category="<?php echo esc_attr( $args['category'] ); ?>"
			data-per-page="<?php echo esc_attr( (int) $args['per_page'] ); ?>"
		>
			<?php if ( $args['show_filters'] && ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
				<div class="dmit-mc-list-filters">
					<label class="screen-reader-text" for="<?php echo esc_attr( $id ); ?>-search"><?php esc_html_e( 'Search events', 'dmit-memory-calendar' ); ?></label>
					<input type="search" id="<?php echo esc_attr( $id ); ?>-search" class="dmit-mc-list-search" placeholder="<?php esc_attr_e( 'Search by title or year…', 'dmit-memory-calendar' ); ?>" />

					<select class="dmit-mc-list-category">
						<option value=""><?php esc_html_e( 'All categories', 'dmit-memory-calendar' ); ?></option>
						<?php foreach ( $categories as $term ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $args['category'], $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<div class="dmit-mc-list-results"></div>
			<div class="dmit-mc-list-pagination"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function submit_form( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'heading' => __( 'Share a History Event', 'dmit-memory-calendar' ),
		) );

		DMIT_MC_Assets::instance()->enqueue_submit_form_assets();

		if ( DMIT_MC_Settings::get( 'require_login_to_submit' ) && ! is_user_logged_in() ) {
			ob_start();
			?>
			<div class="dmit-mc-submit-form-wrap dmit-mc-login-required">
				<p><?php esc_html_e( 'Please log in to submit a history event.', 'dmit-memory-calendar' ); ?> <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Log in', 'dmit-memory-calendar' ); ?></a></p>
			</div>
			<?php
			return ob_get_clean();
		}

		$categories = get_terms( array( 'taxonomy' => DMIT_MC_TAXONOMY, 'hide_empty' => false ) );
		$id         = self::next_id( 'dmit-mc-submit-form' );

		ob_start();
		?>
		<div class="dmit-mc-submit-form-wrap">
			<?php if ( $args['heading'] ) : ?>
				<h2 class="dmit-mc-widget-heading"><?php echo esc_html( $args['heading'] ); ?></h2>
			<?php endif; ?>

			<form id="<?php echo esc_attr( $id ); ?>" class="dmit-mc-submit-form" enctype="multipart/form-data" novalidate>
				<p class="dmit-mc-field">
					<label for="<?php echo esc_attr( $id ); ?>-title"><?php esc_html_e( 'Event title', 'dmit-memory-calendar' ); ?> <span class="req">*</span></label>
					<input type="text" id="<?php echo esc_attr( $id ); ?>-title" name="title" required maxlength="150" />
				</p>

				<p class="dmit-mc-field">
					<label for="<?php echo esc_attr( $id ); ?>-date"><?php esc_html_e( 'Date it happened', 'dmit-memory-calendar' ); ?> <span class="req">*</span></label>
					<input type="date" id="<?php echo esc_attr( $id ); ?>-date" name="event_date" max="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>" required />
				</p>

				<?php if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) : ?>
					<p class="dmit-mc-field">
						<label for="<?php echo esc_attr( $id ); ?>-category"><?php esc_html_e( 'Category', 'dmit-memory-calendar' ); ?></label>
						<select id="<?php echo esc_attr( $id ); ?>-category" name="category">
							<option value=""><?php esc_html_e( 'Choose a category (optional)', 'dmit-memory-calendar' ); ?></option>
							<?php foreach ( $categories as $term ) : ?>
								<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
				<?php endif; ?>

				<p class="dmit-mc-field">
					<label for="<?php echo esc_attr( $id ); ?>-description"><?php esc_html_e( 'What happened?', 'dmit-memory-calendar' ); ?> <span class="req">*</span></label>
					<textarea id="<?php echo esc_attr( $id ); ?>-description" name="description" rows="5" required maxlength="4000"></textarea>
				</p>

				<p class="dmit-mc-field">
					<label for="<?php echo esc_attr( $id ); ?>-source"><?php esc_html_e( 'Source / reference link', 'dmit-memory-calendar' ); ?></label>
					<input type="url" id="<?php echo esc_attr( $id ); ?>-source" name="source_link" placeholder="https://" />
				</p>

				<p class="dmit-mc-field">
					<label for="<?php echo esc_attr( $id ); ?>-image"><?php esc_html_e( 'Photo (optional)', 'dmit-memory-calendar' ); ?></label>
					<input type="file" id="<?php echo esc_attr( $id ); ?>-image" name="featured_image" accept="image/png,image/jpeg,image/webp,image/gif" />
				</p>

				<?php if ( ! is_user_logged_in() ) : ?>
					<p class="dmit-mc-field">
						<label for="<?php echo esc_attr( $id ); ?>-name"><?php esc_html_e( 'Your name', 'dmit-memory-calendar' ); ?></label>
						<input type="text" id="<?php echo esc_attr( $id ); ?>-name" name="submitter_name" maxlength="100" />
					</p>
					<p class="dmit-mc-field">
						<label for="<?php echo esc_attr( $id ); ?>-email"><?php esc_html_e( 'Your email', 'dmit-memory-calendar' ); ?> <span class="req">*</span></label>
						<input type="email" id="<?php echo esc_attr( $id ); ?>-email" name="submitter_email" required />
					</p>
				<?php endif; ?>

				<!-- Honeypot: hidden from real visitors via CSS, bots tend to fill every field -->
				<p class="dmit-mc-honeypot" aria-hidden="true">
					<label for="<?php echo esc_attr( $id ); ?>-website"><?php esc_html_e( 'Website', 'dmit-memory-calendar' ); ?></label>
					<input type="text" id="<?php echo esc_attr( $id ); ?>-website" name="dmit_mc_website" tabindex="-1" autocomplete="off" />
				</p>

				<?php if ( DMIT_MC_Settings::get( 'enable_recaptcha' ) ) : ?>
					<input type="hidden" name="recaptcha_token" class="dmit-mc-recaptcha-token" />
					<p class="dmit-mc-recaptcha-notice"><?php esc_html_e( 'This site is protected by reCAPTCHA.', 'dmit-memory-calendar' ); ?></p>
				<?php endif; ?>

				<p class="dmit-mc-field-submit">
					<button type="submit" class="dmit-mc-submit-btn"><?php esc_html_e( 'Submit for review', 'dmit-memory-calendar' ); ?></button>
					<span class="dmit-mc-form-status" role="status"></span>
				</p>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}
}
