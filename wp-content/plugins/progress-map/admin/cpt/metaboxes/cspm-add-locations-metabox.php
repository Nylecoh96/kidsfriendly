<?php

/**
 * This class contains all the fields used in the widget "Progress Map: Add locations" 
 *
 * @version 1.0 
 */
 
if(!defined('ABSPATH')){
    exit; // Exit if accessed directly
}

if( !class_exists( 'CspmAddLocationsMetabox' ) ){
	
	class CspmAddLocationsMetabox{
		
		private $plugin_path;
		private $plugin_url;
		
		private static $_this;	
		
		public $plugin_settings = array();
		
		protected $metafield_prefix;
		
		/**
		 * The name of the post to which we'll add the metaboxes
		 * @since 1.0 */
		 
		public $object_type;
		
		public $post_id;
		
		public $toggle_before_row; //@since 5.3
		public $toggle_after_row; //@since 5.3
				
		function __construct($atts = array()){
			
			extract( wp_parse_args( $atts, array(
				'plugin_path' => '', 
				'plugin_url' => '',
				'object_type' => '',
				'plugin_settings' => array(), 
				'metafield_prefix' => '',
			)));
             
			self::$_this = $this;       
				           
			$this->plugin_path = $plugin_path;
			
			$this->plugin_url = $plugin_url;
			
			$this->plugin_settings = (array) $plugin_settings;
			
			$this->object_type = $object_type;

			$this->metafield_prefix = $metafield_prefix; 

			/**
			 * [@visible_metafield_prefix] | Remove the underscore from the begining of the custom fields names ...
			 * ... because we want them to be visible in the default WP widget "Custom fields" ... 
			 * ... and for compatibility with previous (very old) versions of the plugin! */
			
			$this->visible_metafield_prefix = str_replace('_', '', $metafield_prefix); 
			
			/**
			 * Get current post type */
			
			$post_id = 0;
			
			if(isset($_REQUEST['post'])){
				
				$post_id = $_REQUEST['post'];
			
			}elseif(isset($_REQUEST['post_ID'])){
				
				$post_id = $_REQUEST['post_ID'];
				
			}
			
			$this->post_id = $post_id;
						
			/**
			 * Include all required Libraries for this metabox */
			 
			$libs_path = array(
				'cmb2' => 'admin/libs/metabox/init.php',
				'cmb2-tabs' => 'admin/libs/metabox-tabs/cmb2-tabs.class.php',
				'cmb2-cs-gmaps' => 'admin/libs/metabox-cs-gmaps/cmb-cs-gmaps.php', //@since 3.5
				'cmb2-cs-file-input' => 'admin/libs/metabox-cs-file-input/cs-file-input.php', //@since 5.3				
			);
				
				foreach($libs_path as $lib_file_path){
					if(file_exists($this->plugin_path . $lib_file_path))
						require_once $this->plugin_path . $lib_file_path;
				}
			
			/**
			 * Load Metaboxes */

			add_action( 'cmb2_admin_init', array($this, 'cspm_add_locations_metabox') );

			/**
			 * Call .js and .css files */
			 
			add_filter( 'cmb2_enqueue_js', array($this, 'cspm_scripts') );
			
			$this->toggle_before_row = '<div class="postbox cmb-row cmb-grouping-organizer closed">
									 	<div class="cmbhandle" title="Click to toggle"><br></div>               
									 	<h3 class="cmb-group-organizer-title cmbhandle-title" style="padding: 11px 15px !important;">[title]</h3>
										<div class="inside">'; //@since 5.3
													
			$this->toggle_after_row = '</div></div>'; //@since 5.3

		}
	

		static function this() {
			
			return self::$_this;
		
		}
		
		
		function cspm_scripts(){
			
			global $typenow;
			
			/**
			 * Our custom metaboxes JS & CSS file must be loaded only on our CPT page */

			if(in_array($typenow, (array) $this->plugin_settings['post_types'])){
				
				/**
				 * Our custom metaboxes CSS */
				
				wp_enqueue_style('cspm-metabox-css');
				
				/**
				 * Dequeue "Conditionals" JS to prevent conflict with other plugins
				 * @since 3.6 */
				 
				add_action('admin_footer', function(){
					wp_dequeue_script('cmb2-conditionals');
				}, 999999);
					
			}
			
		}
	
	
		/**
		 * "Progress Map: Add locations" Metabox.
		 * This metabox will contain the form for adding new locations to the map
		 *
		 * @since 1.0
		 */
		function cspm_add_locations_metabox(){
			
			/**
			 * Add Locations Metabox options */
			 
			$cspm_add_locations_metabox_options = array(
				'id'            => $this->metafield_prefix . '_add_locations_metabox',
				'title'         => esc_attr__( 'Progress Map: Add locations', 'cspm' ),
				'object_types'  => isset($this->plugin_settings['post_types']) ? $this->plugin_settings['post_types'] : array(), // Post types
				'priority'   => 'high',
				//'context'    => 'side',
				'show_names' => true, // Show field names on the left		
				'closed' => false,		
			);
			
				/**
				 * Create post type Metabox */
				 
				$cspm_add_locations_metabox = new_cmb2_box( $cspm_add_locations_metabox_options );

				/**
				 * Display Add Locations Metabox fields */
			
				$this->cspm_add_locations_tabs($cspm_add_locations_metabox, $cspm_add_locations_metabox_options);
			
		}
		
		
		/**
		 * Buill all the tabs that contains "Progress Map" settings
		 *
		 * @since 1.0
		 */
		function cspm_add_locations_tabs($metabox_object, $metabox_options){
			
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
				 	 * Coordinates */
					 
					array(
						'id' => 'post_coordinates', 
						'title' => 'GPS Coordinates', 
						'callback' => 'cspm_post_coordinates_fields'
					),
					
					/**
				 	 * Marker */
					 
					array(
						'id' => 'post_marker', 
						'title' => 'Marker', 
						'callback' => 'cspm_post_marker_fields'
					),
					
					/**
				 	 * Marker Label
					 * @since 5.5 */
					 
					array(
						'id' => 'post_marker_label', 
						'title' => 'Marker Label', 
						'callback' => 'cspm_post_marker_label_fields'
					),

					/**
				 	 * Format & Media */
					 
					array(
						'id' => 'post_format_and_media', 
						'title' => 'Format & Media', 
						'callback' => 'cspm_post_format_and_media'
					),
					
				);

				/**
				 * Custom Fields of the extension "Submit locations"
				 * @since 5.3 */
				
				if(class_exists('CspmSubmitLocations')){ 
					$cspm_tabs[] = array(
						'id' => 'post_submited_custom_fields', 
						'title' => 'Custom Fields', 
						'callback' => 'cspm_post_submit_locations_custom_fields'
					);
				}
				
				foreach($cspm_tabs as $tab_data){
				 
					$tabs_setting['tabs'][] = array(
						'id'     => 'cspm_' . $tab_data['id'],
						'title'  => '<span class="cspm_tabs_menu_image"><img src="'.$this->plugin_url.'admin/img/add-locations-metabox/'.str_replace('_', '-', $tab_data['id']).'.png" style="width:20px;" /></span> <span class="cspm_tabs_menu_item">'.esc_attr__( $tab_data['title'], 'cspm' ).'</span>',						
						'fields' => call_user_func(array($this, $tab_data['callback'])),
					);
		
				}
			
			/*
			 * Set tabs */
			 
			$metabox_object->add_field( array(
				'id'   => 'cspm_add_locations_tabs',
				'type' => 'tabs',
				'tabs' => $tabs_setting
			) );
			
			return $metabox_object;
			
		}
		
		
		/**
		 * Post Coordinates Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_post_coordinates_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'GPS Coordinates',
				'desc' => 'Latitude and longitude of your location. Convert an address or a place to latitude & longitude. Fill the address field, click on "Search", then, click on "Get Pinpoint" to display its latitude and longitude.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_gps_coordinates',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$remove_gmaps_api = isset($this->plugin_settings['remove_gmaps_api']) ? $this->plugin_settings['remove_gmaps_api'] : array(); //@since 5.1
			
			$fields[] = array(
				'name' => 'Address & GPS Coordinates',
				'desc' => 'Enter an address, hit search, drag the marker to find the exact location, then, click on "Get Pinpoint" to copy the coordinates, or simply enter the coordinates in the "Latitude" & "Longitude" fields.',
				'id' => $this->metafield_prefix . '_location',
				'type' => 'cs_gmaps',
				'options' => array(
					'api_key' => isset($this->plugin_settings['api_key']) ? $this->plugin_settings['api_key'] : '',
					'disable_gmaps_api' => in_array('disable_backend', (array) $remove_gmaps_api) ? true : false, //@edited 5.1
					'disable_gmap3_plugin' => false,
					'address_field_name' => CSPM_ADDRESS_FIELD,
					'lat_field_name' => CSPM_LATITUDE_FIELD,
					'lng_field_name' => CSPM_LONGITUDE_FIELD,
					'split_values' => true,
					'map_height' => '250px',
					'map_width' => '100%',
					'map_center' => isset($this->plugin_settings['map_center']) ? $this->plugin_settings['map_center'] : '',
					'map_zoom' => 12,
					'labels' => array(
						'address' => 'Enter a location & search or Geolocate your position',						
						'latitude' => 'Latitude',
						'longitude' => 'Longitude',
						'search' => 'Search',
						'pinpoint' => 'Get pinpoint',
					),
					'secondary_latlng' => array(
						'show' => true,
						'field_name' => CSPM_SECONDARY_LAT_LNG_FIELD
					)
				),
			);

			return $fields;
			
		}
		
		
		/**
		 * Marker icon Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_post_marker_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Marker',
				'desc' => '',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_marker',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			 
			$fields[] = array(				
				'id' => CSPM_MARKER_ICON_FIELD,
				'name' => 'Marker icon',
				'desc' => 'By default, the marker image to be used for this post is the one set in your map settings 
						   under <strong>"Marker settings => Marker image"</strong>. Uploading an image in this field will override the default marker image and also 
						   the one uploaded for this post category in <strong>"Marker categories settings"</strong>.',
				'type' => 'file',
				'text' => array(
					'add_upload_file_text' => 'Upload marker icon',
				),
				'preview_size' => array(32, 32),
				'query_args' => array(
					'type' => 'image',
				),
			);
		
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_icon_height',
				'name' => 'Marker image height', 
				'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
						  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
						  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
				'type' => 'text',
				'default' => isset($this->plugin_settings['marker_icon_height']) ? $this->plugin_settings['marker_icon_height'] : '',
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
				'id' => $this->metafield_prefix . '_marker_icon_width',
				'name' => 'Marker image width', 
				'desc' => 'Specify the image width (in pixels). Set to -1 to ignore or to automatically calculate the width (for PNG/JPEG/GIF images).<br />
						  <span style="color:red;"><strong>Note: When set to -1, the image width will be automatically calculated but only if the image type is PNG/JPEG/GIF.
						  If you\'re using SVG icons, the image width will not be calculated and you may need to specify it.</strong></span>',
				'type' => 'text',
				'default' => isset($this->plugin_settings['marker_icon_width']) ? $this->plugin_settings['marker_icon_width'] : '',
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
				'id'   => CSPM_SINGLE_POST_IMG_ONLY_FIELD,
				'name' => 'Use this image for single post only?',
				'desc' => 'Display the "Marker icon" only on this post\'s single maps.',
				'type' => 'radio_inline',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No',
				),
				'default' => 'no',
			);

			return $fields;
			
		}
		
		
		/**
		 * Marker Label Fields 
		 *
		 * @since 5.5
		 */
		function cspm_post_marker_label_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Marker Label',
				'desc' => 'A marker label is a letter or number that appears inside a marker',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_marker_label',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			 
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_label_options',
				'name' => '', 
				'desc' => '<span style="color:red">The below options will allow you to override the default marker labels options set in your map settings (Check <strong>"Marker labels settings"</strong>) but 
						  you need at least to enable marker labels by editing your map and selecting the option <strong>"Marker labels settings => Use marker labels => Yes"</strong>. 
						  <br />For example, if you don\'t want to display the marker label for this specific post, select the option <strong>"Hide marker label => No"</strong>. The same 
						  thing goes for the color & font options.</span><br />
						  <br /><strong style="color:red">To add the marker label, enter a text in the field "Text"</strong>.',
				'type' => 'group',
				'repeatable' => false,
				'options' => array(
					'group_title' => esc_attr__( 'Marker label', 'cspm' ),
					'sortable' => false,
					'closed' => false,
				),
				'fields' => array(	
					array(
						'id'   => 'hide_label',
						'name' => 'Hide marker label',
						'desc' => 'This option specify the appearance of marker labels. Default "No".',
						'type' => 'radio_inline',
						'options' => array(
							'yes' => 'Yes',
							'no' => 'No',
						),
						'default' => 'no',
					),
					array(
						'id' => 'text',
						'name' => 'Label text', 
						'desc' => 'Enter the text to be displayed in the label.',
						'type' => 'text',
						'default' => '',
					),	
					array(
						'id' => 'label_origin',
						'name' => 'Label Origin',
						'desc' => 'Specify the origin of the label relative to the top-left corner of the marker image. <strong>By default, the origin is located in the center point of the image.</strong>',
						'type' => 'title',
						'attributes' => array(
							'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
						),
					),
					array(
						'id' => 'top',
						'name' => 'Top position', 
						'desc' => '',
						'type' => 'text',
						'default' => '',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '0',
						),
					),
					array(
						'id' => 'left',
						'name' => 'Left position', 
						'desc' => '',
						'type' => 'text',
						'default' => '',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '0',
						),
					),
					array(
						'id' => 'label_style',
						'name' => 'Label Style',
						'desc' => 'Specify the appearance of the marker labels.',
						'type' => 'title',
						'attributes' => array(
							'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
						),
					),
					array(
						'id' => 'color',
						'name' => 'Color',
						'desc' => 'The color of the label text. Default color is  the one set in your map settings in the field "Marker label settings => Color".<br />
								  <span style="color:red">Leave this field empty if your don\'t want to override the default settings!</span><br />
								  Find more about the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/color" target="_blank" class="cspm_blank_link">color</a> property.',
						'type' => 'colorpicker',
						'default' => '',															
					),
					array(
						'id' => 'fontFamily',
						'name' => 'Font family', 
						'desc' => 'The font family of the label text (equivalent to the CSS font-family property). Default size is  the one set in your map settings in the field "Marker label settings => Font family".<br />
								  <span style="color:red">Leave this field empty if your don\'t want to override the default settings!</span><br />
								  Find more about the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/font-family" target="_blank" class="cspm_blank_link">font-family</a> property.',
						'type' => 'text',
						'default' => '',
					),	
					array(
						'id' => 'fontWeight',
						'name' => 'Font weight', 
						'desc' => 'The font weight of the label text (equivalent to the CSS font-weight property). Default size is  the one set in your map settings in the field "Marker label settings => Font weight".<br />
								  <span style="color:red">Leave this field empty if your don\'t want to override the default settings!</span><br />
								  Find more about the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight" target="_blank" class="cspm_blank_link">font-weight</a> property.',
						'type' => 'text',
						'default' => '',
					),	
					array(
						'id' => 'fontSize',
						'name' => 'Font size', 
						'desc' => 'The font size of the label text (equivalent to the CSS font-size property). Default size is the one set in your map settings in the field "Marker label settings => Font size".<br />
								  <span style="color:red">Leave this field empty if your don\'t want to override the default settings!</span><br />
								  Find more about the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/font-size" target="_blank" class="cspm_blank_link">font-size</a> property.',
						'type' => 'text',
						'default' => '',
					),					
				)
			);

			return $fields;
			
		}


		/**
		 * Post Format & Media Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_post_format_and_media(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Format & Media',
				'desc' => 'Set your post format & open it inside a modal. You can pop-up the single post or the media files only. Choose your post/location format and upload the media files.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_format_and_media',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$nearby_palces_option = (!class_exists('CspmNearbyMap')) ? '<strike>Nearby Places Map</strike> <small style="color:red;">(This option requires the addon <a href="https://codecanyon.net/item/nearby-places-wordpress-plugin/15067875?ref=codespacing" target="_blank" class="cspm_blank_link">"Nearby Places"</a>!)</small>' : 'Nearby Places Map';
			$nearby_palces_option_desc = (!class_exists('CspmNearbyMap')) ? '' : '<br /><span style="color:red;"><u>Note:</u> When using the option <strong>"Nearby Places Map"</strong>, make sure to create a new page and assign it to the template page <strong>"Nearby Places Modal"</strong>!</span>';
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_post_format',
				'name' => 'Format',
				'desc' => 'Select the format. Default "Standard". By clicking on the post marker, 
						   the post link or media files will be opened inside a modal/popup! '.$nearby_palces_option_desc,
				'type' => 'radio',				
				'options' => array(
					'standard' => 'Standard',
					'link' => 'Link (Single post page)',
					'nearby_places' => $nearby_palces_option,
					'gallery' => 'Gallery',
					'image' => 'Image',
					'audio' => 'Audio',
					'embed' => 'Embed (Video, Audio playlist, Website and more)',
					'all' => 'All Media',
				),
				'default' => 'standard',
			);
			
			$fields[] = array(
				'id'   => $this->metafield_prefix . '_enable_media_marker_click',
				'name' => 'Open media files on marker click?',
				'desc' => 'By default, when a user clicks on a marker, the media files will pop up on the screen. 
						   You can disable this option from here. Defaults to "Yes".<br />
						   <span style="color:red;"><u>Note:</u> Media files can also be displayed from the map settings under "Marker menu settings => "Media Modal" link"!</span>',
				'type' => 'radio_inline',
				'default' => 'yes',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No',
				),				
			);
			
			/**
			 * Gallery */
			  
			$fields[] = array(
				'name' => 'Gallery',
				'id' => $this->metafield_prefix . '_post_gallery',
				'type' => 'file_list',
				'desc' => 'Upload images. You can sort image by draging an image to the location where you want it to be.<br />
						   <span style="color:red;">To be used with the format "Gallery" or "All"!</span>',
				'text' => array(
					'add_upload_files_text' => 'Upload image(s)',
				),
				'preview_size' => array( 70, 50 ),
				'query_args' => array(
					'type' => 'image',
				)		
			);
			
			/**
			 * Image */
			 
			$fields[] = array(
				'name' => 'Image',
				'id' => $this->metafield_prefix . '_post_image',
				'type' => 'file',
				'desc' => 'Add or upload the image. <span style="color:red;">To be used with the format "Image" or "All"!</span>',
				'text' => array(
					'add_upload_file_text' => 'Upload image',
				),
				'preview_size' => array( 100, 100 ),
				'query_args' => array(
					'type' => 'image',
				),
				'options' => array(
					'url' => false
				),		
			);
			
			/**
			 * Audio */
			 
			$fields[] = array(
				'name' => 'Audio',
				'id' => $this->metafield_prefix . '_post_audio',
				'type' => 'file',
				'desc' => 'Add or upload the audio file. <span style="color:red;">To be used with the format "Audio" or "All"!</span>',
				'text' => array(
					'add_upload_file_text' => 'Upload audio file',
				),
				'preview_size' => array( 100, 100 ),
				'query_args' => array(
					'type' => 'audio',
				)		
			);
			
			/**
			 * Embed */
			 
			$fields[] = array(
				'id' => $this->metafield_prefix . '_post_embed',
				'name' => 'Embed URL',
				'desc' => 'Enter the URL that should be embedded. It could be a video (Youtube, vimeo ...) URL, Audio playlist URL, Website URL and more. <span style="color:red;">To be used with the format "Embed" or "All"!</span><br />
						  <span style="color:red">Check the list of supported services <a href="https://wordpress.org/support/article/embeds/" target="_blank" class="cspm_blank_link">https://wordpress.org/support/article/embeds/</a>.<br />
						  If the URL is a valid url to a supported provider, the plugin will use the embed code provided to it. Otherwise, it will be ignored!</span>',
				'type' => 'text_url',
			);

			return $fields;
						 
		}
		
		
		/** 
		 * Custom fields added from the extension "Submit Locations".
		 *
		 * @sicne 5.3 
		 */	
		function cspm_post_submit_locations_custom_fields(){
			
			$fields = array();
	
			if(!class_exists('CSProgressMap'))
				return $fields;
				
			$CSProgressMap = CSProgressMap::this();
			
			$fields[] = array(
				'name' => 'Custom Fields',
				'desc' => 'This section contains all custom fields added from the extension <strong>"Submit Locations"</strong>. 
						   You can edit the custom fields from here!',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_custom_fields',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			if(!class_exists('CspmslSubmitForm'))
				return $fields;
			
			$CspmslSubmitForm = CspmslSubmitForm::this();
			
			/**
			 * Get all map using the same post type as the current post */
			
			$cspm_prefix = $CSProgressMap->metafield_prefix;
			 			
			$all_cspm_maps = get_posts(array(
				'post_type' => $CSProgressMap->object_type,
				'post_status' => 'publish',
				'posts_per_page' => -1,
			));
			
			if($all_cspm_maps){
				
				$displayed_map_ids = array();
				
				foreach($all_cspm_maps as $cspm_map){
								
					setup_postdata($cspm_map);
					
					$map_id = $cspm_map->ID; // Map ID
					
					$current_post_type = $CSProgressMap->cspm_get_current_post_type();
					$map_post_type = (array) get_post_meta($map_id, $cspm_prefix.'_post_type', false);
					
					/**
					 * Compare map "post type" and current post "post type" */
					 
					if(in_array($current_post_type, $map_post_type) && !in_array($map_id, $displayed_map_ids)){
						
						$displayed_map_ids[] = $map_id;
						
						/**
						 * Get all "Submit locations" custom fields */
						 
						$custom_fields = get_post_meta($map_id, $cspm_prefix.'_new_custom_fields', true);
											
						if(is_array($custom_fields)){
							
							/**
							 * Render all custom fields */
							 
							$render_fields = $CspmslSubmitForm->cspmsl_render_new_fields(NULL, $custom_fields, $this->post_id, false);
	
							$i = 0;
							
							foreach($render_fields as $field){
								
								
								/**
								 * Checking the name to insure only custom fields with a valid Name/ID will be displayed */
								
								$field_name = $this->cspm_setting_exists('name', $field);
								
								if(!empty($field_name)){
									
									if($i == 0){
										$map_title = get_the_title($map_id);
										$map_link = get_edit_post_link($map_id);
										$field['before_row'] = str_replace(
											array('[title]', ' closed'), 
											array('Custom Fields of "<u>' . $map_title . '</u>"', ''), 
											$this->toggle_before_row.'<p class="cmb2-metabox-description" style="margin:0 0 10px 0; padding-top:0;">These are all custom fields added from the map "<u><a href="'.$map_link.'" target="_blank">'.$map_title.'</a></u>" at "<u>Submit Locations Settings => Add/Edit custom fields</u>"!</p><hr />'
										);
									}
									
									if($i == count($render_fields)-1) 
										$field['after_row'] = $this->toggle_after_row;
									
									$fields[] = $field;	
									
								}
								
								$i++;
								
							}
							
						}
						
					}
								
				}
			
				wp_reset_postdata();
				
			}
			
			/**
			 * Display a message when no custom field is available */
			 
			if(count($fields) == 1){
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_no_custom_fields_msg',
					'name' => '<strong>We could\'t find any custom field!</strong><br /><br />
							  Nothing bad! If you want to add new custom fields, edit your map(s) 
							  and go to the menu "<u>Submit Locations Settings => Add/Edit custom fields</u>".',
					'desc' => '',
					'type' => 'title',
					'attributes' => array(
						'style' => 'font-size:16px; color:#fff; background:#000; text-align:center; padding:30px; font-weight:200;'
					),
				);
				
			}
			
			return $fields;
						 
		}
		
		
		/**
		 * This will get the default value of field based on the one selected in the plugin settings.
		 * If a field has a default value in the plugin settings, we'll use, otherwise, we'll use a given default value instead.
		 *
		 * @since 1.0
		 */
		function cspm_get_field_default($option_id, $default_value = ''){
			
			/**
			 * We'll check if the default settings can be found in the array containing the "(shared) plugin settings".
			 * If found, we'll use it. If not found, we'll use the one in [@default_value] instead. */
			 
			$default = $this->cspm_setting_exists($option_id, $this->plugin_settings, $default_value);
			
			return $default;
			
		}
		
		
		/**
		 * Check if array_key_exists and if empty() doesn't return false
		 * Replace the empty value with the default value if available 
		 * @empty() return false when the value is (null, 0, "0", "", 0.0, false, array())
		 *
		 * @since 1.0
		 */
		function cspm_setting_exists($key, $array, $default = ''){
			
			$array_value = isset($array[$key]) ? $array[$key] : $default;
			
			$setting_value = empty($array_value) ? $default : $array_value;
			
			return $setting_value;
			
		}

	}
	
}	
		
