<?php

/**
 * Initialize QA Templates plugin.
 *
 * @since 1.0
 *
 * @return void
 */
function wp_media_qa_templates() {
	add_filter( 'template_include', 'wp_media_qa_load_custom_template' );

	$metabox = new \WPMedia\TemplateLoader\MetaBox();

	add_action( 'add_meta_boxes', [ $metabox, 'add_metabox' ] );
	add_action( 'save_post', [ $metabox, 'save_template_selection' ] );
}

/**
 * Load a custom template.
 *
 * @since 1.0
 *
 * @param string $template Path to current WP template.
 *
 * @return string Path to template to load for QA.
 */
function wp_media_qa_load_custom_template( $template ) {
	if ( ! is_page( 'qa-template' ) ) {
		return $template;
	}

	$template_dir  = WP_CONTENT_DIR . '/qa-templates/';
	$template_file = get_option( 'wp_media_qa_current_template', 'template.php' );

	return $template_dir . $template_file;
}
