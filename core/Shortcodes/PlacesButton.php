<?php
/**
 * Register `cvgt_office_info` shortcode.
 * Shortcode function was reversed in use with `cvgt_office_btn_group`.
 * to avoid massive update in all locations.
 */
namespace Porta_Places\Shortcodes;

use Porta_Places\Base\BaseController;

/**
 * Register `cvgt_office_info` shortcode.
 * Returns info + map that belong to this location via shortcode.
 */
class PlacesButton extends BaseController {

    /**
     * Register `place_office_info` shortcode.
     * Place Info via shortcode.
     *
     * @return void
     */
    public function register() {
        add_shortcode( 'place_office_info', [ $this, 'place_btns_shortcode'] );
    }
    
    /**
     * Returns gd_place that belong to this location + gd_place link buttons.
     *
     * @return void
     */
    public function place_btns_shortcode() {

        $offices = get_field( 'location', get_the_ID() );

        if( empty( $offices ) ) {
            return;
        }

        // Enqueue scripts + buttons template.
        wp_enqueue_style( 'location-styles', $this->plugin_url . '/assets/build/css/porta-places.css' );
        wp_enqueue_script( 'google-maps-apikey', 'https://maps.googleapis.com/maps/api/js?key=' . PORTA_PLACES_GOOGLE_API_KEY . '&callback' );
        wp_enqueue_script( 'office-script', $this->plugin_url . '/assets/build/js/office.js', array( 'google-maps-apikey' ));

        ob_start();
        require_once( $this->plugin_path . '/template-parts/shortcodes/place-buttons.php' );
        return ob_get_clean();

    }

}