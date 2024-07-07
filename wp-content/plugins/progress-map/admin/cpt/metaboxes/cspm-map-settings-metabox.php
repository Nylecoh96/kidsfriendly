<?php

/**
 * This class contains all the fields used in the widget "Progress Map" 
 *
 * @version 1.1 
 */
 
if(!defined('ABSPATH')){
    exit; // Exit if accessed directly
}

if( !class_exists( 'CspmMetabox' ) ){
	
	class CspmMetabox{
		
		private $plugin_path;
		private $plugin_url;
		
		private static $_this;	
		
		public $plugin_settings = array();
		
		protected $metafield_prefix;
		
		/**
		 * The name of the post to which we'll add the metaboxes
		 * @since 1.0 */
		 
		public $object_type;
		
		/**
		 * The ID of the current map */
		
		public $object_id;
		
		public $registred_cpts;
		
		public $selected_cpt;
		public $countries_list;
		
		public $toggle_before_row;
		public $toggle_after_row;
				
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
			
			$this->plugin_settings = $plugin_settings;
			
			$this->metafield_prefix = $metafield_prefix;
			
			$this->object_type = $object_type;

			/**
			 * Include all required Libraries for this metabox */
			 
			$libs_path = array(
				'cmb2' => 'admin/libs/metabox/init.php',
				'cmb2-tabs' => 'admin/libs/metabox-tabs/cmb2-tabs.class.php',
				'cmb2-conditional' => 'admin/libs/metabox-conditionals/cmb2-conditionals.php',
				'cmb2-field-select2' => 'admin/libs/metabox-field-select2/cmb-field-select2.php',
				'cmb2-ajax-search' => 'admin/libs/metabox-ajax-search/cmb2-field-ajax-search.php', //@since 5.2
				'cmb2-radio-image' => 'admin/libs/metabox-radio-image/metabox-radio-image.php',
				'cmb2-field-order' => 'admin/libs/metabox-field-order/cmb2-field-order.php', //@since 3.1
				'cmb2-cs-gmaps' => 'admin/libs/metabox-cs-gmaps/cmb-cs-gmaps.php', //@since 5.2
				'cmb2-cs-gmaps-drawing' => 'admin/libs/metabox-cs-gmaps-drawing/cmb-cs-gmaps-drawing.php', //@since 5.2
			);
				
				foreach($libs_path as $lib_file_path){
					if(file_exists($this->plugin_path . $lib_file_path))
						require_once $this->plugin_path . $lib_file_path;
				}
			
			/**
			 * Load Metaboxes */

			add_action( 'cmb2_admin_init', array($this, 'cspm_progress_map_metabox') );
			
			/**
			 * Call .js and .css files */
			 
			add_filter( 'cmb2_enqueue_js', array($this, 'cspm_scripts') );
			
			/**
			 * Get selected post type based on the post ID */
			 
			$post_id = 0;
			
			if(isset($_REQUEST['post'])){
				
				$post_id = $_REQUEST['post'];
			
			}elseif(isset($_REQUEST['post_ID'])){
				
				$post_id = $_REQUEST['post_ID'];
				
			}
			
			$this->selected_cpt = get_post_meta( $post_id, $this->metafield_prefix . '_post_type', true );
			
			$this->object_id = $post_id;
			
			$this->countries_list = $this->cspm_get_countries();
			
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
			
			global $typenow;
			
			/**
			 * Our custom metaboxes JS & CSS file must be loaded only on our CPT page */

			if($typenow === $this->object_type){
				
				/**
				 * Our custom metaboxes CSS */
				
				wp_enqueue_style('cspm-metabox-css');

				/**
				 * Gradstop | Generate gradient color for Heatmap Layer
				 * @since 5.3 */
				 
				wp_register_script('gradstop', $this->plugin_url .'assets/js/gradstop/gradstopUMD.js', array(), false, true);				
				wp_enqueue_script('gradstop');
				
			}
			
		}
	
	
		/**
		 * "Progress Map" Metabox.
		 * This metabox will contain all the settings needed for "Progress Map"
		 *
		 * @since 1.0
		 */
		function cspm_progress_map_metabox(){
			
			/**
			 * 1. Post type metabox options */
			 
			$cspm_cpt_metabox_options = array(
				'id'            => $this->metafield_prefix . '_pm_cpt_metabox',
				'title'         => esc_attr__( '(Custom) Post Type & Map Settings', 'cspm' ),
				'object_types'  => array( $this->object_type ), // Post type
				'priority'   => 'high',
				//'context'    => 'side',
				'show_names' => true, // Show field names on the left				
			);
			
				/**
				 * Create post type Metabox */
				 
				$cspm_cpt_metabox = new_cmb2_box( $cspm_cpt_metabox_options );

				/**
				 * Post type metabox field(s) */
				 
				$cspm_cpt_metabox->add_field( array(
					'id' => $this->metafield_prefix . '_post_type',
					'name' => 'Main post type',
					'desc' => 'Select the post type to use with this map.<br /><br />
					<span style="color:red;">1. Select a post type, then, click on the button <strong>"Publish"</strong> to save the map. New metabox(es) will be available for you to build your map!</span><br /><br />
					<span style="color:red;">2. If in the future, you want to change the post type, select your new post type and click on the button <strong>"Update"</strong> to adapt/synchronize the Metabox(es) below to the new update!</span><br /><br />
					<span style="color:red;">3. If you don\'t find your post type in the list, click on the menu <strong>"Progress Map"</strong> and make sure to select your post type in the field <strong>"Plugin settings => Post types"</strong>!</span>',
					'type' => 'select',
					'show_option_none' => false,
					'default' => '',
					'options_cb' => function(){
						// Note: Using simply "$this->cspm_get_selected_cpts();" causes an error for some websites!!
						$CspmMetabox = CspmMetabox::this();
						return $CspmMetabox->cspm_get_selected_cpts();
					},
					'before_row' => '<div class="cspm_single_field">',
					'after_row' => '</div>',				
				));

				/** 
				 * Define the map type
				 * @since 3.3 */
				 
				$cspm_cpt_metabox->add_field( array(
					'id'   => $this->metafield_prefix . '_map_type',
					'name' => 'How would you like to use this map?',
					'desc' => 'Choose whether to use this map as a map that displays locations when it has been loaded, 
					  		   or as a search map. The search map will be empty once loaded, it displays locations only 
							   when a search request has been sent from one of the available search/filter tools.<br /><br />
							   <span style="color:red;"><strong>Hint:</strong> Use the Heatmap layer with the search map to depict the intensity of locations on your map! 
							   <strong>"Progress Map settings => Map settings => Heatmap Layer"</strong>!</span>',
					'type' => 'radio',
					'default' => 'normal_map',
					'options' => array(
						'normal_map' => 'As normal map <small>(Display locations after map has been loaded)</small>',
						'search_map' => 'As search map <small>(Display locations after sending a search request)</small>',
					),
					'before_row' => '<div class="cspm_single_field">',
					'after_row' => '</div>',
					'show_on_cb'   => function(){
						global $pagenow;
						return ($pagenow == 'post-new.php') ? false : true;
					},									
				));

				/** 
				 * Show locations with no LatLng
				 * @since 4.0 */
				 
				$cspm_cpt_metabox->add_field( array(
					'id'   => $this->metafield_prefix . '_optional_latlng',
					'name' => 'Display locations with no coordinates?',
					'desc' => 'This option allow you to display locations with no available coordinates (Latitude & Longitude). 
							  If you set this option to "Yes", these locations will be displayed on the carousel (or the list, if you use the extension "List & Filter").',
					'type' => 'radio',
					'default' => 'false',
					'options' => array(
						'true' => esc_attr__('Yes. Display locations with no available coordinates', 'cspm'),
						'false' => esc_attr__('No. Hide locations with no available coordinates', 'cspm'),
					),
					'before_row' => '<div class="cspm_single_field">',
					'after_row' => '</div>',
					'show_on_cb'   => function(){
						global $pagenow;
						return ($pagenow == 'post-new.php') ? false : true;
					},									
				));
				
				/**
				 * Choose single post link
				 * @since 5.0 */
				 
				$cspm_cpt_metabox->add_field( array(
					'id' => $this->metafield_prefix . '_single_posts_link',
					'name' => 'How to get the posts link?',
					'desc' => 'This option allow you to override the parameter <strong style="font-weight: 400;">"Progress Map => Plugin settings => "Outer links" custom field name"</strong>. 
							   Check that parameter for more infos! Default to "Using the default single post URL"' ,
					'type' => 'radio',
					'options' => array(
						'default' => esc_attr__('Using the single post URL', 'cspm'),
						'custom' => esc_attr__('From a custom field', 'cspm'),
					),
					'default' => 'default',
					'before_row' => '<div class="cspm_single_field">',
					'after_row' => '</div>',
					'show_on_cb'   => function(){
						global $pagenow;
						return ($pagenow == 'post-new.php') ? false : true;
					},
				));
				
			/**
			 * 2. Shortcode metabox options */
			 
			$cspm_shortcode_metabox_options = array(
				'id'            => $this->metafield_prefix . '_pm_shortcode_widget',
				'title'         => esc_attr__( 'Map Shortcode', 'cspm' ),
				'object_types'  => array( $this->object_type ), // Post type
				'show_on_cb'   => function(){
					global $pagenow;
					return ($pagenow == 'post-new.php') ? false : true;
				},				
				'priority'   => 'low',
				'context'    => 'side',
				'show_names' => true, // Show field names on the left
			);
			
				/**
				 * Create Shortcode Metabox */
				 
				$cspm_cpt_metabox = new_cmb2_box( $cspm_shortcode_metabox_options );
				
				$post_id = !is_array($this->object_id) ? $this->object_id : ''; // Fixed an odd error (Array to string conversion) when restoring multiple posts from the trash!
				
				$cspm_cpt_metabox->add_field( array(
					'id' => $this->metafield_prefix . '_shortcode_text',
					'name' => 'To display this map in a WP page, use this shortcode:',
					'desc' => '<pre class="cspm_copy_to_clipboar">[cspm_main_map id="'.$post_id.'"]</pre>',
					'type' => 'title',
					'attributes' => array(
						'style' => 'font-size:13px; font-weight:600; font-style:normal; padding:20px 10px 0 10px;'
					),					
				));
				
				$cspm_cpt_metabox->add_field( array(
					'id' => $this->metafield_prefix . '_shortcode_php_code',
					'name' => 'To insert this map in a page template, use this PHP code:',
					'desc' => '<pre class="cspm_copy_to_clipboar">echo do_shortcode(\'[cspm_main_map id="'.$post_id.'"]\');</pre>',
					'type' => 'title',
					'attributes' => array(
						'style' => 'font-size:13px; font-weight:600; font-style:normal; padding:20px 10px 0 10px;'
					),					
				));
									
			
			/**
			 * 3. Progress Map settings metabox options */
			 
			$cspm_metabox_options = array(
				'id'            => $this->metafield_prefix . '_pm_metabox',
				'title'         => '<img src="'.$this->plugin_url.'img/progress-map.png" style="width:19px; margin:0 10px -3px 0;" />'.esc_attr__( 'Progress Map Settings', 'cspm' ),
				'object_types'  => array( $this->object_type ), // Post type
				'show_on_cb'   => function(){
					global $pagenow;
					return ($pagenow == 'post-new.php') ? false : true;
				},
				// 'context'    => 'normal',
				'priority'   => 'high',
				'show_names' => true, // Show field names on the left
				// 'cmb_styles' => false, // false to disable the CMB stylesheet
				// 'closed'     => true, // true to keep the metabox closed by default
				// 'classes'    => 'extra-class', // Extra cmb2-wrap classes
				// 'classes_cb' => 'yourprefix_add_some_classes', // Add classes through a callback.
			);
			
				/**
				 * Create Progress Map settings Metabox */
					 
				$cspm_metabox = new_cmb2_box( $cspm_metabox_options );

				/**
				 * Display Progress Map settings fields */
			
				$this->cspm_progress_map_settings_tabs($cspm_metabox, $cspm_metabox_options);
			
		}
		
		
		/**
		 * Buill all the tabs that contains "Progress Map" settings
		 *
		 * @since 1.0
		 */
		function cspm_progress_map_settings_tabs($metabox_object, $metabox_options){
			
			/**
			 * Setting tabs */
			 
			$tabs_setting = array(
				'args' => $metabox_options,
				'tabs' => array()
			);
				
				/**
				 * Tabs array
				 * @edited 5.5 [Added 'cspm_admin_tabs' hook] */
				 
				$cspm_tabs = apply_filters('cspm_admin_tabs', array(
					
					/**
				 	 * Query Settings */
					 
					array(
						'id' => 'query_settings', 
						'title' => 'Query settings', 
						'callback' => 'cspm_query_fields'
					),
					
					/**
				 	 * Layout Settings */
					 					
					array(
						'id' => 'layout_settings', 
						'title' => 'Layout settings', 
						'callback' => 'cspm_layout_fields'
					),
					
					/**
				 	 * Map Settings */
					 					
					array(
						'id' => 'map_settings', 
						'title' => 'Map settings', 
						'callback' => 'cspm_map_fields'
					),
					
					/**
				 	 * Map Style Settings */
					 					
					array(
						'id' => 'map_style_settings', 
						'title' => 'Map style settings', 
						'callback' => 'cspm_map_style_fields'
					),
					
					/**
					 * Marker settings
					 * @since 4.0 */
					 
					array(
						'id' => 'marker_settings', 
						'title' => 'Marker settings', 
						'callback' => 'cspm_marker_fields'
					),
					
					/**
					 * Clustering settings
					 * @since 4.0 */
					 
					array(
						'id' => 'clustering_settings', 
						'title' => 'Marker Clustering settings', 
						'callback' => 'cspm_clustering_fields'
					),
					
					/**
					 * Marker labels settings
					 * @since 4.0 */
					 
					array(
						'id' => 'marker_labels_settings', 
						'title' => 'Marker Labels settings', 
						'callback' => 'cspm_marker_labels_settings'
					),
					
					/**
				 	 * Marker Popup Settings
					 * @since 4.0 */
					 					
					array(
						'id' => 'marker_popups_settings', 
						'title' => 'Marker popups settings', 
						'callback' => 'cspm_marker_popups_fields'
					),
					
					/**
				 	 * Marker Menu Settings
					 * @since 5.5 */
					 					
					array(
						'id' => 'marker_menu_settings', 
						'title' => 'Marker menu settings', 
						'callback' => 'cspm_marker_menu_fields'
					),
					
					/**
				 	 * Marker Categories Settings */
					 					
					array(
						'id' => 'marker_categories_settings', 
						'title' => 'Marker categories settings', 
						'callback' => 'cspm_marker_categories_fields'
					),
					
					/**
				 	 * Infobox Settings */
					 					
					array(
						'id' => 'infobox_settings', 
						'title' => 'Infobox settings', 
						'callback' => 'cspm_infobox_fields'
					),
					
					/**
					 * Geotargeting settings
					 * @since 4.0 */
					 					
					array(
						'id' => 'geotargeting_settings', 
						'title' => 'Geo-targeting settings', 
						'callback' => 'cspm_geotargeting_settings'
					),
					
					/**
				 	 * Heatmap layer Settings
					 * @since 5.3 */
					 					
					array(
						'id' => 'heatmap_layer_settings', 
						'title' => 'Heatmap layer settings', 
						'callback' => 'cspm_heatmap_layer_fields'
					),
					
					/**
				 	 * KML Settings */
					 					
					array(
						'id' => 'kml_settings', 
						'title' => 'KML layers settings', 
						'callback' => 'cspm_kml_fields'
					),
					
					/**
				 	 * Ground Overlays Settings 
					 * @since 4.0 */
					 					
					array(
						'id' => 'ground_overlays_settings', 
						'title' => 'Ground Overlays Settings', 
						'callback' => 'cspm_ground_overlays_fields'
					),
					
					/**
				 	 * Polylines Settings
					 * @since 4.0 */
					 					
					array(
						'id' => 'polylines_settings', 
						'title' => 'Polylines settings', 
						'callback' => 'cspm_polylines_fields'
					),
					
					/**
				 	 * Polygons Settings 
					 * @since 4.0 */
					 					
					array(
						'id' => 'polygons_settings', 
						'title' => 'Polygons settings', 
						'callback' => 'cspm_polygons_fields'
					),
					
					/**
				 	 * Map Holes Settings 
					 * @since 4.0 */
					 					
					array(
						'id' => 'map_holes_settings', 
						'title' => 'Map Holes settings', 
						'callback' => 'cspm_map_holes_fields'
					),
										
					/**
				 	 * Carousel Settings */
					 					
					array(
						'id' => 'carousel_settings', 
						'title' => 'Carousel settings', 
						'callback' => 'cspm_carousel_fields'
					),
					
					/**
				 	 * Carousel Settings */
					 					
					array(
						'id' => 'carousel_style', 
						'title' => 'Carousel style', 
						'callback' => 'cspm_carousel_style_fields'
					),
					
					/**
				 	 * Carousel Items Settings */
					 					
					array(
						'id' => 'carousel_items_settings', 
						'title' => 'Carousel items settings', 
						'callback' => 'cspm_carousel_items_fields'
					),
					
					/**
				 	 * Posts Count Settings */
					 					
					array(
						'id' => 'posts_count_settings', 
						'title' => 'Posts count settings', 
						'callback' => 'cspm_posts_count_fields'
					),
					
					/**
				 	 * Faceted Search Settings */
					 					
					array(
						'id' => 'faceted_search_settings', 
						'title' => 'Faceted search settings', 
						'callback' => 'cspm_faceted_search_fields'
					),
					
					/**
				 	 * Search Form Settings */
					 					
					array(
						'id' => 'search_form_settings', 
						'title' => 'Search form settings', 
						'callback' => 'cspm_search_form_fields'
					),
					
					/**
				 	 * Zooming to Countries Settings */
					 					
					array(
						'id' => 'zoom_to_country_settings', 
						'title' => 'Zooming to countries settings', 
						'callback' => 'cspm_zoom_to_country_fields'
					),
					
					/**
				 	 * Nearby points of interest Settings
					 * @since 3.2 */
					 					
					array(
						'id' => 'nearby_places_settings', 
						'title' => 'Nearby points of interest settings', 
						'callback' => 'cspm_nearby_places_fields'
					),
					
					/**
				 	 * Autocomplete Settings
					 * @since 4.0 */
					 					
					array(
						'id' => 'autocomplete_settings', 
						'title' => 'Autocomplete settings', 
						'callback' => 'cspm_autocomplete_fields'
					),

					/**
				 	 * Custom CSS */
					 										
					array(
						'id' => 'customize', 
						'title' => 'Customize', 
						'callback' => 'cspm_customize_fields'
					),
					
				));
				
				foreach($cspm_tabs as $tab_data){
				 	
					$tab_icon_src = apply_filters('cspm_admin_tab_icon_'.$tab_data['id'], $this->plugin_url.'admin/img/'.str_replace('_', '-', $tab_data['id']).'.png'); //@added 5.5
					
					$tabs_setting['tabs'][] = array(
						'id'     => 'cspm_' . $tab_data['id'],
						'title'  => '<span class="cspm_tabs_menu_image"><img src="'.$tab_icon_src.'" style="width:20px;" /></span> <span class="cspm_tabs_menu_item">'.esc_attr__( $tab_data['title'], 'cspm' ).'</span>', //@edited 5.5
						'fields' => call_user_func(array(apply_filters('cspm_admin_tab_action_class', $this, $tab_data['id']), $tab_data['callback'])),
					);
		
				}
			
			/*
			 * Set tabs */
			 
			$metabox_object->add_field( array(
				'id'   => 'cspm_pm_settings_tabs',
				'type' => 'tabs',
				'tabs' => $tabs_setting,
				'options_page' => false,
			) );
			
			return $metabox_object;
			
		}
		
		
		/**
		 * Query Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_query_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Query Settings',
				'desc' => 'Filter your posts by controlling the parameters below to your needs. You can always get the information you want without actually dealing with any parameter!',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_query_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_number_of_items',
				'name' => 'Number of posts', 
				'desc' => 'Enter the number of posts to show on the map. Leave this field empty to show all posts.',
				'type' => 'text',
				'default' => '',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '0',
				),
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_taxonomies_section',
				'name' => 'Taxonomy Parameters',
				'desc' => 'This will allow you to show only posts associated with certain taxonomies.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
									
				/**
				 * [@post_type_taxonomy_options] : Takes the list of all taxonomies related to the post type selected in "Query settings" */
			 
				$post_type_taxonomy_options	= $this->cspm_get_post_type_taxonomies($this->selected_cpt);		
					unset($post_type_taxonomy_options['post_format']);
					
				reset($post_type_taxonomy_options); // Set the cursor to 0                        
						
				/**
				 * Loop through all taxonomies (except for 'post_format' and display each one of them */
				
				foreach($post_type_taxonomy_options as $taxonomy_name => $taxonomy_title){
				
					$tax_name = $taxonomy_name;
					$tax_label = $taxonomy_title;	
					
					$fields[] = array(
						'id' => $this->metafield_prefix . '_taxonomie_'.$tax_name,
						'name' => $tax_label.' <small>('.$tax_name.')</small>',
						'desc' => 'Show only posts associated with the selected terms.',
						'type' => 'pw_multiselect',
						'options' => $this->cspm_get_term_options($tax_name),
						'attributes' => array(
							'placeholder' => 'Select term(s)'
						),
					);
					
					$fields[] = array(
						'id' => $this->metafield_prefix . '_'.$tax_name.'_operator_param',
						'name' => '"Operator" parameter', 
						'desc' => 'Operator to test "'.$tax_label.'". Defaults to "IN".  <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters" target="_blank" class="cspm_blank_link">More</a>',
						'type' => 'radio_inline',
						'default' => 'IN',
						'options' => array(
							'AND' => 'AND',
							'IN' => 'IN',
							'NOT IN' => 'NOT IN',
						),
					);
					
				}
							
				$fields[] = array(
					'id' => $this->metafield_prefix . '_taxonomy_relation_param',
					'name' => '"Relation" parameter', 
					'desc' => 'Select the relationship between each taxonomy (when there is more than one). Defaults to "AND". <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters" target="_blank" class="cspm_blank_link">More</a>.',
					'type' => 'radio',
					'default' => 'AND',
					'options' => array(
						'AND' => 'AND',
						'OR' => 'OR',
					),
				);
						
			$fields[] = array(
				'id' => $this->metafield_prefix . '_status_section',
				'name' => 'Status Parameters',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_items_status',
					'name' => 'Status', 
					'desc' => 'Show posts associated with certain status. Defaults to "publish". <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Status_Parameters" target="_blank" class="cspm_blank_link">More</a>',
					'type' => 'pw_multiselect',
					'default' => array('publish'),
					'options' => get_post_stati(),
					'attributes' => array(
						'placeholder' => 'Select status'
					),
				);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_custom_fields_section',
				'name' => 'Custom Fields Parameters',
				'desc' => 'Show posts associated with certain custom field(s). <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters" target="_blank" class="cspm_blank_link">More</a>',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_custom_fields',
					'name' => '', 
					'desc' => '',
					'type' => 'group',
					'repeatable'  => true,
					'options'     => array(
						'group_title'   => esc_attr__( 'Custom Field {#}', 'cspm' ),
						'add_button'    => esc_attr__( 'Add New Custom Field', 'cspm' ),
						'remove_button' => esc_attr__( 'Remove Custom Fields', 'cspm' ),
						'sortable'      => true,
						'closed'     => true,
					),
					'fields' => array(	
						array(
							'id' => 'custom_field_name',
							'name' => 'Custom field key/name', 
							'desc' => '',
							'type' => 'text',
							'default' => '',
							'attributes'  => array(
								'data-group-title' => 'text'
							)
						),		
						array(
							'id' => 'custom_field_values',
							'name' => 'Custom field value(s)', 
							'desc' => 'Custom field value(s). Separate multiple values by comma. (Note: Mulitple values support is limited to a compare value of "IN", "NOT IN", "BETWEEN", or "NOT BETWEEN")',
							'type' => 'text',
							'default' => '',
						),
						array(
							'id' => 'custom_field_type',
							'name' => 'Custom field type', 
							'desc' => '',
							'type' => 'select',
							'default' => 'CHAR',
							'options' => array(
								'NUMERIC' => 'NUMERIC',
								'BINARY' => 'BINARY',
								'CHAR' => 'CHAR',
								'DATE' => 'DATE',
								'DATETIME' => 'DATETIME',
								'DECIMAL' => 'DECIMAL',						
								'SIGNED' => 'SIGNED',
								'TIME' => 'TIME',												
								'UNSIGNED' => 'UNSIGNED',						
							)
						),															
						array(
							'id' => 'custom_field_compare_param',
							'name' => '"Compare" parameter', 
							'desc' => 'Operator to test the custom field value(s).',
							'type' => 'select',
							'default' => '=',
							'options' => array(
								esc_attr('=') => '=',
								esc_attr('!=') => '!=',
								esc_attr('>') => '>',
								esc_attr('>=') => '>=',
								esc_attr('<') => '<',
								esc_attr('<=') => '<=',
								'LIKE' => 'LIKE',						
								'NOT LIKE' => 'NOT LIKE',
								'IN' => 'IN',												
								'NOT IN' => 'NOT IN',						
								'BETWEEN' => 'BETWEEN',
								'NOT BETWEEN' => 'NOT BETWEEN',
								'EXISTS' => 'EXISTS',
								'NOT EXISTS' => 'NOT EXISTS',						
							)
						),				
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_custom_field_relation_param',
					'name' => '"Relation" parameter', 
					'desc' => 'Select the relationship between each custom field (when there is more than one). Defaults to "AND". <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters" target="_blank" class="cspm_blank_link">More</a>',
					'type' => 'radio',
					'default' => 'AND',
					'options' => array(
						'AND' => 'AND',
						'OR' => 'OR'
					)
				);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_post_section',
				'name' => 'Post Parameters',
				'desc' => 'This will allow you to select specific posts to display or to remove from the map.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_post_in',
					'name' => 'Posts to retrieve', 
					'desc' => 'Select the posts to retrieve (to display). <br />Type an empty space to list all available posts! <span style="color:red;">If you use this field, <strong>"Posts not to retrieve"</strong> will be ignored!',
					'type' => 'post_ajax_search',					
					'limit' => -1, 
					'multiple-item' => true,
					'sortable' => false,
					'query_args' => array(
						'post_type' => array( $this->selected_cpt ),
						'posts_per_page' => -1
					),
				);			
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_post_not_in',
					'name' => 'Posts not to retreive', 
					'desc' => 'Select the posts not to retrieve (to remove). <br />Type an empty space to list all available posts!',
					'type' => 'post_ajax_search',										
					'limit' => -1, 
					'multiple-item' => true,
					'default' => '', 
					'sortable' => false,
					'query_args' => array(
						'post_type' => array( $this->selected_cpt ),
						'posts_per_page' => -1
					),
				);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_caching_section',
				'name' => 'Caching parameters',
				'desc' => 'Stop the data retrieved from being added to the cache.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_cache_results',
					'name' => 'Post information cache', 
					'desc' => 'Add post information to the cache. <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Caching_Parameters" target="_blank" class="cspm_blank_link">More</a>',
					'type' => 'radio',
					'default' => 'true',
					'options' => array(
						'true' => 'Yes',
						'false' => 'No'
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_update_post_meta_cache',
					'name' => 'Post meta information cache', 
					'desc' => 'Add post meta information to the cache. <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Caching_Parameters" target="_blank" class="cspm_blank_link">More</a>',
					'type' => 'radio',
					'default' => 'true',
					'options' => array(
						'true' => 'Yes',
						'false' => 'No'
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_update_post_term_cache',
					'name' => 'Post term information cache', 
					'desc' => 'Add post term information to the cache. <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Caching_Parameters" target="_blank" class="cspm_blank_link">More</a>',
					'type' => 'radio',
					'default' => 'true',
					'options' => array(
						'true' => 'Yes',
						'false' => 'No'
					)
				);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_author_section',
				'name' => 'Author Parameters',
				'desc' => 'Show posts associated with certain author.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_authors_prefixing',
					'name' => 'Authors condition', 
					'desc' => 'Select "Yes" if you want to display all posts except those from selected or logged-in authors (In WP Query, this equals to "autors__not_in").<br />
							   Select "No" if you want to display only posts of selected or logged-in authors (In WP Query, this equals to "autors__in"). <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Author_Parameters" target="_blank" class="cspm_blank_link">More</a>',
					'type' => 'radio',
					'default' => 'false',
					'options' => array(
						'true' => 'Yes',
						'false' => 'No',
					),
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_authors_type',
					'name' => 'Authors type', 
					'desc' => 'Would you like to show/hide the posts of only logged-in author or the posts of defined authors? <br />
							   By selecting the option "Logged-in Author/User", the map will automatically detect the logged-in author/user 
							   and will display or hide the locations of that author according to the option selected in the above field ("Authors condition").<br />
							   By selecting the option "Defined Authors", the map will show or hide the locations of the selected authors in the next field "Authors" according 
							   to the option selected in the above field ("Authors condition").',
					'type' => 'radio',
					'default' => 'defined_authors',
					'options' => array(
						'logged_in_authors' => 'Logged-in Author/User',
						'defined_authors' => 'Defined Authors',
					),
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_authors',
					'name' => 'Authors', 
					'desc' => 'Show/Hide posts associated with certain authors. Leave this field empty to ignore the list of authors! <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Author_Parameters" target="_blank" class="cspm_blank_link">More</a>',
					'type' => 'pw_multiselect',
					'default' => '',
					'options' => $this->cspm_get_all_users(),
					'attributes' => array(
						'placeholder' => 'Select Author(s)',
						'data-conditional-id' => $this->metafield_prefix . '_authors_type',
						'data-conditional-value' => wp_json_encode(array('defined_authors')),								
					),
				);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_order_section',
				'name' => 'Order & Orderby Parameters',
				'desc' => 'Sort retrieved posts.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_orderby_param',
					'name' => 'Orderby parameter', 
					'desc' => 'Sort retrieved posts by parameter. Defaults to "date". <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank" class="cspm_blank_link">More</a>',
					'type' => 'select',
					'default_cb' => function(){ return 'date'; }, // Fix an issue on CMB2 that returns error when the default value is the same as a PHP function!
					'options' => array(
						'none' => 'No order',
						'ID' => 'Order by post id',
						'author' => 'Order by author',
						'title' => 'Order by title',
						'name' => 'Order by post name (post slug)',
						'date' => 'Order by date',
						'modified' => 'Order by last modified date',
						'parent' => 'Order by post/page parent id',
						'rand' => 'Random order',
						'comment_count' => 'Order by number of comments',
						'menu_order' => 'Order by Page Order',
						'meta_value' => 'Order by string meta value',
						'meta_value_num' => 'Order by numeric meta value ',
						'post__in' => 'Preserve post ID order given in the post__in array',
					)
				);							
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_orderby_meta_key',
					'name' => 'Custom field name', 
					'desc' => 'This field is used only for "Order by string meta value" & "Order by numeric meta value" in "Orderby parameters".',
					'type' => 'text',
					'default' => '',
					'attributes' => array(
						'data-conditional-id' => $this->metafield_prefix . '_orderby_param',
						'data-conditional-value' => wp_json_encode(array('meta_value', 'meta_value_num')),								
					),					
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_order_meta_type',
					'name' => 'Custom field type', 
					'desc' => 'Select the custom field type. This field is used only for "Order by string meta value" in "Orderby parameters".',
					'type' => 'select',
					'default' => 'CHAR',
					'options' => array(
						'CHAR' => 'CHAR',
						'NUMERIC' => 'NUMERIC',
						'BINARY' => 'BINARY',							
						'DATE' => 'DATE',
						'DATETIME' => 'DATETIME',
						'DECIMAL' => 'DECIMAL',
						'SIGNED' => 'SIGNED',
						'TIME' => 'TIME',
						'UNSIGNED' => 'UNSIGNED',
					),
					'attributes' => array(
						'data-conditional-id' => $this->metafield_prefix . '_orderby_param',
						'data-conditional-value' => wp_json_encode(array('meta_value')),								
					),
				);				
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_order_param',
					'name' => 'Order parameter', 
					'desc' => 'Designates the ascending or descending order of the "orderby" parameter. Defaults to "DESC". <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank" class="cspm_blank_link">More</a>',
					'type' => 'radio',
					'default' => 'DESC',
					'options' => array(
						'ASC' => 'Ascending order from lowest to highest values (1,2,3 | A,B,C)',
						'DESC' => 'Descending order from highest to lowest values (3,2,1 | C,B,A)'
					)
				);							

			return $fields;
			
		}
		
		
		/**
		 * Layout Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_layout_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Layout Settings',
				'desc' => '',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_layout_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_main_layout',
				'name' => 'Main layout',
				'desc' => 'Select main layout alignment.',
				'type' => 'radio',
				'default' => 'mu-cd',
				'options' => array(
					'mu-cd' => 'Map-Up, Carousel-Down',
					'md-cu' => 'Map-Down, Carousel-Up',
					'mr-cl' => 'Map-Right, Carousel-Left',
					'ml-cr' => 'Map-Left, Carousel-Right',
					'fit-in-map' => 'Fit in the box (Map only)',
					'fullscreen-map' => 'Full screen Map (Map only)',
					'm-con' => 'Map with carousel on top',
					'fit-in-map-top-carousel' => 'Fit in the box with carousel on top',
					'fullscreen-map-top-carousel' => 'Full screen Map with carousel on top',
					'map-tglc-top' => 'Map, toggle carousel from top',
					'map-tglc-bottom' => 'Map, toggle carousel from bottom',
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_layout_type',
				'name' => 'Layout type',
				'desc' => 'Select main layout type.',
				'type' => 'radio',
				'default' => 'full_width',
				'options' => array(
					'fixed' => 'Fixed width &amp; Fixed height',
					'full_width' => 'Full width &amp; Fixed height',
					'responsive' => 'Responsive layout <sup>(Hide the carousel on mobile browsers)</sup>'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_layout_fixed_width',
				'name' => 'Layout width',
				'desc' => 'Select the width (in pixels) of the layout. (Works only for the fixed layout)',
				'type' => 'text',
				'default' => '700',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '10'
				),		
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_layout_fixed_height',
				'name' => 'Layout height',
				'desc' => 'Select the height (in pixels) of the layout.',
				'type' => 'text',
				'default' => '600',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '10'
				),				
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
				'name' => 'Map Settings',
				'desc' => 'Override the default map settings.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_map_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
		
			$fields[] = array(
				'id' => $this->metafield_prefix . '_alternative_api_key',
				'name' => 'Override the default API Key',
				/**
				 * https://developers.google.com/maps/signup?csw=1#google-maps-javascript-api */
				'desc' => 'Enter a new API Key to override the default API Key. 
						  <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key" target="_blank" class="cspm_blank_link">Get an API key</a><br />
						  <span style="color:red;"><strong>Important notes:</strong> <br />
						  <strong>1.</strong> It is not required to use a new API Key for this map. 
						  This map will use the API Key you\'ve already provided in the field 
						  <u>"Progress Map => Default Map Settings => API Key"</u>!<br />
						  <strong>2.</strong> Using a different API Key for each of your maps will reduce the cost of using Google Maps APIs!<br />
						  <strong>3.</strong> By entering a new API Key in this field, you confirm its usage instead of the default API Key. Leave this field empty or clear it to use the default API Key!<br />
						  <br /><span style="color:red;"><strong>Caution required!</strong><br />
						  Using two maps with different API Keys on the same page may cause unexpected errors. We highly recommend overriding the default API Key 
						  only for maps that will be called/used alone on a page. If you insist on using more than one map on the same page, you MUST use the same API Key for all these maps!
						  </span>',
				'type' => 'text',
				'default' => '',
				'before_row' => str_replace(
					array('[title]'), 
					array('Map API Key & Language'), 
					$this->toggle_before_row.'<p class="cmb2-metabox-description" style="margin:0 0 10px 0; padding-top:0;">Override the default map API Key and/or change the map default language.</p><hr />'
				),												
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_alternative_map_language',
				'name' => 'Override the default map language',
				'desc' => 'Use a different language for this specific map. 
						  <a href="https://developers.google.com/maps/faq#languagesupport" target="_blank" class="cspm_blank_link">supported list of languages</a>.<br />
						  <span style="color:red;"><strong>Important notes:</strong> <br />
						  <strong>1.</strong> It is not required to use a new language for this map. 
						  This map will use the langauge you\'ve already provided in the field 
						  <u>"Progress Map => Default Map Settings => Map language"</u>!<br />
						  <strong>2.</strong> By entering a new language in this field, you confirm its usage instead of the default langauge. Leave this field empty or clear it to use the default map language!<br />
						  <br /><span style="color:red;"><strong>Caution required!</strong><br />
						  Using two maps with different languages on the same page may cause unexpected conflicts. We highly recommend overriding the default map language 
						  only for maps that will be called/used alone on a page. If you insist on using more than one map on the same page, you MUST use the same language for all these maps!						  
						  </span>',
				'type' => 'text',
				'default' => '',
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
				'id' => $this->metafield_prefix . '_map_center',
				'type' => 'cs_gmaps',
				'options' => array(
					'api_key' => isset($this->plugin_settings['api_key']) ? $this->plugin_settings['api_key'] : '',
					'disable_gmaps_api' => false,
					'disable_gmap3_plugin' => false,
					'split_values' => false,
					'split_latlng_field' => false,
					'save_only_latLng_field' => true,
					'map_height' => '200px',
					'map_width' => '100%',
					'map_center' => $this->cspm_get_field_default('map_center', '51.53096,-0.121064'),
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
				'id' => $this->metafield_prefix . '_map_background',
				'name' => 'Map Background',
				'desc' => 'Color used for the background of the Map div. This color will be visible when tiles have not yet loaded as the user pans. Defaults to "#ededed".',
				'type' => 'colorpicker',
				'default' => '#ededed',
			);			
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_retinaSupport',
				'name' => 'Retina support',
				'desc' => 'Enable retina support for custom markers & Clusters images. When enabled, make sure the uploaded image is twice the size you want it to be displayed on the map. 
						   For example, if you want the marker/cluster image in the map to be displayed as 20x30 pixels, upload an image with 40x60 pixels.<br />
						   <span style="color:red;"><strong>Note: Retina will not be applied to SVG icons!</strong></span>',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('retinaSupport', 'false'),
				'options' => array(
					'true' => 'Enable',
					'false' => 'Disable'
				),
				'attributes' => array(
					'data-conditional-id' => $this->metafield_prefix . '_defaultMarker',
					'data-conditional-value' => wp_json_encode(array('customize')),								
				),		
				'after_row' => $this->toggle_after_row,								
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_restrict_map_bounds',
				'name' => 'Restrict map bounds',
				'desc' => 'Apply a restriction to the Map? By restricting the map bounds, users can only pan and zoom inside the given bounds and the map\'s viewport will not exceed these restrictions.<br />
						  Specify the map bounds in the field <u>"Map bounds restriction"</u>!',
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
						  <strong>4.</strong> If you need to start drawing from scratch, click on the button <u>"Draw from scratch"</u>, then, start 
						  tracing new bounds.<br />
						  <strong>5.</strong> Once satisfied with the area been covered, click on the button <u>"Copy bounds"</u>.<br />
						  <span style="color:red;">
						  <strong>Hint:</strong> For more precision while tracing the bounds, use the map\'s full screen mode!
						  </span>',
				'id' => $this->metafield_prefix . '_map_bounds_restriction',
				'type' => 'cs_gmaps_drawing',
				'default' => $this->cspm_get_field_default('map_bounds_restriction'),
				'options' => array(
					'api_key' => isset($this->plugin_settings['api_key']) ? $this->plugin_settings['api_key'] : '',
					'disable_gmaps_api' => false,
					'disable_gmap3_plugin' => false,
					'map_height' => '200px',
					'map_width' => '100%',
					'map_center' => $this->cspm_get_field_default('map_center', '51.53096,-0.121064'),
					'map_zoom' => 12,
					'labels' => array(
						'address' => 'Enter a location & search or Geolocate your position',						
						'coordinates' => 'Map bounds (North-east & South-west coordinates)',
						'search' => 'Search',
						'pinpoint' => 'Display bounds',
						'clear_overlay' => 'Draw from scratch',
						'top_desc' => 'Draw a rectangle around a specific area to prevent the map from being panned outside it.<br />
									  <span style="color:red;">Read the insctructions below for more details about tracing the bounds!</span><hr />',
					),
					'draw_mode' => 'rectangle',
					'save_coordinates' => true,
				),
			);			
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_strict_bounds',
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
				'id' => $this->metafield_prefix . '_map_zoom',
				'name' => 'Map zoom',
				'desc' => 'Select the map zoom. <span style="color:red;">The map zoom will be ignored if you activate the option (below) <strong>"Autofit"</strong>!</span>',
				'type' => 'select',
				'default' => $this->cspm_get_field_default('map_zoom', '12'),
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
				'id' => $this->metafield_prefix . '_max_zoom',
				'name' => 'Max. zoom',
				'desc' => 'Select the maximum zoom of the map.',
				'type' => 'select',
				'default' => $this->cspm_get_field_default('max_zoom', '19'),
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
				'id' => $this->metafield_prefix . '_min_zoom',
				'name' => 'Min. zoom',
				'desc' => 'Select the minimum zoom of the map. <span style="color:red;">The Min. zoom should be lower than the Max. zoom!</span>',
				'type' => 'select',
				'default' => $this->cspm_get_field_default('min_zoom', '0'),
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
				'id' => $this->metafield_prefix . '_autofit',
				'name' => 'Autofit',
				'desc' => 'This option extends map bounds to contain all markers & clusters. <span style="color:red;">By activating this option, the map zoom will be ignored!</span><br />
						   <strong style="color:red;">The Minimum zoom level is taken into consideration when using this option. Make sure to set the map\'s minimum zoom to a level that allows displaying all items on the map!</strong>',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('autofit', 'false'),
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
								
			$fields[] = array(
				'id' => $this->metafield_prefix . '_recenter_map',
				'name' => 'Recenter Map',
				'desc' => 'Show a button on the map to allow recentring the map. Defaults to "Yes".',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('recenter_map', 'true'),
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_clickableIcons',
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
				'id' => $this->metafield_prefix . '_zoom_on_doubleclick',
				'name' => 'Zoom on double click',
				'desc' => 'Enable/Disable zooming and recentering the map on double click. Defaults to "Disable".',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('zoom_on_doubleclick', 'true'),
				'options' => array(
					'false' => 'Enable',
					'true' => 'Disable'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_gestureHandling',
				'name' => 'Gesture Handling',
				'desc' => 'This setting controls how the API handles gestures on the map. Allowed values:<br />
						  <strong>"cooperative":</strong> Scroll events and one-finger touch gestures scroll the page, and do not zoom or pan the map. Two-finger touch gestures pan and zoom the map. Scroll events with a ctrl key or &#8984; key pressed zoom the map. In this mode the map cooperates with the page.<br />
						  <strong>"greedy":</strong> All touch gestures and scroll events pan or zoom the map.<br />
						  <strong>"none":</strong> The map cannot be panned or zoomed by user gestures.<br />
						  <strong>"auto":</strong> (default) Gesture handling is either cooperative or greedy, depending on whether the page is scrollable or in an iframe.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('gestureHandling', 'auto'),
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
				'id' => $this->metafield_prefix . '_mapTypeControl',
				'name' => 'Show map type control',
				'desc' => 'The MapType control lets the user toggle between map types (such as ROADMAP and SATELLITE). This control appears by default in the top right corner of the map.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('mapTypeControl', 'true'),
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)				
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_streetViewControl',
				'name' => 'Show street view control',
				'desc' => 'The Street View control contains a Pegman icon which can be dragged onto the map to enable Street View. This control appears by default in the right top corner of the map.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('streetViewControl', 'false'),
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_rotateControl',
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
				'id' => $this->metafield_prefix . '_scaleControl',
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
				'id' => $this->metafield_prefix . '_fullscreenControl',
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
				'id' => $this->metafield_prefix . '_zoomControl',
				'name' => 'Show zoom control',
				'desc' => 'The Zoom control displays a small "+/-" buttons to control the zoom level of the map. This control appears by default in the bottom right corner of the map.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('zoomControl', 'true'),
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_zoomControlType',
				'name' => 'Zoom control Type',
				'desc' => 'Select the zoom control type.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('zoomControlType', 'customize'),
				'options' => array(
					'customize' => 'Customized type',
					'default' => 'Default type'
				)
			);
	
			$fields[] = array(
				'id' => $this->metafield_prefix . '_controlSize',
				'name' => 'Map controls size', 
				'desc' => 'Specify the size in pixels of the controls appearing on the map. Defaults to "39".
						   <br />Please note that this option works only for the controls made by the Maps API itself. This does not scale custom controls.',
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
				'id' => $this->metafield_prefix . '_traffic_layer',
				'name' => 'Traffic Layer',
				'desc' => 'Add real-time traffic information to your map. Traffic information is refreshed frequently, but not instantly. Rapid consecutive requests for the same area are unlikely to yield different results.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('traffic_layer', 'false'),
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
				'id' => $this->metafield_prefix . '_transit_layer',
				'name' => 'Transit Layer',
				'desc' => 'Display the public transit network of a city on your map. When the Transit Layer is enabled, and the map is centered on a city that supports transit information, the map will display major transit lines as thick, colored lines. The color of the line is set based upon information from the transit line operator.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('transit_layer', 'false'),
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_bicycling_layer',
				'name' => 'Bicycling Layer',
				'desc' => 'Add bicycle information to your map. The Bicycling Layer renders a layer of bike paths, suggested bike routes and other overlays specific to bicycling usage on top of the map.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('bicycling_layer', 'false'),
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
				'desc' => 'Styled maps allow you to customize the presentation of the standard Google base maps, changing the visual display of such elements as roads, parks, and built-up areas. The lovely styles below are provided by <a href="http://snazzymaps.com" target="_blank" class="cspm_blank_link">Snazzy Maps</a>',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_map_style_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_initial_map_style',
				'name' => 'Initial map style',
				'desc' => 'Select the initial map style.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('initial_map_style', 'ROADMAP'),
				'options' => array(
					'ROADMAP' => 'Road Map <sup>(Displays the default road map view)</sup>',
					'SATELLITE' => 'Satellite <sup>(Displays Google Earth satellite images)</sup>',
					'TERRAIN' => 'Terrain <sup>(Displays a physical map based on terrain information)</sup>',
					'HYBRID' => 'Hybrid <sup>(Displays a mixture of normal and satellite views)</sup>',
					'custom_style' => 'Custom style <sup>(Displays a custom style)</sup>'
				)
			);
					
			$fields[] = array(
				'id' => $this->metafield_prefix . '_style_option',
				'name' => 'Style option', 
				'desc' => 'Select the style option of the map. <span style="color:red;">If you select the option <strong>Progress map styles</strong>, choose one of the available styles below.
						   If you select the option <strong>My custom style</strong>, enter your custom style code in the field <strong>Javascript Style Array</strong>.</span>',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('style_option', 'progress-map'),
				'options' => array(
					'progress-map' => 'Progress Map styles',
					'custom-style' => 'My custom style'
				),
			);
					
			$fields[] = array(
				'id' => $this->metafield_prefix . '_map_style',
				'name' => 'Map style',
				'desc' => 'Select your map style.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('map_style', 'google-map'),
				'options' => $this->cspm_get_all_map_styles(),
				'attributes' => array(
					'data-conditional-id' => $this->metafield_prefix . '_style_option',
					'data-conditional-value' => wp_json_encode(array('progress-map')),								
				),													
			);
					
			$fields[] = array(
				'id' => $this->metafield_prefix . '_custom_style_name',
				'name' => 'Custom style name',
				'desc' => 'Enter your custom style name. Defaults to "Custom style".',
				'type' => 'text',
				'default' => $this->cspm_get_field_default('custom_style_name', 'Custom style'),
				'attributes' => array(
					'data-conditional-id' => $this->metafield_prefix . '_style_option',
					'data-conditional-value' => wp_json_encode(array('custom-style')),								
				),																	
			);
					
			$fields[] = array(
				'id' => $this->metafield_prefix . '_js_style_array',
				'name' => 'Javascript Style Array',
				'desc' => 'If you don\'t like any of the styles above, fell free to add your own style. Please add/paste only the JSON of your style. No extra variables or code.<br />
						  Make use of the following service to create your style:<br />
	  			          . <a href="https://mapstyle.withgoogle.com/" target="_blank" class="cspm_blank_link">Google Maps APIs Styling Wizard</a><br />
						  You may also like to <a href="http://snazzymaps.com/submit" target="_blank" class="cspm_blank_link">submit</a> your style for the world to see :)',
				'type' => 'textarea',
				'default' => $this->cspm_get_field_default('js_style_array', ''),
				'attributes' => array(
					'data-conditional-id' => $this->metafield_prefix . '_style_option',
					'data-conditional-value' => wp_json_encode(array('custom-style')),								
				),																					
			);	

			return $fields;
			
		}
		
		
		/**
		 * Marker settings
		 * 
		 * @since 4.0
		 */
		function cspm_marker_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Marker Settings',
				'desc' => '',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_marker_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_defaultMarker',
				'name' => 'Marker image type',
				'desc' => 'Select the marker image type.<br />
						  <span style="color:red;"><strong>Note:</strong> You can use SVG icons!</span>',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('defaultMarker', 'customize'),
				'options' => array(
					'customize' => 'Customized image type',
					'default' => 'Default image type <sup>(Google Maps red marker)</sup>'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_markerAnimation',
				'name' => 'Marker animation',
				'desc' => 'You can animate a marker so that it exhibit a dynamic movement when it\'s been fired. To specify the way a marker is animated, select
						   one of the supported animations above.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('markerAnimation', 'pulsating_circle'),
				'options' => array(
					'pulsating_circle' => 'Pulsating circle',
					'bouncing_marker' => 'Bouncing marker',
					'flushing_infobox' => 'Flushing infobox <sup style="color:red;">(Use it only when <strong>Show infobox</strong> is set to <strong>Yes</strong>)</sup>'				
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_icon',
				'name' => 'Marker image',
				'desc' => 'Change the default custom marker image. By default, this image will be used for all markers. In case you need to restore the the original marker image, this one can be found in the plugin\'s images directory. 
						  <br /><strong>How to override the default marker image?</strong>
						  <br />1. You can override it by editing a specific post/location and uploading a custom image in the field "Marker => Marker icon". 
						  <br />2. You can also override it by uploading a custom image for a category of posts/locations. The options you need for this method can be found under the section "Marker categories settings".',
				'type' => 'file',
				'default' => $this->cspm_get_field_default('marker_icon', ''),
			);
		
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_icon_height',
				'name' => 'Marker image height', 
				'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
						  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
						  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
				'type' => 'text',
				'default' => $this->cspm_get_field_default('marker_icon_height', ''),
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
				'default' => $this->cspm_get_field_default('marker_icon_width', ''),
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
				'id' => $this->metafield_prefix . '_marker_anchor_point_option',
				'name' => 'Set the anchor point',
				'desc' => 'Depending of the shape of the marker, you may not want the middle of the bottom edge to be used as the anchor point. 
						   In this situation, you need to specify the anchor point of the image. A point is defined with an X and Y value (in pixels). 
						   So if X is set to 10, that means the anchor point is 10 pixels to the right of the top left corner of the image. Setting Y to 10 means 
						   that the anchor is 10 pixels down from the top right corner of the image.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('marker_anchor_point_option', 'disable'),
				'options' => array(
					'auto' => 'Auto detect <sup>*Detects the center of the image.</sup>',
					'manual' => 'Manualy <sup>*Enter the anchor point in the next two fields.</sup>',
					'disable' => 'Disable'				
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_anchor_point',
				'name' => 'Marker anchor point',
				'desc' => 'Enter the anchor point of the Marker image. Separate X and Y by comma. (e.g. 10,15)',
				'type' => 'text',
				'default' => $this->cspm_get_field_default('marker_anchor_point', ''),
			);

			return $fields;
			
		}
		
		
		/**
		 * Clustering settings
		 *
		 * @since 4.0
		 */
		function cspm_clustering_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Marker Clustering Settings',
				'desc' => 'Clustering simplifies your data visualization by consolidating data that are nearby each other on the map in an aggregate form. A cluster is displayed as a circle with a number on it. 
						   The number on a cluster indicates how many markers it contains. As you zoom into any of the cluster locations, the number on the cluster decreases, and you begin to see the individual markers on the map. 
						   Zooming out of the map consolidates the markers into clusters again.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_infobox_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_useClustring',
				'name' => 'Use marker clustering',
				'desc' => 'Clustering simplifies your data visualization by consolidating data that are nearby each other on the map in an aggregate form. <span style="color:red;"><strong>Activating this option will significantly increase the loading speed of the map!</strong></span>',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('useClustring', 'true'),
				'options' => array(
					'true' => 'Yes <span style="color:red;"><sup><strong>(Recommended)</strong></sup></span>',
					'false' => 'No'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_gridSize',
				'name' => 'Grid size',
				'desc' => 'Grid size or Grid-based clustering works by dividing the map into squares of a certain size (the size changes at each zoom) and then grouping the markers into each grid square.',
				'type' => 'text',
				'default' => $this->cspm_get_field_default('gridSize', '60'),
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '0'
				),						
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_clusters_customizations_section',
				'name' => 'Clusters Images & Text color',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);

				$fields[] = array(
					'id' => $this->metafield_prefix . '_cluster_text_color',
					'name' => 'Clusters text color',
					'desc' => 'Change the text color of all your clusters.',
					'type' => 'colorpicker',
					'default' => $this->cspm_get_field_default('cluster_text_color', ''),
				);			

				$fields[] = array(
					'id' => $this->metafield_prefix . '_small_cluster_icon',
					'name' => 'Small cluster image',
					'desc' => 'Upload a new small cluster image. You can always find the original marker in the plugin\'s images directory.',
					'type' => 'file',
					'default' => $this->cspm_get_field_default('small_cluster_icon', ''),
					'before_row' => str_replace(array('[title]', ' closed'), array('Small cluster', ''), $this->toggle_before_row),	
				);
			
					$fields[] = array(
						'id' => $this->metafield_prefix . '_small_cluster_icon_height',
						'name' => 'Small cluster image height', 
						'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => $this->cspm_get_field_default('small_cluster_icon_height', '50'),
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
						'id' => $this->metafield_prefix . '_small_cluster_icon_width',
						'name' => 'Small cluster image width', 
						'desc' => 'Specify the image width (in pixels). Set to -1 to ignore or to automatically calculate the width (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image width will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image width will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => $this->cspm_get_field_default('small_cluster_icon_width', '50'),
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
					'id' => $this->metafield_prefix . '_medium_cluster_icon',
					'name' => 'Medium cluster image',
					'desc' => 'Upload a new medium cluster image. You can always find the original marker in the plugin\'s images directory.',
					'type' => 'file',
					'default' => $this->cspm_get_field_default('medium_cluster_icon', ''),
					'before_row' => str_replace(array('[title]'), array('Medium cluster'), $this->toggle_before_row),
				);
			
					$fields[] = array(
						'id' => $this->metafield_prefix . '_medium_cluster_icon_height',
						'name' => 'Medium cluster image height', 
						'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => $this->cspm_get_field_default('medium_cluster_icon_height', '70'),
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
						'id' => $this->metafield_prefix . '_medium_cluster_icon_width',
						'name' => 'Medium cluster image width', 
						'desc' => 'Specify the image width (in pixels). Set to -1 to ignore or to automatically calculate the width (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image width will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image width will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => $this->cspm_get_field_default('medium_cluster_icon_width', '70'),
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
					'id' => $this->metafield_prefix . '_big_cluster_icon',
					'name' => 'Large cluster image',
					'desc' => 'Upload a new large cluster image. You can always find the original marker in the plugin\'s images directory.<br />
							  <span style="color:red;"><strong>Note:</strong> You can use SVG icons!</span>',
					'type' => 'file',
					'default' => $this->cspm_get_field_default('big_cluster_icon', ''),
					'before_row' => str_replace(array('[title]'), array('Big cluster'), $this->toggle_before_row),
				);
			
					$fields[] = array(
						'id' => $this->metafield_prefix . '_big_cluster_icon_height',
						'name' => 'Large cluster image height', 
						'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => $this->cspm_get_field_default('big_cluster_icon_height', '90'),
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
						'id' => $this->metafield_prefix . '_big_cluster_icon_width',
						'name' => 'Large cluster image width', 
						'desc' => 'Specify the image width (in pixels). Set to -1 to ignore or to automatically calculate the width (for PNG/JPEG/GIF images).<br />
								  <span style="color:red;"><strong>Note: When set to -1, the image width will be automatically calculated but only if the image type is PNG/JPEG/GIF.
								  If you\'re using SVG icons, the image width will not be calculated and you may need to specify it.</strong></span>',
						'type' => 'text',
						'default' => $this->cspm_get_field_default('big_cluster_icon_width', '90'),
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
		 * Marker labels settings
		 *
		 * @since 4.0
		 */
		function cspm_marker_labels_settings(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Marker Labels Settings',
				'desc' => 'A marker label is a letter or number that appears inside a marker.<br /><br />
						  <span style="color:red;"><strong><u>Note:</u> To override these settings for specific posts, edit your posts and use the settings available under the menu "Marker Label"!</strong></span>',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_marker_labels_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);

			$fields[] = array(
				'id'   => $this->metafield_prefix . '_marker_labels',
				'name' => 'Use marker labels',
				'desc' => 'This option specify the appearance of marker labels. Default "No".',
				'type' => 'radio_inline',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No',
				),
				'default' => 'no',
			);

			$fields[] = array(
				'id'   => $this->metafield_prefix . '_marker_labels_type',
				'name' => 'How to label markers?',
				'desc' => '1. The option <strong>"Automatically label all markers with a number"</strong> allow you to automatically label your markers with a number that represents 
						   the order of each marker on the map. Note that the markers order is set and can be changed under <strong>"Query settings => Orderby parameter"</strong>. <br />
						   2. The option <strong>"Manually label markers"</strong> allow you to manually label your markers with a custom text (number/letter). To manually label a marker, you need to add/edit your posts and 
						   enter the label text in the field <strong>"Marker label => Label text"</strong>.<br />
						   <br />Default "Manually label markers".',
				'type' => 'radio_inline',
				'options' => array(
					'auto' => 'Automatically label all markers with a number',
					'manual' => 'Manually label markers',
				),
				'default' => 'manual',
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_label_origin',
				'name' => 'Label Origin',
				'desc' => 'Specify the origin of the label relative to the top-left corner of the marker image. <strong>By default, the origin is located in the center point of the image.</strong>',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);

				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_labels_top',
					'name' => 'Top position', 
					'desc' => '',
					'type' => 'text',
					'default' => '',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '0',
					),
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_labels_left',
					'name' => 'Left position', 
					'desc' => '',
					'type' => 'text',
					'default' => '',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '0',
					),
				);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_label_style',
				'name' => 'Label Style',
				'desc' => 'Specify the appearance of the marker labels.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);

				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_labels_color',
					'name' => 'Color',
					'desc' => 'The color of the label text. Default color is black (#000000).',
					'type' => 'colorpicker',
					'default' => '#000000',															
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_labels_fontFamily',
					'name' => 'Font family', 
					'desc' => 'The font family of the label text (equivalent to the CSS font-family property). Default "monospace".<br />
							  Find more about the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/font-family" target="_blank" class="cspm_blank_link">font-family</a> property.',
					'type' => 'text',
					'default' => 'monospace',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_labels_fontWeight',
					'name' => 'Font weight', 
					'desc' => 'The font weight of the label text (equivalent to the CSS font-weight property). Default "bold".<br />
							  Find more about the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight" target="_blank" class="cspm_blank_link">font-weight</a> property.',
					'type' => 'text',
					'default' => 'bold',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_labels_fontSize',
					'name' => 'Font size', 
					'desc' => 'The font size of the label text (equivalent to the CSS font-size property). Default "14px".<br />
							  Find more about the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/font-size" target="_blank" class="cspm_blank_link">font-size</a> property.',
					'type' => 'text',
					'default' => '14px',
				);								
			
			return $fields;
			
		}
		
		
		/**
		 * Marker popups settings fields
		 *
		 * @since 4.0
		 */
		function cspm_marker_popups_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Marker Popups Settings',
				'desc' => 'A marker popup works like an infobox. It\'s a bubble that contains an information and is always related to a marker. 
						   The aim of using a marker popup is to display an attractive info about a location like a price or a rating note.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_marker_popup_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_use_marker_popups',
				'name' => 'Use marker popups',
				'desc' => 'This option specify the appearance of marker popups. Default "No".',
				'type' => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_popups_placement',
				'name' => 'Popup placement',
				'desc' => 'Select the popup placement, relative to the marker. Default "Right".',
				'type' => 'select',
				'default' => 'right',
				'options' => array(
					'top' => 'Top',
					'right' => 'Right',
					'bottom' => 'Bottom',
					'left' => 'Left'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_popups_content',
				'name' => 'Popups Content',
				'desc' => 'Specify the content of the marker popups.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_popups_content_type',
					'name' => 'Content type',
					'desc' => 'Specify the content type. The content type defines what you want to display as the popup content, it could be a custom field or a taxonomy term(s). Default "Custom field".<br />
							  <strong>How does start rating works?</strong><br />
							  1. If you\'re using a custom field to save the rating note, select the option "5 stars rating <sup>(Saved as custom field)</sup>".<br />
							  2. If you\'re using a taxonomy term to save the rating note, select the option "5 stars rating <sup>(Saved as taxonomy term)</sup>".<br />
							  3. The value of the custom field or the taxonomy term must be a number from 0 to 5. 0 = no star. 1 = one star. 2 = two stars. 3 = three stars. 4 = four stars and 5 = five stars. Any value above 5 will be considered as 5 stars.',
					'type' => 'radio',
					'default' => 'custom_field',
					'options' => array(
						'custom_field' => 'Custom field',
						'one_term' => 'One taxonomy term <sup>(Displays the first term on the terms list)</sup>',
						'all_terms' => 'All taxonomy terms <sup>(Displays all terms separated by comma)</sup>',
						'rating_custom_field' => '5 stars rating <sup>(Saved as <strong>custom field</strong>)</sup>', 
						'rating_term' => '5 stars rating <sup>(Saved as <strong>taxonomy term</strong>)</sup>', 
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_popups_custom_field',
					'name' => 'Custom field name', 
					'desc' => 'Specify the custom field name. The value of this custom field will be used as the content.',
					'type' => 'text',
					'default' => '',
					'attributes' => array(
						'data-conditional-id' => $this->metafield_prefix . '_marker_popups_content_type',
						'data-conditional-value' => wp_json_encode(array('custom_field', 'rating_custom_field')),								
					),										
				);
					
				/**
				 * [@post_type_taxonomy_options] : Takes the list of all taxonomies related to the post type selected in "Query settings" */
				 
				$post_type_taxonomy_options	= $this->cspm_get_post_type_taxonomies($this->selected_cpt);		
					unset($post_type_taxonomy_options['post_format']);
					
				reset($post_type_taxonomy_options); // Set the cursor to 0
		
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_popups_taxonomy',
					'name' => 'Taxonomies',
					'desc' => 'Select a taxonomy. The post\'s related taxonomy term(s) will be used as the content.',
					'type' => 'select',
					'default' => key($post_type_taxonomy_options), // Get the first option (term) in the taxonomies list
					'options' => $post_type_taxonomy_options,
					'attributes' => array(
						'data-conditional-id' => $this->metafield_prefix . '_marker_popups_content_type',
						'data-conditional-value' => wp_json_encode(array('one_term', 'all_terms', 'rating_term')),								
					),															
				);

				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_popups_before_content',
					'name' => 'Before content', 
					'desc' => 'Enter the text to display before the content. For example, you can use this field to display a currency.<br />
							  <span style="color:red;"><strong>Note:</strong> To add an empty space at the end of the "Before content", enter <code>[-]</code>. (e.g. <code>$[-]</code>).</span>',
					'type' => 'text',
					'default' => '',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_popups_after_content',
					'name' => 'After content', 
					'desc' => 'Enter the text to display after the content. For example, you can use this field to display a currency.<br />
							  <span style="color:red;"><strong>Note:</strong> To add an empty space on the beginning of the "After content", enter <code>[-]</code>. (e.g. <code>[-]$</code>).</span>',
					'type' => 'text',
					'default' => '',
				);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_popups_style',
				'name' => 'Popups Style',
				'desc' => 'Specify the appearance of the marker popups.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);

				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_popups_bg_color',
					'name' => 'Background color',
					'desc' => 'The background color of the popup. Default color is white (#ffffff).',
					'type' => 'colorpicker',
					'default' => '#ffffff',															
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_popups_color',
					'name' => 'Font color',
					'desc' => 'The color of the popup text. Default color is white (#000000).',
					'type' => 'colorpicker',
					'default' => '#000000',															
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_popups_fontSize',
					'name' => 'Font size', 
					'desc' => 'The font size of the popup text (equivalent to the CSS font-size property). Default "14px".<br />
							  Find more about the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/font-size" target="_blank" class="cspm_blank_link">font-size</a> property.',
					'type' => 'text',
					'default' => '14px',
				);								
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_popups_fontWeight',
					'name' => 'Font weight', 
					'desc' => 'The font weight of the popup text (equivalent to the CSS font-weight property). Default "bold".<br />
							  Find more about the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight" target="_blank" class="cspm_blank_link">font-weight</a> property.',
					'type' => 'text',
					'default' => 'bold',
				);

			return $fields;
			
		}
		
		
		/**
		 * Marker menu settings fields
		 *
		 * @since 5.5
		 */
		function cspm_marker_menu_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Marker Menu Settings',
				'desc' => 'Add a menu to your markers and allow your map users to find more information about your locations.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_marker_menu_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_use_marker_menu',
				'name' => 'Use marker menu',
				'desc' => 'This option specify the appearance of marker menu. Default "No".',
				'type' => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_menu_items_options',
				'name' => 'Menu items',
				'desc' => 'These are all available items you can add to the menu',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
				
				/**
				 * Single post link */
				 					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_single_post_menu_item',
					//'name' => '"Single Post" link',
					//'desc' => 'Add a link in the menu that allows to open the single post page.',
					'type' => 'group',
					'repeatable' => false,
					'options' => array(
						'group_title' => esc_attr__( '"Single Post" link', 'cspm' ),
						'add_button' => esc_attr__( 'Add New Item', 'cspm' ),
						'remove_button' => esc_attr__( 'Remove Item', 'cspm' ),
						'sortable' => false,
						'closed' => true,
					),
					'classes' => 'cspm-single-group-field padding',
					'fields' => array(	
						array(
							'id' => 'item_desc',
							'name' => '',
							'desc' => 'Add a link in the menu that allows to open the single post page.',
							'type' => 'title',
							'attributes' => array(
								'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
							),
						),
						array(
							'id' => 'visibility',
							'name' => 'Visibility',
							'desc' => 'Show or hide this item from the menu. Defaults to "Show".',
							'type' => 'radio_inline',
							'default' => 'show',
							'options' => array(
								'show' => 'Show',
								'hide' => 'Hide',
							)
						),
						array(
							'id' => 'link_text',
							'name' => 'Link text', 
							'desc' => 'Enter the text of the link. Defaults to "Details page".',
							'type' => 'text',
							'default' => 'Details page',
						),
						array(
							'id' => 'link_type',
							'name' => 'Link type',
							'desc' => 'Choose an option to open the single post page. Defaults to "Open inside a modal/popup"',
							'type' => 'radio',
							'default' => 'popup',
							'options' => array(
								'new_window' => 'Open in a new window',
								'same_window' => 'Open in the same window',
								'popup' => 'Open inside a modal/popup',
							)
						)
					)
				);
				
				/**
				 * Media Modal link */
				 					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_media_modal_menu_item',
					//'name' => '"Media Modal" link',
					//'desc' => 'Add a link in the menu that allows to open the post media inside a modal.',
					'type' => 'group',
					'repeatable' => false,
					'options' => array(
						'group_title' => esc_attr__( '"Media Modal" link', 'cspm' ),
						'add_button' => esc_attr__( 'Add New Item', 'cspm' ),
						'remove_button' => esc_attr__( 'Remove Item', 'cspm' ),
						'sortable' => false,
						'closed' => true,
					),
					'classes' => 'cspm-single-group-field padding',
					'fields' => array(	
						array(
							'id' => 'item_desc',
							'name' => '',
							'desc' => 'Add a link in the menu that allows to open the post media inside a modal.',
							'type' => 'title',
							'attributes' => array(
								'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
							),
						),
						array(
							'id' => 'visibility',
							'name' => 'Visibility',
							'desc' => 'Show or hide this item from the menu. Defaults to "hide".',
							'type' => 'radio_inline',
							'default' => 'hide',
							'options' => array(
								'show' => 'Show',
								'hide' => 'Hide',
							)
						),
						array(
							'id' => 'link_text',
							'name' => 'Link text', 
							'desc' => 'Enter the text of the link. Defaults to "Media files".',
							'type' => 'text',
							'default' => 'Media files',
						),
					)
				);
				
				/**
				 * Proximities link */
				 					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_proximities_menu_item',
					//'name' => '"Nearby points of interest" link',
					//'desc' => 'Add a link in the menu that allows finding nearby points of interest around a post on the map.<br />
							  //<span style="color:red;"><u>Note:</u> This menu item is only operational when the map uses the feature <u>"Nearby points of interest"</u>!</span>',
					'type' => 'group',
					'repeatable' => false,
					'options' => array(
						'group_title' => esc_attr__( '"Nearby points of interest" link', 'cspm' ),
						'add_button' => esc_attr__( 'Add New Item', 'cspm' ),
						'remove_button' => esc_attr__( 'Remove Item', 'cspm' ),
						'sortable' => false,
						'closed' => true,
					),
					'classes' => 'cspm-single-group-field padding',
					'fields' => array(	
						array(
							'id' => 'item_desc',
							'name' => '',
							'desc' => 'Add a link in the menu that allows finding nearby points of interest around a post on the map.<br />
							  		   <span style="color:red;"><u>Note:</u> This menu item is only operational when the map uses the feature <u>"Nearby points of interest"</u>!</span>',
							'type' => 'title',
							'attributes' => array(
								'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
							),
						),
						array(
							'id' => 'visibility',
							'name' => 'Visibility',
							'desc' => 'Show or hide this item from the menu. Defaults to "hide".',
							'type' => 'radio_inline',
							'default' => 'hide',
							'options' => array(
								'show' => 'Show',
								'hide' => 'Hide',
							)
						),
						array(
							'id' => 'link_text',
							'name' => 'Link text', 
							'desc' => 'Enter the text of the link. Defaults to "Nearby points of interest".',
							'type' => 'text',
							'default' => 'Nearby points of interest',
						),
					)
				);
				
				/**
				 * "Nearby Map" link */
				 					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_nearby_map_menu_item',
					//'name' => '"Nearby Map" link',
					//'desc' => 'Add a link in the menu that allows opening nearby map inside a modal.<br />
							  //<span style="color:red;"><u>Note:</u> This menu item is only operational when using the add-on <a href="https://codecanyon.net/item/nearby-places-wordpress-plugin/15067875" target="_blank">"Nearby Places"</a>!</span>',
					'type' => 'group',
					'repeatable' => false,
					'options' => array(
						'group_title' => esc_attr__( '"Nearby Map" link', 'cspm' ),
						'add_button' => esc_attr__( 'Add New Item', 'cspm' ),
						'remove_button' => esc_attr__( 'Remove Item', 'cspm' ),
						'sortable' => false,
						'closed' => true,
					),
					'classes' => 'cspm-single-group-field padding',
					'fields' => array(	
						array(
							'id' => 'item_desc',
							'name' => '',
							'desc' => 'Add a link in the menu that allows opening nearby map inside a modal.<br />
							 		   <span style="color:red;"><u>Note:</u> This menu item is only operational when using the add-on <a href="https://codecanyon.net/item/nearby-places-wordpress-plugin/15067875" target="_blank">"Nearby Places"</a>!</span>',
							'type' => 'title',
							'attributes' => array(
								'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
							),
						),
						array(
							'id' => 'visibility',
							'name' => 'Visibility',
							'desc' => 'Show or hide this item from the menu. Defaults to "hide".',
							'type' => 'radio_inline',
							'default' => 'hide',
							'options' => array(
								'show' => 'Show',
								'hide' => 'Hide',
							)
						),
						array(
							'id' => 'link_text',
							'name' => 'Link text', 
							'desc' => 'Enter the text of the link. Defaults to "Nearby Map".',
							'type' => 'text',
							'default' => 'Nearby Map',
						),
					)
				);
				
				/**
				 * "Directions" link */
				 					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_directions_menu_item',
					//'name' => '"Directions" link',
					//'desc' => '',
					'type' => 'group',
					'repeatable' => false,
					'options' => array(
						'group_title' => esc_attr__( '"Directions" link', 'cspm' ),
						'add_button' => esc_attr__( 'Add New Item', 'cspm' ),
						'remove_button' => esc_attr__( 'Remove Item', 'cspm' ),
						'sortable' => false,
						'closed' => true,
					),
					'classes' => 'cspm-single-group-field',
					'fields' => array(	
						array(
							'id' => 'item_desc',
							'name' => '',
							'desc' => 'Add a link in the menu that when clicked, will display the path/route between the user location and the marker location, as well as the distance and travel time.<br />
									   The directions will be displayed on Google Maps!',
							'type' => 'title',
							'attributes' => array(
								'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
							),
						),
						array(
							'id' => 'visibility',
							'name' => 'Visibility',
							'desc' => 'Show or hide this item from the menu. Defaults to "hide".',
							'type' => 'radio_inline',
							'default' => 'hide',
							'options' => array(
								'show' => 'Show',
								'hide' => 'Hide',
							)
						),
						array(
							'id' => 'link_text',
							'name' => 'Link text', 
							'desc' => 'Enter the text of the link. Defaults to "Directions".',
							'type' => 'text',
							'default' => 'Directions',
						),
						array(
							'id' => 'travel_mode',
							'name' => 'Travel mode',
							'desc' => 'Specify the method of travel. Defaults to "Driving".',
							'type' => 'radio',
							'default' => 'driving',
							'options' => array(
								'driving' => 'Driving',
								'walking' => 'Walking',
								'bicycling' => 'Bicycling',
								'transit' => 'Transit',
							)
						),
					)
				);
				
				/**
				 * Items order */
				 				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_items_order',
					'name' => esc_attr__( 'Menu items order', 'cspm' ),
					'desc' => esc_attr__( 'Change the display order of the menu items', 'cspm' ),				
					'type' => 'order',
					'inline' => false,
					'options' => array(
						'single_post' => '"Single post" link',
						'media' => '"Media Modal" link',
						'proximities' => '"Nearby points of interest" link',
						'nearby_map' => '"Nearby Map" link',
						'directions' => '"Directions" link',
					),
				);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_menu_options',
				'name' => 'Menu Options',
				'desc' => 'Control the appearence of the marker menu.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_placement',
					'name' => 'Menu placement',
					'desc' => 'Select the menu placement, relative to the marker. Default "Right".',
					'type' => 'select',
					'default' => 'right',
					'options' => array(
						'top' => 'Top',
						'right' => 'Right',
						'bottom' => 'Bottom',
						'left' => 'Left'
					),
					'before_row' => str_replace(array('[title]'), array('Menu options'), $this->toggle_before_row),
				);			
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_open_options',
					'name' => 'Opening the menu',
					'desc' => 'These are all available options to open the menu',
					'type' => 'title',
					'attributes' => array(
						'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
					),
				);
	
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_open_leftclick',
					'name' => 'Open on marker click?',
					'desc' => 'Open the menu on marker click. Defaults to "No".',
					'type' => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_open_rightclick',
					'name' => 'Open on marker right click?',
					'desc' => 'Open the menu on marker right click. Defaults to "Yes".',
					'type' => 'radio',
					'default' => 'yes',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_open_mouse_enter',
					'name' => 'Open on marker mouse enter/hover?',
					'desc' => 'Open the menu on marker mouse enter/hover. Defaults to "No".',
					'type' => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				);
					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_close_options',
					'name' => 'Closing the menu',
					'desc' => 'These are all available options to close the menu',
					'type' => 'title',
					'attributes' => array(
						'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
					),
				);
	
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_close_mouse_out',
					'name' => 'Close on marker mouse out?',
					'desc' => 'Close the menu on marker mouse out. Defaults to "No".',
					'type' => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_close_map_click',
					'name' => 'Close on map click?',
					'desc' => 'Close the menu when the map is clicked. Defaults to "Yes".',
					'type' => 'radio',
					'default' => 'yes',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					)
				);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_show_close_btn',
					'name' => 'Show close button',
					'desc' => 'Show/Hide the close button on top of the infobox. Defaults to "No".',
					'type' => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No'
					),
					'after_row' => $this->toggle_after_row,				
				);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_menu_style_options',
				'name' => 'Menu Style',
				'desc' => 'Customize the marker menu.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_bg_color',
					'name' => 'Background color',
					'desc' => 'The background color of the menu. Default color is white (#ffffff).',
					'type' => 'colorpicker',
					'default' => '#ffffff',	
					'before_row' => str_replace(array('[title]'), array('Menu style'), $this->toggle_before_row),
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_color',
					'name' => 'Font color',
					'desc' => 'The color of the menu text. Default color is white (#000000).',
					'type' => 'colorpicker',
					'default' => '#000000',															
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_fontSize',
					'name' => 'Font size', 
					'desc' => 'The font size of the menu text (equivalent to the CSS font-size property). Default "13px".<br />
							  Find more about the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/font-size" target="_blank" class="cspm_blank_link">font-size</a> property.',
					'type' => 'text',
					'default' => '13px',
				);								
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_marker_menu_fontWeight',
					'name' => 'Font weight', 
					'desc' => 'The font weight of the menu text (equivalent to the CSS font-weight property). Default "bold".<br />
							  Find more about the <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/font-weight" target="_blank" class="cspm_blank_link">font-weight</a> property.',
					'type' => 'text',
					'default' => 'normal',
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
				'desc' => 'The infobox, also called infowindow is an overlay that looks like a bubble and is often connected to a marker.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_infobox_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_show_infobox',
				'name' => 'Show Infobox',
				'desc' => 'Show/Hide the Infobox.',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('show_infobox', 'true'),
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_infobox_type',
				'name' => 'Infobox type',
				'desc' => 'Select the Infobox type.',
				'type' => 'radio_image',
				'default' => $this->cspm_get_field_default('infobox_type', 'rounded_bubble'),
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
				'images_path'      => $this->plugin_url,
				'images'           => array(
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
				'id' => $this->metafield_prefix . '_infobox_width',
				'name' => 'Infobox width', 
				'desc' => 'Override the infobox width by providing a new width (in pixels).<br />
						  <span style="color:red;"><strong>Note:<br />
						  1. If you override the infobox width, you must also provide the infobox height even if you have no intention to change it!<br />
						  2. Set to -1 to ignore this option!</strong></span>',
				'type' => 'text',
				'default' => $this->cspm_get_field_default('infobox_width', ''),
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
				'id' => $this->metafield_prefix . '_infobox_height',
				'name' => 'Infobox height', 
				'desc' => 'Override the infobox height by providing a new height (in pixels).<br />
						  <span style="color:red;"><strong>Note:<br />
						  1. If you override the infobox height, you must also provide the infobox width even if you have no intention to change it!<br />
						  2. Set to -1 to ignore this option!</strong></span>',
				'type' => 'text',
				'default' => $this->cspm_get_field_default('infobox_height', ''),
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
				'id' => $this->metafield_prefix . '_infobox_display_event',
				'name' => 'Display event',
				'desc' => 'Select from the options above when to display infoboxes on the map.<br />
						   <span style="color:red;"><strong>Note:</strong> When using the display event <u>"On marker hover"</u>, the plugin will automatically switch to <u>"On marker clcik"</u> for mobile devices!</span>',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('infobox_display_event', 'onload'),
				'options' => array(
					'onload' => 'On map load <sup>(Loads all infoboxes in viewport)</sup>',
					'onclick' => 'On marker click',
					'onhover' => 'On marker hover',
					'onzoom' => 'By reaching a zoom level <sup>(Loads all infoboxes in viewport)</sup>',
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_infobox_display_zoom_level',
				'name' => 'Map zoom',
				'desc' => 'Select the zoom level on which you want to start displaying infoboxes. Defaults to "12".',
				'type' => 'select',
				'default' => $this->cspm_get_field_default('infobox_display_zoom_level', 12),
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
				'attributes' => array(
					'data-conditional-id' => $this->metafield_prefix . '_infobox_display_event',
					'data-conditional-value' => wp_json_encode(array('onzoom')),								
				),																					
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_infobox_show_close_btn',
				'name' => 'Show close button',
				'desc' => 'Show/Hide the close button on top of the infobox. Defaults to "No".',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('infobox_show_close_btn', 'false'),
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_remove_infobox_on_mouseout',
				'name' => 'Remove Infobox on mouseout?',
				'desc' => 'Choose whether you want to remove the infobox when the mouse leaves the marker or not. <span style="color:red">This option is operational only when the <strong>Display event</strong> 
						  equals to <strong>On marker click</strong> or <strong>On marker hover</strong>. This option doesn\'t work on touch devices</span>',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('remove_infobox_on_mouseout', 'false'),
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_infobox_external_link',
				'name' => 'Post URL',
				'desc' => 'Choose an option to open the single post page. You can also disable links in the infoboxes by selecting the option "Disable"',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('infobox_external_link', 'same_window'),
				'options' => array(
					'new_window' => 'Open in a new window',
					'same_window' => 'Open in the same window',
					'popup' => 'Open inside a modal/popup',
					'nearby_places' => 'Open the "Nearby places" map inside a modal/popup',
					'disable' => 'Disable'
				)
			);
						
			$fields[] = array(
				'id' => $this->metafield_prefix . '_infobox_content_section',
				'name' => 'Infobox Content Parameters',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_infobox_title',
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
					'id' => $this->metafield_prefix . '_infobox_content',
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
					'id' => $this->metafield_prefix . '_infobox_ellipses',
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
		 * Markers Categories Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_marker_categories_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Markers Categories Settings',
				'desc' => 'In this section, you will be able to upload custom icons for your markers. To do that, choose from the available taxonomies the one that represents the category of your posts/locations, set the option "Marker Categories Option" to "Yes", then, upload a custom icon for each category of markers.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_marker_categories_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_cats_settings',
				'name' => 'Markers categories option',
				'desc' => 'Select "Yes" to enable this option for this map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
					
			/**
			 * [@post_type_taxonomy_options] : Takes the list of all taxonomies related to the post type selected in "Query settings" */
			 
			$post_type_taxonomy_options	= $this->cspm_get_post_type_taxonomies($this->selected_cpt);		
				unset($post_type_taxonomy_options['post_format']);
				
			reset($post_type_taxonomy_options); // Set the cursor to 0
	
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_categories_taxonomy',
				'name' => 'Taxonomies',
				'desc' => 'Choose the taxonomy that represents the category of your posts.',
				'type' => 'radio',
				'default' => key($post_type_taxonomy_options), // Get the first option (term) in the taxonomies list
				'options' => $post_type_taxonomy_options,
			);

			$marker_categories_fields_array = array();
			
			foreach($post_type_taxonomy_options as $cpt_taxonomy_slug => $cpt_taxonomy_title){
	
				$tax_name = $cpt_taxonomy_slug;
				$tax_label = $cpt_taxonomy_title;
						
				$marker_categories_fields_array[] = array(
					'id' => 'marker_img_category_'.$tax_name,
					'name' => $tax_label, 
					'desc' => 'Select the marker category to which you want to add a custom image.',
					'type' => 'select',
					'options' => array('0'=>'')+$this->cspm_get_term_options($tax_name),
					'attributes'  => array(
						'data-conditional-id' => $this->metafield_prefix . '_marker_categories_taxonomy',
						'data-conditional-value' => wp_json_encode(array($this->metafield_prefix . '_marker_categories_taxonomy', $tax_name)),								
						'data-group-title' => 'select',
					)
				);
														
				$marker_categories_fields_array[] = array(
					'id' => 'marker_img_path_'.$tax_name,
					'name' => 'Marker image', 
					'desc' => 'Upload the marker category image.',
					'type' => 'file',
					'default' => '',
					'attributes'  => array(
						'data-conditional-id' => $this->metafield_prefix . '_marker_categories_taxonomy',
						'data-conditional-value' => wp_json_encode(array($this->metafield_prefix . '_marker_categories_taxonomy', $tax_name)),								
					),
				);
		
				$marker_categories_fields_array[] = array(
					'id' => 'marker_img_height_'.$tax_name,
					'name' => 'Marker image height', 
					'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
							  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
							  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
					'type' => 'text',
					'default' => '-1',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '-1',
						'data-conditional-id' => $this->metafield_prefix . '_marker_categories_taxonomy',
						'data-conditional-value' => wp_json_encode(array($this->metafield_prefix . '_marker_categories_taxonomy', $tax_name)),														
					),
					'sanitization_cb' => function($value, $field_args, $field){
						return ($value == '0') ? '-1' : $value;
					}	
				);
				
				$marker_categories_fields_array[] = array(
					'id' => 'marker_img_width_'.$tax_name,
					'name' => 'Marker image width', 
					'desc' => 'Specify the image width (in pixels). Set to -1 to ignore or to automatically calculate the width (for PNG/JPEG/GIF images).<br />
							  <span style="color:red;"><strong>Note: When set to -1, the image width will be automatically calculated but only if the image type is PNG/JPEG/GIF.
							  If you\'re using SVG icons, the image width will not be calculated and you may need to specify it.</strong></span>',
					'type' => 'text',
					'default' => '-1',
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '-1',
						'data-conditional-id' => $this->metafield_prefix . '_marker_categories_taxonomy',
						'data-conditional-value' => wp_json_encode(array($this->metafield_prefix . '_marker_categories_taxonomy', $tax_name)),														
					),
					'sanitization_cb' => function($value, $field_args, $field){
						return ($value == '0') ? '-1' : $value;
					}						
				);

			}
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_categories_images',
				'name' => 'Markers categories images', 
				'desc' => 'Upload a custom marker image for each category (taxonomy term).<br />
							<span style="color:red;">
							1. If one of the categories doesn\'t have a marker image  
							or that you don\'t want to use the custom markers feature at all, the default marker will be used instead.<br />
							2. If a post is assigned to multiple categories/terms, the plugin will call 
							the marker image of the first category/term in the list.</span>',
				'type' => 'group',
				'repeatable'  => true,
				'options'     => array(
					'group_title'   => esc_attr__( 'Marker Image {#}', 'cspm' ),
					'add_button'    => esc_attr__( 'Add New Marker Image', 'cspm' ),
					'remove_button' => esc_attr__( 'Remove Marker Image', 'cspm' ),
					'sortable'      => true,
					'closed'     => true,
				),
				'fields' => $marker_categories_fields_array,
			);
				
			return $fields;
			
		}
		
		
		/**
		 * Geotargeting settings
		 *
		 * @since 4.0
		 */
		function cspm_geotargeting_settings(){
			
			$fields = array();

			$fields[] = array(
				'name' => 'Geo-targeting Settings',
				'desc' => 'Geo-targeting allow the users to find and display their geographic location on the map',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_geotargeting_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_geoIpControl',
					'name' => 'Allow Geo-targeting',
					'desc' => 'The Geo-targeting is the method of determining the geolocation of a website visitor.',
					'type' => 'radio',
					'default' => $this->cspm_get_field_default('geoIpControl', 'false'),
					'options' => array(
						'true' => 'Yes',
						'false' => 'No'
					)
				);
					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_show_user',
					'name' => 'Show user location?',
					'desc' => 'Show a marker indicating the user\'s location on the map (when the user approves to share their location!).',
					'type' => 'radio',
					'default' => $this->cspm_get_field_default('show_user', 'false'),
					'options' => array(
						'true' => 'Yes',
						'false' => 'No'
					)
				);
					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_user_marker_icon',
					'name' => 'User Marker image',
					'desc' => 'Upload a marker image to display as the user location. When empty, the map will display the default marker of Google Map.',
					'type' => 'file',
					'default' => $this->cspm_get_field_default('user_marker_icon', ''),
				);
		
				$fields[] = array(
					'id' => $this->metafield_prefix . '_user_marker_icon_height',
					'name' => 'User Marker image height', 
					'desc' => 'Specify the image height (in pixels). Set to -1 to ignore or to automatically calculate the height (for PNG/JPEG/GIF images).<br />
							  <span style="color:red;"><strong>Note: When set to -1, the image height will be automatically calculated but only if the image type is PNG/JPEG/GIF.
							  If you\'re using SVG icons, the image height will not be calculated and you may need to specify it.</strong></span>',
					'type' => 'text',
					'default' => $this->cspm_get_field_default('user_marker_icon_height', ''),
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
					'id' => $this->metafield_prefix . '_user_marker_icon_width',
					'name' => 'User Marker image width', 
					'desc' => 'Specify the image width (in pixels). Set to -1 to ignore or to automatically calculate the width (for PNG/JPEG/GIF images).<br />
							  <span style="color:red;"><strong>Note: When set to -1, the image width will be automatically calculated but only if the image type is PNG/JPEG/GIF.
							  If you\'re using SVG icons, the image width will not be calculated and you may need to specify it.</strong></span>',
					'type' => 'text',
					'default' => $this->cspm_get_field_default('user_marker_icon_width', ''),
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
					'id' => $this->metafield_prefix . '_user_map_zoom',
					'name' => 'Geotarget Zoom',
					'desc' => 'Select the zoom of the map when indicating the user\'s location.',
					'type' => 'select',
					'default' => $this->cspm_get_field_default('user_map_zoom', '12'),
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
					'id' => $this->metafield_prefix . '_user_circle',
					'name' => 'Draw a Circle around the user\'s location',
					'desc' => 'Draw a circle within a certain distance of the user\'s location. Set to 0 to ignore this option.',
					'type' => 'text',
					'default' => $this->cspm_get_field_default('user_circle', '0'),
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '0'
					),				
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_user_circle_fillColor',
					'name' => 'Fill color',
					'desc' => 'The fill color of the circle.',
					'type' => 'colorpicker',
					'default' => $this->cspm_get_field_default('user_circle_fillColor', '#189AC9'),															
				);

				$fields[] = array(
					'id' => $this->metafield_prefix . '_user_circle_fillOpacity',
					'name' => 'Fill opacity',
					'desc' => 'The fill opacity of the circle between 0.0 and 1.0.',
					'type' => 'select',
					'default' => $this->cspm_get_field_default('user_circle_fillOpacity', '0.1'),
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
					'id' => $this->metafield_prefix . '_user_circle_strokeColor',
					'name' => 'Stroke color',
					'desc' => 'The stroke color of the circle.',
					'type' => 'colorpicker',
					'default' => $this->cspm_get_field_default('user_circle_strokeColor', '#189AC9'),
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_user_circle_strokeOpacity',
					'name' => 'Stroke opacity',
					'desc' => 'The stroke opacity of the circle between 0.0 and 1.',
					'type' => 'select',
					'default' => $this->cspm_get_field_default('user_circle_strokeOpacity', '1'),
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
					'id' => $this->metafield_prefix . '_user_circle_strokeWeight',
					'name' => 'Stroke weight',
					'desc' => 'The stroke width of the circle in pixels.',
					'type' => 'text',
					'default' => $this->cspm_get_field_default('user_circle_strokeWeight', '1'),
					'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'min' => '0'
					),				
				);	
			
			return $fields;
					
		}
		
		/**
		 * Heatmap Layer Settings Fields 
		 *
		 * @since 5.3
		 */
		function cspm_heatmap_layer_fields(){
			
			$fields = array();

			$fields[] = array(
				'name' => 'Heatmap Layer Settings',
				'desc' => 'A heatmap is a visualization used to depict the intensity of data at geographical points. When the Heatmap Layer is enabled, a colored overlay will appear on top of the map. By default, areas of higher intensity will be colored red, and areas of lower intensity will appear green.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_heatmap_layer_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
						
			$fields[] = array(
				'id' => $this->metafield_prefix . '_heatmap_layer',
				'name' => 'Heatmap Layer',
				'desc' => 'Display the Heatmap layer on the map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No',
					'toggle_markers' => 'Yes and change the markers visibility',
				),
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_heatmap_dissipating',
				'name' => 'Heatmap dissipating',
				'desc' => 'Specifies whether heatmaps dissipate on zoom. 
						   When dissipating is "No" the radius of influence increases with zoom level to ensure that the color intensity 
						   is preserved at any given geographic location. Defaults to "Yes".',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
								
			$fields[] = array(
				'id' => $this->metafield_prefix . '_heatmap_point_weight',
				'name' => 'Heatmap point weight',
				'desc' => 'Set how much each individual point contributes to the intensity of your heatmap. 
						   By default, each point has an implicit weight of 1. 
						   Increasing the weight to 3 will have the same effect as placing three points in the same location. 
						   Defaults to "1".',
				'type' => 'text',
				'default' => '1',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '1'
				)
			);
									
			$fields[] = array(
				'id' => $this->metafield_prefix . '_heatmap_intensity',
				'name' => 'Heatmap maximum intensity',
				'desc' => 'Set the maximum intensity of the heatmap. 
						   By default, heatmap colors are dynamically scaled according to the greatest concentration of points at 
						   any particular pixel on the map. This property allows you to specify a fixed maximum. 
						   Setting the maximum intensity can be helpful when your dataset contains a few outliers with an unusually high intensity.',
				'type' => 'text',
				'default' => '',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '1'
				)
			);
									
			$fields[] = array(
				'id' => $this->metafield_prefix . '_heatmap_radius',
				'name' => 'Heatmap point radius',
				'desc' => 'Set the radius of influence for each point in pixels.',
				'type' => 'text',
				'default' => '',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '1'
				)
			);			
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_heatmap_opacity',
				'name' => 'Heatmap opacity',
				'desc' => 'Set the global opacity of the heatmap layer, between 0 and 1. Defaults to "0.6"',
				'type' => 'select',
				'default' => '0,6',
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
				),						
			);				
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_heatmap_color_type',
				'name' => 'Heatmap color gradient',
				'desc' => 'Choose the color gradient to use for the heatmap layer. <span style="color:red;">When selecting the option <u>"Custom color gradient"</u>, scroll down and 
						   generate your color gradient!</span> Defaults to "Google Maps color gradient".',
				'type' => 'radio',
				'default' => 'default',
				'options' => array(
					'default' => 'Google Maps color gradient',
					'custom' => 'Custom color gradient'
				)
			);
								
			$fields[] = array(
				'id' => $this->metafield_prefix . '_heatmap_colors',
				'name' => '<span style="font-size:15px; color:#ff6600; font-weight:600;">Heatmap Color Gradient Generator</span>',
				'desc' => 'Generate your custom heatmap color gradient. A color gradient (sometimes called a color ramp or color progression) specifies a range of position-dependent colors, usually used to fill a region.<br />
						   <strong style="font-size:15px; margin-top:5px; display:inline-block;">How it works?</strong><br />
						   Select the strat color, the middle color and the end color, then, select the number of stops/steps to add between the selected colors.<br />
						   Scroll down to preview your generated color gradient!',
				'type' => 'group',
				'repeatable' => false,
				'options' => array(
					'group_title' => esc_attr__( 'Color Gradient', 'cspm' ),
					'sortable' => false,
					'closed' => false,
				),
				'fields' => array(	
					array(
						'id' => 'start_color',
						'name' => 'Start color <small style="color:red; font-weight:400;"><u>Required!</u></small>',
						'desc' => 'The start color refers to the color used to represent the lowest density of data.',
						'type' => 'colorpicker',
						'default' => '#defcf6',
					),
					array(
						'id' => 'middle_color',
						'name' => 'Middle color <small style="color:red; font-weight:400;"><u>Optional</u></small>',
						'desc' => 'The middle color refers to the color used to represent the medium density of data.',
						'type' => 'colorpicker',
						'default' => '',					
					),
					array(
						'id' => 'end_color',
						'name' => 'End color <small style="color:red; font-weight:400;"><u>Required!</u></small>',
						'desc' => 'The end color refers to the color used to represent the highest density of data.',
						'type' => 'colorpicker',
						'default' => '#00e5ff',
					),
					array(
						'id' => 'stops',
						'name' => 'Number of stops/steps',
						'desc' => 'The number of stops/steps is the number of colors to add between the Start & End colors (start, middle & end colors included!). 
								   Maximum number of steps is 255. Defaults to "10".',
						'type' => 'text',
						'default' => '10',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '2',
							'max' => '255'
						),
						'after_row' => '<p><strong>Preview</strong></p><div class="cspm_heatmap_gradient_preview" style="width: 100%; height: 20px; display:block; border: 0px; overflow: hidden; margin-bottom: 20px;"></div>',	
					),
				),
			);
						
			return $fields;
					
		}
		
		/**
		 * KML Layers Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_kml_fields(){
			
			$fields = array();

			$fields[] = array(
				'name' => 'KML Layers Settings',
				'desc' => 'Layers are objects on the map that consist of one or more separate items, but are manipulated as a single unit. Layers generally reflect collections of objects that you add on top of the map to designate a common association. The Google Maps API manages the presentation of objects within layers by rendering their constituent items into one object (typically a tile overlay) and displaying them as the map\'s viewport changes. Layers may also alter the presentation layer of the map itself, slightly altering the base tiles in a fashion consistent with the layer.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_kml_layers_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_use_kml',
				'name' => 'KML Layers option',
				'desc' => 'Select "Yes" to enable the KML Layers option for this map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_kml_list',
				'name' => 'KML Layers list',
				'desc' => 'Add a dropdown list to your map to allow users to show & hide KML layers displayed on the map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_kml_list_display_status',
				'name' => 'KML Layers list display status',
				'desc' => 'Choose whether to open or close the KML Layers list on map load. Defaults to "Close".',
				'type' => 'radio',
				'default' => 'close',
				'options' => array(
					'open' => 'Open',
					'close' => 'Close'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_kml_list_placeholder',
				'name' => 'KML Layers list placeholder',
				'desc' => 'Edit the text to show as a placeholder of the KML Layers list',
				'type' => 'text',
				'default' => 'Show/Hide KML Layers',
			);
            
            $fields[] = array(
				'id' => $this->metafield_prefix . '_kml_layers_onload_status',
				'name' => 'KML Layers onLoad status',
				'desc' => 'Choose whether to show or hide all KML Layers on map load. Defaults to "Show".<br />
                           By choosing the option "Show", all KML Layers will be visible once the map is loaded.<br />
                           By choosing the option "Hide", all KML Layers will hidden when the map is loaded. Users will have to 
                           use the KML Layers\'s list to show them.',
				'type' => 'radio',
				'default' => 'show',
				'options' => array(
					'show' => 'Show',
					'hide' => 'Hide'
				)
			);
					
			$fields[] = array(
				'id' => $this->metafield_prefix . '_kml_layers',
				'name' => 'KML/KMZ Layers',
				'desc' => 'Click on the button "Add New KML/KMZ" to add a new KML/KMZ file. You can add Multiple KML/KMZ layers!
						   <br /><span style="color:red"><strong>Note:</strong> If you have multiple KML Layers and you want to automatically center and zoom the map to the bounding box of the contents of your layers, activate the option <strong>"Map settings => Autofit"</strong>!</span>',
				'type' => 'group',
				'repeatable'  => true,
				'options'     => array(
					'group_title'   => esc_attr__( 'KML/KMZ {#}', 'cspm' ),
					'add_button'    => esc_attr__( 'Add New KML/KMZ', 'cspm' ),
					'remove_button' => esc_attr__( 'Remove KML/KMZ', 'cspm' ),
					'sortable'      => true,
					'closed'     => true,
				),
				'fields' => array(	
					array(
						'id' => 'kml_label',
						'name' => 'KML/KMZ Label', 
						'desc' => 'Give a label to this KML/KMZ. The Label will help to distinct a KML/KMZ between multiple KML/KMZ layers. (Example: "Lodon Air Quality")',
						'type' => 'text',
						'default' => '',
						'attributes'  => array(
							'data-group-title' => 'text'
						)
					),
					array(
						'id' => 'kml_url',
						'name' => 'KML/KMZ File URL',
						'desc' => 'Supply a link to a KML file or KMZ file that\'s already <span style="color:red">hosted on the Internet.</span>
								   <br /><span style="color:red"><strong>Note:</strong> You can use the Media Library to upload your file, then, paste its URL in this field.</span>',
						'type' => 'text_url',
						'default' => ''
					),
					array(
						'id' => 'kml_suppressInfoWindows',
						'name' => 'Suppress Infowindows',
						'desc' => 'Suppress the rendering of info windows when layer features are clicked. Defaults to "No".',
						'type' => 'radio',
						'default' => 'false',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						)
					),
					array(
						'id' => 'kml_preserveViewport',
						'name' => 'Preserve Viewport',
						'desc' => 'Select whether you want to center and zoom the map to the bounding box of the contents of the layer. If this option is set to "Yes", the viewport is left unchanged. Defaults to "No".<br />
								   <span style="color:red;"><strong>Note:</strong> If this is the only KML Layer you\'ve created, this option will be ignored if you activate the option <strong>"Map settings => Autofit"</strong>!</span>',
						'type' => 'radio',
						'default' => 'false',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						)
					),
					array(
						'id' => 'kml_screenOverlays',
						'name' => 'Screen Overlays',
						'desc' => 'Select whether to render the screen overlays included in this KML/KMZ layer. Defaults to "no".',
						'type' => 'radio',
						'default' => 'false',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						)
					),
					array(
						'id' => 'kml_zIndex',
						'name' => 'zIndex',
						'desc' => 'The zIndex compared to other KML/KMZ layers.',
						'type' => 'text',
						'default' => '1',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
						),				
					),
					array(
						'id' => 'kml_connected_post',
						'name' => 'KML connected post', 
						'desc' => 'Enter the post ID. By connecting this KML/KMZ layer to a specific post, the KML/KMZ layer visibility will depend on 
									that post visibility on the map.',
						'type' => 'text',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
						),						
					), //@since 4.6
					array(
						'id' => 'kml_visibility',
						'name' => 'Visibility',
						'desc' => 'Whether this KML/KMZ is visible on the map. Defaults to "Yes".',
						'type' => 'radio',
						'default' => 'true',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						)
					)					
				)
			);

			return $fields;
			
		}

		
		/**
		 * Ground Overlays Settings Fields 
		 *
		 * @since 1.0 
		 * @updated 4.0
		 */
		function cspm_ground_overlays_fields(){
			
			$fields = array();
			 
			$fields[] = array(
				'id' => $this->metafield_prefix . '_ground_overlay_section',
				'name' => 'Ground Overlays Settings',
				'desc' => 'Use the ground overlays to place images on the map. The image will be rendered on the map, constrained to the given bounds, and conformed using the map\'s projection.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
					
			$fields[] = array(
				'id' => $this->metafield_prefix . '_ground_overlays_option',
				'name' => 'Ground overlays option',
				'desc' => 'Select "Yes" to enable this option in your map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
									
			$fields[] = array(
				'id' => $this->metafield_prefix . '_ground_overlays',
				'name' => 'Images (Ground Overlays)',
				'desc' => 'Click on the button "Add New Image" to add a new image. You can add Multiple images!',
				'type' => 'group',
				'repeatable'  => true,
				'options'     => array(
					'group_title'   => esc_attr__( 'Image {#}', 'cspm' ),
					'add_button'    => esc_attr__( 'Add New Image', 'cspm' ),
					'remove_button' => esc_attr__( 'Remove Image', 'cspm' ),
					'sortable'      => true,
					'closed'     => true,
				),
				'fields' => array(	
					array(
						'id' => 'image_label',
						'name' => 'Image Label/Title', 
						'desc' => 'Give a title to this image. The title will help to distinct an image between multiple images. (Example: "Lodon Image")',
						'type' => 'text',
						'default' => '',
						'attributes'  => array(
							'data-group-title' => 'text'
						)
					),
					array(
						'name' => '',
						'desc' => '<strong style="font-size:15px; display: inline-block; padding: 5px 0;">How to add your image overlay?</strong><br />						  
								  Before getting started, you need to pan the map to your desired area. 
								  You can use the address search field or geolocate your position for that purpose!<br />
								  <strong>1.</strong> Click (if not already selected) on the rectangle button/icon on the map (next to the hand button/icon).<br />						  
								  <strong>2.</strong> Draw a rectangle around your desired area by clicking on a specific spot on the map, then, drag the cursor 
								  to cover the whole area.<br />
								  <strong>3.</strong> Paste your image URL in the field <u>"Image URL"</u>, then, click on the button <u>"Project image"</u> to project 
								  your image on the map.<br />
								  <strong>4.</strong> Once finished, you can edit the rectangle by dragging a vertex/point to another spot. To drag 
								  the whole rectangle, first, check the option <u>"Activate dragging mode"</u> on top of the map, then start dragging it.<br />
								  <strong>5.</strong> To remove the image, clear the field <u>"Image URL"</u>, then, click on the button <u>"Project image"</u>.<br />
								  <strong>6.</strong> If you need to start drawing from scratch, click on the button <u>"Draw from scratch"</u>, then, start 
								  tracing new bounds.<br />
								  <strong>7.</strong> Once satisfied with the position of your image on the map, click on the button <u>"Display bounds"</u>.<br />
								  <span style="color:red;">
								  <strong>Hint:</strong> For more precision while tracing the bounds, use the map\'s full screen mode!
								  </span>',
						'id' => 'image_url_and_bounds',
						'type' => 'cs_gmaps_drawing',
						'default' => '',
						'options' => array(
							'api_key' => isset($this->plugin_settings['api_key']) ? $this->plugin_settings['api_key'] : '',
							'disable_gmaps_api' => false,
							'disable_gmap3_plugin' => false,
							'map_height' => '200px',
							'map_width' => '100%',
							'map_center' => $this->cspm_get_field_default('map_center', '51.53096,-0.121064'),
							'map_zoom' => 12,
							'img_url_field_name' => 'image_url',		
							'ne_coordinates_field_name' => 'ne_bounds',
		 					'sw_coordinates_field_name' => 'sw_bounds',					
							'labels' => array(
								'address' => 'Enter a location & search or Geolocate your position',						
								'search' => 'Search',
								'pinpoint' => 'Display bounds',
								'clear_overlay' => 'Draw from scratch',
								'ne_label' => 'North-East coordinates',
								'sw_label' => 'South-West coordinates',
								'toggle' => 'Image overlay URL & Bounds',
								'top_desc' => 'Create a ground overlay by providing an image URL and its "Lat,Lng" bounds.<br />
											  <span style="color:red;">Read the insctructions below for more details about finding the image overlay bounds!</span><hr />',
							),
							'fields_desc' => array(
								'img_url' => 'Upload or Enter the image URL.',
								'ne' => 'Enter the north-east (top-right) corner of the image bounds. The coordinates (Latitude & Longitude) of the image top-right corner.',
								'sw' => 'Enter the south-west (bottom-left) corner of the image bounds. The coordinates (Latitude & Longitude) of the image bottom-left corner.',
							),
							'draw_mode' => 'image',
							'save_coordinates' => true,
							'toggle' => true,
							'close' => false,
						),
					),					
					array(
						'id' => 'opacity',
						'name' => 'Image opacity',
						'desc' => 'Select the image opacity, between 0.0 and 1.0.',
						'type' => 'select',
						'default' => '0,9',
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
						),
						'before_row' => str_replace(array('[title]'), array('Image options'), $this->toggle_before_row),			
					),
					array(
						'id' => 'image_visibility',
						'name' => 'Image visibility',
						'desc' => 'Choose the image visibility on the map. Defaults to "Always visible".',
						'type' => 'radio',
						'default' => 'always',
						'options' => array(
							'always' => 'Always visible',
							'hide' => 'Hide on map load and allow users to show & hide it when they want',
							'show' => 'Show on map load and allow users to hide & show it when they want',
							'marker_connected' => 'Connect the image to a marker and show/hide it depending on that marker visibility',
							'disable' => 'Disable'
						) 
					),
					array(
						'id' => 'image_connected_post',
						'name' => 'Image connected post', 
						'desc' => 'Enter the post ID. By connecting this image to a specific post, the image visibility will depend on 
									that post visibility on the map',
						'type' => 'text',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
						),						
					),
					array(
						'id' => 'image_clickable',
						'name' => 'Clickable',
						'desc' => 'Indicate whether this image handles mouse events. Defaults to "No".',
						'type' => 'radio',
						'default' => 'false',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						)
					),
					array(
						'id' => 'image_redirect_url',
						'name' => 'Image redirect URL',
						'desc' => 'If provided, the URL will be executed when the Image is clicked. <span style="color:red;">Works only when "Clickable" is set to "Yes"!</span>',
						'type' => 'text_url',
						'default' => ''
					),
					array(
						'id' => 'image_url_target',
						'name' => 'URL target',
						'desc' => 'Choose an option to open the Image redirect URL. Defaults to "Open in a new window".',
						'type' => 'radio',
						'default' => 'new_window',
						'options' => array(
							'new_window' => 'Open in a new window',
							'same_window' => 'Open in the same window',
                            'popup' => 'Open inside a modal/popup',
						)
					),
					array(
						'id' => 'show_btn_icon',
						'name' => 'Show button icon',
						'desc' => 'Upload the image to display in the show button. This button will allow the users to show the image.',
						'type' => 'file',
						'default' => $this->plugin_url.'img/switch-on.png',
						'text' => array(
							'add_upload_file_text' => 'Upload icon',
						),
						'preview_size' => array( 24, 24 ),
						'query_args' => array(
							'type' => 'image',
						)								
					),
					array(
						'id' => 'hide_btn_icon',
						'name' => 'Hide buttom icon',
						'desc' => 'Upload the image to display in the hide button. This button will allow the users to hide the image.',
						'type' => 'file',
						'default' => $this->plugin_url.'img/switch-off.png',
						'text' => array(
							'add_upload_file_text' => 'Upload icon',
						),
						'preview_size' => array( 24, 24),
						'query_args' => array(
							'type' => 'image',
						)								
					),
					array(
						'id' => 'btn_position',
						'name' => 'Icon position',
						'desc' => 'By default, the show/hide icon will be displayed in the North-East (top-right) corner of the image (ground overlay).
								  If you want to change the icon position, specify other coordinates "Latitude" & "Longitude" or leave this field empty.',
						'type' => 'text',
						'default' => '',
						'after_row' => $this->toggle_after_row,
					),				
				)
			);
						
			return $fields;
			
		}
		
		/**
		 * Polyline Settings Fields 
		 *
		 * @since 4.0 
		 */
		function cspm_polylines_fields(){
			
			$fields = array();			
			 
			$fields[] = array(
				'id' => $this->metafield_prefix . '_polyline_section',
				'name' => 'Polylines',
				'desc' => 'To draw a line on your map, use a polyline. The Polyline class defines a linear overlay of connected line segments on the map. A Polyline object consists of an array of LatLng locations, and creates a series of line segments that connect those locations in an ordered sequence.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
					
			$fields[] = array(
				'id' => $this->metafield_prefix . '_draw_polyline',
				'name' => 'Draw Polyline option',
				'desc' => 'Select "Yes" to enable this option in your map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);		
			
			$group_id = $this->metafield_prefix . '_polylines';
														
			$fields[] = array(
				'id' => $group_id,
				'name' => '<span style="font-size:15px; color:#ff6600; font-weight:600;">Polylines</span>',
				'desc' => 'Click on the button "Add New Polyline" to add a new polyline. You can add Multiple polylines!',
				'type' => 'group',
				'repeatable'  => true,
				'options'     => array(
					'group_title'   => esc_attr__( 'Polyline {#}', 'cspm' ),
					'add_button'    => esc_attr__( 'Add New Polyline', 'cspm' ),
					'remove_button' => esc_attr__( 'Remove Polyline', 'cspm' ),
					'sortable'      => true,
					'closed'     => true,
				),
				'fields' => array(	
					array(
						'id' => 'polyline_label',
						'name' => 'Polyline Label', 
						'desc' => 'Give a label to this Polyline. The Label will help to distinct a polyline between multiple Polylines. (Example: "Lodon Polyline")',
						'type' => 'text',
						'default' => '',
						'attributes'  => array(
							'data-group-title' => 'text'
						)
					),
					array(
						'id' => 'polyline_name',
						'name' => 'Polyline ID/Name', 
						'desc' => 'Give a unique ID/Name to this Polyline. <span style="color:red">If two polylines has the same IDs/Names, the last added polyline will override the old polyline.</span> (Example: "london_polyline")',
						'type' => 'text',
						'default' => '',
					),
					array(
						'id' => 'polyline_points_type',
						'name' => 'Polyline points type',
						'desc' => 'Select the polyline points type. Defaults to "Lat,Lng".<br />
								   <span style="color:red;">You can build a polyline either by adding multiple Lat,Lng coordinates or by connecting 
								   multiple posts already available on your map.</span>',
						'type' => 'radio',
						'default' => 'latlng',
						'options' => array(
							'latlng' => 'Lat,Lng',
							'post_ids' => 'Post IDs'
						)
					),
					array(
						'name' => '',
						'desc' => '<strong style="font-size:15px; display: inline-block; padding: 5px 0;">How to draw a polyline?</strong><br />
								  Before drawing a polyline, you need to pan the map to the area where you want to draw. 
								  You can use the address search field or geolocate your position for that purpose!<br />
								  <strong>1.</strong> Click (if not already selected) on the polyline button/icon on the map (next to the hand button/icon).<br />
								  <strong>2.</strong> Draw your polyline by clicking on specific spots on the map. Double click on the last spot to finish drawing your polyline.<br />
								  <strong>3.</strong> Once finished, you can edit your polyline by dragging a vertex/point to another spot. Double click on a point/vertex to remove it. 
								  To drag the polyline, first, check the option <u>"Activate dragging mode"</u> on top of the map, then start dragging it.<br />
								  <strong>4.</strong> If you need to start drawing from scratch, click on the button <u>"Draw from scratch"</u>, then, start 
								  drawing a new polyline.<br />
								  <strong>5.</strong> Once satisfied with your polyline, click on the button <u>"Display polyline coordinates"</u>.<br />
								  <strong>Hint:</strong> For more precision while drawing the polyline, use the map\'s full screen mode!
								  </span>',								  
						'id' => 'polyline_path',
						'type' => 'cs_gmaps_drawing',
						'default' => '',
						'options' => array(
							'api_key' => isset($this->plugin_settings['api_key']) ? $this->plugin_settings['api_key'] : '',
							'disable_gmaps_api' => false,
							'disable_gmap3_plugin' => false,
							'map_height' => '200px',
							'map_width' => '100%',
							'map_center' => $this->cspm_get_field_default('map_center', '51.53096,-0.121064'),
							'map_zoom' => 12,
							'labels' => array(
								'address' => 'Enter a location & search or Geolocate your position',						
								'coordinates' => 'Polyline coordinates',
								'search' => 'Search',
								'pinpoint' => 'Display polyline coordinates',
								'clear_overlay' => 'Draw from scratch',
								'toggle' => 'Polyline Path [Lat,Lng]',
								'top_desc' => 'Draw your polyline. Or, in case you already have the polyline coordinates, simply paste them in the field/textarea below by putting each line segment (LatLng) as <strong>[Lat,Lng]</strong> or <strong>[Lng,Lat]</strong> separated by comma.<br />
											  <strong>Example: </strong><code>[45.5215,-1.5245],[41.2587,-1.2479],[40.1649,-1.9879]</code><br />
											  <span style="color:red;">Read the instructions below to see how to draw a polyline.</span><hr />',								
							),
							'draw_mode' => 'polyline',
							'save_coordinates' => true,
							'toggle' => false,
							'close' => false,
							'latLng_order' => 'latLng', // Get coordinates order
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'polyline_points_type' ) ),
							'data-conditional-value' => 'latlng',								
						),	
						'before_row' => str_replace(array('[title]'), array('Polyline Path'), $this->toggle_before_row),										
					),
					array(
						'id' => 'polyline_latlng_order',
						'name' => 'LatLngs order', 
						'desc' => 'Select the syntaxe you\'ve followed to enter the coordinates of this Polyline.',
						'type' => 'radio',
						'default' => 'latLng',
						'options' => array(
							'latLng' => 'Lat,Lng',
							'lngLat' => 'Lng,Lat',
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'polyline_points_type' ) ),
							'data-conditional-value' => 'latlng',								
						),							
					),
					array(
						'id' => 'polyline_path_ids',
						'name' => 'Polyline Path (Post IDs)', 
						'desc' => 'Select the posts that will be used/connected to form the polyline. You should add at least 2 posts. 
								   <br /><span style="color:red;">Type an empty space to list all available posts! You can drag & drop the posts in the list to change their order!</span>
								   <br /><span style="color:red"><strong>Note:</strong> The polyline points/vertices order is defined by the order of the posts in the list!</span>',
						'type' => 'post_ajax_search',
						'multiple-item' => true,					
						'limit' => -1, 
						'sortable' => true,
						'query_args' => array(
							'post_type' => array( $this->selected_cpt ),
							'posts_per_page' => -1
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'polyline_points_type' ) ),
							'data-conditional-value' => 'post_ids',								
						),						
						'after_row' => $this->toggle_after_row,	
					),
					
					/**
					 * Polyline options */
					 
					array(
						'id' => 'polyline_visibility',
						'name' => 'Visibility',
						'desc' => 'Whether this polyline is visible on the map. Defaults to "Yes".',
						'type' => 'radio_inline',
						'default' => 'true',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						),						
						'before_row' => str_replace(array('[title]'), array('Polyline options'), $this->toggle_before_row),
					),
					array(
						'id' => 'polyline_clickable',
						'name' => 'Clickable',
						'desc' => 'Indicate whether this Polyline handles mouse events. Defaults to "No".',
						'type' => 'radio_inline',
						'default' => 'false',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						),						
					),
					array(
						'id' => 'polyline_url',
						'name' => 'Polyline redirect URL',
						'desc' => 'If provided, the URL will be executed when the Polyline is clicked. <span style="color:red;">Works only when "Clickable" is set to "Yes"!</span>',
						'type' => 'text_url',
						'default' => ''
					),
					array(
						'id' => 'polyline_url_target',
						'name' => 'URL target',
						'desc' => 'Choose an option to open the Polyline redirect URL. Defaults to "Open in a new window".',
						'type' => 'radio',
						'default' => 'new_window',
						'options' => array(
							'new_window' => 'Open in a new window',
							'same_window' => 'Open in the same window',
                            'popup' => 'Open inside a modal/popup',
						)
					),			
					array(
						'id' => 'polyline_description',
						'name' => 'Polyline description',
						'desc' => 'Enter the message text or the description to display inside an infowindow when the Polyline is hovered over. The infowindow will be removed once the mouse leaves the Polyline!
								    <span style="color:red;">Works only when "Clickable" is set to "Yes"!</span>
									<br /><span style="color:red;"><strong>Note:</strong> Shortcodes are not allowed!</span>',
						'type' => 'wysiwyg',
						'default' => ''
					),
					array(
						'id' => 'polyline_infowindow_maxwidth',
						'name' => 'Infowindow Maximum width',
						'desc' => 'Maximum width (in pixels) of the infowindow, regardless of the Polyline description\'s width. Defaults to "200px".',
						'type' => 'text',
						'default' => '250',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '0'
						),				
					),										
					array(
						'id' => 'polyline_geodesic',
						'name' => 'Geodesic',
						'desc' => 'When "Yes", edges of the polyline are interpreted as geodesic and will follow the curvature of the Earth. When "No", edges of the polyline are rendered as straight lines in screen space. Defaults to "No".',
						'type' => 'radio',
						'default' => 'false',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						)
					),
					array(
						'id' => 'polyline_zIndex',
						'name' => 'zIndex',
						'desc' => 'The zIndex compared to other polylines.',
						'type' => 'text',
						'default' => '',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
						),				
					),
					array(
						'id' => 'polyline_connected_post',
						'name' => 'Polyline connected post', 
						'desc' => 'Enter the post ID. By connecting this polyline to a specific post, the polyline visibility will depend on 
									that post visibility on the map',
						'type' => 'text',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
						),	
						'after_row' => $this->toggle_after_row,					
					),					
					
					/**
					 * Polyline style */
					 
					array(
						'id' => 'polyline_strokeColor',
						'name' => 'Stroke color',
						'desc' => 'The stroke color. Defaults to "#189AC9".',
						'type' => 'colorpicker',
						'default' => '#189AC9',
						'before_row' => str_replace(array('[title]'), array('Polyline style'), $this->toggle_before_row),						
					),		
					array(
						'id' => 'polyline_strokeOpacity',
						'name' => 'Stroke opacity',
						'desc' => 'The stroke opacity between 0.0 and 1. Defaults to "1".',
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
					),	
					array(
						'id' => 'polyline_strokeWeight',
						'name' => 'Stroke weight',
						'desc' => 'The stroke width in pixels. Defaults to "2".',
						'type' => 'text',
						'default' => '2',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '0'
						),		
					),
					array(
						'id' => 'polyline_strokeType',
						'name' => 'Stroke type',
						'desc' => 'The stroke type. Defaults to "Simple line".',
						'type' => 'radio',
						'default' => 'simple',
						'options' => array(
							'simple' => 'Simple line',
							'dashed' => 'Dashed line',
						),						
					),	
					array(
						'id' => 'polyline_icons_repeat',
						'name' => 'Distance between dashes',
						'desc' => 'The distance between consecutive dashes in pixels. Defaults to "15px".',
						'type' => 'text',
						'default' => '10',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '1',
							'data-conditional-id' => wp_json_encode( array( $group_id, 'polyline_strokeType' ) ),
							'data-conditional-value' => 'dashed',								
						),	
						'after_row' => $this->toggle_after_row,			
					),
					
				)
			);
						
			return $fields;
			
		}
		
		/**
		 * Polygons Settings Fields 
		 *
		 * @since 4.0 
		 */
		function cspm_polygons_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_polygon_section',
				'name' => 'Polygons',
				'desc' => 'A polygon represents an area enclosed by a closed path (or loop), which is defined by a series of coordinates. Polygon objects are similar to Polyline objects in that they consist of a series of coordinates in an ordered sequence. Polygons are drawn with a stroke and a fill. You can define custom colors, weights, and opacities for the edge of the polygon (the stroke) and custom colors and opacities for the enclosed area (the fill).',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
					
			$fields[] = array(
				'id' => $this->metafield_prefix . '_draw_polygon',
				'name' => 'Draw Polygon option',
				'desc' => 'Select "Yes" to enable this option in your map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$group_id = $this->metafield_prefix . '_polygons';
														
			$fields[] = array(
				'id' => $group_id,
				'name' => '<span style="font-size:15px; color:#ff6600; font-weight:600;">Polygons</span>',
				'desc' => 'Click on the button "Add New Polygon" to add a new polygon. You can add Multiple polygons!',
				'type' => 'group',
				'repeatable'  => true,
				'options'     => array(
					'group_title'   => esc_attr__( 'Polygon {#}', 'cspm' ),
					'add_button'    => esc_attr__( 'Add New Polygon', 'cspm' ),
					'remove_button' => esc_attr__( 'Remove Polygon', 'cspm' ),
					'sortable'      => true,
					'closed'     => true,
				),
				'fields' => array(	
					array(
						'id' => 'polygon_label',
						'name' => 'Polygon Label', 
						'desc' => 'Give a label to this Polygon. The Label will help to distinct a polygon between multiple Polygons. (Example: "Lodon Polygon")',
						'type' => 'text',
						'default' => '',
						'attributes'  => array(
							//'required'    => 'required',
							//'data-validation' => 'required',
							'data-group-title' => 'text'
						)
					),
					array(
						'id' => 'polygon_name',
						'name' => 'Polygon ID/Name', 
						'desc' => 'Give a unique ID/Name to this Polygon. <span style="color:red">If two polygons has the same IDs/Names, the last added polygon will override the old polygon.</span> (Example: "london_polygon")',
						'type' => 'text',
						'default' => '',
					),
					array(
						'id' => 'polygon_points_type',
						'name' => 'Polygon points type',
						'desc' => 'Select the polygon points type. Defaults to "Lat,Lng".<br />
								   <span style="color:red;">You can build a polygon either by adding multiple Lat,Lng coordinates or by connecting 
								   multiple posts already available on your map.</span>',
						'type' => 'radio',
						'default' => 'latlng',
						'options' => array(
							'latlng' => 'Lat,Lng',
							'post_ids' => 'Post IDs'
						)
					),
					array(
						'name' => '',
						'desc' => '<strong style="font-size:15px; display: inline-block; padding: 5px 0;">How to draw a polygon?</strong><br />
								  Before drawing a polygon, you need to pan the map to the area where you want to draw. 
								  You can use the address search field or geolocate your position for that purpose!<br />
								  <strong>1.</strong> Click (if not already selected) on the polygon button/icon on the map (next to the hand button/icon).<br />
								  <strong>2.</strong> Draw your polygon by clicking on specific spots on the map. To close your polygon, double click on 
								  the last spot or click on an already created point.<br />
								  <strong>3.</strong> Once finished, you can edit your polygon by dragging a vertex/point to another spot. Double click on a point/vertex to remove it. 
								  To drag the polygon, first, check the option <u>"Activate dragging mode"</u> on top of the map, then start dragging it.<br />
								  <strong>4.</strong> If you need to start drawing from scratch, click on the button <u>"Draw from scratch"</u>, then, start 
								  drawing a new polygon.<br />
								  <strong>5.</strong> Once satisfied with your polygon, click on the button <u>"Display polygon coordinates"</u>.<br />
								  <strong>Hint:</strong> For more precision while drawing the polygon, use the map\'s full screen mode!
								  </span>',								  
						'id' => 'polygon_path',
						'type' => 'cs_gmaps_drawing',
						'default' => '',
						'options' => array(
							'api_key' => isset($this->plugin_settings['api_key']) ? $this->plugin_settings['api_key'] : '',
							'disable_gmaps_api' => false,
							'disable_gmap3_plugin' => false,
							'map_height' => '200px',
							'map_width' => '100%',
							'map_center' => $this->cspm_get_field_default('map_center', '51.53096,-0.121064'),
							'map_zoom' => 12,
							'labels' => array(
								'address' => 'Enter a location & search or Geolocate your position',						
								'coordinates' => 'Polygon coordinates',
								'search' => 'Search',
								'pinpoint' => 'Display polygon coordinates',
								'clear_overlay' => 'Draw from scratch',
								'toggle' => 'Polygon Paths [Lat,Lng]',
								'top_desc' => 'Draw your polygon. Or, in case you already have the polygon coordinates, simply paste them in the field/textarea below by putting each line segment (LatLng) as <strong>[Lat,Lng]</strong> or <strong>[Lng,Lat]</strong> separated by comma.<br />
											  <strong>Example: </strong><code>[45.5215,-1.5245],[41.2587,-1.2479],[40.1649,-1.9879]</code><br />
											  <span style="color:red;">Read the instructions below to see how to draw a polygon.</span><hr />',								
							),
							'draw_mode' => 'polygon',
							'save_coordinates' => true,
							'toggle' => false,
							'close' => false,
							'latLng_order' => 'latLng', // Get coordinates order
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'polygon_points_type' ) ),
							'data-conditional-value' => 'latlng',								
						),
						'before_row' => str_replace(array('[title]'), array('Polygon Paths'), $this->toggle_before_row),											
					),
					array(
						'id' => 'polygon_latlng_order',
						'name' => 'LatLngs order', 
						'desc' => 'Select the syntaxe you\'ve followed to enter the coordinates of this Polygon.',
						'type' => 'radio',
						'default' => 'latLng',
						'options' => array(
							'latLng' => 'Lat,Lng',
							'lngLat' => 'Lng,Lat',
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'polygon_points_type' ) ),
							'data-conditional-value' => 'latlng',								
						),							
					),
					array(
						'id' => 'polygon_path_ids',
						'name' => 'Polygon Paths (Post IDs)', 
						'desc' => 'Select the posts that will be used/connected to form the polygon. You should add at least 3 posts. 
								   <br /><span style="color:red;">Type an empty space to list all available posts! You can drag & drop the posts in the list to change their order!</span>
								   <br /><span style="color:red"><strong>Note:</strong> The polygon points/vertices order is defined by the order of the posts in the list!</span>',
						'type' => 'post_ajax_search',
						'multiple-item' => true,					
						'limit' => -1, 
						'sortable' => true,
						'query_args' => array(
							'post_type' => array( $this->selected_cpt ),
							'posts_per_page' => -1
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'polygon_points_type' ) ),
							'data-conditional-value' => 'post_ids',								
						),		
						'after_row' => $this->toggle_after_row,				
					),																										
					
					/**
					 * Polygon options */
					
					array(
						'id' => 'polygon_visibility',
						'name' => 'Visibility',
						'desc' => 'Whether this polygon is visible on the map. Defaults to "Yes".',
						'type' => 'radio_inline',
						'default' => 'true',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						),
						'before_row' => str_replace(array('[title]'), array('Polygon options'), $this->toggle_before_row),
					),
					array(
						'id' => 'polygon_clickable',
						'name' => 'Clickable',
						'desc' => 'Indicate whether this Polygon handles mouse events. Defaults to "No".',
						'type' => 'radio_inline',
						'default' => 'false',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						),						
					),
					array(
						'id' => 'polygon_fitBounds',
						'name' => 'Change the viewport on polygon click?',
						'desc' => 'Change the map viewport to contain the polygon bounds when clicking inside the of it. Defaults to "No".<br />
						 		   <span style="color:red;">Works only when "Clickable" is set to "Yes"!</span>',
						'type' => 'radio_inline',
						'default' => 'no',
						'options' => array(
							'yes' => 'Yes',
							'no' => 'No'
						),						
					),
					array(
						'id' => 'polygon_url',
						'name' => 'Polygon redirect URL',
						'desc' => 'If provided, the URL will be executed when the Polygon is clicked. <span style="color:red;">Works only when "Clickable" is set to "Yes"!</span>',
						'type' => 'text_url',
						'default' => ''
					),
					array(
						'id' => 'polygon_url_target',
						'name' => 'URL target',
						'desc' => 'Choose an option to open the Polygon redirect URL. Defaults to "Open in a new window".',
						'type' => 'radio',
						'default' => 'new_window',
						'options' => array(
							'new_window' => 'Open in a new window',
							'same_window' => 'Open in the same window',
                            'popup' => 'Open inside a modal/popup',
						)
					),
					array(
						'id' => 'polygon_description',
						'name' => 'Polygon description',
						'desc' => 'Enter the message text or the description to display inside an infowindow when the Polygon is hovered over. The infowindow will be removed once the mouse leaves the Polygon!
								   <span style="color:red;">Works only when "Clickable" is set to "Yes"!</span>
								   <br /><span style="color:red;"><strong>Note:</strong> Shortcodes are not allowed!</span>',
						'type' => 'wysiwyg',
						'default' => '',
					),
					array(
						'id' => 'polygon_infowindow_maxwidth',
						'name' => 'Infowindow Maximum width',
						'desc' => 'Maximum width (in pixels) of the infowindow, regardless of the Polygon description\'s width. Defaults to "200px".',
						'type' => 'text',
						'default' => '250',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '0'
						),				
					),
					array(
						'id' => 'polygon_zIndex',
						'name' => 'zIndex',
						'desc' => 'The zIndex compared to other polygons.',
						'type' => 'text',
						'default' => '',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
						),				
					),
					array(
						'id' => 'polygon_connected_post',
						'name' => 'Polygon connected post', 
						'desc' => 'Enter the post ID. By connecting this polygon to a specific post, the polygon visibility will depend on 
									that post visibility on the map.',
						'type' => 'text',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
						),
						'after_row' => $this->toggle_after_row,												
					),
					
					/**
					 * Polygon style */
					 
					array(
						'id' => 'polygon_style_desc',
						'name' => '',
						'desc' => 'The style applied to the polygon when displaying it on the map.',
						'type' => 'title',						
						'before_row' => str_replace(array('[title]'), array('Polygon style'), $this->toggle_before_row),						
					),
					array(
						'id' => 'polygon_fillColor',
						'name' => 'Fill color',
						'desc' => 'The fill color. Defaults to "#189AC9".',
						'type' => 'colorpicker',
						'default' => '#189AC9',						
					),		
					array(
						'id' => 'polygon_fillOpacity',
						'name' => 'Fill opacity',
						'desc' => 'The fill opacity between 0.0 and 1. Defaults to "1".',
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
					),				
					array(
						'id' => 'polygon_geodesic',
						'name' => 'Geodesic',
						'desc' => 'When "Yes", edges of the polygon are interpreted as geodesic and will follow the curvature of the Earth. When "No", edges of the polygon are rendered as straight lines in screen space. Defaults to "No".',
						'type' => 'radio',
						'default' => 'false',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						)
					),
					array(
						'id' => 'polygon_strokeColor',
						'name' => 'Stroke color',
						'desc' => 'The stroke color. Defaults to "#189AC9".',
						'type' => 'colorpicker',
						'default' => '#189AC9',
					),		
					array(
						'id' => 'polygon_strokeOpacity',
						'name' => 'Stroke opacity',
						'desc' => 'The stroke opacity between 0.0 and 1. Defaults to "1".',
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
					),	
					array(
						'id' => 'polygon_strokeWeight',
						'name' => 'Stroke weight',
						'desc' => 'The stroke width in pixels. Defaults to "2".',
						'type' => 'text',
						'default' => '2',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '0'
						),				
					),
					array(
						'id' => 'polygon_strokeType',
						'name' => 'Stroke type',
						'desc' => 'The stroke type. Defaults to "Simple line".',
						'type' => 'radio',
						'default' => 'simple',
						'options' => array(
							'simple' => 'Simple line',
							'dashed' => 'Dashed line',
						),						
					),	
					array(
						'id' => 'polygon_icons_repeat',
						'name' => 'Distance between dashes',
						'desc' => 'The distance between consecutive dashes in pixels. Defaults to "15px".',
						'type' => 'text',
						'default' => '10',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '1',
							'data-conditional-id' => wp_json_encode( array( $group_id, 'polygon_strokeType' ) ),
							'data-conditional-value' => 'dashed',								
						),				
					),
					array(
						'id' => 'polygon_strokePosition',
						'name' => 'Stroke Position',
						'desc' => 'The stroke position. Defaults to "CENTER".<br />
								  <strong>1. Center:</strong> The stroke is centered on the polygon\'s path, with half the stroke inside the polygon and half the stroke outside the polygon.<br />
								  <strong>2. Inside:</strong> The stroke lies inside the polygon.<br />
								  <strong>3. Outside:</strong> The stroke lies outside the polygon.',
						'type' => 'radio',
						'default' => 'CENTER',
						'options' => array(
							'CENTER' => 'Center',
							'INSIDE' => 'Inside',
							'OUTSIDE' => 'Outside',
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'polygon_strokeType' ) ),
							'data-conditional-value' => 'simple',								
						),
						'after_row' => $this->toggle_after_row,
					),
					
					/**
					 * Polygon hover style
					 * @since 5.6 */
					 
					array(
						'id' => 'polygon_hover_style_desc',
						'name' => '',
						'desc' => 'The style applied to the polygon on mouse enter/hover.',
						'type' => 'title',						
						'before_row' => str_replace(array('[title]'), array('Polygon hover style'), $this->toggle_before_row),
					),
					array(
						'id' => 'polygon_hover_style',
						'name' => 'Change the polygon style on hover',
						'desc' => 'Choose whether to change the polygon style on mouse enter/hover. Defaults to "No".<br />
								   <span style="color:red;"><u>Note:</u> To change the polygon style, open the group field "Polygon options" above and select the option <u>"Clickable => Yes"</u>!</span>',
						'type' => 'radio_inline',
						'default' => 'false',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						),
					),
					array(
						'id' => 'polygon_hover_fillColor',
						'name' => 'Fill color',
						'desc' => 'The fill color. Defaults to "#189AC9".',
						'type' => 'colorpicker',
						'default' => '#189AC9',
					),		
					array(
						'id' => 'polygon_hover_fillOpacity',
						'name' => 'Fill opacity',
						'desc' => 'The fill opacity between 0.0 and 1. Defaults to "1".',
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
					),
					array(
						'id' => 'polygon_hover_strokeColor',
						'name' => 'Stroke color',
						'desc' => 'The stroke color. Defaults to "#189AC9".',
						'type' => 'colorpicker',
						'default' => '#189AC9',
					),		
					array(
						'id' => 'polygon_hover_strokeOpacity',
						'name' => 'Stroke opacity',
						'desc' => 'The stroke opacity between 0.0 and 1. Defaults to "1".',
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
					),	
					array(
						'id' => 'polygon_hover_strokeWeight',
						'name' => 'Stroke weight',
						'desc' => 'The stroke width in pixels. Defaults to "2".',
						'type' => 'text',
						'default' => '2',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '0'
						),				
					),
					array(
						'id' => 'polygon_hover_strokeType',
						'name' => 'Stroke type',
						'desc' => 'The stroke type. Defaults to "Simple line".',
						'type' => 'radio',
						'default' => 'simple',
						'options' => array(
							'simple' => 'Simple line',
							'dashed' => 'Dashed line',
						),						
					),	
					array(
						'id' => 'polygon_hover_icons_repeat',
						'name' => 'Distance between dashes',
						'desc' => 'The distance between consecutive dashes in pixels. Defaults to "15px".',
						'type' => 'text',
						'default' => '10',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '1',
							'data-conditional-id' => wp_json_encode( array( $group_id, 'polygon_hover_strokeType' ) ),
							'data-conditional-value' => 'dashed',								
						),				
					),
					array(
						'id' => 'polygon_hover_strokePosition',
						'name' => 'Stroke Position',
						'desc' => 'The stroke position. Defaults to "CENTER".<br />
								  <strong>1. Center:</strong> The stroke is centered on the polygon\'s path, with half the stroke inside the polygon and half the stroke outside the polygon.<br />
								  <strong>2. Inside:</strong> The stroke lies inside the polygon.<br />
								  <strong>3. Outside:</strong> The stroke lies outside the polygon.',
						'type' => 'radio',
						'default' => 'CENTER',
						'options' => array(
							'CENTER' => 'Center',
							'INSIDE' => 'Inside',
							'OUTSIDE' => 'Outside',
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'polygon_hover_strokeType' ) ),
							'data-conditional-value' => 'simple',								
						),
						'after_row' => $this->toggle_after_row,
					)					
				)
			);
			
			return $fields;
			
		}
		
		/**
		 * Map Holes Settings Fields 
		 *
		 * @since 5.3
		 */
		function cspm_map_holes_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_map_holes_section',
				'name' => 'Map Holes',
				'desc' => 'Map Holes is a feature that allows to mask the whole map and highlight only specific areas of interest. 
						   These areas are called "holes" and they can represent a city boundaries, a continent, a street, etc... 
						   Be creative!',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
					
			$fields[] = array(
				'id' => $this->metafield_prefix . '_map_holes_option',
				'name' => 'Map Holes option',
				'desc' => 'Select "Yes" to enable this option in your map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
					
			/*$fields[] = array(
				'id' => $this->metafield_prefix . '_map_holes_union',
				'name' => 'Map Holes unification',
				'desc' => 'Choose whether the map will combine all intersected holes or not. Intersected holes are all holes that  
						   pass or lie across each other. <br />
						   Select "Yes" to enable this option in your map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);*/
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_map_holes_style',
				'name' => '<span style="font-size:15px; color:#ff6600; font-weight:600; width: 200px; display: inline-block;">Map fill color & Holes style</span>',
				'desc' => 'Set the map fill/mask color and the holes stroke/border style.',
				'type' => 'group',
				'repeatable'  => false,
				'options'     => array(
					'group_title'   => esc_attr__( 'Map fill color & Holes style', 'cspm' ),
					'sortable'      => false,
					'closed'     => true,
				),
				'fields' => array(	
					array(
						'id' => 'map_fillColor',
						'name' => 'Map fill color',
						'desc' => 'The map fill/background color. Defaults to "#189AC9".',
						'type' => 'colorpicker',
						'default' => '#000',
					),		
					array(
						'id' => 'map_fillOpacity',
						'name' => 'Map fill opacity',
						'desc' => 'The map fill/background opacity between 0.0 and 1. Defaults to "1".',
						'type' => 'select',
						'default' => '0,5',
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
					),
					array(
						'id' => 'holes_strokeColor',
						'name' => 'Holes stroke color',
						'desc' => 'The holes stroke color. Defaults to "#189AC9".',
						'type' => 'colorpicker',
						'default' => '#000',
					),		
					array(
						'id' => 'holes_strokeOpacity',
						'name' => 'Holes stroke opacity',
						'desc' => 'The holes stroke opacity between 0.0 and 1. Defaults to "1".',
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
					),	
					array(
						'id' => 'holes_strokeWeight',
						'name' => 'Holes stroke weight',
						'desc' => 'The holes stroke width in pixels. Defaults to "2".',
						'type' => 'text',
						'default' => '1',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '0'
						),				
					),
					array(
						'id' => 'holes_strokePosition',
						'name' => 'Holes stroke Position',
						'desc' => 'The holes stroke position. Defaults to "CENTER".<br />
								  <strong>1. Center:</strong> The stroke is centered on the hole\'s path, with half the stroke inside the hole and half the stroke outside the hole.<br />
								  <strong>2. Inside:</strong> The stroke lies inside the hole.<br />
								  <strong>3. Outside:</strong> The stroke lies outside the hole.',
						'type' => 'radio',
						'default' => 'CENTER',
						'options' => array(
							'CENTER' => 'Center',
							'INSIDE' => 'Inside',
							'OUTSIDE' => 'Outside',
						),
					)				
				)
			);
			
			$group_id = $this->metafield_prefix . '_map_holes';

			$fields[] = array(
				'id' => $group_id,
				'name' => '<span style="font-size:15px; color:#ff6600; font-weight:600;">Map Holes</span>',
				'desc' => 'Click on the button "Add New Hole" to add a new hole. You can add Multiple holes!',
				'type' => 'group',
				'repeatable'  => true,
				'options'     => array(
					'group_title'   => esc_attr__( 'Hole {#}', 'cspm' ),
					'add_button'    => esc_attr__( 'Add New Hole', 'cspm' ),
					'remove_button' => esc_attr__( 'Remove Hole', 'cspm' ),
					'sortable'      => true,
					'closed'     => true,
				),
				'fields' => array(	
					array(
						'id' => 'hole_visibility',
						'name' => 'Hole visibility',
						'desc' => 'Whether this hole is visible on the map. Defaults to "Yes".',
						'type' => 'radio_inline',
						'default' => 'true',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						),
					),
					array(
						'id' => 'hole_label',
						'name' => 'Hole Label', 
						'desc' => 'Give a label to this Hole. The Label will help to distinct a hole between multiple Holes. (Example: "Lodon Boundaries Hole")',
						'type' => 'text',
						'default' => '',
						'attributes'  => array(
							//'required'    => 'required',
							//'data-validation' => 'required',
							'data-group-title' => 'text'
						)
					),
					array(
						'id' => 'hole_name',
						'name' => 'Hole ID/Name', 
						'desc' => 'Give a unique ID/Name to this Hole. <span style="color:red">If two holes has the same IDs/Names, the last added hole will override the old hole.</span> (Example: "london_boundaries_hole")',
						'type' => 'text',
						'default' => '',
					),										
					array(
						'id' => 'hole_points_type',
						'name' => 'Hole points type',
						'desc' => 'Select the hole points type. Defaults to "Lat,Lng".<br />
								   <span style="color:red;">You can build a hole either by adding multiple Lat,Lng coordinates or by connecting 
								   multiple posts already available on your map.</span>',
						'type' => 'radio',
						'default' => 'latlng',
						'options' => array(
							'latlng' => 'Lat,Lng',
							'post_ids' => 'Post IDs'
						)
					),
					array(
						'name' => '',
						'desc' => '<strong style="font-size:15px; display: inline-block; padding: 5px 0;">How to draw a hole?</strong><br />
								  Before drawing a hole, you need to pan the map to the area where you want to draw. 
								  You can use the address search field or geolocate your position for that purpose!<br />
								  <strong>1.</strong> Click (if not already selected) on the hole button/icon on the map (next to the hand button/icon).<br />
								  <strong>2.</strong> Draw the hole by clicking on specific spots on the map. To close the hole, double click on 
								  the last spot or click on an already created point.<br />
								  <strong>3.</strong> Once finished, you can edit the hole by dragging a vertex/point to another spot. Double click on a point/vertex to remove it. 
								  To drag the hole, first, check the option <u>"Activate dragging mode"</u> on top of the map, then start dragging it.<br />
								  <strong>4.</strong> If you need to start drawing from scratch, click on the button <u>"Draw from scratch"</u>, then, start 
								  drawing a new hole.<br />
								  <strong>5.</strong> Once satisfied with the hole, click on the button <u>"Display hole coordinates"</u>.<br />
								  <strong>Hint:</strong> For more precision while drawing the hole, use the map\'s full screen mode!
								  </span>',								  
						'id' => 'hole_path',
						'type' => 'cs_gmaps_drawing',
						'default' => '',
						'options' => array(
							'api_key' => isset($this->plugin_settings['api_key']) ? $this->plugin_settings['api_key'] : '',
							'disable_gmaps_api' => false,
							'disable_gmap3_plugin' => false,
							'map_height' => '200px',
							'map_width' => '100%',
							'map_center' => $this->cspm_get_field_default('map_center', '51.53096,-0.121064'),
							'map_zoom' => 12,
							'labels' => array(
								'address' => 'Enter a location & search or Geolocate your position',						
								'coordinates' => 'Hole coordinates',
								'search' => 'Search',
								'pinpoint' => 'Display hole coordinates',
								'clear_overlay' => 'Draw from scratch',
								'toggle' => 'Hole Path [Lat,Lng]',
								'top_desc' => 'Draw a hole. Or, in case you already have the hole coordinates, simply paste them in the field/textarea below by putting each line segment (LatLng) as <strong>[Lat,Lng]</strong> or <strong>[Lng,Lat]</strong> separated by comma.<br />
											  <strong>Example: </strong><code>[45.5215,-1.5245],[41.2587,-1.2479],[40.1649,-1.9879]</code><br />
											  <span style="color:red;">Read the instructions below to see how to draw a hole.</span><hr />',								
							),
							'draw_mode' => 'polygon',
							'save_coordinates' => true,
							'toggle' => false,
							'close' => false,
							'latLng_order' => 'latLng', // Get coordinates order				
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'hole_points_type' ) ),
							'data-conditional-value' => 'latlng',								
						),
						'before_row' => str_replace(array('[title]'), array('Hole Path'), $this->toggle_before_row),
					),
					array(
						'id' => 'hole_latlng_order',
						'name' => 'LatLngs order', 
						'desc' => 'Select the syntaxe you\'ve followed to enter the coordinates of this Hole.',
						'type' => 'radio',
						'default' => 'latLng',
						'options' => array(
							'latLng' => 'Lat,Lng',
							'lngLat' => 'Lng,Lat',
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'hole_points_type' ) ),
							'data-conditional-value' => 'latlng',								
						),							
					),
					array(
						'id' => 'hole_path_ids',
						'name' => 'Hole Path (Post IDs)', 
						'desc' => 'Select the posts that will be used/connected to form the hole. You should add at least 3 posts. 
								   <br /><span style="color:red;">Type an empty space to list all available posts! You can drag & drop the posts in the list to change their order!</span>
								   <br /><span style="color:red"><strong>Note:</strong> The hole points/vertices order is defined by the order of the posts in the list!</span>',
						'type' => 'post_ajax_search',
						'multiple-item' => true,					
						'limit' => -1, 
						'sortable' => true,
						'query_args' => array(
							'post_type' => array( $this->selected_cpt ),
							'posts_per_page' => -1
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'hole_points_type' ) ),
							'data-conditional-value' => 'post_ids',								
						),
						'after_row' => $this->toggle_after_row,						
					),																										
				)
			);
			
			return $fields;
			
		}
		
		
		/**
		 * Carousel Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_carousel_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Carousel Settings',
				'desc' => 'Control carousel mode, movement & animation.<br />
						  <span style="color:red;"><strong><u>Known issue:</u></strong> Using the carousel to display a large amount of locations (500+) may slow down your page loading. 
						  The map in the other hand can handle a very large amount of locations (10000+) with ease. <br />
						  <strong><u>Solution:</u></strong> The alternative solution in this case is to use the extension <a href="https://codecanyon.net/item/progress-map-list-filter-wordpress-plugin/16134134?ref=codespacing" target="_blank" class="cspm_blank_link">List & Filter"</a>. 
						  This extension will switch the carousel to a list and will end the slow loading issues.</span>',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_carousel_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_show_carousel',
				'name' => 'Show carousel',
				'desc' => 'Show/Hide the map\'s carousel.',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_sync_carousel_to_viewport',
				'name' => 'Syncronize the carousel with the viewport of the map',
				'desc' => 'By default, all locations on the map will be listed in the carousel. <br />
						  The following option will allow you to add and remove items from the carousel when the viewport of the map changes. <br />
						  For example, if the viewport of the map contains 10 locations, only these 10 locations will be added to the map. When no location available on the viewport, the 
						  carousel will be empty. <br />Default to "Fill the carousel with all available locations on the map"<br />
						  <span style="color: red;"><strong>Hint:</strong> When filling the carousel based on the viewport of the map, we recommend you to detach the carousel and the map for a better user experience!</span>',
				'type' => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => 'Fill the carousel based on the viewport of the map',
					'no' => 'Fill the carousel with all available locations on the map'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_connect_carousel_with_map',
				'name' => 'Connect the carousel to the map',
				'desc' => 'Connect the carousel to the map so that when you scroll the carousel or you click on a carousel item, the map center point changes to the 
						   position of the highlighted item in the carousel.',
				'type' => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => 'Connect the carousel and the map',
					'no' => 'Detach the carousel and the map'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_move_carousel_on',
				'name' => 'Scroll carousel ...',
				'desc' => 'From the map, you can scroll the carousel to find a specific item. Select from the following options when to scroll the carousel.',
				'type' => 'multicheck',
				'default' => array('marker_click', 'marker_hover', 'infobox_hover'),
				'options' => array(
					'marker_click' => 'On marker click',
					'marker_hover' => 'On marker hover',
					'infobox_hover' => 'On infobox Hover'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_carousel_map_zoom',
				'name' => 'Map zoom',
				'desc' => 'When you click on an item in the carousel, the center point of the map will be changed to the position 
						  of that item. Select the map zoom level to apply when clicking on a carousel item. Defaults to "12".',
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
				'id' => $this->metafield_prefix . '_carousel_mode',
				'name' => 'Mode',
				'desc' => 'Select whether the carousel appears in RTL mode or LTR mode. Defaults to "Left-to-right"',
				'type' => 'select',
				'default' => 'false',
				'options' => array(
					'true' => 'Right-to-left',
					'false' => 'Left-to-right'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_carousel_scroll',
				'name' => 'Scroll',
				'desc' => 'The number of items to scroll by. Defaults to "1"',
				'type' => 'text',
				'default' => '1',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '1'
				),				
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_carousel_easing',
				'name' => 'Easing',
				'desc' => 'The easing effect when scrolling carousel items. Defaults to "linear". <a href="http://easings.net/" target="_blank" class="cspm_blank_link">(Easing Examples)</a>',
				'type' => 'select',
				'default' => 'linear',
				'options' => array(
					'linear' => 'linear',
					'swing' => 'swing',
					'easeInQuad' => 'easeInQuad',
					'easeOutQuad' => 'easeOutQuad',
					'easeInOutQuad' => 'easeInOutQuad',
					'easeInCubic' => 'easeInCubic',
					'easeOutCubic' => 'easeOutCubic',
					'easeInOutCubic' => 'easeInOutCubic',
					'easeInQuart' => 'easeInQuart',
					'easeOutQuart' => 'easeOutQuart',
					'easeInOutQuart' => 'easeInOutQuart',
					'easeInQuint' => 'easeInQuint',
					'easeOutQuint' => 'easeOutQuint',
					'easeInOutQuint' => 'easeInOutQuint',
					'easeInExpo' => 'easeInExpo',
					'easeOutExpo' => 'easeOutExpo',
					'easeInOutExpo' => 'easeInOutExpo',
					'easeInSine' => 'easeInSine',
					'easeOutSine' => 'easeOutSine',
					'easeInOutSine' => 'easeInOutSine',
					'easeInCirc' => 'easeInCirc',
					'easeOutCirc' => 'easeOutCirc',
					'easeInOutCirc' => 'easeInOutCirc',
					'easeInElastic' => 'easeInElastic',
					'easeOutElastic' => 'easeOutElastic',
					'easeInOutElastic' => 'easeInOutElastic',
					'easeInBack' => 'easeInBack',
					'easeOutBack' => 'easeOutBack',
					'easeInOutBack' => 'easeInOutBack',
					'easeInBounce' => 'easeInBounce',
					'easeOutBounce' => 'easeOutBounce',
					'easeInOutBounce' => 'easeInOutBounce',
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_carousel_animation',
				'name' => 'Animation',
				'desc' => 'The speed of the scroll animation. Defaults to "fast".',
				'type' => 'select',
				'default' => 'fast',
				'options' => array(
					'slow' => 'slow',
					'fast' => 'Fast'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_carousel_auto',
				'name' => 'Auto',
				'desc' => 'Specify how many seconds to periodically autoscroll the content. If set to 0 (default) then autoscrolling is turned off.',
				'type' => 'text',
				'default' => '0',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '0'
				),				
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_carousel_wrap',
				'name' => 'Wrap',
				'desc' => 'Specify whether to wrap at the first/last item (or both) and jump back to the start/end. If set to null, wrapping is turned off. Defaults to "Circular".',
				'type' => 'select',
				'default' => 'circular',
				'options' => array(
					'first' => 'First',
					'last' => 'Last',
					'both' => 'Both',
					'circular' => 'Circular',
					'null' => 'Null'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_scrollwheel_carousel',
				'name' => 'Scroll wheel',
				'desc' => 'Move the carousel with scroll wheel. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_touchswipe_carousel',
				'name' => 'Touch swipe',
				'desc' => 'Move the carousel with touch swipe. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
					
			return $fields;
			
		}
		
		
		/**
		 * Carousel Style Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_carousel_style_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Carousel Style Settings',
				'desc' => 'Customize the carousel.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_carousel_style_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_carousel_css',
				'name' => 'Carousel CSS',
				'desc' => 'Add your custom CSS to customize the carousel style.<br /><strong>e.g.</strong> background-color:#ededed; border:1px solid; ...',
				'type' => 'textarea',
				'default' => ''
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_arrows_background',
				'name' => 'Arrows background color',
				'desc' => 'Change the default background color of the arrows.',
				'type' => 'colorpicker',
				'default' => '#fff'
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_horizontal_left_arrow_icon',
				'name' => 'Horizontal left arrow image',
				'desc' => 'Upload a new left arrow image. You can always find the original arrow in the plugin\'s images directory.',
				'type' => 'file',
				'default' => ''
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_horizontal_right_arrow_icon',
				'name' => 'Horizontal right arrow image',
				'desc' => 'Upload a new right arrow image. You can always find the original arrow in the plugin\'s images directory.',
				'type' => 'file',
				'default' => ''
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_vertical_top_arrow_icon',
				'name' => 'Vertical top arrow image',
				'desc' => 'Upload a new top arrow image. You can always find the original arrow in the plugin\'s images directory.',
				'type' => 'file',
				'default' => ''
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_vertical_bottom_arrow_icon',
				'name' => 'Vertical bottom arrow image',
				'desc' => 'Upload a new bottom arrow image. You can always find the original arrow in the plugin\'s images directory.',
				'type' => 'file',
				'default' => ''
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_items_background',
				'name' => 'Carousel items background color',
				'desc' => 'Change the default background color of the carousel items.',
				'type' => 'colorpicker',
				'default' => '#fff'
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_items_hover_background',
				'name' => 'Active carousel items background color',
				'desc' => 'Change the default background color of the carousel items when one of them is selected.',
				'type' => 'colorpicker',
				'default' => '#fbfbfb'
			);
					
			return $fields;
			
		}
		
		
		/**
		 * Carousel Items Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_carousel_items_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Carousel Items Settings',
				'desc' => 'Customize the carousel items style & content.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_carousel_items_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_items_view',
				'name' => 'Items view',
				'desc' => 'Select the view of the carousel items. Defaults to "Horizontal".',
				'type' => 'radio_image',
				'default' => 'listview',
				'options' => array(
					'listview' => 'Horizontal',
					'gridview' => 'Vertical',
				),
				'images_path' => $this->plugin_url,
				'images' => array(
					'listview' => 'admin/img/radio-imgs/horizontal.jpg',
					'gridview' => 'admin/img/radio-imgs/vertical.jpg',				
				)	
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_items_featured_img',
				'name' => 'Items image',
				'desc' => 'Choose whether to hide or show the items image. Defaults to "Show".',
				'type' => 'radio',
				'default' => 'show',
				'options' => array(
					'show' => 'Show',
					'hide' => 'Hide',
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_horizontal_item_section',
				'name' => 'Horizontal view',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_horizontal_item_size',
					'name' => 'Items size <sup>(Horizontal view)</sup>',
					'desc' => 'Enter the size (in pixels) of the carousel items. This field is related to the items of the horizontal view. (Width then height separated by comma. Default: 454,150)',
					'type' => 'text',
					'default' => '454,150',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_horizontal_item_css',
					'name' => 'Items CSS <sup>(Horizontal view)</sup>',
					'desc' => 'Enter your custom CSS of the carousel items. This field is related to the items of the horizontal view.<br /><strong>e.g.</strong> background-color:#ededed; border:1px solid; ...',
					'type' => 'textarea',
					'default' => '',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_horizontal_image_size',
					'name' => 'Image size <sup>(Horizontal view)</sup>',
					'desc' => 'Enter the image size (in pixels) of the carousel items. This field is related to the items of the horizontal view. (Width then height separated by comma. Default: 204,150)<br />
					<strong style="color:red;">Please note that after you change the size of the image, you\'ll have to regenerate your image attachments in order to create new images that matches the new size! Use <a href="https://wordpress.org/plugins-wp/regenerate-thumbnails/" target="_blank" class="cspm_blank_link">this plugin</a> to regenerate your images. </strong>',
					'type' => 'text',
					'default' => '204,150',
					'attributes' => array(
						'data-conditional-id' => $this->metafield_prefix . '_items_featured_img',
						'data-conditional-value' => wp_json_encode(array('show')),								
					),										
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_horizontal_details_size',
					'name' => 'Description area size <sup>(Horizontal view)</sup>',
					'desc' => 'Enter the size (in pixels) of the items description area. This field is related to the items of the horizontal view. (Width then height separated by comma. Default: 250,150)',
					'type' => 'text',
					'default' => '250,150',
					'attributes' => array(
						'data-conditional-id' => $this->metafield_prefix . '_items_featured_img',
						'data-conditional-value' => wp_json_encode(array('show')),								
					),															
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_horizontal_title_css',
					'name' => 'Title CSS <sup>(Horizontal view)</sup>',
					'desc' => 'Customize the items title area and text by entring your CSS. This field is related to the items of the horizontal view.
							   <br /><strong>e.g.</strong> background-color:#ededed; border:1px solid; ...',
					'type' => 'textarea',
					'default' => '',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_horizontal_details_css',
					'name' => 'Description CSS <sup>(Horizontal view)</sup>',
					'desc' => 'Customize the items description area and text by entring your CSS. This field is related to the items of the horizontal view.
							   <br /><strong>e.g.</strong> background-color:#ededed; border:1px solid; ...',
					'type' => 'textarea',
					'default' => '',
				);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_vertical_item_section',
				'name' => 'Vertical view',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_vertical_item_size',
					'name' => 'Items size <sup>(Vertical view)</sup>',
					'desc' => 'Enter the size (in pixels) of the carousel items. This field is related to the items of the vertical view. (Width then height separated by comma. Default: 204,290)',
					'type' => 'text',
					'default' => '204,290',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_vertical_item_css',
					'name' => 'Items CSS <sup>(Vertical view)</sup>',
					'desc' => 'Enter your custom CSS of the carousel items. This field is related to the items of the vertical view.
							   <br /><strong>e.g.</strong> background-color:#ededed; border:1px solid; ...',
					'type' => 'textarea',
					'default' => '',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_vertical_image_size',
					'name' => 'Image size <sup>(Vertical view)</sup>',
					'desc' => 'Enter the image size (in pixels) of the carousel items. This field is related to the items of the vertical view. (Width then height separated by comma. Default: 204,120)<br />
					<strong style="color:red;">Please note that after you change the size of the image, you\'ll have to regenerate your image attachments in order to create new images that matches the new size! Use <a href="https://wordpress.org/plugins-wp/regenerate-thumbnails/" target="_blank" class="cspm_blank_link">this plugin</a> to regenerate your images. </strong>',					
					'type' => 'text',
					'default' => '204,120',
					'attributes' => array(
						'data-conditional-id' => $this->metafield_prefix . '_items_featured_img',
						'data-conditional-value' => wp_json_encode(array('show')),								
					),															
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_vertical_details_size',
					'name' => 'Description area size <sup>(Vertical view)</sup>',
					'desc' => 'Enter the size (in pixels) of the items description area. This field is related to the items of the vertical view. (Width then height separated by comma. Default: 204,170)',
					'type' => 'text',
					'default' => '204,170',
					'attributes' => array(
						'data-conditional-id' => $this->metafield_prefix . '_items_featured_img',
						'data-conditional-value' => wp_json_encode(array('show')),								
					),															
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_vertical_title_css',
					'name' => 'Title CSS <sup>(Vertical view)</sup>',
					'desc' => 'Customize the items title area and text by entring your CSS. This field is related to the items of the vertical view.
							   <br /><strong>e.g.</strong> background-color:#ededed; border:1px solid; ...',
					'type' => 'textarea',
					'default' => '',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_vertical_details_css',
					'name' => 'Description CSS <sup>(Vertical view)</sup>',
					'desc' => 'Customize the items description area and text by entring your CSS. This field is related to the items of the vertical view.
							   <br /><strong>e.g.</strong> background-color:#ededed; border:1px solid; ...',
					'type' => 'textarea',
					'default' => '',
				);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_more_item_section',
				'name' => 'Content settings',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_show_details_btn',
					'name' => '"More" button',
					'desc' => 'Show/Hide "More" button',
					'type' => 'radio',
					'default' => 'yes',
					'options' => array(
						'yes' => 'Show',
						'no' => 'Hide',
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_details_btn_text',
					'name' => '"More" Button text',
					'desc' => 'Enter your custom text to show on the "More" Button.',
					'type' => 'text',
					'default' => 'More',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_details_btn_css',
					'name' => '"More" Button CSS',
					'desc' => 'Enter your CSS to customize the "More" Button\'s look.<br /><strong>e.g.</strong> background-color:#ededed; border:1px solid; ...',
					'type' => 'textarea',
					'default' => '',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_items_title',
					'name' => 'Items title',
					'desc' => 'Create your custom items title by entering the name of your custom fields. You can use as many you want. Leave this field empty to use the default title.
							<br /><strong>Syntax:</strong> <code>[meta_key<sup>1</sup>][separator<sup>1</sup>][meta_key<sup>2</sup>][separator<sup>2</sup>][meta_key<sup>n</sup>]...[title length]</code>
							<br /><strong>Example of use:</strong> <code>[post_category][s=,][post_address][l=50]</code>
							<br /><strong>*</strong> To insert empty an space enter <code>[-]</code>
							<br /><strong>* Make sure there\'s no empty spaces between <code>][</code></strong>
							<br /><a href="http://www.docs.progress-map.com/cspm_guide/carousel-items-settings/build-a-custom-title-from-custom-fields-for-the-carousel-items/" target="_blank" class="cspm_blank_link" style="color:red">Check this post for more details!</a>',
					'type' => 'textarea',
					'default' => '',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_click_on_title',
					'name' => 'Title as a link?',
					'desc' => 'Select "Yes" to make the title as a link to the post page.',
					'type' => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => 'Yes',
						'no' => 'No',
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_external_link',
					'name' => 'Post URL',
					'desc' => 'Choose an option to open the post URL. Defaults to "Open in the same window".',
					'type' => 'radio',
					'default' => 'same_window',
					'options' => array(
						'new_window' => 'Open in a new window',
						'same_window' => 'Open in the same window',
						'popup' => 'Open inside a modal/popup',
						'nearby_places' => 'Open the "Nearby places" map inside a modal/popup',
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_items_details',
					'name' => 'Items description',
					'desc' => 'Create your custom description content. You can combine the content with your custom fields & taxonomies. Leave this field empty to use the default description.
							<br /><strong>Syntax:</strong> <code>[content;content_length][separator][t=label:][meta_key][separator][t=Category:][tax=taxonomy_slug][separator]...[description length]</code>
							<br /><strong>Example of use:</strong> <code>[content;80][s=br][t=Category:][-][tax=category][s=br][t=Address:][-][post_address]</code>
							<br /><strong>*</strong> To specify a description length, use <code>[l=LENGTH]</code>. Change LENGTH to a number (e.g. 100).
							<br /><strong>*</strong> To add a label, use <code>[t=YOUR_LABEL]</code>
							<br /><strong>*</strong> To add a custom field, use <code>[CUSTOM_FIELD_NAME]</code>				
							<br /><strong>*</strong> To insert a taxonomy, use <code>[tax=TAXONOMY_SLUG]</code>
							<br /><strong>*</strong> To insert new line enter <code>[s=br]</code>
							<br /><strong>*</strong> To insert an empty space enter <code>[-]</code>
							<br /><strong>*</strong> To insert the content/excerpt, use <code>[content;LENGTH]</code>. Change LENGTH to a number (e.g. 100).
							<br /><strong>* Make sure there\'s no empty spaces between <code>][</code></strong>							
							<br /><a href="http://www.docs.progress-map.com/cspm_guide/carousel-items-settings/build-a-custom-content-from-custom-fields-andor-categories-for-the-carousel-items/" target="_blank" class="cspm_blank_link" style="color:red">Check this post for more details!</a>',
					'type' => 'textarea',
					'default' => '[l=100]',
				);	
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_ellipses',
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
		 * Posts Count Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_posts_count_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Posts Count Settings',
				'desc' => 'Show the number of posts on the map. Use the settings below to change the default label & style.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_posts_count_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_show_posts_count',
				'name' => 'Show posts count',
				'desc' => 'Show/Hide the posts count clause',
				'type' => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => 'Show',
					'no' => 'Hide',
				)
			);	
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_posts_count_clause',
				'name' => 'Posts count label',
				'desc' => 'Enter your custom label.<br /><strong>Syntaxe:</strong> <code>LABEL [posts_count] LABEL</code>',
				'type' => 'text',
				'default' => '[posts_count] Posts',
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_posts_count_color',
				'name' => 'Label color',
				'desc' => 'Choose the color of the label.',
				'type' => 'colorpicker',
				'default' => '#333333',
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_posts_count_style',
				'name' => 'Label style',
				'desc' => 'Add your CSS code to customize the label style.<br /><strong>e.g.</strong> background-color:#ededed; border:1px solid; ...',
				'type' => 'textarea',
				'default' => '',
			);
								
			return $fields;
			
		}
		
		
		/**
		 * Faceted Search Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_faceted_search_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Faceted Search Settings',
				'desc' => 'Faceted search, also called faceted navigation or faceted browsing, is a technique for accessing information organized according to a faceted classification system, allowing users to explore a collection of information by applying multiple filters. A faceted classification system classifies each information element along multiple explicit dimensions, enabling the classifications to be accessed and ordered in multiple ways rather than in a single, pre-determined, taxonomic order.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_faceted_search_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_alert_msg',
				'name' => '<strong>IMPORTANT</strong>!<br />The faceted search cannot be operated without activating the <strong>"Marker categories option"</strong> in <strong>"Marker categories settings"</strong>',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:16px; color:#fff; background:#000; text-align:center; padding:15px; font-weight:200;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_option',
				'name' => 'Faceted search option',
				'desc' => 'Select "Yes" to enable this option in your map. Defaults to "No".
						  <br /><span style="color:red">The faceted search cannot be operated without activating the <strong>"Marker categories option"</strong> in <strong>"Marker categories settings"</strong>!</span>',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_display_status',
				'name' => 'Faceted search display status',
				'desc' => 'Choose whether to open or close the faceted search on map load. Defaults to "Close".',
				'type' => 'radio',
				'default' => 'close',
				'options' => array(
					'open' => 'Open',
					'close' => 'Close'
				)
			);
			
			/**
			 * [@post_type_taxonomy_options] : Takes the list of all taxonomies related to the post type selected in "Query settings" */
			 
			$post_type_taxonomy_options	= $this->cspm_get_post_type_taxonomies($this->selected_cpt);		
				unset($post_type_taxonomy_options['post_format']);
				
			reset($post_type_taxonomy_options); // Set the cursor to 0
			
			foreach($post_type_taxonomy_options as $cpt_taxonomy_slug => $cpt_taxonomy_title){
	
				$tax_name = $cpt_taxonomy_slug;
				$tax_label = $cpt_taxonomy_title;
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_faceted_search_taxonomy_'.$tax_name,				
					'name' => $tax_label,
					'desc' => 'Select the terms to use in the faceted search.',
					'type' => 'pw_multiselect',
					'options' => $this->cspm_get_term_options($tax_name),				
					'attributes' => array(
						'placeholder' => 'Select Term(s)',
						'data-conditional-id' => $this->metafield_prefix . '_marker_categories_taxonomy',
						'data-conditional-value' => wp_json_encode(array($this->metafield_prefix . '_marker_categories_taxonomy', $tax_name)),
					),
				);
				
			}
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_autocheck',
				'name' => 'Check/Select term(s) on map load?',
				'desc' => 'This will allow you to check/select one our multiple terms by default on map load. Select "Yes" to enable this option in your map. Defaults to "No".
						   <span style="color:red;">When set to <strong>"Yes"</strong>, the option <strong>"Faceted search display status"</strong> will be ignored and the filter will be opened by default!</span>',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			foreach($post_type_taxonomy_options as $cpt_taxonomy_slug => $cpt_taxonomy_title){
	
				$tax_name = $cpt_taxonomy_slug;
				$tax_label = $cpt_taxonomy_title;
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_faceted_search_autocheck_taxonomy_'.$tax_name,				
					'name' => 'Select the term(s) to check/select on map load',
					'desc' => 'Select the term(s) to check/select on map load. <span style="color:red;">When you select multiple terms and the option <strong>"Multiple terms option"</strong> is set to <strong>"No"</strong>, only the last term in this field will be checked/selected by default!</span>',
					'type' => 'pw_multiselect',
					'options' => $this->cspm_get_term_options($tax_name),				
					'attributes' => array(
						'placeholder' => 'Select Term(s)',
						'data-conditional-id' => $this->metafield_prefix . '_marker_categories_taxonomy',
						'data-conditional-value' => wp_json_encode(array($this->metafield_prefix . '_marker_categories_taxonomy', $tax_name)),
					),
				);
				
			}
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_multi_taxonomy_option',
				'name' => 'Multiple terms option', 
				'desc' => 'Select "Yes" if you want to filter the posts/locations by selecting multiple terms in the faceted search form.',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No',
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_drag_map',
				'name' => 'Drag the map', 
				'desc' => 'Choose whether you want to drag the map to the nearest zone containing the markers or to simply autofit the map (After a filter action). Defaults to "Autofit".',
				'type' => 'radio',
				'default' => 'autofit',
				'options' => array(
					'drag' => 'Drag the map to the position of the first post/location in the results list',
					'autofit' => 'Autofit the map to contain all posts/locations in the results list',
					'nothing' => 'Do nothing',
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_customizing_section',
				'name' => 'Customization',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_input_skin',
				'name' => 'Checkbox/Radio skin', 
				'desc' => 'Select the skin of the checkbox/radio input. <a target="_blank" class="cspm_blank_link" href="http://icheck.fronteed.com/">See all skins</a>',
				'type' => 'radio_image',
				'default' => 'polaris',
				'options' => array(
					'minimal' => 'Minimal skin',
					'square' => 'Square skin',
					'flat' => 'Flat skin',
					'line' => 'Line skin',
					'polaris' => 'Polaris skin',
					'futurico' => 'Futurico skin',
				),
				'images_path'      => $this->plugin_url,
				'images'           => array(
					'minimal' => 'admin/img/radio-imgs/minimal.jpg',
					'square' => 'admin/img/radio-imgs/square.jpg',				
					'flat' => 'admin/img/radio-imgs/flat.jpg',
					'line' => 'admin/img/radio-imgs/line.jpg',
					'polaris' => 'admin/img/radio-imgs/polaris.jpg',
					'futurico' => 'admin/img/radio-imgs/futurico.jpg',
				)				
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_input_color',
				'name' => 'Checkbox/Radio skin color', 
				'desc' => 'Select the skin color of the checkbox/radio input. (Polaris & Futurico skins doesn\'t use colors). <a target="_blank" class="cspm_blank_link" href="http://icheck.fronteed.com/">See all colors</a>',
				'type' => 'radio',
				'default' => 'blue',
				'options' => array(
					'black' => 'Black',
					'red' => 'Red',
					'green' => 'Green',
					'blue' => 'Blue',
					'aero' => 'Aero',
					'grey' => 'Grey',
					'orange' => 'Orange',
					'yellow' => 'Yellow',
					'pink' => 'Pink',
					'purple' => 'Purple',
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_css',
				'name' => 'Category list background color',
				'desc' => 'Change the background color of the faceted search form container.',
				'type' => 'colorpicker',
				'default' => '#ffffff',
			);						
			
			return $fields;
			
		}
		
		
		/**
		 * Search Form Settings Fields 
		 *
		 * @since 1.0 
		 */
		function cspm_search_form_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Search Form Settings',
				'desc' => 'The search form is a technique that allow users to provide their address and to find locations on the map within a chosen distance restriction.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_search_form_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_search_form_option',
				'name' => 'Search form option',
				'desc' => 'Select "Yes" to enable this option in your map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_sf_display_status',
				'name' => 'Search form display status',
				'desc' => 'Choose whether to open or close the search form on map load. Defaults to "Close".',
				'type' => 'radio',
				'default' => 'close',
				'options' => array(
					'open' => 'Open',
					'close' => 'Close'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_sf_min_search_distances',
				'name' => 'Min distances of search',
				'desc' => 'Enter the minimum distance to use as a distance search between the origin address and the destinations in the map.',
				'type' => 'text',
				'default' => '3',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '1',
				),								
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_sf_max_search_distances',
				'name' => 'Max distances of search',
				'desc' => 'Enter the maximum distance to use as a distance search between the origin address and the destinations in the map.',
				'type' => 'text',
				'default' => '50',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '1',
				),				
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_sf_distance_unit',
				'name' => 'Distance unit',
				'desc' => 'Select the distance unit.',
				'type' => 'radio',
				'default' => 'metric',
				'options' => array(
					'metric' => 'Km',
					'imperial' => 'Miles'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_sf_geoIpControl',
				'name' => 'Allow Geo-targeting',
				'desc' => 'Allow Geo-targeting on the search form. The Geo-targeting is the method of determining the geolocation of a website visitor.',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
					
			$fields[] = array(
				'id' => $this->metafield_prefix . '_form_customization_section',
				'name' => 'Search form customization',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_address_placeholder',
					'name' => 'Address field placeholder',
					'desc' => 'Edit the text to show as a placeholder of the address field',
					'type' => 'text',
					'default' => 'Enter City & Province, or Postal code',
				);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_slider_label',
					'name' => 'Slider label',
					'desc' => 'Edit the text to show as a label of the slider',
					'type' => 'text',
					'default' => 'Expand the search area up to',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_submit_text',
					'name' => 'Submit button text',
					'desc' => 'Edit the text to show in the submit button.',
					'type' => 'text',
					'default' => 'Search',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_search_form_bg_color',
					'name' => 'Background color',
					'desc' => 'Change the background color of the search form container.',
					'type' => 'colorpicker',
					'default' => '#ffffff',
				);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_warning_msg_section',
				'name' => 'Warning messages',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_no_location_msg',
					'name' => 'No locations message',
					'desc' => 'Edit the text to show when the search form has no locations to display.',
					'type' => 'text',
					'default' => 'We could not find any location',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_bad_address_msg',
					'name' => 'Bad address message',
					'desc' => 'Edit the text to show when the search form don\'t understand the provided address.',
					'type' => 'text',
					'default' => 'We could not understand the location',
				);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_bad_address_sug_1',
					'name' => '"Bad address" first suggestion',
					'desc' => 'Edit the text to show as a first suggestion for the bad address message.',
					'type' => 'text',
					'default' => '- Make sure all street and city names are spelled correctly.',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_bad_address_sug_2',
					'name' => '"Bad address" Second suggestion',
					'desc' => 'Edit the text to show as a second suggestion for the bad address message.',
					'type' => 'text',
					'default' => '- Make sure your address includes a city and state.',
				);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_bad_address_sug_3',
					'name' => '"Bad address" Third suggestion',
					'desc' => 'Edit the text to show as a third suggestion for the bad address message.',
					'type' => 'text',
					'default' => '- Try entering a zip code.',
				);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_circle_customization_section',
				'name' => 'Circle customization',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_circle_option',
					'name' => 'Circle option',
					'desc' => 'The circle option is a technique of drawing a circle of a given radius of the search address. Select "Yes" to enable this option. Defaults to "Yes".',
					'type' => 'radio',
					'default' => 'true',
					'options' => array(
						'true' => 'Yes',
						'false' => 'No'
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_edit_circle',
					'name' => 'Resize the circle',
					'desc' => 'Resizing the circle will allow users to increase and/or decrease the search distance in order to get more or less results. Defaults to "Yes".',
					'type' => 'radio',
					'default' => 'true',
					'options' => array(
						'true' => 'Yes',
						'false' => 'No'
					)
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_fillColor',
					'name' => 'Fill color',
					'desc' => 'The fill color.',
					'type' => 'colorpicker',
					'default' => '#189AC9',
				);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_fillOpacity',
					'name' => 'Fill opacity',
					'desc' => 'The fill opacity between 0.0 and 1.0.',
					'type' => 'select',
					'default' => '0,1',
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
					'id' => $this->metafield_prefix . '_sf_strokeColor',
					'name' => 'Stroke color',
					'desc' => 'The stroke color.',
					'type' => 'colorpicker',
					'default' => '#189AC9',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_strokeOpacity',
					'name' => 'Stroke opacity',
					'desc' => 'The stroke opacity between 0.0 and 1.',
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
					'id' => $this->metafield_prefix . '_sf_strokeWeight',
					'name' => 'Stroke weight',
					'desc' => 'The stroke width in pixels.',
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
		 * Zoom to country Settings Fields 
		 *
		 * @since 3.0 
		 */
		function cspm_zoom_to_country_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Zooming to Countries Settings',
				'desc' => 'This feature will add a countries dropdown list to the map in order to allow users to quickly move between countries.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_custom_css_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_zoom_country_option',
				'name' => 'Zooming to countries option',
				'desc' => 'Select "Yes" to enable this option in your map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_zoom_country_display_status',
				'name' => 'Countries list display status',
				'desc' => 'Choose whether to open or close the countries list on map load. Defaults to "Close".
						  <span style="color:red;">This option will be ignored when you set the option <strong>"Faceted search settings => Check/Select term(s) on map load"</strong> to <strong>"Yes"</strong></span>',
				'type' => 'radio',
				'default' => 'close',
				'options' => array(
					'open' => 'Open',
					'close' => 'Close'
				)
			);
					
			$fields[] = array(
				'id' => $this->metafield_prefix . '_country_zoom_or_autofit',
				'name' => 'Country bounds',
				'desc' => 'Select whether to zoom the map to the country center point or to show the whole country when moving between countries. Defaults to "Show whole country".',
				'type' => 'radio',
				'default' => 'autofit',
				'options' => array(
					'autofit' => 'Show whole country',
					'zoom' => 'Zooming to country center'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_country_zoom_level',
				'name' => 'Map zoom',
				'desc' => 'Select the map zoom to use when moving to the country center point. Defaults to "12". <span style="color:red;">This option works only with <strong>"Country bounds => Zooming to country center"</strong></span>',
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
				'id' => $this->metafield_prefix . '_countries_list_section',
				'name' => 'Dropdown List Parameters',
				'desc' => 'Customize the countries dropdown list.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_country_list_placeholder',
				'name' => 'Dropdown list placeholder',
				'desc' => 'Edit the text to show as a placeholder of the countries list',
				'type' => 'text',
				'default' => 'Select a country',
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_country_search_input',
				'name' => 'Search input field',
				'desc' => 'Select "Yes" to add a search input field within the dropdown list. The search field allows to find a 
						   specific country in the list. Defaults to "Yes". <br />
						   <span style="clear:both;">This option is not operated when selecting the option <u>"Country flag => Yes & Hide the country name"</u>!</span>',
				'type' => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_country_display_language',
				'name' => 'Display language',
				'desc' => 'In the dropdown list that will be displayed on the map, select in which language to display countries names.<br />
						  <span style="color:red;">Not all languages are available! If a language is not available, English will be used instead!</span>',
				'type' => 'select',
				'default' => 'en',
				'options_cb' => array($this, 'cspm_get_world_languages'),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_show_country_flag',
				'name' => 'Country flag',
				'desc' => 'Show the country flag. Defaults to "Yes".',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'only' => 'Yes & Hide the country name',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'name' => 'Countries',
				'desc' => 'Select the countries to display.<br /><br />
						  <span style="color:red;"><strong>You want to sort the countries?</strong> Change this field from "Multicheck" to "Multiselect & Sort" by adding the below PHP code in your theme\'s "functions.php" file.<br /><br />
						  <strong>Notes:</strong><br />
						  1. The "Multiselect & Sort" field is not user friendly when you want to select all countries because it doesn\'t offer an option 
						  to select them all by a simple click!<br />
						  2. The "Multiselect & Sort" field has been tested with all countries selected and it turns out that it slows down the page while sorting countries!<br /><br />
						  <strong>PHP Code:</strong><br />
						  </span>
<pre style="font-size:11px !important;">
/**
 * Progress Map Wordpress Plugin.
 * Change the countries field type to "Multiselect & Sort".
 *
 * @since 3.0
 */
add_filter("cspm_countries_field_type", function(){ return true; });
</pre>',
				'type' => $this->cspm_countries_field_type(),
				'id'   => $this->metafield_prefix . '_countries',
				//'options_cb' => array($this, 'cspm_get_countries'),
				'options' => $this->countries_list,
				'attributes' => array(
					'placeholder' => 'Select country(ies)'
				),
			);
			
			return $fields;
			
		}
		
		
		function cspm_nearby_places_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Nearby points of interest settings',
				'desc' => 'This feature will allow users to select a location on the map and to display all nearby points of interests (POI). All POI are provide by the service Google Places!',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_nearby_places_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_nearby_places_option',
				'name' => 'Nearby points of interest option',
				'desc' => 'Select "Yes" to enable this option in your map. Defaults to "No".',
				'type' => 'radio',
				'default' => 'false',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_np_proximities_display_status',
				'name' => 'Proximities list display status',
				'desc' => 'Choose whether to open or close the proximities list on map load. Defaults to "Close".</span>',
				'type' => 'radio',
				'default' => 'close',
				'options' => array(
					'open' => 'Open',
					'close' => 'Close'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_np_list_placeholder',
				'name' => 'Proximities list placeholder',
				'desc' => 'Edit the text to show as a placeholder of the proximities list',
				'type' => 'text',
				'default' => 'Select a place type',
			);
								
			$fields[] = array(
				'id' => $this->metafield_prefix . '_np_distance_unit',				
				'name' => esc_attr__('Unit System', 'cspm'),
				'type' => 'radio',
				'desc' => esc_attr__('Choose the unit system to use when displaying & calculating the distance. Defaults to "Metric (Km)".', 'cspm'),
				'options' => array(
					'METRIC' => 'Metric (Km)', 
					'IMPERIAL' => 'Imperial (Miles)', 
				 ), 
				'default' => 'METRIC'
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_np_radius',				
				'name' => esc_attr__('Initial Radius', 'cspm'),
				'type' => 'text', 
				'desc' => esc_attr__('Choose the initial distance from the given location within which to search for Places, in meters. The minimum allowed value is 5000 meters (which equals to "5 Km" or "3,106856 Miles"). Defaults to 5000', 'cspm'),
				'default' => '5000',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '50',
					'max' => '50000',
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_np_min_radius',				
				'name' => esc_attr__('Minimum Radius', 'cspm'),
				'type' => 'text', 
				'desc' => esc_attr__('Choose the minimum distance from the given location within which to search for Places, in meters. Defaults to 1000', 'cspm'),
				'default' => '1000',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '50',
					'max' => '50000',
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_np_max_radius',				
				'name' => esc_attr__('Maximum Radius', 'cspm'),
				'type' => 'text', 
				'desc' => esc_attr__('Choose the maximum distance from the given location within which to search for Places, in meters. The maximum allowed value is 50000 meters (which equals to "50 Km" or "31.06856 Miles"). Defaults to 50000', 'cspm'),
				'default' => '50000',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '50',
					'max' => '50000',
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_np_circle_option',
				'name' => 'Circle option',
				'desc' => 'Draw a circle around the search area. Defaults to "Yes".',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_np_edit_circle',
				'name' => 'Resize the circle',
				'desc' => 'Resizing the circle will allow users to increase and/or decrease the search distance in order to get more or less results. Defaults to "Yes".',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'false' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_np_marker_type',
				'name' => 'Markers icon',
				'desc' => 'Choose the markers icons to use for the points of interest. Defaults to "Custome icons".',
				'type' => 'radio',
				'default' => 'custom',
				'options' => array(
					'default' => 'Default icon',
					'custom' => 'Custome icons'
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_show_proximity_icon',
				'name' => 'Proximity icon',
				'desc' => 'Show proximity icon in the list. Defaults to "Yes".',
				'type' => 'radio',
				'default' => 'true',
				'options' => array(
					'true' => 'Yes',
					'only' => 'Yes & Hide proximity name',
					'false' => 'No'
				)
			);
						
			$fields[] = array(
				'id' => $this->metafield_prefix . '_np_proximities',				
				'name' => esc_attr__('Place Types', 'cspm'),
				'type' => 'pw_multiselect',
				'desc' => esc_attr__('Select the place types to use & change their display order.', 'cspm'),
				'options' => array(
					'accounting' => 'Accounting',
					'airport' => 'Airport',
					'amusement_park' => 'Amusement park',
					'aquarium' => 'Aquarium',
					'art_gallery' => 'Art gallery',
					'atm' => 'ATM',
					'bakery' => 'Bakery',
					'bank' => 'Bank',
					'bar' => 'Bar',
					'beauty_salon' => 'Beauty salon',
					'bicycle_store' => 'Bicycle store',
					'book_store' => 'Book store',
					'bowling_alley' => 'Bowling alley',
					'bus_station' => 'Bus station',
					'cafe' => 'Cafe',
					'campground' => 'Campground',
					'car_dealer' => 'Car dealer',
					'car_rental' => 'Car rental',
					'car_repair' => 'Car repair',
					'car_wash' => 'Car wash',
					'casino' => 'Casino',
					'cemetery' => 'Cemetery',
					'church' => 'Church',
					'city_hall' => 'City hall',
					'clothing_store' => 'Clothing store',
					'convenience_store' => 'Convenience store',
					'courthouse' => 'Courthouse',
					'dentist' => 'Dentist',
					'department_store' => 'Department store',
					'doctor' => 'Doctor',
					'electrician' => 'Electrician',
					'electronics_store' => 'Electronics store',
					'embassy' => 'Embassy',
					//'establishment' => 'Establishment',
					//'finance' => 'Finance',
					'fire_station' => 'Fire station',
					'florist' => 'Florist',
					//'food' => 'Food',
					'funeral_home' => 'Funeral home',
					'furniture_store' => 'Furniture store',
					'gas_station' => 'Gas station',
					//'general_contractor' => 'General contractor',
					//'grocery_or_supermarket' => 'Grocery or supermarket',
					'gym' => 'GYM',
					'hair_care' => 'Hair care',
					'hardware_store' => 'Hardware store',
					//'health' => 'Health',
					'hindu_temple' => 'Hindu temple',
					'home_goods_store' => 'Home goods store',
					'hospital' => 'Hospital',
					'insurance_agency' => 'Insurance agency',
					'jewelry_store' => 'Jewelry store',
					'laundry' => 'Laundry',
					'lawyer' => 'Lawyer',
					'library' => 'Library',
					'liquor_store' => 'Liquor store',
					'local_government_office' => 'Local government office',
					'locksmith' => 'Locksmith',
					'lodging' => 'Lodging',
					'meal_delivery' => 'Meal delivery',
					'meal_takeaway' => 'Meal takeaway',
					'mosque' => 'Mosque',
					'movie_rental' => 'Movie rental',
					'movie_theater' => 'Movie theater',
					'moving_company' => 'Moving company',
					'museum' => 'Museum',
					'night_club' => 'Night club',
					'painter' => 'Painter',
					'park' => 'Park',
					'parking' => 'Parking',
					'pet_store' => 'Pet store',
					'pharmacy' => 'Pharmacy',
					'physiotherapist' => 'Physiotherapist',
					//'place_of_worship' => 'Place of worship',
					'plumber' => 'Plumber',
					'police' => 'Police',
					'post_office' => 'Post office',
					'real_estate_agency' => 'Real estate agency',
					'restaurant' => 'Restaurant',
					'roofing_contractor' => 'Roofing contractor',
					'rv_park' => 'RV park',
					'school' => 'School',
					'shoe_store' => 'Shoe store',
					'shopping_mall' => 'Shopping mall',
					'spa' => 'Spa',
					'stadium' => 'Stadium',
					'storage' => 'Storage',
					'store' => 'Store',
					'subway_station' => 'Subway station',
					'synagogue' => 'Synagogue',
					'taxi_stand' => 'Taxi stand',
					'train_station' => 'Train station',
					'travel_agency' => 'Travel agency',
					'university' => 'University',
					'veterinary_care' => 'Veterinary care',
					'zoo' => 'Zoo',
				),
				'default' => array(
					'accounting',
					'airport',
					'amusement_park',
					'aquarium',
					'art_gallery',
					'atm',
					'bakery',
					'bank',
					'bar',
					'beauty_salon',
					'bicycle_store',
					'book_store',
					'bowling_alley',
					'bus_station',
					'cafe',
					'campground',
					'car_dealer',
					'car_rental',
					'car_repair',
					'car_wash',
					'casino',
					'cemetery',
					'church',
					'city_hall',
					'clothing_store',
					'convenience_store',
					'courthouse',
					'dentist',
					'department_store',
					'doctor',
					'electrician',
					'electronics_store',
					'embassy',
					//'establishment',
					//'finance',
					'fire_station',
					'florist',
					//'food',
					'funeral_home',
					'furniture_store',
					'gas_station',
					//'general_contractor',
					//'grocery_or_supermarket',
					'gym',
					'hair_care',
					'hardware_store',
					//'health',
					'hindu_temple',
					'home_goods_store',
					'hospital',
					'insurance_agency',
					'jewelry_store',
					'laundry',
					'lawyer',
					'library',
					'liquor_store',
					'local_government_office',
					'locksmith',
					'lodging',
					'meal_delivery',
					'meal_takeaway',
					'mosque',
					'movie_rental',
					'movie_theater',
					'moving_company',
					'museum',
					'night_club',
					'painter',
					'park',
					'parking',
					'pet_store',
					'pharmacy',
					'physiotherapist',
					//'place_of_worship',
					'plumber',
					'police',
					'post_office',
					'real_estate_agency',
					'restaurant',
					'roofing_contractor',
					'rv_park',
					'school',
					'shoe_store',
					'shopping_mall',
					'spa',
					'stadium',
					'storage',
					'store',
					'subway_station',
					'synagogue',
					'taxi_stand',
					'train_station',
					'travel_agency',
					'university',
					'veterinary_care',
					'zoo',
				),				
				'attributes' => array(
					'placeholder' => 'Select the points of interest',
				),
				'select_all_button' => true
			);

			return $fields;
				
		}
		
		/**
		 * Autocomplete settings
		 *
		 * @since 4.0
		 */
		function cspm_autocomplete_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Autocomplete Settings',
				'desc' => 'The Autocomplete is a feature that gives your map the type-ahead-search behavior of the Google Maps search field. When a user starts typing an address, autocomplete will fill in the rest.',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_autocomplete_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_autocomplete_option',
				'name' => 'Use Autocomplete for address fields?',
				'desc' => 'Select "Yes" to enable this option in your map. Defaults to "Yes".',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('autocomplete_option', 'yes'),
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_autocomplete_strict_bounds',
				'name' => 'Restrict the search to the bounds?',
				'desc' => 'This option restricts the autocomplete search to the area within the current viewport. If this option is enabled, the GMaps API biases the search to the current viewport, but does not restrict it. 
				           Select "Yes" to enable this option in your map. Defaults to "No".',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('autocomplete_strict_bounds', 'no'),
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No'
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_autocomplete_country_restrict',
				'name' => 'Restrict the search to specific countries?',
				'desc' => 'This option restricts the autocomplete search to particular countries. 
				           Select "Yes" to enable this option in your map, then, select your contry(ies). Defaults to "No".',
				'type' => 'radio',
				'default' => $this->cspm_get_field_default('autocomplete_country_restrict', 'no'),
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No'
				)
			);
			
			$fields[] = array(
				'name' => 'Countries',
				'desc' => 'Select the countries to be used with the autocomplete search.',
				'type' => 'pw_multiselect',
				'id' => $this->metafield_prefix . '_autocomplete_countries',
				'options' => $this->countries_list,
				'attributes' => array(
					'data-conditional-id' => $this->metafield_prefix . '_autocomplete_country_restrict',
					'data-conditional-value' => wp_json_encode(array('yes')),								
				),
			);
			
			return $fields;
				
		}
		
		function cspm_customize_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Customize',
				'desc' => '',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_custom_css_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			/**
			 * Elements Positions */
			 	
			$fields[] = array(
				'id' => $this->metafield_prefix . '_map_elements_section',
				'name' => 'Map elements',
				'desc' => 'Change the display order of the icons/elements that appears on the map.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
				
				$countries_btn_icon = apply_filters('cspm_countries_btn_icon', $this->plugin_url.'img/svg/continents.svg', $this->object_id);
				$search_form_icon = apply_filters('cspm_search_form_icon', $this->plugin_url.'img/svg/loup.svg', $this->object_id);
				$faceted_search_icon = apply_filters('cspm_faceted_search_icon', $this->plugin_url.'img/svg/filter.svg', $this->object_id);
				$proximities_icon = apply_filters('cspm_proximities_icon', $this->plugin_url.'img/svg/proximities.svg', $this->object_id);
				$kml_list_icon = apply_filters('cspm_kml_list_icon', $this->plugin_url.'img/svg/kml_list.svg', $this->object_id);
												
				$fields[] = array(
					'id' => $this->metafield_prefix . '_map_horizontal_elements_order',
					'name' => esc_attr__( 'Map horizontal elements display order', 'cspm' ),
					'desc' => esc_attr__( 'Change the display order of the elements displayed on the map', 'cspm' ),				
					'type' => 'order',
					'inline' => true,
					'options' => apply_filters('cspm_map_horizontal_elements_order', array(
						'zoom_country' => '<img src="'.$countries_btn_icon.'" style="height:20px;" alt="'.esc_attr__('Zooming to country', 'cspm').'" title="'.esc_attr__('Zooming to country', 'cspm').'" />',
						'search_form' => '<img src="'.$search_form_icon.'" style="height:20px;" alt="'.esc_attr__('Map search form', 'cspm').'" title="'.esc_attr__('Map search form', 'cspm').'" />',
						'faceted_search' => '<img src="'.$faceted_search_icon.'" style="height:20px;" alt="'.esc_attr__('Faceted search form', 'cspm').'" title="'.esc_attr__('Faceted search form', 'cspm').'" />',
						'proximities' => '<img src="'.$proximities_icon.'" style="height:20px;" alt="'.esc_attr__('Nearby points of interest', 'cspm').'" title="'.esc_attr__('Nearby points of interest', 'cspm').'" />',
						'kml_layers' => '<img src="'.$kml_list_icon.'" style="height:20px;" alt="'.esc_attr__('KML Layers', 'cspm').'" title="'.esc_attr__('KML Layers', 'cspm').'" />',
					)),
				);
							
				$recenter_btn_img = apply_filters('cspm_recenter_map_btn_img', $this->plugin_url.'img/svg/recenter.svg', $this->object_id);
				$geo_btn_img = apply_filters('cspm_geo_btn_img', $this->plugin_url.'img/svg/geoloc.svg', $this->object_id);
				$heatmap_btn_img = apply_filters('cspm_geo_btn_img', $this->plugin_url.'img/svg/heatmap.svg', $this->object_id);
								
				$fields[] = array(
					'id' => $this->metafield_prefix . '_map_vertical_elements_order',
					'name' => esc_attr__( 'Map vertical elements display order', 'cspm' ),
					'desc' => esc_attr__( 'Change the display order of the elements displayed on the map', 'cspm' ),				
					'type' => 'order',
					'inline' => false,
					'options' => apply_filters('cspm_map_vertical_elements_order', array(
						'recenter_map' => '<img src="'.$recenter_btn_img.'" style="height:20px;" alt="'.esc_attr__('Recenter map', 'cspm').'" title="'.esc_attr__('Recenter map', 'cspm').'" />',
						'geo' => '<img src="'.$geo_btn_img.'" style="height:20px;" alt="'.esc_attr__('Geo targeting', 'cspm').'" title="'.esc_attr__('Geo targeting', 'cspm').'" />',
						'heatmap' => '<img src="'.$heatmap_btn_img.'" style="height:20px;" alt="'.esc_attr__('Heatmap', 'cspm').'" title="'.esc_attr__('Heatmap', 'cspm').'" />',
					)),
				);
				
			/**
			 * Icons */
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_map_icons_section',
				'name' => 'Icons',
				'desc' => 'Change the colored icons that appears on the map. Icons used in this plugin are available on <a href="https://www.flaticon.com/" target="_blank" class="cspm_blank_link">Flaticon</a>',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_zoom_in_icon',
					'name' => 'Zoom-in icon',
					'desc' => 'Appears inside the button that allows to zoom-in the map. The original image can be found <a href="https://www.flaticon.com/free-icon/addition-sign_2664" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => '',
				);
					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_zoom_out_icon',
					'name' => 'Zoom-out icon',
					'desc' => 'Appears inside the button that allows to zoom-out the map. The original image can be found <a href="https://www.flaticon.com/free-icon/calculation-operations-minus-sign_42977" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => '',
				);
					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_target_icon',
					'name' => 'Geotarget icon',
					'desc' => 'Appears inside the button that allows to geotarget the user location. The original image can be found <a href="https://www.flaticon.com/free-icon/center-object_73887" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => '',
				);
					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_recenter_icon',
					'name' => 'Recenter icon',
					'desc' => 'Appears inside the button that allows to recenter the map. The original image can be found <a href="https://www.flaticon.com/free-icon/exit-full-screen_1126" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => '',
				);
					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_heatmap_icon',
					'name' => 'Heatmap icon',
					'desc' => 'Appears inside the button that allows to display the heatmap layer. The original image can be found <a href="https://www.flaticon.com/free-icon/burn_59512" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => '',
				);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_faceted_search_icon',
					'name' => 'Faceted search button icon',
					'desc' => 'Appears inside the button that allows to open the faceted search form. The original image can be found <a href="https://www.flaticon.com/free-icon/remove-location_106168" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => ''
				);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_sf_search_form_icon',
					'name' => 'Search form button icon',
					'desc' => 'Appears inside the button that allows to open the search form. The original image can be found <a href="https://www.flaticon.com/free-icon/zoom-or-search-interface-symbol_45707" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => ''
				);
			
				$fields[] = array(
					'id' => $this->metafield_prefix . '_countries_btn_icon',
					'name' => 'Countries button icon',
					'desc' => 'Appears inside the button that allows to open the countries list. The original image can be found <a href="https://www.flaticon.com/free-icon/world-map-with-a-placeholder_52573" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => ''
				);
					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_nearby_icon',
					'name' => 'Nearby places icon',
					'desc' => 'Appears inside the button that allows to display the list of nearby places/proximities list. The original image can be found <a href="https://www.flaticon.com/free-icon/locations_154141" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => '',
				);
				
				$fields[] = array(
					'id' => $this->metafield_prefix . '_kml_list_icon',
					'name' => 'KML Layers list button icon',
					'desc' => 'Appears inside the button that allows to open the KML Layers list. The original image can be found <a href="https://www.flaticon.com/free-icon/layer_320165" target="_blank" class="cspm_blank_link">here</a>.',
					'type' => 'file',
					'default' => ''
				);
					
				$fields[] = array(
					'id' => $this->metafield_prefix . '_multicolor_svg',
					'name' => 'Use multicolor SVG icons?',
					'desc' => 'By default, if you change the main color of the plugin, SVG icons will be affected and their color will be changed, though,  you will not be able to use multicolor SVG icons. 
							   To allow using multicolor SVG icons, select the option "Yes".',
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
				'id' => $this->metafield_prefix . '_additional_css_section',
				'name' => 'Additional CSS',
				'desc' => 'Add you custom CSS code to customize the map.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
				
						
				$fields[] = array(
					'id' => $this->metafield_prefix . '_custom_css',
					'name' => 'Custom CSS code',
					'desc' => 'Add your own CSS here',
					'type' => 'textarea',
					'default' => '',
				);
			
			return $fields;
			
		}
		
		
		/**
		 * Get all registred post types & remove "Progress Map's" CPT from the list
		 *
		 * @since 1.0
		 * @Deprecated 1.2 (@since "PM" 3.5)
		 */
		function cspm_get_registred_cpts(){
				
			$wp_post_types_array = array(
				'post' => esc_attr__('Posts').' (post)', 
				'page' => esc_attr__('Pages').' (page)'
			);

			$all_custom_post_types = get_post_types(array('_builtin' => false), 'objects');
			
			$return_post_types_array = array();
			
			/**
			 * First we'll get the post types selected in the plugin settings.
			 * If not found, then, we'll get all registred post types */
			
			$default_post_types = $this->cspm_get_field_default('post_types', array());

			if(is_array($default_post_types) && count($default_post_types) > 0){
				
				/** 
				 * Loop through default WP post types */
				 
				foreach($wp_post_types_array as $wp_post_type_name => $wp_post_type_label){
					
					if(in_array($wp_post_type_name, $default_post_types)){						
					
						$return_post_types_array[$wp_post_type_name] = $wp_post_type_label;
					
					}
					
				}
				
				/** 
				 * Loop through all custom post types */
				 
				foreach($all_custom_post_types as $post_type){
					
					if(in_array($post_type->name, $default_post_types)){						
					
						if($post_type->name != $this->object_type)
							$return_post_types_array[$post_type->name] = $post_type->labels->name.' ('.$post_type->name.')';
					
					}
					
				}
				
			}else{
				
				$post_types_array = $wp_post_types_array;
				
				foreach($all_custom_post_types as $post_type){
					
					if($post_type->name != $this->object_type)
						$return_post_types_array[$post_type->name] = $post_type->labels->name.' ('.$post_type->name.')';
					
				}
				
			}
			
			return $return_post_types_array;
			
		}


		/**
		 * Get all selected post types in the plugin settings
		 *
		 * @since 1.1
		 */
		function cspm_get_selected_cpts(){
				
			$default_post_types_array = array(
				'post' => esc_attr__('Posts').' (post)', 
				'page' => esc_attr__('Pages').' (page)'
			);
			
			/**
			 * First we'll get the post types selected in the plugin settings.
			 * If not found, then, we'll get all registred post types */
			
			$selected_post_types = $this->cspm_get_field_default('post_types', array());
			
			$return_post_types_array = array();

			if(is_array($selected_post_types) && count($selected_post_types) > 0){
				
				/** 
				 * Loop through the selected post types */
				 
				foreach($selected_post_types as $post_type){
					
					$post_type_object = get_post_type_object($post_type);
					
					if(isset($post_type_object->labels->name)){						
					
						$return_post_types_array[$post_type] = $post_type_object->labels->name.' ('.$post_type.')';
					
					}
					
				}
				
			}else $return_post_types_array = $default_post_types_array;
			
			return $return_post_types_array;
			
		}
		
		
		/**
		 * Get the list of all Authors/Users registred in the site
		 *
		 * @since 1.0
		 */
		function cspm_get_all_users(){

			$blog_users = get_users(array('fields' => array('ID', 'user_nicename', 'user_email')));
			
			$authors_array = array();
			
			foreach($blog_users as $user)
				$authors_array[$user->ID] = $user->user_nicename.' ('.$user->user_email.')';
				
			return $authors_array;
			
		}
		
		
		/**		 
		 * Get all Taxonomies related to a given post type
		 * 
		 * Since 1.0
		 */
		function cspm_get_post_type_taxonomies($post_type){
			
			$taxonomies_fields = $taxonomy_options = array();
			
			$post_type_taxonomies = (array) get_object_taxonomies($post_type, 'objects');
			
			foreach($post_type_taxonomies as $single_taxonomy){
				
				$tax_name = $single_taxonomy->name;
				$tax_label = $single_taxonomy->labels->name;	

				$taxonomy_options[$tax_name] = $tax_label;
				
			}
			
			return $taxonomy_options;
				
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
		 * This will return a list of all world languages
		 *
		 * @since 3.0
		 */
		function cspm_get_world_languages(){
			
			if(file_exists($this->plugin_path . 'inc/cspm-world-languages.php')){

				$world_languages = include_once($this->plugin_path . 'inc/cspm-world-languages.php');
				
				return $world_languages;
				
			}else return;
			
		}
		
				
		/**
		 * This will return a list of all countries in English Language
		 *
		 * @since 3.0
		 */
		function cspm_get_countries(){
			
			if(file_exists($this->plugin_path . 'inc/countries/en/country.php')){

				$countries = include_once($this->plugin_path . 'inc/countries/en/country.php');
				
				return $countries;
				
			}else return;
			
		}

		
		/**
		 * Gets a number of terms and displays them as options
		 *
		 * @since 1.0
		 */
		function cspm_get_term_options($tax_name){

			$terms = get_terms($tax_name, "hide_empty=0");
			
			$term_options = array();
						
			if(count($terms) > 0){	
				
				foreach($terms as $term){			   											
					$term_options[$term->term_id] = $term->name;
				}
				
			}
						
			return $term_options;
			
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
		
		
		/**
		 * Change the countries field type from "multicheck" to "multiselect"
		 *
		 * @since 1.0
		 */
		function cspm_countries_field_type(){
			
			$field_type = apply_filters('cspm_countries_field_type', false);
			
			return $field_type == true ? 'pw_multiselect' : 'multicheck';
			
		}		

	}
	
}	
		
