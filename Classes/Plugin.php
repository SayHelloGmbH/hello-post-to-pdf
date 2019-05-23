<?php
namespace SayHello\PostToPDF;

use mikehaertl\wkhtmlto\Pdf;

class Plugin
{

	private static $instance;
	public $name    = '';
	public $version = '';
	public $file    = '';
	private $allowed_post_types;

	/**
	 * Creates an instance if one isn't already available,
	 * then return the current instance.
	 *
	 * @param  string $file The file from which the class is being instantiated.
	 * @return object       The class instance.
	 */
	public static function getInstance($file = __FILE__)
	{
		if (! isset(self::$instance) && ! ( self::$instance instanceof Plugin )) {
			self::$instance = new Plugin;
			if (! function_exists('get_plugin_data')) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$data                    = get_plugin_data($file);
			self::$instance->name    = $data['Name'];
			self::$instance->version = $data['Version'];
			self::$instance->file    = $file;
			self::$instance->run();
		}
		return self::$instance;
	}

	/**
	 * Execution function which is to be called after the class has been initialized.
	 * This contains hook and filter assignments, etc.
	 */
	private function run()
	{
		$this->allowed_post_types = [ 'post', 'page' ];

		add_action('plugins_loaded', [ $this, 'loadPluginTextdomain' ]);
		register_activation_hook(self::$instance->file, [ $this, 'activate' ]);
		register_deactivation_hook(self::$instance->file, [ $this, 'purge' ]);
		add_action('init', [ $this, 'rewrite' ]);
		add_action('template_include', [ $this, 'changeTemplate' ]);
	}

	public function activate()
	{
		set_transient('hello-post-to-pdf_flush', 1, 60);
	}

	public function purge()
	{
		if (get_transient('hello-post-to-pdf_flush')) {
			delete_transient('hello-post-to-pdf_flush');
			flush_rewrite_rules();
		}
	}

	/**
	 * Load translation files from the indicated directory.
	 */
	public function loadPluginTextdomain()
	{
		load_plugin_textdomain('hello-post-to-pdf', false, dirname(plugin_basename($this->file)) . '/languages');
	}

	/**
	 * Flush rewrite rules on plugin activation
	 * @return void
	 */
	public function rewrite()
	{
		add_rewrite_endpoint('shpdf', EP_PERMALINK | EP_PAGES);

		$this->purge();
	}

	/**
	 * Potentially use a different PHP template if the custom rewrite rule matches
	 * @param  string $template Default (current) template path
	 * @return string           Potentially modified template path
	 */
	public function changeTemplate($template)
	{
		if (get_query_var('shpdf', false) !== false) {
			$newTemplate = $this->getTemplate(true);
			if ($newTemplate) {
				return $newTemplate;
			}
		}

		//Fall back to original template
		return $template;
	}

	/**
	 * Find the template - is it in the Theme/Child Theme or in the Plugin?
	 * @return mixed Path to the template file or null if none found
	 */
	private function getTemplate($url_request = false)
	{
		if (! in_array(get_post_type(), apply_filters('hello-post-to-pdf/allowed-post-types', $this->allowed_post_types))) {
			wp_die(
				'<p>' . _x('PDF generation is not available for this content type.', 'Error message', 'hello-post-to-pdf') . '</p>' .
				sprintf(
					'<p><a href="%1$s">%2$s</a></p>',
					get_permalink(),
					_x('View original content', 'Link text on error page', 'hello-post-to-pdf')
				),
				403
			);
		}

		$pdf = $this->getPdfConvertorObject();

		if (!$pdf) {
			wp_die(
				'<p>' . _x('A required system-level tool (wkhtmltopdf) is not installed on the server. Please contact the system administrator or website owner.', 'Error message', 'hello-post-to-pdf') . '</p>' .
					sprintf(
						'<p><a href="%1$s">%2$s</a></p>',
						get_permalink(),
						_x('View original content', 'Link text on error page', 'hello-post-to-pdf')
					),
				500
			);
		}

		// Check theme / child theme directory
		// Default: use template/simple.php in the plugin
		// Quick override: add single-hello-post-to-pdf.php in the root folder of your Theme
		// Use the filter in your theme to customize the theme template array more specifically.

		$template = locate_template(apply_filters('hello-post-to-pdf/theme-templates', [ 'single-hello-post-to-pdf.php' ]));
		if ($template == '') {
			// Check plugin directory next
			$template = plugin_dir_path(self::getInstance()->file) . 'templates/simple.php';
			if (! file_exists($template)) {
				return null;
			}
		}

		ob_start();
		include($template);
		$html = ob_get_contents();
		ob_end_clean();

		if (empty($html)) {
			wp_die(
				'<p>' . _x('The generated content for the PDF is empty. This may indicate a problem with the template which is being used.', 'Error message', 'hello-post-to-pdf') . '</p>' .
					sprintf(
						'<p><a href="%1$s">%2$s</a></p>',
						get_permalink(),
						_x('View original content', 'Link text on error page', 'hello-post-to-pdf')
					),
				500
			);
		}

		$pdf->addPage($html);

		if ($url_request) {
			if (! $pdf->send()) {
				if (defined('WP_DEBUG') && WP_DEBUG === true) {
					$error = $pdf->getError();
					var_dump($error);
					exit;
				}

				wp_die(
					'<p>' . _x('An unavoidable error occurred when creating the PDF.', 'Error message', 'hello-post-to-pdf') . '</p>' .
					sprintf(
						'<p><a href="%1$s">%2$s</a></p>',
						get_permalink(),
						_x('View original content', 'Link text on error page', 'hello-post-to-pdf')
					),
					500
				);

				exit;
			}
		}

		// Optional hook allows you to save the generated PDF on the server
		// Default behaviour is that the file will NOT be saved.
		if (!apply_filters('hello-post-to-pdf/save-generated-pdf', false)) {
			return;
		}

		$filesystem = new FileSystem();
		$filepath   = $filesystem->getFilepath(get_the_ID());

		if (! $pdf->saveAs($filepath)) {
			if (defined('WP_DEBUG') && WP_DEBUG === true) {
				$error = $pdf->getError();
				var_dump($error);
				exit;
			}
			wp_die(
				'<p>' . _x('An unavoidable error occurred when saving the PDF to the server.', 'Error message', 'hello-post-to-pdf') . '</p>' .
					sprintf(
						'<p><a href="%1$s">%2$s</a></p>',
						get_permalink(),
						_x('View original content', 'Link text on error page', 'hello-post-to-pdf')
					),
				500
			);

			exit;
		}
	}

	public function getPdfConvertorObject()
	{

		$binary = hello_post_to_pdf_binary('wkhtmltopdf');

		if (!$binary) {
			return null;
		}

		return new Pdf(apply_filters('hello-post-to-pdf/generation-settings', [
			'binary'         => $binary,
			'ignoreWarnings' => true,
			'margin-top'     => 10,
			'margin-bottom'  => 10,
			'margin-left'    => 0,
			'margin-right'   => 0,
			'disable-smart-shrinking',
			'header-spacing' => 5,
			'footer-spacing' => 5,
			// 'header-html'    => get_template_directory_uri() . '/hello-post-to-pdf-header.php', // e.g.
			// 'footer-html'    => get_template_directory_uri() . '/hello-post-to-pdf-footer.php', // e.g.
		]));
	}
}
