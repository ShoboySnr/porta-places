<?php
/**
 * Display List of Locations.
 */
    if ( empty( $alpha_locations ) ) {
        ?>
            <h2><?php _e( 'No places found.', 'porta-places' ); ?></h2>
        <?php
            return;
    }
?>
<ul class="locations-alpha-navigation">
    <?php foreach ( $alpha_locations as $alphabet => $posts ) { ?>
        <li><a href="#alpha-<?php echo esc_attr( $alphabet ); ?>"><?php echo esc_attr( $alphabet ); ?></a></li>
    <?php } ?>
</ul>

<div id="locationsgrid">
    <?php foreach ( $alpha_locations as $alphabet => $posts ) { ?>
        <div class="location_group">
            <h2 class='heading' id="alpha-<?php echo esc_attr( $alphabet ); ?>"><?php echo esc_attr( $alphabet ); ?></h2>
            <ul>
                <?php foreach ( $posts as $post ) { ?>
                    <li>
                        <a href="<?php echo esc_url( get_the_permalink( $post ) ); ?>"><?php echo esc_attr( $post->post_title ); ?></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
</div>