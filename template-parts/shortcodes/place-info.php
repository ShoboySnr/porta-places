<?php
/**
 * Display the place Information in `porta_place_info` shortcode.
 */

// Instatiate for Counter.
$first = true;
?>

<div id="place-locations">
	<?php
		// loop through the different place locations.
		foreach( $places as $place ):

			var_dump($place);

			$place_id                 = $place->ID;
			$title                     = get_the_title( $place_id );
			$offlink                   = get_permalink( $place_id );
			//$traditional_country       = get_field( 'traditional_country', $place_id );
			$address                   = get_field( 'address_line_1', $place_id );
			$address2                  = get_field( 'address_line_2', $place_id );
			$city                      = get_field( 'city', $place_id );
			$state                     = get_field( 'state', $place_id );
			$postcode                  = get_field( 'postcode', $place_id );
			$country                   = get_field( 'country', $place_id );
			$lat                       = get_field( 'lat', $place_id );
			$lon                       = get_field( 'long', $place_id );
			
			//get the phone number and secondary phone number
			$phone                     = get_field( 'phone', $place_id );
			//$secondary_phone           = get_field( 'secondary_phone', $place_id ) ;

			$place_anchor_id =  function( $place_id ){
				return strval( $place_id );
			};
		?>
			<div class="office-location" id="<?php echo $place->ID; ?>" >
				<h3 class="grid-width-title office-title" property="name"><?php echo esc_attr( $title ); ?></h3>
				<div id="office-blurb" class="office-blurb">
				
				</div>
					<?php
/**
 * Set the schema for a directory listing from here
 */
					?>
				<div class="pre-map" vocab="https://schema.org/" typeof="" >
					<div style="display:none;">
						<span property="telephone"><?php echo esc_attr( $phone ); ?></span>
						<span property="priceRange">0</span>
						<img loading="eager" property="image" data-src="https://staging-cvgtau.kinsta.cloud/wp-content/uploads/2020/02/parentsnext-mother-with-child-1024x640.jpg" src="https://staging-cvgtau.kinsta.cloud/wp-content/uploads/2020/02/parentsnext-mother-with-child-1024x640.jpg" alt="parentsnext service mother with child" />
					</div>
					
					<h3 class="cvgt-hidden" property="name"><?php echo esc_attr( $title ); ?></h3>
					<div class="adressdet">
						<h5><?php _e( 'Address Details', 'cvgt-locations' ); ?></h5>
						<p><?php echo esc_attr($traditional_country); ?></p>
						<div class="wpseo-address-wrapper" property="address" typeof="PostalAddress">
						<?php
							if( empty( $address2 ) ) { ?>
								<div class="street-address" property="streetAddress"><?php echo esc_attr( $address ); ?></div>
						<?php
							}
							else {
						?>
								<div class="street-address"><?php echo esc_attr( $address ) . "<br>" . esc_attr( $address2 ); ?></div>
						<?php
							}
						?>
							<span class="locality" property="addressLocality"><?php echo esc_attr( $city ); ?></span>
							<span class="region" property="addressRegion"><?php echo esc_attr( $state ); ?></span>
							<span class="postal-code" property="postalCode"><?php echo esc_attr( $postcode ); ?></span>
							<div class="country-name" property="addressCountry"><?php echo esc_attr( $country ); ?></div>
						</div>
						<span class="wpseo-phone"><?php _e( 'Phone:', 'cvgt-locations' ); ?> <a href="tel:<?php echo esc_attr( $phone ); ?>" class="tel"><span><?php echo esc_attr( $phone ); ?></span></a></span>
						<?php if( ! empty($secondary_phone) ) { ?>
							<br><span class="wpseo-phone"><?php _e( 'Other Phone:', 'cvgt-locations' ); ?><a href="tel:<?php echo esc_attr( $secondary_phone ); ?>" class="tel"><span><?php echo esc_attr( $secondary_phone ); ?></span></a></span>
						<?php } ?>
						<div class="ugb-button-container">
							<a class="ugb-button ugb-button--size-normal directions" data-type="URL" data-id="https://www.google.com/maps/dir/?api=1&amp;destination=<?php echo esc_attr( $lat .','. $lon ); ?>" href="https://www.google.com/maps/dir/?api=1&amp;destination=<?php echo esc_attr( $lat .','. $lon ); ?>" target="_blank" rel="noreferrer noopener" rel="nofollow" title="<?php _e( 'Get Directions (opens new window)', 'cvgt-locations' ); ?>">
								<span class="ugb-button--inner"><?php _e( 'Get Directions (opens new window)', 'cvgt-locations' ); ?></span>
							</a>
						</div>
					</div>
					<div class="openhours">
						<h5><?php _e( 'Opening Hours', 'cvgt-locations' ); ?></h5>
						<?php
							$days_by_place_hours = [];

							foreach( $place_hours as $day => $hours ) {
								$days_by_place_hours[$hours][] = $day;
							}

							if ( is_array($days_by_place_hours) || ! empty($days_by_place_hours) ) {
								foreach ( $days_by_place_hours as $time => $collection ) {
									$day_names = ucwords(implode( ", ", $collection ));

									if ( is_string( $time ) ) {
										echo '<p>' . $day_names . ' ' . cvgt_short_clean_time( $time ) . '. ' . '</p>';
									}

									if( 1 === $time ) {
										if ( count($collection) === 1 ) {
											printf(
												'<p>%s is available on appointments</p>',
												$day_names
											);
										}

										if ( count($collection) > 1 ) {
											printf(
												'<p>%s are available on appointments</p>',
												$day_names
											);
										}
									}
								}
							}

							// Hidden for SEO.
							foreach ( $place_hours as $day => $hours ) {
								$hours = str_replace( ' ', '', $hours );
								if( 1 == $hours ) {
									$hours = 'Appointment';
								}
								if( empty( $hours ) ) {
									$hours = 'Closed';
								}
								
								// Hidden for SEO
								echo '<meta property="openingHours" content="' . ucfirst($day) . ' ' . $hours . '">';
							}
						?>
					</div>
				</div> <!-- End pre-map -->

				<div class="acf-map" id="map-<?php echo esc_attr( $place_id ); ?>" property="geo" typeof="GeoCoordinates">
					<meta property="latitude" content="<?php echo esc_attr($lat); ?>" />
					<meta property="longitude" content="<?php echo esc_attr($lon); ?>" />
					<div id="marker-<?php echo esc_attr( $place_id ); ?>"
							data-lat="<?php echo esc_attr( $lat ); ?>"
							data-lng="<?php echo esc_attr( $lon ); ?>"
							data-title='<?php echo esc_attr( $title ); ?>'
							data-address='<?php echo esc_attr( $address ); ?>'
							data-state='<?php echo esc_attr( $state ); ?>'
							data-city='<?php echo esc_attr( $city ); ?>'
							data-postcode='<?php echo esc_attr( $postcode ); ?>'
							data-placelink='<?php echo esc_attr( $offlink ); ?>' ></div>
					
					<script>
						addEventListener( 'DOMContentLoaded', (event) => {
							google.maps.event.addDomListener( window,'load', Office_Map( 'marker-<?php echo esc_attr( $place_id ); ?>', 'map-<?php echo esc_attr( $place_id ); ?>','<?php echo esc_attr( $postcode ); ?>'));
						});
					</script>
				</div> <!--End RDFa schema-->
			</div> <!-- End place-location -->
	<?php
		$first = false;
		endforeach;
	?>
</div><!-- End place-locations -->