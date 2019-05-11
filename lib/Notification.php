<?php

namespace Recovery_Mode_To_Line_Notify;

class Notification {

	private $option_name;

	private $options;

	private $line_notify_endpoint = 'https://notify-api.line.me/api/notify';

	public function __construct() {
		$this->option_name = 'recovery-mode-to-line-notify';
		$this->register();
	}

	public function register() {
		add_filter( 'recovery_mode_email', [ $this, 'recovery_mode_email' ], 10, 2 );
	}

	public function recovery_mode_email( $email, $url ) {
		$this->options = get_option( $this->option_name );
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

		$response = wp_remote_post( $this->line_notify_endpoint, [
			'method'  => 'POST',
			'headers' => [
				'Authorization' => 'Bearer ' . $this->options['access_token'],
			],
			'body'    => [
				'message' => $message,
			],
		] );
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
}
