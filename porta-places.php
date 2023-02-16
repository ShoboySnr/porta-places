<?php
/**
 * Plugin Name:     Porta Places
 * Plugin URI:      https://www.mrkwp.com
 * Description:     Porta Places and Location Management Plugin by MRK WP
 * Author:          MRK WP
 * Author URI:      https://www.mrkwp.com
 * Text Domain:     cvgt-locations
 * Domain Path:     /languages
 * Version:         1.0.7
 *
 * @package
 */

// If this file is called directly, abort!!!
defined( 'ABSPATH' ) or die( 'No Access!' );

// Declare constants.
if ( ! defined( 'PORTA_PLACES_GOOGLE_API_KEY' ) ) {
	define( 'PORTA_PLACES_GOOGLE_API_KEY', 'AIzaSyDSGXiLg9kRk_93B-s_2VFkrnqHfULeZtI' );
}
if ( ! defined( 'PORTA_PLACES_GOOGLE_API_KEY2' ) ) {
	define( 'PORTA_PLACES_GOOGLE_API_KEY2', 'AIzaSyDBWFBX5Mp16wifJwiZ6WRib-uN0oRIKCo' );
}

// Require once the Composer Autoload.
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * The code that runs during plugin activation.
 *
 * @return void
 */
function activate_porta_places_plugin() {
	Porta_Places\Base\Activate::activate();
}
register_activation_hook( __FILE__, 'activate_porta_places_plugin' );

/**
 * The code that runs during plugin deactivation.
 *
 * @return void
 */
function deactivate_porta_places_plugin() {
	Porta_Places\Base\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_porta_places_plugin' );

/**
 * Initialize all the core classes of the plugin.
 */
if ( class_exists( 'Porta_Places\\Init' ) ) {
	
	if ( file_exists( dirname( __FILE__ ) . '/helpers/helper-functions.php' ) ) {
		require_once dirname( __FILE__ ) . '/helpers/helper-functions.php';
	}

	Porta_Places\Init::register_services();

}
