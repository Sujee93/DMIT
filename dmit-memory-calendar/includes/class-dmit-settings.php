<?php
/**
 * Settings screen under History Events → Settings. Holds everything the
 * submission form and REST/notification layers need at runtime: spam
 * protection keys, the moderation notification address, and the calendar's
 * accent colours (also exposed as Elementor style-control defaults).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DMIT_MC_Settings {

	const OPTION_KEY = 'dmit_mc_settings';

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public static function get( $key, $default = '' ) {
		$options = get_option( self::OPTION_KEY, array() );
		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}

	public function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=' . DMIT_MC_CPT,
			__( 'Memory Calendar Settings', 'dmit-memory-calendar' ),
			__( 'Settings', 'dmit-memory-calendar' ),
			'manage_options',
			'dmit-mc-settings',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting( 'dmit_mc_settings_group', self::OPTION_KEY, array( $this, 'sanitize' ) );
	}

	public function sanitize( $input ) {
		$output = array();

		$output['require_login_to_submit'] = ! empty( $input['require_login_to_submit'] ) ? 1 : 0;
		$output['enable_recaptcha']        = ! empty( $input['enable_recaptcha'] ) ? 1 : 0;
		$output['recaptcha_site_key']      = isset( $input['recaptcha_site_key'] ) ? sanitize_text_field( $input['recaptcha_site_key'] ) : '';
		$output['recaptcha_secret_key']    = isset( $input['recaptcha_secret_key'] ) ? sanitize_text_field( $input['recaptcha_secret_key'] ) : '';
		$output['notification_email']      = isset( $input['notification_email'] ) && is_email( $input['notification_email'] )
			? sanitize_email( $input['notification_email'] )
			: get_option( 'admin_email' );
		$output['accent_color']            = isset( $input['accent_color'] ) ? sanitize_hex_color( $input['accent_color'] ) : '#2563EB';
		$output['today_color']             = isset( $input['today_color'] ) ? sanitize_hex_color( $input['today_color'] ) : '#F59E0B';

		return $output;
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options = get_option(
			self::OPTION_KEY,
			array(
				'require_login_to_submit' => 0,
				'enable_recaptcha'        => 0,
				'recaptcha_site_key'      => '',
				'recaptcha_secret_key'    => '',
				'notification_email'      => get_option( 'admin_email' ),
				'accent_color'            => '#2563EB',
				'today_color'             => '#F59E0B',
			)
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Memory Calendar Settings', 'dmit-memory-calendar' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'dmit_mc_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Public submissions', 'dmit-memory-calendar' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[require_login_to_submit]" value="1" <?php checked( $options['require_login_to_submit'], 1 ); ?> />
								<?php esc_html_e( 'Require visitors to be logged in before they can submit a history event', 'dmit-memory-calendar' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'When off, anyone can submit via the guest form (protected by a honeypot field, and optionally reCAPTCHA below). All submissions still land as Pending until an editor approves them.', 'dmit-memory-calendar' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Google reCAPTCHA v3', 'dmit-memory-calendar' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[enable_recaptcha]" value="1" <?php checked( $options['enable_recaptcha'], 1 ); ?> />
								<?php esc_html_e( 'Enable reCAPTCHA v3 on the submission form', 'dmit-memory-calendar' ); ?>
							</label>
							<p>
								<label><?php esc_html_e( 'Site key', 'dmit-memory-calendar' ); ?><br />
								<input type="text" class="regular-text" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[recaptcha_site_key]" value="<?php echo esc_attr( $options['recaptcha_site_key'] ); ?>" /></label>
							</p>
							<p>
								<label><?php esc_html_e( 'Secret key', 'dmit-memory-calendar' ); ?><br />
								<input type="text" class="regular-text" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[recaptcha_secret_key]" value="<?php echo esc_attr( $options['recaptcha_secret_key'] ); ?>" /></label>
							</p>
							<p class="description"><?php esc_html_e( 'Get keys at google.com/recaptcha/admin. Optional — a honeypot field is always active even if this is off.', 'dmit-memory-calendar' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Notify on new submission', 'dmit-memory-calendar' ); ?></th>
						<td>
							<input type="email" class="regular-text" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[notification_email]" value="<?php echo esc_attr( $options['notification_email'] ); ?>" />
							<p class="description"><?php esc_html_e( 'Sent an email whenever a visitor submits a new event for review.', 'dmit-memory-calendar' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Calendar colours', 'dmit-memory-calendar' ); ?></th>
						<td>
							<label><?php esc_html_e( 'Accent (day-with-events dot)', 'dmit-memory-calendar' ); ?>
							<input type="text" class="dmit-color-field" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[accent_color]" value="<?php echo esc_attr( $options['accent_color'] ); ?>" /></label>
							<br /><br />
							<label><?php esc_html_e( "\"Today\" highlight", 'dmit-memory-calendar' ); ?>
							<input type="text" class="dmit-color-field" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[today_color]" value="<?php echo esc_attr( $options['today_color'] ); ?>" /></label>
							<p class="description"><?php esc_html_e( 'Used as defaults for the Elementor widgets; each widget instance can still override colours in its own Style tab.', 'dmit-memory-calendar' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
