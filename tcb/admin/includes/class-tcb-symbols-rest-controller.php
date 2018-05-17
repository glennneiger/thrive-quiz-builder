<?php
/**
 * FileName  class-tcb-symbols-rest-controller.php.
 * @project: thrive-visual-editor
 * @developer: Dragos Petcu
 */

class TCB_REST_Symbols_Controller extends WP_REST_Posts_Controller {

	public static $version = 1;

	/**
	 * Constructor.
	 * We are overwriting the post type for this rest controller
	 */
	public function __construct() {
		parent::__construct( TCB_Symbols_Post_Type::SYMBOL_POST_TYPE );

		$this->namespace = 'tcb/v' . self::$version;
		$this->rest_base = 'symbols';

		$this->register_meta_fields();
		$this->hooks();
	}

	/**
	 * Hooks to change the post rest api
	 */
	public function hooks() {
		add_filter( "rest_prepare_{$this->post_type}", array( $this, 'rest_prepare_symbol' ), 10, 2 );
		add_filter( "rest_insert_{$this->post_type}", array( $this, 'rest_insert_symbol' ), 10, 2 );
		add_action( 'rest_delete_' . TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY, array( $this, 'rest_delete_category' ), 10, 1 );
	}

	/**
	 * Checks if a given request has access to create a post.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		$parent_response = parent::create_item_permissions_check( $request );

		//if we are making a duplicate symbol revert to default, do not check for duplicate titles
		if ( isset( $request['old_id'] ) || is_wp_error( $parent_response ) ) {
			return $parent_response;
		}

		return $this->check_duplicate_title( $request );
	}

	/**
	 * Checks if a given request has access to update a post.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has access to update the item, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$parent_response = parent::update_item_permissions_check( $request );

		if ( is_wp_error( $parent_response ) ) {
			return $parent_response;
		}

		return $this->check_duplicate_title( $request );
	}

	/**
	 * Check if there already exists a symbol with the same title
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return bool|WP_Error
	 */
	public function check_duplicate_title( $request ) {
		$post_title = '';
		$schema     = $this->get_item_schema();

		// Post title.
		if ( ! empty( $schema['properties']['title'] ) && isset( $request['title'] ) ) {
			if ( is_string( $request['title'] ) ) {
				$post_title = $request['title'];
			} elseif ( ! empty( $request['title']['raw'] ) ) {
				$post_title = $request['title']['raw'];
			}
		}

		$post = get_page_by_title( $post_title, OBJECT, TCB_Symbols_Post_Type::SYMBOL_POST_TYPE );

		if ( $post && $post->post_status !== 'trash' ) {
			return new WP_Error( 'rest_cannot_create_post', __( 'Sorry, you are not allowed to create symbols with the same title' ), array( 'status' => 409 ) );
		}

		return true;
	}

	/**
	 * Add the taxonomy data to the rest response
	 *
	 * @param WP_REST_Response $response
	 * @param WP_Post $post
	 *
	 * @return mixed
	 */
	public function rest_prepare_symbol( $response, $post ) {
		$taxonomies = $response->data[ TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY ];

		foreach ( $taxonomies as $key => $term_id ) {
			$term                                                             = get_term_by( 'term_id', $term_id, TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY );
			$response->data[ TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY ][ $key ] = $term;
		}

		//get thumb url
		$upload_dir = wp_upload_dir();

		$file_url  = trailingslashit( $upload_dir['baseurl'] ) . TCB_Symbols_Post_Type::SYMBOL_THUMBS_FOLDER . '/' . $post->ID . '.png';
		$file_path = trailingslashit( $upload_dir['basedir'] ) . TCB_Symbols_Post_Type::SYMBOL_THUMBS_FOLDER . '/' . $post->ID . '.png';

		//use file path to check if the file exists on the server
		$response->data['thumb_url'] = file_exists( $file_path ) ? $file_url : tve_editor_url( 'admin/assets/images/no-template-preview.jpg' );

		return $response;
	}

	/**
	 * After a symbol is created generate a new thumb for it ( if we are duplicating the symbol )
	 *
	 * @param WP_Post $post Inserted or updated post object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_Error|bool
	 */
	public function rest_insert_symbol( $post, $request ) {
		if ( isset( $request['old_id'] ) ) {
			$this->ensure_unique_title( $post );
			if ( ! $this->copy_thumb( $request['old_id'], $post->ID ) ) {
				return new WP_Error( 'could_not_generate_file', __( 'We were not able to copy the symbol' ), array( 'status' => 500 ) );
			};

		}

		return true;
	}

	/**
	 * When we duplicate a post, the duplicate will take the title_{id}, to not have symbols with the same name
	 *
	 * @param WP_Post $post
	 */
	public function ensure_unique_title( $post ) {
		$post->post_title = $post->post_title . "_{$post->ID}";
		wp_update_post( $post );
	}

	/**
	 * Get path for symbol thumbnail
	 *
	 * @param int $old_id
	 * @param int $new_id
	 *
	 * @return bool
	 */
	public function copy_thumb( $old_id, $new_id ) {

		$upload_dir = wp_upload_dir();

		$old_path = trailingslashit( $upload_dir['basedir'] ) . TCB_Symbols_Post_Type::SYMBOL_THUMBS_FOLDER . '/' . $old_id . '.png';
		$new_path = trailingslashit( $upload_dir['basedir'] ) . TCB_Symbols_Post_Type::SYMBOL_THUMBS_FOLDER . '/' . $new_id . '.png';

		if ( file_exists( $old_path ) ) {
			return copy( $old_path, $new_path );
		}

		return true;
	}

	/**
	 * Return symbol html from meta
	 *
	 * @param array $postdata
	 *
	 * @return mixed
	 */
	public function get_symbol_html( $postdata ) {
		$symbol_id = $postdata['id'];

		return get_post_meta( $symbol_id, 'tve_updated_post', true );
	}

	/**
	 * Update symbol html from meta
	 *
	 * @param string $meta_value
	 * @param WP_Post $post_obj
	 * @param string $meta_key
	 *
	 * @return bool|int
	 */
	public function update_symbol_html( $meta_value, $post_obj, $meta_key ) {
		return update_post_meta( $post_obj->ID, $meta_key, $meta_value );
	}

	/**
	 * Get symbol css from meta
	 *
	 * @param array $postdata
	 *
	 * @return mixed
	 */
	public function get_symbol_css( $postdata ) {
		$symbol_id = $postdata['id'];

		return get_post_meta( $symbol_id, 'tve_custom_css', true );
	}

	/**
	 * Update symbols css from meta
	 *
	 * @param string $meta_value
	 * @param WP_Post $post_obj
	 * @param string $meta_key
	 * @param WP_Rest_Request $request
	 *
	 * @return bool|int
	 */
	public function update_symbol_css( $meta_value, $post_obj, $meta_key, $request ) {
		//if old_id is sent -> we are in the duplicate cas, and we need to replace the id from the css with the new one
		$new_css = ( isset( $request['old_id'] ) ) ? str_replace( $request['old_id'], $post_obj->ID, $meta_value ) : $meta_value;

		return update_post_meta( $post_obj->ID, $meta_key, $new_css );
	}

	/**
	 * Move symbol from one category to another
	 *
	 * @param string $new_term_id
	 * @param WP_Post $post_obj
	 *
	 * @return array|bool|WP_Error
	 */
	public function move_symbol( $new_term_id, $post_obj ) {

		if ( intval( $new_term_id ) === 0 ) {
			//if the new category is the uncategorized one, we just have to delete the existing ones
			return $this->remove_current_terms( $post_obj );
		}

		//get the new category and make sure that it exists
		$term = get_term_by( 'term_id', $new_term_id, TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY );
		if ( $term ) {
			$this->remove_current_terms( $post_obj );

			return wp_set_object_terms( $post_obj->ID, $term->name, TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY );
		}

		return false;
	}

	/**
	 * Remove the symbol from the current category( term )
	 *
	 * @param WP_Post $post_obj
	 *
	 * @return bool|WP_Error
	 */
	public function remove_current_terms( $post_obj ) {
		$post_terms = wp_get_post_terms( $post_obj->ID, TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY );
		if ( ! empty( $post_terms ) ) {
			$term_name = $post_terms[0]->name;

			return wp_remove_object_terms( $post_obj->ID, $term_name, TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY );
		}

		return true;
	}

	/**
	 * Add custom meta fields for comments to use them with the rest api
	 */
	public function register_meta_fields() {
		register_rest_field( $this->get_object_type(), 'tve_updated_post', array(
			'get_callback'    => array( $this, 'get_symbol_html' ),
			'update_callback' => array( $this, 'update_symbol_html' ),
		) );

		register_rest_field( $this->get_object_type(), 'tve_custom_css', array(
			'get_callback'    => array( $this, 'get_symbol_css' ),
			'update_callback' => array( $this, 'update_symbol_css' ),
		) );

		register_rest_field( $this->get_object_type(), 'move_symbol', array(
			'update_callback' => array( $this, 'move_symbol' ),
		) );
	}

	/**
	 * After a category is deleted we need to move the symbols to uncategorized
	 *
	 * @param WP_Term $term The deleted term.
	 */
	public function rest_delete_category( $term ) {

		$posts = get_posts( array(
			'post_type'   => TCB_Symbols_Post_Type::SYMBOL_POST_TYPE,
			'numberposts' => - 1,
			'tax_query'   => array(
				array(
					'taxonomy' => TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY,
					'field'    => 'id',
					'terms'    => $term->term_id,
				),
			),
		) );

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$this->remove_current_terms( $post );
			}
		}
	}
}
