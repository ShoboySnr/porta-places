/**
 * Works with `cvgt_office_info` shortcode.
 * Returns info + map that belong to this location via shortcode.
 * 
 * @param int markerId  Places API Marker ID.
 * @param int mapId     Map ID.
 * @param int postCode  Postal Code.
 */
function Office_Map( markerId, mapId, postCode ) {

    let data = document.getElementById( markerId );
    let lat  = parseFloat( data.dataset.lat );
    let lng  = parseFloat( data.dataset.lng );

    let center     = new google.maps.LatLng( lat, lng );

    let mapOptions = {
        zoom: 15,
        center: center,
        mapTypeId: google.maps.MapTypeId.TERRAIN
    };

    let map        = new google.maps.Map( document.getElementById( mapId ), mapOptions );
    let marker     = new google.maps.Marker({
        position: center,
        map: map,
        title:"Fast marker"
    });

    google.maps.event.addListener( marker, 'click', function() {
        
        var infowindow = new google.maps.InfoWindow({
            content: "<div class='map_info_wrapper'>"+
            "<a href="+data.dataset.officelink+">"+data.dataset.title+"<br></a>"+
            "<p>"+data.dataset.address+"<br>"+data.dataset.city+","+data.dataset.state+ " "+ postCode+"</p>"+
            "</div>"
        });

        infowindow.open( map, marker );

    });    
        
}