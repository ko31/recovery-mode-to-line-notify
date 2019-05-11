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

namespace Recovery_Mode_To_Line_Notify;

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );

add_action( 'plugins_loaded', function () {
	load_plugin_textdomain(
		'recovery-mode-to-line-notify',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);

	if ( is_admin() ) {
		$admin = new Admin();
	}
	$notification = new Notification();
} );
