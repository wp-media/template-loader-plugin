<?php

namespace WPMedia\TemplateLoader;

class Loader {

	/**
	 * The directory for custom templates.
	 *
	 * @var string template_dir
	 */
	private $template_dir;

	/**
	 * Loader constructor.
	 *
	 * @param string $template_dir The directory for custom templates.
	 */
	public function __construct($template_dir) {
		$this->template_dir = $template_dir;
	}

	/**
	 * Load a custom template.
	 *
	 * @since 1.0
	 *
	 * @param string $template The current WP Template path.
	 *
	 * @return string Path to the custom template to load instead.
	 */
	public function load_template( $template ) {
			if ( ! is_page( 'qa-template' ) ) {
				return $template;
			}

			$template_dir  = WP_CONTENT_DIR . '/qa-templates/';
			$template_file = get_option( 'wp_media_qa_current_template', 'template.php' );

			return $template_dir . $template_file;
	}
}
