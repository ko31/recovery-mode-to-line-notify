<?php
/**
 * Plugin Name:     Recovery Mode To Line Notify
 * Plugin URI:      https://github.com/ko31/recovery-mode-to-line-notify
 * Description:     Recovery mode e-mail will be sent to LINE Notify.
 * Author:          ko31
 * Author URI:      https://go-sign.info
 * Text Domain:     recovery-mode-to-line-notify
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Recovery_Mode_To_Line_Notify
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Recovery_Mode_To_Line_Notify {

	private $domain_name;

	private $options;

	private $line_notify_endpoint = 'https://notify-api.line.me/api/notify';

	public function __construct() {
		$this->domain_name = 'recovery-mode-to-line-notify';
		$this->activate();
	}

	public function activate() {
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
		if ( is_admin() ) {
			add_action( 'admin_menu', [ $this, 'admin_menu' ] );
			add_action( 'admin_init', [ $this, 'admin_init' ] );
		}
		add_filter( 'recovery_mode_email', [ $this, 'recovery_mode_email' ], 10, 2 );
	}

	public function plugins_loaded() {
		load_plugin_textdomain(
			 $this->domain_name,
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	public function admin_menu() {
		add_options_page(
			__( 'Recovery mode to LINE Notify', 'recovery-mode-to-line-notify' ),
			__( 'Recovery mode to LINE Notify', 'recovery-mode-to-line-notify' ),
			'manage_options',
			__( 'Recovery mode to LINE Notify', 'recovery-mode-to-line-notify' ),
			[ $this, "display" ]
		);
	}

	public function admin_init() {
		$this->options = get_option( $this->domain_name );

		register_setting(
			'recovery-mode-to-line-notify',
			'recovery-mode-to-line-notify'
		);

		add_settings_section(
			'basic_settings',
			__( 'Basic Settings', 'recovery-mode-to-line-notify' ),
			null,
			'recovery-mode-to-line-notify'
		);

		add_settings_field(
			'access_token',
			__( 'LINE Notify Access Token', 'recovery-mode-to-line-notify' ),
			[ $this, 'access_token_callback' ],
			'recovery-mode-to-line-notify',
			'basic_settings'
		);
	}

	public function access_token_callback() {
		$access_token = isset( $this->options['access_token'] ) ? $this->options['access_token'] : '';
		?>
		<input name="<?php echo $this->domain_name; ?>[access_token]" type="text" id="access_token"
		       value="<?php echo $access_token; ?>" class="regular-text">
		<?php
	}

	public function display() {
		?>
		<form action='options.php' method='post'>
			<h1><?php _e( 'Recovery mode to LINE Notify', 'recovery-mode-to-line-notify' ); ?></h1>
			<?php
			settings_fields( 'recovery-mode-to-line-notify' );
			do_settings_sections( 'recovery-mode-to-line-notify' );
			submit_button();
			?>
		</form>
		<?php
	}

	public function recovery_mode_email( $email, $url ) {
		$this->options = get_option( $this->domain_name );
		if ( ! isset( $this->options['access_token'] ) ) {
			return $email;
		}

		/**
		 * Filter the notification message.
		 *
		 * @param string $message
		 * @param string $url
		 */
		$message = apply_filters( 'recovery_mode_to_line_notify_message', $email['message'], $url);

		$response = $this->notify( $this->options['access_token'], $message );
		if ( is_wp_error( $response ) ) {
			/**
			 * Fires after LINE Notify request error occured.
			 *
			 * @param string $error
			 */
			do_action( 'recovery_mode_to_line_notify_request_error', $response->get_error_message() );
		}

		return $email;
	}

	public function notify( $access_token, $message ) {
		return wp_remote_post( $this->line_notify_endpoint, [
			'method'  => 'POST',
			'headers' => [
				'Authorization' => 'Bearer ' . $access_token,
			],
			'body'    => [
				'message' => $message,
			],
		] );
	}
}

$rmtln = new Recovery_Mode_To_Line_Notify();
