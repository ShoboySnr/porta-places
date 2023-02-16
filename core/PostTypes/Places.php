<?php
/**
 * Get the permalink of the location post type.
 * Append the place id to it.
 */
namespace Porta_Places\PostTypes;

/**
 * New permalink of the location CPT with place id.
 */
class Places {

    public function register() {
        add_action( 'init', [ $this, 'place_cpt_init' ] );
        add_filter( 'post_type_link', [ $this, 'prefix_location_permalink' ], 10, 2 );
    }

    /**
     * Postfix location CPT permalink URL
     * prior to being returned by get_post_permalink() .
     *
     * @param string  $url  Post Permalink.
     * @param Object  $post Post Object.
     * @return string $url  Post Permalink.
     */
    public function prefix_location_permalink( $url, $post ) {

        $post_id = $post->ID;

        if ( 'places' === get_post_type( $post_id ) && 'publish' === get_post_status( $post_id ) ) {

            // Location returns an array.
            $location = get_field( 'location', $post_id );
            
            if( !empty($location[0]->ID )){
                $locpermalink = get_permalink( $location[0]->ID );
                $url          = $locpermalink . "#" . $post_id;
            }

        }

        return $url;
    }

    /**
     * Register Location Custom Post Type.
     *
     * @return void
     */
    public function place_cpt_init() {

        $place_labels = array (
            'name'               => _x( 'Places', 'post type general name', 'porta-places' ),
            'singular_name'      => _x( 'Place', 'post type singular name', 'porta-places' ),
            'add_new'            => _x( 'Add New', 'Place', 'porta-places' ),
            'add_new_item'       => __( 'Add New Place', 'porta-places' ),
            'edit_item'          => __( 'Edit Place', 'porta-places' ),
            'new_item'           => __( 'New Place', 'porta-places' ),
            'all_items'          => __( 'All Places', 'porta-places' ),
            'view_item'          => __( 'View Place', 'porta-places' ),
            'search_items'       => __( 'Search Places', 'porta-places' ),
            'not_found'          => __( 'No Place found', 'porta-places' ),
            'not_found_in_trash' => __( 'No Places found in the Trash', 'porta-places' ),
            'menu_name'          => __( 'Places', 'porta-places' )
        );
    
        $rewrite = array(
            'slug'                  => 'places',
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        );

        //Arguments for the place labels
        $place_args = array(
            'label'                 => __( 'Places', 'porta-core' ),
            'labels'        => $place_labels,
            'description'   => __( 'Holds Porta places data', 'porta-places' ),
            'public'        => true,
            'menu_position' => 6,
            'supports'      => array(
                'title',
                'thumbnail',
                'custom-fields',
            ),
            'has_archive'   => 'places',
            'show_in_rest'  => true,
            'menu_icon'     => 'dashicons-location-alt',
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'rewrite'               => $rewrite,
            'capability_type'       => 'post',
        );

        //Register the post type
        register_post_type( 'gd_place', $place_args );

        $this->acf_add_local_field_group();
    }

    /**
     * Add in the ACF Fields for Places Custom Post Type
     *
     * @return void
     */
    protected function acf_add_local_field_group() {

        if( function_exists('acf_add_local_field_group') ):

            acf_add_local_field_group(array(
                'key' => 'group_6087a015bf5d0',
                'title' => 'Place Information',
                'fields' => array(
                    array(
                        'key' => 'field_6087fcc498917',
                        'label' => 'Address Information',
                        'name' => '',
                        'type' => 'tab',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'placement' => 'top',
                        'endpoint' => 0,
                    ),
                    array(
                        'key' => 'field_6087b11198c51',
                        'label' => 'Address Line 1',
                        'name' => 'address_line_1',
                        'type' => 'text',
                        'instructions' => 'Enter main street address',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_6087b14dc760f',
                        'label' => 'Address Line 2',
                        'name' => 'address_line_2',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_6087b185307b4',
                        'label' => 'City',
                        'name' => 'city',
                        'type' => 'text',
                        'instructions' => 'Enter the city location',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_6087b19ba9088',
                        'label' => 'State',
                        'name' => 'state',
                        'type' => 'select',
                        'instructions' => 'Please select the state for the place location address',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'Vic' => 'Vic',
                            'NSW' => 'NSW',
                            'Tas' => 'Tas',
                            'ACT' => 'ACT',
                            'SA' => 'SA',
                            'QLD' => 'QLD',
                            'WA' => 'WA',
                            'NT' => 'NT',
                            'Queensland' => 'Queensland',
                        ),
                        'default_value' => false,
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                    array(
                        'key' => 'field_6087b213d6290',
                        'label' => 'Postcode',
                        'name' => 'postcode',
                        'type' => 'text',
                        'instructions' => 'Please enter a valid postcode',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => 4,
                    ),
                    array(
                        'key' => 'field_6087b27bb91e7',
                        'label' => 'Latitude',
                        'name' => 'lat',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_6087b264a2826',
                        'label' => 'Longitude',
                        'name' => 'long',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_6087b3164c751',
                        'label' => 'Country',
                        'name' => 'country',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => 'Australia',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'gd_place',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'acf_after_title',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => array(
                    0 => 'permalink',
                    1 => 'the_content',
                    2 => 'excerpt',
                    3 => 'discussion',
                    4 => 'comments',
                    5 => 'slug',
                    6 => 'author',
                    7 => 'format',
                    8 => 'page_attributes',
                    9 => 'featured_image',
                    10 => 'send-trackbacks',
                ),
                'active' => true,
                'description' => '',
            ));
            
            endif;
    }

}
