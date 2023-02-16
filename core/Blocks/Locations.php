<?php
/**
 * @package  CVGT_Location
 */
namespace Porta_Places\Blocks;

use Porta_Places\Base\BaseController;

/**
 * Locations Block.
 */
class Locations extends BaseController {

    /**
     * Register function is called by default to get the class running
     *
     * @return void
     */
    public function register() {
        add_action( 'acf/init' , [ $this, 'init_locations_block' ] );
	}

    /**
     * Initialise the BGC locations Block
     * 
     * @return void
     */
    public function init_locations_block() {

        // register the block
        if( function_exists('acf_register_block_type') ) {
            acf_register_block_type(array(
                'name'              => 'cvgt-locations',
                'title'             => __('Porta Locations Block'),
                'description'       => __('Porta Locations Block'),
                'render_template'   => $this->plugin_path .'template-parts/blocks/locations/locations.php',
                'category'          => 'formatting',
                'icon'              => 'location-alt',
                'keywords'          => array( 'office', 'location', 'Porta' ),
                'enqueue_assets'  => function() {
                    wp_enqueue_style( 'location-styles', $this->plugin_url . 'assets/build/css/cvgt-locations.css' );
                    wp_enqueue_script( 'google-maps-apikey', 'https://maps.googleapis.com/maps/api/js?key=' . PORTA_PLACES_GOOGLE_API_KEY2. '&amp;libraries=places' );
                    wp_enqueue_script( 'office-script', $this->plugin_url . 'assets/build/js/office.js' );
				},
            ));
        }
    }
    
}