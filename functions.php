<?php

use WPMedia\TemplateLoader\Loader;
use WPMedia\TemplateLoader\MetaBox;

/**
 * Initialize Template Loader plugin.
 *
 * @since 1.0
 *
 * @return void
 */
function wp_media_template_loader() {
	$template_dir = WP_CONTENT_DIR . '/qa-templates/';

	$loader = new Loader( $template_dir );
	add_filter( 'template_include', [ $loader, 'load_template' ] );

	$metabox = new MetaBox( $template_dir );
	add_action( 'add_meta_boxes', [ $metabox, 'add_metabox' ] );
	add_action( 'save_post', [ $metabox, 'save_template_selection' ] );
}
