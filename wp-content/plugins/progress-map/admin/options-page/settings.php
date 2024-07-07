<?php

/**
 * This class contains all the fields used for the plugin settings 
 *
 * @version 1.0 
 */
 
if(!defined('ABSPATH')){
    exit; // Exit if accessed directly
}

if( !class_exists( 'CspmSettings' ) ){
	
	class CspmSettings{
		
		private $plugin_path;
		private $plugin_url;
		
		private static $_this;	
		
		public $object_type;
		protected $options_key;
		protected $plugin_version;
		
		public $plugin_settings = array(); //@since 5.2
				
		public $toggle_before_row;
		public $toggle_after_row;
		
		public $countries_list; //@since 5.6
						
		function __construct($atts = array()){
			
			extract( wp_parse_args( $atts, array(
				'plugin_path' => '', 
				'plugin_url' => '',
				'object_type' => '',
				'option_key' => '',
				'version' => '',
				'plugin_settings' => array(), //@since 5.2
			)));
             
			self::$_this = $this;       
				           
			$this->plugin_path = $plugin_path;
			$this->plugin_url = $plugin_url;
			$this->object_type = $object_type;
			$this->options_key = $option_key;
			$this->plugin_version = $version;
			$this->plugin_settings = $plugin_settings; //@since 5.2
				
			/**
			 * Include all required Libraries for this metabox */
			 
			$libs_path = array(
				'cmb2' => 'admin/libs/metabox/init.php',
				'cmb2-tabs' => 'admin/libs/metabox-tabs/cmb2-tabs.class.php',
				//'cmb2-conditional' => 'admin/libs/metabox-conditionals/cmb2-conditionals.php',				
				'cmb2-radio-image' => 'admin/libs/metabox-radio-image/metabox-radio-image.php',
				'cs-button' => 'admin/libs/metabox-cs-button-field/cs-button-field.php',
				'cmb2-cs-gmaps' => 'admin/libs/metabox-cs-gmaps/cmb-cs-gmaps.php', //@since 5.2
				'cmb2-cs-gmaps-drawing' => 'admin/libs/metabox-cs-gmaps-drawing/cmb-cs-gmaps-drawing.php', //@since 5.2	
				'cmb2-field-select2' => 'admin/libs/metabox-field-select2/cmb-field-select2.php', //@since 5.6			
			);
				
				foreach($libs_path as $lib_file_path){
					if(file_exists($this->plugin_path . $lib_file_path))
						require_once $this->plugin_path . $lib_file_path;
				}

			
			add_action('cmb2_admin_init', array($this, 'cspm_settings_page'));
			
			/**
			 * Call .js and .css files */
			 
			add_filter( 'cmb2_enqueue_js', array($this, 'cspm_scripts') );				
			
			$this->countries_list = $this->cspm_get_countries(); //@since 5.6
			
			$this->toggle_before_row = '<div class="postbox cmb-row cmb-grouping-organizer closed">
									 	<div class="cmbhandle" title="Click to toggle"><br></div>               
									 	<h3 class="cmb-group-organizer-title cmbhandle-title" style="padding: 11px 15px !important;">[title]</h3>
										<div class="inside">';
													
			$this->toggle_after_row = '</div></div>';

		}
	

		static function this() {
			
			return self::$_this;
		
		}

	
		function cspm_scripts(){
			
			if(isset($_GET['page']) && $_GET['page'] == $this->options_key){
				
				/**
				 * Our custom metaboxes CSS */
				
				wp_register_style('cspm-metabox-css', $this->plugin_url . 'admin/options-page/css/options-page-style.css');
				wp_enqueue_style('cspm-metabox-css');
				
				/**
				 * Hide the menu "Settings" created by CMB2 */
				
				wp_add_inline_style('cspm-metabox-css', 'li#toplevel_page_cspm_default_settings > ul.wp-submenu li.current:not(.wp-first-item){display:none;}');	

			}
			
		}
	
		
		/**
		 * "Progress Map" settings page.
		 * This will contain all the default settings needed for "Progress Map"
		 *
		 * @since 1.0
		 */
		function cspm_settings_page(){
			
			$cspm_settings_page_options = array(
				'id' => 'cspm_settings_page',
				'title' => esc_html__('Progress Map', 'cspm'),
				'description' => 'description',
				'object_types' => array('options-page'),
				
				/*
				 * The following parameters are specific to the options-page box
				 * Several of these parameters are passed along to add_menu_page()/add_submenu_page(). */
				
				'option_key' => $this->options_key,
				'menu_title' => esc_html__('Settings', 'cspm'), // Falls back to 'title' (above).
				'icon_url' => $this->plugin_url.'admin/options-page/img/menu-icon.png',				
				'parent_slug' => 'cspm_default_settings', // Make options page a submenu item of the themes menu.
				'capability' => 'manage_options', // Cap required to view options-page.
				//'position' => '99.2', // Menu position. Only applicable if 'parent_slug' is left empty.
				//'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
				'display_cb'   => array($this, 'cspm_settings_page_output'), // Override the options-page form output (CMB2_Hookup::options_page_output()).
				//'save_button' => esc_html__( 'Save Theme Options', 'cspm' ), // The text for the options-page save button. Defaults to 'Save'.
				//'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
				//'message_cb' => 'yourprefix_options_page_message_callback',				
			);
		
			/**
			 * Create Progress Map settings page */
				 
			$cspm_settings_page = new_cmb2_box($cspm_settings_page_options);

			/**
			 * Display Progress Map settings fields */
		
			$this->cspm_settings_tabs($cspm_settings_page, $cspm_settings_page_options);
			
		}
		
		/** 
		 * This contains the HTML structre of the plugin settings page
		 *
		 * @since 1.0
		 */
		function cspm_settings_page_output($settings_page_obj){
        	
			echo '<div class="wrap cmb2-options-page option-'.$settings_page_obj->option_key.'">';
				
				/**
				 * Header */
				 
				echo $this->cspm_settings_page_header();
				
				echo '<div class="cspm_body">';
				
					echo '<form class="cspm-settings-form" action="'.esc_url(admin_url('admin-post.php')).'" method="POST" id="'.$settings_page_obj->cmb->cmb_id.'" enctype="multipart/form-data" encoding="multipart/form-data">';
						
						echo '<input type="hidden" name="action" value="'.esc_attr($settings_page_obj->option_key).'">';
						
						submit_button(esc_attr($settings_page_obj->cmb->prop('save_button')), 'primary', 'submit-cmb');
					
						$settings_page_obj->options_page_metabox();
						
						submit_button(esc_attr($settings_page_obj->cmb->prop('save_button')), 'primary', 'submit-cmb');
					
					echo '</form>';
								
				echo '</div>';
					
				/**
				 * Sidebar */
				 
				echo $this->cspm_settings_page_widget();
				
				/**
				 * Footer */
				 
				echo $this->cspm_settings_page_footer();
				
            echo '</div>';
				
			echo '<div style="clear:both;"></div>';
			
		}
		
		
		/**
		 * Plugin settings page header
		 *
		 * @since 1.0
		 */
		function cspm_settings_page_header(){
			
			$output = '<div class="cspm_header"><img src="'.$this->plugin_url.'admin/options-page/img/progress-map.png" /></div>';
			
			/**
			 * Inform about the new pricing changes of Google Maps
			 * @since 3.8.1 */

			global $current_user;	
    	 
		    $user_id = $current_user->ID;
	
			if(version_compare($this->plugin_version, '4.5', '>=') && current_user_can('activate_plugins')){
					
				echo '<div class="notice notice-info" style="background: #fff; color: #333;">
					<p><strong>Reminder:</strong> New Google Maps changes went into effect on July 16, 2018. Check out <a href="https://codespacing.com/important-updates-in-google-maps/" target="_blank" class="cspm_blank_link">this post</a> if you\'re not already aware.</p>
				</div>';
					
			}else if(get_user_meta($user_id, 'cspm_dismiss_gmaps_updates_notice') && current_user_can('activate_plugins')){
				
				$output .= '<div class="notice notice-error" style="background: #fff3e0; color: #dd2c00; width:100%; box-sizing:border-box; margin:0; box-shadow: none; border-left-width: 2px;">
					<p><strong>Reminder:</strong> To use Google Maps APIs:</p>
					<ul style="padding-left:15px;">
						<li><strong>. Starting June 11, 2018:</strong> All Google Maps Platform (GMP) API requests must include an API key; GMP no longer support keyless access.</li>
						<li><strong>. Starting July 16, 2018:</strong> You\'ll need to enable billing on each of your projects.</li>			
					</ul>
					<p><strong>Show <a href="'.add_query_arg('cspm_dismiss_gmaps_updates_notice', 0, $_SERVER['REQUEST_URI']).'" style="color: #dd2c00; text-decoration:underline;">the announcement</a> for more information.</strong></p>
				</div>';
				
			}
			
			return $output;
			
		}
		
		
		/**
		 * Plugin settings page sidebar
		 *
		 * @since 1.0
		 */
		function cspm_settings_page_widget(){
			
			if(class_exists('ProgressMapList') 
              && class_exists('CspmNearbyMap')
              && class_exists('CspmSubmitLocations')
              && class_exists('CspmDrawSearch')
              && class_exists('ProgressMapAdvancedSearch')
              && class_exists('CspmKeywordSearch')
              && class_exists('CspmMarkersFilter')
              && class_exists('CspmDirections'))
				return '';
			
			$output = '<div class="cspm-settings-widget">';
				
				$output .= '<div class="cspm_widget">';
				
					$output .= '<h3>Extend "Progress Map" with these awesome and powerful add-ons:</h3>';
					
					$output .= '<div class="widget_content">';
					
						/**
						 * Progress Map List & Filter Ban */
						
						if(!class_exists('ProgressMapList'))
							$output .= '<div style="float:left; margin-right:20px;"><a target="_blank" class="cspm_blank_link" href="https://codecanyon.net/item/progress-map-list-filter-wordpress-plugin/16134134?ref=codespacing"><img src="'.$this->plugin_url.'admin/options-page/img/list-and-filter-thumb.jpg" /></a></div>';
						
						/**
						 * Markers Filter Ban */
						
						if(!class_exists('CspmMarkersFilter'))
							$output .= '<div style="float:left; margin-right:20px;"><a target="_blank" class="cspm_blank_link" href="#"><img src="'.$this->plugin_url.'admin/options-page/img/markers-filter-thumb.png" /></a></div>';
						
						/**
						 * Nearby Places Ban */
						
						if(!class_exists('CspmNearbyMap'))
							$output .= '<div style="float:left; margin-right:20px;"><a target="_blank" class="cspm_blank_link" href="https://codecanyon.net/item/nearby-places-wordpress-plugin/15067875?ref=codespacing"><img src="'.$this->plugin_url.'admin/options-page/img/nearby-places-thumb.png" /></a></div>';
						
						/**
						 * Submit Locations Ban */
						
						if(!class_exists('CspmSubmitLocations'))
							$output .= '<div style="float:left; margin-right:20px;"><a target="_blank" class="cspm_blank_link" href="https://codecanyon.net/item/progress-map-submit-location-wordpress-plugin/21974786?ref=codespacing"><img src="'.$this->plugin_url.'admin/options-page/img/submit-locations-thumb.jpg" /></a></div>';
						
						/**
						 * Draw a Search Ban */
						
						if(!class_exists('CspmDrawSearch'))
							$output .= '<div style="float:left; margin-right:20px;"><a target="_blank" class="cspm_blank_link" href="https://codecanyon.net/item/progress-map-draw-a-search-wordpress-plugin/22775848?ref=codespacing"><img src="'.$this->plugin_url.'admin/options-page/img/draw-search-thumb.jpg" /></a></div>';
						
						/**
						 * Advanced Search Ban */
						
						if(!class_exists('ProgressMapAdvancedSearch'))
							$output .= '<div style="float:left; margin-right:20px;"><a target="_blank" class="cspm_blank_link" href="https://codecanyon.net/item/progress-map-advanced-search/23796651"><img src="'.$this->plugin_url.'admin/options-page/img/advanced-search-thumb.jpg" /></a></div>';
						
						/**
						 * Keyword Search Ban */
						
						if(!class_exists('CspmKeywordSearch'))
							$output .= '<div style="float:left; margin-right:20px;"><a target="_blank" class="cspm_blank_link" href="https://codecanyon.net/item/progress-map-keyword-search/27084500"><img src="'.$this->plugin_url.'admin/options-page/img/keyword-search-thumb.jpg" /></a></div>';
						
						/**
						 * Directions Ban */
						
						if(!class_exists('CspmDirections'))
							$output .= '<div style="float:left; margin-right:20px;"><a target="_blank" class="cspm_blank_link" href="https://codecanyon.net/item/progress-map-directions/32352409"><img src="'.$this->plugin_url.'admin/options-page/img/directions-thumb.jpg" /></a></div>';
												
						$output .= '<div style="clear:both;"></div>';
				
					$output .= '</div>';
				
				$output .= '</div>';
				
			$output .= '</div>';
			
			$output .= '<div style="clear:both;"></div>';
			
			return $output;
			
		}
		
				
		/**
		 * Plugin settings page footer
		 *
		 * @since 1.0
		 */
		function cspm_settings_page_footer(){
			
			$output = '<div class="cspm_footer">';
			
				$output .= '<div class="cspm_rates_fotter">
					<a target="_blank" class="cspm_blank_link" href="https://codecanyon.net/item/progress-map-wordpress-plugin/5581719">
						<img src="'.$this->plugin_url.'admin/options-page/img/rates.jpg" />
					</a>
				</div>';
								
				$output .= '<div style="clear:both;"></div>';
				
				$output .= '<div class="cspm_copyright">&copy; All rights reserved <a target="_blank" class="cspm_blank_link" href="https://codespacing.com">CodeSpacing</a>. Progress Map '.$this->plugin_version.'</div>';
			
			$output .= '</div>';
			
			return $output;
			
		}
		
		
		/**
		 * Buill all the tabs that contains "Progress Map" settings
		 *
		 * @since 1.0
		 */
		function cspm_settings_tabs($metabox_object, $metabox_options){
			
			/**
			 * Setting tabs */
			 
			$tabs_setting = array(
				'args' => $metabox_options,
				'tabs' => array()
			);
				
				/**
				 * Tabs array */
				 
				$cspm_tabs = array(
					
					/**
				 	 * Plugin Settings */
					 					
					array(
						'id' => 'plugin_settings', 
						'title' => 'Plugin settings', 
						'callback' => 'cspm_plugin_settings_fields'
					),
					
					/**
				 	 * Default Map Settings */
					 					
					array(
						'id' => 'map_settings', 
						'title' => 'Default Map settings', 
						'callback' => 'cspm_map_fields'
					),
					
					/**
				 	 * Default Map Style Settings */
					 					
					array(
						'id' => 'map_style_settings', 
						'title' => 'Default Map style settings', 
						'callback' => 'cspm_map_style_fields'
					),
					
					/**
					 * Default Marker settings */
					 
					array(
						'id' => 'marker_settings', 
						'title' => 'Default Marker settings', 
						'callback' => 'cspm_marker_fields'
					),
					
					/**
					 * Default Clustering settings */
					 
					array(
						'id' => 'clustering_settings', 
						'title' => 'Default Marker Clustering settings', 
						'callback' => 'cspm_clustering_fields'
					),
					
					/**
					 * Default Geotargeting settings */
					 					
					array(
						'id' => 'geotargeting_settings', 
						'title' => 'Default Geo-targeting settings', 
						'callback' => 'cspm_geotargeting_fields'
					),
					
					/**
				 	 * Default Infobox Settings */
					 					
					array(
						'id' => 'infobox_settings', 
						'title' => 'Default Infobox settings', 
						'callback' => 'cspm_infobox_fields'
					),
					
					/**
				 	 * Autocomplete Settings
					 * @since 5.6 */
					 					
					array(
						'id' => 'autocomplete_settings', 
						'title' => 'Default Autocomplete settings', 
						'callback' => 'cspm_autocomplete_fields'
					),

					/**
				 	 * Customize */
					 										
					array(
						'id' => 'customize', 
						'title' => 'Customize', 
						'callback' => 'cspm_customize_fields'
					),

					/**
				 	 * Troubleshooting & configs */
					 										
					array(
						'id' => 'troubleshooting', 
						'title' => 'Troubleshooting', 
						'callback' => 'cspm_troubleshooting_fields'
					),
					
				);
				
				foreach($cspm_tabs as $tab_data){
				 
					$tabs_setting['tabs'][] = array(
						'id'     => 'cspm_' . $tab_data['id'],
						'title'  => '<span class="cspm_tabs_menu_image"><img src="'.$this->plugin_url.'admin/img/'.str_replace('_', '-', $tab_data['id']).'.png" style="width:20px;" /></span> <span class="cspm_tabs_menu_item">'.esc_attr__( $tab_data['title'], 'cspm' ).'</span>',						
						'fields' => call_user_func(array($this, $tab_data['callback'])),
					);
		
				}
			
			/**
			 * Set tabs */
			 
			$metabox_object->add_field( array(
				'id'   => 'cspm_pm_settings_tabs',
				'type' => 'tabs',
				'tabs' => $tabs_setting,
				'options_page' => true,
			) );
			
			return $metabox_object;
			
		}
		
		
		/**
		 * Map Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_plugin_settings_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Plugin Settings',
				'desc' => '',
				'type' => 'title',
				'id'   => 'plugin_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => 'post_types_section',
				'name' => 'Post Types Parameters',
				'desc' => 'Select the post types for which Progress Map should be available during post creation/edition.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),				
			);
			
				$fields[] = array(
					'id' => 'post_types',
					'name' => 'Post types',
					'desc' => 'Select the post types for which Progress Map should be available during post creation/edition. (Default: '.esc_attr__('Posts').').',
					'type' => 'multicheck',
					'options' => $this->cspm_get_registred_cpts(),
					'sanitization_cb' => function($value, $field_args, $field){
						if(is_array($value) && count($value) > 0)
							return $value;
						else return array('');
					}					
				);
				
			$fields[] = array(
				'id' => 'form_fields_section',
				'name' => '"Add location" Form fields',
				'desc' => 'IMPORTANT NOTE!<br /><br />This feature is dedicated ONLY for users that want to use the plugin with their already created cutsom fields.
						   You don\'t have any interest to change the name of the form fields below if you will use the plugin with a new post type/website. Just leave them as they are!
						   <br />The "Add location" form is located in the "Add/Edit post" page of your post type.<br /><br />
						   *After CHANGING the Latitude & Longitude fields names, 
						   SAVE your settings, then use the options "Troubleshooting => Regenerate markers" recreate your markers.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),				
			);
			
				$fields[] = array(
					'id' => 'latitude_field_name',
					'name' => '"Latitude" field name',
					'desc' => 'Enter the name of your latitude custom field. Empty spaces are not allowed!',
					'type' => 'text',
					'default' => 'codespacing_progress_map_lat',
				);
				
				$fields[] = array(
					'id' => 'longitude_field_name',
					'name' => '"Longitude" field name',
					'desc' => 'Enter the name of your longitude custom field. Empty spaces are not allowed!',
					'type' => 'text',
					'default' => 'codespacing_progress_map_lng',
				);
			
			$fields[] = array(
				'id' => 'misc_fields_section',
				'name' => 'Other parameters',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),				
			);
			
				$fields[] = array(
					'id' => 'outer_links_field_name',
					'name' => '"Outer links" custom field name',
					'desc' => 'By default, the plugin uses the function get_permalink() of wordpress to get the posts links. <strong>In some cases, users wants to use their locations
							   in the map as links to other pages in outer websites.</strong> To use this option, you MUST have your external <strong>links stored inside a custom field</strong>. Enter the name of that
							   custom field or leave this field empty if you don\'t need this option.',
					'type' => 'text',
					'default' => '',
				);
				
				$fields[] = array(
					'id' => 'custom_list_columns',
					'name' => 'Display the coordinates column',
					'desc' => 'This will display a custom column for all custom post types used with the plugin indicating the coordinates of each post on the map.',
					'type' => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				);
				
				$fields[] = array(
					'id' => 'use_with_wpml',
					'name' => 'Use the plugin with WPML',
					'desc' => 'If you are using WPML plugin for translation, select "Yes" to enable the WPML compatibility code.<br />
							  <span style="color:red"><strong>Note:</strong> After duplicating a post to other languages, you must click one more time on the button "Update" of the original 
							  post in order to add the LatLng coordinates for the duplicated posts on the map.</span>',
					'type' => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				);		

			return $fields;
			
		}
		
		
		/**
		 * Map Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_map_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Default Map Settings',
				'desc' => 'These are the default map settings that are going to be used as the main settings of all single maps and as the default settings of your new maps. 
						   You can always override the default map settings when creating/editing a map.',
				'type' => 'title',
				'id'   => 'map_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
		
			$fields[] = array(
				'id' => 'api_key',
				'name' => 'API Key',
				/**
				 * https://developers.google.com/maps/signup?csw=1#google-maps-javascript-api */
				'desc' => 'Enter your Google Maps API key. 
						  <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key" target="_blank" class="cspm_blank_link">Get an API key</a>',
				'type' => 'text',
				'default' => '',
				'before_row' => str_replace(
					array('[title]'), 
					array('Map API Key & Language'), 
					$this->toggle_before_row.'<p class="cmb2-metabox-description" style="margin:0 0 10px 0; padding-top:0;">Enter the maps API Key and/or set the maps language.</p><hr />'
				),																
			);
				
			$fields[] = array(
				'id' => 'map_language',
				'name' => 'Map language',
				'desc' => 'Localize your Maps API application by altering default language settings. <br />See also the <a href="https://developers.google.com/maps/faq#languagesupport" target="_blank" class="cspm_blank_link">supported list of languages</a>.',
				'type' => 'text',
				'default' => 'en',
				'after_row' => $this->toggle_after_row,			
			);
						
			$fields[] = array(
				'name' => 'Map center',
				'desc' => 'Enter the center point of the map. (Latitude then Longitude separated by comma). 
						  Use the search form & the map above or refer to <a href="https://maps.google.com/" target="_blank" class="cspm_blank_link">https://maps.google.com/</a> to get you center point. <br /><br />
						  <strong style="font-size:15px; display: inline-block; padding: 5px 0;">How to search?</strong><br />
						  If you already know the coordinates to use, simply paste them in the field <u>"Latitude & Longitude"</u>, otherwise, follow these steps:<br />
						  <strong>1.</strong> Enter an address in the search field above, then, click on the button <u>"Search"</u>. You can also geolocate your position!<br />
						  <strong>2.</strong> If necessary, drag the marker to find the exact location.<br />						  
						  <strong>3.</strong> Click on the button <u>"Set map center"</u> to copy the coordinates.',
				'id' => 'map_center',
				'type' => 'cs_gmaps',
				'options' => array(
					'api_key' => isset($this->plugin_settings['api_key']) ? $this->plugin_settings['api_key'] : '',
					'disable_gmaps_api' => false,
					'disable_gmap3_plugin' => false,
					'split_values' => false,
					'split_latlng_field' => false,
					'save_only_latLng_field' => true,
					'map_height' => '250px',
					'map_width' => '100%',
					'map_center' => '',
					'map_zoom' => 12,
					'labels' => array(
						'address' => 'Enter a location & search or Geolocate your position',						
						'latLng' => 'Latitude & Longitude',
						'search' => 'Search',
						'pinpoint' => 'Set map center',
					),
					'secondary_latlng' => array(
						'show' => false,
					)
				),
				'before_row' => str_replace(
					array('[title]'), 
					array('Map Center, Background & Retina Display'), 
					$this->toggle_before_row.'<p class="cmb2-metabox-description" style="margin:0 0 10px 0; padding-top:0;">Set the map center point, change your map background color and set the retina display support for your markers & clusters.</p><hr />'
				),												
			);

			$fields[] = array(
				'id' => 'map_background',
				'name' => 'Map Background',
				'desc' => 'Color used for the background of the Map div. This color will be visible when tiles have not yet loaded as the user pans. Defaults to "#ededed".',
				'type' => 'colorpicker',
				'default' => '#ededed',
			);	
					
			$fields[] = array(
				'id' => 'retinaSupport',
				'name' => 'Retina support',
				'desc' => 'Enable retina support for custom markers & Clusters images. When enabled, make sure the uploaded image is twice the size you want it to be displayed on the map. 
						   For example, if you want the marker/cluster image in the map to be displayed as 20x30 pixels, upload an image with 40x60 pixels.<br />
						   <span style="color:red;"><strong>Note: Retina will not be applied to SVG icons!</strong></span>',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Enable',
					'false' => 'Disable'
				),
				'attributes' => array(
					'data-conditional-id' => 'defaultMarker',
					'data-conditional-value' => wp_json_encode(array('customize')),								
				),
				'after_row' => $this->toggle_after_row,						
			);
								
			$fields[] = array(
				'id' => 'restrict_map_bounds',
				'name' => 'Restrict map bounds',
				'desc' => 'A restriction that can be applied to the Map. By restricting the map bounds, users can only pan and zoom inside the given bounds and the map\'s viewport will not exceed these restrictions.<br />
						  Draw map bounds in the field "Map bounds restriction"!',
				'type' => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No'
				),
				'before_row' => str_replace(
					array('[title]'), 
					array('Restricting Map Bounds'), 
					$this->toggle_before_row.'<p class="cmb2-metabox-description" style="margin:0 0 10px 0; padding-top:0;">Restricting the map bounds will prevent the map from being panned outside a given area.</p><hr />'
				),								
			);		
						
			$fields[] = array(
				'name' => 'Map bounds restriction',
				'desc' => 'In case you already know the bounds to use, simply paste them in the field/textarea above respecting the following format: <br /> 
						  <code>[NE latitude,NE longitude],[SW latitude,SW longitude]</code><br /><br />
						  <strong style="font-size:15px; display: inline-block; padding: 5px 0;">How to trace the map bounds?</strong><br />						  
						  Before tracing the bounds, you need to pan the map to your desired area. 
						  You can use the address search field or geolocate your position for that purpose!<br />
						  <strong>1.</strong> Click (if not already selected) on the rectangle button/icon on the map (next to the hand button/icon).<br />
						  <strong>2.</strong> Draw a rectangle around your desired area by clicking on a specific spot on the map, then, drag the cursor 
						  to cover the whole area.<br />
						  <strong>3.</strong> Once finished, you can edit the rectangle by dragging a vertex/point to another spot. To drag 
						  the whole rectangle, first, check the option <u>"Activate dragging mode"</u> on top of the map, then start dragging it.<br />
						  <strong>4.</strong> If you need to start drawing from scratch, click on the button <u>"Clean map & draw again"</u>, then, start 
						  tracing new bounds.<br />
						  <strong>5.</strong> Once satisfied with the area been covered, click on the button <u>"Copy bounds"</u>.<br />
						  <span style="color:red;">
						  <strong>Hint:</strong> For more precision while tracing the bounds, use the map\'s full screen mode!
						  </span>',
				'id' => 'map_bounds_restriction',
				'type' => 'cs_gmaps_drawing',
				'default' => '',
				'options' => array(
					'api_key' => isset($this->plugin_settings['api_key']) ? $this->plugin_settings['api_key'] : '',
					'disable_gmaps_api' => false,
					'disable_gmap3_plugin' => false,
					'map_height' => '200px',
					'map_width' => '100%',
					'map_center' => isset($this->plugin_settings['map_center']) ? $this->plugin_settings['map_center'] : '51.53096,-0.121064',
					'map_zoom' => 12,
					'labels' => array(
						'address' => 'Enter a location & search or Geolocate your position',						
						'coordinates' => 'Map bounds (North-east & South-west coordinates)',
						'search' => 'Search',
						'pinpoint' => 'Copy bounds',
						'clear_overlay' => 'Clean map & draw again',
						'top_desc' => 'Draw a rectangle around a specific area to prevent the map from being panned outside it.<br />
									  <span style="color:red;">Read the insctructions below for more details about tracing the bounds!</span><hr />',
					),
					'draw_mode' => 'rectangle',
					'save_coordinates' => true,
				),
			);
			
			$fields[] = array(
				'id' => 'strict_bounds',
				'name' => 'Strict bounds',
				'desc' => 'By default bounds are relaxed, meaning that a user can zoom out until the entire bounded area is in view. 
						   Bounds can be made more restrictive by setting this option to "Restrictive". 
						   This reduces how far a user can zoom out, ensuring that everything outside of the restricted bounds stays hidden.',
				'type' => 'radio',
				'default' => 'relaxed',
				'options' => array(
					'relaxed' => 'Relaxed',
					'restrictive' => 'Restrictive'
				),
				'after_row' => $this->toggle_after_row,
			);			
				
			$fields[] = array(
				'id' => 'map_zoom',
				'name' => 'Map zoom',
				'desc' => 'Select the map zoom. <span style="color:red;">The map zoom will be ignored if you activate the option (below) <strong>"Autofit"</strong>!</span>',
				'type' => 'select',
				'default' => '12',
				'options' => array(
					'0' => '0',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16',
					'17' => '17',
					'18' => '18',
					'19' => '19',
                    '20' => '20',
                    '21' => '21',
                    '22' => '22'
				),
				'before_row' => str_replace(
					array('[title]'), 
					array('Map Properties (Zoom, Autofit etc...)'), 
					$this->toggle_before_row.'<p class="cmb2-metabox-description" style="margin:0 0 10px 0; padding-top:0;">Control how the user will interact with the map.</p><hr />'
				),												
			);
				
			$fields[] = array(
				'id' => 'max_zoom',
				'name' => 'Max. zoom',
				'desc' => 'Select the maximum zoom of the map.',
				'type' => 'select',
				'default' => '19',
				'options' => array(
					'0' => '0',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16',
					'17' => '17',
					'18' => '18',
					'19' => '19',
                    '20' => '20',
                    '21' => '21',
                    '22' => '22'
				)
			);
				
			$fields[] = array(
				'id' => 'min_zoom',
				'name' => 'Min. zoom',
				'desc' => 'Select the minimum zoom of the map. <span style="color:red;">The Min. zoom should be lower than the Max. zoom!</span>',
				'type' => 'select',
				'default' => '0',
				'options' => array(
					'0' => '0',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16',
					'17' => '17',
					'18' => '18',
					'19' => '19',
                    '20' => '20',
                    '21' => '21',
                    '22' => '22'
				)
			);			
				
			$fields[] = array(
				'id' => 'autofit',
				'name' => 'Autofit',
				'desc' => 'This option extends map bounds to contain all markers & clusters. <span style="color:red;">By activating this option, the map zoom will be ignored!</span><br />
						   <strong style="color:red;">The Minimum zoom level is taken into consideration when using this option. Make sure to set the map\'s minimum zoom to a level that allows displaying all items on the map!</strong>',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => 'recenter_map',
				'name' => 'Recenter Map',
				'desc' => 'Show a button on the map to allow recentring the map. Defaults to "Yes".',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
				
			$fields[] = array(
				'id' => 'clickableIcons',
				'name' => 'Clickable Icons',
				'desc' => 'When set to "No", map icons are not clickable. A map icon represents a point of interest, also known as a POI. Defaults to "Yes".',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				),
				'after_row' => $this->toggle_after_row,
			);									
			
			$fields[] = array(
				'id' => 'interaction_section',
				'name' => 'Interaction',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
				'before_row' => str_replace(
					array('[title]'), 
					array('Controls & Interaction'), 
					$this->toggle_before_row.'<p class="cmb2-metabox-description" style="margin:0 0 10px 0; padding-top:0;">The maps displayed through the Google Maps API contain UI elements to allow user interaction with the map. These elements are known as controls and you can include and/or customize variations of these controls in your map.</p><hr />'
				),			
			);
							
			$fields[] = array(
				'id' => 'zoom_on_doubleclick',
				'name' => 'Zoom on double click',
				'desc' => 'Enable/Disable zooming and recentering the map on double click. Defaults to "Disable".',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'false' => 'Enable',
					'true' => 'Disable'
				)
			);

			$fields[] = array(
				'id' => 'gestureHandling',
				'name' => 'Gesture Handling',
				'desc' => 'This setting controls how the API handles gestures on the map. Allowed values:<br />
						  <strong>"cooperative":</strong> Scroll events and one-finger touch gestures scroll the page, and do not zoom or pan the map. Two-finger touch gestures pan and zoom the map. Scroll events with a ctrl key or ⌘ key pressed zoom the map. In this mode the map cooperates with the page.<br />
						  <strong>"greedy":</strong> All touch gestures and scroll events pan or zoom the map.<br />
						  <strong>"none":</strong> The map cannot be panned or zoomed by user gestures.<br />
						  <strong>"auto":</strong> (default) Gesture handling is either cooperative or greedy, depending on whether the page is scrollable or in an iframe.',
				'type' => 'radio',
				'default' => 'auto',
				'options' => array(
					'cooperative' => 'Cooperative',
					'greedy' => 'Greedy',
					'none' => 'None',
					'auto' => 'Auto'
				)
			);
			
			$fields[] = array(
				'id' => 'controls_section',
				'name' => 'Controls',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				)			
			);
						
			$fields[] = array(
				'id' => 'mapTypeControl',
				'name' => 'Show map type control',
				'desc' => 'The MapType control lets the user toggle between map types (such as ROADMAP and SATELLITE). This control appears by default in the top right corner of the map.',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
					
			$fields[] = array(
				'id' => 'streetViewControl',
				'name' => 'Show street view control',
				'desc' => 'The Street View control contains a Pegman icon which can be dragged onto the map to enable Street View. This control appears by default in the right top corner of the map.',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => 'rotateControl',
				'name' => 'Show rotate control',
				'desc' => 'Enables/disables the appearance of a Rotate control for controlling the orientation of 45° imagery. 
						   By default, the control\'s appearance is determined by the presence or absence of 45° imagery for the given map type at the current zoom and location.
						   45° imagery is only available for satellite and hybrid map types, within some locations, and at some zoom levels. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => 'scaleControl',
				'name' => 'Show scale control',
				'desc' => 'Show/Hide the scale control .Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => 'fullscreenControl',
				'name' => 'Show fullscreen control',
				'desc' => 'Show/Hide the fullscreen control .Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
								
			$fields[] = array(
				'id' => 'zoomControl',
				'name' => 'Show zoom control',
				'desc' => 'The Zoom control displays a small "+/-" buttons to control the zoom level of the map. This control appears by default in the top left corner of the map on non-touch devices or in the bottom left corner on touch devices.',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
				
			$fields[] = array(
				'id' => 'zoomControlType',
				'name' => 'Zoom control Type',
				'desc' => 'Select the zoom control type.',
				'type' => 'radio',
				'default' => 'customize',
				'options' => array(
					'customize' => 'Customized type',
					'default' => 'Default type'
				)
			);
	
			$fields[] = array(
				'id' => 'controlSize',
				'name' => 'Map controls size', 
				'desc' => 'Specify the size in pixels of the controls appearing on the map. Defaults to "39".
						   <br />Please note that only works for the controls made by the Maps API itself. Does not scale custom controls.',
				'type' => 'text',
				'default' => '39',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '0',
				),
				'after_row' => $this->toggle_after_row,													
			);
	
			$fields[] = array(
				'id' => 'traffic_layer',
				'name' => 'Traffic Layer',
				'desc' => 'Add real-time traffic information to your map. Traffic information is refreshed frequently, but not instantly. Rapid consecutive requests for the same area are unlikely to yield different results.',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				),
				'before_row' => str_replace(
					array('[title]'), 
					array('Traffic, Transit, and Bicycling Layers'), 
					$this->toggle_before_row.'<p class="cmb2-metabox-description" style="margin:0 0 10px 0; padding-top:0;">The Traffic, Transit, and Bicycling layers modify the base map layer to display current traffic conditions, local transit networks, or bicycling route information.</p><hr />'
				),
			);
				
			$fields[] = array(
				'id' => 'transit_layer',
				'name' => 'Transit Layer',
				'desc' => 'Display the public transit network of a city on your map. When the Transit Layer is enabled, and the map is centered on a city that supports transit information, the map will display major transit lines as thick, colored lines. The color of the line is set based upon information from the transit line operator.',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)				
			);
				
			$fields[] = array(
				'id' => 'bicycling_layer',
				'name' => 'Bicycling Layer',
				'desc' => 'Add bicycle information to your map. The Bicycling Layer renders a layer of bike paths, suggested bike routes and other overlays specific to bicycling usage on top of the map.',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				),
				'after_row' => $this->toggle_after_row,	
			);
						
			return $fields;
			
		}
		
		
		/**
		 * Map Style Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_map_style_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Map Style Settings',
				'desc' => 'Styled maps allow you to customize the presentation of the standard Google base maps, changing the visual display of such elements as roads, parks, and built-up areas. The lovely styles below are provided by <a href="http://snazzymaps.com" target="_blank" class="cspm_blank_link">Snazzy Maps</a>. <br />
						   The following style settings are going to be used as the main style settings of all single maps and as the default style settings of your new maps. 
						   You can always override the default style settings when creating/editing a map',
				'type' => 'title',
				'id'   => 'map_style_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
				
			$fields[] = array(
				'id' => 'initial_map_style',
				'name' => 'Initial map style',
				'desc' => 'Select the initial map style.',
				'type' => 'radio',
				'default' => 'ROADMAP',
				'options' => array(
					'ROADMAP' => 'Road Map <sup>(Displays the default road map view)</sup>',
					'SATELLITE' => 'Satellite <sup>(Displays Google Earth satellite images)</sup>',
					'TERRAIN' => 'Terrain <sup>(Displays a physical map based on terrain information)</sup>',
					'HYBRID' => 'Hybrid <sup>(Displays a mixture of normal and satellite views)</sup>',
					'custom_style' => 'Custom style <sup>(Displays a custom style)</sup>'
				)
			);
					
			$fields[] = array(
				'id' => 'style_option',
				'name' => 'Style option', 
				'desc' => 'Select the style option of the map. <span style="color:red;">If you select the option <strong>Progress map styles</strong>, choose one of the available styles below.
						   If you select the option <strong>My custom style</strong>, enter your custom style code in the field <strong>Javascript Style Array</strong>.</span>',
				'type' => 'radio',
				'default' => 'progress-map',
				'options' => array(
					'progress-map' => 'Progress Map styles',
					'custom-style' => 'My custom style'
				),
			);
					
			$fields[] = array(
				'id' => 'map_style',
				'name' => 'Map style',
				'desc' => 'Select your map style.',
				'type' => 'radio',
				'default' => 'google-map',
				'options' => $this->cspm_get_all_map_styles(),
				'attributes' => array(
					'data-conditional-id' => 'style_option',
					'data-conditional-value' => wp_json_encode(array('progress-map')),								
				),													
			);
					
			$fields[] = array(
				'id' => 'custom_style_name',
				'name' => 'Custom style name',
				'desc' => 'Enter your custom style name. Defaults to "Custom style".',
				'type' => 'text',
				'default' => 'Custom style',
				'attributes' => array(
					'data-conditional-id' => 'style_option',
					'data-conditional-value' => wp_json_encode(array('custom-style')),								
				),																	
			);
					
			$fields[] = array(
				'id' => 'js_style_array',
				'name' => 'Javascript Style Array',
				'desc' => 'If you don\'t like any of the styles above, fell free to add your own style. Please include just the array definition. No extra variables or code.<br />
						  Make use of the following services to create your style:<br />
	  			          . <a href="https://mapstyle.withgoogle.com/" target="_blank" class="cspm_blank_link">Google Maps APIs Styling Wizard</a><br />
						  . <a href="http://www.evoluted.net/thinktank/web-design/custom-google-maps-style-tool" target="_blank" class="cspm_blank_link">Custom Google Maps Style Tool by Evoluted</a><br />
						  . <a href="http://software.stadtwerk.org/google_maps_colorizr/" target="_blank" class="cspm_blank_link">Google Maps Colorizr by stadt werk</a><br />			  					  
						  You may also like to <a href="http://snazzymaps.com/submit" target="_blank" class="cspm_blank_link">submit</a> your style for the world to see :)',
				'type' => 'textarea',
				'default' => '',
				'attributes' => array(
					'data-conditional-id' => 'style_option',
					'data-conditional-value' => wp_json_encode(array('custom-style')),								
				),																					
			);	

			return $fields;
			
		}
		
		
		/**
		 * Marker settings
		 * 
		 * @since 1.0
		 */
		function cspm_marker_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Marker Settings',
				'desc' => 'The following marker settings are going to be used as the main marker settings of all single maps and as the default marker settings of your new maps. 
						   You can always override the default marker settings when creating/editing a map',
				'type' => 'title',
				'id'   => 'marker_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
				
			$fields[] = array(
				'id' => 'defaultMarker',
				'name' => 'Marker image type',
				'desc' => 'Select the marker image type.<br />
						  <span style="color:red;">Note: You can use SVG icons!</span>',
				'type' => 'radio',
				'default' => 'customize',
				'options' => array(
					'customize' => 'Customized image type',
					'default' => 'Default image type <sup>(Google Maps red marker)</sup>'
				)
			);
				
			$fields[] = array(
				'id' => 'markerAnimation',
				'name' => 'Marker animation',
				'desc' => 'You can animate a marker so that it exhibit a dynamic movement when it\'s been fired. To specify the way a marker is animated, select
						   one of the supported animations above.',
				'type' => 'radio',
				'default' => 'pulsating_circle',
				'options' => array(
					'pulsating_circle' => 'Pulsating circle',
					'bouncing_marker' => 'Bouncing marker',
					'flushing_infobox' => 'Flushing infobox <sup style="color:red;">(Use it only when <strong>Show infobox</strong> is set to <strong>Yes</strong>)</sup>'				
				)
			);
				
			$fields[] = array(
				'id' => 'marker_icon',
				'name' => 'Marker image',
				'desc' => 'Change the default custom marker image. By default, this image will be used for all markers. In case you need to restore the the original marker image, this one can be found in the plugin\'s images directory. 
						  <br /><strong>How to override the default marker image?</strong>
						  <br />1. You can override it by editing a specific post/location and uploading a custom image in the field "Marker => Marker icon". 
						  <br />2. You can also override it by uploading a custom image for a category of posts/locations. The options you need for this method can be found under the section "Marker categories settings".',
				'type' => 'file',
				'default' => $this->plugin_url.'img/svg/marker.svg',
			);
		
			$fields[] = array(
				'id' => 'marker_icon_height',
				'name' => 'Marker image height', 
				'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
						  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
						  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
				'type' => 'text',
				'default' => '32',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '-1',
				),
				'sanitization_cb' => function($value, $field_args, $field){
					return ($value == '0') ? '-1' : $value;
				}														
			);
			
			$fields[] = array(
				'id' => 'marker_icon_width',
				'name' => 'Marker image width', 
				'desc' => 'Specify the image width (in pixels). Set to -1 to ignore or to automatically calculate the width (for PNG/JPEG/GIF images).<br />
						  <span style="color:red;"><strong>Note: When set to -1, the image width will be automatically calculated but only if the image type is PNG/JPEG/GIF.
						  If you\'re using SVG icons, the image width will not be calculated and you may need to specify it.</strong></span>',
				'type' => 'text',
				'default' => '30',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '-1',
				),
				'sanitization_cb' => function($value, $field_args, $field){
					return ($value == '0') ? '-1' : $value;
				}										
			);

			$fields[] = array(
				'id' => 'marker_anchor_point_option',
				'name' => 'Set the anchor point',
				'desc' => 'Depending of the shape of the marker, you may not want the middle of the bottom edge to be used as the anchor point. 
						   In this situation, you need to specify the anchor point of the image. A point is defined with an X and Y value (in pixels). 
						   So if X is set to 10, that means the anchor point is 10 pixels to the right of the top left corner of the image. Setting Y to 10 means 
						   that the anchor is 10 pixels down from the top right corner of the image.',
				'type' => 'radio',
				'default' => 'disable',
				'options' => array(
					'auto' => 'Auto detect <sup>*Detects the center of the image.</sup>',
					'manual' => 'Manualy <sup>*Enter the anchor point in the next two fields.</sup>',
					'disable' => 'Disable'				
				)
			);
				
			$fields[] = array(
				'id' => 'marker_anchor_point',
				'name' => 'Marker anchor point',
				'desc' => 'Enter the anchor point of the Marker image. Separate X and Y by comma. (e.g. 10,15)',
				'type' => 'text',
				'default' => '',
			);

			return $fields;
			
		}
		
		
		/**
		 * Clustering settings
		 *
		 * @since 1.0
		 */
		function cspm_clustering_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Marker Clustering Settings',
				'desc' => 'Clustering simplifies your data visualization by consolidating data that are nearby each other on the map in an aggregate form. A cluster is displayed as a circle with a number on it. 
						   The number on a cluster indicates how many markers it contains. As you zoom into any of the cluster locations, the number on the cluster decreases, and you begin to see the individual markers on the map. 
						   Zooming out of the map consolidates the markers into clusters again.
						   <br />
						   The following marker clustering settings are going to be used as the default marker clustering settings of your new maps. 
						   You can always override the default marker clustering settings when creating/editing a map',
				'type' => 'title',
				'id'   => 'infobox_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => 'useClustring',
				'name' => 'Use marker clustering',
				'desc' => 'Clustering simplifies your data visualization by consolidating data that are nearby each other on the map in an aggregate form. <span style="color:red;"><strong>Activating this option will significantly increase the loading speed of the map!</strong></span>',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes <span style="color:red;"><sup><strong>(Recommended)</strong></sup></span>',
					'false' => 'No'
				)
			);
				
			$fields[] = array(
				'id' => 'gridSize',
				'name' => 'Grid size',
				'desc' => 'Grid size or Grid-based clustering works by dividing the map into squares of a certain size (the size changes at each zoom) and then grouping the markers into each grid square.',
				'type' => 'text',
				'default' => '60',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '0'
				),						
			);
			
			$fields[] = array(
				'id' => 'clusters_customizations_section',
				'name' => 'Clusters Images & Text color',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);

				$fields[] = array(
					'id' => 'cluster_text_color',
					'name' => 'Clusters text color',
					'desc' => 'Change the text color of all your clusters.',
					'type' => 'colorpicker',
					'default' => '',
				);			

				$fields[] = array(
					'id' => 'small_cluster_icon',
					'name' => 'Small cluster image',
					'desc' => 'Upload a new small cluster image. You can always find the original marker in the plugin\'s images directory.',
					'type' => 'file',
					'default' => $this->plugin_url.'img/svg/cluster.svg',
					'before_row' => str_replace(array('[title]', ' closed'), array('Small cluster', ''), $this->toggle_before_row),	
				);
			
					$fields[] = array(
						'id' => 'small_cluster_icon_height',
						'name' => 'Small cluster image height', 
						'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => '50',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '-1',
						),
						'sanitization_cb' => function($value, $field_args, $field){
							return ($value == '0') ? '-1' : $value;
						}
					);
					
					$fields[] = array(
						'id' => 'small_cluster_icon_width',
						'name' => 'Small cluster image width', 
						'desc' => 'Specify the image width (in pixels). Set to -1 to ignore or to automatically calculate the width (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image width will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image width will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => '50',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '-1',
						),
						'sanitization_cb' => function($value, $field_args, $field){
							return ($value == '0') ? '-1' : $value;
						},
						'after_row' => $this->toggle_after_row,
					);

				$fields[] = array(
					'id' => 'medium_cluster_icon',
					'name' => 'Medium cluster image',
					'desc' => 'Upload a new medium cluster image. You can always find the original marker in the plugin\'s images directory.',
					'type' => 'file',
					'default' => $this->plugin_url.'img/svg/cluster.svg',
					'before_row' => str_replace(array('[title]'), array('Medium cluster'), $this->toggle_before_row),
				);
			
					$fields[] = array(
						'id' => 'medium_cluster_icon_height',
						'name' => 'Medium cluster image height', 
						'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => '70',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '-1',
						),
						'sanitization_cb' => function($value, $field_args, $field){
							return ($value == '0') ? '-1' : $value;
						}
					);
					
					$fields[] = array(
						'id' => 'medium_cluster_icon_width',
						'name' => 'Medium cluster image width', 
						'desc' => 'Specify the image width (in pixels). Set to -1 to ignore or to automatically calculate the width (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image width will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image width will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => '70',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '-1',
						),
						'sanitization_cb' => function($value, $field_args, $field){
							return ($value == '0') ? '-1' : $value;
						},
						'after_row' => $this->toggle_after_row,
					);
				
				$fields[] = array(
					'id' => 'big_cluster_icon',
					'name' => 'Large cluster image',
					'desc' => 'Upload a new large cluster image. You can always find the original marker in the plugin\'s images directory.<br />
							  <span style="color:red;">Note: You can use SVG icons!</span>',
					'type' => 'file',
					'default' => $this->plugin_url.'img/svg/cluster.svg',
					'before_row' => str_replace(array('[title]'), array('Medium cluster'), $this->toggle_before_row),
				);
			
					$fields[] = array(
						'id' => 'big_cluster_icon_height',
						'name' => 'Large cluster image height', 
						'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => '90',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '-1',
						),
						'sanitization_cb' => function($value, $field_args, $field){
							return ($value == '0') ? '-1' : $value;
						}
					);
					
					$fields[] = array(
						'id' => 'big_cluster_icon_width',
						'name' => 'Large cluster image width', 
						'desc' => 'Specify the image width (in pixels). Set to -1 to ignore or to automatically calculate the width (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image width will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image width will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => '90',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '-1',
						),
						'sanitization_cb' => function($value, $field_args, $field){
							return ($value == '0') ? '-1' : $value;
						},
						'after_row' => $this->toggle_after_row,
					);
				
				return $fields;
			
		}
		
		
		/**
		 * Infobox Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_infobox_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Infobox Settings',
				'desc' => 'The infobox, also called infowindow is an overlay that looks like a bubble and is often connected to a marker.<br />
						   The following infobox settings are going to be used as the main infobox settings of all single maps and as the default infobox settings of your new maps. 
						   You can always override the default infobox settings when creating/editing a map.',
				'type' => 'title',
				'id'   => 'infobox_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => 'show_infobox',
				'name' => 'Show Infobox',
				'desc' => 'Show/Hide the Infobox.',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
				
			$fields[] = array(
				'id' => 'infobox_type',
				'name' => 'Infobox type',
				'desc' => 'Select the Infobox type.',
				'type' => 'radio_image',
				'default' => 'rounded_bubble',
				'options' => array(				
					'square_bubble' => 'Square bubble (60x60)',
					'rounded_bubble' => 'Rounded bubble (60x60)',
					'cspm_type3' => 'Infobox 3 (250x50)',
					'cspm_type4' => 'Infobox 4 (250x50)',
					'cspm_type2' => 'Infobox 2 (180x180)',				
					'cspm_type5' => 'Large Infobox (400x300)',
					'cspm_type1' => 'Infobox 1 (380x120)',										
					'cspm_type6' => 'Infobox 6 (380x120)',												
				),
				'images_path' => $this->plugin_url,
				'images' => array(
					'square_bubble' => 'admin/img/radio-imgs/square_bubble.jpg',
					'rounded_bubble' => 'admin/img/radio-imgs/rounded_bubble.jpg',				
					'cspm_type1' => 'admin/img/radio-imgs/infobox_1.jpg',
					'cspm_type2' => 'admin/img/radio-imgs/infobox_2.jpg',
					'cspm_type3' => 'admin/img/radio-imgs/infobox_3.jpg',
					'cspm_type4' => 'admin/img/radio-imgs/infobox_4.jpg',
					'cspm_type5' => 'admin/img/radio-imgs/infobox_5.jpg',
					'cspm_type6' => 'admin/img/radio-imgs/infobox_6.jpg',												
				)
			);
			
			$fields[] = array(
				'id' => 'infobox_width',
				'name' => 'Infobox width', 
				'desc' => 'Override the infobox width by providing a new width (in pixels).<br />
						  <span style="color:red;"><strong>Note:<br />
						  1. If you override the infobox width, you must also provide the infobox height even if you have no intention to change it!<br />
						  2. Set to -1 to ignore this option!</strong></span>',
				'type' => 'text',
				'default' => '-1',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '-1',
				),
				'sanitization_cb' => function($value, $field_args, $field){
					return ($value == '0') ? '-1' : $value;
				}
			);
		
			$fields[] = array(
				'id' => 'infobox_height',
				'name' => 'Infobox height', 
				'desc' => 'Override the infobox height by providing a new height (in pixels).<br />
						  <span style="color:red;"><strong>Note:<br />
						  1. If you override the infobox height, you must also provide the infobox width even if you have no intention to change it!<br />
						  2. Set to -1 to ignore this option!</strong></span>',
				'type' => 'text',
				'default' => '-1',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '-1',
				),
				'sanitization_cb' => function($value, $field_args, $field){
					return ($value == '0') ? '-1' : $value;
				}
			);

			$fields[] = array(
				'id' => 'infobox_display_event',
				'name' => 'Display event',
				'desc' => 'Select from the options above when to display infoboxes on the map.',
				'type' => 'radio',
				'default' => 'onload',
				'options' => array(
					'onload' => 'On map load <sup>(Loads all infoboxes in viewport)</sup>',
					'onclick' => 'On marker click',
					'onhover' => 'On marker hover <span style="color:red;"><sup>(Doesn\'t work on touch devices)</sup></span>',
					'onzoom' => 'By reaching a zoom level <sup>(Loads all infoboxes in viewport)</sup>',					
				)
			);
			
			$fields[] = array(
				'id' => 'infobox_display_zoom_level',
				'name' => 'Map zoom',
				'desc' => 'Select the zoom level on which you want to start displaying infoboxes. Defaults to "12".',
				'type' => 'select',
				'default' => '12',
				'options' => array(
					'0' => '0',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16',
					'17' => '17',
					'18' => '18',
					'19' => '19',
                    '20' => '20',
                    '21' => '21',
                    '22' => '22'
				),
				/*'attributes' => array(
					'data-conditional-id' => 'infobox_display_event',
					'data-conditional-value' => wp_json_encode(array('onzoom')),								
				),	*/																				
			);
			
			$fields[] = array(
				'id' => 'infobox_show_close_btn',
				'name' => 'Show close button',
				'desc' => 'Show/Hide the close button on top of the infobox.',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
															
			$fields[] = array(
				'id' => 'remove_infobox_on_mouseout',
				'name' => 'Remove Infobox on mouseout?',
				'desc' => 'Choose whether you want to remove the infobox when the mouse leaves the marker or not. <span style="color:red">This option is operational only when the <strong>Display event</strong> 
						  equals to <strong>On marker click</strong> or <strong>On marker hover</strong>. This option doesn\'t work on touch devices</span>',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => 'infobox_external_link',
				'name' => 'Post URL',
				'desc' => 'Choose an option to open the single post page. You can also disable links in the infoboxes by selecting the option "Disable"',
				'type' => 'radio',
				'default' => 'same_window',
				'options' => array(
					'new_window' => 'Open in a new window',
					'same_window' => 'Open in the same window',
					'popup' => 'Open inside a modal/popup',
					'nearby_places' => 'Open the "Nearby palces" map inside a modal/popup',
					'disable' => 'Disable'
				)
			);
						
			$fields[] = array(
				'id' => 'infobox_content_section',
				'name' => 'Infobox Content Parameters',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => 'infobox_title',
					'name' => 'Infobox title',
					'desc' => 'Create your custom infobox title by entering the name of your custom fields. You can use as many you want. Leave this field empty to use the default title.
							<br /><strong>Syntax:</strong> <code>[meta_key<sup>1</sup>][separator<sup>1</sup>][meta_key<sup>2</sup>][separator<sup>2</sup>][meta_key<sup>n</sup>]...[title length]</code>.
							<br /><strong>Example of use:</strong> <code>[post_category][s=,][post_address][l=50]</code>
							<br /><strong>*</strong> To insert empty an space enter <code>[-]</code>
							<br /><strong>* Make sure there\'s no empty spaces between <code>][</code></strong>
							<br /><a href="http://www.docs.progress-map.com/cspm_guide/infoboxes-settings/build-a-custom-title-from-custom-fields-for-the-infoboxes/" target="_blank" class="cspm_blank_link" style="color:red">Check this post for more details!</a>',
					'type' => 'textarea',
					'default' => '',
				);
	
				$fields[] = array(
					'id' => 'infobox_content',
					'name' => 'Infobox description',
					'desc' => 'Create your custom infobox description. You can combine the post content with your custom fields & taxonomies. Leave this field empty to use the default description.
							<br /><strong>Syntax:</strong> <code>[content;content_length][separator][t=label:][meta_key][separator][t=Category:][tax=taxonomy_slug][separator]...[description length]</code>
							<br /><strong>Example of use:</strong> <code>[content;80][s=br][t=Category:][-][tax=category][s=br][t=Address:][-][post_address]</code>
							<br /><strong>*</strong> To specify a description length, use <code>[l=LENGTH]</code>. Change LENGTH to a number (e.g. 100).
							<br /><strong>*</strong> To add a label, use <code>[t=YOUR_LABEL]</code>
							<br /><strong>*</strong> To add a custom field, use <code>[CUSTOM_FIELD_NAME]	</code>				
							<br /><strong>*</strong> To insert a taxonomy, use <code>[tax=TAXONOMY_SLUG]</code>
							<br /><strong>*</strong> To insert new line enter <code>[s=br]</code>
							<br /><strong>*</strong> To insert an empty space enter <code>[-]</code>
							<br /><strong>*</strong> To insert the content/excerpt, use <code>[content;LENGTH]</code>. Change LENGTH to a number (e.g. 100).
							<br /><strong>* Make sure there\'s no empty spaces between <code>][</code></strong>							
							<br /><a href="http://www.docs.progress-map.com/cspm_guide/infoboxes-settings/build-a-custom-content-from-custom-fields-and-or-categories-for-the-infoboxes/" target="_blank" class="cspm_blank_link" style="color:red">Check this post for more details!</a>',
					'type' => 'textarea',
					'default' => '[l=100]',
				);	
				
				$fields[] = array(
					'id' => 'infobox_ellipses',
					'name' => 'Show ellipses',
					'desc' => 'Show ellipses (&hellip;) at the end of the content. Defaults to "Yes".',
					'type' => 'radio',
					'default' => 'yes',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No',
					)
				);

			return $fields;
			
		}
		
		
		/**
		 * Geotargeting settings
		 *
		 * @since 1.0
		 */
		function cspm_geotargeting_fields(){
			
			$fields = array();

			$fields[] = array(
				'name' => 'Geo-targeting Settings',
				'desc' => 'Geo-targeting allow the users to find and display their geographic location on the map.<br />
						   The following geotargeting settings are going to be used as the default geotargeting settings of your new maps. 
						   You can always override the default geotargeting settings when creating/editing a map.',
				'type' => 'title',
				'id'   => 'geotargeting_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
				
				$fields[] = array(
					'id' => 'geoIpControl',
					'name' => 'Allow Geo-targeting',
					'desc' => 'The Geo-targeting is the method of determining the geolocation of a website visitor.',
					'type' => 'radio',
					'default' => 'false',
					'options' => array(
						'true' => 'Yes',
						'false' => 'No'
					)
				);
					
				$fields[] = array(
					'id' => 'show_user',
					'name' => 'Show user location?',
					'desc' => 'Show a marker indicating the user\'s location on the map (when the user approves to share their location!).',
					'type' => 'radio',
					'default' => 'false',
					'options' => array(
						'true' => 'Yes',
						'false' => 'No'
					)
				);
					
				$fields[] = array(
					'id' => 'user_marker_icon',
					'name' => 'User Marker image',
					'desc' => 'Upload a marker image to display as the user location. When empty, the map will display the default marker of Google Map.',
					'type' => 'file',
					'default' => $this->plugin_url.'img/svg/user_marker.svg',
				);
		
				$fields[] = array(
					'id' => 'user_marker_icon_height',
					'name' => 'User Marker image height', 
					'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
							  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
							  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
					'type' => 'text',
					'default' => '32',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '-1',
					),
					'sanitization_cb' => function($value, $field_args, $field){
						return ($value == '0') ? '-1' : $value;
					}															
				);
				
				$fields[] = array(
					'id' => 'user_marker_icon_width',
					'name' => 'User Marker image width', 
					'desc' => 'Specify the image width (in pixels). Set to -1 to ignore or to automatically calculate the width (for PNG/JPEG/GIF images).<br />
							  <span style="color:red;"><strong>Note: When set to -1, the image width will be automatically calculated but only if the image type is PNG/JPEG/GIF.
							  If you\'re using SVG icons, the image width will not be calculated and you may need to specify it.</strong></span>',
					'type' => 'text',
					'default' => '30',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '-1',
					),
					'sanitization_cb' => function($value, $field_args, $field){
						return ($value == '0') ? '-1' : $value;
					}															
				);

				$fields[] = array(
					'id' => 'user_map_zoom',
					'name' => 'Geotarget Zoom',
					'desc' => 'Select the zoom of the map when indicating the user\'s location.',
					'type' => 'select',
					'default' => '12',
					'options' => array(
						'0' => '0',
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'11' => '11',
						'12' => '12',
						'13' => '13',
						'14' => '14',
						'15' => '15',
						'16' => '16',
						'17' => '17',
						'18' => '18',
						'19' => '19',
                        '20' => '20',
                        '21' => '21',
                        '22' => '22'
					)
				);
					
				$fields[] = array(
					'id' => 'user_circle',
					'name' => 'Draw a Circle around the user\'s location',
					'desc' => 'Draw a circle within a certain distance of the user\'s location. Set to 0 to ignore this option.',
					'type' => 'text',
					'default' => '0',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '0'
					),				
				);
				
				$fields[] = array(
					'id' => 'user_circle_fillColor',
					'name' => 'Fill color',
					'desc' => 'The fill color of the circle.',
					'type' => 'colorpicker',
					'default' => '#189AC9',															
				);

				$fields[] = array(
					'id' => 'user_circle_fillOpacity',
					'name' => 'Fill opacity',
					'desc' => 'The fill opacity of the circle between 0.0 and 1.0.',
					'type' => 'select',
					'default' => '0.1',
					'options' => array(
						'0,0' => '0.0',
						'0,1' => '0.1',
						'0,2' => '0.2',
						'0,3' => '0.3',
						'0,4' => '0.4',
						'0,5' => '0.5',
						'0,6' => '0.6',
						'0,7' => '0.7',
						'0,8' => '0.8',
						'0,9' => '0.9',
						'1' => '1',
					)			
				);
			
				$fields[] = array(
					'id' => 'user_circle_strokeColor',
					'name' => 'Stroke color',
					'desc' => 'The stroke color of the circle.',
					'type' => 'colorpicker',
					'default' => '#189AC9',
				);
				
				$fields[] = array(
					'id' => 'user_circle_strokeOpacity',
					'name' => 'Stroke opacity',
					'desc' => 'The stroke opacity of the circle between 0.0 and 1.',
					'type' => 'select',
					'default' => '1',
					'options' => array(
						'0,0' => '0.0',
						'0,1' => '0.1',
						'0,2' => '0.2',
						'0,3' => '0.3',
						'0,4' => '0.4',
						'0,5' => '0.5',
						'0,6' => '0.6',
						'0,7' => '0.7',
						'0,8' => '0.8',
						'0,9' => '0.9',
						'1' => '1',
					)			
				);
				
				$fields[] = array(
					'id' => 'user_circle_strokeWeight',
					'name' => 'Stroke weight',
					'desc' => 'The stroke width of the circle in pixels.',
					'type' => 'text',
					'default' => '1',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '0'
					),				
				);	
			
			return $fields;
					
		}
		
		
		/**
		 * Troubleshooting fields
 		 *
		 * @since 1.0 
		 */
		function cspm_troubleshooting_fields(){
		
			$fields = array();
			
			$fields[] = array(
				'name' => 'Troubleshooting',
				'desc' => '',
				'type' => 'title',
				'id'   => 'Troubleshooting_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => 'regenerate_markers_link',
				'name' => 'Regenerate markers',
				'desc' => 'This option is for regenerating all your markers. In most cases, <strong>you wont need to use this option at all</strong> because markers 
						   are automatically created and each time you add or edit a post, the marker(s) related to this post will be regenerated automatically. 
						   Use this options <strong>only when need to</strong>.
						   <span style="color:red">This may take a while when you have too many markers (500+) to regenerate. Please be patient.</span>',
				'type' => 'cs_button',
				'options' => array(
					'action' => 'cspm_regenerate_markers',
					'messages' => array(
						'success' => 'Done',						
						'error' => 'An error has been occured! Please try again later.',
						'confirm' => 'Are you sure you want to regenerate your markers?',
						'loading' => 'Regenerating markers is under process...',
					),
				),
        	);
			
			$fields[] = array(
				'id' => 'loading_scripts_section',
				'name' => 'Debug mode & Speed Optimization',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => 'combine_files',
					'name' => 'Loading scripts & styles',
					'desc' => 'By default, the plugin will load the minified version of all JS & CSS files <del>but you can reduce the number of requests your site has to make to the server in order to 
							   render the map by choosing the option "Combine files".</del><br />
							   <strong><u>Hint:</u></strong> <strong>Modern browsers are able to download multiple files at a time in parallel. So that means that it might be more efficient and faster for your browser to download several smaller files all at once, then one large file. The results will vary from site to site so you will have to test this for yourself.</strong>',
					'type' => 'radio',
					'default' => 'seperate_minify',
					'options' => array(
						'combine' => '<del>Combine files</del> <sup style="color:red;"><strong>Deprecated since 5.6.5</strong></sup>',
						'seperate' => 'Separate files (Debugging versions)',
						'seperate_minify' => 'Separate files (Minified versions) <sup style="color:red;"><strong>Recommended</strong></sup>'
					)
				);
			
			$fields[] = array(
				'id' => 'disabling_scripts_section',
				'name' => 'Disabling Stylesheets & Scripts',
				'desc' => 'Progress Map uses some files that may conflict with your theme. This section will allow you to disable files that are suspected of creating conflicts in your website.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => 'remove_bootstrap',
					'name' => 'Bootstrap (CSS File)',
					'desc' => 'If your theme uses bootstrap v3+, you can disable the one used by this plguin.
							  <br /><del><span style="color:red;">Important Note: This option will not take action if you select the option <strong>"Loading scripts & styles / Combine files"</strong>! Use the option <strong>"Separate files (Debugging versions)"</strong> or <strong>"Separate files (Minified versions)"</strong> instead!</span></del>',
					'type' => 'radio',
					'default' => 'enable',
					'options' => array(
						'enable' => 'Enable',
						'disable' => 'Disable'
					)
				);
				
				$fields[] = array(
					'id' => 'remove_google_fonts',
					'name' => 'Google Fonts "Source Sans Pro" (CSS File)',
					'desc' => 'If your theme uses or load this font, you can disable the one used by this plguin.',
					'type' => 'radio',
					'default' => 'enable',
					'options' => array(
						'enable' => 'Enable',
						'disable' => 'Disable'
					)
				);
			
				$fields[] = array(
					'id' => 'remove_gmaps_api',
					'name' => 'Google Maps API (JS File)<br /> <span style="color:red;">Not recommended. Read the description!</span>',
					'desc' => 'If your theme loads its own GMaps API, you can disbale the one used by this plugin.
							   <br /><span style="color:red;"><strong>Important Notes: <br />
							   1) Your theme MUST load the version 3 (v3) of Google Maps Javascript API!<br /><br />
							   2) Your theme MUST load the following libraries:<br />
							   - Geometry Library.<br />
							   - Places Libary.<br />
							   The list of libraries may change in the future!
							   <br /><br />
							   3) If you remove our GMaps API, you\'ll not be able to use the following features:<br />
							   - Changing the language of the map.<br />
							   - Adding an API Key to your map.<br />
							   - Address search.<br />
							   The list of parameters may change in the future!<br /><br />
							   Unless your theme doesn\'t provide all of this, we highly recommend you to ENABLE our GMaps API!
							   </strong></span>',
					'type' => 'multicheck',
					'select_all_button' => false,
					'default' => array(''),
					'options' => array(
						'disable_frontend' => 'Disable on Frontend',
						'disable_backend' => 'Disable on Backend',
					),
					'sanitization_cb' => function($value, $field_args, $field){
						if(is_array($value) && count($value) > 0)
							return $value;
						else return $field_args['default'];
					}
				);		
	
			return $fields;
					
		}
		
		
		/**
		 * Customize fields
		 *
		 * @since 1.0 
		 */
		function cspm_customize_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Customize',
				'desc' => '',
				'type' => 'title',
				'id'   => 'customize_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
				
			/**
			 * Main colors */
			
			$fields[] = array(
				'id' => 'main_colors_section',
				'name' => 'Main colors',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
								
				$fields[] = array(
					'id' => 'main_hex_color',
					'name' => 'Main color',
					'desc' => 'Pick a color for the plugin. Default to "#008fed".',
					'type' => 'colorpicker',
					'default' => '#008fed',
				);
				
				$fields[] = array(
					'id' => 'main_hex_hover_color',
					'name' => 'Hover color',
					'desc' => 'Pick the hover color for the plugin. Default to "#009bfd".',
					'type' => 'colorpicker',
					'default' => '#009bfd',
				);
				
			/**
			 * Icons */
			
			$fields[] = array(
				'id' => 'map_icons_section',
				'name' => 'Icons',
				'desc' => 'Change the colored icons that appears on the map. Icons used in this plugin are available on <a href="https://www.flaticon.com/" target="_blank" class="cspm_blank_link">Flaticon</a>',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
								
				$fields[] = array(
					'id' => 'zoom_in_icon',
					'name' => 'Zoom-in icon',
					'desc' => 'Appears inside the button that allows to zoom-in the map. The original image can be found <a href="https://www.flaticon.com/free-icon/addition-sign_2664" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => $this->plugin_url.'img/svg/addition-sign.svg',
				);
					
				$fields[] = array(
					'id' => 'zoom_out_icon',
					'name' => 'Zoom-out icon',
					'desc' => 'Appears inside the button that allows to zoom-out the map. The original image can be found <a href="https://www.flaticon.com/free-icon/calculation-operations-minus-sign_42977" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => $this->plugin_url.'img/svg/minus-sign.svg',
				);
					
			
			/**
			 * Modal | @since 4.6 */
			
			$fields[] = array(
				'id' => 'modal_section',
				'name' => 'Modal',
				'desc' => 'Change the options of the modal used to open the single post page or the nearby palces map page.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => 'modal_width',
					'name' => 'Modal width', 
					'desc' => 'Change the width of the modal. You can use %, px, em or cm. Default to "90%".',
					'type' => 'text',
					'default' => '90',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '1',
					)										
				);
				
				$fields[] = array(
					'id' => 'modal_width_unit',
					'name' => 'Modal width unit',
					'desc' => 'Select the modal width measurement unit. Default to "%".',
					'type' => 'select',
					'default' => '%',
					'options' => array(
                        'px' => 'px',
                        'em' => 'em',
						'cm' => 'cm',
                        '%' => '%',
                    )
				);
				
				$fields[] = array(
					'id' => 'modal_height',
					'name' => 'Modal height', 
					'desc' => 'Change the height of the modal (in pixels). Default to "500px".<br />
							   <span style="color:red;"><u>Note:</u> The modal max height equals the screen height!</span>',
					'type' => 'text',
					'default' => '500',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '1',
					)										
				);
				
				$fields[] = array(
					'id' => 'modal_top_margin',
					'name' => 'Modal top margin', 
					'desc' => 'Change the top static margin of the modal (in pixels). Default to "50px".',
					'type' => 'text',
					'default' => '50',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '0',
					)										
				);
				
				
				$fields[] = array(
					'id' => 'modal_bottom_margin',
					'name' => 'Modal bottom margin', 
					'desc' => 'Change the bottom static margin of the modal (in pixels). Default to "50px".',
					'type' => 'text',
					'default' => '50',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '0',
					)										
				);
				
				$fields[] = array(
					'id' => 'modal_allow_fullscreen',
					'name' => 'Allow fullscreen?',
					'desc' => 'Add a button that allows opening the modal in fullscreen. Default to "Yes".',
					'type' => 'radio',
					'default' => 'yes',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				);
				
				$fields[] = array(
					'id' => 'modal_openFullscreen',
					'name' => 'Open fullscreen?',
					'desc' => 'Force to open the modal in fullscreen. Default to "No".',
					'type' => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				);
				
			/**
			 * Custom CSS */
			
			$fields[] = array(
				'id' => 'custom_css_section',
				'name' => 'Custom CSS',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
								
				$fields[] = array(
					'id' => 'custom_css',
					'name' => 'Custom CSS code',
					'desc' => 'Add your own CSS here',
					'type' => 'textarea',
					'default' => '',
				);
			
			return $fields;
			
		}
		
		
		/**
		 * Autocomplete settings
		 *
		 * @since 5.6
		 */
		function cspm_autocomplete_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Default Autocomplete Settings',
				'desc' => 'The Autocomplete is a feature that gives your map the type-ahead-search behavior of the Google Maps search field. When a user starts typing an address, autocomplete will fill in the rest. <br />
						   The following autocomplete settings are going to be used as the main settings of all single maps and the plugin add-ons that uses the autocomplete feature, and as the default autocomplete settings of your new maps. 
						   You can always override the default autocomplete settings when creating/editing a map.',
				'type' => 'title',
				'id'   => 'autocomplete_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => 'autocomplete_option',
				'name' => 'Use Autocomplete for address fields?',
				'desc' => 'Select "Yes" to enable this option in your maps. Defaults to "Yes".',
				'type' => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => 'autocomplete_strict_bounds',
				'name' => 'Restrict the search to the bounds?',
				'desc' => 'This option restricts the autocomplete search to the area within the current viewport. If this option is enabled, the GMaps API biases the search to the current viewport, but does not restrict it. 
				           Select "Yes" to enable this option in your map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => 'autocomplete_country_restrict',
				'name' => 'Restrict the search to specific countries?',
				'desc' => 'This option restricts the autocomplete search to particular countries. 
				           Select "Yes" to enable this option in your map, then, select your contry(ies). Defaults to "No".',
				'type' => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No'
				)
			);
			
			$fields[] = array(
				'name' => 'Countries',
				'desc' => 'Select the countries to be used with the autocomplete search.',
				'type' => 'pw_multiselect',
				'id' => 'autocomplete_countries',
				'options' => $this->countries_list,
			);
			
			return $fields;
				
		}
		

		/**
		 * Get all registred post types & remove "Progress Map's" CPT from the list
		 *
		 * @since 1.0
		 */
		function cspm_get_registred_cpts(){
				
			$wp_post_types_array = array(
				'post' => esc_attr__('Posts').' (post)', 
				'page' => esc_attr__('Pages').' (page)'
			);

			$all_custom_post_types = get_post_types(array('_builtin' => false), 'objects');
			
			$return_post_types_array = array();
				
			/** 
			 * Loop through default WP post types */
			 
			foreach($wp_post_types_array as $wp_post_type_name => $wp_post_type_label){
				$return_post_types_array[$wp_post_type_name] = $wp_post_type_label;					
			}
			
			/**  
			 * Loop through all custom post types */
			 
			foreach($all_custom_post_types as $post_type){
				$cspm_post_types = apply_filters('cspm_all_cpts', array($this->object_type)); //@since 4.7
				if(!in_array($post_type->name, $cspm_post_types))
					$return_post_types_array[$post_type->name] = $post_type->labels->name.' ('.$post_type->name.')';
			}
				
			return $return_post_types_array;
			
		}


		/**
		 * Get the list of all Map Styles from the file "inc/cspm-map-styles.php"
		 *
		 * @since 1.0
		 */
		function cspm_get_all_map_styles(){

			$map_styles_array = array();
			
			if(file_exists($this->plugin_path . 'inc/cspm-map-styles.php')){

				$map_styles = include($this->plugin_path . 'inc/cspm-map-styles.php');
				
				array_multisort($map_styles);
				
				foreach($map_styles as $key => $value){
					
					$value_output  = '';
					$value_output .= empty($value['new']) ? '' : ' <sup class="cspm_new_tag" style="margin:0 5px 0 -2px;">NEW</sup>';		
					$value_output .= $value['title'];				
					$value_output .= empty($value['demo']) ? '' : ' <span class="cspm_demo_tag"><a href="'.$value['demo'].'" target="_blank" class="cspm_blank_link"><small>Demo</small></a></span>';
					
					$map_styles_array[$key] = $value_output;
				
				}
				
			}
			
			return $map_styles_array;

		}
		
				
		/**
		 * This will return a list of all countries in English Language
		 *
		 * @since 5.6
		 */
		function cspm_get_countries(){
			
			if(file_exists($this->plugin_path . 'inc/countries/en/country.php')){

				$countries = include_once($this->plugin_path . 'inc/countries/en/country.php');
				
				return $countries;
				
			}else return;
			
		}

		
	}
	
}
