<?php
/**
 * Plugin Name: CMB2 Google Maps Drawing Tools Field
 * Plugin URI: 
 * Description: 
 * Version: 1.2
 * Author: Hicham Radi (CodeSpacing)
 * Author URI: https://www.codespacing.com/
 */
 
/**
 * Class CS_CMB2_GMaps_Drawing_Field */

if( !class_exists( 'CS_CMB2_GMaps_Drawing_Field' ) ) {
  
	class CS_CMB2_GMaps_Drawing_Field {
		
	
		/**
		 * Current version number */
		 
		const VERSION = '1.2';
				
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
				
			add_filter( 'cmb2_render_cs_gmaps_drawing', array( $this, 'cs_render_gmaps_drawing_field' ), 10, 5 );
			add_filter( 'cmb2_sanitize_cs_gmaps_drawing', array( $this, 'cs_sanitize_gmaps_drawing_field' ), 10, 4 );
		
		}
		
	
		/**
		 * Render field
		 *
		 * [options] | ARRAY | [
		 *		[api_key] 					| STRING  | @Google Maps API Key
		 *		[disable_gmaps_api] 		| BOOLEAN | @Defaults to FALSE
		 *		[disable_gmap3_plugin] 		| BOOLEAN | @Defaults to FALSE
		 *		[coordinates_field_name] 	| STRING  | @Shap coordinates field name
		 *		[ne_coordinates_field_name] | STRING  | @North-East coordinates field name
		 *		[sw_coordinates_field_name] | STRING  | @South-West coordinates field name
		 *		[img_url_field_name] 		| STRING  | @Image URL field name
		 *		[draw_mode]					| STRING  | @Drawing mode ('marker', 'circle', 'polygon', 'polyline', 'rectangle')
		 *		[map_height] 				| STRING  | @Map width. Defaults to '250px'
		 *		[map_width] 				| STRING  | @Map height. Defaults to '100%',
		 *		[map_center] 				| STRING  | @Map center point. Defaults to '51.532580, -0.133216',
		 *		[map_zoom] 					| INT     | @Map center. Defaults to '12',
		 *      [required]					| BOOLEAN | @Defaults to TRUE
		 *		[labels] | ARRAY | [
		 *			[address] 				| STRING  | @Address field label
		 *    		[address_placeholder]	| STRING  | @Address field placeholder
		 *			[coordinates] 			| STRING  | @Coordinates field label
		 *			[search] 				| STRING  | @Search button value
		 *			[pinpoint]				| STRING  | @Get pinpoint button text
		 *   		[clear_overlay]			| STRING  | @Clear overlay button text
		 *  		[toggle]				| STRING  | @Toggle title
		 *			[top_desc]				| STRING  | @Top description
		 *			[ne_label]				| STRING  | @North-East field label
		 *			[sw_label]				| STRING  | @South-West field label
		 *			[copy_ne]				| STRING  | @Copy North-East button text
		 *			[copy_sw]				| STRING  | @Copy South-West button text
		 *			[img_url_label]			| STRING  | @Image URL field label
		 *			[render_img_label]		| STRING  | @Project image button text
		 *		]
		 *		[fields_desc] | ARRAY | [
		 *			[coordinates] 			| STRING  | @Coordinates field description
		 *			[ne]					| STRING  | @North-East field description
		 *			[sw]					| STRING  | @South-West field description
		 *			[img_url]			| STRING  | @Image URL field description
		 *		]		 
		 *		[save_coordinates]			| BOOLEAN | @Save the coordinates field. Default to TRUE
		 *		[toggle]					| BOOLEAN | @Toggle drawing tool. Default to FALSE
		 *		[close]						| BOOLEAN | @Close toggle. Default to FALSE		 
		 *		[latLng_order]			    | STRING  | @The coordinates order. Possible values are "latlng" and "lnglat". Defaults to "latlng". | @since 1.1
		 *
		 * @since 1.0
		 */
		public function cs_render_gmaps_drawing_field( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
	
			/**
			 * Get the field options */
			 
			$field_options = $field->args('options');
			
			/**
			 * Gmaps API Key & Script options */
			 
			$api_key = (isset($field_options['api_key'])) ? $field_options['api_key'] : '';
			$disable_gmaps_api = (isset($field_options['disable_gmaps_api'])) ? $field_options['disable_gmaps_api'] : false;
			$disable_gmap3_plugin = (isset($field_options['disable_gmap3_plugin'])) ? $field_options['disable_gmap3_plugin'] : false;
			$draw_mode = (isset($field_options['draw_mode'])) ? $field_options['draw_mode'] : 'polygon';			
			$toggle = (isset($field_options['toggle'])) ? $field_options['toggle'] : false;			
			$close_toggle = (isset($field_options['close'])) ? $field_options['close'] : false;			
			
			$save_coordinates = (isset($field_options['save_coordinates'])) ? $field_options['save_coordinates'] : true;
			$copy_clipboard = $save_coordinates ? 'false' : 'true';
			$readonly = $save_coordinates ? array() : array('readonly' => 'readonly');
			
			/**
			 * Map settings */
	
			$map_center = (isset($field_options['map_center'])) ? $field_options['map_center'] : '51.532580, -0.133216';
			$map_zoom = (isset($field_options['map_zoom'])) ? $field_options['map_zoom'] : 12;
			$map_height = (isset($field_options['map_height'])) ? $field_options['map_height'] : '250px';
			$map_width = (isset($field_options['map_width'])) ? $field_options['map_width'] : '100%';
			$latLng_order = (isset($field_options['latLng_order'])) ? $field_options['latLng_order'] : 'latlng';			
			
			/** 
			 * Coordinates field name.
			 * Defaults to field ID */
			
			$coordinates_field_name = (isset($field_options['coordinates_field_name'])) ? $field_options['coordinates_field_name'] : 'cs_gmaps_drawing_latLngs';
			$ne_coordinates_field_name = (isset($field_options['ne_coordinates_field_name'])) ? $field_options['ne_coordinates_field_name'] : 'cs_gmaps_drawing_ne_latLng';
			$sw_coordinates_field_name = (isset($field_options['sw_coordinates_field_name'])) ? $field_options['sw_coordinates_field_name'] : 'cs_gmaps_drawing_sw_latLng';
			$img_url_field_name = (isset($field_options['img_url_field_name'])) ? $field_options['img_url_field_name'] : 'cs_gmaps_drawing_img_url';
			
			/**
			 * Fields & buttons labels */
			
			$address_label = (isset($field_options['labels']['address'])) ? $field_options['labels']['address'] : 'Address';
			$address_placeholder = (isset($field_options['labels']['address_placeholder'])) ? $field_options['labels']['address_placeholder'] : 'Enter a location or address';
			$coordinates_label = (isset($field_options['labels']['coordinates'])) ? $field_options['labels']['coordinates'] : 'Coordinates';
			$search_label = (isset($field_options['labels']['search'])) ? $field_options['labels']['search'] : 'Search';			
			$pinpoint_label = (isset($field_options['labels']['pinpoint'])) ? $field_options['labels']['pinpoint'] : 'Get pinpoint';
			$clear_overlay_label = (isset($field_options['labels']['clear_overlay'])) ? $field_options['labels']['clear_overlay'] : 'Clear overlay';
			$toggle_label = (isset($field_options['labels']['toggle'])) ? $field_options['labels']['toggle'] : 'Drawing tool';
			$top_descrciption = (isset($field_options['labels']['top_desc'])) ? $field_options['labels']['top_desc'] : '';
			$ne_coordinates_label = (isset($field_options['labels']['ne_label'])) ? $field_options['labels']['ne_label'] : 'North-East coordinates'; 
			$sw_coordinates_label = (isset($field_options['labels']['sw_label'])) ? $field_options['labels']['sw_label'] : 'South-West coordinates'; 
			$copy_ne_label = (isset($field_options['labels']['copy_ne'])) ? $field_options['labels']['copy_ne'] : 'Copy to clipboard'; 
			$copy_sw_label = (isset($field_options['labels']['copy_sw'])) ? $field_options['labels']['copy_sw'] : 'Copy to clipboard'; 
			$img_url_label = (isset($field_options['labels']['img_url_label'])) ? $field_options['labels']['img_url_label'] : 'Image URL'; 
			$render_img_label = (isset($field_options['labels']['render_img_label'])) ? $field_options['labels']['render_img_label'] : 'Project image'; 
			
			/**
			 * Fields & buttons descriptions */
			
			$coordinates_desc = (isset($field_options['fields_desc']['coordinates'])) ? '<p class="cmb2-metabox-description">'.$field_options['fields_desc']['coordinates'].'</p>' : '';
			$ne_coordinates_desc = (isset($field_options['fields_desc']['ne'])) ? '<p class="cmb2-metabox-description">'.$field_options['fields_desc']['ne'].'</p>' : ''; 
			$sw_coordinates_desc = (isset($field_options['fields_desc']['sw'])) ? '<p class="cmb2-metabox-description">'.$field_options['fields_desc']['sw'].'</p>' : ''; 
			$img_url_desc = (isset($field_options['fields_desc']['img_url'])) ? '<p class="cmb2-metabox-description">'.$field_options['fields_desc']['img_url'].'</p>' : ''; 
			
			$this->cs_enqueue_scripts(array(
				'api_key' => $api_key,
				'disable_gmaps_api' => $disable_gmaps_api,
				'disable_gmap3_plugin' => $disable_gmap3_plugin,
			));
		
			$map_id = $field->args('id');		
				
			$output = '';
			
			$inside_class = '';
			
			if(is_bool($toggle) && $toggle){ 
				
				$close_class = $close_toggle ? 'closed' : '';
				$inside_class = 'inside';
				 
           		$output .= '<div class="cs-gmaps-drawing-toggle postbox cmb-row cmb-grouping-organizer '.$close_class.'">
                
                <div class="cmbhandle" title="Click to toggle"><br></div>               
                <h3 class="cmb-group-organizer-title cmbhandle-title">'.$toggle_label.'</h3>';

           	}
			
			
			/**
			 * Search address field */
					
			$output .= '<div class="cs_gmaps_drawing_container '.$inside_class.'" data-map-id="'.$map_id.'" data-map-center="'.$map_center.'" data-map-zoom="'.$map_zoom.'" data-draw-mode="'.$draw_mode.'">';
				
				$output .= '<p class="cmb2-metabox-top-description">'.$top_descrciption.'</p>';
				
				$output .= '<div>';
							
					$output .= '<div><label for="" class="cs-gmaps-drawing-label">'.$address_label.'</label></div>';
						
					$output .= '<div style="float:left; width:60%; margin-right:1em;">';
						
						$output .= $field_type_object->input( array(
							'type' => 'text',
							'name' => $field->args('_name') . '[cs-gmaps-drawing-address]',
							'value' => '',
							'class' => 'cs-gmaps-drawing-search cs-gmaps-drawing-preventDefault',
							'desc' => '',
							'data-map-id' => $map_id,
							'placeholder' => $address_placeholder,
						) );
					
					$output .= '</div>';
					
					/**
					 * Search buttons */
					 
					$output .= '<div style="float:left; width:35%;">';
						$output .= '<input type="button" id="cs_gmaps_drawing_search_btn" class="button cs-gmaps-drawing-btn cs_gmaps_drawing_search_btn" data-map-id="'.$map_id.'" style="margin-right:0.5em;" value="'.$search_label.'" />';
						$output .= '<button id="cs_gmaps_drawing_geoloc_btn" class="button cs-gmaps-drawing-btn cs_gmaps_drawing_geoloc_btn" data-map-id="'.$map_id.'" title="Find your position"><img src="'.$this->plugin_url.'target.svg" /></button>';
					$output .= '</div>';
					
					$output .= '<div style="clear:both;"></div>';
					
					/**
					 * Dragging mode option */
					 
					$output .= '<div class="cs_gmaps_drawing_drag_mode cs_gmaps_drawing_check_mode">';
						$output .= '<input type="checkbox" id="dragging_mode_'.$map_id.'" name="dragging_mode_'.$map_id.'" class="cs-gmaps-drawing-preventDefault" data-map-id="'.$map_id.'" data-draw-mode="'.$draw_mode.'" /><label for="dragging_mode_'.$map_id.'">Activate dragging mode</label>';
					$output .= '</div>';
					
					/**
					 * Image Overlay Low Opacity option */
					
					if($draw_mode == 'image'){ 
						$output .= '<div class="cs_gmaps_drawing_img_low_opacity cs_gmaps_drawing_check_mode">';
							$output .= '<input type="checkbox" id="img_low_opacity_'.$map_id.'" name="img_low_opacity_'.$map_id.'" class="cs-gmaps-drawing-preventDefault" data-map-id="'.$map_id.'" data-draw-mode="'.$draw_mode.'" /><label for="img_low_opacity_'.$map_id.'">Activate image low opacity</label>';
						$output .= '</div>';
					}
					
					$output .= '<div style="clear:both;"></div>';
					
				$output .= '</div>';
				
				/**
				 * Map */
				 
				$output .= '<div id="cs_gmaps_'.$map_id.'" class="cs-gmaps-drawing-map" data-map-id="'.$map_id.'" style="height:'.$map_height.'; width:'.$map_width.'"></div>';
				
				/** 
				 * Coordinates fields */
				 
				$output .= '<div>';
					
					if($draw_mode == 'image'){
						
						/**
						 * Image URL */
						
						$output .= '<div style="padding:10px 0 5px 0">';
						
							$output .= '<div style="width:60%; float:left; margin-right:1em">';
								
								$output .= '<label for="'.$img_url_field_name.'" class="cs-gmaps-drawing-label">'.$img_url_label.'</label>';
								
								$value = isset( $field_escaped_value[$img_url_field_name] ) ? $field_escaped_value[$img_url_field_name] : '';
															
								$output .= $field_type_object->file( 
									array(
										'name' => $field->args('_name') . '['.$img_url_field_name.']',
										'value' => (!empty($value)) ? $value : '',
										'class' => 'cs-gmaps-drawing-img-url cs-gmaps-drawing-preventDefault '.$map_id.' cmb2-upload-file regular-text',
										'id' => $img_url_field_name,
										'desc' => $img_url_desc,
										'data-map-id' => $map_id,
										'required' => (isset($field_options['required'])) ? (bool) $field_options['required'] : false,
										'preview_size' => array(100, 100),
										'query_args' => array(
											'type' => 'image',
										),																		
									)
								);
							
							$output .= '</div>';
							
							/**
							 * Render image button */
							 
							$output .= '<div style="width:35%; float:right; margin-top:23px;">';
								$output .= '<input type="button" id="cs_gmaps_drawing_render_img" class="button cs-gmaps-drawing-btn cs_gmaps_drawing_render_img" data-map-id="'.$map_id.'" data-draw-mode="'.$draw_mode.'" value="'.$render_img_label.'" style="float: right; width: 100%;" />';
							$output .= '</div>';
							
							$output .= '<div style="clear:both;"></div>';
						
						$output .= '</div>';						
						
						/**
						 * North-East Coordinates */
						 
						$output .= '<div style="padding:10px 0 5px 0">';
						
							if(!$save_coordinates)
								$output .= '<div style="width:60%; float:left; margin-right:1em">';
							
								$output .= '<label for="'.$ne_coordinates_field_name.'" class="cs-gmaps-drawing-label">'.$ne_coordinates_label.'</label>';
								
								$value = isset( $field_escaped_value[$ne_coordinates_field_name] ) ? $field_escaped_value[$ne_coordinates_field_name] : '';
															
								$output .= $field_type_object->input( 
									array_merge(
										array(
											'type' => 'text',
											'name' => $field->args('_name') . '['.$ne_coordinates_field_name.']',
											'value' => (!empty($value)) ? $value : '',
											'class' => 'cs-gmaps-drawing-ne-coordinates cs-gmaps-drawing-preventDefault',
											'id' => $ne_coordinates_field_name,
											'desc' => $ne_coordinates_desc,
											'data-map-id' => $map_id,
											'required' => (isset($field_options['required'])) ? (bool) $field_options['required'] : false,
										),
										$readonly
									)
								);
							
							if(!$save_coordinates)
								$output .= '</div>';
					
							/**
							 * Copy North-East button */
							
							if(!$save_coordinates){ 
								$output .= '<div style="width:35%; float:right; margin-top:23px;">';
									$output .= '<input type="button" disabled="disabled" id="cs_gmaps_drawing_copy_ne_coordinates" class="button cs-gmaps-drawing-btn cs-gmaps-copy-btn cs_gmaps_drawing_copy_ne_coordinates" data-map-id="'.$map_id.'" value="'.$copy_ne_label.'" style="float: right; width: 100%;" />';
								$output .= '</div>';
							}
							
							$output .= '<div style="clear:both;"></div>';
											
						$output .= '</div>';
						
						/**
						 * South-West Coordinates */
						 
						$output .= '<div style="padding:10px 0 5px 0">';
						
							if(!$save_coordinates)
								$output .= '<div style="width:60%; float:left; margin-right:1em">';
							
								$output .= '<label for="'.$sw_coordinates_field_name.'" class="cs-gmaps-drawing-label">'.$sw_coordinates_label.'</label>';
								
								$value = isset( $field_escaped_value[$sw_coordinates_field_name] ) ? $field_escaped_value[$sw_coordinates_field_name] : '';
															
								$output .= $field_type_object->input( 
									array_merge(
										array(
											'type' => 'text',
											'name' => $field->args('_name') . '['.$sw_coordinates_field_name.']',
											'value' => (!empty($value)) ? $value : '',
											'class' => 'cs-gmaps-drawing-sw-coordinates cs-gmaps-drawing-preventDefault',
											'id' => $sw_coordinates_field_name,
											'desc' => $sw_coordinates_desc,
											'data-map-id' => $map_id,
											'required' => (isset($field_options['required'])) ? (bool) $field_options['required'] : false,
										),
										$readonly
									)
								);
							
							if(!$save_coordinates)
								$output .= '</div>';
					
							/**
							 * Copy South-West button */
							
							if(!$save_coordinates){ 
								$output .= '<div style="width:35%; float:right; margin-top:23px;">';
									$output .= '<input type="button" disabled="disabled" id="cs_gmaps_drawing_copy_sw_coordinates" class="button cs-gmaps-drawing-btn cs-gmaps-copy-btn cs_gmaps_drawing_copy_sw_coordinates" data-map-id="'.$map_id.'" value="'.$copy_sw_label.'" style="float: right; width: 100%;" />';
								$output .= '</div>';
							}
							
							$output .= '<div style="clear:both;"></div>';
											
						$output .= '</div>';
						
					}else{
						
						$output .= '<div style="width:100%;">';
								
							$output .= '<label for="'.$coordinates_field_name.'" class="cs-gmaps-drawing-label" style="float:left;">'.$coordinates_label.'</label>';
							
							/*if($draw_mode == 'polygon'){
								$output .= '<div class="cs_gmaps_drawing_polygon_direction"><u>Note:</u> This polygon is oriented <strong class="poly_direction"></strong>!</div>';																		
								$output .= '<div style="clear:both;"></div>';
							}*/
							
							$value = isset( $field_escaped_value[$coordinates_field_name] ) ? $field_escaped_value[$coordinates_field_name] : $field_escaped_value;
														
							$output .= $field_type_object->textarea( 
								array_merge(
									array(
										'name' => $field->args('_name') . '['.$coordinates_field_name.']',
										'value' => (!empty($value)) ? $value : '',
										'class' => 'cs-gmaps-drawing-coordinates',
										'id' => $coordinates_field_name,
										'desc' => $coordinates_desc,
										'rows' => 5,
										'data-map-id' => $map_id,
										'data-latlng-order' => $latLng_order, //@since 1.1										
										'required' => (isset($field_options['required'])) ? (bool) $field_options['required'] : false,
									),
									$readonly
								)
							);
						
						$output .= '</div>';
						
					}
							
					/**
					 * Pinpoint buttons */
					
					$copy_btn_class = ($draw_mode != 'image') ? '' : 'cs-gmaps-copy-btn';
					 
					$output .= '<div style="width:auto float:right; margin-top:10px;">';												
						$output .= '<input type="button" id="cs_gmaps_drawing_get_latLngs" class="button cs-gmaps-drawing-btn '.$copy_btn_class.' cs_gmaps_drawing_get_latLngs" data-map-id="'.$map_id.'" data-draw-mode="'.$draw_mode.'" data-copy-clipboard="'.$copy_clipboard.'" value="'.$pinpoint_label.'" />';
						$output .= '<input type="button" id="cs_gmaps_drawing_clear_overlay" class="button cs-gmaps-drawing-btn cs_gmaps_drawing_clear_overlay" data-map-id="'.$map_id.'" data-draw-mode="'.$draw_mode.'" value="'.$clear_overlay_label.'" />';
						$output .= '<div style="clear:both;"></div>';
						$output .= '<div class="cs_gmaps_drawing_copied_notice"><span>Coordinates copied to clipboard!</span></div>';
					$output .= '</div>';
					
					$output .= '<div style="clear:both;"></div>';
					
				$output .= '</div>';
				
				$output .= '<p class="cmb2-metabox-description">'.$field->args('desc').'</p>';
			
			$output .= '</div>';
			
			if(is_bool($toggle) && $toggle){ 
				$output .= '</div>';
           	}
									
			echo $output;		
		
		}
		
	
		/**
		 * Optionally save the latitude/longitude values into separated custom fields
		 *
		 * @since 1.0
		 */
		public function cs_sanitize_gmaps_drawing_field( $override_value, $value, $object_id, $field_args ) {
			
			if($object_id === 0)
				return;

			$field_options = $field_args['options'];
			
			$save_coordinates = (isset($field_options['save_coordinates'])) ? $field_options['save_coordinates'] : true;
			$draw_mode = (isset($field_options['draw_mode'])) ? $field_options['draw_mode'] : 'polygon';			
					 
			/**
			 * Get all field names */
				
			$coordinates_field_name = (isset($field_options['coordinates_field_name'])) ? $field_options['coordinates_field_name'] : 'cs_gmaps_drawing_latLngs';
			
			unset($value['cs-gmaps-drawing-address']);
			
			/**
			 * Save our fields 
				
			if(isset($value[$coordinates_field_name]) && $save_coordinates)
				update_post_meta($object_id, $coordinates_field_name, $value[$coordinates_field_name]);*/
						
			return ($save_coordinates) ? ($draw_mode == 'image') ? str_replace(' ', '', $value) : str_replace(' ', '', $value[$coordinates_field_name]) : '';
			
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
								
			wp_enqueue_script('cs-gmaps-drawing-script', plugins_url( 'js/script.js', __FILE__ ), array('jquery'), self::VERSION);
			
			/**
			 * Custom CSS */
								
			wp_enqueue_style('cs-gmaps-drawing-style', plugins_url( 'css/style.css', __FILE__ ), array(), self::VERSION);
			
		}
		
	}
	$CS_CMB2_GMaps_Drawing_Field = new CS_CMB2_GMaps_Drawing_Field();

}