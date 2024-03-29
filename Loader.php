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
	public function __construct( $template_dir ) {
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
		global $post;
		
		$test_template = '';
		
		if ( is_object( $post ) ){
			$test_template = get_post_meta( $post->ID, '_test_template', true );
		}

		if ( ! $test_template ) {
			return $template;
		}

		return $this->template_dir . $test_template;
	}
}
