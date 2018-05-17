<?php
/**
 * FileName  class-tcb-symbols-taxonomy.php.
 * @project: thrive-visual-editor
 * @developer: Dragos Petcu
 * @company: BitStone
 */

class TCB_Symbols_Taxonomy {

	const SYMBOLS_TAXONOMY = 'tcb_symbols_tax';

	private $_default_terms;

	public function __construct() {
		$this->init();
	}

	public function init() {
		add_action( 'init', array( $this, 'register_symbols_tax' ) );
		add_filter( 'tcb_main_frame_localize', array( $this, 'terms_localization' ) );
	}

	public function register_symbols_tax() {
		$tax_labels = $this->get_labels();

		register_taxonomy( self::SYMBOLS_TAXONOMY, array( TCB_Symbols_Post_Type::SYMBOL_POST_TYPE ), array(
			'hierarchical'      => true,
			'labels'            => $tax_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'symbol' ),
			'show_in_rest'      => true,
		) );

		register_taxonomy_for_object_type( self::SYMBOLS_TAXONOMY, TCB_Symbols_Post_Type::SYMBOL_POST_TYPE );
		$this->insert_default_terms();

	}

	public function insert_default_terms() {
		$terms = array( 'Headers', 'Footers' );

		foreach ( $terms as $term ) {
			$exists = term_exists( $term, self::SYMBOLS_TAXONOMY );
			if ( $exists !== 0 && $exists !== null ) {
				$term_id = $exists['term_id'];
			} else {
				$term_insert = wp_insert_term( $term, TCB_Symbols_Taxonomy::SYMBOLS_TAXONOMY );
				if ( ! is_wp_error( $term_insert ) ) {
					$term_id = $term_insert['term_id'];
				}
			}

			if ( isset( $term_id ) ) {
				$this->add_default_term( $term_id );
			}
		}
	}

	public function get_default_terms() {
		return $this->_default_terms;
	}

	public function add_default_term( $term_id ) {
		$this->_default_terms[] = $term_id;
	}

	public function get_labels() {

		$default_labels = array(
			'name'              => __( 'Symbols', 'thrive-cb' ),
			'singular_name'     => __( 'Symbol', 'thrive-cb' ),
			'search_items'      => __( 'Search Symbols', 'thrive-cb' ),
			'all_items'         => __( 'All Symbols', 'thrive-cb' ),
			'parent_item'       => __( 'Parent Symbol', 'thrive-cb' ),
			'parent_item_colon' => __( 'Parent Symbol', 'thrive-cb' ),
			'edit_item'         => __( 'Edit Symbol', 'thrive-cb' ),
			'update_item'       => __( 'Update Symbol', 'thrive-cb' ),
			'add_new_item'      => __( 'Add New Symbol', 'thrive-cb' ),
			'new_item_name'     => __( 'New Symbol Name', 'thrive-cb' ),
			'menu_name'         => __( 'Symbols', 'thrive-cb' ),
		);

		return apply_filters( 'tcb_symbols_tax_labels', $default_labels );
	}

	/**
	 * Get the symbols from the site without the ones that are in the default categories (Header and Footer )
	 *
	 * @param bool $hide_defaults
	 *
	 * @return array|int|WP_Error
	 */
	public function get_symbols_tax_terms( $hide_defaults = true ) {
		$result = array();
		$terms  = get_terms( array(
			'order'      => 'DESC',
			'orderby'    => 'term_id',
			'taxonomy'   => self::SYMBOLS_TAXONOMY,
			'hide_empty' => false,
		) );
		if ( $hide_defaults ) {
			foreach ( $terms as $key => $term ) {
				if ( ! in_array( $term->name, array( 'Headers', 'Footers' ) ) ) {
					$result[] = $term;
				}
			}

			return $result;
		}

		return $terms;
	}

	/**
	 * Add categories to localization for tcb editor
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function terms_localization( $data ) {
		$data['symbols_tax_terms']  = $this->get_symbols_tax_terms();
		$data['symbols_rest_terms'] = rest_url( sprintf( '%s/%s', 'wp/v2', self::SYMBOLS_TAXONOMY ) );
		$data['symbols_rest_nonce'] = wp_create_nonce( 'wp_rest' );

		return $data;
	}
}

global $tcb_symbol_taxonomy;
/**
 * Main instance of TCB Symbols Dashboard
 *
 * @return TCB_Symbols_Dashboard
 */
function tcb_symbol_taxonomy() {
	global $tcb_symbol_taxonomy;

	if ( ! $tcb_symbol_taxonomy ) {
		$tcb_symbol_taxonomy = new TCB_Symbols_Taxonomy();
	}

	return $tcb_symbol_taxonomy;
}

tcb_symbol_taxonomy();
