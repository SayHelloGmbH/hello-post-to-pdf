<?php
namespace SayHello\PostToPDF;

// use mikehaertl\wkhtmlto\Pdf;

class Plugin
{
	private static $instance;
	public $name = '';
	public $version = '';
	public $file = '';

	/**
	 * Creates an instance if one isn't already available,
	 * then return the current instance.
	 *
	 * @param  string $file The file from which the class is being instantiated.
	 * @return object       The class instance.
	 */
	public static function getInstance($file)
	{
		if (!isset(self::$instance) && !(self::$instance instanceof Plugin)) {
			self::$instance = new Plugin;
			if (!function_exists('get_plugin_data')) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$data = get_plugin_data($file);
			self::$instance->name = $data['Name'];
			self::$instance->version = $data['Version'];
			self::$instance->file = $file;
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
		add_action('plugins_loaded', [$this, 'loadPluginTextdomain']);
		register_activation_hook(self::$instance->file, [$this, 'activate']);
		add_action('init', [$this, 'rewrite']);
		add_action('template_include', [$this, 'changeTemplate']);
	}

	public function activate()
	{
		set_transient('hello-post-to-pdf_flush', 1, 60);
	}

	/**
	 * Load translation files from the indicated directory.
	 */
	public function loadPluginTextdomain()
	{
		load_plugin_textdomain('hello-post-to-pdf', false, dirname(plugin_basename($this->file)) . '/languages');
	}

	public function rewrite()
	{
		add_rewrite_endpoint('shpdf', EP_PERMALINK);
 
		if (get_transient('hello-post-to-pdf_flush')) {
			delete_transient('hello-post-to-pdf_flush');
			flush_rewrite_rules();
		}
	}

	public function changeTemplate($template)
	{

		if (get_query_var('shpdf', false) !== false) {
			//Check theme directory first
			$newTemplate = locate_template(['template-hello-post-to-pdf.php']);
			if ('' != $newTemplate) {
				return $newTemplate;
			}
 
			//Check plugin directory next
			$newTemplate = plugin_dir_path(self::$instance->file) . 'templates/simple.php';
			if (file_exists($newTemplate)) {
				return $newTemplate;
			}
		}
 
		//Fall back to original template
		return $template;
	}
}
