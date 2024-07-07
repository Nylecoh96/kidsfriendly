var cs_gmaps_drawing = [];
var drawingManager = {};
var groundOverlay = {};
var last_overlay_obj = {};
var plugin_map = {};

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
 * Find if a polygon is clockwise or counter-clockwise.
 * Return TRUE if Clockwise (-negative sum) & FALSE if Counter-clockwise (+positive sum).
 *
 * Note that the area of a convex polygon is defined to be positive if the points are ...
 * ... arranged in a counterclockwise order, and negative if they are in clockwise order!
 *
 * @since 1.0
 */

function cs_gmaps_drawing_is_clockwise(vertices){	
	var sum = 0;
	for(var i=0; i<vertices.length-1; i++){
		var current = vertices[i],
			next = vertices[i+1];
		sum += (next.lat() - current.lat()) * (next.lng() + current.lng());
	}
	return sum < 0;
}	

/**
 * initialize "cs-gmaps-drawing" map */

function cs_gmaps_drawing_init(mapHTML){

	var map_id = mapHTML.attr('data-map-id');
	
	cs_gmaps_drawing.push(map_id);
	
	var plugin_map_placeholder = 'div.cs-gmaps-drawing-map[data-map-id='+map_id+']';
	plugin_map[map_id] = jQuery(plugin_map_placeholder);
	
	var draw_mode = jQuery('div.cs_gmaps_drawing_container[data-map-id='+map_id+']').attr('data-draw-mode');

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

	var latLng = new google.maps.LatLng(lat, lng);

	/**
	 * Render the map */
	 
	plugin_map[map_id].gmap3({	
		map:{
			options: { 
				center: latLng,
				zoom: parseInt(map_zoom),
				scrollwheel: true,
				zoomControl: true,
				mapTypeControl: true,
				streetViewControl: false,
				controlSize: 21,
			},	
			onces: {
				tilesloaded: function(map){
							
					var paths_array = new Array();

					/**
					 * Draw already cerated overlay */
					
					if(draw_mode != 'image'){
					
						var drawn_overlay_coordinates = jQuery('textarea.cs-gmaps-drawing-coordinates[data-map-id='+map_id+']').val();
					
						if(drawn_overlay_coordinates != ''){
							
							if(typeof drawn_overlay_coordinates === 'undefined')
								return;
							
							var latlng_order = 'latlng'; //jQuery('textarea.cs-gmaps-drawing-coordinates[data-map-id='+map_id+']').attr('data-latlng-order'); //@since 1.1

							var split_coordinates = drawn_overlay_coordinates.split('],[');
							
							/**
							 * Check the coordinates order
							 * @since 1.1 */
							  
							if(typeof split_coordinates[0] !== 'undefined'){
								var check_latLng = split_coordinates[0].replace('[', '').replace(']', '').split(',');
								var latitude = check_latLng[0];
								var longitude = check_latLng[0];
								if(latitude < -90 || latitude > 90){
									latlng_order = 'lnglat';
								}
							}
							
							for(var i = 0; i < split_coordinates.length; i++){
								
								var latLng = split_coordinates[i].replace('[', '').replace(']', '').split(',');
								
								/**
								 * Convert "Lng,Lat" coordinates to "Lat,Lng" 
								 * @since 1.1 */
								
								var vertex_coordinates = (latlng_order == 'lnglat') 
									? new google.maps.LatLng(latLng[1], latLng[0]) 
									: new google.maps.LatLng(latLng[0], latLng[1]);								
							
								paths_array.push(vertex_coordinates);
								
							}

							/**
							 * Polygon */
							 
							if(draw_mode == 'polygon'){
								
								plugin_map[map_id].gmap3({					
									polygon:{
										options:{
											paths: paths_array,
											editable: true,
											draggable: false,
										},
										callback: function(polygon){
											drawingManager[map_id].setMap(null);
											last_overlay_obj[map_id] = polygon;
											google.maps.event.addListener(polygon, 'dblclick', function(event){					
												if(typeof event.vertex !== 'undefined' && event.vertex !== null){
													polygon.getPath().removeAt(event.vertex);
												}
											});	
											/*var poly_direction = cs_gmaps_drawing_is_clockwise(last_overlay_obj[map_id].getPath().getArray()) ? '"clockwise"' : '"counter-clockwise"';
											jQuery('div.cs_gmaps_drawing_polygon_direction').show();
											jQuery('div.cs_gmaps_drawing_polygon_direction .poly_direction').text(poly_direction);*/
										}
									}, 
									autofit:{}	
								});				
							
							/**
							 * Polyline */
							 
							}else if(draw_mode == 'polyline'){

								plugin_map[map_id].gmap3({					
									polyline:{
										options:{
											path: paths_array,
											editable: true,
											draggable: false,
										},
										callback: function(polyline){
											drawingManager[map_id].setMap(null);
											last_overlay_obj[map_id] = polyline;
											google.maps.event.addListener(polyline, 'dblclick', function(event){	
												if(typeof event.vertex !== 'undefined' && event.vertex !== null){
													polyline.getPath().removeAt(event.vertex);
												}
											});
										}
									}, 
									autofit:{}	
								});				
							
							/**
							 * Rectangle */
								
							}else if(draw_mode == 'rectangle'){
	
								plugin_map[map_id].gmap3({					
									rectangle:{
										options:{
											bounds: paths_array,
											editable: true,
											draggable: false,
										},
										callback: function(obj){
											drawingManager[map_id].setMap(null);
											last_overlay_obj[map_id] = obj;
										}
									}, 
									autofit:{}	
								});				

							}
							
						}
							
					/**
					 * Image (Ground Overlay/ */
					
					}else if(draw_mode == 'image'){

						var ne_coordinates = jQuery('input[type=text].cs-gmaps-drawing-ne-coordinates[data-map-id='+map_id+']').val().split(',');
						var sw_coordinates = jQuery('input[type=text].cs-gmaps-drawing-sw-coordinates[data-map-id='+map_id+']').val().split(',');
						var img_url = jQuery('input[type=text].cs-gmaps-drawing-img-url.'+map_id).val();

						if(typeof ne_coordinates[0] !== 'undefined' 
						&& typeof ne_coordinates[1] !== 'undefined' 
						&& typeof sw_coordinates[0] !== 'undefined'
						&& typeof sw_coordinates[1] !== 'undefined'){
						
							paths_array.push(new google.maps.LatLng(ne_coordinates[0], ne_coordinates[1]));
							paths_array.push(new google.maps.LatLng(sw_coordinates[0], sw_coordinates[1]));
								
							plugin_map[map_id].gmap3({													
								rectangle:{
									options:{
										bounds: paths_array,
										editable: true,
										draggable: false,
										fillOpacity: 0,
										strokeOpacity: 0.7,
										strokePosition: google.maps.StrokePosition.OUTSIDE,
									},
									callback: function(obj){
										drawingManager[map_id].setMap(null);
										last_overlay_obj[map_id] = obj;
										jQuery('input.cs_gmaps_drawing_render_img[data-map-id='+map_id+']').trigger('click');
									},
									events:{
										dragend: function(rectangle, event){
											jQuery('input.cs_gmaps_drawing_render_img[data-map-id='+map_id+']').trigger('click');	
										},
										bounds_changed: function(rectangle, event){
											jQuery('input.cs_gmaps_drawing_render_img[data-map-id='+map_id+']').trigger('click');	
										}
									}
								},								
								autofit:{}	
							});		
							
						}
												
					}	
									
				}
			},
			callback: function(mapObject){
					
				/**
				 * Add drawing tools to our map */
				
				var drawing_manager_options = {
					map: mapObject,
					drawingMode: (draw_mode == 'image') ? 'rectangle' : draw_mode,
					drawingControl: true,
					drawingControlOptions: {
						position: google.maps.ControlPosition.TOP_CENTER,
					  	drawingModes: [(draw_mode == 'image') ? 'rectangle' : draw_mode]
					},
					/*markerOptions: {
						icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png',
						draggable: true,
					},
					circleOptions: {		  
					  	editable: true,
					  	draggable: true,
					},*/					
				}
				
				if(draw_mode == 'image'){
					drawing_manager_options = jQuery.extend({}, drawing_manager_options, {
						rectangleOptions: {		  
							fillOpacity: 0,
							strokeOpacity: 0.7,
							strokePosition: google.maps.StrokePosition.OUTSIDE,
						}
					});
				}
				
				drawingManager[map_id] = new google.maps.drawing.DrawingManager(drawing_manager_options);
					
				/**
				 * Save the last drawn overlay on the map and remove the drawing tools */
				 
				google.maps.event.addListener(drawingManager[map_id], 'overlaycomplete', function(event){
					
					var overlay = event.overlay;
					var overlay_type = event.type;
					
					drawingManager[map_id].setMap(null);
					drawingManager[map_id].setDrawingMode(null);
					
					overlay.setEditable(true);
					last_overlay_obj[map_id] = overlay;
					
					/**
					 * Remove vertices */
					 
					if(overlay_type == 'polygon' || overlay_type == 'polyline'){
						last_overlay_obj[map_id].addListener('dblclick', function(e){	
							if(typeof e.vertex !== 'undefined' && e.vertex !== null){
								last_overlay_obj[map_id].getPath().removeAt(e.vertex);
							}
						});									
					}
						
					/**
					 * Render/Project image overlay on rectangle "overlaycomplete" event */
					
					if(draw_mode == 'image'){
						
						jQuery('input.cs_gmaps_drawing_render_img[data-map-id='+map_id+']').trigger('click');
						
							
						/**
						 * Render/Project image overlay on rectangle "dragend" event */
						 
						last_overlay_obj[map_id].addListener('dragend', function(){
							if(draw_mode == 'image'){
								jQuery('input.cs_gmaps_drawing_render_img[data-map-id='+map_id+']').trigger('click');
							}
						});
						
						/**
						 * Render/Project image overlay on rectangle "bounds_changed" event */
						 
						last_overlay_obj[map_id].addListener('bounds_changed', function(){
							if(draw_mode == 'image'){
								jQuery('input.cs_gmaps_drawing_render_img[data-map-id='+map_id+']').trigger('click');
							}
						});
					
					}
				
				});							
				
			}
		}
	});	
	
	/**
	 * Add Autocomplete to the address field */
	
	var input = jQuery('input.cs-gmaps-drawing-search[data-map-id='+map_id+']');
	var autocomplete = new google.maps.places.Autocomplete(input[0]);
	
}


function cs_gmaps_drawing_resize(map_id){
	
	if(typeof map_id !== 'undefined'){
		
		var mapObject = plugin_map[map_id].gmap3('get');
		
		google.maps.event.trigger(mapObject, 'resize');
					
		/**
		 * Create the map */
		
		var map_center = jQuery('div.cs_gmaps_drawing_container[data-map-id='+map_id+']').attr('data-map-center').split(',');
			
			/**
			 * Default Lat,Lng */
			 
			var lat = map_center[0];
			var lng = map_center[1];
		
		if(typeof lat !== 'undefined' && lat != '' && typeof lng !== 'undefined' && lng != ''){
		
			var latLng = new google.maps.LatLng(lat, lng);
	
			mapObject.panTo(latLng);
			mapObject.setCenter(latLng);
		
		}
		
	}
	
}

/**
 * Copy to clipboard
 *
 * @since 1.0
 */
function cs_gmaps_drawing_copy_to_clipboard(value){
	
	var $temp = jQuery('<input>');
	
	jQuery('body').append($temp);
	
	$temp.val(value).select();
	
	document.execCommand('copy');
	
	$temp.remove();						
	
	jQuery('div.cs_gmaps_drawing_copied_notice').fadeIn();
	
	setTimeout(function(){
		jQuery('div.cs_gmaps_drawing_copied_notice').fadeOut();
	}, 2000);
		
}
	
jQuery(document).ready(function($){ 

	/**
	 * Loop throught all "cs-gmaps-drawing" and init each map instance
	 * @since 1.0 */	 
	 
	$('.cs_gmaps_drawing_container').each(function(){
		
		var map_id = $(this);
		
		if(typeof map_id !== 'undefined')
			cs_gmaps_drawing_init(map_id);
	
	});
	
	/**
	 * Make this field repeatable
	 * @since 1.0 */
	 	
	$('.cmb-repeatable-group').on( 'cmb2_add_row', function(event, newRow) {
		
		/**
		 * Get group ID
		 * Change "cmb-group-_GROUP_ID" to "_GROUP_ID" */
		 
		var new_group_id = $(newRow).attr('id').replace('cmb-group-', '').split('-')[0];
		
		/**
		 * Get group index */
		 
		var new_group_index = $(newRow).attr('data-iterator');
		
		/**
		 * Get map ID attribute */
		 
		var cs_gmaps_drawing_map_id = $(newRow).find('.cs_gmaps_drawing_container').attr('data-map-id');
		
		/**
		 * Get gmaps drawing field ID			 
		 * 1. Change "_GROUP_ID_{INDEX}_{FIELD_ID}" to "{INDEX}_{FIELD_ID}"
		 * 2. Explode Map ID attribute to ["{INDEX}", "{FIELD_ID}"].
		 * 3. Remove "{INDEX}" from exploded array. */
		 
		var explode_cs_gmaps_drawing_field_id = cs_gmaps_drawing_map_id.replace(new_group_id+'_', '').split('_');
			explode_cs_gmaps_drawing_field_id.shift();
		
		/**
		 * Get gmaps drawing field ID */
		 
		var cs_gmaps_drawing_field_id = explode_cs_gmaps_drawing_field_id.join('_');
		
		var new_data_map_id = new_group_id + '_' + new_group_index + '_' + cs_gmaps_drawing_field_id;
		
		/**
		 * Change map ID of all fields inside the new row */
		 
		$(newRow).find('[data-map-id='+cs_gmaps_drawing_map_id+']').each(function (index, element) {
			$(this).attr('data-map-id', new_data_map_id);
		});
		 
		$(newRow).find('[name=dragging_mode_'+cs_gmaps_drawing_map_id+']').each(function (index, element) {
			$(this).attr('name', 'dragging_mode_'+new_data_map_id);
		});
		
		$(newRow).find('[name=img_low_opacity_'+cs_gmaps_drawing_map_id+']').each(function (index, element) {
			$(this).attr('name', 'img_low_opacity_'+new_data_map_id);
		});
			
		setTimeout(function(){						
			var map_id = $('.cs_gmaps_drawing_container[data-map-id='+new_data_map_id+']');
			if(typeof map_id !== 'undefined')
				cs_gmaps_drawing_init(map_id);
		}, 100);
		
	});
	
	/**
	 * Search for address coordinates
	 * @since 1.0 */	 
	
	$(document).on('click', 'input.cs_gmaps_drawing_search_btn', function(e){
		
		e.preventDefault();
		
		var map_id = $(this).attr('data-map-id');
		var address = $('input.cs-gmaps-drawing-search[data-map-id='+map_id+']').val();
								
		var error_address1 = 'We could not understand the location ';
		var error_address2 = '\n\nSuggestions:';
			error_address2 += '\n--------------';
			error_address2 += '\n1. Make sure all street and city names are spelled correctly.';
			error_address2 += '\n2. Make sure your address includes a city and state.';
			error_address2 += '\n3. Try entering a zip code.';
		
		plugin_map[map_id].gmap3({
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
			 
			if(status == google.maps.GeocoderStatus.OK){
							
				plugin_map[map_id].gmap3({							
					map:{
						options:{
							center: results[0].geometry.location,
							zoom: 12,
						}
					}
				});
				
			}else alert(error_address1 + '"' + address + '"' + error_address2);
			
		});
		
	});
	
	$(document).on('keypress', '.cs-gmaps-drawing-preventDefault', function(e){
		if(e.keyCode == 13){
			e.preventDefault();
		}
	});
	
	/**
	 * Get user location
	 * @since 1.0 */	 
	
	$(document).on('click', 'button.cs_gmaps_drawing_geoloc_btn', function(e){
		
		e.preventDefault();
		
		var map_id = $(this).attr('data-map-id');
		
		plugin_map[map_id].gmap3({
			clear: {
				name:"marker",
				all: true
			},
		});
	
		plugin_map[map_id].gmap3({
			getgeoloc:{
				callback : function(latLng){

					if(latLng){
						
						plugin_map[map_id].gmap3({
							map:{
								options:{
									center: latLng,
									zoom: 12,
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
	 * Copy/Paste latLngs to coordinates field
	 * @since 1.0 */ 
	 		  
	$(document).on('click', 'input.cs_gmaps_drawing_get_latLngs', function(e){
		
		e.preventDefault();
		
		var map_id = $(this).attr('data-map-id');
		
		var draw_mode = $(this).attr('data-draw-mode');

		if(typeof last_overlay_obj[map_id] !== 'undefined'){

			var current_overlay = last_overlay_obj[map_id];

			var coordinates = new Array();
			
			if(current_overlay.getVisible() === true){

				/**
				 * Polygon & Polyline */
				 
				if(draw_mode == 'polygon' || draw_mode == 'polyline'){			
					
					var poly_paths = current_overlay.getPath();		
					
					for (var i = 0; i < poly_paths.getLength(); i++) {
						var xy = poly_paths.getAt(i);
						coordinates.push('[' + xy.lat() + ',' + xy.lng() + ']');
					}
					
					/**
					 * Show Polygon orientation notice 
					 
					if(draw_mode == 'polygon'){
						var poly_direction = cs_gmaps_drawing_is_clockwise(poly_paths.getArray()) ? '"clockwise"' : '"counter-clockwise"';
						jQuery('div.cs_gmaps_drawing_polygon_direction').show();
						jQuery('div.cs_gmaps_drawing_polygon_direction .poly_direction').text(poly_direction);					
					}*/
					
				/**
				 * Rectangle */
					 
				}else if(draw_mode == 'rectangle' || draw_mode == 'image'){
					
					var bounds = current_overlay.getBounds();
					
					var northEast = bounds.getNorthEast();			
						coordinates.push('[' + northEast.lat() + ',' + northEast.lng() + ']');
					
					var getSouthWest = bounds.getSouthWest();
						coordinates.push('[' + getSouthWest.lat() + ',' + getSouthWest.lng() + ']');					
				
				}
				
				current_overlay.setEditable(true);
			
				drawingManager[map_id].setDrawingMode(null);
			
			}
			
			/**
			 * Write the coordinates */
			
			if(coordinates.length > 0){ 
				
				if(draw_mode == 'image'){

					var $ne_coordinates_input = $('input[type=text].cs-gmaps-drawing-ne-coordinates[data-map-id='+map_id+']');
					var $sw_coordinates_input = $('input[type=text].cs-gmaps-drawing-sw-coordinates[data-map-id='+map_id+']');
					
					$ne_coordinates_input.val(coordinates[0].replace('[', '').replace(']', ''));
					$sw_coordinates_input.val(coordinates[1].replace('[', '').replace(']', ''));
					
					$('input.cs_gmaps_drawing_copy_ne_coordinates[data-map-id='+map_id+']').removeAttr('disabled');
					$('input.cs_gmaps_drawing_copy_sw_coordinates[data-map-id='+map_id+']').removeAttr('disabled');
					$('input.cs_gmaps_drawing_render_img[data-map-id='+map_id+']').removeAttr('disabled');
	
				}else{
					
					var $coordinates_input = $('textarea.cs-gmaps-drawing-coordinates[data-map-id='+map_id+']');
					
					$coordinates_input.val(coordinates);
	
					/**
					 * Copy coordinates to clipboard */
					
					var copy_clipboard = $(this).attr('data-copy-clipboard');
					
					setTimeout(function(){
						if(copy_clipboard == 'true'){
							cs_gmaps_drawing_copy_to_clipboard(coordinates);
						}
					}, 500);
					
				}
				
			}
		
		}
		
	});
	
	/**
	 * Project/Render (ground overlay) on the map
	 * @since 1.0 */
	 
	$(document).on('click', 'input.cs_gmaps_drawing_render_img', function(e){
		
		e.preventDefault();
		
		var map_id = $(this).attr('data-map-id');
		
		var img_url = $(this).parent().prev('div').find('input[type=text].cs-gmaps-drawing-img-url').val(); //@edited 1.2
		
		var mapObject = plugin_map[map_id].gmap3('get');
		
		if(typeof last_overlay_obj[map_id] === 'undefined')
			return;
		
		var current_rectangle = last_overlay_obj[map_id];
		
		if(typeof groundOverlay[map_id] !== 'undefined' && groundOverlay[map_id].setMap !== 'undefined')
			groundOverlay[map_id].setMap(null);
		
		if(img_url != '' && current_rectangle.getVisible() === true){	
		
			var image_bounds = current_rectangle.getBounds();
	
			groundOverlay[map_id] = new google.maps.GroundOverlay(
				img_url,
				image_bounds
			);
			
			groundOverlay[map_id].setMap(mapObject);
			
			var opacity_level = $('div.cs_gmaps_drawing_img_low_opacity input[type=checkbox][data-map-id='+map_id+']').is(':checked') ? 0.1 : 0.8;			
			groundOverlay[map_id].setOpacity(opacity_level);
			
		}
		
	});
	 
	/**
	 * Copy North-East coordinates to clipboard
	 * @since 1.0 */
	
	$(document).on('click', 'input.cs_gmaps_drawing_copy_ne_coordinates', function(e){
		
		e.preventDefault();
		
		var map_id = $(this).attr('data-map-id');
					
		var ne_coordinates = $('input.cs-gmaps-drawing-ne-coordinates[data-map-id='+map_id+']').val();
		
		cs_gmaps_drawing_copy_to_clipboard(ne_coordinates);
			
	});
	
	/**
	 * Copy South-West coordinates to clipboard
	 * @since 1.0 */
	
	$(document).on('click', 'input.cs_gmaps_drawing_copy_sw_coordinates', function(e){
		
		e.preventDefault();
		
		var map_id = $(this).attr('data-map-id');
					
		var sw_coordinates = $('input.cs-gmaps-drawing-sw-coordinates[data-map-id='+map_id+']').val();
		
		cs_gmaps_drawing_copy_to_clipboard(sw_coordinates);
			
	});
	
	/**
	 * Clear the overlay from the map and reset/show drawing tools
	 * @since 1.0 */
	 
	$(document).on('click', 'input.cs_gmaps_drawing_clear_overlay', function(e){

		e.preventDefault();
		
		var map_id = $(this).attr('data-map-id');
		
		var draw_mode = $(this).attr('data-draw-mode');
		
		var mapObject = plugin_map[map_id].gmap3('get');
		
		$('div.cs_gmaps_drawing_drag_mode input[type=checkbox][data-map-id='+map_id+']').removeAttr('checked');
		
		plugin_map[map_id].gmap3({
			get: {
				name: draw_mode,
				callback: function(overlayObj){
					
					if(typeof last_overlay_obj[map_id] !== 'undefined'){
						last_overlay_obj[map_id].setVisible(false);
					}
					
					drawingManager[map_id].setMap(mapObject);
					drawingManager[map_id].setDrawingMode((draw_mode == 'image') ? 'rectangle' : draw_mode);
					
					if(draw_mode == 'image'){
						
						if(typeof groundOverlay[map_id] !== 'undefined' && groundOverlay[map_id].setMap !== 'undefined')
							groundOverlay[map_id].setMap(null);
					/**
					 * Show Polygon orientation notice */
					
					}/*else if(draw_mode == 'polygon'){						
						jQuery('div.cs_gmaps_drawing_polygon_direction .poly_direction').text('');					
						jQuery('div.cs_gmaps_drawing_polygon_direction').hide();
					}*/
					
				}
			}
		});					
		
	});	
	
	/**
	 * Activate overlay dragging mode
	 * @since 1.0 */
	 
	$(document).on('click', 'div.cs_gmaps_drawing_drag_mode input[type=checkbox]', function(e){

		var map_id = $(this).attr('data-map-id');
		
		if(typeof last_overlay_obj[map_id] !== 'undefined'){
			var drag = $(this).is(':checked') ? true : false;
			last_overlay_obj[map_id].setDraggable(drag);
		}else e.preventDefault();
		
	});
	
	/**
	 * Activate image overlay low opacity mode
	 * @since 1.0 */
	 
	$(document).on('click', 'div.cs_gmaps_drawing_img_low_opacity input[type=checkbox]', function(e){

		var map_id = $(this).attr('data-map-id');
		
		if(typeof groundOverlay[map_id] !== 'undefined' && groundOverlay[map_id].setOpacity !== 'undefined'){
			var opacity_level = $(this).is(':checked') ? 0.4 : 0.8;
			groundOverlay[map_id].setOpacity(opacity_level);
		}else e.preventDefault();
		
	});
								
	/**
	 * Resize each map instance when meta box is opened 
	 * @since 1.0 */

	if(typeof postboxes !== 'undefined'){
		
		postboxes.pbshow = function () {

			for(var i=0; i<cs_gmaps_drawing.length; i++) {
				
				var map_id = cs_gmaps_drawing[i];
 
				cs_gmaps_drawing_resize(map_id);
				
			};
	
		};
		
	}
					
	/**
	 * Resolve a problem of Google Maps & jQuery Tabs.
	 * Resize the map once visible on the screen.
	 * @since 1.0 */

    $('body').ready(function(){
        
        if($('div.cs-gmaps-drawing-map').is(':visible')){                            
            
            var map_id = $(this).attr('data-map-id');
		
            setTimeout(function(){
                cs_gmaps_drawing_resize(map_id);
            }, 100);
            
        }
        
    });
	
});
