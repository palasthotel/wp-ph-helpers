<?php
namespace PhHelpers\View;

/**
 * Render class which makes rendering of template files allot easier
 */
class Renderer
{
	protected $themeFolder = 'parts';

	/**
	 * Set the name of the theme folder where the plugins are stored (default /parts)
	 * @param string $subfolder
	 * @return \Renderer
	 */
	public function setThemeFolder($subfolder)
	{
		$this->themeFolder = $themeFolder;
		return $this;
	}

	/**
	 * Render a template
	 * @param string $template
	 * @param array $args
	 * @param boolean $directOutput (optional)
	 */
	public function render($template, $args = array(), $directOutput = false)
	{
		$path = $this->getTemplatePath($template);

		if (!$path) {
			return '<p>The template file <code>' .
				$template .
				'</code> could in the subfolder <code>' .
				$this->themeFolder .
				'</code> not be found in the theme or any plugin.</p>';
		}

		extract($args);
		ob_start();
		include $path;
		$content .= ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Returns the path to a given template path
	 * @param string $template
	 * @return string
	 */
	protected function getTemplatePath($template)
	{
		$path = '/' . $this->themeFolder . '/' . $template;

		if (file_exists(get_template_directory() . $path)) {
			return get_template_directory() . $path;
		}

		if (file_exists(get_stylesheet_directory() . $path)) {
			return get_stylesheet_directory() . $path;
		}

		// the latest activated plugin will be called last
		$plugins = array_reverse(get_option('active_plugins'));
		foreach ($plugins as $plugin) {
			if (
				file_exists(
					WP_PLUGIN_DIR . '/' . plugin_dir_path($plugin) . $path
				)
			) {
				return WP_PLUGIN_DIR . '/' . plugin_dir_path($plugin) . $path;
			}
		}

		return null;
	}
}
