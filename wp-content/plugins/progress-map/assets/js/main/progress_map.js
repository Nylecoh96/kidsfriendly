/* @Version 5.7.1 */

//=======================//
//==== Map functions ====//
//=======================//

	window.cspm_global_object = {};
	
	var map_layout = {};
	var heatmap = {}; //@since 3.3	
	var all_map_markers_objs = {}; //@since 5.0

	/**
	 * This will validate the latitude & longitude 
	 * @since 3.6 
	 */	 
	function cspm_validate_latLng(lat, lng){

		if(lat < -90 || lat > 90 || lat.toString().indexOf(',') != -1 || isNaN(Math.floor(lat))){
			return false;
		}else if(lng < -180 || lng > 180 || lng.toString().indexOf(',') != -1 || isNaN(Math.floor(lng))){
			return false;
		}else if(lat == "" || lng == ""){
			return false;
		}else return new google.maps.LatLng(lat, lng);
				
	}
	
	/**
	 * Load map options
	 *
	 * @light_map, Declare the light map in order to use the apropriate options for this type of map.
	 * @latLng, The center point of the map.
	 * @zoom, The default zoom of the map.
	 *
	 */
	function cspm_load_map_options(map_id, light_map, latLng, zoom){
      
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		var latlng = (latLng != null) ? latLng.split(',') : progress_map_vars.map_script_args[map_script_id]['center'].split(',');
		
		if(!cspm_validate_latLng(latlng[0], latlng[1])){
			
			var title = 'The map center is incorrect!<br /><br />';
			var message  = 'Edit your map. Click on the menu <strong>\"Map settings\"</strong> and ...</strong><br />';
				message += '<strong>1.</strong> Make sure the Laitude & Longitude are comma separated.<br />';
				message += '<strong>2.</strong> Make sure the Latitude ranges between -90 and 90 degrees.<br />';
				message += '<strong>3.</strong> Make sure the Longitude ranges between -180 and 180 degrees.<br />';
				message += '<strong>4.</strong> Make sure the Latitude coordinate is written first, followed by the Longitude.<br />';
				message += '<strong>5.</strong> Make sure to replace the comma in the Latitude and/or the Longitude by a dot.';
			cspm_popup_msg(map_id, 'fatal_error', title, message);
		
		}
		
		var zoom_value = (zoom != null) ? parseInt(zoom) : parseInt(progress_map_vars.map_script_args[map_script_id]['zoom']);
		var max_zoom_value = parseInt(progress_map_vars.map_script_args[map_script_id]['max_zoom']);
		var min_zoom_value = parseInt(progress_map_vars.map_script_args[map_script_id]['min_zoom']);
		
		var zoom_on_doubleclick = (progress_map_vars.map_script_args[map_script_id]['zoom_on_doubleclick'] == 'true') ? true : false;
   
		var default_options = {			
			center:[latlng[0], latlng[1]],
			zoom: zoom_value,			
			maxZoom: max_zoom_value,
			minZoom: min_zoom_value,				
			mapTypeControl: (progress_map_vars.map_script_args[map_script_id]['mapTypeControl'] == 'false') ? false : true,
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
				position: google.maps.ControlPosition.TOP_RIGHT,
				mapTypeIds: [
					google.maps.MapTypeId.ROADMAP,
					google.maps.MapTypeId.SATELLITE,
					google.maps.MapTypeId.TERRAIN,
					google.maps.MapTypeId.HYBRID
				]				
			},
			streetViewControl: (progress_map_vars.map_script_args[map_script_id]['streetViewControl'] == 'false') ? false : true,	
			streetViewControlOptions: {
				position: google.maps.ControlPosition.RIGHT_TOP  
			},
			gestureHandling: progress_map_vars.map_script_args[map_script_id]['gestureHandling'], //@since 5.6.5
			disableDoubleClickZoom: zoom_on_doubleclick,
			backgroundColor: progress_map_vars.map_script_args[map_script_id]['map_background'],
			clickableIcons: (progress_map_vars.map_script_args[map_script_id]['clickableIcons'] == 'false') ? false : true,
			rotateControl: (progress_map_vars.map_script_args[map_script_id]['rotateControl'] == 'false') ? false : true,
			rotateControlOptions: {
				position: google.maps.ControlPosition.RIGHT_TOP,			
			},
			scaleControl: (progress_map_vars.map_script_args[map_script_id]['scaleControl'] == 'false') ? false : true,			
			fullscreenControl: (progress_map_vars.map_script_args[map_script_id]['fullscreenControl'] == 'false') ? false : true,
			/*fullscreenControlOptions:{
				position: google.maps.ControlPosition.BOTTOM_LEFT
			}*/
			controlSize: progress_map_vars.map_script_args[map_script_id]['controlSize'], //@since 4.8			
		};

		if(progress_map_vars.map_script_args[map_script_id]['zoomControl'] == 'true' && progress_map_vars.map_script_args[map_script_id]['zoomControlType'] == 'default'){
			
			var zoom_options = {
				zoomControl: true,
				zoomControlOptions:{
					//position: google.maps.ControlPosition.LEFT_BOTTOM,
				},
			};
		
		}else{
			var zoom_options = {
				zoomControl: false,
			};
		}
		
		var map_options = jQuery.extend({}, default_options, zoom_options);

		return map_options;
		
	}					
	
	/**
	 * Set the initial map style
	 * @since 2.4
	 */
	function cspm_initial_map_style(map_style, custom_style_status){
			
		if(map_style == 'custom_style' && custom_style_status == false)
			var map_type_id = {mapTypeId: google.maps.MapTypeId.ROADMAP};
		
		else if(map_style == 'custom_style')
			var map_type_id = {mapTypeId: "custom_style"};
			
		else if(map_style == 'ROADMAP')
			var map_type_id = {mapTypeId: google.maps.MapTypeId.ROADMAP};
			
		else if(map_style == 'SATELLITE')
			var map_type_id = {mapTypeId: google.maps.MapTypeId.SATELLITE};
			
		else if(map_style == 'TERRAIN')				
			var map_type_id = {mapTypeId: google.maps.MapTypeId.TERRAIN};
			
		else if(map_style == 'HYBRID')				
			var map_type_id = {mapTypeId: google.maps.MapTypeId.HYBRID};
		
		return map_type_id;
		
	}
	
	var post_ids_and_categories = {};
	var post_lat_lng_coords = {};
	var post_ids_and_child_status = {}

	/**
	 * Create Marker Object
	 *
	 * @Since 2.5 
	 * @Updated 3.5 | 4.0
	 */
	function cspm_new_pin_object(map_id, pin_options){

		var post_id = pin_options.post_id;
		var post_categories = pin_options.post_categories;
		var lat = pin_options.coordinates.lat;
		var lng = pin_options.coordinates.lng;
		var marker_img = pin_options.icon.url;
		var marker_size = pin_options.icon.size;
		var is_child = pin_options.is_child;
		var media = pin_options.media;
		var address = pin_options.coordinates.address; //@since 5.4

		if(!cspm_validate_latLng(lat, lng))
			return false;		

		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		 
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		post_lat_lng_coords[map_id][post_id] = lat+'_'+lng;
	
		/**
		 * Create an object of that post_id and its categories/status for the faceted search */
		 
		post_lat_lng_coords[map_id]['post_id_'+post_id] = {};
		post_ids_and_child_status[map_id][lat+'_'+lng] = is_child;
		
		/**
		 * Get the current post categories */
		 
		var post_category_ids = (post_categories != '') ? post_categories.split(',') : '';
		
		/**
		 * Collect an object of all posts in the map and their categories
		 * Useful for the faceted search & the search form */
		 
		post_lat_lng_coords[map_id]['post_id_'+post_id][0] = post_category_ids;
		
		/**
		 * By default the marker image is the default Google map red marker */
		 
		var marker_icon = '';
		
		/**
		 * If the selected marker is the customized type */
		 
		if(progress_map_vars.map_script_args[map_script_id]['defaultMarker'] == 'customize'){
			
			/**
			 * Get the custom marker image
			 * If the marker categories option is set to TRUE, the marker image will be the uploaded marker category image
			 * If the marker categories option is set to FALSE, the marker image will be the default custom image provided by the plugin */
			 
			var marker_cat_img = marker_img;

			/**
			 * Marker image size with retina support */
			 
			var marker_img_width = (progress_map_vars.map_script_args[map_script_id]['retinaSupport'] == "true" && !cspm_is_svg(marker_cat_img)) ? parseInt(marker_size.split('x')[0])/2 : parseInt(marker_size.split('x')[0]);
			var marker_img_height = (progress_map_vars.map_script_args[map_script_id]['retinaSupport'] == "true" && !cspm_is_svg(marker_cat_img)) ? parseInt(marker_size.split('x')[1])/2 : parseInt(marker_size.split('x')[1]);

			/**
			 * Marker image anchor point */
			 
			var anchor_y = marker_img_height/2;
			var anchor_x = marker_img_width/2;	
			var anchor_point = null;
			
			if(progress_map_vars.map_script_args[map_script_id]['marker_anchor_point_option'] == 'auto'){				
				anchor_point = new google.maps.Point(anchor_x, anchor_y);
			}else if(progress_map_vars.map_script_args[map_script_id]['marker_anchor_point_option'] == 'manual'){
				if(progress_map_vars.map_script_args[map_script_id]['retinaSupport'] == "true" && !cspm_is_svg(marker_cat_img)){
					anchor_point = new google.maps.Point(
						progress_map_vars.map_script_args[map_script_id]['marker_anchor_point'].split(',')[0]/2, 
						progress_map_vars.map_script_args[map_script_id]['marker_anchor_point'].split(',')[1]/2
					);
				}else anchor_point = new google.maps.Point(progress_map_vars.map_script_args[map_script_id]['marker_anchor_point'].split(',')[0], progress_map_vars.map_script_args[map_script_id]['marker_anchor_point'].split(',')[1]);
			}

			/**
			 * Marker icon */
			
			marker_icon = {
				url: marker_cat_img,
			}
			
			if(marker_img_width > 0 && marker_img_height > 0){
				marker_icon = jQuery.extend({}, marker_icon, {					
					anchor: anchor_point,
					scaledSize: new google.maps.Size(marker_img_width, marker_img_height),
					size: new google.maps.Size(marker_img_width, marker_img_height),
				});
			}
			
		}		

		/**
		 * Marker title
		 * @since 4.0 */
		
		var title = (typeof progress_map_vars['titles_'+map_id] !== 'undefined' && typeof progress_map_vars['titles_'+map_id][post_id] !== 'undefined') ? progress_map_vars['titles_'+map_id][post_id] : ''; 
		
		/**
		 * Marker labels
		 * @since 4.0 */
								
		var marker_label_obj = cspm_marker_label_obj(map_id, post_id);
		var marker_label = {};
		
		if(cspm_object_size(marker_label_obj) > 0){
			
			var marker_label = { label: marker_label_obj };
		
			/**
			 * Marker label origin */

			if(typeof marker_label_obj.top !== 'undefined' && !isNaN(marker_label_obj.top) && typeof marker_label_obj.left !== 'undefined' && !isNaN(marker_label_obj.left))
				marker_icon = jQuery.extend({}, marker_icon, { labelOrigin: new google.maps.Point(marker_label_obj.top, marker_label_obj.left) });			
		
		}
		
		/**
		 * Build the marker object */

		var pin_options = { 
			optimized: false, 
			icon: marker_icon, 
			id: post_id, 
			post_id: post_id, 
			is_child: is_child,
			media: media,
			title: title, //@since 4.0
			map_id: map_id, //@since 4.6
		};
		
		pin_options = jQuery.extend({}, pin_options, marker_label);
	
		var pin_object = {
			latLng: [lat, lng], 
			tag: ['post_id__'+ post_id, 'to_cluster'],
			id: post_id +'_'+ is_child,
			options: pin_options,
		};	
		
		return pin_object;									
	
	}
	
	
	/**
	 * This will return the marker label object.
	 *
	 * @since 4.0
	 */
	function cspm_marker_label_obj(map_id, post_id){

		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		 
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		var marker_labels = progress_map_vars.map_script_args[map_script_id]['marker_labels']; //@since 4.0

		if( marker_labels == 'yes' && typeof progress_map_vars['marker_labels_'+map_id] !== 'undefined'
		&& typeof progress_map_vars['marker_labels_'+map_id][post_id] !== 'undefined' 
		&& typeof progress_map_vars['marker_labels_'+map_id][post_id]['hide'] !== 'undefined' 
		&& progress_map_vars['marker_labels_'+map_id][post_id]['hide'].toString() == 'no'
		&& progress_map_vars['marker_labels_'+map_id][post_id]['text'].toString() != '' ){

			return {
				text: progress_map_vars['marker_labels_'+map_id][post_id]['text'].toString(),
				color: progress_map_vars['marker_labels_'+map_id][post_id]['color'].toString(),
				fontFamily: progress_map_vars['marker_labels_'+map_id][post_id]['fontFamily'].toString(),
				fontSize: progress_map_vars['marker_labels_'+map_id][post_id]['fontSize'].toString(),
				fontWeight: progress_map_vars['marker_labels_'+map_id][post_id]['fontWeight'].toString(),
				top: parseInt(progress_map_vars['marker_labels_'+map_id][post_id]['top']),
				left: parseInt(progress_map_vars['marker_labels_'+map_id][post_id]['left']),
			};
				
		}else return {}; 
	
	}
	
	/**
	 * This will check if an image is an SVG icon
	 *
	 * @since 4.0
	 */
	function cspm_is_svg(file){
		var ext = file.substr( (file.lastIndexOf('.') +1) );
		return (ext == 'svg') ? true : false;		
	}
	
	/**
	 * This will set a label for each marker on the map.
	 *
	 * Note: This will prevent an issue where marker labels stay visible when markers are not in the viewport of the map!
	 *
	 * @since 4.0
	 */
	function cspm_set_marker_labels_visibility(plugin_map, map_id){

		var r = jQuery.Deferred();
		
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';
		 
		var marker_labels = progress_map_vars.map_script_args[map_script_id]['marker_labels'];
		
		if(marker_labels == 'no')
			return; 

		plugin_map.gmap3({
			get: {
				name: 'marker',
				tag: 'to_cluster',
				all:  true,
				callback: function(markers) {				

					for(var i = 0; i < markers.length; i++){	
						
						var marker = markers[i];
						var post_id = parseInt(marker.post_id);

						if(post_id != '' && post_id != 'user_location' && !isNaN(post_id)){
													
							var latLng = marker.position;
							var lat = latLng.lat();
							var lng = latLng.lng();

							// if the marker is within the viewport of the map
							if(cspm_map_bounds_contains_marker(plugin_map, lat, lng) && marker.getMap() != null && marker.visible == true){
								
								if(marker.getLabel() == null){
									var marker_label = cspm_marker_label_obj(map_id, post_id);
									if(cspm_object_size(marker_label) > 0)
										marker.setLabel(marker_label);
								}
								
							}else marker.setLabel(null);
							
						}
						
					}
				
					r.resolve(); 
																												
				}
			}
		});
		
		return r;
		
	}
	
	/**
	 * Clustering markers 
	 */
	function cspm_clustering(plugin_map, map_id, light_map){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		var markerCluster;
		
		var mapObject = plugin_map.gmap3('get');
	
		small_cluster_size = progress_map_vars.map_script_args[map_script_id]['small_cluster_size'];
		medium_cluster_size = progress_map_vars.map_script_args[map_script_id]['medium_cluster_size']
		big_cluster_size = progress_map_vars.map_script_args[map_script_id]['big_cluster_size'];

		var max_zoom = parseInt(progress_map_vars.map_script_args[map_script_id]['max_zoom']);

		plugin_map.gmap3({
			get: {
				name: 'marker',
				tag: 'to_cluster', //@since 3.5 | Define markers to cluster
				all: true,
				callback: function(objs){

					if(objs && typeof MarkerClusterer !== 'undefined'){
						
						markerCluster = new MarkerClusterer(mapObject, objs, {
							gridSize: parseInt(progress_map_vars.map_script_args[map_script_id]['grid_size']),
							styles: [
								{
									url: progress_map_vars.map_script_args[map_script_id]['small_cluster_icon'],
									height: parseInt(small_cluster_size.split('x')[0]),
									width: parseInt(small_cluster_size.split('x')[1]),
									textColor: progress_map_vars.map_script_args[map_script_id]['cluster_text_color'],
									textSize: 15,
									fontWeight: 'bold',
									fontFamily: 'monospace'
								}, {
									url: progress_map_vars.map_script_args[map_script_id]['medium_cluster_icon'],
									height: parseInt(medium_cluster_size.split('x')[0]),
									width: parseInt(medium_cluster_size.split('x')[1]),
									textColor: progress_map_vars.map_script_args[map_script_id]['cluster_text_color'],
									textSize: 17,	
									fontWeight: 'bold',								
									fontFamily: 'monospace'
								}, {
									url: progress_map_vars.map_script_args[map_script_id]['big_cluster_icon'],
									height: parseInt(big_cluster_size.split('x')[0]),
									width: parseInt(big_cluster_size.split('x')[1]),
									textColor: progress_map_vars.map_script_args[map_script_id]['cluster_text_color'],
									textSize: 19,		
									fontWeight: 'bold',							
									fontFamily: 'monospace'
								}
							],
							zoomOnClick: true, //(progress_map_vars.map_script_args[map_script_id]['initial_map_style'] == 'TERRAIN') ? true : false,	
							maxZoom: max_zoom,
							minimumClusterSize: 2,
							averageCenter: true,
							ignoreHidden: true,	
							title: progress_map_vars.cluster_text, //@since 2.8
							enableRetinaIcons: (progress_map_vars.map_script_args[map_script_id]['retinaSupport'] == 'true') ? true : false,	
						});					
						
						var cluster_xhr;
						
						/**
						 * Draw a circle around the cluster area
						 * @since 3.6 */
						
						var main_hex_color = (typeof progress_map_vars.map_script_args['initial']['main_hex_color'] !== 'undefined') 
							? progress_map_vars.map_script_args['initial']['main_hex_color'] : "#189AC9"; //@since 4.7
						 
						google.maps.event.addListener(markerCluster, 'mouseover', function(cluster){							
							var radius = google.maps.geometry.spherical.computeDistanceBetween(cluster.getCenter(), cluster.getBounds().getNorthEast());							
							plugin_map.gmap3({
							  	circle:{
									options:{
										center: cluster.getCenter(),
										radius: radius,
										strokeColor: main_hex_color,
										strokeOpacity: 0.5,
										strokeWeight: 3,
										fillColor: main_hex_color,
										fillOpacity: 0.3,
									},
									tag: 'cluster_area',
							  	}
							});
						});
						
						/**
						 * Remove the circle around the cluster area
						 * @since 3.6 */
						 
						google.maps.event.addListener(markerCluster, 'mouseout', function(cluster){	
							plugin_map.gmap3({
							  	clear:{
									tag: 'cluster_area',
								}								  
							});
						});
						
						/**
						 * On cluster click, Hide and show overlays depending on markers positions */
						
						google.maps.event.addListener(markerCluster, 'click', function(cluster){

							/**
							 * Remove the circle around the cluster area
							 * @since 3.6 */
						 
							plugin_map.gmap3({
								clear:{
									tag: 'cluster_area',
								}								  
							});
						
							/**
							 * Show locations inside the cluster */
							 
							var current_zoom = mapObject.getZoom();	
	
							if(current_zoom >= max_zoom || (progress_map_vars.map_script_args[map_script_id]['initial_map_style'] == 'TERRAIN' && current_zoom >= 15)) {
								 
								if(typeof cluster.markers_ !== 'undefined')
									var cluster_markers = cluster.markers_;
								else var cluster_markers = cluster.getMarkers();									

								google.maps.event.addListenerOnce(mapObject, 'idle', function(){
										
									current_zoom = mapObject.getZoom();	
									
									// Get cluster position and convert it to XY
									var scale = Math.pow(2, current_zoom);
									var nw = new google.maps.LatLng(mapObject.getBounds().getNorthEast().lat(), mapObject.getBounds().getSouthWest().lng());
									var worldCoordinateNW = mapObject.getProjection().fromLatLngToPoint(nw);
									var worldCoordinate = mapObject.getProjection().fromLatLngToPoint(cluster.center_);
									var pixelOffset = new google.maps.Point(Math.floor((worldCoordinate.x - worldCoordinateNW.x) * scale), Math.floor((worldCoordinate.y - worldCoordinateNW.y) * scale));
									var mapposition = plugin_map.position();
			
									var count_li = 0;						
	
									// @since 2.5 ====							
									var clustred_post_ids = [];
									// ===============
									
									if(typeof cluster_markers !== 'undefined'){
										
										for (var i = 0; i < cluster_markers.length; i++ ){
											
											if(cluster_markers[i].visible == true){											
												
												count_li++;
												
												// @since 2.5 ====
												clustred_post_ids.push(cluster_markers[i].id);										
												// ===============
												
											}
											
										}
										
										// @since 3.5 ====
										var implode_clustred_post_ids = clustred_post_ids.join().replace(',', '');										
										// ===============
											
										setTimeout(function(){
											
											// @since 3.5 ====
											var cluster_id = jQuery('div.cluster_posts_widget_'+map_id).attr('data-cluster-id');
												if(cluster_id == implode_clustred_post_ids)
													return; // Prevent re-loading the same data if already exists!
											// ===============
												
											jQuery('div.cluster_posts_widget_'+map_id).html('<div class="cspm_infobox_spinner cspm_border_top_after_hex"></div></div>');
											
											if(count_li > 0){

												// @since 2.5 ====
												jQuery('div.cluster_posts_widget_'+map_id).removeClass('flipOutX');
												jQuery('div.cluster_posts_widget_'+map_id).addClass('cspm_animated flipInX').css('display', 'block');
												jQuery('div.cluster_posts_widget_'+map_id).css({
													left: (((pixelOffset.x / 2) - mapposition.left) - 70) + 'px', 
													top: (((pixelOffset.y / 2) + mapposition.top) - 40) + 'px'
												});	
												
												// @since 3.5 ====
												jQuery('div.cluster_posts_widget_'+map_id).attr('data-cluster-id', implode_clustred_post_ids);
												// ===============
												
												if(cluster_xhr && cluster_xhr.readystate != 4){
													cluster_xhr.abort();
												}
												
												cluster_xhr = jQuery.post(
													progress_map_vars.ajax_url,
													{
														action: 'cspm_load_clustred_markers_list',
														post_ids: clustred_post_ids,
														map_object_id: progress_map_vars.map_script_args[map_script_id]['map_object_id'],
														map_settings: progress_map_vars.map_script_args[map_script_id]['map_settings'],																	
														light_map: light_map
													},
													function(data){	
														
														jQuery('div.cluster_posts_widget_'+map_id).html(data);
														
														/**
														 * Call custom scroll bar for infowindow */
														
														if(typeof jQuery('div.cluster_posts_widget_'+map_id).mCustomScrollbar === 'function'){
																													
															jQuery('div.cluster_posts_widget_'+map_id).mCustomScrollbar({
																autoHideScrollbar:false,
																mouseWheel:{
																	enable: true,
																	preventDefault: true,																	
																},
																theme: 'dark-2'
															});		
														
														}

                                                        jQuery(document).on('click', 'div.cluster_posts_widget_'+map_id+' ul li', function(){ //@edited 5.6.3
                                                            var post_id = jQuery(this).attr('data-post-id');											
                                                            if(!light_map){
                                                                var index = jQuery('li.cspm_carousel_item[data-map-id='+map_id+'][data-post-id='+post_id+']').attr('jcarouselindex'); //@edited 5.0
                                                                cspm_call_carousel_item(jQuery('ul#cspm_carousel_'+map_id).data('jcarousel'), index);													
                                                                cspm_carousel_item_hover_style('li.jcarousel-item-'+index+'.cspm_carousel_item[data-map-id='+map_id+'][data-post-id='+post_id+']', map_id); //@edited 5.0														
                                                            }
                                                        });

													}
												);												
												
											}else jQuery('div.cluster_posts_widget_'+map_id).css({'display':'none'});
									
										}, 1000);
																				
									}													
									
									/**
									 * Fix an issue where the cluster disappers when reaching the max_zoom
									 *
									 * @since 2.6.5
									 */
									setTimeout(function(){						
										current_zoom = mapObject.getZoom();	
										if(progress_map_vars.map_script_args[map_script_id]['initial_map_style'] != 'TERRAIN' && current_zoom > max_zoom){
											mapObject.setZoom(max_zoom);
										}
									}, 100);
									
								});
							
							}
							
						});
					
					}
					
				}
			}
			
		});
				
		return markerCluster;
	
	}
	
	function cspm_zoom_in_and_out(plugin_map){
		
		var mapObject = plugin_map.gmap3('get');
		
		mapObject.setZoom(mapObject.getZoom() - 1);
		mapObject.setZoom(mapObject.getZoom() + 1);
		
	}
	
	function cspm_simple_clustering(plugin_map, map_id){
		
		var mapObject = plugin_map.gmap3('get');
		
		if(typeof MarkerClusterer !== 'undefined')
			var markerCluster = new MarkerClusterer(mapObject);
		
    	cspm_zoom_in_and_out(plugin_map);
							
	}
	
	/**
	 * Animate the selected marker 
	 */
	function cspm_animate_marker(plugin_map, map_id, post_id){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		plugin_map.gmap3({
			get: {
				name: 'marker',
				tag: 'post_id__'+post_id,
				callback: function(marker){
				
					if(typeof marker !== 'undefined' && marker.visible == true){
						
						var is_child = marker.is_child;	
						var marker_infobox = 'div.cspm_infobox_container[data-post-id='+post_id+'][data-map-id='+map_id+']';

						if(progress_map_vars.map_script_args[map_script_id]['markerAnimation'] == 'pulsating_circle'){
								
							var mapObject = plugin_map.gmap3('get');
	
							// Get marker position and convert it to XY
							var scale = Math.pow(2, mapObject.getZoom());
							var nw = new google.maps.LatLng(mapObject.getBounds().getNorthEast().lat(), mapObject.getBounds().getSouthWest().lng());
							var worldCoordinateNW = mapObject.getProjection().fromLatLngToPoint(nw);
							var worldCoordinate = mapObject.getProjection().fromLatLngToPoint(marker.position);
							var pixelOffset = new google.maps.Point(Math.floor((worldCoordinate.x - worldCoordinateNW.x) * scale), Math.floor((worldCoordinate.y - worldCoordinateNW.y) * scale));
							var mapposition = plugin_map.position();
		
							jQuery('div#pulsating_holder.'+map_id+'_pulsating').css({'display':'block', 'left':(pixelOffset.x + mapposition.left - 15 + 'px'), 'top':(pixelOffset.y + mapposition.top - 18 + 'px')});
							setTimeout(function(){
								jQuery('div#pulsating_holder.'+map_id+'_pulsating').css('display', 'none');
							},1500);
							
						}else if(progress_map_vars.map_script_args[map_script_id]['markerAnimation'] == 'bouncing_marker'){
						 								
							marker.setAnimation(google.maps.Animation.BOUNCE);
							setTimeout(function(){
								marker.setAnimation(null);
							},1500);
							
						}else if(progress_map_vars.map_script_args[map_script_id]['markerAnimation'] == 'flushing_infobox'){						
							
							jQuery('div.cspm_infobox_container[data-map-id='+map_id+']').removeClass('cspm_animated flash');
							setTimeout(function(){								
								jQuery(marker_infobox).addClass('cspm_animated flash');
								jQuery(marker_infobox).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
									jQuery(marker_infobox).removeClass('flash');
								});
							}, 600);
							
						}

					}
					
				}
			}
		});
	
	}

	// Zoom-in function
	function cspm_zoom_in(map_id, selector, plugin_map){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		selector.on('click', function(){        
			
			var map = plugin_map.gmap3("get");
			var current_zoom = map.getZoom();
			
			if(current_zoom < progress_map_vars.map_script_args[map_script_id]['max_zoom'])
	    		map.setZoom(current_zoom + 1);

			jQuery('div[class^=cluster_posts_widget]').removeClass('flipInX');
			jQuery('div[class^=cluster_posts_widget]').addClass('cspm_animated flipOutX');

		});
		
	}

	// Zoom-out function
	function cspm_zoom_out(map_id, selector, plugin_map){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		selector.on('click', function(){
					
			var map = plugin_map.gmap3("get");
			var current_zoom = map.getZoom();
			
			if(current_zoom > progress_map_vars.map_script_args[map_script_id]['min_zoom'])
				map.setZoom(current_zoom - 1);

			jQuery('div[class^=cluster_posts_widget]').removeClass('flipInX');
			jQuery('div[class^=cluster_posts_widget]').addClass('cspm_animated flipOutX');
			
		});
		
	}
	
//============================//
//==== Carousel functions ====//
//============================//

	/**
	 * Initialize carousel
	 *
	 * @updated 3.0
	 */
	function cspm_init_carousel(carousel_size, map_id){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		var carousel_id = map_id;

		if(progress_map_vars.map_script_args[map_script_id]['show_carousel'] == 'true' && map_layout[map_id] != 'fullscreen-map' && map_layout[map_id] != 'fit-in-map'){
			
			var vertical_value = false;	
			var dimension = (progress_map_vars.map_script_args[map_script_id]['items_view'] == 'listview') ? progress_map_vars.map_script_args[map_script_id]['horizontal_item_width'] : progress_map_vars.map_script_args[map_script_id]['vertical_item_width'];
			
			if(map_layout[map_id] == "mr-cl" || map_layout[map_id] == "ml-cr"  || map_layout[map_id] == "map-tglc-right"  || map_layout[map_id] == "map-tglc-left"){
				var vertical_value = true;
				var dimension = (progress_map_vars.map_script_args[map_script_id]['items_view'] == 'listview') ? progress_map_vars.map_script_args[map_script_id]['horizontal_item_height'] : progress_map_vars.map_script_args[map_script_id]['vertical_item_height'];
			}
		
			var size = {}; 
			var auto_scroll_option = {}; 
			
			if(progress_map_vars.map_script_args[map_script_id]['number_of_items'] != '')
				var size = { size: parseInt(progress_map_vars.map_script_args[map_script_id]['number_of_items']) };
			else if(carousel_size != null)
				var size = { size: parseInt(carousel_size) };
				
			var default_options = {
				
				scroll: parseInt(progress_map_vars.map_script_args[map_script_id]['carousel_scroll']),
				wrap: progress_map_vars.map_script_args[map_script_id]['carousel_wrap'],
				auto: parseInt(progress_map_vars.map_script_args[map_script_id]['carousel_auto']),		
				initCallback: cspm_carousel_init_callback,
				itemFallbackDimension: parseInt(dimension),
				itemLoadCallback: cspm_carousel_itemLoadCallback,
				rtl: (progress_map_vars.map_script_args[map_script_id]['carousel_mode'] == 'false') ? false : true,
				animation: progress_map_vars.map_script_args[map_script_id]['carousel_animation'],
				easing: progress_map_vars.map_script_args[map_script_id]['carousel_easing'],
				vertical: vertical_value
			
			};
			
			if(parseInt(progress_map_vars.map_script_args[map_script_id]['carousel_auto']) > 0){
				var auto_scroll_option = { 
					itemFirstInCallback:{ 
						onAfterAnimation: cspm_carousel_item_request
					}
				}
			}else var auto_scroll_option = { itemFirstInCallback: cspm_carousel_itemFirstInCallback }
		
			var carousel_options = jQuery.extend({}, default_options, size, auto_scroll_option);
			
			// Init jcarousel
			jQuery('ul#cspm_carousel_'+carousel_id).jcarousel(carousel_options);	

		}else return false;		
		
	}
	
	/**
	 * Add hover style to the first item in the carousel
	 *
	 * @updated 3.9 | 5.6.3
	 */
	function cspm_carousel_itemFirstInCallback(carousel, item, idx, state) {
		
		var map_id = jQuery(carousel.container).find('ul').attr('data-map-id'); //@edited 5.6.3 //carousel.container.context.id.split('cspm_carousel_')[1];
		
		if(state == "prev" || state == "next"){
			var item_index = item.value;
			cspm_carousel_item_hover_style('li.cspm_carousel_item[data-map-id='+map_id+'][data-index='+item_index+']', map_id);													
		}
		
		return false;
		
	}

	/**
	 * Load Items in the screenview
	 *
	 * @Updated 2.7.1 | 3.9 | 5.6.3
	 */	 
	function cspm_carousel_itemLoadCallback(carousel){
	
		var map_id = jQuery(carousel.container).find('ul').attr('data-map-id'); //@edited 5.6.3 //carousel.container.context.id.split('cspm_carousel_')[1];

		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		for(var i = parseInt(carousel.first); i <= parseInt(carousel.last); i++){
			
			var post_id = jQuery('li.jcarousel-item-'+ i +'[data-map-id='+map_id+']').attr('data-post-id');

			/**
			 * Check if the requested items already exist */
			 
			if(jQuery('li.cspm_carousel_item[data-map-id='+map_id+'][data-post-id='+post_id+']').has('div.cspm_spinner').length){
				
				/**
				 * Get items details */

				var carousel_item_content = progress_map_vars['carousel_items_'+map_script_id][post_id.split('-')[0]]; //progress_map_vars.map_script_args[map_script_id]['carousel_items'][post_id.split('-')[0]];
				
				jQuery('li.cspm_carousel_item[data-map-id='+map_id+'][data-post-id='+post_id+']').addClass('cspm_animated fadeIn').html(carousel_item_content);
				
			}
			
		}
		
	}

	// Carousel callback function
	function cspm_carousel_init_callback(carousel){
		
		var carousel_id = jQuery(carousel.container).find('ul').attr('data-map-id'); //@edited 5.6.3 //carousel.container.context.id.split('cspm_carousel_')[1];
		
		var map_id = carousel_id;
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';
			
		var target_map = (progress_map_vars.map_script_args[map_script_id]['connect_carousel_with_map'] == 'yes') ? true : false; //@since 5.0
		
		// Move the carousel with scroll wheel
		if(progress_map_vars.map_script_args[map_script_id]['scrollwheel_carousel'] == 'true'){
			
			jQuery('ul#cspm_carousel_'+carousel_id).mousewheel(function(event){ //@edited 5.6.5
					
				if(event.deltaY > 0){ //@edited 5.6.5
					carousel.prev();
					setTimeout(function(){
						cspm_carousel_item_request(carousel, 0, target_map);
					}, 600);
					return false;
				}else if(event.deltaY < 0){ //@edited 5.6.5
					carousel.next();
					setTimeout(function(){
						cspm_carousel_item_request(carousel, 0, target_map);
					}, 600);
					return false;
				}
					
			});
			
		}
		
		// Touch swipe option
		if(progress_map_vars.map_script_args[map_script_id]['touchswipe_carousel'] == 'true' && typeof jQuery('ul#cspm_carousel_'+carousel_id).swipe === 'function'){

			jQuery('ul#cspm_carousel_'+carousel_id).swipe({ 
				
				//Generic swipe handler for all directions
				swipe:function(event, direction, distance, duration, fingerCount) {

					if(map_layout[map_id] == 'mu-cd' || map_layout[map_id] == 'md-cu' || map_layout[map_id] == 'm-con' || map_layout[map_id] == 'fullscreen-map-top-carousel' || map_layout[map_id] == 'fit-in-map-top-carousel'){
						
						if(direction == 'left'){
							carousel.next();
							setTimeout(function(){
								cspm_carousel_item_request(carousel, 0, target_map);
							}, 600);
							return false;
						}else if(direction == 'right'){
							carousel.prev();
							setTimeout(function(){
								cspm_carousel_item_request(carousel, 0, target_map);
							}, 600);
							return false;
						}
						
					}else if(map_layout[map_id] == 'ml-cr' || map_layout[map_id] == 'mr-cl'){
						
						if(direction == 'up'){
							carousel.next();
							setTimeout(function(){
								cspm_carousel_item_request(carousel, 0, target_map);
							}, 600);
							return false;
						}else if(direction == 'down'){
							carousel.prev();
							setTimeout(function(){
								cspm_carousel_item_request(carousel, 0, target_map);
							}, 600);
							return false;
						}
						
					}															
					
				},
				threshold:0				
			});
			
		}
		
		// Pause autoscrolling if the user moves with the cursor over the carousel
		carousel.clip.hover(function() {
			carousel.stopAuto();
		}, function() {
			carousel.startAuto();
		});
		
		// Next button 
		carousel.buttonNext.bind('click', function(){
			setTimeout(function(){ 
				cspm_carousel_item_request(carousel, 0, target_map); 
			}, 600);
		});
		
		// Previous button
		carousel.buttonPrev.bind('click', function(){		
			setTimeout(function(){
				cspm_carousel_item_request(carousel, 0, target_map);
			}, 600);
		});
		
	}					
	
	function cspm_carousel_item_request(carousel, item_value, target_map){

		var map_id = jQuery(carousel.container).find('ul').attr('data-map-id'); //@edited 5.6.3 //carousel.container.context.id.split('cspm_carousel_')[1];
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		var plugin_map = jQuery('div#codespacing_progress_map_div_'+map_id);
		
		var firstItem = parseInt(carousel.first);
		
		var carouselItemValue = (parseInt(progress_map_vars.map_script_args[map_script_id]['carousel_auto']) > 0) ? 0 : parseInt(item_value);

		if(carouselItemValue != 0 && carouselItemValue != firstItem) 
			firstItem = carouselItemValue;

		var post_id = jQuery('.jcarousel-item-'+ firstItem).attr('data-post-id');

		if(post_id){

			cspm_carousel_item_hover_style('li.cspm_carousel_item[data-map-id='+map_id+'][data-post-id='+post_id+']', map_id);													
			
			if(target_map == true){
				
				var item_latlng = jQuery('li.cspm_carousel_item[data-map-id='+map_id+'][data-post-id='+post_id+']').attr('data-latlng');
	
				if(item_latlng && typeof item_latlng !== 'undefined'){
	
					var split_item_latlng = item_latlng.split('_');
					var this_lat = split_item_latlng[0].replace(/\"/g, '');
					var this_lng = split_item_latlng[1].replace(/\"/g, '');
									
					cspm_center_map_at_point(plugin_map, map_id, this_lat, this_lng, 'zoom');
				
				}
				
			}
			
			setTimeout(function(){
				cspm_animate_marker(plugin_map, map_id, post_id);
			}, 200);			
			
		}
				
	}

	/**
	 * Call carousel items			
	 * @edited 5.6.2					
	 */

	function cspm_call_carousel_item(carousel, id){
		
		if(typeof carousel !== 'undefined')
			carousel.scroll(jQuery.jcarousel.intval(id));
		
		return false;
		
	}
	
	/**
	 * Add different style for the first and/or the selected item in the carousel
	 */
	function cspm_carousel_item_hover_style(item_selector, map_id){								
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		jQuery('li.cspm_carousel_item[data-map-id='+map_id+']').removeClass('cspm_carousel_first_item').css({'background-color':progress_map_vars.map_script_args[map_script_id]['items_background']});
		jQuery(item_selector).addClass('cspm_carousel_first_item').css({'background-color':progress_map_vars.map_script_args[map_script_id]['items_hover_background']});	
		
	}
	
	function cspm_carousel_item_HTML(atts){

		var carousel_item = '';							
		
		if(typeof atts !== 'object')
			return carousel_item;
			
		var map_id = atts.map_id;
		var post_id = atts.post_id;
		var is_child = atts.is_child;
		var index = atts.index;
		var latLng = atts.latLng;

		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		if(progress_map_vars.map_script_args[map_script_id]['items_view'] == "listview"){ 
		
			item_width = parseInt(progress_map_vars.map_script_args[map_script_id]['horizontal_item_width']);										
			item_height = parseInt(progress_map_vars.map_script_args[map_script_id]['horizontal_item_height']);
			item_css = progress_map_vars.map_script_args[map_script_id]['horizontal_item_css'];
			items_background  = progress_map_vars.map_script_args[map_script_id]['items_background'];
		
		}else if(progress_map_vars.map_script_args[map_script_id]['items_view'] == "gridview"){ 
		
			item_width = parseInt(progress_map_vars.map_script_args[map_script_id]['vertical_item_width']);
			item_height = parseInt(progress_map_vars.map_script_args[map_script_id]['vertical_item_height']);
			item_css = progress_map_vars.map_script_args[map_script_id]['vertical_item_css'];
			items_background  = progress_map_vars.map_script_args[map_script_id]['items_background'];
			
		}

		carousel_item = '<li class="cspm_carousel_item cspm_border_radius cspm_border_shadow" value="'+index+'" data-map-id="'+map_id+'" data-post-id="'+post_id+'" data-is-child="'+is_child+'" data-index="'+index+'" data-latlng="'+latLng+'" style="width:'+item_width+'px; height:'+item_height+'px; background-color:'+items_background+'; '+item_css+'">';
			carousel_item += '<div class="cspm_spinner cspm_border_top_before_hex"></div>';
		carousel_item += '</li>';
		
		return carousel_item;

	}
	
	function cspm_rewrite_carousel(map_id, show_carousel, posts_to_retrieve){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		if(show_carousel == "yes" && progress_map_vars.map_script_args[map_script_id]['show_carousel'] == "true" && map_layout[map_id] != 'fullscreen-map' && map_layout[map_id] != 'fit-in-map'){
	
			var carousel = jQuery('ul#cspm_carousel_'+map_id).data('jcarousel');
	
			if(typeof carousel !== 'undefined'){
				
				carousel.reset();
				
				var carousel_length = cspm_object_size(posts_to_retrieve);
	
				var count_loop = 0;
							
				for(var c = 0; c < carousel_length; c++){
					
					var post_id = posts_to_retrieve[c];
					var is_child = post_ids_and_child_status[map_id][post_lat_lng_coords[map_id][post_id]];
					var index = (c+1);

					var carousel_item = cspm_carousel_item_HTML({
						map_id: map_id,
						post_id: post_id,
						is_child: is_child,
						index: index,
						latLng: post_lat_lng_coords[map_id][post_id],
					});							

					carousel.add(index, carousel_item);
					
					count_loop++;
					
				}					
	
				cspm_init_carousel(carousel_length, map_id);
				
				return count_loop++;

			}
			
		}else return posts_to_retrieve.length;

	}

	function cspm_fullscreen_map(map_id){
		
		var screenWidth = window.innerWidth;
		var screenHeight = window.innerHeight;

		jQuery('div.codespacing_progress_map_area[data-map-id='+map_id+']').css({height : screenHeight, width : screenWidth});
	
	}
		
	function cspm_carousel_width(map_id){
		
		var carouselWidth = jQuery('div#codespacing_progress_map_div_'+map_id).width();
		
		carouselWidth = parseInt(carouselWidth - 40);

		var carouselHalf = parseInt((-0) - (carouselWidth/ 2));

		jQuery('div.cspm_carousel_on_top[data-map-id='+map_id+']').css({width : carouselWidth, 'margin-left' : carouselHalf+'px'});
	
	}

	function cspm_fitIn_map(map_id){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		var parentHeight = jQuery('div.codespacing_progress_map_area[data-map-id='+map_id+']').parent().height();

		if(parentHeight == 0) parentHeight = progress_map_vars.map_script_args[map_script_id]['layout_fixed_height'];

		jQuery('div.codespacing_progress_map_area[data-map-id='+map_id+']').css({height : parentHeight});
	
	}

	/**
	 * Returns the distance, in meters, between two LatLngs.
	 */
	function cspm_compute_distance_between(origin_lat, origin_lng, destination_lat, destination_lng){

		var from = new google.maps.LatLng(origin_lat, origin_lng);
		var to = new google.maps.LatLng(destination_lat, destination_lng);
		
		var distance = google.maps.geometry.spherical.computeDistanceBetween(from, to);
		
		return distance;
		
	}
	
	/**
	 * Display user position on the map
	 *
	 * @since 2.8
	 * @updated 3.2 [added retina support for user marker icon]
	 * @updated 4.0
	 */
	function cspm_geolocate(plugin_map, map_id, show_user, user_marker_icon, marker_size, user_circle, zoom, show_error){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		var mapObject = plugin_map.gmap3("get");
		var current_zoom = mapObject.getZoom();		

		// Marker image size with Retina support
		var marker_img_width = (progress_map_vars.map_script_args[map_script_id]['retinaSupport'] == "true" && !cspm_is_svg(user_marker_icon)) ? parseInt(marker_size.split('x')[0])/2 : parseInt(marker_size.split('x')[0]);
		var marker_img_height = (progress_map_vars.map_script_args[map_script_id]['retinaSupport'] == "true" && !cspm_is_svg(user_marker_icon)) ? parseInt(marker_size.split('x')[1])/2 : parseInt(marker_size.split('x')[1]);

		// Marker image anchor point
		var anchor_y = marker_img_height/2;
		var anchor_x = marker_img_width/2;	
		var anchor_point = null;

		anchor_point = new google.maps.Point(anchor_x, anchor_y);
		
		/**
		 * Marker icon */
		
		marker_icon = {
			url: user_marker_icon,
		}
		
		if(marker_img_width > 0 && marker_img_height > 0){
			marker_icon = jQuery.extend({}, marker_icon, {					
				anchor: anchor_point,
				scaledSize: new google.maps.Size(marker_img_width, marker_img_height)
			});
		}
		
		/**
		 * Geolocate */
		 
		plugin_map.gmap3({
			getgeoloc:{
				callback: function(latLng){
				  
				  if(latLng){
					  						
					plugin_map.gmap3({						  
					  map:{
						options:{
							center: latLng,
							zoom: parseInt(zoom)
						},
						onces: {
							tilesloaded: function(map){
								
								/**
								 * Add a marker indicating the user location */
								
								if(show_user){

									setTimeout(function(){

										plugin_map.gmap3({
											marker:{
												latLng: latLng,
												tag: ['cspm_user_marker_'+map_id, 'to_cluster'],
												options:{
													icon: marker_icon,
													post_id: 'user_location',
													id: 'user_location',													
												},
												events:{
													click: function(marker, event, elements){
														
														/**
														 * Nearby points of interets.
														 * Add the coordinates of this marker to the list of Proximities ...
														 * ... in order to use them (latLng) to display nearby points of interest ...
														 * ... of that marker 
														 * @since 3.2 */
														 
														if(progress_map_vars.map_script_args[map_script_id]['nearby_places_option'] == 'true'){
														
															jQuery('select.cspm_proximities_list[data-map-id='+map_id+'] option.cspm_proximity_name[data-map-id='+map_id+']').attr('data-marker-latlng', marker.position).attr('data-marker-post-id', 'user_location'); //@since 5.6.3 || Fix an issue when selecting a marker before opening the proximities list. "Tail.select" inherit all select options attributes!!							
                                                            jQuery('li.cspm_proximity_name[data-map-id='+map_id+']').attr('data-marker-latlng', marker.position).attr('data-marker-post-id', 'user_location').removeClass('selected');	
															
															cspm_popup_msg(map_id, 'success', '', progress_map_vars.new_marker_selected_msg);
																														
														}
													}
												},																																																		
											},											
											circle:{
												options:{
													center: latLng,
													radius : parseInt(user_circle*1000),
													fillColor : progress_map_vars.map_script_args[map_script_id]['user_circle_fillColor'],
													fillOpacity: progress_map_vars.map_script_args[map_script_id]['user_circle_fillOpacity'],
													strokeColor : progress_map_vars.map_script_args[map_script_id]['user_circle_strokeColor'],
													strokeOpacity: progress_map_vars.map_script_args[map_script_id]['user_circle_strokeOpacity'],
													strokeWeight: parseInt(progress_map_vars.map_script_args[map_script_id]['user_circle_strokeWeight']),
													editable: false,
												},
											}
										});
										
									}, 500);
								
								}	
								
							}
						}
					  }
					});																		
				  
				  }else if(show_error){
					
					/**
					 * More info at https://support.google.com/maps/answer/152197
					 * Deprecate Geolocation API: https://developers.google.com/web/updates/2016/04/geolocation-on-secure-contexts-only */
						
					var title = progress_map_vars.geoErrorTitle;
					var message = progress_map_vars.geoErrorMsg;
					
					if(!cspm_popup_msg(map_id, 'error', title, '<br />' + message))
						alert(title + '\n\n' + message);
					
					console.log('PROGRESS MAP: ' + progress_map_vars.geoDeprecateMsg);
					  
				  }
				  
				}
			},							
		});
	}
	
	/**
	 * Get User marker on the map
	 *
	 * @since 2.8
	 */
	function cspm_get_user_marker(plugin_map, map_id){

		plugin_map.gmap3({
		  get: {
			name: 'marker',
			tag: 'cspm_user_marker_'+map_id,
			callback: function(objs){
				if(objs)
				  return objs;
			}
		  }
		});

	}
	
	/**
	 * This will detect if the Street View panorama is activated
	 * @since 2.7
	 */
	function cspm_is_panorama_active(plugin_map){
		
		var mapObject = plugin_map.gmap3("get");
		
		if(typeof mapObject.getStreetView === "function"){
			
			var streetView = mapObject.getStreetView();

			return streetView.getVisible();
			
		}else return false;
								
	}

	function cspm_center_map_at_point(plugin_map, map_id, latitude, longitude, effect){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		var mapObject = plugin_map.gmap3("get");
		
		if(typeof mapObject !== 'undefined'){
			
			var current_zoom = (typeof mapObject.getZoom === 'function') ? mapObject.getZoom() : 12;
			var latLng = new google.maps.LatLng(latitude, longitude);
			
			if(typeof progress_map_vars.map_script_args[map_script_id]['carousel_map_zoom'] !== 'undefined'){
				var carousel_map_zoom = progress_map_vars.map_script_args[map_script_id]['carousel_map_zoom'];
			}else var carousel_map_zoom = current_zoom;
			
			if(effect == 'zoom' && current_zoom != parseInt(carousel_map_zoom))
				mapObject.setZoom(parseInt(carousel_map_zoom));
			else if(effect == 'resize')
				google.maps.event.trigger(mapObject, 'resize');

			setTimeout(function(){
				if(typeof mapObject.panTo === 'function')
					mapObject.panTo(latLng);
			}, 200);
			
		}
			
	}

	function cspm_map_bounds_contains_marker(plugin_map, latitude, longitude){
		
		var mapObject = plugin_map.gmap3('get');
		
		if(typeof mapObject === 'undefined')
			return false;
			
		var myLatlng = new google.maps.LatLng(latitude, longitude);
		
		if(typeof mapObject.getBounds === 'function'){
			return mapObject.getBounds().contains(myLatlng);		
		}else return false;
		
	}
	
	/**
	 * Computes whether the given point lies inside the specified polygon
	 */
	function cspm_polygon_contains_marker(polygon_paths, marker_lat, marker_lng){

		var marker_latLng = new google.maps.LatLng(marker_lat, marker_lng);
		
		return google.maps.geometry.poly.containsLocation(marker_latLng, polygon_paths);

	}
	
	var cspm_infoboxes = {};
	
	/**
	 * Set infobox settings
	 *
	 * @updated 5.3
	 */
	function cspm_infobox(plugin_map, map_id, marker, infobox_options){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		var post_id = parseInt(marker.post_id);
		var icon_height = (typeof marker.icon === 'undefined' || typeof marker.icon.size === 'undefined' || typeof marker.icon.size.height === 'undefined') ? 38 : marker.icon.size.height;

		var infobox_type = infobox_options.type;
		var infobox_display_event = infobox_options.display_event;
		var infobox_external_link = infobox_options.link_target;
		var remove_infobox_on_mouseout = infobox_options.remove_on_mouseout;
		var show_close_btn = infobox_options.show_close_btn; //@since 5.3
		
		var map_type = infobox_options.map_type; //@since 5.3
		
		var panOnOpen = (infobox_display_event == 'onload' || infobox_display_event == 'onzoom') ? false : true; //@since 5.3
			if(map_type == 'light_map') panOnOpen = true; //@since 5.3
			
		return new SnazzyInfoWindow({
			map: plugin_map.gmap3('get'),
			marker: marker,
			content: progress_map_vars['infoboxes_'+map_id][post_id],
			wrapperClass: 'cspm_marker_infobox cspm_marker_overlay',
			panOnOpen: panOnOpen, //@edited 5.3
			closeOnMapClick: (infobox_display_event != 'onload' && infobox_display_event != 'onzoom') ? true : false, //@edited 5.3
			openOnMarkerMouseOver: (infobox_display_event == 'onhover') ? true : false,
			closeOnMarkerMouseOut: ((infobox_display_event != 'onload' && infobox_display_event != 'onzoom') && remove_infobox_on_mouseout == 'true') ? true : false, //@edited 5.3
			openOnMarkerClick: (infobox_display_event == 'onclick') ? true : false,
			closeWhenOthersOpen: (infobox_display_event != 'onload' && infobox_display_event != 'onzoom') ? true : false, //@edited 5.3
			showCloseButton: (show_close_btn == 'true') ? true : false, //@edited 5.3
			pointer: '10px',
			borderRadius: (infobox_type == 'rounded_bubble') ? '50%' : '2px',
			backgroundColor: 'transparent',
			padding: '0px',
			border: false,
			offset: {
				top: '-'+(icon_height+2)+'px',
			},
			shadow: {
				h: '0px',
				v: '1px',
				blur: '4px',
				spread: '-1px',
				opacity: 0.2,
				color: '#000'
			},
			callbacks: {				
                open: function(){
					
					/**
					 * Fix an issue with the maps where it wasn't possible to open the single post modal.
					 * @since 5.6.3 */
					
                    jQuery('a.cspm_popup_single_post').on('click', function(e){ // Don't use $(document).on(event, handler, ... !!!
                        e.preventDefault(); 
                        var iframe_url = jQuery(this).attr('href');
                        if(typeof iframe_url !== 'undefined' && iframe_url != ''){
                            cspm_open_single_post_modal(iframe_url);		
                        }	
                    });	
                    
                    /**
                     * Fix an issue with the maps where it wasn't possible to open the "Nearby places" map inside a modal
                     * @since 5.6.3 */

                    jQuery('a.cspm_popup_nearby_palces_map').on('click', function(e){ // Don't use $(document).on(event, handler, ... !!!
                        e.preventDefault();			            
                        var iframe_url = progress_map_vars.nearby_places_page_url;
                        var post_id = jQuery(this).attr('data-post-id');
                        if(typeof iframe_url !== 'undefined' && iframe_url != '' && typeof post_id !== 'undefined' && post_id != ''){
                            cspm_open_single_post_modal(iframe_url+'?post_id='+post_id);		
                        }
                    });	
			
				},
                afterOpen: function(){
					
					/**
					 * Fix an issue with "Single post" maps where it wasn't possible to ...
					 *... open the modal.
					 * @since 4.6
					 * @edited 5.3 [fixed and issue where some infoboxes open the link in a new window/tab] */
					
					jQuery('body').remove('#cspm_single_post_modal'); //@since 5.3
					 
					setTimeout(function(){
						if(jQuery('#cspm_single_post_modal').length == 0){ 
							jQuery('body').append('<div id="cspm_single_post_modal"></div>');
						}	
					}, 100);
                    
                },                
				beforeClose: function(){
					
					/**
					 * Make sure not to close the infobox when opening the marker menu.
					 * This will fix a conflict between multiple SnazzyInfowindow instances regarding the option "closeWhenOthersOpen".
					 * When [@cspm_marker_menus_status] is empty or equal to "closed", this means a marker menu will be rendred on the map, ...
					 * ... therfore, we can use this information to ignore "closeWhenOthersOpen"!
					 * @since 5.5 */

					if(typeof cspm_marker_menus_status[map_id] !== 'undefined' && cspm_marker_menus_status[map_id] == 'opened'){
						return false;
					}
					
				}
			}
		});	
	
	}
	
	function cspm_draw_multiple_infoboxes(plugin_map, map_id, infobox_options){

		var r = jQuery.Deferred();
		
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		var infobox_display_event = infobox_options.display_event; //@since 5.3

		plugin_map.gmap3({
			get: {
				name: 'marker',
				tag: 'to_cluster',
				all:  true,
				callback: function(objs) {				

					for(var i = 0; i < objs.length; i++){	
						
						var id = objs[i].id;
						var this_marker_object = objs[i];														
						var post_id = parseInt(objs[i].post_id);

						if(post_id != '' && post_id != 'user_location' && !isNaN(post_id)){
													
							var latLng = objs[i].position;
							
							// Convert the LatLng object to array
							var lat = latLng.lat();
							var lng = latLng.lng();
							
							/**
							 * Load infobox on specific zoom level 
							 * @since 5.3 */
							 
							var load_by_reaching_zoom_level = false; //@since 5.3
							if(infobox_display_event == 'onzoom' && typeof infobox_options.display_zoom_level !== 'undefined'){
								var mapObject = plugin_map.gmap3('get');
								var current_zoom = mapObject.getZoom();								
								if(current_zoom >= infobox_options.display_zoom_level)
									load_by_reaching_zoom_level = true;
							} //@since 5.3
							
							// ==========================

							// if the marker is within the viewport of the map
							if(	cspm_map_bounds_contains_marker(plugin_map, lat, lng) 
								&& objs[i].getMap() != null 
								&& objs[i].visible == true
								&& (infobox_display_event == 'onload' || (infobox_display_event == 'onzoom' && load_by_reaching_zoom_level)) //@since 5.3
							){

								if(typeof cspm_infoboxes[map_id][id] === 'undefined'){
									cspm_infoboxes[map_id][id] = cspm_infobox(plugin_map, map_id, this_marker_object, infobox_options);
									cspm_infoboxes[map_id][id].open();
								}else{
									if(!cspm_infoboxes[map_id][id].isOpen())
										cspm_infoboxes[map_id][id].open();
								}
								
							}else{
									
								if(typeof cspm_infoboxes[map_id][id] === 'object' && cspm_infoboxes[map_id][id].isOpen())
									cspm_infoboxes[map_id][id].close();			
									
							}
							
						}
						
					}
				
					r.resolve(); 
																												
				}
			}
		});
		
		return r;
		
	}

    function cspm_draw_single_infobox(plugin_map, map_id, marker_obj, infobox_options){

		var r = jQuery.Deferred();

		var id = marker_obj.id;
		var post_id = parseInt(marker_obj.post_id);
				
		if(typeof cspm_infoboxes[map_id][id] === 'undefined'){
			
			cspm_infoboxes[map_id][id] = cspm_infobox(plugin_map, map_id, marker_obj, infobox_options);
			cspm_infoboxes[map_id][id].open();
				
			r.resolve(); 
																												
		}
		
		return r;

	}

	function cspm_connect_carousel_and_infobox(){
		
		setTimeout(function(){
			jQuery('div.cspm_infobox_container[data-move-carousel=true] div.cspm_infobox_content_container').on('mouseenter', function(){
				var map_id = jQuery(this).attr('data-map-id');
				var post_id = jQuery(this).attr('data-post-id');
				var carousel = jQuery(this).attr('data-show-carousel');
				var item_value = jQuery('li.cspm_carousel_item[data-map-id='+map_id+'][data-post-id='+post_id+']').attr('jcarouselindex'); //@edited 5.0
				if(carousel == 'true'){
					cspm_call_carousel_item(jQuery('ul#cspm_carousel_'+map_id).data('jcarousel'), item_value);			
				}
			});
		}, 1000);
		
	}

	/**
	 * Marker Popups
	 * @since 4.0 */
	 
	var cspm_marker_popups = {};

	function cspm_popup(plugin_map, map_id, marker, popup_options){
		
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';
	
		var post_id = parseInt(marker.post_id);
		var icon_height = (typeof marker.icon === 'undefined' || typeof marker.icon.size === 'undefined' || typeof marker.icon.size.height === 'undefined') ? 38 : marker.icon.size.height;
		var icon_width = (typeof marker.icon === 'undefined' || typeof marker.icon.size === 'undefined' || typeof marker.icon.size.width === 'undefined') ? 35 : marker.icon.size.width;
		
		var placement = popup_options.placement.toString();
		var pointer = (placement == 'left' || placement == 'right') ? '7px' : '5px';
		var offsetTop = '0px';
		
		if(placement == 'left' || placement == 'right'){
			offsetTop = '-'+(icon_height/2)+'px';
		}else if(placement == 'top'){
			offsetTop = '-'+(icon_height-7)+'px';
		}
		
		var content = '<div class="cspm_marker_popup_content" style="font-weight:'+popup_options.fontWeight+'">' + 
			progress_map_vars['marker_popups_'+map_id][post_id] + 
		'</div>';
		
		return new SnazzyInfoWindow({
			map: plugin_map.gmap3('get'),
			marker: marker,
			content: content,
			wrapperClass: 'cspm_marker_popup cspm_marker_overlay',
			panOnOpen: false,
			closeOnMapClick: false,
			openOnMarkerMouseOver: true,
			closeOnMarkerMouseOut: false,
			openOnMarkerClick: true,
			closeWhenOthersOpen: false,
			showCloseButton: false,
			pointer: pointer,
			borderRadius: '2px',
			backgroundColor: popup_options.bgColor,
			fontColor: popup_options.color,
			fontSize: popup_options.fontSize,
			padding: '5px 7px',
			border: false,
			placement: placement,
			offset: {
				top: offsetTop,
			},
			shadow: {
				h: '3px',
				v: '3px',
				blur: '0px',
				spread: '-1px',
				opacity: 0.15,
				color: '#000'
			},
		});	
	
	}
	
	function cspm_draw_multiple_popups(plugin_map, map_id, infobox_options){
	
		var r = jQuery.Deferred();
		
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';
	
		plugin_map.gmap3({
			get: {
				name: 'marker',
				tag: 'to_cluster',
				all:  true,
				callback: function(objs) {				
	
					for(var i = 0; i < objs.length; i++){	
						
						var id = objs[i].id;
						var this_marker_object = objs[i];														
						var post_id = parseInt(objs[i].post_id);
	
						if(post_id != '' && post_id != 'user_location' && !isNaN(post_id) && progress_map_vars['marker_popups_'+map_id][post_id] != ''){
													
							var latLng = objs[i].position;
							
							// Convert the LatLng object to array
							var lat = latLng.lat();
							var lng = latLng.lng();
	
							// if the marker is within the viewport of the map
							if(cspm_map_bounds_contains_marker(plugin_map, lat, lng) && objs[i].getMap() != null && objs[i].visible == true){
	
								if(typeof cspm_marker_popups[map_id][id] === 'undefined'){
									cspm_marker_popups[map_id][id] = cspm_popup(plugin_map, map_id, this_marker_object, infobox_options);
									cspm_marker_popups[map_id][id].open();
								}else{
									if(!cspm_marker_popups[map_id][id].isOpen())
										cspm_marker_popups[map_id][id].open();
								}
								
							}else{
									
								if(typeof cspm_marker_popups[map_id][id] === 'object' && cspm_marker_popups[map_id][id].isOpen())
									cspm_marker_popups[map_id][id].close();			
									
							}
							
						}
						
					}
				
					r.resolve(); 
																												
				}
			}
		});
		
		return r;
		
	}
	
	/****** End of Marker popups ******/


	/**
	 * Marker Menu
	 * @since 5.5 */
	
	var cspm_marker_menus = {};
	var cspm_marker_menus_status = {};
	
	function cspm_marker_menu(plugin_map, map_id, marker, menu_options){
		
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';
	
		var post_id = parseInt(marker.post_id);
		var icon_height = (typeof marker.icon === 'undefined' || typeof marker.icon.size === 'undefined' || typeof marker.icon.size.height === 'undefined') ? 38 : marker.icon.size.height;
		var icon_width = (typeof marker.icon === 'undefined' || typeof marker.icon.size === 'undefined' || typeof marker.icon.size.width === 'undefined') ? 35 : marker.icon.size.width;
		
		var placement = menu_options.placement.toString();
		var pointer = (placement == 'left' || placement == 'right') ? '7px' : '5px';
		var offsetTop = '0px';
		
		if(placement == 'left' || placement == 'right'){
			offsetTop = '-'+(icon_height/2)+'px';
		}else if(placement == 'top'){
			offsetTop = '-'+(icon_height-7)+'px';
		}
		
		var content = '<div class="cspm_marker_menu_content" style="font-weight:'+menu_options.fontWeight+'">' + 
			progress_map_vars['marker_menus_'+map_id][post_id] + 
		'</div>';
		
		return cspm_marker_menus[map_id] = new SnazzyInfoWindow({
			map: plugin_map.gmap3('get'),
			marker: marker,
			post_id: post_id,
			content: content,
			wrapperClass: 'cspm_marker_menu cspm_marker_overlay',
			//maxWidth: '50',
			//maxHeight: '50',
			panOnOpen: true,
			edgeOffset: {
			  top: 50,
			  right: 50,
			  bottom: 50,
			  left: 50
			},
			closeOnMapClick: (menu_options.close_map_click == 'yes') ? true : false,
			openOnMarkerMouseOver: (menu_options.mouse_enter == 'yes') ? true : false,
			closeOnMarkerMouseOut: (menu_options.mouse_out == 'yes') ? true : false,
			openOnMarkerClick: false,
			closeWhenOthersOpen: true,
			showCloseButton: (menu_options.close_btn == 'yes') ? true : false,
			pointer: pointer,
			borderRadius: '2px',
			backgroundColor: menu_options.bgColor,
			fontColor: menu_options.color,
			fontSize: menu_options.fontSize,
			padding: '10px 17px',
			border: false,
			placement: placement,
			offset: {
				top: offsetTop,
			},
			shadow: {
				h: '3px',
				v: '3px',
				blur: '0px',
				spread: '-1px',
				opacity: 0.15,
				color: '#000'
			},
			callbacks: {
                open: function(){
					
					/**
					 * Fix an issue with the maps where it wasn't possible to open the single post modal.
					 * @since 5.6.3 */
					
                    jQuery('a.cspm_popup_single_post').on('click', function(e){ // Don't use $(document).on(event, handler, ... !!!
                        e.preventDefault(); 
                        var iframe_url = jQuery(this).attr('href');
                        if(typeof iframe_url !== 'undefined' && iframe_url != ''){
                            cspm_open_single_post_modal(iframe_url);		
                        }	
                    });	
                    
                    /**
                     * Fix an issue with the maps where it wasn't possible to open the "Nearby places" map inside a modal
                     * @since 5.6.3 */

                    jQuery('a.cspm_popup_nearby_palces_map').on('click', function(e){ // Don't use $(document).on(event, handler, ... !!!
                        e.preventDefault();			            
                        var iframe_url = progress_map_vars.nearby_places_page_url;
                        var post_id = jQuery(this).attr('data-post-id');
                        if(typeof iframe_url !== 'undefined' && iframe_url != '' && typeof post_id !== 'undefined' && post_id != ''){
                            cspm_open_single_post_modal(iframe_url+'?post_id='+post_id);		
                        }
                    });	
			
				},
				afterOpen: function(){
					
					/**
					 * The marker menu status well help us to prevent closing other SnazzyInfowindow instances.
					 * Other SI instances can use the status in the "beforeClose" callback */
					 
					cspm_marker_menus_status[map_id] = 'opened';
					setTimeout(function(){
						cspm_marker_menus_status[map_id] = '';
					}, 100);
			
					/**
					 * Fix an issue with "Single post" maps where it wasn't possible to ...
					 *... open the modal.
					 * @since 4.6
					 * @edited 5.3 [fixed and issue where some marker menus open the link in a new window/tab] */
					
					jQuery('body').remove('#cspm_single_post_modal'); //@since 5.3
					 
					setTimeout(function(){
						if(jQuery('#cspm_single_post_modal').length == 0){ 
							jQuery('body').append('<div id="cspm_single_post_modal"></div>');
						}	
					}, 100);
			
					/**
					 * Nearby points of interest 
					 * @since 5.5 */
					 
					if(menu_options.proximities == 'true'){
			
						var latLng = marker.position;
						var post_id = marker.post_id;
			
						jQuery('a.cspm_prepare_for_proximity').click(function(e){ // Don't use $(document).on(event, handler, ... !!!
							e.preventDefault();
				            jQuery('select.cspm_proximities_list[data-map-id='+map_id+'] option.cspm_proximity_name[data-map-id='+map_id+']').attr('data-marker-latlng', latLng).attr('data-marker-post-id', post_id); //@since 5.6.3 || Fix an issue when selecting a marker before opening the proximities list. "Tail.select" inherit all select options attributes!!							
                            jQuery('li.cspm_proximity_name[data-map-id='+map_id+']').attr('data-marker-latlng', latLng).attr('data-marker-post-id', post_id).removeClass('selected');																
							cspm_popup_msg(map_id, 'success', '', menu_options.proximities_msg);
							if(jQuery('div.cspm_proximities_container_'+map_id).is(':hidden')){
								jQuery('div.cspm_proximities_btn[data-map-id='+map_id+']').trigger('click');
							}
							if(typeof cspm_marker_menus[map_id] === 'object'){
								cspm_marker_menus[map_id].close(); // Close the marker menu
							}
						});
						
					}
					
					/**
					 * Open the post/location media modal
					 * @since 5.5 */
						
					marker.media['post_id'] = marker.post_id; // Add post ID to media array | @since 4.6													
		
					jQuery('a.cspm_popup_marker_media').click(function(e){ // Don't use $(document).on(event, handler, ... !!!						
                        e.preventDefault();
						if(typeof marker.media !== 'undefined' && typeof marker.media.format !== 'undefined'){
							if(marker.media.format != 'link' && marker.media.format != 'nearby_places'){
                                cspm_open_media_modal(marker.media, map_id, 'yes');
							}else return false;
						}
					});	
					
					if(jQuery.inArray(marker.media.format, ['link', 'nearby_places']) > -1 
					|| cspm_is_media_empty(marker.media, map_id)
					){
						jQuery('a.cspm_popup_marker_media').parent('.cspm_marker_menu_item').hide();	
					} // hide the media modal link if the post format is not a media type or media files are empty!
					
					/**
					 * Open the directions URL
					 * @since 5.5 */
						
					jQuery('a.cspm_open_directions').click(function(e){ // Don't use $(document).on(event, handler, ... !!!
						e.preventDefault();				
						if(typeof marker.position !== 'undefined'){
							var travel_mode = jQuery(this).attr('data-travel-mode');
							var directions_url = 'https://www.google.com/maps/dir/?api=1&origin=&destination='+marker.position.lat()+','+marker.position.lng()+'&travelmode='+travel_mode;									
							window.open(directions_url);
						}else return false;
					});			
										
				},				
				afterClose: function(){
					if(typeof cspm_marker_menus[map_id] === 'object'){
						cspm_marker_menus[map_id].destroy();
						cspm_marker_menus[map_id] = '';
						cspm_marker_menus_status[map_id] = 'closed';
					}
				}
			}
		});	
	
	}
	
	function cspm_open_marker_menu(plugin_map, map_id, marker, menu_options){
		
		if(typeof cspm_marker_menus !== 'undefined'){
			
			if(typeof cspm_marker_menus[map_id] === 'object'){
				cspm_marker_menus[map_id].close(); // Close other marker menus
			} //@since 5.6.2
							
			/**
			 * Open marker menu 
			 * @since 5.5 */
			
			cspm_marker_menus[map_id] = cspm_marker_menu(plugin_map, map_id, marker, menu_options);
			cspm_marker_menus[map_id].open();
		
		}
	
	}
	
	/****** End of Marker Menu ******/
	
	/** 
	 * Count the number of visible markers in the map
	 * @since 2.5
	 */
	function cspm_nbr_of_visible_markers(plugin_map){
		
		var count_posts = 0;
		
		plugin_map.gmap3({
			get: {
				name: 'marker',
				tag: 'to_cluster',
				all:  true,
				callback: function(objs) {				
					for(var i = 0; i < objs.length; i++){	
						if(objs[i].visible == true){
							count_posts++;
						}
					}
				}
			}
		});		
		
		return count_posts;
		
	}
	
	function cspm_set_markers_visibility(plugin_map, map_id, value, j, post_ids_and_categories, posts_to_retrieve, retrieve_posts){

		if(retrieve_posts == true){
			
			// @value: Refers to the category ID
			if(value != null){
				// Show markers comparing with the category ID (faceted search case)
				plugin_map.gmap3({
					get: {
						name: 'marker',
						tag: 'to_cluster',
						all: true,
						callback: function(objs){
							
							if(objs){
								
								jQuery.each(objs, function(i, obj){

									if(typeof obj.post_id !== 'undefined' && obj.post_id != 'user_location' && jQuery.inArray(value, post_ids_and_categories['post_id_'+obj.post_id][0]) > -1){
									
										var obj_post_id = parseInt(obj.post_id);
									
										if(typeof obj.setVisible === 'function'){
											obj.setVisible(true);
											cspm_toggle_marker_connected_layers(plugin_map, map_id, obj, 'show');
										}
										
										if(jQuery.inArray(parseInt(obj.post_id), posts_to_retrieve) === -1){
											posts_to_retrieve[j] = parseInt(obj.post_id);
											j++;
										}	
											
									}
									
									/**
									 * Show the user's marker for GeoTargeting
									 * @since 2.7.4 */
									 
									if(obj.post_id == 'user_location' && typeof obj.setVisible === 'function')
										obj.setVisible(true);
									
								});
								
							}
				
						}
					}
				});
				
			}else{
				
				/**
				 * Show markers within the search area/radius (Search form case) */
				 
				plugin_map.gmap3({
					get: {
						name: "marker",
						tag: 'to_cluster',
						all: true,
						callback: function(objs){
							
							if(objs){
								
								jQuery.each(objs, function(i, obj){
									
									if(typeof obj.post_id !== 'undefined'){
										
										var obj_post_id = parseInt(obj.post_id);
									
										if(jQuery.inArray(obj_post_id, posts_to_retrieve) > -1){
										
											if(typeof obj.setVisible === 'function'){
												obj.setVisible(true);												
												cspm_toggle_marker_connected_layers(plugin_map, map_id, obj, 'show');
											}
											
										}
											
									}
									
								});
								
							}
							
						}
					}
				});
			
			}
		
		/**
		 * Show all markers */
		 
		}else{
			
			plugin_map.gmap3({
				get: {
					name: "marker",
					tag: 'to_cluster',
					all: true,
					callback: function(objs){
						
						if(objs){
							
							jQuery.each(objs, function(i, obj){
								
								if(typeof obj.post_id !== 'undefined'){
									
									var obj_post_id = parseInt(obj.post_id);
									
									if(typeof obj.setVisible === 'function'){ 
										obj.setVisible(true);
										cspm_toggle_marker_connected_layers(plugin_map, map_id, obj, 'show');
									}
									
									if(typeof obj.post_id !== 'undefined' && obj.post_id != 'user_location')										
										posts_to_retrieve[j] = parseInt(obj.post_id);
										
									j++;
									
								}
								
							});
							
						}
						
					}
				}
			});

		}
		
		return posts_to_retrieve;
		
	}

	/**
	 * Hide all visible markers on the map
	 *
	 * @since 2.5
	 */
	function cspm_hide_all_markers(plugin_map, map_id){
		
		var r = jQuery.Deferred();
		
		plugin_map.gmap3({
			get: {
				name: 'marker',	
				tag: 'to_cluster',			
				all: true,	
				callback: function(objs){
					
					jQuery.each(objs, function(i, obj){
						if(typeof obj.setVisible === 'function' && obj.post_id != 'user_location'){
							obj.setVisible(false);							
							cspm_toggle_marker_connected_layers(plugin_map, map_id, obj, 'hide');
						}
					});

					r.resolve();
				}
			}
		});
				
		return r;
		
	}
	
	/**
	 * Show all visible markers in the map.
	 * Note: Make sure to display only markers that are currently available on the map ...
	 * ... and exclude markers hidden by a filter and/or search request!
	 *
	 * @since 3.7
	 */
	function cspm_show_all_markers(plugin_map, map_id){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		var r = jQuery.Deferred();
		
		plugin_map.gmap3({
			get: {
				name: 'marker',	
				tag: 'to_cluster',			
				all: true,	
				callback: function(objs){
					jQuery.each(objs, function(i, obj){
						if(typeof obj.setVisible === 'function' && obj.post_id != 'user_location'){
							
							/**
							 * Show all markers and exclude those hidden by a filter/search request */
							 
							if(typeof cspm_global_object.posts_to_retrieve[map_id] !== 'undefined' && jQuery.inArray(parseInt(obj.post_id), cspm_global_object.posts_to_retrieve[map_id]) !== -1){
								obj.setVisible(true);
								cspm_toggle_marker_connected_layers(plugin_map, map_id, obj, 'show');
							
							/**
							 * Show all markers but only if it isn't a search map type! */
							 
							}else if(typeof cspm_global_object.posts_to_retrieve[map_id] === 'undefined' && progress_map_vars.map_script_args[map_script_id]['map_type'] != 'search_map'){
								obj.setVisible(true);	
								cspm_toggle_marker_connected_layers(plugin_map, map_id, obj, 'show');							
							}
							
						}
					});
			
					r.resolve();
				}
			}
		});
				
		return r;
		
	}
	
	/**
	 * Return locations on the current viewport of the map
	 *
	 * @since 5.0 
	 */
	function cspm_get_markers_on_viewport(plugin_map, map_id){

		var markers_on_viewport = [];
		
		plugin_map.gmap3({
			get: {
				name: 'marker',	
				tag: 'to_cluster',			
				all: true,	
				callback: function(objs){
					jQuery.each(objs, function(i, obj){
						if(typeof obj.setVisible === 'function' && obj.visible == true && obj.post_id != 'user_location'){
							if(cspm_map_bounds_contains_marker(plugin_map, obj.position.lat(), obj.position.lng())){
								markers_on_viewport.push(obj.post_id);
							}
						}
					});
				}
			}
		});
		
		return markers_on_viewport;
		
	}
	
	
	/**
	 * Show/Hide all marker's connected layers
	 *
	 * @since 4.6
	 */
	function cspm_toggle_marker_connected_layers(plugin_map, map_id, marker_obj, toggle){
		
		if(typeof plugin_map === 'undefined' 
		//|| typeof map_id === 'undefined'
		|| typeof marker_obj === 'undefined' 
		|| typeof toggle === 'undefined')
			return;
      	
		var map_id = (typeof map_id === 'undefined' && typeof marker_obj.map_id !== 'undefined') ? marker_obj.map_id : map_id;
		
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

		if(typeof marker_obj.post_id !== 'undefined'){
			
			var post_id =  marker_obj.post_id ;
					
			/**
			 * KML Layers */
			
			var use_kml = progress_map_vars.map_script_args[map_script_id]['use_kml'];
			
			if(use_kml == 'true'){

				setTimeout(function(){																		
					plugin_map.gmap3({
						exec: {
							name: 'kmllayer',
							tag: 'kmlConnectedPost_' + post_id,
							all: true,
							func: function(data){
								if(typeof data.object !== 'undefined' && typeof data.object.setMap === 'function'){
									if(toggle == 'show')
										data.object.setMap(plugin_map.gmap3('get'));
									else data.object.setMap(null);
								}
							}
						}
					});
				}, 100);
				
			}
								
			/**
			 * Ground Overlays */
			
			var ground_overlays_option = progress_map_vars.map_script_args[map_script_id]['ground_overlays_option'];

			if(ground_overlays_option == 'true'){

				setTimeout(function(){																		
					plugin_map.gmap3({
						exec: {
							name: 'groundoverlay',
							all: true,
							func: function(data){
								var original_opacity = data.object.original_opacity;
								var connected_post = data.object.connectedPost;
								if(connected_post == post_id){																									
									if(typeof data.object !== 'undefined' && typeof data.object.setOpacity === 'function'){
										if(toggle == 'show')
											data.object.setOpacity(original_opacity);
										else data.object.setOpacity(0);
									}
								}
							}
						}
					});
				}, 100);

			}
								
			/**
			 * Polylines */
			
			var draw_polyline = progress_map_vars.map_script_args[map_script_id]['draw_polyline'];
			
			if(draw_polyline == 'true'){

				setTimeout(function(){																		
					plugin_map.gmap3({
						exec: {
							name: 'polyline',
							tag: 'polylineConnectedPost_' + post_id,
							all: true,
							func: function(data){
								if(typeof data.object !== 'undefined' && typeof data.object.setVisible === 'function'){
									if(toggle == 'show')
										data.object.setVisible(true);
									else data.object.setVisible(false);
								}
							}
						}
					});
				}, 100);
				
			}
								
			/**
			 * Polygons */
			
			var draw_polygon = progress_map_vars.map_script_args[map_script_id]['draw_polygon'];
			
			if(draw_polygon == 'true'){

				setTimeout(function(){																		
					plugin_map.gmap3({
						exec: {
							name: 'polygon',
							tag: 'polygonConnectedPost_' + post_id,
							all: true,
							func: function(data){
								if(typeof data.object !== 'undefined' && typeof data.object.setVisible === 'function'){
									if(toggle == 'show')
										data.object.setVisible(true);
									else data.object.setVisible(false);
								}
							}
						}
					});
				}, 100);
				
			}
								
		}
																				
	}
	 
	/**
	 * Get the bounds of a polygon, polyline ...
	 * [@latLngs] Array of latLng coordinates [[1,2],[1,2],[1,2],...]
	 *
	 * @since 3.0
	 */
	function cspm_get_latLngs_bounds(latLngs){

		var bounds = new google.maps.LatLngBounds();
		var i;
		
		var LatLngsCoords = [];
		
		jQuery.each(latLngs, function(i, latLngs){
			var lat = latLngs[0];
			var lng = latLngs[1];
			LatLngsCoords.push(new google.maps.LatLng(lat, lng));
		});

		for (i = 0; i < LatLngsCoords.length; i++) {
		  bounds.extend(LatLngsCoords[i]);
		}
		
		return bounds;		
		
	}
	
	/**
	 * Get the center of a polygon, polyline ...
	 * [@latLngs] Array of latLng coordinates [[1,2],[1,2],[1,2],...]
	 *
	 * @since 3.0
	 */
	function cspm_get_latLngs_center(latLngs){

		var bounds = cspm_get_latLngs_bounds(latLngs)
		
		return bounds.getCenter();		
		
	}
	
	/**
	 * Check if bounds (polygon, polyline) are empty ...
	 * [@latLngs] Array of latLng coordinates [[1,2],[1,2],[1,2],...]
	 *
	 * @since 3.0
	 */
	function cspm_are_bounds_empty(latLngs){

		var bounds = cspm_get_latLngs_bounds(latLngs)
		
		return bounds.isEmpty();		
		
	}
	
	
	/**
	 * Zoom to address
	 *
	 * @since 3.0
	 * @updated 3.9
	 */
	function cspm_zoom_to_address(map_id, plugin_map, address, autofit, map_zoom){

		plugin_map.gmap3({
			getlatlng:{
				address: address,
				callback: function(results, status){

					if(status == 'OK'){
						
						if(!results || typeof results[0].geometry.location === 'undefined')
							return;
					
						var mapObject = plugin_map.gmap3("get");
						
						if(!autofit){
							mapObject.setZoom(parseInt(map_zoom));
							mapObject.panTo(results[0].geometry.location);
						}else{
							if(typeof results[0].geometry.bounds !== 'undefined'){
								mapObject.fitBounds(results[0].geometry.bounds);
							}else{
								mapObject.panTo(results[0].geometry.location);
							}
						}
					
					}else{
						var api_status_codes = progress_map_vars.geocoding_api_status_codes; //@since 3.9
						cspm_api_status_info(map_id, api_status_codes, status, 'Geocoding API'); //@since 3.9
					}
					
				}
			}
		});	
		
	}
	
	function cspm_recenter_map(plugin_map, map_id){
        
		/**
		 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
		 * @since 3.0 */
		var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';
		
		var autofit = progress_map_vars.map_script_args[map_script_id]['autofit'];
		
		if(autofit == 'true'){
			
			plugin_map.gmap3({
				get: {
					name: 'marker',
					all:  true,										
				},
				autofit:{}
			});	//@since 4.6
					
		}else{
			
			var map_center = progress_map_vars.map_script_args[map_script_id]['center'].split(',');
			
			if(typeof map_center !== 'undefined'){
				
				var zoom = parseInt(progress_map_vars.map_script_args[map_script_id]['zoom']);
		
				var mapObject = plugin_map.gmap3("get");
						
				var latLng = new google.maps.LatLng(map_center[0], map_center[1]);
				
				mapObject.setZoom(zoom);
	
				setTimeout(function(){
					if(typeof mapObject.panTo === 'function')
						mapObject.panTo(latLng);
				}, 200);
				
			}else console.log('PROGRESS MAP: There was a problem centring the map. Check your initial map center coordinates in "Progress Map => Map settings => Map center", then, try again.');
		
		}
		
	}
	
	/**
	 * This will open the single post page inside a modal/popup
	 *
	 * @since 3.6
	 * @updated 5.4 | 5.6.3
	 */
	function cspm_open_single_post_modal(iframe_url){
		
        if(jQuery('#cspm_single_post_modal').length == 0){ 
            jQuery('body').append('<div id="cspm_single_post_modal"></div>');
        } //@since 5.6.3
        
		var modal_state = jQuery('#cspm_single_post_modal').iziModal('getState'); //@since 5.4
		
		if(typeof iframe_url === 'undefined' || iframe_url == '' || modal_state == 'opened' || modal_state == 'opening') //@edited 5.4
			return false;
		
		var modal_width = progress_map_vars.map_script_args['initial']['modal_width']; //@since 4.6
		var modal_width_unit = progress_map_vars.map_script_args['initial']['modal_width_unit']; //@since 4.6
		var modal_height = progress_map_vars.map_script_args['initial']['modal_height']; //@since 4.6
		var modal_openFullscreen = progress_map_vars.map_script_args['initial']['modal_openFullscreen']; //@since 4.6
		var modal_allow_fullscreen = progress_map_vars.map_script_args['initial']['modal_allow_fullscreen']; //@since 4.7
		var modal_top_margin = progress_map_vars.map_script_args['initial']['modal_top_margin']; //@since 5.5
		var modal_bottom_margin = progress_map_vars.map_script_args['initial']['modal_bottom_margin']; //@since 5.5
		
		jQuery('#cspm_single_post_modal').iziModal({
			title: '<br>',
			headerColor: progress_map_vars.map_script_args['initial']['main_rgb_color'],
			width: parseInt(modal_width) + modal_width_unit,
			top: parseInt(modal_top_margin),
			bottom: parseInt(modal_bottom_margin),
			borderBottom: true,
			padding: 0,
			radius: 2,
			zindex: 99999,
			closeOnEscape: true,
			overlay: true,
			overlayClose: true,
			transitionIn: 'fadeInDown',
			iframe: true,
			iframeHeight: parseInt(modal_height),
			iframeURL: iframe_url,
			fullscreen: (modal_allow_fullscreen == 'no') ? false : true,
			openFullscreen: (modal_openFullscreen == 'no') ? false : true,
			bodyOverflow: true,
			onClosed: function(modal){
				jQuery('#cspm_single_post_modal').iziModal('destroy');					
			}
		});
		
		jQuery('#cspm_single_post_modal').iziModal('open');
		
	}
	
	/**
	 * This will display a modal containing the post media elements
	 *
	 * @ince 3.5
	 * @updated 3.6 [Open the single post page link]
	 * @updated 4.6 [Open the nearby map page]
	 * @updated 5.5 [Added new attribute "allow_marker_click" to prevent opening media files on marker click event]
	 */
	function cspm_open_media_modal(media, map_id, allow_marker_click){

		if(cspm_object_size(media) == 0 || cspm_is_media_empty(media, map_id))
			return;
		
		var format = media.format;

		/**
		 * Open the single post page if the format is "Link"
		 * @since 3.6 */
		 
		if(format == 'link' && typeof media.link !== 'undefined' && media.link != ''){
			cspm_open_single_post_modal(media.link);
			return;
		}
		
		/**
		 * Open the nearby palces map if the format is "Nearby Places Map"
		 * @since 4.6 */
		 
		if(format == 'nearby_places' && typeof media.post_id !== 'undefined' && media.post_id != ''){
			if(progress_map_vars.nearby_places_page_url != ''){
				cspm_open_single_post_modal(progress_map_vars.nearby_places_page_url+'?post_id='+media.post_id);
			}
			return;
		}
		
		/**
		 * Return if marker click is disabled 
		 * @since 5.5 */
		 
		if(allow_marker_click == 'no')
			return;
        
        /**
         * Build the modal 
         * @since 5.6.3 */
        
		var modal_class_name = 'div.cspm_location_modal';	   

        if(jQuery(modal_class_name).length == 0){
            jQuery('body').append('<div class="cspm_location_modal"></div>');
        }
        
		/**
		 * This will output the modal content based on the media format.
		 * @since 3.5 */
		 
		var cspm_modal_output = function(media, map_id){
			
			var format = media.format;
			var gallery = media.gallery;
			var image = media.image;
			var embed = media.embed;
			var audio = media.audio;
			
			var output = '';
			
			/**
			 * Media container */
			
			output += '<div class="cspm_modal_media_container" data-map-id="'+map_id+'">';
				
				/**
				 * Close modal button */
				 
				output += '<a href="javascript:void(0)" class="cspm_close_modal cspm_modal_action_btn" data-map-id="'+map_id+'">';
					output += '<img src="'+progress_map_vars.plugin_url+'img/svg/close_modal.svg" />';
				output += '</a>';
		
				if(format == 'all' || format == 'gallery'){
					
					/**
					 * Media preview area */
					 
					output += '<div class="cspm_modal_carousel_preview cspm_border_top_radius" data-map-id="'+map_id+'"></div>';
					
					/**
					 * Carousel media items container */
					
					if(format == 'all' || format == 'gallery'){
						
						output += '<div class="cspm_modal_carousel_container" data-map-id="'+map_id+'">';
						
							/**
							 * Prev & Next buttons */
						 
							output += '<div class="cspm_modal_carousel_prev cspm_border_right_radius cspm_border_shadow cspm_modal_action_btn" data-map-id="'+map_id+'">';
								output += '<img src="'+progress_map_vars.plugin_url+'img/svg/prev.svg" />';
							output += '</div>';
							
							output += '<div class="cspm_modal_carousel_next cspm_border_left_radius cspm_border_shadow cspm_modal_action_btn" data-map-id="'+map_id+'">';
								output += '<img src="'+progress_map_vars.plugin_url+'img/svg/next.svg" />';
							output += '</div>';
							
							/**
							 * Carousel media items/thumbs */
						 
							output += '<div class="cspm_modal_carousel" data-map-id="'+map_id+'">';
							
								var index = 0;
								
								/**
								 * Gallery */
								
								if(cspm_object_size(gallery) > 0){
									
									jQuery.each(gallery, function(i, img){
										if(cspm_object_size(img) && img.preview != '' && img.thumb != ''){
											index = i;
											output += '<div class="cspm_modal_carousel_item" data-index="'+index+'" data-type="image" data-src="'+img.preview+'" data-full-src="'+img.fullscreen+'">';
												output += '<img class="cspm_border_radius cspm_border_shadow" src="'+img.thumb+'" />';
											output += '</div>';
										}
									});
									
								}
								
								/**
								 * Image */
								 
								if(format == 'all' && cspm_object_size(image) > 0){
									output += '<div class="cspm_modal_carousel_item" data-index="'+index+'" data-type="image" data-src="'+image.preview+'" data-full-src="'+image.fullscreen+'">';
										output += '<img class="cspm_border_radius cspm_border_shadow" src="'+image.thumb+'" />';
									output += '</div>';
								}
								
								/**
								 * Audio */
								 
								if(format == 'all' && audio != ''){
									output += '<div class="cspm_modal_carousel_item cspm_thumb_audio cspm_border_radius cspm_border_shadow" data-index="'+(++index)+'" data-type="audio" data-src="'+audio+'" data-full-src="">';
										output += '<img src="'+progress_map_vars.plugin_url+'img/svg/audio.svg" />';
									output += '</div>';
								}
								
								/**
								 * Embed */
								 
								if(format == 'all' && cspm_object_size(embed) > 0){ 
									
									if(typeof embed.html != 'undefined' && embed.html != ''){
										
										var embed_title = embed.title;
										var embed_type = embed.type;
										var embed_thumb = embed.thumb;
										var embed_provider = embed.provider;
										var embed_html = embed.html;
										
										var thumb_url = (embed_thumb != '') ? embed_thumb : progress_map_vars.plugin_url+'img/svg/video.svg';
										var thumb_conatiner_class = (embed_thumb != '') ? 'cspm_thumb_embed' : 'cspm_default_thumb_embed cspm_border_radius cspm_border_shadow';
										var thumb_img_class =  (embed_thumb != '') ? 'cspm_border_radius cspm_border_shadow' : '';
										
										output += '<div class="cspm_modal_carousel_item '+thumb_conatiner_class+'" data-index="'+(++index)+'" data-type="embed" data-src="'+embed_html+'" data-full-src="">';
											output += '<img src="'+thumb_url+'" class="'+thumb_img_class+'" title="'+embed_title+'" alt="'+embed_title+'" />';
										output += '</div>';
										
									}
								
								}
								
							output += '</div>';
								
						output += '</div>';
					
					}

				}else{
							
					/**
					 * Image */
					 
					if(format == 'image' && cspm_object_size(image) > 0)
						output += '<div class="cspm_modal_media_img cspm_border_radius" data-map-id="'+map_id+'">'+media_format_output(format, image.single, image.fullscreen)+'</div>';
					
					/**
					 * Audio */
					 
					if(format == 'audio' && audio != '')
						output += '<div class="cspm_modal_media_audio cspm_border_radius cspm_border_shadow" data-map-id="'+map_id+'">'+media_format_output(format, audio, '')+'</div>';
					
					/**
					 * Embed */
					 
					if(format == 'embed' && cspm_object_size(embed) > 0){

						if(typeof embed.html != 'undefined' && embed.html != '')
							output += '<div class="cspm_modal_media_embed cspm_border_radius cspm_border_shadow" data-map-id="'+map_id+'">'+media_format_output(format, embed.html, '')+'</div>';						
							
					}
					
				}
					
			output += '</div>';
			
			return output;
			
		}
		
		/**
		 * Get the media output based on its type.
		 * @since 3.5 */
		 
		var media_format_output = function(type, src, full_src){
			
			var output = '';
			
			/**
			 * Image */
			 
			if(type == 'image'){

				output += '<img src="'+src+'" />';
				output += '<a href="'+full_src+'" target="_blank" class="cspm_open_fullscreen_img cspm_modal_action_btn">';
					output += '<img src="'+progress_map_vars.plugin_url+'img/svg/fullscreen.svg" />';
				output += '</a>';
			
			/**
			 * Audio */
			 
			}else if(type == 'audio'){
		
				output += '<audio class="cspm_audio_player" preload="none" controls controlsList="nodownload" autoplay="true" style="width:100% !important; margin:0 !important;">';
					output += '<source src="'+src+'">';
				output += '</audio>';
			
			/**
			 * Embed */
			 
			}else if(type == 'embed'){
				
				var iframe_HTML = src.replace(/&quot;/g, '"').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
				
				output += '<div class="cspm_embed_container">';
					output += iframe_HTML;
				output += '</div>';								
				
			}
			
			return output;
						
		}
		
		/**
		 * This will display the selected media from the carousel in the preview area.
		 * This works only for the format 'All' & 'Gallery'!
		 * @since 3.5 */
		 
		var preview_media_output = function(type, src, fullscreen_size){
			
			var output = media_format_output(type, src, fullscreen_size);
			
			jQuery('div.cspm_modal_carousel_preview[data-map-id='+map_id+']').fadeOut('slow', function(){
				
				jQuery(this).attr('data-type', type).html(output).fadeIn('fast');
				
				if(type == 'audio')
					jQuery('.cspm_audio_player').mediaelementplayer();
			
			});
			
		}

		/**
		 * Initialize the modal
         * @edited 5.6.3 */

		if(jQuery(modal_class_name).length > 0){
            jQuery(modal_class_name).iziModal({
                background: '#fefefe',
                overlayClose: true,
                focusInput: false,
                borderBottom: false,
                width: 150,
                zindex: 9999,
                restoreDefaultContent: true,
                transitionIn: 'fadeInDown',
            });
        }
				
		/**
		 * Modal output */
		
		var modal_HTML = cspm_modal_output(media, map_id);
		
		/**
		 * On opening, start loading the modal */
		 
		jQuery(modal_class_name).on('opening', function(e, modal){
			jQuery(modal_class_name).iziModal('startLoading');		
		});
		
		/**
		 * When opend, set the modal content */
		 
		jQuery(modal_class_name).on('opened', function(e, modal){
			
			jQuery(modal_class_name).iziModal('setContent', modal_HTML);			
				jQuery('.cspm_modal_action_btn').hide();
			
			/**
			 * Gallery & All format */
			 
			if(format == 'all' || format == 'gallery'){

				/** 
				 * Initialize the carousel */
				 
				var cspm_modal_carousel = new Siema({
					
					selector: 'div.cspm_modal_carousel[data-map-id='+map_id+']',
					loop: true,
					perPage: {
						100: 1, 300: 3, 400: 3, 
						500: 4, 600: 5, 700: 5,
					},	
						
					/**
					 * On init, show the first carousel item on the preview area */
															
					onInit: function(){		
						var type = jQuery('div.cspm_modal_carousel[data-map-id='+map_id+'] div[data-index=0]').attr('data-type');
						var src = jQuery('div.cspm_modal_carousel[data-map-id='+map_id+'] div[data-index=0]').attr('data-src');
						var full_src = jQuery('div.cspm_modal_carousel[data-map-id='+map_id+'] div[data-index=0]').attr('data-full-src');
						preview_media_output(type, src, full_src);
					},	
					
					/**
					 * On change, get the current slide index an show the media on the preview area */
																
					onChange: function(){						 
						var index = this.currentSlide;
						var type = jQuery('div.cspm_modal_carousel[data-map-id='+map_id+'] div[data-index='+index+']').attr('data-type');
						var src = jQuery('div.cspm_modal_carousel[data-map-id='+map_id+'] div[data-index='+index+']').attr('data-src');							
						var full_src = jQuery('div.cspm_modal_carousel[data-map-id='+map_id+'] div[data-index='+index+']').attr('data-full-src');
						preview_media_output(type, src, full_src);
					}
					
				});
				
				/**
				 * Detect the click on the carousel items and display the selected item in the preview area */
				 
				//jQuery('div.cspm_modal_carousel[data-map-id='+map_id+'] div.cspm_modal_carousel_item').on('click', function(){
                jQuery(document).on('click', 'div.cspm_modal_carousel[data-map-id='+map_id+'] div.cspm_modal_carousel_item', function(){ //@edited 5.6.3
					var index = jQuery(this).attr('data-index');
					var type = jQuery('div.cspm_modal_carousel[data-map-id='+map_id+'] div[data-index='+index+']').attr('data-type');
					var src = jQuery('div.cspm_modal_carousel[data-map-id='+map_id+'] div[data-index='+index+']').attr('data-src');
					var full_src = jQuery('div.cspm_modal_carousel[data-map-id='+map_id+'] div[data-index='+index+']').attr('data-full-src');
					preview_media_output(type, src, full_src);
				});	
		
				/**
				 * Detect the click on the previous & next btns and display the current item in the preview area */
				 				
                jQuery(document).on('click', 'div.cspm_modal_carousel_prev[data-map-id='+map_id+']', function(){ //@edited 5.6.3
					cspm_modal_carousel.prev(cspm_modal_carousel.perPage); 
				});
				
                jQuery(document).on('click', 'div.cspm_modal_carousel_next[data-map-id='+map_id+']', function(){ //@edited 5.6.3
					cspm_modal_carousel.next(cspm_modal_carousel.perPage);
				});	
		
				/**
				 * Stop the modal loading when all images loaded & display the modal btns/links */
				 
				jQuery('div.cspm_modal_carousel[data-map-id='+map_id+'] .cspm_modal_carousel_item').imagesLoaded().done(function(instance){
					jQuery(modal_class_name).iziModal('stopLoading');
					jQuery(modal_class_name).iziModal('setWidth', 700);										
					cspm_modal_carousel.resizeHandler(); // Resize the carousel	
					jQuery('.cspm_modal_action_btn').show();									
				});
			
			/**
			 * Image format */
			 		
			}else if(format == 'image'){
				
				jQuery('div.cspm_modal_media_img[data-map-id='+map_id+']').imagesLoaded().done(function(instance){
					
					jQuery(modal_class_name).iziModal('stopLoading');
					jQuery(modal_class_name).iziModal('setWidth', 700);	
					jQuery('.cspm_modal_action_btn').show();						
				
				}).fail(function(instance){
					
					jQuery(modal_class_name).iziModal('close');
					
					if(!cspm_popup_msg(map_id, 'error', 'Oops!', progress_map_vars.broken_img_msg))
						alert('Oops! '+progress_map_vars.broken_img_msg);
				
				});
			
			/**
			 * Audio & Video format */
			 	
			}else{
				jQuery(modal_class_name).iziModal('stopLoading');
				jQuery(modal_class_name).iziModal('setWidth', (format == 'audio') ? 400 : 700);												
				jQuery('.cspm_modal_action_btn').show();	
			}
			
			if(format == 'audio'){
				jQuery(modal_class_name).css({'min-height': 'auto'});
				jQuery('.cspm_audio_player').mediaelementplayer();
			}

		});
	
		/**
		 * On closed, remove the modal and destroy it */
			 
		jQuery(modal_class_name).on('closed', function(e, modal){
			jQuery(modal_class_name).remove().iziModal('destroy');
		});

		/**
		 * Open the modal */
			 
		jQuery(modal_class_name).iziModal('open');
		
		/**
		 * Close the modal */
		
		//jQuery('a.cspm_close_modal').on('click', function(){
        jQuery(document).on('click', 'a.cspm_close_modal', function(){ //@edited 5.6.3
			jQuery(modal_class_name).iziModal('close');
		});
		
	}
	
	/**
	 * This will check if the post media is available based on its format
	 *
	 * @since 3.5
	 * @updated 3.6 | 5.5
	 */
	function cspm_is_media_empty(media, map_id){

		var format = media.format;
		var gallery = media.gallery;
		var image = media.image;
		var embed = media.embed;
		var audio = media.audio;
		var link = media.link; //@since 3.6

		if((format == 'all' 
			&& (cspm_object_size(gallery) > 0 
				|| cspm_object_size(image) > 0 
				|| audio.length > 0 //@edited 5.5
				|| (typeof embed.html !== 'undefined' && embed.html.length > 0) //@edited 5.5
			))
		|| (format == 'link' && link != '') //@since 3.6
		|| (format == 'gallery' && cspm_object_size(gallery) > 0)
		|| (format == 'image' && cspm_object_size(image) > 0)
		|| (format == 'audio' && audio.length > 0) //@edited 5.5
		|| (format == 'embed' && (typeof embed.html !== 'undefined' && embed.html.length > 0)) //@edited 5.5
		|| (format == 'nearby_places')){
			
			return false;
			
		}else return true;
			
	}
	
	
	/**
	 * This will open a message that informs the user that if they click ...
	 * ... on the marker, media will be displayed.
	 *
	 * @since 3.5
	 * @updated 5.5
	 */
	function cspm_open_media_message(media, map_id){

		if(cspm_object_size(media) == 0)
			return;
			
		if(typeof media.format === 'undefined' || media.format == '')
			return;

		var format = media.format;
		var gallery = media.gallery;
		var image = media.image;
		var embed = media.embed;
		var audio = media.audio;
		
		/**
		 * Don't show popup message for markers with media type format & marker click is disabled 
		 * @since 5.5 */
		 
		if(jQuery.inArray(format, ['link', 'nearby_places']) === -1 && media.marker_click == 'no')
			return;
			
		if(!cspm_is_media_empty(media, map_id)){
			var message = progress_map_vars.media_format_msg['format_'+format+'_msg'];
			if(message != ''){
				cspm_popup_msg(map_id, 'info', '',  message); 
			}
		}
		
	}
	
	
	/**
	 * This will close the post media message
	 *
	 * @since 3.5
	 */
	function cspm_close_media_message(map_id){
		
		cspm_close_popup_msg(map_id, 'info');
 	
	}
	
	
	/**
	 * This will pop-up a (success, error, info or warning) message
	 * [@type] Accepts "success", "info", "warning", "error".
	 *
	 * @since 3.5 
	 */
	function cspm_popup_msg(map_id, type, title, message){
		
		if(typeof iziToast === 'undefined'){
			
			/**
			 * A fallback if something went wrong with iziToast */
			 
			if(type == 'error')
				return false; // Errors will be displayed using alert()!
				
			var message_type = type.replace('success', 'green').replace('info', 'blue').replace('warning', 'orange').replace('error', 'red').replace('fatal_error', 'red');
			
			var map_message_widget = jQuery('div.cspm_map_'+message_type+'_msg_widget[data-map-id='+map_id+']');
			
			map_message_widget.text(message).removeClass('fadeOut').addClass('cspm_animated fadeIn').css({'display':'block'});
			
			setTimeout(function(){ 
				map_message_widget.removeClass('fadeIn').addClass('fadeOut');
				setTimeout(function(){
					map_message_widget.css({'display':'none'});
				}, 100);
			}, 3000);
			
		}else{
						
			var default_settings = {
				class: 'cspm_'+type+'_popup_'+map_id,				
				message: message,
				animateInside: true,
				titleSize: 13,
				messageSize: 13,						
				maxWidth: 450,
			}
			
			var custom_settings = {
				success: { id: 'cspm_success_popup_'+map_id, position: 'topRight', transitionIn: 'fadeInLeft', timeout: 3000, displayMode: 1, close: true, },
				info: { position: 'topLeft', transitionIn: 'fadeIn', timeout: 3000, displayMode: 2, close: false, },
				warning: { id: 'cspm_warning_popup_'+map_id, position: 'center', transitionIn: 'fadeInUp', timeout: 3000, toastOnce: true, close: true, },			
				error: { id: 'cspm_error_popup_'+map_id, title: title, icon: '', position: 'center', transitionIn: 'fadeInUp', timeout: 15000, displayMode: 1, close: true, },
				fatal_error: { id: 'cspm_fatal_error_popup_'+map_id, title: title, icon: '', position: 'center', transitionIn: 'fadeInUp', timeout: false, displayMode: 1, close: true, drag: false, }, //@edited 5.6.5
				detailed_warning: { id: 'cspm_warning_popup_'+map_id, title: title, position: 'center', transitionIn: 'fadeInUp', timeout: 10000, displayMode: 1, close: true, }, //@since 5.2
			};
			
			var popup_settings = jQuery.extend({}, default_settings, custom_settings[type]);
	
			if(type == 'success')
				iziToast.success(popup_settings);
			
			else if(type == 'error' || type == 'fatal_error')
				iziToast.error(popup_settings);
			
			else if(type == 'warning' || type == 'detailed_warning')
				iziToast.warning(popup_settings);
			
			else iziToast.info(popup_settings);
			
		}
		
		return true;
		
	}
	
	
	/**
	 * This will close the poped-up message
	 *
	 * @since 3.5 
	 */
	function cspm_close_popup_msg(map_id, type){
		
		if(typeof iziToast === 'undefined')
			return false;	
					
		jQuery('.cspm_'+type+'_popup_'+map_id).each(function(i, message){
			iziToast.hide({
				transitionOut: 'fadeOutUp'
			}, message);
		});
		
		return true;
 	
	}
	
	
	/**
	 * This will display a marker/icon on the top-right corner of a "Ground Overlay" ...
	 * ... which we will use to hide & show the image.
	 *
	 * @since 3.5 
	 */	 
	function cspm_groundoverlay_callback(map_id, plugin_map, overlay){

		if(typeof map_id === 'undefined' || typeof plugin_map != 'object' || typeof overlay != 'object')
			return;
		
		var visibility = overlay.visibility;
				
		if(visibility == 'always' || visibility == 'marker_connected')
			return;

		var ne_latLng = overlay.getBounds().getNorthEast();
		
		var opacity = overlay.opacity;
		var original_opacity = overlay.original_opacity;									
		
		var show_icon = overlay.show_icon;
		var hide_icon = overlay.hide_icon;
		var icon_position = (overlay.icon_position && overlay.icon_position != '') ? overlay.icon_position : ne_latLng;

		var marker_icon = (opacity == 0) ? show_icon : hide_icon;

		plugin_map.gmap3({
			marker:{
				latLng: icon_position,
				tag: 'cspm_groundoverlay_marker_'+map_id,
				options:{
					icon: {
						url: marker_icon,
					},
					marker_id: 'groundoverlay_marker',
				},
				events: {
					click: function(marker, event, elements){
						if(overlay.getOpacity() == 0){
							marker.setIcon(hide_icon);
							overlay.setOpacity(original_opacity);
							cspm_close_popup_msg(map_id, 'info');														
						}else{														
							marker.setIcon(show_icon);
							overlay.setOpacity(0);
							cspm_close_popup_msg(map_id, 'info');	
						}
					},
					mouseover: function(){
						cspm_popup_msg(map_id, 'info', '',  progress_map_vars.hide_img_overlay_msg);
					},
					mouseout: function(){
						cspm_close_popup_msg(map_id, 'info');
					}
				}
			}
		});
	
	}
	
	
	/**
	 * This will display a GMaps API status message to allow debugging errors.
	 *
	 * @since 3.9
	 */
	function cspm_api_status_info(map_id, api_status_codes, current_status, api_string_name){
		
		if(typeof map_id === 'undefined' || typeof api_status_codes === 'undefined' || typeof current_status === 'undefined')
			return;
							
		var message = '';
		var msg_type = '';
		
		if(typeof api_status_codes.warning[current_status] !== 'undefined'){
			message = api_status_codes.warning[current_status];
			msg_type = 'warning';
			
		}else if(typeof api_status_codes.error[current_status] !== 'undefined'){
			message = '<br />' + api_status_codes.error[current_status];
			msg_type = 'error';
			
		}else if(typeof api_status_codes.console[current_status] !== 'undefined'){
			message = api_status_codes.console[current_status];
			msg_type = 'console';
		}
		
		if(message != ''){
			message = message.replace(/&quot;/g, '"').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
			if(msg_type !== 'console')
				cspm_popup_msg(map_id, msg_type, current_status, message);
			else console.warn('PROGRESS MAP, "' + api_string_name + '": (' + current_status + ') ' + message);
		}
		
	}
	
	
	/** 
	 * This will show error messages when Google Maps fails to load.
	 * @since 5.6.5
	 */
	function cspm_check_gmaps_failure(map_id){

		var GCC_link = '<a href="https://console.cloud.google.com/apis/dashboard" target="_blank">Google Cloud Console</a>';
		var required_apis_list = '<ul><li>Maps JavaScript API</li><li>Directions API</li><li>Distance Matrix API</li><li>Geocoding API</li><li>Places API</li></ul>';
								
		if(window.console && console.error){
			
			var original_console_errors = console.error;
			
			/**
			 * Overwrite original console error to read errors and show our custom messages */
			 
			console.error = function(){	
					
				if(typeof arguments[0].indexOf === 'function'){
					
					var title = ''; var message = ''; var note = '';
					
					/**
					 * Missing an API Key */
					 
					if(arguments[0].indexOf('You are using this API without a key') != -1 
					|| arguments[0].indexOf('MissingKeyMapError') != -1){
						
						title = 'The API Key is missing';
						
						message  = 'You are using this map without an API Key. ';
						message += 'Please make sure to add an API Key in the map settings.<br /><br />';
						message += 'If you already have an API Key, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/insert-the-google-maps-api-key/" target="_blank">link</a> to see where to use it. ';
						message += 'if you did not create any API Key, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a> for more details. ';
						message += 'If you are new to Google Maps Platform, visit this <a href="https://developers.google.com/maps/gmp-get-started" target="_blank">link</a> to get started.';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API Key added, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * Deleted API project from GCC */
					 
					}else if(arguments[0].indexOf('DeletedApiProjectMapError') != -1){
						
						title = 'API project may have been deleted';
						
						message  = 'Your API project may have been deleted from your ' + GCC_link + '. ';
						message += 'Please check the project for which you generated the API key that\'s included in your map settings. ';
						message += 'You can <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">create a new API project and get a new key</a> in the Cloud Console. ';
						message += 'Once created, please make sure to <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/insert-the-google-maps-api-key/" target="_blank">add the new API Key in the map settings</a>. <br /><br />';
						message += 'If you are new to Google Maps Platform, visit this <a href="https://developers.google.com/maps/gmp-get-started" target="_blank">link</a> to get started.';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API Key added, it may take up to 5 minutes for settings to take effect!';
						
					/**
					 * API Key not authorised to use "Directions API" */
					 
					}else if(arguments[0].indexOf('Directions Service: This API key is not authorized to use this service or API') != -1){
						
						title = 'The API Key is not authorised to use "Directions API"';
						
						message  = 'The API key you provided in your map setting is not authorized to use the "Directions API". ';
						message += 'Please check the API restrictions settings of your API key in your ' + GCC_link +' to ensure that the "Directions API" is correctly specified in the list of enabled APIs. ';
						message += 'See <a href="https://console.cloud.google.com/project/_/apiui/credential?_ga=2.51736993.949578656.1620363005-1066915472.1487262090" target=_blank">API keys in the Cloud Console</a>. <br /><br />';
						message += 'For more info about fixing this error, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a>';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API authorised, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * API Key not authorised to use "Distance Matrix API" */
					 
					}else if(arguments[0].indexOf('Distance Matrix Service: This API key is not authorized to use this service or API') != -1){
						
						title = 'The API Key is not authorised to use "Distance Matrix API"';
						
						message  = 'The API key you provided in your map setting is not authorized to use the "Distance Matrix API". ';
						message += 'Please check the API restrictions settings of your API key in your ' + GCC_link +' to ensure that the "Distance Matrix API" is correctly specified in the list of enabled APIs. ';
						message += 'See <a href="https://console.cloud.google.com/project/_/apiui/credential?_ga=2.51736993.949578656.1620363005-1066915472.1487262090" target=_blank">API keys in the Cloud Console</a>. <br /><br />';
						message += 'For more info about fixing this error, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a>';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API authorised, it may take up to 5 minutes for settings to take effect!';
						
					/**
					 * API Key not authorised to use "Geocoding API" */
					 
					}else if(arguments[0].indexOf('Geocoding Service: This API key is not authorized to use this service or API') != -1){
						
						title = 'The API Key is not authorised to use "Geocoding API"';
						
						message  = 'The API key you provided in your map setting is not authorized to use the "Geocoding API". ';
						message += 'Please check the API restrictions settings of your API key in your ' + GCC_link +' to ensure that the "Geocoding API" is correctly specified in the list of enabled APIs. ';
						message += 'See <a href="https://console.cloud.google.com/project/_/apiui/credential?_ga=2.51736993.949578656.1620363005-1066915472.1487262090" target=_blank">API keys in the Cloud Console</a>. <br /><br />';
						message += 'For more info about fixing this error, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a>';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API authorised, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * API Key not authorised to use "Places API" */
					 
					}else if(arguments[0].indexOf('Places API error: ApiTargetBlockedMapError') != -1){
						
						title = 'The API Key is not authorised to use "Places API"';
						
						message  = 'The API key you provided in your map setting is not authorized to use the "Places API". ';
						message += 'Please check the API restrictions settings of your API key in your ' + GCC_link +' to ensure that the "Places API" is correctly specified in the list of enabled APIs. ';
						message += 'See <a href="https://console.cloud.google.com/project/_/apiui/credential?_ga=2.51736993.949578656.1620363005-1066915472.1487262090" target=_blank">API keys in the Cloud Console</a>. <br /><br />';
						message += 'For more info about fixing this error, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a>';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API authorised, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * API Key not authorised to use required APIs */
					 
					}else if(arguments[0].indexOf('ApiTargetBlockedMapError') != -1){
						
						title = 'The API Key is not authorised to use Maps APIs';
						message = 'The API key you provided in your map setting is not authorized to use all Google Maps APIs required by this plugin. Please check the API restrictions settings of your API key in your ' + GCC_link +' to ensure that all of the required APIs you need to use are correctly specified in the list of enabled APIs. See <a href="https://console.cloud.google.com/project/_/apiui/credential?_ga=2.51736993.949578656.1620363005-1066915472.1487262090" target=_blank">API keys in the Cloud Console</a>. <br /><br />Here\'s the list of all required APIs: ' + required_apis_list + 'For more info about fixing this error, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a>';
						note = '<br /><br /><u><strong>Note:</strong></u> Once APIs authorised, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * Invalid API Key */
					 
					}else if(arguments[0].indexOf('InvalidKeyMapError') != -1){
						
						title = 'Invalid Google Maps API Key';
						
						message  = 'The <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/insert-the-google-maps-api-key/" target="_blank">API key included in the plugin</a> is not found in your Google Cloud Console or is incorrect. ';
						message += 'Please make sure you are using a correct API key. You can generate a new API key in the '+ GCC_link +'. <br /><br />';
						message += 'For more infos about generating an API Key, visit this <a href="https://developers.google.com/maps/gmp-get-started#api-key" target="_blank">link</a>';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API Key corrected, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * "Geocoding API" not enabled */
					 
					}else if(arguments[0].indexOf('Geocoding Service: This API project is not authorized to use this API.') != -1){
						
						title = 'Missing required API: "Geocoding API"';
						message = 'Please make sure to enable the "Geocoding API" from your ' + GCC_link + '. For more info about fixing this error, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a>.';
						note = '<br /><br /><u><strong>Note:</strong></u> Once API enabled, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * "Directions API" not enabled */
					 
					}else if(arguments[0].indexOf('Directions Service: This API project is not authorized to use this API.') != -1){
						
						title = 'Missing required API: "Directions API"';
						
						message  = 'Please make sure to enable the "Directions API" from your ' + GCC_link + '. ';
						message += 'For more info about fixing this error, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a>.';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API enabled, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * "Distance Matrix API" not enabled */
					 
					}else if(arguments[0].indexOf('Distance Matrix Service: This API project is not authorized to use this API.') != -1){
						
						title = 'Missing required API: "Distance Matrix API"';
						
						message  = 'Please make sure to enable the "Distance Matrix API" from your ' + GCC_link + '. ';
						message += 'For more info about fixing this error, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a>.';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API enabled, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * "Places API" not enabled */
					 					
					}else if(arguments[0].indexOf('Places API error: ApiNotActivatedMapError') != -1){
						
						title = 'Missing required API: "Places API"';
						
						message  = 'Please make sure to enable the "Places API" from your ' + GCC_link + '. ';
						message += 'For more info about fixing this error, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a>.';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API enabled, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * Required API(s) not enabled */
					 
					}else if(arguments[0].indexOf('ApiNotActivatedMapError') != -1){
						
						title = 'Missing required API(s)';
						
						message  = 'Please make sure to enable all the required APIs from your ' + GCC_link + '. ' + required_apis_list;
						message += 'For more info about fixing this error, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a>.';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API enabled, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * Billing not enabled */
					 
					}else if(arguments[0].indexOf('BillingNotEnabledMapError') != -1 
						|| arguments[0].indexOf('BillingNotEnabledMapError') != -1 
						|| arguments[0].indexOf('You must enable Billing on the Google Cloud Project at') != -1){
						
						title = 'Client or project billing not enabled';
						
						message  = 'You have not enabled billing on your project. ';
						message += 'You must enable Billing on the Google Cloud Project associated to this client ID or in your project which is causing this error <a href="https://console.cloud.google.com/project/_/billing/enable?_ga=2.218824337.949578656.1620363005-1066915472.1487262090" target="_blank">here</a>.<br /><br />';
						message += 'If you are new to Google Maps Platform, visit this <a href="https://developers.google.com/maps/gmp-get-started" target="_blank">link</a> to get started.';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once billing enabled, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * Expired API Key */
					 
					}else if(arguments[0].indexOf('ExpiredKeyMapError') != -1){
						
						title = 'Expired API Key';
						
						message  = 'The API key included in the map settings has expired or is not recognized by the system. ';
						message += 'You may receive this error after creating a new API key if you try to use the key before it is recognized by the system. ';
						message += 'Wait a few minutes and try again, or you may need to generate a new API key in your ' + GCC_link + '<br /><br />';
						message += '- To create an API Key, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a> for all details.<br />';
						message += '- To use the API Key with your map, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/insert-the-google-maps-api-key/" target="_blank">link</a> to see where to use it.<br />';
						message += '- If you are new to Google Maps Platform, visit this <a href="https://developers.google.com/maps/gmp-get-started" target="_blank">link</a> to get started.';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API Key created, it may take up to 5 minutes for settings to take effect!';
					
					/**
					 * Invalid Client ID */
					 
					}else if(arguments[0].indexOf('InvalidClientIdMapError') != -1){
						
						title = 'Invalid Client ID';
						
						message  = 'The client ID included in the script element that loads the API is invalid, or expired. ';
						message += 'Please make sure you are using your client ID correctly. ';
						message += 'The client ID should start with "gme-" prefix. ';
						message += 'If you see this error even when using your client ID correctly, the client ID may have expired.<br /><br />';						
						message += 'If you do not have a Premium Plan or Maps APIs for Work license, you need to use a <strong>key</strong> parameter with your API key instead of the <strong>client</strong> parameter.<br /><br />';
						message += 'See the guide to <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#premium-auth" target="_blank">Premium Plan authentication</a>.';
						
						note = '<br /><br /><u><strong>Note:</strong></u> If you don\'t now how to fix this error, contact your Google Account Manager.<br /><br />';
					
					/**
					 * Project Denied */
					 
					}else if(arguments[0].indexOf('ProjectDeniedMapError') != -1){
						
						title = 'Project Denied';
						
						message  = 'Your request has not been completed. You may be able to find the more details about the error in the Cloud Console.<br /><br />';
						message += 'See your ' + GCC_link;
						
						note = '<br /><br /><u><strong>Note:</strong></u> If you don\'t now how to fix this error, contact your Google Account Manager.<br /><br />';
					
					/**
					 * Referer Denied */
					 
					}else if(arguments[0].indexOf('RefererDeniedMapError') != -1){
						
						title = 'Referer Denied';
						
						message  = 'Your application was blocked for non-compliance with the Google Maps Platform Terms of Service, following several email notifications. ';
						message += 'To appeal the block and have your implementation reviewed, please complete <a href="https://docs.google.com/forms/d/e/1FAIpQLSdndZBL6qgP4iDUx8M7i5_yyf1AVji5OEag8qERRJ6vyqK7Kw/viewform" target="_blank">this form</a>. You will receive a response via email within a few business days.';
						
						note = '';
					
					/**
					 * Referer Not Allowed */
					 
					}else if(arguments[0].indexOf('RefererNotAllowedMapError') != -1){
						
						title = 'Referer Not Allowed';
						
						message  = 'The current URL loading the Maps JavaScript API has not been added to the list of allowed referrers. Please check the referrer settings of your API key in your ' + GCC_link + '.<br /><br />';
						message += 'See the Maps JavaScript API and <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#restrict_key" target="_blank">Restricting API keys.</a>';
						
						note = '';
					
					/**
					 * Over Quota Error */
					 
					}else if(arguments[0].indexOf('OverQuotaMapError') != -1){
						
						title = 'Over Quota';
						
						message  = 'The number of requests has exceeded the usage limits for the Maps JavaScript API. Your app\'s requests will work again at the next daily quota reset.<br /><br />';
						message += 'If you are NOT the website owner, there are no steps you can take to fix this error. However, you may want to notify the site owner if possible.<br /><br />';
						message += 'For more details, see the guide to <a href="https://developers.google.com/maps/documentation/javascript/usage" target="_blank">usage limits</a>. The page also explains how you can get higher usage limits.';
						
						note = '';
						
					/**
					 * API Project Error */
					 
					}else if(arguments[0].indexOf('ApiProjectMapError') != -1){
						
						title = 'API Project Map Error';
						
						message  = 'Either the provided API key or the API project with which it is associated, could not be resolved. This error may be temporary. If this error message persists you may need to get a new API key or create a new API project.<br /><br />';
						message += 'If you are NOT the website owner, there are no steps you can take to fix this error. However, you may want to notify the site owner if possible.<br /><br />';
						message += '- To create an API Key, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/create-an-api-key-activate-the-required-apis/" target="_blank">link</a> for all details.<br />';
						message += '- To use the API Key with your map, visit this <a href="http://www.docs.progress-map.com/cspm_guide/first-steps/insert-the-google-maps-api-key/" target="_blank">link</a> to see where to use it.<br />';
						message += '- If you are new to Google Maps Platform, visit this <a href="https://developers.google.com/maps/gmp-get-started" target="_blank">link</a> to get started.';
						
						note = '<br /><br /><u><strong>Note:</strong></u> Once API Key created, it may take up to 5 minutes for settings to take effect!';
					
					} 
					
					if(message != ''){
						cspm_popup_msg(map_id, 'fatal_error', title, '<br />' + message + note);
					}
				
				}
				
				Array.prototype.unshift.call(arguments);
				
				/**
				 * Rewrite original console error by re-adding errors to browser console */
				 
				if(original_console_errors.apply){
					original_console_errors.apply(console, arguments); // Do this for normal browsers
				}else{
					var message = Array.prototype.slice.apply(arguments).join(' ');
					original_console_errors(message); // Do this for IE
				}
				
				/**
				 * End the Progress Bar Loader */
					
				if(typeof NProgress !== 'undefined')
					NProgress.done();
					
			}
			
		}
						
	}
	
		
	/**
	 * Customize element scrollbar
	 * @since 5.6 */
	
	function cspm_apply_custom_scrollabr(className){
		jQuery(className).mCustomScrollbar({
			autoHideScrollbar:false,
			mouseWheel:{
				enable: true,
				preventDefault: true,
			},
			theme: 'dark-2',
			live: 'on',
		});
	}


	/**
	 * Find if a polygon is clockwise or counter-clockwise.
	 * Return TRUE if Clockwise (-negative sum) & FALSE if Counter-clockwise (+positive sum).
	 *
	 * Note that the area of a convex polygon is defined to be positive if the points are ...
	 * ... arranged in a counterclockwise order, and negative if they are in clockwise order!
	 *
	 * @since 5.3
	 */
	function cspm_is_clockwise(vertices){		
		var sum = 0;
		for(var i=0; i<vertices.length-1; i++){
			var current = vertices[i],
				next = vertices[i+1];
			sum += (next[0] - current[0]) * (next[1] + current[1]);
		}
		return sum < 0;
	}
		
//=========================//
//==== Other functions ====//
//=========================//
	
	// Remove duplicated emlements from an array
	// @since 2.5
	function cspm_remove_array_duplicates(array){
		var new_array = [];
		var i = 0;
		jQuery.each(array, function(index, element){
			if(jQuery.inArray(element, new_array) === -1){
				new_array[i] = element;	
				i++;
			}
		});
		return new_array;
	}

	function cspm_object_size(obj){
			
		var size = 0, key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) size++;
		}
		return size;
					
	}

	function cspm_strpos(haystack, needle, offset) {
		
	  // From: http://phpjs.org/functions
	  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // +   improved by: Onno Marsman
	  // +   bugfixed by: Daniel Esteban
	  // +   improved by: Brett Zamir (http://brett-zamir.me)
	  // *     example 1: strpos('Kevin van Zonneveld', 'e', 5);
	  // *     returns 1: 14
	  var i = (haystack + '').indexOf(needle, (offset || 0));
	  return i === -1 ? false : i;
	  
	}

	function cspm_deg2rad(angle) {
		
	  // From: http://phpjs.org/functions
	  // +   original by: Enrique Gonzalez
	  // +     improved by: Thomas Grainger (http://graingert.co.uk)
	  // *     example 1: deg2rad(45);
	  // *     returns 1: 0.7853981633974483
	  return angle * .017453292519943295; // (angle / 180) * Math.PI;
	
	}
	
	/**
	 * Return the difference of two arrays
	 * @since 5.0 
	 */
	 function cspm_arrayDiff(arrayA, arrayB){
		return arrayA.concat(arrayB).filter(function (e, i, array) {
			// Check if the element is appearing only once
			return array.indexOf(e) === array.lastIndexOf(e);
		});
	}	

	/**
	 * Replace all SVG images with inline SVG
	 * @since 3.8 */
		 
	function cspm_convert_img_to_svg(){
		
		jQuery('img.cspm_svg').each(function(){
			
			var $img = jQuery(this);
			var imgID = $img.attr('id');
			var imgClass = $img.attr('class');
			var imgURL = $img.attr('src');
			var imgStyle = $img.attr('style');
		
			jQuery.get(imgURL, function(data) {
				// Get the SVG tag, ignore the rest
				var $svg = jQuery(data).find('svg');
		
				// Add replaced image's ID to the new SVG
				if(typeof imgID !== 'undefined') {
					$svg = $svg.attr('id', imgID);
				}
				// Add replaced image's classes to the new SVG
				if(typeof imgClass !== 'undefined') {
					$svg = $svg.attr('class', imgClass);
				}
				// Add replaced image's style to the new SVG
				if(typeof imgStyle !== 'undefined') {
					$svg = $svg.attr('style', imgStyle);
				}
		
				// Remove any invalid XML tags as per http://validator.w3.org
				$svg = $svg.removeAttr('xmlns:a');
		
				// Check if the viewport is set, if the viewport is not set the SVG wont't scale.
				if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
					$svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
				}
		
				// Replace image with new SVG
				$img.replaceWith($svg);
		
			}, 'xml');
		
		});
	}
	
	/**
	 * This will toggle the map sidebar
	 * @since 5.6.5 
	 */
	
	function cspm_toggle_map_sidebar(map_id, content_id, $this){
		
		if(typeof $this !== 'object' || typeof map_id === 'undefined' || typeof content_id === 'undefined')
			return;

        var action = $this.attr('data-action');
		var side = $this.attr('data-side');		
		
		if(typeof action === 'undefined' || typeof side === 'undefined')
			return;
		
		if(jQuery('.cspm_sidebar.'+side+'[data-map-id='+map_id+'][data-content-id='+content_id+']').length > 0){
			jQuery('.cspm_sidebar.'+side+'[data-map-id='+map_id+'][data-content-id='+content_id+']').trigger('sidebar:'+action);
		}
		
		return false;
		
	}
	
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

 	var posts_to_retrieve = {};
	
	/**
	 * Save the post_ids in the global object */
	 
	cspm_global_object.posts_to_retrieve = {};

    (function($){
		
		/**
		 * Map sidebars 
		 * @since 5.6.5 */
		 
		var cspm_sidebars = ['left', 'top', 'right', 'bottom'];	
		
		for(var i = 0; i < cspm_sidebars.length; ++i){
			var cspm_side = cspm_sidebars[i];
			if($('.cspm_sidebar.'+cspm_side).length > 0){
				$('.cspm_sidebar.'+cspm_side).sidebar({side: cspm_side});
			}
		}
			 
		$(document).on('click', '.cspm_toggle_map_side', function(){
			var map_id = $(this).attr('data-map-id');
            var content_id = $(this).attr('data-content-id');
			cspm_toggle_map_sidebar(map_id, content_id, $(this));
		});		
				
		/**
		 * Customize Checkbox and Radio buttons
		 * @since 1.0 */
		
		$.each($('form.faceted_search_form'), function(i, obj){
			
			var map_id = $(this).attr('data-map-id');
					
			/**
			 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
			 * @since 3.0 */
			var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

			if(typeof $('form.faceted_search_form[data-map-id='+map_id+'] input').iCheck === 'function'){

				if(progress_map_vars.map_script_args[map_script_id]['faceted_search_input_skin'] == 'line'){
					
					var skin_color = (progress_map_vars.map_script_args[map_script_id]['faceted_search_input_color'] != 'black') ? '-' + progress_map_vars.map_script_args[map_script_id]['faceted_search_input_color'] : '';
					
					$('form.faceted_search_form[data-map-id='+map_id+'] input').each(function(){
						
						var self = $(this),
						  label = self.next(),
						  label_text = label.text();
						
						label.remove();
						self.iCheck({
							checkboxClass: 'icheckbox_line' + skin_color,
							radioClass: 'iradio_line' + skin_color,
							insert: '<div class="icheck_line-icon"></div>' + label_text,
							inheritClass: true
						});
					
					});
					
				}else{
					
					if(progress_map_vars.map_script_args[map_script_id]['faceted_search_input_skin'] != 'polaris' && progress_map_vars.map_script_args[map_script_id]['faceted_search_input_skin'] != 'futurico')
						var skin_color = (progress_map_vars.map_script_args[map_script_id]['faceted_search_input_color'] != 'black') ? '-' + progress_map_vars.map_script_args[map_script_id]['faceted_search_input_color'] : '';
					else var skin_color = '';

					$('form.faceted_search_form[data-map-id='+map_id+'] input').iCheck({
						checkboxClass: 'icheckbox_' + progress_map_vars.map_script_args[map_script_id]['faceted_search_input_skin'] + skin_color,
						radioClass: 'iradio_' + progress_map_vars.map_script_args[map_script_id]['faceted_search_input_skin'] + skin_color,
						increaseArea: '20%',
						inheritClass: true
					});
				
				}
		
			}
		
		});
	
		/**
		 * Faceted search
		 * @edited 5.5 */
		
        $(document).on('click', 'div.faceted_search_btn', function(){ //@edited 5.6.3

			var map_id = $(this).attr('id');
		
			$('div.cspm_top_element:not(.faceted_search_container_'+map_id+')').hide();
			
			$('div.faceted_search_container_'+map_id).slideToggle(0, 'easeOutCirc', function(){
				
				if($(this).is(':visible')){
					
					/**
					 * Call custom scroll bar for faceted search list */
												
					if(typeof jQuery('div.faceted_search_container_'+map_id).mCustomScrollbar === 'function'){
														
						$("div[class^=faceted_search_container] form.faceted_search_form ul").mCustomScrollbar("destroy");
						$("div[class^=faceted_search_container] form.faceted_search_form ul").mCustomScrollbar({
							autoHideScrollbar:false,
							mouseWheel:{
								enable: true,
								preventDefault: true,
							},
							theme: 'dark-2',
						});
						
					}
					
				}
				
			});			
			
		});

        $(document).on('click', 'a[class^=reset_map_list]', function(e){ //@edited 5.6.3
			
			e.preventDefault(); //@added 5.5
			
			var map_id = $(this).attr('id');

			var icheck_selector = $('form#faceted_search_form_'+map_id+' input');
			
			if(typeof icheck_selector.iCheck === 'function')
				icheck_selector.iCheck('uncheck');
			
			$(this).hide();
			
			/**
			 * Reset nearby points of interest */
			
			$('a.cspm_reset_proximities[data-map-id='+map_id+']').trigger('click'); //@edited 5.5
			
		});
		
		var change_event = (typeof $('form.faceted_search_form input').iCheck === 'function') ? 'ifChanged' : 'change';
		
		$('form.faceted_search_form input').on(change_event, function(event){
			
			var map_id = $(this).attr('data-map-id');
			var show_carousel = $(this).attr('data-show-carousel');
			var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
					
			/**
			 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
			 * @since 3.0 */
			var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';
			
			var multi_taxonomy_option = progress_map_vars.map_script_args[map_script_id]['faceted_search_multi_taxonomy_option'];
				
			if(typeof NProgress !== 'undefined'){
				
				NProgress.configure({
				  parent: 'div#codespacing_progress_map_div_'+map_id,
				  showSpinner: true
				});				
				
				NProgress.start();
				
			}
			
			/**
			 * Hide all markers */
			 
			cspm_hide_all_markers(plugin_map, map_id).done(function(){
				
				if(typeof NProgress !== 'undefined')
					NProgress.set(0.5);
				
				if(multi_taxonomy_option == 'false')
					$('a.reset_map_list_'+map_id).show(); //@edited 5.5
				
				posts_to_retrieve[map_id] = [];
				var retrieved_posts = [];
				var i = 0;
				var j = 0;
				var num_checked = 0;
				var count_posts = 0;					
				
				/**
				 * Loop throught checkboxes/radios and get the one(s) selected, then, display related markers */
						
				$('div.faceted_search_container_'+map_id+' form.faceted_search_form input').each(function(){
	
					if($(this).prop('checked') == true){ 
						
						num_checked++;

						var value = $(this).val();

						/**
						 * Loop throught post_ids and check its relation with the current category
						 * Then show markers within the selected category */
						 
						retrieved_posts = cspm_remove_array_duplicates(retrieved_posts.concat(cspm_set_markers_visibility(plugin_map, map_id, value, j, post_lat_lng_coords[map_id], posts_to_retrieve[map_id], true)));

						cspm_simple_clustering(plugin_map, map_id);
						
						i++;
	
					}
					
				});
				
				posts_to_retrieve[map_id] = retrieved_posts;
				
				/**
				 * Show all markers when there is no checked category */
				 
				if(num_checked == 0){
					
					var j = 0;
			
					if(progress_map_vars.map_script_args[map_script_id]['map_type'] == 'search_map'){
						cspm_hide_all_markers(plugin_map, map_id);
					}else{
						cspm_set_markers_visibility(plugin_map, map_id, null, j, post_lat_lng_coords[map_id], posts_to_retrieve[map_id], false);
						cspm_simple_clustering(plugin_map, map_id);
					}
						
				}
									
				/**
				 * Center the map on the first post in the array 
				 * @since 2.8
				 * @updated 2.8.2 */
											
				var marker_objs_length = count_posts = cspm_object_size(posts_to_retrieve[map_id]);

				if(progress_map_vars.map_script_args[map_script_id]['faceted_search_drag_map'] == 'autofit'){
					
					setTimeout(function(){
						
						var markers_bounds = new google.maps.LatLngBounds();
						
						plugin_map.gmap3({
							get: {
								name: 'marker',
								tag: 'to_cluster',
								all: true,
								callback: function(markers){
									
									if(!markers)
										return;
									
									var visible_markers = 0;
										
									$.each(markers, function(i, marker){
										
										var marker_id = marker.id;
										
										if(typeof marker !== 'undefined' && marker.visible == true && marker_id != 'user_location'){
											markers_bounds.extend(marker.getPosition());
											visible_markers++;
										}

										if(i == (markers.length-1)){
											setTimeout(function(){								
												if(visible_markers > 0){
													plugin_map.gmap3('get').fitBounds(markers_bounds);
												}else{
													cspm_recenter_map(plugin_map, map_id);
												}
												cspm_zoom_in_and_out(plugin_map);						
											}, 100);
										}

									});

								}
							}
						});
						
					}, 100);
				
				}else if(progress_map_vars.map_script_args[map_script_id]['faceted_search_drag_map'] == 'drag'){
					
					if(marker_objs_length > 0){
						var first_post_latlng = post_lat_lng_coords[map_id][posts_to_retrieve[map_id][0]].split('_');
						cspm_center_map_at_point(plugin_map, map_id, first_post_latlng[0], first_post_latlng[1], 'zoom');
					}
					
				}
								
				/**
				 * Save the post_ids in the global object.
				 * This is usefull when using the plugin with an extension */
				 
				cspm_global_object.posts_to_retrieve[map_id] = posts_to_retrieve[map_id];																											
	
				if(progress_map_vars.map_script_args[map_script_id]['show_posts_count'] == "yes")
					$('span.the_count_'+map_id).empty().html(count_posts);

				/**
				 * Rewrite Carousel */
				
				if(progress_map_vars.map_script_args[map_script_id]['sync_carousel_to_viewport'] == 'no'){ //@since 5.0
					setTimeout(function(){
						cspm_rewrite_carousel(map_id, show_carousel, posts_to_retrieve[map_id]);
					}, 100);
				}
			
				/**
				 * Hide the Countries list & Remove hover style on Countries list just in case a user ...
				 * ... already choosed a country then decided they want to filter the map
				 * @edited 5.5 */
				
				if($('div.countries_container_'+map_id).length){
					$('li.cspm_country_name[data-map-id='+map_id+']').removeClass('selected');
					$('div.countries_container_'+map_id).hide();
				}
						
				/**
				 * Reset nearby points of interest */
				
				$('a.cspm_reset_proximities[data-map-id='+map_id+']').trigger('click'); //@edited 5.5
			
				/**
				 * End the Progress Bar Loader */
					
				if(typeof NProgress !== 'undefined')
					NProgress.done();
				
			});
				
		});
	
		// @Facetd search ====				
			
		// The event handler of the carousel items
		
        $(document).on('click', 'ul[id^=cspm_carousel_] li', function(){ //@edited 5.6.3
			
			var map_id = $(this).attr('data-map-id');
			
			if(map_layout[map_id] != 'fullscreen-map' && map_layout[map_id] != 'fit-in-map'){
					
				/**
				 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
				 * @since 3.0 */
				var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';
								
				var carousel = $('ul#cspm_carousel_'+map_id).data('jcarousel');				
				var item_index = $(this).attr('jcarouselindex'); //@edited 5.0		
				var target_map = (progress_map_vars.map_script_args[map_script_id]['connect_carousel_with_map'] == 'yes') ? true : false; //@since 5.0
				
				// Move the clicked carousel item to the first position
				cspm_call_carousel_item(carousel, item_index);				
				cspm_carousel_item_request(carousel, parseInt(item_index), target_map);
				
			}
			
		}).css('cursor','pointer');
		
		// @Event handler
		
		
		// Search form request
	
		/**
		 * Customize the text box "radius" to slider
		 * @edited 5.6 */
		
		if(typeof $("input.cspm_sf_slider_range").ionRangeSlider === 'function'){
				
			$("input.cspm_sf_slider_range").ionRangeSlider({
				type: 'single',
				skin: 'round', //@since 5.6
				grid: true,
			});
		
		}
		
		/**
		 * Load the search form to the screen
		 * @edited 5.6.3 */
		 
        $(document).on('click', 'div.search_form_btn', function(event){ //@edited 5.6.3

			var map_id = $(this).attr('id');
								
			$('div.cspm_top_element:not(.search_form_container_'+map_id+')').hide();
			
            $('div.search_form_container_'+map_id).slideToggle(0, 'easeOutCirc', function(){
                if($(this).is(':visible')){
                    if(typeof event.originalEvent !== 'undefined'){
                        $('form#search_form_'+map_id+' input[name=cspm_address]').trigger('focus'); //@edited 5.6.3											
                    }
                }
            });
					
		});
		
		/**
		 * Submit the search form data */
		
        $(document).on('click', 'a.cspm_geotarget_search_btn', function(e){ //@edited 5.6.3
			
			e.preventDefault(); //@since 5.5
			
			var map_id = $(this).attr('data-map-id');
				        
			/**
			 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
			 * @since 3.0 */
			var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

			if(typeof NProgress !== 'undefined'){
				NProgress.configure({
				  parent: 'div#codespacing_progress_map_div_'+map_id,
				  showSpinner: true
				});				
				NProgress.start();			
			}
					
			var saved_latLng = $('a.cspm_geotarget_search_btn[data-map-id='+map_id+']').attr('data-latLng'); //@edited 5.5
			
			var plugin_map = $('div#codespacing_progress_map_div_'+map_id);

			plugin_map.gmap3({
			  getgeoloc:{
				callback : function(latLng){
				  if(latLng){
					
					var lat = latLng.lat();
					var lng = latLng.lng();
					
					if(saved_latLng != lat+','+lng){

						plugin_map.gmap3({
						  getaddress:{
							latLng: latLng,
							callback: function(results){
								var address = (results && results[1]) ? results && results[1].formatted_address : latLng.lat()+','+latLng.lng();
								$('form#search_form_'+map_id+' input[name=cspm_address]').val(address);
								var message = '<strong>Your address:</strong> '+address;
								message += '<br /><strong>Your position:</strong> '+latLng.lat()+','+latLng.lng();
								cspm_popup_msg(map_id, 'info', '',  message);
								$('a.cspm_geotarget_search_btn[data-map-id='+map_id+']').attr('data-latLng', lat+','+lng); //@edited 5.5
							}
						  }
						});	
						
					}
					
				  }else{
					
					/**
					 * More info at https://support.google.com/maps/answer/152197
					 * Deprecate Geolocation API: https://developers.google.com/web/updates/2016/04/geolocation-on-secure-contexts-only */
						
					var title = progress_map_vars.geoErrorTitle;
					var message = progress_map_vars.geoErrorMsg;
					
					if(!cspm_popup_msg(map_id, 'error', title, '<br />' + message))
						alert(title + '\n\n' + message);
					
					console.log('PROGRESS MAP: ' + progress_map_vars.geoDeprecateMsg);
					  
				  }
				}
			  }
			});
			
			if(typeof NProgress !== 'undefined')
				NProgress.done();
				
		});
		 
        $(document).on('click', 'a[class^=cspm_submit_search_form_]', function(e){ //@edited 5.6.3
			
			e.preventDefault(); //@since 5.5
			
			var map_id = $(this).attr('data-map-id');
			var open_btn = $(this).attr('data-open-btn'); //@since 5.6.5 | We use this to determine if the search form is displayed outside the map
			
			/**
			 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
			 * @since 3.0 */
			var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

			if(typeof NProgress !== 'undefined'){
				
				NProgress.configure({
				  parent: 'div#codespacing_progress_map_div_'+map_id,
				  showSpinner: true
				});				
			
				NProgress.start();
			
			}
			
			cspm_close_popup_msg(map_id, 'error');				
			
			var show_carousel = $(this).attr('data-show-carousel');
			var address = progress_map_vars.map_script_args[map_script_id]['before_search_address'] + $('form#search_form_'+map_id+' input[name=cspm_address]').val() + progress_map_vars.map_script_args[map_script_id]['after_search_address'];
			
			var draw_circle = progress_map_vars.map_script_args[map_script_id]['sf_circle_option'] == 'true' ? true : false;
			var edit_circle = progress_map_vars.map_script_args[map_script_id]['sf_edit_circle'] == 'true' ? true : false;
			
			/**
			 * Distance in KM or Miles */
			
			var decimals_distance = $('form#search_form_'+map_id+' input[name=cspm_decimals_distance]').val();
			
			if(decimals_distance != '' && typeof e.isTrigger !== 'undefined'){
				var distance = decimals_distance;
			}else var distance = $('form#search_form_'+map_id+' input[name=cspm_distance]').val();
			 
			var distance_unit = $('form#search_form_'+map_id+' input[name=cspm_distance_unit]').val();
			
				/**
				 * [@distance_in_meters] Distance converted to meters */
				 
				if(distance_unit == "metric")
					var distance_in_meters = distance * 1000; // 1KM = 1000Meter
				else var distance_in_meters = (distance * 1.60934) * 1000; // 1Mile = 1.60934KM = 1609.34Meter
			
			/**
			 * Min & Max Distances in KM or Miles */
			 
			var min_distance = $('form#search_form_'+map_id+' input[name=cspm_distance]').attr('data-min');
			var max_distance = $('form#search_form_'+map_id+' input[name=cspm_distance]').attr('data-max');
			
				/**
				 * [@min_distance_in_meters] Min Distance converted to meters
				 * [@max_distance_in_meters] Max Distance converted to meters */
				 
				if(distance_unit == "metric"){
					var min_distance_in_meters = min_distance * 1000; // 1KM = 1000Meter
					var max_distance_in_meters = max_distance * 1000; // 1KM = 1000Meter
				}else{
					var min_distance_in_meters = (min_distance * 1.60934) * 1000; // 1Mile = 1.60934KM = 1609.34Meter
					var max_distance_in_meters = (max_distance * 1.60934) * 1000; // 1Mile = 1.60934KM = 1609.34Meter
				}

			var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
			
			var posts_in_search = {};				
			posts_in_search[map_id] = [];

			var geocoder = new google.maps.Geocoder();
			
			/**
			 * Convert our address to Lat & Long */
			 
			geocoder.geocode({ 'address': address }, function (results, status) {
				
				/**
				 * If the address is found */
				 
				if(status == google.maps.GeocoderStatus.OK){
					
					var latLng = results[0].geometry.location; //@since 5.6.5
					var latitude = latLng.lat(); //@edited 5.6.5
					var longitude = latLng.lng(); //@edited 5.6.5
									
					plugin_map.gmap3({
						get: {
							name: 'marker',
							tag: 'to_cluster',
							all:  true,
							callback: function(objs) {
								
								var j = 0;
								
								/**
								 * Get all markers inside the radius of our address */
								 
								$.each(objs, function(i, obj) {									
									
									var marker_id = obj.id;
									
									/**
									 * Convert the LatLng object to array */
									 
									var post_latlng = obj.position;
									
									/**
									 * Compute the distance and save the post_id */

									if(marker_id != 'user_location' && cspm_compute_distance_between(latitude, longitude, post_latlng.lat(), post_latlng.lng()) < distance_in_meters){
										posts_in_search[map_id][j] = parseInt(marker_id);													
										j++;
									}
									
								});
								
								/**
								 * If one or more posts were found within the radius area */
								 
								if(cspm_object_size(posts_in_search[map_id]) > 0){
									
									if(open_btn == 'yes'){
										$('div.search_form_container_'+map_id).hide();
									} //@edited 5.6.5 | Will make sure not to hide the search form when displayed outside the map

									/**
									 * Hide all markers */
									 
									cspm_hide_all_markers(plugin_map, map_id).done(function(){
				
										/**
										 * Center the map in our address position */
										 
										cspm_center_map_at_point(plugin_map, map_id, latitude, longitude, 'zoom');										
						
										/**
										 * Loop throught post_ids and check the post relation with the current category
										 * Then show markers within the selected category */
										 
										cspm_set_markers_visibility(plugin_map, map_id, null, 0, post_lat_lng_coords[map_id], posts_in_search[map_id], true);
										cspm_simple_clustering(plugin_map, map_id);

										/**
										 * Save the post_ids in the global object
										 * This is usefull when using the plugin with an extension */
										 
										cspm_global_object.posts_to_retrieve[map_id] = posts_in_search[map_id];																																						
										
										if(draw_circle){
											
											var circle_radius = distance_in_meters;										

											/**
											 * [@circle_mouseover_count] | Count the mouseover events so we can decide when to display the ...
											 * ... pop-up message that informs the user that they can reize the circle.
											 * @since 3.5 */
											
											var circle_mouseover_count = 0;
											
											plugin_map.gmap3({
												clear: {
													tag: "search_circle",
													all: true
												},
												circle:{
													tag: 'search_circle',
													options:{
														center: [latitude, longitude],
														radius : circle_radius, // The radius in meters on the Earth's surface
														fillColor : progress_map_vars.map_script_args[map_script_id]['fillColor'],
														fillOpacity: progress_map_vars.map_script_args[map_script_id]['fillOpacity'],
														strokeColor : progress_map_vars.map_script_args[map_script_id]['strokeColor'],
														strokeOpacity: progress_map_vars.map_script_args[map_script_id]['strokeOpacity'],
														strokeWeight: parseInt(progress_map_vars.map_script_args[map_script_id]['strokeWeight']),
														editable: edit_circle,
													},
													callback: function(circle){
														plugin_map.gmap3('get').fitBounds(circle.getBounds());														
													},
													events:{
														mouseover: function(circle){
															
															/**
															 * Inform the user that they can resize the circle ...
															 * ... to show more or less results.
															 * @since 3.5 */
															
															if(edit_circle && (circle_mouseover_count == 0 || circle_mouseover_count == 6)){
																
																var message = progress_map_vars.resize_circle_msg;
																cspm_popup_msg(map_id, 'info', '',  message);
																
																if(circle_mouseover_count == 6)
																	circle_mouseover_count = 1;
																	
															}
															
															circle_mouseover_count++;
															
														},
														mouseout: function(circle){
																														
															/**
															 * Close the info message
															 * @since 3.5 */
															
															if(edit_circle)
																cspm_close_popup_msg(map_id, 'info');

														},
														radius_changed: function(circle){
															
															if(edit_circle){
																															
																/**
																 * Close the info message
																 * @since 3.5 */
															
																cspm_close_popup_msg(map_id, 'info');

																/**
																 * Get circle radius */
																 
																var radius_in_meters = circle.getRadius();
																
																/**
																 * Compare circle radius to min and max distances and redraw circle if needs to */
																
																var readable_distance_unit = (distance_unit == 'metric') ? 'Km' : 'Miles';
																
																if(radius_in_meters > max_distance_in_meters){
																	
																	circle.setRadius(max_distance_in_meters);
																	
																	var message = progress_map_vars.circle_reached_max_msg+' ('+max_distance+readable_distance_unit+')';
																	cspm_popup_msg(map_id, 'warning', '', message);
																	
																}else if(radius_in_meters < min_distance_in_meters){
																	
																	circle.setRadius(min_distance_in_meters);
																	
																	var message = progress_map_vars.circle_reached_min_msg+' ('+min_distance+readable_distance_unit+')';
																	cspm_popup_msg(map_id, 'warning', '', message);
																	
																}
															
																setTimeout(function(){
																
																	var radius_in_meters = circle.getRadius();

																	if(distance_unit == "metric")
																		var radius_in_distance_unit = (radius_in_meters / 1000);
																	else var radius_in_distance_unit = (radius_in_meters / 1609.34);
																																
																	$('form#search_form_'+map_id+' input[name=cspm_decimals_distance]').attr('value', radius_in_distance_unit);
																				
																	var distance_slider = $('input.cspm_sf_slider_range[data-map-id='+map_id+']').data('ionRangeSlider');

																	distance_slider.update({
																		from: radius_in_distance_unit
																	});

																	cspm_hide_all_markers(plugin_map, map_id).done(function(){
																		$('a.cspm_submit_search_form_'+map_id).trigger('click'); //@edited 5.5
																		cspm_zoom_in_and_out(plugin_map);
																	});
																	
																}, 500);
																
															}
																												
														}
													}
												}											
											});
											
										}
										
										/**
										 * Show the reset button */
										 
										$('a.cspm_reset_search_form_'+map_id).show(); //@edited 5.5
				
										/**
										 * Reload post count value */
										 
										if(progress_map_vars.map_script_args[map_script_id]['show_posts_count'] == "yes")
											$('span.the_count_'+map_id).empty().html(posts_in_search[map_id].length);
				
										/**
										 * Rewrite Carousel */
				
										if(progress_map_vars.map_script_args[map_script_id]['sync_carousel_to_viewport'] == 'no'){ //@since 5.0
											setTimeout(function(){
												cspm_rewrite_carousel(map_id, show_carousel, posts_in_search[map_id]);
											}, 500);
										}
			
									});
								
								/**
								 * No post found */
								 	
								}else{
									
									var message = progress_map_vars.map_script_args[map_script_id]['no_location_found_msg'];
									cspm_popup_msg(map_id, 'warning', '', message);		
									
									/**
									 * If circle resized but no locations found */
									 
									if(decimals_distance != '' && draw_circle && edit_circle){
										
										$('form#search_form_'+map_id+' input[name=cspm_decimals_distance]').val('');							
				
										/**
										 * Reload post count value */
										 
										if(progress_map_vars.map_script_args[map_script_id]['show_posts_count'] == "yes")
											$('span.the_count_'+map_id).empty().html(posts_in_search[map_id].length);
				
										/**
										 * Rewrite Carousel */
				
										if(progress_map_vars.map_script_args[map_script_id]['sync_carousel_to_viewport'] == 'no'){ //@since 5.0
											setTimeout(function(){
												cspm_rewrite_carousel(map_id, show_carousel, posts_in_search[map_id]);
											}, 500);
										}
			
									}
		
								}

							}
						}
					});
				
				/**
				 * The address is not found */
				 
				}else{
					
					if(status == google.maps.GeocoderStatus.ZERO_RESULTS){
										
						var title = progress_map_vars.map_script_args[map_script_id]['bad_address_msg'];
						
						var sug_1 = progress_map_vars.map_script_args[map_script_id]['bad_address_sug_1'];
						var sug_2 = progress_map_vars.map_script_args[map_script_id]['bad_address_sug_2'];
						var sug_3 = progress_map_vars.map_script_args[map_script_id]['bad_address_sug_3'];										
						
						var message = '<br />' + sug_1 + '<br />' + sug_2 + '<br />' + sug_3;
							
						if(!cspm_popup_msg(map_id, 'error', title, message))
							alert(title + '\n\n' + sug_1 + '\n' + sug_2 + '\n' + sug_3);

					}else{
						var api_status_codes = progress_map_vars.geocoding_api_status_codes; //@since 3.9
						cspm_api_status_info(map_id, api_status_codes, status, 'Geocoding API'); //@since 3.9
					}
					
				}
			
				/**
				 * Hide the Countries list & Remove hover style on Countries list just in case a user ...
				 * ... already choosed a country then decided they want to search the map
				 * @edited 5.5 */
				
				if($('div.countries_container_'+map_id).length){
					$('li.cspm_country_name[data-map-id='+map_id+']').removeClass('selected');
					$('div.countries_container_'+map_id).hide();
				}
			
				/**
				 * Reset nearby points of interest */
				
				$('a.cspm_reset_proximities[data-map-id='+map_id+']').trigger('click'); //@edited 5.5
			
				if(typeof NProgress !== 'undefined')
					NProgress.done();
				
			});
			
		});
		
		/**
		 * Reset the search form & the map */
		 
        $(document).on('click', 'a[class^=cspm_reset_search_form]', function(e){ //@edited 5.6.3
			
			e.preventDefault(); //@since 5.5
			
			var map_id = $(this).attr('data-map-id');
					
			/**
			 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
			 * @since 3.0 */
			var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

			if(typeof NProgress !== 'undefined'){

				NProgress.configure({
				  parent: 'div#codespacing_progress_map_div_'+map_id,
				  showSpinner: true
				});				
				
				NProgress.start();
				
			}
							
			var show_carousel = $(this).attr('data-show-carousel');
			var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
			
			posts_to_retrieve[map_id] = [];
			
			if(progress_map_vars.map_script_args[map_script_id]['map_type'] == 'search_map'){
				cspm_hide_all_markers(plugin_map, map_id);
			}else{
				cspm_set_markers_visibility(plugin_map, map_id, null, 0, post_lat_lng_coords[map_id], posts_to_retrieve[map_id], false);
				cspm_simple_clustering(plugin_map, map_id);
			}
			
			/**
			 * Save the post_ids in the global object
			 * This is usefull when using the plugin with an extension */
			 
			cspm_global_object.posts_to_retrieve[map_id] = posts_to_retrieve[map_id];																											
										
			if(progress_map_vars.map_script_args[map_script_id]['show_posts_count'] == "yes")
				$('span.the_count_'+map_id).empty().html(posts_to_retrieve[map_id].length);
			
			if(typeof NProgress !== 'undefined')
				NProgress.set(0.5);
								
			plugin_map.gmap3({
				clear: {
					tag: "search_circle",
					all: true
				},
			});
        	
			cspm_recenter_map(plugin_map, map_id);						
			cspm_zoom_in_and_out(plugin_map); // Fix issue whith clusters when current map center equals initial map center
			
			$('form#search_form_'+map_id+' input#cspm_address_'+map_id).val('').trigger('focus'); //@edited 5.6.3
				
			/**
			 * Rewrite Carousel */
				
			if(progress_map_vars.map_script_args[map_script_id]['sync_carousel_to_viewport'] == 'no'){ //@since 5.0
				setTimeout(function(){
					cspm_rewrite_carousel(map_id, show_carousel, posts_to_retrieve[map_id]);
				}, 500);
			}
						
			/**
			 * Hide the Countries list & Remove hover style on Countries list just in case a user ...
			 * ... already choosed a country then decided they want to search the map
			 * @edited 5.5 */
			
			if($('div.countries_container_'+map_id).length){
				$('li.cspm_country_name[data-map-id='+map_id+']').removeClass('selected');
				$('div.countries_container_'+map_id).hide();
			}
			
			/**
			 * Reset nearby points of interest */
			
			$('a.cspm_reset_proximities[data-map-id='+map_id+']').trigger('click'); //@edited 5.5
			
			$(this).removeClass('fadeIn').hide('fast', function(){
				if(typeof NProgress !== 'undefined')
					NProgress.done();	
			});
					
		});
		
		// @Search form request
		
		
		// Toogle the carousel
				
        $(document).on('click', 'div.toggle-carousel-bottom, div.toggle-carousel-top', function(){ //@edited 5.6.3
			
			var map_id = $(this).attr('data-map-id');
			
			$('div#cspm_carousel_container[data-map-id='+map_id+']').slideToggle("slow", function(){
			
				cspm_init_carousel(null, map_id);
				
			});
			
		});								
		
		// Frontend Form @since 2.6.3
		
        $(document).on('click', 'button#cspm_search_btn', function(){ //@edited 5.6.3
			
			var map_id = $(this).attr('data-map-id');
			var address = $('input[name=cspm_search_address]').val();

			var plugin_map = $('div#cspm_frontend_form_'+map_id);
			
			plugin_map.gmap3({
				clear: {
					name:"marker",
					all: true
				},
			});

			var geocoder = new google.maps.Geocoder();
			
			// Convert our address to Lat & Lng
			geocoder.geocode({ 'address': address }, function (results, status) {
				
				// If the address was found
				if (status == google.maps.GeocoderStatus.OK) {

					var latitude = results[0].geometry.location.lat();
					var longitude = results[0].geometry.location.lng();
					
					setTimeout(function(){
						// Center the map in our address position
						cspm_center_map_at_point(plugin_map, map_id, latitude, longitude, 'zoom');
						
						plugin_map.gmap3({							
							marker:{
							  latLng:results[0].geometry.location,
							  options:{
							  	draggable: true,
							  }
							}
						});
					}, 500);
					
				}else{
					var api_status_codes = progress_map_vars.geocoding_api_status_codes; //@since 3.9
					cspm_api_status_info(map_id, api_status_codes, status, 'Geocoding API'); //@since 3.9
				}
				
			});
			
		});
		
        $(document).on('keypress', 'input#cspm_search_address', function(e){ //@edited 5.6.3
			if (e.keyCode == 13) {
				e.preventDefault();
			}
		});
				  
        $(document).on('click', 'button#cspm_get_pinpoint', function(){ //@edited 5.6.3
			
			var map_id = $(this).attr('data-map-id');
							
			var plugin_map = $('div#cspm_frontend_form_'+map_id);
			
			plugin_map.gmap3({
			  get: {
				name:"marker",
				callback: function(marker){
					if(marker){
						$("input#cspm_latitude").val(marker.getPosition().lat());
						$("input#cspm_longitude").val(marker.getPosition().lng());
					}
				}
			  }
			});
			
		});
				
		// @Frontend Form		
					
		/**
		 * Geolocalization
		 *
		 * @since 2.8
		 * @updated 5.6.5 */
		
        $(document).on('click', 'div.cspm_geotarget_btn', function(){ //@edited 5.6.3
			
			var map_id = $(this).attr('data-map-id');
				        
			/**
			 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
			 * @since 3.0 */
			var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';

			var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
			
			cspm_geolocate(
				plugin_map, 
				map_id, 
				progress_map_vars.map_script_args[map_script_id]['show_user'], 
				progress_map_vars.map_script_args[map_script_id]['user_marker_icon'], 
				progress_map_vars.map_script_args[map_script_id]['user_marker_size'], 
				progress_map_vars.map_script_args[map_script_id]['user_circle'], 
				progress_map_vars.map_script_args[map_script_id]['user_map_zoom'], 
				true
			); //@edited 5.6.5
			
		});
		
		
		/**
		 * Zoom to country
		 *
		 * @since 3.0 
		 * @updated 5.6 | 5.6.3
		 */
		
		var countries_list = {}; //@edited 5.6.3
		
        $(document).on('click', 'div.countries_btn', function(){ //@edited 5.6.3

			var map_id = $(this).attr('data-map-id');
								
			$('div.cspm_top_element:not(.countries_container_'+map_id+')').hide();
			
			$('div.countries_container_'+map_id).slideToggle(0, 'easeOutCirc', function(){
				
				var $countries_container = $(this);
				
				if($(this).is(':visible')){
					
					var search_input = $(this).attr('data-search-input');
					var show_flag = $(this).attr('data-show-flag');
		
					if(typeof countries_list[map_id] === 'undefined'){

						countries_list[map_id] = tail.select('.cspm_countries_list[data-map-id='+map_id+']', {
							classNames: 'cspm_custom_tailselect',
							multiple: false,
							multiShowCount: false,
							multiSelectAll: false,
							startOpen: true,
							stayOpen: true,
							openAbove: false,
							height: 215,
							search: (search_input == 'yes') ? ((show_flag == 'only') ? false : true) : false,
							searchMarked: true,
							width: (show_flag == 'only') ? 55 : null, 
							deselect: true,
							hideSelected: true,
							cbLoopItem: function(item, group, search){
								// Create LI element
								var li = document.createElement('li');
								// Add option attributes to the LI element
								if(item.option.hasAttributes()){
								   var attrs = item.option.attributes;
								   for(var i = attrs.length - 1; i >= 0; i--) {
									   li.setAttribute(attrs[i].name, attrs[i].value);
								   }
								}
								// Get the element data
								var item_key = item.key;
								var country_name = li.getAttribute('data-country-name');
								var country_code = li.getAttribute('data-country-code');
								// Assign element class names
								li.className = 'dropdown-option cspm_dropdown_no_icon' + (item.selected ? ' selected' : '') + (item.disabled ? ' disabled' : '');
								if(search && search.length > 0 && this.con.searchMarked){
									country_name = country_name.replace(new RegExp('(' + search + ')', 'i'), '<mark>$1</mark>');
								}
								li.innerHTML = '<div class="item-inner">';							
									if(jQuery.inArray(show_flag, ['true', 'only']) !== -1){
										 li.innerHTML += '<div class="cspm_flg_icon cspm_'+country_code+'"></div>';
									}
									if(show_flag != 'only'){
										li.innerHTML += country_name;
									}
								li.innerHTML += '</div>';													
								return li;
							},
							cbComplete: function(){
								$countries_container.find('input.search-input[type=text]').attr('placeholder', progress_map_vars.type_to_search);								
								cspm_apply_custom_scrollabr('.cspm_custom_tailselect.tail-select .select-dropdown .dropdown-inner');
							}
						}).on('change', function(item, state){
							
							if(state != 'select')
								return;
	
							var country_name = $(item.option).attr('data-country-name');
							var country_code = $(item.option).attr('data-country-code');
							
							var custom_label = '';
							
							if(jQuery.inArray(show_flag, ['true', 'only']) !== -1)
								custom_label = '<div class="cspm_flg_icon cspm_'+country_code+'"></div>';
							
							if(show_flag != 'only')
								custom_label += country_name;
								
							countries_list[map_id].updateLabel(custom_label);
				 
							$('div.countries_container_'+map_id).hide();
							
							if(typeof NProgress !== 'undefined'){
								NProgress.configure({
								  parent: 'div#codespacing_progress_map_div_'+map_id,
								  showSpinner: true
								});				
								NProgress.start();
							}								
						
							/**
							 * [@map_script_id] | Used only to get map options from the PHP option wp_localize_script()
							 * @since 3.0 */
							var map_script_id = (typeof progress_map_vars.map_script_args[map_id] !== 'undefined') ? map_id : 'initial';
							
							var zoom_or_autofit = progress_map_vars.map_script_args[map_script_id]['country_zoom_or_autofit'];
							
							var autofit = (zoom_or_autofit == 'autofit') ? true : false;
							
							var map_zoom = progress_map_vars.map_script_args[map_script_id]['country_zoom_level'];
						
							var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
											
							if(typeof NProgress !== 'undefined')
								NProgress.set(0.5);
				
							cspm_zoom_to_address(map_id, plugin_map, country_name, autofit, map_zoom);
				
							if(typeof NProgress !== 'undefined')
								NProgress.done();
							
						});											
					
					}
					
				}
				
			});			
					
		});
		 
		
		/**
		 * Recenter the map
		 *
		 * @since 3.0 
		 */
		
        $(document).on('click', 'div.cspm_recenter_map_btn', function(){ //@edited 5.6.3

			var map_id = $(this).attr('data-map-id');
		
			var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
			
			if(typeof NProgress !== 'undefined'){

				NProgress.configure({
				  parent: 'div#codespacing_progress_map_div_'+map_id,
				  showSpinner: true
				});				
				
				NProgress.start();
				
			}								
        	
			cspm_recenter_map(plugin_map, map_id);						
			cspm_zoom_in_and_out(plugin_map); // Fix issue whith clusters when current map center equals initial map center
			
			/**
			 * Hide the Countries list & Remove hover style on Countries list just in case a user ...
			 * ... already choosed a country then decided they want to recenter the map
			 * @edited 5.5 */
			
			if($('div.countries_container_'+map_id).length){
				$('li.cspm_country_name[data-map-id='+map_id+']').removeClass('selected');
				$('div.countries_container_'+map_id).hide();
			}
			
			if(typeof NProgress !== 'undefined')
				NProgress.done();	

		});
		
		
		/**
		 * Init Directions Display */
		 
		var DirectionsDisplay = {};
		
		/**
		 * Display nearby points of interest */
		 
		var cspm_find_nearby_places = function(listObj, listItemObj, proximity_id, callback){

			var map_id = listItemObj.attr('data-map-id');

			var plugin_map = jQuery('div#codespacing_progress_map_div_'+map_id);
			var mapObject = plugin_map.gmap3('get');
			
			/**
			 * Get all data needed to get proximities */
			
			var $elementObj = $('div.cspm_proximities_container_'+map_id).find('li.cspm_proximity_name[data-map-id='+map_id+'][data-proximity-id='+proximity_id+']');
			
			//var proximity_id = $elementObj.attr('data-proximity-id');
			var proximity_name = $elementObj.attr('data-proximity-name');				
			var distance_unit = $elementObj.attr('data-distance-unit');	
			var radius = $elementObj.attr('data-radius');
			var initial_radius = $elementObj.attr('data-initial-radius');
			var travel_mode = $elementObj.attr('data-travel-mode');	
			var draw_circle_attr = $elementObj.attr('data-draw-circle');
				var draw_circle = (draw_circle_attr == 'true') ? true : false;
			var edit_circle_attr = $elementObj.attr('data-edit-circle');
				var edit_circle = (edit_circle_attr == 'true') ? true : false;
			var marker_type = $elementObj.attr('data-marker-type');
			var target_marker_latlng = $elementObj.attr('data-marker-latlng');			        		

			/**
			 * Min & Max Radius in meters */
			
			var min_radius = $elementObj.attr('data-min-radius');
			var max_radius = $elementObj.attr('data-max-radius');	
			
				/**
				 * [@radius_in_distance_unit] Radius converted to Km or Miles
				 * [@min_radius_in_distance_unit] Min Radius converted to Km or Miles
				 * [@max_radius_in_distance_unit] Max Radius converted to Km or Miles */
													
				if(distance_unit == "METRIC"){
					var radius_in_distance_unit = (radius / 1000); 					
					var min_radius_in_distance_unit = (min_radius / 1000);
					var max_radius_in_distance_unit = (max_radius / 1000);
				}else{
					var radius_in_distance_unit = (radius / 1609.34);
						radius_in_distance_unit = radius_in_distance_unit / 1000;					
					var min_radius_in_distance_unit = (min_radius / 1609.34);
						min_radius_in_distance_unit = min_radius_in_distance_unit / 1000;
					var max_radius_in_distance_unit = (max_radius / 1609.34);
						max_radius_in_distance_unit = max_radius_in_distance_unit / 1000;
				}
				
			var min_radius_attempt = $elementObj.attr('data-min-radius-attempt');
			var max_radius_attempt = $elementObj.attr('data-max-radius-attempt');
			
			var hasResults =  false;
			
			/**
			 * No marker coordinates has been found */
			 
			if(target_marker_latlng == '' || typeof target_marker_latlng === 'undefined'){
				
				var message = progress_map_vars.no_marker_selected_msg;
				cspm_popup_msg(map_id, 'warning', '', message);				
				
			/**
			 * Proceed when marker coordinates are available */
			 	
			}else{

				if(typeof NProgress !== 'undefined'){
	
					NProgress.configure({
					  parent: 'div#codespacing_progress_map_div_'+map_id,
					  showSpinner: true
					});				
					
					NProgress.start();
					
				}								
				
				/**
				 * Convert marker latLng string to array */
				 
				var location_latlng = target_marker_latlng.replace('(', '').replace(')', '').split(',');
				
				if(typeof location_latlng[0] === 'undefined' && typeof location_latlng[1] === 'undefined'){
					
					if(typeof NProgress !== 'undefined')
							NProgress.done();
					
					return;
					
				}
			
				/**
				 * Clear already displayed proximity markers */
				 
				plugin_map.gmap3({
					clear: {
						tag: ['cspm_nearby_point_'+map_id, 'cspm_nearby_circle_'+map_id]
					}
				});	
				
				/**
				 * Prepare the map to display new markers and routes */
				
				var DirectionsRendererOptions = {
					suppressMarkers: true,
					suppressInfoWindows: true,
					draggable: false,
					preserveViewport: false,
					suppressBicyclingLayer: true,
				};
				
				if(DirectionsDisplay[map_id] != null) {
					DirectionsDisplay[map_id].setMap(null);
					DirectionsDisplay[map_id] = null;
				}
			
				DirectionsDisplay[map_id] = new google.maps.DirectionsRenderer(DirectionsRendererOptions);
				DirectionsDisplay[map_id].setMap(mapObject);
				
				/**
				 * New request for places */
				 	
				var request = {
				  location: new google.maps.LatLng(location_latlng[0], location_latlng[1]),
				  radius: radius,	  
				  types: [proximity_id], /* https://developers.google.com/places/documentation/supported_types?hl=fr */
				};
	
				var service = new google.maps.places.PlacesService(mapObject);
				
				var cspm_existing_places = {};
					cspm_existing_places[map_id] = new Array();
							
				if(typeof NProgress !== 'undefined')
					NProgress.set(0.5);
				
				var markers_bounds = new google.maps.LatLngBounds();

				service.nearbySearch(request, function(results, status, pagination){
			
					if(status === google.maps.places.PlacesServiceStatus.OK){
				
						/**
						 * Hide the proximities list
						 * @edited 5.5 */
						
						if($('div.cspm_proximities_container_'+map_id).is(':visible'))
							$('div.cspm_proximities_container_'+map_id).slideToggle();

						if(results.length > 0){

							/**
							 * [@marker_mouseover_count] | Count the mouseover events so we can decide when to display the ...
							 * ... pop-up message that informs the user that they can display place details by clicking on the marker.
							 * @since 3.5 */
							
							var marker_mouseover_count = 0;
											
							/**
							 * List all places */
							
							for (var i = 0; i < results.length; i++){
									
								var this_place_id = results[i].place_id;
								var this_place_location = results[i].geometry.location;
								var this_place_name = results[i].name;
										
								if(jQuery.inArray( this_place_id, cspm_existing_places[map_id] ) == -1 && jQuery.inArray( proximity_id, results[i].types ) != -1){
									
									cspm_existing_places[map_id].push(this_place_id);
									
									/**
									 * Add this nearby point of interest as new marker on the map */
									
									var marker_icon = (marker_type == 'default') ? '' : progress_map_vars.plugin_url+'img/nearby/pointers/'+proximity_id+'-pin.png';
									
									plugin_map.gmap3({ 
										marker:{
											latLng: this_place_location,
											title: this_place_name,
											tag: 'cspm_nearby_point_'+map_id,
											options:{
												icon: marker_icon, //@edited 5.4
												animation: google.maps.Animation.DROP,
												opacity: 1,
												placeID: this_place_id,
												placeName: this_place_name, 
											},
											callback: function(marker){	
												markers_bounds.extend(marker.getPosition());
											},
											events:{
												mouseover: function(marker){
															
													/**
													 * Inform the user that they can display the route ...
													 * ... and the place details.
													 * @since 3.5 */
															
													if(marker_mouseover_count == 0 || marker_mouseover_count == 10){
														
														var message = progress_map_vars.show_nearby_details_msg;
														cspm_popup_msg(map_id, 'info', '',  message);
														
														if(marker_mouseover_count == 10)
															marker_mouseover_count = 1;
															
													}
													
													marker_mouseover_count++;
														
												},
												mouseout: function(marker){
																												
													/**
													 * Close the info message
													 * @since 3.5 */
													
													cspm_close_popup_msg(map_id, 'info');

												},
												click: function(marker, event, context){
													
													var origin = new google.maps.LatLng(location_latlng[0], location_latlng[1]);
													var destination = marker.position;
													var travel_mode = $elementObj.attr('data-travel-mode');	
													
													$('a.cspm_switch_np_travel_mode[data-map-id='+map_id+']').attr('data-origin', origin).attr('data-destination', destination); //@edited 5.5
													
													/**
													 * Get route */
													 
													plugin_map.gmap3({ 
														getroute:{
															options:{
																origin: origin,
																destination: destination,
																travelMode: google.maps.DirectionsTravelMode[travel_mode.toUpperCase()],
																unitSystem: google.maps.UnitSystem[distance_unit.toUpperCase()],
															},
															callback: function(response, status){

																if(typeof response !== 'undefined' && status === 'OK'){
																
																	/**
																	 *  Render the route on the map */
																	 
																	DirectionsDisplay[map_id].setDirections(response);						
																	 
																	var route = response.routes[0];
																			
																	var distance = route.legs[0].distance.text;
																	var duration = route.legs[0].duration.text;		
		
																	var place_name = marker.placeName;
																	
																	/**
																	 * Get place details */
																	 
																	service.getDetails({
																		
																		placeId: marker.placeID,
																		fields: [
																			//'name', 
																			//'photo', 
																			//'rating', 
																			//'user_ratings_total', 
																			'place_id', 
																			//'formatted_address',
																			//'plus_code',
																			//'geometry',
																			//'website',
																			//'international_phone_number',
																			'url',
																			//'type',
																			//'review'
																		] //@since 5.2
																																		
																	}, function(place, status){

																		if(status === google.maps.places.PlacesServiceStatus.OK){
																			
																			var origin_latLng = origin.toString().replace('(', '').replace(')', '');
																			var destination_latLng = destination.toString().replace('(', '').replace(')', '');
																			
																			var infowindow_content = 
																				'<div style="padding:10px 5px; margin-bottom:5px; border-bottom:1px solid #eee; text-align:center;">'+
																					'<strong><a href="'+place.url+'" target="_blank">'+place_name+'</a></strong>'+
																				'</div>'+
																				'<div style="padding:5px; text-align:center;">'+
																					'<span style="text-align:center; padding:5px; margin-right:15px;"><img src="'+progress_map_vars.plugin_url+'img/svg/route_distance.svg" style="width:15px; margin-right:5px" /><span class="cspm_infowindow_distance">'+distance+'</span></span>'+
																					'<span style="text-align:center; padding:5px;"><img src="'+progress_map_vars.plugin_url+'img/svg/clock.svg" style="width:15px; margin-right:5px" /><span class="cspm_infowindow_duration">'+duration+'</span></span>'+
																					'<span style="text-align:center; padding:5px;"><img src="'+progress_map_vars.plugin_url+'img/svg/directions.svg" style="width:15px; margin-right:5px" /><span class="cspm_infowindow_directions">'+
																						'<a href="https://www.google.com/maps/dir/?api=1&origin='+origin_latLng+'&destination='+destination_latLng+'&destination_place_id='+place.place_id+'&travelmode='+travel_mode.toLowerCase()+'" target="_blank">Directions</a>'+
																					'</span></span>'+
																				'</div>';
																			
																			infowindow = plugin_map.gmap3({get: {name: "infowindow", id: "nearby_place"}});
																			
																			if(infowindow){
																			  infowindow.open(mapObject, marker);
																			  infowindow.setContent(infowindow_content);
																			}else{
																				plugin_map.gmap3({
																					infowindow: {
																						anchor: marker, 
																						id: "nearby_place",
																						options:{
																							content: infowindow_content
																						}
																					}
																				});
																			}
																			
																		}else{
																			var api_status_codes = progress_map_vars.places_api_status_codes; //@since 3.9
																			cspm_api_status_info(map_id, api_status_codes, status, 'Places API'); //@since 3.9
																		}
																		
																	});
																	
																}else{
																	var api_status_codes = progress_map_vars.directions_api_status_codes; //@since 3.9
																	cspm_api_status_info(map_id, api_status_codes, status, 'Directions API'); //@since 3.9
																}

																if(typeof NProgress !== 'undefined')
																	NProgress.done();
						
																return;
													
															}
															
														}
													});
	
												}
											}
											
										}
									});
				
								}
								
							}
							
							if(pagination.hasNextPage){

								pagination.nextPage();
								
							}else{
								
								/**
								 * Draw a circle around the nearby points of interest */

								if(!draw_circle){
									
									plugin_map.gmap3('get').fitBounds(markers_bounds);
									 											
									if(typeof NProgress !== 'undefined')
										NProgress.done();
										
								}else{
									
									setTimeout(function(){
											
										/**
										 * [@circle_mouseover_count] | Count the mouseover events so we can decide when to display the ...
										 * ... pop-up message that informs the user that they can reize the circle.
										 * @since 3.5 */
										
										var circle_mouseover_count = 0;
						
										var main_hex_color = (typeof progress_map_vars.map_script_args['initial']['main_hex_color'] !== 'undefined') 
											? progress_map_vars.map_script_args['initial']['main_hex_color'] : "#189AC9"; //@since 4.7
										 
										plugin_map.gmap3({
											circle:{
												tag: 'cspm_nearby_circle_'+map_id,
												options:{
													center: [location_latlng[0], location_latlng[1]],
													radius: parseInt(radius),
													fillColor: main_hex_color,
													fillOpacity: 0.05,
													strokeColor: main_hex_color,
													strokeOpacity: 0.5,
													strokeWeight: 5,
													editable: edit_circle,
												},										
												callback: function(circle){
	
													plugin_map.gmap3('get').fitBounds(markers_bounds);
													
													if(typeof NProgress !== 'undefined')
														NProgress.done();
												
												},										
												events:{
													mouseover: function(circle){
														
														/**
														 * Inform the user that they can resize the circle ...
														 * ... to show more or less results.
														 * @since 3.5 */
															
														if(edit_circle && (circle_mouseover_count == 0 || circle_mouseover_count == 6)){
															
															var message = progress_map_vars.resize_circle_msg;
															cspm_popup_msg(map_id, 'info', '',  message);
															
															if(circle_mouseover_count == 6)
																circle_mouseover_count = 1;
																
														}
														
														circle_mouseover_count++;
														
													},
													mouseout: function(circle){
																													
														/**
														 * Close the info message
														 * @since 3.5 */
														
														if(edit_circle)
															cspm_close_popup_msg(map_id, 'info');

													},
													radius_changed: function(circle){
														
														if(edit_circle){
															
															/**
															 * Get circle radius */
															 
															var circle_radius_in_meters = circle.getRadius();
																	
															/**
															 * Compare circle radius to min and max distances and redraw circle if needs to */
															
															var readable_distance_unit = (distance_unit == 'METRIC') ? 'Km' : 'Miles';
															
															/**
															 * Circle should not be more than max radius */
															 
															if(circle_radius_in_meters > max_radius){
																
																circle.setRadius(parseInt(max_radius));
																
																$elementObj.attr('data-radius', max_radius).attr('data-max-radius-attempt', parseInt(max_radius_attempt)+1);
																
																var reached_edge = 'max';
																				
																var message = progress_map_vars.max_search_radius_msg+' ('+Math.floor(max_radius_in_distance_unit)+readable_distance_unit+')';
																cspm_popup_msg(map_id, 'warning', '', message);							
																
															/**
															 * Circle should not be less than max radius */
															 
															}else if(circle_radius_in_meters < min_radius){
																
																circle.setRadius(parseInt(min_radius));
																
																$elementObj.attr('data-radius', min_radius).attr('data-min-radius-attempt', parseInt(min_radius_attempt)+1);
															
																var reached_edge = 'min';
																				
																var message = progress_map_vars.min_search_radius_msg+' ('+Math.floor(min_radius_in_distance_unit)+readable_distance_unit+')';
																cspm_popup_msg(map_id, 'warning', '', message);							
															
															/**
															 * Circle is between min radius & max radius */
															 
															}else{
																
																var reached_edge = '';
																	
																$elementObj.attr('data-radius', circle_radius_in_meters).attr('data-min-radius-attempt', parseInt(0)).attr('data-max-radius-attempt', parseInt(0));
																
															}
															
															/**
															 * Research for nearby points of interest */
								 
															setTimeout(function(){
																
																var min_radius_attempt = $elementObj.attr('data-min-radius-attempt');
																var max_radius_attempt = $elementObj.attr('data-max-radius-attempt');
																
																if((min_radius_attempt == 0 && max_radius_attempt == 0) || (reached_edge == 'min' && min_radius_attempt <= 1) || (reached_edge == 'max' && max_radius_attempt <= 1)){
																	listObj.options.all('unselect');
																	setTimeout(function(){
																		$elementObj.trigger('click');
																	}, 10);
																}
															
															}, 500);
															
														}
																											
													}
												}
											}
											
										});
									
									}, 500);
									
								}
									
							}
					
						}
								
					}else{
											
						var api_status_codes = progress_map_vars.places_api_status_codes; //@since 3.9
						cspm_api_status_info(map_id, api_status_codes, status, 'Places API'); //@since 3.9
															
						/**
						 * Back to the initial situation */
						 
					 	$elementObj.attr('data-radius', initial_radius).attr('data-min-radius-attempt', 0).attr('data-max-radius-attempt', 0);
					 	listObj.options.all('unselect');
						
						if(typeof NProgress !== 'undefined')						
							NProgress.done();
									
						return;
						
					}

				});
				
				/**
				 * Display the reset button */
				
				setTimeout(function(){
					$('a.cspm_reset_proximities[data-map-id='+map_id+']').show(); //@edited 5.5
				},1000);	
				
				hasResults = true;			
				
			}
			
			callback(hasResults);

		}
		
		/**
		 * Nearby points of interest
		 *
		 * @since 3.2
		 * @updated 5.2 [Added support for "fields" attribute in the function "getDetails()"]
		 * @updated 5.6 | 5.6.3
		 */
		
		var proximities_list = {}; //@edited 5.6.3
		
        $(document).on('click', 'div.cspm_proximities_btn', function(){ //@edited 5.6.3

			var map_id = $(this).attr('data-map-id');
		
			var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
		
			$('div.cspm_top_element:not(.cspm_proximities_container_'+map_id+')').hide();
			
			$('div.cspm_proximities_container_'+map_id).slideToggle(0, 'easeOutCirc', function(){
				
				if($(this).is(':visible')){
					
					var show_proximity_icon = $(this).attr('data-show-proximity-icon');
					
					if(typeof proximities_list[map_id] === 'undefined'){
						
						proximities_list[map_id] = tail.select('.cspm_proximities_list[data-map-id='+map_id+']', {
							classNames: 'cspm_custom_tailselect',
							multiple: false,
							multiShowCount: false,
							multiSelectAll: false,
							startOpen: false,
							stayOpen: false,
							openAbove: false,
							height: 195,						
							width: 195, 
							search: false,
							deselect: true,
							hideSelected: true,
							cbLoopItem: function(item, group, search){
								// Create LI element
								var li = document.createElement('li');
								// Add option attributes to the LI element
								if(item.option.hasAttributes()){
								   var attrs = item.option.attributes;
								   for(var i = attrs.length - 1; i >= 0; i--) {
									   li.setAttribute(attrs[i].name, attrs[i].value);
								   }
								}
								// Get the element data
								var item_key = item.key;
								var proximity_id = li.getAttribute('data-proximity-id');
								var proximity_name = li.getAttribute('data-proximity-name');
								// Assign element class names
								li.className = 'dropdown-option cspm_dropdown_no_icon' + (item.selected ? ' selected' : '') + (item.disabled ? ' disabled' : '');
								li.className += ' cspm_proximity_name'; // Required!
								li.innerHTML = '<div class="item-inner">';							
									if(jQuery.inArray(show_proximity_icon, ['true', 'only']) !== -1){
										 li.innerHTML += '<div class="cspm_np_icon cspm_'+proximity_id+'"></div>';
									}
									if(show_proximity_icon != 'only'){
										li.innerHTML += proximity_name;
									}
								li.innerHTML += '</div>';	
								return li;
							},
							cbComplete: function(){
								cspm_apply_custom_scrollabr('.cspm_custom_tailselect.tail-select .select-dropdown .dropdown-inner');                                
                            }
						}).on('change', function(item, state){						
	
							if(state != 'select')
								return;
								
							var proximity_id = $(item.option).attr('data-proximity-id');
							var proximity_name = $(item.option).attr('data-proximity-name');

							var custom_label = '';
							
							if(jQuery.inArray(show_proximity_icon, ['true', 'only']) !== -1)
								custom_label = '<div class="cspm_np_icon cspm_'+proximity_id+'"></div>';
							
							if(show_proximity_icon != 'only')
								custom_label += proximity_name;
							
							cspm_find_nearby_places(proximities_list[map_id], $(item.option), proximity_id, function(hasResults){
								if(!hasResults){		
									proximities_list[map_id].options.all('unselect');
								}else{
									proximities_list[map_id].updateLabel(custom_label);
								}
							});
						
						});
								
					}
					
				}
				
			});			
			
		});		
		
		
		/**
		 * Switch the travel mode
		 * @since 3.2
		 * @updated 3.8 */
		
        $(document).on('click', 'a.cspm_switch_np_travel_mode', function(e){ //@edited 5.6.3
			
			e.preventDefault(); //@since 5.5
			
			var map_id = $(this).attr('data-map-id');
			var travel_mode = $(this).attr('data-travel-mode');
			var origin = $(this).attr('data-origin');
			var destination = $(this).attr('data-destination');
			var distance_unit = $(this).attr('data-distance-unit');
			
			if($(this).hasClass('active'))
				return;
		
			$.each($('a.cspm_switch_np_travel_mode[data-map-id='+map_id+'] svg.cspm_svg'), function(index, element){
				$(this).removeClass('cspm_svg_white').addClass('cspm_svg_colored');
			}); //@edited 5.5
			
			$('a.cspm_switch_np_travel_mode[data-map-id='+map_id+']').removeClass('active cspm_bg_hex_hover'); //@edited 5.5
			$(this).addClass('active cspm_bg_hex_hover');			
			
			var this_icon = $('a.cspm_switch_np_travel_mode[data-map-id='+map_id+'][data-travel-mode='+travel_mode+'] svg.cspm_svg'); //@edited 5.5
			this_icon.removeClass('cspm_svg_colored').addClass('cspm_svg_white');
			
			var plugin_map = jQuery('div#codespacing_progress_map_div_'+map_id);
			var mapObject = plugin_map.gmap3('get');
				
			$('li.cspm_proximity_name[data-map-id='+map_id+']').attr('data-travel-mode', travel_mode);
						
			if(origin == '' && destination == '')
				return;
				
			/**
			 * Convert marker latLng string to array */
			 
			var origin_latlng = origin.replace('(', '').replace(')', '').split(',');
			
			if(typeof origin_latlng[0] === 'undefined' && typeof origin_latlng[1] === 'undefined'){
				
				if(typeof NProgress !== 'undefined')
						NProgress.done();
				
				return;
				
			}
				
			/**
			 * Convert destination latLng string to array */
			 
			var destination_latlng = destination.replace('(', '').replace(')', '').split(',');
			
			if(typeof destination_latlng[0] === 'undefined' && typeof destination_latlng[1] === 'undefined'){
				
				if(typeof NProgress !== 'undefined')
						NProgress.done();
				
				return;
				
			}
						
			if(typeof NProgress !== 'undefined'){

				NProgress.configure({
				  parent: 'div#codespacing_progress_map_div_'+map_id,
				  showSpinner: true
				});				
				
				NProgress.start();
				
			}								
		
			/**
			 * Get route */
														
			plugin_map.gmap3({ 
				getroute:{
					options:{
						origin: new google.maps.LatLng(origin_latlng[0], origin_latlng[1]),
						destination: new google.maps.LatLng(destination_latlng[0], destination_latlng[1]),
						travelMode: google.maps.DirectionsTravelMode[travel_mode.toUpperCase()],
						unitSystem: google.maps.UnitSystem[distance_unit.toUpperCase()],
					},
					callback: function(response, status){
							
						if(typeof response !== 'undefined' && status === 'OK'){
						
							/**
							 *  Render the route on the map */
							 
							DirectionsDisplay[map_id].setDirections(response);						
							 
							var route = response.routes[0];
									
							var distance = route.legs[0].distance.text;
							var duration = route.legs[0].duration.text;		

							$('span.cspm_infowindow_distance').text(distance);
							$('span.cspm_infowindow_duration').text(duration);
							
							var directions_url = $('span.cspm_infowindow_directions a').attr('href');
							
							if(typeof directions_url !== 'undefined'){
								var directions_url_array = directions_url.split('&');								
								var new_directions_url = directions_url_array[0]+'&'+directions_url_array[1]+'&'+directions_url_array[2]+'&'+directions_url_array[3]+'&travelmode='+travel_mode.toLowerCase();
								$('span.cspm_infowindow_directions a').attr('href', new_directions_url);
							}
						
						}else{
							var api_status_codes = progress_map_vars.directions_api_status_codes; //@since 3.9
							cspm_api_status_info(map_id, api_status_codes, status, 'Directions API'); //@since 3.9
						}

						if(typeof NProgress !== 'undefined')
							NProgress.done();

						return;
		
					}
					
				}
				
			});
	
		});
		
		/**
		 * Reset proximities list */
		
        $(document).on('click', 'a.cspm_reset_proximities', function(e){ //@edited 5.6.3
			
			e.preventDefault(); //@since 5.5
			
			var map_id = $(this).attr('data-map-id');

			if(typeof NProgress !== 'undefined'){

				NProgress.configure({
				  parent: 'div#codespacing_progress_map_div_'+map_id,
				  showSpinner: true
				});				
				
				NProgress.start();
				
			}		
			
			var plugin_map = jQuery('div#codespacing_progress_map_div_'+map_id);
			var mapObject = plugin_map.gmap3('get');
			
			$(this).hide();
		
			/**
			 * Remove selected class name and previous marker latLng from the list items */
			 
			$('li.cspm_proximity_name[data-map-id='+map_id+']').attr('data-marker-latlng', '');
			
			if(typeof proximities_list[map_id] !== 'undefined'){	                
				proximities_list[map_id].options.all('unselect');
				proximities_list[map_id].updateLabel(proximities_list[map_id].con.placeholder);
			}
			
			/**
			 * Clear already displayed proximity markers */
			 
			plugin_map.gmap3({
				clear: {
					tag: ['cspm_nearby_point_'+map_id, 'cspm_nearby_circle_'+map_id]
				}
			});	
				
			/**
			 * Clear already displayed routes */
			
			if(typeof DirectionsDisplay[map_id] !== 'undefined')
				DirectionsDisplay[map_id].setMap(null);
				
			if(typeof NProgress !== 'undefined')						
				NProgress.done();
																		
		});
					
		/**
		 * Heatmap Layer
		 *
		 * @since 3.3 */
		
        $(document).on('click', 'div.cspm_heatmap_btn', function(){ //@edited 5.6.3
			
			var map_id = $(this).attr('data-map-id');			
			var toggle_marker_option = $(this).attr('data-toggle-markers');
				
			var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
			var mapObject = plugin_map.gmap3('get');
			
			/**
			 * Hide heatmap layer */
			 
			if(heatmap[map_id].getMap()){

				$(this).removeClass('cspm_active_btn cspm_bg_rgb_hover');
				$(this).find('svg.cspm_svg').removeClass('cspm_svg_white').addClass('cspm_svg_colored');
				
				heatmap[map_id].setMap(null);
				
				if(toggle_marker_option == 'yes'){
					cspm_show_all_markers(plugin_map, map_id);
					cspm_zoom_in_and_out(plugin_map);
				}
				
			/**
			 * Show heatmap layer */
			 
			}else{

				$(this).addClass('cspm_active_btn cspm_bg_rgb_hover');
				$(this).find('svg.cspm_svg').removeClass('cspm_svg_colored').addClass('cspm_svg_white');
				
				heatmap[map_id].setMap(mapObject);
				
				if(toggle_marker_option == 'yes'){				
					cspm_hide_all_markers(plugin_map, map_id);
					cspm_zoom_in_and_out(plugin_map);
				}
				
			}
			
		});
		
		/**
		 * Open the single post inside a modal
		 * @since 3.6 */
				
        $(document).on('click', 'a.cspm_popup_single_post', function(e){ //@edited 5.6.3
			e.preventDefault(); 
			var iframe_url = $(this).attr('href');
			if(typeof iframe_url !== 'undefined' && iframe_url != ''){
				cspm_open_single_post_modal(iframe_url);		
			}	
		});		
		
		/**
		 * Open the "Nearby places" map inside a modal
		 * @since 4.6 */
					
        $(document).on('click', 'a.cspm_popup_nearby_palces_map', function(e){ //@edited 5.6.3
			e.preventDefault();			            
			var iframe_url = progress_map_vars.nearby_places_page_url;
			var post_id = $(this).attr('data-post-id');
			if(typeof iframe_url !== 'undefined' && iframe_url != '' && typeof post_id !== 'undefined' && post_id != ''){
				cspm_open_single_post_modal(iframe_url+'?post_id='+post_id);		
			}
		});	

		/**
		 * Replace all SVG images with inline SVG
		 * @since 3.8
		 * @edited 5.6.5 */
		
		cspm_convert_img_to_svg();		
		
		/**
		 * KML Layers List
		 *
		 * @since 5.6
         * @updated 5.6.3
		 */	
		
		var kml_layers_list = {}; //@edited 5.6.3
			
        $(document).on('click', 'div.kml_list_btn', function(){ //@edited 5.6.3

			var map_id = $(this).attr('data-map-id');
		
			var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
							
			$('div.cspm_top_element:not(.kml_list_container_'+map_id+')').hide();
			
			$('div.kml_list_container_'+map_id).slideToggle(0, 'easeOutCirc', function(){
				
				var $kml_list_container = $(this);
				
				if($(this).is(':visible')){
					
					/**
					 * Display a list with all available KML Layers ...
					 * ... and show/Hide the selected KML Layer(s) */
					
					if(typeof kml_layers_list[map_id] === 'undefined'){
						 
						kml_layers_list[map_id] = tail.select('.cspm_kml_layer_list[data-map-id='+map_id+']', {
							classNames: 'cspm_custom_tailselect',
							multiple: true,
							multiShowCount: true,
							multiSelectAll: true,
							startOpen: true,
							stayOpen: true,
							openAbove: false,
							local: 'en',
							cbComplete: function(){
								$kml_list_container.find(".tail-all").html(progress_map_vars.all);
								$kml_list_container.find(".tail-none").html(progress_map_vars.none);							
								cspm_apply_custom_scrollabr('.cspm_custom_tailselect.tail-select .select-dropdown .dropdown-inner');
							}						
						}).on('change', function(item, state){
							kml_layers_list[map_id].disable(false);
							plugin_map.gmap3({
								get: {
									name: 'kmllayer',
									tag: item.key,
									callback: function(layer){
										if(typeof layer !== 'undefined' 
										&& typeof layer.setMap === 'function'){
											if(state == 'select'){
												layer.setMap(plugin_map.gmap3('get'));
												layer.setOptions({preserveViewport: false});
											}else if(state == 'unselect'){
												layer.setMap(null);
											}
										}
										kml_layers_list[map_id].enable(false);
									}
								}
							});	
						});
						
					}
					
				}
				
			});			
					
		});
		 			
	})(jQuery);
