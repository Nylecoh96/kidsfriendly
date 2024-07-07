<?php
/**
 * Plugin Name: Progress Map by CodeSpacing
 * Plugin URI: http://codecanyon.net/item/progress-map-wordpress-plugin/5581719?ref=codespacing
 * Description: <strong>Progress Map</strong> is a Wordpress plugin for location listings. With this plugin, your locations will be listed on both Google map (as markers) and a carousel (as locations details), this one will be related to the map, which means that the selected item in the carousel will target its location in the map and vice versa.
 * Version: 5.7.1
 * Author: Hicham Radi (CodeSpacing)
 * Author URI: https://www.codespacing.com/
 * Text Domain: cspm
 * Domain Path: /languages
 */
 
if(!defined('ABSPATH')){
    exit; // Exit if accessed directly
}

if(!class_exists('CSProgressMap')){
	
	class CSProgressMap{
		
		private static $_this;
		
		public $plugin_version = '5.7.1';
			
		private $plugin_path;
		private $plugin_url;

		public $cspm_plugin_path;
		public $cspm_plugin_url;
			
		public $metafield_prefix;
		
		private $plugin_get_var = 'cs_progress_map_plugin';
	
		public $plugin_settings = array();
		public $shared_plugin_settings = array();
		
		/**
		 * The name of the post type to which we'll add the metaboxes
		 * @since 1.0 */
		 
		public $object_type;
			
		/**
		 * The name/key of the option used to save the plugin settings
		 * @since 4.0 */
			 
		private $plugin_settings_option_key;
		public $public_plugin_settings_option_key; // @since 4.7
		
		/**
		 * The URL of the GMaps API
		 * @since 5.2 */
		 
		public $gmaps_unformatted_url = '';
		public $gmaps_formatted_url = '';
			
		/**
		 * Plugin settings */
		 
		public $post_types = array('post');
		public $outer_links_field_name = ''; //@since 2.5				
		public $custom_list_columns = 'no'; //@since 2.6.3
		public $use_with_wpml = 'no'; //@since 2.6.3
		public $latitude_field_name = 'codespacing_progress_map_lat'; // @since 2.5		
		public $longitude_field_name = 'codespacing_progress_map_lng'; // @since 2.5

		/**
		 * Default Map settings */
		
		public $api_key = ''; //@since 2.8
		public $map_language = 'en';
		public $center = '51.53096,-0.121064';			
		public $wrong_center_point = false;
		public $zoom = '12';
		public $mapTypeControl = 'true';
		public $streetViewControl = 'false';
		public $zoomControl = 'true';
		public $zoomControlType = 'customize';
		public $retinaSupport = 'false';
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
		public $restrict_map_bounds = ''; //@since 5.2
		public $strict_bounds = ''; //@since 5.2
		public $map_bounds_restriction = ''; //@since 5.2
		public $bicycling_layer = 'false'; //@since 5.3
		public $gestureHandling = 'auto'; //@since 5.6.5
		
		/** 
		 * Default marker settings */

		public $marker_icon = '';					
		public $defaultMarker = '';
		public $pulsating_circle = 'pulsating_circle'; // @since 2.5		
		public $marker_anchor_point_option = 'disable'; //@since 2.6.1
		public $marker_anchor_point = ''; //@since 2.6.1
		public $marker_icon_height = ''; //@since 4.0
		public $marker_icon_width = ''; //@since 4.0
		
		/**
		 * Default clustering settings */
		
		public $useClustring = 'true';
		public $gridSize = '60';
		public $big_cluster_icon = '';
		public $medium_cluster_icon = '';
		public $small_cluster_icon = ''; 
		public $cluster_text_color = '#ffffff';			
		public $big_cluster_icon_height = '90'; //@since 4.0
		public $big_cluster_icon_width = '90'; //@since 4.0		
		public $medium_cluster_icon_height = '70'; //@since 4.0
		public $medium_cluster_icon_width = '70'; //@since 4.0				
		public $small_cluster_icon_height = '50'; //@since 4.0
		public $small_cluster_icon_width = '50'; //@since 4.0				

		/**
		 * Default geotargeting settings */

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
		 * Default Map style settings */
		
		public $initial_map_style = 'ROADMAP';
		public $style_option = 'progress-map';
		public $map_style = 'google-map';	
		public $js_style_array = '';
		public $custom_style_name = 'Custom style'; //@since 2.6.1
		 		 		
		/**
		 * Default infobox settings */
		 
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
		 * Troubleshooting & configs */
		 
		public $combine_files = 'seperate_minify'; // @edited 5.6.5
		public $loading_scripts = 'entire_site'; // @since 2.5
		public $include_or_remove_option = 'include'; //@since 2.6.1		
		public $load_on_page_ids = ''; // @since 2.5		
		public $load_on_post_ids = ''; // @since 2.5
		public $load_on_page_templates = ''; // @since 2.6.1
		public $remove_bootstrap = 'enable'; //@since 2.8.2
		public $remove_gmaps_api = array(); //@since 2.8.2 //updated 2.8.5	
		public $remove_google_fonts = 'enable'; //@since 2.8.2		
		
		/**
		 * Styling options 
		 * @since 3.8 */
		
		public $main_rgb_color = 'rgba(224,11,11,.97)'; //'rgba(0,134,237,.97)';
		public $main_rgb_hover_color = 'rgba(176,5,5,0.97)'; //'rgba(0,133,220,.97)';
		public $main_hex_color = '#e00b0b'; //#008fed';
		public $main_hex_hover_color = '#b00505'; //#009bfd';					
		public $zoom_in_icon = '';	
		public $zoom_out_icon = '';
		public $custom_css = ''; //@since 2.6.1
			
		/**
		 * Modal options
		 * @since 4.6 */
		
		public $modal_width = '90';
		public $modal_width_unit = '%';
		public $modal_height = '500';
		public $modal_openFullscreen = 'no';
		public $modal_allow_fullscreen = 'yes'; //@since 4.7
		public $modal_top_margin; //@since 5.5
		public $modal_bottom_margin; //@since 5.5
			
		function __construct(){				
			
			self::$_this = $this;  
			     
			$this->plugin_path = $this->cspm_plugin_path = plugin_dir_path( __FILE__ );
			$this->plugin_url = $this->cspm_plugin_url =  plugin_dir_url( __FILE__ );

			/**
			 * Our metabox custom fields prefix */
			 
			$this->metafield_prefix = '_cspm';
			
			/**
			 * Our custom post type for adding new maps */
			 
			$this->object_type = 'cspm_post_type';
			 
			/**
			 * The name/key of the option used to save the plugin settings
			 * @since 4.0 */
			 
			$this->plugin_settings_option_key = $this->public_plugin_settings_option_key = 'cspm_default_settings';
			$this->plugin_settings = get_option($this->plugin_settings_option_key);			

			/** 
			 * Plugin settings */
			
			$this->post_types = $this->cspm_get_setting('post_types', array());
			$this->latitude_field_name = str_replace(' ', '', $this->cspm_get_setting('latitude_field_name', 'codespacing_progress_map_lat'));
			$this->longitude_field_name = str_replace(' ', '', $this->cspm_get_setting('longitude_field_name', 'codespacing_progress_map_lng'));
			$this->use_with_wpml = $this->cspm_get_setting('use_with_wpml', 'no');
			$this->outer_links_field_name = str_replace(' ', '', $this->cspm_get_setting('outer_links_field_name', ''));
			$this->custom_list_columns = $this->cspm_get_setting('custom_list_columns', 'no');			
			
			/**
			 * Default map settings */
			 
			$this->api_key = $this->cspm_get_setting('api_key', '');
			$this->map_language = str_replace(' ', '', $this->cspm_get_setting('map_language', 'en'));
			$this->center = $this->cspm_get_setting('map_center', '51.53096,-0.121064');			
			$this->zoom = $this->cspm_get_setting('map_zoom', '12');
			$this->max_zoom = $this->cspm_get_setting('max_zoom', 19); // @since 2.6.3
			$this->min_zoom = $this->cspm_get_setting('min_zoom', 0); // @since 2.6.3			
			$this->zoom_on_doubleclick = $this->cspm_get_setting('zoom_on_doubleclick', 'false'); // @since 2.6.3												
			$this->autofit = $this->cspm_get_setting('autofit', 'false'); // @since 2.7												
			$this->traffic_layer = $this->cspm_get_setting('traffic_layer', 'false'); // @since 2.7
			$this->transit_layer = $this->cspm_get_setting('transit_layer', 'false'); // @since 2.7.4
			$this->recenter_map = $this->cspm_get_setting('recenter_map', 'true'); // @since 3.0									
			$this->mapTypeControl = $this->cspm_get_setting('mapTypeControl', 'true');
			$this->streetViewControl = $this->cspm_get_setting('streetViewControl', 'false');
			$this->zoomControl = $this->cspm_get_setting('zoomControl', 'true');
			$this->zoomControlType = $this->cspm_get_setting('zoomControlType', 'customize');
			$this->retinaSupport = $this->cspm_get_setting('retinaSupport', 'false');
			$this->map_background = $this->cspm_get_setting('map_background', '#ededed'); //@since 4.2
			$this->clickableIcons = $this->cspm_get_setting('clickableIcons', 'true'); //@since 4.2
			$this->rotateControl = $this->cspm_get_setting('rotateControl', 'false'); //@since 4.2
			$this->scaleControl = $this->cspm_get_setting('scaleControl', 'false'); //@since 4.2
			$this->fullscreenControl =  $this->cspm_get_setting('fullscreenControl', 'false'); //@since 4.2
			$this->controlSize = $this->cspm_get_setting('controlSize', '39'); //@since 4.8
			$this->restrict_map_bounds = $this->cspm_get_setting('restrict_map_bounds', 'no'); //@since 5.2
			$this->strict_bounds = $this->cspm_get_setting('strict_bounds'); //@since 5.2
			$this->map_bounds_restriction = $this->cspm_get_setting('map_bounds_restriction', 'relaxed'); //@since 5.2
			$this->bicycling_layer = $this->cspm_get_setting('bicycling_layer', 'false'); // @since 5.3
			$this->gestureHandling = $this->cspm_get_setting('gestureHandling', 'auto'); // @since 5.6.5
			
			/**
			 * Default map styles section */
			 
			$this->initial_map_style = $this->cspm_get_setting('initial_map_style', 'ROADMAP');			 
			$this->style_option = $this->cspm_get_setting('style_option', 'progress-map');
			$this->map_style = $this->cspm_get_setting('map_style', 'google-map');
			$this->js_style_array = $this->cspm_get_setting('js_style_array', '');
			$this->custom_style_name = $this->cspm_get_setting('custom_style_name', 'Custom style'); //@since 2.6.1
			
			/**
			 * Default marker settings */

			$this->defaultMarker = $this->cspm_get_setting('defaultMarker');
			$this->marker_icon = $this->cspm_get_setting('marker_icon', $this->plugin_url.'img/svg/marker.svg');						
			$this->markerAnimation = $this->cspm_get_setting('markerAnimation', 'pulsating_circle'); // @since 2.5
			$this->marker_anchor_point_option = $this->cspm_get_setting('marker_anchor_point_option', 'disable'); // @since 2.6.1
			$this->marker_anchor_point = $this->cspm_get_setting('marker_anchor_point', ''); // @since 2.6.1				
			$this->marker_icon_height = $this->cspm_get_setting('marker_icon_height', ''); //@since 4.0
			$this->marker_icon_width = $this->cspm_get_setting('marker_icon_width', ''); //@since 4.0
			
			/**
			 * Default clustering settings */
			
			$this->useClustring = $this->cspm_get_setting('useClustring', 'true');
			$this->gridSize = $this->cspm_get_setting('gridSize', '60');
			$this->big_cluster_icon = $this->cspm_get_setting('big_cluster_icon', $this->plugin_url.'img/svg/cluster.svg');
			$this->medium_cluster_icon = $this->cspm_get_setting('medium_cluster_icon', $this->plugin_url.'img/svg/cluster.svg');
			$this->small_cluster_icon = $this->cspm_get_setting('small_cluster_icon', $this->plugin_url.'img/svg/cluster.svg'); 
			$this->cluster_text_color = $this->cspm_get_setting('cluster_text_color', '#ffffff');			
			$this->big_cluster_icon_height = $this->cspm_get_setting('big_cluster_icon_height', '90'); //@since 4.0
			$this->big_cluster_icon_width = $this->cspm_get_setting('big_cluster_icon_width', '90'); //@since 4.0		
			$this->medium_cluster_icon_height = $this->cspm_get_setting('medium_cluster_icon_height', '70'); //@since 4.0
			$this->medium_cluster_icon_width = $this->cspm_get_setting('medium_cluster_icon_width', '70'); //@since 4.0				
			$this->small_cluster_icon_height = $this->cspm_get_setting('small_cluster_icon_height', '50'); //@since 4.0
			$this->small_cluster_icon_width = $this->cspm_get_setting('small_cluster_icon_width', '50'); //@since 4.0	
			
			/**
			 * Default geo-targeting settings */

			$this->geoIpControl = $this->cspm_get_setting('geoIpControl', 'false');			
			$this->show_user = $this->cspm_get_setting('show_user', 'false'); // @since 2.7.4
			$this->user_marker_icon = $this->cspm_get_setting('user_marker_icon', $this->plugin_url.'img/svg/user_marker.svg'); // @since 2.7.4
			$this->user_map_zoom = $this->cspm_get_setting('user_map_zoom', '12'); // @since 2.7.4
			$this->user_circle = $this->cspm_get_setting('user_circle', '0'); // @since 2.7.4
			$this->user_circle_fillColor = $this->cspm_get_setting('user_circle_fillColor', '#189AC9'); // @since 3.0
			$this->user_circle_fillOpacity = $this->cspm_get_setting('user_circle_fillOpacity', '0.1'); // @since 3.0
			$this->user_circle_strokeColor = $this->cspm_get_setting('user_circle_strokeColor', '#189AC9'); // @since 3.0				
			$this->user_circle_strokeOpacity = $this->cspm_get_setting('user_circle_strokeOpacity', '1'); // @since 3.0
			$this->user_circle_strokeWeight = $this->cspm_get_setting('user_circle_strokeWeight', '1'); // @since 3.0						
			$this->user_marker_icon_height = $this->cspm_get_setting('user_marker_icon_height', ''); //@since 4.0
			$this->user_marker_icon_width = $this->cspm_get_setting('user_marker_icon_width', ''); //@since 4.0
		
			/**
			 * Default Infobox settings
			 * @since 2.5 */
			
			$this->show_infobox = $this->cspm_get_setting('show_infobox', 'true');
			$this->infobox_type = $this->cspm_get_setting('infobox_type', 'rounded_bubble');
			$this->infobox_display_event = $this->cspm_get_setting('infobox_display_event', 'onload');
			$this->infobox_external_link = $this->cspm_get_setting('infobox_external_link', 'same_window');
			$this->remove_infobox_on_mouseout = $this->cspm_get_setting('remove_infobox_on_mouseout', 'false'); //@since 2.7.4
			$this->infobox_width = $this->cspm_get_setting('infobox_width', ''); //@since 4.0
			$this->infobox_height = $this->cspm_get_setting('infobox_height', ''); //@since 4.0
			$this->infobox_title = $this->cspm_get_setting('infobox_title', NULL); //@since 5.0
			$this->infobox_details = $this->cspm_get_setting('infobox_content', '[l=100]'); //@since 5.0
			$this->infobox_ellipses = $this->cspm_get_setting('infobox_ellipses', 'yes'); //@since 5.0			
			$this->infobox_display_zoom_level = $this->cspm_get_setting('infobox_display_zoom_level', 12); //@since 5.3						
			$this->infobox_show_close_btn = $this->cspm_get_setting('infobox_show_close_btn', 'false'); //@since 5.3
			
			/**
			 * Troubleshooting & Configs
			 * @since 2.5 */
			 
			$this->combine_files = $this->cspm_get_setting('combine_files', 'seperate_minify');
			$this->remove_bootstrap = $this->cspm_get_setting('remove_bootstrap', 'enable'); //@since 2.8.2
			$this->remove_google_fonts = $this->cspm_get_setting('remove_google_fonts', 'enable'); //@since 2.8.2	
			$this->remove_gmaps_api = $this->cspm_get_setting('remove_gmaps_api', array());
			
			/**
			 * Styling options 
			 * @since 3.8 */
			 
			$this->main_hex_color = $this->cspm_get_setting('main_hex_color', '#008fed');
			$this->main_hex_hover_color = $this->cspm_get_setting('main_hex_hover_color', '#009bfd');							
			$this->main_rgb_color = 'rgba('.$this->cspm_hex2RGB($this->main_hex_color).',.97)'; //'rgba(0,134,237,.97)';
			$this->main_rgb_hover_color = 'rgba('.$this->cspm_hex2RGB($this->main_hex_hover_color).',0.97)'; //'rgba(0,133,220,.97)';
			$this->zoom_in_icon = $this->cspm_get_setting('zoom_in_icon', $this->plugin_url.'img/svg/addition-sign.svg');
			$this->zoom_out_icon = $this->cspm_get_setting('zoom_out_icon', $this->plugin_url.'img/svg/minus-sign.svg');	
			$this->custom_css = $this->cspm_get_setting('custom_css', ''); //@since 2.6.1
			
			/**
			 * Modal options
			 * @since 4.6 */
			
			$this->modal_width = $this->cspm_get_setting('modal_width', '90');
			$this->modal_width_unit = $this->cspm_get_setting('modal_width_unit', '%');
			$this->modal_height = $this->cspm_get_setting('modal_height', '500');
			$this->modal_openFullscreen = $this->cspm_get_setting('modal_openFullscreen', 'no');
			$this->modal_allow_fullscreen = $this->cspm_get_setting('modal_allow_fullscreen', 'yes'); //@since 4.7
			$this->modal_top_margin = $this->cspm_get_setting('modal_top_margin'); //@since 5.5
			$this->modal_bottom_margin = $this->cspm_get_setting('modal_bottom_margin'); //@since 5.5
			
			/**
			 * GMaps API URL, version, librairies and other infos
			 * @since 5.2 */
			
			$gmaps_libs = implode(',', array(
				'geometry',
				'places',
				'drawing', //@since 3.3
				'visualization', //@since 3.3
			));
							
			$this->gmaps_unformatted_url = '//maps.google.com/maps/api/js?v=3.exp&key=%s&callback=Function.prototype&language=%s&libraries='.$gmaps_libs; //@edited 5.7.1
			$this->gmaps_formatted_url = sprintf($this->gmaps_unformatted_url, $this->api_key, $this->map_language);
			
			/** 
			 * Define all custom fields used in the "Add locations" widget */
			 
			$this->cspm_define_metabox_field_names();

			/**
			 * [@shared_plugin_settings] | Build an array containing the plugin settings that we can share with other classes.
			 * The idea is to use the shared settings as default settings in other classes. */
			
			$this->shared_plugin_settings = apply_filters('cspm_shared_plugin_settings', array_merge(
				(array) $this->plugin_settings,
				array(
					'main_rgb_color' => $this->main_rgb_color,
					'main_rgb_hover_color' => $this->main_rgb_hover_color,
				)
			));

			/**
			 * Add our custom templates to the theme using our plugin
			 * @since 4.6 */
			
			$this->templates = array(
				'templates/nearby-places-modal.php' => 'Nearby Places Modal',
			);

				/**
				 * Add a filter to the attributes metabox to inject template into the cache
				 * @since 4.6 */
				 
				if(version_compare(floatval(get_bloginfo('version')), '4.7', '<')){
					add_filter('page_attributes_dropdown_pages_args', array($this, 'cspm_register_custom_templates')); // WP 4.6 and older			
				}else add_filter('theme_page_templates', array($this, 'cspm_add_new_templates')); // WP 4.7 and above
					
				/**
				 * Add a filter to the save post to inject out template into the page cache
				 * @since 4.6 */
				 
				add_filter('wp_insert_post_data', array($this, 'cspm_register_custom_templates'));
			
				/**
				 * Add a filter to the template include to determine if the page has our template assigned and return it's path
				 * @since 4.6 */
				
				add_filter('template_include', array($this, 'cspm_view_custom_templates'));

		}

	  
		static function this(){
			
			return self::$_this;
			
		}
	 
	 
		function cspm_hooks(){
			
			/**
			 * Load plugin textdomain.
			 * @since 2.8 */
			 
			add_action('init', array($this, 'cspm_load_plugin_textdomain')); 

			if(is_admin()){
				
				/**
				 * Include and setup our custom post type */
				
				if(file_exists($this->plugin_path . 'admin/cpt/cspm-post-type.php')){
					
					require_once( $this->plugin_path . 'admin/cpt/cspm-post-type.php' );

					if(class_exists('CspmPostType')){
						
						$CspmPostType = new CspmPostType(array(
							'object_type' => $this->object_type,
						));
						
					}

				}
				
				/**
				 * Include and setup the plugin settings page 
				 * Note: The portion {!empty($_POST)} will ensure AJAX requests can be executed! 
				 * @since 4.0 */
				
				if((isset($_GET['page']) && $_GET['page'] == $this->plugin_settings_option_key) || !empty($_POST)){
					
					if(file_exists($this->plugin_path . 'admin/options-page/settings.php')){
					
						require_once( $this->plugin_path . 'admin/options-page/settings.php' );
	
						if(class_exists('CspmSettings')){
							
							$CspmSettings = new CspmSettings(array(
								'plugin_path' => $this->plugin_path, 
								'plugin_url' => $this->plugin_url,
								'object_type' => $this->object_type,
								'option_key' => $this->plugin_settings_option_key,
								'version' => $this->plugin_version,
								'plugin_settings' => $this->plugin_settings, //@since 5.2
							));
							
						}
						
					}

				}
				
				/**
				 * Make sure to load our metaboxes only when the current post type ...
				 * ... matches our custom post type. This will prevent JS conflicts with other plugins.
				 * Note: The portion {!empty($_POST)} will ensure AJAX requests can be executed!
				 * @since 3.1
				 * @updated 3.5 | Load "Add locations" metabox/widget in selected post types of plugin settings */
 
				$current_post_type = $this->cspm_get_current_post_type();		

				if($current_post_type == $this->object_type || in_array($current_post_type, $this->post_types) || !empty($_POST)){
					
					/**
					 * Include and setup custom metaboxes and fields */
							
					if(file_exists($this->plugin_path . 'admin/cpt/cspm-metaboxes.php')){
						
						require_once( $this->plugin_path . 'admin/cpt/cspm-metaboxes.php' );
					
						if(class_exists('CspmMetaboxes')){
							
							new CspmMetaboxes(array(
								'plugin_path' => $this->plugin_path, 
								'plugin_url' => $this->plugin_url,
								'plugin_settings' => $this->shared_plugin_settings,
								'metafield_prefix' => $this->metafield_prefix,
								'object_type' => $this->object_type,
							));				
							
						}
						
					}
					
				}
				
				/**
				 * Ajax functions */
			
				add_action('wp_ajax_cspm_regenerate_markers', array($this, 'cspm_regenerate_markers'));
				 
				/**
				 * Add plugin menu */
				 
				add_action('admin_menu', array($this, 'cspm_admin_menu'));

				/**
				 * Add marker object to the option "cspm_markers_array" after saving or editing a post
				 * @since 3.5 */
				 
				add_action('wp_insert_post', array($this, 'cspm_save_marker_object'), 10, 2);
				
				/**
				 * Remove all metaboxes from our CPT except for the ones we built */
				 
				add_action('current_screen', array($this, 'cspm_remove_all_metaboxes'));
				
				/**
				 * Register new carousel image sizes */
				 			
				add_action('admin_init', array($this, 'cspm_add_carousel_image_sizes'));
					
				/**
				 * Add custom links to plugin instalation area */
				 
				add_filter('plugin_row_meta', array($this, 'cspm_plugin_meta_links'), 10, 2);
				add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'cspm_add_plugin_action_links'));
						
				/**
				 * Alter the list of acceptable file extensions WordPress checks during media uploads
				 * @since 2.7 */
				 
				add_filter('upload_mimes', array($this, 'cspm_custom_upload_mimes'));

				/**
				 * Display a message in the admin to promote for Progress Map extensions
				 * @since 2.8
				 */
				 
				add_action('admin_notices', array($this, 'cspm_admin_notices'));

				/**
				 * Dismiss admin notice(s)
				 * @since 3.8.1
				 */
				 
				add_action('admin_init', array($this, 'cspm_dismiss_admin_notices'));
			
				/**
				 * Add a custom column on all posts list showing the coordinates of each post
				 * @since 2.6.3 */

				if($this->custom_list_columns == 'yes'){
					foreach($this->post_types as $post_type){
						if($post_type == 'page'){
							add_filter('manage_pages_columns', array($this, 'cspm_manage_posts_columns')); // @since 2.6.3
							add_action('manage_pages_custom_column', array($this, 'cspm_manage_posts_custom_column'), 10, 2); // @since 2.6.3	
						}else{
							add_filter('manage_'.$post_type.'_posts_columns', array($this, 'cspm_manage_posts_columns')); // @since 2.6.3
							add_action('manage_'.$post_type.'_posts_custom_column', array($this, 'cspm_manage_posts_custom_column'), 10, 2); // @since 2.6.3
						}
					}
				}

				/**
				 * Executed when activating the plugin in order to run any sync code needed for the latest version of the plugin 
				 * @since 2.4 */
				 
				register_activation_hook(__FILE__, array($this, 'cspm_sync_settings_for_latest_version'));
				
				/**
				 * Duplicate map "link", "action" & "link style"
				 * @since 3.2 */
				 				
				add_filter('post_row_actions', array($this, 'cspm_duplicate_map_link'), 10, 2);		
				add_action('admin_action_cspm_duplicate_map', array($this, 'cspm_duplicate_map'));
				add_action('admin_head', array($this, 'cspm_duplicate_map_link_style'));
				
				/**
				 * Regenerate map markers "link", "action" & "link style"
				 * @since 3.9 */
				 				
				add_filter('post_row_actions', array($this, 'cspm_regenerate_map_markers_link'), 10, 2);		
				add_action('admin_action_cspm_regenerate_markers', array($this, 'cspm_regenerate_map_markers'));
				add_action('admin_head', array($this, 'cspm_regenerate_map_markers_link_style'));
				
			}else{
				
				/**
				 * Call .js and .css files */
				 
				add_action('wp_enqueue_scripts', array($this, 'cspm_register_styles'));
				add_action('wp_enqueue_scripts', array($this, 'cspm_register_scripts'));

			}
					
			/**
			 * Add Marker/Infobox Images Size */
			 
			if(function_exists('add_image_size')){
				add_image_size('cspm-marker-thumbnail', 100, 100, true);
				add_image_size('cspm-horizontal-thumbnail-map', 204, 150, true);
				add_image_size('cspm-vertical-thumbnail-map', 204, 120, true);
				add_image_size('cspm-modal-fullscreen', 1024, 1024, true); //@since 3.5
				add_image_size('cspm-modal-carousel-preview', 700, 300, true); //@since 3.5
				add_image_size('cspm-modal-single', 700, 400, true); //@since 3.5
				add_image_size('cspm-modal-carousel-thumb', 120, 80, true); //@since 3.5
			}
			
			/**
			 * Include and load the main map shortcode class */
			
			if(file_exists($this->plugin_path . 'shortcodes/cspm-main-map.php')){
				
				require_once( $this->plugin_path . 'shortcodes/cspm-main-map.php' );
				
				if(class_exists('CspmMainMap')){
					
					$CspmMainMap = new CspmMainMap(array(
						'init' => true, 
						'plugin_settings' => $this->shared_plugin_settings,
						'metafield_prefix' => $this->metafield_prefix,
						'object_type' => $this->object_type,
					));
				
					$CspmMainMap->cspm_hooks();
					
				}
					
			}
			
			/**
			 * Include and load all shortcodes/maps classes */
			
			$cspm_shortcodes_classes = array(
				'cspm_light_map' => 'shortcodes/cspm-light-map.php',
				'cspm_static_map' => 'shortcodes/cspm-static-map.php',
				'cspm_streetview_map' => 'shortcodes/cspm-streetview-map.php',
				'cspm_contact_map' => 'shortcodes/cspm-contact-map.php',
				'cspm_frontend_form' => 'shortcodes/cspm-frontend-form.php',
				'cspm_route_map' => 'shortcodes/cspm-route-map.php', // @since 3.6
			);
			
				foreach($cspm_shortcodes_classes as $class_key => $class_path){
					if(file_exists($this->plugin_path . $class_path))
						require_once($this->plugin_path . $class_path);
				}

		}


		/**
		 * Register & Enqueue CSS files 
		 *
		 * @updated 3.0
		 * @updated 3.5 [Changed files URLs]
		 */
		function cspm_register_styles(){
			
			do_action('cspm_before_register_style');

			$min_prefix = $this->combine_files == 'seperate' ? '' : '.min' ;

			/**
			 * Font Style */
			
			if($this->remove_google_fonts == 'enable'){  	
				wp_register_style('cspm-font', '//fonts.googleapis.com/css?family=Source+Sans+Pro:400,200,200italic,300,300italic,400italic,600,600italic,700,700italic&subset=latin,vietnamese,latin-ext');				
			}

			/**
			 * icheck skins */
							
			$icheck_skins = array('flat', 'futurico', 'polaris', 'line', 'minimal', 'square');
			$icheck_skin_colors = array('black', 'red', 'green', 'blue', 'aero', 'grey', 'orange', 'yellow', 'pink', 'purple');
			
			foreach($icheck_skins as $skin){

				foreach($icheck_skin_colors as $color){
				
					$color_file_name = ($color == 'black') ? $skin : $color;
					$color_file_path = 'assets/css/icheck/'.$skin.'/'.$color_file_name.$min_prefix.'.css';
					
					if(file_exists($this->plugin_path . $color_file_path))
						wp_register_style('jquery-icheck-'.$skin.'-'.$color_file_name, $this->plugin_url.$color_file_path, array(), $this->plugin_version);
					
				}
				
			}
				
			$script_handle = 'cspm-style';
				
			/**
			 * Bootstrap */
			
			if($this->remove_bootstrap == 'enable')	
				wp_register_style('cspm-custom-bootstrap', $this->plugin_url .'assets/css/bootstrap/bootstrap'.$min_prefix.'.css', array(), $this->plugin_version);
			
			/**
			 * jCarousel */
			 
			wp_register_style('jquery-jcarousel-06', $this->plugin_url .'assets/css/jCarousel/jcarousel'.$min_prefix.'.css', array(), $this->plugin_version);
			
			/**
			 * Custom Scroll bar */
			 
			wp_register_style('jquery-mcustomscrollbar', $this->plugin_url .'assets/css/customScroll/jquery.mCustomScrollbar'.$min_prefix.'.css', array(), $this->plugin_version);

			/**
			 * Range Slider
			 * @edited 5.6 */
							
			wp_register_style('jquery-ion-rangeslider', $this->plugin_url .'assets/css/rangeSlider/ion.rangeSlider'.$min_prefix.'.css', array(), $this->plugin_version);
						
			/**
			 * iziModal
			 * @since 3.5 */
			 
			wp_register_style('jquery-izimodal', $this->plugin_url .'assets/css/izimodal/iziModal'.$min_prefix.'.css', array(), $this->plugin_version);

			/**
			 * iziToast 
			 * @since 3.5 */
			
			wp_register_style('jquery-izitoast', $this->plugin_url .'assets/css/izitoast/iziToast'.$min_prefix.'.css', array(), $this->plugin_version);
			
			/**
			 * Snazzy Infobox
			 * @since 3.9 */
			
			wp_register_style('snazzy-info-window', $this->plugin_url .'assets/css/snazzy-infobox/snazzy-info-window'.$min_prefix.'.css', array(), $this->plugin_version);
			
			/**
			 * Tail Select
			 * @since 5.6 */
			
			wp_register_style('tail-select', $this->plugin_url .'assets/css/tail-select/tail.select-light-feather'.$min_prefix.'.css', array(), $this->plugin_version);
			
			/** 
			 * Progress Map styles */
			
			wp_register_style('nprogress', $this->plugin_url .'assets/css/nProgress/nprogress'.$min_prefix.'.css', array(), $this->plugin_version);
			
			wp_register_style('cspm-custom-animate', $this->plugin_url .'assets/css/animate/animate'.$min_prefix.'.css', array(), $this->plugin_version);
				
			wp_register_style($script_handle, $this->plugin_url .'assets/css/main/style'.$min_prefix.'.css', array(), $this->plugin_version);
			
			do_action('cspm_after_register_style');
		
			/**
			 * Add custom header script */
			
			wp_add_inline_style($script_handle, $this->cspm_main_colors().$this->custom_css);			
			
			/**
			 * Add custom header script */
			 
			add_filter('wp_head', array($this, 'cspm_header_script'));
			
		}	
		
		
		/**
		 * Register & Enqueue JS files 
		 *
		 * @updated 3.0
		 * @updated 3.5 [Changed files URLs]
		 * @updated 4.6 [Added nearby places template page URL]
		 */
		function cspm_register_scripts(){		
						
			if(class_exists('CspmMainMap'))
				$CspmMainMap = CspmMainMap::this();
			
			/**
			 * Get "Nearby places" template page ID
			 * @since 4.6 */
			
			$nearby_places_page_ID = $this->cspm_get_page_by_template_name(
				array('template_name' => 'templates/nearby-places-modal.php')
			);
			
			/**
			 * Localize the script with new data */
			 
			$wp_localize_script_args = array(
				
				'ajax_url' => admin_url('admin-ajax.php'),
				'plugin_url' => $this->plugin_url,
			
				/**
				 * [@map_script_args] | Default settings to use for maps like "Light map", "Nearby map ext", etc...
				 * Note: we'll use the key "initial" to identify the default map settings! */
					
				'map_script_args' => array(
					
					'initial' => array(
					
						/**
						 * Even empty, we need them inside AJAX functions */
						 
						'map_object_id' => '',
						'map_settings' => '',
									
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
						'big_cluster_size' => $CspmMainMap->cspm_get_image_size(
							array(
								'path' => $CspmMainMap->cspm_get_image_path_from_url($this->big_cluster_icon),
								'retina' => $this->retinaSupport,
								'default_height' => $this->big_cluster_icon_height,
								'default_width' => $this->big_cluster_icon_width,				
							)
						),
						'medium_cluster_icon' => $this->medium_cluster_icon,
						'medium_cluster_size' => $CspmMainMap->cspm_get_image_size(
							array(
								'path' => $CspmMainMap->cspm_get_image_path_from_url($this->medium_cluster_icon),
								'retina' => $this->retinaSupport,
								'default_height' => $this->medium_cluster_icon_height,
								'default_width' => $this->medium_cluster_icon_width,				
							)
						),						
						'small_cluster_icon' => $this->small_cluster_icon,
						'small_cluster_size' => $CspmMainMap->cspm_get_image_size(
							array(
								'path' => $CspmMainMap->cspm_get_image_path_from_url($this->small_cluster_icon),
								'retina' => $this->retinaSupport,
								'default_height' => $this->small_cluster_icon_height,
								'default_width' => $this->small_cluster_icon_width,				
							)
						),												
						'cluster_text_color' => $this->cluster_text_color,
						'grid_size' => $this->gridSize,
						'retinaSupport' => $this->retinaSupport,
						'initial_map_style' => $this->initial_map_style,
						'markerAnimation' => $this->markerAnimation, // @since 2.5
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
						'gestureHandling' => $this->gestureHandling, //@since 5.6.5
		
						/**
						 * Geotarget
						 * @since 2.8 */
						 
						'geo' => $this->geoIpControl,
						'show_user' => $this->show_user,
						'user_marker_icon' => $this->user_marker_icon,
						'user_map_zoom' => $this->user_map_zoom,
						'user_circle' => $this->user_circle,
					
						/**
						 * Search form settings */
						 
						'before_search_address' => '', //@since 2.8, Add text before the search field value
						'after_search_address' => '', //@since 2.8, Add text after the search field value
						
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
						 * Main colors 
						 * @since 4.2 */
						 			
						'main_rgb_color' => $this->main_rgb_color,
						'main_rgb_hover_color' => $this->main_rgb_hover_color,
						'main_hex_color' => $this->main_hex_color,
						'main_hex_hover_color' => $this->main_hex_hover_color,
			
						/**
						 * Modal options
						 * @since 4.6 */
						
						'modal_width' => $this->modal_width,
						'modal_width_unit' => $this->modal_width_unit,
						'modal_height' => $this->modal_height,
						'modal_openFullscreen' => $this->modal_openFullscreen,
						'modal_allow_fullscreen' => $this->modal_allow_fullscreen, //@since 4.7
						'modal_top_margin' => $this->modal_top_margin, //@since 5.5
						'modal_bottom_margin' => $this->modal_bottom_margin, //@since 5.5
			
					)
				
				),
				
				/**
				 * Geotarget
				 * @since 2.8 */
				 
				'geoErrorTitle' => esc_attr__('Give Maps permission to use your location!', 'cspm'),				
				'geoErrorMsg' => esc_attr__('If you can\'t center the map on your location, a couple of things might be going on. It\'s possible you denied Google Maps access to your location in the past, or your browser might have an error. Please visit the <a href="https://support.google.com/maps/answer/2839911?hl=en" target="_blank" style="color:#202020; text-decoration:underline;">Google Maps Help</a> for more details about fixing this issue.', 'cspm'),				
				'geoDeprecateMsg' => esc_attr__('Browsers no longer supports obtaining the user\'s location using the HTML5 Geolocation API from pages delivered by non-secure connections. This means that the page that\'s making the Geolocation API call must be served from a secure context such as HTTPS.', 'cspm'), //@since 2.8.3
				
				/**
				 * Cluster text
				 * @since 2.8 */
				 
				'cluster_text' => esc_attr__('Click to view all markers in this area', 'cspm'),
				
				/**
				 * Map Messages
				 * @since 3.2 */
				 
				'new_marker_selected_msg' => esc_attr__('A new location has been selected and can be used to display nearby points of interest!', 'cspm'),	
				'no_marker_selected_msg' => esc_attr__('First, select a marker/location from the map!', 'cspm'),
				'circle_reached_max_msg' => esc_attr__('The circle has reached the maximum distance!', 'cspm'),
				'circle_reached_min_msg' => esc_attr__('The circle has reached the minimum distance!', 'cspm'),
				'max_search_radius_msg' => esc_attr__('You have reached the maximum radius of search!', 'cspm'),
				'min_search_radius_msg' => esc_attr__('You have reached the minimum radius of search!', 'cspm'),
				'no_nearby_point_found' => esc_attr__('No nearby points of interest has been found!', 'cspm'),
				
				/**
				 * The message to display to inform about hiding an image overlay
				 * @since 3.5 */
				 
				'hide_img_overlay_msg' => apply_filters('cspm_hide_img_overlay_msg', esc_attr__('Click to hide & show the image overlay', 'cspm')),
				
				/**
				 * Messages to display to inform about hidden options
				 * @since 3.5 */
				
				'media_format_msg' => array(
					'format_all_msg' => apply_filters('cspm_format_all_msg', esc_attr__('Click on the marker to pop-up the media files', 'cspm')),
					'format_gallery_msg' => apply_filters('cspm_format_gallery_msg', esc_attr__('Click on the marker to pop-up the image gallery', 'cspm')),
					'format_image_msg' => apply_filters('cspm_format_image_msg', esc_attr__('Click on the marker to pop-up the image', 'cspm')),
					'format_audio_msg' => apply_filters('cspm_format_audio_msg', esc_attr__('Click on the marker to pop-up to the audio file', 'cspm')),
					'format_embed_msg' => apply_filters('cspm_format_embed_msg', esc_attr__('Click on the marker to pop-up the visual file', 'cspm')),
					'format_link_msg' => apply_filters('cspm_format_link_msg', esc_attr__('Click on the marker to pop-up the post details', 'cspm')), //@since 3.6
					'format_nearby_places_msg' => (class_exists('CspmNearbyMap') && !empty($nearby_places_page_ID)) ? apply_filters('cspm_format_nearby_places_msg', esc_attr__('Click on the marker to pop-up the map of all nearby places', 'cspm')) : '', //@since 4.6
				),
				'broken_img_msg' => apply_filters('cspm_broken_img_msg', esc_attr__('The image appears to be broken', 'cspm')),
				'resize_circle_msg' => apply_filters('cspm_resize_circle_msg', esc_attr__('Resize the circle to show less or more results', 'cspm')),
				'show_nearby_details_msg' => apply_filters('cspm_show_nearby_details_msg', esc_attr__('Click on the nearby marker to display the route and the place details', 'cspm')),
				'polyline_url_msg' => apply_filters('cspm_polyline_url_msg', esc_attr__('Click on the polyline to open the polyline associated link', 'cspm')),
				'polygon_url_msg' => apply_filters('cspm_polygon_url_msg', esc_attr__('Click on the polygon to open the polygon associated link', 'cspm')),
				'image_redirect_url_msg' => apply_filters('cspm_image_redirect_url_msg', esc_attr__('Click on the image to open the image associated link', 'cspm')), //@since 4.0
				
				/**
				 * "Places API" Error messages
				 * @since 3.9 */
				
				'places_api_status_codes' => array(
					
					/**
					 * Status displayed as warnings */
					 
					'warning' => array(
						'ZERO_RESULTS' => esc_html__('We couldn\'t find any result!', 'cspm'),
						'NOT_FOUND' => esc_html__('The place referenced was not found', 'cspm'),
					),
					
					/**
					 * Status displayed as errors */
					 					
					'error' => array(
						'REQUEST_DENIED' => esc_html__('The application is not allowed to use the <strong>Places API</strong>. If you are the administrator of this website, please visit the <strong><a href="https://developers.google.com/places/web-service/faq#why_do_i_keep_receiving_status_request_denied" target="_blank" style="color:#202020; text-decoration:underline;">Troubleshooting</strong></a> page for more details on fixing this issue.', 'cspm'),
						'UNKNOWN_ERROR' => esc_html__('The request could not be processed due to a server error. The request may succeed if you try again.', 'cspm'),
					),
					
					/**
					 * Status displayed in the browser console
					 * Note: No HTML tags in the message! */
					 					
					'console' => array(
						'OVER_QUERY_LIMIT' => esc_html__('The application has gone over its request quota. Try again later.', 'cspm'),
						'INVALID_REQUEST' => esc_html__('This request was invalid.', 'cspm'),
					)
										
				), 
				
				/** 
				 * "Directions API" Error messages
				 * @since 3.9 */
				
				'directions_api_status_codes' => array(
					
					/**
					 * Status displayed as warnings */
					 
					'warning' => array(
						'ZERO_RESULTS' => esc_html__('No route could be found to your destination. Try another travel mode!', 'cspm'),
						'NOT_FOUND' => esc_html__('No route could be found to your destination. Try another address!', 'cspm'),
					),
					
					/**
					 * Status displayed as errors */
					 					
					'error' => array(
						'REQUEST_DENIED' => esc_html__('The application is not allowed to use the <strong>Directions API</strong>. If you are the administrator of this website, please visit your <strong><a href="https://console.developers.google.com/" target="_blank" style="color:#202020; text-decoration:underline;">Google Cloud Platform Console</strong></a> and enable the <strong>Directions API</strong>.', 'cspm'),
						'INVALID_REQUEST' => esc_html__('The provided DirectionsRequest was invalid. The most common causes of this error code are requests that are missing either an origin or destination, or a transit request that includes waypoints.', 'cspm'),
						'UNKNOWN_ERROR' => esc_html__('A directions request could not be processed due to a server error. The request may succeed if you try again.', 'cspm'),											
					),
					
					/**
					 * Status displayed in the browser console
					 * Note: No HTML tags in the message! */
					 					
					'console' => array(
						'MAX_WAYPOINTS_EXCEEDED' => esc_html__('Too many DirectionsWaypoint fields were provided in the DirectionsRequest. See the "limits for way points" https://developers.google.com/maps/documentation/javascript/directions#waypoint-limits', 'cspm'),
						'MAX_ROUTE_LENGTH_EXCEEDED' => esc_html__('The requested route is too long and cannot be processed. This error occurs when more complex directions are returned. Try reducing the number of waypoints, turns, or instructions.', 'cspm'),											
						'OVER_QUERY_LIMIT' => esc_html__('The webpage has sent too many requests within the allowed time period.', 'cspm'),						
					)
										
				), 
				
				/** 
				 * "Distance Matrix API" Error messages
				 * @since 3.9 */
				
				'distance_matrix_api_status_codes' => array(
					
					/**
					 * Status displayed as warnings */
					 
					'warning' => array(),
					
					/**
					 * Status displayed as errors */
					 					
					'error' => array(),
					
					/**
					 * Status displayed in the browser console
					 * Note: No HTML tags in the message! */
					 					
					'console' => array(
						'ZERO_RESULTS' => esc_html__('No route could be found between the origin and destination.', 'cspm'),
						'REQUEST_DENIED' => esc_html__('The application is not allowed to use the "Distance Matrix API". If you are the administrator of this website, please visit your "Google Cloud Platform Console" https://console.developers.google.com/ and enable the "Distance Matrix API"', 'cspm'),
						'OVER_QUERY_LIMIT' => esc_html__('Your application has requested too many elements within the allowed time period. The request should succeed if you try again after a reasonable amount of time.', 'cspm'),
						'UNKNOWN_ERROR' => esc_html__('A Distance Matrix request could not be processed due to a server error. The request may succeed if you try again.', 'cspm'),
						'INVALID_REQUEST' => esc_html__('The provided DirectionsRequest was invalid. The most common causes of this error code are requests that are missing either an origin or destination, or a transit request that includes waypoints.', 'cspm'),
						'NOT_FOUND' => esc_html__('The origin and/or destination of this pairing could not be geocoded.', 'cspm'),
						'MAX_ELEMENTS_EXCEEDED' => esc_html__('The product of origins and destinations exceeds the "per-query limit". Please visit this page for more info https://developers.google.com/maps/documentation/javascript/distancematrix#usage_limits_and_requirements', 'cspm'),
						'MAX_DIMENSIONS_EXCEEDED' => esc_html__('Your request contained more than 25 origins, or more than 25 destinations.', 'cspm'),
					)
										
				), 
				
				/** 
				 * "Geocoding API" Error messages
				 * @since 3.9 */
				
				'geocoding_api_status_codes' => array(
					
					/**
					 * Status displayed as warnings */
					 
					'warning' => array(
						'ZERO_RESULTS' => esc_html__('No result was found for this address.', 'cspm'),
						'INVALID_REQUEST' => esc_html__('Please make sure to provide an address or location coordinates.', 'cspm'),
					),
					
					/**
					 * Status displayed as errors */
					 					
					'error' => array(
						'REQUEST_DENIED' => esc_html__('The application is not allowed to use the <strong>Geocoding API</strong>. If you are the administrator of this website, please visit your <strong><a href="https://console.developers.google.com/" target="_blank" style="color:#202020; text-decoration:underline;">Google Cloud Platform Console</strong></a> and enable the <strong>Geocoding API</strong>.', 'cspm'),
						'UNKNOWN_ERROR' => esc_html__('A geocoding request could not be processed due to a server error. The request may succeed if you try again.', 'cspm'),
						'ERROR' => esc_html__('The request timed out or there was a problem contacting the Google servers. The request may succeed if you try again.', 'cspm'),
					),
					
					/**
					 * Status displayed in the browser console
					 * Note: No HTML tags in the message! */
					 					
					'console' => array(
						'OVER_QUERY_LIMIT' => esc_html__('The application has gone over its request quota. Try again later.', 'cspm'),
					)
					
				),
					
				/**
				 * Nearby Places template page link
				 * @since 4.6 */
				
				'nearby_places_page_url' => (class_exists('CspmNearbyMap') && !empty($nearby_places_page_ID)) ? get_permalink($nearby_places_page_ID) : '',
				
				/**
				 * Misc strings
				 * @since 5.6 */
				
				'all' => apply_filters('__cspm_all', esc_html__('All', 'cspm')),
				'none' => apply_filters('__cspm_none', esc_html__('None', 'cspm')),
				'type_to_search' => apply_filters('__cspm_type_to_search', esc_html__('Type in to search ...', 'cspm')),
			);
			
			do_action('cspm_before_register_script');
						
			/**
			 * GMaps API */
			
			if(!in_array('disable_frontend', $this->remove_gmaps_api)){ 
				wp_register_script('cs-gmaps-api-v3', $this->gmaps_formatted_url, array(), false, true);
			}
				 
			$localize_script_handle = 'cspm-script';
			
			$min_prefix = $this->combine_files == 'seperate' ? '' : '.min' ;

			/**
			 * GMap3 jQuery Plugin */
			 
			wp_register_script('cspm-gmap3', $this->plugin_url .'assets/js/gmap3/gmap3'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);		

			/**
			 * Marker Clusterer | No dependencies!  */
			 
			wp_register_script('markerclustererplus', $this->plugin_url .'assets/js/markerClusterer/MarkerClustererPlus'.$min_prefix.'.js', array(), $this->plugin_version, true);
			
			/**
			 * ScrollTo jQuery Plugin
			 * Used for the extensions, "List & Filter" & "Nearby Places"
			 * @since 2.8.6 */
			
			if(class_exists('CspmListFilter') || class_exists('CspmNearbyMap'))
				wp_register_script('jquery-scrollto', $this->plugin_url .'assets/js/scrollTo/jquery.scrollTo'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);
			
			/**
			 * Touche Swipe */
			 
			wp_register_script('jquery-touchswipe', $this->plugin_url .'assets/js/toucheSwipe/jquery.touchSwipe'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);		
			
			/**
			 * jCarousel & jQuery Easing */
			 
			wp_register_script('jquery-jcarousel-06', $this->plugin_url .'assets/js/jCarousel/jquery.jcarousel'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);
			wp_register_script('jquery-easing', $this->plugin_url .'assets/js/easing/jquery.easing.1.3'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);
			
			/**
			 * Custom Scroll bar */
			 
			wp_register_script('jquery-mcustomscrollbar', $this->plugin_url .'assets/js/customScroll/jquery.mCustomScrollbar'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);		
			
			/**
			 * jQuery Mousewheel */
			 
			wp_register_script('jquery-mousewheel', $this->plugin_url .'assets/js/mouseWheel/jquery.mousewheel'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);		

			/**
			 * icheck */
							
			wp_register_script('jquery-icheck', $this->plugin_url .'assets/js/icheck/jquery.icheck'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);
			
			/**
			 * Progress Bar loader */
			 
			wp_register_script('nprogress', $this->plugin_url .'assets/js/nProgress/nprogress'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);

			/**
			 * Range Slider */
			
			wp_register_script('jquery-ion-rangeslider', $this->plugin_url .'assets/js/rangeSlider/ion.rangeSlider'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);
						
			/**
			 * iziModal
			 * @since 3.5 */
			 
			wp_register_script('jquery-izimodal', $this->plugin_url .'assets/js/izimodal/iziModal'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);

			/**
			 * iziToast | No dependencies! 
			 * @since 3.5 */
			
			wp_register_script('jquery-izitoast', $this->plugin_url .'assets/js/izitoast/iziToast'.$min_prefix.'.js', array(), $this->plugin_version, true);

			/**
			 * Siema | No dependencies!  
			 * @since 3.5 */
			
			wp_register_script('siema', $this->plugin_url .'assets/js/siema/siema.min.js', array(), $this->plugin_version, true);
			
			/**
			 * Snazzy Infobox
			 * @since 3.9 */
			
			wp_register_script('snazzy-info-window', $this->plugin_url .'assets/js/snazzy-infobox/snazzy-info-window'.$min_prefix.'.js', array(), $this->plugin_version, true);
			
			/**
			 * Tail Select
			 * @since 5.6 */
			
			wp_register_script('tail-select', $this->plugin_url .'assets/js/tail-select/tail.select'.$min_prefix.'.js', array(), $this->plugin_version, true);

			/**
			 * Gradstop | Generate gradient color for Heatmap Layer
			 * @since 5.3 */
			 
			wp_register_script('gradstop', $this->plugin_url .'assets/js/gradstop/gradstopUMD'.$min_prefix.'.js', array(), $this->plugin_version, true);
											
			/**
			 * jQuery Sidebar
			 * @since 1.0 */ 
			 
			wp_register_script('jquery-sidebar', $this->plugin_url .'assets/js/jquery-sidebar/jquery.sidebar'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);				

			/**
			 * Progress Map Script */
			 
			wp_register_script($localize_script_handle, $this->plugin_url .'assets/js/main/progress_map'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);					
			
			/**
			 * Localize the script with new data */
 
			wp_localize_script($localize_script_handle, 'progress_map_vars', $wp_localize_script_args);

			do_action('cspm_after_register_script');
			
		}		
		
		/**
		 * Print Custom CSS/JS in the page header 
		 *
		 * @since 2.5
		 * @updated 2.8 | 4.9.1
		 */
		function cspm_header_script(){
			
			$header_script = '';
			
			/**
			 * Prevent $(document).ready from being fired twice */			 
			
			$header_script .= 'var _CSPM_DONE = {}; var _CSPM_MAP_RESIZED = {}';
			
			wp_add_inline_script('cspm-script', $header_script, 'before');
			
		}
		
		
		/**
		 * Get the value of a setting
		 *
		 * @since 2.4 
		 * @updated 4.0
		 */
		function cspm_get_setting($setting_id, $default_value = ''){

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
		 * This contains all custom fields names used in our "Add locations" metabox
		 *
		 * @since 4.0
		 */
		function cspm_define_metabox_field_names(){
			 
			if(!defined('CSPM_ADDRESS_FIELD')) define('CSPM_ADDRESS_FIELD', 'codespacing_progress_map_address');
			if(!defined('CSPM_LATITUDE_FIELD')) define('CSPM_LATITUDE_FIELD', $this->latitude_field_name);
			if(!defined('CSPM_LONGITUDE_FIELD')) define('CSPM_LONGITUDE_FIELD', $this->longitude_field_name);	
			if(!defined('CSPM_SECONDARY_LAT_LNG_FIELD')) define('CSPM_SECONDARY_LAT_LNG_FIELD', 'codespacing_progress_map_secondary_lat_lng');												
			if(!defined('CSPM_MARKER_ICON_FIELD')) define('CSPM_MARKER_ICON_FIELD', 'cspm_primary_marker_image'); /* @since 2.8 */
			if(!defined('CSPM_SINGLE_POST_IMG_ONLY_FIELD')) define('CSPM_SINGLE_POST_IMG_ONLY_FIELD', 'cspm_single_post_marker_img_only'); /* @since 3.0 */
			if(!defined('CSPM_FORMAT_FIELD')) define('CSPM_FORMAT_FIELD', $this->metafield_prefix.'_post_format'); //@since 3.5
			if(!defined('CSPM_MEDIA_MARKER_CLICK_FIELD')) define('CSPM_MEDIA_MARKER_CLICK_FIELD', $this->metafield_prefix.'_enable_media_marker_click'); //@since 5.5
			if(!defined('CSPM_AUDIO_FIELD')) define('CSPM_AUDIO_FIELD', $this->metafield_prefix.'_post_audio'); //@since 3.5
			if(!defined('CSPM_IMAGE_FIELD')) define('CSPM_IMAGE_FIELD', $this->metafield_prefix.'_post_image'); //@since 3.5
			if(!defined('CSPM_EMBED_FIELD')) define('CSPM_EMBED_FIELD', $this->metafield_prefix.'_post_embed'); //@since 3.5
			if(!defined('CSPM_GALLERY_FIELD')) define('CSPM_GALLERY_FIELD', $this->metafield_prefix.'_post_gallery'); //@since 3.5
			
			/**
			 * Define session name used to override query args
			 * @since 4.7 */
			 
			if(!defined('CSPM_SESSION_OVERRIDE_QUERY_ARGS')) define('CSPM_SESSION_OVERRIDE_QUERY_ARGS', $this->metafield_prefix.'_session_override_query_args');
			 	
		}
		
		
		/**
		 * This will add any post coordinates to the markers object.
		 * This is necessary to display the location on the map!
		 *
		 * @since 3.5
		 * @updated 5.6.3 | Possibility to convert address to latLng
		 */
		function cspm_save_marker_object($post_id, $post){
				
			$post_type = $post->post_type;
		
			if(empty($post_id) || empty($post_type))
				return;
				
			$markers_object = (array) get_option('cspm_markers_array');
			$post_markers_object = array();
			
			$latitude = get_post_meta($post_id, CSPM_LATITUDE_FIELD, true);
			$longitude = get_post_meta($post_id, CSPM_LONGITUDE_FIELD, true);
			$address = get_post_meta($post_id, CSPM_ADDRESS_FIELD, true); //@since 5.4
			
			/**
			 * Convert address to coordinates when LatLng is not provided and save them
			 * @since 5.6.3 */
			
			if(!empty($address) && (empty($latitude) || empty($longitude))){
				
				$address_coordinates = $this->cspm_convert_address_to_latlng(array(
					'address' => $address
				));
				
				if($this->cspm_setting_exists('lat', $address_coordinates) && $this->cspm_setting_exists('lng', $address_coordinates)){
					update_post_meta($post_id, CSPM_LATITUDE_FIELD, $address_coordinates['lat']);
					update_post_meta($post_id, CSPM_LONGITUDE_FIELD, $address_coordinates['lng']);
				}				

			}
			
			/**
			 * Save the Lat & Lng Fields */
				
			if(!empty($latitude) && !empty($longitude)){								  

				
				/**
				 * Get this post taxonomies terms */
				 
				$post_taxonomy_terms = array();
				
				$post_taxonomies = get_object_taxonomies($post_type, 'names');	
				
				foreach($post_taxonomies as $taxonomy_name){
					$post_taxonomy_terms[$taxonomy_name] = wp_get_post_terms($post_id, $taxonomy_name, array(
						'fields' => 'ids',
					));
				}
				
				/**
				 * Build the marker object */
				 	
				$post_markers_object = array_merge(
					array(
						'lat' => $latitude,
						'lng' => $longitude,
						'address' => $address, //@since 5.4
						'post_id' => $post_id,
						'post_tax_terms' => $post_taxonomy_terms,
						'is_child' => 'no',
						'child_markers' => array(),
					),
					$this->cspm_get_post_media($post_id)
				);																 
				
				/**
				 * Secondary latLng */
				
				$secondary_latlng = get_post_meta($post_id, CSPM_SECONDARY_LAT_LNG_FIELD, true);
				
				if(!empty($secondary_latlng)){
					
					$child_markers = array();
						
					$j = 0;
									
					$lats_lngs = explode(']', $secondary_latlng);	
							
					foreach($lats_lngs as $single_coordinate){
					
						$strip_coordinates = str_replace(array('[', ']', ' '), '', $single_coordinate);
						
						$coordinates = explode(',', $strip_coordinates);
						
						if(isset($coordinates[0], $coordinates[1]) && !empty($coordinates[0]) && !empty($coordinates[1])){
							
							$lat = $coordinates[0];
							$lng = $coordinates[1];
							
							$child_markers[] = array(
								'lat' => $lat,
								'lng' => $lng,
								'address' => '', //@since 5.4
								'post_id' => $post_id,
								'post_tax_terms' => $post_taxonomy_terms,
								'is_child' => 'yes_'.$j
							);
							
							$lat = '';
							$lng = '';
							$j++;
						
						} 
						
						$post_markers_object['child_markers'] = $child_markers;
						
					}
				
				}
				
				$markers_object[$post_type]['post_id_'.$post_id] = $post_markers_object;

				update_option('cspm_markers_array', $markers_object);

			}
					
		}

		
		/**
		 * This will return an array containing the post media format & files
		 *
		 * @since 3.5
		 */
		function cspm_get_post_media($post_id, $default = array()){
				
			extract( wp_parse_args( $default, array(
				'format' => get_post_meta($post_id, CSPM_FORMAT_FIELD, true),
				'marker_click' => get_post_meta($post_id, CSPM_MEDIA_MARKER_CLICK_FIELD, true), //@since 5.5
				'gallery' => get_post_meta($post_id, CSPM_GALLERY_FIELD, true),	// Images URLs	
				'image_id' => get_post_meta($post_id, CSPM_IMAGE_FIELD.'_id', true), // Image URL
				'audio_url' => get_post_meta($post_id, CSPM_AUDIO_FIELD, true), // Audio URL
				'embed_url' => get_post_meta($post_id, CSPM_EMBED_FIELD, true), // Embed URL				
			)));

			if(empty($post_id))
				return array();
				
			/**
			 * Init the media array */
			 
			$media_array = array();
				
			/**
			 * Build this post gallery (Location format: Gallery)
			 * @since 3.5 */
			
			$post_gallery = $gallery;
				
			if(empty($post_gallery))
				$post_gallery = array();
				
			$gallery_imgs = array();
			
			foreach($post_gallery as $img_id => $img_url){
					
				$fullscreen_img = wp_get_attachment_image_src($img_id, 'full');
				$single_img = wp_get_attachment_image_src($img_id, 'cspm-modal-single');
				$preview_img = wp_get_attachment_image_src($img_id, 'cspm-modal-carousel-preview');
				$thumb_img = wp_get_attachment_image_src($img_id, 'cspm-modal-carousel-thumb');
				
				$gallery_imgs[] = array(
					'fullscreen' => $this->cspm_setting_exists(0, $fullscreen_img, ''),
					'single' => $this->cspm_setting_exists(0, $single_img, ''),
					'preview' => $this->cspm_setting_exists(0, $preview_img, ''),
					'thumb' => $this->cspm_setting_exists(0, $thumb_img, ''),
				);
				
			}
			
			/**
			 * Get this post image (Location format: Image)
			 * @since 3.5 */
							
			$post_image_id = $image_id;
			
			$post_image = array();
			
			if($post_image_id){
					
				$fullscreen_img = wp_get_attachment_image_src($post_image_id, 'full');
				$single_img = wp_get_attachment_image_src($post_image_id, 'cspm-modal-single');					
				$preview_img = wp_get_attachment_image_src($post_image_id, 'cspm-modal-carousel-preview');
				$thumb_img = wp_get_attachment_image_src($post_image_id, 'cspm-modal-carousel-thumb');
				
				$post_image = array(
					'fullscreen' => $this->cspm_setting_exists(0, $fullscreen_img, ''),
					'single' => $this->cspm_setting_exists(0, $single_img, ''),
					'preview' => $this->cspm_setting_exists(0, $preview_img, ''),						
					'thumb' => $this->cspm_setting_exists(0, $thumb_img, ''),
				);
				
			}
			
			/**
			 * Get the embed thumbnail and HTML
			 * @since 3.5 */
			
			$embed = array(
				'title' => '',
				'type' => '',
				'thumb' => '',
				'provider' => '',
				'html' => '',																
			);
			
			if(!empty($embed_url)){
				 
				$wp_oembed = _wp_oembed_get_object(); // Get the WP_oEmbed object			
				$embed_data = (array) $wp_oembed->get_data($embed_url); // Get the oEmbed data from the embed URL | Return false on failure!
			
				if($embed_data){
				
					$embed['title'] = $this->cspm_setting_exists('title', $embed_data);
					$embed['type'] = $this->cspm_setting_exists('type', $embed_data);
					$embed['thumb'] = $this->cspm_setting_exists('thumbnail_url', $embed_data);
					$embed['provider'] = $this->cspm_setting_exists('provider_name', $embed_data);
					$embed['html'] = esc_html($this->cspm_setting_exists('html', $embed_data));
				
				}else $embed['html'] = esc_html(wp_oembed_get($embed_url));
				
			}
			
			/**
			 * Build this post media array
			 * @since 3.5 */
			 
			$media_array = array(
				'media' => array(
					'format' => $format,
					'marker_click' => $marker_click, //@since 5.5					
					'gallery' => $gallery_imgs,
					'image' => $post_image,
					'audio' => $audio_url,
					'embed' => $embed,					
				),
			);
			
			/**
			 * Add post data to media array
			 * @since 3.6 */
			 						
			if(class_exists('CspmMainMap')){
				$CspmMainMap = CspmMainMap::this();
				$media_array['media']['link'] = $CspmMainMap->cspm_get_permalink($post_id);
			}
			
			return $media_array;
			
		}
		
			
		/**
		 * Add the Laitude & the Longitude custom columns to the post type list
		 *
		 * @since 2.6.3 
		 */
		function cspm_manage_posts_columns($columns){
			
			$columns['pm_coordinates'] = esc_html__('PM. Coordinates', 'cspm');
				
			return $columns;
			
		}
		
		
		/**
		 * fill our Latitude & Longitude columns with data
		 *
		 * @since 2.6.3 
		 */		 
		function cspm_manage_posts_custom_column($column_name, $post_id){
			
			switch( $column_name ) {

				case 'pm_coordinates':
					$latitude = get_post_meta( $post_id, CSPM_LATITUDE_FIELD, true );
					$longitude = get_post_meta( $post_id, CSPM_LONGITUDE_FIELD, true );
					if(!empty($latitude) && !empty($longitude))
						echo '<div id="pm-coordinates-'.$post_id.'">'.$latitude.', '.$longitude.'</div>';
					else echo 'None';
				break;
		
			}
			
		}
		
		
		/** 
		 * This will make sure only our Metaboxes will be displayed in our Custom Post Type
		 *
		 * @since 3.0
		 * @updated 5.1 (Added metabox IDs to CSPM custom metaboxes arrays to display metaboxes in "WP 5.2")
		 */
		function cspm_remove_all_metaboxes(){
			
			$type = $this->object_type;
			$metafield_prefix = $this->metafield_prefix;

			/**
			 * Note: Regarding the timing issue, best place to reset the variable [@wp_meta_boxes] is just before it is used. 
			 * Metaboxes are printed via "do_meta_boxes()" function and inside it there are no hooks, however it contains ...
			 * ... "get_user_option( "meta-box-order_$page" )" ...
			 * ... and "get_user_option()" fires the filter "get_user_option_{$option}" so we can use it to perform our cleaning. */

			add_filter('get_user_option_meta-box-order_'.$type, function() use($type, $metafield_prefix){
			  
				global $wp_meta_boxes;
				
				if(get_current_screen()->id === $type){

					/**
					 * Publish Metabox */
					 
					$publish_metabox = $wp_meta_boxes[$type]['side']['core']['submitdiv'];
					
					/**
					 * Author Metabox */
					 
					$author_metabox = $wp_meta_boxes[$type]['normal']['core']['authordiv'];
					
					/**
					 * Progress Map Metaboxes */
					
					$cspm_normal_core_metaboxes_arrays = array();
					
						$cspm_normal_core_metaboxes = apply_filters('cspm_normal_high_metaboxes', array(
							$metafield_prefix.'_pm_cpt_metabox',
							$metafield_prefix.'_pm_metabox',
						));
					
						foreach($cspm_normal_core_metaboxes as $cspm_metabox){
							if(isset($wp_meta_boxes[$type]['normal']['high'][$cspm_metabox]))
								$cspm_normal_core_metaboxes_arrays[$cspm_metabox] = $wp_meta_boxes[$type]['normal']['high'][$cspm_metabox]; //@edited 5.1
						}
								
					$cspm_side_low_metaboxes_arrays = array();
					
						$cspm_side_low_metaboxes = apply_filters('cspm_side_low_metaboxes', array(
							$metafield_prefix.'_pm_shortcode_widget'
						));
						
						foreach($cspm_side_low_metaboxes as $cspm_metabox){
							if(isset($wp_meta_boxes[$type]['side']['low'][$cspm_metabox]))
								$cspm_side_low_metaboxes_arrays[$cspm_metabox] = $wp_meta_boxes[$type]['side']['low'][$cspm_metabox]; //@edited 5.1
						}
						
					/**
					 * Edit [@wp_meta_boxes] */
					 
					$wp_meta_boxes[$type] = array(
						'advanced' => array(),
						'side' => array(
							'core' => array(
								'submitdiv' => $publish_metabox
							),
							'low' => $cspm_side_low_metaboxes_arrays,
						),
						'normal' => array(
							'core' => array(
								'authordiv' => $author_metabox
							),
							'high' => $cspm_normal_core_metaboxes_arrays,
						),
					);					
														
					return array();
					
				}
			
				/**
				 * [PHP_INT_MAX] is the largest integer supported in this build of PHP. Usually int(2147483647. Available since PHP 5.0.5 */
				
			}, PHP_INT_MAX );
		  
		} 

						
		/**
		 * Add the plugin in the administration menu 
		 */
		function cspm_admin_menu(){	
			
			/**
			 * Plugin settings menu */
			 
			add_menu_page( esc_attr__( 'Progress Map', 'cspm' ), esc_attr__( 'Progress Map', 'cspm' ), 'manage_options', $this->plugin_settings_option_key, function(){}, $this->plugin_url.'admin/options-page/img/menu-icon.png', '99.2' );
						
			/**
			 * All maps submenu */
			 	
			add_submenu_page( $this->plugin_settings_option_key, esc_attr__( 'All maps', 'cspm' ), esc_attr__( 'All maps', 'cspm' ), 'manage_options', 'edit.php?post_type='.$this->object_type, NULL );
			
			/**
			 * Add new map submenu */
			 
			add_submenu_page( $this->plugin_settings_option_key, esc_attr__( 'Add new map', 'cspm' ), esc_attr__( 'Add new map', 'cspm' ), 'manage_options', 'post-new.php?post_type='.$this->object_type, NULL );

		}
		
		/**
		 * Load plugin text domain
		 *
		 * @since 2.8
		 */
		function cspm_load_plugin_textdomain(){
			
			/**
			 * To translate the plugin, create a new folder in "wp-content/languages" ...
			 * ... and name it "cs-progress-map". Inside "cs-progress-map", paste your .mo & .po files.
			 * The plugin will detect the language of your website and display the appropriate language. */
			 
			$domain = 'cspm';
			
			$locale = apply_filters('plugin_locale', get_locale(), $domain);
		
			load_textdomain($domain, WP_LANG_DIR.'/cs-progress-map/'.$domain.'-'.$locale.'.mo');
	
			load_plugin_textdomain($domain, FALSE, $this->plugin_path.'languages/');
			
		}
		
				
		/**
		 * Add settings link to plugin instalation area 
		 */
		function cspm_add_plugin_action_links($links){
		 
			return array_merge(
				array(
					'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page='.$this->plugin_settings_option_key.'">Settings</a>'
				),
				$links
			);
		 
		}	
		
	
		/**
		 * Add plugin site link to plugin instalation area 
		 */
		function cspm_plugin_meta_links($links, $file){
		 
			$plugin = plugin_basename(__FILE__);
		 
			/**
			 * create the link */
			 
			if ( $file == $plugin ) {
				return array_merge(
					(array) $links,
					array(
						'extensions' => '<a target="_blank" href="https://codespacing.com/progress-map-add-ons/">'.esc_html__('Add-ons', 'cspm').'</a>',
						'documentation' => '<a target="_blank" href="http://www.docs.progress-map.com/progress-map-user-guide/">'.esc_html__('Documentation', 'cspm').'</a>'
					)
				);
			}
			
			return $links;
		 
		}


		/**
		 * Alter the list of acceptable file extensions WordPress checks during media uploads 
		 *
		 * @since 2.7
		 * @updated 4.0 | 5.2
		 */
		function cspm_custom_upload_mimes($existing_mimes = array()){
			
			//$existing_mimes['kml'] = 'application/vnd.google-earth.kml+xml'; //@Deprecated since 5.2		
			//$existing_mimes['kmz'] = 'application/vnd.google-earth.kmz'; //@Deprecated since 5.2	
			$existing_mimes['kml'] = 'text/xml'; //@since 5.2	
			$existing_mimes['kmz'] = 'application/zip'; //@since 5.2							
			$existing_mimes['svg'] = 'image/svg+xml'; //@since 4.0
			
			return apply_filters('cspm_allowed_mimes', $existing_mimes);
			
		}
		
		
		/**
		 * Display messages in the admin to inform changes or infos about the plugin and/or its extensions
		 *
		 * @since 3.0
		 * @updated 4.0
		 */
		function cspm_admin_notices(){
			
			/**
			 * Inform about the new pricing changes of Google Maps
			 * @since 3.8.1 */

			global $current_user;	
    	 
		    $user_id = $current_user->ID;
	
			if(version_compare($this->plugin_version, '4.5', '>=') && isset($_GET['post_type']) && $_GET['post_type'] == $this->object_type && current_user_can('activate_plugins')){
					
				echo '<div class="notice notice-info" style="background: #fff; color: #333;">
					<p><strong>Reminder:</strong> New Google Maps changes went into effect on July 16, 2018. Check out <a href="https://codespacing.com/important-updates-in-google-maps/" target="_blank">this post</a> if you\'re not already aware.</p>
				</div>';
					
			}else{
				
				if(get_user_meta($user_id, 'cspm_dismiss_gmaps_updates_notice') && isset($_GET['post_type']) && $_GET['post_type'] == $this->object_type && current_user_can('activate_plugins')){
					
					echo '<div class="notice notice-error" style="background: #fff3e0; color: #dd2c00;">
						<p><strong>Reminder:</strong> To use Google Maps APIs:</p>
						<ul style="padding-left:15px;">
							<li><strong>. Starting June 11, 2018:</strong> All Google Maps Platform (GMP) API requests must include an API key; GMP no longer support keyless access.</li>
							<li><strong>. Starting July 16, 2018:</strong> You\'ll need to enable billing on each of your projects.</li>			
						</ul>
						<p><strong>Show <a href="'.add_query_arg('cspm_dismiss_gmaps_updates_notice', 0, $_SERVER['REQUEST_URI']).'" style="color: #dd2c00; text-decoration:underline;">the announcement</a> for more information.</strong></p>
					</div>';
					
				}elseif(!get_user_meta($user_id, 'cspm_dismiss_gmaps_updates_notice') && current_user_can('activate_plugins')){
					
					echo '<div class="notice notice-warning"> 
					
						<h4 style="text-decoration:underline; font-size: 15px;"><strong style="color:red">Important Updates in Google Maps!</strong></h4>
						
						<p>If you\'re not already aware, Google Maps (GMaps) announced some important updates to continue using their APIs. 
						Here is an excerpt of the announcement from <a href="https://cloud.google.com/maps-platform/" target="_blank">GMaps Platform</a>:</p>
						</p>
						
						<blockquote><strong style="font-style: italic;">Starting on June 11, 2018, you\'ll need a valid API key and a billing account to access our APIs. When you enable billing, you will get $200 free usage every month for Maps, Routes, or Places. Based on the millions of users using our APIs today, most of them can continue to use Google Maps Platform for free with this credit. Having a billing account helps us understand our developers\' needs better and allows you to scale seamlessly. 
						To avoid a service interruption to your projects, please visit our <a href="https://cloud.google.com/maps-platform/#get-started" target="_blank">Get Started</a> page to create a project, generate an API key, and enable a billing account. Once you generate and secure an API key, make sure to update your application with the new API key.
						<br /><br />Most websites and applications may use the standard Maps JavaScript API free of charge. A project is restricted to the complimentary per-day-limit of 25,000 map loads, unless you enable billing on the project. If billing is enabled on the project, the standard Maps JavaScript API supports up to 100,000 map loads daily. If your website exceeds 25,000 map loads in a day, the API will cease to function for the rest of the day, unless you enable billing to access higher daily quota.
						<br /><br />We\'ll continue to partner with Google programs that bring our products to <a href="https://www.google.com/nonprofits/eligibility/" target="_blank">nonprofits</a>, <a href="https://startup.google.com/" target="_blank">startups</a>, <a href="https://cloud.google.com/maps-platform/credits/application/crisis-response" target="_blank">crisis response</a>, and <a href="https://cloud.google.com/maps-platform/credits/application/media" target="_blank">news media organizations</a>. We\'ve put new processes in place to help us scale these programs to hundreds of thousands of organizations and more countries around the world. 
						</strong></blockquote>
						
						<p>Please take a few minutes to review the <a href="https://mapsplatform.googleblog.com/2018/05/introducing-google-maps-platform.html" target="_blank">announcement</a> to familiarize yourself with the upcoming changes.</p>
						
						<h4 style="text-decoration:underline;"><strong>Updates summary</strong></h4>
						
						<p style="padding-left: 15px;">
							1. Starting on June 11, 2018, you\'ll need to generate a GMaps API key.<br />
							2. The billing requirement will be enforced and you\'ll need to associate your projects/APIs with a billing account.<br />
							3. In return, GMaps will grant you a $200 USD credit each month. This credit will allow you to continue using GMaps for free. View the <a href="https://cloud.google.com/maps-platform/pricing/sheet/" target="_blank">pricing table</a> for more details on what you can get under the $200 free credit.<br />
							4. If your website generates requests or map load volumes below the complimentary $200 per month usage, your usage is free. Usage that exceeds the $200 monthly credit will be charged to your billing account but only if you enable billing. See the <a href="https://cloud.google.com/maps-platform/pricing/sheet/" target="_blank">Pricing Sheet</a> for an overview of cost per API.<br />
							5. New pricing changes will go into effect starting July 16, 2018.
						</p>
						
						<h4 style="text-decoration:underline;"><strong>My personal opinion about GMaps updates</strong></h4>
						
						<p style="padding-left: 15px;">
							I also believe that most users, including myself, will continue using the GMaps platform for free with the $200/mo credit. 
							I can say that if you\'re not a Premium Plan customer of GMaps, and that your site generates reasonable traffic, then, you\'ll continue using GMaps APIs like you were always doing and your cost will be covered by the $200 monthly free credit. 
							However, because providing a billing account can make most of us uncomfortable knowing that we\'ll be charged if our usage exceeds the $200 monthly credit, i highly recommend you to <a href="https://developers.google.com/maps/faq#usage_cap" target="_blank">change the quotas limits</a> in order to protect yourself against unexpected increases in use.
							I also recommend you to <a href="https://cloud.google.com/billing/docs/how-to/budgets" target="_blank">set up billing alerts</a> in order to receive emails when the charges on the billing account reach a threshold you set.
						</p>
						
						<p style="padding-left: 15px; font-style: italic;">
							<strong>Note:</strong> Please note that the changes are required by GMaps and are outside of our control. Note also that nothing is required from the plugin\'s <strong>(Progress Map WordPress Plugin)</strong> side and that the plugin will continue to work fine. You\'ll have to provide your billing info to Google only, not to a GMaps developer (including myself). All changes must be done in the <a href="https://console.cloud.google.com" target="_blank">Google Maps section</a> in your Google Cloud Platform Console!
						</p>
						
						<h4 style="text-decoration:underline;"><strong>Resources</strong></h4>
						
						<p style="padding-left: 15px; ">
							<a href="https://mapsplatform.googleblog.com/2018/05/introducing-google-maps-platform.html" target="_blank">The Announcement</a><br />
							<a href="https://developers.google.com/maps/billing/important-updates" target="_blank">Important Updates</a><br />				
							<a href="https://developers.google.com/maps/billing/understanding-cost-of-use" target="_blank">Understanding billing for Maps, Routes, and Places</a><br />	
							<a href="https://cloud.google.com/maps-platform/pricing/sheet/" target="_blank">Pricing Sheet (effective July 16, 2018)</a><br />	
							<a href="https://developers.google.com/maps/faq#usage-limits" target="_blank">Usage limits and billing</a><br />
							<a href="https://cloud.google.com/maps-platform/pricing/" target="_blank">Google Maps Platform Pricing</a><br />
							<a href="https://cloud.google.com/maps-platform/user-guide/account-changes/" target="_blank">Account Changes</a><br />
							<a href="https://cloud.google.com/maps-platform/user-guide/pricing-changes/" target="_blank">Pricing and Billing Changes</a><br />
							<a href="https://cloud.google.com/maps-platform/user-guide/" target="_blank">Guide for Existing Users</a><br />	
							<a href="https://mapsplatformtransition.withgoogle.com/calculator" target="_blank">Pricing Calculator</a><br />
							<a href="https://developers.google.com/maps/pricing-and-plans/standard-plan-2016-update" target="_blank">Standard Plan Updates</a><br />
							<a href="https://developers.google.com/maps/documentation/javascript/usage" target="_blank">Maps JavaScript API Usage Limits</a><br />
							<a href="https://developers.google.com/maps/faq" target="_blank">FAQ</a>
						</p>
						
						<p style="text-align:right;">
							<a href="'.add_query_arg('cspm_dismiss_gmaps_updates_notice', 1, $_SERVER['REQUEST_URI']).'" class="button button-primary button-large"><strong>I confirm that i\'ve read and understood this announcement. Remove this notice</strong></a>
						</p>
						
						<br />
					
					</div>';
				
				}
				
			}
		
			/**
 * Make sure to use "List & Filter" 3.6.1 or newer
 */
if (class_exists('ProgressMapList')) {
    $ProgressMapList_required_version = '3.6.1';

    // Instantiate ProgressMapList object
    $ProgressMapList = ProgressMapList::this();

    // Use version_compare to check plugin version
    if (version_compare($ProgressMapList->plugin_version, $ProgressMapList_required_version, '<')) {
        echo '<div class="notice notice-warning"><p>';
        echo '<strong><em><u>"Progress Map v'.$this->plugin_version.'"</u></em> requires version '.$ProgressMapList_required_version.' or newer of the "Progress Map, List & Filter" extension. Please update this extension!</strong>';
        echo '</p></div>';
    }
}

	
			/**
			 * Make sure to use "Nearby Places" 2.6.7 or upper */
			 	
			if(class_exists('CspmNearbyMap')){
				
				$CspmNearbyMap_required_version = '2.6.7';
                
                $CspmNearbyMap = CspmNearbyMap::this();
				
				$reflect = new ReflectionClass($CspmNearbyMap);
				
				$plugin_version = $reflect->getProperty('plugin_version');
				
				if(($plugin_version->isPublic() && version_compare($CspmNearbyMap->plugin_version, $CspmNearbyMap_required_version, '<')) || !$plugin_version->isPublic()){
				
					echo '<div class="notice notice-warning"><p>';
						echo '<strong><em><u>"Progress Map v'.$this->plugin_version.'"</u></em> requires the version '.$CspmNearbyMap_required_version.' or upper of the extension <em><u>"Nearby Places"</u></em>. Please update this extension!</strong>';
					echo '</p></div>';
			
				}
			
			}
	
			/**
			 * Make sure to use "Submit Locations" 1.9.5 or upper */
			 	
			if(class_exists('CspmSubmitLocations')){
				
				$CspmSubmitLocations_required_version = '1.9.5';
                
                $CspmSubmitLocations = CspmSubmitLocations::this();
				
				$reflect = new ReflectionClass($CspmSubmitLocations);
				
				$plugin_version = $reflect->getProperty('plugin_version');

				if(($plugin_version->isPublic() && version_compare($CspmSubmitLocations->plugin_version, $CspmSubmitLocations_required_version, '<')) || !$plugin_version->isPublic()){
				
					echo '<div class="notice notice-warning"><p>';
						echo '<strong><em><u>"Progress Map v'.$this->plugin_version.'"</u></em> requires the version '.$CspmSubmitLocations_required_version.' or upper of the extension <em><u>"Progress Map, Submit Locations"</u></em>. Please update this extension!</strong>';
					echo '</p></div>';
			
				}
			
			}
	
			/**
			 * Make sure to use "Draw a Search" 1.9.1 or upper */
			 	
			if(class_exists('CspmDrawSearch')){
				
				$CspmDrawSearch_required_version = '1.9.1';
                
                $CspmDrawSearch = CspmDrawSearch::this();
				
				$reflect = new ReflectionClass($CspmDrawSearch);
				
				$plugin_version = $reflect->getProperty('plugin_version');
				
				if(($plugin_version->isPublic() && version_compare($CspmDrawSearch->plugin_version, $CspmDrawSearch_required_version, '<')) || !$plugin_version->isPublic()){
				
					echo '<div class="notice notice-warning"><p>';
						echo '<strong><em><u>"Progress Map v'.$this->plugin_version.'"</u></em> requires the version '.$CspmDrawSearch_required_version.' or upper of the extension <em><u>"Progress Map, Draw a Search"</u></em>. Please update the extension!</strong>';
					echo '</p></div>';
			
				}
			
			}
            
			/**
			 * Make sure to use "Advanced Search" 1.4.3 or upper */
			 	
			if(class_exists('ProgressMapAdvancedSearch')){
				
                $advancedSearch_required_version = '1.4.3';
                
				$ProgressMapAdvancedSearch = ProgressMapAdvancedSearch::this();
				
				$reflect = new ReflectionClass($ProgressMapAdvancedSearch);
				
				$plugin_version = $reflect->getProperty('plugin_version');
				
				if(($plugin_version->isPublic() && version_compare($ProgressMapAdvancedSearch->plugin_version, $advancedSearch_required_version, '<')) || !$plugin_version->isPublic()){
				
					echo '<div class="notice notice-warning"><p>';
						echo '<strong><em><u>"Progress Map v'.$this->plugin_version.'"</u></em> requires the version '.$advancedSearch_required_version.' or upper of the extension <em><u>"Progress Map, Advanced Search"</u></em>. Please update the extension!</strong>';
					echo '</p></div>';
			
				}
			
			}
            
            /**
			 * Make sure to use "Keyword Search" 1.3 or upper */
			 	
			if(class_exists('CspmKeywordSearch')){
				
                $CspmKeywordSearch_required_version = '1.3';
                
				$CspmKeywordSearch = CspmKeywordSearch::this();
				
				$reflect = new ReflectionClass($CspmKeywordSearch);
				
				$plugin_version = $reflect->getProperty('plugin_version');
				
				if(($plugin_version->isPublic() && version_compare($CspmKeywordSearch->plugin_version, $CspmKeywordSearch_required_version, '<')) || !$plugin_version->isPublic()){
				
					echo '<div class="notice notice-warning"><p>';
						echo '<strong><em><u>"Progress Map v'.$this->plugin_version.'"</u></em> requires the version '.$CspmKeywordSearch_required_version.' or upper of the extension <em><u>"Progress Map, Keyword Search"</u></em>. Please update the extension!</strong>';
					echo '</p></div>';
			
				}
			
			}
	       
            /**
			 * Make sure to use "Directions" 1.0 or upper */
			 	
			if(class_exists('CspmDirections')){
				
                $CspmDirections_required_version = '1.0';
                
				$CspmDirections = CspmDirections::this();
				
				$reflect = new ReflectionClass($CspmDirections);
				
				$plugin_version = $reflect->getProperty('plugin_version');
				
				if(($plugin_version->isPublic() && version_compare($CspmDirections->plugin_version, $CspmDirections_required_version, '<')) || !$plugin_version->isPublic()){
				
					echo '<div class="notice notice-warning"><p>';
						echo '<strong><em><u>"Progress Map v'.$this->plugin_version.'"</u></em> requires the version '.$CspmDirections_required_version.' or upper of the extension <em><u>"Progress Map, Directions"</u></em>. Please update the extension!</strong>';
					echo '</p></div>';
			
				}
			
			}
            
		}
		
		/**
		 * Dismiss admin notice(s)
		 *
		 * @since 3.8.1
		 */
		function cspm_dismiss_admin_notices(){

			global $current_user;	
    	 
		    $user_id = $current_user->ID;
			
			if(isset($_GET['cspm_dismiss_gmaps_updates_notice'])){
				if($_GET['cspm_dismiss_gmaps_updates_notice'] == 1)			
					add_user_meta($user_id, 'cspm_dismiss_gmaps_updates_notice', 'true', true);
				else delete_user_meta($user_id, 'cspm_dismiss_gmaps_updates_notice');
			}
			
		}
		
				
		/**
		 * Register new carousel image sizes
		 *
		 * @since 3.0 
		 */				 			
		function cspm_add_carousel_image_sizes(){
			
			if(function_exists('add_image_size')){
					
				$args = array( 'post_type' => $this->object_type);
				
				$loop = new WP_Query( $args );
				
				while ( $loop->have_posts() ) : $loop->the_post();
					
					$object_id = get_the_id();
					
					$horizontal_image_size = explode(',', get_post_meta( $object_id, $this->metafield_prefix.'_horizontal_image_size', true ));
							
						$horizontal_image_width = isset($horizontal_image_size[0]) ? $horizontal_image_size[0] : 204;
						$horizontal_image_height = isset($horizontal_image_size[1]) ? $horizontal_image_size[1] : 150; 
		
						add_image_size('cspm-horizontal-thumbnail-map'.$object_id, $horizontal_image_width, $horizontal_image_height, true);
					
					$vertical_image_size = explode(',', get_post_meta( $object_id, $this->metafield_prefix.'_vertical_image_size', true ));
							
						$vertical_image_width = isset($vertical_image_size[0]) ? $vertical_image_size[0] : 204;
						$vertical_image_height = isset($vertical_image_size[1]) ? $vertical_image_size[1] : 120; 
					
						add_image_size('cspm-vertical-thumbnail-map'.$object_id, $vertical_image_width, $vertical_image_height, true);
	
				endwhile;
				
			}

		}
		
		
		/**
		 * Returns an element's ID in the current language or in another specified language.
		 *
		 * @since 2.5
		 * @updated 2.8.4
		 */
		function cspm_wpml_object_id($ID, $post_tag, $orginal_val = true, $lang_code = "", $use_with_wpml = "no"){
			
			if(!empty($lang_code))
				$lang_code = apply_filters('wpml_current_language', NULL);
			
			return ($use_with_wpml == 'yes') ? apply_filters('wpml_object_id', $ID, $post_tag, $orginal_val, $lang_code) : $ID;
			
		}
		
		
		/**
		 * Get the Default language of the website
		 *
		 * @since 2.5
		 * @updated 2.8.4
		 */	
		function cspm_wpml_default_lang($use_with_wpml = "no"){
			
			return ($use_with_wpml == 'yes') ? apply_filters('wpml_default_language', NULL ) : '';	
			
		}
		
		
		/**
		 * Register strings for WPML 
		 *
		 * @since 2.5
		 * @updated 2.8.4
		 */
		function cspm_wpml_register_string($name, $value, $use_with_wpml = "no"){
				
			if($use_with_wpml == 'yes' && !empty($name) && !empty($value))
				do_action('wpml_register_single_string', 'Progress map', $name, $value);
			
		}
		
		
		/**
		 * Get registered string from WPML DB when displaying
		 *
		 * @since 2.5
		 * @updated 2.8.4
		 */
		function cspm_wpml_get_string($name, $value, $use_with_wpml = "no"){
		
			if($use_with_wpml == 'yes' && !empty($name) && !empty($value))
				return apply_filters('wpml_translate_single_string', $value, 'Progress map', $name);
			else return $value;
			
		}


		/**
		 * AJAX function
		 * Get all posts and create a JSON array of markers base on the ...
		 * ... custom fields latitude & longitude + Secondary lat & lng
		 *
		 * @since 2.5
		 * @updated 3.5 | Added the media files to markers
		 * @updated 5.6.3 | Possibility to convert address to latLng
		 */
		function cspm_regenerate_markers($is_ajax = true, $post_types_array = array()){
						
			if( class_exists( 'CspmMainMap' ) )
				$CspmMainMap = CspmMainMap::this();
			
			$post_types = (count($post_types_array) > 0) ? $post_types_array : $this->post_types;
			
			$meta_values = array(
				CSPM_LATITUDE_FIELD, 
				CSPM_LONGITUDE_FIELD, 
				CSPM_SECONDARY_LAT_LNG_FIELD,
				CSPM_FORMAT_FIELD, //@since 3.5
				CSPM_AUDIO_FIELD, //@since 3.5
				CSPM_IMAGE_FIELD.'_id', //@since 3.5
				CSPM_EMBED_FIELD, //@since 3.5
				CSPM_GALLERY_FIELD, //@since 3.5
				CSPM_ADDRESS_FIELD, //@since 5.4
			);
			
			if(count($post_types) > 0){
				
				/**
				 * Init the array that will contain all markers of each post type */
				 
				$post_types_markers = array();
				
				/**
				 * Loop throught all post types selected in the plugin settings */
				 
				foreach($post_types as $post_type){
					
					$post_types_markers[$post_type] = array();
					
					/**
					 * Get all the values of the Latitude/Longitude/Secondary coordinates ...
					 * ... where each row in the array contains the value of the custom field and ...
					 * ... the post ID related to */
					 
					foreach($meta_values as $meta_value)
						$post_types_markers[$post_type][$meta_value] = $CspmMainMap->cspm_get_meta_values($meta_value, $post_type);
									
					$post_types_markers[$post_type] = array_merge_recursive(
						$post_types_markers[$post_type][CSPM_LATITUDE_FIELD], 
						$post_types_markers[$post_type][CSPM_LONGITUDE_FIELD],
						$post_types_markers[$post_type][CSPM_SECONDARY_LAT_LNG_FIELD],
						$post_types_markers[$post_type][CSPM_FORMAT_FIELD], //@since 3.5
						$post_types_markers[$post_type][CSPM_AUDIO_FIELD], //@since 3.5
						$post_types_markers[$post_type][CSPM_IMAGE_FIELD.'_id'], //@since 3.5
						$post_types_markers[$post_type][CSPM_EMBED_FIELD], //@since 3.5
						$post_types_markers[$post_type][CSPM_GALLERY_FIELD], //@since 3.5
						$post_types_markers[$post_type][CSPM_ADDRESS_FIELD] //@since 5.4
					);								
																	   
				}
			
				global $wpdb;
				
				$markers_object = $post_taxonomy_terms = array();
				
				/**
				 * Create the map markers object for each post type */
				 
				foreach($post_types_markers as $post_type => $posts_and_coordinates){
					
					$i = $j = 0;						
					
					/**
					 * Get post type taxonomies */
					 
					$post_taxonomies = (array) get_object_taxonomies($post_type, 'names');
						if(($key = array_search('post_format', $post_taxonomies)) !== false) {
							unset($post_taxonomies[$key]);
						}
						
					/**
					 * Implode taxonomies to use them in the Mysql IN clause */
					 
					$taxonomies = "'" . implode("', '", $post_taxonomies) . "'";
					
					/**
					 * Directly querying the database is normally frowned upon, but all ...
					 * ... of the API functions will return the full post objects which will
					 * ... suck up lots of memory. This is best, just not as future proof */
					 
					$query = "SELECT t.term_id, tt.taxonomy, tr.object_id FROM $wpdb->terms AS t 
								INNER JOIN $wpdb->term_taxonomy AS tt 
									ON tt.term_id = t.term_id 
								INNER JOIN $wpdb->term_relationships AS tr 
									ON tr.term_taxonomy_id = tt.term_taxonomy_id 
								WHERE tt.taxonomy IN ($taxonomies)";
					
					/**
					 * Run the query. This will get an array of all terms where each term ...
					 * ... is listed with the taxonomy name and the post id */
					 
					$taxonomy_terms_and_posts = $wpdb->get_results( $query, ARRAY_A );
			
					/**
					 * Loop through the terms and order them in a way, the array will have the post_id as key ...
					 * ... inside that array, there will be another array with the key == taxonomy name ...
					 * ... inside that last array, there will be all the terms of a post */
					 
					foreach($taxonomy_terms_and_posts as $term)							
						$post_taxonomy_terms[$term['object_id']][$term['taxonomy']][] = $term['term_id'];

					/**
					 * Biuld the marker object of each post 
					 * @updated in 3.5 by adding the media files to the marker object */
					 
					foreach($posts_and_coordinates as $post_id => $post_coordinates){						
						
						$post_id = str_replace('post_id_', '', $post_id);
						
						/**
						 * Convert address to coordinates when LatLng is not provided and save them
						 * @since 5.6.3 */

						if((isset($post_coordinates[CSPM_ADDRESS_FIELD]) && !empty($post_coordinates[CSPM_ADDRESS_FIELD])) && (
							(!isset($post_coordinates[CSPM_LATITUDE_FIELD]) || empty($post_coordinates[CSPM_LATITUDE_FIELD])) || 
							(!isset($post_coordinates[CSPM_LONGITUDE_FIELD]) || empty($post_coordinates[CSPM_LONGITUDE_FIELD]))
						)){

							$address_coordinates = $this->cspm_convert_address_to_latlng(array(
								'address' => $post_coordinates[CSPM_ADDRESS_FIELD]
							));

							if($this->cspm_setting_exists('lat', $address_coordinates) && $this->cspm_setting_exists('lng', $address_coordinates)){
								
								$post_coordinates[CSPM_LATITUDE_FIELD] = $address_coordinates['lat'];								
								$post_coordinates[CSPM_LONGITUDE_FIELD] = $address_coordinates['lng'];
								
								update_post_meta($post_id, CSPM_LATITUDE_FIELD, $address_coordinates['lat']);
								update_post_meta($post_id, CSPM_LONGITUDE_FIELD, $address_coordinates['lng']);
								
							}				

						}
						
						/**
						 * Build the marker object */
						
						if(isset($post_coordinates[CSPM_LATITUDE_FIELD]) && isset($post_coordinates[CSPM_LONGITUDE_FIELD])){
							
							/**
							 * If a taxonomy is not set in the $post_taxonomy_terms array ...
							 * ... it means that the post has no terms available for that taxonomy ...
							 * ... but we still need to create an empty array for that taxonomy in order ...
							 * ... to use it with faceted search */
							 
							foreach($post_taxonomies as $taxonomy_name){
								
								/**
								 * Extend the $post_taxonomy_terms array with an empty array of the not existing taxonomy */
								 
								if(!isset($post_taxonomy_terms[$post_id][$taxonomy_name]))
									$post_taxonomy_terms[$post_id][$taxonomy_name] = array(); 
							
							}
							
							$markers_object[$post_type]['post_id_'.$post_id] = array_merge(
								array(
									'lat' => $post_coordinates[CSPM_LATITUDE_FIELD],
									'lng' => $post_coordinates[CSPM_LONGITUDE_FIELD],
									'address' => $post_coordinates[CSPM_ADDRESS_FIELD], //@since 5.4
									'post_id' => $post_id,
									'post_tax_terms' => $post_taxonomy_terms[$post_id],
									'is_child' => 'no',
									'child_markers' => array(),
								),
								
								/**
								 * Add post media to marker object 
								 * @since 3.5 */
								 
								$this->cspm_get_post_media($post_id, array(
									'format' => isset($post_coordinates[CSPM_FORMAT_FIELD]) ? $post_coordinates[CSPM_FORMAT_FIELD] : '',
									'marker_click' => isset($post_coordinates[CSPM_MEDIA_MARKER_CLICK_FIELD]) ? $post_coordinates[CSPM_MEDIA_MARKER_CLICK_FIELD] : '', //@since 5.5
									'gallery' => isset($post_coordinates[CSPM_GALLERY_FIELD]) ? maybe_unserialize($post_coordinates[CSPM_GALLERY_FIELD]) : array(),
									'image_id' => isset($post_coordinates[CSPM_IMAGE_FIELD.'_id']) ? $post_coordinates[CSPM_IMAGE_FIELD.'_id'] : '',
									'audio_url' => isset($post_coordinates[CSPM_AUDIO_FIELD]) ? $post_coordinates[CSPM_AUDIO_FIELD] : '',
									'embed_url' => isset($post_coordinates[CSPM_EMBED_FIELD]) ? $post_coordinates[CSPM_EMBED_FIELD] : '',
								))
							);																 
				
							$i++;
							
							/**
							 * Sencondary latLng */
							 
							if(isset($post_coordinates[CSPM_SECONDARY_LAT_LNG_FIELD]) && !empty($post_coordinates[CSPM_SECONDARY_LAT_LNG_FIELD])){
								
								$child_markers = array();
								
								$lats_lngs = explode(']', $post_coordinates[CSPM_SECONDARY_LAT_LNG_FIELD]);	
										
								foreach($lats_lngs as $single_coordinate){
								
									$strip_coordinates = str_replace(array('[', ']', ' '), '', $single_coordinate);
									
									$coordinates = explode(',', $strip_coordinates);
									
									if(isset($coordinates[0]) && isset($coordinates[1]) && !empty($coordinates[0]) && !empty($coordinates[1])){
										
										$lat = $coordinates[0];
										$lng = $coordinates[1];
										
										$child_markers[] = array(
											'lat' => $lat,
											'lng' => $lng,
											'address' => '', //@since 5.4
											'post_id' => $post_id,
											'post_tax_terms' => $post_taxonomy_terms,
											'is_child' => 'yes_'.$j
										);
																														
										$lat = '';
										$lng = '';
										$j++;
									
									} 
									
									$i++;
									
								}
								
								$markers_object[$post_type]['post_id_'.$post_id]['child_markers'] = $child_markers;
							
							}								
																																					
						}
						
					}
					
				}
											
				/**
				 * Update settings */
				 
				if(count($markers_object) > 0){
					update_option('cspm_markers_array', $markers_object);
				}
				
			}
			
			if($is_ajax) die();
			
		}
		
		
		/**
		 * Run some settings updates to sync. with the latest version
		 *
		 * @since 2.4 
		 * @updated 3.0
		 * @updated 3.1 [check if it's plugin update or new installation]
		 * @updated 3.2 [fixed issue with "Post in" & "Post not in" + Marker categories images not been imported]
		 * @updated 4.0 [convert old settings to new settings structure]
		 * @updated 5.2 [convert image overlays + polylines + polygons settings to new settings structure]
		 */
		function cspm_sync_settings_for_latest_version(){
				
			$opt_group = preg_replace("/[^a-z0-9]+/i", "", basename($this->plugin_path .'settings/cspm.php', '.php'));

			if(version_compare($this->plugin_version, '3.0', '>=')){
				
				/**
				 * It's based on this options that we'll define if the below code has been executed before or not.
				 * The option "cspm_settings_v2" is a backup option for the settings of "Progress Map v2.+"
				 * 
				 * @updated 3.1 --------
				 * 
				 * This will first check if it's a plugin update or a new installation by looking for the option "cspm_settings" ...
				 * ... then it'll look for the backup option "cspm_settings_v2" to define if we'll excute this code or not.
				 */
				 
				if(get_option($opt_group.'_settings') && !get_option($opt_group.'_settings_v2')){
					
					/**
					 * Get "PM. v2.+" settings */
					 
					$cspm_v2_options = get_option($opt_group.'_settings');
					
					/**
					 * Save/Backup "PM. v2.+" settings just in case */
						
					update_option($opt_group.'_settings_v2', $cspm_v2_options);
				
					/**
					 * Post types */
					
						/**
						 * Get post types selected in the v2.+ fields "Query settings => Main content type" ...
						 * ... & "Query settings => Secondary content types" */
						
						$main_post_type = $this->cspm_setting_exists('cspm_generalsettings_post_type', $cspm_v2_options, 'post');
						$secondary_post_types = $this->cspm_setting_exists('cspm_generalsettings_secondary_post_type', $cspm_v2_options, array());
						
						/**
						 * Save all post types in the new v3.0 field "Plugin settings => Post types" */
						
						$cspm_v2_options['cspm_pluginsettings_post_types'] = array_merge((array) $main_post_type, $secondary_post_types);
						
						/**
						 * Update v2.+ options.
						 * Now, in this step and with this update, we can concider these settings as compatible with "PM v3.0" */
						 
						update_option($opt_group.'_settings', $cspm_v2_options);
						
					/**
					 * == Create a new map with all v2.+ settings as new post of our CPT (Defined in [@object_type]) in v3.0 == */
																				
					/**
					 * Query settings */
					
					$query_settings = array(
						$this->metafield_prefix.'_number_of_items' => $this->cspm_setting_exists('cspm_generalsettings_number_of_items', $cspm_v2_options, ''), 
						$this->metafield_prefix.'_taxonomy_relation_param' => $this->cspm_setting_exists('cspm_generalsettings_taxonomy_relation_param', $cspm_v2_options, ''),
						$this->metafield_prefix.'_custom_fields' => ($this->cspm_setting_exists('cspm_generalsettings_custom_fields', $cspm_v2_options, '')),
						$this->metafield_prefix.'_custom_field_relation_param' => $this->cspm_setting_exists('cspm_generalsettings_custom_field_relation_param', $cspm_v2_options, ''),
						$this->metafield_prefix.'_cache_results' => $this->cspm_setting_exists('cspm_generalsettings_cache_results', $cspm_v2_options, ''),
						$this->metafield_prefix.'_update_post_meta_cache' => $this->cspm_setting_exists('cspm_generalsettings_update_post_meta_cache', $cspm_v2_options, ''),
						$this->metafield_prefix.'_update_post_term_cache' => $this->cspm_setting_exists('cspm_generalsettings_update_post_term_cache', $cspm_v2_options, ''),
						$this->metafield_prefix.'_authors_prefixing' => $this->cspm_setting_exists('cspm_generalsettings_authors_prefixing', $cspm_v2_options, ''),
						$this->metafield_prefix.'_authors' => $this->cspm_setting_exists('cspm_generalsettings_authors', $cspm_v2_options, ''),
						$this->metafield_prefix.'_orderby_param' => $this->cspm_setting_exists('cspm_generalsettings_orderby_param', $cspm_v2_options, ''),
						$this->metafield_prefix.'_orderby_meta_key' => $this->cspm_setting_exists('cspm_generalsettings_orderby_meta_key', $cspm_v2_options, ''),					
						$this->metafield_prefix.'_order_param' => $this->cspm_setting_exists('cspm_generalsettings_order_param', $cspm_v2_options, ''),
					);
						
						/**
						 * "Post In" and "Post not in" 
						 * @since 3.2 */
						 
						$post_in = $this->cspm_setting_exists('cspm_generalsettings_post_in', $cspm_v2_options, '');
						
						if(!empty($post_in))
							$query_settings[$this->metafield_prefix.'_post_in'] = explode(',', $post_in);
						 
						$post_not_in = $this->cspm_setting_exists('cspm_generalsettings_post_not_in', $cspm_v2_options, '');
						
						if(!empty($post_not_in))
							$query_settings[$this->metafield_prefix.'_post_not_in'] = explode(',', $post_not_in);
					
						/**
						 * Build Taxonomies */
						 
						$main_post_type_taxonomies = (array) get_object_taxonomies($main_post_type, 'objects');
							unset($main_post_type_taxonomies['post_format']);
							
						reset($main_post_type_taxonomies); // Set the cursor to 0
						
						if(is_array($main_post_type_taxonomies)){
							
							foreach($main_post_type_taxonomies as $single_taxonomy){
								
								if(isset($single_taxonomy->name)){
								
									$tax_name = $single_taxonomy->name;
									
									$query_settings[$this->metafield_prefix . '_taxonomie_'.$tax_name] = ($this->cspm_setting_exists('cspm_generalsettings_taxonomie_'.$tax_name, $cspm_v2_options, ''));
									$query_settings[$this->metafield_prefix.'_'.$tax_name.'_operator_param'] = $this->cspm_setting_exists('cspm_generalsettings_'.$tax_name.'_operator_param', $cspm_v2_options);
							
								}
								
							}
					
						}
						
						/**
						 * Build statuses */
						 
						$statuses = get_post_stati();
						
						$selected_statuses = array();
						
						if(is_array($statuses)){
							
							foreach($statuses as $status){	
								
								$status_name = $this->cspm_setting_exists('cspm_generalsettings_items_status_'.$status, $cspm_v2_options);
								
								if(!empty($status_name))
									$selected_statuses[] = $status_name;
							
							}
							
						}
						
						$query_settings[$this->metafield_prefix.'_items_status'] = $selected_statuses;
						
					/**
					 * Layout settings */
					
					$layout_settings = array( 
						$this->metafield_prefix.'_main_layout' => $this->cspm_setting_exists('cspm_layoutsettings_main_layout', $cspm_v2_options, ''),
						$this->metafield_prefix.'_layout_type' => $this->cspm_setting_exists('cspm_layoutsettings_layout_type', $cspm_v2_options, ''),
						$this->metafield_prefix.'_layout_fixed_width' => $this->cspm_setting_exists('cspm_layoutsettings_layout_fixed_width', $cspm_v2_options, ''),
						$this->metafield_prefix.'_layout_fixed_height' => $this->cspm_setting_exists('cspm_layoutsettings_layout_fixed_height', $cspm_v2_options, ''),
					);
										
					/**
					 * Map settings */
					 
					$map_settings = array( 
						$this->metafield_prefix.'_map_center' => $this->cspm_setting_exists('cspm_mapsettings_map_center', $cspm_v2_options, ''),
						$this->metafield_prefix.'_initial_map_style' => $this->cspm_setting_exists('cspm_mapsettings_initial_map_style', $cspm_v2_options, ''),
						$this->metafield_prefix.'_map_zoom' => $this->cspm_setting_exists('cspm_mapsettings_map_zoom', $cspm_v2_options, ''),
						$this->metafield_prefix.'_max_zoom' => $this->cspm_setting_exists('cspm_mapsettings_max_zoom', $cspm_v2_options, ''),
						$this->metafield_prefix.'_min_zoom' => $this->cspm_setting_exists('cspm_mapsettings_min_zoom', $cspm_v2_options, ''),
						$this->metafield_prefix.'_zoom_on_doubleclick' => $this->cspm_setting_exists('cspm_mapsettings_zoom_on_doubleclick', $cspm_v2_options, ''),
						$this->metafield_prefix.'_map_draggable' => $this->cspm_setting_exists('cspm_mapsettings_map_draggable', $cspm_v2_options, ''),
						$this->metafield_prefix.'_useClustring' => $this->cspm_setting_exists('cspm_mapsettings_useClustring', $cspm_v2_options, ''),
						$this->metafield_prefix.'_gridSize' => $this->cspm_setting_exists('cspm_mapsettings_gridSize', $cspm_v2_options, ''),
						$this->metafield_prefix.'_autofit' => $this->cspm_setting_exists('cspm_mapsettings_autofit', $cspm_v2_options, ''),
						$this->metafield_prefix.'_traffic_layer' => $this->cspm_setting_exists('cspm_mapsettings_traffic_layer', $cspm_v2_options, ''),
						$this->metafield_prefix.'_transit_layer' => $this->cspm_setting_exists('cspm_mapsettings_transit_layer', $cspm_v2_options, ''),
						$this->metafield_prefix.'_geoIpControl' => $this->cspm_setting_exists('cspm_mapsettings_geoIpControl', $cspm_v2_options, ''),
						$this->metafield_prefix.'_show_user' => $this->cspm_setting_exists('cspm_mapsettings_show_user', $cspm_v2_options, ''),
						$this->metafield_prefix.'_user_marker_icon' => $this->cspm_setting_exists('cspm_mapsettings_user_marker_icon', $cspm_v2_options, ''),
						$this->metafield_prefix.'_user_map_zoom' => $this->cspm_setting_exists('cspm_mapsettings_user_map_zoom', $cspm_v2_options, ''),
						$this->metafield_prefix.'_mapTypeControl' => $this->cspm_setting_exists('cspm_mapsettings_mapTypeControl', $cspm_v2_options, ''),
						$this->metafield_prefix.'_streetViewControl' => $this->cspm_setting_exists('cspm_mapsettings_streetViewControl', $cspm_v2_options, ''),
						$this->metafield_prefix.'_scrollwheel' => $this->cspm_setting_exists('cspm_mapsettings_scrollwheel', $cspm_v2_options, ''),
						$this->metafield_prefix.'_zoomControl' => $this->cspm_setting_exists('cspm_mapsettings_zoomControl', $cspm_v2_options, ''),
						$this->metafield_prefix.'_zoomControlType' => $this->cspm_setting_exists('cspm_mapsettings_zoomControlType', $cspm_v2_options, ''),
						$this->metafield_prefix.'_retinaSupport' => $this->cspm_setting_exists('cspm_mapsettings_retinaSupport', $cspm_v2_options, ''),
						$this->metafield_prefix.'_defaultMarker' => $this->cspm_setting_exists('cspm_mapsettings_defaultMarker', $cspm_v2_options, ''),
						$this->metafield_prefix.'_markerAnimation' => $this->cspm_setting_exists('cspm_mapsettings_markerAnimation', $cspm_v2_options, ''),
						$this->metafield_prefix.'_marker_icon' => $this->cspm_setting_exists('cspm_mapsettings_marker_icon', $cspm_v2_options, ''),
						$this->metafield_prefix.'_marker_anchor_point_option' => $this->cspm_setting_exists('cspm_mapsettings_marker_anchor_point_option', $cspm_v2_options, ''),
						$this->metafield_prefix.'_marker_anchor_point' => $this->cspm_setting_exists('cspm_mapsettings_marker_anchor_point', $cspm_v2_options, ''),
						$this->metafield_prefix.'_big_cluster_icon' => $this->cspm_setting_exists('cspm_mapsettings_big_cluster_icon', $cspm_v2_options, ''),
						$this->metafield_prefix.'_medium_cluster_icon' => $this->cspm_setting_exists('cspm_mapsettings_medium_cluster_icon', $cspm_v2_options, ''),
						$this->metafield_prefix.'_small_cluster_icon' => $this->cspm_setting_exists('cspm_mapsettings_small_cluster_icon', $cspm_v2_options, ''),
						$this->metafield_prefix.'_cluster_text_color' => $this->cspm_setting_exists('cspm_mapsettings_cluster_text_color', $cspm_v2_options, ''),
						$this->metafield_prefix.'_zoom_in_icon' => $this->cspm_setting_exists('cspm_mapsettings_zoom_in_icon', $cspm_v2_options, ''),
						$this->metafield_prefix.'_zoom_in_css' => $this->cspm_setting_exists('cspm_mapsettings_zoom_in_css', $cspm_v2_options, ''),
						$this->metafield_prefix.'_zoom_out_icon' => $this->cspm_setting_exists('cspm_mapsettings_zoom_out_icon', $cspm_v2_options, ''),
						$this->metafield_prefix.'_zoom_out_css' => $this->cspm_setting_exists('cspm_mapsettings_zoom_out_css', $cspm_v2_options, ''),
					);
								
					/**
					 * Map style settings */
					
					$map_style_settings = array(  
						$this->metafield_prefix.'_style_option' => $this->cspm_setting_exists('cspm_mapstylesettings_style_option', $cspm_v2_options, ''),
						$this->metafield_prefix.'_map_style' => $this->cspm_setting_exists('cspm_mapstylesettings_map_style', $cspm_v2_options, ''),
						$this->metafield_prefix.'_custom_style_name' => $this->cspm_setting_exists('cspm_mapstylesettings_custom_style_name', $cspm_v2_options, ''),
						$this->metafield_prefix.'_js_style_array' => $this->cspm_setting_exists('cspm_mapstylesettings_js_style_array', $cspm_v2_options, ''),
					);
					
					/** 
					 * Infobox settings */
					 
					$infobox_settings = array(
						$this->metafield_prefix.'_show_infobox' => $this->cspm_setting_exists('cspm_infoboxsettings_show_infobox', $cspm_v2_options, ''),
						$this->metafield_prefix.'_infobox_type' => $this->cspm_setting_exists('cspm_infoboxsettings_infobox_type', $cspm_v2_options, ''),
						$this->metafield_prefix.'_infobox_display_event' => $this->cspm_setting_exists('cspm_infoboxsettings_infobox_display_event', $cspm_v2_options, ''),
						$this->metafield_prefix.'_remove_infobox_on_mouseout' => $this->cspm_setting_exists('cspm_infoboxsettings_remove_infobox_on_mouseout', $cspm_v2_options, ''),
						$this->metafield_prefix.'_infobox_external_link' => $this->cspm_setting_exists('cspm_infoboxsettings_infobox_external_link', $cspm_v2_options, ''),
					);
					
					/**
					 * Marker categories settings */
					
					$selected_taxonomy = $this->cspm_setting_exists('cspm_markercategoriessettings_marker_taxonomies', $cspm_v2_options, '');
					
					$marker_categories_settings = array(  
						$this->metafield_prefix.'_marker_cats_settings' => $this->cspm_setting_exists('cspm_markercategoriessettings_marker_cats_settings', $cspm_v2_options, ''),
						$this->metafield_prefix.'_marker_categories_taxonomy' => $selected_taxonomy,					
					);
					
						if(!empty($selected_taxonomy)){
							
							$marker_categories_images_array = array();
							
							$marker_categories_images = (array) json_decode(str_replace('tag_', '', $this->cspm_setting_exists('cspm_markercategoriessettings_marker_category_'.$selected_taxonomy, $cspm_v2_options, '')));
							
							if(is_array($marker_categories_images)){
								
								foreach($marker_categories_images as $marker_image){
									
									if(count((array) $marker_image) > 0){
										
										$new_marker_imag = array();
										
										foreach((array) $marker_image as $key => $value)
											$new_marker_imag[$key.'_'.$selected_taxonomy] = $value;
											
									}
									
									$marker_categories_images_array[] = $new_marker_imag;
								
								}
								
							}
								
							$marker_categories_settings[$this->metafield_prefix.'_marker_categories_images'] = $marker_categories_images_array;
						
						}
					
					/**
					 * KML Layers settings */
					 
					$kml_layers_settings = array( 
						$this->metafield_prefix.'_use_kml' => $this->cspm_setting_exists('cspm_kmlsettings_use_kml', $cspm_v2_options, ''),					
					);
						
						$kml_url = $this->cspm_setting_exists('cspm_kmlsettings_kml_file', $cspm_v2_options, '');
						
						$kml_data_array = array(
							'kml_label' => (!empty($kml_url)) ? 'My KML Layer' : '',
							'kml_url' => $kml_url,
							'kml_suppressInfoWindows' => $this->cspm_setting_exists('cspm_kmlsettings_suppressInfoWindows', $cspm_v2_options, ''),
							'kml_preserveViewport' => $this->cspm_setting_exists('cspm_kmlsettings_preserveViewport', $cspm_v2_options, ''),
							'kml_visibility' => 'true',
						);
						
						$kml_layers_settings[$this->metafield_prefix.'_kml_layers'] = array(0 => $kml_data_array);
					
					/**
					 * Overlays (Polylines & Polygons) settings */
					
					$overlays_settings = array( 						
						$this->metafield_prefix.'_draw_polyline' => $this->cspm_setting_exists('cspm_overlayssettings_draw_polyline', $cspm_v2_options, ''),
						$this->metafield_prefix.'_draw_polygon' => $this->cspm_setting_exists('cspm_overlayssettings_draw_polygon', $cspm_v2_options, ''),
					);
						
						/**
						 * Polylines */
						 
						$polylines_array = array();
						
						$polylines = (array) json_decode(str_replace('tag_', '', $this->cspm_setting_exists('cspm_overlayssettings_polylines', $cspm_v2_options, '')));
						
						if(is_array($polylines)){
							
							foreach($polylines as $polyline){
								$polylines_array[] = (array) $polyline;
							}
							
						}
								
						$overlays_settings[$this->metafield_prefix.'_polylines'] = $polylines_array;
						
						/**
						 * Polygons */
						 
						$polygons_array = array();
						
						$polygons = (array) json_decode(str_replace('tag_', '', $this->cspm_setting_exists('cspm_overlayssettings_polygons', $cspm_v2_options, '')));
						
						if(is_array($polygons)){
							
							foreach($polygons as $polygon){
								$polygons_array[] = (array) $polygon;
							}
						
						}
						
						$overlays_settings[$this->metafield_prefix.'_polygons'] = $polygons_array;
					
					/**
					 * Carousel settings */
					
					$carousel_settings = array(  
						$this->metafield_prefix.'_show_carousel' => $this->cspm_setting_exists('cspm_carouselsettings_show_carousel', $cspm_v2_options, ''),
						$this->metafield_prefix.'_carousel_mode' => $this->cspm_setting_exists('cspm_carouselsettings_carousel_mode', $cspm_v2_options, ''),
						$this->metafield_prefix.'_carousel_scroll' => $this->cspm_setting_exists('cspm_carouselsettings_carousel_scroll', $cspm_v2_options, ''),
						$this->metafield_prefix.'_carousel_easing' => $this->cspm_setting_exists('cspm_carouselsettings_carousel_easing', $cspm_v2_options, ''),
						$this->metafield_prefix.'_carousel_animation' => $this->cspm_setting_exists('cspm_carouselsettings_carousel_animation', $cspm_v2_options, ''),
						$this->metafield_prefix.'_carousel_auto' => $this->cspm_setting_exists('cspm_carouselsettings_carousel_auto', $cspm_v2_options, ''),
						$this->metafield_prefix.'_carousel_wrap' => $this->cspm_setting_exists('cspm_carouselsettings_carousel_wrap', $cspm_v2_options, ''),
						$this->metafield_prefix.'_scrollwheel_carousel' => $this->cspm_setting_exists('cspm_carouselsettings_scrollwheel_carousel', $cspm_v2_options, ''),
						$this->metafield_prefix.'_touchswipe_carousel' => $this->cspm_setting_exists('cspm_carouselsettings_touchswipe_carousel', $cspm_v2_options, ''),
						$this->metafield_prefix.'_move_carousel_on' => array_merge(
							(array) $this->cspm_setting_exists('cspm_carouselsettings_move_carousel_on_marker_click', $cspm_v2_options, ''),
							(array) $this->cspm_setting_exists('cspm_carouselsettings_move_carousel_on_marker_hover', $cspm_v2_options, ''),
							(array) $this->cspm_setting_exists('cspm_carouselsettings_move_carousel_on_infobox_hover', $cspm_v2_options, '')
						),
						$this->metafield_prefix.'_carousel_map_zoom' => $this->cspm_setting_exists('cspm_carouselsettings_carousel_map_zoom', $cspm_v2_options, ''),
					);
					
					/**
					 * Carousel style settings */
					
					$carousel_style_settings = array( 
						$this->metafield_prefix.'_carousel_css' => $this->cspm_setting_exists('cspm_carouselstyle_carousel_css', $cspm_v2_options, ''), 
						$this->metafield_prefix.'_arrows_background' => $this->cspm_setting_exists('cspm_carouselstyle_arrows_background', $cspm_v2_options, ''),
						$this->metafield_prefix.'_items_background' => $this->cspm_setting_exists('key', $cspm_v2_options, ''),
						$this->metafield_prefix.'_items_hover_background' => $this->cspm_setting_exists('key', $cspm_v2_options, ''),
					);
					
					/**
					 * Carousel items settings */
					 
					$carousel_items_settings = array( 
						$this->metafield_prefix.'_items_view' => $this->cspm_setting_exists('key', $cspm_v2_options, ''),
						$this->metafield_prefix.'_horizontal_item_size' => $this->cspm_setting_exists('cspm_itemssettings_horizontal_item_size', $cspm_v2_options, ''),
						$this->metafield_prefix.'_horizontal_image_size' => $this->cspm_setting_exists('cspm_itemssettings_horizontal_image_size', $cspm_v2_options, ''),
						$this->metafield_prefix.'_horizontal_details_size' => $this->cspm_setting_exists('cspm_itemssettings_horizontal_details_size', $cspm_v2_options, ''),
						$this->metafield_prefix.'_vertical_item_size' => $this->cspm_setting_exists('cspm_itemssettings_vertical_item_size', $cspm_v2_options, ''),
						$this->metafield_prefix.'_vertical_image_size' => $this->cspm_setting_exists('cspm_itemssettings_vertical_image_size', $cspm_v2_options, ''),
						$this->metafield_prefix.'_vertical_details_size' => $this->cspm_setting_exists('cspm_itemssettings_vertical_details_size', $cspm_v2_options, ''),
						$this->metafield_prefix.'_show_details_btn' => $this->cspm_setting_exists('cspm_itemssettings_show_details_btn', $cspm_v2_options, ''),
						$this->metafield_prefix.'_details_btn_text' => $this->cspm_setting_exists('cspm_itemssettings_details_btn_text', $cspm_v2_options, ''),
						$this->metafield_prefix.'_click_on_title' => $this->cspm_setting_exists('cspm_itemssettings_click_on_title', $cspm_v2_options, ''),
						$this->metafield_prefix.'_external_link' => $this->cspm_setting_exists('cspm_itemssettings_external_link', $cspm_v2_options, ''),
						$this->metafield_prefix.'_items_details' => $this->cspm_setting_exists('cspm_itemssettings_items_details', $cspm_v2_options, ''),
					);
					
					/**
					 * Posts count settings */
					
					$posts_count_settings = array(  
						$this->metafield_prefix.'_show_posts_count' => $this->cspm_setting_exists('cspm_postscountsettings_show_posts_count', $cspm_v2_options, ''),
						$this->metafield_prefix.'_posts_count_clause' => $this->cspm_setting_exists('cspm_postscountsettings_posts_count_clause', $cspm_v2_options, ''),
						$this->metafield_prefix.'_posts_count_color' => $this->cspm_setting_exists('cspm_postscountsettings_posts_count_color', $cspm_v2_options, ''),
					);
					
					/**
					 * Faceted search settings */
					 
					$faceted_search_settings = array( 
						$this->metafield_prefix.'_faceted_search_option' => $this->cspm_setting_exists('cspm_facetedsearchsettings_faceted_search_option', $cspm_v2_options, ''),						
						$this->metafield_prefix.'_faceted_search_multi_taxonomy_option' => $this->cspm_setting_exists('cspm_facetedsearchsettings_faceted_search_multi_taxonomy_option', $cspm_v2_options, ''),
						$this->metafield_prefix.'_faceted_search_drag_map' => $this->cspm_setting_exists('cspm_facetedsearchsettings_faceted_search_drag_map', $cspm_v2_options, ''),
						$this->metafield_prefix.'_faceted_search_input_skin' => $this->cspm_setting_exists('cspm_facetedsearchsettings_faceted_search_input_skin', $cspm_v2_options, ''),
						$this->metafield_prefix.'_faceted_search_input_color' => $this->cspm_setting_exists('cspm_facetedsearchsettings_faceted_search_input_color', $cspm_v2_options, ''),
						$this->metafield_prefix.'_faceted_search_css' => $this->cspm_setting_exists('cspm_facetedsearchsettings_faceted_search_css', $cspm_v2_options, ''),
					);
					
						if(!empty($selected_taxonomy)){
						
							$faceted_search_settings[$this->metafield_prefix.'_faceted_search_taxonomy_'.$selected_taxonomy] = $this->cspm_setting_exists('cspm_facetedsearchsettings_faceted_search_taxonomy_'.$selected_taxonomy, $cspm_v2_options, '');
							
						}
					
					/**
					 * Search form settings */
					 
					$search_form_settings = array( 
						$this->metafield_prefix.'_search_form_option' => $this->cspm_setting_exists('cspm_searchformsettings_search_form_option', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_distance_unit' => $this->cspm_setting_exists('cspm_searchformsettings_sf_distance_unit', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_address_placeholder' => $this->cspm_setting_exists('cspm_searchformsettings_address_placeholder', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_slider_label' => $this->cspm_setting_exists('cspm_searchformsettings_slider_label', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_submit_text' => $this->cspm_setting_exists('cspm_searchformsettings_submit_text', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_search_form_bg_color' => $this->cspm_setting_exists('cspm_searchformsettings_search_form_bg_color', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_no_location_msg' => $this->cspm_setting_exists('cspm_searchformsettings_no_location_msg', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_bad_address_msg' => $this->cspm_setting_exists('cspm_searchformsettings_bad_address_msg', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_bad_address_sug_1' => $this->cspm_setting_exists('cspm_searchformsettings_bad_address_sug_1', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_bad_address_sug_2' => $this->cspm_setting_exists('cspm_searchformsettings_bad_address_sug_2', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_bad_address_sug_3' => $this->cspm_setting_exists('cspm_searchformsettings_bad_address_sug_3', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_circle_option' => $this->cspm_setting_exists('cspm_searchformsettings_circle_option', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_fillColor' => $this->cspm_setting_exists('cspm_searchformsettings_fillColor', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_fillOpacity' => $this->cspm_setting_exists('cspm_searchformsettings_fillOpacity', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_strokeColor' => $this->cspm_setting_exists('cspm_searchformsettings_strokeColor', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_strokeOpacity' => $this->cspm_setting_exists('cspm_searchformsettings_strokeOpacity', $cspm_v2_options, ''),
						$this->metafield_prefix.'_sf_strokeWeight' => $this->cspm_setting_exists('cspm_searchformsettings_strokeWeight', $cspm_v2_options, ''),
					);
					
						$distance = explode(',', $this->cspm_setting_exists('cspm_searchformsettings_sf_search_distances', $cspm_v2_options, array(3,15)));
						
						$search_form_settings[$this->metafield_prefix.'_sf_min_search_distances'] = $this->cspm_setting_exists(0, $distance, '3');
						$search_form_settings[$this->metafield_prefix.'_sf_max_search_distances'] = $this->cspm_setting_exists(1, $distance, '15');
					
					/**
					 * Merge all "Progress Map" settings arrays into one */
					
					$cspm_settings_arrays = array_merge(
						array($this->metafield_prefix.'_post_type' => $main_post_type),
						$query_settings,
						$layout_settings,
						$map_settings,
						$map_style_settings,
						$infobox_settings,
						$marker_categories_settings,
						$kml_layers_settings,
						$overlays_settings,
						$carousel_settings,
						$carousel_style_settings,
						$carousel_items_settings,
						$posts_count_settings,
						$faceted_search_settings,
						$search_form_settings
					);
					
					/**
					 * == List & filter Extension settings == */
					
					if(class_exists('ProgressMapList')){
						
						$cspml_v1_options = get_option('cspml_settings');
					
						/**
						 * Layout settings */
						  
						$list_layout_settings = array( 
							$this->metafield_prefix.'_list_layout' => $this->cspm_setting_exists('cspml_layout_list_layout', $cspml_v1_options, ''),
							$this->metafield_prefix.'_map_height' => $this->cspm_setting_exists('cspml_layout_map_height', $cspml_v1_options, ''),
							$this->metafield_prefix.'_list_height' => $this->cspm_setting_exists('cspml_layout_list_height', $cspml_v1_options, ''),
						);
						
						/**
						 * Options bar settings */
						 
						$options_bar_settings = array( 
							$this->metafield_prefix.'_show_options_bar' => $this->cspm_setting_exists('cspml_options_bar_show_options_bar', $cspml_v1_options, ''),
							$this->metafield_prefix.'_show_view_options' => $this->cspm_setting_exists('cspml_options_bar_show_view_options', $cspml_v1_options, ''),
							$this->metafield_prefix.'_default_view_option' => $this->cspm_setting_exists('cspml_options_bar_default_view_option', $cspml_v1_options, ''),
							$this->metafield_prefix.'_grid_cols' => $this->cspm_setting_exists('cspml_options_bar_grid_cols', $cspml_v1_options, ''),
							$this->metafield_prefix.'_cslf_show_posts_count' => $this->cspm_setting_exists('cspml_options_bar_show_posts_count', $cspml_v1_options, ''),
							$this->metafield_prefix.'_cslf_posts_count_clause' => $this->cspm_setting_exists('cspml_options_bar_posts_count_clause', $cspml_v1_options, ''),
						);
						
						/**
						 * List items settings */
		
						$list_items_settings = array( 
							$this->metafield_prefix.'_listings_title' => $this->cspm_setting_exists('cspml_list_items_listings_title', $cspml_v1_options, ''),					 
							$this->metafield_prefix.'_listings_details' => $this->cspm_setting_exists('cspml_list_items_listings_details', $cspml_v1_options, ''),
							$this->metafield_prefix.'_cslf_click_on_title' => $this->cspm_setting_exists('cspml_list_items_click_on_title', $cspml_v1_options, ''),
							$this->metafield_prefix.'_cslf_click_on_img' => $this->cspm_setting_exists('cspml_list_items_click_on_img', $cspml_v1_options, ''),
							$this->metafield_prefix.'_cslf_external_link' => $this->cspm_setting_exists('cspml_list_items_external_link', $cspml_v1_options, ''),
							$this->metafield_prefix.'_show_fire_pinpoint_btn' => $this->cspm_setting_exists('cspml_list_items_show_fire_pinpoint_btn', $cspml_v1_options, ''),
						);
						
						/**
						 * Sort options settings */
						
						$sort_options_settings = array(  
							$this->metafield_prefix.'_show_sort_option' => $this->cspm_setting_exists('cspml_sort_option_show_sort_option', $cspml_v1_options, ''),
							$this->metafield_prefix.'_sort_options' => $this->cspm_setting_exists('cspml_sort_option_sort_options', $cspml_v1_options, ''),
						);
							 
							$custom_sort_options_array = array();
							
							$custom_sort_options = (array) json_decode(str_replace('tag_', '', $this->cspm_setting_exists('cspml_sort_option_custom_sort_options', $cspml_v1_options, '')));
							
							if(is_array($custom_sort_options)){
								
								foreach($custom_sort_options as $custom_sort_option){
									$custom_sort_options_array[] = (array) $custom_sort_option;
								}
							
							}
							
							$sort_options_settings[$this->metafield_prefix.'_custom_sort_options'] = $custom_sort_options_array;
						
						/**
						 * Pagination settings */
						
						$pagination_settings = array( 
							$this->metafield_prefix.'_posts_per_page' => $this->cspm_setting_exists('cspml_pagiantion_posts_per_page', $cspml_v1_options, ''),						 
							$this->metafield_prefix.'_pagination_position' => $this->cspm_setting_exists('cspml_pagiantion_pagination_position', $cspml_v1_options, ''),
							$this->metafield_prefix.'_pagination_align' => $this->cspm_setting_exists('cspml_pagiantion_pagination_align', $cspml_v1_options, ''),
							$this->metafield_prefix.'_prev_page_text' => $this->cspm_setting_exists('cspml_pagiantion_prev_page_text', $cspml_v1_options, ''),
							$this->metafield_prefix.'_next_page_text' => $this->cspm_setting_exists('cspml_pagiantion_next_page_text', $cspml_v1_options, ''),
							$this->metafield_prefix.'_show_all' => $this->cspm_setting_exists('cspml_pagiantion_show_all', $cspml_v1_options, ''),
						);
						
						/**
						 * Filter search settings */
						
						$filter_search_settings = array(  
							$this->metafield_prefix.'_cslf_faceted_search_option' => $this->cspm_setting_exists('cspml_list_filter_faceted_search_option', $cspml_v1_options, ''),
							$this->metafield_prefix.'_faceted_search_position' => $this->cspm_setting_exists('cspml_list_filter_faceted_search_position', $cspml_v1_options, ''),
							$this->metafield_prefix.'_faceted_search_display_option' => $this->cspm_setting_exists('cspml_list_filter_filter_btns_position', $cspml_v1_options, ''),
							$this->metafield_prefix.'_filter_btns_position' => $this->cspm_setting_exists('key', $cspml_v1_options, ''),
							$this->metafield_prefix.'_cslf_taxonomy_relation_param' => $this->cspm_setting_exists('cspml_list_filter_taxonomy_relation_param', $cspml_v1_options, ''),
							$this->metafield_prefix.'_cslf_custom_field_relation_param' => $this->cspm_setting_exists('cspml_list_filter_custom_field_relation_param', $cspml_v1_options, ''),
							$this->metafield_prefix.'_cslf_filter_btn_text' => $this->cspm_setting_exists('cspml_list_filter_filter_btn_text', $cspml_v1_options, ''),
						);
							
							/**
							 * Taxonomies */
							  
							$taxonomies_array = array();
							
							$taxonomies = (array) json_decode(str_replace('tag_', '', $this->cspm_setting_exists('cspml_list_filter_taxonomies', $cspml_v1_options, '')));
							
							if(is_array($taxonomies)){
								
								foreach($taxonomies as $taxonomy){
									$taxonomies_array[] = (array) $taxonomy;
								}
								
							}
									
							$filter_search_settings[$this->metafield_prefix.'_cslf_taxonomies'] = $taxonomies_array;
							
							/**
							 * Custom Fields */
							  
							$custom_fields_array = array();
							
							$custom_fields = (array) json_decode(str_replace('tag_', '', $this->cspm_setting_exists('cspml_list_filter_custom_fields', $cspml_v1_options, '')));
							
							if(is_array($custom_fields)){
								
								foreach($custom_fields as $custom_field){
									$custom_fields_array[] = (array) $custom_field;
								}
							
							}
							
							$filter_search_settings[$this->metafield_prefix.'_cslf_custom_fields'] = $custom_fields_array;
						
						/**
						 * Merge all "List & Filter" settings arrays into one */
						
						$cspml_settings_arrays = array_merge(
							array($this->metafield_prefix.'_list_ext' => 'on'),					
							$list_layout_settings,
							$options_bar_settings,
							$list_items_settings,
							$sort_options_settings,
							$pagination_settings,
							$filter_search_settings
						);
					
					}else $cspml_settings_arrays = array();
					
					/**
					 * Build wp_insert_post() args */
					 
					$insert_post_args = array(
						'post_type' => $this->object_type,
						'post_status' => 'publish',
						'post_title' => 'Auto generated map based on v2 settings',
						'meta_input' => array_merge(						
							
							/**
							 * Progress Map settings */
							 
							$cspm_settings_arrays,
							
							/**
							 * List & filter settings */
							
							$cspml_settings_arrays
							
						),
					);
								
					wp_insert_post($insert_post_args);

					/**
					 * Regenerate Markers just in case */
					 
					$this->cspm_regenerate_markers(false);	
				
				}
					
				/**
				 * === Changes required for the version 4.0 */
				
				if(version_compare($this->plugin_version, '4.0', '>=')){
			
					/**
					 * It's based on this options that we'll define if the below code has been executed before or not.
					 * This will first check if it's a plugin update or a new installation by looking for the option "cspm_settings" ...
					 * ... then it'll look for the option "cspm_default_settings" to define if we'll excute this code or not.
					 */
					 
					if(get_option($opt_group.'_settings') && !get_option($this->plugin_settings_option_key)){
					
						$v3_settings = get_option($opt_group.'_settings');
						$v4_settings = array();
						
						unset($v3_settings['cspm_hiddensettings_json_markers_method']);
						
						$remove_gmaps_api = array();
						
						foreach($v3_settings as $key => $value){
							
							$new_key_name = str_replace(array(
								'cspm_pluginsettings_',
								'cspm_mapsettings_', 
								'cspm_mapstylesettings_', 
								'cspm_infoboxsettings_',
								'cspm_troubleshooting_',
								'cspm_customize_',
							), '', $key);
							
							if($new_key_name == 'remove_gmaps_api_disable_frontend'){
								
								if($value == 'disable_frontend'){
									$new_key_name = 'remove_gmaps_api';
									$remove_gmaps_api[] = $value;
									$v4_settings[$new_key_name] = $remove_gmaps_api;
								}else unset($new_key_name);
							
							}elseif($new_key_name == 'remove_gmaps_api_disable_backend'){
								
								if($value == 'disable_backend'){
									$new_key_name = 'remove_gmaps_api';
									$remove_gmaps_api[] = $value;
									$v4_settings[$new_key_name] = $remove_gmaps_api;
								}else unset($new_key_name);
														
							}else $v4_settings[$new_key_name] = $value;
							
						}
						
						$v4_settings['marker_icon_height'] = '-1';
						$v4_settings['marker_icon_width'] = '-1';
						$v4_settings['user_marker_icon_height'] = '-1';
						$v4_settings['user_marker_icon_width'] = '-1';
						$v4_settings['big_cluster_icon_height'] = '-1';
						$v4_settings['big_cluster_icon_width'] = '-1';
						$v4_settings['medium_cluster_icon_height'] = '-1';
						$v4_settings['medium_cluster_icon_width'] = '-1';
						$v4_settings['small_cluster_icon_height'] = '-1';
						$v4_settings['small_cluster_icon_width'] = '-1';
						$v4_settings['infobox_height'] = '-1';
						$v4_settings['infobox_width'] = '-1';
						
						update_option($this->plugin_settings_option_key, $v4_settings);
												
					}
				
				}
					
				/**
				 * === Changes required for the version 5.0 */
				
				if(version_compare($this->plugin_version, '5.0', '>=')){
					
					$default_settings = get_option($this->plugin_settings_option_key);
					
					$outer_links_field_name = $this->cspm_setting_exists('outer_links_field_name', $default_settings, '');
					
					if(!isset($default_settings['last_sync'])){
						
						$default_settings['last_sync'] = '5.0';

						global $post;
						
						$query_v5_0 = get_posts(array(
							'post_type' => $this->object_type,
							'post_status' => 'publish',
							'posts_per_page' => -1,
						));
						
						foreach($query_v5_0 as $post){
							
							setup_postdata($post);
							
							$post_id = $post->ID;							
							
							if(!empty($outer_links_field_name))
								update_post_meta($post_id, $this->metafield_prefix . '_single_posts_link', 'custom');
												
							/**
							 * Infobox content parameters */
							 
							$items_title = get_post_meta($post_id, $this->metafield_prefix . '_items_title', true);
							$items_details = get_post_meta($post_id, $this->metafield_prefix . '_items_details', true);
							$items_ellipses = get_post_meta($post_id, $this->metafield_prefix . '_ellipses', true);
							
							update_post_meta($post_id, $this->metafield_prefix . '_infobox_title', $items_title);
							update_post_meta($post_id, $this->metafield_prefix . '_infobox_content', $items_details);
							update_post_meta($post_id, $this->metafield_prefix . '_infobox_ellipses', $items_ellipses);

						}
						
						update_option($this->plugin_settings_option_key, $default_settings);
						
						wp_reset_postdata();
						
					}

				}

				/**
				 * === Changes required for the version 5.2 */
				
				if(version_compare($this->plugin_version, '5.2', '>=')){
					
					$default_settings = get_option($this->plugin_settings_option_key);
					
					if(!isset($default_settings['last_sync']) 
					|| (isset($default_settings['last_sync']) && $default_settings['last_sync'] == '5.0')){
					
						$default_settings['last_sync'] = '5.2';

						global $post;
						
						$query_v5_2 = get_posts(array(
							'post_type' => $this->object_type,
							'post_status' => 'publish',
							'posts_per_page' => -1,
						));
						
						foreach($query_v5_2 as $post){
							
							setup_postdata($post);
							
							$post_id = $post->ID;
							
							/**
							 * Change "Ground Overlays" structure */
							 
							$ground_overlays = get_post_meta($post_id, $this->metafield_prefix . '_ground_overlays', true);
							$new_ground_overlays = array();
							
							if(is_array($ground_overlays) && count($ground_overlays) > 0){
								
								foreach($ground_overlays as $ground_overlay_data){
									
									$image_url = (isset($ground_overlay_data['image_url'])) ? esc_url($ground_overlay_data['image_url']) : '';
									$ne_bounds = (isset($ground_overlay_data['ne_bounds'])) ? esc_attr($ground_overlay_data['ne_bounds']) : '';
									$sw_bounds = (isset($ground_overlay_data['sw_bounds'])) ? esc_attr($ground_overlay_data['sw_bounds']) : '';
									
									if(!empty($image_url) && !empty($ne_bounds) && !empty($sw_bounds)){
										$ground_overlay_data['image_url_and_bounds'] = array(
											'image_url' => $image_url,
											'ne_bounds' => $ne_bounds,
											'sw_bounds' => $sw_bounds,
										);
									}
									
									$new_ground_overlays[] = $ground_overlay_data;
									
								}
							
								update_post_meta($post_id, $this->metafield_prefix . '_ground_overlays', $new_ground_overlays);
								
							}
							
							/**
							 * Change "Polylines" structure */
							 
							$polylines = get_post_meta($post_id, $this->metafield_prefix . '_polylines', true);
							$new_polylines = array();
							
							if(is_array($polylines) && count($polylines) > 0){
								
								foreach($polylines as $polyline_data){
								
									$polyline_path = (isset($polyline_data['polyline_path'])) ? esc_attr($polyline_data['polyline_path']) : '';
									
									if(strpos($polyline_path, '],[') !== false){
										$polyline_data['polyline_points_type'] = 'latlng';
									}elseif(!empty($polyline_path)){
										$polyline_data['polyline_points_type'] = 'post_ids';
										$polyline_data['polyline_path_ids'] = explode(',', $polyline_path);
										$polyline_data['polyline_path'] = '';										
									}else $polyline_data['polyline_points_type'] = 'latlng';
									
									$new_polylines[] = $polyline_data;
									
								}
								
								update_post_meta($post_id, $this->metafield_prefix . '_polylines', $new_polylines);
							
							}
							
							/**
							 * Change "Polygons" structure */
							 
							$polygons = get_post_meta($post_id, $this->metafield_prefix . '_polygons', true);
							$new_polygons = array();
							
							if(is_array($polygons) && count($polygons) > 0){
								
								foreach($polygons as $polygon_data){
								
									$polygon_path = (isset($polygon_data['polygon_path'])) ? esc_attr($polygon_data['polygon_path']) : '';
									
									if(strpos($polygon_path, '],[') !== false){
										$polygon_data['polygon_points_type'] = 'latlng';
									}elseif(!empty($polygon_path)){
										$polygon_data['polygon_points_type'] = 'post_ids';
										$polygon_data['polygon_path_ids'] = explode(',', $polygon_path);	
										$polygon_data['polygon_path'] = '';										
									}else $polygon_data['polygon_points_type'] = 'latlng';
									
									$new_polygons[] = $polygon_data;
									
								}
								
								update_post_meta($post_id, $this->metafield_prefix . '_polygons', $new_polygons);
							
							}
							
						}
						
						update_option($this->plugin_settings_option_key, $default_settings);
						
						wp_reset_postdata();
						
					}
					
				}
				
			}
						 
		}
		
		
		/**
		 * This will get the current post type in the WordPress Admin
		 * 
		 * @since 3.1
		 */
		function cspm_get_current_post_type() {
			
			global $post, $typenow, $current_screen, $pagenow;
			
			/**
			 * we have a post so we can just get the post type from that */
			 
			if($post && $post->post_type)
				return $post->post_type;
			
			/**
			 * check the global $typenow - set in admin.php */
			 
			elseif($typenow)
				return $typenow;
			
			/**
			 * check the global $current_screen object - set in sceen.php */
			 
			elseif($current_screen && $current_screen->post_type)
				return $current_screen->post_type;
			
			/**
			 * check the post_type querystring */
			 
			elseif(isset($_REQUEST['post_type']))
				return sanitize_key($_REQUEST['post_type']);
			
			/**
			 * Check if post ID is in query string */
			 
			elseif(isset($_REQUEST['post']))
				return get_post_type($_REQUEST['post']);
			
			/**
			 * Detect the default post type "post" when adding new posts */
			 
			elseif($pagenow && $pagenow == 'post-new.php' && !isset($_REQUEST['post_type']))
				return 'post';
			
			/**
			 * we do not know the post type! */
			 
			else return '';
			
		}
		
 
		/*
		 * This will add a duplicate link to the "All maps" list
		 *
		 * @since 3.2
		 */
		function cspm_duplicate_map_link($actions, $post){
			
			if($post->post_type == $this->object_type && current_user_can('edit_posts')){
				
				$actions['cspm_duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=cspm_duplicate_map&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ) . '" title="Duplicate this map" rel="permalink" class="cspm_duplicate_map_link">Duplicate this map</a>';
				
			}
			
			return $actions;
			
		}
		
		
		/**
		 * A custom CSS style for our duplicate link
		 *
		 * @since 3.2
		 */
		function cspm_duplicate_map_link_style(){
			
			echo '<style>
				a.cspm_duplicate_map_link{
					padding: 5px 10px 7px 10px;
					background: #FE5E05;
					border-radius: 2px;
					color: #fff;
					box-shadow: rgba(0,0,0,.298039) 0 1px 4px -1px, inset 0 -1px 0 0 rgba(0,0,0,.24);
					display:inline-block;
					margin-top:5px;
				}				
				a.cspm_duplicate_map_link:hover{
					background:#FF3902;
				}
			</style>';
		  
		}


		/*
		 * Duplicate a map and redirect to the edit map screen
		 *
		 * @since 3.2
		 */
		function cspm_duplicate_map(){
			
			global $wpdb;
			
			if(!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'cspm_duplicate_map' == $_REQUEST['action'])))
				wp_die('No map to duplicate has been supplied!');
		 
			/*
			 * Nonce verification */
			 
			if(!isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], basename( __FILE__ )))
				return;
		 
			/*
			 * Get the original post id */
			 
			$post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
			
			/*
			 * Get all the original post data */
			 
			$post = get_post($post_id);
		 
			/*
			 * if you don't want current user to be the new post author,
			 * then change next couple of lines to this: $new_post_author = $post->post_author; */
			 
			$current_user = wp_get_current_user();
			$new_post_author = $current_user->ID;
		 
			/*
			 * if post data exists, create the post duplicate */
			 
			if(isset($post) && $post != null){
		 
				/*
				 * new post data array */
				 
				$args = array(
					'comment_status' => $post->comment_status,
					'ping_status'    => $post->ping_status,
					'post_author'    => $new_post_author,
					'post_content'   => $post->post_content,
					'post_excerpt'   => $post->post_excerpt,
					'post_name'      => $post->post_name,
					'post_parent'    => $post->post_parent,
					'post_password'  => $post->post_password,
					'post_status'    => 'publish',
					'post_title'     => 'Duplicate of, "'.$post->post_title.'"',
					'post_type'      => $post->post_type,
					'to_ping'        => $post->to_ping,
					'menu_order'     => $post->menu_order
				);
		 
				/*
				 * Insert the post by wp_insert_post() function */
				 
				$new_post_id = wp_insert_post( $args );
		 
				/*
				 * Duplicate all post meta just in two SQL queries */
				 
				$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
				
				if(count($post_meta_infos) != 0){
					
					$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
					
                    $sql_query_sel = array(); //@since 5.7.1
                    
					foreach ($post_meta_infos as $meta_info){
						
						$meta_key = $meta_info->meta_key;
						
						if($meta_key == '_wp_old_slug')
							continue;
						
						$meta_value = addslashes($meta_info->meta_value);
						
						$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
					
					}
					
					$sql_query .= implode(" UNION ALL ", $sql_query_sel);
					
					$wpdb->query($sql_query);
					
				}
		 
				/*
				 * Finally, redirect to the edit map screen */
				 
				wp_redirect(admin_url('post.php?action=edit&post='.$new_post_id));
				
				exit;
				
			}else wp_die('Map creation failed, could not find the original map: '.$post_id);
		
		}
		
		
		/**
		 * This contains needed CSS code to customize and change the main colors in the plugin
		 *
		 * @since 3.8
		 * @updated 5.3 [Added infobox close button class name]
		 */
		function cspm_main_colors(){
			
			$main_rgb_color = $this->main_rgb_color;
			$main_rgb_hover_color = $this->main_rgb_hover_color;
			
			$main_hex_color = $this->main_hex_color;
			$main_hex_hover_color = $this->main_hex_hover_color;
			
			$main_colors_css = '
				
				.cspm_bg_rgb, .cspm_bg_rgb_hover, .cspm_bg_before_rgb:before, .cspm_bg_after_rgb:after{background-color: '.$main_rgb_color.' !important;}
				.cspm_bg_rgb_hover:hover, .cspm_bg_rgb_only_hover:hover{background-color: '.$main_rgb_hover_color.' !important;}	
				
				.cspm_bg_hex, .cspm_bg_hex_hover, .cspm_bg_before_hex:before, .cspm_bg_after_hex:after{background-color: '.$main_hex_color.' !important;}
				.cspm_bg_hex_hover:hover, .cspm_bg_hex_only_hover:hover{background-color: '.$main_hex_hover_color.' !important;}
				
				.cspm_border_rgb, .cspm_border_after_rgb:after, .cspm_border_before_rgb:before{border-color: '.$main_rgb_color.' !important;}
				.cspm_border_top_rgb, .cspm_border_top_after_rgb:after, .cspm_border_top_before_rgb:before{border-top-color: '.$main_rgb_color.' !important;}
				.cspm_border_bottom_rgb, .cspm_border_bottom_after_rgb:after, .cspm_border_bottom_before_rgb:before{border-bottom-color: '.$main_rgb_color.' !important;}
				.cspm_border_left_rgb, .cspm_border_left_after_rgb:after, .cspm_border_left_before_rgb:before{border-left-color: '.$main_rgb_color.' !important;}
				.cspm_border_right_rgb, .cspm_border_right_after_rgb:after, .cspm_border_right_before_rgb:before{border-right-color: '.$main_rgb_color.' !important;}
				
				.cspm_border_hex, .cspm_border_after_hex:after, .cspm_border_before_hex:before{border-color: '.$main_hex_color.' !important;}
				.cspm_border_top_hex, .cspm_border_top_after_hex:after, .cspm_border_top_before_hex:before{border-top-color: '.$main_hex_color.' !important;}
				.cspm_border_bottom_hex, .cspm_border_bottom_after_hex:after, .cspm_border_bottom_before_hex:before{border-bottom-color: '.$main_hex_color.' !important;}
				.cspm_border_left_hex, .cspm_border_left_after_hex:after, .cspm_border_left_before_hex:before{border-left-color: '.$main_hex_color.' !important;}
				.cspm_border_right_hex, .cspm_border_right_after_hex:after, .cspm_border_right_before_hex:before{border-right-color: '.$main_hex_color.' !important;}
				
				.cspm_txt_rgb, .cspm_link_rgb a, .cspm_txt_rgb_hover, .cspm_txt_rgb_hover a{color: '.$main_rgb_color.' !important;}
				.cspm_txt_rgb_hover:hover, .cspm_txt_rgb_hover a:hover{color: '.$main_rgb_hover_color.' !important;}
				
				.cspm_txt_hex, .cspm_link_hex a, .cspm_txt_hex_hover, .cspm_txt_hex_hover a, .cspm_marker_overlay button.si-close-button{color: '.$main_hex_color.' !important;}
				.cspm_txt_hex_hover:hover, .cspm_txt_hex_hover a:hover, .cspm_marker_overlay button.si-close-button:hover{color: '.$main_hex_hover_color.' !important;}
				
			';
			
			return $main_colors_css;
			
		}


		/**
		 * This will convert a hexa decimal color code to its RGB equivalent
		 *
		 * @since 3.8 
		 */
		function cspm_hex2RGB($hexStr, $returnAsString = true, $seperator = ','){
			
			$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
			
			$rgbArray = array();
			
			if(strlen($hexStr) == 6){ // If a proper hex code, convert using bitwise operation. No overhead... faster
				
				$colorVal = hexdec($hexStr);
				$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
				$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
				$rgbArray['blue'] = 0xFF & $colorVal;
				
			}elseif(strlen($hexStr) == 3){ // if shorthand notation, need some string manipulations
				
				$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
				$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
				$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
			
			}else return false; // Invalid hex color code
			
			return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
		
		}
		
 
		/*
		 * This will add a link to regenerate given map markers
		 *
		 * @since 3.9
		 */
		function cspm_regenerate_map_markers_link($actions, $post){
			
			if($post->post_type == $this->object_type && current_user_can('edit_posts')){
				
				$actions['regenerate'] = '<a href="' . wp_nonce_url('admin.php?action=cspm_regenerate_markers&post=' . $post->ID, basename(__FILE__), 'regenerate_markers_nonce' ) . '" title="Regenerate markers" rel="permalink" class="cspm_regenerate_markers_link">Regenerate markers</a>';
				
			}
			
			return $actions;
			
		}
		
		
		/**
		 * A custom CSS style for our regenerate map markers link
		 *
		 * @since 3.2
		 */
		function cspm_regenerate_map_markers_link_style(){
			
			echo '<style>
				a.cspm_regenerate_markers_link{
					padding: 5px 10px 7px 10px;
					background: #008fed;
					border-radius: 2px;
					color: #fff;
					box-shadow: rgba(0,0,0,.298039) 0 1px 4px -1px, inset 0 -1px 0 0 rgba(0,0,0,.24);
					display:inline-block;
					margin-top:5px;
				}				
				a.cspm_regenerate_markers_link:hover{
					background:#009bfd;
				}
			</style>';

		}


		/**
		 * Regenerate given map markers
		 *
		 * @since 3.9
		 */
		function cspm_regenerate_map_markers(){
			
			global $wpdb;
			
			if(!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'cspm_regenerate_markers' == $_REQUEST['action'])))
				wp_die('Can\'t regenerate markers. No map has been supplied!');
		 
			/**
			 * Nonce verification */
			 
			if(!isset($_GET['regenerate_markers_nonce']) || !wp_verify_nonce($_GET['regenerate_markers_nonce'], basename( __FILE__ )))
				return;

			/**
			 * Get the post id */
			 
			$map_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
			
			/**
			 * Get the post type */
			 
			$post = get_post_type($map_id);
		 
			/**
			 * if post type exists, regenerate markers */
			 
			if($post && $post == $this->object_type){
				
				$markers_object = $this->cspm_get_post_type_markers_obj($map_id, get_post_meta($map_id, $this->metafield_prefix.'_post_type', true));
										
				/**
				 * Update markers object */
				 
				if(count($markers_object) > 0){
					$cspm_markers_array = get_option('cspm_markers_array');
					update_option('cspm_markers_array', array_merge($cspm_markers_array, $markers_object));
				}
				
				/**
				 * Finally, redirect to the edit map screen */
				 
				wp_redirect(admin_url('edit.php?post_type='.$this->object_type));
				exit;
				
			}else wp_die('Regenerate markers failed, could not find the map: '.$map_id);
		
		 
		}
		

		/**
		 * Get post type posts and create a JSON array of markers based on the ...
		 * ... custom fields latitude & longitude + Secondary lat & lng
		 *
		 * @since 3.9
		 * @updated 5.6.3 | Possibility to convert address to latLng
		 */
		function cspm_get_post_type_markers_obj($map_id, $post_type){

			if(empty($post_type))
				return;
						
			if(!class_exists('CspmMainMap'))
				return;
			
			$CspmMainMap = CspmMainMap::this();
				
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
			 * Set map settings */

			$CspmMainMap->map_settings = $map_settings;
			
			/**
			 * Our custom fields */
			 		
			$meta_values = array(
				CSPM_LATITUDE_FIELD, 
				CSPM_LONGITUDE_FIELD, 
				CSPM_SECONDARY_LAT_LNG_FIELD,
				CSPM_FORMAT_FIELD, //@since 3.5
				CSPM_AUDIO_FIELD, //@since 3.5
				CSPM_IMAGE_FIELD.'_id', //@since 3.5
				CSPM_EMBED_FIELD, //@since 3.5
				CSPM_GALLERY_FIELD, //@since 3.5
				CSPM_ADDRESS_FIELD, //@since 5.4
			);
											
			/**
			 * Init the array that will contain all markers of the post type */
			 
			$post_types_markers = array();
			
			/**
			 * Get all the values of the Latitude/Longitude/Secondary coordinates ...
			 * ... where each row in the array contains the value of the custom field and ...
			 * ... the post ID related to */
			 
			foreach($meta_values as $meta_value)
				$post_types_markers[$post_type][$meta_value] = $CspmMainMap->cspm_get_meta_values($meta_value, $post_type);
							
			$post_types_markers[$post_type] = array_merge_recursive(
				$post_types_markers[$post_type][CSPM_LATITUDE_FIELD], 
				$post_types_markers[$post_type][CSPM_LONGITUDE_FIELD],
				$post_types_markers[$post_type][CSPM_SECONDARY_LAT_LNG_FIELD],
				$post_types_markers[$post_type][CSPM_FORMAT_FIELD], //@since 3.5
				$post_types_markers[$post_type][CSPM_AUDIO_FIELD], //@since 3.5
				$post_types_markers[$post_type][CSPM_IMAGE_FIELD.'_id'], //@since 3.5
				$post_types_markers[$post_type][CSPM_EMBED_FIELD], //@since 3.5
				$post_types_markers[$post_type][CSPM_GALLERY_FIELD], //@since 3.5
				$post_types_markers[$post_type][CSPM_ADDRESS_FIELD] //@since 5.4
			);								
		
			global $wpdb;
			
			$markers_object = $post_taxonomy_terms = array();
			
			/**
			 * Create the map markers object for each post type */
										
			foreach($post_types_markers as $post_type => $posts_and_coordinates){
				
				$i = $j = 0;						
				
				/**
				 * Get post type taxonomies */
				 
				$post_taxonomies = (array) get_object_taxonomies($post_type, 'names');
					if(($key = array_search('post_format', $post_taxonomies)) !== false) {
						unset($post_taxonomies[$key]);
					}
					
				/**
				 * Implode taxonomies to use them in the Mysql IN clause */
				 
				$taxonomies = "'" . implode("', '", $post_taxonomies) . "'";
				
				/**
				 * Directly querying the database is normally frowned upon, but all ...
				 * ... of the API functions will return the full post objects which will
				 * ... suck up lots of memory. This is best, just not as future proof */
				 
				$query = "SELECT t.term_id, tt.taxonomy, tr.object_id FROM $wpdb->terms AS t 
							INNER JOIN $wpdb->term_taxonomy AS tt 
								ON tt.term_id = t.term_id 
							INNER JOIN $wpdb->term_relationships AS tr 
								ON tr.term_taxonomy_id = tt.term_taxonomy_id 
							WHERE tt.taxonomy IN ($taxonomies)";
				
				/**
				 * Run the query. This will get an array of all terms where each term ...
				 * ... is listed with the taxonomy name and the post id */
				 
				$taxonomy_terms_and_posts = $wpdb->get_results( $query, ARRAY_A );
		
				/**
				 * Loop through the terms and order them in a way, the array will have the post_id as key ...
				 * ... inside that array, there will be another array with the key == taxonomy name ...
				 * ... inside that last array, there will be all the terms of a post */
				 
				foreach($taxonomy_terms_and_posts as $term)							
					$post_taxonomy_terms[$term['object_id']][$term['taxonomy']][] = $term['term_id'];

				/**
				 * Biuld the marker object of each post 
				 * @updated in 3.5 by adding the media files to the marker object */
				 
				foreach($posts_and_coordinates as $post_id => $post_coordinates){						
					
					$post_id = str_replace('post_id_', '', $post_id);	
					
					/**
					 * Convert address to coordinates when LatLng is not provided and save them
					 * @since 5.6.3 */

					if((isset($post_coordinates[CSPM_ADDRESS_FIELD]) && !empty($post_coordinates[CSPM_ADDRESS_FIELD])) && (
						(!isset($post_coordinates[CSPM_LATITUDE_FIELD]) || empty($post_coordinates[CSPM_LATITUDE_FIELD])) || 
						(!isset($post_coordinates[CSPM_LONGITUDE_FIELD]) || empty($post_coordinates[CSPM_LONGITUDE_FIELD]))
					)){

						$address_coordinates = $this->cspm_convert_address_to_latlng(array(
							'address' => $post_coordinates[CSPM_ADDRESS_FIELD]
						));

						if($this->cspm_setting_exists('lat', $address_coordinates) && $this->cspm_setting_exists('lng', $address_coordinates)){

							$post_coordinates[CSPM_LATITUDE_FIELD] = $address_coordinates['lat'];								
							$post_coordinates[CSPM_LONGITUDE_FIELD] = $address_coordinates['lng'];

							update_post_meta($post_id, CSPM_LATITUDE_FIELD, $address_coordinates['lat']);
							update_post_meta($post_id, CSPM_LONGITUDE_FIELD, $address_coordinates['lng']);

						}				

					}
					
					/**
					 * Build the marker object */
					
					if(isset($post_coordinates[CSPM_LATITUDE_FIELD]) && isset($post_coordinates[CSPM_LONGITUDE_FIELD])){										
						
						/**
						 * If a taxonomy is not set in the $post_taxonomy_terms array ...
						 * ... it means that the post has no terms available for that taxonomy ...
						 * ... but we still need to create an empty array for that taxonomy in order ...
						 * ... to use it with faceted search */
						 
						foreach($post_taxonomies as $taxonomy_name){
							
							/**
							 * Extend the $post_taxonomy_terms array with an empty array of the not existing taxonomy */
							 
							if(!isset($post_taxonomy_terms[$post_id][$taxonomy_name]))
								$post_taxonomy_terms[$post_id][$taxonomy_name] = array(); 
						
						}											
						
						$markers_object[$post_type]['post_id_'.$post_id] = array_merge(
							array(
								'lat' => $post_coordinates[CSPM_LATITUDE_FIELD],
								'lng' => $post_coordinates[CSPM_LONGITUDE_FIELD],
								'address' => $post_coordinates[CSPM_ADDRESS_FIELD], //@since 5.4
								'post_id' => $post_id,
								'post_tax_terms' => isset($post_taxonomy_terms[$post_id]) ? $post_taxonomy_terms[$post_id] : array(),
								'is_child' => 'no',
								'child_markers' => array(),
							),
							
							/**
							 * Add post media to marker object 
							 * @since 3.5 */
							 
							$this->cspm_get_post_media($post_id, array(
								'format' => isset($post_coordinates[CSPM_FORMAT_FIELD]) ? $post_coordinates[CSPM_FORMAT_FIELD] : '',
								'marker_click' => isset($post_coordinates[CSPM_MEDIA_MARKER_CLICK_FIELD]) ? $post_coordinates[CSPM_MEDIA_MARKER_CLICK_FIELD] : '', //@since 5.5
								'gallery' => isset($post_coordinates[CSPM_GALLERY_FIELD]) ? maybe_unserialize($post_coordinates[CSPM_GALLERY_FIELD]) : array(),
								'image_id' => isset($post_coordinates[CSPM_IMAGE_FIELD.'_id']) ? $post_coordinates[CSPM_IMAGE_FIELD.'_id'] : '',
								'audio_url' => isset($post_coordinates[CSPM_AUDIO_FIELD]) ? $post_coordinates[CSPM_AUDIO_FIELD] : '',
								'embed_url' => isset($post_coordinates[CSPM_EMBED_FIELD]) ? $post_coordinates[CSPM_EMBED_FIELD] : '',
							))
						);																 
			
						$i++;
						
						/**
						 * Sencondary latLng */
						 
						if(isset($post_coordinates[CSPM_SECONDARY_LAT_LNG_FIELD]) && !empty($post_coordinates[CSPM_SECONDARY_LAT_LNG_FIELD])){
							
							$child_markers = array();
							
							$lats_lngs = explode(']', $post_coordinates[CSPM_SECONDARY_LAT_LNG_FIELD]);	
									
							foreach($lats_lngs as $single_coordinate){
							
								$strip_coordinates = str_replace(array('[', ']', ' '), '', $single_coordinate);
								
								$coordinates = explode(',', $strip_coordinates);
								
								if(isset($coordinates[0]) && isset($coordinates[1]) && !empty($coordinates[0]) && !empty($coordinates[1])){
									
									$lat = $coordinates[0];
									$lng = $coordinates[1];
									
									$child_markers[] = array(
										'lat' => $lat,
										'lng' => $lng,
										'address' => '', //@since 5.4
										'post_id' => $post_id,
										'post_tax_terms' => $post_taxonomy_terms,
										'is_child' => 'yes_'.$j
									);
																													
									$lat = '';
									$lng = '';
									$j++;
								
								} 
								
								$i++;
								
							}
							
							$markers_object[$post_type]['post_id_'.$post_id]['child_markers'] = $child_markers;
						
						}								
																																				
					}
					
				}
				
			}
										
			return $markers_object;
			
		}
		

		/**
		 * This will add our template page (WP 4.7+ Compatible)
		 *
		 * @since 4.6
		 */
		function cspm_add_new_templates($posts_templates) {
			
			$posts_templates = array_merge($posts_templates, $this->templates);
			
			return $posts_templates;
			
		}
		

		/**
		 * This will add our template to the pages cache in order to trick WordPress ...
		 * ... into thinking the template file exists where it doens't really exist.
		 *
		 * @since 4.6		 
		 */
		function cspm_register_custom_templates($atts){
		
			/**
			 * Create the key used for the themes cache */
			 
			$cache_key = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());
		
			/**
			 * Retrieve the cache list. 
			 * If it doesn't exist, or it's empty prepare an array */
			 
			$templates = wp_get_theme()->get_page_templates();
			
			if(empty($templates)){
				$templates = array();
			} 
		
			/**
			 * New cache, therefore remove the old one */
			 
			wp_cache_delete($cache_key , 'themes');
		
			/**
			 * Now add our template to the list of templates by merging our templates
			 * with the existing templates array from the cache. */
			 
			$templates = array_merge($templates, $this->templates);
		
			/**
			 * Add the modified cache to allow WordPress to pick it up for listing available templates */
			 
			wp_cache_add($cache_key, $templates, 'themes', 1800);
		
			return $atts;
		
		} 


		/**
		 * Checks if the template is assigned to the page
		 *
		 * @since 4.6		  
		 */
		function cspm_view_custom_templates($template){
			
			global $post;
		
			/**
			 * Return template if post is empty */
			 
			if(!$post)
				return $template;
		
			/**
			 * Return default template if we don't have a custom one defined */
			 
			if(!isset($this->templates[get_post_meta($post->ID, '_wp_page_template', true)])){
				return $template;
			} 
		
			$file = $this->plugin_path . get_post_meta($post->ID, '_wp_page_template', true);
		
			if(file_exists($file)){
				return $file;
			}else echo $file;
		
			return $template;
		
		}
		

		/**
		 * This will get the ID of a page by the page template it uses
		 *
		 * @since 1.0
		 */
		function cspm_get_page_by_template_name($atts = array()){
			
			$defaults = array(
				'template_name' => '',
				'number' => 1, // The number of pages to get
			);
			
			extract( wp_parse_args( $atts, $defaults ) );
			
			$page_id = '';
			
			if(!empty($template_name)){
				
				global $wp_rewrite;
				
				$pages = get_pages(array(
					'meta_key' => '_wp_page_template',
					'meta_value' => $template_name,
					'number' => $number,
					'post_type' => 'page',
					'post_status' => 'publish',
				));
		
				if(count($pages) > 0)	
					foreach($pages as $page)
						$page_id = $page->ID;
			
			}
			
			return $page_id;
					
		}
		
		
		/**
		 * This will geocode an address and return the latLng coordinates
		 *
		 * @since 5.6.3
		 */
		function cspm_convert_address_to_latlng($atts = array()){
			
			$defaults = array(
				'address' => '',
				'api_key' => $this->api_key,
				'map_language' => $this->map_language,
			);
			
			extract( wp_parse_args( $atts, $defaults ) );
						
			$coordinates = array('lat' => '', 'lng' => '');
			
			if(empty($address) || empty($api_key) || empty($map_language))
				return $coordinates;
			
			$encoded_address = urlencode_deep(trim(str_replace(' ', '+', esc_attr($address))));								
			$geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s&language=%s';
			$geocode_formatted_url = file_get_contents(sprintf($geocode_url, $encoded_address, esc_attr($api_key), esc_attr($map_language)));			

			$geocode_output= json_decode($geocode_formatted_url);

			if($geocode_output->status == 'OK'){

				$coordinates['lat'] = $geocode_output->results[0]->geometry->location->lat;
				$coordinates['lng'] = $geocode_output->results[0]->geometry->location->lng;

			}
            
			return $coordinates;
			
		}
		
	}
	
}

if(class_exists('CSProgressMap')){
	$CSProgressMap = new CSProgressMap();
	$CSProgressMap->cspm_hooks();
}
