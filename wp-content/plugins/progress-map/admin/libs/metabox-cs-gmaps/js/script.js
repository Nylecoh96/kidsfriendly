/*(function( $ ) {
	'use strict';

	var maps = [];


	// Resize map when meta box is opened
	if ( typeof postboxes !== 'undefined' ) {
		postboxes.pbshow = function () {
			var arrayLength = maps.length;
			for (var i = 0; i < arrayLength; i++) {
				var mapCenter = maps[i].getCenter();
				google.maps.event.trigger(maps[i], 'resize');
				maps[i].setCenter(mapCenter);
			}
		};
		
	}

	// When a new row is added, reinitialize Google Maps
	$( '.cmb-repeatable-group' ).on( 'cmb2_add_row', function( event, newRow ) {
		var groupWrap = $( newRow ).closest( '.cmb-repeatable-group' );
		groupWrap.find( '.cmb-type-cs-gmaps' ).each( function() {
			initializeMap( $( this ) );
		});
	});

})( jQuery );*/


var cs_gmaps = [];

function alerte(obj) {
	
	if (typeof obj == 'object') {
		var foo = '';
		for (var i in obj) {
			if (obj.hasOwnProperty(i)) {
				foo += '[' + i + '] => ' + obj[i] + '\n';
			}
		}
		alert(foo);
	}else {
		alert(obj);
	}
	
}

/**
 * initialize "cs-gmaps" map */

function cs_gmaps_init(mapHTML){

	var map_id = mapHTML.attr('data-map-id');
	
	cs_gmaps.push(map_id);
	
	var plugin_map_placeholder = 'div.cs-gmaps-map[data-map-id='+map_id+']';
	var plugin_map = jQuery(plugin_map_placeholder);
	
	/**
	 * Activate the new google map visual */
	 
	google.maps.visualRefresh = true;
					
	/**
	 * Create the map */
	
	var map_center = mapHTML.attr('data-map-center').split(',');
		
		/**
		 * Default Lat,Lng */
		 
		var lat = map_center[0];
		var lng = map_center[1];
		
	var map_zoom = mapHTML.attr('data-map-zoom');

	/**
	 * Overwrite map center if latLng fileds contain values
	 * @edited 1.1 */
	
	var latLng_field_type = mapHTML.attr('data-latLng-field-type'); //@since 1.1
	
	if(latLng_field_type == 'unique'){ //@since 1.1
		var latLng_field = jQuery('.cs-gmaps-latLng[data-map-id='+map_id+']').val().split(','); //@since 1.1
		var lat_field = (latLng_field != '' && typeof latLng_field[0] !== 'undefined') ? latLng_field[0] : ''; //@since 1.1
		var lng_field = (latLng_field != '' && typeof latLng_field[1] !== 'undefined') ? latLng_field[1] : ''; //@since 1.1
	}else{
		var lat_field = jQuery('.cs-gmaps-latitude[data-map-id='+map_id+']').val();
		var lng_field = jQuery('.cs-gmaps-longitude[data-map-id='+map_id+']').val();
	}
		
		if(lat_field != '' && lng_field != ''){
			lat = lat_field;
			lng = lng_field;
		}

	var latLng = new google.maps.LatLng(lat, lng);

	plugin_map.gmap3({	
		map:{
			options: { 
				center: latLng,
				zoom: parseInt(map_zoom),
				scrollwheel: true,
				zoomControl: true,
				mapTypeControl: true,
				streetViewControl: false,
				controlSize: 21, //@since 1.1
			},						
		},		
	});	
	
	/** 
	 * Add main marker on the map when Lat,Lng already provided/saved */
	
	if(lat_field != '' && lng_field != ''){
					
		plugin_map.gmap3({							
			marker:{
				latLng: latLng,
				options: {
					draggable: true,
				},
				events:{
					click: function(marker, event, context){
						
						var map = plugin_map.gmap3("get");
						var infowindow = plugin_map.gmap3({get:{name:"infowindow"}});
						var content = '<p style="height:50px; width:150px">Main: '+lat_field+','+lng_field+'</p>';
						
						if(infowindow){
							infowindow.open(map, marker);
							infowindow.setContent(content);
						}else{
							plugin_map.gmap3({
								infowindow:{
									anchor:marker, 
									options:{
										content: content
									}
								}
							});
						}	
										
					}
				}
			},
		});
		
	}
	
	/** 
	 * Add secondary markers on the map */
	
	var secondary_latlng = jQuery('textarea.cs-gmaps-secondary-latlng[data-map-id='+map_id+']').val();
	
	if(typeof secondary_latlng !== 'undefined'){
		
		var clean_secondary_latlng = secondary_latlng.replace(' ', '').replace('][', '|').replace('[', '').replace(']', '');
		var explode_secondary_latlng = clean_secondary_latlng.split('|');
			
		for(i=0; i<explode_secondary_latlng.length; i++){
			
			var explode_latlng = explode_secondary_latlng[i].split(',');
			
			var secondary_lat = explode_latlng[0];
			var secondary_lng = explode_latlng[1];
			
			if(secondary_lat != '' && secondary_lng != ''){
				
				var secondary_latLng = new google.maps.LatLng(secondary_lat, secondary_lng);
							
				plugin_map.gmap3({							
					marker:{
						latLng: secondary_latLng,
						options: {
							draggable: false,
						},
						events:{
							click: function(marker, event, context){
								
								var map = plugin_map.gmap3("get");
								var infowindow = plugin_map.gmap3({get:{name:"infowindow"}});
								var content = '<p style="height:50px; width:150px">Secondary: '+secondary_lat+','+secondary_lng+'</p>';
								
								if(infowindow){
									infowindow.open(map, marker);
									infowindow.setContent(content);
								}else{
									plugin_map.gmap3({
										infowindow:{
											anchor:marker, 
											options:{
												content: content
											}
										}
									});
								}	
												
							}
						}
					},
				});
				
			}
		
		}
		
	}
	
	/**
	 * Add Autocomplete to the address field */
	
	var input = jQuery('input.cs-gmaps-search[data-map-id='+map_id+']');
	var autocomplete = new google.maps.places.Autocomplete(input[0]);
	
}


function cs_gmaps_resize(map_id){
	
	if(typeof map_id !== 'undefined'){

		var plugin_map = jQuery('div.cs-gmaps-map[data-map-id='+map_id+']');
		
		var mapObject = plugin_map.gmap3("get");
		
		google.maps.event.trigger(mapObject, 'resize');
	
		/**
		 * Get latLng fileds values & center the map on that fields */
						
		var lat_field = jQuery('.cs-gmaps-latitude[data-map-id='+map_id+']').val();
		var lng_field = jQuery('.cs-gmaps-longitude[data-map-id='+map_id+']').val();
	
		if(typeof lat_field !== 'undefined' && lat_field != '' && typeof lng_field !== 'undefined' && lng_field != ''){
			
			lat = lat_field;
			lng = lng_field;
		
			var latLng = new google.maps.LatLng(lat, lng);
	
			mapObject.panTo(latLng);
			mapObject.setCenter(latLng);
		
		}
		
	}
	
}

jQuery(document).ready(function($){ 
	
	/**
	 * Loop throught all "cs-gmaps" and init each map instance
	 *
	 * @since 1.0 */	 
	 
	$('.cs_gmaps_container').each(function(){
		
		var map_id = $(this);
		
		if(typeof map_id !== 'undefined')
			cs_gmaps_init(map_id);
	
	});
	
	/**
	 * Search for address coordinates
	 *
	 * @since 1.0 */	 
	
	$(document).on('click', 'input.cs_gmaps_search_btn', function(e){
		
		e.preventDefault();
		
		var map_id = $(this).attr('data-map-id');
		var address = $('input.cs-gmaps-search[data-map-id='+map_id+']').val();
								
		var error_address1 = 'We could not understand the location ';
		var error_address2 = '\n\nSuggestions:';
			error_address2 += '\n--------------';
			error_address2 += '\n1. Make sure all street and city names are spelled correctly.';
			error_address2 += '\n2. Make sure your address includes a city and state.';
			error_address2 += '\n3. Try entering a zip code.';
	
		var plugin_map = $('div.cs-gmaps-map[data-map-id='+map_id+']');
		
		plugin_map.gmap3({
			clear: {
				name:"marker",
				all: true
			},
		});
	
		var geocoder = new google.maps.Geocoder();
		
		/**
		 * Convert our address to Lat & Lng */
		 
		geocoder.geocode({ 'address': address }, function (results, status) {
			
			/**
			 * If the address was found */
			 
			if (status == google.maps.GeocoderStatus.OK) {
				
				/**
				 * Display the marker position */
							
				plugin_map.gmap3({							
					marker:{
					  latLng:results[0].geometry.location,
					  options:{
						draggable: true,
					  }
					},
					map:{
						options:{
							center:results[0].geometry.location,
						}
					}
				});
				
			}else alert(error_address1 + '"' + address + '"' + error_address2);
			
		});
		
	});
		
	$(document).on('keypress', '.cs-gmaps-preventDefault', function(e){        
		if(e.keyCode == 13){
			e.preventDefault();
		}
	});
	
	/**
	 * Get user location
	 *
	 * @since 1.0 */	 
	
	$(document).on('click', 'button.cs_gmaps_geoloc_btn', function(e){
		
		e.preventDefault();
		
		var map_id = $(this).attr('data-map-id');
	
		var plugin_map = $('div.cs-gmaps-map[data-map-id='+map_id+']');
		
		plugin_map.gmap3({
			clear: {
				name:"marker",
				all: true
			},
		});
	
		plugin_map.gmap3({
			getgeoloc:{
				callback : function(latLng){

					if (latLng){
						
						/**
						 * Display the marker position */
						
						plugin_map.gmap3({
							marker:{ 
								latLng:latLng,
								name: 'marker',												
								options:{
									draggable: true,
								},								
							},
							map:{
								options:{
									center:latLng,
								}
							}
						});
			
					}else{
					
						/**
						 * More info at https://support.google.com/maps/answer/152197
						 * Deprecate Geolocation API: https://developers.google.com/web/updates/2016/04/geolocation-on-secure-contexts-only */

						var geoErrorTitle = 'Give Maps permission to use your location!';			
						var geoErrorMsg = 'If you can\'t center the map on your location, a couple of things might be going on. It\'s possible you denied Google Maps access to your location in the past, or your browser might have an error.';
						var geoDeprecateMsg = 'Browsers no longer supports obtaining the user\'s location using the HTML5 Geolocation API from pages delivered by non-secure connections. This means that the page that\'s making the Geolocation API call must be served from a secure context such as HTTPS.';				
						
						alert(geoErrorTitle + '\n\n' + geoErrorMsg);
						console.log('PROGRESS MAP: ' + geoDeprecateMsg);
					  
					}
			
				}
				
			}
			
		});								
		
	});
	
	/**
	 * Copy/Paste latLng to Latitude & Longitude fields
	 *
	 * @since 1.0
	 * @updated 1.1 */	 
	 		  
	$(document).on('click', 'input.cs_gmaps_get_pinpoint', function(e){
		
		e.preventDefault();
		
		var map_id = $(this).attr('data-map-id');
						
		var plugin_map = $('div.cs-gmaps-map[data-map-id='+map_id+']');
		
		var latLng_field_type = $('div.cs_gmaps_container[data-map-id='+map_id+']').attr('data-latLng-field-type'); //@since 1.1
        
		plugin_map.gmap3({
		  get: {
			name:"marker",
			callback: function(marker){
				if(marker){
					if(latLng_field_type == 'unique'){ //@since 1.1
						$('.cs-gmaps-latLng[data-map-id='+map_id+']').val(marker.getPosition().lat()+','+marker.getPosition().lng()); //@since 1.1
					}else{
						$('.cs-gmaps-latitude[data-map-id='+map_id+']').val(marker.getPosition().lat());
						$('.cs-gmaps-longitude[data-map-id='+map_id+']').val(marker.getPosition().lng());
					}
				}
			}
		  }
		});
		
	});
	
	/**
	 * Copy/Paste latLng to Secondary Latitude & Longitude field
	 *
	 * @since 1.0 */	 
	 		  
	$(document).on('click', 'input.cs_gmaps_get_more_pinpoint', function(e){
		
		e.preventDefault();
		
		var map_id = $(this).attr('data-map-id');
		var old_value = $('textarea.cs-gmaps-secondary-latlng[data-map-id='+map_id+']').val();
						
		var plugin_map = $('div.cs-gmaps-map[data-map-id='+map_id+']');
		
		plugin_map.gmap3({
		  get: {
			name:"marker",
			callback: function(marker){
				if(marker){
					
					var lat = marker.getPosition().lat();
					var lng = marker.getPosition().lng();
					
					$('textarea.cs-gmaps-secondary-latlng[data-map-id='+map_id+']').val(old_value + '[' + lat + ',' + lng + ']');
						
				}
			}
		  }
		});
	
	});
				
	/**
	 * Resize each map instance when meta box is opened 
	 *
	 * @since 1.0 */

	if(typeof postboxes !== 'undefined'){
		
		postboxes.pbshow = function () {

			for(var i=0; i<cs_gmaps.length; i++) {
				
				var map_id = cs_gmaps[i];
 
				cs_gmaps_resize(map_id);
				
			};
	
		};
		
	}
					
	/**
	 * Resolve a problem of Google Maps & jQuery Tabs.
	 * Resize the map once visible on the screen.
	 *
	 * @since 1.0 */
	
	$('div.cs-gmaps-map:visible').on(function(){
		
		var map_id = $(this).attr('data-map-id');
		
		setTimeout(function(){
			cs_gmaps_resize(map_id);
		}, 100);
		
	});
	
});
