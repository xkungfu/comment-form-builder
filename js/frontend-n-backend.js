function cfb_initialize( div_map , zoom , input_name , lat_lng , draggable , restrict_country , place_type ) {

	var draggable_status = ( draggable == true ) ? true : false ;

	var cor = lat_lng.split(',');

	/* Show the google map */
    window['mapCanvas'+div_map] = document.getElementById( div_map );
    window['mapOptions'+ div_map] = {
      	center: new google.maps.LatLng( parseFloat(cor[0]) , parseFloat(cor[1]) ),
      	zoom: zoom,
      	mapTypeId: google.maps.MapTypeId.ROADMAP,
      	mapTypeControl: false,
    }

    window['map'+input_name] = new google.maps.Map( window['mapCanvas'+div_map] , window['mapOptions'+ div_map] );

    cfb_place_autocomplete( window['map'+input_name] , div_map , input_name , restrict_country , place_type );

   	cfb_marker( window['map'+input_name] , lat_lng , div_map , draggable_status , input_name);
    
}

function cfb_place_autocomplete( map , prefix , input_name , restrict_country , place_type ){

	window['options'+prefix] = {};

	if( restrict_country != '' ){
		window['options'+prefix]['componentRestrictions'] = { country : restrict_country };
	}

	if( place_type != 'all' ){
		window['options'+prefix]['types'] = [ place_type ];
	}

	/* Get autocomplete */
    window['input_name'+prefix] = document.getElementById( input_name );
    window['autocomplete'+prefix] = new google.maps.places.Autocomplete( window['input_name'+prefix] , window['options'+prefix] );

    // Disable form submit on enter
    google.maps.event.addDomListener( window['input_name'+prefix] , 'keydown', function(e) { 
        if (e.keyCode == 13) { 
            e.preventDefault(); 
        }
    }); 

    google.maps.event.addListener( window['autocomplete'+prefix], 'place_changed', function(event) {

        var place = window['autocomplete'+prefix].getPlace();
        
        if ( !("geometry" in place) ){
        	return;
        }

        if ( place.geometry.viewport != undefined ) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }

        cfb_moveMarker(place.name, place.geometry.location,prefix);
        jQuery( '#lat_' + prefix ).val(place.geometry.location.lat());
        jQuery( '#lng_' + prefix ).val(place.geometry.location.lng());
    });

}

function cfb_marker( map , lat_lng , prefix , draggable_status , input_name ){

	var cor = lat_lng.split(',');

	var options = {};
	options['position'] = new google.maps.LatLng( parseFloat(cor[0]) , parseFloat(cor[1]) );
	options['map'] = map;

	if( draggable_status == true ){
		options['draggable'] = true;	
	}
	
	window['marker'+prefix] = new google.maps.Marker(options);

	google.maps.event.addListener( window['marker'+prefix] , 'dragend' , function() {
	    cfb_geocodePosition( window['marker'+prefix].getPosition() , input_name , prefix );
	});
}

function cfb_moveMarker( placeName, latlng , prefix) {
    window['marker'+prefix].setPosition(latlng);
}

function cfb_geocodePosition( pos , input_name , prefix ) {
   	geocoder = new google.maps.Geocoder();
   	geocoder.geocode({
        latLng: pos
    }, 
        function(results, status) 
        {
            if (status == google.maps.GeocoderStatus.OK) 
            {
                jQuery('#'+input_name).val( results[0].formatted_address );
                jQuery( '#lat_' + prefix ).val(results[0].geometry.location.lat());
        		jQuery( '#lng_' + prefix ).val(results[0].geometry.location.lng());
            } 
            else 
            {
                alert('Error !!! Plz try again.');
            }
        }
    );
}