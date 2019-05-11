<?php

namespace Recovery_Mode_To_Line_Notify;

class Admin {

	private $option_name;

	private $options;

	public function __construct() {
		$this->option_name = 'recovery-mode-to-line-notify';
		$this->activate();
	}

	public function activate() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );
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
		$this->options = get_option( $this->option_name );

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
		<input name="<?php echo $this->option_name; ?>[access_token]" type="text" id="access_token"
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
}
