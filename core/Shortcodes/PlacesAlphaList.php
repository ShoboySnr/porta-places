<?php
/**
 * List all the available locations via shortcode.
 */
namespace Porta_Places\Shortcodes;

use Porta_Places\Base\BaseController;
use WP_Query;

/**
 * List all the available locations via `locations_alpha_list` shortcode.
 */
class PlacesAlphaList extends BaseController {
    
    /**
     * Register `locations_alpha_list` shortcode.
     *
     * @return void
     */
    public function register() {
        add_shortcode( 'places_alpha_list', [ $this, 'places_alpha_list' ] );
    }
    
    /**
     * Display the Locations lists.
     *
     * @param array  $atts    Default Attributes from Shortcode Registration.
     * @param string $content Any content passed by shortcode usage in Editor.
     * @return void
     */
    public function places_alpha_list( $atts = [], $content = '' ) {

        wp_enqueue_style( 'alpha-location-styles',  $this->plugin_url . 'assets/build/css/places-alpha-locations.css' );

        $args            = [
            'post_type'      => 'gd_place',
            'posts_per_page' => '-1',
            'orderby'        => 'post_title',
            'order'          => 'ASC',
        ];

        $query           = new WP_Query($args);
        $alpha_locations = [];
        
        if ($query->have_posts()) {
            foreach ($query->posts as $post) {
                $alphabet                     = strtoupper(substr($post->post_title, 0, 1));
                $alpha_locations[$alphabet][] = $post;
            }
        }
        
        require_once $this->plugin_path . 'template-parts/shortcodes/places-alpha-list.php';
        wp_reset_query();
    }

}