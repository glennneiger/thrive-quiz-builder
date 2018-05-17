<?php

/**
 * FileName  class-tcb-symbols-post-types.php.
 * @project: thrive-visual-editor
 * @developer: Dragos Petcu
 * @company: BitStone
 */
class TCB_Symbols_Post_Type {

	/**
	 * Symbol custom post type
	 */
	const SYMBOL_POST_TYPE = 'tcb_symbol';

	/**
	 * The folder where to save the thumbnails ( previews )
	 */
	const SYMBOL_THUMBS_FOLDER = 'symbols';

	/**
	 * Symbol template file
	 *
	 * @var string
	 */
	private $_template = 'symbol-content.php';

	/**
	 * TCB_Symbols_Post_Type constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Adds action for register another post type
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_symbol_post_type' ), 5 );

		add_filter( 'tcb_custom_post_layouts', array( $this, 'symbol_layout' ), 10, 3 );
	}

	/**
	 * Register symbol post type
	 */
	public function add_symbol_post_type() {

		if ( post_type_exists( self::SYMBOL_POST_TYPE ) ) {
			return;
		}

		register_post_type( self::SYMBOL_POST_TYPE, array(
			'publicly_queryable'  => true,
			'query_var'           => false,
			'description'         => __( 'Thrive Symbol', 'thrive-cb' ),
			'rewrite'             => false,
			'labels'              => array(
				'name'          => __( 'Thrive Symbols', 'thrive-cb' ),
				'singular_name' => __( 'Thrive Symbol', 'thrive-cb' ),
				'add_new_item'  => __( 'Add New Thrive Symbol', 'thrive-cb' ),
				'edit_item'     => __( 'Edit Thrive Symbol', 'thrive-cb' ),
			),
			'show_in_nav_menus'   => false,
			'show_in_menu'        => false,
			'exclude_from_search' => true,
			'public'              => true,
			'show_in_rest'        => true,
			'has_archive'         => false,
		) );
	}

	/**
	 * Render symbol layout
	 *
	 * @param array $layouts
	 * @param int $post_id
	 * @param string $post_type
	 *
	 * @return mixed
	 */
	public function symbol_layout( $layouts, $post_id, $post_type ) {

		if ( $post_type === self::SYMBOL_POST_TYPE ) {

			$file_path = TVE_TCB_ROOT_PATH . 'inc/views/symbols/' . $this->_template;

			if ( ! is_file( $file_path ) ) {
				return $layouts;
			}

			$layouts['symbol_template'] = $file_path;
		}

		return $layouts;
	}
}

global $tcb_symbol;
/**
 *  Main instance of TCB Symbols Post Type
 *
 * @return TCB_Symbols_Post_Type
 */
function tcb_symbol() {
	global $tcb_symbol;
	if ( ! $tcb_symbol ) {
		$tcb_symbol = new TCB_Symbols_Post_Type();
	}

	return $tcb_symbol;
}

tcb_symbol();
