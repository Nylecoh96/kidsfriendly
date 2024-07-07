<?php

if(!class_exists('CspmFrontendForm')){
	
	class CspmFrontendForm{
		
		private static $_this;	
		
		public $plugin_settings = array();

		function __construct(){
			
			if (!class_exists('CspmMainMap'))
				return; 
		
			self::$_this = $this;       

			$CspmMainMap = CspmMainMap::this();
			 
			$this->plugin_settings = $CspmMainMap->plugin_settings;
			
		}
		
	
		static function this(){
			
			return self::$_this;
			
		}
		
		
		function cspm_hooks(){
			
			if(!is_admin()){
				
				/**
				 * Add map's shortcode */
					
				add_shortcode('cspm_frontend_form', array($this, 'cspm_frontend_form'));
				add_shortcode('progress_map_add_location_form', array($this, 'cspm_frontend_form')); //@deprecated since 3.0
				
			}

		}
		
		
		/**
		 * This will load the styles needed by our shortcodes based on its settings
		 *
		 * @since 3.0
		 */
		function cspm_enqueue_styles(){
			
			do_action('cspm_before_enqueue_frontent_form_style');
							
			/**
			 * Font Style */
			
			if($this->plugin_settings['remove_google_fonts'] == 'enable')  	
				wp_enqueue_style('cspm-font');
				
			/**
			 * Bootstrap */
			
			if($this->plugin_settings['remove_bootstrap'] == 'enable')
				wp_enqueue_style('cspm-custom-bootstrap');
			
			do_action('cspm_after_enqueue_frontent_form_style');
			
		}
		
		
		/**
		 * This will load the scripts needed by our shortcodes based on its settings
		 *
		 * @since 3.0
		 */
		function cspm_enqueue_scripts(){
			
			/**
			 * jQuery */
			 
			wp_enqueue_script('jquery');				 			

			do_action('cspm_before_enqueue_frontent_form_script');
			
			/**
			 * GMaps API */
			
			if(!in_array('disable_frontend', $this->plugin_settings['remove_gmaps_api']))				 
				wp_enqueue_script('cs-gmaps-api-v3');
			
			$localize_script_handle = 'cspm-script';
				
			/**
			 * GMap3 jQuery Plugin */
			 
			wp_enqueue_script('cspm-gmap3');
				
			/**
			 * Progress Map Script */
			 
			wp_enqueue_script($localize_script_handle);

			do_action('cspm_after_enqueue_frontent_form_script');
			
		}
		
		
		/**
		 * Save locations from the frontend using the frontend form
		 *
		 * @since 2.6.3
		 * @updated 3.5
		 */
		function cspm_save_frontend_location($atts = array()){
		
			$defaults = array(
				'post_id' => '',
				'post_type' => '',
				'latitude' => '',
				'longitude' => '',
			);
			
			extract(wp_parse_args($atts, $defaults));
				
			global $wpdb;
			
			$post_id = (isset($post_id) && !empty($post_id)) ? esc_attr($post_id) : $wpdb->insert_id;
			
			if(!empty($post_type) && !empty($post_id) && !empty($latitude) && !empty($longitude)){
			
				update_post_meta($post_id, CSPM_LATITUDE_FIELD, $latitude);
				update_post_meta($post_id, CSPM_LONGITUDE_FIELD, $longitude);
					
				if (!class_exists('CSProgressMap'))
					return; 
					
				$CSProgressMap = CSProgressMap::this();
				
				$CSProgressMap->cspm_save_marker_object($post_id, get_post($post_id)); //@since 3.5
				
			}
			
		}
		
			
		/**
		 * A form to add locations from the frontend
		 *
		 * @since 2.6.3 
		 * @updated 2.7 
		 */
		function cspm_frontend_form($atts){
			
			/**
			 * Prevent the shortcode from been executed in the WP admin.
			 * This will prevent errors like the error "headers already sent"!
			 * @since 3.4 */
			 
			if(is_admin())
				return;
				
			if (!class_exists('CspmMainMap'))
				return; 
				
			$CspmMainMap = CspmMainMap::this();

			extract( shortcode_atts( array(
			  
				'map_id' => 'frontend_form_map',
				'post_id' => '',
				'post_type' => '',			  
				'embed' => 'no',
				'center_at' => '',
				'height' => '400px',
				'width' => '100%',
				'zoom' => 5,
				'map_style' => '',
				'initial_map_style' => esc_attr($this->plugin_settings['initial_map_style']),

			), $atts, 'cspm_frontend_form' ) ); 
					
			$map_id = esc_attr($map_id);
			
			/**
			 * Default center point */
			 
			$centerLat = 51.53096;
			$centerLng = -0.121064;
			
			/**
			 * Get the center point */
			 
			if(!empty($center_at)){
				
				$center_point = esc_attr($center_at);
			
				if(strpos($center_point, ',') !== false){
						
					$center_latlng = explode(',', str_replace(' ', '', $center_point));
	
					/**
					 * Get lat and lng data */
					 
					$centerLat = isset($center_latlng[0]) ? $center_latlng[0] : 37.09024;
					$centerLng = isset($center_latlng[1]) ? $center_latlng[1] : -95.71289100000001;
					
				}			
				
			}
			
			/**
			 * Map Styling */
			 
			$this_map_style = empty($map_style) ? $this->plugin_settings['map_style'] : esc_attr($map_style);
							
			$map_styles = array();
			
			if($this->plugin_settings['style_option'] == 'progress-map'){
					
				/**
				 * Include the map styles array	*/
		
				if(file_exists($CspmMainMap->map_styles_file))
					$map_styles = include($CspmMainMap->map_styles_file);
						
			}elseif($this->plugin_settings['style_option'] == 'custom-style' && !empty($this->plugin_settings['js_style_array'])){
				
				$this_map_style = 'custom-style';
				$map_styles = array('custom-style' => array('style' => $this->plugin_settings['js_style_array']));
				
			}
			
			ob_start(); //@since 4.9.1
			
			?>
			
			<script>
			
			jQuery(document).ready(function($){ 
				
				"use strict"; 
				
				/**
				 * init plugin map */
				 
				var plugin_map_placeholder = 'div#cspm_frontend_form_<?php echo $map_id; ?>';
				var plugin_map = $(plugin_map_placeholder);
				
				/**
				 * Activate the new google map visual */
				 
				google.maps.visualRefresh = true;
				
				var map_options = { center:[<?php echo $centerLat; ?>, <?php echo $centerLng; ?>],
									zoom: <?php echo esc_attr($zoom); ?>,
									scrollwheel: false,
									zoomControl: true,
									//panControl: true,
									mapTypeControl: true,
									streetViewControl: true,
								  };
				
				/**
				 * The initial map style */
				 
				var initial_map_style = "<?php echo $initial_map_style; ?>";
				
				<?php if(count($map_styles) > 0 && $this_map_style != 'google-map' && isset($map_styles[$this_map_style])){ ?> 
										
					/**
					 * The initial style */
					 
					var map_type_id = cspm_initial_map_style(initial_map_style, true);
												
					var map_options = $.extend({}, map_options, map_type_id);
					
				<?php }else{ ?>
										
					/**
					 * The initial style */
					 
					var map_type_id = cspm_initial_map_style(initial_map_style, false);
												
					var map_options = $.extend({}, map_options, map_type_id);
					
				<?php } ?>
				
				var map_id = 'frontend_form_<?php echo $map_id ?>';
				
				_CSPM_MAP_RESIZED[map_id] = 0;	
				
				/**
				 * Create the map */
				 
				plugin_map.gmap3({	
						  
					map:{
						options: map_options,						
					},
					
					/**
					 * Set the map style */
					 
					<?php if(count($map_styles) > 0 && $this_map_style != 'google-map' && isset($map_styles[$this_map_style])){ ?>
						
						styledmaptype:{
							id: "custom_style",
							styles: <?php echo $map_styles[$this_map_style]['style']; ?>
						}
						
					<?php } ?>
															
				});	
				
				/**
				 * Center the Map on screen resize */
				 
				<?php if(!empty($centerLat) && !empty($centerLng)){ ?>						
					
					/**
					 * Store the window width */
					
					var windowWidth = $(window).width();

					$(window).resize(function(){
						
						/**
						 * Check window width has actually changed and it's not just iOS triggering a resize event on scroll */
						 
						if ($(window).width() != windowWidth) {
				
							/**
							 * Update the window width for next time */
							 
							windowWidth = $(window).width();
		
							setTimeout(function(){
							
								var latLng = new google.maps.LatLng (<?php echo $centerLat; ?>, <?php echo $centerLng; ?>);																														
							
								var map = plugin_map.gmap3("get");
								
								if(typeof map.panTo === 'function')
									map.panTo(latLng);
									
								if(typeof map.setCenter === 'function')
									map.setCenter(latLng);	
									
							}, 500);
							
						}
						
					});
					
				<?php } ?>
				 
				<?php 
				
				/**
				 * Resolve a problem of Google Maps & jQuery Tabs */
				 
				if(!empty($centerLat) && !empty($centerLng)){ ?>

                    $('body').ready(function(){
                        if($(plugin_map_placeholder).is(':visible')){                            
                            if(_CSPM_MAP_RESIZED[map_id] <= 1){ // 0 is for the first loading, 1 is when the user clicks the map tab.
                                cspm_center_map_at_point(plugin_map, '<?php echo $map_id ?>', <?php echo $centerLat; ?>, <?php echo $centerLng; ?>, 'resize');
                                _CSPM_MAP_RESIZED[map_id]++;
                            }
                        }
                    }); //@edited 5.6.3
				
				<?php } ?>

				/**
				 * Add support for the Autocomplete for the address field
				 * @since 2.8 */
				
				var input = document.getElementById('cspm_search_address');
				var autocomplete = new google.maps.places.Autocomplete(input);
										
			});
			
			</script> 
						
			<?php
			
			$map_js_script = str_replace(array('<script>', '</script>'), '', ob_get_contents()); //@since 4.9.1
			ob_end_clean(); //@since 4.9.1
			
			$this->cspm_enqueue_styles();
			$this->cspm_enqueue_scripts();			
			
			wp_add_inline_script('cspm-script', $map_js_script); //@since 4.9.1	
			
			/**
			 * Save the location */
			 
			if(!empty($post_type) && !empty($post_id) && isset($_POST[CSPM_LATITUDE_FIELD], $_POST[CSPM_LONGITUDE_FIELD])){
				
				$this->cspm_save_frontend_location(array(
					'post_id' => esc_attr($post_id),
					'latitude' => esc_attr($_POST[CSPM_LATITUDE_FIELD]),
					'longitude' => esc_attr($_POST[CSPM_LONGITUDE_FIELD]),
					'post_type' => $post_type,
				));	
	
			}
			
			/**
			 * If you want to save the coordinates using PHP, this is the code you need to use to save the location
			 
			if(class_exists('CspmFrontendForm') && isset($_POST[CSPM_LATITUDE_FIELD], $_POST[CSPM_LONGITUDE_FIELD])){			
				$CspmFrontendForm = CspmFrontendForm::this();
				$CspmFrontendForm->cspm_save_frontend_location(array(
					'post_id' => THE_POST_ID,
					'latitude' => esc_attr($_POST[CSPM_LATITUDE_FIELD]),
					'longitude' => esc_attr($_POST[CSPM_LONGITUDE_FIELD]),
					'post_type' => THE_POST_TYPE_NAME,
				));	
			}
			
			*/ 			 

			$output = '';
			
			/**
			 * Use this filter to add some custom code before the frontend form */
			 
			$output .= apply_filters('cspm_before_frontend_form', '');
			
			/**
			 * Use this filter to remove the opening tag */
			 
			if(esc_attr($embed) == 'no')
				$output .= apply_filters('cspm_open_frontend_form_tag', '<form action="" method="post" role="form" class="cspm_frontend_form">');
				
				$output .= '<div class="cspm-row">';

					/**
					 * Use this filter to add some custom code before the search field */
					 
					$output .= apply_filters('cspm_frontend_form_before_search_field', '');
					
					$output .= '<div class="form-group '.apply_filters('cspm_frontend_form_search_field_class', 'cspm-col-lg-10 cspm-col-md-10 cspm-col-sm-10 cspm-col-xs-12').'">
									<label for="cspm_search_address">'.esc_html__('Search', 'cspm').'</label>
									<input type="text" class="form-control" id="cspm_search_address" name="cspm_search_address" placeholder="'.esc_html__('Enter an address and search', 'cspm').'">
								</div>';
					
					$output .= '<div class="form-group '.apply_filters('cspm_frontend_form_search_btn_class', 'cspm-col-lg-2 cspm-col-md-2 cspm-col-sm-2 cspm-col-xs-12').'"> 
									<button type="button" id="cspm_search_btn" class="btn btn-primary" data-map-id="'.$map_id.'">'.esc_html__('Search', 'cspm').'</button>
								</div>';

					/**
					 * Use this filter to add some custom code after the search field */
					 
					$output .= apply_filters('cspm_frontend_form_after_search_field', '');
					
				$output .= '</div>';
				
				$output .= '<div class="cspm-row">';

					/**
					 * Use this filter to add some custom code before the map */
					 
					$output .= apply_filters('cspm_frontend_form_before_map', '');
					
					$output .= '<div id="cspm_frontend_form_'.$map_id.'" class="'.apply_filters('cspm_frontend_form_map_class', 'cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12 cspm_linear_gradient_bg').'" style="height:'.$height.'; width:'.$width.'"></div>';

					/**
					 * Use this filter to add some custom code after the map */
					 
					$output .= apply_filters('cspm_frontend_form_after_map', '');
									
				$output .= '</div>';
				
				$output .= '<div class="cspm-row">';

					/**
					 * Use this filter to add some custom code before the latlng fields */
					 
					$output .= apply_filters('cspm_frontend_form_before_latlng', '');
									
					$output .= '<div class="form-group '.apply_filters('cspm_frontend_form_lat_field_class', 'cspm-col-lg-5 cspm-col-md-5 cspm-col-sm-12 cspm-col-xs-12').'">
									<label for="cspm_latitude">'.esc_html__('Latitude', 'cspm').'</label>
									<input type="text" class="form-control" id="cspm_latitude" name="'.CSPM_LATITUDE_FIELD.'">
								</div>';
				
					$output .= '<div class="form-group '.apply_filters('cspm_frontend_form_lng_field_class', 'cspm-col-lg-5 cspm-col-md-5 cspm-col-sm-12 cspm-col-xs-12').'">
									<label for="cspm_longitude">'.esc_html__('Longitude', 'cspm').'</label>
									<input type="text" class="form-control" id="cspm_longitude" name="'.CSPM_LONGITUDE_FIELD.'">
								</div>';
					
					$output .= '<div class="form-group '.apply_filters('cspm_frontend_form_pinpoint_btn_class', 'cspm-col-lg-2 cspm-col-md-2 cspm-col-sm-12 cspm-col-xs-12').'"> 
									<button type="button" id="cspm_get_pinpoint" class="btn btn-primary cspm_get_pinpoint" data-map-id="'.$map_id.'">'.esc_html__('Get Pinpoint', 'cspm').'</button>
								</div>';
								
					/**
					 * Use this filter to add some custom code after the latlng fields */
					 
					$output .= apply_filters('cspm_frontend_form_after_latlng', '');
					
				$output .= '</div>';
				
				if($embed == 'no'){
					
					$output .= '<div class="cspm-row">';
					
						/**
						 * Use this filter to add some custom code before the submit button */
						 
						$output .= apply_filters('cspm_frontend_form_before_submit_btn', '');
						
						$output .= '<div class="form-group '.apply_filters('cspm_frontend_form_submit_btn_class', 'cspm-col-lg-2 cspm-col-md-2 cspm-col-sm-12 cspm-col-xs-12').'"> 
										<button type="submit" id="cspm_add_location" class="btn btn-primary cspm_add_location" data-map-id="'.$map_id.'">'.esc_html__('Add Location', 'cspm').'</button>
									</div>';
						
						/**
						 * Use this filter to add some custom code after the submit button */
						 
						$output .= apply_filters('cspm_frontend_form_after_submit_btn', '');
														
					$output .= '</div>';
				
				}
				
			/**
			 * Use this filter to remove the closing tag of the form */
			 
			if(esc_attr($embed) == 'no')
				$output .= apply_filters('cspm_close_frontend_form_tag', '</form>');
			
			/**
			 * A filter to add some custom code after the frontend form */
			 
			$output .= apply_filters('cspm_after_frontend_form', '');
			
			return $output;
			
		}				
		
	}
	
}


if(class_exists('CspmFrontendForm')){
	$CspmFrontendForm = new CspmFrontendForm();
	$CspmFrontendForm->cspm_hooks();
}

