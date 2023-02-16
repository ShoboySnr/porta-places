<?php
 /**
 * Carousel Block Template.
 */

$id = 'cvgt-locations-' . $block['id'];

if( !empty($block['anchor']) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'cvgt-locations-block';

if( !empty($block['className']) ) {
	$className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
	$className .= ' align' . $block['align'];
}

// Parent Location title.
$parent_title = get_the_title($post_id);
// Attached gd_place to location.
$locations = get_field( 'location', $post_id );

?>
<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">

	<?php

		// Instatiate for Counter attached gd_place.
		$first = true;

		foreach ( $locations as $location ) {

			$office_id                 = $location->ID;
			$title                     = get_the_title( $office_id );
			$offlink                   = get_permalink( $office_id );

			$address                   = get_field( 'address_line_1', $office_id );
			$address2                  = get_field( 'address_line_2', $office_id );
			$city                      = get_field( 'city', $office_id );
			$state                     = get_field( 'state', $office_id );
			$postcode                  = get_field( 'postcode', $office_id );
			$country                   = get_field( 'country', $office_id );
			$lat                       = get_field( 'lat', $office_id );
			$lon                       = get_field( 'long', $office_id );
			
			// Get the Office Hours.
			$monday_hours              = get_field( 'monday_hours', $office_id );
			$office_hours['monday']    = $monday_hours['open'] ? $monday_hours['open_time'] . ' - ' . $monday_hours['close_time'] : $monday_hours['open_for_monday_appointments'];

			$tuesday_hours             = get_field( 'tuesday_hours', $office_id );
			$office_hours['tuesday']   = $tuesday_hours['open'] ? $tuesday_hours['open_time'] . ' - ' . $tuesday_hours['close_time'] : $tuesday_hours['open_for_tuesday_appointments'];

			$wednesday_hours           = get_field( 'wednesday_hours', $office_id );
			$office_hours['wednesday'] = $wednesday_hours['open'] ? $wednesday_hours['open_time'] . ' - ' . $wednesday_hours['close_time'] : $wednesday_hours['open_for_wednesday_appointments'];

			$thursday_hours            = get_field( 'thursday_hours', $office_id );
			$office_hours['thursday']  = $thursday_hours['open'] ? $thursday_hours['open_time'] . ' - ' . $thursday_hours['close_time'] : $thursday_hours['open_for_thursday_appointments'];

			$friday_hours              = get_field( 'friday_hours', $office_id );
			$office_hours['friday']    = $friday_hours['open'] ? $friday_hours['open_time'] . ' - ' . $friday_hours['close_time'] : $friday_hours['open_for_friday_appointments'];

			$saturday_hours            = get_field( 'saturday_hours', $office_id );
			$office_hours['saturday']  = $saturday_hours['open'] ? $saturday_hours['open_time'] . ' - ' . $saturday_hours['close_time'] : $saturday_hours['open_for_saturday_appointments'];

			$sunday_hours              = get_field( 'sunday_hours', $office_id );
			$office_hours['sunday']    = $sunday_hours['open'] ? $sunday_hours['open_time'] . ' - ' . $sunday_hours['close_time'] : $sunday_hours['open_for_sunday_appointments'];

			//get the phone number and secondary phone number
			$phone                     = get_field( 'phone', $office_id );
			$secondary_phone           = get_field( 'secondary_phone', $office_id ) ;

			$office_anchor_id =  function( $office_id ){
				return strval( $office_id );
			};
				
		?>
			<div class="office-location" id="<?php echo $office_id; ?>" >
				<h3 class="grid-width-title office-title" property="name"><?php echo esc_attr( $title ); ?></h3>
				<div id="office-blurb" class="office-blurb">
				<?php
					// Get general message for the first loop item.
					if ( $first ) {
						printf( 
							'<p>%s</p>',
							get_field('general_support_message', 'option')
						);
						$first = false;
					}

					// Get the terms in category.
					$terms = get_terms( array(
						'taxonomy' => 'gd_placecategory',
						'hide_empty' => false,
					) );

					$services = [];

					// Set up array of the sentences.
					foreach ( $terms as $term ) {
						$item = [
							[
								'slug'        => $term->slug,
								'description' => get_field('service_area_description', 'gd_placecategory_' . $term->term_id ),
							] 
						];
						array_push( $services, $item );
					}

					foreach( $services as $service ) {

						$svs_title = $service[0]['slug'];
						$title     = strtolower($title);

						if ( false !== strpos( $title , cvgt_whole_title( $svs_title ) ) || false !== strpos( $title , cvgt_words_joined( $svs_title ) ) || false !== strpos( $title,cvgt_first_letters_joined ( $svs_title ) ) ) {
							echo $service[0]['description'];
						}      

					}
				?>
				</div>

				<div class="pre-map" vocab="https://schema.org/" typeof="EmploymentAgency" >
					<div style="display:none;">
						<span property="telephone"><?php echo esc_attr( $phone ); ?></span>
						<span property="priceRange">0</span>
						<img loading="eager" property="image" data-src="https://staging-cvgtau.kinsta.cloud/wp-content/uploads/2020/02/parentsnext-mother-with-child-1024x640.jpg" src="https://staging-cvgtau.kinsta.cloud/wp-content/uploads/2020/02/parentsnext-mother-with-child-1024x640.jpg" alt="parentsnext service mother with child" />
					</div>
					<h3 class="cvgt-hidden" property="name"><?php echo esc_attr( $title ); ?></h3>
					<div class="adressdet">
						<h5><?php _e( 'Address Details', 'cvgt-locations' ); ?></h5>
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
							$days_by_office_hours = [];

							foreach( $office_hours as $day => $hours ) {
								$days_by_office_hours[$hours][] = $day;
							}

							if ( is_array($days_by_office_hours) || ! empty($days_by_office_hours) ) {
								foreach ( $days_by_office_hours as $time => $collection ) {
									$day_names = ucwords(implode( ", ", $collection ));

									if ( is_string( $time ) ) {
										echo '<p>' . $day_names . ' ' . cvgt_short_clean_time( $time ) . '. ' . '</p>';
									}

									if( 1 == $time ) {
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
							foreach ( $office_hours as $day => $hours ) {
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

				<div class="acf-map" id="map-<?php echo esc_attr( $office_id ); ?>" property="geo" typeof="GeoCoordinates">
					<meta property="latitude" content="<?php echo esc_attr($lat); ?>" />
					<meta property="longitude" content="<?php echo esc_attr($lon); ?>" />
					<div id="marker-<?php echo esc_attr( $office_id ); ?>"
							data-lat="<?php echo esc_attr( $lat ); ?>"
							data-lng="<?php echo esc_attr( $lon ); ?>"
							data-title='<?php echo esc_attr( $title ); ?>'
							data-address='<?php echo esc_attr( $address ); ?>'
							data-state='<?php echo esc_attr( $state ); ?>'
							data-city='<?php echo esc_attr( $city ); ?>'
							data-postcode='<?php echo esc_attr( $postcode ); ?>'
							data-officelink='<?php echo esc_attr( $offlink ); ?>' ></div>
					
					<script>
						addEventListener( 'DOMContentLoaded', (event) => {
							google.maps.event.addDomListener( window,'load', Office_Map( 'marker-<?php echo esc_attr( $office_id ); ?>', 'map-<?php echo esc_attr( $office_id ); ?>','<?php echo esc_attr( $postcode ); ?>'));
						});
					</script>
				</div> <!--End acf-map-->

			</div><!-- End Location-->

			<?php 
		}
	?>
</div>