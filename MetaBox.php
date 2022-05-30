<?php

namespace WPMedia\TemplateLoader;

use WP_Post;

class MetaBox {

	/**
	 * The directory for custom templates.
	 *
	 * @var string template_dir
	 */
	private $template_dir;

	/**
	 * MetaBox constructor.
	 *
	 * @param string $template_dir The directory for custom templates.
	 */
	public function __construct( $template_dir ) {
		$this->template_dir = $template_dir;
	}

	/**
	 * Add the Template Loader Metabox to the QA Templates page.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function add_metabox() {
		$files        = list_files( $this->template_dir, 1 );
		$filenames    = [];

		foreach ( $files as $file ) {
			$filenames[] = substr( $file, strrpos( $file, DIRECTORY_SEPARATOR ) + 1 );
		}

		if ( empty( $filenames ) ) {
			return;
		}
		
		sort( $filenames );
		
		add_meta_box(
			'wp-media-qa-template-chooser',
			__( 'Choose Test Template' ),
			[ $this, 'render_template_chooser' ],
			'page',
			'side',
			'high',
			$filenames
		);
	}

	/**
	 * Render the template chooser metabox.
	 *
	 * @since 1.0
	 *
	 * @param array $box_array The metabox arguments.
	 * @param WP_Post $post The current post object.
	 *
	 * @return void
	 */
	public function render_template_chooser( $post, $box_array ) {
		wp_nonce_field( 'wp-media-set-qa-template', 'wp-media-set-qa-template' );

		$filenames = $box_array['args'];
		$selected  = get_post_meta( $post->ID, 'w_test_template', true );

		if ( ! $selected ) {
			$selected = '';
		}

		echo '<label for="wp_media_qa_template_select">Choose a template:</label>';
		echo '<select name="wp_media_qa_template_select" id="wp-media-qa-template-select" 
					class="components-select-control__input" style="max-width:218px">';
		echo '<option value="" ' . selected( $selected, '' ) . '>' . __( 'Theme default page.') . '</option>';

		foreach ( $filenames as $filename ) {
			echo '<option value="' . $filename . '" ';
			echo '>' . $filename . '</option>';
		}

		echo '</select>';
	}

	/**
	 * Save template selection.
	 *
	 * @since 1.0
	 *
	 * @param int $post_id The current post ID.
	 *
	 * @return void
	 */
	public function save_template_selection( $post_id ) {
		if ( ! $this->ok_to_save( $post_id ) ) {
			return;
		}

		$template = isset( $_POST['wp_media_qa_template_select'] )
			? sanitize_text_field( $_POST['wp_media_qa_template_select'] )
			: '';

		update_post_meta( $post_id, '_test_template', $template );
	}

	/**
	 * Is Ok to save data.
	 *
	 * @param int $post_id The current post ID.
	 *
	 * @return bool
	 */
	private function ok_to_save( $post_id ) {
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
}
