<?php
/**
 * Register `cvgt_office_btn_group` shortcode
 * Shortcode function was reversed in use with `cvgt_office_info`.
 * to avoid massive update in all locations.
 */
namespace Porta_Places\Shortcodes;

use Porta_Places\Base\BaseController;

/**
 * Register `cvgt_office_btn_group` shortcode.
 * Returns gd_place that belong to this location in gd_place link buttons.
 * Number of buttons depend on the number of gd_place related to this location.
 */
class PlaceInfo extends BaseController {

	/**
	* Register `place_info` shortcode.
	*
	* @return void
	*/
	public function register() {
		add_shortcode( 'porta_places_btn_group', [ $this, 'place_info_shortcode'] );
	}

	/**
	* Displays the gd_place Info.
	*
	* @return void
	*/
	public function place_info_shortcode() {

		$places = get_field( 'gd_place', get_the_ID() );

		if( empty( $places ) ) {

			echo "places is empty";

			return;
		}

		// Enqueue scripts + buttons template.
		wp_enqueue_style( 'location-styles', $this->plugin_url . 'assets/build/css/porta-places.css');
		wp_enqueue_script( 'google-maps-apikey', 'https://maps.googleapis.com/maps/api/js?key=' . PORTA_PLACES_GOOGLE_API_KEY2. '&amp;libraries=places' );
		wp_enqueue_script( 'office-script', $this->plugin_url . 'assets/build/js/place.js' );

		ob_start();
		require_once( $this->plugin_path . '/template-parts/shortcodes/place-info.php' );
		return ob_get_clean();

	}
}