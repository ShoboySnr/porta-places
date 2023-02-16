<?php

/**
 * Trigger this file on Plugin uninstall.
 *
 * @package  CVGT_Locations.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// List all the post types available for the loo deletion.
$available_posts_types = ['locations', 'gd_place'];

foreach( $available_posts_types as $available_posts_type ) {
	
	$offices = get_posts( array( 'post_type' => $available_posts_type, 'numberposts' => -1 ) );
	
	// Clear Database stored data.
	foreach( $offices as $office ) {
		wp_delete_post( $office->ID, true );
	}
	
	// Double check by Accessing the database via SQL.
	global $wpdb;
	$wpdb->query( "DELETE FROM wp_posts WHERE post_type = $available_posts_type" );
	$wpdb->query( "DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)" );
	$wpdb->query( "DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id FROM wp_posts)" );

}
