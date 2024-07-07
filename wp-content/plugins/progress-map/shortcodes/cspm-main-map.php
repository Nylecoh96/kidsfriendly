<?php
 
if(!defined('ABSPATH')){
    exit; // Exit if accessed directly
}

if(!class_exists('CspmMainMap')){
	
	class CspmMainMap{
		
		private static $_this;	
		
		private $plugin_path;
		private $plugin_url;
		
		public $plugin_settings = array();
		public $map_settings = array();
		
		public $map_styles_file;
		
		public $metafield_prefix; //@since 3.0
		public $object_type; //@since 3.0
		public $map_object_id = ''; //@since 3.0
		
		/**
		 * Other Map settings 
		 * @since 3.3 */
		
		public $map_type = 'normal_map';
		public $optional_latlng = 'false';
		public $single_posts_link = 'default'; //@since 5.0
		
		/**
		 * The URL of the GMaps API
		 * @since 5.2 */
		 
		public $gmaps_unformatted_url = '';
		public $gmaps_formatted_url = '';
		
		/**
		 * Plugin settings */
		 
		public $outer_links_field_name = ''; //@since 2.5	
		public $combine_files = 'seperate_minify'; // @edited 5.6.5
		public $use_with_wpml = 'no'; //@since 2.6.3
		public $remove_bootstrap = 'enable'; //@since 2.8.2
		public $remove_gmaps_api = array(); //@since 2.8.2 //updated 2.8.5	
		public $remove_google_fonts = 'enable'; //@since 2.8.2	
		
		/**
		 * Query settings */
		 
		public $post_type = '';
		public $post_in = '';		
		public $post_not_in = '';		
		public $cache_results = '';
		public $update_post_meta_cache = '';
		public $update_post_term_cache = '';
		public $orderby_param = '';
		public $orderby_meta_key = '';
		public $order_param = '';
		public $number_of_items = '';		
		public $custom_fields = '';		
		public $custom_field_relation_param = '';
		public $post_status = 'publish'; // @since 2.8.2
		public $authors_prefixing = 'false'; //@since 2.8.6
		public $taxonomy_relation_param = 'AND'; //@since 2.8.6
		public $order_meta_type = ''; //@since 3.0
		public $authors_type = 'defined_authors'; //@since 5.6.5
		
		/**
		 * Layout settings */
		
		public $main_layout = 'mu-cd';	
		public $layout_type = 'full_width';
		public $layout_fixed_width = '700';
		public $layout_fixed_height = '600';
		
		/**
		 * Map settings */
		 
		public $center = '51.53096,-0.121064';			
		public $wrong_center_point = false;	
		public $zoom = '12';
		public $mapTypeControl = 'true';
		public $streetViewControl = 'false';
		public $zoomControl = 'true';
		public $zoomControlType = 'customize';					
		public $max_zoom = 19; //@since 2.6.3
		public $min_zoom = 0; //@since 2.6.3		
		public $zoom_on_doubleclick = 'false'; //@since 2.6.4
		public $autofit = 'false'; //@since 2.7
		public $traffic_layer = 'false'; //@since 2.7
		public $transit_layer = 'false'; //@since 2.7.4
		public $recenter_map = 'true'; //@since 3.0
		public $map_background = '#ededed'; //@since 4.2
		public $clickableIcons = 'true'; //@since 4.2
		public $rotateControl = 'false'; //@since 4.2
		public $scaleControl = 'false'; //@since 4.2
		public $fullscreenControl = 'false'; //@since 4.2
		public $controlSize = '39'; //@since 4.8
		public $alternative_api_key = ''; //@since 5.2
		public $alternative_map_language = ''; //@since 5.2
		public $restrict_map_bounds = ''; //@since 5.2
		public $strict_bounds = ''; //@since 5.2
		public $map_bounds_restriction = ''; //@since 5.2
		public $bicycling_layer = 'false'; //@since 5.3
		public $gestureHandling = 'auto'; //@since 5.6.5
					
		/**
		 * Heatmap Layer
		 * @since 3.3
		 * @edited 5.3 */
		 
		public $heatmap_layer = 'true'; //@since 3.3												
		public $heatmap_intensity = ''; //@since 5.3
		public $heatmap_point_weight = ''; //@since 5.3
		public $heatmap_dissipating = ''; //@since 5.3
		public $heatmap_radius = ''; //@since 5.3
		public $heatmap_opacity = ''; //@since 5.3
		public $heatmap_color_type = ''; //@since 5.3
		public $heatmap_colors = ''; //@since 5.3		
		
		/**
		 * Map style settings */
		
		public $initial_map_style = 'ROADMAP';
		public $style_option = 'progress-map';
		public $map_style = 'google-map';	
		public $js_style_array = '';
		public $custom_style_name = 'Custom style'; //@since 2.6.1
		
		/**
		 * Marker settings */

		public $marker_icon = '';				
		public $defaultMarker = '';
		public $retinaSupport = 'false';		
		public $markerAnimation = 'pulsating_circle'; // @since 2.5		
		public $marker_anchor_point_option = 'disable'; //@since 2.6.1
		public $marker_anchor_point = ''; //@since 2.6.1
		public $marker_icon_height = ''; //@since 4.0
		public $marker_icon_width = ''; //@since 4.0
		
		/**
		 * Clustering settings */
		
		public $useClustring = 'true';
		public $gridSize = '60';
		public $big_cluster_icon = '';
		public $big_cluster_icon_height = '90'; //@since 4.0
		public $big_cluster_icon_width = '90'; //@since 4.0		
		public $medium_cluster_icon = '';
		public $medium_cluster_icon_height = '70'; //@since 4.0
		public $medium_cluster_icon_width = '70'; //@since 4.0				
		public $small_cluster_icon = ''; 
		public $small_cluster_icon_height = '50'; //@since 4.0
		public $small_cluster_icon_width = '50'; //@since 4.0				
		public $cluster_text_color = '#ffffff';			
		
		/**
		 * Geotargeting settings */
		
		public $geoIpControl = 'false';
		public $show_user = 'false'; //@since 2.7.4
		public $user_marker_icon = ''; //@since 2.7.4
		public $user_map_zoom = 12; //@since 2.7.4
		public $user_circle = 0; //@since 2.7.4
		public $user_circle_fillColor = '#189AC9'; // @since 3.0
		public $user_circle_fillOpacity = '0.1'; // @since 3.0
		public $user_circle_strokeColor = '#189AC9'; // @since 3.0				
		public $user_circle_strokeOpacity = '1'; // @since 3.0
		public $user_circle_strokeWeight = '1'; // @since 3.0	
		public $user_marker_icon_height = ''; //@since 4.0
		public $user_marker_icon_width = ''; //@since 4.0
		
		/**
		 * Carousel settings */
				
		public $show_carousel = 'true';
		public $carousel_mode = 'false';
		public $carousel_scroll = '1';
		public $carousel_animation = 'fast';
		public $carousel_easing = 'linear';
		public $carousel_auto = '0';
		public $carousel_wrap = 'circular';	
		public $scrollwheel_carousel = 'false';	
		public $touchswipe_carousel = 'false';
		public $move_carousel_on = array('marker_click', 'marker_hover', 'infobox_hover');	
		public $carousel_map_zoom = '12';
		public $sync_carousel_to_viewport = 'no'; //@since 5.0
		public $connect_carousel_with_map = 'yes'; //@since 5.0
		
		/**
		 * Carousel style */
		 
		public $carousel_css = '';	
		public $arrows_background = '#fff';	
		public $horizontal_left_arrow_icon = '';
		public $horizontal_right_arrow_icon = '';	
		public $vertical_top_arrow_icon = '';
		public $vertical_bottom_arrow_icon = '';
		public $items_background = '#fff';	
		public $items_hover_background = '#fbfbfb';	
		
		/**
		 * Carousel items settings */
		 
		public $items_view = 'listview';
		public $items_featured_img = 'show'; //@since 3.7
		public $horizontal_item_css = '';
		public $horizontal_title_css = '';
		public $horizontal_details_css = '';
		public $vertical_item_css = '';
		public $vertical_title_css = '';
		public $vertical_details_css = '';
		public $horizontal_item_size = '454,150'; //@updated 2.8
		public $horizontal_item_width = '454'; //@updated 2.8
		public $horizontal_item_height = '150'; //@updated 2.8
		public $horizontal_image_size = '204,150'; //@updated 2.8
		public $horizontal_img_width = '204'; //@updated 2.8
		public $horizontal_img_height = '150'; //@updated 2.8
		public $horizontal_details_size = '250,150'; //@updated 2.8
		public $horizontal_details_width = '250'; //@updated 2.8
		public $horizontal_details_height = '150'; //@updated 2.8		
		public $vertical_item_size = '204,290'; //@updated 2.8
		public $vertical_item_width = '204'; //@updated 2.8
		public $vertical_item_height =  '290'; //@updated 2.8
		public $vertical_image_size = '204,120'; //@updated 2.8
		public $vertical_img_width = '204'; //@updated 2.8
		public $vertical_img_height = '120'; //@updated 2.8
		public $vertical_details_size = '204,170'; //@updated 2.8
		public $vertical_details_width = '204'; //@updated 2.8
		public $vertical_details_height = '170'; //@updated 2.8
		public $show_details_btn = 'yes';
		public $items_title = '';
		public $click_on_title = 'no'; //@since 2.5	
		public $external_link = 'same_window'; //@since 2.5
		public $items_details = '';
		public $details_btn_css = '';
		public $details_btn_text = 'More';	
		public $ellipses = 'yes';							
		
		/**
		 * Posts count settings */
		 
		public $show_posts_count = 'no';
		public $posts_count_clause = '[posts_count] Posts';
		public $posts_count_color = '#333333';
		public $posts_count_style = '';
		
		/**
		 * Marker categories settings */
		 
		public $marker_cats_settings = 'false';
		public $marker_categories_taxonomy = '';
		
		/**
		 * Faceted search settings */
		 
		public $faceted_search_option = 'false';
		public $faceted_search_multi_taxonomy_option = 'true';
		public $faceted_search_input_skin = 'polaris';
		public $faceted_search_input_color = 'blue';
		public $faceted_search_css = '';
		public $faceted_search_drag_map = 'autofit'; //@since 2.8.2
		public $faceted_search_autocheck = 'false'; //@since 3.0
		public $faceted_autocheck_terms = 'false'; //@since 3.0
		public $faceted_search_display_status = 'close'; //@since 3.0
		
		/**
		 * Search form settings */
		 
		public $search_form_option = 'false';
		public $sf_min_search_distances = '3';
		public $sf_max_search_distances = '50';
		public $sf_distance_unit = 'metric';
		public $address_placeholder = 'Enter City & Province, or Postal code';
		public $slider_label = 'Expand the search area up to';
		public $no_location_msg = 'We could not find any location';
		public $bad_address_msg = 'We could not understand the location';
		public $bad_address_sug_1 = '- Make sure all street and city names are spelled correctly.';
		public $bad_address_sug_2 = '- Make sure your address includes a city and state.';
		public $bad_address_sug_3 = '- Try entering a zip code.';		
		public $submit_text = 'Search';
		public $search_form_bg_color = 'rgba(255,255,255,0.95)';
		public $circle_option = 'true';
		public $fillColor = '#189AC9';
		public $fillOpacity = '0.1';
		public $strokeColor = '#189AC9';				
		public $strokeOpacity = '1';
		public $strokeWeight = '1';		
		public $sf_display_status = 'close'; //@since 3.0	
		public $sf_edit_circle = 'true'; //@since 3.0
		public $sf_geoIpControl = 'true'; //@since 5.3
					
		/**
		 * Infobox settings */
		 
		public $show_infobox = 'true'; // @since 2.5		
		public $infobox_type = 'rounded_bubble'; // @since 2.5		
		public $infobox_display_event = 'onload'; // @since 2.5		
		public $infobox_external_link = 'same_window'; // @since 2.5	
		public $remove_infobox_on_mouseout = 'false'; //@since 2.7.4	
		public $infobox_width = ''; //@since 4.0
		public $infobox_height = ''; //@since 4.0
		public $infobox_title = ''; //@since 5.0
		public $infobox_details = ''; //@since 5.0
		public $infobox_ellipses = 'yes'; //@since 5.0
		public $infobox_display_zoom_level = 12; //@since 5.3
		public $infobox_show_close_btn = 'false'; //@since 5.3
				
		/**
		 * KML Layers */
		 
		public $use_kml = 'false'; //@since 2.7
		public $kml_layers = ''; //@since 2.7
		public $kml_list = 'false'; //@since 5.6
		public $kml_list_display_status = 'close'; //@since 5.6
		public $kml_list_placeholder = ''; //@since 5.6		
        public $kml_layers_onload_status = ''; //@since 5.6.7	
		
		/**
		 * Overlays: Images
		 * @since 3.5 */
		 
		public $ground_overlays_option = 'false';
		public $ground_overlays = '';
		public $ground_overlay_clickable = '';
		
		/**
		 * Overlays: Polyline */
		 
		public $draw_polyline = 'false'; //@since 2.7
		public $polylines = ''; //@since 2.7		
		
		/**
		 * Overlays: Polygon */
		 
		public $draw_polygon = 'false'; //@since 2.7
		public $polygons = ''; //@since 2.7		
		
		/**
		 * Overlays: Holes
		 * @since 5.3 */
		 
		public $map_holes_option = 'false';
		//public $map_holes_union = 'false';
		public $map_holes_style = '';
		public $map_holes = '';
		
		/**
		 * Zoom to country 
		 * @since 3.0 */				
		
		public $zoom_country_option = 'false'; 
		public $zoom_country_display_status = 'close';
		public $country_zoom_or_autofit = 'autofit';
		public $country_zoom_level = '12';
		public $countries_display_lang = 'en';
		public $country_flag = 'true';
		public $countries = array();
		public $country_list_placeholder = ''; //@since 5.6		
		
		/**
		 * Nearby points of interest
		 * @since 3.2 */
		
		public $nearby_places_option = 'false';
		public $np_proximities_display_status = 'close';
		public $np_distance_unit = 'METRIC';
		public $np_radius = '5000'; //@since 4.7
		public $np_min_radius = '1000'; //@since 4.7
		public $np_max_radius = '50000';
		public $np_circle_option = 'true';
		public $np_edit_circle = 'true';
		public $np_marker_type = 'default';
		public $show_proximity_icon = 'true';
		public $np_proximities = array();	
		public $np_list_placeholder = ''; //@since 5.6
		 
		/**
		 * Customize */
		
		public $custom_css = '';
		public $map_horizontal_elements_order = array(); //@since 3.2
		public $map_vertical_elements_order = array(); //@since 3.2
		public $zoom_in_icon = '';
		public $zoom_out_icon = '';
		public $faceted_search_icon = '';
		public $search_form_icon = '';
		public $countries_btn_icon = '';
		public $target_icon = ''; //@since 3.8
		public $recenter_icon = ''; //@since 3.8
		public $heatmap_icon = ''; //@since 3.8
		public $nearby_icon = ''; //@since 3.8
		public $multicolor_svg = 'no'; //@since 3.8
		public $kml_list_icon = ''; //@since 5.6
					
		/**
		 * Marker labels settings
		 * @since 4.0 */
		
		public $marker_labels = 'no';
		public $marker_labels_type = 'manual';
		public $marker_labels_color = '#000000';
		public $marker_labels_fontFamily = 'monospace';
		public $marker_labels_fontWeight = 'bold';
		public $marker_labels_fontSize = '14px';
		public $marker_labels_top_position = '';
		public $marker_labels_left_position = '';
					
		/**
		 * Autocomplete settings
		 * @since 4.0 */
		
		public $autocomplete_option = 'yes';
		public $autocomplete_strict_bounds = 'no';
		public $autocomplete_country_restrict = 'no';
		public $autocomplete_countries = array();
		
		/**
		 * Marker popup settings 
		 * @since 4.0 */
		
		public $use_marker_popups = 'no';
		public $marker_popups_placement = 'right';	
		public $marker_popups_content_type = 'custom_field';		 
		public $marker_popups_custom_field = '';		 
		public $marker_popups_taxonomy = '';		 
		public $marker_popups_before_content = '';		 
		public $marker_popups_after_content = '';		 
		public $marker_popups_bg_color = '#ffffff';		 
		public $marker_popups_color = '#000000';
		public $marker_popups_fontSize = '14px';
		public $marker_popups_fontWeight = 'bold';
		
		/**
		 * Queried post ids 
		 * @since 4.8 */
		
		public $queried_post_ids = array();
		
		/**
		 * Marker menu settings 
		 * @since 5.5 */
		
		public $use_marker_menu = 'no';
		public $single_post_menu_item = array();
		public $media_modal_menu_item = array();
		public $proximities_menu_item = array();
		public $nearby_map_menu_item = array();	
		public $directions_menu_item = array();	
		public $marker_menu_items_order = array();	
		public $marker_menu_placement = 'right';	
		public $marker_menu_open_leftclick = 'no';	
		public $marker_menu_open_rightclick = 'yes';
		public $marker_menu_open_mouse_enter = 'no';	
		public $marker_menu_close_mouse_out = 'no';	
		public $marker_menu_close_map_click = 'yes';	
		public $marker_menu_show_close_btn = 'yes';	
		public $marker_menu_bg_color = '#ffffff';		 
		public $marker_menu_color = '#000000';
		public $marker_menu_fontSize = '13px';
		public $marker_menu_fontWeight = 'normal';
		
		function __construct($atts = array()){
	
			if (!class_exists('CSProgressMap'))
				return; 
				
			$CSProgressMap = CSProgressMap::this();
			
			extract( wp_parse_args( $atts, array(
				'init' => false, 
				'plugin_settings' => array(),
				'map_settings' => array(), 
				'metafield_prefix' => '',
				'object_type' => '',
			)));

			self::$_this = $this;       
			
			$this->plugin_path = $CSProgressMap->cspm_plugin_path;
			$this->plugin_url = $CSProgressMap->cspm_plugin_url;
				
			$this->map_styles_file = $this->plugin_path.'inc/cspm-map-styles.php';
			
			$this->metafield_prefix = $metafield_prefix;
			$this->object_type = $object_type;
			
			$this->gmaps_unformatted_url = $CSProgressMap->gmaps_unformatted_url; //@since 5.2
			$this->gmaps_formatted_url = $CSProgressMap->gmaps_formatted_url; //@since 5.2
			
			/**
			 * Get plugin settings */
			 
			$this->plugin_settings = $plugin_settings;

			if(!$init){
				
				/**
				 * Get all map settings */
				 
				$this->map_settings = $map_settings;				

				/**
				 * [@map_object_id] | The ID of the map
				 * @since 3.0 */
				 
				$this->map_object_id = isset($this->map_settings['map_object_id']) ? $this->map_settings['map_object_id'] : '';
				
				if(!is_admin()){

					/**
					 * Plugin settings */
					
					$this->outer_links_field_name = $this->cspm_get_plugin_setting('outer_links_field_name');
					$this->use_with_wpml = $this->cspm_get_plugin_setting('use_with_wpml');
					$this->combine_files = $this->cspm_get_plugin_setting('combine_files');
					$this->remove_bootstrap = $this->cspm_get_plugin_setting('remove_bootstrap');
					$this->remove_google_fonts = $this->cspm_get_plugin_setting('remove_google_fonts');
					$this->remove_gmaps_api = $this->cspm_get_plugin_setting('remove_gmaps_api', array());
		
					/**
					 * Other Map settings 
					 * @since 3.3 */
					
					$this->map_type = $this->cspm_get_map_option('map_type', 'normal_map');
					$this->optional_latlng = $this->cspm_get_map_option('optional_latlng', 'false');
					$this->single_posts_link = $this->cspm_get_map_option('single_posts_link', 'default'); //@since 5.0
					
					/**
					 * Query settings */

					$this->post_type = $this->cspm_get_map_option('post_type', ''); 
					$this->number_of_items = $this->cspm_get_map_option('number_of_items');		
					$this->custom_fields = $this->cspm_get_map_option('custom_fields', array());
					$this->custom_field_relation_param = $this->cspm_get_map_option('custom_field_relation_param');
					$this->post_in = $this->cspm_get_map_option('post_in', array());
					$this->post_not_in = $this->cspm_get_map_option('post_not_in', array());
					$this->cache_results = $this->cspm_get_map_option('cache_results');
					$this->update_post_meta_cache = $this->cspm_get_map_option('update_post_meta_cache');
					$this->update_post_term_cache = $this->cspm_get_map_option('update_post_term_cache');
					$this->orderby_param = $this->cspm_get_map_option('orderby_param');
					$this->orderby_meta_key = $this->cspm_get_map_option('orderby_meta_key');
					$this->order_param = $this->cspm_get_map_option('order_param');
					$this->authors_prefixing = $this->cspm_get_map_option('authors_prefixing', 'false'); //@since 2.8.6
					$this->authors_type = $this->cspm_get_map_option('authors_type', 'defined_authors'); //@since 5.6.5
					$this->authors = $this->cspm_get_map_option('authors', array());
					$this->taxonomy_relation_param = $this->cspm_get_map_option('taxonomy_relation_param', 'AND'); //@since 2.8.6								
					$this->post_status = $this->cspm_get_map_option('items_status', array('publish'));
					$this->order_meta_type = $this->cspm_get_map_option('order_meta_type'); //@since 3.0
						
					/**
					 * Layout settings */
					 
					$this->main_layout = $this->cspm_get_map_option('main_layout', 'mu-cd');	
					$this->layout_type = $this->cspm_get_map_option('layout_type', 'full_width');
					$this->layout_fixed_width = $this->cspm_get_map_option('layout_fixed_width', '700');
					$this->layout_fixed_height = $this->cspm_get_map_option('layout_fixed_height', '600');
						
					/**
					 * Map settings */
					 
					$this->center = $this->cspm_get_map_option('map_center', '51.53096,-0.121064');			
						$this->wrong_center_point = (strpos($this->center, ',') !== false) ? false : true;
															
					$this->zoom = $this->cspm_get_map_option('map_zoom', '12');
					$this->mapTypeControl = $this->cspm_get_map_option('mapTypeControl', 'true');
					$this->streetViewControl = $this->cspm_get_map_option('streetViewControl', 'false');
					$this->zoomControl = $this->cspm_get_map_option('zoomControl', 'true');
					$this->zoomControlType = $this->cspm_get_map_option('zoomControlType', 'customize');
					$this->max_zoom = $this->cspm_get_map_option('max_zoom', 19); // @since 2.6.3
					$this->min_zoom = $this->cspm_get_map_option('min_zoom', 0); // @since 2.6.3
					$this->zoom_on_doubleclick = $this->cspm_get_map_option('zoom_on_doubleclick', 'false'); // @since 2.6.3												
					$this->autofit = $this->cspm_get_map_option('autofit', 'false'); // @since 2.7												
					$this->traffic_layer = $this->cspm_get_map_option('traffic_layer', 'false'); // @since 2.7
					$this->transit_layer = $this->cspm_get_map_option('transit_layer', 'false'); // @since 2.7.4
					$this->recenter_map = $this->cspm_get_map_option('recenter_map', 'true'); // @since 3.0											
					$this->map_background = $this->cspm_get_map_option('map_background', '#ededed'); //@since 4.2
					$this->clickableIcons = $this->cspm_get_map_option('clickableIcons', 'true'); //@since 4.2
					$this->rotateControl = $this->cspm_get_map_option('rotateControl', 'false'); //@since 4.2
					$this->scaleControl = $this->cspm_get_map_option('scaleControl', 'false'); //@since 4.2
					$this->fullscreenControl =  $this->cspm_get_map_option('fullscreenControl', 'false'); //@since 4.2
					$this->controlSize = $this->cspm_get_map_option('controlSize', '39'); //@since 4.8
					$this->alternative_api_key =  $this->cspm_get_map_option('alternative_api_key', $this->cspm_get_plugin_setting('api_key')); //@since 5.2
					$this->alternative_map_language =  $this->cspm_get_map_option('alternative_map_language', $this->cspm_get_plugin_setting('map_language')); //@since 5.2
					$this->restrict_map_bounds = $this->cspm_get_map_option('restrict_map_bounds', 'no'); //@since 5.2
					$this->strict_bounds = $this->cspm_get_map_option('strict_bounds'); //@since 5.2
					$this->map_bounds_restriction = $this->cspm_get_map_option('map_bounds_restriction', 'relaxed'); //@since 5.2
					$this->bicycling_layer = $this->cspm_get_map_option('bicycling_layer', 'false'); // @since 5.3
					$this->gestureHandling = $this->cspm_get_map_option('gestureHandling', 'auto'); // @since 5.6.5
					
					/**
					 * Heatmap Layer
					 * @since 3.3
					 * @edited 5.3 */
										
					$this->heatmap_layer = $this->cspm_get_map_option('heatmap_layer', 'false'); // @since 3.3
					$this->heatmap_intensity = $this->cspm_get_map_option('heatmap_intensity'); // @since 5.3
					$this->heatmap_point_weight = $this->cspm_get_map_option('heatmap_point_weight', 1); // @since 5.3
					$this->heatmap_dissipating = $this->cspm_get_map_option('heatmap_dissipating', 'true'); // @since 5.3
					$this->heatmap_radius = $this->cspm_get_map_option('heatmap_radius'); // @since 5.3
					$this->heatmap_opacity = $this->cspm_get_map_option('heatmap_opacity', 1); // @since 5.3					
					$this->heatmap_color_type = $this->cspm_get_map_option('heatmap_color_type', 'default'); // @since 5.3
					$this->heatmap_colors = $this->cspm_get_map_option('heatmap_colors', array()); //@since 5.3
					
					/**
					 * Map styling */
					
					$this->initial_map_style = $this->cspm_get_map_option('initial_map_style', 'ROADMAP');
					$this->style_option = $this->cspm_get_map_option('style_option', 'progress-map');
					$this->map_style = $this->cspm_get_map_option('map_style', 'google-map');
					$this->js_style_array = $this->cspm_get_map_option('js_style_array', '');
					$this->custom_style_name = $this->cspm_get_map_option('custom_style_name', 'Custom style'); //@since 2.6.1
					
					/**
					 * Marker settings */

					$this->marker_icon = $this->cspm_get_map_option('marker_icon', $this->plugin_url.'img/svg/marker.svg');								
					$this->defaultMarker = $this->cspm_get_map_option('defaultMarker');
					$this->retinaSupport = $this->cspm_get_map_option('retinaSupport', 'false');
					$this->markerAnimation = $this->cspm_get_map_option('markerAnimation', 'pulsating_circle'); // @since 2.5
					$this->marker_anchor_point_option = $this->cspm_get_map_option('marker_anchor_point_option', 'disable'); // @since 2.6.1
					$this->marker_anchor_point = $this->cspm_get_map_option('marker_anchor_point', ''); // @since 2.6.1				
					$this->marker_icon_height = $this->cspm_get_map_option('marker_icon_height', ''); //@since 4.0
					$this->marker_icon_width = $this->cspm_get_map_option('marker_icon_width', ''); //@since 4.0

					/**
					 * Clustering settings */

					$this->useClustring = $this->cspm_get_map_option('useClustring', 'true');
					$this->gridSize = $this->cspm_get_map_option('gridSize', '60');
					$this->big_cluster_icon = $this->cspm_get_map_option('big_cluster_icon', $this->plugin_url.'img/svg/cluster.svg');
					$this->medium_cluster_icon = $this->cspm_get_map_option('medium_cluster_icon', $this->plugin_url.'img/svg/cluster.svg');
					$this->small_cluster_icon = $this->cspm_get_map_option('small_cluster_icon', $this->plugin_url.'img/svg/cluster.svg'); 
					$this->cluster_text_color = $this->cspm_get_map_option('cluster_text_color', '#ffffff');			
					$this->big_cluster_icon_height = $this->cspm_get_map_option('big_cluster_icon_height', '90'); //@since 4.0
					$this->big_cluster_icon_width = $this->cspm_get_map_option('big_cluster_icon_width', '90'); //@since 4.0		
					$this->medium_cluster_icon_height = $this->cspm_get_map_option('medium_cluster_icon_height', '70'); //@since 4.0
					$this->medium_cluster_icon_width = $this->cspm_get_map_option('medium_cluster_icon_width', '70'); //@since 4.0				
					$this->small_cluster_icon_height = $this->cspm_get_map_option('small_cluster_icon_height', '50'); //@since 4.0
					$this->small_cluster_icon_width = $this->cspm_get_map_option('small_cluster_icon_width', '50'); //@since 4.0	
								
					/**
					 * Geotargeting settings */
					 
					$this->geoIpControl = $this->cspm_get_map_option('geoIpControl', 'false');								
					$this->show_user = $this->cspm_get_map_option('show_user', 'false'); // @since 2.7.4
					$this->user_marker_icon = $this->cspm_get_map_option('user_marker_icon', $this->plugin_url.'img/svg/user_marker.svg'); // @since 2.7.4
					$this->user_map_zoom = $this->cspm_get_map_option('user_map_zoom', '12'); // @since 2.7.4
					$this->user_circle = $this->cspm_get_map_option('user_circle', '0'); // @since 2.7.4
					$this->user_circle_fillColor = $this->cspm_get_map_option('user_circle_fillColor', '#189AC9'); // @since 3.0
					$this->user_circle_fillOpacity = $this->cspm_get_map_option('user_circle_fillOpacity', '0.1'); // @since 3.0
					$this->user_circle_strokeColor = $this->cspm_get_map_option('user_circle_strokeColor', '#189AC9'); // @since 3.0				
					$this->user_circle_strokeOpacity = $this->cspm_get_map_option('user_circle_strokeOpacity', '1'); // @since 3.0
					$this->user_circle_strokeWeight = $this->cspm_get_map_option('user_circle_strokeWeight', '1'); // @since 3.0						
					$this->user_marker_icon_height = $this->cspm_get_map_option('user_marker_icon_height', ''); //@since 4.0
					$this->user_marker_icon_width = $this->cspm_get_map_option('user_marker_icon_width', ''); //@since 4.0
					
					/**
					 * KML Layers 
					 * @since 2.7 */
					 
					$this->use_kml = $this->cspm_get_map_option('use_kml', 'false');
					$this->kml_layers = $this->cspm_get_map_option('kml_layers', array());
					$this->kml_list = $this->cspm_get_map_option('kml_list', 'false'); //@since 5.6
					$this->kml_list_display_status = $this->cspm_get_map_option('kml_list_display_status', 'close'); //@since 5.6
					$this->kml_list_placeholder = $this->cspm_get_map_option('kml_list_placeholder', 'Show/Hide KML Layers'); //@since 5.6
					$this->kml_layers_onload_status = $this->cspm_get_map_option('kml_layers_onload_status', 'show'); //@since 5.6.7
                    
					/**
					 * Overlays: Images
					 * @since 3.5 */
					 
					$this->ground_overlays_option = $this->cspm_get_map_option('ground_overlays_option', 'false');
					$this->ground_overlays = $this->cspm_get_map_option('ground_overlays', array());

					/**
					 * Overlays: Polyline
					 * @since 2.7 */
	
					$this->draw_polyline = $this->cspm_get_map_option('draw_polyline', 'false');
					$this->polylines = $this->cspm_get_map_option('polylines', array());				
					
					/**
					 * Overlays: Polygon
					 * @since 2.7 */
	
					$this->draw_polygon = $this->cspm_get_map_option('draw_polygon', 'false');
					$this->polygons = $this->cspm_get_map_option('polygons', array());				
					
					/**
					 * Overlays: Holes
					 * @since 5.3 */
	
					$this->map_holes_option = $this->cspm_get_map_option('map_holes_option', 'false');
					//$this->map_holes_union = $this->cspm_get_map_option('map_holes_union', 'false');
					$this->map_holes_style = $this->cspm_get_map_option('map_holes_style', array());	
					$this->map_holes = $this->cspm_get_map_option('map_holes', array());	

					/**
					 * Infobox settings
					 * @since 2.5 */
					
					$this->show_infobox = $this->cspm_get_map_option('show_infobox', 'true');
					$this->infobox_type = $this->cspm_get_map_option('infobox_type', 'rounded_bubble');
					$this->infobox_display_event = $this->cspm_get_map_option('infobox_display_event', 'onload');
					if($this->cspm_is_mobile_device() && $this->infobox_display_event == 'onhover'){
						$this->infobox_display_event = 'onclick';
					} //@since 5.5 | Makes sure to always use "onclick" event on mobile devices
					$this->infobox_external_link = $this->cspm_get_map_option('infobox_external_link', 'same_window');
					$this->remove_infobox_on_mouseout = $this->cspm_get_map_option('remove_infobox_on_mouseout', 'false'); //@since 2.7.4
					$this->infobox_width = $this->cspm_get_map_option('infobox_width'); //@since 4.0
					$this->infobox_height = $this->cspm_get_map_option('infobox_height'); //@since 4.0
					$this->infobox_title = $this->cspm_get_map_option('infobox_title', NULL); //@since 5.0
					$this->infobox_details = $this->cspm_get_map_option('infobox_content', '[l=100]'); //@since 5.0
					$this->infobox_ellipses = $this->cspm_get_map_option('infobox_ellipses', 'yes'); //@since 5.0
					$this->infobox_display_zoom_level = $this->cspm_get_map_option('infobox_display_zoom_level', 12); //@since 5.3						
					$this->infobox_show_close_btn = $this->cspm_get_map_option('infobox_show_close_btn', 'false'); //@since 5.3
					
					/**
					 * Carousel settings */
					 
					$this->show_carousel = $this->cspm_get_map_option('show_carousel', 'true');
					$this->carousel_scroll = $this->cspm_get_map_option('carousel_scroll', '1');
					$this->carousel_animation = $this->cspm_get_map_option('carousel_animation', 'fast');
					$this->carousel_easing = $this->cspm_get_map_option('carousel_easing', 'linear');
					$this->carousel_auto = $this->cspm_get_map_option('carousel_auto', '0');
					$this->carousel_mode = $this->cspm_get_map_option('carousel_mode', 'false');	
					$this->carousel_wrap = $this->cspm_get_map_option('carousel_wrap', 'circular');	
					$this->scrollwheel_carousel = $this->cspm_get_map_option('scrollwheel_carousel', 'false');	
					$this->touchswipe_carousel = $this->cspm_get_map_option('touchswipe_carousel', 'false');
					$this->carousel_map_zoom = $this->cspm_get_map_option('carousel_map_zoom', '12');
					$this->move_carousel_on = $this->cspm_get_map_option('move_carousel_on', array());	
					$this->sync_carousel_to_viewport =  $this->cspm_get_map_option('sync_carousel_to_viewport', 'no'); //@since 5.0
					$this->connect_carousel_with_map =  $this->cspm_get_map_option('connect_carousel_with_map', 'yes'); //@since 5.0
	
					/**
					 * Carousel style */
					 
					$this->carousel_css = $this->cspm_get_map_option('carousel_css');	
					$this->arrows_background = $this->cspm_get_map_option('arrows_background', '#fff');	
					$this->horizontal_left_arrow_icon = $this->cspm_get_map_option('horizontal_left_arrow_icon');	
					$this->horizontal_right_arrow_icon = $this->cspm_get_map_option('horizontal_right_arrow_icon');	
					$this->vertical_top_arrow_icon = $this->cspm_get_map_option('vertical_top_arrow_icon');	
					$this->vertical_bottom_arrow_icon = $this->cspm_get_map_option('vertical_bottom_arrow_icon');	
					$this->items_background = $this->cspm_get_map_option('items_background', '#fff');	
					$this->items_hover_background = $this->cspm_get_map_option('items_hover_background', '#fbfbfb');	
						
					/**
					 * Carousel Items Settings */
					 
					$this->items_view = $this->cspm_get_map_option('items_view', 'listview');
					$this->items_featured_img = $this->cspm_get_map_option('items_featured_img', 'show');
					$this->show_details_btn = $this->cspm_get_map_option('show_details_btn', 'yes');
					$this->click_on_title = $this->cspm_get_map_option('click_on_title');
					$this->external_link = $this->cspm_get_map_option('external_link', 'same_window');
					$this->details_btn_css = $this->cspm_get_map_option('details_btn_css');
					$this->details_btn_text = $this->cspm_get_map_option('details_btn_text', esc_html__('More', 'cspm'));
					$this->items_title = $this->cspm_get_map_option('items_title');
					$this->items_details = $this->cspm_get_map_option('items_details');
					$this->ellipses = $this->cspm_get_map_option('ellipses', 'yes');
					
						/**
						 * Horizontal */
						 
						$this->horizontal_item_css = $this->cspm_get_map_option('horizontal_item_css');
						$this->horizontal_title_css = $this->cspm_get_map_option('horizontal_title_css');
						$this->horizontal_details_css = $this->cspm_get_map_option('horizontal_details_css');
		
						$this->horizontal_item_size = $this->cspm_get_map_option('horizontal_item_size', '454,150');
							
							if($explode_horizontal_item_size = explode(',', $this->horizontal_item_size)){
								$this->horizontal_item_width = $this->cspm_setting_exists(0, $explode_horizontal_item_size, '454');
								$this->horizontal_item_height = $this->cspm_setting_exists(1, $explode_horizontal_item_size, '150');
							}else{
								$this->horizontal_item_width = '454';
								$this->horizontal_item_height = '150';
							}
						
						$this->horizontal_image_size = $this->cspm_get_map_option('horizontal_image_size', '204,150');
							
							if($explode_horizontal_img_size = explode(',', $this->horizontal_image_size)){
								$this->horizontal_img_width = $this->cspm_setting_exists(0, $explode_horizontal_img_size, '204');
								$this->horizontal_img_height = $this->cspm_setting_exists(1, $explode_horizontal_img_size, '150');
							}else{
								$this->horizontal_img_width = '204';
								$this->horizontal_img_height = '150';
							}
						
						$this->horizontal_details_size = $this->cspm_get_map_option('horizontal_details_size', '250,150');
							
							if($explode_horizontal_details_size = explode(',', $this->horizontal_details_size)){
								$this->horizontal_details_width = $this->cspm_setting_exists(0, $explode_horizontal_details_size, '250');
								$this->horizontal_details_height = $this->cspm_setting_exists(1, $explode_horizontal_details_size, '150');
							}else{
								$this->horizontal_details_width = '250';
								$this->horizontal_details_height = '150';
							}
						
						/**
						 * Vertical */
					
						$this->vertical_item_css = $this->cspm_get_map_option('vertical_item_css');
						$this->vertical_title_css = $this->cspm_get_map_option('vertical_title_css');
						$this->vertical_details_css = $this->cspm_get_map_option('vertical_details_css');
						
						$this->vertical_item_size = $this->cspm_get_map_option('vertical_item_size', '204,290');
							
							if($explode_vertical_item_size = explode(',', $this->vertical_item_size)){
								$this->vertical_item_width = $this->cspm_setting_exists(0, $explode_vertical_item_size, '204');
								$this->vertical_item_height =  $this->cspm_setting_exists(1, $explode_vertical_item_size, '290');
							}else{
								$this->vertica_item_width = '204';
								$this->vertica_item_height = '290';
							}
							
						$this->vertical_image_size = $this->cspm_get_map_option('vertical_image_size', '204,120');			
							
							if($explode_vertical_img_size = explode(',', $this->vertical_image_size)){
								$this->vertical_img_width = $this->cspm_setting_exists(0, $explode_vertical_img_size, '204');
								$this->vertical_img_height = $this->cspm_setting_exists(1, $explode_vertical_img_size, '120');
							}else{
								$this->vertical_img_width = '204';
								$this->vertical_img_height = '120';
							}
							 
						$this->vertical_details_size = $this->cspm_get_map_option('vertical_details_size', '204,170');
							
							if($explode_vertical_details_size = explode(',', $this->vertical_details_size)){
								$this->vertical_details_width = $this->cspm_setting_exists(0, $explode_vertical_details_size, '204');
								$this->vertical_details_height = $this->cspm_setting_exists(1, $explode_vertical_details_size, '170');
							}else{
								$this->vertical_details_width = '204';
								$this->vertical_details_height = '170';
							}
		
					/**
					 * Posts count settings */
					 
					$this->show_posts_count = $this->cspm_get_map_option('show_posts_count', 'no');
					$this->posts_count_clause = $this->cspm_get_map_option('posts_count_clause', '[posts_count] Posts');
					$this->posts_count_color = $this->cspm_get_map_option('posts_count_color', '#333333');
					$this->posts_count_style = $this->cspm_get_map_option('posts_count_style');
		
					/**
					 * Marker categories settings */
					 
					$this->marker_cats_settings = $this->cspm_get_map_option('marker_cats_settings', 'false');
					$this->marker_categories_taxonomy = $this->cspm_get_map_option('marker_categories_taxonomy');

					/**
					 * Faceted search settings */
					 
					$this->faceted_search_option = $this->cspm_get_map_option('faceted_search_option', 'false');
					$this->faceted_search_terms = $this->cspm_get_map_option('faceted_search_taxonomy_'.$this->marker_categories_taxonomy, array());
					$this->faceted_search_multi_taxonomy_option = $this->cspm_get_map_option('faceted_search_multi_taxonomy_option', 'true');
					$this->faceted_search_input_skin = $this->cspm_get_map_option('faceted_search_input_skin', 'polaris');
					$this->faceted_search_input_color = $this->cspm_get_map_option('faceted_search_input_color', 'blue');
					$this->faceted_search_css = $this->cspm_get_map_option('faceted_search_css');
					$this->faceted_search_drag_map = $this->cspm_get_map_option('faceted_search_drag_map', 'autofit'); //@since 2.8.2
					$this->faceted_search_autocheck = $this->cspm_get_map_option('faceted_search_autocheck', 'false'); //@since 3.0
					$this->faceted_autocheck_terms = $this->cspm_get_map_option('faceted_search_autocheck_taxonomy_'.$this->marker_categories_taxonomy, array()); //@since 3.0
					$this->faceted_search_display_status = $this->cspm_get_map_option('faceted_search_display_status', 'close'); //@since 3.0
										
					/**
					 * Search form settings */
					 
					$this->search_form_option = $this->cspm_get_map_option('search_form_option', 'false');
					$this->sf_min_search_distances = $this->cspm_get_map_option('sf_min_search_distances', '3');
					$this->sf_max_search_distances = $this->cspm_get_map_option('sf_max_search_distances', '50');
					$this->sf_distance_unit = $this->cspm_get_map_option('sf_distance_unit', 'metric');
					$this->address_placeholder = $this->cspm_get_map_option('sf_address_placeholder', esc_html__('Enter City & Province, or Postal code', 'cspm'));
					$this->slider_label = $this->cspm_get_map_option('sf_slider_label', esc_html__('Expand the search area up to', 'cspm'));
					$this->no_location_msg = $this->cspm_get_map_option('sf_no_location_msg', esc_html__('We could not find any location', 'cspm'));
					$this->bad_address_msg = $this->cspm_get_map_option('sf_bad_address_msg', esc_html__('We could not understand the location', 'cspm'));
					$this->bad_address_sug_1 = $this->cspm_get_map_option('sf_bad_address_sug_1', esc_html__('- Make sure all street and city names are spelled correctly.', 'cspm'));
					$this->bad_address_sug_2 = $this->cspm_get_map_option('sf_bad_address_sug_2', esc_html__('- Make sure your address includes a city and state.', 'cspm'));
					$this->bad_address_sug_3 = $this->cspm_get_map_option('sf_bad_address_sug_3', esc_html__('- Try entering a zip code.', 'cspm'));
					$this->submit_text = $this->cspm_get_map_option('sf_submit_text', esc_html__('Find it', 'cspm'));
					$this->search_form_bg_color = $this->cspm_get_map_option('sf_search_form_bg_color', 'rgba(255,255,255,1)');
					$this->circle_option = $this->cspm_get_map_option('sf_circle_option', 'true');
					$this->fillColor = $this->cspm_get_map_option('sf_fillColor', '#189AC9');
					$this->fillOpacity = $this->cspm_get_map_option('sf_fillOpacity', '0.1');
					$this->strokeColor = $this->cspm_get_map_option('sf_strokeColor', '#189AC9');				
					$this->strokeOpacity = $this->cspm_get_map_option('sf_strokeOpacity', '1');
					$this->strokeWeight = $this->cspm_get_map_option('sf_strokeWeight', '1');
					$this->sf_display_status = $this->cspm_get_map_option('sf_display_status', 'close');						
					$this->sf_edit_circle = $this->cspm_get_map_option('sf_edit_circle', 'true'); //@since 3.2
					$this->sf_geoIpControl = $this->cspm_get_map_option('sf_geoIpControl', 'true'); //@since 3.2
		
					/**
					 * Zoom to country 
					 * @since 3.0 */				
					
					$this->zoom_country_option = $this->cspm_get_map_option('zoom_country_option', 'false');
					$this->zoom_country_display_status = $this->cspm_get_map_option('zoom_country_display_status', 'close');
					$this->country_zoom_or_autofit = $this->cspm_get_map_option('country_zoom_or_autofit', 'autofit');
					$this->country_zoom_level = $this->cspm_get_map_option('country_zoom_level', '12');	
					$this->country_flag = $this->cspm_get_map_option('show_country_flag', '12');					
					$this->countries_display_lang = $this->cspm_get_map_option('country_display_language', 'en');
					$this->countries = $this->cspm_get_map_option('countries', array());
					$this->country_search_input = $this->cspm_get_map_option('country_search_input', 'yes'); //@since 5.6
					$this->country_list_placeholder = $this->cspm_get_map_option('country_list_placeholder', 'Select a country'); //@since 5.6
		
					/**
					 * Nearby points of interest
					 * @since 3.2 */
					
					$this->nearby_places_option = $this->cspm_get_map_option('nearby_places_option', 'false');
					$this->np_proximities_display_status = $this->cspm_get_map_option('np_proximities_display_status', 'close');
					$this->np_distance_unit = $this->cspm_get_map_option('np_distance_unit', 'METRIC');
					$this->np_radius = $this->cspm_get_map_option('np_radius', '5000'); //@since 4.7
					$this->np_min_radius = $this->cspm_get_map_option('np_min_radius', '1000'); //@since 4.7
					$this->np_max_radius = $this->cspm_get_map_option('np_max_radius', '50000');
					$this->np_circle_option =  $this->cspm_get_map_option('np_circle_option', 'true');
					$this->np_edit_circle =  $this->cspm_get_map_option('np_edit_circle', 'true');	
					$this->np_marker_type =  $this->cspm_get_map_option('np_marker_type', 'default');				
					$this->show_proximity_icon = $this->cspm_get_map_option('show_proximity_icon', 'true');
					$this->np_proximities = $this->cspm_get_map_option('np_proximities', array());					
		 			$this->np_list_placeholder = $this->cspm_get_map_option('np_list_placeholder', 'Select a place type'); //@since 5.6
		 
					/**
					 * Customize */
					
					$this->custom_css = $this->cspm_get_map_option('custom_css', '');
					$this->map_horizontal_elements_order = $this->cspm_get_map_option('map_horizontal_elements_order', array('zoom_country', 'search_form', 'faceted_search', 'proximities')); //@since 3.2
					$this->map_vertical_elements_order = $this->cspm_get_map_option('map_vertical_elements_order', array('recenter_map', 'geo', 'heatmap')); //@since 3.2
					$this->multicolor_svg = $this->cspm_get_map_option('multicolor_svg', 'no');
					$this->zoom_in_icon = $this->cspm_get_map_option('zoom_in_icon', $this->plugin_url.'img/svg/addition-sign.svg');	
					$this->zoom_out_icon = $this->cspm_get_map_option('zoom_out_icon', $this->plugin_url.'img/svg/minus-sign.svg');						
					$this->faceted_search_icon = $this->cspm_get_map_option('faceted_search_icon', $this->plugin_url.'img/svg/filter.svg');					
					$this->search_form_icon = $this->cspm_get_map_option('sf_search_form_icon', $this->plugin_url.'img/svg/loup.svg');			
					$this->countries_btn_icon = $this->cspm_get_map_option('countries_btn_icon', $this->plugin_url.'img/svg/continents.svg');	
					$this->target_icon = $this->cspm_get_map_option('target_icon', $this->plugin_url.'img/svg/geoloc.svg');	
					$this->recenter_icon = $this->cspm_get_map_option('recenter_icon', $this->plugin_url.'img/svg/recenter.svg');	
					$this->heatmap_icon = $this->cspm_get_map_option('heatmap_icon', $this->plugin_url.'img/svg/heatmap.svg');	
					$this->nearby_icon = $this->cspm_get_map_option('nearby_icon', $this->plugin_url.'img/svg/proximities.svg');	
					$this->kml_list_icon = $this->cspm_get_map_option('kml_list_icon', $this->plugin_url.'img/svg/kml_list.svg');	
					
					/**
					 * Marker labels settings
					 * @since 4.0 */
					
					$this->marker_labels = $this->cspm_get_map_option('marker_labels', 'no');
					$this->marker_labels_type = $this->cspm_get_map_option('marker_labels_type', 'manual');
					$this->marker_labels_color = $this->cspm_get_map_option('marker_labels_color', '#000000');
					$this->marker_labels_fontFamily = $this->cspm_get_map_option('marker_labels_fontFamily', 'monospace');
					$this->marker_labels_fontWeight = $this->cspm_get_map_option('marker_labels_fontWeight', 'bold');
					$this->marker_labels_fontSize = $this->cspm_get_map_option('marker_labels_fontSize', '14px');
					$this->marker_labels_top_position = $this->cspm_get_map_option('marker_labels_top', '');
					$this->marker_labels_left_position = $this->cspm_get_map_option('marker_labels_left', '');
					
					/**
					 * Autocomplete settings
					 * @since 4.0 */
					
					$this->autocomplete_option = $this->cspm_get_map_option('autocomplete_option', 'yes');
					$this->autocomplete_strict_bounds = $this->cspm_get_map_option('autocomplete_strict_bounds', 'no');
					$this->autocomplete_country_restrict = $this->cspm_get_map_option('autocomplete_country_restrict', 'no');
					$this->autocomplete_countries = $this->cspm_get_map_option('autocomplete_countries', array());
		
					/**
					 * Marker popup settings 
					 * @since 4.0 */
					
					$this->use_marker_popups = $this->cspm_get_map_option('use_marker_popups', 'no');
					$this->marker_popups_placement = $this->cspm_get_map_option('marker_popups_placement', 'right');	
					$this->marker_popups_content_type = $this->cspm_get_map_option('marker_popups_content_type', 'custom_field');		 
					$this->marker_popups_custom_field = $this->cspm_get_map_option('marker_popups_custom_field', '');		 
					$this->marker_popups_taxonomy = $this->cspm_get_map_option('marker_popups_taxonomy', '');		 
					$this->marker_popups_before_content = $this->cspm_get_map_option('marker_popups_before_content', '');		 
					$this->marker_popups_after_content = $this->cspm_get_map_option('marker_popups_after_content', '');		 
					$this->marker_popups_bg_color = $this->cspm_get_map_option('marker_popups_bg_color', '#ffffff');		 
					$this->marker_popups_color = $this->cspm_get_map_option('marker_popups_color', '#000000');
					$this->marker_popups_fontSize = $this->cspm_get_map_option('marker_popups_fontSize', '14px');
					$this->marker_popups_fontWeight = $this->cspm_get_map_option('marker_popups_fontWeight', 'bold');
		
					/**
					 * Marker menu settings 
					 * @since 5.5 */
					
					$this->use_marker_menu = $this->cspm_get_map_option('use_marker_menu', 'no');
					$this->single_post_menu_item = $this->cspm_get_map_option('single_post_menu_item', array());
					$this->media_modal_menu_item = $this->cspm_get_map_option('media_modal_menu_item', array());
					$this->proximities_menu_item = $this->cspm_get_map_option('proximities_menu_item', array());
					$this->nearby_map_menu_item = $this->cspm_get_map_option('nearby_map_menu_item', array());		
					$this->directions_menu_item = $this->cspm_get_map_option('directions_menu_item', array());		
					$this->marker_menu_items_order = $this->cspm_get_map_option('marker_menu_items_order', array());		
					$this->marker_menu_placement = $this->cspm_get_map_option('marker_menu_placement', 'right');	
					$this->marker_menu_open_leftclick = $this->cspm_get_map_option('marker_menu_open_leftclick', 'no');	
					$this->marker_menu_open_rightclick = $this->cspm_get_map_option('marker_menu_open_rightclick', 'yes');
					$this->marker_menu_open_mouse_enter = $this->cspm_get_map_option('marker_menu_open_mouse_enter', 'no');	
					$this->marker_menu_close_mouse_out = $this->cspm_get_map_option('marker_menu_close_mouse_out', 'no');	
					$this->marker_menu_close_map_click = $this->cspm_get_map_option('marker_menu_close_map_click', 'yes');
					$this->marker_menu_show_close_btn = $this->cspm_get_map_option('marker_menu_show_close_btn', 'yes');						
					$this->marker_menu_bg_color = $this->cspm_get_map_option('marker_menu_bg_color', '#ffffff');		 
					$this->marker_menu_color = $this->cspm_get_map_option('marker_menu_color', '#000000');
					$this->marker_menu_fontSize = $this->cspm_get_map_option('marker_menu_fontSize', '13px');
					$this->marker_menu_fontWeight = $this->cspm_get_map_option('marker_menu_fontWeight', 'normal');
		
					if(empty($this->map_object_id)){
				 	
						/** 
						 * Just in case the map ID wasn't defined in the shortcode.
						 * We'll make sure to disable the following features/options just in case! */
						
						$this->faceted_search_option = $this->show_carousel = $this->search_form_option = 'false';
					
					}
					
					/**
					 * The main query 
					 * @since 4.8 */
					 
					add_filter('cs_progress_map_main_query', array($this, 'cspm_override_query_args_using_sessions'), 10);
					
					$this->queried_post_ids = $this->cspm_main_query(array(
						'optional_latlng' => $this->optional_latlng,
					));
										
				}
		
			}

		}
		
	
		static function this(){
			
			return self::$_this;
			
		}
		
		
		function cspm_hooks(){

			/**
			 * "Progress Map" Ajax functions */

			add_action('wp_ajax_cspm_load_clustred_markers_list', array($this, 'cspm_load_clustred_markers_list'));
			add_action('wp_ajax_nopriv_cspm_load_clustred_markers_list', array($this, 'cspm_load_clustred_markers_list'));
		
			/**
			 * Add main map shortcode */
			 
			add_shortcode('cspm_main_map', array($this, 'cspm_main_map_shortcode'));
			add_shortcode('codespacing_progress_map', array($this, 'cspm_main_map_shortcode')); // Deprecated @since 3.0
			
		}
		
		
		/**
		 * Get an option from a map options
		 *
		 * @since 3.0 
		 * @edited 5.0 [added "is_null()" to ignore default plugin settings]
         * @edited 5.6.3 
		 */
		function cspm_get_map_option($option_id, $default_value = ''){            
            
			/**
             * Determine the type of the default value
             * @since 5.6.3 */
            
			$default_by_type = is_array($default_value) ? maybe_serialize($default_value) : $default_value;
            
			/**
			 * We'll check if the default settings can be found in the array containing the "(shared) plugin settings".
			 * If found, we'll use it. If not found, we'll use the one in [@default_value] instead. */
			
            $default = is_null($default_by_type) ? '' : $this->cspm_setting_exists($option_id, $this->plugin_settings, $default_by_type); //@edited 5.0
			
			$option = $this->cspm_setting_exists($this->metafield_prefix.'_'.$option_id, $this->map_settings, $default);
			
			return is_serialized($option) ? (array) maybe_unserialize(stripslashes($option)) : $option;
			
		}
		
		
		/**
		 * Get the value of a setting
		 *
		 * @since 3.0 
		 */
		function cspm_get_plugin_setting($setting_id, $default_value = ''){
			
			return $this->cspm_setting_exists($setting_id, $this->plugin_settings, $default_value);			
			
		}
		
		
		/**
		 * Check if array_key_exists and if empty() doesn't return false
		 * Replace the empty value with the default value if available 
		 * @empty() return false when the value is (null, 0, "0", "", 0.0, false, array())
		 *
		 * @since 2.4 
		 */
		function cspm_setting_exists($key, $array, $default = ''){
			
			$array_value = isset($array[$key]) ? $array[$key] : $default;
			
			$setting_value = empty($array_value) ? $default : $array_value;
			
			return $setting_value;
			
		}
		
		
		/**
		 * This will load the styles needed by our shortcode based on its settings
		 *
		 * @since 3.0
		 */
		function cspm_enqueue_styles($combine_files = ''){
			
			$combine_files = (empty($combine_files)) ? $this->combine_files : $combine_files;			
			
			do_action('cspm_before_enqueue_style');
			
			/**
			 * Media Element | From WP Library! 
			 * @since 3.5 */
			 
			wp_enqueue_style('wp-mediaelement');				 			

			/**
			 * Font Style */
			
			if($this->remove_google_fonts == 'enable')  	
				wp_enqueue_style('cspm-font');

			/**
			 * icheck
			 * Note: Loaded only when using the faceted seach feature */
							
			if($this->faceted_search_option == 'true'){
				
				$icheck_skin = $this->faceted_search_input_skin;
				
				if($this->faceted_search_input_skin != 'polaris' && $this->faceted_search_input_skin != 'futurico')
					$icheck_color = ($this->faceted_search_input_color != 'black') ? $this->faceted_search_input_color : $this->faceted_search_input_skin;
				else $icheck_color = $this->faceted_search_input_skin;
				
				wp_enqueue_style('jquery-icheck-'.$icheck_skin.'-'.$icheck_color);
				
			}
										
			$script_handle = 'cspm-style';			

			/**
			 * Bootstrap */
			
			if($this->remove_bootstrap == 'enable')
				wp_enqueue_style('cspm-custom-bootstrap');
			
			/**
			 * jCarousel
			 * Note: Loaded only when using the carousel feature */
			 
			if($this->show_carousel == 'true' && !empty($this->map_object_id))
				wp_enqueue_style('jquery-jcarousel-06');
			
			/**
			 * Custom Scroll bar
			 * Note: Loaded only when using the clustring feature and/or the faceted seach feature */
			 
			//if($this->useClustring == 'true' || $this->faceted_search_option == 'true')
				wp_enqueue_style('jquery-mcustomscrollbar');

			/**
			 * Range Slider
			 * Note: Loaded only when using the search form feature */
							
			if($this->search_form_option == 'true' && !empty($this->map_object_id)){
				wp_enqueue_style('jquery-ion-rangeslider');
			}
						
			/**
			 * iziModal
			 * @since 3.5 */
			 
			wp_enqueue_style('jquery-izimodal');
						
			/**
			 * iziToast
			 * @since 3.5 */
			 
			wp_enqueue_style('jquery-izitoast');
			
			/**
			 * Snazzy Infobox
			 * @since 3.9 */
			
			wp_enqueue_style('snazzy-info-window');
			
			/**
			 * Tail Select
			 * @since 5.6 */
			
			wp_enqueue_style('tail-select');
			
			/** 
			 * Progress Map styles */
			
			wp_enqueue_style('nprogress');
			wp_enqueue_style('cspm-custom-animate');
			wp_enqueue_style($script_handle);
		
			/**
			 * Add custom header script */
			
			wp_add_inline_style($script_handle, $this->cspm_custom_map_style());			
			
			do_action('cspm_after_enqueue_style');

		}

		
		/** 
		 * This will build the custom CSS needed for this map
		 *
		 * @since 3.0
		 * @updated 3.7 | 3.8 | 4.0
		 */
		function cspm_custom_map_style(){
				
			$custom_map_style = '';
			
			if($this->show_carousel == 'true' && !empty($this->map_object_id)){
				
				/**
				 * Carousel Style */
				
				if(!empty($this->carousel_css))
					$custom_map_style .= 'div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-container{'. $this->carousel_css.'}';
				
				/** 
				 * Carousel Items Style
				 * @updated 3.7  */
				 
				if($this->items_view == "listview"){ // Horizontal / List view
					
					$xy_position = ($this->carousel_mode == 'false') ? 'left' : 'right';
					
					if($this->items_featured_img == 'show'){ //@since 3.7
						
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_container{width:'.$this->horizontal_details_width.'px;height:'.$this->horizontal_details_height.'px;}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .item_img{width:'.$this->horizontal_img_width.'px; height:'.$this->horizontal_img_height.'px;float:left;}';
						
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_btn{'.$xy_position.':'.($this->horizontal_details_width-80).'px;top:'.($this->horizontal_details_height-50).'px;}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_title{width:'.$this->horizontal_details_width.'px;'.$this->horizontal_title_css.'}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_infos{width:'.$this->horizontal_details_width.'px;'.$this->horizontal_details_css.'}';
						
					}else{
						
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_container{width:'.$this->horizontal_item_width.'px;height:'.$this->horizontal_item_height.'px;}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .item_img{display:none;}';
						
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_btn{'.$xy_position.':'.($this->horizontal_item_width-80).'px;top:'.($this->horizontal_item_height-50).'px;}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_title{width:'.$this->horizontal_item_width.'px;'.$this->horizontal_title_css.'}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_infos{width:'.$this->horizontal_item_width.'px;'.$this->horizontal_details_css.'}';
						
					}
				    
                    /**
                     * Responsive style for horizontal carousel.
                     * Simply use vertical carousel items style for horizontal carousel items on small screen!
                     * @since 5.6.7 
                    
                    $custom_map_style .= '@media (max-width: 700px){';

                        $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.'{height: calc('.$this->vertical_item_height.'px + 10px) !important;}';
                        $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' li.cspm_carousel_item{width: '.$this->vertical_item_width.'px !important; height: '.$this->vertical_item_height.'px !important;}';
                        $custom_map_style .= '.codespacing_progress_map_area{ height: auto !important;}';
                        $custom_map_style .= '#cspm_carousel_container{ width: auto !important;}';

                        if($this->items_featured_img == 'show'){

                            $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_container{width:'.$this->vertical_details_width.'px !important; height:'.$this->vertical_details_height.'px !important;}';
                            $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .item_img{width:'.$this->vertical_img_width.'px !important; height:'.$this->vertical_img_height.'px !important; overflow: hidden;}';

                            $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_btn{'.$xy_position.':'.($this->vertical_details_width-80).'px !important; top:'.($this->vertical_details_height-50).'px !important;}';
                            $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_title{width:'.$this->vertical_details_width.'px !important;'.$this->vertical_title_css.'}';
                            $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_infos{width:'.$this->vertical_details_width.'px !important;'.$this->vertical_details_css.'}';

                        }else{

                            $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_container{width:'.$this->vertical_item_width.'px !important; height:'.$this->vertical_item_height.'px !important;}';
                            $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .item_img{display:none !important;}';

                            $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_btn{'.$xy_position.':'.($this->vertical_item_width-80).'px !important;top:'.($this->vertical_item_height-50).'px !important;}';
                            $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_title{width:'.$this->vertical_item_width.'px !important;'.$this->vertical_title_css.'}';
                            $custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_infos{width:'.$this->vertical_item_width.'px !important;'.$this->vertical_details_css.'}';

                        }
                    $custom_map_style .= '}';*/
					
				}else{ // Vertical / Grid view
					
					$xy_position = ($this->carousel_mode == 'false') ? 'left' : 'right';
					
					if($this->items_featured_img == 'show'){ //@since 3.7
							
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_container{width:'.$this->vertical_details_width.'px; height:'.$this->vertical_details_height.'px;}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .item_img{width:'.$this->vertical_img_width.'px;height:'.$this->vertical_img_height.'px;}';
						
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_btn{'.$xy_position.':'.($this->vertical_details_width-80).'px;top:'.($this->vertical_details_height-50).'px;}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_title{width:'.$this->vertical_details_width.'px;'.$this->vertical_title_css.'}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_infos{width:'.$this->vertical_details_width.'px;'.$this->vertical_details_css.'}';
					
					}else{
						
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_container{width:'.$this->vertical_item_width.'px; height:'.$this->vertical_item_height.'px;}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .item_img{display:none;}';
						
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_btn{'.$xy_position.':'.($this->vertical_item_width-80).'px;top:'.($this->vertical_item_height-50).'px;}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_title{width:'.$this->vertical_item_width.'px;'.$this->vertical_title_css.'}';
						$custom_map_style .= 'ul#cspm_carousel_map'.$this->map_object_id.' .details_infos{width:'.$this->vertical_item_width.'px;'.$this->vertical_details_css.'}';
						
					}
				}
                
				/**
				 * Horizontal Right Arrow CSS Style */
				
				if(!empty($this->horizontal_right_arrow_icon)){
					
					$custom_map_style .= 'div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-next-horizontal, div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-next-horizontal:hover,.jcarousel-skin-default .jcarousel-next-horizontal:focus{background-image: url('.$this->horizontal_right_arrow_icon.') !important;}';
				
				}
				
				/**
				 * Horizontal Left Arrow CSS Style */
				
				if(!empty($this->horizontal_left_arrow_icon)){
				
					$custom_map_style .= 'div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-prev-horizontal, div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-prev-horizontal:hover,.jcarousel-skin-default .jcarousel-prev-horizontal:focus{background-image: url('.$this->horizontal_left_arrow_icon.') !important;}';
					
				}
				
				/**
				 * Vertical Top Arrow CSS Style */
				
				if(!empty($this->vertical_top_arrow_icon)){
							
					$custom_map_style .= 'div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-prev-vertical, div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-prev-vertical:hover,.jcarousel-skin-default .jcarousel-prev-vertical:focus,.jcarousel-skin-default .jcarousel-prev-vertical:active{background-image: url('.$this->vertical_top_arrow_icon.') !important;}';
				
				}
		
				/**
				 * Vertical Bottom Arrow CSS Style */
				
				if(!empty($this->vertical_bottom_arrow_icon)){ 
					
					$custom_map_style .= 'div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-next-vertical, div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-next-vertical:hover,.jcarousel-skin-default .jcarousel-next-vertical:focus,.jcarousel-skin-default .jcarousel-next-vertical:active{background-image: url('.$this->vertical_bottom_arrow_icon.') !important;}';
						
				}
										
				/**
				 * Custom Vertical Carousel CSS */
				
				$custom_map_style .= 'div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-container-vertical{height:'.$this->layout_fixed_height.'px !important;}';
											
				/**
				 * Arrows background color */
				 
				$background_color = $this->arrows_background;
				if($background_color != '#fff' && $background_color != '#ffffff'){
					$custom_map_style .= 'div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-prev-horizontal,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-next-horizontal,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-direction-rtl .jcarousel-next-horizontal, 
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-next-horizontal:hover,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-next-horizontal:focus,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-direction-rtl .jcarousel-prev-horizontal,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-prev-horizontal:hover,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-prev-horizontal:focus,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-direction-rtl .jcarousel-next-vertical,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-next-vertical,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-next-vertical:hover,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-next-vertical:focus,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-direction-rtl .jcarousel-prev-vertical,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-prev-vertical,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-prev-vertical:hover,
					div#cspm_carousel_container[data-map-id=map'.$this->map_object_id.'] .jcarousel-skin-default .jcarousel-prev-vertical:focus{background-color:'.$background_color.';}';
				}
			}
			
			/**
			 * Posts count clause */
			 
			if($this->show_posts_count == 'yes' && !empty($this->map_object_id)){
				
				$custom_map_style .= 'div.codespacing_progress_map_area[data-map-id=map'.$this->map_object_id.'] div.number_of_posts_widget{color:'.$this->posts_count_color.';'.$this->posts_count_style.'}';
					
			}
			
			/**
			 * Faceted search CSS
			 * @updated 2.8 */

			if($this->faceted_search_option == 'true' && !empty($this->faceted_search_css) && !in_array($this->faceted_search_css, array('#fff', '#ffffff'))){
				
				$custom_map_style .= 'div[class^=faceted_search_container_map'.$this->map_object_id.']{background:'.$this->faceted_search_css.'}';
					
			}
			
			/**
			 * Search form CSS */ 
						
			if($this->search_form_option == 'true'){ 
			
				if(!empty($this->search_form_bg_color) && !in_array($this->search_form_bg_color, array('#fff', '#ffffff'))){
					$custom_map_style .= 'div[class^=search_form_container_map'.$this->map_object_id.']{background:'.$this->search_form_bg_color.';}';
				} //@edited 5.6.5
				
				/**
				 * Custom CSS for the range slider
				 * @edited 5.6 */
				
				$custom_map_style .= '.irs-from,.irs-to,.irs-single{background: '.$this->cspm_get_plugin_setting('main_hex_color').' !important;}';
				$custom_map_style .= '.irs-from:after,.irs-to:after,.irs-single:after,.irs--round .irs-from:before, .irs--round .irs-to:before, .irs--round .irs-single:before{border-top-color: '.$this->cspm_get_plugin_setting('main_hex_color').' !important;}'; //@edited 5.6
				$custom_map_style .= '.irs--round .irs-from, .irs--round .irs-to, .irs--round .irs-single{border-radius:2px !important;}'; //@since 5.6
				$custom_map_style .= '.irs--round .irs-handle{cursor: grab; border: 2px solid '.$this->cspm_get_plugin_setting('main_hex_color').' !important;}'; //@since 5.6
				$custom_map_style .= '.irs--round .irs-handle.state_hover, .irs--round .irs-handle:hover{border: 2px solid '.$this->cspm_get_plugin_setting('main_hex_hover_color').' !important; background-color:#ffffff !important;}'; //@since 5.6
				$custom_map_style .= '.irs--round .irs-bar{background-color: '.$this->cspm_get_plugin_setting('main_hex_color').' !important;}'; //@since 5.6
				$custom_map_style .= '.irs--round .irs-min, .irs--round .irs-max, .irs--round .irs-from, .irs--round .irs-to, .irs--round .irs-single{font-size: 11px !important;}'; //@since 5.6					
			
			}
			
			/**
			 * Nprogress CSS
			 * @since 3.8 */
			
			$custom_map_style .= '#nprogress .bar{background: '.$this->cspm_get_plugin_setting('main_hex_color').' !important;}';
			$custom_map_style .= '#nprogress .spinner-icon{border-top-color: '.$this->cspm_get_plugin_setting('main_hex_color').' !important;}';
			
			/**
			 * SVG custom colors
			 * @since 3.8 */
			
			if($this->multicolor_svg == 'no'){
				$custom_map_style .= '.codespacing_progress_map_area svg.cspm_svg_colored *{fill: '.$this->cspm_get_plugin_setting('main_hex_color').' !important;}';
				$custom_map_style .= '.codespacing_progress_map_area svg.cspm_svg_white *{fill: #ffffff !important;}';
			}

			/**
			 * Override infobox width & height
			 * @since 4.0 */
						
			if(!empty($this->infobox_width) && !empty($this->infobox_height)){
				 
				$custom_map_style .= $this->cspm_infobox_size(array(
					'map_id' => 'map'.$this->map_object_id,
					'width' => $this->infobox_width,
					'height' => $this->infobox_height,
					'type' => $this->infobox_type,
				));
				
			}
						
			/**
			 * Custom CSS */
			 
			$custom_map_style .= $this->custom_css;
			
			return $custom_map_style;
				
		}
		
		
		/**
		 * This will build an array containing the map object ID and ...
		 * ... the options needed for this map to communicate with our JS file/functoins
		 *
		 * @since 3.0
		 * @updated 3.5
		 */
		function cspm_localize_map_script(){
			
			/**
			 * Add text before and/or after the search field value
			 * @since 2.8 */
			 
			$before_search_address = apply_filters('cspm_before_search_address', '', $this->map_object_id);
			$after_search_address = apply_filters('cspm_after_search_address', '', $this->map_object_id);
			
			$map_id = 'map'.$this->map_object_id;
            
			$map_script_args = array(
				
				$map_id => array(
				
					/**
					 * All map settings */
					
					'map_object_id' => $this->map_object_id,
						
					/**
					 * Hook to change the map settings
					 * @since 3.5 */
	
					'map_settings' => apply_filters( 'cspm_localize_script_map_settings', 
						$this->map_settings, 
						$map_id, 
						$this->metafield_prefix
					),
					
					/**
					 * Query settings */
					
					'number_of_items' => $this->number_of_items,
					
					/**
					 * Map settings */
					 
					'center' => $this->center,
					'zoom' => $this->zoom,
					'mapTypeControl' => $this->mapTypeControl,
					'streetViewControl' => $this->streetViewControl,
					'zoomControl' => $this->zoomControl,
					'zoomControlType' => $this->zoomControlType,
					'defaultMarker' => $this->defaultMarker,
					'marker_icon' => $this->marker_icon,
					'big_cluster_icon' => $this->big_cluster_icon,
					'big_cluster_size' => $this->cspm_get_image_size(
						array(
							'path' => $this->cspm_get_image_path_from_url($this->big_cluster_icon),
							'retina' => $this->retinaSupport,
							'default_height' => $this->big_cluster_icon_height,
							'default_width' => $this->big_cluster_icon_width,				
						)
					),
					'medium_cluster_icon' => $this->medium_cluster_icon,
					'medium_cluster_size' => $this->cspm_get_image_size(
						array(
							'path' => $this->cspm_get_image_path_from_url($this->medium_cluster_icon),
							'retina' => $this->retinaSupport,
							'default_height' => $this->medium_cluster_icon_height,
							'default_width' => $this->medium_cluster_icon_width,				
						)
					),					
					'small_cluster_icon' => $this->small_cluster_icon,
					'small_cluster_size' => $this->cspm_get_image_size(
						array(
							'path' => $this->cspm_get_image_path_from_url($this->small_cluster_icon),
							'retina' => $this->retinaSupport,
							'default_height' => $this->small_cluster_icon_height,
							'default_width' => $this->small_cluster_icon_width,				
						)
					),					
					'cluster_text_color' => $this->cluster_text_color,
					'grid_size' => $this->gridSize,
					'retinaSupport' => $this->retinaSupport,
					'initial_map_style' => $this->initial_map_style,
					'markerAnimation' => $this->markerAnimation, //@since 2.5
					'marker_anchor_point_option' => $this->marker_anchor_point_option, //@since 2.6.1
					'marker_anchor_point' => $this->marker_anchor_point, //@since 2.6.1
					'min_zoom' => $this->min_zoom, //@since 2.6.3
					'controlSize' => $this->controlSize, //@since 4.8
					
					/**
					 * @max_zoom, since 2.6.3
					 * @updated 2.8 (Fix issue when min zoom is bigger than max zoom) */
					'max_zoom' => ($this->min_zoom > $this->max_zoom) ? 19 : $this->max_zoom,
					
					'zoom_on_doubleclick' => $this->zoom_on_doubleclick, //@since 2.6.3
					'map_background' => $this->map_background, //@since 4.2
					'clickableIcons' => $this->clickableIcons, //@since 4.2
					'rotateControl' => $this->rotateControl, //@since 4.2
					'scaleControl' => $this->scaleControl, //@since 4.2
					'fullscreenControl' => $this->fullscreenControl, //@since 4.2
					'autofit' => $this->autofit, //@since 4.6
					'gestureHandling' => $this->gestureHandling, //@since 5.6.5
					
					/**
					 * Carousel settings */
					 
					'items_view' => $this->items_view,
					'show_carousel' => $this->show_carousel,
					'carousel_scroll' => $this->carousel_scroll,
					'carousel_wrap' => $this->carousel_wrap,
					'carousel_auto' => $this->carousel_auto,
					'carousel_mode' => $this->carousel_mode,
					'carousel_animation' => $this->carousel_animation,
					'carousel_easing' => $this->carousel_easing,
					'carousel_map_zoom' => $this->carousel_map_zoom,
					'scrollwheel_carousel' => $this->scrollwheel_carousel,
					'touchswipe_carousel' => $this->touchswipe_carousel,
					'connect_carousel_with_map' => $this->connect_carousel_with_map, //@since 5.0
					
					/**
					 * Layout settings */
					
					'layout_fixed_height' => $this->layout_fixed_height,
					
					/**
					 * Carousel items settings */
					 
					'horizontal_item_css' => $this->horizontal_item_css,
					'horizontal_item_width' => $this->horizontal_item_width,
					'horizontal_item_height' => $this->horizontal_item_height,
					'vertical_item_css' => $this->vertical_item_css,
					'vertical_item_width' => $this->vertical_item_width,
					'vertical_item_height' => $this->vertical_item_height,			
					'items_background' => $this->items_background,
					'items_hover_background' => $this->items_hover_background,
					
					/**
					 * Faceted search settings */
					 
					'faceted_search_option' => $this->faceted_search_option,
					'faceted_search_multi_taxonomy_option' => $this->faceted_search_multi_taxonomy_option,
					'faceted_search_input_skin' => $this->faceted_search_input_skin,
					'faceted_search_input_color' => $this->faceted_search_input_color,
					'faceted_search_drag_map' => $this->faceted_search_drag_map, //@since 2.8.2
					
					/**
					 * Posts count settings */
					 
					'show_posts_count' => $this->show_posts_count,
					
					/**
					 * Search form settings */
					 
					'sf_circle_option' => $this->circle_option,
					'fillColor' => $this->fillColor,
					'fillOpacity' => str_replace(',', '.', $this->fillOpacity),
					'strokeColor' => $this->strokeColor,
					'strokeOpacity' => str_replace(',', '.', $this->strokeOpacity),
					'strokeWeight' => $this->strokeWeight,
					'search_form_option' => $this->search_form_option,
					'sf_edit_circle' => $this->sf_edit_circle, //@since 3.2
					'no_location_found_msg' => esc_html__($this->no_location_msg, 'cspm'), //@since 3.5
					'bad_address_msg' => esc_html__($this->bad_address_msg, 'cspm'), //@since 3.5
					'bad_address_sug_1' => esc_html__($this->bad_address_sug_1, 'cspm'), //@since 3.5
					'bad_address_sug_2' => esc_html__($this->bad_address_sug_2, 'cspm'), //@since 3.5
					'bad_address_sug_3' => esc_html__($this->bad_address_sug_3, 'cspm'), //@since 3.5
					
					/**
					 * Geotarget
					 * @since 2.8 */
					 
					'geo' => $this->geoIpControl,
					'show_user' => $this->show_user,
					'user_marker_icon' => $this->user_marker_icon,
					'user_marker_size' => $this->cspm_get_image_size(
						array(
							'path' => $this->cspm_get_image_path_from_url($this->user_marker_icon),
							'default_width' => $this->user_marker_icon_height,
							'default_height' => $this->user_marker_icon_height,																					
						)
					), //@since 5.6.5
					'user_map_zoom' => $this->user_map_zoom,
					'user_circle' => $this->user_circle,
					'user_circle_fillColor' => $this->user_circle_fillColor, //@since 3.0
					'user_circle_fillOpacity' => str_replace(',', '.', $this->user_circle_fillOpacity), //@since 3.0
					'user_circle_strokeColor' => $this->user_circle_strokeColor, //@since 3.0
					'user_circle_strokeOpacity' => str_replace(',', '.', $this->user_circle_strokeOpacity), //@since 3.0
					'user_circle_strokeWeight' => $this->user_circle_strokeWeight, //@since 3.0
					
					/**
					 * Search form settings */
					 
					'before_search_address' => $before_search_address, //@since 2.8, Add text before the search field value
					'after_search_address' => $after_search_address, //@since 2.8, Add text after the search field value
					
					/**
					 * Zoom to country 
					 * @since 3.0 */
					
					'country_zoom_or_autofit' => $this->country_zoom_or_autofit,
					'country_zoom_level' => $this->country_zoom_level,		
					
					/**
					 * Nearby points of interest 
					 * @since 3.2 */
					
					'nearby_places_option' => $this->nearby_places_option,	
					
					/**
					 * Map type
					 * @since 3.3 */
					 
					'map_type' => $this->map_type,
					
					/** 
					 * Infobox settings
					 * @since 3.9
					 * @edited 5.3 */
					
					'show_infobox' => $this->show_infobox,
					'infobox_type' => $this->infobox_type,
					'infobox_display_event' => $this->infobox_display_event,
					'infobox_external_link' => $this->infobox_external_link,
					'remove_infobox_on_mouseout' => $this->remove_infobox_on_mouseout,
					'infobox_display_zoom_level' => $this->infobox_display_zoom_level, //@since 5.3
					'infobox_show_close_btn' => $this->infobox_show_close_btn, //@since 5.3
					
					/**
					 * Marker labels
					 * @since 4.0 */
					
					'marker_labels' => $this->marker_labels,
					
					/**
					 * Marker popups
					 * @since 4.0 */
					
					'marker_popups' => $this->use_marker_popups,
					
					/**
					 * KML Layers
					 * @since 4.6 */
					
					'use_kml' => $this->use_kml,
		
					/**
					 * Overlays: Images
					 * @since 4.6 */
					 
					'ground_overlays_option' => $this->ground_overlays_option,
		
					/**
					 * Overlays: Polyline
					 * @since 4.6 */
	
					'draw_polyline' => $this->draw_polyline,
					
					/**
					 * Overlays: Polygon
					 * @since 4.6 */
	
					'draw_polygon' => $this->draw_polygon,
					
					'sync_carousel_to_viewport' => $this->sync_carousel_to_viewport, //@since 5.5
									
				)
			
			);
			
			return $map_script_args;
			
		}
		
		
		/**
		 * This will build the posts JS data.
		 *
		 * @since 4.0
		 */
		function cspm_posts_JS_data($atts = array()){
						
			extract( wp_parse_args( $atts, array(
				'map_id' => 'map'.$this->map_object_id,						
			)));
			
			$query_post_ids = $this->queried_post_ids;
			
			$js_data = array();
			
			/**
			 * Infoboxes & Carousel items */
			
			$infobox_and_carousel = $this->cspm_infobox_and_carousel_data(array(
				'post_ids' => $query_post_ids,
			));
			
			/**
			 * Posts Info 
			 * @since 4.0 */
			 
			$posts_info = $this->cspm_posts_data(array(
				'post_ids' => $query_post_ids,
			));
			
			/**
			 * Markers data
			 * @since 4.0 */
			
			$markers_data = $this->cspm_markers_data(array(
				'post_ids' => $query_post_ids,
			));
			
			$js_data = array_merge(
				$infobox_and_carousel,
				$posts_info,
				$markers_data
			);
			
			return $js_data;
			
		}
		
		
		/**
		 * This will build the infoboxes & carousel items content JS data.
		 *
		 * @since 3.9
		 * @updated 4.0
		 */
		function cspm_infobox_and_carousel_data($atts = array()){
						
			extract( wp_parse_args( $atts, array(
				'map_id' => 'map'.$this->map_object_id,		
				'post_ids' => array(),				
				'show_infobox' => $this->show_infobox,
				'show_carousel' => $this->show_carousel,
			)));
			
			$all_content = $infoboxes = $carousel_items = array();
			
			foreach($post_ids as $post_id){
				
				/**
				 * Infoboxes */
				 
				if($show_infobox == 'true'){
					$infoboxes[$post_id] = $this->cspm_infobox(array(
						'post_id' => $post_id,
						'map_id' => $map_id,
					));
				}

				/**
				 * Carousel items */
				 
				if($show_carousel == 'true')
					$carousel_items[$post_id] = $this->cspm_load_carousel_item($post_id);
					
			}
			
			if($show_infobox == 'true')
				$all_content['infoboxes_'.$map_id] = $infoboxes;
			
			if($show_carousel == 'true')
				$all_content['carousel_items_'.$map_id] = $carousel_items;
			
			return $all_content;
						
		}
		
		
		/**
		 * This will build the posts JS data.
		 *
		 * @since 4.0
		 * @updated 4.3
		 */
		function cspm_posts_data($atts = array()){
						
			extract( wp_parse_args( $atts, array(
				'map_id' => 'map'.$this->map_object_id,						
				'post_ids' => array(),
				'items_title' => $this->cspm_get_map_option('items_title'),
				'click_on_title' => $this->cspm_get_map_option('click_on_title'),
				'external_link' => $this->cspm_get_map_option('external_link'),
			)));
			
			$all_content = $titles = $links = array();
			
			/**
			 * Posts titles */
			 
			foreach($post_ids as $post_id){
				
				$titles[$post_id] = apply_filters(
					'cspm_custom_infobox_title', 
					stripslashes_deep(
						$this->cspm_items_title(array(
							'post_id' => $post_id, 
							'title' => $items_title, 
							'click_title_option' => false,
							'click_on_title' => $click_on_title,
							'external_link' => $external_link,
						))
					), 
					$post_id
				); 
				
			}
			
			if(count($titles) > 0)
				$all_content['titles_'.$map_id] = $titles;
			
			/**
			 * Posts links
			 * @since 4.3 */
						
			foreach($post_ids as $post_id){
				$links[$post_id] = $this->cspm_get_permalink($post_id); 
			}
			
			if(count($titles) > 0)
				$all_content['links_'.$map_id] = $links;
			
			return $all_content;
						
		}
		
		
		/**
		 * This will build marker JS data
		 *
		 * @since 4.0
		 * @edited 5.5 | 5.5.1
		 */
		function cspm_markers_data($atts = array()){
						
			extract( wp_parse_args( $atts, array(
				'map_id' => 'map'.$this->map_object_id,						
				'post_ids' => array(),
			)));
			
			$all_content = $marker_labels = $marker_popups = $marker_menus = array(); //@edited 5.5.1
			
			$label_index = 1;
						
			foreach($post_ids as $post_id){
				
				/**
				 * Marker labels
				 * @since 4.0 */
				 
				if($this->marker_labels == 'yes'){
					
					$marker_label_options = get_post_meta($post_id, $this->metafield_prefix.'_marker_label_options', true);
					
					if(!isset($marker_label_options[0]))
						$marker_label_options = array(array());				
					
					$hide_label = $this->cspm_setting_exists('hide_label', $marker_label_options[0], 'no');					
					$label_default = ($this->marker_labels_type == 'manual') ? '' : $label_index;
					$label_text = $this->cspm_setting_exists('text', $marker_label_options[0], $label_default);
							
					if($hide_label == 'no' && !empty($label_text)){
						
						$marker_labels[$post_id] = array(
							'hide' => $hide_label,
							'text' => apply_filters('cspm_marker_label', $label_text, $map_id, $post_id, $label_index),
							'fontFamily' => $this->cspm_setting_exists('fontFamily', $marker_label_options[0], $this->marker_labels_fontFamily),
							'fontSize' => $this->cspm_setting_exists('fontSize', $marker_label_options[0], $this->marker_labels_fontSize),
							'fontWeight' => $this->cspm_setting_exists('fontWeight', $marker_label_options[0], $this->marker_labels_fontWeight),
							'color' => $this->cspm_setting_exists('color', $marker_label_options[0], $this->marker_labels_color),
							'top' => $this->cspm_setting_exists('top', $marker_label_options[0], $this->marker_labels_top_position),
							'left' => $this->cspm_setting_exists('left', $marker_label_options[0], $this->marker_labels_left_position),
						);
						
						$label_index++;
					
					}
				
				}
				
				/** 
				 * Marker popup
				 * @since 4.0 */
				
				if($this->use_marker_popups == 'yes'){
				
					$marker_popups[$post_id] = $this->cspm_marker_popup_content(array(
						'map_id' => $map_id,
						'post_id' => $post_id,
					));
					
				}
				
				/** 
				 * Marker menu
				 * @since 5.5 */
				
				if($this->use_marker_menu == 'yes'){
				
					$marker_menus[$post_id] = $this->cspm_marker_menu_content(array(
						'map_id' => $map_id,
						'post_id' => $post_id,
					));
					
				}

			}
			
			if(count($marker_labels) > 0 && $this->marker_labels == 'yes')
				$all_content['marker_labels_'.$map_id] = $marker_labels;

			if(count($marker_popups) > 0 && $this->use_marker_popups == 'yes')
				$all_content['marker_popups_'.$map_id] = $marker_popups;

			if(count($marker_menus) > 0 && $this->use_marker_menu == 'yes') //@edited 5.5.1
				$all_content['marker_menus_'.$map_id] = $marker_menus; //@since 5.5
				
			return $all_content;
			
		}
		
		
		/**
		 * This will load the scripts needed by our shortcode based on its settings
		 *
		 * @since 3.0
		 */
		function cspm_enqueue_scripts($combine_files = ''){
			
			$combine_files = (empty($combine_files)) ? $this->combine_files : $combine_files;
			
			/**
			 * jQuery */
			 
			wp_enqueue_script('jquery');			
			wp_enqueue_script('jquery-effects-core'); //@since 5.6.9		
            
			/**
			 * Media Element | From WP Library!
			 * @since 3.5 */
			 
			wp_enqueue_script('wp-mediaelement');
			
			/**
			 * Images Loaded | From WP Library!
			 * @since 3.5 */
			 			
			wp_enqueue_script('imagesloaded');			 			

			do_action('cspm_before_enqueue_script');
			
			/**
			 * GMaps API
			 * @updated 5.2 [Possibility to override default API Key & Language] */
			
			if(!in_array('disable_frontend', $this->remove_gmaps_api)){		 
				
				$alternative_gmaps_url = sprintf(
					$this->gmaps_unformatted_url, 
					$this->alternative_api_key, 
					$this->alternative_map_language
				);

				if($alternative_gmaps_url != $this->gmaps_formatted_url && !empty($this->alternative_api_key)){
					wp_deregister_script('cs-gmaps-api-v3');
					wp_enqueue_script('cs-gmaps-api-v3', $alternative_gmaps_url, array(), false, true);
				}else wp_enqueue_script('cs-gmaps-api-v3');
				
			}
			
			$localize_script_handle = 'cspm-script';
				
			/**
			 * GMap3 jQuery Plugin */
			 
			wp_enqueue_script('cspm-gmap3');
			
			/**
			 * Marker Clusterer
			 * Note: Loaded only when using the clustering feature */
			 
			if($this->useClustring == 'true')
				wp_enqueue_script('markerclustererplus');
				
			/**
			 * Touche Swipe
			 * Note: Loaded only when using the touchswipe feature */
			 
			if($this->touchswipe_carousel == 'true' && !empty($this->map_object_id))
				wp_enqueue_script('jquery-touchswipe');
														
			/**
			 * jQuery Mousewheel
			 * Note: Loaded only when using the touchswipe feature */
			 
			if($this->scrollwheel_carousel == 'true' && !empty($this->map_object_id))
				wp_enqueue_script('jquery-mousewheel');
				
			/**
			 * jCarousel & jQuery Easing
			 * Note: Loaded only when using the carousel feature */
			 
			if($this->show_carousel == 'true' && !empty($this->map_object_id)){
				
				/**
				 * jCarousel */
				 
				wp_enqueue_script('jquery-jcarousel-06');
				
				/**
				 * jQuery Easing */
				 
				wp_enqueue_script('jquery-easing');
				
			}
				
			/**
			 * Custom Scroll bar */
			 
			wp_enqueue_script('jquery-mcustomscrollbar');

			/**
			 * icheck
			 * Note: Loaded only when using the faceted seach feature */
							
			if($this->faceted_search_option == 'true' && !empty($this->map_object_id))
				wp_enqueue_script('jquery-icheck');
				
			/**
			 * Progress Bar loader */
			 
			wp_enqueue_script('nprogress');

			/**
			 * Range Slider
			 * Note: Loaded when using the search form feature */
							
			if($this->search_form_option == 'true' && !empty($this->map_object_id))
				wp_enqueue_script('jquery-ion-rangeslider');
						
			/**
			 * iziModal
			 * @since 3.5 */
			 
			wp_enqueue_script('jquery-izimodal');
						
			/**
			 * iziToast
			 * @since 3.5 */
			 
			wp_enqueue_script('jquery-izitoast');
						
			/**
			 * Siema 
			 * @since 3.5 */
			
			wp_enqueue_script('siema');
			
			/**
			 * Tail Select
			 * @since 5.6 */
			
			wp_enqueue_script('tail-select');
			
			/**
			 * Snazzy Infobox
			 * @since 3.9 */
			
			wp_enqueue_script('snazzy-info-window');

			/**
			 * Gradstop | Generate gradient color for Heatmap Layer
			 * @since 5.3 */
			 
			if($this->heatmap_layer != 'false' && $this->heatmap_color_type == 'custom')
				wp_enqueue_script('gradstop');			
			
			/**
			 * jQuery Sidebar
			 * @since 1.0 */
			 
			wp_enqueue_script('jquery-sidebar');
							
			/**
			 * Progress Map Script */
			 
			wp_enqueue_script($localize_script_handle);
			
			/**
			 * Localize the script with new data
			 * 1) We'll get the old data already localized.
			 * 2) Add this map's options to the old data array.
			 * 3) We'll clear the old wp_localize_script(), then, send a new one that contains old & new data. */
			
			if(!empty($this->map_object_id)){

				global $wp_scripts;
				
				$progress_map_vars = $wp_scripts->get_data($localize_script_handle, 'data');
				
				$current_progress_map_vars = json_decode(str_replace('var progress_map_vars = ', '', substr($progress_map_vars, 0, -1)), true);

                $old_map_script_args = (isset($current_progress_map_vars['map_script_args'])) ? (array) $current_progress_map_vars['map_script_args'] : array(); //@edited 5.6.3
				
				$new_map_scripts_args = array_merge(
					$old_map_script_args, 
					$this->cspm_localize_map_script()
				);
				
				$current_progress_map_vars['map_script_args'] = $new_map_scripts_args;
				
				$current_progress_map_vars = array_merge(
					$current_progress_map_vars,
					$this->cspm_posts_JS_data() //@since 3.9
				);
				
				$wp_scripts->add_data($localize_script_handle, 'data', '');
				
				wp_localize_script($localize_script_handle, 'progress_map_vars', $current_progress_map_vars);
				 
			}

			do_action('cspm_after_enqueue_script');
			
		}
		
			
		/**
		 * Get the link of the post either with the get_permalink() function ...
		 * ... or the custom field defined by the user
		 *
		 * @since 2.5
		 * @updated 3.3 | 5.0
		 */
		function cspm_get_permalink($post_id){
			
			$outer_links_field_name = $this->cspm_get_plugin_setting('outer_links_field_name'); //@since 3.3
			$single_posts_link = $this->cspm_get_map_option('single_posts_link'); //@since 5.0
			
			/**
			 * Note: Make sure to test if [@single_posts_link] so we can tell if it's a light map or not!
			 * If is "custom", this means the main map!
			 * If EMPTY, this means a light map! */
			 
			if(!empty($outer_links_field_name) && ($single_posts_link == 'custom' || empty($single_posts_link))){ // @edited 5.0
				$the_permalink = get_post_meta($post_id, $outer_links_field_name, true);			
			}else $the_permalink = get_permalink($post_id);
			
			return apply_filters('cspm_get_permalink', $the_permalink, $post_id); //@edited 5.6.8
			
		}
		
		
		/**
		 * Parse item custom title 
		 */
		function cspm_items_title($atts){
			
			extract( wp_parse_args( $atts, array(
				'post_id' => '', 
				'title' => '', 
				'click_title_option' => false,
				'click_on_title' => $this->click_on_title,
				'external_link' => $this->external_link,
			)));
			
			/**
			 * Custom title structure */
			 
			$post_meta = esc_attr($title);
	
			$the_permalink = ($click_title_option && $click_on_title == 'yes') ? ' href="'.$this->cspm_get_permalink($post_id).'"' : '';
			$target = ($external_link == "new_window") ? ' target="_blank"' : '';
			
			/**
			 * Init vars */
			 
			$items_title = '';		
			$items_title_lenght = 0;
			
			/**
			 * If no custom title is set, call item original title */
			 
			if(empty($post_meta)){						
				
				$items_title = get_the_title($post_id);
				
			/**
			 * If custom title is set ... */
			 
			}else{
				
				// ... Get post metas from custom title structure
				$explode_post_meta = explode('][', $post_meta);
				
				// Loop throught post metas
				foreach($explode_post_meta as $single_post_meta){
					
					// Clean post meta name 
					$single_post_meta = str_replace(array('[', ']'), '', $single_post_meta);
					
					// Get the first two letters from post meta name
					$check_string = substr($single_post_meta, 0, 2);
					
					if(!empty($check_string)){
						
						// Separator case
						if($check_string === 's='){
							
							// Add separator to title
							$items_title .= str_replace('s=', '', $single_post_meta);
						
						// Lenght case	
						}elseif($check_string === 'l='){
							
							// Define title lenght
							$items_title_lenght = str_replace('l=', '', $single_post_meta);
						
						// Empty space case
						}elseif($single_post_meta == '-'){
							
							// Add space to title
							$items_title .= ' ';
						
						// Post metas case		
						}else{
							
							// Add post meta value to title
							$items_title .= get_post_meta($post_id, $single_post_meta, true);
								
						}
					
					}
					
				}
				
				// If custom title is empty (Maybe someone will type something by error), call original title
				if(empty($items_title)) $items_title = get_the_title($post_id);
				
			}
			
			/**
			 * Show title as title lenght is defined */
								 
			/**
			 * Use the function mb_substr() instead of substr()!
			 * mb_substr() is multi-byte safe !
			 *
			 * @since 2.8.6 */
				 
			if($items_title_lenght > 0)
				$items_title = mb_substr($items_title, 0, (int)$items_title_lenght); //@edited 5.7
				
				if($external_link == 'popup'){
					$link_class = 'class="cspm_popup_single_post"'; //@since 3.6
				}elseif($external_link == 'nearby_places'){
					$link_class = 'class="cspm_popup_nearby_palces_map"'; //@since 4.6
				}else $link_class = '';
				
			return ($click_title_option) ? '<a'.$the_permalink.''.$target.' title="'.addslashes_gpc($items_title).'" data-post-id="'.$post_id.'" '.$link_class.'>'.addslashes_gpc($items_title).'</a>' : addslashes_gpc($items_title);
			
		}
		
		
		/**
		 * Parse item custom details 
		 *
		 * @updated 2.8.6 | 5.4
		 */
		function cspm_items_details($post_id, $details, $add_ellipses){
			
			/**
			 * Custom details structure */
			 
			$post_meta = esc_attr($details);		
			
			/**
			 * Init vars */
			 
			$items_details = '';
			$items_title_lenght = 0;
			$items_details_lenght = '100';
					
			$ellipses = '';
			
			if($add_ellipses == 'yes') //@edited 5.4
				$ellipses = '&hellip;';	
									 
			/**
			 * If new structure is set ... */
			 
			if(!empty($post_meta)){
				
				/**
				 * ... Get post metas from custom details structure */
				 
				$explode_post_meta = explode('][', $post_meta);
				
				/**
				 * Loop throught post metas */
				 
				foreach($explode_post_meta as $single_post_meta){
					
					/**
					 * Clean post meta name */
					 
					$single_post_meta = str_replace(array('[', ']'), '', $single_post_meta);
					
					/**
					 * Get the first two letters from post meta name */
					 
					$check_string = substr($single_post_meta, 0, 2);
					$check_taxonomy = substr($single_post_meta, 0, 4);
					$check_content = substr($single_post_meta, 0, 7);
					
					/**
					 * Taxonomy case */
					 
					if(!empty($check_taxonomy) && $check_taxonomy == 'tax='){
						
						/**
						 * Add taxonomy term(s) */
						 
						$taxonomy = str_replace('tax=', '', $single_post_meta);
						$items_details .= implode(', ', (array) wp_get_post_terms($post_id, $taxonomy, array("fields" => "names"))); //@edited 5.7.1
						
					/**
					 * The content */
					 
					}elseif(!empty($check_content) && $check_content == 'content'){
						
						$explode_content = explode(';', str_replace(' ', '', $single_post_meta));
						
						/**
						 * Get original post details */
						 
						$post_record = get_post($post_id, ARRAY_A);
						
						/**
						 * Post content */
						 
						$post_content = trim(preg_replace('/\s+/', ' ', $post_record['post_content']));
						
						/**
						 * Post excerpt */
						 
						$post_excerpt = trim(preg_replace('/\s+/', ' ', $post_record['post_excerpt']));
						
						/**
						 * Excerpt is recommended */
						 
						$the_content = (!empty($post_excerpt)) ? $post_excerpt : $post_content;
				
						/**
						 * Show excerpt/content as details lenght is defined */
					 
						/**
						 * Use the function mb_substr() instead of substr()!
						 * mb_substr() is multi-byte safe !
						 *
						 * @since 2.8.6 */
				 
						if(isset($explode_content[1]) && $explode_content[1] > 0) 
							$items_details .= mb_substr($the_content, 0, (int)$explode_content[1]).$ellipses; //@edited 5.7
									
					/**
					 * Separator case */
					 
					}elseif(!empty($check_string) && $check_string == 's='){
						
						/**
						 * Add separator to details */
						 
						$separator = str_replace('s=', '', $single_post_meta);
						
						$separator == 'br' ? $items_details .= '<br />' : $items_details .= $separator;
						
					/**
					 * Meta post title OR Label case */
					 
					}elseif(!empty($check_string) && $check_string == 't='){
						
						/**
						 * Add label to details */
						 
						$items_details .= str_replace('t=', '', $single_post_meta);
						
					/**
					 * Lenght case */
					 
					}elseif(!empty($check_string) && $check_string == 'l='){
						
						/**
						 * Define details lenght */
						 
						$items_details_lenght = str_replace('l=', '', $single_post_meta);
						
					/**
					 * Empty space case */
					 
					}elseif($single_post_meta == '-'){
						
						/**
						 * Add space to details */
						 
						$items_details .= ' ';
						
					/**
					 * Post metas case */
					 
					}else{
	
						/**
						 * Add post metas to details */
						 
						$items_details .= get_post_meta($post_id, $single_post_meta, true);
							
					}
					
				}						
				
			}
			
			/**
			 * If no custom details structure is set ... */
			 
			if(empty($post_meta) || empty($items_details)){
				
				/**
				 * Get original post details */
				 
				$post_record = get_post($post_id, ARRAY_A, 'display');
				
				/**
				 * Post content */
				 
				$post_content = trim(preg_replace('/\s+/', ' ', $post_record['post_content']));
				
				/**
				 * Post excerpt */
				 
				$post_excerpt = trim(preg_replace('/\s+/', ' ', $post_record['post_excerpt']));
				
				/**
				 * Excerpt is recommended */
				 
				$items_details = (!empty($post_excerpt)) ? $post_excerpt : $post_content;
				
				/**
				 * Show excerpt/content as details lenght is defined */
				 
				if($items_details_lenght > 0){
					 
					/**
					 * Use the function mb_substr() instead of substr()!
					 * mb_substr() is multi-byte safe !
					 *
					 * @since 2.8.6 */
				 
					$items_details = mb_substr($items_details, 0, (int)$items_details_lenght).$ellipses; //@edited 5.7
					
				}
				
			}
			
			return addslashes_gpc($items_details);
			
		}
		
		
		/**
		 * Get Carousel Item details 
		 *
		 * @updated 3.7 | 3.9
		 */
		function cspm_load_carousel_item($post_id){
				
			global $wpdb;
			
			/**
			 * Remove the index for secondary markers. e.g. 551-0 will be 551 */
			 
			if(strpos($post_id, '-') !== false) $post_id = substr($post_id, 0, strpos($post_id, '-'));
			
			/**
			 * View style (horizontal/vertical) */
			 
			$items_view = $this->items_view;
					
			/**
			 * Get items title or custom title */
			 
			$item_title = apply_filters(
				'cspm_custom_item_title', 
				stripslashes_deep(
					$this->cspm_items_title(array(
						'post_id' => $post_id, 
						'title' => $this->cspm_get_map_option('items_title'), 
						'click_title_option' => true,
						'click_on_title' => $this->cspm_get_map_option('click_on_title'),
						'external_link' => $this->cspm_get_map_option('external_link'),
					))
				), 
				$post_id
			); 
			
			$only_item_title = apply_filters(
				'cspm_custom_item_title', 
				stripslashes_deep(
					$this->cspm_items_title(array(
						'post_id' => $post_id, 
						'title' => $this->cspm_get_map_option('items_title'), 
						'click_title_option' => false,
					))
				), 
				$post_id
			); // @since 3.6
			
			$item_description = apply_filters(
				'cspm_custom_item_description', 
				stripslashes_deep(
					$this->cspm_items_details($post_id, $this->cspm_get_map_option('items_details'), $this->cspm_get_map_option('ellipses'))
				), 
				$post_id
			);
			
			/**
			 * Create items single page link */
			 
			$the_permalink = $this->cspm_get_permalink($post_id);				
			$target = ($this->cspm_get_map_option('external_link') == "new_window") ? ' target="_blank"' : '';
			
			$external_link = $this->cspm_get_map_option('external_link');
			
			if($external_link == 'popup'){
				$link_class = ' cspm_popup_single_post'; //@since 3.6
			}elseif($external_link == 'nearby_places'){
				$link_class = ' cspm_popup_nearby_palces_map'; //@since 4.6
			}else $link_class = '';

			$more_button_text = esc_html__($this->cspm_get_map_option('details_btn_text'), 'cspm');
			$more_button_text = apply_filters('cspm_more_button_text', $more_button_text, $post_id);
			
			$output = '';
			
			/* ========================= */
			/* ==== Horizontal view ==== */
			/* ========================= */
					
			if($items_view == "listview"){
				
				if($this->cspm_get_map_option('items_featured_img', 'show') == 'show'){ //@since 3.7
						
					$this->horizontal_image_size = $this->cspm_get_map_option('horizontal_image_size', '204,150');
						
						if($explode_horizontal_img_size = explode(',', $this->horizontal_image_size)){
							$this->horizontal_img_width = $this->cspm_setting_exists(0, $explode_horizontal_img_size, '204');
							$this->horizontal_img_height = $this->cspm_setting_exists(1, $explode_horizontal_img_size, '150');
						}else{
							$this->horizontal_img_width = '204';
							$this->horizontal_img_height = '150';
						}
							
					$parameter = array(
						'class' => 'cspm_border_left_radius',
					);				
					
					/**
					 * Item thumb */
	
					$image_size = array($this->horizontal_img_width, $this->horizontal_img_height);
					
					$post_thumbnail = apply_filters(
						'cspm_post_thumb', 
						$this->cspm_get_the_post_thumbnail($post_id, $image_size, $parameter), 
						$post_id, 
						$this->horizontal_img_width, 
						$this->horizontal_img_height
					);
					
				}
								
				$output .= '<div class="item_infos">';
								
									
					/* =========================== */
					/* ==== LTR carousel mode ==== */
					/* =========================== */
					
					if($this->cspm_get_map_option('carousel_mode') == 'false'){
						
						/**
						 * Image or Thumb area */
						
						if($this->cspm_get_map_option('items_featured_img', 'show') == 'show'){ //@since 3.7
						 
							$output .= '<div class="item_img cspm_linear_gradient_bg">';
									
								$output .= $post_thumbnail;
					
							$output .= '</div>';
							
						}
						
						/**
						 * Details area */
						 
						$output .= '<div class="details_container">';
							
							/**
							 * "More" Button */
							 
							if($this->cspm_get_map_option('show_details_btn') == 'yes')
								$output .= '<div><a class="details_btn cspm_bg_rgb_hover cspm_border_radius cspm_border_shadow '.$link_class.'" href="'.$the_permalink.'" title="'.$only_item_title.'" data-post-id="'.$post_id.'" style="'.$this->cspm_get_map_option('details_btn_css').'"'.$target.'>'.$more_button_text.'</a></div>';
							
							/**
							 * Item title */
							 
							$output .= '<div class="details_title cspm_txt_hex_hover">'.$item_title.'</div>';
							
							/**
							 * Items details */
							 
							$output .= '<div class="details_infos">'.$item_description.'</div>';
							
						$output .= '</div>';
									
									
					/* =========================== */
					/* ==== RTL carousel mode ==== */
					/* =========================== */
					
					}else{
					
						/**
						 * Details area */
						 
						$output .= '<div class="details_container">';
							
							/**
							 * "More" Button */
							 
							if($this->cspm_get_map_option('show_details_btn') == 'yes')
								$output .= '<div><a class="details_btn cspm_bg_rgb_hover cspm_border_radius cspm_border_shadow '.$link_class.'" href="'.$the_permalink.'" title="'.$only_item_title.'" data-post-id="'.$post_id.'" style="'.$this->cspm_get_map_option('details_btn_css').'"'.$target.'>'.$more_button_text.'</a></div>';
							
							/**
							 * Item title */
							 
							$output .= '<div class="details_title cspm_txt_hex_hover">'.$item_title.'</div>';
							
							/**
							 * Items details */
							 
							$output .= '<div class="details_infos">'.$item_description.'</div>';
							
						$output .= '</div>';
						
						/**
						 * Image or Thumb area */
						
						if($this->cspm_get_map_option('items_featured_img', 'show') == 'show'){ //@since 3.7
						 
							$output .= '<div class="item_img cspm_linear_gradient_bg">';
									
								$output .= $post_thumbnail;
					
							$output .= '</div>';
							
						}
					
					}
					
					$output .= '<div style="clear:both"></div>';				
					
				$output .= '</div>';
			
			
			/* ======================= */
			/* ==== Vertical view ==== */
			/* ======================= */
					
			}elseif($items_view == "gridview"){					
				
				if($this->cspm_get_map_option('items_featured_img', 'show') == 'show'){ //@since 3.7
								
					$this->vertical_image_size = $this->cspm_get_map_option('vertical_image_size', '204,120');			
						
						if($explode_vertical_img_size = explode(',', $this->vertical_image_size)){
							$this->vertical_img_width = $this->cspm_setting_exists(0, $explode_vertical_img_size, '204');
							$this->vertical_img_height = $this->cspm_setting_exists(1, $explode_vertical_img_size, '120');
						}else{
							$this->vertical_img_width = '204';
							$this->vertical_img_height = '120';
						}
								 
					$parameter = array(
						'class' => 'cspm_border_top_radius',
					);
					
					/**
					 * Item thumb */
					
					$image_size = array($this->vertical_img_width, $this->vertical_img_height);
					
					$post_thumbnail = apply_filters(
						'cspm_post_thumb', 
						$this->cspm_get_the_post_thumbnail($post_id, $image_size, $parameter), 
						$post_id, 
						$this->vertical_img_width, 
						$this->vertical_img_height
					);
						
				}
				
				$output .= '<div class="item_infos">';
					
					/**
					 * Image or Thumb area */
					
					if($this->cspm_get_map_option('items_featured_img', 'show') == 'show'){ //@since 3.7
					 
						$output .= '<div class="item_img cspm_linear_gradient_bg">';
								
							$output .= $post_thumbnail;
				
						$output .= '</div>';
					
					}
					
					/**
					 * Details area	*/
					 
					$output .= '<div class="details_container">'; 
						
						/**
						 * "More" Button */
						 
						if($this->cspm_get_map_option('show_details_btn') == 'yes')
							$output .= '<div><a class="details_btn cspm_bg_rgb_hover cspm_border_radius cspm_border_shadow '.$link_class.'" href="'.$the_permalink.'" title="'.$only_item_title.'" data-post-id="'.$post_id.'" style="'.$this->cspm_get_map_option('details_btn_css').'"'.$target.'>'.$more_button_text.'</a></div>';
						
						/**
						 * Item title */
						 
						$output .= '<div class="details_title cspm_txt_hex_hover">'.$item_title.'</div>';
						
						/** 
						 * Items details */
						 
						$output .= '<div class="details_infos">'.$item_description.'</div>';
						
					$output .= '</div>';
					
					$output .= '<div style="clear:both"></div>';
					
				$output .= '</div>';
				
			}
		   
			return $output;
					
		}
		
		
	   /**
		* Build the main query
		*
		* @since 2.0 
		* @updated 2.8.2
		* @updated 2.8.6
		* @updated 3.0 [@added @order_meta_type]
		* @updated 4.8 [@Changed "query_posts" & "get_posts" with "WP_Query"]
		*/
		function cspm_main_query($atts = array()){

			if(class_exists('CSProgressMap'))
				$CSProgressMap = CSProgressMap::this();
			
			extract( wp_parse_args( $atts, array(
				'post_type' => '', 
				'post_status' => '',
				'number_of_posts' => '', 
				'tax_query' => '',
				'tax_query_field' => 'id', // @since 2.6.1
				'tax_query_relation' => '', 
				'cache_results' => '', 
				'update_post_meta_cache' => '',
				'update_post_term_cache' => '',
				'post_in' => '',
				'post_not_in' => '',
				'custom_fields' => '',
				'custom_field_relation' => '',
				'authors' => '',
				'orderby' => '',
				'orderby_meta_key' => '',
				'order_meta_type' => '', //@since 3.0
				'order' => '',
				'optional_latlng' => $this->optional_latlng,
			)));

			/**
			 * Post type */
			 
			if(empty($post_type)) $post_type = (empty($this->post_type)) ? 'post' : $this->post_type;					
			$post_type_array = explode(',', esc_attr($post_type));
					
			/**
			 * Query limit */
			 
			if(empty($number_of_posts)) 
				$nbr_items = (empty($this->number_of_items)) ? -1 : $this->number_of_items;
			else $nbr_items = esc_attr($number_of_posts);
			
			/**
			 * Status */
			 
			if(empty($post_status)){ 
				$status = $this->post_status;
			}else $status = explode(',', esc_attr($post_status));
			
			/**
			 * Caching */		 
			 
			if(empty($cache_results)) 
				$cache_results = ($this->cache_results == 'true') ? true : false;
			else $cache_results = (esc_attr($cache_results) == 'true') ? true : false;
			
			if(empty($update_post_meta_cache))
				$update_post_meta_cache = ($this->update_post_meta_cache == 'true') ? true : false;
			else $update_post_meta_cache = (esc_attr($update_post_meta_cache) == 'true') ? true : false;
			
			if(empty($update_post_term_cache))
				$update_post_term_cache = ($this->update_post_term_cache == 'true') ? true : false;					
			else $update_post_term_cache = (esc_attr($update_post_term_cache) == 'true') ? true : false;		
			
			/**
			 * Post parameters */
			 
			if(empty($post_in))
				$post_in = $this->post_in;
			else $post_in = explode(',', esc_attr($post_in));
			
			if(empty($post_not_in))
				$post_not_in = $this->post_not_in;		
			else $post_not_in = explode(',', esc_attr($post_not_in));
			
			/**
			 * OrderBy meta key */
			 
			if(empty($orderby)){ 
				$orderby_param = $this->orderby_param;
				$orderby_meta_key = ($orderby_param != 'meta_value' && $orderby_param != 'meta_value_num') ? '' : $this->orderby_meta_key;
				$order_meta_type = ($orderby_param == 'meta_value') ? $this->order_meta_type : ''; //@since 3.0
				$order_param = $this->order_param;
			}else{
				$orderby_param = esc_attr($orderby);
				$orderby_meta_key = esc_attr($orderby_meta_key);
				$order_meta_type = esc_attr($order_meta_type); //@since 3.0
				$order_param = strtoupper(esc_attr($order));
			}
			
			/**
			 * Custom fields */
			 
			if(empty($custom_fields)) 
				$custom_fields = $this->cspm_parse_query_meta_fields($this->custom_fields, $this->custom_field_relation_param, $optional_latlng);
			else $custom_fields = $this->cspm_parse_query_meta_fields(esc_attr($custom_fields), strtoupper(esc_attr($custom_field_relation)), $optional_latlng);
		
			/**
			 * Taxonomies */
			 
			if(empty($tax_query)){
							
				$post_taxonomies = (array) get_object_taxonomies($this->post_type, 'objects');
					unset($post_taxonomies['post_format']); // @since 3.0 | Remove the taxonomy "post_format" from the list.
				
				$taxonomies_array = array();
				
				/**
				 * Loop throught taxonomies	*/
				 
				foreach($post_taxonomies as $single_taxonomie){
					
					/**
					 * Get Taxonomy Name */
					 
					$tax_name = $single_taxonomie->name;
					
					/**
					 * Make sure the taxonomy operator exists */
					
					$taxonomy_operator_param = $this->cspm_get_map_option($tax_name.'_operator_param', 'IN');
					 
					if(!empty($taxonomy_operator_param)){
						
						/**
						 * Get all terms related to this taxonomy */
						
						$terms = $this->cspm_get_map_option('taxonomie_'.$tax_name, array());
						 
						if(is_array($terms) && count($terms) > 0){
							
							// For WPML =====
							$WPML_terms = array();
							foreach($terms as $term)
								$WPML_terms[] = $CSProgressMap->cspm_wpml_object_id($term, $tax_name, false, '', $this->use_with_wpml);
							// @For WPML ====							 
							
							$taxonomies_array[] = array(
								'taxonomy' => $tax_name,
								'field' => 'id',
								'terms' => $WPML_terms,
								'operator' => $taxonomy_operator_param,
						   );
						
						}
						
					}
					
				}
				
				$tax_query = (count($taxonomies_array) == 0) ? array('tax_query' => '') : array('tax_query' => array_merge(array('relation' => $this->taxonomy_relation_param), $taxonomies_array));
	
			}else{
				
				/**
				 * tax_query = "cat_1{1.2.3|IN},cat_2{2.3|NOT IN}"
				 * tax_query_relation = "AND" */
				
				$taxonomies_array = array();
				
				$taxonomy_term_names_and_term_ids = explode(',',  str_replace(' ', '', esc_attr($tax_query))); // array(cat_1{1.2.3|IN}, cat_2{2.3|NOT IN})
				
				if(count($taxonomy_term_names_and_term_ids) > 0){
					
					foreach($taxonomy_term_names_and_term_ids as $single_term_name_and_term_ids){
						
						$term_name = $term_relation = '';
						$term_ids = array();
						
						$term_name_and_term_ids = explode('{', $single_term_name_and_term_ids); // array(cat_1, 1.2.3|IN})
				
						if(isset($term_name_and_term_ids[0])) $term_name = $term_name_and_term_ids[0]; // cat_1
						
						if(isset($term_name_and_term_ids[0])){
							
							$term_ids_and_relation = explode('|', $term_name_and_term_ids[1]); // array(1.2.3, IN})
						
							if(isset($term_ids_and_relation[0])) $term_ids = explode('.', $term_ids_and_relation[0]); // array(1, 2, 3)
							
							if(isset($term_ids_and_relation[0])) $term_relation = str_replace('}', '', $term_ids_and_relation[1]); // IN;
							
						}
						
						if(count($term_ids) > 0){
													
							// For WPML ===== 
							$WPML_terms = array();
							foreach($term_ids as $term)
								$WPML_terms[] = $CSProgressMap->cspm_wpml_object_id($term, $term_name, false, '', $this->use_with_wpml);
							// @For WPML ====
	
							$taxonomies_array[] = array(
								'taxonomy' => $term_name,
								'field' => $tax_query_field, //'id',
								'terms' => $WPML_terms,
								'operator' => strtoupper($term_relation),
						   );
													   
						}
													   
					}
					
				}
				
				$tax_query = (count($taxonomies_array) == 0) ? array('tax_query' => '') : array('tax_query' => array_merge(array('relation' => strtoupper(esc_attr($tax_query_relation))), $taxonomies_array));
				
			}
				
			/**
			 * Authors
			 * @updated 5.6.5 */
					
			$authors_condition = $this->authors_prefixing;
			$authors_type = $this->authors_type; //@since 5.6.5			
			$authors_prefixing = ($authors_condition == 'false') ? 'author__in' : 'author__not_in'; //@edited 5.6.5
			 
			if(empty($authors)){
				
				if(isset($this->authors)){
						
					$author_ids = (array) $this->authors;
					 
					if(is_user_logged_in() && $authors_type == 'logged_in_authors'){
						$author_ids = (array) get_current_user_id();
					} //@since 5.6.5
					
				}
				
			}else $author_ids = explode(',', esc_attr($authors)); //@edited 5.6.5
			
			/**
			 * Call items ids */
			 
			$query_args = array( 
				'post_type' => $post_type_array,							
				'post_status' => $status, 
				
				'posts_per_page' => $nbr_items, 
				
				'tax_query' => $tax_query['tax_query'],
				
				'cache_results' => $cache_results,
				'update_post_meta_cache' => $update_post_meta_cache,
				'update_post_term_cache' => $update_post_term_cache,
				
				'post__in' => $post_in,
				'post__not_in' => $post_not_in,
				
				'meta_query' => $custom_fields['meta_query'],
				
				$authors_prefixing => $author_ids, //@edited 5.6.5
				
				'orderby' => $orderby_param,
				'meta_key' => $orderby_meta_key,
				'meta_type' => $order_meta_type, //@since 3.0
				'order' => $order_param,
				'fields' => 'ids'
			);
			
			$query_args = apply_filters('cs_progress_map_main_query', $query_args);
										 
			$query_args = (isset($query_args['fields']) && $query_args['fields'] == 'ids') ? $query_args : $query_args + array('fields' => 'ids');

			/**
			 * Execute query */
			
			$the_main_query = new WP_Query( $query_args );

			$post_ids = $the_main_query->posts;

			/**
			 * Reset Wp_Query */
			 
			wp_reset_postdata();
			
			return $post_ids;
			
		}
		
	   
	   /**
		* Parse custom fields to use in the main query
		*
		* @since 2.0 
		* @updated 2.8.5
		* @updated 3.0
		*/
		function cspm_parse_query_meta_fields($meta_fields, $relation, $optional_latlng = 'false'){

			if(class_exists('CSProgressMap'))
				$CSProgressMap = CSProgressMap::this();
						
			$custom_fields = array('meta_query' => '');
			
			if(is_array($meta_fields) && count($meta_fields) > 0){
				
				/**
				 * Init meta_query array */
				 
				$meta_query_array = array();
	
				/**
				 * Loop through the custom fields */
				 
				foreach($meta_fields as $single_meta_field){
	
					/**
					 * Init parameters array.
					 * On each turn of the loop, $parameters_array must be empty */
					 
					$parameters_array = array();
				
					if(isset($single_meta_field['custom_field_name']) && !empty($single_meta_field['custom_field_name'])){
						
						/**
						 * Key */
						
						$parameters_array['key'] = $single_meta_field['custom_field_name'];
						
						/**
						 * Value(s) */
						 
						$value_values = isset($single_meta_field['custom_field_values']) ? $single_meta_field['custom_field_values'] : '';
						
						$values_array = explode(',', $value_values);
						
						if(count($values_array) == 1){
                            $parameters_array['value'] = $values_array[0];
                        }else{
                            $parameters_array['value'] = $values_array;                            
                        }
													
						/**
						 * Type */
						
						$parameters_array['type'] = isset($single_meta_field['custom_field_type']) ? $single_meta_field['custom_field_type'] : '';
						
						/**
						 * Compare */
						
						$parameters_array['compare'] = isset($single_meta_field['custom_field_compare_param']) ? $single_meta_field['custom_field_compare_param'] : '';
						
						/**
						 * Associate them to meta_query[] */
						 
						$meta_query_array[] = $parameters_array;	
						
					}
					
				}
			
				/**
				 * Check whether we'll display locations with no coordinates for the extension "List & Filter"
				 * @since 2.8.5 */
				 
				if($optional_latlng == 'false'){
					 
					$meta_query_array[] = array(
						'key' => CSPM_LATITUDE_FIELD,
						'value' => '',
						'compare' => '!='
					);
					
					$meta_query_array[] = array(
						'key' => CSPM_LONGITUDE_FIELD,
						'value' => '',
						'compare' => '!='
					);					 
					
				}
				
				$custom_fields = array('meta_query' => array_merge(array('relation' => $relation), $meta_query_array));
		
			}elseif($optional_latlng == 'false'){
				
				$custom_fields = array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => CSPM_LATITUDE_FIELD,
							'value' => '',
							'compare' => '!='
						),
						array(
							'key' => CSPM_LONGITUDE_FIELD,
							'value' => '',
							'compare' => '!='
						)
					)
				);
								
			}

			return $custom_fields;
						
		}
		
		
		/**
		 * This will allow to override the default query using the supper global "$_COOKIE"
		 *
		 * 1. Session name must be "cspm_session_override_query_args"!
		 * 2. Must provide the URL of the page(s) where to override the query!
		 *
		 * $_COOKIE[SESSION_NAME]['override_on_urls'] = array(URL1, URL2, ...)
		 * $_COOKIE[SESSION_NAME]['query_args'] = WP Query Args		 
		 *
		 * @since 4.7
		 */
		function cspm_override_query_args_using_sessions($query_args){
			
			if(isset($_COOKIE[CSPM_SESSION_OVERRIDE_QUERY_ARGS]))
				$override_query_cookie = json_decode(stripslashes($_COOKIE[CSPM_SESSION_OVERRIDE_QUERY_ARGS]), true);
						
			if( isset($override_query_cookie['override_on_urls'], $override_query_cookie['query_args'])
				&& is_array($override_query_cookie['override_on_urls'])
				&& is_array($override_query_cookie['query_args']) ){
				
				global $wp, $wp_rewrite;
				
				$rewrite = $wp_rewrite->wp_rewrite_rules();
				
				/**
				 * Note: Remove pagination attributes from URL before testing, then, ...
				 * return URL to test with! */
				 
				if(empty($rewrite)){
					$https = is_ssl() ? 'https://' : 'http://';
					$url_request = $https . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					$current_url = remove_query_arg(array('paged', 'page', 'paginate'), $url_request);
				}else{
					$url_request = home_url(add_query_arg(array(), $wp->request));
					$url_patterns = array('/page\/[0-9]+/', '/page\/[0-9]+\//', '/paged\/[0-9]+/', '/paged\/[0-9]+\//');
					$current_url = untrailingslashit(preg_replace($url_patterns, '', $url_request));
				}

				$override_on_urls = (empty($rewrite)) ? $override_query_cookie['override_on_urls']
					: array_map(
						'untrailingslashit', 
						$override_query_cookie['override_on_urls']
					);
				
				if(in_array($current_url, $override_on_urls))
					$query_args = $override_query_cookie['query_args'];	
				
			}

			return $query_args;
			
		}
		

		/**
		 * Main map
		 */
		function cspm_main_map_function($atts = array()){
			
			/**
			 * Prevent the shortcode from been executed in the WP admin.
			 * This will prevent errors like the error "headers already sent"!
			 * @since 3.4 */
			 
			if(is_admin())
				return;
				
			/**
			 * Overide the default post_ids array by the shortcode atts post_ids */
			 
			extract( wp_parse_args( $atts, array(
			
				'map_id' => 'initial',
				'post_ids' => '',
			
				/**
				 * Layout
				 * @since 2.8 */
				 
				'layout' => $this->main_layout,
			
				/**
				 * Map Settings */
				 				 
				'center_at' => '',	
				'zoom' => $this->zoom,
				'show_secondary' => 'yes',
				
				/**
				 * Carousel */
				 
				'carousel' => 'yes',
				
				/**
				 * Faceted Search */
				 
				'faceted_search' => 'yes',
				'faceted_search_tax_slug' => esc_attr($this->marker_categories_taxonomy),
				'faceted_search_tax_terms' => '',
				
				/**
				 * Search Form */
				 
				'search_form' => 'yes',
				
				/**
				 * Infobox */
				'infobox_type' => $this->infobox_type, //@since 2.6.3
				'infobox_link_target' => $this->infobox_external_link, //@since 2.8.6 | Possible value, "same_window", "new_window" & "disable"
				
				/**
				 * Geotarget
				 * @since 2.7.4 */
				 
				'geo' => $this->geoIpControl,
				'show_user' => $this->show_user,
				'user_marker' => $this->user_marker_icon,
				'user_map_zoom' => $this->user_map_zoom,
				'user_circle' => $this->user_circle,
				
				/**
				 * Overlays: Polyline, Polygon
				 * @since 2.7 */
				 
				'polyline_objects' => $this->polylines,
				'polygon_objects' => $this->polygons,
				
				/**
				 * [@optional_latlng] Wether we will display all posts event those with no Lat & Lng.
				 * Note: This is only used for the extension "List & Filter"
				 * @since 2.8 */				 
				
				'optional_latlng' => $this->optional_latlng,
				
				/**
				 * [@window_resize] Whether to recenter the Map on window resize or not.
				 * @since 2.8.5 */
				
				'window_resize' => 'yes', // Possible values, "yes" & "no"
			  				
			)));

			$map_id = esc_attr($map_id);
			$map_layout = esc_attr($layout);
			
			/**
			 * Get the terms to use in the faceted search.
			 * This will override the default settings */
			 
			$faceted_search_tax_terms = (empty($faceted_search_tax_terms)) ? array() : explode(',', $faceted_search_tax_terms);
			
			/**
			 * Map Styling */
						
			$map_styles = array();
			
			if($this->style_option == 'progress-map'){
					
				/**
				 * Include the map styles array */				 
		
				if(file_exists($this->map_styles_file))

					$map_styles = include($this->map_styles_file);
						
			}elseif($this->style_option == 'custom-style' && !empty($this->js_style_array)){
				
				$this->map_style = 'custom-style';
				$map_styles = array('custom-style' => array('style' => $this->js_style_array));
				
			}
			
			do_action('cspm_before_main_map_query', $map_id, $atts); // @since 2.6.3
			
			/**
			 * If post ids being pased from the shortcode parameter @post_ids
			 * Check the format of the @post_ids value */
			 
			if(!empty($post_ids)){
				
				$query_post_ids = explode(',', $post_ids);	
					
			}else{
				
				/**
				 * The main query */
				
				$query_post_ids = $this->queried_post_ids;
	
			}
				
			$post_type = !empty($post_type) ? esc_attr($post_type) : $this->post_type;
			$post_ids = apply_filters('cspm_main_map_post_ids', $query_post_ids, $map_id, $atts);
										
			/**
			 * Get the center point */
			 
			if(!empty($center_at)){
				
				$center_point = esc_attr($center_at);
				
				/**
				 * If the center point is Lat&Lng coordinates */
				 
				if(strpos($center_point, ',') !== false){
						
					$center_latlng = explode(',', str_replace(' ', '', $center_point));
					
					/**
					 * Get lat and lng data */
					 
					$centerLat = isset($center_latlng[0]) ? $center_latlng[0] : '';
					$centerLng = isset($center_latlng[1]) ? $center_latlng[1] : '';
					
				/**
				 * If the center point is a post id */
				 
				}else{
					
					/**
					 * Center the map on the first post in $post_ids */
					 
					if($center_point == "auto" && count($post_ids) > 0 && isset($post_ids[0]))
						$center_point = $post_ids[0];					
					
					/**
					 * Get lat and lng data */
					 
					$centerLat = get_post_meta($center_point, CSPM_LATITUDE_FIELD, true);
					$centerLng = get_post_meta($center_point, CSPM_LONGITUDE_FIELD, true);
					
			
				}
				
				/**
				 * The center point */
				 
				$center_point = array($centerLat, $centerLng);
			
			}else{
				
				/**
				 * The center point */
				 
				$center_point = explode(',', $this->center);
					
				/**
				 * Get lat and lng data */
				 
				$centerLat = isset($center_latlng[0]) ? $center_latlng[0] : '';
				$centerLng = isset($center_latlng[1]) ? $center_latlng[1] : '';
				
			}
							
			$latLng = '"'.$centerLat.','.$centerLng.'"';

			$zoom = esc_attr($zoom);
			$carousel = ($this->show_carousel == 'true' && esc_attr($carousel) == 'yes') ? 'yes' : 'no';
			$faceted_search = esc_attr($faceted_search);
			$search_form = esc_attr($search_form);
			
			/**
			 * Polyline PHP Objects
			 * @since 2.7 */
			
			$polylines = '';		
			
			if(!empty($polyline_objects) && is_array($polyline_objects))
				$polylines = $this->cspm_build_polyline_objects($polyline_objects);
			
			/**
			 * Polygon PHP Objects
			 * @since 2.7 */
			
			$polygons = '';		
			
			if(!empty($polygon_objects) && is_array($polygon_objects))
				$polygons = $this->cspm_build_polygon_objects($polygon_objects);
			
			/**
			 * Map Holes PHP Objects
			 * @since 5.3 */
			
			$map_holes = '';		
			
			if(!empty($this->map_holes) && is_array($this->map_holes))
				$map_holes = $this->cspm_build_map_holes_objects($this->map_holes);
				
			/**
			 * Infoboxes JS options
			 * @since 3.9
			 * @updated 5.3 */
					
			$infobox_options = array(
				'type' => $infobox_type,
				'display_event' => $this->infobox_display_event,
				'link_target' => $infobox_link_target,
				'remove_on_mouseout' => $this->remove_infobox_on_mouseout,
				'display_zoom_level' => $this->infobox_display_zoom_level, //@since 5.3
				'show_close_btn' => $this->infobox_show_close_btn, //@since 5.3
				'map_type' => 'main_map', //@since 5.3
			);
					
			/**
			 * Marker popups
			 * @since 4.0 */
			 
			$marker_popups_options = array(		
				'placement' => $this->marker_popups_placement,
				'bgColor' => $this->marker_popups_bg_color,
				'color' => $this->marker_popups_color,
				'fontSize' => $this->marker_popups_fontSize,
				'fontWeight' => $this->marker_popups_fontWeight,
			);
					
			/**
			 * Marker menu
			 * @since 5.5 */
			
			$proximities_popup_msg = esc_html__('A new location has been selected and can be used to display nearby points of interest!', 'cspm');
			
			$marker_menu_options = array(		
				'placement' => $this->marker_menu_placement,
				'mouse_enter' => $this->marker_menu_open_mouse_enter,
				'mouse_out' => $this->marker_menu_close_mouse_out,
				'close_map_click' => $this->marker_menu_close_map_click,
				'close_btn' => $this->marker_menu_show_close_btn,
				'bgColor' => $this->marker_menu_bg_color,
				'color' => $this->marker_menu_color,
				'fontSize' => $this->marker_menu_fontSize,
				'fontWeight' => $this->marker_menu_fontWeight,
				'proximities' => $this->nearby_places_option,
				'proximities_msg' => $proximities_popup_msg, 
			);
			
			ob_start(); //@since 4.9.1
				
			/**
			 * Execute actions before map load
			 * @since 5.6.2 */
			 
			do_action('cspm_do_before_map_load', $map_id); 
						
			?>

			<script>
	
			jQuery(document).ready(function($) { 		
				
				"use strict"; 
				
				var map_id = '<?php echo $map_id ?>';
				
				if(typeof _CSPM_DONE === 'undefined' || _CSPM_DONE[map_id] === true) 
					return;
				
				_CSPM_DONE[map_id] = false;
				_CSPM_MAP_RESIZED[map_id] = 0;
				
				/**
				 * Start displaying the Progress Bar loader */
				 
				if(typeof NProgress !== 'undefined'){
					
					NProgress.configure({
					  parent: 'div#codespacing_progress_map_div_'+map_id,
					  showSpinner: true
					});				
					
					NProgress.start();
					
				}
				
				/**
				 * @map_layout, Will contain the layout type of the current map.
				 * This variable was first initialized in "progress_map.js"!
				 * @since 2.8 */
									
				map_layout[map_id] = '<?php echo $map_layout; ?>';
				
				/**
				 * @cspm_infoboxes, Will store the created marker infoboxes in the viewport of the map.
				 * This will prevent creating the same infobox multiple times! */
				 
				cspm_infoboxes[map_id] = []; //@since 3.9
				
				/**
				 * @cspm_marker_popups, Will store the created marker popups in the viewport of the map.
				 * This will prevent creating the same popup multiple times! */
				 
				cspm_marker_popups[map_id] = []; //@since 4.0
								
				/**
				 * @post_ids_and_categories, Will store the markers categories in order to use with faceted search and to define the marker icon */
				 
				post_ids_and_categories[map_id] = {}; 
				
				/** 
				 * @post_lat_lng_coords, Will store the markers coordinates in order to use them when rewriting the map & the carousel */
				 
				post_lat_lng_coords[map_id] = {}; 
				
				/**
				 * @post_ids_and_child_status, Will store the markers and their child status in order to use when rewriting the carousel */
				 
				post_ids_and_child_status[map_id] = {}; 
				
				/**
				 * @json_markers_data, Will store the markers objects */
				 
				var json_markers_data = [];					
		
				var false_latLngs = []; // Contains the IDs of all posts with false latLng | @since 3.6
				var false_ground_overlays = []; // Contains the names of all images with wrong coordinates | @since 3.6
				
				/**
				 * init plugin map */
				 
				var plugin_map_placeholder = 'div#codespacing_progress_map_div_'+map_id;
				var plugin_map = $(plugin_map_placeholder);
				
				/**
				 * Load Map options */
				 
				<?php if(!empty($center_at)){ ?>
					var map_options = cspm_load_map_options('<?php echo $map_id; ?>', false, <?php echo $latLng; ?>, <?php echo $zoom; ?>);
				<?php }else{ ?>
					var map_options = cspm_load_map_options('<?php echo $map_id; ?>', false, null, <?php echo $zoom; ?>);
				<?php } ?>
				
				/**
				 * Activate the new google map visual */
				 
				google.maps.visualRefresh = true;				

				/**
				 * The initial map style */
				 
				var initial_map_style = "<?php echo $this->initial_map_style; ?>";
				
				/**
				 * Enhance the map option with the map type id of the style */
				 
				<?php if(count($map_styles) > 0 && $this->map_style != 'google-map' && isset($map_styles[$this->map_style])){ ?> 
										
					/**
					 * The initial style */
					 
					var map_type_id = cspm_initial_map_style(initial_map_style, true);
		
					/**
					 * Map type control option */
					 
					var mapTypeControlOptions = {
						mapTypeControlOptions: {
							style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
							position: google.maps.ControlPosition.TOP_RIGHT,
							mapTypeIds: [
								google.maps.MapTypeId.ROADMAP,
								google.maps.MapTypeId.SATELLITE,
								google.maps.MapTypeId.TERRAIN,
								google.maps.MapTypeId.HYBRID,
								"custom_style", 
							]				
						}
					};
												
					var map_options = $.extend({}, map_options, map_type_id, mapTypeControlOptions);
					
				<?php }else{ ?>
										
					/**
					 * The initial style */
					 
					var map_type_id = cspm_initial_map_style(initial_map_style, false);
					var map_options = $.extend({}, map_options, map_type_id);
					
				<?php } ?>
				
				<?php 
				
				/**
				 * Restrict map bounds
				 * @since 5.2 */
				
				if($this->restrict_map_bounds == 'yes' && !empty($this->map_bounds_restriction)){ ?>
				
					var map_bounds_array = []; <?php
					
					$clean_map_bounds = str_replace(
						array('],[', '[', ']'), 
						array('|', '', ''), 
						$this->map_bounds_restriction
					);
					
					$explode_map_bounds = explode('|', $clean_map_bounds);
					
					if(is_array($explode_map_bounds) && count($explode_map_bounds) == 2){						
						
						foreach($explode_map_bounds as $bounds_latlng){ 
							$latlng = explode(',', $bounds_latlng);
							if(is_array($latlng) && count($latlng) == 2){ ?>
								map_bounds_array.push(cspm_validate_latLng('<?php echo $latlng[0]; ?>', '<?php echo $latlng[1]; ?>')); <?php
							}
						}
						
						?>
	
						var ne = map_bounds_array[0];
						var sw = map_bounds_array[1];
						
						if(ne != false && sw != false){
							var strictBounds = '<?php echo $this->strict_bounds; ?>';
							var map_bounds_restriction = {
								restriction: {
									strictBounds: (strictBounds == 'relaxed') ? false : true,
									latLngBounds: new google.maps.LatLngBounds(sw, ne)
								}
							}
							
							var map_options = $.extend({}, map_options, map_bounds_restriction);
						} 
						
						<?php
						
					}
					
				}
				
				?>				

				<?php 
				
				/** 
				 * Determine whether we'll remove the carousel or not.
				 * Note: In this plugin, a map without carousel is called "Light Map"! */
				 
				$light_map = ($map_layout == 'fullscreen-map' || $map_layout == 'fit-in-map' || $this->show_carousel == 'false' || $carousel == 'no' || ($this->layout_type == 'responsive' && $this->cspm_hide_carousel_from_mobile()) || (isset($list_ext) && $list_ext == 'yes')) ? true : false;					
				
				/**
				 * @polylines_of_post_ids (array) - This will hold the coordinates of all polylines built from post IDs.
				 * @polygons_of_post_ids (array) - This will hold the coordinates of all polygons built from post IDs.
				 * @map_holes_of_post_ids (array) - This will hold the coordinates of all map holes built from post IDs.
				 * @since 2.7
				 * @edited 5.3 [Add map holes variable] */
				
				$polylines_of_post_ids = $polygons_of_post_ids = $map_holes_of_post_ids = array();
				
				/**
				 * @markers_array (array) - Contain all the makers of all post types
				 * @markers_object (array) - Contain all the markers of a given post type */
				 
				$markers_array = get_option('cspm_markers_array');
				$markers_object = isset($markers_array[$post_type]) ? $markers_array[$post_type] : array();

				if($light_map){ ?> var light_map = true; <?php }else{ ?> var light_map = false; <?php } 
															
				/**
				 * Count items */
				 
				$count_post = count($post_ids);
				
				/**
				 * [$m] Refers to the current marker in the loop */
					
				$m = $nbr_pins = 0;
				
				if($count_post > 0){
					
					while($m < $count_post){
						
						$post_id = isset($markers_object['post_id_'.$post_ids[$m]]['post_id']) ? $markers_object['post_id_'.$post_ids[$m]]['post_id'] : '';
														
						$lat = isset($markers_object['post_id_'.$post_ids[$m]]['lat']) ? $markers_object['post_id_'.$post_ids[$m]]['lat'] : '';
						$lng = isset($markers_object['post_id_'.$post_ids[$m]]['lng']) ? $markers_object['post_id_'.$post_ids[$m]]['lng'] : '';						
						$address = isset($markers_object['post_id_'.$post_ids[$m]]['address']) ? $markers_object['post_id_'.$post_ids[$m]]['address'] : ''; //@since 5.4
						$is_child = 'no';
						$child_markers  = isset($markers_object['post_id_'.$post_ids[$m]]['child_markers']) ? $markers_object['post_id_'.$post_ids[$m]]['child_markers'] : array();
						$media = isset($markers_object['post_id_'.$post_ids[$m]]['media']) ? $markers_object['post_id_'.$post_ids[$m]]['media'] : array(); //@since 3.5
						
						if(!empty($post_id) && !empty($lat) && !empty($lng)){
							
							if($this->marker_cats_settings == 'true'){
								
								/**
								 * 1. Get marker category
								 * Note: [@post_categories] | We're not using the function wp_get_post_terms() because it may ...
								 * ... loop throught big amount of markers which will slows down the map. Instead ...
								 * ... we'll use another workaround using the already builtin array that contains all our marker and their categories! */
								 
								$post_categories = isset($markers_object['post_id_'.$post_ids[$m]]['post_tax_terms'][$faceted_search_tax_slug]) ? $markers_object['post_id_'.$post_ids[$m]]['post_tax_terms'][$faceted_search_tax_slug] : array();
								$implode_post_categories = (count($post_categories) == 0) ? '' : implode(',', $post_categories);
								
								/**
								 * 2. Get marker image */
								 
								$marker_img_and_size = $this->cspm_get_marker_img(
									array(
										'post_id' => $post_id,
										'tax_name' => $this->marker_categories_taxonomy,
										'term_id' => (isset($post_categories[0])) ? $post_categories[0] : '',											
										'marker_width' => $this->marker_icon_width, //@since 4.0
										'marker_height' => $this->marker_icon_height, //@since 4.0
									)
								);
								
								$marker_img_by_cat = $marker_img_and_size['image'];

								/**
								 * 3. Get marker image size for Retina or SVG display */
								 
								$marker_img_size = $this->cspm_get_image_size(
									array(
										'path' => $this->cspm_get_image_path_from_url($marker_img_by_cat),											
										'default_width' => $marker_img_and_size['size']['width'],
										'default_height' => $marker_img_and_size['size']['height'],				
									)
								);
												
							}else{
								
								/**
								 * 1. Get marker category */
								 
								$post_categories = array();
								$implode_post_categories = '';
								
								/**
								 * 2. Get marker image */
								 
								$marker_img_and_size = $this->cspm_get_marker_img(
									array(
										'post_id' => $post_id,											
										'marker_width' => $this->marker_icon_width, //@since 4.0	
										'marker_height' => $this->marker_icon_height, //@since 4.0										
									)
								);
								
								$marker_img_by_cat = $marker_img_and_size['image'];

								/**
								 * 3. Get marker image size for Retina or SVG display */
								 
								$marker_img_size = $this->cspm_get_image_size(
									array(
										'path' => $this->cspm_get_image_path_from_url($marker_img_by_cat),											
										'default_width' => $marker_img_and_size['size']['width'],
										'default_height' => $marker_img_and_size['size']['height'],				
									)
								);
												
							}
							
							/**
							 * [@pin_options] Contains all infos about the marker/post ...
							 * ... that will be sent to the JS function "cspm_new_pin_object()" ...
							 * ... to add markers/pins to the map.
							 * @since 3.5 */
							 
							$pin_options = array(
								'post_id' => $post_id,
								'post_categories' => $implode_post_categories,
								'coordinates' => array(
									'lat' => $lat,
									'lng' => $lng,
									'address' => $address, //@since 5.4
								),									
								'icon' => array(
									'url' => $marker_img_by_cat,
									'size' => $marker_img_size,
								),
								'is_child' => $is_child,
								'media' => $media,
							);
							
							?>
						
							/**
							 * Create the pin object.
							 * Validate latLng | @since 3.6 */
													 
							var marker_object = cspm_new_pin_object(map_id, <?php echo wp_json_encode($pin_options); ?>);
							if(marker_object){ 
								json_markers_data.push(marker_object);
							}else false_latLngs.push('<?php echo $post_id; ?>'); <?php 
								
							/**
							 * Create polylines/polygons
							 * @since 2.7 */
							 
							/**
							 * Check for Polylines built from post IDs and convert the IDs to LatLng coordinates.
							 * @since 2.7 */
							  
							if(is_array($polylines) && array_key_exists('ids', $polylines)){
							
								/**
								 * Loop through all Polylines built from post IDs */
								 
								foreach($polylines['ids'] as $single_polyline_id => $single_polyline_data){
									
									if(!empty($single_polyline_data['path'])){											
										
										/**
										 * Loop through Polyline post IDs and convert them to LatLng coordinates */
										 
										foreach($single_polyline_data['path'] as $path_post_id){
											
											/**
											 * If the post ID exists on the map, Save the post coordinates */
											 
											if($path_post_id == $post_id)
												$polylines_of_post_ids[$single_polyline_id][$path_post_id] = $lat.','.$lng;
											
										}
											
									}
									
								}
								
							}											
							
							/**
							 * Check for Polygons built from post IDs and convert the IDs to LatLng coordinates.
							 * @since 2.7 */
							 
							if(is_array($polygons) && array_key_exists('ids', $polygons)){
							
								/**
								 * Loop through all Polygons built from post IDs */
								 
								foreach($polygons['ids'] as $single_polygon_id => $single_polygon_data){
									
									if(!empty($single_polygon_data['path'])){											
										
										/**
										 * Loop through Polygon post IDs and convert them to LatLng coordinates */
										 
										foreach($single_polygon_data['path'] as $path_post_id){

											/**
											 * If the post ID exists on the map, Save the post coordinates */
											 
											if($path_post_id == $post_id)
												$polygons_of_post_ids[$single_polygon_id][$path_post_id] = $lat.','.$lng;
											
										}
											
									}
									
								}
								
							}											
							
							/**
							 * Check for Map Holes built from post IDs and convert the IDs to LatLng coordinates.
							 * @since 5.3 */
							 
							if(is_array($map_holes) && array_key_exists('ids', $map_holes)){
							
								/**
								 * Loop through all holes built from post IDs */
								 
								foreach($map_holes['ids'] as $single_hole_id => $single_hole_data){
									
									if(is_array($single_hole_data['path']) && count($single_hole_data['path']) >= 3){											
										
										/**
										 * Loop through holes post IDs and convert them to LatLng coordinates */
										 
										foreach($single_hole_data['path'] as $path_post_id){
							
											/**
											 * If the post ID exists on the map, Save the post coordinates */
											 
											if($path_post_id == $post_id)
												$map_holes_of_post_ids[$single_hole_id][$path_post_id] = $lat.','.$lng;
							
										}
											
									}
									
								}
								
							}																					
											
							/**
							 * Proceed for the Child Markers */
							 
							$nbr_pins++;

							if(count($child_markers) > 0 && esc_attr($show_secondary) == 'yes'){
								
								for($c=0; $c<count($child_markers); $c++){
									
									$post_id = isset($child_markers[$c]['post_id']) ? $child_markers[$c]['post_id'] : '';
									$lat = isset($child_markers[$c]['lat']) ? $child_markers[$c]['lat'] : '';
									$lng = isset($child_markers[$c]['lng']) ? $child_markers[$c]['lng'] : '';						
									$is_child = isset($child_markers[$c]['is_child']) ? $child_markers[$c]['is_child'] : '';									
						
									if(!empty($post_id) && !empty($lat) && !empty($lng)){ 
							
										/**
										 * Update the main marker [@pin_options].
										 * Secondary markers will share everything with the main marker ...
										 * ... except for the coordinates & post ID.
										 * @since 3.5 */
										 
										$pin_options['post_id'] = $post_id.'-'.$c;
										$pin_options['coordinates']['lat'] = $lat;
										$pin_options['coordinates']['lng'] = $lng;
										$pin_options['is_child'] = $is_child;
										
										?>

										/**
										 * Create the child pin object */
													 
										var marker_object = cspm_new_pin_object(map_id, <?php echo wp_json_encode($pin_options); ?>);
										if(marker_object){
											json_markers_data.push(marker_object);
										}else false_latLngs.push('<?php echo $post_id; ?>'); <?php
															
										$nbr_pins++;	
																		
									}									
						
								}
								
							}
							 
						}
						
						$m++;

					}
					
				}
				
				$show_infoboxes = $this->show_infobox;											
				$move_carousel_on_infobox_hover = (in_array('infobox_hover', $this->move_carousel_on)) ? 'true' : 'false';

				?>

				var show_infobox = '<?php echo $show_infoboxes; ?>';
				var infobox_type = '<?php echo esc_attr($infobox_type); ?>';
				var infobox_display_event = '<?php echo $this->infobox_display_event; ?>';
				var useragent = navigator.userAgent;
				var infobox_loaded = false;
				var clustering_method = false;
				var remove_infobox_on_mouseout = '<?php echo $this->remove_infobox_on_mouseout; ?>';
				var use_marker_popups = '<?php echo $this->use_marker_popups; ?>'; //@since 4.0
				var marker_popups_loaded = false; //@since 4.0
										
				/**
				 * [@polyline_values] - will store an Object of all available Polylines	
				 * [@polygon_values] - will store an Object of all available Polygons
				 * @since 2.7 */
				 
				var polyline_values = [];
				var polygon_values = [];
				
				/**
				 * [@kml_values] - will store an Object of all available KML layers	
				 * @since 3.0 */
				
				var kml_values = [];
				
				/**
				 * [@ground_overlays_values] - will store an Object of all available Ground overlays (Images)
				 * @since 3.5 */
				
				var ground_overlays_values = [];
				
				/**
				 * [@markers_on_viewport] Contains the IDs of all markers available on the current viewport
				 * @since 5.0 */
				 
				var markers_on_viewport = [];
				
				/**
				 * [@viewport_diff_ids] Contains the difference between IDs available in the previous viewport and the current viewport
				 * @since 5.0 */
				 				
				var viewport_diff_ids = [];				 
				
				<?php
				
				/**
				 * Build all Polyline JS objects
				 * @since 2.7
				 * @updated 3.0 [Added clickable, URL & Description options]
				 */
				 
				if(is_array($polylines) && $this->draw_polyline == 'true'){ //@since 5.6.1
					
					/**
					 * Loop through all Polylines */
					 
					foreach($polylines as $polylines_type => $polylines_type_data){
						
						/**
						 * Loop through each polyline by its type ("latLng" or "post IDs")
						 * @polyline_id = "latlng" or "post_ids" */
						 
						foreach($polylines_type_data as $polyline_id => $polyline_data){

							
							/**
							 * Build Polyline JS Object */
							
							$polyline_visible = !empty($polyline_data['visible']) ? esc_attr($polyline_data['visible']) : '';									
							$polyline_connected_post = !empty($polyline_data['connected_post']) ? esc_attr($polyline_data['connected_post']) : ''; //@since 4.6
							
							if($polyline_visible == 'true' && (empty($polyline_connected_post) || (!empty($polyline_connected_post) && in_array($polyline_connected_post, $post_ids))) ){
								
								$polyline_id = !empty($polyline_data['polyline_id']) ? esc_attr($polyline_data['polyline_id']) : time(); //@since 5.6
								$polyline_clickable = !empty($polyline_data['clickable']) ? esc_attr($polyline_data['clickable']) : 'false'; //@since 3.0
								$polyline_url = !empty($polyline_data['url']) ? esc_url($polyline_data['url']) : ''; //@since 3.0
								$polyline_url_target = !empty($polyline_data['url_target']) ? esc_attr($polyline_data['url_target']) : 'new_window'; //@since 3.0
								$polyline_description = !empty($polyline_data['description']) ? wp_json_encode($polyline_data['description']) : ''; //@since 3.0 | @edited 5.0
								$polyline_infowindow_maxwidth = !empty($polyline_data['infowindow_maxwidth']) ? esc_attr($polyline_data['infowindow_maxwidth']) : '250'; //@since 3.0
								$polyline_geodesic = !empty($polyline_data['geodesic']) ? esc_attr($polyline_data['geodesic']) : 'false';
								$polyline_color = !empty($polyline_data['color']) ? esc_attr($polyline_data['color']) : '#189AC9';
								$polyline_opacity = !empty($polyline_data['opacity']) ? esc_attr($polyline_data['opacity']) : '1';
								$polyline_weight = !empty($polyline_data['weight']) ? esc_attr($polyline_data['weight']) : '2';
								$polyline_zindex = !empty($polyline_data['zindex']) ? esc_attr($polyline_data['zindex']) : '1';
								$polyline_strokeType = !empty($polyline_data['stroke_type']) ? esc_attr($polyline_data['stroke_type']) : 'simple'; //@since 5.6
								$polyline_icons_repeat = !empty($polyline_data['icons_repeat']) ? esc_attr($polyline_data['icons_repeat']) : '15'; //@since 5.6
								
								?>							

								var polyline_path = []; <?php
								
								$polyline_path = !empty($polyline_data['path']) ? $polyline_data['path'] : array();
								
								/**
								 * Post IDs Polylines */
								 
								if($polylines_type == 'ids'){
								
									foreach($polyline_path as $path_post_id){ 
										
										/**
										 * @polylines_of_post_ids - Already created inside marker objects loop */
										 
										if(isset($polylines_of_post_ids[$polyline_id][$path_post_id])){
										
											$post_id_latlng = $polylines_of_post_ids[$polyline_id][$path_post_id]; ?>
											
											polyline_path.push([<?php echo $post_id_latlng; ?>]); <?php
											
										}
										
									
									} 
								
								/**
								* LatLng coordinates Polylines */
								
								}elseif($polylines_type == 'latlng'){
									
									foreach($polyline_path as $latlng){ 
										
										if(count(explode(',', $latlng)) == 2){ ?>
											
											polyline_path.push([<?php echo $latlng; ?>]); <?php
											
										}
										
									}
									
								}
									
								$polyline_tag = !empty($polyline_connected_post) ? 'polylineConnectedPost_'.esc_attr($polyline_connected_post) : ''; //@since 4.6
									
								?>
								
								if(polyline_path.length > 0){
									
									var dashed_polyline_icons = {}; //@since 5.6
									
									<?php 
									
									/**
									 * Dashed polyline icons
									 * @since 5.6 */
									 
									if($polyline_strokeType == 'dashed'){ ?>
									
										dashed_polyline_icons = {
											icons: [{
												icon: {														
													path: 'M 0,-1 0,1',
													scale: 2,
													strokeColor: '<?php echo esc_attr($polyline_color); ?>',
													strokeOpacity: <?php echo esc_attr($polyline_opacity); ?>,														
													strokeWeight: <?php echo esc_attr($polyline_weight); ?>,
												},
												offset: '0',
												repeat: '<?php echo $polyline_icons_repeat; ?>px'
											}]
										};
									
									<?php } ?>
									
									var polyline_data = {										
										tag: '<?php echo $polyline_tag; ?>', //@since 4.6
										options:
											$.extend({}, dashed_polyline_icons, {
												path: polyline_path,											
												clickable: <?php if($polyline_clickable == 'false'){ ?> false <?php }else{ ?> true <?php } ?>,
												editable: false,
												strokeColor: '<?php echo esc_attr($polyline_color); ?>',
												strokeOpacity: <?php echo ($polyline_strokeType == 'dashed') ? 0 : esc_attr($polyline_opacity); ?>, //@edited 5.6
												strokeWeight: <?php echo esc_attr($polyline_weight); ?>,
												geodesic: <?php if($polyline_geodesic == 'false'){ ?> false <?php }else{ ?> true <?php } ?>,
												zIndex: parseInt(<?php echo esc_attr($polyline_zindex); ?>), //@edited 5.6
												visible: <?php if($polyline_visible == 'false'){ ?> false <?php }else{ ?> true <?php } ?>,									
												connectedPost: '<?php echo esc_attr($polyline_connected_post); ?>', //@since 4.6
											}) //@edited 5.6
										,
										events: {
											click: function(polyline){
												var clickable = polyline.clickable;
												var urlTarget = "<?php echo $polyline_url_target; ?>";
												var url = "<?php echo $polyline_url; ?>"; //@since 5.4
												if(clickable && url != '#' && url != ''){ //@edited 5.4
													if(urlTarget == 'new_window'){
														window.open(url); //@edited 5.4
                                                    }else if(urlTarget == 'popup'){
                                                        cspm_open_single_post_modal(url); //@since 5.6.3
													}else window.location = url; //@edited 5.4
												}
											},
											mouseover: function(polyline, event, context){
												
												<?php 
												
												/**
												 * Inform the user that they can open a link by clicking on the polyline
												 * @since 3.5 */
													
												if($polyline_clickable == 'true' && !empty($polyline_url) && $polyline_url != '#'){ //@edited 5.4 ?>	
													
													var message = progress_map_vars.polyline_url_msg;
													cspm_popup_msg(map_id, 'info', '',  message); <?php 
													
												} 
												
												/**
												 * Show polyline description */
																								
												if(!empty($polyline_description)){ ?>
												
													var map = plugin_map.gmap3("get");
													plugin_map.gmap3({
														get:{
															name: "infowindow",
															id: "<?php echo $polyline_id; ?>",
															callback: function(infowindow){
																if(!infowindow){
																	plugin_map.gmap3({
																		infowindow:{
																			latLng: event.latLng,
																			id: "<?php echo $polyline_id; ?>",
																			options:{
																				content: '<?php echo trim(wp_specialchars_decode($polyline_description, ENT_COMPAT), '"'); // @edited 5.0 ?>',
																				maxWidth: <?php echo $polyline_infowindow_maxwidth; ?>,
																				zIndex: 9,
																			}
																		}
																	});
																}else{
																	infowindow.open(map, polyline);	
																}
															}
														},
													}); <?php 
													
												} 
												
												?>
											},
											mouseout: function(polyline, event, context){
																								
												<?php 
												
												/**
												 * Close polyline info
												 * @since 3.5 */
													
												if($polyline_clickable == 'true' && !empty($polyline_url)){ ?>	
													
													cspm_close_popup_msg(map_id, 'info'); <?php 
													
												} 
												
												/**
												 * Close polyline description */
																								
												if(!empty($polyline_description)){ ?>
													
													plugin_map.gmap3({
														get:{
															name: "infowindow",
															id: "<?php echo $polyline_id; ?>",
															callback: function(infowindow){
																if(infowindow){
																  infowindow.close();
																}
															}
														}
													}); <?php 
												
												} 
												
												?>
											},																						
										}										
									};
									
									polyline_values.push(polyline_data);
								
								}
								
								<?php		
								
							}
							
						}
						
					}
				
				}
				
				/**
				 * Build all Polygon JS objects
				 * @since 2.7
				 * @updated 3.0 [Added clickable, URL & Description options]
				 */
				 
				if(is_array($polygons) && $this->draw_polygon == 'true'){ //@since 5.6.1
										
					/**
					 * Overwrite [@polyline_values] when polylines are deactivated. 
					 * This will prevent displaying polylines with dashed polygons line when polylines are deactivated!
					 *  @since 5.6 */
					if($this->draw_polyline != 'true'){ ?>
						var polyline_values = []; <?php 
					}
					
					$dashed_polygons = 0; //@since 5.6
						
					/**
					 * Loop through all Polygons */
					 
					foreach($polygons as $polygons_type => $polygons_type_data){
						
						/**
						 * Loop through each polygon by its type ("latLng" or "post IDs")
						 * @polygon_id = "latlng" or "post_ids" */
						 
						foreach($polygons_type_data as $polygon_id => $polygon_data){

							/**
							 * Build Polygon JS Object */
							$polygon_visible = !empty($polygon_data['visible']) ? esc_attr($polygon_data['visible']) : '';
							$polygon_connected_post = !empty($polygon_data['connected_post']) ? esc_attr($polygon_data['connected_post']) : ''; //@since 4.6 
							
							if($polygon_visible == 'true' && (empty($polygon_connected_post) || (!empty($polygon_connected_post) && in_array($polygon_connected_post, $post_ids))) ){
								
								$polygon_id = !empty($polygon_data['polygon_id']) ? esc_attr($polygon_data['polygon_id']) : time(); //@since 5.6
								$polygon_clickable = !empty($polygon_data['clickable']) ? esc_attr($polygon_data['clickable']) : 'false'; //@since 3.0
								$polygon_url = !empty($polygon_data['url']) ? esc_url($polygon_data['url']) : ''; //@since 3.0
								$polygon_url_target = !empty($polygon_data['url_target']) ? esc_attr($polygon_data['url_target']) : 'new_window'; //@since 3.0
								$polygon_description = !empty($polygon_data['description']) ? wp_json_encode($polygon_data['description']) : ''; //@since 3.0 | @edited 5.0
								$polygon_infowindow_maxwidth = !empty($polygon_data['infowindow_maxwidth']) ? esc_attr($polygon_data['infowindow_maxwidth']) : '250'; //@since 3.0
								$polygon_fill_color = !empty($polygon_data['fill_color']) ? esc_attr($polygon_data['fill_color']) : '#189AC9';
								$polygon_fill_opacity = !empty($polygon_data['fill_opacity']) ? esc_attr($polygon_data['fill_opacity']) : '1';									
								$polygon_geodesic = !empty($polygon_data['geodesic']) ? esc_attr($polygon_data['geodesic']) : 'false';
								$polygon_stroke_color = !empty($polygon_data['stroke_color']) ? esc_attr($polygon_data['stroke_color']) : '#189AC9';
								$polygon_stroke_opacity = !empty($polygon_data['stroke_opacity']) ? esc_attr($polygon_data['stroke_opacity']) : '1';
								$polygon_stroke_weight = !empty($polygon_data['stroke_weight']) ? esc_attr($polygon_data['stroke_weight']) : '2';
								$polygon_stroke_position = !empty($polygon_data['stroke_position']) ? esc_attr($polygon_data['stroke_position']) : 'CENTER';
								$polygon_stroke_type = !empty($polygon_data['stroke_type']) ? esc_attr($polygon_data['stroke_type']) : 'simple'; //@since 5.6
								$polygon_icons_repeat = !empty($polygon_data['icons_repeat']) ? esc_attr($polygon_data['icons_repeat']) : '15'; //@since 5.6
								$polygon_zindex = !empty($polygon_data['zindex']) ? esc_attr($polygon_data['zindex']) : '1';
								$polygon_hover_style = !empty($polygon_data['polygon_hover_style']) ? esc_attr($polygon_data['polygon_hover_style']) : 'false'; //@since 5.6								
								$polygon_hover_fill_color = !empty($polygon_data['fill_hover_color']) ? esc_attr($polygon_data['fill_hover_color']) : '#189AC9'; //@since 5.6
								$polygon_hover_fill_opacity = !empty($polygon_data['fill_hover_opacity']) ? esc_attr($polygon_data['fill_hover_opacity']) : '1'; //@since 5.6									
								$polygon_hover_stroke_color = !empty($polygon_data['stroke_hover_color']) ? esc_attr($polygon_data['stroke_hover_color']) : '#189AC9'; //@since 5.6
								$polygon_hover_stroke_opacity = !empty($polygon_data['stroke_hover_opacity']) ? esc_attr($polygon_data['stroke_hover_opacity']) : '1'; //@since 5.6
								$polygon_hover_stroke_weight = !empty($polygon_data['stroke_hover_weight']) ? esc_attr($polygon_data['stroke_hover_weight']) : '2'; //@since 5.6
								$polygon_hover_stroke_position = !empty($polygon_data['stroke_hover_position']) ? esc_attr($polygon_data['stroke_hover_position']) : 'CENTER'; //@since 5.6
								$polygon_hover_stroke_type = !empty($polygon_data['stroke_hover_type']) ? esc_attr($polygon_data['stroke_hover_type']) : 'simple'; //@since 5.6
								$polygon_hover_icons_repeat = !empty($polygon_data['hover_icons_repeat']) ? esc_attr($polygon_data['hover_icons_repeat']) : '15'; //@since 5.6
								$polygon_fitBounds = !empty($polygon_data['polygon_fitBounds']) ? esc_attr($polygon_data['polygon_fitBounds']) : 'no'; //@since 5.6
								
								?>
								 
								var polygon_path = []; <?php
								
								$polygon_path = !empty($polygon_data['path']) ? $polygon_data['path'] : array();

								/**
								 * Post IDs polygons */
								 
								if($polygons_type == 'ids'){
								
									foreach($polygon_path as $path_post_id){ 
										
										/**
										 * @polygons_of_post_ids - Already created inside marker objects loop */

										if(isset($polygons_of_post_ids[$polygon_id][$path_post_id])){
										
											$post_id_latlng = $polygons_of_post_ids[$polygon_id][$path_post_id]; ?>
											
											polygon_path.push([<?php echo $post_id_latlng; ?>]); <?php
											
										}
										
									
									} 
								
								/**
								* LatLng coordinates polygons */
								
								}elseif($polygons_type == 'latlng'){
									
									foreach($polygon_path as $latlng){ 
										
										if(count(explode(',', $latlng)) == 2){ ?>
											
											polygon_path.push([<?php echo $latlng; ?>]); <?php
											
										}
										
									}
									
								}
									
								$polygon_tag = !empty($polygon_connected_post) ? 'polygonConnectedPost_'.esc_attr($polygon_connected_post) : ''; //@since 4.6
									
								?>
								
								if(polygon_path.length > 0){

									var polygon_bounds = cspm_get_latLngs_bounds(polygon_path);
									var polygon_center = polygon_bounds.getCenter();
									
									<?php 
									
									/**
									 * Draw dashed polyline around the polygon
									 * @since 5.6 */
									 
									if(in_array('dashed', array($polygon_stroke_type, $polygon_hover_stroke_type))){ 
									
										$dashed_polygons++ ; ?>
										
										var dashed_polyline_path = polygon_path;				
										var first_vertex = dashed_polyline_path[0];
										dashed_polyline_path.push(first_vertex);
										
										var dashed_polyline_tag = 'dashed_poylgon_stroke_<?php echo $polygon_id; ?>';
										
										var dashed_polyline_data = {								
											tag: dashed_polyline_tag,		
											options:{												
												path: dashed_polyline_path,
												icons: [{
													icon: {														
														path: 'M 0,-1 0,1',
														scale: 2,
														strokeColor: '<?php echo esc_attr($polygon_stroke_color); ?>',
														strokeOpacity: <?php echo esc_attr($polygon_stroke_opacity); ?>,														
														strokeWeight: <?php echo esc_attr($polygon_stroke_weight); ?>,
													},
													offset: '0',
													repeat: '<?php echo $polygon_icons_repeat; ?>px'
												}],
												clickable: false,
												editable: false,
												strokeOpacity: 0,
												geodesic: <?php if($polygon_geodesic == 'false'){ ?> false <?php }else{ ?> true <?php } ?>,
												zIndex: parseInt(<?php echo esc_attr($polygon_zindex)+1; ?>), //@edited 5.6
												visible: <?php if($polygon_visible == 'false' || $polygon_stroke_type != 'dashed'){ ?> false <?php }else{ ?> true <?php } ?>,									
											},
										};
						
										polyline_values.push(dashed_polyline_data);									
										
									<?php } ?>
										
									var polygon_data = {										
										tag: '<?php echo $polygon_tag; ?>', //@since 4.6
										options:{
											id: '<?php echo $polygon_id; ?>', //@since 5.6
											paths: polygon_path,
											clickable: <?php if($polygon_clickable == 'false'){ ?> false <?php }else{ ?> true <?php } ?>,
											geodesic: <?php if($polygon_geodesic == 'false'){ ?> false <?php }else{ ?> true <?php } ?>,
											zIndex: parseInt(<?php echo esc_attr($polygon_zindex); ?>), //@edited 5.6
											visible: <?php if($polygon_visible == 'false'){ ?> false <?php }else{ ?> true <?php } ?>,
											editable: false,
											strokeColor: '<?php echo esc_attr($polygon_stroke_color); ?>',
											strokeOpacity: <?php  echo ($polygon_stroke_type == 'dashed') ? 0 : esc_attr($polygon_stroke_opacity); ?>, //@edited 5.6
											strokeWeight: <?php echo esc_attr($polygon_stroke_weight); ?>,
											fillColor: '<?php echo esc_attr($polygon_fill_color); ?>',
											fillOpacity: <?php echo esc_attr($polygon_fill_opacity); ?>,
											strokePosition: google.maps.StrokePosition['<?php echo esc_attr($polygon_stroke_position); ?>'],
											polygonBounds: polygon_bounds,
											polygonCenter: polygon_center,
											connectedPost: '<?php echo esc_attr($polygon_connected_post); ?>', //@since 4.6
										},										
										events: {
											click: function(polygon){
												
												var clickable = polygon.clickable;
												var urlTarget = "<?php echo $polygon_url_target; ?>";
												var url = "<?php echo $polygon_url; ?>"; //@since 5.4
												if(clickable && url != '#' && url != ''){ //@edited 5.4
													if(urlTarget == 'new_window'){
														window.open(url); //@edited 5.4
                                                    }else if(urlTarget == 'popup'){
                                                        cspm_open_single_post_modal(url); //@since 5.6.3
													}else  window.location = url; //@edited 5.4
												}
												
												<?php 
												
												/**
												 * Set the map viewport to contain the polygon bounds when clicking inside the of it.
												 * @since 5.6 */
												 
												if($polygon_fitBounds == 'yes'){ ?>
													plugin_map.gmap3('get').fitBounds(polygon.polygonBounds);
												<?php } ?>
												
											},
											mouseover: function(polygon, event, context){
												
												var polygon_id = polygon.id; //@since 5.6
												
												<?php
												
												/**
												 * Change the polygon style on mouse enter/hover
												 * @since 5.6 */
												
												if($polygon_hover_style == 'true'){ 
													
													/**
													 * Case 1: Apply dashed stroke on Polygon mouse enter */
													 
													if($polygon_hover_stroke_type == 'dashed'){ ?>									
														
														/**
														 * 1. Hide the polygon stroke & apply the polygon fill style */
														 
														polygon.setOptions({
															strokeOpacity: 0,
															fillColor: '<?php echo esc_attr($polygon_hover_fill_color); ?>',
															fillOpacity: <?php echo esc_attr($polygon_hover_fill_opacity); ?>,															
														});
														
														/**
														 * 2. Show the dashed poyline around the polygon */
														 
														plugin_map.gmap3({
														  get: {
															name: 'polyline',
															tag: 'dashed_poylgon_stroke_'+polygon_id,
															first: true,
															callback: function(dashed_polyline){
																
																if(typeof dashed_polyline === 'undefined')
																	return;
																	
																dashed_polyline.setOptions({
																	icons: [{
																		icon: {
																			path: 'M 0,-1 0,1',
																			scale: 2,																			
																			strokeColor: '<?php echo esc_attr($polygon_hover_stroke_color); ?>',
																			strokeOpacity: <?php echo esc_attr($polygon_hover_stroke_opacity); ?>,																			
																			strokeWeight: <?php echo esc_attr($polygon_hover_stroke_weight); ?>,
																		},
																		offset: '0',
																		repeat: '<?php echo $polygon_hover_icons_repeat; ?>px'
																	}],
																	strokeOpacity: 0,																	
																	visible: true,
																});
																
															}
														  }
														});
													
													<?php 
													
													/**
													 * Case 2: Apply simple stroke on Polygon mouse enter */
													 													
													}else{ ?>
														
														<?php 
														
														/**
														 * 1. Hide the dashed polyline around the polygon */
														 
														if($polygon_stroke_type == 'dashed'){ ?>
														
															plugin_map.gmap3({
															  get: {
																name: 'polyline',
																tag: 'dashed_poylgon_stroke_'+polygon_id,
																first: true,
																callback: function(dashed_polyline){																	
																	if(typeof dashed_polyline === 'undefined')
																		return;																																		
																	dashed_polyline.setVisible(false);																	
																}
															  }
															});
														
														<?php } ?>
														
														/**
														 * 2. Apply the polygon hover style */
														 									
														polygon.setOptions({
															strokeColor: '<?php echo esc_attr($polygon_hover_stroke_color); ?>',
															strokeOpacity: <?php echo esc_attr($polygon_hover_stroke_opacity); ?>,
															strokeWeight: <?php echo esc_attr($polygon_hover_stroke_weight); ?>,
															fillColor: '<?php echo esc_attr($polygon_hover_fill_color); ?>',
															fillOpacity: <?php echo esc_attr($polygon_hover_fill_opacity); ?>,
															strokePosition: google.maps.StrokePosition['<?php echo esc_attr($polygon_hover_stroke_position); ?>'],
														}); <?php 
														
													}
													
												}
												
												?>
												
												<?php 
												
												/**
												 * Inform the user that they can open a link by clicking on the polygon
												 * @since 3.5 */
													
												if($polygon_clickable == 'true' && !empty($polygon_url) && $polygon_url != '#'){ //@edited 5.4 ?>	
													
													var message = progress_map_vars.polygon_url_msg;
													cspm_popup_msg(map_id, 'info', '',  message); <?php 
													
												} 
												
												/**
												 * Show polygon description */
																																				
												if(!empty($polygon_description)){ ?>
												
													var polygon_center = polygon.polygonCenter;
													var map = plugin_map.gmap3("get");
													plugin_map.gmap3({
														get:{
															name: "infowindow",
															id: "<?php echo $polygon_id; ?>",
															callback: function(infowindow){
																if(!infowindow){
																	plugin_map.gmap3({
																		infowindow:{
																			latLng: polygon_center,
																			id: "<?php echo $polygon_id; ?>",
																			options:{
																				content: '<?php echo trim(wp_specialchars_decode($polygon_description, ENT_COMPAT), '"'); // @edited 5.0 ?>',
																				maxWidth: <?php echo $polygon_infowindow_maxwidth; ?>,
																				zIndex: 9,
																			}
																		}
																	});
																}else{
																	infowindow.open(map, polygon);	
																}
															}
														},
													}); <?php 
												
												} 
												
												?>
											},
											mouseout: function(polygon, event, context){
												
												var polygon_id = polygon.id; //@since 5.6
												
												<?php
												
												/**
												 * Change the polygon style back to default on mouse out
												 * @since 5.6 */
												
												if($polygon_hover_style == 'true'){ 
													
													/**
													 * Case 1: Apply dashed stroke on Polygon mouse out */
													 
													if($polygon_stroke_type == 'dashed'){ ?>									
														
														/**
														 * 1. Hide the polygon stroke & apply the default polygon fill style */
														 
														polygon.setOptions({
															strokeOpacity: 0,
															fillColor: '<?php echo esc_attr($polygon_fill_color); ?>',
															fillOpacity: <?php echo esc_attr($polygon_fill_opacity); ?>,
														});
														
														/**
														 * 2. Show a dashed polyline around the polygon */
														 
														plugin_map.gmap3({
														  get: {
															name: 'polyline',
															tag: 'dashed_poylgon_stroke_'+polygon_id,
															first: true,
															callback: function(dashed_polyline){
																
																if(typeof dashed_polyline === 'undefined')
																	return;
																																	
																dashed_polyline.setOptions({
																	icons: [{
																		icon: {
																			strokeColor: '<?php echo esc_attr($polygon_stroke_color); ?>',
																			path: 'M 0,-1 0,1',
																			strokeOpacity: <?php echo esc_attr($polygon_stroke_opacity); ?>,
																			scale: 2,
																			strokeWeight: <?php echo esc_attr($polygon_stroke_weight); ?>,
																		},
																		offset: '0',
																		repeat: '<?php echo $polygon_icons_repeat; ?>px'
																	}],
																	strokeOpacity: 0,
																	visible: true,
																});
																
															}
														  }
														});
													
													<?php 
													
													/**
													 * Case 2: Apply simple stroke on Polygon mouse out */
													 																										
													}else{ ?>
														
														<?php 
														
														/**
														 * 1. Hide the dashed polyline around the polygon */
														 
														if($polygon_hover_stroke_type == 'dashed'){ ?>
														
															plugin_map.gmap3({
															  get: {
																name: 'polyline',
																tag: 'dashed_poylgon_stroke_'+polygon_id,
																first: true,
																callback: function(dashed_polyline){
																	if(typeof dashed_polyline === 'undefined')
																		return;
																	dashed_polyline.setVisible(false);
																}
															  }
															}); 
															
														<?php } ?>
														
														/**
														 * 1. Apply the default polygon style */
														 

														polygon.setOptions({
															strokeColor: '<?php echo esc_attr($polygon_stroke_color); ?>',
															strokeOpacity: <?php echo esc_attr($polygon_stroke_opacity); ?>,
															strokeWeight: <?php echo esc_attr($polygon_stroke_weight); ?>,
															fillColor: '<?php echo esc_attr($polygon_fill_color); ?>',
															fillOpacity: <?php echo esc_attr($polygon_fill_opacity); ?>,
															strokePosition: google.maps.StrokePosition['<?php echo esc_attr($polygon_stroke_position); ?>'],
														}); <?php 
														
													}
													
												}
												
												?>
												
												<?php 
												
												/**
												 * Close polygon info
												 * @since 3.5 */
													
												if($polygon_clickable == 'true' && !empty($polygon_url)){ ?>	
													cspm_close_popup_msg(map_id, 'info'); <?php 
												} 
												
												/**
												 * Close polygon description */
																																				
												if(!empty($polygon_description)){ ?>
												
													plugin_map.gmap3({
														get:{
															name: "infowindow",
															id: "<?php echo $polygon_id; ?>",
															callback: function(infowindow){
																if(infowindow){
																  infowindow.close();
																}
															}
														}
													}); <?php 
												
												} 
												
												?>
											},
										}									
									};
	
									polygon_values.push(polygon_data);
									
								}
								
								<?php	
								
							}
							
						}
						
					}
				
				}
				
				/**
				 * Map Holes
				 * @since 5.3
				 */
				 
				if($this->map_holes_option == 'true' && is_array($map_holes)){
					
					$map_holes_style = $this->map_holes_style[0];
					
					$map_fillColor = $this->cspm_setting_exists('map_fillColor', $map_holes_style, '#000');
					$map_fillOpacity = $this->cspm_setting_exists('map_fillOpacity', $map_holes_style, '0.5');
					$holes_strokeColor = $this->cspm_setting_exists('holes_strokeColor', $map_holes_style, '#000');
					$holes_strokeOpacity = $this->cspm_setting_exists('holes_strokeOpacity', $map_holes_style, '1');
					$holes_strokeWeight = $this->cspm_setting_exists('holes_strokeWeight', $map_holes_style, '1');
					$holes_strokePosition = $this->cspm_setting_exists('holes_strokePosition', $map_holes_style, 'CENTER'); ?>
					
					var map_holes_paths = [];					
					
					<?php
													
					/**
					 * Loop through all Map Hole types */
					 
					foreach($map_holes as $hole_type => $hole_type_data){						
						
						/**
						 * Loop through each hole by its type ("latLng" or "post IDs")
						 * @hole_id = "latlng" or "post_ids" */
						 
						foreach($hole_type_data as $hole_id => $hole_data){ ?>
						
							var single_map_hole = [];
							
							<?php
						
							/**
							 * Build Holes JS Object */
							 
							$hole_visible = !empty($hole_data['visible']) ? esc_attr($hole_data['visible']) : '';
							
							if($hole_visible == 'true'){
								
								$hole_path = is_array($hole_data['path']) ? $hole_data['path'] : array();

								if(is_array($hole_path) && count($hole_path) >= 3){

									/**
									 * Post IDs holes */
									 
									if($hole_type == 'ids'){ 
																			
										/**
										 * @map_holes_of_post_ids - Already created inside marker objects loop */
										 
										if(is_array($map_holes_of_post_ids) && isset($map_holes_of_post_ids[$hole_id])){ 
											$first_vertex = array_shift($map_holes_of_post_ids[$hole_id]); ?>
											single_map_hole.push(<?php echo '['.$first_vertex.'],['.implode('],[', $map_holes_of_post_ids[$hole_id]).'],['.$first_vertex.']'; ?>); <?php
										}
									
									/**
									* LatLng coordinates holes */
									
									}elseif($hole_type == 'latlng'){ 
										$first_vertex = array_shift($hole_path); ?>
										single_map_hole.push(<?php echo '['.$first_vertex.'],['.implode('],[', $hole_path).'],['.$first_vertex.']'; ?>); <?php
									}
									
									?>
									
									if(single_map_hole.length > 0){
										map_holes_paths.push(single_map_hole);
									}
									
									<?php
								}
							
							}
							
						}
						
					}
									
					?>
					
					if(cspm_object_size(map_holes_paths) > 0){

						var clockwise_outer_path = [[-87,0],[-87,-87],[-87,120]];
						var counter_clockwise_outer_path = [[-87,120],[-87,-87],[-87,0]];
						
						/** 
						 * Convert all holes orientation to counter-clockwise ...
						 * ... by simply reversing hole coordinates order */
						 
						jQuery.each(map_holes_paths, function(index, path_vertices){
							if(cspm_is_clockwise(path_vertices)){
								path_vertices.reverse(); // Counter-clockwise!
							}
						});
						
						map_holes_paths.unshift(clockwise_outer_path); // Add the outer path to the beginning of the paths
						
						var map_holes_values = {
							options:{
								paths: map_holes_paths,
								clickable: true,
								geodesic: false,
								visible: true,
								editable: false,
								zIndex: parseInt(<?php echo apply_filters('cspm_holes_mask_zindex', PHP_INT_MAX); ?>),
								fillColor: '<?php echo $map_fillColor; ?>',
								fillOpacity: <?php echo str_replace(',', '.', $map_fillOpacity); ?>,											
								strokeColor: '<?php echo $holes_strokeColor; ?>',
								strokeOpacity: <?php echo str_replace(',', '.', $holes_strokeOpacity); ?>,
								strokeWeight: <?php echo $holes_strokeWeight; ?>,
								strokePosition: google.maps.StrokePosition['<?php echo $holes_strokePosition; ?>'],
							}
						}
						
						<?php 
						/**
						 * Overwrite [@polygon_values] when polygons are deactivated. 
						 * This will prevent displaying polygons with holes when polygons are deactivated!
						 *  @since 5.4 */
						if($this->draw_polygon != 'true'){ ?>
							var polygon_values = [];
						<?php } ?>
						
						polygon_values.push(map_holes_values);
						
					}
					
					<?php	
				
				}
				
				/**
				 * Build all KML layers JS objects
				 * @since 3.0 */
				
				$count_kml_layers = 0;
				 
				if($this->use_kml == 'true' && is_array($this->kml_layers)){
					
					/**
					 * Loop through all KML layers */
					 
					foreach($this->kml_layers as $kml_data){

						/**
						 * Build KML JS Object */
						
						if(isset($kml_data['kml_url']) && !empty($kml_data['kml_url'])){
							
							$kml_url = esc_url($kml_data['kml_url']);
							
							$kml_visibility = !empty($kml_data['kml_visibility']) ? esc_attr($kml_data['kml_visibility']) : 'true';
							$kml_connected_post = !empty($kml_data['kml_connected_post']) ? esc_attr($kml_data['kml_connected_post']) : ''; //@since 4.6 
							
							if($kml_visibility == 'true' && (empty($kml_connected_post) || (!empty($kml_connected_post) && in_array($kml_connected_post, $post_ids))) ){
								
								$kml_name = !empty($kml_data['kml_label']) ? esc_attr($kml_data['kml_label']) : '';
								$kml_suppressInfoWindows = !empty($kml_data['kml_suppressInfoWindows']) ? esc_attr($kml_data['kml_suppressInfoWindows']) : 'false';
								$kml_preserveViewport = !empty($kml_data['kml_preserveViewport']) ? esc_attr($kml_data['kml_preserveViewport']) : 'false';
								$kml_screenOverlays = !empty($kml_data['kml_screenOverlays']) ? esc_attr($kml_data['kml_screenOverlays']) : 'false';
								$kml_zindex = !empty($kml_data['kml_zindex']) ? esc_attr($kml_data['kml_zindex']) : '1'; 
								
								$count_kml_layers++;
								
								$kml_tag = !empty($kml_data['kml_connected_post']) ? 'kmlConnectedPost_'.esc_attr($kml_connected_post) : ''; //@since 4.6
								
								?>
								
								var kml_data = {
									tag: [
										'<?php echo str_replace('-', '_', sanitize_title_with_dashes($kml_name)); ?>', //@since 5.6
										'<?php echo $kml_tag; ?>'
									], //@since 4.6
									options:{
										url: "<?php echo esc_attr($kml_url); ?>",
										opts:{
											suppressInfoWindows: <?php if($kml_suppressInfoWindows == 'false'){ ?> false <?php }else{ ?> true <?php } ?>,
											preserveViewport: <?php if($kml_preserveViewport == 'false'){ ?> false <?php }else{ ?> true <?php } ?>,												
											screenOverlays: <?php if($kml_screenOverlays == 'false'){ ?> false <?php }else{ ?> true <?php } ?>,
											zIndex: parseInt(<?php echo esc_attr($kml_zindex); ?>),
											kmlName: '<?php echo esc_attr($kml_name); ?>',											
											connectedPost: '<?php echo esc_attr($kml_connected_post); ?>', //@since 4.6
										},											
										events: {
											status_changed: function(kml){

												var status = kml.status;
												var kmlName = kml.kmlName;
																									
												if(status === "DOCUMENT_NOT_FOUND"){
													alert("The KML Layer "+kmlName+" could not be found. Most likely it is an invalid URL, or the document is not publicly available.");
												}else if(status === "DOCUMENT_TOO_LARGE"){
													alert("The KML Layer "+kmlName+" exceeds the file size limits of KmlLayer.");
												}else if(status === "FETCH_ERROR"){
													alert("The KML Layer "+kmlName+" could not be fetched.");
												}else if(status === "INVALID_DOCUMENT"){
													alert("The KML Layer "+kmlName+" is not a valid KML, KMZ or GeoRSS document.");
												}else if(status === "INVALID_REQUEST"){
													alert("The KML Layer "+kmlName+" is invalid.");
												}else if(status === "LIMITS_EXCEEDED"){
													alert("The KML Layer "+kmlName+" exceeds the feature limits of KmlLayer.");
												}else if(status === "TIMED_OUT"){
													alert("The KML Layer "+kmlName+" could not be loaded within a reasonable amount of time.");
												}else if(status === "UNKNOWN"){
													alert("The KML Layer "+kmlName+" failed to load for an unknown reason.");
												}
	
											}
										},
									}
								};

								kml_values.push(kml_data);
								
								<?php
								
							}
							
						}
						
					}
				
				}
									
				/**
				 * Build all Images (Ground overlays) JS objects
				 * @since 3.5
				 * @updated 5.2 [Merging "image URL" + "NE bounds" + "SW bounds" inside array] */
				
				$count_ground_overlays = 0;
				 
				if($this->ground_overlays_option == 'true' && is_array($this->ground_overlays)){
					
					/**
					 * Loop through all ground overlays */
					 
					foreach($this->ground_overlays as $ground_overlay_data){

						/**
						 * Build the ground overlay JS Object */
							
						$image_visibility = !empty($ground_overlay_data['image_visibility']) ? esc_attr($ground_overlay_data['image_visibility']) : 'true';
						$image_connected_post = !empty($ground_overlay_data['image_connected_post']) ? esc_attr($ground_overlay_data['image_connected_post']) : ''; //@since 4.6
						
						if($image_visibility != 'disable' || ($image_visibility == 'marker_connected' && !empty($image_connected_post) && in_array($image_connected_post, $post_ids))){
					
							$image_url_and_bounds = (isset($ground_overlay_data['image_url_and_bounds']) && !empty($ground_overlay_data['image_url_and_bounds'])) 
								? $ground_overlay_data['image_url_and_bounds'] 
								: array(); //@since 5.2
							
							if(is_array($image_url_and_bounds)){
								$image_url = (isset($image_url_and_bounds['image_url']) && !empty($image_url_and_bounds['image_url'])) ? esc_url($image_url_and_bounds['image_url']) : ''; //@since 5.2
								$image_ne_bounds = (isset($image_url_and_bounds['ne_bounds']) && !empty($image_url_and_bounds['ne_bounds'])) ? explode(',', esc_attr($image_url_and_bounds['ne_bounds'])) : array(); //@since 5.2
								$image_sw_bounds = (isset($image_url_and_bounds['sw_bounds']) && !empty($image_url_and_bounds['sw_bounds'])) ? explode(',', esc_attr($image_url_and_bounds['sw_bounds'])) : array(); //@since 5.2
							}else $image_url = $image_ne_bounds = $image_sw_bounds = ''; //@since 5.2
							
							$image_name = !empty($ground_overlay_data['image_label']) ? esc_attr($ground_overlay_data['image_label']) : '';								
							
							if( !empty($image_url)
								&& is_array($image_ne_bounds) && count($image_ne_bounds) == 2 
								&& is_array($image_sw_bounds) && count($image_sw_bounds) == 2
							){ //@since 5.2
								
								$image_opacity = !empty($ground_overlay_data['opacity']) ? str_replace(',', '.', esc_attr($ground_overlay_data['opacity'])) : '';
								$show_btn_icon = !empty($ground_overlay_data['show_btn_icon']) ? esc_attr($ground_overlay_data['show_btn_icon']) : $this->plugin_url.'img/switch-on.png';
								$hide_btn_icon = !empty($ground_overlay_data['hide_btn_icon']) ? esc_attr($ground_overlay_data['hide_btn_icon']) : $this->plugin_url.'img/switch-off.png';
								$icon_position = !empty($ground_overlay_data['btn_position']) ? explode(',', esc_attr($ground_overlay_data['btn_position'])) : $image_ne_bounds;
								$image_clickable = !empty($ground_overlay_data['image_clickable']) ? esc_attr($ground_overlay_data['image_clickable']) : 'false'; //@since 4.0
								$image_redirect_url = !empty($ground_overlay_data['image_redirect_url']) ? esc_attr($ground_overlay_data['image_redirect_url']) : ''; //@since 4.0
								$image_url_target = !empty($ground_overlay_data['image_url_target']) ? esc_attr($ground_overlay_data['image_url_target']) : 'new_window'; //@since 4.0									
								
								$image_tag = !empty($image_connected_post) ? 'imgConnectedPost_'.esc_attr($image_connected_post) : ''; //@since 4.6
																	
								$opacity = ($image_visibility == 'hide') ? 0 : $image_opacity;
									
								$count_ground_overlays++;
									
								if(isset($image_ne_bounds[0], $image_ne_bounds[1], $image_sw_bounds[0], $image_sw_bounds[1])){
									
									if(!empty($image_ne_bounds[0]) && !empty($image_ne_bounds[1]) && !empty($image_sw_bounds[0]) && !empty($image_sw_bounds[1])){ ?>
																		
										var ne_latLng = cspm_validate_latLng('<?php echo $image_ne_bounds[0]; ?>', '<?php echo $image_ne_bounds[1]; ?>');
										var sw_latLng = cspm_validate_latLng('<?php echo $image_sw_bounds[0]; ?>', '<?php echo $image_sw_bounds[1]; ?>');

										/**
										 * Warn about available images with wrong Lat,Lng coordinates
										 * @since 3.6 */
										 
										if(!ne_latLng || !sw_latLng){
												
											false_ground_overlays.push('<?php echo $image_name; ?>'); 
											
										}else{												

											var ground_overlay = {
												tag: 'ground_overlay',
												options:{
													url: "<?php echo esc_attr($image_url); ?>",															
													bounds: {
													  ne: ne_latLng,
													  sw: sw_latLng
													},
													opts:{
													  opacity: <?php echo $opacity; ?>,
													  original_opacity: <?php echo $image_opacity; ?>,
													  visibility: '<?php echo $image_visibility; ?>',
													  show_icon: '<?php echo $show_btn_icon; ?>',
													  hide_icon: '<?php echo $hide_btn_icon; ?>',
													  icon_position: cspm_validate_latLng('<?php echo $icon_position[0]; ?>', '<?php echo $icon_position[1]; ?>'),
													  clickable: <?php if($image_clickable == 'false'){ ?> false <?php }else{ ?> true <?php } ?>, //@since 4.0
													  redirectURL: '<?php echo $image_redirect_url; ?>', //@since 4.0
													  urlTarget: '<?php echo $image_url_target; ?>', //@since 4.0
													  connectedPost: '<?php echo esc_attr($image_connected_post); ?>', //@since 4.6
													}
												},														
											};
		
											ground_overlays_values.push(ground_overlay); 
											
										} <?php
									
									}
							
								}
							
							}
							
						}
						
					}
				
				}
			
				/**
				 * Warn about available images with wrong Lat,Lng coordinates
				 * @since 3.6
                 * @edited 5.6.9 */
				
				if(current_user_can('activate_plugins')){ ?>
																
					if(cspm_object_size(false_ground_overlays) > 0){
																			
						var title = 'Incorrect \"North-East(NE)\" and/or \"South-West(SW)\" coordinates!<br><br>';
						var message = 'Edit your map. Click on the menu <strong>\"Overlays settings\"</strong>, edit the image(s) <strong>\"'+false_ground_overlays.join('\", \"')+'\"</strong> and ...<br>';
							message += '<strong>1.</strong> Make sure the \"NE/SW\" coordinates are comma separated.<br>';
							message += '<strong>2.</strong> Make sure the \"NE/SW\" latitude coordinate ranges between -90 and 90 degrees.<br>';
							message += '<strong>3.</strong> Make sure the \"NE/SW\" longitude coordinate ranges between -180 and 180 degrees.<br>';
							message += '<strong>4.</strong> Make sure the \"NE/SW\" latitude coordinate is written first, followed by the Longitude.<br>';
							message += '<strong>5.</strong> Make sure to replace the comma in the \"NE/SW\" Latitude and/or the Longitude by a dot.';
						
						cspm_popup_msg(map_id, 'fatal_error', title, message);
					
					} <?php
					
				}
												
				?>
				
				/**
				 * Build the map */
				
				plugin_map.gmap3({		
					map:{
						options: map_options,							
						onces: {
							tilesloaded: function(map){

								var carousel_output = []; 

								plugin_map.gmap3({ 										
									marker:{
										values: json_markers_data,
										callback: function(markers){
											
											all_map_markers_objs[map_id] = markers; //@since 5.0
											
											<?php 
										
											/**
											 * Warn about available posts with wrong Lat,Lng coordinates
											 * @since 3.6
                                             * @edited 5.6.9 */
											
											if(current_user_can('activate_plugins')){ ?>
												 
												if(cspm_object_size(false_latLngs) > 0){
			
													var title = 'Incorrect coordinates!<br /><br />';
													var message = 'Edit the post(s) width the ID(s) <strong>\"'+false_latLngs.join('\", \"')+'\"</strong> and ...<br />';
														message += '<strong>1.</strong> Make sure the Laitude & Longitude are comma separated.<br />';
														message += '<strong>2.</strong> Make sure the Latitude ranges between -90 and 90 degrees.<br />';
														message += '<strong>3.</strong> Make sure the Longitude ranges between -180 and 180 degrees.<br />';
														message += '<strong>4.</strong> Make sure the Latitude coordinate is written first, followed by the Longitude.<br />';
														message += '<strong>5.</strong> Make sure to replace the comma in the Latitude and/or the Longitude by a dot.';
													cspm_popup_msg(map_id, 'fatal_error', title, message);
		
												}<?php
												
											} 
											
											?>
											
											<?php 
																							
											/**
											 * Autofit the map to contain all markers & clusters */

											if($this->autofit == 'true'){ ?>
											
												plugin_map.gmap3({
													get: {
														name: 'marker',
														all:  true,										
													},
													autofit:{}
												});
												
											<?php } ?>
											
											/**
											 * [@markers_latlngs] Contains all marker latLngs
											 * @since 3.3 */
											 
											var markers_latlngs = [];
											var heatmap_data = []; //@since 5.3 [Contains heatmap weighted data points]
											
											/**
											 * Build the carousel items */
												
											for(var i = 0; i < markers.length; i++){	
												
												var post_id = markers[i].post_id;
												var is_child = markers[i].is_child;
												var marker_position = markers[i].position;
												
												markers_latlngs.push(marker_position); //@since 3.3
												
												<?php if($this->heatmap_layer != 'false'){ ?> 
													heatmap_data.push({location: marker_position, weight: <?php echo $this->heatmap_point_weight; ?>});  //@since 5.3
												<?php } //@since 5.3 ?>
												
												if(!light_map){
													
													<?php
								
													/**
													 * Build the carousel items when using the map as a normal map
													 * @since 3.3 */
								 
													if($this->map_type == 'normal_map'){ ?>
													
														/**
														 * Convert the LatLng object to array */
														 
														var lat = marker_position.lat();
														var lng = marker_position.lng();											
														
														var sync_to_viewport = '<?php echo $this->sync_carousel_to_viewport; ?>'; //@since 5.0
														var is_in_viewport = (sync_to_viewport == 'yes') ? cspm_map_bounds_contains_marker(plugin_map, lat, lng) : true; //@since 5.0
													
														if(is_in_viewport){
															
															markers_on_viewport.push(post_id);
															
															carousel_output.push(
																cspm_carousel_item_HTML({
																	map_id: map_id,
																	post_id: post_id,
																	is_child: is_child,
																	index: (i+1),
																	latLng: lat+'_'+lng,
																})
															);							
														
														}	
													
														if(i == markers.length-1)
															$('ul#cspm_carousel_'+map_id).append(carousel_output.join(''));	

													<?php } ?>	
													
													if(i == markers.length-1)
														cspm_init_carousel(null, map_id);
												
												}

											}	
											
											<?php 
											
											/**
											 * Heatmap Layer
											 * @since 3.3
											 * @updated 3.7 | 5.3 */
											
											if($this->heatmap_layer != 'false'){ ?>
													
												var heatmap_options = {
													data: heatmap_data,
													map: plugin_map.gmap3('get'),
													dissipating: <?php if($this->heatmap_dissipating == 'true'){ ?> true <?php }else{ ?> false <?php } ?>, //@since 5.3
													opacity: <?php echo str_replace(',', '.', $this->heatmap_opacity); ?>, //@since 5.3
												};
																																				
												<?php if(!empty($this->heatmap_intensity)){ ?> heatmap_options = $.extend({}, heatmap_options, {maxIntensity: <?php echo $this->heatmap_intensity; ?>}); <?php } ?> //@since 5.3
												<?php if(!empty($this->heatmap_radius)){ ?> heatmap_options = $.extend({}, heatmap_options, {radius: <?php echo $this->heatmap_radius; ?>}); <?php } ?> //@since 5.3
												<?php 
												
												/**
												 * Build the custom heatmap color gradient 
												 * @since 5.3 */
												 
												if($this->heatmap_color_type != 'default' && is_array($this->heatmap_colors)){ ?> 
													
													var color_array = [];
													
													color_array.push('<?php echo $this->cspm_hexToRgb($this->cspm_setting_exists('start_color', $this->heatmap_colors[0], 'rgb(238, 238, 34)')); ?>');
														
													var middle_color = '<?php echo $this->cspm_hexToRgb($this->cspm_setting_exists('middle_color', $this->heatmap_colors[0], '')); ?>';
														if(middle_color != '') color_array.push(middle_color);
														
													color_array.push('<?php echo $this->cspm_hexToRgb($this->cspm_setting_exists('end_color', $this->heatmap_colors[0], 'rgb(221, 51, 51)')); ?>');
													
													var stops = <?php echo $this->cspm_setting_exists('stops', $this->heatmap_colors[0], 10); ?>;
													
													var custom_gradient = gradstop({
														stops: stops,
														inputFormat: 'rgb',
														colorArray: color_array
													});												
													custom_gradient.unshift(custom_gradient[0]); // Add the start color as the first color with 0 opacity to prevent an issue in GMaps!
																									
													heatmap_options = $.extend({}, heatmap_options, {
														gradient: jQuery.map(custom_gradient, function(color, index){
															var rgba = color.replace('rgb', 'rgba');
															return (index == 0) ? rgba.replace(')', ', 0)') : rgba.replace(')', ', 1)');
														}),
													});
													
												<?php } ?>
												
												heatmap[map_id] = new google.maps.visualization.HeatmapLayer(heatmap_options); //@edited 5.3
												
												<?php
												
												/**
												 * Hide heatmap layer on map load when set to "toggle_markers"
												 * @since 3.7 */
												 
												if($this->heatmap_layer == 'toggle_markers'){ ?>
													heatmap[map_id].setMap(null); <?php 
												} 
												
												?>
											
											<?php } ?>																						
                                            
                                            <?php 
					
											/**
											 * Geo Targeting
											 * @updated 3.2 [added retina support for user marker icon] */
																	
											if(esc_attr($geo) == "true"){ ?>
												
												if((typeof NProgress !== 'undefined' && NProgress.done()) || typeof NProgress === 'undefined'){
													
													setTimeout(function(){
														
														<?php if(esc_attr($show_user) == 'true'){ ?> 
															var show_user_marker = true; 
														<?php }else{ ?>
															var show_user_marker = false;
														<?php } ?>
														
														<?php
														
														/**
														 * Get marker image size for Retina display
														 * @since 3.2 */
														 
														$marker_img_size = (esc_attr($show_user) == 'true') ? $this->cspm_get_image_size(
															array(
																'path' => $this->cspm_get_image_path_from_url($user_marker),
																'default_width' => $this->user_marker_icon_height,
																'default_height' => $this->user_marker_icon_height,																					
															)
														) : '';
												
														?>
														
														cspm_geolocate(plugin_map, map_id, show_user_marker, '<?php echo esc_attr($user_marker); ?>', '<?php echo esc_attr($marker_img_size); ?>', <?php echo esc_attr($user_circle); ?>, <?php echo esc_attr($user_map_zoom); ?>, false);															
														
													}, 1000);
													
												}
											
											<?php } ?>
		
											<?php 
					
											/**
											 * Auto check/select an option/term in the filter
											 * @since 3.0 */
																	
											if($this->faceted_search_option == "true" 
											&& $this->faceted_search_autocheck == "true" 
											&& count($this->faceted_autocheck_terms) > 0){ ?>
													
												setTimeout(function(){

													var inputValues = [];
													
													<?php foreach($this->faceted_autocheck_terms as $key => $val){ ?>
													
														inputValues.push('<?php echo $val; ?>');
													
													<?php } ?>

													/**
													 * Loop throught selected terms and check their related checkboxes or radio button */
													
													for(var i = 0; i < cspm_object_size(inputValues); i++){
															
														var icheck_selector = $('form#faceted_search_form_'+map_id+' input[data-map-id='+map_id+'][data-term-id='+inputValues[i]+']');
														
														if(icheck_selector.is(':visible') && typeof icheck_selector.iCheck === 'function')
															icheck_selector.iCheck('check');
														
													}

													if(typeof $('div.faceted_search_container_'+map_id).mCustomScrollbar === 'function')
														$('form.faceted_search_form[data-map-id='+map_id+'] ul').mCustomScrollbar("scrollTo", 'input[data-map-id='+map_id+'][data-term-id='+inputValues[inputValues.length-1]+']', {timeout: 500});
													
												}, 500);
											
											<?php } ?>
                            
                                            <?php 

                                            /**
                                             * Hide all KML Layers on map load
                                             * @since 5.6.7 */

                                            if($this->kml_list == 'true' && $this->kml_layers_onload_status != 'show'){ ?>
                                                plugin_map.gmap3({
                                                    get: {
                                                        name: 'kmllayer',
                                                        all: true,
                                                        callback: function(layers){
                                                            jQuery.each(layers, function(i, layer){
                                                                if(typeof layer !== 'undefined' 
                                                                && typeof layer.setMap === 'function'){
                                                                    layer.setMap(null);
                                                                }
                                                            });
                                                        }
                                                    }
                                                });	
                                            <?php } ?>
                                
										},											
										events:{
											mouseover: function(marker, event, elements){
												
												/**
												 * Show a message to the user to inform them that ...
												 * ... they can open the the post media modal if they ...
												 * ... click on the marker.
												 *
												 * @since 3.5 */

												if(typeof marker.media !== 'undefined' && marker.media.format != 'standard' && marker.media.format != '')
													cspm_open_media_message(marker.media, map_id);

												/**
												 * Display the single infobox */

												if(json_markers_data.length > 0 && show_infobox == 'true' && infobox_display_event == 'onhover'){
													cspm_draw_single_infobox(plugin_map, map_id, marker, <?php echo wp_json_encode($infobox_options); ?>).done(function(){
														cspm_connect_carousel_and_infobox();														
													});
												}
												
												<?php if(in_array('marker_hover', $this->move_carousel_on)){ ?>
												
													/**
													 * Apply the style for the active item in the carousel */
													 
													if(!light_map){	
														
														var post_id = marker.post_id;
														var is_child = marker.is_child;	
														var i = $('li.cspm_carousel_item[data-map-id='+map_id+'][data-post-id='+post_id+'][data-is-child='+is_child+']').attr('jcarouselindex'); //@edited 5.6
														
														cspm_call_carousel_item($('ul#cspm_carousel_'+map_id).data('jcarousel'), i);
														cspm_carousel_item_hover_style('li.jcarousel-item-'+i+'.cspm_carousel_item[data-map-id='+map_id+'][data-post-id='+post_id+']', map_id); //@edited 5.0
													
													}
												
												<?php } ?>
											
												<?php 
						
												/**
												 * Marker menu
												 * @since 5.5 */
																		
												if($this->use_marker_menu == 'yes' && $this->marker_menu_open_mouse_enter == 'yes'){ ?>
													cspm_open_marker_menu(plugin_map, map_id, marker, <?php echo wp_json_encode($marker_menu_options); ?>);<?php 
												} 
												
												?>

											},	
											mouseout: function(marker, event, elements){
												
												var post_id = marker.post_id;
												
												/**
												 * Hide the post media message
												 * @since 3.5 */

												if(typeof marker.media !== 'undefined' && marker.media.format != 'standard' && marker.media.format != '')
													cspm_close_media_message(map_id);
												
											},
											rightclick: function(marker, event, elements){
											
												<?php 
						
												/**
												 * Marker menu
												 * @since 5.5 */
																		
												if($this->use_marker_menu == 'yes' && $this->marker_menu_open_rightclick == 'yes'){ ?>
													cspm_open_marker_menu(plugin_map, map_id, marker, <?php echo wp_json_encode($marker_menu_options); ?>);<?php 
												} 
												
												?>

											},
											click: function(marker, event, elements){
												
												var latLng = marker.position;
												var post_id = marker.post_id;
												
												marker.media['post_id'] = marker.post_id; // Add post ID to media array | @since 4.6													
												
												/**
												 * Center the map on that marker */
												
												map.panTo(latLng);

												/**
												 * Display the single infobox */

												if(json_markers_data.length > 0 && show_infobox == 'true' && infobox_display_event == 'onclick'){
													cspm_draw_single_infobox(plugin_map, map_id, marker, <?php echo wp_json_encode($infobox_options); ?>).done(function(){
														cspm_connect_carousel_and_infobox();														
													});
												}
												
												<?php if(in_array('marker_click', $this->move_carousel_on)){ ?>						
												
													/**
													 * Apply the style for the active item in the carousel */
													 
													if(!light_map){	
														
														var post_id = marker.post_id;
														var is_child = marker.is_child;
														var i = $('li.cspm_carousel_item[data-map-id='+map_id+'][data-post-id='+post_id+'][data-is-child='+is_child+']').attr('jcarouselindex'); //@edited 5.6														
														
														cspm_call_carousel_item($('ul#cspm_carousel_'+map_id).data('jcarousel'), i);
														cspm_carousel_item_hover_style('li.jcarousel-item-'+i+'.cspm_carousel_item[data-map-id='+map_id+'][data-post-id='+post_id+']', map_id); //@edited 5.0
													
													}
												
												<?php } ?>
											
												<?php 
						
												/**
												 * Marker menu
												 * @since 5.5 */
																		
												if($this->use_marker_menu == 'yes' && $this->marker_menu_open_leftclick == 'yes'){ ?>
													cspm_open_marker_menu(plugin_map, map_id, marker, <?php echo wp_json_encode($marker_menu_options); ?>);<?php 													
												} 
												
												?>
												
												<?php 
												
												/**
												 * This will add hover/active style to a list item 
												 * Note: Used only for the extension "List & Filter"
												 * @since 2.8.3 */
												 
												if(class_exists('ProgressMapList')){ ?>

													if(typeof cspml_animate_list_item == 'function')
														cspml_animate_list_item(map_id, post_id);
																												
												<?php } ?>
												
												<?php
												
												/**
												 * Nearby points of interests.
												 * Add the coordinates of this marker to the list of Proximities ...
												 * ... in order to use them (latLng) to display nearby points of interest ...
												 * ... of that marker 
												 * @since 3.2 */
												 
												if($this->nearby_places_option == 'true'){ ?>
												
													$('select.cspm_proximities_list[data-map-id='+map_id+'] option.cspm_proximity_name[data-map-id='+map_id+']').attr('data-marker-latlng', latLng).attr('data-marker-post-id', post_id); //@since 5.6.3 || Fix an issue when selecting a marker before opening the proximities list. "Tail.select" inherit all select options attributes!!
                                                    $('li.cspm_proximity_name[data-map-id='+map_id+']').attr('data-marker-latlng', latLng).attr('data-marker-post-id', post_id).removeClass('selected');
													
													var message = '<?php echo $proximities_popup_msg; ?>'; //@edited 5.5
													cspm_popup_msg(map_id, 'success', '', message);
																													
													<?php
												}
												
												?>									
												
												/**
												 * Open the post/location media modal
												 * @since 3.5 */

												if(typeof marker.media !== 'undefined' && marker.media.format != 'standard' && marker.media.format != ''){
													var allow_marker_click = (typeof marker.media.marker_click !== 'undefined') ? marker.media.marker_click : 'yes'; //@since 5.5
													cspm_open_media_modal(marker.media, map_id, allow_marker_click);
												}
												
											}
										}
									},
									
								});									
								
								<?php
								
								/**
								 * Clustring markers */
								 
								if($this->useClustring == 'true'){ ?>
									clustering_method = true;
									var clusterer = cspm_clustering(plugin_map, map_id, light_map);<?php
								}
								
								?>
								
								/**
								 * Show map custom controls
								 * @since 5.6 */
								 
								$('div.cspm_custom_controls[data-map-id='+map_id+'], .cspm_map_btn[data-map-id='+map_id+']').show();

								<?php
								
								/**
								 * Show the faceted search after the map load
								 * @updated 5.6 */
								 
								if($faceted_search == 'yes' 
								&& $this->faceted_search_option == 'true' 
								&& $this->marker_cats_settings == 'true'
								&& ($this->faceted_search_display_status == 'open' || $this->faceted_search_autocheck == 'true')){ ?>
										$('div.faceted_search_btn[data-map-id='+map_id+']').trigger('click'); <?php 
								}
							
								/**
								 * Show the search form after the map load
								 * @updated 5.6 */
								 
								if($search_form == 'yes' 
								&& $this->search_form_option == 'true'
								&& $this->sf_display_status == 'open'){ ?>
									setTimeout(function(){                    
										$('div.search_form_btn[data-map-id='+map_id+']').trigger('click');
									}, 100); <?php 
								}
								
								if(!$light_map){
									if($map_layout == 'map-tglc-bottom'){ ?> 
										$('div.toggle-carousel-bottom').show(); <?php 
									}elseif($map_layout == 'map-tglc-top'){ ?> 
										$('div.toggle-carousel-top').show(); <?php  
									} 
								}
								
								/**
								 * Show countries list after map load 
								 * @since 3.0
								 * @updated 5.6 */
								 
								if($this->zoom_country_option == 'true'
								&& $this->zoom_country_display_status == 'open' 
								&& $this->faceted_search_autocheck == 'false'){ ?>
									setTimeout(function(){
										$('div.countries_btn[data-map-id='+map_id+']').trigger('click');
									}, 100); <?php 
								}
								
								/**
								 * Show nearby points of interests list after map load 
								 * @since 3.2
								 * @updated 5.6 */
								 
								if($this->nearby_places_option == 'true'
								&& $this->np_proximities_display_status == 'open'){ ?>
									setTimeout(function(){
										$('div.cspm_proximities_btn[data-map-id='+map_id+']').trigger('click');
									}, 100); <?php 
								}
								
								/**
								 * Show the KML Layers list after map load
								 * @since 5.6 */
								 
								if($this->use_kml == 'true'
								&& $this->kml_list == 'true'
								&& $this->kml_list_display_status == 'open'){ ?>
									setTimeout(function(){
										$('div.kml_list_btn[data-map-id='+map_id+']').trigger('click');
									}, 100); <?php 
								}
																
								/**
								 * Show post count widget
								 * @since 3.8 */

								if($this->show_posts_count == 'yes'){ ?>
									$('div.number_of_posts_widget[data-map-id='+map_id+']').show(); <?php 
								}
								
								/**
								 * Execute actions after map load
								 * @since 3.3 */
								 
								do_action('cspm_do_after_map_load', $map_id); 
								apply_filters('cspm_after_map_load', '', $map_id); 
								
								?>
								
								/**
								 * Draw on the map */
								 
								if(json_markers_data.length > 0){
								
									/**
									 * Draw infoboxes (onload event)
									 * @edited 5.3 */
									 
									if((infobox_display_event == 'onload' || infobox_display_event == 'onzoom') && show_infobox == 'true'){ //@edited 5.3												
										if(clustering_method == true){
											google.maps.event.addListenerOnce(clusterer, 'clusteringend', function(cluster){																	
												setTimeout(function(){
													cspm_draw_multiple_infoboxes(plugin_map, map_id, <?php echo wp_json_encode($infobox_options); ?>).done(function(){
														cspm_connect_carousel_and_infobox();														
													});
													infobox_loaded = true;														
												}, 500);																
											});	
										}else{
											setTimeout(function(){
												cspm_draw_multiple_infoboxes(plugin_map, map_id, <?php echo wp_json_encode($infobox_options); ?>).done(function(){
													cspm_connect_carousel_and_infobox();														
												});											
												infobox_loaded = true;
											}, 500);
										}
									}else infobox_loaded = true;
								
									/**
									 * Draw Marker popups
									 * @since 4.0 */
									 
									if(use_marker_popups == 'yes'){			
										if(clustering_method == true){
											google.maps.event.addListenerOnce(clusterer, 'clusteringend', function(cluster){																	
												setTimeout(function(){
													cspm_draw_multiple_popups(plugin_map, map_id, <?php echo wp_json_encode($marker_popups_options); ?>);													
													marker_popups_loaded = true;
												}, 500);																
											});	
										}else{
											setTimeout(function(){
												cspm_draw_multiple_popups(plugin_map, map_id, <?php echo wp_json_encode($marker_popups_options); ?>);													
												marker_popups_loaded = true;
											}, 500);
										}
									}
																
								}									

								<?php
								
								/**
								 * Hide all markers when using the map as a search map
								 * @since 3.3 */
								 
								if($this->map_type == 'search_map'){ ?>
									cspm_hide_all_markers(plugin_map, map_id);
								<?php } ?>
								
								/**
								 * End the Progress Bar Loader */
									
								if(typeof NProgress !== 'undefined')
									NProgress.done();
								
							}
							
						},
						events:{
							idle: function(map){

								/**
								 * re-display infoboxes after search/filter request & map idle event
								 * @edited 5.3 */
								 
								if(infobox_loaded && !cspm_is_panorama_active(plugin_map)){
									setTimeout(function(){
										if(	json_markers_data.length > 0 
											&& show_infobox == 'true' 
											&& (infobox_display_event == 'onload' || infobox_display_event == 'onzoom') //@edited 5.3	
										){
											cspm_draw_multiple_infoboxes(plugin_map, map_id, <?php echo wp_json_encode($infobox_options); ?>).done(function(){
												cspm_connect_carousel_and_infobox();														
											});											
											infobox_loaded = true;
												
										}
									}, 200);
								}
								
								/**
								 * Marker popups
								 * @since 4.0 */
																	
								if(use_marker_popups == 'yes' && marker_popups_loaded){
									setTimeout(function(){
										cspm_draw_multiple_popups(plugin_map, map_id, <?php echo wp_json_encode($marker_popups_options); ?>);													
									}, 200);
								}
								
								/**
								 * Set Marker labels visibility
								 * Note: This will prevent an issue where marker labels stay visible when markers are not in the viewport of the map!
								 * @since 4.0 */
								
								cspm_set_marker_labels_visibility(plugin_map, map_id);

							},
							bounds_changed: function(map){
									
								setTimeout(function() {
									$('div[class^=cluster_posts_widget]').removeClass('flipInX');
									$('div[class^=cluster_posts_widget]').addClass('cspm_animated flipOutX');
									$('div[class^=cluster_posts_widget]').attr('data-cluster-id', ''); //@since 3.5
									setTimeout(function() {
										if(typeof $('div.cluster_posts_widget_'+map_id).mCustomScrollbar === 'function'){
											$('div.cluster_posts_widget_'+map_id).mCustomScrollbar("destroy");
										}
									}, 500);
								}, 500);
							
								<?php 
								
								/**
								 * Get all IDs in the current viewport and compare them with the ones in the previous viewport.
								 * If different IDs exists, rewrite carousel by loading only posts/locations on the viewport of the map
								 * @since 5.0 */
								
								if($this->sync_carousel_to_viewport == 'yes' && !$light_map){ ?>
									
									setTimeout(function(){									
										var rewrite_markers_on_viewport = [];
										rewrite_markers_on_viewport = cspm_get_markers_on_viewport(plugin_map, map_id);
										viewport_diff_ids = cspm_arrayDiff(markers_on_viewport, rewrite_markers_on_viewport);									
										if(cspm_object_size(viewport_diff_ids) > 0){
											
											markers_on_viewport = rewrite_markers_on_viewport;
											
											if(typeof NProgress !== 'undefined'){
												NProgress.configure({
												  parent: 'div#cspm_carousel_container[data-map-id='+map_id+']',
												  showSpinner: true
												});				
												NProgress.start();
												NProgress.set(0.5);
											}
												
											setTimeout(function(){
												cspm_rewrite_carousel(map_id, 'yes', markers_on_viewport);
												if(typeof NProgress !== 'undefined') NProgress.done();
											}, 500);
											
										}
									}, 500);
								
								<?php } ?>									
								
							},
						}
					},					
											
					<?php 
	
					/**
					 * Images (Ground overlays)
					 * @since 3.5 */
					
					if($this->ground_overlays_option == 'true' && !empty($this->ground_overlays) && $count_ground_overlays > 0){ ?>
						
						groundoverlays:{
							values: ground_overlays_values,
							callback: function(overlay){
								cspm_groundoverlay_callback(map_id, plugin_map, overlay);
							},
							events: {
								click: function(overlay, event){
									var clickable = overlay.clickable;
									var redirectURL = overlay.redirectURL;
									var urlTarget = overlay.urlTarget;
									if(clickable){
										if(urlTarget == 'new_window'){
											window.open(redirectURL);
                                        }else if(urlTarget == 'popup'){
                                            cspm_open_single_post_modal(redirectURL); //@since 5.6.3
										}else window.location = redirectURL;
									}
								},
								mouseover: function(overlay, event, context){
									var clickable = overlay.clickable;
									var redirectURL = overlay.redirectURL;
									if(clickable && redirectURL != ''){
										var message = progress_map_vars.image_redirect_url_msg;
										cspm_popup_msg(map_id, 'info', '',  message);
									}
								},
								mouseout: function(overlay, event, context){
									var clickable = overlay.clickable;
									var redirectURL = overlay.redirectURL;
									if(clickable && redirectURL != ''){
										cspm_close_popup_msg(map_id, 'info');
									} 
								}
							},
						},
					
					<?php } ?>
											
					<?php 
					
					/**
					 * Draw Polylines
					 * @since 2.7
					 */
					
					if(($this->draw_polyline == 'true' && !empty($polyline_objects)) || ($this->draw_polygon == 'true' && $dashed_polygons > 0)){ //@edited 5.6.1 ?>

						polyline:{
							values: polyline_values
						},
					
					<?php } ?>
					
					<?php 
					
					/**
					 * Draw Polygons
					 * @since 2.7
					 * @updated 5.3
					 */
					
					if(($this->draw_polygon == 'true' && !empty($polygon_objects)) || ($this->map_holes_option == 'true')){ //@edietd 5.3 ?>

						polygon:{
							values: polygon_values
						},
					
					<?php } ?>
					
					<?php 
					
					/**
					 * Display KML Layers
					 * @since 2.7
					 * @updated 3.0 [supports multiple KML layers]
					 */
					 
					if($this->use_kml == 'true' && $count_kml_layers > 0){ ?>
									
						kmllayer:{
							values: kml_values
						},

					<?php } ?>
						
					<?php 
					
					/**
					 * Show the Traffic Layer
					 * @since 2.7
					 */
					
					if($this->traffic_layer == "true"){ ?>
						trafficlayer:{},
					<?php } ?>										
					
					<?php 
					
					/**
					 * Show the Transit Layer
					 * @since 2.7.4
					 */
					
					if($this->transit_layer == "true"){ ?>
						transitlayer:{},
					<?php } ?>
						
					<?php 
					
					/**
					 * Show the Bicycling Layer
					 * @since 5.3
					 */
					
					if($this->bicycling_layer == "true"){ ?>
						bicyclinglayer:{},
					<?php } ?>
					 
					<?php 
					
					/**
					 * Set the map style */
					
					if(count($map_styles) > 0 && $this->map_style != 'google-map' && isset($map_styles[$this->map_style])){ ?> 
						<?php $style_title = isset($map_styles[$this->map_style]['title']) ? $map_styles[$this->map_style]['title'] : $this->custom_style_name; ?>
						
						styledmaptype:{
							id: "custom_style",
							options:{
								name: "<?php echo wp_trim_words($style_title, 1, '...'); //@edited 5.4 ?>",
								alt: "Show <?php echo $style_title; ?>"
							},
							styles: <?php echo $map_styles[$this->map_style]['style']; ?>
						},
						
					<?php } ?>
											
				});		
				
				var mapObject = plugin_map.gmap3('get');

				/** 
				 * Rotating 45° imagery support
				 * @since 3.3 */
				 
				if(typeof setTilt === 'function')
					mapObject.setTilt(45);
				
				/**
				 * Hide/Show UI Controls depending on the streetview visibility
				 * @updated 5.6 */

				if(typeof mapObject.getStreetView === 'function'){
											
					var streetView = mapObject.getStreetView();
				
					google.maps.event.addListener(streetView, 'visible_changed', function(){
						
						if(this.getVisible()){
								
							/**
							 * Hide map custom controls
							 * @since 5.6 */
								 
							$('div.cspm_custom_controls[data-map-id='+map_id+']').hide();
								
							<?php 
							
							/**
							 * Execute actions when turning on the streetview mode
							 * @since 3.3 */
							
							do_action('cspm_do_streetview_mode_on', $map_id); 
							apply_filters('cspm_streetview_mode_on', '', $map_id); 
							
							?>
							 
						}else{
								
							/**
							 * Show map custom controls
							 * @since 5.6 */
								 
							$('div.cspm_custom_controls[data-map-id='+map_id+']').show();
								
							<?php 
							
							/**
							 * Execute actions when turning off the streetview mode
							 * @since 3.3 */
							
							do_action('cspm_do_streetview_mode_off', $map_id); 
							apply_filters('cspm_streetview_mode_off', '', $map_id); 
							
							?>

						}
							
					});
					
				}
						
				<?php 
									
				/**
				 * Show error msg when center point is not correct */

				if($this->wrong_center_point){ 
					
					$error_message = esc_html__('The map center is incorrect. Please make sure the Latitude & the Longitude in \"Map Settings => Map center\" are comma separated!', 'cspm'); ?>
						
					plugin_map.gmap3({
						panel:{
							options:{
								content: '<div class="error_widget"><?php echo $error_message; ?></div>',
								middle: true,
								center: true,								
							}
						}
					});
						
					<?php 
				
				} 
				
				?>
				
				<?php 
				
				/**
				 * Custome zoom controls */
				 
				if($this->zoomControl == 'true' && $this->zoomControlType == 'customize'){ ?>						 
					cspm_zoom_in('<?php echo $map_id; ?>', $('div.cspm_zoom_in_'+map_id), plugin_map);
					cspm_zoom_out('<?php echo $map_id; ?>', $('div.cspm_zoom_out_'+map_id), plugin_map); <?php 
				} 
				
				?>
				
				<?php 		
				
				/**
				 * Fit map to its container
				 * @since 2.8 */
				
				if($map_layout == 'fit-in-map' || $map_layout == 'fit-in-map-top-carousel'){ ?>
					
					cspm_fitIn_map(map_id);
					$(window).resize(function(){ cspm_fitIn_map(map_id); }); <?php
				
				/**
				 * Fit map to screen size
				 * @since 2.8 */
				
				}else if($map_layout == 'fullscreen-map' || $map_layout == 'fullscreen-map-top-carousel'){ ?>
					
					cspm_fullscreen_map(map_id);
					$(window).resize(function(){ cspm_fullscreen_map(map_id); }); <?php
				
				}

				/**
				 * Resize Carousel when it's a "fullscreen" map or "fit in map"
				 * @since 2.8 */
				
				if($map_layout == 'm-con' || $map_layout == 'fullscreen-map-top-carousel' || $map_layout == 'fit-in-map-top-carousel'){ ?>
				
					cspm_carousel_width(map_id);
					$(window).resize(function(){ cspm_carousel_width(map_id); }); <?php
					
				}	
				
				?>
				 
				<?php 
									
				/**
				 * Recenter the Map on screen resize */

				if(esc_attr($window_resize) == 'yes' && isset($center_point[0]) && !empty($center_point[0]) && isset($center_point[1]) && !empty($center_point[1])){ ?>
					
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

								
								var latLng = cspm_validate_latLng('<?php echo $center_point[0]; ?>', '<?php echo $center_point[1]; ?>');							
							
								if(!latLng)
									return;
									
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
				
				if(!$this->wrong_center_point && !empty($center_point[0]) && !empty($center_point[1])){ ?>					
					
					$('body').ready(function(){
                        if($(plugin_map_placeholder).is(':visible')){                            
                            if(_CSPM_MAP_RESIZED[map_id] <= 1){ /* 0 is for the first time loading, 1 is when the user clicks the map tab */							
                                cspm_center_map_at_point(plugin_map, '<?php echo $map_id ?>', <?php echo $center_point[0]; ?>, <?php echo $center_point[1]; ?>, 'resize');
                                _CSPM_MAP_RESIZED[map_id]++;
                            }
                            cspm_zoom_in_and_out(plugin_map);
                        }
                    }); //@edited 5.6.3

				<?php } ?>
				 
				<?php
				
				/**
				 * Add support for the Autocomplete for the address in the search form
				 * @since 2.8
				 * @updated 4.0 */
				
				if($search_form == "yes" && $this->search_form_option == "true" && $this->autocomplete_option == 'yes'){ ?>
							
					var input = document.getElementById('cspm_address_'+map_id);
					var autocomplete = new google.maps.places.Autocomplete(input); 

					<?php 
					
					/** 
					 * Restrict the autocomplete to map bounds
					 * @since 4.0 */
					
					if($this->autocomplete_strict_bounds == 'yes'){ ?>
						autocomplete.bindTo('bounds', mapObject);
						autocomplete.setOptions({strictBounds: true}); <?php 
					} 
					
					/** 
					 * Restrict the autocomplete to specific countries
					 * @since 4.0 */
					
					if($this->autocomplete_country_restrict == 'yes' && count($this->autocomplete_countries) > 0){ 
						$countries = wp_json_encode($this->autocomplete_countries); ?>
						autocomplete.setComponentRestrictions({'country': <?php echo $countries; ?>}); <?php 
					} 
					
				} 
				
				/**
				 * Execute actions before "ob_end_clean"
				 * @since 5.6.5 */
				 
				do_action('cspm_do_before_ob_end_clean', $map_id); 
				apply_filters('cspm_before_ob_end_clean', '', $map_id); 
				
				?>

				_CSPM_DONE[map_id] = true;
				
				<?php if((is_user_logged_in() && current_user_can('administrator')) || $this->combine_files == 'seperate'){ ?> 
					cspm_check_gmaps_failure(map_id); 
				<?php } //@since 5.6.5 ?>
	
			});
			
			</script> 
			
			<?php
			
			$map_js_script = str_replace(array('<script>', '</script>'), '', ob_get_contents()); //@since 4.9.1
			ob_end_clean(); //@since 4.9.1
			
			$this->cspm_enqueue_styles();
			$this->cspm_enqueue_scripts();		
			
			wp_add_inline_script('cspm-script', $map_js_script); //@since 4.9.1	
			
			/**
			 * Data to be passed to another extension or custom function!
			 * @since 2.6.3
			 * @updated 2.8.5 */
			 
			$atts_array = apply_filters(
				'cspm_main_map_output_atts',
				array(	
					'map_id' => $map_id,
					'post_ids' => implode(',', $post_ids),
					'faceted_search' => $faceted_search,
					'search_form' => $search_form,					
					'faceted_search_tax_slug' => $faceted_search_tax_slug,
					'faceted_search_tax_terms' => $faceted_search_tax_terms,	
					'geo' => esc_attr($geo),
					'infobox_type' => esc_attr($infobox_type), //@since 2.8.5	
				),
				$atts
			);

			/**
			 * Carousel
			 * @since 2.6.3
			 * @updated 2.8
			 * @updated 2.8.5 
			 * @updated 2.8.6 */
			 
			return apply_filters(
				'cspm_main_map_output', 
				$this->cspm_main_map_output(
					array(
						'map_id' => $map_id,
						'carousel' => $carousel,
						'faceted_search' => $faceted_search,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'search_form' => $search_form,
						'show_infoboxes' => $show_infoboxes,
						'infobox_display_event' => $this->infobox_display_event,
						'infobox_type' => esc_attr($infobox_type), //@since 2.8.5
						'infobox_target_link' => esc_attr($infobox_link_target), //@since 2.8.6
						'map_layout' => $map_layout,
						'geo' => esc_attr($geo),
						'nbr_pins' => $nbr_pins, //@since 3.8
					)
				),
				$atts_array
			);
			
			return $output;
			
		}
		
		
		/**
		 * Display the carousel
		 *
		 * @since 2.6 
		 * @updated 2.8 | 2.8.5 | 2.8.6 | 3.8
		 */
		function cspm_main_map_output($atts = array()){
					
			$defaults = array(
				'map_id' => '',
				'carousel' => '',
				'faceted_search' => '',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => '',
				'search_form' => '',
				'show_infoboxes' => '',
				'infobox_display_event' => '',
				'infobox_type' => '', //@since 2.8.5
				'infobox_target_link' => '', //@since 2.8.6
				'map_layout' => '',
				'geo' => '',
				'nbr_pins' => 0, //@since 3.8
			);
			
			extract(wp_parse_args($atts, $defaults));

			$layout_style = '';
			
			/**
			 * Define fixed/fullwidth layout height and width */
			 
			if($map_layout != 'fullscreen-map' && $map_layout != 'fit-in-map'){
	
				if($this->layout_type == 'fixed'){
					$layout_style = "width:".$this->layout_fixed_width."px; height:".$this->layout_fixed_height."px;";
				}else{
					 $layout_style = (($map_layout == "mu-cd" || $map_layout == "md-cu") && $carousel == 'yes')  //@edited 5.5
					 	? "width:100%; height:".($this->layout_fixed_height+20)."px;"
						: "width:100%; height:".$this->layout_fixed_height."px;";
				}
				
			}elseif($map_layout == 'fit-in-map'){ 
				
				$layout_style = "width:100%;";
				
			}elseif($map_layout == 'fullscreen-map'){
				
				$layout_style = "display:block; margin:0; padding:0; position:absolute; top:0; right:0; bottom:0; left:0; z-index:9999"; 
			
			}	
						
			$output = '';
			
			/**
			 * Plugin Container */
				
			$output .= '<div class="codespacing_progress_map_area cspm_linear_gradient_bg" data-map-id="'.$map_id.'" data-show-infobox="'.$show_infoboxes.'" data-infobox-display-event="'.$infobox_display_event.'" '.apply_filters('cspm_container_custom_atts', '', $map_id).' style="'.$layout_style.'">';
				
				/**
				 * This is usefull to know the page template where the map is displayed in order
				 * to execute hooks or function by template page
				 * @since 2.7.5 */
				 
				if(is_single()){
					$queried_object = get_queried_object();
					$page_id = $queried_object->post_type;		
				}elseif(is_author()){
					$page_id = 'author';
				}else $page_id = get_the_ID();
				
				$output .= '<input type="hidden" name="cspm_map_page_id_'.$map_id.'" id="cspm_map_page_id_'.$map_id.'" value="'.$page_id.'" />';
				
				/**
				 * Plugin's Map */
											
				/* =============================== */
				/* ==== Map-Up, Carousel-Down ==== */
				/* =============================== */
				
				if($map_layout == "mu-cd"){
									
					if($this->items_view == "listview")
						$carousel_height = $this->horizontal_item_height + 8;
						
					elseif($this->items_view == "gridview")
						$carousel_height = $this->vertical_item_height + 8;
					
					/**
					 * Detect Mobile browsers and adjust the map height depending on the result	*/
					 
					if(!$this->cspm_hide_carousel_from_mobile()){
						
						$map_height = ($this->show_carousel == 'true' && $carousel == "yes") ? $this->layout_fixed_height - $carousel_height . 'px' : $this->layout_fixed_height . 'px';
						
					}else $map_height = $this->layout_fixed_height . 'px';
					
					/**
					 * Layout
					 * @updated 2.8.5 */
					 
					$output .= $this->cspm_map_up_carousel_down_layout(array(
						'map_id' => $map_id,
						'map_height' => $map_height,								
						'carousel' => $carousel,
						'carousel_height' => $carousel_height,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
									
				/* =============================== */
				/* ==== Map-Down, Carousel-Up ==== */
				/* =============================== */
				
				}elseif($map_layout == "md-cu"){
					
					if($this->items_view == "listview")
						$carousel_height = $this->horizontal_item_height + 8;
						
					elseif($this->items_view == "gridview")
						$carousel_height = $this->vertical_item_height + 8;
					
					/**
					 * Detect Mobile browsers and adjust the map height depending on the result	*/
					 
					if(!$this->cspm_hide_carousel_from_mobile()){
						
						$map_height = ($this->show_carousel == 'true' && $carousel == "yes") ? $this->layout_fixed_height - $carousel_height . 'px' : $this->layout_fixed_height . 'px';
						
					}else $map_height = $this->layout_fixed_height . 'px';
					
					/**
					 * Layout
					 * @updated 2.8.5 */
					 
					$output .= $this->cspm_map_down_carousel_up_layout(array(
						'map_id' => $map_id,
						'map_height' => $map_height,								
						'carousel' => $carousel,
						'carousel_height' => $carousel_height,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
									
									
				/* ================================== */
				/* ==== Map-Right, Carousel-Left ==== */
				/* ================================== */
				
				}elseif($map_layout == "mr-cl"){
					
					if($this->items_view == "listview"){
						
						$carousel_width = $this->horizontal_item_width + 8;
						
					}elseif($this->items_view == "gridview"){
						
						$carousel_width = $this->vertical_item_width + 8;
						
					}
					
					/**
					 * Layout
					 * @updated 2.8.5 */
					 
					$output .= $this->cspm_map_right_carousel_left_layout(array(
						'map_id' => $map_id,
						'carousel' => $carousel,
						'carousel_width' => $carousel_width,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
									
				/* ================================== */
				/* ==== Map-Left, Carousel-Right ==== */
				/* ================================== */
				
				}elseif($map_layout == "ml-cr"){
					
					if($this->items_view == "listview"){
						
						$carousel_width = $this->horizontal_item_width + 8;
						
					}elseif($this->items_view == "gridview"){
						
						$carousel_width = $this->vertical_item_width + 8;
						
					}
					
					/**
					 * Layout
					 * @updated 2.8.5 */
					 
					$output .= $this->cspm_map_left_carousel_right_layout(array(
						'map_id' => $map_id,
						'carousel' => $carousel,
						'carousel_width' => $carousel_width,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
				
				/* ====================== */
				/* ==== Only The Map ==== */
				/* ====================== */
				
				}elseif($map_layout == "fullscreen-map" || $map_layout == "fit-in-map"){
					
					/**
					 * Layout
					 * @updated 2.8.5 */
					 
					$output .= $this->cspm_only_map_layout(array(
						'map_id' => $map_id,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
				
				/* ============================================== */
				/* ==== Fullscreen Map/Fit in map & Carousel ==== */
				/* ============================================== */
				
				}elseif($map_layout == "fullscreen-map-top-carousel" || $map_layout == "fit-in-map-top-carousel"){
					
					if($this->items_view == "listview")
						$carousel_height = $this->horizontal_item_height + 8;
						
					elseif($this->items_view == "gridview")
						$carousel_height = $this->vertical_item_height + 8;
					
					/**
					 * Layout
					 * @updated 2.8.5 */
					 
					$output .= $this->cspm_full_map_carousel_over_layout(array(
						'map_id' => $map_id,
						'carousel' => $carousel,
						'carousel_height' => $carousel_height,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
					
	
				/* ============================ */
				/* ==== Map, Carousel over ==== */
				/* ============================ */
				
				}elseif($map_layout == "m-con"){
					
					if($this->items_view == "listview")
						$carousel_height = $this->horizontal_item_height + 8;
						
					elseif($this->items_view == "gridview")
						$carousel_height = $this->vertical_item_height + 8;
					
					$map_height = $this->layout_fixed_height . 'px';
					
					/**
					 * Layout
					 * @updated 2.8.5 */
					 
					$output .= $this->cspm_map_up_carousel_over_layout(array(
						'map_id' => $map_id,
						'map_height' => $map_height,								
						'carousel' => $carousel,
						'carousel_height' => $carousel_height,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));

	
				/* ======================================== */
				/* ==== Map, Carousel toggled from top ==== */
				/* ======================================== */
				
				}elseif($map_layout == "map-tglc-top"){
					
					$map_height = $this->layout_fixed_height . 'px';
					
					if($this->items_view == "listview")
						$carousel_height = $this->horizontal_item_height + 8;
						
					elseif($this->items_view == "gridview")
						$carousel_height = $this->vertical_item_height + 8;
					
					/**
					 * Layout
					 * @updated 2.8.5 */
						
					$output .= $this->cspm_map_toggle_carousel_top_layout(array(
						'map_id' => $map_id,
						'map_height' => $map_height,								
						'carousel' => $carousel,
						'carousel_height' => $carousel_height,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));

					
	
				/* =========================================== */
				/* ==== Map, Carousel toggled from bottom ==== */
				/* =========================================== */
				
				}elseif($map_layout == "map-tglc-bottom"){
					
					$map_height = $this->layout_fixed_height . 'px';
					
					if($this->items_view == "listview")
						$carousel_height = $this->horizontal_item_height + 8;
						
					elseif($this->items_view == "gridview")
						$carousel_height = $this->vertical_item_height + 8;
					
					/**
					 * Layout
					 * @updated 2.8.5 */
					 
					$output .= $this->cspm_map_toggle_carousel_bottom_layout(array(
						'map_id' => $map_id,
						'map_height' => $map_height,								
						'carousel' => $carousel,
						'carousel_height' => $carousel_height,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
										
				}
				
			$output .= '</div>';

			return $output;
			
		} 
		
		
		/**
		 * Build the Polyline PHP Objects
		 * @return - Array of all polylines
		 *
		 * @since 2.7 
		 * @updated 3.0 [Added clickable, URL, URL Target, Description & infowindow max-width options]
		 * @updated 4.6 [Added connected post visibity]
		 * @updated 5.2 [Added polyline points type field and seperated "polyline_path" to "lat,lng field" & post "IDs field"]
		 * @updated 5.6 [Added dashed polyline options + Converting polyline path from "lng,lat" to "lat,lng"]
		 */
		function cspm_build_polyline_objects($lines_segments){
			
			$polyline_paths = array();
			
			if(!empty($lines_segments) && is_array($lines_segments)){
				
				/**
				 * Loopt through all available Polylines */
				 
				foreach($lines_segments as $polyline_id => $single_line_segments){
					
					$line_segments_path = (array) $single_line_segments;
					
					$polyline_id = (isset($line_segments_path['polyline_name'])) ? str_replace('-', '_', sanitize_title_with_dashes($line_segments_path['polyline_name'])) : ''; //@edited 5.6
					$polyline_points_type = (isset($line_segments_path['polyline_points_type'])) ? $line_segments_path['polyline_points_type'] : 'latlng'; //@since 5.2
					
					if($polyline_points_type == 'latlng'){
						
						$polyline_latlng_order = (isset($line_segments_path['polyline_latlng_order'])) ? $line_segments_path['polyline_latlng_order'] : 'latlng'; //@since 5.2
						$polyline_path = (isset($line_segments_path['polyline_path'])) ? $line_segments_path['polyline_path'] : '';
						
						if($polyline_latlng_order == 'lngLat'){
							$polyline_path = $this->cspm_convert_lngLat_to_latLng($polyline_path);
						} //@since 5.6 | Convert "Lng,Lat" path to "Lat,Lng" path
																		
					}elseif($polyline_points_type == 'post_ids'){
						$polyline_path = (isset($line_segments_path['polyline_path_ids'])) ? $line_segments_path['polyline_path_ids'] : array();
					}else $polyline_path = ''; //@since 5.2
					
					if(!empty($polyline_id) && !empty($polyline_path)){
						
						$polyline_clickable = (isset($line_segments_path['polyline_clickable'])) ? $line_segments_path['polyline_clickable'] : 'false'; //@since 3.0
						$polyline_url = (isset($line_segments_path['polyline_url'])) ? esc_url($line_segments_path['polyline_url']) : ''; //@since 3.0
						$polyline_url_target = (isset($line_segments_path['polyline_url_target'])) ? esc_attr($line_segments_path['polyline_url_target']) : 'new_window'; //@since 3.0
						$polyline_description = (isset($line_segments_path['polyline_description'])) ? esc_html($line_segments_path['polyline_description']) : ''; //@since 3.0
						$polyline_infowindow_maxwidth = (isset($line_segments_path['polyline_infowindow_maxwidth'])) ? $line_segments_path['polyline_infowindow_maxwidth'] : '250'; //@since 3.0
						$polyline_geodesic = (isset($line_segments_path['polyline_geodesic'])) ? $line_segments_path['polyline_geodesic'] : 'false';
						$polyline_strokeColor = (isset($line_segments_path['polyline_strokeColor'])) ? $line_segments_path['polyline_strokeColor'] : '#189AC9';
						$polyline_strokeOpacity = (isset($line_segments_path['polyline_strokeOpacity'])) ? $line_segments_path['polyline_strokeOpacity'] : '1';
						$polyline_strokeWeight = (isset($line_segments_path['polyline_strokeWeight'])) ? $line_segments_path['polyline_strokeWeight'] : '2';	
						$polyline_zIndex = (isset($line_segments_path['polyline_zIndex'])) ? $line_segments_path['polyline_zIndex'] : '1';				
						$polyline_visibility = (isset($line_segments_path['polyline_visibility'])) ? $line_segments_path['polyline_visibility'] : 'true';
						$polyline_connected_post = !empty($line_segments_path['polyline_connected_post']) ? esc_attr($line_segments_path['polyline_connected_post']) : ''; //@since 4.6 
						$polyline_strokeType = (isset($line_segments_path['polyline_strokeType'])) ? $line_segments_path['polyline_strokeType'] : 'simple'; //@since 5.6 
						$polyline_icons_repeat = (isset($line_segments_path['polyline_icons_repeat'])) ? $line_segments_path['polyline_icons_repeat'] : '15'; //@since 5.6 
						
						$polyline_options_array = array(
							'polyline_id' => $polyline_id, //@since 5.6
							'clickable' => $polyline_clickable, //@since 3.0
							'url' => $polyline_url, //@since 3.0
							'url_target' => $polyline_url_target, //@since 3.0
							'description' => $polyline_description, //@since 3.0
							'infowindow_maxwidth' => $polyline_infowindow_maxwidth, //@since 3.0
							'geodesic' => $polyline_geodesic,
							'color' => $polyline_strokeColor,
							'opacity' => str_replace(',', '.', $polyline_strokeOpacity),
							'weight' => $polyline_strokeWeight,
							'zindex' => $polyline_zIndex,
							'visible' => $polyline_visibility,
							'connected_post' => $polyline_connected_post, //@since 4.6
							'stroke_type' => $polyline_strokeType, //@since 5.6
							'icons_repeat' => $polyline_icons_repeat, //@since 5.6									
						); //@since 5.6
						
						/**
						 * Check if the polyline segments are LatLng coordinate.
						 * If not, line segments must be post IDs. */
						 
						if(!is_array($polyline_path) && strpos($polyline_path, '],[') !== false){
						
							$explode_polyline_path = str_replace('],[', '|', $polyline_path);
							$line_path = explode('|', str_replace(array('[', ']'), '', $explode_polyline_path));
							
							if(is_array($line_path) && count($line_path) > 1){
								$polyline_options_array['path'] = $line_path; //@since 5.6
								$polyline_paths['latlng'][$polyline_id] = $polyline_options_array; //@edited 5.6	
							}
							
						}elseif(is_array($polyline_path)){ //@edited 5.2 (Post IDs case!)
	
							if(count($polyline_path) > 1){
								$polyline_options_array['path'] = $polyline_path; //@since 5.6
								$polyline_paths['ids'][$polyline_id] = $polyline_options_array; //@edited 5.6					
							}
							
						}else $polyline_paths['ids'][$polyline_id] = array(); //@since 5.2
					
					}
					
				}
				
			}
			
			return $polyline_paths;
			
		}
		

		/**
		 * Build the Polygon PHP Objects
		 * @return - Array of all polygons
		 * 
		 * @since 2.7 
		 * @updated 3.0 [Added clickable, URL, URL Target, Description & infowindow max-width options]
		 * @updated 4.6 [Added connected post visibility]
		 * @updated 5.2 [Added polygon points type field and seperated "polygon_paths" to "lat,lng field" & post "IDs field"]
		 * @updated 5.6 [Added polygon hover style options + Converting polygon path from "lng,lat" to "lat,lng"]
		 */		 
		function cspm_build_polygon_objects($lines_segments){
			
			$polygon_paths = array();
			
			if(!empty($lines_segments) && is_array($lines_segments)){
				
				/**
				 * Loopt through all available Polylgons */
				 
				foreach($lines_segments as $polygon_id => $single_line_segments){
										
					$line_segments_path = (array) $single_line_segments;

					$polygon_id = (isset($line_segments_path['polygon_name'])) ? str_replace('-', '_', sanitize_title_with_dashes($line_segments_path['polygon_name'])) : ''; //@edited 5.6
					$polygon_points_type = (isset($line_segments_path['polygon_points_type'])) ? $line_segments_path['polygon_points_type'] : 'latlng'; //@since 5.2
					
					if($polygon_points_type == 'latlng'){
						
						$polygon_latlng_order = (isset($line_segments_path['polygon_latlng_order'])) ? $line_segments_path['polygon_latlng_order'] : 'latlng'; //@since 5.2
						$polygon_path = (isset($line_segments_path['polygon_path'])) ? $line_segments_path['polygon_path'] : '';
						
						if($polygon_latlng_order == 'lngLat'){
							$polygon_path = $this->cspm_convert_lngLat_to_latLng($polygon_path);
						} //@since 5.6 | Convert "Lng,Lat" path to "Lat,Lng" path
												
					}elseif($polygon_points_type == 'post_ids'){
						$polygon_path = (isset($line_segments_path['polygon_path_ids'])) ? $line_segments_path['polygon_path_ids'] : array();
					}else $polygon_path = ''; //@since 5.2

					if(!empty($polygon_id) && !empty($polygon_path)){
						
						$polygon_clickable = (isset($line_segments_path['polygon_clickable'])) ? $line_segments_path['polygon_clickable'] : 'false'; //@since 3.0
						$polygon_url = (isset($line_segments_path['polygon_url'])) ? esc_url($line_segments_path['polygon_url']) : ''; //@since 3.0
						$polygon_url_target = (isset($line_segments_path['polygon_url_target'])) ? esc_attr($line_segments_path['polygon_url_target']) : 'new_window'; //@since 3.0						
						$polygon_description = (isset($line_segments_path['polygon_description'])) ? esc_html($line_segments_path['polygon_description']) : ''; //@since 3.0
						$polygon_infowindow_maxwidth = (isset($line_segments_path['polygon_infowindow_maxwidth'])) ? $line_segments_path['polygon_infowindow_maxwidth'] : '250'; //@since 3.0						
						$polygon_fillColor = (isset($line_segments_path['polygon_fillColor'])) ? $line_segments_path['polygon_fillColor'] : '#189AC9';
						$polygon_fillOpacity = (isset($line_segments_path['polygon_fillOpacity'])) ? $line_segments_path['polygon_fillOpacity'] : '1';
						$polygon_geodesic = (isset($line_segments_path['polygon_geodesic'])) ? $line_segments_path['polygon_geodesic'] : 'false';
						$polygon_strokeColor = (isset($line_segments_path['polygon_strokeColor'])) ? $line_segments_path['polygon_strokeColor'] : '#189AC9';
						$polygon_strokeOpacity = (isset($line_segments_path['polygon_strokeOpacity'])) ? $line_segments_path['polygon_strokeOpacity'] : '1';
						$polygon_strokeWeight = (isset($line_segments_path['polygon_strokeWeight'])) ? $line_segments_path['polygon_strokeWeight'] : '2';	
						$polygon_strokePosition = (isset($line_segments_path['polygon_strokePosition'])) ? $line_segments_path['polygon_strokePosition'] : 'CENTER';
						$polygon_strokeType = (isset($line_segments_path['polygon_strokeType'])) ? $line_segments_path['polygon_strokeType'] : 'simple'; //@since 5.6 
						$polygon_icons_repeat = (isset($line_segments_path['polygon_icons_repeat'])) ? $line_segments_path['polygon_icons_repeat'] : '15'; //@since 5.6 						
						$polygon_zIndex = (isset($line_segments_path['polygon_zIndex'])) ? $line_segments_path['polygon_zIndex'] : '1';				
						$polygon_visibility = (isset($line_segments_path['polygon_visibility'])) ? $line_segments_path['polygon_visibility'] : 'true';
						$polygon_connected_post = !empty($line_segments_path['polygon_connected_post']) ? esc_attr($line_segments_path['polygon_connected_post']) : ''; //@since 4.6 
						$polygon_hover_style = (isset($line_segments_path['polygon_hover_style'])) ? $line_segments_path['polygon_hover_style'] : 'false'; //@since 5.6 
						$polygon_hover_fillColor = (isset($line_segments_path['polygon_hover_fillColor'])) ? $line_segments_path['polygon_hover_fillColor'] : '#189AC9'; //@since 5.6 
						$polygon_hover_fillOpacity = (isset($line_segments_path['polygon_hover_fillOpacity'])) ? $line_segments_path['polygon_hover_fillOpacity'] : '1'; //@since 5.6 
						$polygon_hover_strokeColor = (isset($line_segments_path['polygon_hover_strokeColor'])) ? $line_segments_path['polygon_hover_strokeColor'] : '#189AC9'; //@since 5.6 
						$polygon_hover_strokeOpacity = (isset($line_segments_path['polygon_hover_strokeOpacity'])) ? $line_segments_path['polygon_hover_strokeOpacity'] : '1'; //@since 5.6 
						$polygon_hover_strokeWeight = (isset($line_segments_path['polygon_hover_strokeWeight'])) ? $line_segments_path['polygon_hover_strokeWeight'] : '2';	 //@since 5.6 
						$polygon_hover_strokePosition = (isset($line_segments_path['polygon_hover_strokePosition'])) ? $line_segments_path['polygon_hover_strokePosition'] : 'CENTER'; //@since 5.6 
						$polygon_hover_strokeType = (isset($line_segments_path['polygon_hover_strokeType'])) ? $line_segments_path['polygon_hover_strokeType'] : 'simple'; //@since 5.6 
						$polygon_hover_icons_repeat = (isset($line_segments_path['polygon_hover_icons_repeat'])) ? $line_segments_path['polygon_hover_icons_repeat'] : '15'; //@since 5.6 
						$polygon_fitBounds = (isset($line_segments_path['polygon_fitBounds'])) ? $line_segments_path['polygon_fitBounds'] : 'no'; //@since 5.6 
						
						$polygon_options_array = array(
							'polygon_id' => $polygon_id, //@since 5.6
							'fill_color' => $polygon_fillColor,
							'clickable' => $polygon_clickable, //@since 3.0
							'url' => $polygon_url, //@since 3.0
							'url_target' => $polygon_url_target, //@since 3.0
							'description' => $polygon_description, //@since 3.0	
							'infowindow_maxwidth' => $polygon_infowindow_maxwidth, //@since 3.0																							
							'fill_opacity' => str_replace(',', '.', $polygon_fillOpacity),
							'geodesic' => $polygon_geodesic,
							'stroke_color' => $polygon_strokeColor,
							'stroke_opacity' => str_replace(',', '.', $polygon_strokeOpacity),
							'stroke_position' => $polygon_strokePosition,
							'stroke_weight' => $polygon_strokeWeight,
							'stroke_type' => $polygon_strokeType, //@since 5.6
							'icons_repeat' => $polygon_icons_repeat, //@since 5.6
							'zindex' => $polygon_zIndex,
							'visible' => $polygon_visibility,
							'connected_post' => $polygon_connected_post, //@since 4.6
							'polygon_hover_style' => $polygon_hover_style, //@since 5.6
							'fill_hover_color' => $polygon_hover_fillColor, //@since 5.6
							'fill_hover_opacity' => str_replace(',', '.', $polygon_hover_fillOpacity), //@since 5.6
							'stroke_hover_color' => $polygon_hover_strokeColor, //@since 5.6
							'stroke_hover_opacity' => str_replace(',', '.', $polygon_hover_strokeOpacity), //@since 5.6
							'stroke_hover_position' => $polygon_hover_strokePosition, //@since 5.6
							'stroke_hover_weight' => $polygon_hover_strokeWeight, //@since 5.6
							'stroke_hover_type' => $polygon_hover_strokeType, //@since 5.6	
							'hover_icons_repeat' => $polygon_hover_icons_repeat, //@since 5.6
							'polygon_fitBounds' => $polygon_fitBounds, //@since 5.6
						); //@since 5.6
						
						/**
						 * Check if the polygon segments are LatLng coordinate.
						 * If not, line segments must be post IDs. */
						 
						if(!is_array($polygon_path) && strpos($polygon_path, '],[') !== false){ //@edited 5.2 (latLng case!)
						
							$explode_polygon_path = str_replace('],[', '|', $polygon_path);
							$paths = explode('|', str_replace(array('[', ']'), '', $explode_polygon_path));
							
							if(is_array($paths) && count($paths) > 2){
								$polygon_options_array['path'] = $paths; //@since 5.6
								$polygon_paths['latlng'][$polygon_id] = $polygon_options_array; //@edited 5.6	
							}
							
						}elseif(is_array($polygon_path)){ //@edited 5.2 (Post IDs case!)
	
							if(count($polygon_path) > 2){
								$polygon_options_array['path'] = $polygon_path; //@since 5.6
								$polygon_paths['ids'][$polygon_id] = $polygon_options_array; //@edited 5.6					
							}
						
						}else $polygon_paths['latlng'][$polygon_id] = array(); //@since 5.2
					
					}
					
				}
				
			}
			
			return $polygon_paths;
			
		}		
		

		/**
		 * Build the Map Holes PHP Objects
		 * @return - Array of all holes
		 * 
		 * @since 5.3
		  * @updated 5.6 [Converting hole path from "lng,lat" to "lat,lng"]
		 */		 
		function cspm_build_map_holes_objects($lines_segments){
			
			$hole_paths = array();
			
			if(!empty($lines_segments) && is_array($lines_segments)){
				
				/**
				 * Loopt through all available Holes */
				 
				foreach($lines_segments as $hole_id => $single_line_segments){
										
					$line_segments_path = (array) $single_line_segments;

					$hole_id = (isset($line_segments_path['hole_name'])) ? $line_segments_path['hole_name'] : '';
					$hole_points_type = (isset($line_segments_path['hole_points_type'])) ? $line_segments_path['hole_points_type'] : 'latlng';
					
					if($hole_points_type == 'latlng'){
						
						$hole_latlng_order = (isset($line_segments_path['hole_latlng_order'])) ? $line_segments_path['hole_latlng_order'] : 'latlng'; //@since 5.2
						$hole_path = (isset($line_segments_path['hole_path'])) ? $line_segments_path['hole_path'] : '';
						
						if($hole_latlng_order == 'lngLat'){
							$hole_path = $this->cspm_convert_lngLat_to_latLng($hole_path);
						} //@since 5.6 | Convert "Lng,Lat" path to "Lat,Lng" path
																								
					}elseif($hole_points_type == 'post_ids'){
						$hole_path = (isset($line_segments_path['hole_path_ids'])) ? $line_segments_path['hole_path_ids'] : array();
					}else $hole_path = ''; 

					if(!empty($hole_id) && !empty($hole_path)){
						
						$hole_visibility = (isset($line_segments_path['hole_visibility'])) ? $line_segments_path['hole_visibility'] : 'true';
						
						/**
						 * Check if the hole segments are LatLng coordinate.
						 * If not, line segments must be post IDs. */
						 
						if(!is_array($hole_path) && strpos($hole_path, '],[') !== false){ // latLng case!
						
							$explode_hole_path = str_replace('],[', '|', $hole_path);
							$paths = explode('|', str_replace(array('[', ']'), '', $explode_hole_path));
							
							if(is_array($paths) && count($paths) > 2){
								$hole_paths['latlng'][$hole_id] = array(
									'path' => $paths,
									'visible' => $hole_visibility,
								);	
							}
							
						}elseif(is_array($hole_path)){ // Post IDs case!
	
							if(count($hole_path) > 2){
								$hole_paths['ids'][$hole_id] = array(
									'path' => $hole_path,
									'visible' => $hole_visibility,
								);					
							}
						
						}else $hole_paths['latlng'][$hole_id] = array();
					
					}
					
				}
				
			}
			
			return $hole_paths;
			
		}		
				
				
		/**
		 * Create the infobox of the marker
		 *
		 * @since 2.5 
		 * @updated 2.7 
		 * @updated 2.8.6
		 * @updated 3.5 [added new type]
		 * @updated 3.5.1 [possibility to change infobox size]
		 * @updated 3.9
		 * @updated 4.0
		 * @updated 5.0 [added infobox title & content]
		 */
		function cspm_infobox($atts = array()){
						
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'post_id' => '',
				'carousel' => $this->show_carousel,		
				'infobox_type' => $this->infobox_type,
				'infobox_link_target' => $this->infobox_external_link,
				'infobox_width' => $this->infobox_width, //@since 4.0
				'infobox_height' => $this->infobox_height, //@since 4.0		
				'infobox_title' => $this->infobox_title, //@since 5.0		
				'infobox_external_link' => $this->infobox_external_link, //@since 5.0		
				'infobox_details' => $this->infobox_details, //@since 5.0		
			)));
			
			$move_carousel_on_infobox_hover = (in_array('infobox_hover', $this->move_carousel_on)) ? 'true' : 'false';
			
			$output = '';
			$width = $height = ''; //@since 3.5.1			
			
			/**
			 * Square & Rounded infobox */
			 
			if($infobox_type == 'square_bubble' || $infobox_type == 'rounded_bubble'){
				
				$width = apply_filters('cspm_bubble_width', '60px', $map_id); //@since 3.5.1		
				$height = apply_filters('cspm_bubble_height', '60px', $map_id); //@since 3.5.1		
			
			/**
			 * Image, title & content infobox */
			 
			}elseif($infobox_type == 'cspm_type1'){
				
				$width = apply_filters('cspm_type1_width', '380px', $map_id); //@since 3.5.1		
				$height = apply_filters('cspm_type1_height', '120px', $map_id); //@since 3.5.1		
			
			/**
			 * Image & title (vertical) infobox */
			 
			}elseif($infobox_type == 'cspm_type2'){
				
				$width = apply_filters('cspm_type2_width', '180px', $map_id); //@since 3.5.1		
				$height = apply_filters('cspm_type2_height', '180px', $map_id); //@since 3.5.1		
			
			/**
			 * Image & title (horizontal) infobox */
			 
			}elseif($infobox_type == 'cspm_type3'){
				
				$width = apply_filters('cspm_type3_width', '250px', $map_id); //@since 3.5.1		
				$height = apply_filters('cspm_type3_height', '50px', $map_id); //@since 3.5.1		
			
			/**
			 * Only title infobox */
			 
			}elseif($infobox_type == 'cspm_type4'){
				
				$width = apply_filters('cspm_type4_width', '250px', $map_id); //@since 3.5.1		
				$height = apply_filters('cspm_type4_height', '50px', $map_id); //@since 3.5.1		
			
			/**
			 * Image, title & content (biggest) infobox */
			 				
			}elseif($infobox_type == 'cspm_type5'){
				
				$width = apply_filters('cspm_type5_width', '400px', $map_id); //@since 3.5.1		
				$height = apply_filters('cspm_type5_height', '300px', $map_id); //@since 3.5.1		
			
			/**
			 * Title & content only
			 * @since 3.5 */
			 	
			}elseif($infobox_type == 'cspm_type6'){
				
				$width = apply_filters('cspm_type6_width', '380px', $map_id); //@since 3.5.1		
				$height = apply_filters('cspm_type6_height', '120px', $map_id); //@since 3.5.1		
			
			}
			 
			$style = 'style="width:'.$width.'; height:'.$height.'"';
			
			/**
			 * Override infobox width & height
			 * @since 4.0 */
						
			if(!empty($infobox_width) && !empty($infobox_height) && $infobox_width > 0 && $infobox_height > 0){
				$width = esc_attr($infobox_width);
				$height = esc_attr($infobox_height);
				$style = 'style="width:'.$width.'px; height:'.$height.'px"';				
			}
			
			/**
			 * Infobx container */
			
            $infobox_custom_class = apply_filters('cspm_infobox_custom_class', '', array(
                'map_id' => $map_id,
                'post_id' => $post_id
            )); //@since 5.6.7
            
			$output .= '<div class="cspm_infobox_container cspm_border_shadow cspm_infobox_'.$map_id.' '.$infobox_type.' '.$infobox_custom_class.'" '.$style.' data-map-id="'.$map_id.'" data-post-id="'.$post_id.'" data-move-carousel="'.$move_carousel_on_infobox_hover.'" data-infobox-link-target="'.$infobox_link_target.'">';
		
				$output .= $this->cspm_infobox_content(array(
					'map_id' => $map_id,
					'post_id' => $post_id,
					'carousel' => $carousel,		
					'infobox_type' => $infobox_type,
					'infobox_link_target' => $infobox_link_target,
					'infobox_width' => $infobox_width, //@since 4.0
					'infobox_height' => $infobox_height, //@since 4.0
					'infobox_title' => $infobox_title, //@since 5.0		
					'infobox_external_link' => $infobox_external_link, //@since 5.0		
					'infobox_details' => $infobox_details, //@since 5.0																					
				));
			
			$output .= '</div>';
			
			return $output;
			
		}
		
		
		/**
		 * Draw the infobox content
		 *
		 * @since 2.5 
		 * @updated 2.7 | 2.8.6 | 3.9 | 4.0 | 4.5.0 | 5.0
		 */
		function cspm_infobox_content($atts = array()){
						
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'post_id' => '',
				'carousel' => $this->show_carousel,		
				'infobox_type' => $this->infobox_type,
				'infobox_link_target' => $this->infobox_external_link,
				'infobox_width' => $this->infobox_width, //@since 4.0
				'infobox_height' => $this->infobox_height, //@since 4.0	
				'infobox_title' => $this->infobox_title, //@since 5.0		
				'infobox_external_link' => $this->infobox_external_link, //@since 5.0		
				'infobox_details' => $this->infobox_details, //@since 5.0													
			)));
				
			$no_title = array(); // Infoboxes to display with no title
			$no_link = array(); // Infobox to display with no link
			
			/** 
			 * Infoboxes to display with no description */
			 
			$no_description = array(
				'square_bubble', 
				'rounded_bubble', 
				'cspm_type2', 
				'cspm_type3', 
				'cspm_type4'
			);
			
			/**
			 * Infoboxes to display with no image */
			
			$no_image = array(
				'cspm_type4', 
				'cspm_type_6', //@since 3.5
			); 
			
			if(!in_array($infobox_type, $no_title)){
				
				$item_title = apply_filters(
					'cspm_custom_infobox_title', 
					stripslashes_deep(
						$this->cspm_items_title(array(
							'post_id' => $post_id, 
							'title' => $infobox_title, //@edited 5.0
							'external_link' => $infobox_external_link, //@edited 5.0
						))
					), 
					$post_id
				); 
				
			}
			
			if(!in_array($infobox_type, $no_description)){

				$item_description = apply_filters(
					'cspm_custom_infobox_description', 
					stripslashes_deep(
						$this->cspm_items_details($post_id, $infobox_details, $this->cspm_get_map_option('infobox_ellipses')) //@edited 5.0 | 5.4
					), 
					$post_id
				); 
				
			}
			
			if(!in_array($infobox_type, $no_link)) 
				$the_permalink = $this->cspm_get_permalink($post_id);
			
			if(!in_array($infobox_type, $no_image)){
				
				/**
				 * Infobox CSS style */
				 
				if($infobox_type == 'square_bubble' || $infobox_type == 'rounded_bubble'){
					
					$img_width = 50;
					$img_height = 50;
					
					if(!empty($infobox_width) && !empty($infobox_height) && $infobox_width > 0 && $infobox_height > 0){
						$img_width = esc_attr($infobox_width);
						$img_height = esc_attr($infobox_height);
					} //@since 4.0
					
					$parameter = array( 'style' => 'width:'.$img_width.'px; height:'.$img_height.'px;' );
					
				}elseif($infobox_type == 'cspm_type1'){
					
					$img_width = 160;
					$img_height = 120;
					
					if(!empty($infobox_width) && !empty($infobox_height) && $infobox_width > 0 && $infobox_height > 0){
						$img_height = esc_attr($infobox_height);
						$img_width = $img_height + 40; // 40 = The difference between the image width and height
					} //@since 4.0
										
					$parameter = array( 'style' => 'width:'.$img_width.'px; height:'.$img_height.'px;' );
					
				}elseif($infobox_type == 'cspm_type2'){
					
					$img_width = 180;
					$img_height = 132;
					
					if(!empty($infobox_width) && !empty($infobox_height) && $infobox_width > 0 && $infobox_height > 0){
						$img_width = esc_attr($infobox_height);
						$img_height = (esc_attr($infobox_height) - 48); // 48 = title height
					} //@since 4.0
										
					$parameter = array( 'style' => 'width:'.$img_width.'px; height:'.$img_height.'px;' );
					
				}elseif($infobox_type == 'cspm_type3'){
					
					$img_width = 70;
					$img_height = 50;
					
					if(!empty($infobox_width) && !empty($infobox_height) && $infobox_width > 0 && $infobox_height > 0){
						$img_height = esc_attr($infobox_height);
						$img_width = $img_height + 20; // 20 = The difference between the image width and height
					} //@since 4.0
										
					$parameter = array( 'style' => 'width:'.$img_width.'px; height:'.$img_height.'px;' );
					
				}elseif($infobox_type == 'cspm_type5'){
					
					$img_width = 70;
					$img_height = 50;
					
					$parameter = array( 'style' => 'width:'.$img_width.'px; height:'.$img_height.'px;' );
					
				}else $parameter = array();
				
				/**
				 * Get Infobox Image */
				
				$infobox_image_size = has_image_size('cspm-horizontal-thumbnail-map'.$this->map_object_id) 
					? 'cspm-horizontal-thumbnail-map'.$this->map_object_id
					: 'cspm-horizontal-thumbnail-map';
		
				if($infobox_type == 'square_bubble' || $infobox_type == 'rounded_bubble'){
					
					$infobox_thumb = $this->cspm_get_the_post_thumbnail($post_id, 'cspm-marker-thumbnail', $parameter);
					
				}elseif($infobox_type == 'cspm_type1'){
				
					$infobox_thumb = $this->cspm_get_the_post_thumbnail($post_id, $infobox_image_size, $parameter);
					
				}else $infobox_thumb = $this->cspm_get_the_post_thumbnail($post_id, $infobox_image_size, $parameter);
				
				if(empty($infobox_thumb))
					$infobox_thumb = $this->cspm_get_the_post_thumbnail($post_id, $infobox_image_size, $parameter);
			
				$post_thumbnail = apply_filters('cspm_infobox_thumb', $infobox_thumb, $post_id, $infobox_type, $parameter);
		
			}else $post_thumbnail = ''; //@since 4.5.0
		
			$target = ($infobox_link_target == 'new_window') ? ' target="_blank"' : ''; 
			
			if($infobox_link_target == 'popup'){
				$link_class = 'class="cspm_popup_single_post"'; //@since 3.6
			}elseif($infobox_link_target == 'nearby_places'){
				$link_class = 'class="cspm_popup_nearby_palces_map"'; //@since 4.6
			}else $link_class = '';
			
			$the_post_link = ($infobox_link_target == 'disable') ? $item_title : '<a href="'.$the_permalink.'" data-post-id="'.$post_id.'" title="'.$item_title.'"'.$target.' '.$link_class.'>'.$item_title.'</a>'; 						
			$output = '';

			$output .= '<div class="cspm_infobox_content_container infobox_'.$map_id.' '.$infobox_type.'" data-map-id="'.$map_id.'" data-post-id="'.$post_id.'" data-show-carousel="'.$carousel.'">';
				
				if($infobox_type == 'square_bubble' || $infobox_type == 'rounded_bubble'){
					
					$output .= '<div class="cspm_infobox_img cspm_linear_gradient_bg">';
						$output .= ($infobox_link_target != 'disable') ? '<a href="'.$the_permalink.'" data-post-id="'.$post_id.'" title="'.$item_title.'"'.$target.' '.$link_class.'>'.$post_thumbnail.'</a>' : $post_thumbnail;
					$output .= '</div>';
					
				}elseif($infobox_type == 'cspm_type1'){
					
					$output .= '<div class="cspm_infobox_img cspm_linear_gradient_bg">'.$post_thumbnail.'</div>';
					$output .= '<div class="cspm_infobox_content">';
						$output .= '<div class="title cspm_txt_rgb_hover">'.$the_post_link.'</div>';
						$output .= '<div class="description">'.$item_description.'</div>';
					$output .= '</div>';
					$output .= '<div style="clear:both"></div>';
					
				}elseif($infobox_type == 'cspm_type2'){
									
					$output .= '<div class="cspm_infobox_img cspm_linear_gradient_bg">'.$post_thumbnail.'</div>';
					$output .= '<div class="cspm_infobox_content">';
						$output .= '<div class="title cspm_txt_rgb_hover">'.$the_post_link.'</div>';
					$output .= '</div>';
					
				}elseif($infobox_type == 'cspm_type3'){
									
					$output .= '<div class="cspm_infobox_img cspm_linear_gradient_bg">'.$post_thumbnail.'</div>';
					$output .= '<div class="cspm_infobox_content">';
						$output .= '<div class="title cspm_txt_rgb_hover">'.$the_post_link.'</div>';
					$output .= '</div>';
					
				}elseif($infobox_type == 'cspm_type4'){
									
					$output .= '<div class="cspm_infobox_content">';
						$output .= '<div class="title cspm_txt_rgb_hover">'.$the_post_link.'</div>';
					$output .= '</div>';
				
				/**
				 * @since 2.7 */
					
				}elseif($infobox_type == 'cspm_type5'){
		
					$output .= '<div class="cspm_infobox_content">';
						$output .= '<div>';
							$output .= '<div class="cspm_infobox_img cspm_linear_gradient_bg">'.$post_thumbnail.'</div>';
							$output .= '<div class="title cspm_txt_rgb_hover">'.$the_post_link.'</div>';
						$output .= '</div><div style="clear:both"></div>';
						$output .= '<div class="description">';
							$output .= apply_filters('cspm_large_infobox_content', $item_description, $post_id);
						$output .= '</div>';
					$output .= '</div>';
					$output .= '<div style="clear:both"></div>';
				
				/**
				 * @since 3.5 */
					
				}elseif($infobox_type == 'cspm_type6'){
					
					$output .= '<div class="cspm_infobox_content">';
						$output .= '<div class="title cspm_txt_rgb_hover">'.$the_post_link.'</div>';
						$output .= '<div class="description">'.$item_description.'</div>';
					$output .= '</div>';
					$output .= '<div style="clear:both"></div>';
					
				}
			
			$output .= '</div>';
			
			return $output;
			
		}
		
		
		/**
		 * This will contain the infobox CSS code to be used to override the infobox width & height
		 *
		 * @since 4.0
		 */
		function cspm_infobox_size($atts = array()){
						
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'type' => '',
				'width' => '',
				'height' => '',
			)));
			
			$custom_infobox_style = '';
			
			/**
			 * Override infobox width & height
			 * @since 4.0 */
						
			if(!empty($map_id) && !empty($type) && !empty($width) && !empty($height) && $width > 0 && $height > 0){
						
				$map_id = esc_attr($map_id);
				$width = esc_attr($width);
				$height = esc_attr($height);
				$infobox_type = esc_attr($type);
				$custom_infobox_style = $infobox_width_css = $infobox_height_css = '';
			
				/**
				 * Override infobox width */
				 
				if($infobox_type == 'square_bubble' || $infobox_type == 'rounded_bubble'){ //60x60

					$infobox_width_css = '
						div.cspm_infobox_content_container.'.$infobox_type.'[data-map-id='.$map_id.']{ width: '.$width.'px !important; }
						div.cspm_infobox_content_container.'.$infobox_type.'[data-map-id='.$map_id.'] div.cspm_infobox_img{ width: '.($width - 10).'px !important; }
						div.cspm_infobox_content_container.'.$infobox_type.'[data-map-id='.$map_id.'] div.cspm_infobox_img a img{ width: '.($width-10).'px !important; }'; //@edited 5.5
						
				}elseif($infobox_type == 'cspm_type1'){ //380x120
					
					$img_width = $height + 40; // 40 = The difference between the image width and height
					
					$infobox_width_css = '
						div.cspm_infobox_content_container.cspm_type1[data-map-id='.$map_id.']{ width:'.$width.'px !important; }
						div.cspm_infobox_content_container.cspm_type1[data-map-id='.$map_id.'] div.cspm_infobox_img{ width:'.$img_width.'px !important; }
						div.cspm_infobox_content_container.cspm_type1[data-map-id='.$map_id.'] div.cspm_infobox_content{ width:'.($width - $img_width).'px !important; }';
						
				}elseif($infobox_type == 'cspm_type2'){ //180x180

					$infobox_width_css = '
						div.cspm_infobox_content_container.cspm_type2[data-map-id='.$map_id.'],
						div.cspm_infobox_content_container.cspm_type2[data-map-id='.$map_id.'] div.cspm_infobox_img,
						div.cspm_infobox_content_container.cspm_type2[data-map-id='.$map_id.'] div.cspm_infobox_content{ width:'.$width.'px !important; }';

				}elseif($infobox_type == 'cspm_type3'){ //250x50

					$img_width = $height + 20; // 20 = The difference between the image width and height
					
					$infobox_width_css = '
						div.cspm_infobox_content_container.cspm_type3[data-map-id='.$map_id.']{ width:'.$width.'px !important; }
						div.cspm_infobox_content_container.cspm_type3[data-map-id='.$map_id.'] div.cspm_infobox_img{ width:'.$img_width.'px !important; }
						div.cspm_infobox_content_container.cspm_type3[data-map-id='.$map_id.'] div.cspm_infobox_content{ width:'.($width - $img_width).'px !important; }';
					
				}elseif($infobox_type == 'cspm_type4'){ //250x50
					
					$infobox_width_css = '
						div.cspm_infobox_content_container.cspm_type4[data-map-id='.$map_id.'],
						div.cspm_infobox_content_container.cspm_type4[data-map-id='.$map_id.'] div.cspm_infobox_content{ width:'.$width.'px !important; }';
						
				}elseif($infobox_type == 'cspm_type5'){ //400x300
					
					$infobox_width_css = '
						div.cspm_infobox_content_container.cspm_type5[data-map-id='.$map_id.']{ width:'.$width.'px !important; }
						div.cspm_infobox_content_container.cspm_type5[data-map-id='.$map_id.'] div.cspm_infobox_content{ width:'.($width - 20).'px !important; }';
					
				}elseif($infobox_type == 'cspm_type6'){ //380x120

					$infobox_width_css = '
						div.cspm_infobox_content_container.cspm_type6[data-map-id='.$map_id.']{ width:'.$width.'px !important; }';
				
				}
				
				$custom_infobox_style .= $infobox_width_css;
			
				/**
				 * Override infobox height */
				
				if($infobox_type == 'square_bubble' || $infobox_type == 'rounded_bubble'){ //60x60

					$infobox_height_css = '
						div.cspm_infobox_content_container.'.$infobox_type.'[data-map-id='.$map_id.']{ height: '.$height.'px !important; }
						div.cspm_infobox_content_container.'.$infobox_type.'[data-map-id='.$map_id.'] div.cspm_infobox_img{ height: '.($height - 10).'px !important; }
						div.cspm_infobox_content_container.'.$infobox_type.'[data-map-id='.$map_id.'] div.cspm_infobox_img a img{ height: '.($height-10).'px !important; }'; //@edited 5.5

				}elseif($infobox_type == 'cspm_type1'){ //380x120
					
					$desc_height = $height - 50; // 50 = 30 (title height) + 20 (infobox content padding top & bottom)
					
					$infobox_height_css = '
						div.cspm_infobox_content_container.cspm_type1[data-map-id='.$map_id.'],
						div.cspm_infobox_content_container.cspm_type1[data-map-id='.$map_id.'] div.cspm_infobox_img,
						div.cspm_infobox_content_container.cspm_type1[data-map-id='.$map_id.'] div.cspm_infobox_content{ height:'.$height.'px !important; }
						div.cspm_infobox_content_container.cspm_type1[data-map-id='.$map_id.'] div.cspm_infobox_content div.description{ height:'.$desc_height.'px !important; }';
						
				}elseif($infobox_type == 'cspm_type2'){ //180x180

					$infobox_height_css = '
						div.cspm_infobox_content_container.cspm_type2[data-map-id='.$map_id.']{ height:'.$height.'px !important; }
						div.cspm_infobox_content_container.cspm_type2[data-map-id='.$map_id.'] div.cspm_infobox_img{ height:'.($height - 48).'px !important; }';

				}elseif($infobox_type == 'cspm_type3'){ //250x50
					
					$infobox_height_css = '
						div.cspm_infobox_content_container.cspm_type3[data-map-id='.$map_id.'],
						div.cspm_infobox_content_container.cspm_type3[data-map-id='.$map_id.'] div.cspm_infobox_img,
						div.cspm_infobox_content_container.cspm_type3[data-map-id='.$map_id.'] div.cspm_infobox_content,
						div.cspm_infobox_content_container.cspm_type3[data-map-id='.$map_id.'] div.cspm_infobox_content div.title{ height:'.$height.'px !important; }';
					
				}elseif($infobox_type == 'cspm_type4'){ //250x50
					
					$infobox_height_css = '
						div.cspm_infobox_content_container.cspm_type4[data-map-id='.$map_id.'],
						div.cspm_infobox_content_container.cspm_type4[data-map-id='.$map_id.'] div.cspm_infobox_content,
						div.cspm_infobox_content_container.cspm_type4[data-map-id='.$map_id.'] div.cspm_infobox_content div.title{ height:'.$height.'px !important; }';
						
				}elseif($infobox_type == 'cspm_type5'){ //400x300
					
					$infobox_height_css = '
						div.cspm_infobox_content_container.cspm_type5[data-map-id='.$map_id.']{ height:'.$height.'px !important; }
						div.cspm_infobox_content_container.cspm_type5[data-map-id='.$map_id.'] div.cspm_infobox_content div.description{ max-height:'.($height - 80).'px !important; }';
					
				}elseif($infobox_type == 'cspm_type6'){ //380x120

					$desc_height = $height - 50; // 50 = 30 (title height) + 20 (infobox content padding top & bottom)

					$infobox_height_css = '
						div.cspm_infobox_content_container.cspm_type6[data-map-id='.$map_id.']{ height:'.$height.'px !important; }
						div.cspm_infobox_content_container.cspm_type6[data-map-id='.$map_id.'] div.cspm_infobox_content{ height:'.$height.'px !important; }
						div.cspm_infobox_content_container.cspm_type6[data-map-id='.$map_id.'] div.cspm_infobox_content div.description{ height:'.$desc_height.'px !important; }';
				
				}
				
				$custom_infobox_style .= $infobox_height_css;
				
			}
			
			return $custom_infobox_style;

		}
		
		
		/**
		 * This will build the marker popup content
		 *
		 * @since 4.0 
		 */
		function cspm_marker_popup_content($atts = array()){
						
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'post_id' => '',
				'content_type' => $this->marker_popups_content_type,
			)));
			
			$content = '';
			$before_content = apply_filters('cspm_marker_popups_before_content', $this->marker_popups_before_content, $map_id, $post_id);
			$after_content = apply_filters('cspm_marker_popups_after_content', $this->marker_popups_after_content, $map_id, $post_id);
			
			if($this->use_marker_popups == 'no' || empty($post_id))
				return '';
				
			/**
			 * Custom field content */
			
			if($content_type == 'custom_field' && !empty($this->marker_popups_custom_field)){
				
				$post_meta = get_post_meta($post_id, $this->marker_popups_custom_field, true);
				
				if(!is_array($post_meta))
					$content = $post_meta;
			
			/**
			 * Taxonomy term(s) content */
			 		
			}elseif(in_array($content_type, array('one_term', 'all_terms')) && !empty($this->marker_popups_taxonomy)){
				
				$args = apply_filters('cspm_marker_popups_terms_order', array(
					'orderby' => 'name', 
					'order' => 'ASC', 
					'fields' => 'names'
				), $map_id, $post_id);
				
				$post_terms = (array) wp_get_post_terms($post_id, $this->marker_popups_taxonomy, $args); //@edited 5.7.1
				
				$separator = apply_filters('cspm_marker_popups_terms_separator', ', ', $map_id, $post_id);
				
				if(is_array($post_terms) && count($post_terms) > 0)
					$content = ($content_type == 'one_term') ? array_shift($post_terms) : implode($separator, $post_terms);		
			
			/**
			 * 5 stars rating */			 
			
			}elseif(in_array($content_type, array('rating_custom_field', 'rating_term'))){
				
				$rating_value = 0;
				
				if($content_type == 'rating_custom_field'){ 
					$rating_value = get_post_meta($post_id, $this->marker_popups_custom_field, true);
				}else{
					$rating_value = wp_get_post_terms($post_id, $this->marker_popups_taxonomy, array('fields' => 'slugs'));
					if(is_array($rating_value) && count($rating_value) > 0)
						$rating_value = array_shift($rating_value);
				}
				
				if(is_numeric($rating_value) && $rating_value > 0){
					
					$stars = '';

					for($i = 1; $i <= 5; $i++){
						if($i <= $rating_value){
							$stars .= '<span class="cspm_gold_star"></span>';
						}else $stars .= '<span class="cspm_grey_star"></span>';							
					}
					
					$content = '<div class="cspm_marker_popups_rating">'.$stars.'</div>';
				
				}
			
			}
			
			if(!empty($content))
				$content = str_replace('[-]', ' ', $before_content . $content . $after_content);
				
			return apply_filters('cspm_marker_popup_content', $content, $map_id, $post_id);
		
		}
		
		
		/**
		 * This will build the marker menu content
		 *
		 * @since 5.5 
		 */
		function cspm_marker_menu_content($atts = array()){
						
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'post_id' => '',
			)));
			
			$content = '';
			
			$menu_items_order = is_array($this->marker_menu_items_order) 
				? $this->marker_menu_items_order 
				: array('single_post', 'media', 'proximities', 'nearby_map');
			
			foreach($menu_items_order as $menu_item){
				
				/**
				 * Single post */
				 
				if($menu_item == 'single_post' && is_array($this->single_post_menu_item) && $this->cspm_setting_exists('visibility', $this->single_post_menu_item[0], 'show') == 'show'){
				
					$the_permalink = $this->cspm_get_permalink($post_id);
					
					$link_text = esc_html__($this->cspm_setting_exists('link_text', $this->single_post_menu_item[0], 'Details page'), 'cspm');
					$link_type = $this->cspm_setting_exists('link_type', $this->single_post_menu_item[0], 'popup');
					
					$target = ($link_type == 'new_window') ? ' target="_blank"' : ''; 					
					$link_class = ($link_type == 'popup') ? 'class="cspm_popup_single_post"' : '';
					
					$content .= '<div class="cspm_marker_menu_item">';
						$content .= '<a href="'.$the_permalink.'" data-post-id="'.$post_id.'" '.$target.' '.$link_class.'>'.$link_text.'</a>';
					$content .= '</div>';
					
				}
				
				/**
				 * Media Modal */
				 
				if($menu_item == 'media' && is_array($this->media_modal_menu_item) && $this->cspm_setting_exists('visibility', $this->media_modal_menu_item[0], 'hide') == 'show'){
	
					$link_text = esc_html__($this->cspm_setting_exists('link_text', $this->media_modal_menu_item[0], 'Media files'), 'cspm');
				
					$content .= '<div class="cspm_marker_menu_item">';
						$content .= '<a href="#" data-post-id="'.$post_id.'" target="_blank" class="cspm_popup_marker_media">'.$link_text.'</a>';			
					$content .= '</div>';
					
				}
				
				/**
				 * Nearby points of interest */
				 
				if($menu_item == 'proximities' 
					&& is_array($this->proximities_menu_item) 
					&& $this->cspm_setting_exists('visibility', $this->proximities_menu_item[0], 'hide') == 'show'
					&& $this->nearby_places_option == 'true'
				){
	
					$link_text = esc_html__($this->cspm_setting_exists('link_text', $this->proximities_menu_item[0], 'Nearby points of interest'), 'cspm');
				
					$content .= '<div class="cspm_marker_menu_item">';
						$content .= '<a href="#" data-post-id="'.$post_id.'" target="_blank" class="cspm_prepare_for_proximity">'.$link_text.'</a>';			
					$content .= '</div>';
					
				}
				
				/**
				 * Nearby Map */
				 
				if($menu_item == 'nearby_map' 
					&& is_array($this->nearby_map_menu_item) 
					&& $this->cspm_setting_exists('visibility', $this->nearby_map_menu_item[0], 'hide') == 'show'
					&& class_exists('CspmNearbyMap')
				){
	
					$link_text = esc_html__($this->cspm_setting_exists('link_text', $this->nearby_map_menu_item[0], 'Nearby Map'), 'cspm');
				
					$content .= '<div class="cspm_marker_menu_item">';
						$content .= '<a href="#" data-post-id="'.$post_id.'" target="_blank" class="cspm_popup_nearby_palces_map">'.$link_text.'</a>';			
					$content .= '</div>';
					
				}
				
				/**
				 * Directions */
				 
				if($menu_item == 'directions' && $this->cspm_setting_exists('visibility', $this->directions_menu_item[0], 'hide') == 'show'){
	
					$link_text = esc_html__($this->cspm_setting_exists('link_text', $this->directions_menu_item[0], 'Directions'), 'cspm');
					$travel_mode = $this->cspm_setting_exists('travel_mode', $this->directions_menu_item[0], 'driving');
				
					$content .= '<div class="cspm_marker_menu_item">';
						$content .= '<a href="#" data-post-id="'.$post_id.'" data-travel-mode="'.$travel_mode.'" target="_blank" class="cspm_open_directions">'.$link_text.'</a>';			
					$content .= '</div>';
					
				}
			
			}
			
			return apply_filters('cspm_marker_menu_content', $content, $map_id, $post_id);
		
		}
		
		/**
		 * This contains all the UI elements that will be displayed in the map
		 *
		 * @updated 2.8.5
		 * @updated 2.8.6
		 * @updated 3.0 [Added "Zoom to country" feature]
		 * @updated 3.2 [Added map elements display order + Error message widget]
		 * @updated 3.8 [Added post count widget]
		 */
		function cspm_map_interface_element($atts = array(), $extensions = array()){			
			
			extract( wp_parse_args( $atts, array(
				'map_id' => 'initial',		
				'carousel' => '',
				'faceted_search' => '',
				'search_form' => '',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => array(),
				'geo' => 'true',
				'infobox_type' => '', //@since 2.8.5
				'infobox_target_link' => '', //@since 2.8.6
				'extensions' => $extensions,
				'nbr_pins' => 0, //@since 3.8
			)));

			$output = '<div class="cspm_custom_controls" data-map-id="'.$map_id.'">';
			
				/**
				 * Echo the post count label
				 * @since 3.8 */
				
				if($this->show_posts_count == 'yes')				
					$output .= '<div class="number_of_posts_widget cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'">'.$this->cspm_posts_count_clause($nbr_pins, $map_id).'</div>';
											
				/**
				 * Message widgets that appears on the top right corner of the map to display "Errors/Infos/Warnings"
				 * @since 3.2 */
				
				$output .= '<div class="cspm_map_red_msg_widget cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'">'.apply_filters('cspm_red_widget_content', '', $map_id).'</div>';
				$output .= '<div class="cspm_map_green_msg_widget cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'">'.apply_filters('cspm_green_widget_content', '', $map_id).'</div>';
				$output .= '<div class="cspm_map_orange_msg_widget cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'">'.apply_filters('cspm_orange_widget_content', '', $map_id).'</div>'; //@since 3.3
				$output .= '<div class="cspm_map_blue_msg_widget cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'">'.apply_filters('cspm_blue_widget_content', '', $map_id).'</div>'; //@since 3.5
				$output .= '<div class="cspm_map_help_msg_widget cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'">'.apply_filters('cspm_help_widget_content', '', $map_id).'</div>'; //@since 3.3
            
				/**
				 * This dot appears when the pin is fired */
				 
				$output .= '<div id="pulsating_holder" class="'.$map_id.'_pulsating"><div class="dot cspm_border_hex"></div></div>';
	
				/**
				 * Zoom Control */
				 
				if($this->zoomControl == 'true' && $this->zoomControlType == 'customize'){
				
					$output .= '<div class="cspm_zoom_container">';
						$output .= '<div class="cspm_zoom_in_'.$map_id.' cspm_map_btn cspm_zoom_in_control cspm_bg_rgb_hover cspm_border_shadow cspm_border_top_radius" title="'.esc_attr__('Zoom in', 'cspm').'">';
							$output .= '<img class="cspm_svg cspm_svg_white" src="'.$this->zoom_in_icon.'" />';
						$output .= '</div>';
						$output .= '<div class="cspm_zoom_out_'.$map_id.' cspm_map_btn cspm_zoom_out_control cspm_bg_rgb_hover cspm_border_shadow cspm_border_bottom_radius" title="'.esc_attr__('Zoom out', 'cspm').'">';
							$output .= '<img class="cspm_svg cspm_svg_white" src="'.$this->zoom_out_icon.'" />';
						$output .= '</div>';
					$output .= '</div>';
				
				}
				
				/**
				 * Cluster Posts widget	*/
				 
				$output .= '<div class="cluster_posts_widget_'.$map_id.' cspm_border_shadow cspm_border_radius"><div class="cspm_infobox_spinner cspm_border_top_after_hex"></div></div>';
				
				/**
				 * [@top_positions] | Contains all available top positions (in pixels). Listed from lower to upper.
				 * A top position refers to an available position on the map where an element can be displayed.
				 */
				
				$top_positions_array = ($this->zoomControl == 'false' || $this->zoomControlType != 'customize') ? array('10px', '60px', '110px') : array('115px', '165px', '215px');
				
				$top_positions = apply_filters('cspm_map_elements_top_positions', $top_positions_array, $map_id);
					
					$last_top_position = end($top_positions);
					
					foreach($this->map_vertical_elements_order as $display_order){
						
						/** 
						 * Hook before displaying left buttons on the map 
						 * @since 3.3 */
						
						if(in_array($display_order, apply_filters('cspm_extend_left_btns', array(), $map_id))){ 
							
							$top_position = array_shift($top_positions);
							
							$output .= apply_filters('cspm_before_displaying_left_buttons', '', array(
								'display_order' => $display_order, 
								'top_position' => $top_position, 
								'map_id' => $map_id,
								'carousel' => $carousel,
								'extensions' => $extensions, //@since 4.1
							));
							
						}
						
						/**
						 * Recenter the map
						 * @since 3.0
						 * @updated 5.6 */
						
						if($display_order == 'recenter_map' && $this->recenter_map == 'true'){
								
							$top_position = array_shift($top_positions);
							
							$recenter_btn_img = apply_filters('cspm_recenter_map_btn_img', $this->recenter_icon, str_replace('map', '', $map_id));
							
							$output .= '<div class="cspm_recenter_map_btn cspm_map_btn cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" title="'.esc_attr__('Recenter the map', 'cspm').'" style="top:'.$top_position.'">';
								$output .= '<img class="cspm_svg cspm_svg_colored" src="'.$recenter_btn_img.'" />';
							$output .= '</div>';					
						
						/**
						 * Geo targeting
						 * @since 2.8
						 * @updated 5.6 */
						
						}elseif($display_order == 'geo' && $geo == 'true'){
							
							$top_position = array_shift($top_positions);
							
							$geo_btn_img = apply_filters('cspm_geo_btn_img', $this->target_icon, str_replace('map', '', $map_id));
							
							$output .= '<div class="cspm_geotarget_btn cspm_map_btn cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" data-show-user="" data-user-marker-icon="" title="'.esc_attr__('Show your position', 'cspm').'" style="top:'.$top_position.'">';
								$output .= '<img class="cspm_svg cspm_svg_colored" src="'.$geo_btn_img.'" />';
							$output .= '</div>';
						
						/**
						 * Heatmap Layer
						 * @since 3.3
						 * @updated 3.7 | 3.8 | 5.6 */
	
						}elseif($display_order == 'heatmap' && $this->heatmap_layer != 'false'){
							
							$top_position = array_shift($top_positions);
							
							$heatmap_btn_img = apply_filters('cspm_heatmap_btn_img', $this->heatmap_icon, str_replace('map', '', $map_id));
							
							$btn_status = ($this->heatmap_layer == 'true') ? 'cspm_active_btn cspm_bg_rgb_hover' : ''; //@since 3.7
							$icon_color = ($this->heatmap_layer == 'true') ? 'cspm_svg_white' : 'cspm_svg_colored'; //@since 3.8
							$toggle_markers_option = ($this->heatmap_layer == 'true') ? 'no' : 'yes'; //@since 3.7
							
							$output .= '<div class="cspm_heatmap_btn cspm_map_btn '.$btn_status.' cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" title="'.esc_attr__('Toggle the Heatmap layer', 'cspm').'" data-toggle-markers="'.$toggle_markers_option.'" style="top:'.$top_position.'">';
								$output .= '<img class="cspm_svg '.$icon_color.'" src="'.$heatmap_btn_img.'" />';
							$output .= '</div>';
							
						}
							
					}						
						
					/** 
					 * Hook after displaying left buttons on the map 
					 * @since 3.3 */
					
					if(count($top_positions) == 0)
						$top_position = (str_replace('px', '', $last_top_position) + 50).'px';
					else $top_position = array_shift($top_positions);
					 
					$output .= apply_filters('cspm_after_displaying_left_buttons', '', array(
						'top_position' => $top_position, 
						'map_id' => $map_id,
						'carousel' => $carousel,
						'extensions' => $extensions, //@since 4.1
					));
						
				/**
				 * [@left_positions] | Contains all available left positions (in pixels). Listed from left to right.
				 * A left position refers to an available position on the map where an element can be displayed.
				 */
					
				$left_positions = array_unique(apply_filters('cspm_map_elements_left_positions', array(
					'60px', 
					'110px', 
					'160px', 
					'210px', 
					'260px',
				), $map_id));
					
					$last_left_position = end($left_positions);
					
					foreach($this->map_horizontal_elements_order as $display_order){
						
						/** 
						 * Hook before displaying top buttons on the map 
						 * @since 3.3 */
						
						if(in_array($display_order, apply_filters('cspm_extend_top_btns', array(), $map_id))){ 
							
							$left_position = array_shift($left_positions);
							
							$output .= apply_filters('cspm_before_displaying_top_buttons', '', array(
								'display_order' => $display_order, 
								'left_position' => $left_position, 
								'map_id' => $map_id,
								'carousel' => $carousel,
								'extensions' => $extensions, //@since 4.1
							));
							
						}
							
						/**
						 * Zoom to country
						 * @since 3.0 */
						
						if($display_order == 'zoom_country' && $this->zoom_country_option == 'true'){
							
							$left_position = array_shift($left_positions);
									
							$output .= $this->cspm_countries_list($map_id, $left_position);
							
						/**
						 * Search form */
					
						}elseif($display_order == 'search_form' && $search_form == "yes" && $this->search_form_option == "true"){
							
							$left_position = array_shift($left_positions);
							 
							$output .= $this->cspm_search_form(array(
								'map_id' => $map_id,
								'carousel' => $carousel,
								'left_position' => $left_position,
								'extensions' => $extensions,
							));
							
						/**
						 * Faceted search
						 * @updated 2.8.5 */
				
						}elseif($display_order == 'faceted_search' && $faceted_search == "yes" && $this->faceted_search_option == "true" && $this->marker_cats_settings == "true"){
							
							$left_position = array_shift($left_positions);
							
							$output .= $this->cspm_faceted_search(array(
								'map_id' => $map_id,
								'carousel' => $carousel,
								'faceted_search_tax_slug' => $faceted_search_tax_slug,
								'faceted_search_tax_terms' => $faceted_search_tax_terms,
								'left_position' => $left_position,
								'extensions' => $extensions,
							));
							
						/**
						 * Nearby Points of interest
						 * @since 3.2 */
							
						}elseif($display_order == 'proximities' && $this->nearby_places_option == 'true'){
							
							$left_position = array_shift($left_positions);
							
							$output .= $this->cspm_get_proximities_list(array(
								'map_id' => $map_id,
								'left_position' => $left_position,
								'extensions' => $extensions,
							)); 
						
						/**
						 * KML Layers list
						 * @since 5.6 */
													
						}elseif($display_order == 'kml_layers' && $this->use_kml == 'true' && $this->kml_list == 'true'){
							
							$left_position = array_shift($left_positions);
									
							$output .= $this->cspm_kml_layers_list($map_id, $left_position);
					
						}
					
					}
					
					/** 
					 * Hook after displaying top buttons on the map 
					 * @since 3.3 */
					
					if(count($left_positions) == 0)
						$left_position = (str_replace('px', '', $last_left_position) + 50).'px';
					else $left_position = array_shift($left_positions);
					 
					$output .= apply_filters('cspm_after_displaying_top_buttons', '', array(
						'left_position' => $left_position, 
						'map_id' => $map_id,
						'carousel' => $carousel,
						'extensions' => $extensions, //@since 4.1
					));
				
				/**
				 * Map Sidebars
				 * @edited 5.6.7 */
				 
				$output .= '<div class="cspm_map_sidebars" data-map-id="'.$map_id.'">';
					$output .= '<div class="cspm_sidebar top" data-map-id="'.$map_id.'" data-content_id=""></div>';
					$output .= '<div class="cspm_sidebar right" data-map-id="'.$map_id.'" data-content_id=""></div>';
					$output .= '<div class="cspm_sidebar bottom" data-map-id="'.$map_id.'" data-content_id=""></div>';
					$output .= '<div class="cspm_sidebar left" data-map-id="'.$map_id.'" data-content_id=""></div>';
                    $output .= apply_filters('cspm_custom_sidebars', '', array(
                        'map_id' => $map_id,
                        'carousel' => $carousel,
                        'extensions' => $extensions,
                    ));
				$output .= '</div>';
					
				/**
				 * An available hook to display extra elements on the map 
				 * @since 2.8.5 */
					
				$output .= apply_filters('cspm_add_map_interface_element', '', array(
					'map_id' => $map_id,
					'carousel' => $carousel,
					'extensions' => $extensions, //@since 4.1
				));
			
			$output .= '</div>';
										
			return $output;
							
		}
		
		/**
		 * Map-up, Carousel-down layout
		 * 
		 * @updated 2.8.5 
		 * @updated 2.8.6
		 */		 
		function cspm_map_up_carousel_down_layout($atts = array()){
				
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'map_height' => '',
				'carousel' => 'yes',		
				'carousel_height' => '',				
				'faceted_search' => 'yes',
				'search_form' => 'yes',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => array(),
				'geo' => 'true',
				'infobox_type' => '', //@since 2.8.5
				'infobox_target_link' => '', //@since 2.8.6
				'nbr_pins' => 0, //@since 3.8
			)));
		
			$output = '<div class="cspm-row" style="margin:0; padding:0;">';
					
				/**
				 * An available hook to display extra elements before the map container
				 * @since 2.8.5 */
				
				$output .= apply_filters('cspm_before_map', '');
								
				/**
				 * Map */
				 
				$output .= '<div class="'.apply_filters('cspm_map_container_classes', 'cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12').'" style="position:relative; overflow:hidden; margin:0; padding:0;">';
				
					/**
					 * Interface elements
					 * @updated 2.8.5 
					 * @updated 2.8.6 */
					 
					$output .= $this->cspm_map_interface_element(array(
						'map_id' => $map_id,		
						'carousel' => $carousel,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));

					$output .= '<div id="codespacing_progress_map_div_'.$map_id.'" data-map-id="'.$map_id.'" style="height:'.$map_height.';"></div>';
				
				$output .= '</div>';
					
				/**
				 * An available hook to display extra elements after the map container
				 * @since 2.8.5 */
				
				$output .= apply_filters('cspm_after_map', '');
								
			$output .= '</div>';
				
			$output .= '<div class="cspm-row" style="margin:0; padding:0">';
			
				/**
				 * An available hook to display extra elements before the carousel
				 * @since 2.8.5 */
				
				$output .= apply_filters('cspm_before_carousel', '');
										
				/**
				 * Carousel */
				 
				if($this->show_carousel == 'true' && $carousel == "yes" && !$this->cspm_hide_carousel_from_mobile()){
					
					$output .= '<div id="cspm_carousel_container" data-map-id="'.$map_id.'" class="cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12" style="margin:0; padding:0; height:auto;">';
					
						$output .= '<ul id="cspm_carousel_'.$map_id.'" data-map-id="'.$map_id.'" class="jcarousel-skin-default" style="height:'.$carousel_height.'px;"></ul>'; //@edited 5.6.3
					
					$output .= '</div>';
				
				}
			
				/**
				 * An available hook to display extra elements after the carousel
				 * @since 2.8.5 */
				
				$output .= apply_filters('cspm_after_carousel', '');
			
			$output .= '</div>';		
			
			return $output;
			
		}


		/**
		 * Map-down, Carousel-up layout
		 * 
		 * @updated 2.8.5 
		 */		 
		function cspm_map_down_carousel_up_layout($atts = array()){
				
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'map_height' => '',						
				'carousel' => 'yes',
				'carousel_height' => '',
				'faceted_search' => 'yes',
				'search_form' => 'yes',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => array(),
				'geo' => 'true',
				'infobox_type' => '', //@since 2.8.5
				'infobox_target_link' => '', //@since 2.8.6
				'nbr_pins' => 0, //@since 3.8
			)));
		
			$output = '<div class="cspm-row" style="margin:0; padding:0">';
			
				/**
				 * An available hook to display extra elements before the carousel
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_before_carousel', '');
										
				/**
				 * Carousel */
				 
				if($this->show_carousel == 'true' && $carousel == "yes" && !$this->cspm_hide_carousel_from_mobile()){

					$output .= '<div id="cspm_carousel_container" data-map-id="'.$map_id.'" class="cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12" style="margin:0; padding:0; height:auto;">';
						
						$output .= '<ul id="cspm_carousel_'.$map_id.'" data-map-id="'.$map_id.'" class="jcarousel-skin-default" style="height:'.$carousel_height.'px;"></ul>'; //@edited 5.6.3
					
					$output .= '</div>';
					
				}
			
				/**
				 * An available hook to display extra elements after the carousel
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_after_carousel', '');
										
			$output .= '</div>';	
			
			$output .= '<div class="cspm-row" style="margin:0; padding:0">';
		
				/**
				 * An available hook to display extra elements before the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_before_map', '');
																				
				/**
				 * Map */
				 
				$output .= '<div class="'.apply_filters('cspm_map_container_classes', 'cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12').'" style="position:relative; overflow:hidden; margin:0; padding:0;">';
		
					/** 
					 * Interface elements
					 * @updated 2.8.5 
					 * @updated 2.8.6 */
					 
					$output .= $this->cspm_map_interface_element(array(
						'map_id' => $map_id,		
						'carousel' => $carousel,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
					
					$output .= '<div id="codespacing_progress_map_div_'.$map_id.'" style="height:'.$map_height.';"></div>';
				
				$output .= '</div>';
			
				/**
				 * An available hook to display extra elements after the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_after_map', '');
																		
			$output .= '</div>';
			
			return $output;
			
		}
		
		
		/**
		 * Map-right, Carousel-left layout 
		 * 
		 * @updated 2.8.5
		 * @updated 2.8.6 
		 */		 
		function cspm_map_right_carousel_left_layout($atts = array()){
				

			extract( wp_parse_args( $atts, array(
				'map_id' => '',				
				'carousel' => 'yes',
				'carousel_width' => '',
				'faceted_search' => 'yes',
				'search_form' => 'yes',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => array(),
				'geo' => 'true',
				'infobox_type' => '', //@since 2.8.5
				'infobox_target_link' => '', //@since 2.8.6
				'nbr_pins' => 0, //@since 3.8
			)));
		
			$output = '<div style="width:100%; height:100%; margin:0; padding:0;">';
				
				if($this->show_carousel == 'true' && $carousel == "yes" && !$this->cspm_hide_carousel_from_mobile()){
					
					$map_width = 'auto';
					$margin_left = 'margin-left:'.($carousel_width+20).'px;';
					
				}else{
					
					$map_width = '100%';
					$margin_left = '';
					
				}
			
				/**
				 * An available hook to display extra elements before the carousel
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_before_carousel', '');
														
				if($this->show_carousel == 'true' && $carousel == "yes" && !$this->cspm_hide_carousel_from_mobile()){ 
					
					/**
					 * Carousel */
					 
					$output .= '<div id="cspm_carousel_container" data-map-id="'.$map_id.'" style="position:absolute; width:auto; height:auto;">';
						
						$output .= '<ul id="cspm_carousel_'.$map_id.'" data-map-id="'.$map_id.'" class="jcarousel-skin-default" style="width:'.$carousel_width.'px; height:'.$this->layout_fixed_height.'px"></ul>'; //@edited 5.6.3
					
					$output .= '</div>';
					
				}
			
				/**
				 * An available hook to display extra elements before the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_before_map', '');
														
				/**
				 * Map */
				 
				$output .= '<div style="height:'.$this->layout_fixed_height.'px; width:'.$map_width.'; position:relative; overflow:hidden; '.$margin_left.'">';
				
					/**
					 * Interface elements
					 * @updated 2.8.5 
					 * @updated 2.8.6 */
					 
					$output .= $this->cspm_map_interface_element(array(
						'map_id' => $map_id,		
						'carousel' => $carousel,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
					
					$output .= '<div id="codespacing_progress_map_div_'.$map_id.'" class="gmap3" style="width:100%; height:100%"></div>';
				
				$output .= '</div>';
			
				/**
				 * An available hook to display extra elements after the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_after_map', '');
										
			$output .= '</div>';
			
			return $output;
			
		}
		
		
		/**
		 * Map-left, Carousel-right layout 
		 * 
		 * @updated 2.8.5 
		 * @updated 2.8.6
		 */		 
		function cspm_map_left_carousel_right_layout($atts = array()){
				
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'carousel' => 'yes',
				'carousel_width' => '',				
				'faceted_search' => 'yes',
				'search_form' => 'yes',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => array(),
				'geo' => 'true',
				'infobox_type' => '', //@since 2.8.5
				'infobox_target_link' => '', //@since 2.8.6
				'nbr_pins' => 0, //@since 3.8
			)));
			
			$output = '<div style="width:100%; height:100%; margin:0; padding:0;">';
				
				if($this->show_carousel == 'true' && $carousel == "yes" && !$this->cspm_hide_carousel_from_mobile()){
					
					$map_width = 'auto';
					$margin_right = 'margin-right:'.($carousel_width+20).'px;';
					
				}else{
					
					$map_width = '100%';
					$margin_right = '';
					
				}
			
				/**
				 * An available hook to display extra elements before the carousel
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_before_carousel', '');
										
				if($this->show_carousel == 'true' && $carousel == "yes" && !$this->cspm_hide_carousel_from_mobile()){
					
					/**
					 * Carousel */
					 
					$output .= '<div id="cspm_carousel_container" data-map-id="'.$map_id.'" style="float:right; width:auto; height:auto;">';
						
						$output .= '<ul id="cspm_carousel_'.$map_id.'" data-map-id="'.$map_id.'" class="jcarousel-skin-default" style="width:'.$carousel_width.'px; height:'.$this->layout_fixed_height.'px"></ul>'; //@edited 5.6.3
					
					$output .= '</div>';
					
				}
			
				/**
				 * An available hook to display extra elements before the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_before_map', '');
										
				/**
				 * Map */
				 
				$output .= '<div style="height:'.$this->layout_fixed_height.'px; width:'.$map_width.'; position:relative; overflow:hidden; '.$margin_right.'">';
				
					/**
					 * Interface elements
					 * @updated 2.8.5 
					 * @updated 2.8.6 */
					 
					$output .= $this->cspm_map_interface_element(array(
						'map_id' => $map_id,		
						'carousel' => $carousel,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
					
					$output .= '<div id="codespacing_progress_map_div_'.$map_id.'" class="gmap3" style="width:100%; height:100%"></div>';
				
				$output .= '</div>';
			
				/**
				 * An available hook to display extra elements after the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_after_map', '');
										
			$output .= '</div>';
			
			return $output;
			
		}
		
		
		/**
		 * Fullscreen & Fit in map
		 *
		 * @since 2.0
		 * @updated 2.8.5 
		 * @updated 2.8.6
		 */		 
		function cspm_only_map_layout($atts = array()){
				
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'faceted_search' => 'yes',
				'search_form' => 'yes',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => array(),
				'geo' => 'true',
				'infobox_type' => '', //@since 2.8.5
				'infobox_target_link' => '', //@since 2.8.6
				'nbr_pins' => 0, //@since 3.8
			)));
		
			$output = '';
			
			/**
			 * An available hook to display extra elements before the map container
			 * @since 2.8.5 */
			
			$output .= apply_filters('cspm_before_map', '');
										
			/**
			 * Interface elements
			 * @updated 2.8.5
			 * @updated 2.8.6 */
			 
			$output .= $this->cspm_map_interface_element(array(
				'map_id' => $map_id,		
				'carousel' => 'no',
				'faceted_search' => $faceted_search,
				'search_form' => $search_form,
				'faceted_search_tax_slug' => $faceted_search_tax_slug,
				'faceted_search_tax_terms' => $faceted_search_tax_terms,
				'geo' => $geo,
				'infobox_type' => $infobox_type, //@since 2.8.5
				'infobox_target_link' => $infobox_target_link, //@since 2.8.6
				'nbr_pins' => $nbr_pins, //@since 3.8
			));
					
			/**
			 * Map */
			 
			$output .= '<div id="codespacing_progress_map_div_'.$map_id.'" class="gmap3" style="width:100%; height:100%"></div>';
			
			/**
			 * An available hook to display extra elements after the map container
			 * @since 2.8.5 */
			
			$output .= apply_filters('cspm_after_map', '');
										
			return $output;
			
		}
		
		
		/** 
		 * Map-up, Carousel-over layout
		 *
		 * @since 2.3
		 * @updated 2.8.5
		 * @updated 2.8.6
		 */		 
		function cspm_map_up_carousel_over_layout($atts = array()){
				
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'map_height' => '',		
				'carousel_height' => '',
				'carousel' => 'yes',
				'faceted_search' => 'yes',
				'search_form' => 'yes',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => array(),
				'geo' => 'true',
				'infobox_type' => '', //@since 2.8.5
				'infobox_target_link' => '', //@since 2.8.6
				'nbr_pins' => 0, //@since 3.8
			)));
		
			$output = '<div class="cspm-row" style="margin:0; padding:0">';
			
				/**
				 * An available hook to display extra elements before the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_before_map', '');
										
				/**
				 * Map Container */
				 
				$output .= '<div class="'.apply_filters('cspm_map_container_classes', 'cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12').'" style="position:relative; margin:0; padding:0;">';
										
					/**
					 * Interface elements
					 * @updated 2.8.5 
					 * @updated 2.8.6 */
					 
					$output .= $this->cspm_map_interface_element(array(
						'map_id' => $map_id,		
						'carousel' => $carousel,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
					
					/**
					 * Carousel */
					 
					if($this->show_carousel == 'true' && $carousel == "yes" && !$this->cspm_hide_carousel_from_mobile()){
						
						$output .= '<div id="cspm_carousel_container" class="cspm_carousel_on_top" data-map-id="'.$map_id.'">';
						
							$output .= '<ul id="cspm_carousel_'.$map_id.'" data-map-id="'.$map_id.'" class="jcarousel-skin-default" style="height:'.$carousel_height.'px;"></ul>'; //@edited 5.6.3
						
						$output .= '</div>';
					
					}
					
					/**
					 * Map */
					 
					$output .= '<div id="codespacing_progress_map_div_'.$map_id.'" style="height:'.$map_height.'"></div>';
				
				$output .= '</div>';
			
				/**
				 * An available hook to display extra elements after the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_after_map', '');
										
			$output .= '</div>';
			
			return $output;
			
		}
		
		
		/**
		 * Fullscreen & Fit in map with carousel
		 *
		 * @since 2.3
		 * @updated 2.8.5 
		 * @updated 2.8.6
		 */		 
		function cspm_full_map_carousel_over_layout($atts = array()){
				
			extract( wp_parse_args( $atts, array(
				'map_id' => '',				
				'carousel' => 'yes',
				'carousel_height' => '',
				'faceted_search' => 'yes',
				'search_form' => 'yes',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => array(),
				'geo' => 'true',
				'infobox_type' => '', //@since 2.8.5
				'infobox_target_link' => '', //@since 2.8.6
				'nbr_pins' => 0, //@since 3.8
			)));
		
			$output = '';
										
			/**
			 * Interface elements
			 * @updated 2.8.5 
			 * @updated 2.8.6 */
			 
			$output .= $this->cspm_map_interface_element(array(
				'map_id' => $map_id,		
				'carousel' => $carousel,
				'faceted_search' => $faceted_search,
				'search_form' => $search_form,
				'faceted_search_tax_slug' => $faceted_search_tax_slug,
				'faceted_search_tax_terms' => $faceted_search_tax_terms,
				'geo' => $geo,
				'infobox_type' => $infobox_type, //@since 2.8.5
				'infobox_target_link' => $infobox_target_link, //@since 2.8.6
				'nbr_pins' => $nbr_pins, //@since 3.8
			));
					
			/**
			 * Carousel */
			 
			if($this->show_carousel == 'true' && $carousel == "yes" && !$this->cspm_hide_carousel_from_mobile()){
				
				$output .= '<div id="cspm_carousel_container" class="cspm_carousel_on_top col cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12" data-map-id="'.$map_id.'">';
				
					$output .= '<ul id="cspm_carousel_'.$map_id.'" data-map-id="'.$map_id.'" class="jcarousel-skin-default" style="height:'.$carousel_height.'px;"></ul>'; //@edited 5.6.3
				
				$output .= '</div>';
			
			}
			
			/**
			 * An available hook to display extra elements before the map container
			 * @since 2.8.5 */
			
			$output .= apply_filters('cspm_before_map', '');
										
			/**
			 * Map */
			 
			$output .= '<div id="codespacing_progress_map_div_'.$map_id.'" class="gmap3" style="width:100%; height:100%"></div>';
			
			/**
			 * An available hook to display extra elements after the map container
			 * @since 2.8.5 */
			
			$output .= apply_filters('cspm_after_map', '');
										
			return $output;
			
		}
		
		
		/**
		 * Map, Toggle-Carousel-top layout
		 *
		 * @since 2.4
		 * @updated 2.8.5 
		 * @updated 2.8.6
		 */		 
		function cspm_map_toggle_carousel_top_layout($atts = array()){
				
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'map_height' => '',
				'carousel' => 'yes',	
				'carousel_height' => '',				
				'faceted_search' => 'yes',
				'search_form' => 'yes',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => array(),
				'geo' => 'true',
				'infobox_type' => '', //@since 2.8.5
				'infobox_target_link' => '', //@since 2.8.6
				'nbr_pins' => 0, //@since 3.8
			)));
		
			$output = '<div class="cspm-row" style="margin:0; padding:0">';
			
				/**
				 * An available hook to display extra elements before the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_before_map', '');
										
				/**
				 * Map Container */
				 
				$output .= '<div class="'.apply_filters('cspm_map_container_classes', 'cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12').'" style="position:relative; margin:0; padding:0;">';
											
					/**
					 * Interface elements
					 * @updated 2.8.5 */
					 
					$output .= $this->cspm_map_interface_element(array(
						'map_id' => $map_id,		
						'carousel' => $carousel,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
					
					/**
					 * Carousel */
					 
					if($this->show_carousel == 'true' && $carousel == "yes" && !$this->cspm_hide_carousel_from_mobile()){
						
						$output .= '<div class="cspm_toggle_carousel_horizontal_top" style="width:100%;">';
							
							$output .= '<div id="cspm_carousel_container" data-map-id="'.$map_id.'" style="display:none;">';
								
								$output .= '<ul id="cspm_carousel_'.$map_id.'" data-map-id="'.$map_id.'" class="jcarousel-skin-default" style="height:'.$carousel_height.'px;"></ul>'; //@edited 5.6.3
								 
							$output .= '</div>';
								
							$output .= '<div class="toggle-carousel-top cspm_bg_hex_hover cspm_border_bottom_radius cspm_border_shadow" data-map-id="'.$map_id.'">'.apply_filters('cspm_toggle_carousel_text', esc_html__('Toggle carousel', 'cspm')).'</div>';
							
						$output .= '</div>';
					
					}
					
					/**
					 * Map */
					 
					$output .= '<div id="codespacing_progress_map_div_'.$map_id.'" style="height:'.$map_height.'"></div>';
				
				$output .= '</div>';
			
				/**
				 * An available hook to display extra elements after the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_after_map', '');
										
			$output .= '</div>';
			
			return $output;
			
		}
		
		
		/**
		 * Map, Toggle-Carousel-bottom layout
		 *
		 * @since 2.4
		 * @updated 2.8.5 
		 * @updated 2.8.6
		 */		 
		function cspm_map_toggle_carousel_bottom_layout($atts = array()){
				
			extract( wp_parse_args( $atts, array(
				'map_id' => '',
				'map_height' => '',
				'carousel' => 'yes',		
				'carousel_height' => '',				
				'faceted_search' => 'yes',
				'search_form' => 'yes',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => array(),
				'geo' => 'true',
				'infobox_type' => '', //@since 2.8.5
				'infobox_target_link' => '', //@since 2.8.6
				'nbr_pins' => 0, //@since 3.8
			)));
		
			$output = '<div class="cspm-row" style="margin:0; padding:0">';
			
				/**
				 * An available hook to display extra elements before the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_before_map', '');
														
				/**
				 * Map Container */
				 
				$output .= '<div class="'.apply_filters('cspm_map_container_classes', 'cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12').'" style="position:relative; margin:0; padding:0;">';
										
					/**
					 * Interface elements
					 * @updated 2.8.5 */
					 
					$output .= $this->cspm_map_interface_element(array(
						'map_id' => $map_id,		
						'carousel' => $carousel,
						'faceted_search' => $faceted_search,
						'search_form' => $search_form,
						'faceted_search_tax_slug' => $faceted_search_tax_slug,
						'faceted_search_tax_terms' => $faceted_search_tax_terms,
						'geo' => $geo,
						'infobox_type' => $infobox_type, //@since 2.8.5
						'infobox_target_link' => $infobox_target_link, //@since 2.8.6
						'nbr_pins' => $nbr_pins, //@since 3.8
					));
					
					/**
					 * Map */
					 
					$output .= '<div id="codespacing_progress_map_div_'.$map_id.'" style="height:'.$map_height.';"></div>';
					
					/**
					 * Carousel */
					 
					if($this->show_carousel == 'true' && $carousel == "yes" && !$this->cspm_hide_carousel_from_mobile()){
						
						$output .= '<div class="cspm_toggle_carousel_horizontal_bottom" style="width:100%;">';
								
							$output .= '<div class="toggle-carousel-bottom cspm_bg_hex_hover cspm_border_top_radius cspm_border_shadow" data-map-id="'.$map_id.'">'.apply_filters('cspm_toggle_carousel_text', esc_html__('Toggle carousel', 'cspm')).'</div>';
							
							$output .= '<div id="cspm_carousel_container" data-map-id="'.$map_id.'" style="display:none;">';
								
								$output .= '<ul id="cspm_carousel_'.$map_id.'" data-map-id="'.$map_id.'" class="jcarousel-skin-default" style="height:'.$carousel_height.'px;"></ul>'; //@edited 5.6.3
								 
							$output .= '</div>';
						
						$output .= '</div>';
					
					}
				
				$output .= '</div>';
			
				/**
				 * An available hook to display extra elements after the map container
				 * @since 2.8.5 */
			 	
				$output .= apply_filters('cspm_after_map', '');
														
			$output .= '</div>';
			
			return $output;
			
		}

				
		/**
		 * The widget that contains the posts count
		 *
		 * @since 2.1
		 * @updated 3.2 [replaced cspm_wpml_get_string() by esc_html__()]
		 * @updated 3.3
		 */
		function cspm_posts_count_clause($count, $map_id){
			
			$posts_count_clause = $this->posts_count_clause;
			
			$count = ($this->map_type == 'search_map') ? 0 : $count; //@since 3.3
			
			return str_replace('[posts_count]', '<span class="the_count_'.$map_id.'">'.$count.'</span>', esc_html__($posts_count_clause, 'cspm'));
			
		}
		
		
		/**
		 * Get marker image when markers are displayed by category
		 *
		 * @since 2.4
		 * @updated 2.8
		 * @updated 3.0 [Added single map option]
		 * @updated 4.0 [Added marker width & height]
		 */
		function cspm_get_marker_img($atts = array()){
		
			$defaults = array(
				
				'post_id' => '',
				
				/**
				 * Define if the map is a single map or not
				 * @since 3.0 */
				 
				'is_single' => false,
				
				/**
				 * The taxonomy name to which a post is connected via a term */
				 
				'tax_name' => '',
				
				/**
				 * The term ID to which a post is connected */
				 
				'term_id' => '',
				
				'default_marker_icon' => $this->marker_icon,				
				'marker_width' => '', //@since 4.0
				'marker_height' => '', //@since 4.0
			);
			
			extract(wp_parse_args($atts, $defaults));
				
			$marker_image = $default_marker_icon;
						
			/**
			 * The Primary marker is the one set for a post in the "Add/Edit post" page.
			 * If a post have a primary marker, this means that we want to display this marker no matter what marker is used for the taxonomy term. */
			 
			$primary_marker_img = (!empty($post_id)) ? get_post_meta($post_id, CSPM_MARKER_ICON_FIELD, true) : '';
						
			$single_post_img_only = get_post_meta($post_id, CSPM_SINGLE_POST_IMG_ONLY_FIELD, true);

			if(!empty($primary_marker_img) && ($is_single || (!$is_single && $single_post_img_only != 'yes'))){
				
				$marker_image = $primary_marker_img;
				$marker_width = get_post_meta($post_id, $this->metafield_prefix . '_marker_icon_width', true); //@since 4.0
				$marker_height = get_post_meta($post_id, $this->metafield_prefix . '_marker_icon_height', true); //@since 4.0				
		
			/**
			 * Check if the user wants to display custom marker for each category of post */
			 
			}elseif($this->marker_cats_settings == 'true'){
								
				$marker_categories_images = $this->cspm_get_map_option('marker_categories_images', array());				
				$marker_categories_images_array = array();
				
				foreach($marker_categories_images as $marker_category_img){				
					if(isset($marker_category_img['marker_img_category_'.$tax_name]))
						$marker_categories_images_array[$marker_category_img['marker_img_category_'.$tax_name]] = $marker_category_img;							
				}
					
				if(is_array($marker_categories_images_array) && count($marker_categories_images_array) > 0){
					
					if(isset($marker_categories_images_array[$term_id])){
						
						$marker_object = $marker_categories_images_array[$term_id];
						
						if(isset($marker_object['marker_img_path_'.$tax_name])){
							
							$marker_image = $marker_object['marker_img_path_'.$tax_name];
							
							if(isset($marker_object['marker_img_width_'.$tax_name], $marker_object['marker_img_height_'.$tax_name])){
								$marker_width = $marker_object['marker_img_width_'.$tax_name]; //@since 4.0
								$marker_height = $marker_object['marker_img_height_'.$tax_name]; //@since 4.0
							}
							
						}
							
					}
					
				}
				
			}
				
			return array(
				'image' => $marker_image,
				'size' => array(
					'width' => $marker_width,
					'height' => $marker_height,
				)
			);
			
		}
		
		
		/**
		 * Get image path from its URL
		 *
		 * @since 2.4
		 * @Updated 2.7.2 
		 */
		function cspm_get_image_path_from_url($url){
			
			if(!empty($url)){
				
				/**
				 * [@wp_content_directory_url] & [@wp_content_directory_name]
				 * Get the wp-content folder name dynamicaly as some users may change its name to a custom name
				 * @since 2.7.2 */
				 
				$wp_content_directory_url = explode('/', WP_CONTENT_URL);
				$wp_content_directory_name = is_array($wp_content_directory_url) ? array_pop($wp_content_directory_url) : 'wp-content';
				
				$exploded_url = explode($wp_content_directory_name, $url);
				
				if(isset($exploded_url[1])){
					return WP_CONTENT_DIR.$exploded_url[1];
				}else return false;		
				
			}else return false;
				
		}
		
		
		/**
		 * Get image size by image URL
		 * 
		 * return "Width", then, "Height".
		 *
		 * @since 2.4
		 * @updated 4.0
		 */
		function cspm_get_image_size($atts = array()){
						
			extract( wp_parse_args( $atts, array(
				'path' => '',
				'retina' => 'false',				
				'default_width' => '',
				'default_height' => '',			
			)));
			
			if(!empty($path) && file_exists($path)){
				
				/**
				 * If the width & height were specified */
				 
				if(!empty($default_height) && !empty($default_width) && $default_height > 0 && $default_width > 0){
					
					return $default_width.'x'.$default_height;
				
				/**
				 * Automatically calculate the width & height (works with PNG/JPEG/GIF only) */
				 	
				}else{
					
					$img_size = getimagesize($path);
					
					if(isset($img_size[0]) && isset($img_size[1])){
						
						return $retina == 'false' ? $img_size[0].'x'.$img_size[1] : ($img_size[0]/2).'x'.($img_size[1]/2);
						
					}else return '';
					
				}
	
			}else return '';
			
		}
		
		
		/**
		 * Load the markers clustred inside a small area on the map
		 *
		 * @since 2.5
		 * updated 2.8.6 | 5.0
		 */
		function cspm_load_clustred_markers_list(){
			
			$post_ids = $_POST['post_ids'];
			$light_map = esc_attr($_POST['light_map']); // Whether we use the carousel or not
			
			/**
			 * Reload map settings */
			 
			$this->map_object_id = esc_attr($_POST['map_object_id']);
			$this->map_settings = $_POST['map_settings'];
				
			$this->items_title = $this->cspm_get_map_option('infobox_title'); //@edited 5.0
			$this->infobox_external_link = $this->cspm_get_map_option('infobox_external_link');
			
			/**
			 * List */
			 		
			$output = '<ul>';
			
				foreach($post_ids as $post_id){
					
					$item_title = apply_filters(
						'cspm_custom_item_title', 
						stripslashes_deep(
							$this->cspm_items_title(array(
								'post_id' => $post_id, 
								'title' => $this->items_title, //@edited 5.0
								'external_link' => $this->infobox_external_link, //@edited 5.0
							))
						), 
						$post_id
					); 
					
					$parameter = array(
						'style' => "width:70px; height:50px;"
					);
				
					$infobox_image_size = has_image_size('cspm-horizontal-thumbnail-map'.$this->map_object_id) 
						? 'cspm-horizontal-thumbnail-map'.$this->map_object_id
						: 'cspm-horizontal-thumbnail-map';
					
					$post_thumbnail = $this->cspm_get_the_post_thumbnail($post_id, $infobox_image_size, $parameter);
					$the_permalink  = ($light_map == 'true') ? ' href="'.$this->cspm_get_permalink($post_id).'"' : '';
					$the_permalink .= ($light_map == 'true' && $this->infobox_external_link == 'new_window') ? ' target="_blank"' : ''; //@edited 5.0
			
					if($this->infobox_external_link == 'popup' && $light_map == 'true'){
						$link_class = 'class="cspm_popup_single_post"'; //@since 3.6
					}elseif($this->infobox_external_link == 'nearby_places' && $light_map == 'true'){
						$link_class = 'class="cspm_popup_nearby_palces_map"'; //@since 4.6
					}else $link_class = '';
								
					$output .= '<li id="'.$post_id.'" data-post-id="'.$post_id.'" class="cspm_border_shadow cspm_border_radius cspm_txt_hex_hover">';
						$output .= $post_thumbnail;
						$output .= '<a'.$the_permalink.' title="'.$item_title.'" data-post-id="'.$post_id.'" '.$link_class.'>'.$item_title.'</a>';
						$output .= '<div style="clear:both"></div>';
					$output .= '</li>';
		
				}
			
			$output .= '</ul>';
			
			die($output);
			
		}
		
		
		/**
		 * Create the faceted search form to filter markers and posts
		 *
		 * @since 2.1
		 * @updated 2.8.5
		 * @updated 3.0 [added $left_position]
		 */
		function cspm_faceted_search($atts = array()){

			if(class_exists('CSProgressMap'))
				$CSProgressMap = CSProgressMap::this();
			
			$defaults = array(
				'map_id' => '',
				'carousel' => 'yes',
				'faceted_search_tax_slug' => '',
				'faceted_search_tax_terms' => '',
				'left_position' => '',
				'extensions' => array(),
			);
			
			extract(wp_parse_args($atts, $defaults));
				
			$output = '';
				
			$faceted_search_icon = apply_filters('cspm_faceted_search_icon', $this->faceted_search_icon, str_replace('map', '', $map_id));
							
			$output .= '<div class="faceted_search_btn cspm_map_btn cspm_top_btn cspm_expand_map_btn cspm_border_shadow cspm_border_radius" id="'.$map_id.'" data-map-id="'.$map_id.'" style="left:'.$left_position.'" title="'.esc_html__('Filter', 'cspm').'">';
				$output .= '<img class="cspm_svg cspm_svg_colored" src="'.$faceted_search_icon.'" alt="'.esc_html__('Filter', 'cspm').'" title="'.esc_html__('Filter', 'cspm').'" />';
			$output .= '</div>';

			$output .= '<div class="faceted_search_container_'.$map_id.' cspm_top_element cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" style="left:'.$left_position.'">';
				
				$output .= '<form action="" method="post" class="faceted_search_form" id="faceted_search_form_'.$map_id.'" data-map-id="'.$map_id.'" data-ext="'.apply_filters('cspm_ext_name', '', $extensions).'">';
					
					$output .= '<input type="hidden" name="map_id" value="'.$map_id.'" />';
					$output .= '<input type="hidden" name="show_carousel" value="'.$carousel.'" />';
					
					$output .= '<ul>';

						/**
						 * Get the taxonomy name from the marker categories settings */
						 
						if(!empty($faceted_search_tax_slug)){
								
							/**
							 * Get Taxonomy Name */
							 
							$tax_name = $faceted_search_tax_slug;
							
							if(empty($faceted_search_tax_terms)){
								
								$terms = $this->faceted_search_terms;
								
							}else $terms = $faceted_search_tax_terms;
							
							if(is_array($terms) && count($terms) > 0){
								
								foreach($terms as $term_id){
									
									// For WPML =====
									$term_id = $CSProgressMap->cspm_wpml_object_id($term_id, $tax_name, false, '', $this->use_with_wpml);
									// @For WPML ====
									
									if($term = get_term($term_id, $tax_name)){
										
										$term_name = isset($term->name) ? $term->name : '';
									
										if($this->faceted_search_multi_taxonomy_option == 'true'){
											
											$output .= '<li>';				
												$output .= '<input type="checkbox" name="'.$tax_name.'___'.$term_id.'[]" id="'.$map_id.'_'.$tax_name.'___'.$term_id.'" value="'.$term_id.'" data-map-id="'.$map_id.'" data-show-carousel="'.$carousel.'" data-taxonomy="'.$tax_name.'" data-term-id="'.$term_id.'" class="faceted_input '.$map_id.' '.$carousel.'">';
												$output .= '<label for="'.$map_id.'_'.$tax_name.'___'.$term_id.'">'.$term_name.'</label>';
												$output .= '<div style="clear:both"></div>';												
											$output .= '</li>';
											
										}else{
											
											$output .= '<li>';				
												$output .= '<input type="radio" name="'.$tax_name.'" id="'.$map_id.'_'.$tax_name.'_'.$term_id.'" value="'.$term_id.'" data-map-id="'.$map_id.'" data-show-carousel="'.$carousel.'" data-taxonomy="'.$tax_name.'" data-term-id="'.$term_id.'" class="faceted_input '.$map_id.' '.$carousel.'">';
												$output .= '<label for="'.$map_id.'_'.$tax_name.'_'.$term_id.'">'.$term_name.'</label>';
												$output .= '<div style="clear:both"></div>';												
											$output .= '</li>';
											
										}
										
									}
									
								}
								
							}else $output .= '<li>'.esc_attr__('No option found!', 'cspm').'</li>';
							
						}
											
					$output .= '</ul>';
								
				$output .= '</form>';			
				 	
				$output .= '<a class="reset_map_list_'.$map_id.' cspm_reset_btn cspm_border_shadow cspm_border_radius" id="'.$map_id.'" data-map-id="'.$map_id.'">';
					$output .= esc_html('Reset', 'cspm').'<img class="cspm_svg cspm_svg_white" src="'.apply_filters('cspm_fs_refresh_img', $this->plugin_url.'img/svg/small-refresh.svg', str_replace('map', '', $map_id)).'" />';
				$output .= '</a>'; //@edited 5.5
					
			$output .= '</div>';
			
			return $output;
			
		}
		
		
		/**
		 * Create the search form
		 *
		 * @since 2.4 
		 * @updated 2.8.5
		 * @updated 3.0 [added {@left_position}, {@open_btn}, {@custom_css}]
		 * @updated 3.2 [replaced cspm_wpml_get_string() by esc_html__()]
		 * @updated 3.5 [removed warning messages and placed them inside the JS file]
		 * @updated 4.0 [added geotarget button]
		 * @updated 5.3 [added possibility to hide geotarget button]
		 * @updated 5.6 [removed range slider css]
		 */
		function cspm_search_form($atts = array()){

			$defaults = array(
				'map_id' => '',
				'carousel' => 'yes',
				'left_position' => '', //@since 3.0
				'open_btn' => true, //@since 3.0
				'custom_css' => '', //@since 3.0
				'extensions' => array(),
			);
			
			extract(wp_parse_args($atts, $defaults));
				
			$distance_unit = ($this->sf_distance_unit == 'metric') ? "Km" : "Miles";
			$min_search_distances = $this->sf_min_search_distances;
			$max_search_distances = $this->sf_max_search_distances;
			
			/**
			 * @WPML String translate */
 
			$address_placeholder = esc_html__($this->address_placeholder, 'cspm');
			$slider_label = esc_html__($this->slider_label, 'cspm');
			$submit_text = esc_html__($this->submit_text, 'cspm');
			
			$output = '';
			
			if($open_btn){
				
				$search_form_icon = apply_filters('cspm_search_form_icon', $this->search_form_icon, str_replace('map', '', $map_id));
				
				$output .= '<div class="search_form_btn cspm_map_btn cspm_top_btn cspm_expand_map_btn cspm_border_shadow cspm_border_radius" id="'.$map_id.'" data-map-id="'.$map_id.'" style="left:'.$left_position.'" title="'.esc_html__('Search', 'cspm').'">';
					$output .= '<img class="cspm_svg cspm_svg_colored" src="'.$search_form_icon.'" alt="'.esc_html__('Search', 'cspm').'" title="'.esc_html__('Search', 'cspm').'" />';
				$output .= '</div>';
			
			}
			
			$output .= '<div class="search_form_container_'.$map_id.' cspm_top_element cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" style="left:'.$left_position.'; '.$custom_css.'">';
				
				$output .= '<form action="" method="post" class="search_form" id="search_form_'.$map_id.'" onsubmit="return false;">';
					
					$output .= '<div class="cspm_search_form_row">';
						
						/**
						 * Address field */
						
						$full_class = ($this->sf_geoIpControl == 'false') ? 'cspm_full' : ''; //@since 5.3
						
						$output .= '<div class="cspm_search_input_text_container input cspm_border_shadow cspm_border_radius '.$full_class.'">';
							$output .= '<input type="text" name="cspm_address" id="cspm_address_'.$map_id.'" value="" placeholder="'.$address_placeholder.'" title="'.$address_placeholder.'" tabindex="9996" />';
						$output .= '</div>';
						
						/**
						 * Geotarget button
						 * @since 4.0
						 * @edited 5.3 */
						
						if($this->sf_geoIpControl == 'true'){ 	
							
							$geo_btn_img = apply_filters('cspm_geo_btn_img', $this->target_icon, str_replace('map', '', $map_id));
						
							$output .= '<a class="cspm_geotarget_search_btn cspm_map_btn cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" data-latLng="" title="'.esc_attr__('Your position', 'cspm').'" tabindex="9997">';
								$output .= '<img class="cspm_svg cspm_svg_colored" src="'.$geo_btn_img.'" />';
							$output .= '</a>'; //@edited 5.5
							
						}	
						
						$output .= '<div style="clear:both;"></div>';
											
					$output .= '</div>';
					
					$output .= '<div class="cspm_expand_search_area">';
						$output .= '<div class="cspm_search_label_container">';
							$output .= '<img class="cspm_svg cspm_svg_colored" src="'.apply_filters('cspm_sf_radius_img', $this->plugin_url.'img/svg/radius.svg', str_replace('map', '', $map_id)).'" />';
							$output .= '<span class="cspm_txt_hex">'.$slider_label.'</span>';
						$output .= '</div>';
						$output .= '<div class="cspm_search_slider_container">';
							$output .= '<input type="text" name="cspm_distance" class="cspm_sf_slider_range" data-map-id="'.$map_id.'" data-min="'.$min_search_distances.'" data-max="'.$max_search_distances.'" data-postfix=" '.$distance_unit.'" data-keyboard="true" data-keyboard-step="0.1" />';
							$output .= '<input type="hidden" name="cspm_distance_unit" value="'.$this->sf_distance_unit.'" />';
							$output .= '<input type="hidden" name="cspm_decimals_distance" value="" />';
						$output .= '</div>';
					$output .= '</div>';
					
					$output .= '<div class="cspm_search_btns_container">';
						$output .= '<a class="cspm_submit_search_form_'.$map_id.' cspm_search_in_ext_data cspm_bg_hex_hover cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" data-show-carousel="'.$carousel.'" data-ext="'.apply_filters('cspm_ext_name', '', $extensions).'" data-open-btn="'.(($open_btn) ? 'yes' : 'no').'" tabindex="9997">';
							$output .= $submit_text.'<img class="cspm_svg cspm_svg_white" src="'.apply_filters('cspm_sf_loup_img', $this->plugin_url.'img/svg/search-loup.svg', str_replace('map', '', $map_id)).'" />';
						$output .= '</a>'; //@edited 5.6.6
						$output .= '<a class="cspm_reset_search_form_'.$map_id.' cspm_reset_ext_data cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" data-show-carousel="'.$carousel.'" data-ext="'.apply_filters('cspm_ext_name', '', $extensions).'" tabindex="9998">';
							$output .= '<img class="cspm_svg" src="'.apply_filters('cspm_sf_refresh_img', $this->plugin_url.'img/svg/refresh-circular-arrow.svg', str_replace('map', '', $map_id)).'" />';
						$output .= '</a>'; //@edited 5.6.6
						$output .= '<div style="clear:both"></div>';				
												
					$output .= '</div>';
					
				$output .= '</form>';	
			
			$output .= '</div>';
			
			return $output;
			
		}
		
		
		/**
		 * Determine displaying carousel on mobile devices
		 *
		 * @since 2.4
		 * @updated 2.7 | 2.8.5 | 5.5
		 */
		function cspm_hide_carousel_from_mobile(){

			if($this->layout_type == 'responsive'){
				return $this->cspm_is_mobile_device();		
			}else return false;
			
		}
		
		
		/**
		 * Detect mobile device
		 *
		 * @since 5.5
		 */
		function cspm_is_mobile_device(){
				
			if(!class_exists('Mobile_Detect')) //@since 2.8.5
				require_once $this->plugin_path.'libs/Mobile_Detect.php';
			
			$detect = new Mobile_Detect;

			return $detect->isMobile() ? true : false;
			
		}
				
		
		/**
		 * Get All Values of A Custom Field Key 
		 * @key: The meta_key of the post meta
		 * @type: The name of the custom post type
		 *
		 * @since 2.5 
		 */
		function cspm_get_meta_values( $key = '', $post_type = 'post' ) {
			
			global $wpdb;
			
			if( empty( $key ) )
				return;
			
			$rows = $wpdb->get_results( $wpdb->prepare( "
				SELECT pm.meta_value, p.ID FROM {$wpdb->postmeta} pm
				LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = '%s' 
				AND pm.meta_value != '' 
				AND p.post_type = '%s'
			", $key, $post_type ), ARRAY_A );
			
			$results = array();
			
			foreach($rows as $row)
				$results['post_id_'.$row['ID']] = array($key => $row['meta_value']);
			
			return $results;
			
		}
		
		
		/**
		 * This will display a Dropdown list of certain countries on the map.
		 * This function is related to the feature "Zoom to country".
		 * Note: Some countries may not be found. Check https://fr.wikipedia.org/wiki/ISO_3166
		 *
		 * @since 3.0
		 * @edited 5.4 | 5.6
		 */
		function cspm_countries_list($map_id, $left_position){
			
			/**
			 * Get all countries in English language */
			 
			$all_en_countries = $this->cspm_get_en_countries();
			
			/** 
			 * Get all countries based on selected language in "[@countries_display_lang]" */
			 
			$all_countries = $this->cspm_get_countries();
			
			/**
			 * Get selected countries */
			 
			$selected_countries = $this->countries;
			
			$output = '';
			
			$countries_btn_icon = apply_filters('cspm_countries_btn_icon', $this->countries_btn_icon, str_replace('map', '', $map_id));
			
			$output .= '<div class="countries_btn cspm_map_btn cspm_top_btn cspm_expand_map_btn cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" style="left:'.$left_position.'" title="'.esc_html__('Countries', 'cspm').'">';
				
				$output .= '<img class="cspm_svg cspm_svg_colored" src="'.$countries_btn_icon.'" alt="'.esc_html__('Countries', 'cspm').'" title="'.esc_html__('Countries', 'cspm').'" />';
			
			$output .= '</div>';
		
			$output .= '<div class="countries_container_'.$map_id.' cspm_top_element" data-map-id="'.$map_id.'" data-show-flag="'.$this->country_flag.'" data-search-input="'.$this->country_search_input.'" style="left:'.$left_position.'">';
				
				$output .= '<select class="cspm_countries_list cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" data-placeholder="'.$this->country_list_placeholder.'">';
					
					if(count($selected_countries) > 0){
						
						foreach($selected_countries as $country_code){
							
							if(array_key_exists($country_code, $all_countries)){
								
								$output .= '<option class="cspm_country_name" data-map-id="'.$map_id.'" value="'.$all_en_countries[$country_code].'__'.$country_code.'" data-country-name="'.$all_en_countries[$country_code].'" data-country-code="'.$country_code.'">';
									
									if($this->country_flag != 'only')
										$output.= $all_countries[$country_code];
									
								$output .= '</option>';	
								
							}
						
						}
					
					}
					
				$output .= '</select>';
				
			$output .= '</div>';			
			
			return $output;
			
		}
		
		
		/**
		 * This will return a list of all countries in English Language
		 *
		 * @since 3.0
		 */
		function cspm_get_en_countries(){
			
			if(file_exists($this->plugin_path . 'inc/countries/en/country.php')){

				$countries = include($this->plugin_path . 'inc/countries/en/country.php');
				
				return $countries;
				
			}else return array();
			
		}

		
		/**
		 * This will return a list of all countries based on given language
		 *
		 * @since 3.0
		 */
		function cspm_get_countries(){
			
			$display_lang = $this->countries_display_lang;
			
			if(file_exists($this->plugin_path . 'inc/countries/'.$display_lang.'/country.php')){

				$countries = include($this->plugin_path . 'inc/countries/'.$display_lang.'/country.php');
				
				return $countries;
				
			}else return $this->cspm_get_en_countries();
			
		}
			
		
		/**
		 * Build the list of proximities
		 *
		 * @since 3.2
		 * @updated 3.8 | 5.4
		 */
		function cspm_get_proximities_list($atts = array()){

			$defaults = array(
				'map_id' => '',
				'left_position' => '',
				'open_btn' => true,
				'custom_css' => '',
				'extensions' => array(),
			);
			
			extract(wp_parse_args($atts, $defaults));
			
			$output = '';
			
			if($open_btn){
				
				$proximities_icon = apply_filters('cspm_proximities_icon', $this->nearby_icon, str_replace('map', '', $map_id));
				
				$output .= '<div class="cspm_proximities_btn cspm_map_btn cspm_top_btn cspm_expand_map_btn cspm_border_shadow cspm_border_radius" id="'.$map_id.'" data-map-id="'.$map_id.'" style="left:'.$left_position.'" title="'.esc_html__('Nearby points of interest', 'cspm').'">';
					$output .= '<img class="cspm_svg cspm_svg_colored" src="'.$proximities_icon.'" alt="'.esc_html__('Nearby points of interest', 'cspm').'" title="'.esc_html__('Nearby points of interest', 'cspm').'" />';
				$output .= '</div>';
			
			}
			
			$output .= '<div class="cspm_proximities_container_'.$map_id.' cspm_top_element cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" data-show-proximity-icon="'.$this->show_proximity_icon.'" style="left:'.$left_position.'; '.$custom_css.'">';
				
				$driving_icon = apply_filters('cspm_driving_icon', $this->plugin_url.'img/svg/car.svg', str_replace('map', '', $map_id)); 
				$transit_icon = apply_filters('cspm_transit_icon', $this->plugin_url.'img/svg/train.svg', str_replace('map', '', $map_id)); 
				$walking_icon = apply_filters('cspm_walking_icon', $this->plugin_url.'img/svg/walk.svg', str_replace('map', '', $map_id)); 
				$bicycling_icon = apply_filters('cspm_bicycling_icon', $this->plugin_url.'img/svg/bicycle.svg', str_replace('map', '', $map_id)); 
				
				/**
				 * Proximities list */
				 
				$output .= '<div class="cspm_proximities_list_container" data-map-id="'.$map_id.'">';	
            
					$output .= '<select class="cspm_proximities_list cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" data-placeholder="'.$this->np_list_placeholder.'">';
						
						$proximities_name = $this->cspm_proximities_names();
						
						foreach($this->np_proximities as $proximity_id){
							
							if(isset($proximities_name[$proximity_id])){
								
								$output .= '<option value="'.$proximities_name[$proximity_id].'" 
									class="cspm_proximity_name" 
									data-proximity-id="'.$proximity_id.'" 
									data-proximity-name="'.$proximities_name[$proximity_id].'" 
									data-map-id="'.$map_id.'" 
									data-distance-unit="'.$this->np_distance_unit.'" 
									data-initial-radius="'.$this->np_radius.'" 
									data-radius="'.$this->np_radius.'" 
									data-min-radius="'.$this->np_min_radius.'" 
									data-max-radius="'.$this->np_max_radius.'" 
									data-travel-mode="DRIVING" 
									data-min-radius-attempt="0" 
									data-max-radius-attempt="0"
									data-draw-circle="'.$this->np_circle_option.'"
									data-edit-circle="'.$this->np_edit_circle.'"
									data-marker-type="'.$this->np_marker_type.'">';
																
									if($this->show_proximity_icon != 'only')			
										$output .= $proximities_name[$proximity_id];
									
								$output .= '</option>';
								
							}
							
						}
					
					$output .= '</select>';
            
				$output .= '</div>';

				/**
				 * Travel modes */
				 
				$output .= '<div class="cspm_swicth_np_mode_container">';
					
					/**
					 * Help button */
					
					$output .= '<a class="cspm_switch_np_travel_mode cspm_map_btn cspm_border_shadow cspm_border_radius active cspm_bg_hex_hover" data-map-id="'.$map_id.'" data-travel-mode="DRIVING" data-origin="" data-destination="" data-distance-unit="'.$this->np_distance_unit.'" title="'.esc_html__('Driving', 'cspm').'">';
						$output .= '<img class="cspm_svg cspm_svg_white" src="'.$driving_icon.'" title="'.esc_html__('Driving', 'cspm').'" />';
					$output .= '</a>'; //@edited 5.5
					
					$output .= '<a class="cspm_switch_np_travel_mode cspm_map_btn cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" data-travel-mode="TRANSIT" data-origin="" data-destination="" data-distance-unit="'.$this->np_distance_unit.'" title="'.esc_html__('Transit', 'cspm').'">';
						$output .= '<img class="cspm_svg cspm_svg_colored" src="'.$transit_icon.'" title="'.esc_html__('Transit', 'cspm').'" />';
					$output .= '</a>'; //@edited 5.5
					
					$output .= '<a class="cspm_switch_np_travel_mode cspm_map_btn cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" data-travel-mode="WALKING" data-origin="" data-destination="" data-distance-unit="'.$this->np_distance_unit.'" title="'.esc_html__('Walking', 'cspm').'">';
						$output .= '<img class="cspm_svg cspm_svg_colored" src="'.$walking_icon.'" title="'.esc_html__('Walking', 'cspm').'" />';
					$output .= '</a>'; //@edited 5.5
					
					$output .= '<a class="cspm_switch_np_travel_mode cspm_map_btn cspm_border_shadow cspm_border_radius last" data-map-id="'.$map_id.'" data-travel-mode="BICYCLING" data-origin="" data-destination="" data-distance-unit="'.$this->np_distance_unit.'" title="'.esc_html__('Bicycling', 'cspm').'">';
						$output .= '<img class="cspm_svg cspm_svg_colored" src="'.$bicycling_icon.'" title="'.esc_html__('Bicycling', 'cspm').'" />';
					$output .= '</a>'; //@edited 5.5
					
					$output .= '<div style="clear:both"></div>';
					
				$output .= '</div>';
				
				$output .= '<div style="clear:both;"></div>';

				/**
				 * Reset */
				 	
				$output .= '<a class="cspm_reset_proximities cspm_reset_btn cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'">';
					$output .= esc_html('Reset', 'cspm').'<img class="cspm_svg cspm_svg_white" src="'.apply_filters('cspm_np_refresh_img', $this->plugin_url.'img/svg/small-refresh.svg', str_replace('map', '', $map_id)).'" />';
				$output .= '</a>'; //@edited 5.5
					
			$output .= '</div>';

			return $output;
			
		}
		
		
		/**

		 * Build the list of all places
		 *
		 * @since 1.1
		 */
		function cspm_proximities_names(){
			
			$places_array = array(
				'accounting' => esc_html__('Accounting', 'cspm'),
				'airport' => esc_html__('Airport', 'cspm'),
				'amusement_park' => esc_html__('Amusement park', 'cspm'),
				'aquarium' => esc_html__('Aquarium', 'cspm'),
				'art_gallery' => esc_html__('Art gallery', 'cspm'),
				'atm' => esc_html__('Atm', 'cspm'),
				'bakery' => esc_html__('Bakery', 'cspm'),
				'bank' => esc_html__('Bank', 'cspm'),
				'bar' => esc_html__('Bar', 'cspm'),
				'beauty_salon' => esc_html__('Beauty salon', 'cspm'),
				'bicycle_store' => esc_html__('Bicycle store', 'cspm'),
				'book_store' => esc_html__('Book store', 'cspm'),
				'bowling_alley' => esc_html__('Bowling alley', 'cspm'),
				'bus_station' => esc_html__('Bus station', 'cspm'),
				'cafe' => esc_html__('Cafe', 'cspm'),
				'campground' => esc_html__('Campground', 'cspm'),
				'car_dealer' => esc_html__('Car dealer', 'cspm'),
				'car_rental' => esc_html__('Car rental', 'cspm'),
				'car_repair' => esc_html__('Car repair', 'cspm'),
				'car_wash' => esc_html__('Car wash', 'cspm'),
				'casino' => esc_html__('Casino', 'cspm'),
				'cemetery' => esc_html__('Cemetery', 'cspm'),
				'church' => esc_html__('Church', 'cspm'),
				'city_hall' => esc_html__('City hall', 'cspm'),
				'clothing_store' => esc_html__('Clothing store', 'cspm'),
				'convenience_store' => esc_html__('Convenience store', 'cspm'),
				'courthouse' => esc_html__('Courthouse', 'cspm'),
				'dentist' => esc_html__('Dentist', 'cspm'),
				'department_store' => esc_html__('Department store', 'cspm'),
				'doctor' => esc_html__('Doctor', 'cspm'),
				'electrician' => esc_html__('Electrician', 'cspm'),
				'electronics_store' => esc_html__('Electronics store', 'cspm'),
				'embassy' => esc_html__('Embassy', 'cspm'),
				//'establishment' => esc_html__('Establishment', 'cspm'),
				//'finance' => esc_html__('Finance', 'cspm'),
				'fire_station' => esc_html__('Fire station', 'cspm'),
				'florist' => esc_html__('Florist', 'cspm'),
				//'food' => esc_html__('Food', 'cspm'),
				'funeral_home' => esc_html__('Funeral home', 'cspm'),
				'furniture_store' => esc_html__('Furniture store', 'cspm'),
				'gas_station' => esc_html__('Gas station', 'cspm'),
				//'general_contractor' => esc_html__('General contractor', 'cspm'),
				//'grocery_or_supermarket' => esc_html__('Grocery or supermarket', 'cspm'),
				'gym' => esc_html__('Gym', 'cspm'),
				'hair_care' => esc_html__('Hair care', 'cspm'),
				'hardware_store' => esc_html__('Hardware store', 'cspm'),
				//'health' => esc_html__('Health', 'cspm'),
				'hindu_temple' => esc_html__('Hindu temple', 'cspm'),
				'home_goods_store' => esc_html__('Home goods store', 'cspm'),
				'hospital' => esc_html__('Hospital', 'cspm'),
				'insurance_agency' => esc_html__('Insurance agency', 'cspm'),
				'jewelry_store' => esc_html__('Jewelry store', 'cspm'),
				'laundry' => esc_html__('Laundry', 'cspm'),
				'lawyer' => esc_html__('Lawyer', 'cspm'),
				'library' => esc_html__('Library', 'cspm'),
				'liquor_store' => esc_html__('Liquor store', 'cspm'),
				'local_government_office' => esc_html__('Local government office', 'cspm'),
				'locksmith' => esc_html__('Locksmith', 'cspm'),
				'lodging' => esc_html__('Lodging', 'cspm'),
				'meal_delivery' => esc_html__('Meal delivery', 'cspm'),
				'meal_takeaway' => esc_html__('Meal takeaway', 'cspm'),
				'mosque' => esc_html__('Mosque', 'cspm'),
				'movie_rental' => esc_html__('Movie rental', 'cspm'),
				'movie_theater' => esc_html__('Movie theater', 'cspm'),
				'moving_company' => esc_html__('Moving company', 'cspm'),
				'museum' => esc_html__('Museum', 'cspm'),
				'night_club' => esc_html__('Night club', 'cspm'),
				'painter' => esc_html__('Painter', 'cspm'),
				'park' => esc_html__('Park', 'cspm'),
				'parking' => esc_html__('Parking', 'cspm'),
				'pet_store' => esc_html__('Pet store', 'cspm'),
				'pharmacy' => esc_html__('Pharmacy', 'cspm'),
				'physiotherapist' => esc_html__('Physiotherapist', 'cspm'),
				//'place_of_worship' => esc_html__('Place of worship', 'cspm'),
				'plumber' => esc_html__('Plumber', 'cspm'),
				'police' => esc_html__('Police', 'cspm'),
				'post_office' => esc_html__('Post office', 'cspm'),
				'real_estate_agency' => esc_html__('Real estate agency', 'cspm'),
				'restaurant' => esc_html__('Restaurant', 'cspm'),
				'roofing_contractor' => esc_html__('Roofing contractor', 'cspm'),
				'rv_park' => esc_html__('Rv park', 'cspm'),
				'school' => esc_html__('School', 'cspm'),
				'shoe_store' => esc_html__('Shoe store', 'cspm'),
				'shopping_mall' => esc_html__('Shopping mall', 'cspm'),
				'spa' => esc_html__('Spa', 'cspm'),
				'stadium' => esc_html__('Stadium', 'cspm'),
				'storage' => esc_html__('Storage', 'cspm'),
				'store' => esc_html__('Store', 'cspm'),
				'subway_station' => esc_html__('Subway station', 'cspm'),
				'synagogue' => esc_html__('Synagogue', 'cspm'),
				'taxi_stand' => esc_html__('Taxi stand', 'cspm'),
				'train_station' => esc_html__('Train station', 'cspm'),
				'travel_agency' => esc_html__('Travel agency', 'cspm'),
				'university' => esc_html__('University', 'cspm'),
				'veterinary_care' => esc_html__('Veterinary care', 'cspm'),
				'zoo' => esc_html__('Zoo', 'cspm'),
			);
			
			return $places_array;
				
		}
		

		/**
		 * This will return a list of all available KML Layers on the map
		 * 
		 * @since 5.6 
		 */		
		function cspm_kml_layers_list($map_id, $left_position){
						 
			if($this->use_kml == 'true' && is_array($this->kml_layers)){
				
				$output = '';
				
				$kml_list_btn_icon = apply_filters('cspm_kml_list_icon', $this->kml_list_icon, str_replace('map', '', $map_id));
				
				$output .= '<div class="kml_list_btn cspm_map_btn cspm_top_btn cspm_expand_map_btn cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" style="left:'.$left_position.'" title="'.esc_html__('KML Layers', 'cspm').'">';
					
					$output .= '<img class="cspm_svg cspm_svg_colored" src="'.$kml_list_btn_icon.'" alt="'.esc_html__('KML Layers', 'cspm').'" title="'.esc_html__('KML Layers', 'cspm').'" />';
				
				$output .= '</div>';
			
				$output .= '<div class="kml_list_container_'.$map_id.' cspm_top_element" data-map-id="'.$map_id.'" style="left:'.$left_position.'">';
					
					$output .= '<select class="cspm_kml_layer_list cspm_border_shadow cspm_border_radius" data-map-id="'.$map_id.'" data-placeholder="'.$this->kml_list_placeholder.'" multiple>';
					
						/**
						 * Loop through all KML layers */
						
						if(is_array($this->kml_layers) && count($this->kml_layers) > 0){
							
							foreach($this->kml_layers as $kml_data){
					
								/**
								 * Build KML Layers list */
								
								if(isset($kml_data['kml_url']) && !empty($kml_data['kml_url'])){
									
									$kml_visibility = !empty($kml_data['kml_visibility']) ? esc_attr($kml_data['kml_visibility']) : 'true';
									
									if($kml_visibility == 'true' && (empty($kml_connected_post) || (!empty($kml_connected_post) && in_array($kml_connected_post, $post_ids))) ){
										
										$kml_name = !empty($kml_data['kml_label']) ? esc_attr($kml_data['kml_label']) : '';
		                                
                                        $selected = ($this->kml_layers_onload_status == 'show') ? 'selected="selected"' : ''; //@since 5.6.7
                                        
										$output .= '<option class="cspm_country_name" '.$selected.' value="'.str_replace('-', '_', sanitize_title_with_dashes($kml_name)).'" data-map-id="'.$map_id.'">';
											$output.= $kml_name;
										$output .= '</option>';	//@edited 5.6.7
										
									}
									
								}
								
							}
						
						}
						
					$output .= '</select>';
					
				$output .= '</div>';			
				
				return $output;
		
			}
			
		}
					
		
		/**
		 * This will add inline CSS to the appropriate style file
		 *
		 * @since 3.8
		 * @updated 3.9
		 */
		function cspm_add_inline_style($custom_css){
			 
			$script_handle = 'cspm-style';			
			
			wp_add_inline_style($script_handle, $custom_css);
			
		}
		
		/**
		 * This will get the post thumb from the cache if exists ...
		 * ... or from the DBB and will save it to cache 
		 *
		 * @since 5.4
		 */
		function cspm_get_the_post_thumbnail($post_id, $image_size, $parameter){
			
			$img_size = is_array($image_size) ? implode('x', $image_size) : $image_size;
			
			$thumb_key = 'cspm_thumb_'.$img_size.'_'.$post_id;
			
			$infobox_thumb = wp_cache_get($thumb_key);
			
			if($infobox_thumb === false){ 
				$infobox_thumb = get_the_post_thumbnail($post_id, $image_size, $parameter); 
				wp_cache_set($thumb_key, $infobox_thumb, '', 1800); 
			}
			
			return $infobox_thumb;
			
		}
		
		
		/**
		 * Convert HEX color to RGB(A)
		 *
		 * @since 5.3 
		 */
		function cspm_hexToRgb($hex, $alpha = false){
			
			if(empty($hex))
				return;
				
			$hex      = str_replace('#', '', $hex);
			$length   = strlen($hex);
			$rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
			$rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
			$rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
			
			if ( $alpha ) {
				$rgb['a'] = $alpha;
			}
			
			return implode(array_keys($rgb)) . '(' . implode(', ', $rgb) . ')';
			
		}


		/**
		 * This will convert "Lng,Lat" coordinate to "Lat,Lng" format
		 *
		 * @since 5.3
		 */
		function cspm_convert_lngLat_to_latLng($coordinates){
			
			if(empty($coordinates))
				return $coordinates;
				
			$explode_coordinates = str_replace(array('[', ']'), array('', ''), explode('],[', $coordinates));
			
			$latLng_coordinates_array = array_map(function($lngLat){
				$explode_lngLat = explode(',', $lngLat);
				return isset($explode_lngLat[1], $explode_lngLat[0]) ? $explode_lngLat[1].','.$explode_lngLat[0] : '';
			}, $explode_coordinates);
			
			return '[' . implode('],[', $latLng_coordinates_array) . ']';

		}

		
		/**
		 * This will display the map with all the settings it needs.
		 * The function will get the custom map ID and all its settings.
		 *
		 * @since 3.0
		 * @updated 3.9
		 */
		function cspm_main_map_shortcode($atts){

			extract( shortcode_atts( array(
			
				/**
				 * [@id] The ID of the map */
				 				 
				'id' => '',
			
				/**
				 * [@center_at] Change the default map center to a given "Lat,Lng" coordinates or a post ID */
				 				 
				'center_at' => '',	
			
				/**
				 * [@post_ids] Display certains posts by providing their IDs (comma separated) */
				 				 
				'post_ids' => '',
				
				/**
				 * [@list_ext] Used for the extension "List & Filter"
				 * Wheather to override the Ext. from the shortocode */
				 
				'list_ext' => '',		
				
				/**
				 * [@window_resize] Whether to recenter the Map on window resize or not.
				 * @since 2.8.5 */
				
				'window_resize' => 'yes', // Possible values, "yes" & "no"			  																
			  
			), $atts, 'cspm_main_map' ) ); 						
			
			/**
			 * Get the post metadata (all custom fields) */
			
			$map_id = esc_attr($id);
			
			if(!empty($map_id)){
				
				/**
				 * Get this map post type & status */
				
				$map_post_type = get_post_type($map_id);
				 
				$map_status = get_post_status($map_id);
				
				/**
				 * Should match our CPT and any status than "trash" */
				 
				if($map_post_type == $this->object_type && $map_status && $map_status != 'trash'){
					
					/**
					 * Get all the custom fields related to that object/map/post */
					 
					$map_custom_fields = array_map(
						function($value){
							return isset($value[0]) ? $value[0] : $value;
						}, 
						get_post_custom($map_id)
					);
				
					/** 
					 * From all custom fields, we'll extract all the keys that starts with our custom fields prefix */
					 
					$map_settings = array_intersect_key(
						$map_custom_fields, 
						array_flip(
							preg_grep('/^'.$this->metafield_prefix.'/', array_keys($map_custom_fields))
						)
					);
				
					$map_settings['map_object_id'] = $map_id;
					
					/**
					 * Hook to change map settings */

					$map_settings = apply_filters('cspm_map_settings', $map_settings, $map_id, $this->metafield_prefix);
					
					/**
					 * Re-init "Progress Map" Class with (admin/default) plugin settings & map settings */
						
					$CspmMainMap = new CspmMainMap(array(
						'init' => false, 
						'plugin_settings' => $this->plugin_settings,
						'map_settings' => $map_settings,
						'metafield_prefix' => $this->metafield_prefix,
					));
										
						$main_map_args = array(
							'map_id' => 'map'.$map_id,
							'window_resize' => esc_attr($window_resize),
							'center_at' => esc_attr($center_at),	
							'post_ids' => esc_attr($post_ids),
						);
						
						$extensions_array = array(
							'list_ext' => $list_ext,
						); //@since 5.6.2
						
					/**
					 * Run actions before displaying the map
					 * e.g. Run an extension or Initialize other classes that will inherit this class settings!
					 * @since 3.3 */
					 
					do_action('cspm_before_execute_main_map_function', array(
						'init' => false, 
						'plugin_settings' => $this->plugin_settings,
						'map_settings' => $map_settings,
						'metafield_prefix' => $this->metafield_prefix,
						'object_type' => $this->object_type,
						'map_id' => 'map'.$map_id,
						'extensions' => $extensions_array, //@since 5.6.2
					));
					
					/**
					 * Display the map
					 * Note: Do not use [$this] to call the main map function, use [$CspmMainMap] instead! */

					return $CspmMainMap->cspm_main_map_function($main_map_args);
					
				}else{
					
					?><script>console.log('PROGRESS MAP: The map with the ID "<?php echo $map_id; ?>" is no longer available or don\'t really exist! Check your trash from "Progress Map => All Maps => Trash", you might have deleted the map accidentally!');</script><?php
					
					return '<strong>'.esc_attr__('Map not available!', 'cspm').'</strong>';
					
				}
				
			}else{
					
				?><script>console.log('PROGRESS MAP: You should provide the ID of the map to display! Edit your page and add the ID of the map to the shortcode using the attribute "id". Example: [cspm_main_map id="???"]');</script><?php
				
				return '<strong>'.esc_attr__('Map not available!', 'cspm').'</strong>';
				
			}
					
		}
			
	}

}	
