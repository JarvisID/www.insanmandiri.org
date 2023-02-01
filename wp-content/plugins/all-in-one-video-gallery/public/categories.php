<?php

/**
 * Categories
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AIOVG_Public_Categories class.
 *
 * @since 1.0.0
 */
class AIOVG_Public_Categories {

	/**
	 * The detault shortcode attribute values.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array     $defaults An associative array of defaults.
	 */
	protected $defaults = array();
	
	/**
	 * Get things started.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Register shortcode(s)
		add_shortcode( "aiovg_categories", array( $this, "run_shortcode_categories" ) );
	}
	
	/**
	 * Run the shortcode [aiovg_categories].
	 *
	 * @since 1.0.0
	 * @param array $atts An associative array of attributes.
	 */
	public function run_shortcode_categories( $atts ) {	
		// Vars
		$attributes = shortcode_atts( $this->get_defaults(), $atts );	
		$attributes['ratio'] = ! empty( $attributes['ratio'] ) ? (float) $attributes['ratio'] . '%' : '56.25%';
		
		// Enqueue style dependencies
		wp_enqueue_style( AIOVG_PLUGIN_SLUG . '-public' );
		
		// Process output
		$template = sanitize_text_field( $attributes['template'] );

		if ( 'grid' == $template ) {
			$args = array(			
				'orderby'      => sanitize_text_field( $attributes['orderby'] ), 
				'order'        => sanitize_text_field( $attributes['order'] ),
				'hide_empty'   => (int) $attributes['hide_empty'],
				'hierarchical' => false
			);

			if ( ! empty( $attributes['include'] ) ) { // Include category IDs
				$args['include'] = array_map( 'intval', explode( ',', $attributes['include'] ) );
			} else {
				$args['child_of'] = (int) $attributes['id'];
				$args['pad_counts'] = true;
			}

			$terms = get_terms( 'aiovg_categories', $args );			
			
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				if ( isset( $args['child_of'] ) ) {
					foreach ( $terms as $key => $term ) {
						if ( $term->parent != $args['child_of'] ) {
							unset( $terms[ $key ] );
						}
					}

					$terms = array_values( $terms );
				}

				ob_start();
				include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . "public/templates/categories-template-grid.php" );
				return ob_get_clean();		
			} else {
				return aiovg_get_message( 'categories_empty' );
			}			
		} else {
			$args = array(
				'taxonomy'         => 'aiovg_categories',      
				'orderby'          => sanitize_text_field( $attributes['orderby'] ),
				'order'            => sanitize_text_field( $attributes['order'] ),
				'hide_empty'       => (int) $attributes['hide_empty'], 
				'hierarchical'     => (int) $attributes['hierarchical'],                
				'show_count'       => (int) $attributes['show_count'], 
				'show_option_none' => '',   
				'title_li'         => '',
				'echo'             => 0
			);
			
			if ( ! empty( $attributes['include'] ) ) { // Include category IDs
				$args['include'] = array_map( 'intval', explode( ',', $attributes['include'] ) );
			} else {
				if ( $args['hierarchical'] ) {
					$args['child_of'] = (int) $attributes['id'];
				} else {
					$args['parent'] = (int) $attributes['id'];
				}				
			}
			
			$categories_li = wp_list_categories( $args ); 

			if ( ! empty( $categories_li ) ) {
				ob_start();
				include apply_filters( 'aiovg_load_template', AIOVG_PLUGIN_DIR . "public/templates/categories-template-list.php" );
				return ob_get_clean();				
			} else {
				return aiovg_get_message( 'categories_empty' );
			}			
		}		
	}	

	/**
	 * Get the default shortcode attribute values.
	 *
	 * @since  1.0.0
	 * @return array $atts An associative array of attributes.
	 */
	public function get_defaults() {	
		if ( empty( $this->defaults ) ) {			
			$fields = aiovg_get_shortcode_fields();

			foreach ( $fields['categories']['sections'] as $section ) {
				foreach ( $section['fields'] as $field ) {
					$this->defaults[ $field['name'] ] = $field['value'];
				}
			}			
		}
		
		return $this->defaults;
	}
	
}
