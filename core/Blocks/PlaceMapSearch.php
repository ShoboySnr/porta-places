<?php
/**
 * Register Blocks for Office Map Search
 *
 * @package  CVGT_Location
 */
namespace Porta_Places\Blocks;

use Porta_Places\Base\BaseController;

/**
 * Office Map Search Block.
 */
class PlaceMapSearch extends  BaseController {
    
    /**
     * Register function is called by default to get the class running
     *
     * @return void
     */
    public function register() {
        add_action( 'init' , [ $this, 'create_place_map_search_init' ]);
    }
    
    /**
     * Get Place Map Search is a render callback for the dynamic block - location map search.
     * Returns a formatted list for Gutenberg block
     *
     * @param $attr
     * @param $content
     * @return string
     */
    public function get_place_map_search($attr, $content) {
        $service_area = '';
        if(isset($attr['serviceArea'])) {
          $service_area = $attr['serviceArea'];
        }
        
        return '<div class="office-map-search">'.  do_shortcode('[office_map_search service_area="'.$service_area.'"]'). '</div>';;
        
    }
    
    /**
     * Register block function called by init hook
     *
     * @return void
     */
    public function create_place_map_search_init() {
        register_block_type_from_metadata( $this->plugin_path . 'build/place-map-search/'  , [
            'render_callback' => [ $this, 'get_place_map_search'],
        ] );
    }
}


