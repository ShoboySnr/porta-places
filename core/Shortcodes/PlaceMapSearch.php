<?php

namespace Porta_Places\Shortcodes;

use Porta_Places\Base\BaseController;

class PlaceMapSearch extends BaseController {
    
    /**
     * Register `office_map_search` shortcode.
     *
     * @return void
     */
    public function register() {
        add_shortcode( 'place_map_search', [ $this, 'place_map_search'] );
        add_shortcode( 'single_place_map_search', [ $this, 'single_place_map_search'] );
        add_action( 'wp_ajax_nopriv_remove_from_places_favourites', [$this, 'places_user_favourites']);
        add_action( 'wp_ajax_remove_from_places_favourites', [$this, 'places_user_favourites']);
        add_action( 'wp_ajax_nopriv_add_to_places_favourites', [$this, 'places_user_favourites']);
        add_action( 'wp_ajax_add_to_places_favourites', [$this, 'places_user_favourites']);
    }

    /**
     * Get the id(s)  of the category of each office.
     *
     * @param int    $id           Category ID.
     * @param string $taxonomy     Taxonomy Name.
     * @return array $category_ids Category IDs.
     */
    function get_office_category_id( $id, $taxonomy ) {

        $category_ids = [];
        $categories  = get_the_terms( $id, $taxonomy );

        if( empty( $categories ) ) {
            return;
        }

        foreach( $categories as $category ){
            array_push( $category_ids, $category->term_id );
        }

        return $category_ids;

    }
    
    /**
     * Get the name and url  of the category of each office.
     *
     * @param int    $id           Category ID.
     * @param string $taxonomy     Taxonomy Name.
     * @return array $category_ids Category IDs.
     */
    function get_place_category( $id, $taxonomy, $all_items = false ) {
        $categories  = get_the_terms( $id, $taxonomy );
        
        if($all_items && !empty($categories)) {
          $results = [];
          foreach ($categories as $category) {
            array_push($results, [
                'id' => $category->term_id,
                'link' => get_term_link($category->term_id),
                'name' => $category->name,
                'html' => sprintf('<a href="%s" title="%s">%s</a>', get_term_link($category->term_id), $category->name, $category->name),
            ]);
          }
          
          return $results;
        }
        
        if( empty( $categories[0] ) ) return;
        
        return [
            'id' => $categories[0]->term_id,
            'link' => get_term_link($categories[0]->term_id),
            'name' => $categories[0]->name,
        ];
        
    }

    function async_loading( $tag, $handle ) {
        if ( 'google-maps-apikey' !== $handle ) {
            return $tag;
        }
        return str_replace( ' src', ' async src', $tag );
    }
    
    
    public function places_user_favourites() {
        if(!wp_verify_nonce($_POST['nonce'], 'porta-favourites-nonce')) {
            wp_die();
        }
    
        $user_id = (int) sanitize_text_field($_POST['user_id']);
        $place_id = (int) sanitize_text_field($_POST['place_id']);
        $action = sanitize_text_field($_POST['action']);
        
        if(empty($user_id)) {
            $current_url = sanitize_url($_POST['current_url']);
            $response = [
                'status'      => false,
                'message'     => 'Cannot add to favourites as user is not logged in.',
                'link'        => wp_login_url($current_url)
            ];
            
            wp_send_json_error($response, 401);
            wp_die();
        }
        
        $user_saved_favourites = (array) get_user_meta($user_id, '_user_bookmarked_places', true);
        
        $color = '';
        $return_action = '';
        if($action == 'remove_from_places_favourites') {
          $array_index = array_search($place_id, $user_saved_favourites);
          unset($user_saved_favourites[$array_index]);
          $color = 'grey';
          $return_action = 'remove';
        } else if( $action == 'add_to_places_favourites') {
            array_push($user_saved_favourites, $place_id);
            $color = '#e84739';
            $return_action = 'added';
        }
        
        $saved_places = update_user_meta($user_id, '_user_bookmarked_places', array_unique($user_saved_favourites));
        
        if(!$saved_places) {
            $response = [
                'status'      => false,
                'message'     => 'Something went wrong, cannot add to favourites',
            ];
    
            wp_send_json_error($response, 403);
            wp_die();
        }
    
    
        $response = [
            'status'      => true,
            'message'     => 'Place added to user favourites successfully.',
            'color'       => $color,
            'action'      => $return_action
        ];
    
        wp_send_json_success($response, 200);
        
        wp_die();
    }
    
    /**
     * Display Office map search.
     *
     * @param array $atts         Default Attributes from Shortcode Registration.
     * @param string $content      Any content passed by shortcode usage in Editor.
     */
    public function place_map_search($atts, $content) {
      global $wp;
      
        $args = wp_parse_args($atts, [
            'place_category'  => !empty($_GET['place_category']) ? $_GET['place_category'] : '',
            'distance'  => !empty($_GET['dist']) ? $_GET['dist'] : '',
            'city'  => !empty($_GET['city']) ? $_GET['city'] : '',
            'lat'  => !empty($_GET['lat']) ? $_GET['lat'] : '',
            'lng'  => !empty($_GET['lng']) ? $_GET['lng'] : '',
        ]);
    
        $place_category = $args['place_category'];
        $distance = $args['distance'];
        $city = $args['city'];
        $lat = $args['lat'];
        $lng = $args['lng'];
        $taxonomy = 'gd_placecategory';
        $place_term_id = 0;
        
        if(!empty($place_category)) {
          $place_term = get_term_by('slug', $place_category, $taxonomy);
          if(!empty($place_term)) $place_term_id = $place_term->term_id;
        }
        
        $all_places = $this->get_all_places($taxonomy, $place_category, ['lat' => $lat, 'lng' => $lng, 'distance' => $distance]);
        
        $post_type_link = get_post_type_archive_link('gd_place');
    
        $wp_ajax_url = admin_url('admin-ajax.php');
    
        wp_enqueue_style( 'places-map-search', $this->plugin_url . '/assets/build/css/places-map-search.css' );
        wp_enqueue_script( 'google-maps-custering', 'https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js' );
        wp_enqueue_script( 'google-maps-apikey', 'https://maps.googleapis.com/maps/api/js?key=' . PORTA_PLACES_GOOGLE_API_KEY2. '&amp;libraries=places&amp;callback=maps_setup' );
        wp_enqueue_script( 'places-map-search', $this->plugin_url . 'assets/build/js/places-map-search.js' ,array('google-maps-apikey'),'2', false);
        wp_localize_script( 'places-map-search', 'map_search_data', array( 'placedata' => $all_places, 'selected_placedata' => $place_term_id, 'wp_ajax_url' => $wp_ajax_url ) );
        
        add_filter( 'script_loader_tag', [ $this, 'async_loading' ], 10, 2 );

        ob_start();
        ?>
            <div class="parent-div">
              <form role="search" method="get" action="/">
                <div class="searchbar" >
                  <div class="search-term">
                    <div>
                      <!-- TODO: Use a class to tap the event not the `onchange`. -->
                      <select id="place_category" class="services" name="place_category">
                        <option value="" ><?php _e( 'All Categories', 'porta-places' ); ?></option>
                          <?php
                              unset($categories);
                
                              $categories = get_terms( $taxonomy );
                
                              foreach( $categories as $category ) {
                                  $selected = $category->slug == $place_category ? 'selected' : '';
                                  ?>
                                <option value="<?php echo esc_attr( $category->slug ); ?>" <?php echo $selected; ?>><?php echo esc_attr( $category->name ); ?></option>
                                  <?php
                              }
                
                              unset($categories);
                          ?>
                      </select>
                    </div>
                  </div>

                  <div class="search-term">
                    <div class="search-icon">
                      <i class='fa-solid fa-location-dot'></i>
                    </div>
                    <input id="suburb" type="text" placeholder="Near" class="form-control" name="city" value="<?php echo $city; ?>">
                    <a id="reset-map-search" href="#clear" title="Clear your Search"><i class='fa fa-location'></i></a>
                    <input type="hidden" name="s" value="<?php the_search_query();?>" />
                    <input type="hidden" id="lng-field" name="lng" value="<?php echo $lng; ?>" />
                    <input type="hidden" id="lat-field" name="lat" value="<?php echo $lat; ?>" />
                  </div>
                  <div class="form-group submit-group-button">
                    <button type="submit" id="submit-search-btn" class="geodir_submit_search btn btn-primary w-100 " data-title="fas fa-search" aria-label="Search"><i class="fas fas fa-search" aria-hidden="true"></i><span class="sr-only">Search</span></button>
                  </div>
                </div>
                <div class="search-criteria-bg">
                  <label>Search By Distance</label>
                  <div class="distance-check">
                    <div class="form-check as--distance">
                      <input type="radio" class="form-check-input" name="dist" value="10" id="search_distance-10" <?php checked('10', $distance) ?>>
                      <label for="search_distance-10" class="form-check-label text-muted">Within 10 miles</label>
                    </div>
                    <div class="form-check as--distance">
                      <input type="radio" class="form-check-input" name="dist" value="20" id="search_distance-20" <?php checked('20', $distance) ?>>
                      <label for="search_distance-20" class="form-check-label text-muted">Within 20 miles</label>
                    </div>
                    <div class="form-check as--distance">
                      <input type="radio" class="form-check-input" name="dist" value="30" id="search_distance-30" <?php checked('30', $distance) ?>>
                      <label for="search_distance-30" class="form-check-label text-muted">Within 30 miles</label>
                    </div>
                    <div class="form-check as--distance">
                      <input type="radio" class="form-check-input" name="dist" value="40" id="search_distance-40" <?php checked('40', $distance) ?>>
                      <label for="search_distance-40" class="form-check-label text-muted">Within 40 miles</label>
                    </div>
                    <div class="form-check as--distance">
                      <input type="radio" class="form-check-input" name="dist" value="50" id="search_distance-50" <?php checked('50', $distance) ?>>
                      <label for="search_distance-50" class="form-check-label text-muted">Within 50 miles</label>
                    </div>
                  </div>
                </div>
                <div class="advanced-search-button">
                  <button type="submit" class="geodir_submit_search btn btn-primary w-100 " data-title="fas fa-search" aria-label="Search"><i class="fas fas fa-search" aria-hidden="true"></i><span class="sr-only">Search</span></button>
                </div>
              </form>
                <div class="offmap">
                    <div id="map-canvas" class="acf-map"></div>
                    <div class="dropdown-list-group">
                      <button id="gd-list-view-select-grid" class="button" type="button">
                        <i class='fa fa-table'></i>
                      </button>
                      <div class="dropdown-menu-list" id="dropdown-menu-list">
                        <button class="dropdown-item" data-gridview="1">View: Grid 1</button>
                        <button class="dropdown-item" data-gridview="2">View: Grid 2</button>
                        <button class="dropdown-item active" data-gridview="3">View: Grid 3</button>
                        <button class="dropdown-item" data-gridview="4">View: Grid 4</button>
                        <button class="dropdown-item" data-gridview="5">View: Grid 5</button>
                        <button class="dropdown-item" data-gridview="0">View: List</button>
                      </div>
                    </div>
                    <?php
                        
                        global $wp_query;
                        
                        $args = [
                            'post_type'      => 'gd_place',
                            'posts_per_page' => 15,
                            'orderby'        => 'post_title',
                            'order'          => 'ASC',
                            'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
                            'meta_query'    => [],
                        ];
    
                        if( !empty ( $place_category ) ) {
                            $args['tax_query'] = [
                                [
                                    'taxonomy'    => 'gd_placecategory',
                                    'field'       => 'slug',
                                    'terms'       => $place_category
                                ]
                            ];
                        }
    
                        if ( ! empty( $lat ) ) {
                            $lat_array = [ceil($lat), floatval($lat), round($lat)];
    
                            if( $distance ) {
        
                                $lat_north = floatval($lat) + (floatval($distance) / 69);
                                $lat_south = floatval($lat) - (floatval($distance) / 69);
                                $lat_west = floatval($lat) - (floatval($distance) / 69);
                                $lat_east = floatval($lat) + (floatval($distance) / 69);
        
                                $lat_array = array_merge($lat_array, [$lat_north, $lat_south, $lat_west, $lat_east]);
                            }
                            
                            rsort($lat_array);
                            $lat_args = [
                                'key'       => 'lat',
                                'value'     => [min($lat_array), max($lat_array)],
                                'compare'   => 'BETWEEN'
                            ];
        
                            array_push($args['meta_query'], $lat_args);
                        }
    
                        if ( ! empty( $lng ) ) {
                            $lng_array = [ceil($lng), floatval($lng), round($lng)];
    
                            if( $distance) {
                                $lng_north = floatval($lng) + (floatval($distance) / 54.6);
                                $lng_south = floatval($lng) - (floatval($distance) / 54.6);
                                $lng_west = floatval($lng) - (floatval($distance) / 54.6);
                                $lng_east = floatval($lng) + (floatval($distance) / 54.6);
        
                                $lng_array = array_merge($lng_array, [$lng_north, $lng_south, $lng_west, $lng_east]);
                            }
                            
                            rsort($lng_array);
                            $lng_args = [
                                'key'       => 'long',
                                'value'     => [min($lng_array), max($lng_array)],
                                'compare'   => 'BETWEEN'
                            ];
        
                            array_push($args['meta_query'], $lng_args);
                        }
                        
                        if(!empty($args['meta_query'])) {
                          $args['meta_query']['relation'] = 'OR';
                        }
                        
                        
                
                        $wp_query   = new \WP_Query( $args );
                
                        if ( $wp_query->have_posts() ) {
                        
                        ?>
                      <div class="all-gd_place" id="place-data">
                    <?php
                        while ( $wp_query->have_posts() ) {
                            $wp_query->the_post();
    
                            $categories = $this->get_place_category( get_the_ID(), $taxonomy, true );
                            $category_name = implode('&nbsp;and&nbsp;', wp_list_pluck($categories, 'html'));
                            
                              $address = get_field('address_line_1');
                              
                              if( ! empty( get_field('address_line_2') ) ) {
                                $address .= ' '. get_field('address_line_2');
                              }
                              
                              $is_user_bookmarked = false;
                              $user_id = 0;
                              if(is_user_logged_in()) {
                                $user_id = get_current_user_id();
                                $get_bookmarked_places = (array) get_user_meta($user_id, '_user_bookmarked_places', true);
                                
                                if(in_array(get_the_ID(), $get_bookmarked_places)) $is_user_bookmarked = true;
                              }
                              ?>
                              <div class="address-container">
                                <div class="address-featured-image">
                                  <?php the_post_thumbnail('fullsize'); ?>
                                </div>
                                <div class="address-content">
                                  <h4><a href="<?php echo get_the_permalink() ?>" title="<?php echo get_the_title() ?>"><?php the_title() ?></a></h4>
                                  <div class="favourites"><a href="javascript:void" class="<?php echo $is_user_bookmarked ? 'porta-removefromfav-icon' : 'porta-addtofav-icon'; ?>" data-place-id="<?php echo get_the_ID(); ?>" data-user-id="<?php echo $user_id; ?>" data-action="<?php echo $is_user_bookmarked ? 'remove_from_places_favourites' : 'add_to_places_favourites'; ?>" data-nonce="<?php echo wp_create_nonce('porta-favourites-nonce'); ?>" data-current-url="<?php echo home_url( add_query_arg( [], $wp->request ) ); ?>"><i class="fas fa-heart" style="color: <?php echo $is_user_bookmarked ? '#e84739;' : 'grey;'; ?>"></i> </a></div>
                                  <div class='title-container'>
                                    <i class="fas fa-folder-open fa-fw" aria-hidden="true"></i> <span> Category: </span>
                                      <?php
                                          if(! empty( $category_name) ) {
                                              echo $category_name;
                                          } ?>
                                  </div>
                                  <div class="address-content">
                                    <p><i class='fas fa-map-marker-alt fa-fw' aria-hidden='true'></i>
                                        <?php echo $address; ?>
                                      <br />
                                        <?php echo get_field( 'city' );; ?>
                                      <br />
                                        <?php echo get_field( 'state' ); ?>
                                      <br />
                                        <?php echo get_field( 'postcode' ); ?>
                                      <br />
                                      Austrialia
                                    </p>
                                  </div>
                                </div>
                              </div>
                          <?php } ?>
                      </div>
                      <?php
                        } else {
                        ?>
                            <p><?php _e( 'Sorry, no place matched your criteria.', 'porta-places' ); ?></p>
                          <?php } ?>
                </div>
                <?php
                    // pagination template from kadence
                    get_template_part( 'template-parts/content/pagination' );
                    
                    wp_reset_postdata();
                    
                    if(is_page()) {
                    
                ?>
                  <div class="geodir-widget-bottom"><a href="<?php echo $post_type_link; ?>" class="geodir-all-link btn btn-outline-primary">View all</a></div>
                <?php } ?>
            </div>
        
        <?php
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    
    public function single_place_map_search($atts, $content) {
        global $post;
    
        if( is_single() && isset($post->post_type) && $post->post_type == 'gd_place' ) {
        
            $place_data = $this->get_place($post->ID);
        
            wp_enqueue_style( 'places-map-search', $this->plugin_url . '/assets/build/css/places-map-search.css' );
            wp_enqueue_script( 'google-maps-custering', 'https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js' );
            wp_enqueue_script( 'google-maps-apikey', 'https://maps.googleapis.com/maps/api/js?key=' . PORTA_PLACES_GOOGLE_API_KEY2. '&amp;libraries=places&amp;callback=maps_setup' );
            wp_enqueue_script( 'places-map-search', $this->plugin_url . 'assets/build/js/places-map-search.js' ,array('google-maps-apikey'),'2', false);
            wp_localize_script( 'places-map-search', 'map_search_data', array( 'placedata' => $place_data, 'selected_placedata' => 0 ) );
    
            add_filter( 'script_loader_tag', [ $this, 'async_loading' ], 10, 2 );
            
            ob_start();
            ?>
          <input id="suburb" type="hidden" placeholder="Near" class="form-control">
            <div class="offmap">
              <div id="map-canvas" class="acf-map"></div>
            </div>
            <?php
    
            return ob_get_clean();
        }
    }
    
    /**
     * Get single place details
     *
     * @param $post_id
     * @return array
     */
    public function get_place($post_id) {
      $return_array[] = [
          'state'         => get_field('state', $post_id),
          'city'          => get_field('city', $post_id),
          'lat'           => get_field('lat', $post_id),
          'lng'           => get_field('long', $post_id),
          'address1'      => get_field('address_line_1', $post_id),
          'address2'      => get_field('address_line_2', $post_id),
          'postcode'      => get_field('postcode', $post_id),
          'permalink'      => get_permalink( $post_id ),
          'title'         => get_the_title($post_id),
          'category'      => $this->get_place_category( $post_id, 'gd_placecategory'),
      ];
      
        return $return_array;
    }
    
    /**
     * @param $taxonomy
     * @param string $service_area
     * @return array
     */
    public function get_all_places($taxonomy, $term = '', $extras = []) {
        $offices  = [];
        
        $args = [
            'post_type'      => 'gd_place',
            'posts_per_page' => 15,
            'orderby'        => 'post_title',
            'order'          => 'ASC',
            'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
            'meta_query'    => []
        ];
        
        if( !empty ( $term ) ) {
          $args['tax_query'] = [
              [
                  'taxonomy'    => 'gd_placecategory',
                  'field'       => 'slug',
                  'terms'       => $term
              ]
          ];
        }
        
        
        // if the latitude is set - add to meta query
        if ( ! empty( $extras['lat'] ) ) {
          $lat = $extras['lat'];
          $lat_array = [ceil($lat), floatval($lat), round($lat)];
          
          if( $extras['distance' ] ) {
            $distance = $extras['distance'];
            
            $lat_north = floatval($lat) + (floatval($distance) / 69);
            $lat_south = floatval($lat) - (floatval($distance) / 69);
            $lat_west = floatval($lat) - (floatval($distance) / 69);
            $lat_east = floatval($lat) + (floatval($distance) / 69);
            
            $lat_array = array_merge($lat_array, [$lat_north, $lat_south, $lat_west, $lat_east]);
          }
          
          rsort($lat_array);
          $lat_args = [
              'key'       => 'lat',
              'value'     => [min($lat_array), max($lat_array)],
              'compare'   => 'BETWEEN'
          ];
          
            array_push($args['meta_query'], $lat_args);
        }
        
        // if the longitude is set - add to meta query
        if ( ! empty( $extras['lng'] ) ) {
            $lng = $extras['lng'];
            $lng_array = [ceil($lng), floatval($lng), round($lng)];
    
            if( $extras['distance' ] ) {
                $distance = $extras['distance'];
        
                $lng_north = floatval($lng) + (floatval($distance) / 54.6);
                $lng_south = floatval($lng) - (floatval($distance) / 54.6);
                $lng_west = floatval($lng) - (floatval($distance) / 54.6);
                $lng_east = floatval($lng) + (floatval($distance) / 54.6);
    
                $lng_array = array_merge($lng_array, [$lng_north, $lng_south, $lng_west, $lng_east]);
            }
            
            rsort($lng_array);
            $lng_args = [
                'key'       => 'long',
                'value'     => [min($lng_array), max($lng_array)],
                'compare'   => 'BETWEEN'
            ];
            
            array_push($args['meta_query'], $lng_args);
        }
    
        // use the relation OR is the meta query is not empty
        if(! empty($args['meta_query'])) {
            $args['meta_query']['relation'] = 'OR';
        }
        
        $query   = new \WP_Query( $args );
        
        if ( $query->have_posts() ) {
            
            $counter = 0;
            
            while ( $query->have_posts() ) {
                $query->the_post();
    
                $place_categories = $this->get_place_category( get_the_ID(), $taxonomy, true );
                $category_name = implode(' and ', wp_list_pluck($place_categories, 'html') );
                
                //Get the ACFs of the office post type
                $offices[$counter]['state']          = get_field( 'state' );
                $offices[$counter]['city']           = get_field( 'city' );
                $offices[$counter]['lat']            = get_field( 'lat' );
                $offices[$counter]['lng']            = get_field( 'long' );
                $offices[$counter]['address1']       = get_field( 'address_line_1' );
                $offices[$counter]['address2']       = get_field( 'address_line_2' );
                $offices[$counter]['postcode']       = get_field( 'postcode' );
                $offices[$counter]['permalink']      = get_the_permalink();
                $offices[$counter]['title']          = get_the_title();
                $offices[$counter]['category']       = $place_categories;
                $offices[$counter]['category_name']  = $category_name;
                
                $counter++;
            }
            
        }
        
        return $offices;
    }
}