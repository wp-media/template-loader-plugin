<?php
/**
 * Plugin Name: WP Media | Template Loader plugin.
 * Description: A WordPress plugin to loading specific page templates regardless of theme.
 * Plugin URI: https://github.com/wp-media/template-loader-plugin
 * Version: 1.0.0
 * Author: WP Rocket PHP Engineering Team
 * Author URI: https://wp-rocket.me
 * License:    GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright 2020 WP Media <support@wp-rocket.me>
 */

add_action( 'plugins_loaded', 'wp_media_qa_templates' );
/**
 * Initialize QA Templates plugin.
 *
 * @since 1.0
 *
 * @return void
 */
function wp_media_qa_templates() {
	add_filter( 'template_include', 'wp_media_qa_load_custom_template' );

	add_action( 'add_meta_boxes', 'wp_media_qa_add_metabox' );
	add_action( 'save_post', 'wp_media_qa_save_qa_template' );
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

function wp_media_qa_add_metabox() {
	$template_dir = WP_CONTENT_DIR . '/qa-templates/';
	$files        = list_files( $template_dir, 1 );
	$filenames    = [];

	foreach ( $files as $file ) {
		$filenames[] = substr( $file, strrpos( $file, DIRECTORY_SEPARATOR ) + 1 );
	}

	if ( empty( $filenames ) ) {
		return;
	}

	add_meta_box(
		'wp-media-qa-template-chooser',
		__( 'Choose Test Template' ),
		'wp_media_qa_render_template_chooser',
		'page',
		'side',
		'high',
		$filenames
	);
}

function wp_media_qa_render_template_chooser( $post, $box_array ) {
	if ( 'qa-template' !== $post->post_name ) {
		echo '<p>QA Templates are not available for this page.</p>';

		return;
	}

	wp_nonce_field( 'wp-media-set-qa-template', 'wp-media-set-qa-template' );

	$filenames = $box_array['args'];
	$selected  = get_option( 'wp_media_qa_current_template', 'template.php' );
	echo '<label for="wp_media_qa_template_select">Choose a template:</label>';
	echo '<select name="wp_media_qa_template_select" id="wp-media-qa-template-select" 
					class="components-select-control__input" style="max-width:218px">';

	foreach ( $filenames as $filename ) {
		echo '<option value="' . $filename . '" ';
		selected( $selected, $filename );
		echo '>' . $filename . '</option>';
	}

	echo '</select>';
}

function wp_media_qa_save_qa_template( $post_id ) {
	if ( ! wp_media_qa_ok_to_save( $post_id ) ) {
		return;
	}

	$template = isset( $_POST['wp_media_qa_template_select'] )
		? sanitize_text_field( $_POST['wp_media_qa_template_select'] )
		: 'template.php';

	update_option( 'wp_media_qa_current_template', $template );
}

function wp_media_qa_ok_to_save( $post_id ) {
	if ( ! isset( $_POST['wp-media-set-qa-template'] ) ||
	     ! wp_verify_nonce( $_POST['wp-media-set-qa-template'], 'wp-media-set-qa-template' ) ) {
		return false;
	}

	if ( ! current_user_can( "edit_post", $post_id ) ) {
		return false;
	}

	if ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) {
		return false;
	}

	return true;
}