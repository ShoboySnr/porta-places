<?php 
/**
 * TODO: Remove after template plugin concept
 * @package  CVGT_Locations
 */
namespace Porta_Places\Base;

use Porta_Places\Base\BaseController;

/**
* 
*/
class Enqueue extends BaseController
{
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'enqueue_scripts', array( $this, 'front_enqueue' ) );
	}
	
	function admin_enqueue() {
		// enqueue all our admin scripts
		//wp_enqueue_style( 'mypluginstyle', $this->plugin_url . 'assets/mystyle.css' );
	}
	
	function front_enqueue() {
		// enqueue all our front scripts
		//wp_enqueue_style( 'mypluginstyle', $this->plugin_url . 'assets/mystyle.css' );
	}
}