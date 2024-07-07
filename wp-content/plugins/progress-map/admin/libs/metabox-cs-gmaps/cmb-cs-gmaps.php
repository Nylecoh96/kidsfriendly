<?php
/**
 * Plugin Name: CMB2 Google Maps Field
 * Plugin URI: 
 * Description: 
 * Version: 1.1
 * Author: Hicham Radi (CodeSpacing)
 * Author URI: https://www.codespacing.com/
 */
 
/**
 * Class CS_CMB2_GMaps_Field */

if( !class_exists( 'CS_CMB2_GMaps_Field' ) ) {
  
	class CS_CMB2_GMaps_Field {
		
	
		/**
		 * Current version number */
		 
		const VERSION = '1.1';
				
		private $plugin_path;
		private $plugin_url;
			
	
		/**
		 * Initialize the plugin by hooking into CMB2
		 *
		 * @since 1.0
		 */
		public function __construct(){
					 
			$this->plugin_path = plugin_dir_path( __FILE__ );
			$this->plugin_url = plugin_dir_url( __FILE__ );
				
			add_filter( 'cmb2_render_cs_gmaps', array( $this, 'cs_render_gmaps_field' ), 10, 5 );
			add_filter( 'cmb2_sanitize_cs_gmaps', array( $this, 'cs_sanitize_gmaps_field' ), 10, 4 );
		
		}
		
	
		/**
		 * Render field
		 *
		 * [options] | ARRAY | [
		 *		[api_key] 					| STRING  | @Google Maps API Key
		 *		[disable_gmaps_api] 		| BOOLEAN | @Defaults to FALSE
		 *		[disable_gmap3_plugin] 		| BOOLEAN | @Defaults to FALSE
		 *		[address_field_name] 		| STRING  | @Address field name
		 *		[lat_field_name] 			| STRING  | @Latitude field name
		 *		[lng_field_name] 			| STRING  | @Longitude field name
		 *      [latLng_field_name] 		| STRING  | @Latitude & Longitude field name (works only id [@split_latlng_field] is true!) //@since 1.1
		 *		[split_values] 				| BOOLEAN | @Defaults to FALSE
		 *		[split_latlng_field] 		| BOOLEAN | @Defaults to FALSE //@since 1.1
		 * 		[save_only_latLng_field] 	| BOOLEAN | @Defaults to FALSE (works only id [@split_latlng_field] is true!) //@since 1.1
		 *		[map_height] 				| STRING  | @Map width. Defaults to '250px'
		 *		[map_width] 				| STRING  | @Map height. Defaults to '100%',
		 *		[map_center] 				| STRING  | @Map center point. Defaults to '51.532580, -0.133216',
		 *		[map_zoom] 					| INT     | @Map center. Defaults to '12',
		 *      [required]					| BOOLEAN | @Defaults to TRUE
		 *		[labels] | ARRAY | [
		 *			[address] 	| STRING | @Address field label
		 *			[latitude] 	| STRING | @Latitude field label
		 *			[longitude] | STRING | @Longitude field label
		 *			[search] 	| STRING | @Search button value
		 *			[pinpoint] 	| STRING | @Get pinpoint button value
		 *          [latLng] 	| STRING | @Latitude & Longitude field label (works only id [@split_latlng_field] is true!) //@since 1.1
		 *		]
		 *		[secondary_latlng]
		 *			[show]			| BOOLEAN | @Defaults to FALSE
		 * 			[field_label] 	| STRING  | @Secondary latLng field label
		 *   		[field_name] 	| STRING  | @Secondary latLng field name
		 * ]
		 *
		 * @since 1.0
		 */
		public function cs_render_gmaps_field( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
	
			/**
			 * Get the field options */
			 
			$field_options = $field->args('options');
			
			/**
			 * Gmaps API Key & Script options */
			 
			$api_key = (isset($field_options['api_key'])) ? $field_options['api_key'] : '';
			$disable_gmaps_api = (isset($field_options['disable_gmaps_api'])) ? $field_options['disable_gmaps_api'] : false;
			$disable_gmap3_plugin = (isset($field_options['disable_gmap3_plugin'])) ? $field_options['disable_gmap3_plugin'] : false;
			
			/**
			 * Map settings */
	
			$map_center = (isset($field_options['map_center'])) ? $field_options['map_center'] : '51.532580, -0.133216';
			$map_zoom = (isset($field_options['map_zoom'])) ? $field_options['map_zoom'] : 12;
			$map_height = (isset($field_options['map_height'])) ? $field_options['map_height'] : '250px';
			$map_width = (isset($field_options['map_width'])) ? $field_options['map_width'] : '100%';
			
			/** 
			 * Address field name.
			 * Defaults to field ID */
			
			$address_field_name = (isset($field_options['address_field_name'])) ? $field_options['address_field_name'] : 'cs_gmaps_address';
			
			/**
			 * Lat & Lng field names */
			
			$split_latlng_field = (isset($field_options['split_latlng_field']) && !$field_options['split_latlng_field']) ? false : true; //@since 1.1
			$lat_field_name = (isset($field_options['lat_field_name'])) ? $field_options['lat_field_name'] :  'cs_gmaps_latitude';
			$lng_field_name = (isset($field_options['lng_field_name'])) ? $field_options['lng_field_name'] : 'cs_gmaps_longitude';
			$latLng_field_name = (isset($field_options['latLng_field_name'])) ? $field_options['latLng_field_name'] : 'cs_gmaps_latLng'; //@since 1.1
			$latLng_field_type = (!$split_latlng_field) ? 'unique' : 'separated';
			
			/**
			 * Fields & buttons labels */
			
			$address_label = (isset($field_options['labels']['address'])) ? $field_options['labels']['address'] :  'Address';
			$address_placeholder = (isset($field_options['labels']['address_placeholder'])) ? $field_options['labels']['address_placeholder'] :  'Enter a location or address';
			$latitude_label = (isset($field_options['labels']['latitude'])) ? $field_options['labels']['latitude'] :  'Latitude';
			$longitude_label = (isset($field_options['labels']['longitude'])) ? $field_options['labels']['longitude'] :  'Longitude';
			$search_label = (isset($field_options['labels']['search'])) ? $field_options['labels']['search'] :  'Search';
			$pinpoint_label = (isset($field_options['labels']['pinpoint'])) ? $field_options['labels']['pinpoint'] :  'Get pinpoint';
			$latLng_label = (isset($field_options['labels']['latLng'])) ? $field_options['labels']['latLng'] :  'Latitude & Longitude'; //@since 1.1
			
			/**
			 * Show secondary latLng field */
			
			$show_secondary_latlng = (isset($field_options['secondary_latlng']['show'])) ? $field_options['secondary_latlng']['show'] : false;
			$secondary_latlng_label = (isset($field_options['secondary_latlng']['field_label'])) ? $field_options['secondary_latlng']['field_label'] : 'Add more locations';
			$secondary_latlng_field_name = (isset($field_options['secondary_latlng']['field_name'])) ? $field_options['secondary_latlng']['field_name'] : 'cs_gmaps_secondary_latlng';
			
			/**
			 * Split values */
			
			$split_values = (isset($field_options['split_values']) && $field_options['split_values']) ? true : false;
			
			/**
			 * Save only latLng field value
			 * @since 1.1 */
			
			$save_only_latLng_field = (isset($field_options['save_only_latLng_field']) && $field_options['save_only_latLng_field']) ? true : false;
			
			$this->cs_enqueue_scripts(array(
				'api_key' => $api_key,
				'disable_gmaps_api' => $disable_gmaps_api,
				'disable_gmap3_plugin' => $disable_gmap3_plugin,
			));
		
			$map_id = $field->args('id');		
				
			$output = '';
			
			/**
			 * Search address field */
					
			$output .= '<div class="cs_gmaps_container" data-map-id="'.$map_id.'" data-map-center="'.$map_center.'" data-map-zoom="'.$map_zoom.'" data-latLng-field-type="'.$latLng_field_type.'">';
				
				$output .= '<div>';
							
					$output .= '<div><label for="" class="cs-gmaps-label">'.$address_label.'</label></div>';
						
					$output .= '<div style="float:left; width:60%; margin-right:1em;">';
						
						$value = isset( $field_escaped_value[$address_field_name] ) ? $field_escaped_value[$address_field_name] : '';
						
						$output .= $field_type_object->input( array(
							'type' => 'text',
							'name' => $field->args('_name') . '['.$address_field_name.']',
							'id' => $field->args('_name') . '['.$address_field_name.']',
							'value' => (!empty($value) && !$split_values) ? $value : get_post_meta($field_object_id, $address_field_name, true),
							'class' => 'cs-gmaps-search cs-gmaps-preventDefault',
							'desc' => '',
							'data-map-id' => $map_id,
							'placeholder' => $address_placeholder,
						) );
					
					$output .= '</div>';
					
					/**
					 * Search buttons */
					 
					$output .= '<div style="float:left; width:35%;">';
						$output .= '<input type="button" id="cs_gmaps_search_btn" class="button cs-gmaps-btn cs_gmaps_search_btn" data-map-id="'.$map_id.'" style="margin-right:0.5em;" value="'.$search_label.'" />';
						$output .= '<button id="cs_gmaps_geoloc_btn" class="button cs-gmaps-btn cs_gmaps_geoloc_btn" data-map-id="'.$map_id.'" title="Find your position"><img src="'.$this->plugin_url.'target.svg" /></button>';
					$output .= '</div>';
					
					$output .= '<div style="clear:both;"></div>';
					
				$output .= '</div>';
				
				/**
				 * Map */
				 
				$output .= '<div id="cs_gmaps_'.$map_id.'" class="cs-gmaps-map" data-map-id="'.$map_id.'" style="height:'.$map_height.'; width:'.$map_width.'"></div>';
				
				/** 
				 * Lat & Lng fields */
				 
				$output .= '<div>';
					
					/**
					 * Output Lat & Lng in separated fields */
					 
					if($split_latlng_field){ //@since 1.1
						
						$output .= '<div style="width:30%; float:left; margin-right:1.5em">';
								
							$output .= '<label for="" class="cs-gmaps-label">'.$latitude_label.'</label>';
							
							$value = isset( $field_escaped_value[$lat_field_name] ) ? $field_escaped_value[$lat_field_name] : '';
							
							$output .= $field_type_object->input( array(
								'type' => 'text',
								'name' => $field->args('_name') . '['.$lat_field_name.']',
								'id' => $field->args('_name') . '['.$lat_field_name.']',
								'value' => (!empty($value) && !$split_values) ? $value : get_post_meta($field_object_id, $lat_field_name, true),
								'class' => 'cs-gmaps-latitude cs-gmaps-preventDefault',
								'desc' => '',
								'data-map-id' => $map_id,
								'required' => (isset($field_options['required'])) ? (bool) $field_options['required'] : false,
							) );
						
						$output .= '</div>';
							
						$output .= '<div style="width:30%; float:left; margin-right:1em">';
								
							$output .= '<label for="" class="cs-gmaps-label">'.$longitude_label.'</label>';
							
							$value = isset( $field_escaped_value[$lng_field_name] ) ? $field_escaped_value[$lng_field_name] : '';
							
							$output .= $field_type_object->input( array(
								'type' => 'text',
								'name' => $field->args('_name') . '['.$lng_field_name.']',
								'id' => $field->args('_name') . '['.$lng_field_name.']',
								'value' => (!empty($value) && !$split_values) ? $value : get_post_meta($field_object_id, $lng_field_name, true),
								'class' => 'cs-gmaps-longitude cs-gmaps-preventDefault',
								'desc' => '',
								'data-map-id' => $map_id,
								'required' => (isset($field_options['required'])) ? (bool) $field_options['required'] : false,
							) );
						
						$output .= '</div>';
					
					/**
					 * Output Lat & Lng in a unique field */
					 						
					}else{
							
						$output .= '<div style="width:65%; float:left; margin-right:1em">';
								
							$output .= '<label for="" class="cs-gmaps-label">'.$latLng_label.'</label>';
							
							if($save_only_latLng_field){
								$value = $field_escaped_value; 
							}else $value = isset( $field_escaped_value[$latLng_field_name] ) ? $field_escaped_value[$latLng_field_name] : '';
														
							$output .= $field_type_object->input( array(
								'type' => 'text',
								'name' => $field->args('_name') . '['.$latLng_field_name.']',
								'value' => (!empty($value)) ? $value : get_post_meta($field_object_id, $latLng_field_name, true),
								'class' => 'cs-gmaps-latLng cs-gmaps-preventDefault',
								'desc' => '',
								'data-map-id' => $map_id,
								'required' => (isset($field_options['required'])) ? (bool) $field_options['required'] : false,
							) );
						
						$output .= '</div>';
						
					}
					
					/**
					 * Pinpoit button */
					 
					$output .= '<div style="width:30%; float:right; margin-top:23px;">';
						$output .= '<input type="button" id="cs_gmaps_get_pinpoint" class="button cs-gmaps-btn cs_gmaps_get_pinpoint" data-map-id="'.$map_id.'" value="'.$pinpoint_label.'" />';
					$output .= '</div>';
					
					$output .= '<div style="clear:both;"></div>';
					
				$output .= '</div>';
				
				$output .= '<p class="cmb2-metabox-description">'.$field->args('desc').'</p>';
			
			$output .= '</div>';
						
			/**
			 * Secondary Lat & Lng Field */
			
			if($show_secondary_latlng){
				
				$output .= '<br><hr /><br>';
				
				$output .= '<div>';
					
					/**
					 * Latitudes & Longitudes */
					 
					$output .= '<label for="'.$secondary_latlng_field_name.'" class="cs-gmaps-label">'.$secondary_latlng_label.'</label>';
						
					$value = isset( $field_escaped_value[$secondary_latlng_field_name] ) ? $field_escaped_value[$secondary_latlng_field_name] : '';
						
					$output .= $field_type_object->textarea( array(
						'name'       => $field->args('_name') . '['.$secondary_latlng_field_name.']',
						'id'       => $field->args('_name') . '['.$secondary_latlng_field_name.']',
						'value'      => (!empty($value) && !$split_values) ? $value : get_post_meta($field_object_id, $secondary_latlng_field_name, true),
						'class'      => 'cs-gmaps-secondary-latlng',
						'rows'  	 => 4,
						'desc'       => '',
						'data-map-id' => $map_id,
					) );
					
					$output .= '<input type="button" id="cs_gmaps_get_more_pinpoint" class="button cs-gmaps-btn cs_gmaps_get_more_pinpoint" data-map-id="'.$map_id.'" data-textarea-name="'.$secondary_latlng_field_name.'" style="margin-top:5px;" value="'.esc_html__('Add more locations', 'cspmsl').'" />';
					
					$output .= '<p class="cmb2-metabox-description">';
						
						$output .= 'This field allows you to display the same post on multiple places on the map. 
						For example, let\'s say that this post is about "McDonald\'s" and that you want to use it to show your website\'s visitors all the locations in your country/city/town...
						where they can find "McDonald\'s". So, instead of creating - for instance - 10 posts with the same content but with different coordinates, this field will allow you to share the same content with all the different locations that points to "McDonald\'s".<br />
						<br /><strong style="font-size:15px; margin-top:5px; display:inline-block;">How to add more locations?</strong><br />
						1. Insert the coordinates of one location in the fields <strong>Latitude</strong> & <strong>Longitude</strong>.
						<br />
						2. Enter the coordinates of the remaining locations in the field <strong>"Add more locations"</strong> by dragging the marker on the map to the exact location or by entering the location\'s address in the field <strong>"Enter an address"</strong>, then, click on the button <strong>"Add more locations".</strong> <br /><br /> 
						<strong>Note:</strong> All the locations will share the same title, content, link and featured image. Each location represents a new item on the carousel!';
					
					$output .= '</p>';
					
				$output .= '</div>';
			
			}
			
			echo $output;		
		
		}
		
	
		/**
		 * Optionally save the latitude/longitude values into separated custom fields
		 *
		 * @since 1.0
		 * @updated 1.1
		 */
		public function cs_sanitize_gmaps_field( $override_value, $value, $object_id, $field_args ) {
			
			if($object_id === 0)
				return;
	
			$field_options = $field_args['options'];
					 
			/**
			 * Get all field names */
				
			$address_field_name = (isset($field_options['address_field_name'])) ? $field_options['address_field_name'] : 'cs_gmaps_address';
			$lat_field_name = (isset($field_options['lat_field_name'])) ? $field_options['lat_field_name'] :  'cs_gmaps_latitude';
			$lng_field_name = (isset($field_options['lng_field_name'])) ? $field_options['lng_field_name'] : 'cs_gmaps_longitude';
			$secondary_latlng_field_name = (isset($field_options['secondary_latlng']['field_name'])) ? $field_options['secondary_latlng']['field_name'] : 'cs_gmaps_secondary_latlng';
			$latLng_field_name = (isset($field_options['latLng_field_name'])) ? $field_options['latLng_field_name'] : 'cs_gmaps_latLng'; //@since 1.1
			
			/**
			 * Save our fields */
			 
			if(isset($field_options['split_values']) && $field_options['split_values']){
				
				if(isset($value[$address_field_name]))
					update_post_meta($object_id, $address_field_name, $value[$address_field_name]);
				
				/**
				 * Save Lat & Lng into one meta data
				 * @since 1.1 */
				 
				if(isset($field_options['split_latlng_field']) && !$field_options['split_latlng_field']){
					
					if(isset($value[$latLng_field_name]))
						update_post_meta($object_id, $latLng_field_name, $value[$latLng_coordinates]);
				
				/**
				 * Save Lat & Lng into separated meta data
				 * @since 1.0 */
				 
				}else{
					
					if(isset($value[$lat_field_name]))
						update_post_meta($object_id, $lat_field_name, $value[$lat_field_name]);
							
					if(isset($value[$lng_field_name]))
						update_post_meta($object_id, $lng_field_name, $value[$lng_field_name]);
				
				}
			
				/**
				 * Save secondary latLng field */
				
				$show_secondary_latlng = (isset($field_options['secondary_latlng']['show'])) ? $field_options['secondary_latlng']['show'] : false;
			
				if($show_secondary_latlng && isset($value[$secondary_latlng_field_name]))
					update_post_meta($object_id, $secondary_latlng_field_name, $value[$secondary_latlng_field_name]);
			
			}
			
			return (isset($field_options['save_only_latLng_field']) && $field_options['save_only_latLng_field']) 
				? $value[$latLng_field_name] 
				: $value;
			
		}
	
	
		/**
		 * Enqueue scripts and styles
		 *
		 * @since 1.0
		 */
		public function cs_enqueue_scripts($atts = array()){
			
			extract( wp_parse_args( $atts, array(
				'api_key' => '',
				'disable_gmaps_api' => false,
				'disable_gmap3_plugin' => false,
			)));
				
			$gmaps_api_key = (!empty($api_key)) ? '&key='.$api_key : '';
					
			/**
			 * Google Maps API */
			 
			if(!$disable_gmaps_api)		
				wp_enqueue_script('gmaps-api', '//maps.google.com/maps/api/js?v=3.exp'.$gmaps_api_key.'&libraries=places,drawing', array( 'jquery' ), self::VERSION);
					
			/**
			 * GMap3 jQuery Plugin */
			 
			if(!$disable_gmap3_plugin) 
				wp_enqueue_script('gmap3', plugins_url( 'js/gmap3.js', __FILE__ ), array('jquery'), self::VERSION);		

            /**
			 * Custom JS */
								
			wp_enqueue_script('cs-gmaps-script', plugins_url( 'js/script.js', __FILE__ ), array('jquery'), self::VERSION);
			
			/**
			 * Custom CSS */
								
			wp_enqueue_style('cs-gmaps-style', plugins_url( 'css/style.css', __FILE__ ), array(), self::VERSION);
			
		}
		
	}
	$CS_CMB2_GMaps_Field = new CS_CMB2_GMaps_Field();

}