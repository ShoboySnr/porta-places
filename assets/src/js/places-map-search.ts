/**
 * Add a Filterable office location map.
 */
//Declarations for Typescript
declare let map_search_data: any;
declare let google: any;
declare let MarkerClusterer: any;

// Get WP localized data.

let data = map_search_data.placedata;

// Initial data
let average = getAverageCoordinates(data);
const init_latitude  = average.lat;
let init_longitude = average.lng;

// Hold all the markers used in the clustering and filtering
let gmarkers1 = [];
let defaultMarkers = [];
let markerCluster;
let center;
let mapOptions;
let map;
let map_zoom_default: Number = 5;

//Add base level stuff to say category is selected
let global_category_id = "";

//set the location details
let places_search_active = false;
let places_lat;
let places_lng;
let places_location;

//call back for setup
function maps_setup() {
    center = new google.maps.LatLng(init_latitude, init_longitude);
    mapOptions = {
        zoom: map_zoom_default,
        center: center,
        mapTypeID: google.maps.MapTypeId.ROADMAP,
    };
    map = new google.maps.Map(
        document.getElementById("map-canvas"),
        mapOptions
    );

    // Initialize the map
    initialize_map();
}

/**
 * Initialize the map installation.
 * Call addMarker for each office.
 * Cluster all the gd_place.
 *
 * @returns void
 */
function initialize_map() {

    city_search_autocomplete();

    for (let i = 0; i < data.length; i++) {
        add_marker(data[i]);
    }

    markerCluster = new MarkerClusterer(map, gmarkers1, {
        imagePath:
            "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m",
        gridSize: 1,
    });

}


function getAverageCoordinates(data) {
    let sum_lat = 0, sum_lng = 0;

    data.forEach((value) => {
        sum_lat = parseFloat(value.lat) + sum_lat;
        sum_lng = parseFloat(value.lng) + sum_lng;
    });

    return {
        'lat' : sum_lat / data.length,
        'lng' : sum_lng / data.length
    };
}

/**
 * Add markers to map and cluster the markers on the map.
 *
 * @param object marker Marker object for Google API JS.
 *
 * @returns void
 */
function add_marker(marker) {

    let pos = new google.maps.LatLng(marker['lat'], marker["lng"]);
    let marker1 = new google.maps.Marker({
        category: marker['category'],
        category_name: marker['category_name'],
        postcode: marker["postcode"],
        city: marker["city"],
        title: marker["title"],
        address1: marker["address1"],
        address2: marker["address2"],
        state: marker["state"],
        permalink: marker["permalink"],
        position: pos,
        animation: google.maps.Animation.DROP,
    });

    gmarkers1.push(marker1);
    defaultMarkers.push(marker1);

    // Marker click listener (zoom in and open info window)
    google.maps.event.addListener(marker1, "click", function () {

        var address_line = marker["address1"];

        if ( '' != marker["address2"]) {
            address_line = address_line + ", " + marker["address2"];
        }

        let infowindow = new google.maps.InfoWindow({
            content:
                "<div class='map_info_wrapper'>" +
                "<h4><a href='" + marker["permalink"] + "' title='" + marker["title"] + "' >" + marker["title"] + "</a></h4>" +
                "<div class='list-group-item'><span><i class='fa fa-minus'></i> Place Title: </span>" +  marker["title"] +
                "</div>" +
                "</div>",

        });
        infowindow.open(map, marker1);
    });

    // offices_scrollbar(marker1);
}

//loop through the category array that comes with every office and find if a service area id exists in it then return true else false this function is used in the filterMarkers function
function searchOffices(search_id, office) {
    for (let i = 0; i < office.length; i++) {
        if (office[i] == search_id) {
            return true;
        }
        else {
            return false;
        }
    }
}

/**
 * Clear all markers on map.
 * Set Filtered markers by category id.
 *
 * @param string category_id Services categories ID form the <select> options.
 *
 * @returns void
 */
const filterMarkersCat = function (category_id ) {
    global_category_id = category_id;

    place_markers();
};

function marker_placement_defaults(_markers){
    for (let i = 0; i < _markers.length; i++) {
        let marker = _markers[i];
        marker.setVisible(true);
        // offices_scrollbar(_markers[i]);
    }

    map.setZoom(map_zoom_default);
    if(places_search_active){
        map.panTo(places_location);
    }

    //Check zoom to see if we should cluster
    if(map.getZoom() < 11 ){
        markerCluster.clearMarkers();
        markerCluster.addMarkers(_markers);
    }
}

// Global Place Markers
function place_markers(){

    if (global_category_id  === "") {
        if(places_search_active){
            marker_placement_defaults(gmarkers1);
        }else{
            marker_placement_defaults(defaultMarkers);
        }
    } else {
        let filter_array = [];
        let marker_array = [];
        let gmarked = [];

        //Filter array
        filter_array = data.filter(dataArr => dataArr.category != null);
        marker_array = filter_array.filter( dataObj =>  {
            dataObj.category.some(cat => cat == global_category_id);
        });

        if(places_search_active){
            for (let i = 0; i < marker_array.length; i++) {
                marker_array[i]['distance'] = haversine_distance(places_lat, places_lng, parseFloat(marker_array[i].lat), parseFloat(marker_array[i].lng));
            }

            marker_array.sort(distanceSort);

            setDefaultZoom(marker_array[0]['distance']);
        }

        const markers_lab = marker_array.map((location, i) => {
            add_marker(location);
            let marked = new google.maps.Marker({
                postcode: location["postcode"],
                city: location["city"],
                title: location["title"],
                address1: location["address1"],
                address2: location["address2"],
                state: location["state"],
                permalink: location["permalink"],
                position: new google.maps.LatLng(location["lat"],location["lng"]),
                animation: google.maps.Animation.DROP,
            });
            gmarked.push(marked);


            // Marker click listener (zoom in and open info window)
            google.maps.event.addListener(marked, "click", function () {

                var address_line = marked["address1"];

                if ( '' != marked["address2"]) {
                    address_line = address_line + ", " + marked["address2"];
                }

                let infowindow = new google.maps.InfoWindow({
                    content:
                        "<div class='map_info_wrapper'>" +
                        "<p><a href=" + marked["permalink"] + ">" + marked["title"] + "</a><br>" +
                        address_line + "<br>" + marked["city"] + ", " + marked["state"] + ", " + marked["postcode"] + "</p>" +
                        "</div>",
                });
                infowindow.open(map, marked);
            });
        });

        //Get if places are active to focus on the customer location
        if(places_search_active){
            map.setZoom(map_zoom_default);
            map.panTo(places_location);
        }

        //Check zoom to see if we should cluster
        markerCluster.clearMarkers();
        markerCluster.addMarkers(gmarked);
    }
}

/**
 * Add the gd_place in the scrollbar view.
 * @param marker Marker object for Google API JS.
 *
 * @returns void
 */
function offices_scrollbar(marker) {
    if (marker == "") {

    } else {
        var address_line = marker["address1"];

        if ( '' != marker["address2"]) {
            address_line = address_line + ", " + marker["address2"];
        }
    }
}

/**
 * Search by city if the city filled.
 * utilizes the Google Places API.
 *
 * @returns void
 */
function city_search_autocomplete() {

    const center = { lat: init_latitude, lng: init_longitude };
    // Create a bounding box with sides ~10km away from the center point
    let defaultBounds = {
        north: center.lat + 0.3,
        south: center.lat - 0.3,
        east: center.lng + 0.3,
        west: center.lng - 0.3,
    };

    const input = document.getElementById("suburb");
    const options = {
        bounds: defaultBounds,
        componentRestrictions: { country: "AU" },
        fields: ["address_components", "geometry", "icon", "name"],
        origin: center,
        strictBounds: false,
        types: ["(cities)"],
    };

    const autocomplete = new google.maps.places.Autocomplete(input, options);

    const place = autocomplete.getPlace();

    google.maps.event.addListener(autocomplete, "place_changed", function () {

        //console.log('Event Listener fired for place changed');
        places_search_active = true;

        let place = autocomplete.getPlace();

        console.log(autocomplete, place.geometry.location.lat(),  place.geometry.location.lng(), place.address_components);

        map_zoom_default = 11;

        //get the long and lat for the selected places attribute
        places_location = place.geometry.location;
        let lat = places_location.lat();
        let lng = places_location.lng();

        //set this value into long and lat input fields
        let lng_field = document.getElementById('lng-field') as HTMLInputElement | null;
        let lat_field = document.getElementById('lat-field') as HTMLInputElement | null;

        lng_field.value = lng;
        lat_field.value = lat;
    });
}

//check distance between two markers
function haversine_distance(place_lat, place_lng, office_lat, office_lng) {
    let R = 3958.8; // Radius of the Earth in miles
    let rlat1 = Number(place_lat) * (Math.PI/180); // Convert degrees to radians
    let rlat2 = Number(office_lat) * (Math.PI/180); // Convert degrees to radians
    let difflat = rlat2-rlat1; // Radian difference (latitudes)
    let difflon = (Number(office_lng)-Number(place_lng)) * (Math.PI/180); // Radian difference (longitudes)

    let d = 2 * R * Math.asin(Math.sqrt(Math.sin(difflat/2)*Math.sin(difflat/2)+Math.cos(rlat1)*Math.cos(rlat2)*Math.sin(difflon/2)*Math.sin(difflon/2)));
    return d;
}

//function for sort comparison
function distanceSort(a, b) {
    // get the distance and make sure its a number
    const distanceA = Number(a['distance']);
    const distanceB = Number(b['distance']);

    let comparison = 0;
    if (distanceA > distanceB) {
        comparison = 1;
    } else if (distanceA < distanceB) {
        comparison = -1;
    }
    return comparison;
}

//Reset the form
const resetButton = (<HTMLInputElement>document.getElementById("reset-map-search"));
const suburbInput = (<HTMLInputElement>document.getElementById("suburb"));
const categorySelect = (<HTMLSelectElement>document.getElementById("place_category"));
const submitButton = (<HTMLSelectElement>document.getElementById("submit-search-btn"));

if(resetButton) {
    resetButton.addEventListener('click', function(){
        reset();

        place_markers();
    });
}

//reset function
function reset(){
    suburbInput.value = '';
    categorySelect.selectedIndex = 0;

    //reset the places filter
    places_search_active = false;
    places_lat = init_latitude;
    places_lat = init_longitude;
    // map_zoom_default = 4.2;
}

/*
Function for shortest distance
 */
function setDefaultZoom(_distance){

    //console.log('closest location distance');
    console.log(_distance);

    switch (true) {
        case (_distance < 2):
            map_zoom_default = 13;
            break;
        case (_distance < 8):
            map_zoom_default = 12;
            break;
        case (_distance < 31):
            map_zoom_default = 11;
            break;
        case (_distance < 41):
            map_zoom_default = 10;
            break;
        case (_distance < 51):
            map_zoom_default = 9;
            break;
        case (_distance < 71):
            map_zoom_default = 8;
            break;
        case (_distance < 111):
            map_zoom_default = 7;
            break;
        default:
            map_zoom_default = 6;
            break;
    }
}

function handleFavourites(user_id, action = 'porta-addtofav-icon') {

}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.porta-addtofav-icon, .porta-removefromfav-icon').forEach((element, index) => {
       element.addEventListener('click', async (event) => {
           event.preventDefault();

           let place_id = element.getAttribute('data-place-id');
           let user_id = element.getAttribute('data-user-id');
           let action = element.getAttribute('data-action');
           let nonce = element.getAttribute('data-nonce');
           let current_url = element.getAttribute('data-current-url');
           let url = map_search_data.wp_ajax_url;

           const data = new FormData();

           data.append('place_id', place_id);
           data.append('user_id', user_id);
           data.append('action', action);
           data.append('nonce', nonce);
           data.append('current_url', current_url);

           const response = await fetch(url, {
               method: 'POST',
               credentials: 'same-origin',
               body: data
           });

           let json_response = response.json();

           json_response.then((data) => {
                if(!data.success) {
                    console.log(data);
                    if(data.data.link) {
                        console.log(data);
                        setTimeout(() => {
                            location.href = data.data.link;
                        }, 1500)
                    }

                    return;
                }

                if(data.success) {
                    element.querySelector('i').setAttribute('style', 'color:' + data.data.color);
                    if(data.data.action == 'added') {
                        element.classList.remove('porta-addtofav-icon');
                        element.classList.add('porta-removefromfav-icon');
                        element.setAttribute('data-action', 'remove_from_places_favourites');
                    } else {
                        element.classList.add('porta-addtofav-icon');
                        element.classList.remove('porta-removefromfav-icon');
                        element.setAttribute('data-action', 'add_to_places_favourites');
                    }
                    return;
                }
           }).catch((error) => {
               console.log(error);
           });

       } )
    });

    if(document.querySelector('#gd-list-view-select-grid')) {
        document.querySelector('#gd-list-view-select-grid').addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            let classlist = document.querySelector('#dropdown-menu-list').classList;
            if(classlist.contains('show-dropdown')) {
                classlist.remove('show-dropdown');
            } else {
                classlist.add('show-dropdown');
            }
        })
    }

    document.addEventListener('click', () => {
        document.querySelector('#dropdown-menu-list.show-dropdown').classList.remove('show-dropdown');
    });

    if(document.querySelectorAll('.dropdown-item').length >= 1) {
        document.querySelectorAll('.dropdown-item').forEach((element, index) => {
            element.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                let target = event.target;

                let gridview = element.getAttribute('data-gridview');

                //remove all available classes
                document.querySelector('#place-data').classList.remove('grid-0', 'grid-1', 'grid-2', 'grid-3', 'grid-4', 'grid-5' );
                // add the selected grid view
                document.querySelector('#place-data').classList.add('grid-' + gridview);

                // remove all active classes
                document.querySelector('.dropdown-item.active').classList.remove('active');
                // add active class to element
                element.classList.add('active');
            })
        });
    }
});


