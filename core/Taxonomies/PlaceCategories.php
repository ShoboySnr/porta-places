<?php
/**
 * Register Services Taxonomy for Offices.
 */
namespace Porta_Places\Taxonomies;

/**
 * Register Services Taxonomy.
 */
class PlaceCategories {

    /**
     * Register Services Taxonomy in WordPress.
     *
     * @return void
     */
    public function register() {
        add_action( 'init', [ $this, 'porta_place_taxonomy'], 10 );
        add_action( 'acf/init', [ $this, 'acf_fields'], 10 );
    }

    /**
     * Register Taxonomy Arguments.
     *
     * @return void
     */
    public function porta_place_taxonomy() {

        register_taxonomy(
            'gd_placecategory',
            array( 'gd_place', 'locations' ),
            array(
                'hierarchical'      => false,
                'public'            => true,
                'show_in_nav_menus' => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => false,
                'capabilities'      => array(
                    'manage_terms' => 'edit_posts',
                    'edit_terms'   => 'edit_posts',
                    'delete_terms' => 'edit_posts',
                    
                ),
                'labels'                => array(
                    'name'                       => __( 'Place Categories', 'cvgt-locations' ),
                    'singular_name'              => _x( 'Place Category', 'taxonomy general name', 'cvgt-locations' ),
                    'search_items'               => __( 'Search Place Categories', 'cvgt-locations' ),
                    'popular_items'              => __( 'Popular Place Categories', 'cvgt-locations' ),
                    'all_items'                  => __( 'All Place Categories', 'cvgt-locations' ),
                    'parent_item'                => __( 'Parent Place Category', 'cvgt-locations' ),
                    'parent_item_colon'          => __( 'Parent Place Category:', 'cvgt-locations' ),
                    'edit_item'                  => __( 'Edit Place Category', 'cvgt-locations' ),
                    'update_item'                => __( 'Update Place Category', 'cvgt-locations' ),
                    'add_new_item'               => __( 'New Place Category', 'cvgt-locations' ),
                    'new_item_name'              => __( 'New Place Category', 'cvgt-locations' ),
                    'separate_items_with_commas' => __( 'Separate Place Categories with commas', 'cvgt-locations' ),
                    'add_or_remove_items'        => __( 'Add or remove Place Categories', 'cvgt-locations' ),
                    'choose_from_most_used'      => __( 'Choose from the most used Place Categories', 'cvgt-locations' ),
                    'not_found'                  => __( 'No Place Categories found.', 'cvgt-locations' ),
                    'menu_name'                  => __( 'Place Categories', 'cvgt-locations' ),
                ),
                'show_in_rest'          => true,
                'rest_base'             => 'gd_placecategory',
                'rest_controller_class' => 'WP_REST_Terms_Controller',
            )
        );

    }

    public function acf_fields() {

        if( function_exists('acf_add_local_field_group') ):

            acf_add_local_field_group( array(
                'key' => 'group_60ec23aac1772',
                'title' => 'Place Category',
                'fields' => array(
                    array(
                        'key' => 'field_60ec242a73305',
                        'label' => 'Category Top Description',
                        'name' => 'category_top_description',
                        'type' => 'wysiwyg',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'tabs' => 'all',
                        'toolbar' => 'basic',
                        'media_upload' => 0,
                        'delay' => 0,
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'taxonomy',
                            'operator' => '==',
                            'value' => 'gd_placecategory',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => array(
                    0 => 'permalink',
                    1 => 'the_content',
                    2 => 'excerpt',
                    3 => 'discussion',
                    4 => 'comments',
                    5 => 'revisions',
                    6 => 'slug',
                    7 => 'author',
                    8 => 'format',
                    9 => 'page_attributes',
                    10 => 'featured_image',
                    11 => 'tags',
                    12 => 'send-trackbacks',
                ),
                'active' => true,
                'description' => '',
            ));
            
        endif;

    }
}
