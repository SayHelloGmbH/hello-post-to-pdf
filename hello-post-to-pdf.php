<?php
/*
Plugin Name: Post To PDF
Plugin URI: https://gitlab.com/say-hello/plugins/hello-post-to-pdf
Description: Adds a URL suffix which allows the site visitor to download the selected Post as a PDF.
Author: Say Hello GmbH (hello@sayhello.ch)
Version: 1.0.1
Author URI: https://sayhello.ch/
Text Domain: hello-post-to-pdf
Domain Path: /languages
*/

if ( version_compare( get_bloginfo( 'version' ), '4.9', '<' ) || version_compare( PHP_VERSION, '7.1', '<' ) ) {
	function hello_post_to_pdf_compatibility_warning() {
		echo '<div class="error"><p>' . sprintf(
			_x( '“%1$s” requires PHP %2$s (or newer) and WordPress %3$s (or newer) to function properly. Your site is using PHP %4$s and WordPress %5$s. Please upgrade. The plugin has been automatically deactivated.', 'Plugin warning message', 'hello-post-to-pdf' ),
			'PLUGIN NAME',
			'7.1',
			'4.9',
			PHP_VERSION,
			$GLOBALS['wp_version']
		) . '</p></div>';
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
	add_action( 'admin_notices', 'hello_post_to_pdf_compatibility_warning' );
	function hello_post_to_pdf_deactivate_self() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
	add_action( 'admin_init', 'hello_post_to_pdf_deactivate_self' );
	return;
} else {
	require_once 'vendor/autoload.php';
	require_once 'Classes/Plugin.php';
	function hello_post_to_pdf_get_instance() {
		 return SayHello\PostToPDF\Plugin::getInstance( __FILE__ );
	}
	hello_post_to_pdf_get_instance();
}
