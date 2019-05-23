<?php
/*
Plugin Name: Post To PDF
Plugin URI: https://gitlab.com/say-hello/plugins/hello-post-to-pdf
Description: Adds a URL suffix which allows the site visitor to download the selected Post as a PDF - e.g. https://example.org/2019/05/my-post/spdf/
Author: Say Hello GmbH (hello@sayhello.ch)
Version: 2.0.1
Author URI: https://sayhello.ch/
Text Domain: hello-post-to-pdf
Domain Path: /languages
*/

function hello_post_to_pdf_deactivate_self()
{
	deactivate_plugins(plugin_basename(__FILE__));
}

if (version_compare(get_bloginfo('version'), '5.2', '<') || version_compare(PHP_VERSION, '7.1', '<')) {
	function hello_post_to_pdf_compatibility_warning()
	{
		echo '<div class="error"><p>' . sprintf(
			_x('“%1$s” requires PHP %2$s (or newer) and WordPress %3$s (or newer) to function properly. Your site is using PHP %4$s and WordPress %5$s. Please upgrade. The plugin has been automatically deactivated.', 'Plugin warning message', 'hello-post-to-pdf'),
			'PLUGIN NAME',
			'7.1',
			'5.2',
			PHP_VERSION,
			$GLOBALS['wp_version']
		) . '</p></div>';
		if (isset($_GET['activate'])) {
			unset($_GET['activate']);
		}
	}
	add_action('admin_notices', 'hello_post_to_pdf_compatibility_warning');
	add_action('admin_init', 'hello_post_to_pdf_deactivate_self');
	return;
} else {

	function hello_post_to_pdf_binary($cmd)
	{
		$return = shell_exec(sprintf('command -v %s', escapeshellarg($cmd)));
		return !empty($return) ? trim($return) : false;
	}

	function hello_post_to_pdf_no_binary()
	{
		printf(
			'<div class="error"><p>%s</p></div>',
			sprintf(
				_x('The plugin “%1$s” needs “%2$s” to be installed on the server. A test indicated that this is not available, so the plugin has been deactivated.', 'Binary not available', ' hello-post-to-pdf'),
				__('Post to PDF', 'hello-post-to-pdf'),
				'wkhtmltopdf'
			)
		);
	}

	if (!hello_post_to_pdf_binary('wkhtmltopdf')) {
		add_action('admin_notices', 'hello_post_to_pdf_no_binary');
		add_action('admin_init', 'hello_post_to_pdf_deactivate_self');
		if (isset($_GET['activate'])) {
			unset($_GET['activate']);
		}
		return;
	}

	require_once 'vendor/autoload.php';
	require_once 'Classes/Plugin.php';
	function hello_post_to_pdf_get_instance()
	{
		 return SayHello\PostToPDF\Plugin::getInstance(__FILE__);
	}
	hello_post_to_pdf_get_instance();
}
