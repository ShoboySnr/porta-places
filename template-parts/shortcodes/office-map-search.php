<?php
/**
 * Add filters and Map for Locations Page.
 */
?>
<div class="parent-div">
	<div class="searchbar"> 
		<div class="search-term">
			<label><?php _e( 'Select a Service', 'cvgt-locations' ); ?></label>
			<!-- TODO: Use a class to tap the event not the `onchange`. -->
			<select id="services-select" class="services" onchange="filterMarkersCat(this.value);" >
				<option value="all-services" ><?php _e( 'All Services', 'cvgt-locations' ); ?></option>
				<?php
					unset($categories);

				 	$categories = get_terms( $taxonomy );

					foreach( $categories as $category ) {
				?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_attr( $category->name ); ?></option>
				<?php 
					}
					
					unset($categories);
				?>  
			</select>
		</div>

		<div class="search-term">
			<label><?php _e( 'Find your City/State', 'cvgt-locations' ); ?></label>
			<input id="suburb" type="text" placeholder="Suburb">
			<a id="reset-map-search" href="#clear" title="Clear your Search">Clear Suburb</a>
		</div>
	</div>
	<div class="offmap">
		<div class="all-gd_place" id="place-data"></div>
		<div id="map-canvas" class="acf-map"></div> 
	</div>
</div>
		 

