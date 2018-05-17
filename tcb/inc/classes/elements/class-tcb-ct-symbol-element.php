<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class TCB_Ct_Element
 *
 * Content templates - allows inserting saved content templates into the page
 */
class TCB_Ct_Symbol_Element extends TCB_Element_Abstract {

	/**
	 * Config for symbol
	 *
	 * @var string
	 */
	private $_cfg_code = '__CONFIG_post_symbol__';

	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Templates & Symbols', 'thrive-cb' );
	}

	/**
	 * Return icon class needed for display in menu
	 *
	 * @return string
	 */
	public function icon() {
		return 'templatesnsymbols';
	}

	/**
	 * Element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.thrv_ct_symbol';
	}

	/**
	 * This is only a placeholder element
	 *
	 * @return bool
	 */
	public function is_placeholder() {
		return true;
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		return array(
			'ct_symbol' => array(
				'config' => array(),
			),
		);
	}

	/**
	 * General components that apply to all elements
	 *
	 * @return array
	 */
	public function general_components() {
		return array(
			'layout'     => array(
				'order' => 100,
			),
			'responsive' => array(
				'order' => 140,
			),
		);
	}
	/**
	 * Get all information about all saved templates
	 *
	 * @return mixed|array
	 */
	public function get() {
		return get_option( 'tve_user_templates', array() );
	}

	/**
	 * Gets the list of saved templates ( just names and indexes, no content )
	 * Used in searching for content templates - autocomplete-ready list
	 *
	 * @return array
	 */
	public function get_list( $templates = null ) {
		if ( $templates === null ) {
			$templates = $this->get();
		}
		$list = array();
		if ( empty( $templates ) ) {
			$templates = array();
		}
		foreach ( $templates as $key => $tpl ) {
			$temp_array = array(
				'id'    => $key,
				'label' => rawurldecode( $tpl['name'] ),
				'type'  => ! empty( $tpl['type'] ) ? $tpl['type'] : '',
			);

			if ( in_array( $temp_array['type'], array( 'button' ) ) ) {
				$temp_array = array_merge( $temp_array, array(
					'media'   => $tpl['media_css'],
					'content' => stripslashes( $tpl['content'] ),
				) );
			}

			$list[] = $temp_array;
		}

		return $list;
	}

	/**
	 * Loads data for a template
	 *
	 * @param int $key
	 *
	 * @return array
	 */
	public function load( $key ) {
		$templates = $this->get();

		$media_css = isset( $templates[ $key ]['media_css'] ) ? array_map( 'stripslashes', $templates[ $key ]['media_css'] ) : null;
		if ( $media_css ) {
			/* make sure the server did not mess up the inline rules - e.g. instances where it replaces double quotes with single quotes */
			foreach ( $media_css as $k => $value ) {
				$media_css[ $k ] = preg_replace( "#data-css='(.+?)'#s", 'data-css="$1"', $value );
			}
		}

		$response = array(
			'html_code' => stripslashes( $templates[ $key ]['content'] ),
			'css_code'  => stripslashes( $templates[ $key ]['css'] ),
			'media_css' => $media_css,
		);
		if ( ob_get_contents() ) {
			ob_clean();
		}

		return $response;
	}

	/**
	 * Deletes a saved content template
	 *
	 * @param int $key
	 *
	 * @return array with template information
	 */
	public function delete( $key ) {
		$templates = $this->get();
		array_splice( $templates, $key, 1 );

		update_option( 'tve_user_templates', $templates );

		return $this->get_list( $templates );
	}

	/**
	 * Element category that will be displayed in the sidebar
	 *
	 * @return string
	 */
	public function category() {
		return $this->get_thrive_basic_label();
	}

	/**
	 * Get all symbols
	 */
	public function get_all( $args ) {
		$result   = array();
		$defaults = array(
			'post_type'      => TCB_Symbols_Post_Type::SYMBOL_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => 'publish'
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Add the possibility for other plugins to change the arguments for getting the symbols
		 *
		 * @param array $args
		 */
		$args    = apply_filters( 'tcb_get_symbols_args', $args );
		$symbols = get_posts( $args );

		if ( is_wp_error( $symbols ) ) {
			return new WP_Error( 'query_error', __( 'Error when retrieving symbols', 'thrive-cb' ) );
		}

		foreach ( $symbols as $symbol ) {
			$result[ $symbol->ID ] = $this->prepare_symbol( $symbol );
		}

		/**
		 * Change the symbols array returned
		 *
		 * @param array $result
		 */
		$result = apply_filters( 'tcb_get_symbols_response', $result );

		return $result;
	}

	/**
	 * Prepare symbol before listing in TAR
	 *
	 * @param WP_Post $symbol
	 *
	 * @return array
	 */
	public function prepare_symbol( $symbol ) {

		$content = TCB_Symbol_Template::render_content( array( 'id' => $symbol->ID ) );

		$symbol_data = array(
			'id'         => $symbol->ID,
			'content'    => $content,
			'post_title' => $symbol->post_title,
			'config'     => $this->_get_symbol_config( $symbol ),
			'css'        => $this->get_symbol_css( $symbol->ID ),
			'thumb_url'  => $this->get_thumb_path( $symbol->ID ),
		);

		/**
		 * Change symbol data before showing it in the list
		 *
		 * @param array $symbol_data
		 */
		$symbol_data = apply_filters( 'tcb_symbol_data_before_return', $symbol_data );

		return $symbol_data;
	}

	/**
	 * Get path for symbol thumbnail
	 *
	 * @param int $symbol_id
	 *
	 * @return string
	 */
	public function get_thumb_path( $symbol_id ) {
		$upload_dir = wp_upload_dir();
		$thumb_url = trailingslashit( $upload_dir['baseurl'] ) . TCB_Symbols_Post_Type::SYMBOL_THUMBS_FOLDER . '/' . $symbol_id . '.png';
		$thumb_path = trailingslashit( $upload_dir['basedir'] ) . TCB_Symbols_Post_Type::SYMBOL_THUMBS_FOLDER . '/' . $symbol_id . '.png';

		if ( file_exists( $thumb_path ) ) {
			return $thumb_url;
		}

		return tve_editor_url( 'editor/css/images/symbol_placeholder.png' );
	}

	/**
	 * Get css for a certain symbol
	 *
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function get_symbol_css( $id ) {
		$custom_css = get_post_meta( $id, 'tve_custom_css', true );

		return $custom_css;
	}

	/**
	 * Get config for symbol
	 *
	 * @param WP_Post $symbol
	 *
	 * @return string
	 */
	private function _get_symbol_config( $symbol ) {
		$encoded_config = tve_json_utf8_unslashit( json_encode( array( 'id' => ( string ) $symbol->ID ) ) );

		return $this->_cfg_code . $encoded_config . $this->_cfg_code;
	}

	/**
	 * Save a symbol changed from within the editor page
	 *
	 * @param array $symbol_data
	 *
	 * @return array|WP_Error
	 */
	public function edit_symbol( $symbol_data ) {

		if ( ! isset( $symbol_data['id'] ) ) {
			return new WP_Error( 'id_is_not_set', __( 'Missing symbol id', 'thrive-cb' ), array( 'status' => 500 ) );
		}

		update_post_meta( $symbol_data['id'], 'tve_updated_post', $symbol_data['content'] );
		update_post_meta( $symbol_data['id'], 'tve_custom_css', $symbol_data['css'] );

		$symbol = get_post( $symbol_data['id'] );

		return array( 'symbol' => $symbol );
	}

	/**
	 * Create symbol from content elements
	 *
	 * @param array $symbol_data
	 *
	 * @return array|int|WP_Error
	 */
	public function create_symbol( $symbol_data ) {
		$create_symbol_defaults = array(
			'post_type'   => TCB_Symbols_Post_Type::SYMBOL_POST_TYPE,
			'post_status' => 'publish'
		);

		$create_symbol_args = wp_parse_args( array( 'post_title' => $symbol_data['symbol_title'] ), $create_symbol_defaults );

		/**
		 * Add the possibility for other plugins to change the arguments for creating a symbol
		 *
		 * @param array $args
		 */
		$create_symbol_args = apply_filters( 'tcb_create_symbol_args', $create_symbol_args );
		$post_id            = wp_insert_post( $create_symbol_args, true );

		//if something went wrong at insert, just return the error
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		/**
		 * After save actions: add to category and update meta ( html and css )
		 */
		$this->after_save( $post_id, $symbol_data );

		//return the newly created symbol for later use, if needed
		$symbol = get_post( $post_id );

		//prepare the symbol to be inserted in the page after a successful save
		$response = $this->prepare_symbol( $symbol );

		return $response;
	}

	/**
	 * Actions taken if a symbols is successfully created
	 *
	 * @param int $post_id
	 * @param array $symbol_data
	 */
	public function after_save( $post_id, $symbol_data ) {

		//if we are sending the category than assign the symbol to it
		$terms = isset( $symbol_data['term_id'] ) ? array( $symbol_data['term_id'] ) : array();
		wp_set_post_terms( $post_id, $terms, TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY );

		//if the insert was ok, update the meta attributes for the symbol
		update_post_meta( $post_id, 'tve_updated_post', $symbol_data['content'] );
		update_post_meta( $post_id, 'tve_custom_css', $symbol_data['css'] );
	}

	/**
	 * Save css for elements with extra css. i.e call to action
	 * The css selectors are updated with proper thrv_symbol selectors
	 *
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function save_extra_css( $data ) {

		if ( ! isset( $data['id'] ) ) {
			return new WP_Error( 'id_is_not_set', __( 'Missing symbol id', 'thrive-cb' ), array( 'status' => 500 ) );
		}

		update_post_meta( $data['id'], 'tve_custom_css', $data['css'] );

		$symbol = get_post( $data['id'] );

		return array( 'symbol' => $symbol );
	}

	/**
	 * Generate preview for the symbol
	 *
	 * @param int $post_id
	 *
	 * @return array|WP_Error
	 */
	public function generate_preview( $post_id ) {

		add_filter( 'upload_dir', array( $this, 'upload_dir' ) );

		$moved_file = wp_handle_upload( $_FILES['preview_file'], array(
			'action'                   => TCB_Editor_Ajax::ACTION,
			'unique_filename_callback' => array( $this, 'get_preview_filename' ),
		) );

		remove_filter( 'upload_dir', array( $this, 'upload_dir' ) );

		if ( empty( $moved_file['url'] ) ) {
			return new WP_Error( 'file_not_saved', __( 'The file could not be saved', 'thrive-cb' ), array( 'status' => 500 ) );
		}

		$editor = wp_get_image_editor( $moved_file['file'] );

		$editor->save( $moved_file['file'] );

		return array(
			'file_path' => $moved_file['url'],
		);

	}

	/**
	 * Get the name for the thumbnail
	 * Prevent wordpress for creating a new file when it already exists in the uploads folder
	 *
	 * @param string $dir
	 * @param string $name
	 * @param string $ext
	 *
	 * @return mixed
	 */
	public function get_preview_filename( $dir, $name, $ext ) {
		return $name;
	}

	/**
	 * Get the upload directory where the file will be kept
	 *
	 * @param array $upload
	 *
	 * @return mixed
	 */
	public static function upload_dir( $upload ) {

		$sub_dir = '/' . TCB_Symbols_Post_Type::SYMBOL_THUMBS_FOLDER;

		$upload['path']   = $upload['basedir'] . $sub_dir;
		$upload['url']    = $upload['baseurl'] . $sub_dir;
		$upload['subdir'] = $sub_dir;

		return $upload;
	}

}
