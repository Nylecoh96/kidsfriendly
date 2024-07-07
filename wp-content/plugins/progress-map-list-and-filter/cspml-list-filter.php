<?php
 
if(!defined('ABSPATH')){
    exit; // Exit if accessed directly
}

if( !class_exists( 'CspmListFilter' ) ){
	
	class CspmListFilter{
		
		private static $_this;	
		
		private $plugin_path;		
		private $plugin_url;
		
		public $plugin_settings = array();
		public $map_settings = array();
		
		public $metafield_prefix; //@since 2.0
		
		public $post_type = '';
		public $map_type = 'normal_map'; //@since 2.3 | Whethear it's a "search map" or a "normal map"
		public $map_object_id;
		
		/**
		 * List & Filter extension */
		
		public $list_ext = ''; //@since 3.5.1
				
		/**
		 * Layout settings */
		
		public $cspml_list_layout = 'vertical'; // Possible values are, "vertical", "horizontal-left", "horizontal-right"
		public $cspml_map_height = '400';
		public $cspml_list_height = '';
		public $cspml_filter_height = '';
		public $cspml_map_cols = '4'; //@since 2.6
		public $cspml_filter_cols = '3'; //@since 3.4.7
		
		/**
		 * Options Bar Settings */
		 
		public $cspml_show_options_bar = 'yes';
		
		/**
		 * View Options Settings */
		 
		public $cspml_show_view_options = 'yes';
		public $cspml_default_view_option = 'list';
		public $cspml_grid_cols = 'cols3';
		
		/**
		 * Listings Count Settings */
		 
		public $cspml_show_posts_count = 'yes';
		public $cspml_posts_count_clause = '[posts_count] Result(s)';
		
		/**
		 * Content Settings */
		 
		public $cspml_listings_title = '';
		public $cspml_listings_details = '[l=700]';
		public $cspml_click_on_title = 'no'; //@since 1.1
		public $cspml_click_on_img = 'no'; //@since 1.1	
		public $cspml_external_link = 'same_window'; //@since 1.1
		public $cspml_scroll_to_list_item = 'yes'; //@since 2.2
		public $cspml_list_items_featured_img = 'show'; //@since 2.6
		public $cspml_ellipses = 'yes'; //@since 2.6
		
		/**
		 * "Marker Position" Button Settings */
		 
		public $cspml_show_fire_pinpoint_btn = 'yes';
        public $cspml_fire_pinpoint_on_hover = 'no';
		
		/**
		 * Sort Listings Settings */
		 
		public $cspml_show_sort_option = 'yes';
		public $cspml_sort_options = array(
			'default|Default|init',
			'data-date-created|Date (Newest first)|desc',
			'data-date-created|Date (Oldest first)|asc',
			'data-title|Title A to Z|asc',
			'data-title|Title Z to A|desc'
		);
		public $cspml_custom_sort_options = '';
		public $cspml_sort_options_order = array(); //@since 2.1
		
		/**
		 * Pagination Settings */
		 
		public $cspml_posts_per_page = '';
		public $cspml_pagination_position = 'bottom'; // Possible values are, "top", "bottom", "both"
		public $cspml_pagination_align = 'center'; // Possible values are, "left", "right", "center"
		public $cspml_prev_page_text = '&laquo;';
		public $cspml_next_page_text = '&raquo;';
		public $cspml_show_all = 'false';
		
		/**
		 * Listing's Filter Form Settings */
		 
		public $cspml_faceted_search_option = 'no';
		public $cspml_faceted_search_position = 'left';
		public $cspml_faceted_search_display_option = 'show'; //@since 2.0
		public $cspml_filter_btns_position = 'bottom'; // @since 1.2 / Possible values are, "top", "bottom", "both"
		
		public $cspml_taxonomies = array();
		public $cspml_taxonomy_relation_param = 'AND';
		
		public $cspml_custom_fields = array();
		public $cspml_custom_field_relation_param = 'AND';
		
		public $cspml_filter_btn_text = 'Filter';
		
		public $cspml_date_filter_option = 'no'; //@since 2.1
		public $cspml_date_filter_parameters = ''; //@since 2.1
		public $cspml_filter_fields_order = array(); //@since 2.1
		public $cspml_date_filter_display_status = 'open'; //@since 2.3
		
		public $cspml_keyword_filter_option = 'no'; //@since 2.2
		public $cspml_keyword_filter_label = 'Keyword'; //@since 2.2
		public $cspml_keyword_filter_placeholder = 'Keyword'; //@since 2.2
		public $cspml_keyword_display_status = 'open'; //@since 2.3
		public $cspml_keyword_filter_description = ''; //@since 2.4

		/**
		 * Map Filter Form Settings */
		 
		public $cspml_mfs_faceted_search_option = 'no';
		
		/**
		 * Custom settings */
		
		//public $cspml_exclude_from_field_options = array(); //@edited 3.7.2
	
		function __construct($atts = array()){

			if (!class_exists('CSProgressMap'))
				return; 
				
			$CSProgressMap = CSProgressMap::this();
			
			if (!class_exists('CspmMainMap') && !class_exists('ProgressMapList'))
				return; 

			extract( wp_parse_args( $atts, array(
				'init' => false, 
				'plugin_settings' => array(),
				'map_settings' => array(), 
				'metafield_prefix' => '',
				'extensions' => array(), //@since 3.5.1
			)));

			self::$_this = $this;    
			
			$ProgressMapList = ProgressMapList::this();   
			
			$this->plugin_path = $ProgressMapList->cspml_plugin_path;
			$this->plugin_url = $ProgressMapList->cspml_plugin_url;
			
			$this->metafield_prefix = $metafield_prefix;
				
			/**
			 * Get plugin settings */
			 
			$this->plugin_settings = $plugin_settings;
			
			if(!$init){
				
				/**
				 * Get all map settings */
				 
				$this->map_settings = $map_settings;

				/**
				 * Get this map type
				 * @since 2.3 */
				
				$this->map_type = $this->cspml_get_map_option('map_type', 'normal_map');
				
				/**
				 * [@map_object_id] | The ID of the map
				 * @since 2.0 */								
				 
				$this->map_object_id = isset($this->map_settings['map_object_id']) ? $this->map_settings['map_object_id'] : '';
				
				/**
				 * List & Filter extension
				 * @since 3.5.1 */
				
				$this->list_ext = str_replace(array('on', 'off'), array('yes', 'no'), $this->cspml_get_map_option('list_ext', 'off')); //@since 3.5.1
				
				/**
				 * List Layout Settings */
			
				$this->cspml_list_layout = $this->cspml_get_map_option('list_layout', 'vertical');
				$this->cspml_map_height = $this->cspml_get_map_option('map_height', '400');
				$this->cspml_list_height = $this->cspml_get_map_option('list_height', '');
				$this->cspml_filter_height = $this->cspml_get_map_option('filter_height', '');
				$this->cspml_map_cols = $this->cspml_get_map_option('map_cols', '4');
				$this->cspml_filter_cols = $this->cspml_get_map_option('filter_cols', '3'); //@since 3.4.7
	
				/**
				 * Options Bar Settings */
		
				$this->cspml_show_options_bar = $this->cspml_get_map_option('show_options_bar', 'yes');
				$this->cspml_show_view_options = $this->cspml_get_map_option('show_view_options', 'yes');
				$this->cspml_default_view_option = $this->cspml_get_map_option('default_view_option', 'list');
				$this->cspml_grid_cols = $this->cspml_get_map_option('grid_cols', 'cols3');
				$this->cspml_show_posts_count = $this->cspml_get_map_option('cslf_show_posts_count', 'yes');
				$this->cspml_posts_count_clause = $this->cspml_get_map_option('cslf_posts_count_clause', '[posts_count] Result(s)');
				$this->cspml_sort_options_order = unserialize($this->cspml_get_map_option('sort_options_order', serialize(array('default', 'custom')))); //@since 2.1
				
				/**
				 * List Items settings */
		
				$this->cspml_listings_title = $this->cspml_get_map_option('listings_title', '');
				$this->cspml_listings_details = $this->cspml_get_map_option('listings_details', '[l=700]');
				$this->cspml_show_fire_pinpoint_btn = $this->cspml_get_map_option('show_fire_pinpoint_btn', 'yes');
				$this->cspml_fire_pinpoint_on_hover = $this->cspml_get_map_option('fire_pinpoint_on_hover', 'no'); //@since 3.6
                $this->cspml_click_on_title = $this->cspml_get_map_option('cslf_click_on_title', 'yes'); //@since 1.1
				$this->cspml_click_on_img = $this->cspml_get_map_option('cslf_click_on_img', 'yes'); //@since 1.1
				$this->cspml_external_link = $this->cspml_get_map_option('cslf_external_link', 'same_window'); //@since 1.1
		 		$this->cspml_scroll_to_list_item = $this->cspml_get_map_option('scroll_to_list_item', 'yes'); //@since 2.2
				$this->cspml_list_items_featured_img = $this->cspml_get_map_option('list_items_featured_img', 'show'); //@since 2.6			
				$this->cspml_ellipses = $this->cspml_get_map_option('cslf_ellipses', 'yes'); //@since 2.6
				
				/**
				 * Sort listings settings */
				
				$this->cspml_show_sort_option = $this->cspml_get_map_option('show_sort_option', 'yes');
				$this->cspml_sort_options = unserialize($this->cspml_get_map_option('sort_options', serialize(array())));
				$this->cspml_custom_sort_options = unserialize($this->cspml_get_map_option('custom_sort_options', serialize(array())));
		
				/**
				 * Pagination Settings */
				
				$this->cspml_posts_per_page = $this->cspml_get_map_option('posts_per_page', get_option('posts_per_page'));
				$this->cspml_pagination_position = $this->cspml_get_map_option('pagination_position', 'bottom');
				$this->cspml_pagination_align = $this->cspml_get_map_option('pagination_align', 'center');
				$this->cspml_prev_page_text = $this->cspml_get_map_option('prev_page_text', '&laquo;');
				$this->cspml_next_page_text = $this->cspml_get_map_option('next_page_text', '&raquo;');
				$this->cspml_show_all = $this->cspml_get_map_option('show_all', 'false');
		
				/**
				 * Filter serch settings section */
				
				$this->cspml_faceted_search_option = $this->cspml_get_map_option('cslf_faceted_search_option', 'no');
				$this->cspml_faceted_search_position = $this->cspml_get_map_option('faceted_search_position', 'left');
				$this->cspml_taxonomies = unserialize($this->cspml_get_map_option('cslf_taxonomies', serialize(array())));
				$this->cspml_taxonomy_relation_param = $this->cspml_get_map_option('cslf_taxonomy_relation_param', 'AND');
				$this->cspml_custom_fields = unserialize($this->cspml_get_map_option('cslf_custom_fields', serialize(array())));
				$this->cspml_custom_field_relation_param = $this->cspml_get_map_option('cslf_custom_field_relation_param', 'AND');
				$this->cspml_filter_btn_text = $this->cspml_get_map_option('cslf_filter_btn_text', esc_html__('Filter'));
				$this->cspml_filter_btns_position = $this->cspml_get_map_option('filter_btns_position', 'bottom'); //@since 1.2
				$this->cspml_faceted_search_display_option = $this->cspml_get_map_option('faceted_search_display_option', 'show'); //@since 2.0
				$this->cspml_date_filter_option = $this->cspml_get_map_option('date_filter_option', 'no'); //@since 2.1
				$this->cspml_date_filter_parameters = unserialize($this->cspml_get_map_option('date_filter_param', serialize(array()))); //@since 2.1
				$this->cspml_filter_fields_order = unserialize($this->cspml_get_map_option('filter_fields_order', serialize(array('keyword', 'date_filter', 'taxonomies', 'custom_fields')))); //@since 2.1
				$this->cspml_keyword_filter_option = $this->cspml_get_map_option('keyword_filter_option', 'no'); //@since 2.2
				$this->cspml_keyword_filter_label = $this->cspml_get_map_option('keyword_filter_label', 'Keyword'); //@since 2.2
				$this->cspml_keyword_filter_placeholder = $this->cspml_get_map_option('keyword_filter_placeholder', 'Keyword'); //@since 2.2
				$this->cspml_keyword_display_status = $this->cspml_get_map_option('keyword_display_status', 'open'); //@since 2.3
				$this->cspml_keyword_filter_description = $this->cspml_get_map_option('keyword_filter_description', ''); //@since 2.4
				$this->cspml_date_filter_display_status = $this->cspml_get_map_option('date_filter_display_status', 'open'); //@since 2.3				

				if(!is_admin()){							
					
					/**
					 * These are all the hooks that communicates with Progress Map by displaying the listings and the filters
					 * Note: Always call these hooks in the custructor 'cause we need to execute them any time ...
					 * ... a shortcode is called. In each shortcode, these hooks will load different settings ...
					 * ... that a map needs. */
					 
					add_filter('cspm_main_map_output_atts', array($this, 'cspml_add_main_map_output_atts'), 10, 2);
					add_filter('cspm_main_map_output', array($this, 'cspml_listings_map'), 10, 2);
					add_filter('cspm_main_map_post_ids', array($this, 'cspml_overide_post_ids_array'), 10, 3);
					add_action('cspm_before_main_map_query', array($this, 'cspml_clear_session_on_page_load'), 10, 2);
					
				}
											
			}

		}
		
		
		static function this() {
			
			return self::$_this;
		
		}
		
		
		function cspml_hooks(){
				
			/**
			 * Ajax function */
			 
			add_action('wp_ajax_cspml_listings_html', array($this, 'cspml_listings_html'));
			add_action('wp_ajax_nopriv_cspml_listings_html', array($this, 'cspml_listings_html'));
			
			add_action('wp_ajax_cspml_faceted_search_query', array($this, 'cspml_faceted_search_query'));
			add_action('wp_ajax_nopriv_cspml_faceted_search_query', array($this, 'cspml_faceted_search_query'));

			if(is_admin()){
				
				/**
				 * Add plugin menu */
				 
				add_action( 'admin_menu', array($this, 'cspml_admin_menu'), 100 );
			
			}else{

				/**
				 * Use this hook available in "Progress Map" shortcode to load our tools classes 
				 * @since 3.5.1 */
				 
				add_action('cspm_before_execute_main_map_function', function($atts){
					
					$this->list_ext = str_replace(array('on', 'off'), array('yes', 'no'), $this->cspml_setting_exists($this->metafield_prefix . '_list_ext', $atts['map_settings'], 'off')); //@since 3.5.1
					
					if((isset($atts['extensions'], $atts['extensions']['list_ext']) && $atts['extensions']['list_ext'] == 'no') 
						|| $this->list_ext == 'no'
					){
						return;
					} //Stop Loading the extension from "PM" shortcode attribute "List_ext" | @since 3.5.1
		
					if(!class_exists('CspmMainMap'))
						return; 
						
					$CspmMainMap = CspmMainMap::this();

					/**
					 * force to hide "Progress Map" post count & carousel */
					 
					$CspmMainMap->show_posts_count = 'no';
					$CspmMainMap->show_carousel = 'false';
					
					$CspmListFilter = new CspmListFilter($atts);
						
				}, 11, 1);
									
			}
			
		}
		
    	
		/**
		 * Add this plugin to the admin menu
		 *
		 * @since 1.0
		 */
		function cspml_admin_menu(){	
			
			if (!class_exists('CSProgressMap'))
				return;
				
			//add_submenu_page( 'cspm_default_settings', esc_html__( 'List & Filter', 'cspml' ), esc_html__( 'List & Filter', 'cspml' ), 'manage_options', 'cspml', array($this, 'cspml_settings_page') );
	
		}
		
		
		/**
		 * This will display the plugin settings form 
		 *
		 * @since 1.0
		 * @updated 2.0 [settings removed]
		 */
		function cspml_settings_page(){
								
			echo '<div class="update-nag">';
				 
				echo '<strong style="color:red;">'.esc_html__( 'Since the version 2.0, the "List & Filter" settings page has been removed. To create/edit your list & filter, follow these instructions:', 'cspml' ).'</strong>';
				
				echo '<br /><br />';
				
				echo '<strong>'.esc_html__( 'Create a new map:', 'cspml' ).'</strong>';
				
					echo '<br /><br />';
					
					echo esc_html__( '1. Click on the menu "Progress Map => Add new map".', 'cspml' );
					
					echo '<br /><br />';
					
					echo esc_html__( '2. Select a post type in the metabox "(Custom) Post Type", then, Click on the button "Publish".', 'cspml' );
					
					echo '<br /><br />';
					
					echo esc_html__( '3. A new metabox titled "List & Filter Settings" will be available. Use the fields on that metabox to create your list & filter.', 'cspml' );
					
					echo '<br /><br />';
				
				echo '<strong>'.esc_html__( 'Edit an existing map:', 'cspml' ).'</strong>';
				
					echo '<br /><br />';
					
					echo esc_html__( '1. Click on the menu "Progress Map => All maps".', 'cspml' );
				
					echo '<br /><br />';
					
					echo esc_html__( '2. Edit a map on the list of all maps.', 'cspml' );
				
					echo '<br /><br />';
					
					echo esc_html__( '3. Use the fields on the metabox "List & Filter Settings" to create/edit your list & filter.', 'cspml' );
					
			echo '</div>';
			
		}
		
		
		/**
		 * Get an option from a map options
		 *
		 * @since 2.0 
		 */
		function cspml_get_map_option($option_id, $default_value = ''){
			
			/**
			 * We'll check if the default settings can be found in the array containing the "(shared) plugin settings".
			 * If found, we'll use it. If not found, we'll use the one in [@default_value] instead. */
			 
			$default = $this->cspml_setting_exists($option_id, $this->plugin_settings, $default_value);
			
			return $this->cspml_setting_exists($this->metafield_prefix.'_'.$option_id, $this->map_settings, $default);
			
		}
		
		
		/**
		 * Get the value of a setting
		 *
		 * @since 2.0 
		 */
		function cspml_get_plugin_setting($setting_id, $default_value = ''){
			
			return $this->cspml_setting_exists($setting_id, $this->plugin_settings, $default_value);			
			
		}
		
		    
		/**
		 * Check if array_key_exists and if empty() doesn't return false
		 * Replace the empty value with the default value if available 
		 * @empty() return false when the value is (null, 0, "0", "", 0.0, false, array())
		 *
		 * @since 1.0
		 */
		function cspml_setting_exists($key, $array, $default = ''){
			
			$array_value = isset($array[$key]) ? $array[$key] : $default;
			
			$setting_value = empty($array_value) ? $default : $array_value;
			
			return $setting_value;
			
		}
	
			
		/**
		 * Enqueue CSS files
		 * 
		 * @since 1.0
		 */
		function cspml_enqueue_styles(){

			if (!class_exists('CspmMainMap'))
				return; 
			
			$CspmMainMap = CspmMainMap::this();
			
			do_action('cspml_before_enqueue_style');	
				
			/**
			 * Custom Scroll bar */
			 
			wp_enqueue_style('jquery-mcustomscrollbar');
			
			/**
			 * Selectize
			 * @since 1.0 */
			 
			wp_enqueue_style('jquery-selectize');
			wp_enqueue_style('jquery-selectize-skin');
						
			/**
			 * ion-Check Radio
			 * @since 1.0 */
			 
			wp_enqueue_style('jquery-ion-check-radio');								
			wp_enqueue_style('jquery-ion-check-radio-skin');

			/**
			 * Range Slider
			 * Note: File located in "Progress Map" directory
			 * @since 1.0 */
							
			wp_enqueue_style('jquery-ion-rangeslider');
				
			/**
			 * Spinner
			 * @since 1.0 */
			 
			wp_enqueue_style('jquery-input-spinner');
			
			/**
			 * Hover
			 * @since 1.0 */
			 
			wp_enqueue_style('hover');
			
			/**
			 * Datepicker 
			 * @since 2.1 */
			
			wp_enqueue_style('jquery-fengyuan-datepicker');
			
			/**
			 * Date Range Picker 
			 * @since 2.4 */
			
			wp_enqueue_style('jquery-liu-daterangepicker');
						
			/**
			 * iziModal (Registred inside "Progress Map")
			 * @since 2.5 */
			 
			wp_enqueue_style('jquery-izimodal');							
			
			/**
			 * Progress Map List Style
			 * @since 1.0 */
						
			wp_enqueue_style('cspml-style');
		
			/**
			 * Custom CSS
			 * @since 2.8 */
			
			$CSProgressMap = CSProgressMap::this();
			
			$custom_css  = $CSProgressMap->cspm_main_colors();

			$custom_css .= 'div[class^=cspml_pagination_] ul li span.current{background-color: '.$this->cspml_get_plugin_setting('main_hex_color').' !important;}';
			$custom_css .= 'div[class^=cspml_pagination_] ul li:hover a{background-color: '.$this->cspml_get_plugin_setting('main_hex_color').' !important;}';
			
			$custom_css .= '.icr-item, .icr-label:hover .icr-item, .icr-label.checked .icr-item, .icr-label.checked:hover .icr-item:after, .icr-label.checked:hover .icr-item:before{border-color: '.$this->cspml_get_plugin_setting('main_hex_color').' !important;}';
			$custom_css .= '.icr-label.checked .type_radio:after, .icr-label.checked .type_checkbox:after, .icr-label.checked .type_checkbox:before{background-color: '.$this->cspml_get_plugin_setting('main_hex_color').' !important;}';
			
			/**
			 * Custom CSS for the range slider
			 * Note: Should be rendered only when the map search form is not used! 
			 * @since 3.5 */ 
						
			if($CspmMainMap->search_form_option != 'true'){ 
				
				$custom_css .= '.irs-from,.irs-to,.irs-single{background: '.$this->cspml_get_plugin_setting('main_hex_color').' !important;}';
				$custom_css .= '.irs-from:after,.irs-to:after,.irs-single:after,.irs--round .irs-from:before, .irs--round .irs-to:before, .irs--round .irs-single:before{border-top-color: '.$this->cspml_get_plugin_setting('main_hex_color').' !important;}'; //@edited 5.6
				$custom_css .= '.irs--round .irs-from, .irs--round .irs-to, .irs--round .irs-single{border-radius:2px !important;}'; //@since 5.6
				$custom_css .= '.irs--round .irs-handle{cursor: grab; border: 2px solid '.$this->cspml_get_plugin_setting('main_hex_color').' !important;}'; //@since 5.6
				$custom_css .= '.irs--round .irs-handle.state_hover, .irs--round .irs-handle:hover{border: 2px solid '.$this->cspml_get_plugin_setting('main_hex_hover_color').' !important; background-color:#ffffff !important;}'; //@since 5.6
				$custom_css .= '.irs--round .irs-bar{background-color: '.$this->cspml_get_plugin_setting('main_hex_color').' !important;}'; //@since 5.6
				$custom_css .= '.irs--round .irs-min, .irs--round .irs-max, .irs--round .irs-from, .irs--round .irs-to, .irs--round .irs-single{font-size: 11px !important;}'; //@since 5.6					
			
			}
			
			/**
			 * SVG custom colors
			 * @since 2.8 */
			
			$custom_css .= '#cspml_listings_container svg.cspm_svg_colored *{fill: '.$this->cspml_get_plugin_setting('main_hex_color').' !important;}';
			$custom_css .= '#cspml_listings_container svg.cspm_svg_white *{fill: #ffffff !important;}';
			
			/**
			 * List Items hover shadow
			 * @since 3.5.1 */
			
			$custom_css .= 'div.cspml_item:hover, div.cspml_active_item{
				box-shadow: '.str_replace(',.97)', ',.1)', $this->cspml_get_plugin_setting('main_rgb_color')).' 0 1px 4px -1px, inset 0 -3px 0 0 '.str_replace(',.97)', ',.1)', $this->cspml_get_plugin_setting('main_rgb_color')).' !important;
			}';
			
			$CspmMainMap->cspm_add_inline_style($custom_css);			
			
			do_action('cspml_after_enqueue_style');
						
		}	
		
		
		/**
		 * Enqueue JS files
		 *
		 * @since 1.0
		 */
		function cspml_enqueue_scripts(){		

			if (!class_exists('CspmMainMap'))
				return; 
			
			$CspmMainMap = CspmMainMap::this();
			
			do_action('cspml_before_enqueue_script');
		
			/**
			 * Custom Scroll bar */
			 
			wp_enqueue_script('jquery-mcustomscrollbar');
			
			/**
			 * ScrollTo jQuery Plugin
			 * Registered in the plugin "Progress Map */
			
			wp_enqueue_script('jquery-scrollto');
												
			/**
			 * Selectize
			 * @since 1.0 */
			 
			wp_enqueue_script('jquery-selectize');
		
			/**
			 * ion-Check Radio
			 * @since 1.0 */
			 
			wp_enqueue_script('jquery-ion-check-radio');

			/**
			 * Range Slider
			 * Note: File located in "Progress Map" directory
			 * @since 1.0 */
							
			wp_enqueue_script('jquery-ion-rangeslider');
				
			/**
			 * Spinner
			 * @since 1.0 */
			 
			wp_enqueue_script('jquery-input-spinner');
			
			/**
			 * Datepicker 
			 * @since 2.1 */
			
			wp_enqueue_script('jquery-fengyuan-datepicker');
			
			/**
			 * Moment.js
			 * @since 2.4 */
			
			wp_enqueue_script('moment-js');
			
			/**
			 * Date Range Picker 
			 * @since 2.4 */
			
			wp_enqueue_script('jquery-liu-daterangepicker');
						
			/**
			 * iziModal (Registred inside "Progress Map")
			 * @since 2.5 */
			 
			wp_enqueue_script('jquery-izimodal');

			/**
			 * jQuery Mask
			 * @since 1.0  */
			
			wp_enqueue_script('jquery-mask');
			
			/**
			 * Price format
			 * @since 3.2  */
			
			wp_enqueue_script('jquery-priceformat');
			
			/**
			 * Progress Map List Custom Functions
			 * @since 1.0 */
		 
			wp_enqueue_script('cspml-script');
			
			do_action('cspml_after_enqueue_script');
						
		}
			
		
		/**
		 * Add custom attributes to the array of attributes sent to the main map output (carousel)
		 *
		 * @since 1.0
		 */
		function cspml_add_main_map_output_atts($val, $atts){
			
			if(!is_array($atts))
				return;
			
			foreach($atts as $key => $value)
				$val[$key] = $value;
			
			return $val;
			
		}
		
		
		/**
		 * If there was no pagination passed, clean the filtering session	
		 * In other words, we clean all session when the page is refreshed ...
		 * ... because everything works with ajax, and the only case when the page is reloaded ...		
		 * ... is when using/ the pagination.
		 * We use the URL attribute "paginate" to detect the pagination
		 *
		 * @since 1.0
		 * @updated 3.5.1
		 */
		function cspml_clear_session_on_page_load($map_id, $atts = array()){
						
			extract( wp_parse_args( $atts, array() ) );
	
			if($this->list_ext == 'yes' && !isset($_GET['paginate'])){ //@edited 3.5.1
	
				unset($_SESSION['cspml_posts_in_search'][$map_id]);	
				unset($_SESSION['cspml_sort_args'][$map_id]);				
				unset($_SESSION['cspml_listings_filter_post_ids'][$map_id]);
				
			}
			
		}

		
		/**
		 * Used in the filter hook that overides the post_ids array when there's a faceted search request
		 * The faceted search request can be detected by the presence of the $_SESSION['cspml_listings_filter_post_ids'][$map_id]
		 *
		 * @since 1.0
		 * @updated 3.5.1
		 */
		function cspml_overide_post_ids_array($value, $map_id, $atts = array()){
			
			extract( wp_parse_args( $atts, array() ) );
					
			if($this->list_ext == 'yes' && isset($_SESSION['cspml_listings_filter_post_ids'][$map_id])){ //@edited 3.5.1
				return $_SESSION['cspml_listings_filter_post_ids'][$map_id];
			}else return $value;
				
		}
		
		
		function cspml_listings_map($output, $atts = array()){

			if (!class_exists('CspmMainMap'))
				return; 
			
			$CspmMainMap = CspmMainMap::this();
			
			extract( wp_parse_args( $atts, array() ) );

			if($this->list_ext == 'yes'){ //@edited 3.5.1
			
				/**
				 * Load styles & scripts from "Progress Map" */
				
				$CspmMainMap->cspm_enqueue_styles();
				$CspmMainMap->cspm_enqueue_scripts();
				
				/**
				 * Load styles & scripts of this extension */
				 
				$this->cspml_enqueue_styles();
				$this->cspml_enqueue_scripts();                        
			
				$list_layout = $this->cspml_list_layout;
				$map_height = $this->cspml_map_height.'px';
				$list_filter = $this->cspml_faceted_search_option;
				$map_filter = $this->cspml_mfs_faceted_search_option;

				/**
				 * Define fixed/fullwidth layout height and width */
				
				$layout_style = ($CspmMainMap->layout_type == 'fixed') 
					? 'width:'.$CspmMainMap->layout_fixed_width.'px; height:auto;'
					: 'width:100%; height:auto;'; //@edited 3.5
					
				$output = '';
				
				/**
				 * Set layout CSS classes */
				
				if(isset($list_layout) && ($list_layout == 'horizontal-left' || $list_layout == 'horizontal-right')){
					
					$map_position = ($list_layout == 'horizontal-right') ? ' pull-right' : '';
					
					$row_classes = ' class="cspm-row no-margin"';
					$map_container_classes = ' class="cspm-col-lg-'.$this->cspml_map_cols.' cspm-col-md-'.$this->cspml_map_cols.' cspm-col-sm-12 cspm-col-xs-12'.$map_position.'"';
					
					$list_cols = array(
						'2' => '10', 
						'3' => '9',
						'4' => '8', 
						'5' => '7', 
						'6' => '6', 
						'7' => '5', 
						'8' => '4',
						'9' => '3',
						'10' => '2',
					); //@edited 3.5
					
					$list_container_classes = ' class="cspm-col-lg-'.$list_cols[$this->cspml_map_cols].' cspm-col-md-'.$list_cols[$this->cspml_map_cols].' cspm-col-sm-12 cspm-col-xs-12"';
					$clearfix_div_classes = ' class="visible-sm visible-xs margin-top-30"';
					
				}else{
					
					$row_classes = $map_container_classes = $clearfix_div_classes = '';
					$list_container_classes = ' class="margin-top-30"';
					
				}
				
				$output .= '<div id="cspml_container" data-map-id="'.$map_id.'" '.$row_classes.'>';
				
					$output .= '<div'.$map_container_classes.'>';
				
						/**
						 * Plugin Container */
							
						$output .= '<div class="codespacing_progress_map_area cspm_linear_gradient_bg" data-map-id="'.$map_id.'" '.apply_filters('cspml_container_custom_atts', '', $map_id).' style="'.$layout_style.'">'; //@edited 3.6.3
							
							/**
							 * force to hide "Progress Map" faceted search if the listing's faceted search is active */
							 
							if($list_filter == 'yes' || $map_filter == 'yes')
								$faceted_search = 'no';
																			
							/**
							 * Interface elements
							 * @updated 1.1 */
							 
							$output .= $CspmMainMap->cspm_map_interface_element(array(
								'map_id' => $map_id,		
								'carousel' => 'no',
								'faceted_search' => $faceted_search,
								'search_form' => $search_form,
								'faceted_search_tax_slug' => $faceted_search_tax_slug,
								'faceted_search_tax_terms' => $faceted_search_tax_terms,
								'geo' => $geo,
								'infobox_type' => $infobox_type, //@since 1.1
								'extensions' => array('list_ext' => $this->list_ext), //@since 3.5.1
							));
										
							/**
							 * Map */
							 
							$output .= '<div class="position-relative">';
										
								$output .= '<div id="codespacing_progress_map_div_'.$map_id.'" class="cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12" style="height:'.$map_height.'"></div>';
								
								if(isset($list_layout) && $list_layout == 'vertical'){
									
									$output .= '<div class="clearfix"></div>';
									
									$txt_resize_the_map = apply_filters('__resize_the_map', esc_html__('Resize the map', 'cspml'));
									
									$output .= '<div class="cspml_resize_map cspm_bg_hex_hover cspm_border_top_radius cspm_border_shadow" data-map-id="'.$map_id.'" data-map-height="'.$map_height.'" title="'.$txt_resize_the_map.'">';
										$output .= '<img src="'.apply_filters('cspml_collapse_map_img', $this->plugin_url.'img/svg/collapse.svg', str_replace('map', '', $map_id)).'" class="cspm_animated push" alt="'.$txt_resize_the_map.'" />';
									$output .= '</div>';
									
								}
								
							$output .= '</div>';			
						
						$output .= '</div>';	
				
					$output .= '</div>';
				
					/**
					 * HTML Listings */
	
					$attr = array();
					
					if(!empty($post_ids)) $attr['post_ids'] = esc_attr($post_ids);
					
					$attr['optional_latlng'] = (!empty($optional_latlng)) ? esc_attr($optional_latlng) : 'true';
					
					/**
					 * Posts per page */
					 
					$posts_per_page = (isset($posts_per_page) && !empty($posts_per_page)) ? $posts_per_page : $this->cspml_posts_per_page;
					
					/**
					 * Display the list */

					$output .= '<div'.$list_container_classes.'>';
					
						$output .= '<div'.$clearfix_div_classes.'></div>';
						
						$output .= $this->cspml_listings_html(
							array_merge(
								array(				
									'map_id' => $map_id,
								),
								$attr
							)
						);
				
					$output .= '</div>';
				
				$output .= '</div>';
			
			}
			
			/**
			 * Remove the filter "cspm_main_map_output" after the current "list & filter" been displayed.
			 *
			 * @since 2.1
			 * ----------
			 * A fix to the following issue:
			 *
			 * When displaying multiple "lists & filters" on the same page,
			 * the hook "cspm_main_map_output" merge the the previous list(s) data to the next list data!
			 * This creats a JS conflicts in the Datepicker used in the feature "Filter by Posts publish date"!
			 * Removing the filter "cspm_main_map_output" after displaying a list solved the problem!
			 * If another "list & filter" should be displayed on the same page, the hook "cspm_main_map_output" ...
			 * ... will be called again from the constructor and will load the new list data only!
			 */
			 					
			remove_filter('cspm_main_map_output', array($this, 'cspml_listings_map')); //@since 2.1
			
			/**
			 * Return the list & filter */
			 
			return $output;
			
		}
		
		
		/**
		 * This will display all Listings & the faceted search
		 *
		 * @since 1.0
		 * @updated 2.7
		 * @updated 3.3 [Changed "query_posts" by "WP_Query"]
		 */	
		function cspml_listings_html($atts = array()){

			if (!class_exists('CspmMainMap'))
				return; 
				
			$CspmMainMap = CspmMainMap::this();
				
			global $wp_query, $wp_rewrite, $paged;

			/**
			 * Reload map settings */
			 
			$this->map_settings = (isset($_POST['map_settings'])) ? $_POST['map_settings'] : $this->map_settings;
            unset(
                $this->map_settings[$this->metafield_prefix.'_js_style_array'],
                $this->map_settings[$this->metafield_prefix.'_custom_css']
            ); //@since 3.6 | Fix an issue where the map JS style break all map settings when trying to read them using the function "maybe_unserialize()"				
		            			
			$map_id = (isset($_POST['map_id'])) ? esc_attr($_POST['map_id']) : 'initial';
		
			/**
			 * Get The ID Of The Page Where The Shortcode was Executed */
			 
			$shortcode_page_id = isset($_POST['shortcode_page_id']) ? esc_attr($_POST['shortcode_page_id']) : $wp_query->get_queried_object_id();
			
			$template_tax_query = ''; //@since 2.7
			
			if(is_tax() || is_category() || is_tag()){

				/**
				 * Get the current taxonomy and terms
				 * @since 2.7 */
				
				$queried_object = get_queried_object();
				
				$taxonomy = (isset($queried_object->taxonomy)) ? $queried_object->taxonomy : ''; // The taxonomy slug/name
				$term_id = (isset($queried_object->term_id)) ? $queried_object->term_id : ''; // The term ID
				$term_slug = (isset($queried_object->slug)) ? $queried_object->slug : ''; // The term Name
				
				$page_template = $taxonomy;
				
				$template_tax_query = $taxonomy.','.$term_slug;
				
			}elseif(is_author())
				$page_template = 'author';
			else $page_template = get_page_template_slug($shortcode_page_id);
			
			if(isset($_POST['page_template'])) $page_template = esc_attr($_POST['page_template']);
			if(isset($_POST['template_tax_query'])) $template_tax_query = esc_attr($_POST['template_tax_query']);			
									
			/**
			 * Get the link of the page where the shortcode was executed */
			
			if($page_template == 'author'){
				
				$shortcode_page_link = get_author_posts_url($shortcode_page_id);
			
			}elseif(!empty($template_tax_query)){ //@since 2.7
				
				$shortcode_page_link = (isset($_POST['shortcode_page_link']))
					? esc_attr($_POST['shortcode_page_link'])
					: get_term_link($shortcode_page_id); //@since 2.7
			
			}else $shortcode_page_link = get_permalink($shortcode_page_id);
									 									
			/**
			 * Get the page template slug used to render the listings
			 * Usefull for re-executing hooks after AJAX load */
			 
			do_action('cspml_before_listings', $shortcode_page_id, $page_template, $template_tax_query);

			/**
			 * Function atts */
			 
			extract( wp_parse_args( $atts, array(
				
				'map_id' => $map_id,		
				'post_ids' => '',
				'post_type' => $this->cspml_get_map_option('post_type'),
				'map_type' => $this->cspml_get_map_option('map_type', 'normal_map'), //@since 2.3
				'orderby' => 'post__in',
				'order' => 'ASC',
				'posts_per_page' => $this->cspml_get_map_option('posts_per_page', get_option('posts_per_page')),
				'list_filter' => $this->cspml_get_map_option('cslf_faceted_search_option'),
				'filter_position' => $this->cspml_get_map_option('faceted_search_position'),
				'filter_display_option' => $this->cspml_get_map_option('faceted_search_display_option', 'show'),
				'list_height' => $this->cspml_get_map_option('list_height'),
				'filter_height' => $this->cspml_get_map_option('filter_height'), //@since 3.5
				'default_view' => $this->cspml_get_map_option('default_view_option'),
				'paginate_position' => $this->cspml_get_map_option('pagination_position'),
				'paginate_align' => $this->cspml_get_map_option('pagination_align'),
				'show_options_bar' => $this->cspml_get_map_option('show_options_bar'),
				'show_listings' => 'true',
				'list_layout' => $this->cspml_get_map_option('list_layout', 'vertical'), //@since 2.6
				'map_cols' => $this->cspml_get_map_option('map_cols', '4'), //@since 2.6
				'optional_latlng' => '', // Wether we will display all posts event those with no Lat & Lng				
			
			)));				

			/**
			 * Clear the following sessions after the page refresh.
			 * Note: The page refresh on, Pagination, Sort Listings, Filter listings & on any other Ajax Request. ...
			 * ... This code will detect a simple page refresh (F5), that's what we want. */
			 
			if(!isset($_GET['paginate']) && !isset($_POST['sort_call']) && !isset($_POST['ajax_call']) && !isset($_POST['filter_form_call'])){
				
				unset($_SESSION['cspml_posts_in_search'][$map_id]);	
				unset($_SESSION['cspml_sort_args'][$map_id]);				
				unset($_SESSION['cspml_listings_filter_post_ids'][$map_id]);
				
			}else{				
						
				/**
				 * Recover shortcode atts missed during an AJAX request.
				 * Note: When runing an AJAX request, some shortcode atts became empty. 
				 * A workaround fix is to send them whith the AJAX request to get/use them later! */
				
				if(isset($_POST['paginate_position']))
					$paginate_position = esc_attr($_POST['paginate_position']);
					
				if(isset($_POST['paginate_align']))
					$paginate_align = esc_attr($_POST['paginate_align']);
				
				if(isset($_POST['posts_per_page']))
					$posts_per_page = esc_attr($_POST['posts_per_page']);
				
			}

			$map_id = esc_attr($map_id);
			
			/**
			 * Specify which divider to use for the pagination */
			 
			$exploded_URI = explode('?', esc_url($_SERVER['REQUEST_URI']));
			
			if(is_home() || is_front_page()) $divider = '?';
			else $divider = (count($exploded_URI) > 1) ? '&' : '?';
			
			/**
			 * This will save the current view (List or Grid) even after AJAX request */
			 
			$current_view = (isset($_POST['current_view'])) ? esc_attr($_POST['current_view']) : $default_view;
			
			/**
			 * Init empty sort_args array() */
			 
			$sort_args = array();
			
			/**
			 * Detect when a sort call was sent and return data */
			 
			if(isset($_POST['sort_call'])){
				$shortcode_page_id = esc_attr($_POST['shortcode_page_id']);
				$divider = esc_attr($_POST['divider']);			
				$post_ids = esc_attr($_POST['init_post_ids']);
				$optional_latlng = esc_attr($_POST['optional_latlng']);
			}

			/**
			 * $_POST['ajax_call'] is only used when the user filter listings OR uses the search/filter form of the map */

			if(isset($_POST['ajax_call'])){
				
				$map_id = esc_attr($_POST['map_id']);
				$post_ids = isset($_POST['post_ids']) ? $_POST['post_ids'] : '';
				$shortcode_page_id = esc_attr($_POST['shortcode_page_id']);
				$divider = esc_attr($_POST['divider']);
				$optional_latlng = esc_attr($_POST['optional_latlng']);
				
				if(isset($_POST['save_session'])){
	
					/**
					 * After filtering, we store the post_ids retreived from the map ...
					 * ... in order to use them again in the pagination, this way the pagination ...
					 * ... wont start again from the begining.
					 * After the page has been loaded from the pagination ...
					 * ... the $_POST['ajax_call'] will not exist ...
					 * ... now we check if $_SESSION['cspml_posts_in_search'][$map_id]['filtering'] equals TRUE ...
					 * ... if so, we call the post_ids stored in $_SESSION['cspml_posts_in_search'][$map_id]['post_ids'] */
					 
					$_SESSION['cspml_posts_in_search'][$map_id]['filtering'] = true;
					$_SESSION['cspml_posts_in_search'][$map_id]['post_ids'] = $post_ids;	
								
					/**
					 * Save the post_ids in a session in order to not start ...
					 * ... calling all posts when paginating or sorting by or filtering */
					 
					$_SESSION['cspml_listings_filter_post_ids'][$map_id] = $post_ids;
					
				}
				
			/**
			 * This case means that no filtering has been done from the faceted search in the map ...
			 * ... or that we've just simply start loading the script.
			 * But, This could be fired also when doing a sort call */
			 
			}else{

				/**
				 * Check if there was no filtering done before by the faceted search in the map */
				 
				if(!isset($_SESSION['cspml_posts_in_search'][$map_id]['filtering'])){
					
					/**
					 * Remove $_SESSION['cspml_posts_in_search'][$map_id]['filtering'] And laod post_ids */
					 
					unset($_SESSION['cspml_posts_in_search'][$map_id]);
						
					/**
					 * If post ids being pased from the shortcode parameter @post_ids
					 * ... OR ...
					 * If a sort call has been done and returns a list of post IDs */
					 
					if(!empty($post_ids)){
						$query_post_ids = explode(',', $post_ids);							
					}else $query_post_ids = $CspmMainMap->queried_post_ids;
					
					$post_ids = $query_post_ids;				

				/**
				 * If there was a filetring before, we call post_ids stored in the session */
				 
				}else $post_ids = $_SESSION['cspml_posts_in_search'][$map_id]['post_ids'];
	
			}
			
			/**
			 * Get the sort data (Of the "sort by" select box) after posts filtering & pagination */
			 
			if((isset($_POST['ajax_call']) || isset($_GET['paginate'])) && isset($_SESSION['cspml_sort_args'][$map_id]))
				$sort_args = $_SESSION['cspml_sort_args'][$map_id];
	
			/**
			 * Get "post_ids" when a request for the listings filter form (Of the listings's faceted search) was sent */

			if(isset($_SESSION['cspml_listings_filter_post_ids'][$map_id]))
				$post_ids = $_SESSION['cspml_listings_filter_post_ids'][$map_id];
			
			/**
			 * Set "init_post_ids" as "post_ids" when we reset the filter or the "Progress Map" search */

			if(isset($_POST['reset_list']) && isset($_POST['init_post_ids'])){

				$post_ids = explode(',', esc_attr($_POST['init_post_ids']));
				 
				$_SESSION['cspml_listings_filter_post_ids'][$map_id] = $post_ids;
				$_SESSION['cspml_posts_in_search'][$map_id]['post_ids'] = $post_ids;	
				
			}
			
			/**
			 * This will contain all the post IDs of the first page load
			 * This is usefull to know the post_ids we begins with in order to do other calculations */		

			$init_post_ids = (isset($_POST['init_post_ids'])) ? esc_attr($_POST['init_post_ids']) : implode(',', $post_ids);
			$count_init_post_ids = !empty($init_post_ids) ? count(explode(',', $init_post_ids)) : '0';

			$output = '';	

			if(esc_attr($show_listings) == 'true'){
				
				/**
				 * Note: When doing an AJAX request, the result returns this element. 
				 * We need this element only when loading the listings for the first time.
				 * To prevent this, we must detect AJAX requests and hide/show this element accordingly! */
					 
				if(!isset($_POST['ajax_call']) && !isset($_POST['sort_call']))
					$output .= '<div id="cspml_listings_container" data-map-id="'.$map_id.'" data-grid-cols="'.$this->cspml_get_map_option('grid_cols').'" data-map-type="'.$map_type.'" data-first-load="true" class="cspm-row">';
									
					/**
					 * == The options bar
					 * Note: When doing an AJAX request, the result returns this element. 
					 * We need this element only when loading the listings for the first time.
					 * To prevent this, we must detect AJAX requests and hide/show this element accordingly! */
					
					if(!isset($_POST['ajax_call']) && !isset($_POST['sort_call']) && $show_options_bar == 'yes'){
						
						/**
						 * Make sure to set the posts count to 0 on the first map load ...
						 * ... but only when it's a search map
						 * @since 2.3 */
						 
						if($map_type != 'normal_map')
							$count_init_post_ids = 0;
						
						/** 
						 * Display the options bar
						 * @updated 2.3 */
						 
						$output .= '<div class="cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12">';
							$output .= $this->cspml_options_bar(
								array(
									'map_id' => $map_id,		
									'nbr_items' => $count_init_post_ids,
									'page_id' => $shortcode_page_id,
									'current_view' => $current_view,
									'list_filter' => $list_filter,
									'filter_position' => $filter_position,
								)
							);
						$output .= '</div>';
					
					}
					
					/**
					 * Note: When doing an AJAX request, the result returns this element. 
					 * We need this element only when loading the listings for the first time.
					 * To prevent this, we must detect AJAX requests and hide/show this element accordingly! */
					 
					if(!isset($_POST['ajax_call']) && !isset($_POST['sort_call'])){
					
						$output .= '<div class="clear-both"></div>';
						
						$output .= '<div data-map-id="'.$map_id.'" class="cspml_list_and_filter_container cspm-row no-margin">'; //@edited 3.5
					
					}
					
						if(esc_attr($list_filter) == 'yes'){
							
							/**
							 * Note: When doing an AJAX request, the result returns this element. 
							 * We need this element only when loading the listings for the first time.
							 * To prevent this, we must detect AJAX requests and hide/show this element accordingly! */
						 
							if(!isset($_POST['ajax_call']) && !isset($_POST['sort_call'])){
							
								/**
								 * == The faceted search form */
								
								$pull_direction = ($filter_position == 'right') ? 'pull-right' : ''; //@edited 3.5
								$display_option = ($filter_display_option == 'hide') ? 'display:none;' : '';
					
								/**
								 * Set the filter height & style
								 * @since 3.5
								 * @edited 3.5.1 */
								
								$filter_height_style = '';
								$filter_scrollable = 'no';
														
								if(!empty($filter_height)){
									$filter_height = (strpos($filter_height, 'px') !== false) ? $filter_height : $filter_height.'px';
									$filter_height_style = 'max-height:'.$filter_height.'; overflow:auto;';
									$filter_scrollable = 'yes';
								}
								
								$filter_style_attr = ' style="'.$display_option . $filter_height_style.'"'; //@since 3.5
								
								$filter_cols_classes = 'cspm-col-lg-'.$this->cspml_filter_cols.' cspm-col-md-'.$this->cspml_filter_cols.' cspm-col-sm-12 cspm-col-xs-12'; //@since 2.6
								
								$output .= '<div class="cspml_fs_container '.$filter_cols_classes.' '.$pull_direction.'" data-map-id="'.$map_id.'" data-scrollable="'.$filter_scrollable.'" '.$filter_style_attr.'>'; //@edited 3.5
								
									$output .= $this->cspml_listings_faceted_search_form($map_id, $post_type, $post_ids, $list_filter);
								
								$output .= '</div>';
							 
							}
							
							$listings_area_cols_width = ($this->cspml_filter_cols == 12) ? 12 : (12 - $this->cspml_filter_cols); //@since 3.5
							
							$listings_area_cols_classes = 'cspm-col-lg-'.$listings_area_cols_width.' cspm-col-md-'.$listings_area_cols_width.' cspm-col-sm-12 cspm-col-xs-12'; //@edited 3.5
								
							$listings_area_cols = ($filter_display_option == 'show') ? $listings_area_cols_classes : 'cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12';
							
						}else $listings_area_cols = 'cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12';
					
						/**
						 * Set the list height & style
						 * @edited 3.5.1 */
						
						$list_style = '';
						$list_scrollable = 'no'; //@since 3.5
												
						if(!empty($list_height)){
							$list_height = (strpos($list_height, 'px') !== false) ? $list_height : $list_height.'px';
							$list_style = 'style="max-height:'.$list_height.'; overflow:auto;"';
							$list_scrollable = 'yes'; //@since 3.5
						}
																
						/**
						 * Note: When doing an AJAX request, the result returns this element. 
						 * We need this element only when loading the listings for the first time.
						 * To prevent this, we must detect AJAX requests and hide/show this element accordingly! */
						 
						if(!isset($_POST['ajax_call']) && !isset($_POST['sort_call']))
							$output .= '<div class="cspml_listing_items_container '.$listings_area_cols.'" data-map-id="'.$map_id.'" data-list-layout="'.$list_layout.'" data-map-cols="'.$map_cols.'" data-scrollable="'.$list_scrollable.'" '.$list_style.'>'; //@edited 3.5
							
							/**
							 * == The loading spinner
							 * Note: When doing an AJAX request, the result returns this element. 
							 * We need this element only when loading the listings for the first time.
							 * To prevent this, we must detect AJAX requests and hide/show this element accordingly! */
							 
							if(!isset($_POST['ajax_call']) && !isset($_POST['sort_call'])){
									
								$output .= '<div id="cspml_loading_container_'.$map_id.'" class="cspml_loading_container cspm_border_shadow cspm_border_radius">';
									$output .= '<span>'.apply_filters('__loading', esc_html__('Loading', 'cspml')).'</span>';
									$output .= '<span class="wrapper"><span class="cssload-loader cspm_bg_after_hex cspm_bg_before_hex"></span></span>';
								$output .= '</div>';
								
							}
							
							/**
							 * == Listings
							 * Note: When doing an AJAX request, the result returns this element. 
							 * We need this element only when loading the listings for the first time.
							 * To prevent this, we must detect AJAX requests and hide/show this element accordingly! */
							 
							if(!isset($_POST['ajax_call']) && !isset($_POST['sort_call']))
								$output .= '<div class="cspml_listings_area_'.$map_id.' cspm-row" data-map-id="'.$map_id.'" data-paginate-position="'.$paginate_position.'" data-paginate-align="'.$paginate_align.'" data-posts-per-page="'.$posts_per_page.'" data-map-type="'.$map_type.'">'; 
							
								/** 
								 * Post IDs loaded for the first time */
								 
								$output .= '<input type="hidden" name="init_post_ids" id="init_post_ids" value="'.$init_post_ids.'" />';
								
								/**
								 * Number of posts loaded for the first time */
								 
								$output .= '<input type="hidden" name="count_init_post_ids" id="count_init_post_ids" value="'.$count_init_post_ids.'" />';							
								
								/**
								 * Post IDs returned after filter request */
								 
								$output .= '<input type="hidden" name="post_ids" id="post_ids" value="'.implode(',', $post_ids).'" />';
								
								/**
								 * The ID of the page that executes the shortcode */
								 
								$output .= '<input type="hidden" name="shortcode_page_id" id="shortcode_page_id" value="'.$shortcode_page_id.'" />';
								
								/**
								 * The name of the page template that contains the shortcode.
								 * Useful to hide/show or customize elements by page template. */
								 
								$output .= '<input type="hidden" name="page_template" id="page_template" value="'.$page_template.'" />';
								
								/**
								 * The taxonomy of the page template.
								 * Used for archive and taxonomy page templates. */
								 
								$output .= '<input type="hidden" name="template_tax_query" id="template_tax_query" value="'.$template_tax_query.'" />';
								
								/**
								 * The devider to use befor pagination (? or &) */
								 
								$output .= '<input type="hidden" name="divider" id="divider" value="'.$divider.'" />';
								
								/**
								 * Whether to show or hide listings */
								 
								$output .= '<input type="hidden" name="show_listings" id="show_listings" value="'.$show_listings.'" />';
								
								/**
								 * Whether to show or hide listings that doesn't have LatLng coordinates */
								 
								$output .= '<input type="hidden" name="optional_latlng" id="optional_latlng" value="'.$optional_latlng.'" />';
								
								/**
								 * The shortcode page link | @since 2.7 */
								 
								$output .= '<input type="hidden" name="shortcode_page_link" id="shortcode_page_link" value="'.$shortcode_page_link.'" />';

								if(!empty($post_ids) && !empty($init_post_ids) && $show_listings == 'true'){
									
									if ( get_query_var('paged') ) $paged = get_query_var('paged');
									elseif ( get_query_var('page') ) $paged = get_query_var('page');
									else $paged = 1;
									
									$total_pages = ceil(count($post_ids)/$posts_per_page); 
					 
									/**
									 * Check to see if we are using rewrite rules */
									
									$rewrite = $wp_rewrite->wp_rewrite_rules();
										$format = (!empty($rewrite)) ? 'page/%#%/?paginate=true' : $divider.'page=%#%&paginate=true';										
									 									
									$show_all = $this->cspml_get_map_option('show_all');
									
									$show_all_pages = ($show_all == 'false') ? false : true;
									
									/**
									 * Get the sort args when a sort request was sent
									 * @updated 2.0 [added @data_type] */
									 
									if(isset($_POST['sort_call']) && isset($_POST['data_sort']) && isset($_POST['data_order']) && isset($_POST['data_type'])){
										$sort_args = $this->cspml_set_posts_order(esc_attr($_POST['data_sort']), esc_attr($_POST['data_order']), esc_attr($_POST['data_type']));
										$_SESSION['cspml_sort_args'][$map_id] = $sort_args;
									} 
		
									$query_args = array(
										'post_type' => $post_type, 
										'post__in' => $post_ids, 
										'posts_per_page' => $posts_per_page, 
										'post_status' => unserialize(stripslashes($this->cspml_get_map_option('items_status', serialize('publish')))), //$post_status,
										'paged' => $paged,
										'orderby' => $orderby,
										'order' => $order,
									);
	
									/**
									 * Remove the default query order atts and replace them with the new atts ...
									 * ... of the listings "sort by" feature */
									 
									if(count($sort_args) > 0){
										unset($query_args['orderby']);
										unset($query_args['meta_key']);
										unset($query_args['order']);	
									}
									
									/**
									 * Make the query args and the sort data one array */
									 
									$combined_query_args = $query_args + $sort_args;														
									
									/**
									 * The listings */
			 
									$cspml_query_posts = new WP_Query($combined_query_args);
									
									/**
									 * First, check the map type. 
									 * Make sure to display locations in the following cases:
									 * - When it's a normal map.
									 * - When it's a search map but only after sending a filter/search request. Hide them when reset!
									 * - When using pagination.
									 *
									 * @since 2.3 */

								 	if(
										$map_type == 'normal_map' 
										|| ($map_type != 'normal_map' && isset($_POST['request_type']) && $_POST['request_type'] != 'reset') 
										|| isset($_GET['paginate'])
									){ // @since 2.3 
									 
										if ( $cspml_query_posts->have_posts() ) {
	
											$base = !is_wp_error($shortcode_page_link) ? $shortcode_page_link.'%_%' : '%_%'; // the base URL, including query arg
											
											/**
											 * ==============
											 * Fixed a pagination issue when using "WP Rewrite Rule" with WPML URL Format "?lang=CODE".
											 * @since 3.4.5 */
											
											$use_with_wpml = $this->cspml_setting_exists('use_with_wpml', $this->plugin_settings, 'no');
											
											if($use_with_wpml == 'yes'){
												$wpml_url_format = apply_filters('wpml_setting', NULL, 'language_negotiation_type'); // Refers to the option "WPML => Languages => Language URL format => Language name added as a parameter"		
												$default_lang = apply_filters('wpml_default_language', NULL); // Default WPML Language						
												$current_lang = apply_filters('wpml_current_language', NULL); // Current Page Language
												if(!empty($rewrite) && $wpml_url_format == 3 && $default_lang != $current_lang){ 	
													$base = substr($base, 0, strpos($base, '/?') + 1);
													$base = $base . 'page/%#%/?lang=' . $current_lang . '&paginate=true';
												}
											}
											
											/**
											 * End WPML + WP Rewrite Rule issue
											 * ============== */
	
											$pagination = paginate_links( array(
												'base' => $base, //@edited 3.4.5
												'format' => $format, // this defines the query parameter that will be used, in this case "p"
												'prev_text' => esc_html__($this->cspml_get_map_option('prev_page_text'), 'cspml'), // text for previous page
												'next_text' => esc_html__($this->cspml_get_map_option('next_page_text'), 'cspml'), // text for next page
												'total' => $total_pages, // the total number of pages we have
												'current' => $paged, // the current page
												'show_all' => $show_all_pages,
												'end_size' => 1,
												'mid_size' => 2,
												'type' => 'array',
											));										
											
											/**
											 * Pagination */
											 
											if($paginate_position == 'top' || $paginate_position == 'both') 
												$output .= $this->cspml_pagination($map_id, $pagination, $paginate_align);								
											
											$i = 1;
											
											$output .= apply_filters('cspml_before_listing_loop', '');
											
											while ( $cspml_query_posts->have_posts() ) : $cspml_query_posts->the_post(); //while ( have_posts() ) : the_post(); 
											
												$post_id = get_the_ID();
												
												/**
												 * Get lat and lng data */
												 
												$lat = get_post_meta($post_id, CSPM_LATITUDE_FIELD, true);
												$lng = get_post_meta($post_id, CSPM_LONGITUDE_FIELD, true);
												
												$secondary_latlng = get_post_meta($post_id, CSPM_SECONDARY_LAT_LNG_FIELD, true);
											
												/**
												 * Show items only if lat and lng are not empty.
												 * Note: To display all items even those without Lat&Lng, we use the attribute @optional_latlng. */
												
												if(!empty($lat) && !empty($lng) || esc_attr($optional_latlng) == 'true'){
													
													$marker_img_array = apply_filters('cspml_post_thumb', wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'cspml-listings-thumbnail' ), $post_id);
													
													$marker_img = isset($marker_img_array[0]) ? $marker_img_array[0] : apply_filters('cspml_empty_thumb_img', $this->plugin_url.'img/thumbnail.jpg', str_replace('map', '', $map_id));																			
	
													$item_title = $this->cspml_items_title(
														$post_id, 
														$this->cspml_get_map_option('listings_title'), 
														true
													);
													
													$item_details = $this->cspml_items_details(
														$post_id, 
														$this->cspml_get_map_option('listings_details')
													);
													
													$click_on_img = $this->cspml_get_map_option('cslf_click_on_img');
													
													$the_permalink = ($click_on_img == 'yes') ? $this->cspml_get_permalink($post_id) : ''; //@edited 3.4.4
																			
													if($current_view == "list"){
														
														$list_view_atts = array(
															'index' => $i,
															'map_id' => $map_id,
															'post_id' => $post_id,
															'lat' => $lat,
															'lng' => $lng,
															'marker_img' => $marker_img,
															'item_title' => $item_title,
															'item_details' => apply_filters('cspml_list_item_description', $item_details, $post_id, 'list', esc_attr($list_filter), $map_id, $lat.'_'.$lng),
															'the_permalink' => $the_permalink,
														);
														
														$output .= $this->cspml_list_view_output($list_view_atts);
													
													}else{
																										
														$grid_view_atts = array(
															'index' => $i,
															'map_id' => $map_id,
															'post_id' => $post_id,
															'lat' => $lat,
															'lng' => $lng,
															'marker_img' => $marker_img,
															'item_title' => $item_title,
															'item_details' => apply_filters('cspml_grid_item_description', $item_details, $post_id, 'grid', esc_attr($list_filter), $map_id, $lat.'_'.$lng),
															'the_permalink' => $the_permalink,
														);
														 
														$output .= $this->cspml_grid_view_output($grid_view_atts);
													
													}
													
													$i++;
																					
												}
												
											endwhile; 
											
											$output .= apply_filters('cspml_after_listing_loop', '');
											
											/**
											 * Pagination */
											 
											if($paginate_position == 'bottom' || $paginate_position == 'both') 
												$output .= $this->cspml_pagination($map_id, $pagination, $paginate_align);							
	
										}else{
								
											$output .= '<div class="cspml_no_results cspm_txt_hex">'.apply_filters('cspml_no_results_msg', esc_html__('We couldn\'t find any results!', 'cspml')).'</div>';
										
										}
										
									}else{
										
										/**
										 * @since 2.3 */
										 
										$output .= '<div class="cspml_no_results cspm_txt_hex">'.apply_filters('cspml_start_search_msg', esc_html__('Find your locations', 'cspml')).'</div>';
										
									}
									
									wp_reset_postdata();
									
								}else $output .= '<div class="cspml_no_results cspm_txt_hex">'.apply_filters('cspml_no_results_msg', esc_html__('We couldn\'t find any results!', 'cspml')).'</div>';
						
							/**
							 * Note: When doing an AJAX request, the result returns this element. 
							 * We need this element only when loading the listings for the first time.
							 * To prevent this, we must detect AJAX requests and hide/show this element accordingly! */
							 
							if(!isset($_POST['ajax_call']) && !isset($_POST['sort_call']))
								$output .= '</div><div class="clear-both"></div>';
						
						/**
						 * Note: When doing an AJAX request, the result returns this element. 
						 * We need this element only when loading the listings for the first time.
						 * To prevent this, we must detect AJAX requests and hide/show this element accordingly! */
						 
						if(!isset($_POST['ajax_call']) && !isset($_POST['sort_call']))
							$output .= '</div>';
						
					/**
					 * Note: When doing an AJAX request, the result returns this element. 
					 * We need this element only when loading the listings for the first time.
					 * To prevent this, we must detect AJAX requests and hide/show this element accordingly! */
					 
					if(!isset($_POST['ajax_call']) && !isset($_POST['sort_call']))
						$output .= '</div>';
					
				/**
				 * Note: When doing an AJAX request, the result returns this element. 
				 * We need this element only when loading the listings for the first time.
				 * To prevent this, we must detect AJAX requests and hide/show this element accordingly! */
				 
				if(!isset($_POST['ajax_call']) && !isset($_POST['sort_call']))
					$output .= '</div><div class="clear-both"></div>';
			
			/**
			 * In case we don't want to display the listings. And in case we want to display a fullscreen map with the filter,
			 * we'll need this hidden inputs for the filter query */
			 	
			}else{
				
				$output .= '<div class="cspml_listings_area_'.$map_id.'" data-map-id="'.$map_id.'" data-paginate-position="'.$paginate_position.'" data-paginate-align="'.$paginate_align.'" data-map-type="'.$map_type.'">'; 					
								
					/** 
					 * Post IDs loaded for the first time */
					 
					$output .= '<input type="hidden" name="init_post_ids" id="init_post_ids" value="'.$init_post_ids.'" />';
					
					/**
					 * Number of posts loaded for the first time */
					 
					$output .= '<input type="hidden" name="count_init_post_ids" id="count_init_post_ids" value="'.$count_init_post_ids.'" />';							
					
					/**
					 * Post IDs returned after filter request */
					 
					$output .= '<input type="hidden" name="post_ids" id="post_ids" value="'.implode(',', $post_ids).'" />';
					
					/**
					 * The ID of the page that executes the shortcode */
					 
					$output .= '<input type="hidden" name="shortcode_page_id" id="shortcode_page_id" value="'.$shortcode_page_id.'" />';
					
					/**
					 * The page template name of the page that contains the shortcode.
					 * Useful to hide/show or customize elements by page template. */
					 
					$output .= '<input type="hidden" name="page_template" id="page_template" value="'.$page_template.'" />';
					
					/**
					 * The taxonomy of the page template.
					 * Used for archive and taxonomy page templates. */
					 
					$output .= '<input type="hidden" name="template_tax_query" id="template_tax_query" value="'.$template_tax_query.'" />';
					
					/**
					 * The devider to use befor pagination (? or &) */
					 
					$output .= '<input type="hidden" name="divider" id="divider" value="'.$divider.'" />';
					
					/**
					 * Whether to show or hide listings */
					 
					$output .= '<input type="hidden" name="show_listings" id="show_listings" value="'.$show_listings.'" />';
								
					/**
					 * Whether to show or hide listings that doesn't have LatLng coordinates */
					 
					$output .= '<input type="hidden" name="optional_latlng" id="optional_latlng" value="'.$optional_latlng.'" />';
								
					/**
					 * The shortcode page link | @since 2.7 */
					 
					$output .= '<input type="hidden" name="shortcode_page_link" id="shortcode_page_link" value="'.$shortcode_page_link.'" />';

				$output .= '</div>';
						
			}
					
			if(isset($_POST['ajax_call']) || isset($_POST['sort_call']) || isset($_POST['filter_form_call'])) die($output);
			else return $output;
			
		}
		
		
		/**
		 * The list view output
		 *
		 * @since 1.0
		 * @updated 1.1 | 3.5
		 */
		function cspml_list_view_output($atts = array()){
			
			$defaults = array(
				'index' => '',
				'map_id' => '',
				'post_id' => '',
				'lat' => '',
				'lng' => '',
				'marker_img' => '',
				'item_title' => '',
				'item_details' => '',
				'the_permalink' => '',
			);	
			
			extract(wp_parse_args($atts, $defaults));
			
			$this->cspml_show_fire_pinpoint_btn = $this->cspml_get_map_option('show_fire_pinpoint_btn', 'yes');
			$this->cspml_fire_pinpoint_on_hover = $this->cspml_get_map_option('fire_pinpoint_on_hover', 'no'); //@since 3.6
            $this->cspml_external_link = $this->cspml_get_map_option('cslf_external_link', 'same_window');
			$this->cspml_list_items_featured_img = $this->cspml_get_map_option('list_items_featured_img', 'show'); //@since 2.6
			$this->cspml_scroll_to_list_item = $this->cspml_get_map_option('scroll_to_list_item', 'yes'); //@since 3.4.2
	
			$output = '';
			
			$output .= apply_filters('cspml_custom_listing_item', '', array(
				'post_id' => $post_id, 
				'map_id' => $map_id, 
				'view' => 'listview', 
				'coordinates' => $lat.'_'.$lng, 
				'permalink' => $the_permalink, 
				'marker_img' => $marker_img,
				'item_title' => stripslashes_deep($item_title),
				'item_details' => stripslashes_deep($item_details),				
				'scroll_to_item' => $this->cspml_scroll_to_list_item,
				'show_pinpoint' => $this->cspml_show_fire_pinpoint_btn,
				'fire_pinpoint_on_hover' => $this->cspml_fire_pinpoint_on_hover,
                'show_image' => $this->cspml_list_items_featured_img,
				'external_link' => $this->cspml_external_link,
			)); //@since 3.5
			
			if(empty($output)){
			     
                $data_coords = (!empty($lat) && !empty($lng)) ? $lat.'_'.$lng : ''; //@since 3.6
                
				$output .= '<div id="'.$map_id.'_listing_item_'.$post_id.'" 
                    data-coords="'.$data_coords.'" 
                    data-hover-fire="'.$this->cspml_fire_pinpoint_on_hover.'" 
                    data-view="list" 
                    data-map-id="'.$map_id.'" 
                    data-post-id="'.$post_id.'" 
                    class="cspml_item_holder cspm-row cspm_animated fadeIn" 
                    data-attr-scrollto="'.$this->cspml_scroll_to_list_item.'">'; //@edited 3.6
					
					$output .= '<div id="list_view_holder_'.$map_id.'" class="list_view_holder cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12">';				
						
						/**
						 * A filter to display custom HTML/code before the item's HTML */
						
						$output .= apply_filters('cspml_before_listing_item', '', $post_id, $map_id, 'listview', $lat.'_'.$lng, $the_permalink);
						
						$output .= '<div class="clear-both"></div>';	
								
						$output .= '<div class="'.apply_filters('cspml_item_holder_class', 'cspml_item', $post_id).' cspm-row row-no-margin cspm_border_shadow cspm_border_radius">';
							
							$show_pinpoint_btn = ($this->cspml_show_fire_pinpoint_btn == 'yes' && !empty($lat) && !empty($lng)) ? 'yes' : 'no';
							
							if($this->cspml_list_items_featured_img == 'show'){ //@since 2.6
		
								$output .= '<div data-map-id="'.$map_id.'" class="cspml_thumb_container '.apply_filters('cspml_thumb_container_class', 'cspm-col-lg-5 cspm-col-md-5 cspm-col-sm-5 cspm-col-xs-12', 'listview').' no-padding">';
							
									/**
									 * A filter to display custom HTML/code before the item's thumb */
									 
									$output .= apply_filters('cspml_before_listing_thumb', '', $post_id, $map_id, 'listview', $lat.'_'.$lng, $the_permalink);
									
									$link_overlay_corner_class = $show_pinpoint_btn == 'yes' ? 'cspm_remove_bg_corner' : '';
									
									/**
									 * Item's permalink */
									
									if(!empty($the_permalink)){
		
										$target = ($this->cspml_external_link == "same_window") ? '' : ' target="_blank"';
										$link_class = ($this->cspml_external_link == 'popup') ? 'class="cspm_popup_single_post"' : ''; //@since 2.5
										
										$only_item_title = $this->cspml_items_title(
											$post_id, 
											$this->cspml_get_map_option('listings_title'), 
											false
										); //@since 2.5
																							
										$output .= '<a href="'.$the_permalink.'" title="'.$only_item_title.'"'.$target.' '.$link_class.'><div class="cspml_item_link_overlay '.$link_overlay_corner_class.' cspm_animated fadeIn">';
											$output .= '<img alt="" src="'.apply_filters('cspml_item_link_img', $this->plugin_url.'img/svg/link.svg', str_replace('map', '', $map_id)).'" title="'.apply_filters('__visit', esc_html__('Visit', 'cspml')).'" class="cspml_item_link" />';
										$output .= '</div></a>';
									
									}
		
									/**
									 * Item's thumb */
									
									$output .= apply_filters('cspml_item_thumb_html', '<img alt="" src="'.$marker_img.'" class="thumb img-responsive" />', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink); //@edited 3.4.4								
									
									/**
									 * Item's Pinpoint */
									 
									if($show_pinpoint_btn == 'yes'){
								        
                                        $data_coords = (!empty($lat) && !empty($lng)) ? $lat.'_'.$lng : ''; //@since 3.6
                                        
										$output .= '<div class="cspml_item_pinpoint_overlay cspml_fire_pinpoint cspm_bg_rgb_hover" data-map-id="'.$map_id.'" data-coords="'.$data_coords.'" data-post-id="'.$post_id.'">'; //@edited 3.6
											$output .= '<img alt="" src="'.apply_filters('cspml_item_pinpoint_img', $this->plugin_url.'img/svg/pinpoint.svg', str_replace('map', '', $map_id)).'" title="'.apply_filters('__show_position', esc_html__('Show position on the map', 'cspml')).'" class="cspml_item_pinpoint" />';
										$output .= '</div>';
									
									}
									
									/**
									 * A filter to display custom HTML/code after the item's thumb */
									 
									$output .= apply_filters('cspml_after_listing_thumb', '', $post_id, $map_id, 'listview', $lat.'_'.$lng, $the_permalink);
									
								$output .= '</div>';
							
							}
							
							/**
							 * Title & Content */
							
							$content_row_classes = ($this->cspml_list_items_featured_img == 'show') 
								? 'cspm-col-lg-7 cspm-col-md-7 cspm-col-sm-7 cspm-col-xs-12' 
								: 'cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12'; //@since 2.6
									
							$output .= '<div data-map-id="'.$map_id.'" class="'.apply_filters('cspml_details_container_class', 'cspml_details_container '.$content_row_classes, 'listview').'" data-view="list">';	
																
								/**
								 * Item's Pinpoint
								 * @since 2.6 */
						
								if($this->cspml_list_items_featured_img == 'hide' && $show_pinpoint_btn == 'yes'){
									
                                    $data_coords = (!empty($lat) && !empty($lng)) ? $lat.'_'.$lng : ''; //@since 3.6
                                    
									$output .= '<div class="cspml_item_pinpoint_overlay cspml_fire_pinpoint cspm_bg_rgb_hover" data-map-id="'.$map_id.'" data-coords="'.$data_coords.'" data-post-id="'.$post_id.'">'; //@edited 3.6
										$output .= '<img alt="" src="'.apply_filters('cspml_item_pinpoint_img', $this->plugin_url.'img/svg/pinpoint.svg', str_replace('map', '', $map_id)).'" title="'.apply_filters('__show_position', esc_html__('Show position on the map', 'cspml')).'" class="cspml_item_pinpoint" />';
									$output .= '</div>';
										
								}
												
								/**
								 * A filter to display custom HTML/code before the item's title container */
								 
								$output .= apply_filters('cspml_before_title_container', '', $post_id, $map_id, 'listview', $lat.'_'.$lng, $the_permalink);
	
								$output .= '<div data-map-id="'.$map_id.'" class="cspml_details_title cspm_txt_hex_hover '.apply_filters('cspml_details_title_extra_class', '').' cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12">';
						
									/**
									 * A filter to display custom HTML/code before the item's title */
									 
									$output .= apply_filters('cspml_before_listing_title', '', $post_id, $map_id, 'listview', $lat.'_'.$lng, $the_permalink);
									
									/** 
									 * Item's title */
									 
									$output .= stripslashes_deep($item_title);
						
									/**
									 * A filter to display custom HTML/code before the item's title */
									 
									$output .= apply_filters('cspml_after_listing_title', '', $post_id, $map_id, 'listview', $lat.'_'.$lng, $the_permalink);
									
								$output .= '</div>';
								
								/**
								 * Item's excerpt/content */
								 
								$output .= '<div class="cspml_details_content '.apply_filters('cspml_details_content_extra_class', '').' cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12">';
									
									$output .= '<hr class="no-margin-top margin-bottom-15" />';
									
									/**
									 * A filter to display custom HTML/code before the item's title */
									 
									$output .= apply_filters('cspml_before_listing_excerpt', '', $post_id, $map_id, 'listview', $lat.'_'.$lng, $the_permalink);
									
									/** 
									 * Item's excerpt/content */
									 
									$output .= stripslashes_deep($item_details);
									
									/**
									 * A filter to display custom HTML/code after the item's title */
									 
									$output .= apply_filters('cspml_after_listing_excerpt', '', $post_id, $map_id, 'listview', $lat.'_'.$lng, $the_permalink);
									
								$output .= '</div>';	
						
								/**
								 * A filter to display custom HTML/code after the item's excerpt/content container */
								 
								$output .= apply_filters('cspml_after_excerpt_container', '', $post_id, $map_id, 'listview', $lat.'_'.$lng, $the_permalink);
								
							$output .= '</div>';
								
							$output .= '<div class="clear-both"></div>';
								
						$output .= '</div>';
					
						$output .= '<div class="clear-both"></div>';
	
						/**
						 * A filter to display custom HTML/code after the item's HTML */
													
						$output .= apply_filters('cspml_after_listing_item', '', $post_id, $map_id, 'listview', $lat.'_'.$lng, $the_permalink);
				
					$output .= '</div>';				
		
				$output .= '</div>';						
			
			}
			
			return apply_filters('cspml_list_view_item', $output, $post_id, $map_id, $lat.'_'.$lng, $the_permalink);						
																
		}
		
		
		/**
		 * The grid view output
		 *
		 * @since 1.0
		 * @updated 1.1 | 3.5
		 */
		function cspml_grid_view_output($atts = array()){
			
			$defaults = array(
				'index' => '',
				'map_id' => '',
				'post_id' => '',
				'lat' => '',
				'lng' => '',
				'marker_img' => '',
				'item_title' => '',
				'item_details' => '',
				'the_permalink' => '',
			);	
			
			extract(wp_parse_args($atts, $defaults));
			
			$this->cspml_show_fire_pinpoint_btn = $this->cspml_get_map_option('show_fire_pinpoint_btn', 'yes');
			$this->cspml_fire_pinpoint_on_hover = $this->cspml_get_map_option('fire_pinpoint_on_hover', 'no'); //@since 3.6
            $this->cspml_external_link = $this->cspml_get_map_option('cslf_external_link', 'same_window');
			$this->cspml_grid_cols = $this->cspml_get_map_option('grid_cols', 'cols3');
			$this->cspml_list_items_featured_img = $this->cspml_get_map_option('list_items_featured_img', 'show'); //@since 2.6
			$this->cspml_scroll_to_list_item = $this->cspml_get_map_option('scroll_to_list_item', 'yes'); //@since 3.4.2
	
			$grid_cols = $this->cspml_grid_cols;
			
			/**
			 * @cols1, displays one item per row */

			if($grid_cols == 'cols1'){
				
				$grid_classes = ' cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12';
			
			/**
			 * @cols2, displays two items per row */
			 
			}elseif($grid_cols == 'cols2'){
				
				$grid_classes = ' cspm-col-lg-6 cspm-col-md-6 cspm-col-sm-6 cspm-col-xs-12';
			
			/**
			 * @cols4, displays four items per row */
			 
			}elseif($grid_cols == 'cols4') {
				
				$grid_classes = ' cspm-col-lg-3 cspm-col-md-3 cspm-col-sm-6 cspm-col-xs-12';
			
			/**
			 * @cols6, displays six items per row */
			 
			}elseif($grid_cols == 'cols6') {
				
				$grid_classes = ' cspm-col-lg-2 cspm-col-md-2 cspm-col-sm-6 cspm-col-xs-12';
			
			/**
			 * @cols3, displays three items per row */
			 
			}else $grid_classes = ' cspm-col-lg-4 cspm-col-md-4 cspm-col-sm-6 cspm-col-xs-12';
			
			/**
			 * A filter to change the grid classes */
			 
			$grid_classes = apply_filters('cspml_listing_grid_classes', $grid_classes);
			
			/**
			 * Displaying items */
			 	
			$output = '';
			
			$output .= apply_filters('cspml_custom_listing_item', '', array(
				'post_id' => $post_id, 
				'map_id' => $map_id, 
				'view' => 'gridview', 
				'coordinates' => $lat.'_'.$lng, 
				'permalink' => $the_permalink, 
				'marker_img' => $marker_img,
				'item_title' => stripslashes_deep($item_title),
				'item_details' => stripslashes_deep($item_details),				
				'scroll_to_item' => $this->cspml_scroll_to_list_item,
				'show_pinpoint' => $this->cspml_show_fire_pinpoint_btn,
				'fire_pinpoint_on_hover' => $this->cspml_fire_pinpoint_on_hover, //@since 3.6
                'show_image' => $this->cspml_list_items_featured_img,
				'external_link' => $this->cspml_external_link,
			)); //@since 3.5
			
			if(empty($output)){
			
				$data_coords = (!empty($lat) && !empty($lng)) ? $lat.'_'.$lng : ''; //@since 3.6
                
                $output .= '<div id="'.$map_id.'_listing_item_'.$post_id.'" 
                    data-coords="'.$data_coords.'" 
                    data-hover-fire="'.$this->cspml_fire_pinpoint_on_hover.'" 
                    data-view="grid" 
                    data-map-id="'.$map_id.'" 
                    data-post-id="'.$post_id.'" 
                    class="cspml_item_holder '.$grid_classes.' cspm_animated fadeIn" 
                    data-attr-scrollto="'.$this->cspml_scroll_to_list_item.'">'; //@edited 3.6
					
					$output .= '<div id="list_view_holder_'.$map_id.'" class="list_view_holder">';				
						
						/**
						 * A filter to display custom HTML/code before the item's HTML */
						 
						$output .= apply_filters('cspml_before_listing_item', '', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink);
						
						$output .= '<div class="clear-both"></div>';	
								
						$output .= '<div class="'.apply_filters('cspml_item_holder_class', 'cspml_item', $post_id).' cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12 cspm_border_shadow cspm_border_radius">';
									
							$show_pinpoint_btn = ($this->cspml_show_fire_pinpoint_btn == 'yes' && !empty($lat) && !empty($lng)) ? 'yes' : 'no';
							
							if($this->cspml_list_items_featured_img == 'show'){ //@since 2.6
		
								$output .= '<div data-map-id="'.$map_id.'" class="cspml_thumb_container '.apply_filters('cspml_thumb_container_class', 'cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12', 'gridview').' no-padding">';
							
									/**
									 * A filter to display custom HTML/code before the item's thumb */
									 
									$output .= apply_filters('cspml_before_listing_thumb', '', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink);
									
									$link_overlay_corner_class = $show_pinpoint_btn == 'yes' ? 'cspm_remove_bg_corner' : '';
									
									/**
									 * Item's permalink */
									
									if(!empty($the_permalink)){ 								
										
										$target = ($this->cspml_external_link == "same_window") ? '' : ' target="_blank"';
										$link_class = ($this->cspml_external_link == 'popup') ? 'class="cspm_popup_single_post"' : ''; //@since 2.5
										
										$only_item_title = $this->cspml_items_title(
											$post_id, 
											$this->cspml_get_map_option('listings_title'), 
											false
										); //@since 2.5
																							
										$output .= '<a href="'.$the_permalink.'" title="'.$only_item_title.'"'.$target.' '.$link_class.'><div class="cspml_item_link_overlay '.$link_overlay_corner_class.' cspm_animated fadeIn">';
										
											$output .= '<img alt="" src="'.apply_filters('cspml_item_link_img', $this->plugin_url.'img/svg/link.svg', str_replace('map', '', $map_id)).'" title="'.apply_filters('__visit', esc_html__('Visit', 'cspml')).'" class="cspml_item_link" />';
										
										$output .= '</div></a>';
										
									}
		
									/**
									 * Item's thumb */
									 
									$output .= apply_filters('cspml_item_thumb_html', '<img alt="" src="'.$marker_img.'" class="thumb img-responsive" />', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink); //@edited 3.4.4
									
									/**
									 * Item's Pinpoint */
									 
									if($show_pinpoint_btn == 'yes'){
										 
										$output .= '<div class="cspml_item_pinpoint_overlay cspml_fire_pinpoint cspm_bg_rgb_hover" data-map-id="'.$map_id.'" data-coords="'.$data_coords.'" data-post-id="'.$post_id.'">'; //@edited 3.6
											$output .= '<img alt="" src="'.apply_filters('cspml_item_pinpoint_img', $this->plugin_url.'img/svg/pinpoint.svg', str_replace('map', '', $map_id)).'" title="'.apply_filters('__show_position', esc_html__('Show position on the map', 'cspml')).'" class="cspml_item_pinpoint" />';
										$output .= '</div>';
									
									}
									
									/**
									 * A filter to display custom HTML/code after the item's thumb */
									 
									$output .= apply_filters('cspml_after_listing_thumb', '', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink);
									
								$output .= '</div>';
								
							}
							
							/**
							 * Title & Content */
															
							$output .= '<div data-map-id="'.$map_id.'" class="'.apply_filters('cspml_details_container_class', 'cspml_details_container cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12', 'gridview').'" data-view="grid">';	
																
								/**
								 * Item's Pinpoint
								 * @since 2.6 */
						
								if($this->cspml_list_items_featured_img == 'hide' && $show_pinpoint_btn == 'yes'){
										 
									$output .= '<div class="cspml_item_pinpoint_overlay cspml_fire_pinpoint cspm_bg_rgb_hover" data-map-id="'.$map_id.'" data-coords="'.$data_coords.'" data-post-id="'.$post_id.'">'; //@edited 3.6
										$output .= '<img alt="" src="'.apply_filters('cspml_item_pinpoint_img', $this->plugin_url.'img/svg/pinpoint.svg', str_replace('map', '', $map_id)).'" title="'.apply_filters('__show_position', esc_html__('Show position on the map', 'cspml')).'" class="cspml_item_pinpoint" />';
									$output .= '</div>';
																	
								}
												
								/**
								 * A filter to display custom HTML/code before the item's title container */
								 
								$output .= apply_filters('cspml_before_title_container', '', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink);
								
								$output .= '<div data-map-id="'.$map_id.'" class="cspml_details_title cspm_txt_hex_hover '.apply_filters('cspml_details_title_extra_class', '').' cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12">';
						
									/**
									 * A filter to display custom HTML/code before the item's title */
									 
									$output .= apply_filters('cspml_before_listing_title', '', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink);
									
									/** 
									 * Item's title */
									 
									$output .= stripslashes_deep($item_title);
						
									/**
									 * A filter to display custom HTML/code after the item's title */
									 
									$output .= apply_filters('cspml_after_listing_title', '', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink);
								
								$output .= '</div>';
								
								$output .= '<div class="cspml_details_content '.apply_filters('cspml_details_content_extra_class', 'grid').' cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12">';
									
									$output .= '<hr class="no-margin-top margin-bottom-15" />';
									
									/**
									 * A filter to display custom HTML/code before the item's title */
									 
									$output .= apply_filters('cspml_before_listing_excerpt', '', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink);
									
									/** 
									 * Item's excerpt/content */
									 
									$output .= stripslashes_deep($item_details);
						
									/**
									 * A filter to display custom HTML/code after the item's title */
									 
									$output .= apply_filters('cspml_after_listing_excerpt', '', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink);
								
								$output .= '</div>';											
						
								/**
								 * A filter to display custom HTML/code after the item's excerpt/content container */
								 
								$output .= apply_filters('cspml_after_excerpt_container', '', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink);
								
							$output .= '</div>';
							
							$output .= '<div class="clear-both"></div>';
							
						$output .= '</div>';
						
						/**
						 * A filter to display custom HTML/code after the item's HTML */
						 
						$output .= apply_filters('cspml_after_listing_item', '', $post_id, $map_id, 'gridview', $lat.'_'.$lng, $the_permalink);
					
					$output .= '</div>';
										
				$output .= '</div>';
			
			}
			
			return apply_filters('cspml_grid_view_item', $output, $post_id, $map_id, $lat.'_'.$lng, $the_permalink);						
																
		}
		
		
		/**
		 * Listings pagination
		 *
		 * @since 1.0
         * @edited 3.7
		 */
		function cspml_pagination($map_id, $pagination = array(), $paginate_align = 'bottom'){
			
			if(is_array($pagination) && count($pagination) > 0){
				
				$output = '<div class="clear-both"></div>';
				
				$output .= '<div class="cspm-row no-margin">';
					
					$output .= '<div id="'.$map_id.'" class="cspml_pagination_'.$map_id.' cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12" style="text-align:'.$paginate_align.'">'; 
						
						$output .= '<ul>';
						
							$output .= '<div class="cspml_transparent_layer_'.$map_id.'"></div>';
						
							foreach($pagination as $pagination_link)
								$output .= '<li data-map-id="'.$map_id.'" class="cspm_link_hex">'.$pagination_link.'</li>';						
							
							$output .= '<div class="clear-both"></div>';
							
						$output .= '</ul>';
						
					$output .= '</div>';
				
				$output .= '</div>';
				
				$output .= '<div class="clear-both"></div>';
				
			}else $output = '';
					
			return $output;
			
		}
		
		
		/**
		 * This will display an option on top of the listings.
		 * The option bar will contain the following items:
		 * - Widget that displays the number of listings
		 * - A list that allow sorting listings
		 * - Two buttons to switch between different views (List/Grid)
		 * - Open/Close Faceted search button
		 *
		 * @since 1.0
		 * @updated 2.3
		 * @updated 2.6
		 */
		function cspml_options_bar($atts = array()){

			/**
			 * Function atts */
			 
			extract( wp_parse_args( $atts, array(
				'map_id' => '',		
				'nbr_items' => 0,
				'page_id' => '',
				'current_view' => '',
				'list_filter' => '',
				'filter_position' => '',
			)));	

			/**
			 * Options Bar */
			
			$output = '';
					
			/**
			 * A filter that allows adding extra HTML before the options bar */
			 
			$output .= apply_filters('cspml_before_options_bar', '');

			/**
			 * Options Bar Container */
		 	
			$show_posts_count = $this->cspml_show_posts_count;						
			$show_view_options = $this->cspml_show_view_options;
			$show_sort_option = $this->cspml_show_sort_option;

			$output .= '<div class="cspml_options_bar_'.$map_id.' cspm-row">';					
				
				$pull_fs_direction = ($filter_position == 'left') ? 'pull-right' : '';
				
				if($list_filter == 'yes'){
					
					$options_bar_btns_cols = ($this->cspml_filter_cols >= 4) ? 8 : (12 - $this->cspml_filter_cols); //@since 3.5
					
					if($this->cspml_list_layout != 'vertical' && $this->cspml_map_cols >= 6){ //@since 2.6
						$options_bar_cols_classes = 'cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12'; //@since 2.6
					}else $options_bar_cols_classes = 'cspm-col-lg-'.$options_bar_btns_cols.' cspm-col-md-'.$options_bar_btns_cols.' cspm-col-sm-12 cspm-col-xs-12'; //@edited 3.5
					
					$output .= '<div class="'.$pull_fs_direction.' '.$options_bar_cols_classes.'">';
					
				}
					
					if($list_filter == 'yes')
						$output .= '<div class="cspm-row">';					
																
						/**
						 * Number of listings */
							
						$post_count_classes = ($show_view_options == 'yes') ? 'cspm-col-lg-4 cspm-col-md-4 cspm-col-sm-10 cspm-col-xs-8' : 'cspm-col-lg-4 cspm-col-md-4 cspm-col-sm-12 cspm-col-xs-12';
						
						$output .= '<div class="cspml_nbr_items_container '.apply_filters('cspml_post_count_class', $post_count_classes).'">';				
						
							if($show_posts_count == "yes")
								$output .= '<div class="cspml_nbr_items cspm_border_radius cspm_border_shadow text-center">'.$this->cspml_posts_count_clause($nbr_items, $map_id).'</div>';									
					
						$output .= '</div>';
																
						/**
						 * View options */																
						
						if($show_view_options == 'yes'){
							
							$output .= '<div class="cspml_view_options_container '.apply_filters('cspml_view_options_class', 'cspm-col-lg-2 cspm-col-md-2 cspm-col-sm-2 cspm-col-xs-4').' pull-right">';
								
								$output .= '<div class="cspml_transparent_layer_'.$map_id.'"></div>';
								
								/**
								 * A filter that allows adding extra HTML before the view options */
								 
								$output .= apply_filters('cspml_before_view_options', '', $page_id);
								
								/**
								 * Switch between the grid view & the list view */
								
								$next_view = ($current_view == 'grid') ? 'list' : 'grid'; 
								
								$list_icon_display = ($current_view == 'grid') ? 'display:none;' : ''; 
								$grid_icon_display = ($current_view == 'list') ? 'display:none;' : ''; 								
								
								$output .= '<div class="'.$current_view.'_view view_option '.apply_filters('cspml_view_option_class', 'cspm-col-lg-12 cspm-col-sm-12 cspm-col-xs-12 cspm-col-md-12').' cspm_border_right_radius cspm_border_shadow" data-map-id="'.$map_id.'">';
									
									$output .= '<a class="cspml_view_icon push" data-map-id="'.$map_id.'" data-current-view="'.$current_view.'" data-next-view="'.$next_view.'" title="'.sprintf(apply_filters('__swicth_view', esc_html__('Switch to %s view', 'cspml')), $next_view).'">';
									
										$output .= '<img class="cspm_svg cspm_svg_colored cspml_list_view" src="'.apply_filters('cspml_list_view_img', $this->plugin_url.'img/svg/list.svg', str_replace('map', '', $map_id), $current_view).'" style="height:15px; width:auto; '.$list_icon_display.'" />';
										$output .= '<img class="cspm_svg cspm_svg_colored cspml_grid_view" src="'.apply_filters('cspml_grid_view_img', $this->plugin_url.'img/svg/grid.svg', str_replace('map', '', $map_id), $current_view).'" style="height:15px; width:auto; '.$grid_icon_display.'" />';
									
									$output .= '</a>';
									
								$output .= '</div>';		
								
								/**
								 * A filter that allows adding extra HTML after the view options */
																
								$output .= apply_filters('cspml_after_view_options', '', $page_id);
								
							$output .= '</div>';
						
						}
						
						if($show_posts_count == 'yes')
							$output .= '<div class="clear-both visible-sm visible-xs margin-bottom-10"></div>';
						
						/**
						 * Sort by container */

						if($show_posts_count == 'yes'){
							
							$sort_list_classes = 'cspm-col-lg-6 cspm-col-md-6 cspm-col-sm-12 cspm-col-xs-12 pull-right';
							
						}else{
							
							if($show_view_options == 'yes')
								$sort_list_classes = 'cspm-col-lg-6 cspm-col-md-6 cspm-col-sm-10 cspm-col-xs-8 pull-right';
							else $sort_list_classes = 'cspm-col-lg-6 cspm-col-md-6 cspm-col-sm-12 cspm-col-xs-12 pull-right';
							
						}
						
						$output .= '<div class="'.apply_filters('cspml_sort_and_view_options_class', $sort_list_classes).'">';					
							
							$output .= '<div class="cspml_transparent_layer_'.$map_id.'"></div>';
							
							/**
							 * Drop-down sort list */
									
							if($show_sort_option == 'yes')
								$output .= $this->cspml_build_sort_options_list($map_id);

						$output .= '</div>';
						
						$output .= '<div class="clear-both"></div>';
					
					if($list_filter == 'yes')
						$output .= '</div>';
					
				if($list_filter == 'yes')
					$output .= '</div>';
						
				/**
				 * Open/Close button for Faceted Search container */					
				 
				if($list_filter == 'yes'){
					
					$hide_filter_btn_cols = ($this->cspml_filter_cols >= 4) ? 4 : $this->cspml_filter_cols; //@since 3.5
					
					if($this->cspml_list_layout != 'vertical' && $this->cspml_map_cols >= 6){ //@since 2.6
						$toggle_filter_cols_classes = 'cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12 margin-top-10'; //@since 2.6
					}else $toggle_filter_cols_classes = 'cspm-col-lg-'.$hide_filter_btn_cols.' cspm-col-md-'.$hide_filter_btn_cols.' cspm-col-sm-12 cspm-col-xs-12'; //@edited 3.5

					$output .= '<div class="'.$toggle_filter_cols_classes.'">';
						
						$output .= '<div class="visible-sm visible-xs margin-top-10"></div>';
						
						/**
						 * Faceted Search Open/Close button */					
						
						$output .= '<div class="cspml_fs_title cspm_bg_hex cspm_border_radius cspm_border_shadow" data-map-id="'.$map_id.'">';
						
							$output .= '<img src="'.apply_filters('cspml_refine_img', $this->plugin_url.'img/svg/refine.svg', str_replace('map', '', $map_id)).'" title="'.apply_filters('__refine_search', esc_html__('Refine Search', 'cspml')).'" style="width:15px; height:auto;" />';
						
							$output .= apply_filters('__refine_search', esc_html__('Refine Search', 'cspml'));
							
							$output .= '<span class="cspml_close_fs cspm_bg_hex_hover cspm_border_right_radius cspm_border_shadow" data-map-id="'.$map_id.'" data-filter-width="'.$this->cspml_filter_cols.'">'; //@edited 3.4.7
																						
								$output .= '<img src="'.apply_filters('cspml_toggle_filter_img', $this->plugin_url.'img/svg/close.svg', str_replace('map', '', $map_id)).'" title="'.apply_filters('__close', esc_html__('Close', 'cspml')).'" style="width:15px; height:auto;" />';
							
							$output .= '</span>';
							
						$output .= '</div>';
					
					$output .= '</div>';

				}
				
				$output .= '<div class="clear-both"></div>';
					
			$output .= '</div>';
							
			/**
			 * A filter that allows adding extra HTML after the options bar */
						 
			$output .= apply_filters('cspml_after_options_bar', '');
				
			return $output;
				
		}
		
		/**
		 * Build the sort data HTML list
		 *
		 * @since 1.0
		 */
		function cspml_build_sort_options_list($map_id){
			
			$output = '';
			
			$output .= '<div data-map-id="'.$map_id.'" class="cspml_sort_list_container cspm_border_radius cspm_border_shadow">';
			
				$output .= '<img src="'.apply_filters('cspml_sort_img', $this->plugin_url.'img/svg/sort.svg', str_replace('map', '', $map_id)).'" title="'.apply_filters('__sort_listings', esc_html__('Sort Listings', 'cspml')).'" style="height:15px; width:auto;" />';
				
				$output .= '<span class="cspml_sort_val">'.apply_filters('__default', esc_html__('Default', 'cspml')).'</span>';
				
				$output .= '<span class="cspml_sort_btn cspm_border_right_radius cspm_border_shadow">';
					
					$output .= '<img src="'.apply_filters('cspml_sort_arrow_down_img', $this->plugin_url.'img/svg/arrow-down.svg', str_replace('map', '', $map_id)).'" title="'.apply_filters('__sort_listings', esc_html__('Sort Listings', 'cspml')).'" style="height:15px; width:auto;" />';
				
				$output .= '</span>';
				
			$output .= '</div>';
			
			$output .= '<ul data-map-id="'.$map_id.'" class="cspml_sort_list cspm_border_radius cspm_border_shadow">';
				
				/** 
				 * Display the sort list */
				 
				$output .= $this->cspml_build_sort_data($map_id, 'list', NULL);
				
			$output .= '</ul>';
			
			return $output;
			
		}
		
		
		/**
		 * Create the list of the sort data (Default data)
		 *
		 * @since 1.0
		 * @updated 2.0 [added @data_type]
		 */
		function cspml_build_sort_data($map_id, $type, $post_id = NULL){
			
			/**
			 * Usage Example:
			 *
			 * $sort_options_array = array(
			 		'default|Default|init' => 'Default',
					'data-date-created|Date (Newest first)|desc' => 'Date (Newest first)',
					'data-date-created|Date (Oldest first)|asc' => 'Date (Oldest first)',
					'data-title|Title A to Z|asc' => 'Title A to Z',
					'data-title|Title Z to A|desc' => 'Title Z to A'
			   ); */
	
			$output = '';

			foreach($this->cspml_sort_options_order as $display_order){
				
				if($display_order == 'default'){
						
					/**
					 * The default sort by data */
		
					foreach($this->cspml_sort_options as $sort_option){
					
						$explode_sort_option = explode("|", $sort_option);
						
						if(isset($explode_sort_option[0]) && isset($explode_sort_option[1]) && isset($explode_sort_option[2])){
							
							/**
							 * Build the list */
							 
							if($type == 'list'){
								
								$output .= '<li class="sort push" data-map-id="'.$map_id.'" ';
								
									$output .= 'data-sort="'.$explode_sort_option[0].'"';
								
									if($explode_sort_option[2] != 'init')
										$output .= ' data-order="'.$explode_sort_option[2].'"';
										
									else $output .= ' data-order="desc"';
									
									$output .= 'data-type=""'; //@since 2.0
											
								$output .= '>'.esc_html__($explode_sort_option[1], 'cspml').'</li>';
							
							/**
							 * Build the data attributes */
							 
							}elseif($type == 'data' && $post_id != NULL){
								
								/**
								 * data-title attribute/value */
								 
								if($explode_sort_option[0] == 'data-title'){
									
									$item_title = $this->cspml_items_title($post_id, $this->cspml_listings_title);
									
									$output .= ' data-title="'.stripslashes_deep($item_title).'"';
								
								/**
								 * data-date-created attribute/value */
								 
								}elseif($explode_sort_option[0] == 'data-date-created'){
									
									$output .= ' data-date-created="'.$post_id.'"';
									
								}
							
							}
							
						}
						
					}
					
				}elseif($display_order == 'custom'){
			
					/**
					 * The custom sort data (Custom fields) */
		
					if(!empty($this->cspml_custom_sort_options)){
						
						if($type == 'list'){
							
							$output .= $this->cspml_build_custom_sort_data($map_id, $type, NULL, $this->cspml_custom_sort_options);
									
						}elseif($type == 'data' && $post_id != NULL){
							
							$output .= $this->cspml_build_custom_sort_data($map_id, $type, $post_id, $this->cspml_custom_sort_options);
				
						}
							
					}
					
				}
				
			}
			
			return $output;
			
		}
		
		
		/**
		 * Create the list of the custom sort data (Custom fields)
		 *
		 * @since 1.0
		 * @updated 2.0 [added @sort_meta_type]
		 */
		function cspml_build_custom_sort_data($map_id, $type, $post_id = NULL, $sort_by_structure = ''){
			
			$output = '';

			if(!empty($sort_by_structure)){
						
				/**
				 * Get custom sort by structure */
				 
				$sort_data_attrs = (array) $sort_by_structure;
							
				/**
				 * Loop throught sort by data */
				 
				foreach($sort_data_attrs as $sort_data_value){
					
					/**
					 * Get the custom field name */
					 
					$custom_field_name = isset($sort_data_value['sort_options_name']) ? $sort_data_value['sort_options_name'] : '';
					
					if(!empty($custom_field_name)){
						
						$sort_options_name = $this->cspml_setting_exists('sort_options_name', $sort_data_value);
						$sort_meta_type = $this->cspml_setting_exists('sort_meta_type', $sort_data_value); //@since 2.0
						$sort_options_label = $this->cspml_setting_exists('sort_options_label', $sort_data_value);
						$sort_options_order = $this->cspml_setting_exists('sort_options_order', $sort_data_value, array('desc', 'asc'));
						$sort_options_desc_suffix = $this->cspml_setting_exists('sort_options_desc_suffix', $sort_data_value);
						$sort_options_asc_suffix = $this->cspml_setting_exists('sort_options_asc_suffix', $sort_data_value);
						$sort_options_visibilty = $this->cspml_setting_exists('sort_options_visibilty', $sort_data_value, 'yes');
		
						/**
						 * Loop throught the sort order and create the sort list */
						 
						foreach($sort_options_order as $sort_order){
							
							if($type == 'list'){
								
								$label_suffix = ($sort_order == 'desc') ? $sort_options_desc_suffix : $sort_options_asc_suffix;
									
								$output .= '<li class="sort push" data-map-id="'.$map_id.'" ';
								
									$output .= 'data-sort="'.$sort_options_name.'"';
								
									$output .= ' data-order="'.$sort_order.'"';
									
									$output .= ' data-type="'.$sort_meta_type.'"'; //@since 2.0
											
								$output .= '>'.esc_html__($sort_options_label, 'cspml').' '.esc_html__($label_suffix, 'cspml').'</li>';
						
							}elseif($type == 'data' && $post_id != NULL){
								
								$post_meta = $sort_options_name;
								
								$meta_data = get_post_meta($post_id, $post_meta, true);
								
								if(!empty($meta_data))
									$output .= ' data-'.$post_meta.'="'.$meta_data.'"';
								
								else $output .= ' data-'.$post_meta.'="'.$post_id.'"';
						
							}
							
						}
						
					}
									
				}			
				
			}
			
			return $output;
			
		}
		
		
		/**
		 * Preapare the sort order Query when a sort by request is sent via ajax
		 *
		 * @since 1.0
		 * @updated 2.0 [added @meta_type for custom fields]
		 */
		function cspml_set_posts_order($sort_data, $sort_order, $meta_type = 'CHAR'){
			
			$output = array();
			
			if(!empty($sort_data) && !empty($sort_order)){
				
				/**
				 * Check the value of the $sort_data
				 * If the data is a title sort request */
				 
				if($sort_data == "data-title"){
					
					$orderby = array('orderby' => 'title');
				
				/**
				 * If the data is a date sort request */
				 
				}elseif($sort_data == "data-date-created"){
					
					$orderby = array('orderby' => 'date');
				
				/**
				 * If the data is a default sort request */
				 
				}elseif($sort_data == "default"){
					
					//$orderby = array('orderby' => 'post__in');
					
					$orderby = array(
						'orderby' => $this->cspml_get_map_option('orderby_param'),
						'meta_key' => $this->cspml_get_map_option('orderby_meta_key'),
					);
					
					$sort_order = $this->cspml_get_map_option('order_param');
				
				/**
				 * If the data is a custom field request */
				 
				}else{
					
					$orderby = array(
						'meta_key' => $sort_data, 
						'orderby' => 'meta_value', 
						'meta_type' => $meta_type //@since 2.0
					);			   
					
				}
				
				$output = array_merge($orderby, array('order' => strtoupper($sort_order)));
				
			}
			
			return $output;
			
		}
		
		
		/**
		 * show the number of listings 
		 *
		 * @since 1.0
		 */
		function cspml_posts_count_clause($count, $map_id){
			
			return str_replace('[posts_count]', '<span class="cspml_the_count_'.$map_id.'">'.$count.'</span>', esc_html__($this->cspml_posts_count_clause, 'cspml'));
					
		}
		
		
		/**
		 * Parse item custom title
		 *
		 * @since 1.0
		 * @updated 1.1 | 3.4.4
		 */
		function cspml_items_title($post_id, $title, $click_title = false){
				
			/**
			 * Custom title structure */
			 
			$post_meta = esc_attr($title);
			
			$this->cspml_click_on_title = $this->cspml_get_map_option('cslf_click_on_title', 'yes');
			$this->cspml_external_link = $this->cspml_get_map_option('cslf_external_link', 'same_window');
			
			$the_permalink = ($click_title && $this->cspml_click_on_title == 'yes') ? ' href="'.$this->cspml_get_permalink($post_id).'"' : ''; //@edited 3.4.4
			$target = ($this->cspml_external_link == "same_window") ? '' : ' target="_blank"';
			
			/**
			 * Init vars */
			 
			$items_title = '';		
			$items_title_lenght = 0;
			
			/**
			 * If no custom title is set ...
			 * ... Call item original title */
			 
			if(empty($post_meta)){						
				
				$items_title = get_the_title($post_id);
				
			/**
			 * If custom title is set ... */
			 
			}else{
				
				/**
				 * ... Get post metas from custom title structure */
				 
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
					$check_title = substr($single_post_meta, 0, 5);
					
					/**
					 * Title case */
					 
					if($check_title == 'title'){
						
						$items_title .= get_the_title($post_id);
						
					/**
					 * Separator case */
					 
					}elseif($check_string === 's='){
						
						/**
						 * Add separator to title */
						 
						$items_title .= str_replace('s=', '', $single_post_meta);
					
					/**
					 * Lenght case */
					 
					}elseif($check_string === 'l='){
						
						/**
						 * Define title lenght */
						 
						$items_title_lenght = str_replace('l=', '', $single_post_meta);
					
					/**
					 * Empty space case */
					 
					}elseif($single_post_meta == '-'){
						
						/**
						 * Add space to title */
						 
						$items_title .= ' ';
					
					/**
					 * Taxonomy case */
					 
					}elseif($check_string === 'x='){
						
						/**
						 * Add taxonomy term(s) */
						 
						$taxonomy = str_replace('x=', '', $single_post_meta);
						$items_title .= implode(', ', (array) wp_get_post_terms($post_id, $taxonomy, array("fields" => "names"))); //@edited 3.7.1
						
					/**
					 * Post metas case */
					 
					}else{
						
						/**
						 * Add post meta value to title */
						 
						$items_title .= get_post_meta($post_id, $single_post_meta, true);
							
					}
					
				}
				
				/**
				 * If custom title is empty (Maybe someone will type something by error), call original title */
				 
				if(empty($items_title)) $items_title = get_the_title($post_id);
				
			}
			
			/**
			 * Show title as title lenght is defined */
		 
			/**
			 * Use the function mb_substr() instead of substr()!
			 * mb_substr() is multi-byte safe !
			 *
			 * @since 1.2 */
		 
			if($items_title_lenght > 0) $items_title = mb_substr($items_title, 0, (int)$items_title_lenght); //@edited 3.7
			
			if($click_title && $this->cspml_click_on_title == 'yes'){
				
				$link_class = ($this->cspml_external_link == 'popup') ? 'class="cspm_popup_single_post"' : ''; //@since 2.5
				
				return apply_filters('cspml_item_title', '<a'.$the_permalink.''.$target.' title="'.addslashes_gpc($items_title).'" '.$link_class.'>'.addslashes_gpc($items_title).'</a>', $post_id);
			
			}else return apply_filters('cspml_item_title', addslashes_gpc($items_title), $post_id);
			
		}
		
		
		/**
		 * Parse item custom details 
		 *
		 * @since 1.0
		 */		 
		function cspml_items_details($post_id, $details){			
			
			/**
			 * Custom details structure */
			 
			$post_meta = esc_attr($details);		
			
			/**
			 * Init vars */
			 
			$items_details = '';
			$items_title_lenght = 0;
			$items_details_lenght = 0;
					
			$ellipses = '';
			
			if($this->cspml_get_map_option('cslf_ellipses') == 'yes')
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
						$items_details .= implode(', ', (array) wp_get_post_terms($post_id, $taxonomy, array("fields" => "names"))); //@edited 3.7.1
						
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
						 * @since 1.2 */
					 
						if(isset($explode_content[1]) && $explode_content[1] > 0) $items_details .= mb_substr($the_content, 0, (int)$explode_content[1]).'&hellip;'; //@edited 3.7
									
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
				 
				$post_record = get_post($post_id, ARRAY_A);
				
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
					  * @since 1.2 */
					 
					$items_details = mb_substr($items_details, 0, (int)$items_details_lenght).$ellipses; //@edited 3.7
					
				}
				
			}
			
			return apply_filters('cspml_item_description', strip_tags(addslashes_gpc($items_details), '<strong><b><i><u><a><br>'), $post_id);
			
		}
		
		
		/**
		 * Create the "Listings faceted search form"
		 *
		 * @since 1.0
		 * @updated 2.1
         * @updated 3.7
		 */
		function cspml_listings_faceted_search_form($map_id, $post_type, $post_ids = array(), $list_filter = 'no'){
			
			if (!class_exists('CspmMainMap'))
				return; 
			
			$CspmMainMap = CspmMainMap::this();
				
			$output = '';

			if($list_filter == 'yes'){
				
				$output .= '<div class="cspml_transparent_layer_'.$map_id.'"></div>';	
				
				/**
				 * A filter that allows adding extra HTML before the filter form */
				 
				$output .= apply_filters('cspml_before_filter_form', '', $map_id);

				$output .= '<form action="" method="post" id="cspml_listings_filter_form" class="cspml_filter_form cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12 cspm_border_radius cspm_border_shadow no-padding cspm_animated fadeIn" data-map-id="'.$map_id.'">';
					
					/**
					 * Dispay filter buttons on the top area
					 * @since 1.2 */
					
					if($this->cspml_filter_btns_position == 'top' || $this->cspml_filter_btns_position == 'both'){
						
						$output .= '<div class="cspm-row row-no-margin cspm_filter_top_btns">';	
							
							$output .= '<a class="cspml_reset_lsitings_filter cspml_btn cspm_bg_hex_hover cspm_border_shadow cspm-col-lg-4 cspm-col-md-4 cspm-col-sm-3 cspm-col-xs-3" title="'.apply_filters('__reset_all_filters', esc_html__('Reset all filters', 'cspml')).'" data-request-type="reset" data-map-id="'.$map_id.'" data-display-location="listings">';
								$output .= '<img src="'.apply_filters('cspml_refresh_filter_img', $this->plugin_url.'img/svg/refresh.svg', str_replace('map', '', $map_id)).'" class="no-margin-right" />';
							$output .= '</a>';
										
							$output .= '<a class="cspml_submit_listings_filter cspml_btn cspm_bg_hex_hover cspm_border_shadow cspm-col-lg-8 cspm-col-md-8 cspm-col-sm-9 cspm-col-xs-9" title="'.apply_filters('__filter', esc_html__('Filter', 'cspml')).'" data-request-type="filter" data-map-id="'.$map_id.'" data-display-location="listings">';
								$output .= '<img src="'.apply_filters('cspml_filter_search_img', $this->plugin_url.'img/svg/filter.svg', str_replace('map', '', $map_id)).'" />';
								$output .= esc_html__($this->cspml_filter_btn_text, 'cspml');
							$output .= '</a>';
						
						$output .= '</div>';

					}
					
					$output .= '<div class="clear-both"></div>';
					
					foreach($this->cspml_filter_fields_order as $display_order){
						
						if($display_order == 'keyword'){
							
							/**
							 * Keyword text field
							 * @since 2.2 */
							
							$output .= $this->cspml_keyword_search_field($map_id, $post_type, $post_ids);
							
						}elseif($display_order == 'date_filter'){
							
							/**
							 * Display the date filter field
							 * @since 2.1 */
							
							$output .= $this->cspml_posts_publish_date_field($map_id, $post_type, $post_ids);		
					 
						}elseif($display_order == 'taxonomies'){
							
							/**
							 * Loop throught taxonomies */
							
							if(count($this->cspml_taxonomies) > 0){
								
								$rendered_taxonomies = array();
								
								foreach($this->cspml_taxonomies as $taxonomy_data){
									
									$taxonomy_data = (array) $taxonomy_data;
									
									if(count($taxonomy_data) > 0){
		
										$taxonomy_label = $this->cspml_setting_exists('taxonomy_label', $taxonomy_data);
										$taxonomy_name = $this->cspml_setting_exists('taxonomy_name', $taxonomy_data);
										$taxonomy_description = esc_html__($this->cspml_setting_exists('taxonomy_description', $taxonomy_data), 'cspml'); //@since 2.4										
										$taxonomy_visbility = $this->cspml_setting_exists('taxonomy_visibilty', $taxonomy_data, 'yes');
										$taxonomy_exists = taxonomy_exists($taxonomy_name);
															
										if(!empty($taxonomy_label) && !empty($taxonomy_name) && $taxonomy_visbility == "yes" && $taxonomy_exists && !in_array($taxonomy_name, $rendered_taxonomies)){
											
											/**
											 * Hide empty terms */
											 
											$taxonomy_hide_empty = $this->cspml_setting_exists('taxonomy_hide_empty', $taxonomy_data, "yes");
												$hide_empty = ($taxonomy_hide_empty == 'yes') ? true : false;									
												
											/**
											 * Terms to exclude */
											 
											$exclude_term_ids = str_replace(' ', '', $this->cspml_setting_exists('taxonomy_exclude_terms_'.$taxonomy_name, $taxonomy_data));
												
											/**
											 * Terms to include */
											 
											$include_term_ids = str_replace(' ', '', $this->cspml_setting_exists('taxonomy_include_terms_'.$taxonomy_name, $taxonomy_data));
												
											/**
											 * Terms orderby */
											 
											$terms_orderby = str_replace(' ', '', $this->cspml_setting_exists('taxonomy_orderby_param', $taxonomy_data));
												
											/**
											 * Terms order */
											 
											$terms_order = str_replace(' ', '', $this->cspml_setting_exists('taxonomy_order_param', $taxonomy_data));
											
											/**
											 * Get taxonomy terms
											 * @updated 1.1 */
											 
											$terms = (array) get_terms(array(
																'taxonomy' => $taxonomy_name,
																'orderby' => apply_filters('cspml_filter_terms_orderby', $terms_orderby), //@since 1.1
																'order' => apply_filters('cspml_filter_terms_order', $terms_order), //@since 1.1													 	
																'include' => $include_term_ids, 
																'exclude' => $exclude_term_ids, 
																'hide_empty' => $hide_empty
															 ));
		
											/**
											 * Show the field only when terms are more than 0 */
											 
											if(count($terms) > 0){																
												
												$search_all_option = $this->cspml_setting_exists('taxonomy_search_all_option', $taxonomy_data, 'no');
												$search_all_text = $this->cspml_setting_exists('taxonomy_search_all_text', $taxonomy_data, 'All');
												$show_count = $this->cspml_setting_exists('taxonomy_show_count', $taxonomy_data, 'no');
												$symbol = $this->cspml_setting_exists('taxonomy_symbol', $taxonomy_data);
												$symbol_position = $this->cspml_setting_exists('taxonomy_symbol_position', $taxonomy_data, 'before');
												$display_status = $this->cspml_setting_exists('taxonomy_display_status', $taxonomy_data, 'open'); //@since 2.3
														
												$field_atts = array(
													'field_name' => $taxonomy_name,
													'field_label' => esc_html__($taxonomy_label, 'cspml'),
													'field_description' => $taxonomy_description, //@since 2.4
													'field_options' => $terms,
													'symbol' => esc_html__($symbol, 'cspml'),
													'symbol_position' => $symbol_position,
													'search_all_option' => $search_all_option,
													'search_all_text' => esc_html__($search_all_text, 'cspml'),
													'show_count' => $show_count,
													'display_status' => $display_status, //@since 2.3
												);
							
												$output .= '<div class="cspml_fs_item_container cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12" data-map-id="'.$map_id.'">';
													
													$display_type = $this->cspml_setting_exists('taxonomy_display_type', $taxonomy_data);
													
													if(!empty($display_type))
														$output .= $this->cspml_faceted_search_fields($map_id, $display_type, $field_atts, $post_type, true, $post_ids); //@edited 3.7		
													
												$output .= '</div>';
												
											}
											
										}
										
										$rendered_taxonomies[] = $taxonomy_name;
			
									}
									
								}
												
							}
							
						}elseif($display_order == 'custom_fields'){
	
							/**
							 * Loop throught custom fields */
							
							if(count($this->cspml_custom_fields) > 0){
								
								$rendered_custom_fields = array();
								
								foreach($this->cspml_custom_fields as $custom_field_data){
									
									$custom_field_data = (array) $custom_field_data;
		
									if(count($custom_field_data) > 0){
										
										$custom_field_orderby = $this->cspml_setting_exists('custom_field_orderby_param', $custom_field_data, 'pm.meta_value');
										$custom_field_order = $this->cspml_setting_exists('custom_field_order_param', $custom_field_data, 'ASC');
										$custom_field_label = $this->cspml_setting_exists('custom_field_label', $custom_field_data);
										$custom_field_name = $this->cspml_setting_exists('custom_field_name', $custom_field_data);
										$custom_field_description = esc_html__($this->cspml_setting_exists('custom_field_description', $custom_field_data), 'cspml'); //@since 2.4																				
										$custom_field_visbility = $this->cspml_setting_exists('custom_field_visibilty', $custom_field_data, 'yes');
										
										if(!empty($custom_field_label) && !empty($custom_field_name) && $custom_field_visbility == "yes" && !in_array($custom_field_name, $rendered_custom_fields)){
										
											/**
											 * Get All Post Meta And Count The Number Of Each One */
											 
											$status = (is_array($CspmMainMap->post_status) && count($CspmMainMap->post_status) == 0) ? 'publish' : $CspmMainMap->post_status; //@edited 3.7.3
											 
										    $custom_field_values = $this->cspml_get_meta_values(array(
                                                'key' => $custom_field_name,
                                                'post_type' => $post_type,
                                                'status' => $status,
                                                'orderby' => $custom_field_orderby,
                                                'order' => $custom_field_order,
                                                'post_in' => $post_ids,
                                            )); // @edited 3.6
                                            
											$custom_field_options = array_unique($custom_field_values);
		
											/**
											 * Show options only when custom_field_options are more than 0 */
											 
											if(count($custom_field_options) > 0){
												
												$term_ids_array = array();
													
												$display_type = $this->cspml_setting_exists('custom_field_display_type', $custom_field_data);
												$search_all_option = $this->cspml_setting_exists('custom_field_search_all_option', $custom_field_data, 'no');
												$search_all_text = $this->cspml_setting_exists('custom_field_search_all_text', $custom_field_data, 'All');
												$show_count = $this->cspml_setting_exists('custom_field_show_count', $custom_field_data, 'no');
												$symbol = $this->cspml_setting_exists('custom_field_symbol', $custom_field_data);
												$symbol_position = $this->cspml_setting_exists('custom_field_symbol_position', $custom_field_data, 'before');
												$compare_parameter = $this->cspml_setting_exists('custom_field_compare_param', $custom_field_data);
												$display_status = $this->cspml_setting_exists('custom_field_display_status', $custom_field_data, 'open'); //@since 2.3
				
												/**
												 * Custom field Datepicker parameters 
												 * @since 2.4 */
												
												$cf_datepicker_parameters = ($display_type == 'datepicker') ? array(
													'date_format' => $this->cspml_setting_exists('cf_datepicker_date_format', $custom_field_data, 'YYYY/MM/DD'),
													'date_separator' => $this->cspml_setting_exists('cf_datepicker_date_separator', $custom_field_data, 'slash'),
													'start_date' => $this->cspml_setting_exists('cf_datepicker_start_date', $custom_field_data, ''),
													'end_date' => $this->cspml_setting_exists('cf_datepicker_end_date', $custom_field_data, ''),
													'week_start' => $this->cspml_setting_exists('cf_datepicker_week_start', $custom_field_data, 'sunday'),
												) : array();
									
												/**
												 * Custom field "Text" & "Min & Max text" parameters
												 * @since 3.2 */
												
												$cf_text_mask_parameters = (in_array($display_type, array('text', 'min_max_text'))) ? array(
													'mask' => $this->cspml_setting_exists('custom_field_mask', $custom_field_data, ''),
													'clear_mask' => $this->cspml_setting_exists('custom_field_clear_mask', $custom_field_data, 'yes'),
													'placeholder' => $this->cspml_setting_exists('custom_field_placeholder', $custom_field_data, ''),
												) : array();
				
												/**
												 * Custom field "Price" & "Min & Max price" parameters
												 * @since 3.2 */
												
												$cf_price_parameters = (in_array($display_type, array('price', 'min_max_price'))) ? array(
													'cents_separator' => $this->cspml_setting_exists('cf_cents_separator', $custom_field_data, ','),
													'thousands_separator' => $this->cspml_setting_exists('cf_thousands_separator', $custom_field_data, '.'),
													'price_limit' => $this->cspml_setting_exists('cf_price_limit', $custom_field_data, '0'),
													'cents_limit' => $this->cspml_setting_exists('cf_price_cents_limit', $custom_field_data, '0'),
													'negative_price' => $this->cspml_setting_exists('cf_allow_negative_price', $custom_field_data, 'no'),
												) : array();
									
												/**
												 * [@field_atts]
												 * @updated 2.4 [added custom field Datepicker parameters] */
												 
												$field_atts = array_merge(
													array(
														'field_name' => $custom_field_name,
														'field_label' => esc_html__($custom_field_label, 'cspml'),
														'field_description' => $custom_field_description, //@since 2.4
														'field_options' => $custom_field_options,
														'symbol' => esc_html__($symbol, 'cspml'),
														'symbol_position' => $symbol_position,
														'search_all_option' => $search_all_option,
														'search_all_text' => esc_html__($search_all_text, 'cspml'),
														'show_count' => $show_count,
														'compare_parameter' => $compare_parameter,
														'display_status' => $display_status, //@since 2.3
                                                        'field_options_order' => $custom_field_order, //@since 3.6
													),
													$cf_datepicker_parameters, // CF. Datepicker atts | @since 2.4
													$cf_text_mask_parameters, // CF. "Text" & "Min & Max text" atts | @since 3.2
													$cf_price_parameters // CF. "Price" & "Min & Max price" atts | @since 3.2
												);
												
												$output .= '<div class="cspml_fs_item_container cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12" data-map-id="'.$map_id.'">';
													
													if(!empty($display_type))
														$output .= $this->cspml_faceted_search_fields($map_id, $display_type, $field_atts, $post_type, false, $post_ids); //@edited 3.7	
												
												$output .= '</div>';
												
											}
											
										}
										
										$rendered_custom_fields[] = $custom_field_name;
									
									}
									
								}
												
							}
						
						}
						
					}
					 
					$output .= '<div class="clearfix"></div>';
					
					if($this->cspml_filter_btns_position == 'bottom' || $this->cspml_filter_btns_position == 'both'){
						
						$output .= '<div class="cspm-row row-no-margin cspm_filter_bottom_btns">';	
							
							$output .= '<a class="cspml_reset_lsitings_filter cspml_btn cspm_bg_hex_hover cspm_border_shadow cspm-col-lg-4 cspm-col-md-4 cspm-col-sm-3 cspm-col-xs-3" title="'.apply_filters('__reset_all_filters', esc_html__('Reset all filters', 'cspml')).'" data-request-type="reset" data-map-id="'.$map_id.'" data-display-location="listings">';
								$output .= '<img src="'.apply_filters('cspml_refresh_filter_img', $this->plugin_url.'img/svg/refresh.svg', str_replace('map', '', $map_id)).'" class="no-margin-right" />';
							$output .= '</a>';
										
							$output .= '<a class="cspml_submit_listings_filter cspml_btn cspm_bg_hex_hover cspm_border_shadow cspm-col-lg-8 cspm-col-md-8 cspm-col-sm-9 cspm-col-xs-9" title="'.apply_filters('__filter', esc_html__('Filter', 'cspml')).'" data-request-type="filter" data-map-id="'.$map_id.'" data-display-location="listings">';
								$output .= '<img src="'.apply_filters('cspml_filter_search_img', $this->plugin_url.'img/svg/filter.svg', str_replace('map', '', $map_id)).'" />';
								$output .= esc_html__($this->cspml_filter_btn_text, 'cspml');
							$output .= '</a>';
						
						$output .= '</div>';
						
						$output .= '<div class="clear-both"></div>';
						
					}
					
				$output .= '</form>';
				
				/**
				 * A filter that allows adding extra HTML after the filter form */
				 
				$output .= apply_filters('cspml_after_filter_form', '', $map_id);

				$output .= '<div class="clear-both visible-sm visible-xs"></div>';
				
			}
			
			return $output;
		
		}
		
		
		/**
		 * Prepare the fields for the filter form 
		 *
		 * @since 1.0
		 * @update 1.1
		 * @updated 2.3
		 * @updated 2.4 [added datepicker for custom fields]
         * @updated 3.7 [changed the order of function atts | PHP8 compatibility]
		 */
		function cspml_faceted_search_fields($map_id, $field_type, $field_atts, $post_type, $is_terms = false, $post_ids = array(), $filter_container = "listings"){
			
			$defaults = array(
				'field_name' => '',
				'field_label' => '',
				'field_description' => '', //@since 2.4
				'field_options' => array(),
				'symbol' => '',
				'symbol_position' => 'text',
				'search_all_option' => 'false',
				'search_all_text' => apply_filters('__all', esc_html__('All', 'cspml')),
				'show_count' => '',
				'compare_parameter' => '',
				'default_value' => '',
				'display_status' => 'open', //@since 2.3
                'field_options_order' => 'ASC', //@since 3.6
				
				/**
				 * Custom fields Datepicker parameters 
				 * @since 2.4 */
				
				'date_format' => '',
				'date_separator' => '',
				'start_date' => '',
				'end_date' => '',
				'week_start' => '',
				
				/**
				 * "Text" & "Min & Max text" field parameters 
				 * @since 3.2 */
				 
				'mask' => '',
				'clear_mask' => 'true',
				'placeholder' => '',
				
				/**
				 * "Price" & "Min & Max price" parameters 
				 * @since 3.2 */
				
				'cents_separator' => ',',
				'thousands_separator' => '.',
				'price_limit' => 0,
				'cents_limit' => 0,
				'negative_price' => 'no',
				
			);
			
			extract( wp_parse_args( $field_atts, $defaults ) );

			if(class_exists('CSProgressMap'))
				$CSProgressMap = CSProgressMap::this(); //@since 3.6
            
			$option_ids_array = array();
			
			$output = '';				
			
			$i = 1;
			
			$count_options = count($field_options);
			
			$arrow_down_img = apply_filters('cspml_filter_arrow_down_img', $this->plugin_url.'img/svg/arrow-down.svg', str_replace('map', '', $map_id));
			
			$display_css = ($display_status == 'open') ? '' : ' style="display:none;"';	//@since 2.3
			
			/**
			 * Text/price field */
			
			if(in_array($field_type, array('text', 'price'))){
					
				if($is_terms)
					$filter_type = "taxonomy";			
				else $filter_type = "custom_field";
															
				$output .= '<div class="cspml_fs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'" data-map-id="'.$map_id.'">';
					$output .= '<span class="cspml_label_text">'.$field_label.'</span>';
					$output .= '<span class="cspml_toggle_btn" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'">';
						$output .= '<img src="'.$arrow_down_img.'" style="height:15px; width:auto;" />';
					$output .= '</span>';
				$output .= '</div>';
					 	
				$output .= '<div class="cspml_fs_options_list" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'" '.$display_css.'>';
					
					if(!empty($field_description))
						$output .= '<div class="cspml_field_desc">'.$field_description.'</div>';
										
					$output .= '<input type="hidden" name="'.$field_name.'_filter_type" value="'.$filter_type.'" />';
											
					/**
					 * Text parameters */
					 
					$text_mask = (!empty($mask) && $field_type == 'text') ? 'data-mask="'.$mask.'"' : '';
					$clear_if_not_match = ($clear_mask == 'true' && !empty($mask) && $field_type == 'text') ? 'data-mask-clearifnotmatch="'.$clear_mask.'"' : '';
					
					/**
					 * Price parameters */
					
					if($field_type == 'price'){
						
						$prefix = ($symbol_position == 'before' && !empty($symbol)) ? $symbol : '';
						$suffix = ($symbol_position == 'after' && !empty($symbol)) ? $symbol : '';
						$limit = ($price_limit > 0) ? $price_limit : 'false';
						$centsLimit = ($cents_limit > 0) ? $cents_limit : 'false';
						$allowNegative = ($negative_price != 'no') ? 'true' : 'false';
                        
                        $price_js_script = "jQuery(document).ready(function($){	
                            $('input[type=text][name=".'"'.$field_name.'"'."][data-map-id=".$map_id."]').priceFormat({
                                prefix: '" . $prefix . "',
                                suffix: '" . $suffix . "',
                                centsSeparator: '" . $cents_separator . "',
                                thousandsSeparator: '" . $thousands_separator . "',
                                limit: " . $limit . ",
                                centsLimit: " . $centsLimit . ",
                                clearPrefix: false,
                                clearSufix: false,
                                allowNegative: " . $allowNegative . ",
                                insertPlusSign: false,
                                clearOnEmpty: true,
                                leadingZero: true
                            });
                        });"; //@edited 3.7.3
                        
                        /**
                         * Load price field script based on the type of the theme
                         *
                         * This is to fix an issue with "Full-site-editing (FSE) / block" themes where it's impossible to ...
                         * ... pass JS data inside a shortcode to an already registred script because in ...
                         * ... FSE themes, shortcode callback will be executed before a plugin ...
                         * ... had a chance to register the script with "wp_enqueue_scripts". ... 
                         * ... The fix will be to enqueue scripts with "add_action" using the hook "wp_enqueue_scripts" in FSE themes ...
                         * ... which will allow our scripts to be executed before the shortcode callback.
                         * In classic themes, shortcode callback will be executed after "wp_enqueue_scripts" and we can call ...
                         * ... our enqueue functions directly with no need for "add_action". Doing like with FSE themes won't work ...
                         * ... for classic themes!
                         *
                         * Note: "wp_script_is()" serves as a fallback for FSE themes, typically no-theme platforms, which cannot be detected using "wp_is_block_theme()"!
                         *
                         * @since 3.7.3
                         */
                        
                        if(wp_is_block_theme() || !wp_script_is('cspml-script', 'registered')){
                            add_action('wp_enqueue_scripts', function() use ($price_js_script){
                                wp_add_inline_script('cspml-script', $price_js_script);
                            });
                        }else{
                            wp_add_inline_script('cspml-script', $price_js_script);
                        }
						
					}
											
					/**
					 * The Input */
										
					$output .= '<div class="cspml_input_container">';
						$output .= ($symbol_position == 'before' && !empty($symbol) && $field_type == 'text') ? '<span class="cspml_input_symbol_before" data-map-id="'.$map_id.'" data-field-name="'.$field_name.'">'.$symbol.'</span>' : '';
						$output .= '<input type="text" name="'.$field_name.'" value="" data-filter-type="'.$search_type.'" data-map-id="'.$map_id.'" placeholder="'.$placeholder.'" '.$text_mask. ' ' .$clear_if_not_match.' class="cspml_mask_input cspml_type_text_like">';							
						$output .= ($symbol_position == 'after' && !empty($symbol) && $field_type == 'text') ? '<span class="cspml_input_symbol_after" data-map-id="'.$map_id.'" data-field-name="'.$field_name.'">'.$symbol.'</span>' : '';												
					$output .= '</div>';

				$output .= '</div>';	
				
			/**
			 * Min & Max text/price field */
			
			}elseif(in_array($field_type, array('min_max_text', 'min_max_price'))){
					
				if($is_terms)
					$filter_type = "taxonomy";			
				else $filter_type = "custom_field";
															
				$output .= '<div class="cspml_fs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'" data-map-id="'.$map_id.'">';
					$output .= '<span class="cspml_label_text">'.$field_label.'</span>';
					$output .= '<span class="cspml_toggle_btn" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'">';
						$output .= '<img src="'.$arrow_down_img.'" style="height:15px; width:auto;" />';
					$output .= '</span>';
				$output .= '</div>';
					 	
				$output .= '<div class="cspml_fs_options_list" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'" '.$display_css.'>';
					
					if(!empty($field_description))
						$output .= '<div class="cspml_field_desc">'.$field_description.'</div>';
										
					$output .= '<input type="hidden" name="'.$field_name.'_filter_type" value="'.$filter_type.'" />';
					
					/**
					 * Text paremeters */
					 
					$text_mask = (!empty($mask) && $field_type == 'min_max_text') ? 'data-mask="'.$mask.'"' : '';
					$clear_if_not_match = ($clear_mask == 'true' && !empty($mask) && $field_type == 'min_max_text') ? 'data-mask-clearifnotmatch="'.$clear_mask.'"' : '';
					
					/**
					 * Price parameters */
					
					if($field_type == 'min_max_price'){
						
						$prefix = ($symbol_position == 'before' && !empty($symbol)) ? $symbol : '';
						$suffix = ($symbol_position == 'after' && !empty($symbol)) ? $symbol : '';
						$limit = ($price_limit > 0) ? $price_limit : 'false';
						$centsLimit = ($cents_limit > 0) ? $cents_limit : 'false';
						$allowNegative = ($negative_price != 'no') ? 'true' : 'false';
                        
                        $min_max_price_js_script = "jQuery(document).ready(function($){	
								$('input[type=text][name=".'"'.$field_name.'[]"'."][data-map-id=".$map_id."]').priceFormat({
									prefix: '" . $prefix . "',
									suffix: '" . $suffix . "',
									centsSeparator: '" . $cents_separator . "',
									thousandsSeparator: '" . $thousands_separator . "',
									limit: " . $limit . ",
									centsLimit: " . $centsLimit . ",
									clearPrefix: false,
									clearSufix: false,
									allowNegative: " . $allowNegative . ",
									insertPlusSign: false,
									clearOnEmpty: true,
									leadingZero: true
								});
							});"; //@edited 3.7.3
                            
                            /**
                             * Load Min-Max price script based on the type of the theme
                             *
                             * This is to fix an issue with "Full-site-editing (FSE) / block" themes where it's impossible to ...
                             * ... pass JS data inside a shortcode to an already registred script because in ...
                             * ... FSE themes, shortcode callback will be executed before a plugin ...
                             * ... had a chance to register the script with "wp_enqueue_scripts". ... 
                             * ... The fix will be to enqueue scripts with "add_action" using the hook "wp_enqueue_scripts" in FSE themes ...
                             * ... which will allow our scripts to be executed before the shortcode callback.
                             * In classic themes, shortcode callback will be executed after "wp_enqueue_scripts" and we can call ...
                             * ... our enqueue functions directly with no need for "add_action". Doing like with FSE themes won't work ...
                             * ... for classic themes!
                             *
                             * Note: "wp_script_is()" serves as a fallback for FSE themes, typically no-theme platforms, which cannot be detected using "wp_is_block_theme()"!
                             *
                             * @since 3.7.3
                             */
                        
                            if(wp_is_block_theme() || !wp_script_is('cspml-script', 'registered')){
                                add_action('wp_enqueue_scripts', function() use ($min_max_price_js_script){
                                    wp_add_inline_script('cspml-script', $min_max_price_js_script);
                                });
                            }else{
                                wp_add_inline_script('cspml-script', $min_max_price_js_script);
                            }
						
					}
																						
					$output .= '<div class="cspml_input_container">';
						
						$output .= '<div class="cspml_min_max_container">';
							$output .= ($symbol_position == 'before' && !empty($symbol) && $field_type == 'min_max_text') ? '<span class="cspml_input_symbol_before" data-map-id="'.$map_id.'" data-field-name="'.$field_name.'[]">'.$symbol.'</span>' : '';
							$output .= '<input type="text" name="'.$field_name.'[]" value="" data-filter-type="'.$filter_type.'" data-map-id="'.$map_id.'" placeholder="'.$placeholder.'" '.$text_mask. ' ' .$clear_if_not_match.' class="cspml_mask_input min_max cspml_type_text_like">';							
							$output .= ($symbol_position == 'after' && !empty($symbol) && $field_type == 'min_max_text') ? '<span class="cspml_input_symbol_after" data-map-id="'.$map_id.'" data-field-name="'.$field_name.'[]">'.$symbol.'</span>' : '';																			
						$output .= '</div>';
						
						$output .= '<span class="cspml_min_max_to">'.apply_filters('__to', esc_html__('To', 'cspmas')).'</span>';
						
						$output .= '<div class="cspml_min_max_container">';
							$output .= ($symbol_position == 'before' && !empty($symbol) && $field_type == 'min_max_text') ? '<span class="cspml_input_symbol_before" data-map-id="'.$map_id.'" data-field-name="'.$field_name.'[]">'.$symbol.'</span>' : '';
							$output .= '<input type="text" name="'.$field_name.'[]" value="" data-filter-type="'.$filter_type.'" data-map-id="'.$map_id.'" placeholder="'.$placeholder.'" '.$text_mask. ' ' .$clear_if_not_match.' class="cspml_mask_input min_max cspml_type_text_like">';							
							$output .= ($symbol_position == 'after' && !empty($symbol) && $field_type == 'min_max_text') ? '<span class="cspml_input_symbol_after" data-map-id="'.$map_id.'" data-field-name="'.$field_name.'[]">'.$symbol.'</span>' : '';												
						$output .= '</div>';
						
						$output .= '<div class="clear-both"></div>';
						
					$output .= '</div>';

				$output .= '</div>';		
				
			/** 
			 * Checkbox field */
			 
			}elseif($field_type == "checkbox"){
				
				//if($filter_container != "map"){
															
					$output .= '<div class="cspml_fs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'" data-map-id="'.$map_id.'">';
						$output .= '<span class="cspml_label_text">'.$field_label.'</span>';
						$output .= '<span class="cspml_toggle_btn" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'">';
							$output .= '<img src="'.$arrow_down_img.'" style="height:15px; width:auto;" />';
						$output .= '</span>';
					$output .= '</div>';
					
				/*}else{
					
					$output .= '<div class="cspml_mfs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'">';										
						$output .= '<span class="cspml_label_text cspm-col-lg-10 cspm-col-sm-10 cspm-col-xs-11 cspm-col-md-10">'.$field_label.'</span>';
					$output .= '</div>';
					
				}*/
					 	
				$output .= '<div class="cspml_fs_options_list" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'" '.$display_css.'>';
					
					if(!empty($field_description))
						$output .= '<div class="cspml_field_desc">'.$field_description.'</div>'; //@since 2.4
					
					$filter_type = ($is_terms) ? "taxonomy" : "custom_field";
					
					$output .= '<input type="hidden" name="'.$field_name.'_filter_type" value="'.$filter_type.'" />';
					
					/**
					 * List of options */
                
					foreach($this->cspml_convert_serialized_values_to_unique_options($field_options, $field_options_order) as $option){ //@edited 3.6
						
						if($is_terms){
															
							$label = $option->name;
							$name  = $option->slug;
							$id    = $option->term_id;
							$value = $option->term_id;
							
							$filter_type = "taxonomy";
							
							if($show_count == "yes")																									
								$count_posts = $this->cspml_count_term_posts($field_name, $id, $post_type, $post_ids);
							
						}else{
                                                        
                            $CSProgressMap->cspm_wpml_register_string($option, $option, $CSProgressMap->use_with_wpml); //@since 3.6
							
							$label = $name = $id = $value = esc_attr($option); //@edited 3.7.3
							
							if($show_count == "yes"){		
								$count_posts = $this->cspml_count_custom_field_posts($post_type, $field_name, $post_ids); //@edited 3.6						
								$count_posts = isset($count_posts[$name]) ? $count_posts[$name] : '0';
							}
							
							$filter_type = "custom_field";
						
						}
						
						$option_ids_array[] = $id;
						
						$output .= '<div class="cspml_input_container">';
						$output .= '<label>'; //@edited 3.5
							$output .= '<input type="checkbox" name="'.$field_name.'[]" data-filter-type="'.$filter_type.'" value="'.$value.'" />'; //@edited 3.5						
							$output .= ($symbol_position == "before") ? $symbol : ''; 
							$output .= $CSProgressMap->cspm_wpml_get_string($label, $label, $CSProgressMap->use_with_wpml); //@edited 3.6
							$output .= ($symbol_position == "after") ? $symbol : '';
							$output .= ($show_count == "yes") ? ' ('.$count_posts.')' : '';
						$output .= '</label>';	
						$output .= '</div>';				
					
						/**
						 * Show all option */
						 
						if($search_all_option == "yes" && $i++ == $count_options){
							$output .= '<div class="cspml_input_container">';
							$output .= '<input type="checkbox" name="'.$field_name.'[]" class="cspml_show_all" id="show_all_'.$map_id.'_'.$field_name.'" data-filter-type="'.$filter_type.'" value="'.implode(',', $option_ids_array).'" />';
							$output .= '<label for="show_all_'.$map_id.'_'.$field_name.'" id="show_all_'.$map_id.'_'.$field_name.'">'.$search_all_text.'</label>';
							$output .= '</div>';
						}
		
					}
				
				$output .= '</div>';
			
			/**
			 * Radio field */
			 
			}elseif($field_type == "radio"){
					
				//if($filter_container != "map"){
															
					$output .= '<div class="cspml_fs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'" data-map-id="'.$map_id.'">';
						$output .= '<span class="cspml_label_text">'.$field_label.'</span>';
						$output .= '<span class="cspml_toggle_btn" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'">';
							$output .= '<img src="'.$arrow_down_img.'" style="height:15px; width:auto;" />';
						$output .= '</span>';
					$output .= '</div>';
					
				/*}else{
					
					$output .= '<div class="cspml_mfs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'">';										
						$output .= '<span class="cspml_label_text cspm-col-lg-10 cspm-col-sm-10 cspm-col-xs-11 cspm-col-md-10">'.$field_label.'</span>';
					$output .= '</div>';
					
				}*/
					
				$output .= '<div class="cspml_fs_options_list" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'" '.$display_css.'>';
					
					if(!empty($field_description))
						$output .= '<div class="cspml_field_desc">'.$field_description.'</div>'; //@since 2.4
					
					$filter_type = ($is_terms) ? "taxonomy" : "custom_field";
					
					$output .= '<input type="hidden" name="'.$field_name.'_filter_type" value="'.$filter_type.'" />';
					
					/**
					 * List of options */
					 
					foreach($this->cspml_convert_serialized_values_to_unique_options($field_options, $field_options_order) as $option){ //@edited 3.6
						
						if($is_terms){
															
							$label = $option->name;
							$name  = $option->slug;
							$id    = $option->term_id;
							$value = $option->term_id;
							
							$filter_type = "taxonomy";
							
							if($show_count == "yes")																									
								$count_posts = $this->cspml_count_term_posts($field_name, $id, $post_type, $post_ids);
							
						}else{
							
                            $CSProgressMap->cspm_wpml_register_string($option, $option, $CSProgressMap->use_with_wpml); //@since 3.6

                            $label = $name = $id = $value = esc_attr($option); //@edited 3.7.3
																							
							if($show_count == "yes"){		
								$count_posts = $this->cspml_count_custom_field_posts($post_type, $field_name, $post_ids); //@edited 3.6
								$count_posts = isset($count_posts[$name]) ? $count_posts[$name] : '0';
							}
							
							$filter_type = "custom_field";
						
						}
						
						$option_ids_array[] = $id;

						$output .= '<div class="cspml_input_container">';
						$output .= '<label>'; //@edited 3.5
							$output .= '<input type="radio" name="'.$field_name.'" data-filter-type="'.$filter_type.'" value="'.$value.'" />'; //@edited 3.5						
							$output .= ($symbol_position == "before") ? $symbol : ''; 
							$output .= $CSProgressMap->cspm_wpml_get_string($label, $label, $CSProgressMap->use_with_wpml); //@edited 3.6
							$output .= ($symbol_position == "after") ? $symbol : '';
							$output .= ($show_count == "yes") ? ' ('.$count_posts.')' : '';
						$output .= '</label>';
						$output .= '</div>';	

						if($i++ == $count_options){
							
							/**
							 * Show all option */
							 
							if($search_all_option == "yes"){
								$output .= '<div class="cspml_input_container">';
								$output .= '<input type="radio" name="'.$field_name.'" class="cspml_show_all" id="show_all_'.$map_id.'_'.$field_name.'" data-filter-type="'.$filter_type.'" value="'.implode(',', $option_ids_array).'" />';
								$output .= '<label for="show_all_'.$map_id.'_'.$field_name.'" id="show_all_'.$map_id.'_'.$field_name.'">'.$search_all_text.'</label>';
								$output .= '</div>';
							}
								
						}
						
					}
						
				$output .= '</div>';					
				
			/**
			 * Select field */
			 
			}elseif($field_type == "select"){
					
				if($is_terms)
					$filter_type = "taxonomy";			
				else
					$filter_type = "custom_field";
				
				$select_extra_class = ($search_all_option == "yes") ? ' cspml_show_all' : '';
					$filter_type;
				
				//if($filter_container != "map"){
															
					$output .= '<div class="cspml_fs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'" data-map-id="'.$map_id.'">';
						$output .= '<span class="cspml_label_text">'.$field_label.'</span>';
						$output .= '<span class="cspml_toggle_btn" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'">';
							$output .= '<img src="'.$arrow_down_img.'" style="height:15px; width:auto;" />';
						$output .= '</span>';
					$output .= '</div>';
					
				/*}else{
					
					$output .= '<div class="cspml_mfs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'">';										
						$output .= '<span class="cspml_label_text cspm-col-lg-10 cspm-col-sm-10 cspm-col-xs-11 cspm-col-md-10">'.$field_label.'</span>';
					$output .= '</div>';
					
				}*/
					
				$output .= '<div class="cspml_fs_options_list" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'" '.$display_css.'>';
					
					if(!empty($field_description))
						$output .= '<div class="cspml_field_desc">'.$field_description.'</div>'; //@since 2.4
					
					$output .= '<input type="hidden" name="'.$field_name.'_filter_type" value="'.$filter_type.'" />';
										
					$output .= '<div class="cspml_input_container">';
					$output .= '<select name="'.$field_name.'" id="'.$field_name.'" data-filter-type="'.$filter_type.'" class="cspml_fs_selectize'.$select_extra_class.'" data-map-id="'.$map_id.'">';
						$output .= '<option value=""></option>';
						
						/**
						 * List of options */
						 
						foreach($this->cspml_convert_serialized_values_to_unique_options($field_options, $field_options_order) as $option){ //@edited 3.6
							
							if($is_terms){
																
								$label = $option->name;
								$name  = $option->slug;
								$id    = $option->term_id;
								$value = $option->term_id;
								
								$filter_type = "taxonomy";
								
								if($show_count == "yes")																									
									$count_posts = $this->cspml_count_term_posts($field_name, $id, $post_type, $post_ids);
								
							}else{
                            
                                $CSProgressMap->cspm_wpml_register_string($option, $option, $CSProgressMap->use_with_wpml); //@since 3.6

                                $label = $name = $id = $value = esc_attr($option); //@edited 3.7.3
																
								if($show_count == "yes"){		
									$count_posts = $this->cspml_count_custom_field_posts($post_type, $field_name, $post_ids); //@edited 3.6
									$count_posts = isset($count_posts[$name]) ? $count_posts[$name] : '0';
								}
								
								$filter_type = "custom_field";
							
							}
							
							$option_ids_array[] = $id;
																																
							$output .= '<option value="'.$value.'">';
								$output .= ($symbol_position == "before") ? $symbol : ''; 
								$output .= $CSProgressMap->cspm_wpml_get_string($label, $label, $CSProgressMap->use_with_wpml); //@edited 3.6
								$output .= ($symbol_position == "after") ? $symbol : '';
								$output .= ($show_count == "yes") ? ' ('.$count_posts.')' : '';
							$output .= '</option>';											
						
							/**
							 * Show all option */
							 
							if($search_all_option == "yes" && $i++ == $count_options){
								$output .= '<option class="cspml_show_all" value="'.implode(',', $option_ids_array).'">';
									$output .= ($symbol_position == "before") ? $symbol : ''; 
									$output .= $search_all_text;
									$output .= ($symbol_position == "after") ? $symbol : '';
								$output .= '</option>';											
							}
		
						}
				
					$output .= '</select>';
					$output .= '</div>';
				
				$output .= '</div>';		
				
			/**
			 * Number field */
			 
			}elseif($field_type == "number"){
				
				//$field_options = (array) str_replace($this->cspml_exclude_from_field_options, array('', ''), $field_options); //@edited 3.7.2
				
				$implode_options = '';
					
				if($is_terms){
					
					$filter_type = "taxonomy";			
					
					$taxonomy_options = array();
					
					foreach($field_options as $option){
						
						$option = (array) $option;
						
						if(isset($option['name']))
							$taxonomy_options[] = $option['name'];
					}
					
					$field_options = $taxonomy_options;
					
					$implode_options = implode('', $taxonomy_options);
					
				}else{
					
					$filter_type = "custom_field";
					
					$implode_options = implode('', $field_options);
					
				}
				
				//if(is_numeric(floor($implode_options))){
				if(ctype_digit(str_replace(' ', '', $implode_options))){
							
					$min_value = min($field_options);
					$max_value = max($field_options) == $min_value ? $min_value + 10 : max($field_options);
					
					//if($filter_container != "map"){
																
						$output .= '<div class="cspml_fs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'" data-map-id="'.$map_id.'">';
							$output .= '<span class="cspml_label_text">'.$field_label.'</span>';
							$output .= '<span class="cspml_toggle_btn" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'">';
								$output .= '<img src="'.$arrow_down_img.'" style="height:15px; width:auto;" />';
							$output .= '</span>';
						$output .= '</div>';
						
					/*}else{
						
						$output .= '<div class="cspml_mfs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'">';										
							$output .= '<span class="cspml_label_text cspm-col-lg-10 cspm-col-sm-10 cspm-col-xs-11 cspm-col-md-10">'.$field_label.'</span>';
						$output .= '</div>';
						
					}*/
					
					$output .= '<div class="cspml_fs_options_list" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'" '.$display_css.'>';
					
						if(!empty($field_description))
							$output .= '<div class="cspml_field_desc">'.$field_description.'</div>'; //@since 2.4
					
						$output .= '<input type="hidden" name="'.$field_name.'_filter_type" value="'.$filter_type.'" />';
					
						$output .= '<div class="cspml_input_container">';
						
							$output .= '<div data-trigger="spinner" class="cspml_input_spinner cspml_fs_number_field">';
								
								$output .= '<div class="cspml_spinner_input">';
									$output .= ($symbol_position == 'before' && !empty($symbol)) ? '<span class="cspml_input_symbol_before" data-map-id="'.$map_id.'" data-field-name="'.$field_name.'">'.$symbol.'</span>' : '';
									$output .= '<input type="text" name="'.$field_name.'" value="'.$min_value.'" data-rule="quantity" data-filter-type="'.$filter_type.'" data-min="'.$min_value.'" data-max="'.$max_value.'" data-map-id="'.$map_id.'" class="cspml_type_text_like">';
									$output .= ($symbol_position == 'after' && !empty($symbol)) ? '<span class="cspml_input_symbol_after" data-map-id="'.$map_id.'" data-field-name="'.$field_name.'">'.$symbol.'</span>' : '';
								$output .= '</div>';
								
								$output .= '<div class="cspml_spinner_btns_container">';
									$output .= '<a href="javascript:;" data-spin="up" class="cspml_spinner_btn cspml_btn cspm_bg_hex_hover">+</a>';									
									$output .= '<a href="javascript:;" data-spin="down" class="cspml_spinner_btn cspml_btn cspm_bg_hex_hover down">-</a>';
									$output .= '<a href="javascript:;" class="cspml_reset_spinner cspml_spinner_btn cspml_btn cspm_bg_hex_hover" data-map-id="'.$map_id.'" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'">'.apply_filters('__any', esc_html__('Any', 'cspml')).'</a>';	
								$output .= '</div>';
								
							$output .= '</div>';
														
						$output .= '</div>';
						
					$output .= '</div>';	
				
				}
											
			/**
			 * Double Range slider */
			 
			}elseif($field_type == "double_slider"){
				
				//$field_options = (array) str_replace($this->cspml_exclude_from_field_options, array('', ''), $field_options); //@edited 3.7.2
				
				$implode_options = '';
					
				if($is_terms){
					
					$filter_type = "taxonomy";			
					
					$taxonomy_options = array();
					
					foreach($field_options as $option){
						
						$option = (array) $option;
						
						if(isset($option['name']))
							$taxonomy_options[] = $option['name'];
					}
					
					$field_options = $taxonomy_options;
					
					$implode_options = implode('', $taxonomy_options);
					
				}else{
					
					$filter_type = "custom_field";
					
					$implode_options = implode('', $field_options);
					
				}
				
				$slider_margin = apply_filters('cspml_slider_margin', 10); //@since 3.5.1
				
				if(ctype_digit(str_replace(' ', '', $implode_options))){

					$min_value = (min($field_options) - $slider_margin < 1) ? 1 : min($field_options) - $slider_margin; //@edited 3.5.1
					$max_value = max($field_options) == $min_value ? ($min_value + $slider_margin) : max($field_options) + $slider_margin; //@edited 3.5.1
					
					//if($filter_container != "map"){
																
						$output .= '<div class="cspml_fs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'" data-map-id="'.$map_id.'">';
							$output .= '<span class="cspml_label_text">'.$field_label.'</span>';
							$output .= '<span class="cspml_toggle_btn" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'">';
								$output .= '<img src="'.$arrow_down_img.'" style="height:15px; width:auto;" />';
							$output .= '</span>';
						$output .= '</div>';
						
					/*}else{
						
						$output .= '<div class="cspml_mfs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'">';										
							$output .= '<span class="cspml_label_text cspm-col-lg-10 cspm-col-sm-10 cspm-col-xs-11 cspm-col-md-10">'.$field_label.'</span>';
						$output .= '</div>';
						
					}*/
					
					$output .= '<div class="cspml_fs_options_list" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'" '.$display_css.'>';
					
						if(!empty($field_description))
							$output .= '<div class="cspml_field_desc">'.$field_description.'</div>'; //@since 2.4
					
						if($symbol_position == "before")
							$prefix = 'data-prefix="'.$symbol.'"';				
						elseif($symbol_position == "after")
							$prefix = 'data-postfix="'.$symbol.'"';
						else $prefix = ''; 
					
						$output .= '<input type="hidden" name="'.$field_name.'_filter_type" value="'.$filter_type.'" />';
					
						$output .= '<div class="cspml_input_container">';
							$output .= '<input type="text" name="'.$field_name.'" class="cspml_fs_slider_range" data-min="'.$min_value.'" data-max="'.$max_value.'" data-values-separator=" to " data-prettify-separator="," data-filter-type="'.$filter_type.'" data-type="double" '.$prefix.' />';
						$output .= '</div>';
						
					$output .= '</div>';	
					
				}
											
			/**
			 * Single slider */
			 
			}elseif($field_type == "single_slider"){
				
				//$field_options = (array) str_replace($this->cspml_exclude_from_field_options, array('', ''), $field_options); //@edited 3.7.2
				
				$implode_options = '';
					
				if($is_terms){
					
					$filter_type = "taxonomy";			
					
					$taxonomy_options = array();
					
					foreach($field_options as $option){
						
						$option = (array) $option;
						
						if(isset($option['name']))
							$taxonomy_options[] = $option['name'];
					}
					
					$field_options = $taxonomy_options;
					
					$implode_options = implode('', $taxonomy_options);
					
				}else{
					
					$filter_type = "custom_field";
					
					$implode_options = implode('', $field_options);
					
				}
				
				$slider_margin = apply_filters('cspml_slider_margin', 10); //@since 3.5.1
				
				if(ctype_digit(str_replace(' ', '', $implode_options))){
								
					$min_value = (min($field_options) - $slider_margin < 1) ? 1 : min($field_options) - $slider_margin; //@edited 3.5.1
					$max_value = max($field_options) == $min_value ? ($min_value + $slider_margin) : max($field_options) + $slider_margin; //@edited 3.5.1
					
					//if($filter_container != "map"){
																
						$output .= '<div class="cspml_fs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'" data-map-id="'.$map_id.'">';
							$output .= '<span class="cspml_label_text">'.$field_label.'</span>';
							$output .= '<span class="cspml_toggle_btn" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'">';
								$output .= '<img src="'.$arrow_down_img.'" style="height:15px; width:auto;" />';
							$output .= '</span>';
						$output .= '</div>';
						
					/*}else{
						
						$output .= '<div class="cspml_mfs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'">';										
							$output .= '<span class="cspml_label_text cspm-col-lg-10 cspm-col-sm-10 cspm-col-xs-11 cspm-col-md-10">'.$field_label.'</span>';
						$output .= '</div>';
						
					}*/
					
					$output .= '<div class="cspml_fs_options_list" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'" '.$display_css.'>';
						
						if(!empty($field_description))
							$output .= '<div class="cspml_field_desc">'.$field_description.'</div>'; //@since 2.4
					
						if($symbol_position == "before")
							$prefix = 'data-prefix="'.$symbol.'"';				
						elseif($symbol_position == "after")
							$prefix = 'data-postfix="'.$symbol.'"';
						else $prefix = ''; 
					
						$output .= '<input type="hidden" name="'.$field_name.'_filter_type" value="'.$filter_type.'" />';
							
						$output .= '<div class="cspml_input_container">';
							$output .= '<input type="text" name="'.$field_name.'" class="cspml_fs_slider_range" value="'.$min_value.'" data-min="'.$min_value.'" data-max="'.$max_value.'" data-filter-type="'.$filter_type.'" data-type="single" '.$prefix.' />';
						$output .= '</div>';	
						
					$output .= '</div>';	
				
				}
				
			/**
			 * Datepicker (for custom fields)
			 * @since 2.4 */

			}elseif($field_type == "datepicker"){
				
				$datepicker_atts = array_merge(
					$field_atts,
					array(
						'map_id' => $map_id,
						'post_type' => $post_type,
						'post_ids' => $post_ids,
						'filter_type' => 'custom_field',
					)
				);
				
				$output .= $this->cspml_custom_field_datepicker($datepicker_atts);		

			}
			
			return $output;
			
		}
		
		
		/** 
		 * This will display a text field in the filter for keyword search 
		 *
		 * @since 2.2 
		 * @updated 2.3
		 */
		function cspml_keyword_search_field($map_id, $post_type, $post_ids){
						
			$output = '';
			
			$filter_container = 'list';
			
			if($this->cspml_keyword_filter_option == 'yes'){
				
				$field_name = 'cspml_keyword';				
				$field_label = esc_html__($this->cspml_keyword_filter_label, 'cspml');
				$field_placeholder = esc_html__($this->cspml_keyword_filter_placeholder, 'cspml');
				$field_description = esc_html__($this->cspml_keyword_filter_description, 'cspml'); //@since 2.4
				$display_status = $this->cspml_keyword_display_status; //@since 2.3
				
				$arrow_down_img = apply_filters('cspml_filter_arrow_down_img', $this->plugin_url.'img/svg/arrow-down.svg', str_replace('map', '', $map_id));
				
				$output .= '<div class="cspml_fs_item_container cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12" data-map-id="'.$map_id.'">';
				
					//if($filter_container != "map"){
																
						$output .= '<div class="cspml_fs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'" data-map-id="'.$map_id.'">';
							$output .= '<span class="cspml_label_text">'.$field_label.'</span>';
							$output .= '<span class="cspml_toggle_btn" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'">';
								$output .= '<img src="'.$arrow_down_img.'" style="height:15px; width:auto;" />';
							$output .= '</span>';
						$output .= '</div>';
						
					/*}else{
						
						$output .= '<div class="cspml_mfs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'">';										
							$output .= '<span class="cspml_label_text cspm-col-lg-10 cspm-col-sm-10 cspm-col-xs-11 cspm-col-md-10">'.$field_label.'</span>';
						$output .= '</div>';
						
					}*/
					
					$display_css = ($display_status == 'open') ? '' : ' style="display:none;"';
					
					$output .= '<div class="cspml_fs_options_list" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'" '.$display_css.'>';
							
						$output .= '<input type="hidden" name="'.$field_name.'_filter_type" value="keyword" />';
						
						if(!empty($field_description))
							$output .= '<div class="cspml_field_desc">'.$field_description.'</div>'; //@since 2.4
													
						$output .= '<div class="cspml_input_container">';
							$output .= '<input type="text" name="'.$field_name.'" class="'.$field_name.' cspml_type_text_like" value="" placeholder="'.$field_placeholder.'" data-map-id="'.$map_id.'" />';
						$output .= '</div>';
					
					$output .= '</div>';
												
				$output .= '</div>';
					
			}
			
			return $output;
			
		}
		
		
		/**
		 * This will display a datepicker in the filter that allows filtering ...
		 * ... posts by publish date
		 * 
		 * @since 2.1
		 * @updated 2.3
		 */
		function cspml_posts_publish_date_field($map_id, $post_type, $post_ids){
						
			$output = '';
			
			$filter_container = 'list';
			
			if($this->cspml_date_filter_option == 'yes'){
				
				$display_status = $this->cspml_date_filter_display_status; //@since 2.3
					
				/**
				 * Get Datepicker parameters */
			
				$date_filter_params = isset($this->cspml_date_filter_parameters[0])
					? $this->cspml_date_filter_parameters[0]
					: array();
				
				$field_name = 'cspml_posts_date_filter';
				$field_label = esc_html__($this->cspml_setting_exists('date_filter_label', $date_filter_params, 'Posts publish date'), 'cspml');
				$field_description = esc_html__($this->cspml_setting_exists('date_filter_description', $date_filter_params, ''), 'cspml'); //@since 2.4
				
				$arrow_down_img = apply_filters('cspml_filter_arrow_down_img', $this->plugin_url.'img/svg/arrow-down.svg', str_replace('map', '', $map_id));
				
				$date_filter_type = $this->cspml_setting_exists('date_filter_type', $date_filter_params, 'exact_date');					
				
				/**
				 * Set date format */
				
				$date_format = $this->cspml_setting_exists('date_format', $date_filter_params, 'ymd');
				$exclude_from_date = $this->cspml_setting_exists('exclude_date_atts', $date_filter_params, array());
				$final_date_format = implode( 
					'/',
					str_replace(
						'yy',
						'yyyy',
						str_split(
							str_replace(
								array('d', 'm', 'y'),
								array('dd', 'mm', 'yy'),
								str_replace($exclude_from_date, '', $date_format) /** 1. Remove attribute ("y" OR "m" OR "d") from date format ("ymd" or "dmy") **/
							), /** 2. Replace ["ymd" To "yymmdd"] OR ["dmy" To "ddmmyy"] **/
							2
						) /** 3. Replace [from "yymmdd" To "array('yy', 'mm', 'dd')"] OR ["ddmmyy" To "array('dd', 'mm', 'yy')"] **/
					) /** 4. Replace [array('yyyy', 'mm', 'dd') To array('yyyy', 'mm', 'dd')] OR ["array('dd', 'mm', 'yy')" To "array('dd', 'mm', 'yyyy')"] **/
				); /** 5. Implode ["array('yyyy', 'mm', 'dd')" To "yyyy/mm/dd"] OR ["array('dd', 'mm', 'yy')" To "dd/mm/yyyy"] **/
					
				$language = $this->cspml_setting_exists('language', $date_filter_params, 'en-US');
				$start_date = $this->cspml_setting_exists('start_date', $date_filter_params, '');
				$end_date = $this->cspml_setting_exists('end_date', $date_filter_params, '');					
				
				$start_view = str_replace(					
					array('days', 'months', 'years'),
					array(0, 1, 2),
					$this->cspml_setting_exists('start_view', $date_filter_params, 'days')
				); // 0: days | 1: months | 2: years
				
				$week_start = str_replace(					
					array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'),
					array(0, 1, 2, 3, 4, 5, 6, 7),
					$this->cspml_setting_exists('week_start', $date_filter_params, 'sunday')
				); // 0: Sunday | 1: Monday | 2: Tuesday | 3: Wednesday | 4: Thursday | 5: Friday | 6: Saturday
				
				$year_first = str_replace(
					array('yes', 'no'), 
					array('true', 'false'), 
					$this->cspml_setting_exists('year_first', $date_filter_params, 'no')
				);
				
				$year_suffix = esc_html__($this->cspml_setting_exists('year_suffix', $date_filter_params, ''), 'cspml');
				
				$auto_show = str_replace(
					array('yes', 'no'), 
					array('true', 'false'), 
					$this->cspml_setting_exists('auto_show', $date_filter_params, 'no')
				);
				
				$auto_hide = str_replace(
					array('yes', 'no'), 
					array('true', 'false'), 
					$this->cspml_setting_exists('auto_hide', $date_filter_params, 'no')
				);
				
				$inline = str_replace(
					array('yes', 'no'), 
					array('true', 'false'), 
					$this->cspml_setting_exists('inline', $date_filter_params, 'no')
				);
				
				$output .= '<div class="cspml_fs_item_container cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12" data-map-id="'.$map_id.'">';
				
					//if($filter_container != "map"){
																
						$output .= '<div class="cspml_fs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'" data-map-id="'.$map_id.'">';
							$output .= '<span class="cspml_label_text">'.$field_label.'</span>';
							$output .= '<span class="cspml_toggle_btn" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'">';
								$output .= '<img src="'.$arrow_down_img.'" style="height:15px; width:auto;" />';
							$output .= '</span>';
						$output .= '</div>';
						
					/*}else{
						
						$output .= '<div class="cspml_mfs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'">';										
							$output .= '<span class="cspml_label_text cspm-col-lg-10 cspm-col-sm-10 cspm-col-xs-11 cspm-col-md-10">'.$field_label.'</span>';
						$output .= '</div>';
						
					}*/
					
					/**
					 * Whether we'll filter using two dates or one date.
					 * Two dates, means we need to datepickers */
					 
					$nbr_datepickers = ($date_filter_type == 'date_range') ? 2 : 1;
					
					$display_css = ($display_status == 'open') ? '' : ' style="display:none;"';					 
					 
					$output .= '<div class="cspml_fs_options_list" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'" '.$display_css.'>';
						
						$output .= '<div class="cspml_input_container">';
							
							if(!empty($field_description))
								$output .= '<div class="cspml_field_desc">'.$field_description.'</div>'; //@since 2.4
							
							/**
							 * Datepickers */
							
							for($i = 1; $i <= $nbr_datepickers; $i++){
								
								$new_field_name = $field_name.'_'.str_replace(array(1, 2), array('start', 'end'), $i);
							
								$output .= '<input type="hidden" name="'.$new_field_name.'_filter_type" value="'.$date_filter_type.'" />';
								
								$output .= '<div class="cspml_datepicker_container">';
								
									if($nbr_datepickers == 2){
										$output .= '<div class="cspml_datepicker_label_'.$i.'">';
											$output .= str_replace(
												array(1, 2), 
												array(
													apply_filters('__start_date', esc_html__('Start date', 'cspml')), 
													apply_filters('__start_date', esc_html__('End date', 'cspml'))
												), 
												$i
											);
										$output .= '</div>'; 
									}
									
									$output .= '<div style="float:left; width:75%;">';
										$output .= '<input type="text" data-toggle="cspml_datepicker_'.$i.'" name="'.$new_field_name.'" data-map-id="'.$map_id.'" class="cspml_fs_datepicker cspml_type_text_like" />';
									$output .= '</div>';
									
									$output .= '<div style="float:left; width:25%;">';
										$output .= '<div class="cspm_datepicker_trigger_'.$map_id.'_'.$i.' cspm_border_radius cspm_border_shadow">';
											$output .= '<img src="'.apply_filters('cspml_calendar_img', $this->plugin_url.'img/svg/calendar.svg', str_replace('map', '', $map_id)).'" />';
										$output .= '</div>';
									$output .= '</div>';
									
									$output .= '<div style="clear:both;"></div>';
									
								$output .= '</div>';
								
								if($inline == 'true')
									$output .= '<div class="cspml_inline_datepicker_container_'.$i.'" data-map-id="'.$map_id.'"></div>';
							
							}
							
						$output .= '</div>';
                
						/**
						 * Build Datepickers script */
						 
						$datepicker_js_script = "jQuery(document).ready(function($){";
						
						for($i = 1; $i <= $nbr_datepickers; $i++){ 
							
							/**
							 * Disable auto show option for the second datepicker in filter type "date_range" */
							 
							if($nbr_datepickers == 2 && $i == 2)
								$auto_show = 'false';
							
							/**
							 * Datepicker script */
								
							$new_field_name = $field_name.'_'.str_replace(array(1, 2), array('start', 'end'), $i);
							 		
							$datepicker_js_script .= "						
							$('[name=".$new_field_name."][data-map-id=".$map_id."]').cspmSyncDatepicker({
								language: '".$language."',
								autoShow: ".strval($auto_show).",
								autoHide: ".strval($auto_hide).",
								inline: ".strval($inline).",
								container: 'div.cspml_inline_datepicker_container_".$i."[data-map-id=".$map_id."]',
								trigger: '.cspm_datepicker_trigger_".$map_id."_".$i."', // A element for triggering the datepicker
								date: null, // Initial date								
								format: '".$final_date_format."',
								startDate: '".$start_date."',
								endDate: '".$end_date."',
								startView: ".$start_view.",
								weekStart: ".$week_start.",
								yearFirst: ".strval($year_first).",
								yearSuffix: '".$year_suffix."',
							});";
	
						}
											
						$datepicker_js_script .= "});";
					
                        /**
						 * Load Datepickers language & Add Datepicker script based on the type of the theme
                         *
                         * This is to fix an issue with "Full-site-editing (FSE) / block" themes where it's impossible to ...
                         * ... pass JS data inside a shortcode to an already registred script because in ...
                         * ... FSE themes, shortcode callback will be executed before a plugin ...
                         * ... had a chance to register the script with "wp_enqueue_scripts". ... 
                         * ... The fix will be to enqueue scripts with "add_action" using the hook "wp_enqueue_scripts" in FSE themes ...
                         * ... which will allow our scripts to be executed before the shortcode callback.
                         * In classic themes, shortcode callback will be executed after "wp_enqueue_scripts" and we can call ...
                         * ... our enqueue functions directly with no need for "add_action". Doing like with FSE themes won't work ...
                         * ... for classic themes!
                         *
                         * Note: "wp_script_is()" serves as a fallback for FSE themes, typically no-theme platforms, which cannot be detected using "wp_is_block_theme()"!
                         *
                         * @since 3.7.3
                         */
                
                        if(wp_is_block_theme() || !wp_script_is('cspml-script', 'registered')){
                            add_action('wp_enqueue_scripts', function() use ($language, $datepicker_js_script){
                                wp_enqueue_script('jquery-fengyuan-datepicker_i18n_'.$language);
                                wp_add_inline_script('cspml-script', $datepicker_js_script);
                            });
                        }else{
                            wp_enqueue_script('jquery-fengyuan-datepicker_i18n_'.$language);
                            wp_add_inline_script('cspml-script', $datepicker_js_script);
                        }
                
					$output .= '</div>';	
				
				$output .= '</div>';	
					
			}
			
			return $output;	
					
		}
		
				
		/**
		 * This will build the Posts publish date_query args ...
		 * ... to add to the filter query args.
		 *
		 * [@filter_args] | The selected date(s) returned by the filter form when sending a filter request.
		 *
		 * @since 2.1 
		 */
		function cspml_posts_publish_date_query($filter_args = array()){
							
			$date_query_args = $start_date_attributes = $end_date_attributes = array();
			
			/**
			 * Check if the start date field exists
			 * The array [@filter_args['cspml_posts_date_filter_start']] contains the following:
			 * 1 - The filter type. ("extact_date", "after_date", "before_date" and "date_range")
			 * 2 - The selected start date. */
			 
			if(is_array($filter_args) && array_key_exists('cspml_posts_date_filter_start', $filter_args)){
				
				if(count($filter_args['cspml_posts_date_filter_start']) > 0){
					
					/**
					 * Get Datepicker parameters */
				
					$date_filter_params = (array) unserialize(stripslashes($this->cspml_get_map_option('date_filter_param', serialize(array())))); //@since 2.1
						
					$date_filter_params = isset($date_filter_params[0])
						? (array) $date_filter_params[0]
						: array();
					
					$date_format = $this->cspml_setting_exists('date_format', $date_filter_params, 'ymd');
					$exclude_from_date = $this->cspml_setting_exists('exclude_date_atts', $date_filter_params, array());
					$final_date_format = str_split(
						str_replace($exclude_from_date, '', $date_format), /** 1. Remove attribute ("y" OR "m" OR "d") from date format ("ymd" or "dmy") **/
						1
					); /** 2. Replace [from "ymd" To "array('y', 'm', 'd')"] OR ["dmy" To "array('d', 'm', 'y')"] **/
					
					/**
					 * Get the start date */
					
					if(isset($filter_args['cspml_posts_date_filter_start'][1])){
						
						/**
						 * Explode the start date from "yy/mm/dd" to "array(yy, mm, dd)" */
						 
						$start_date = explode('/', $filter_args['cspml_posts_date_filter_start'][1]);
							
						/**
						 * Combine the date format with the selected date to build ...
						 * ... array of date attibutes.
						 *
						 * Array(
						 * 	[y] => selected year, 
						 * 	[m] => selected month, 
						 * 	[d] => selected day
						 * ) */
						 
						$start_date_array = array_combine($final_date_format, $start_date);
						
						/**
						 * Build the Start "date_query" array
						 *
						 * Array(
						 * 	[year] => selected year, 
						 * 	[month] => selected month, 
						 * 	[day] => selected day
						 * ) */
						 
						foreach($final_date_format as $date_attribute){
							if($date_attribute == 'y')
								$start_date_attributes['year'] = $start_date_array['y'];
							elseif($date_attribute == 'm')
								$start_date_attributes['month'] = $start_date_array['m'];
							elseif($date_attribute == 'd')
								$start_date_attributes['day'] = $start_date_array['d'];
						}
					
						/**
						 * Get end date
						 * Note: If end date exists, this means that the filter type is "data_range (Between two dates)" */
						
						if(isset($filter_args['cspml_posts_date_filter_end'][1])){
						
							/**
							 * Explode the end date from "yy/mm/dd" to "array(yy, mm, dd)" */
														 
							$end_date = (array_key_exists('cspml_posts_date_filter_end', $filter_args))
								? explode('/', $filter_args['cspml_posts_date_filter_end'][1])
								: array();
															
							if(is_array($end_date) && count($end_date) > 0){
								
								/**
								 * Combine the date format with the selected date to build ...
								 * ... array of date attibutes.
								 *
								 * Array(
								 * 	[y] => selected year, 
								 * 	[m] => selected month, 
								 * 	[d] => selected day
								 * ) */
													
								$end_date_array = array_combine($final_date_format, $end_date);
							
								/**
								 * Build the End "date_query" array
								 *
								 * Array(
								 * 	[year] => selected year, 
								 * 	[month] => selected month, 
								 * 	[day] => selected day
								 * ) */
							
								foreach($final_date_format as $date_attribute){
									if($date_attribute == 'y')
										$end_date_attributes['year'] = $end_date_array['y'];
									elseif($date_attribute == 'm')
										$end_date_attributes['month'] = $end_date_array['m'];
									elseif($date_attribute == 'd')
										$end_date_attributes['day'] = $end_date_array['d'];
								}
								
							}
								
						}

						/**
						 * Build the "date_query" args */
						
						if(isset($filter_args['cspml_posts_date_filter_start'][0])){
							
							/**
							 * Get the filter type */
							 											
							$filter_type = $filter_args['cspml_posts_date_filter_start'][0];
						
							if($filter_type == 'exact_date'){
							
								$date_query_args = array(
									$start_date_attributes,
									'compare' => '=',
								);
							
							}elseif($filter_type == 'after_date'){
								
								$date_query_args = array(
									array(
										'after' => $start_date_attributes,
										'inclusive' => false, // Don't get the posts created in the same date in "after" parameters
									)
								);
							
							}elseif($filter_type == 'before_date'){
								
								$date_query_args = array(
									array( 
										'before' => $start_date_attributes,
										'inclusive' => false, // Don't get the posts created in the same date in "before" parameters
									)
								);
								
							}elseif($filter_type == 'date_range'){
								
								$date_query_args = array(
									array(
										'after' => array_merge(
											
											$start_date_attributes,
																						
											/**
											 * Note: set (H:m:s) to 0 (day start) to get post created the same date in "after" parameters ...
											 * ... when attribute "inclusive" is set to TRUE! */
		
											array(																		
												'hour' => '0',
												'minute' => '0',
												'second' => '0',
											)
											
										),
										'before' => array_merge(
											
											$end_date_attributes,
											
											/**
											 * Note: set (H:m:s) to 23:59:59 (day end) to get post created the same date in "before" parameters ...
											 * ... when attribute "inclusive" is set to TRUE! */
											
											array(
												'hour' => '23', 
												'minute' => '59',
												'second' => '59',	
											)
																	
										),
										'inclusive' => true, // Get the post created in the same date in "after" & "before" parameters
										'relation' => 'AND',
										'compare' => 'BETWEEN',
									)
								);
								
							}
							
						}
						
					}
					
				}
				
			}
			
			return $date_query_args;

		}
		
		
		/**
		 * This will display a datepicker in the filter that allows filtering ...
		 * ... posts by a custom field type datepicker
		 * 
		 * @since 2.4
		 */
		function cspml_custom_field_datepicker($field_atts){
			
			$defaults = array(
				'map_id' => '',
				'post_type' => '',
				'post_ids' => '',
				'field_name' => '',
				'field_label' => '',
				'field_description' => '',
				'field_options' => array(),
				'symbol' => '',
				'symbol_position' => 'text',
				'search_all_option' => 'false',
				'search_all_text' => apply_filters('__all', esc_html__('All', 'cspml')),
				'show_count' => '',
				'compare_parameter' => '',
				'default_value' => '',
				'display_status' => 'open',
				'filter_type' => '',
				
				/**
				 * Custom fields Datepicker parameters 
				 * @since 2.4 */
				
				'date_format' => '',
				'date_separator' => '',
				'start_date' => '',
				'end_date' => '',
				'week_start' => '',
			);
			
			extract( wp_parse_args( $field_atts, $defaults ) );
			
			$output = '';
			
			$filter_container = 'list';
				
			$display_status = $display_status;
				
			/**
			 * Get Datepicker parameters */

			$arrow_down_img = apply_filters('cspml_filter_arrow_down_img', $this->plugin_url.'img/svg/arrow-down.svg', str_replace('map', '', $map_id));
			
			$output .= '<div class="cspml_fs_item_container cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12" data-map-id="'.$map_id.'">';
			
				//if($filter_container != "map"){
															
					$output .= '<div class="cspml_fs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'" data-map-id="'.$map_id.'">';
						$output .= '<span class="cspml_label_text">'.$field_label.'</span>';
						$output .= '<span class="cspml_toggle_btn" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'">';
							$output .= '<img src="'.$arrow_down_img.'" style="height:15px; width:auto;" />';
						$output .= '</span>';
					$output .= '</div>';
					
				/*}else{
					
					$output .= '<div class="cspml_mfs_label cspm-row" for="'.$field_name.'" id="'.$field_name.'">';										
						$output .= '<span class="cspml_label_text cspm-col-lg-10 cspm-col-sm-10 cspm-col-xs-11 cspm-col-md-10">'.$field_label.'</span>';
					$output .= '</div>';
					
				}*/
				
				$display_css = ($display_status == 'open') ? '' : ' style="display:none;"';					 
					
				/**
				 * Add Range Datepicker */
					
				$output .= '<div class="cspml_fs_options_list" data-field-name="'.$field_name.'" data-display-location="'.$filter_container.'" data-map-id="'.$map_id.'" '.$display_css.'>';
					
					if(!empty($field_description))
						$output .= '<div class="cspml_field_desc">'.$field_description.'</div>'; //@since 2.4
					
					$output .= '<input type="hidden" name="'.$field_name.'_filter_type" value="custom_field" />';
												
					$output .= '<div class="cspml_input_container">';
						
						/**
						 * Range Datepicker */
							
						$output .= '<input type="text" name="'.$field_name.'" id="date_range_value_'.$field_name.'" data-map-id="'.$map_id.'" value="" style="display:none;">';				
						
						$output .= '<div style="width:100%;">';
							$output .= '<span id="date_range_'.$field_name.'" data-map-id="'.$map_id.'" class="cspml_fs_range_datepicker cspml_type_text_like">'.apply_filters('__select_a_date', esc_html__('Select a date', 'cspml')).'</span>';
						$output .= '</div>';
									
					$output .= '</div>';
					
					/**
					 * Build Range Datepicker script */
		
					$single_date = $single_month = (in_array($compare_parameter, array('IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'))) ? 'false' : 'true';
					 
					$date_separator = str_replace(					
						array('slash', 'dash', 'dot', 'space', 'none'),
						array('/', '-', '.', ' ', ''),
						$date_separator
					);
					
					$explode_date_format = explode('/', $date_format); // From "YYYY/MM/DD" to array("YYYY", "MM", "DD")
					
					$date_format = implode($date_separator, $explode_date_format); // From "array("YYYY", "MM", "DD")" to "YYYY{@date_separator}MM{@date_separator}DD"
					
					$start_and_end_date_format = 'Y'.$date_separator.'m'.$date_separator.'d'; // Default to "Y{@date_separator}m{@date_separator}d"
					
					if(isset($explode_date_format[0], $explode_date_format[2])){
						if($explode_date_format[0] == 'YYYY'){
							$start_and_end_date_format = 'Y'.$date_separator.'m'.$date_separator.'d';
						}elseif($explode_date_format[0] == 'YY'){
							$start_and_end_date_format = 'y'.$date_separator.'m'.$date_separator.'d';
						}elseif($explode_date_format[0] == 'DD' && $explode_date_format[2] == 'YYYY'){
							$start_and_end_date_format = 'd'.$date_separator.'m'.$date_separator.'Y';
						}elseif($explode_date_format[0] == 'DD' && $explode_date_format[2] == 'YY'){
							$start_and_end_date_format = 'd'.$date_separator.'m'.$date_separator.'y';
						}
					}
					
					if(!empty($start_date)){
						$start_date_obj = new DateTime($start_date);
						$start_date = date($start_and_end_date_format, $start_date_obj->getTimestamp());
					}else $start_date = false;
					
					if(!empty($end_date)){
						$end_date_obj = new DateTime($end_date);
						$end_date = date($start_and_end_date_format, $end_date_obj->getTimestamp());
					}else $end_date = false;
					
					$datepicker_js_script = "jQuery(document).ready(function($){";
		
						$datepicker_js_script .= "$('span#date_range_".$field_name."[data-map-id=".$map_id."]').dateRangePicker({
							autoClose: true,
							format: '".$date_format."',
							separator: '".apply_filters('cspml_range_datepicker_separator', ' ... ', $map_id)."',
							language: 'auto',
							startOfWeek: '".$week_start."',
							startDate: '".$start_date."',
							endDate: '".$end_date."',
							time: { enabled: false },
							minDays: 0,
							maxDays: 0,
							inline:false,
							container:'body',
							singleDate:".$single_date.",
							singleMonth:".$single_month.",
							showTopbar: true,
							monthSelect: true,
							yearSelect: true,
							getValue: function(){
								return this.innerHTML;
							},
							setValue: function(s){
								this.innerHTML = s;
								$('input#date_range_value_".$field_name."[data-map-id=".$map_id."]').val(s);
								$('span#open_date_range_".$field_name."[data-map-id=".$map_id."]').hide();
								$('span#clear_date_range_".$field_name."[data-map-id=".$map_id."]').show();
							},
							customArrowPrevSymbol: '&lsaquo;',
							customArrowNextSymbol: '&rsaquo;',																																
						});";
											
					$datepicker_js_script .= "});";
					
					/**
					 * Add Range Datepicker script based on the type of the theme
                     *
                     * This is to fix an issue with "Full-site-editing (FSE) / block" themes where it's impossible to ...
                     * ... pass JS data inside a shortcode to an already registred script because in ...
                     * ... FSE themes, shortcode callback will be executed before a plugin ...
                     * ... had a chance to register the script with "wp_enqueue_scripts". ... 
                     * ... The fix will be to enqueue scripts with "add_action" using the hook "wp_enqueue_scripts" in FSE themes ...
                     * ... which will allow our scripts to be executed before the shortcode callback.
                     * In classic themes, shortcode callback will be executed after "wp_enqueue_scripts" and we can call ...
                     * ... our enqueue functions directly with no need for "add_action". Doing like with FSE themes won't work ...
                     * ... for classic themes!
                     *
                     * Note: "wp_script_is()" serves as a fallback for FSE themes, typically no-theme platforms, which cannot be detected using "wp_is_block_theme()"!
                     *
                     * @since 3.7.3
                     */
            
                    if(wp_is_block_theme() || !wp_script_is('cspml-script', 'registered')){
                        add_action('wp_enqueue_scripts', function() use ($datepicker_js_script){
                            wp_add_inline_script('cspml-script', $datepicker_js_script);
                        });
                    }else{
                        wp_add_inline_script('cspml-script', $datepicker_js_script);
                    }
					
				$output .= '</div>';	
			
			$output .= '</div>';	
			
			return $output;	
					
		}
					
		
		/**
		 * The filter form Query
		 *
		 * @since 1.0
		 */
		function cspml_faceted_search_query(){
			
			if (!class_exists('CspmMainMap'))
				return; 
			
			$CspmMainMap = CspmMainMap::this();
			
			/**
			 * Reload map settings */
			 
			$this->map_settings = (isset($_POST['map_settings'])) ? $_POST['map_settings'] : $this->map_settings;
            unset(
                $this->map_settings[$this->metafield_prefix.'_js_style_array'],
                $this->map_settings[$this->metafield_prefix.'_custom_css']
            ); //@since 3.6 | Fix an issue where the map JS style break all map settings when trying to read them using the function "maybe_unserialize()"				
		            
			if(!isset($_POST['filter_args']) && !isset($_POST['map_id'])){
				$post_ids = array();
				print_r(json_encode($post_ids));
				die();
			}
		
			$post_ids = $tax_query = $custom_fields = $date_query_args = array();
			
			/**
			 * [@filter_args] The selected fields and their values */
			 
			$filter_args = isset($_POST['filter_args']) ? $_POST['filter_args'] : array();
		
			$map_id = esc_attr($_POST['map_id']);
			
			/**
			 * [@post_ids] Post IDs of all post to show */
			 
			$post_ids = isset($_POST['post_ids']) ? $_POST['post_ids'] : array();
			
			/**
			 * [@init_post_ids] Post IDs of all post we've started with before doing any filter */
			 
			$init_post_ids = !empty($_POST['init_post_ids']) ? explode(',', esc_attr($_POST['init_post_ids'])) : array();			
									
			/**
			 * Get The ID Of The Page Where The Shortcode was Executed */
			 
			$shortcode_page_id = esc_attr($_POST['shortcode_page_id']);
			$page_template = esc_attr($_POST['page_template']);
			$template_tax_query  = esc_attr($_POST['template_tax_query']);
			
			$post__in = array();
			
			/**
			 * Usefull for re-executing hooks after AJAX load */
			 
			do_action('cspml_before_faceted_search_query', $shortcode_page_id, $page_template, $template_tax_query);

			if(count($filter_args) > 0){

				/**
				 * Get the post_ids that were the result of the search form in the map ("Progress Map Search").
				 * That way, the faceted search query will take in concediration that it should only search within a given post_ids */
				 
				if(isset($_SESSION['cspml_posts_in_search'][$map_id]['filtering']) && $_SESSION['cspml_posts_in_search'][$map_id]['filtering'] == true && isset($_SESSION['cspml_posts_in_search'][$map_id]['post_ids']))
					$post__in = $_SESSION['cspml_posts_in_search'][$map_id]['post_ids'];
				
				/**
				 * Taxonomies
				 * Note: Do not use the variables initialized in the constructor.
				 * Vars in the constructor cannot be called in an AJAX request! */

				$cspml_taxonomies_array = (array) unserialize(stripslashes($this->cspml_get_map_option('cslf_taxonomies', serialize(array()))));
					$cspml_taxonomies = array();
					foreach($cspml_taxonomies_array as $key => $values){
						$cspml_taxonomies[$values['taxonomy_name']] = $values;
					}
				
				$cspml_taxonomy_relation_param = $this->cspml_get_map_option('cslf_taxonomy_relation_param', 'AND');

				/**
				 * Custom fields
				 * Note: Do not use the variables initialized in the constructor.
				 * Vars in the constructor cannot be called in an AJAX request! */
				 
				$cspml_custom_fields_array = (array) unserialize(stripslashes($this->cspml_get_map_option('cslf_custom_fields', serialize(array()))));
					$cspml_custom_fields = array();
					foreach($cspml_custom_fields_array as $key => $values){
						if(isset($values['custom_field_name']))
							$cspml_custom_fields[$values['custom_field_name']] = $values;
					}
				
				$cspml_custom_field_relation_param = $this->cspml_get_map_option('cslf_custom_field_relation_param', 'AND');

				$taxonomies_array = $meta_query_array = array();
				
				/**
				 * When used in a taxonomy page (archive), we need to add the current tax to the query */
				  
				if(!empty($template_tax_query)){
					
					$explode_template_tax_query = explode(',', $template_tax_query);
					
					if(count($explode_template_tax_query == 2)){
						
						$taxonomies_array[] = array(
							'taxonomy' => $explode_template_tax_query[0], // taxonomy name
							'field' => 'slug', // Compare with term_ids
							'terms' => array($explode_template_tax_query[1]), // Selected values
							'operator' => 'IN', // Operator parameter for select values
						);
											   
					}
					
				}

				/**
				 * Loop throught the fields in the filter form */
				 
				foreach($filter_args as $field_key => $field_values){

					/**
					 * Get the name of the field which in fact could be the name of ...
					 * ... the taxonomy or the custom_field */
					 
					$attr_name = $field_key;
					
					/**
					 * The field values are:
					 * @The field_type (taxonomy or custom_field)
					 * @The selected values (terms, tags, custom field value, ...) */
						
					/**
					 * If the field_values contains more that one value
					 * The first value in the array is always the field_type (taxonomy or custom_field) ...
					 * ... So if there's only the field_type, no need to continue cause there will be no selected values */

					if(count($field_values) > 1){
						
						/**
						 * If the field_type is a taxonomy
						 * Create taxonomy args */
							 
						if(isset($field_values[0]) && $field_values[0] == "taxonomy"){
							
							/**
							 * Get Taxonomy Name */
							 
							$tax_name = $attr_name;
							
							/**
							 * Remove the field_type from the array of selected values */
							 
							unset($field_values[0]);
						
							/**
							 * Get the value of the field from the data registered for its tag in the admin */
							 
							$tag_array_value = (array) $cspml_taxonomies[$tax_name];
					
							/**
							 * Call the taxonomy operator and make sure it exists */

							if(isset($tag_array_value['taxonomy_operator_param'])){	
								
								$operator = $tag_array_value['taxonomy_operator_param'];
								
								/**
								 * Check if the select values array is not empty */
								 
								if(count($field_values) > 0){
									
									$array_compare = array('IN', 'NOT IN', 'AND');
									
									if(in_array($operator, $array_compare))
										$value = array_values(array_unique($field_values));
									else $value = implode('', $field_values);

									if($tag_array_value['taxonomy_display_type'] == "double_slider")
										$value = explode(';', implode('', $value));
									
									if(!empty($value)){
											
										/**
										 * And create the args array for the query */
										 
										$taxonomies_array[] = array(
											'taxonomy' => $tax_name, // taxonomy name
											'field' => 'id', // Compare with term_ids
											'terms' => $value, // Selected values
											'operator' => $operator, // Operator parameter for select values
										);
									
									}
									
								}
							
							}
						
						/**
						 * If the field_type is a custom field
						 * Create custom fields args */
						 
						}elseif(isset($field_values[0]) && $field_values[0] == "custom_field"){
	
							/**
							 * Get custom field Name */
							 
							$custom_field_name = $attr_name;
							
							/**
							 * Remove the field_type from the array of selected values */
							 
							unset($field_values[0]);
							
							/**
							 * Get the value of the field from the data registered for its tag in the admin */
							 
							$tag_array_value = (array) $cspml_custom_fields[$custom_field_name];
				            
                            /**
                             * Build the meta query args for serialized custom fields 
                             * @since 3.6 */
                            
                            if(isset($tag_array_value['custom_field_serialized']) && $tag_array_value['custom_field_serialized'] == 'yes'){
								
								/**
								 * Check if the select values array is not empty */
								 
								if(count($field_values) > 0){
                                    
                                    $selected_values = array_values(array_unique($field_values));
										
                                    /**
                                     * Create the args array for WP Query */

                                    if(count($selected_values) > 1){ 
                                        $serialized_field_query = array('relation' => 'OR');
                                        foreach($selected_values as $selected_value){
                                            $serialized_field_query[] = array(
                                                'key' => $custom_field_name, // custom field name
                                                'value' => '"'.$selected_value.'"', // Selected values
                                                'compare' => 'LIKE', // Operator parameter for select values
                                            );
                                        }
                                        $meta_query_array[] = $serialized_field_query;
                                    }else{
                                        $meta_query_array[] = array(
                                            'key' => $custom_field_name, // custom field name
                                            'value' => (isset($selected_values[0])) ? '"'.$selected_values[0].'"' : '"'.$selected_values.'"', // Selected values
                                            'compare' => 'LIKE', // Operator parameter for select values
                                        );
                                    }
							
                                    
                                }
                                
							/**
							 * Build the meta query args for the other custom fields */
							 
                            }elseif(isset($tag_array_value['custom_field_compare_param'])){ // Call the custom field operator and make sure it exists
								
								$compare = $tag_array_value['custom_field_compare_param'];
								
								/**
								 * Check if the select values array is not empty */
								 
								if(count($field_values) > 0){
				
									$array_compare = array('IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN');
									
									if(in_array($compare, $array_compare)){
										$value = array_values(array_unique($field_values));
									}else $value = implode('', $field_values);
									
									if($tag_array_value['custom_field_display_type'] == "double_slider"){
										$value = explode(';', implode('', $value));
									}elseif($tag_array_value['custom_field_display_type'] == "datepicker" && in_array($compare, $array_compare)){ //@since 2.4
										$value = explode(apply_filters('cspml_range_datepicker_separator', ' ... ', $map_id), implode('', $value)); //@since 2.4
                                    }
                                    
									if(!empty($value)){
										
										$args = array(
											'key' => $custom_field_name, // custom field name
											'value' => $value, // Selected values
											'compare' => $compare, // Operator parameter for select values
										);
										
										$numeric_fields = array('double_slider', 'single_slider', 'number');
										
										if(in_array($tag_array_value['custom_field_display_type'], $numeric_fields)){
											$args = array_merge($args, array('type' => 'NUMERIC'));
										}elseif($tag_array_value['custom_field_display_type'] == 'datepicker'){
											$args = array_merge($args, array('type' => 'DATE'));
										}
													
										/**
										 * And create the args array for the query */
										 
										$meta_query_array[] = $args;
									
									}
									
								}
							
							}
							
						}
						
					}
					
				}
					
				/**
				 * At the end, collect all taxonomies and custom fields args array for the query */
				 
				$tax_query = (count($taxonomies_array) == 0) ? array() : array('tax_query' => array_merge(array('relation' => $cspml_taxonomy_relation_param), $taxonomies_array));
				$custom_fields = (count($meta_query_array) == 0) ? array() : array('meta_query' => array_merge(array('relation' => $cspml_custom_field_relation_param), $meta_query_array));		
				
				/**
				 * Get the "date_query" args based on the field "Posts publish date"
				 * @since 2.1 */
				 
				$date_query_args = $this->cspml_posts_publish_date_query($filter_args);
				$custom_fields = (count($meta_query_array) == 0) ? array() : array('meta_query' => array_merge(array('relation' => $cspml_custom_field_relation_param), $meta_query_array));		
				 
			}

			if(count($init_post_ids) > 0){

				/**
				 * post status */
				 
				$status = unserialize(stripslashes($this->cspml_get_map_option('items_status', serialize('publish'))));
				
				/**
				 * Query */
				 
				$query_args = array(
					'post_type' => $this->cspml_get_map_option('post_type'), 
					'post_status' => $status,
					'post__in' => (is_array($post__in) && count($post__in) > 0) ? $post__in : $init_post_ids,
					'posts_per_page' => -1,
					'orderby' => 'post__in',							
					'fields' => 'ids',
				);
				
				/**
				 * Get the keyword term from the field "keyword"
				 * @since 2.2 */
				
				if(is_array($filter_args) && array_key_exists('cspml_keyword', $filter_args)){
					if($this->cspml_setting_exists(1, $filter_args['cspml_keyword']))
						$query_args['s'] = $filter_args['cspml_keyword'][1];
				}
				
				/**
				 * Add "date_query" args
				 * @since 2.1
				 */
				
				if(is_array($date_query_args) && count($date_query_args) > 0){
					$query_args['date_query'] = $date_query_args;
				}

				/**
				 * Taxonomies query args */
				 
				if(count($tax_query) > 0 && isset($tax_query['tax_query'])) $query_args = array_merge($query_args, array('tax_query' => $tax_query['tax_query']));
				
				/**
				 * Custom fields query args */
				 
				if(count($custom_fields) > 0 && isset($custom_fields['meta_query'])){ 
					$query_args = array_merge(
						$query_args, 
						array('meta_query' => $custom_fields['meta_query'])				
					);			
				}						

				/**
				 * Add Progress Map Custom Fields to the query */
				
				$cspm_extra_custom_fields = unserialize(stripslashes($this->cspml_get_map_option('custom_fields', serialize(array()))));

				if(!empty($cspm_extra_custom_fields)){
					
					$cspm_custom_fields = $CspmMainMap->cspm_parse_query_meta_fields($cspm_extra_custom_fields, $this->cspml_get_map_option('custom_field_relation_param'), $this->cspml_get_map_option('optional_latlng')); //@edited 3.5
					
					if(count($custom_fields) == 0 && count($cspm_custom_fields) > 0){		
									
						$query_args = array_merge(
							$query_args,
							array('meta_query' => $cspm_custom_fields['meta_query'])
						);
						
					}elseif(count($custom_fields) > 0 && count($cspm_custom_fields) > 0){
						
						unset($cspm_custom_fields['meta_query']['relation']);
						
						$custom_field_query = array_merge(
							$query_args['meta_query'],
							$cspm_custom_fields['meta_query']
						);
						
						$query_args['meta_query'] = $custom_field_query;
						
					}
				}
                
				$the_query = new WP_Query($query_args);
				$post_ids = $the_query->posts;
				wp_reset_postdata(); 

			}else $post_ids = $init_post_ids;
			
			/**
			 * Save the post_ids in a session in order not to start ...
			 * ... calling all posts when paginating or sorting by or filtering */
			 
			$_SESSION['cspml_listings_filter_post_ids'][$map_id] = $post_ids;

			print_r(json_encode($post_ids));
			
			die();
			
		}
		
		
		/**
		 * Count the number of posts per term
		 *
		 * @since 1.0
		 */
		function cspml_count_term_posts($taxonomy, $term_id, $post_type, $post_ids = array()){
					
			if (!class_exists('CspmMainMap'))
				return; 
			
			$CspmMainMap = CspmMainMap::this();
				
			/**
			 * post status */
			 
			$status = (is_array($CspmMainMap->post_status) && count($CspmMainMap->post_status) == 0) ? 'publish' : $CspmMainMap->post_status; //@edited 3.7.3

			if(count($post_ids) > 0){
																	   
				$posts = get_posts(array(
					'post_type' => $post_type,
					'post_status' => $status,
					'tax_query' => array(
						array(
							'taxonomy' => $taxonomy,
							'field' => 'id',
							'terms' => $term_id
						)
					),
					'post__in' => $post_ids,
					'fields' => 'ids',
					'posts_per_page' => -1,
					'suppress_filters' => false,
				)); 

				wp_reset_postdata();
	
			}else $posts = array();
			
			return count($posts);
		
		}
		
		
		/**
		 * Count the number of posts by custom field value
		 *
		 * @since 1.0
         * @edited 3.6
		 */
		function cspml_count_custom_field_posts($post_type, $custom_field_name, $post_ids = array()){
						
			if (!class_exists('CspmMainMap'))
				return; 
			
			$CspmMainMap = CspmMainMap::this();
				
			/**
			 * post status */
			 
			$status = (is_array($CspmMainMap->post_status) && count($CspmMainMap->post_status) == 0) ? 'publish' : $CspmMainMap->post_status; //@edited 3.7.3
			
			$custom_field_values = $this->cspml_get_meta_values(array(
                'key' => $custom_field_name,
                'post_type' => $post_type,
                'status' => $status,
                'post_in' => $post_ids,
            )); // @edited 3.6
            
			$meta_counts = array();
			
			if(!empty($custom_field_values)){

                $meta_counts = array();
				
				foreach($custom_field_values as $meta_value){
                    
                    $readable_options = array();
                    
                    if(is_serialized($meta_value)){
                        $unserialized_value = maybe_unserialize($meta_value);
                        if(is_array($unserialized_value)){
                            foreach($unserialized_value as $val){
                                $readable_options[] = $val;
                            }
                        }
                    } //@since 3.6
					
                    if(count($readable_options) > 0){
                        foreach($readable_options as $meta_value){
                            $meta_counts[$meta_value] = (isset($meta_counts[$meta_value])) ? $meta_counts[$meta_value] + 1 : 1;
                        }
                    }else $meta_counts[$meta_value] = (isset($meta_counts[$meta_value])) ? $meta_counts[$meta_value] + 1 : 1;
					
				}
				
			}
			
			return $meta_counts;
			
		}
		
		
		/**
		 * Get All Values For A Custom Field Key 
		 *
		 * @key: The meta_key of the post meta
		 * @type: The name of the custom post type
		 * @status: The status of the post
		 * 
		 * @since 1.0
		 * @updated 1.2 | 3.6
		 */
		function cspml_get_meta_values($atts = array()){
			
			/**
			 * Function atts */
			 
			extract( wp_parse_args( $atts, array(				
				'key' => '',		
				'post_type' => 'post',
				'status' => 'publish',
				'orderby' => 'pm.meta_value',
				'order' => 'ASC',
				'post_in' => '',
			)));		
            
			global $wpdb;
			
			if( empty( $key ) )
				return;
			
			/**
			 * [@status_cndt] Different SQL condition depending on the number of selected status!
			 * @since 1.2 */
			  
			if(is_array($status)){
				$sql_statuses = implode("','", $status);
				$sql_statuses = "'".$sql_statuses."'";
				$status_cndt = "(p.post_status IN (".$sql_statuses."))";
			}else $status_cndt = "p.post_status = '".$status."' ";
			
			if(!empty($post_in)){
				$post_in_cndt = 'AND p.ID IN (' . ((is_array($post_in) && count($post_in) > 0) ? implode(',', $post_in) : $post_in) . ')';
			}else $post_in_cndt = ''; //@since 3.6
			
			$results = $wpdb->get_col( $wpdb->prepare( "
				SELECT pm.meta_value FROM {$wpdb->postmeta} pm
				LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = '%s' 
				AND pm.meta_value != '' 
				AND ".$status_cndt."
				AND p.post_type = '%s' 
				".$post_in_cndt." 
				ORDER BY ".$orderby." ".$order."
			", $key, $post_type ) ); //@edited 3.6
			
			return $results;
			
		}
		
        
        /**
         * This will convert a serialized option(s) to humain readable option(s)
         *
         * @since 3.6 
         */
        function cspml_convert_serialized_values_to_unique_options($options, $options_order = 'ASC'){
            
            if(!is_array($options))
                return $options;
            
            $readable_options = array();

            foreach($options as $value){
                if(is_serialized($value)){
                    $unserialized_value = maybe_unserialize($value);
                    if(is_array($unserialized_value)){
                        foreach($unserialized_value as $val){
                            $readable_options[] = $val;
                        }
                    }
                }
            }

            $unique_options = (count($readable_options) > 0) ? array_unique($readable_options) : $options;
            
            return $unique_options;
            
        }
        
			
		/**
		 * Get the link of the post either with the get_permalink() function ...
		 * ... or the custom field defined by the user
		 *
		 * Note: This function must be used instead of "cspm_get_permalink()" in "Progress Map" 'cause ...
		 * ... AJAX won't be able get the "single_posts_link" from when filtering posts!
		 *
		 * @since 3.4.4
		 */
		function cspml_get_permalink($post_id){

			$outer_links_field_name = isset($this->plugin_settings['outer_links_field_name']) ? $this->plugin_settings['outer_links_field_name'] : '';
			$single_posts_link = $this->cspml_get_map_option('single_posts_link');
			
			/**
			 * Note: Make sure to test if [@single_posts_link] so we can tell if it's a light map or not!
			 * If is "custom", this means the main map!
			 * If EMPTY, this means a light map! */
			 
			if(!empty($outer_links_field_name) && ($single_posts_link == 'custom' || empty($single_posts_link))){
				$the_permalink = get_post_meta($post_id, $outer_links_field_name, true);			
			}else $the_permalink = get_permalink($post_id);
			
			return $the_permalink;
			
		}
  
	}
	
}	
