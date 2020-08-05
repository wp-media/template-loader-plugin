<?php
/**
 * Plugin Name: WP Media | Template Loader for QA
 * Description: A WordPress plugin to allow QA to load specific page templates regardless of theme.
 * Plugin URI: https://github.com/wp-media/qa-template-loader
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

	if ( is_admin() ) {
		add_action( 'add_meta_boxes', 'wp_media_qa_add_metabox' );
		add_action( 'save_post', 'wp_media_qa_save_qa_template' );
	}
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

	add_meta_box( 'wp-media-qa-template-chooser', __( 'Choose Test Template' ), 'wp_media_qa_render_template_chooser', 'page', 'side', 'high', $filenames );
}

function wp_media_qa_render_template_chooser( $post, $box_array ) {
	if ( 'qa-template' !== $post->post_name ) {
		echo '<p>QA Templates are not available for this page.</p>';

		return;
	}

	wp_nonce_field( 'wp-media-set-qa-template', 'wp-media-set-qa-template' );

	$filenames = $box_array['args'];

	echo '<select>';

	foreach ( $filenames as $filename ) {
		echo '<option value="' . $filename . '">' . $filename . '</option>';
	}
	echo '</select>';
}

function wp_media_qa_save_qa_template( $post_id, $post, $update ) {
	if ( ! isset( $_POST["meta-box-nonce"] ) || ! wp_verify_nonce( $_POST["meta-box-nonce"], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	if ( ! current_user_can( "edit_post", $post_id ) ) {
		return $post_id;
	}

	if ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	$slug = "post";
	if ( $slug != $post->post_type ) {
		return $post_id;
	}

	$meta_box_text_value     = "";
	$meta_box_dropdown_value = "";
	$meta_box_checkbox_value = "";

	if ( isset( $_POST["meta-box-text"] ) ) {
		$meta_box_text_value = $_POST["meta-box-text"];
	}
	update_post_meta( $post_id, "meta-box-text", $meta_box_text_value );

	if ( isset( $_POST["meta-box-dropdown"] ) ) {
		$meta_box_dropdown_value = $_POST["meta-box-dropdown"];
	}
	update_post_meta( $post_id, "meta-box-dropdown", $meta_box_dropdown_value );

	if ( isset( $_POST["meta-box-checkbox"] ) ) {
		$meta_box_checkbox_value = $_POST["meta-box-checkbox"];
	}
	update_post_meta( $post_id, "meta-box-checkbox", $meta_box_checkbox_value );
}