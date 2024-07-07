<?php

/**
 * This class contains all the fields used in the widget "Progress Map List & Filter" 
 *
 * @version 1.0 
 */
 
if(!defined('ABSPATH')){
    exit; // Exit if accessed directly
}

if( !class_exists( 'CspmlMetabox' ) ){
	
	class CspmlMetabox{
		
		private $plugin_path;
		private $plugin_url;
				
		private static $_this;	
		
		/**
		 * [@object_type] The name of the post to which we'll add the metaboxes
		 * @since 1.0 */
		 
		public $object_type;
		
		public $plugin_settings = array();
		
		protected $metafield_prefix;
		
		public $selected_cpt;
		
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
			 * Get selected post type based on the post ID */
			 
			$post_id = 0;
			
			if(isset($_REQUEST['post'])){
				
				$post_id = $_REQUEST['post'];
			
			}elseif(isset($_REQUEST['post_ID'])){
				
				$post_id = $_REQUEST['post_ID'];
			}

			$this->selected_cpt = get_post_meta( $post_id, $this->metafield_prefix . '_post_type', true );
			
			$this->toggle_before_row = '<div class="postbox cmb-row cmb-grouping-organizer closed">
									 	<div class="cmbhandle" title="Click to toggle"><br></div>               
									 	<h3 class="cmb-group-organizer-title cmbhandle-title" style="padding: 11px 15px !important;">[title]</h3>
										<div class="inside">';
													
			$this->toggle_after_row = '</div></div>';

		}

		static function this() {
			
			return self::$_this;
		
		}

		
		/**
		 * "Progress Map List & Filter" Metabox.
		 * This metabox will contain all the settings needed for "Progress Map List & Filter"
		 *
		 * @since 1.0
		 */
		function cspml_list_and_filter_metabox(){
			
			$cspml_metabox_options = array(
				'id'            => $this->metafield_prefix . '_pmlf_metabox',
				'title'         => '<img src="'.$this->plugin_url.'/img/list-and-filter-icon.png" style="width:20px; margin:0 10px -3px 0;" />'.esc_attr__( 'List & Filter Settings', 'cspml' ),
				'object_types'  => array( $this->object_type ), // Post type
				'show_on_cb'   => function(){
					global $pagenow;
					return ($pagenow == 'post-new.php') ? false : true;
				},
				'show_names' => true, // Show field names on the left
			);
			
			/**
			 * Metabox */
			 
			$cspml_metabox = new_cmb2_box( $cspml_metabox_options );

			/** 
			 * Check if we'll use the extension "List & Filter" or not */
			 
			$cspml_metabox->add_field( array(
				'id'   => $this->metafield_prefix . '_list_ext',
				'name' => 'Activate/Deactivate',
				'desc' => 'Check this option if you want to activate the extension "List & Filter". This will replace the carousel of "Progress Map" with a list and a filter.',
				'type' => 'checkbox',
				'before_row' => '<div class="cspm_single_field">',
				'after_row' => '</div>',
			) );

			/**
			 * Display the settings */
			 
			$this->cspml_list_and_filter_settings_tabs($cspml_metabox, $cspml_metabox_options);
			
		}
		
		
		/**
		 * Buill all the tabs that contains "Progress Map List & Filter" settings
		 *
		 * @since 1.0
		 */
		function cspml_list_and_filter_settings_tabs($metabox_object, $metabox_options){

			/**
			 * setting tabs */
			 
			$tabs_setting = array(
				'args' => $metabox_options,
				'tabs' => array()
			);
				
				/**
				 * Tabs array */
				 
				$cspml_tabs = array(
					
					/**
				 	 * Layout Settings */
					 
					array(
						'id' => 'list_layout_settings', 
						'title' => 'Layout settings', 
						'callback' => 'cspml_layout_fields'
					),

					/**
					 * Options bar Settings */
					 
					array(
						'id' => 'options_bar_settings',
						'title' => 'Options bar settings',
						'callback' => 'cspml_options_bar_fields',
					),
	
					/**
					 * List items Settings */
					 
					array(
						'id' => 'list_items_settings',
						'title' => 'List items settings',
						'callback' => 'cspml_list_items_fields',
					),
				
	
					/**
					 * Sort options Settings */
					 
					array(
						'id' => 'sort_options_settings',
						'title' => 'Sort options settings',
						'callback' => 'cspml_sort_options_fields',
					),
				
	
					/**
					 * Pagination Settings */
					 
					array(
						'id' => 'pagination_settings',
						'title' => 'Pagination settings',
						'callback' => 'cspml_pagination_fields',
					),
				
	
					/**
					 * Search filter Settings */
					 
					array(
						'id' => 'search_filter_settings',
						'title' => 'Search filter settings',
						'callback' => 'cspml_search_filter_fields',
					),
						
				);
				
				foreach($cspml_tabs as $tab_data){
				 
					$tabs_setting['tabs'][] = array(
						'id'     => 'cspml_' . $tab_data['id'],
						'title'  => '<span class="cspm_tabs_menu_image"><img src="'.$this->plugin_url.'/img/admin-icons/'.str_replace('_', '-', $tab_data['id']).'.png" style="width:20px;" /></span> <span class="cspm_tabs_menu_item">'.esc_attr__( $tab_data['title'], 'cspm' ).'</span>',						
						'fields' => call_user_func(array($this, $tab_data['callback'])),
					);
		
				}
									
			/*
			 * set tabs */
			 
			$metabox_object->add_field( array(
				'id'   => 'cspml_pmlf_settings_tabs',
				'type' => 'tabs',
				'tabs' => $tabs_setting
			) );
			
			return $metabox_object;
			
		}
		
		
		/**
		 * Layout Fields
		 *
		 * @since 1.0 
		 */
		function cspml_layout_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Layout Settings',
				'desc' => '',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_list_layout_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_list_layout',
				'name' => 'Layout Type',
				'desc' => 'Choose a layout type.',
				'type' => 'radio',
				'default' => 'vertical',
				'options' => array(
					'vertical' => 'Vertical',
					'horizontal-left' => 'Horizontal - Map on the Left, List & Filter on the Right',
					'horizontal-right' => 'Horizontal - Map on the Right, List & Filter on the Left',
				)
			);
			
			$fields[] = array(
				'id'   => $this->metafield_prefix . '_layout_map_section',
				'name' => esc_attr__('The Map', 'cspml' ),
				'desc' => 'Change the width & the height of the map',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),				
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_map_height',
				'name' => 'Map Height',
				'desc' => 'Specify the height of the map in pixels. Defaults to "400"',
				'type' => 'text',
				'default' => '400',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '0',
				),								
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_map_cols',
				'name' => 'Map Width',
				'desc' => 'Specify how many columns the map will occupy on the page. The page is divided to 12 columns. Defaults to "4 of 12 columns"',
				'type' => 'select',
				'default' => '4',
				'options' => array(
					'2' => '2 of 12 columns',
					'3' => '3 of 12 columns',
					'4' => '4 of 12 columns',
					'5' => '5 of 12 columns',
					'6' => '6 of 12 columns',
					'7' => '7 of 12 columns',
					'8' => '8 of 12 columns',
					'9' => '9 of 12 columns',
					'10' => '10 of 12 columns',
				),	
				'attributes' => array(
					'data-conditional-id' => $this->metafield_prefix . '_list_layout',
					'data-conditional-value' => wp_json_encode(array('horizontal-left', 'horizontal-right')),								
				),																
			);
			
			$fields[] = array(
				'id'   => $this->metafield_prefix . '_layout_filter_section',
				'name' => esc_attr__('The Filter', 'cspml' ),
				'desc' => 'Change the display position, the width & the height of the filter',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),				
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_position',
				'name' => esc_attr__('Search Filter position', 'cspml'),				
				'type' => 'radio',
				'desc' => esc_attr__('Choose the position of the search filter on a page. Defaults to "Left".', 'cspml'),
				'options' => array(
					'left' => esc_attr__( 'Left', 'cspml' ),
					'right' => esc_attr__( 'Right', 'cspml' ),
				),
				'default' => 'left',
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_filter_cols',
				'name' => 'Filter width',
				'desc' => 'Specify how many columns the filter will occupy on the page. The page is divided to 12 columns. Defaults to "3 of 12 columns"',
				'type' => 'select',
				'default' => '3',
				'options' => array(
					'2' => '2 of 12 columns',
					'3' => '3 of 12 columns',
					'4' => '4 of 12 columns',
					'5' => '5 of 12 columns',
					'6' => '6 of 12 columns',
					'7' => '7 of 12 columns',
					'8' => '8 of 12 columns',
					'9' => '9 of 12 columns',
					'10' => '10 of 12 columns',
					'11' => '11 of 12 columns',
					'12' => '12 of 12 columns',
				),								
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_filter_height',
				'name' => 'Filter Max-Height',
				'desc' => 'Specify the height of the filter in pixels. By default, the filter has no maximum height. Clear this field to remove max-height!',
				'type' => 'text',
				'default' => '',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '0',
				),								
			);
			
			$fields[] = array(
				'id'   => $this->metafield_prefix . '_layout_list_section',
				'name' => esc_attr__('The List', 'cspml' ),
				'desc' => 'Change the list items view & the height of the list',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),				
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_default_view_option',
				'name' => esc_attr__('Initial view', 'cspml'),					
				'type' => 'radio',
				'desc' => esc_attr__('Select the initial view. Defaults to "List".', 'cspml'),
				'options' => array(
					'list' => esc_attr__( 'List', 'cspml' ),
					'grid' => esc_attr__( 'Grid', 'cspml' ),
				),
				'default' => 'list',
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_grid_cols',
				'name' => esc_attr__('Grid columns', 'cspml'),					
				'type' => 'radio',
				'desc' => esc_attr__('Select the number of items to display in a single row.', 'cspml'),
				'options' => array(
					'cols1' => esc_attr__( 'One item/column', 'cspml' ),
					'cols2' => esc_attr__( 'Two items/columns', 'cspml' ),
					'cols3' => esc_attr__( 'Three items/columns', 'cspml' ),
					'cols4' => esc_attr__( 'Four items/columns', 'cspml' ),
					'cols6' => esc_attr__( 'Six items/columns', 'cspml' ),
				),
				'default' => 'cols3',
				'attributes' => array(
					'data-conditional-id' => $this->metafield_prefix . '_default_view_option',
					'data-conditional-value' => 'grid',								
				),
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_list_height',
				'name' => 'List Max-Height',
				'desc' => 'Specify the height of the list in pixels. By default, the list has no maximum height. Clear this field to remove max-height!',
				'type' => 'text',
				'default' => '',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '0',
				),								
			);
			
			return $fields;
			
		}
		
		
		/**
		 * Options Bar Fields
		 *
		 * @since 1.0 
		 */
		function cspml_options_bar_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Options Bar Settings',
				'desc' => '',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_options_bar_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_show_options_bar',
				'name' => esc_attr__('Options Bar Visibility', 'cspml'),				
				'type' => 'radio',
				'desc' => esc_attr__('Show/Hide the Options bar. The "Options bar" is the top area containing the "View options", the "Sort by" option and the "Posts count".', 'cspml'),
				'options' => array(
					'yes' => esc_attr__( 'Show', 'cspml' ),
					'no' => esc_attr__( 'Hide', 'cspml' ),
				),
				'default' => 'yes',
			);
			
			$fields[] = array(
				'id'   => $this->metafield_prefix . '_view_options_section',
				'name' => esc_attr__('View options settings', 'cspml' ),
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),				
			);

				$fields[] = array(
					'id' => $this->metafield_prefix . '_show_view_options',
					'name' => esc_attr__('View Options Visibility', 'cspml'),					
					'type' => 'radio',
					'desc' => esc_attr__('Show/Hide the "View options". The "View options" is the botton in the Options bar that allows switching from Grid view to List view.', 'cspml'),
					'options' => array(
						'yes' => esc_attr__( 'Show', 'cspml' ),
						'no' => esc_attr__( 'Hide', 'cspml' ),
					),
					'default' => 'yes',
				);
				
			$fields[] = array(
				'id'   => $this->metafield_prefix . '_post_count_section',
				'name' => esc_attr__('Posts count settings', 'cspml' ),
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),			
			);

				$fields[] = array(
					'id' => $this->metafield_prefix . '_cslf_show_posts_count',				
					'name' => esc_attr__('Posts Count Visibility', 'cspml'),
					'type' => 'radio',
					'desc' => esc_attr__('Show/Hide the "Posts count".', 'cspml'),
					'options' => array(
						'yes' => esc_attr__( 'Show', 'cspml' ),
						'no' => esc_attr__( 'Hide', 'cspml' ),
					),
					'default' => 'yes',
				);
				
				$fields[] = array(
					'id'      => $this->metafield_prefix . '_cslf_posts_count_clause',
					'name'    => esc_attr__('Posts count label', 'cspml'),
					'desc' => 'Use this field to enter your custom label.<br /><strong>Syntaxe:</strong> YOUR LABEL [posts_count] YOUR LABEL',
					'default' => '[posts_count] Result(s)',					
					'type'    => 'text',
				);
				
			return $fields;
			
		}
		
		
		/**
		 * List Items Fields
		 *
		 * @since 1.0 
		 */
		function cspml_list_items_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'List Items Settings',
				'desc' => '',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_list_items_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_scroll_to_list_item',
				'name' => 'Scroll to list item',
				'desc' => 'Scroll to a list item when its related marker on the map is clicked. Defaults to "Yes".',
				'type' => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No',
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_list_items_featured_img',
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
				'id' => $this->metafield_prefix . '_listings_title',
				'name' => esc_attr__('List Item Title', 'cspml'),
				'desc' => 'Create your customized title by entering the name of your custom syntaxe. You can combine the title with your custom fields. Leave this field empty to use the default title.
					<br /><strong>Syntax:</strong> [meta_key<sup>1</sup>][separator<sup>1</sup>][meta_key<sup>2</sup>][separator<sup>2</sup>][meta_key<sup>n</sup>]...[title lenght].
					<br /><strong>Example of use:</strong> [property_price][s=,][-][title][l=50]
					<br /><strong>*</strong> To add the title enter [title]
					<br /><strong>*</strong> To insert empty an space enter [-]
					<br /><strong>* Make sure there\'s no empty spaces between ][</strong>',				
				'type' => 'textarea'
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_listings_details',
				'name' => esc_attr__('List Item Description', 'cspml'),
				'desc' => 'Create your customized description content. You can combine the content with your custom fields & taxonomies. Leave this field empty to use the default description.
					<br /><strong>Syntax:</strong> [content;content_length][separator][t=label:][meta_key][separator][t=Category:][tax=taxonomy_slug][separator]...[description length]
					<br /><strong>Example of use:</strong> [content;80][s=br][t=Category:][-][tax=category][s=br][t=Address:][-][post_address]
					<br /><strong>*</strong> To specify a description length, use <strong>[l=LENGTH]</strong>. Change LENGTH to a number (e.g. 100).
					<br /><strong>*</strong> To add a label, use <strong>[t=YOUR_LABEL]</strong>
					<br /><strong>*</strong> To add a custom field, use <strong>[CUSTOM_FIELD_NAME]	</strong>				
					<br /><strong>*</strong> To insert a taxonomy, use <strong>[tax=TAXONOMY_SLUG]</strong>
					<br /><strong>*</strong> To insert new line enter <strong>[s=br]</strong>
					<br /><strong>*</strong> To insert an empty space enter <strong>[-]</strong>
					<br /><strong>*</strong> To insert the content/excerpt, use <strong>[content;LENGTH]</strong>. Change LENGTH to a number (e.g. 100).
					<br /><strong>* Make sure there\'s no empty spaces between ][</strong>',
				'default' => '[l=700]',
				'type' => 'textarea'
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_click_on_title',
				'name' => 'Title as link',
				'desc' => 'Select "Yes" to use the title as a link to the post page.',
				'type' => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No',
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_ellipses',
				'name' => 'Show ellipses',
				'desc' => 'Show ellipses (&hellip;) at the end of the content. Defaults to "Yes".',
				'type' => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No',
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_click_on_img',
				'name' => 'Image as link',
				'desc' => 'Select "Yes" to use the featured image as a link to the post page.',
				'type' => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No',
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_external_link',
				'name' => 'Post URL',
				'desc' => 'Choose the way you want to open the post page.',
				'type' => 'radio',
				'default' => 'same_window',
				'options' => array(
					'new_window' => 'Open in a new window',
					'same_window' => 'Open in the same window',
					'popup' => 'Open inside a modal/popup',
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_marker_position_btn_section',
				'name' => 'Showing the item on the map',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_fire_pinpoint_on_hover',
				'name' => 'Show item on map on item hover?',
				'desc' => 'When hovering over an item in the list for 1/2 second, choose whether or not to center the map on that the item\'s location. 
                           Defaults to "No".',
				'type' => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No',
				)
			);
            
            $fields[] = array(
				'id' => $this->metafield_prefix . '_show_fire_pinpoint_btn',
				'name' => '"Position on the map" Button\'s Visibilty',
				'desc' => 'Show/Hide the button that when clicked, will zoom-in the map to the item\'s location. 
                           To adjust the zoom level, scroll up to the widget <u>"Progress Map Settings"</u> and change the zoom from <u>"Carousel settings => Map zoom"</u>!',
				'type' => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => 'Show',
					'no' => 'Hide',
				)
			);
		
			return $fields;
					
		}
		
		
		/**
		 * Sort Options settings Fields
		 *
		 * @since 1.0 
		 */
		function cspml_sort_options_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Sort Options Settings',
				'desc' => '',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_sort_options_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_show_sort_option',
				'name' => esc_attr__('Turn On/Off the sort option', 'cspml'),				
				'type' => 'radio',
				'desc' => esc_attr__('Enable/Disable the sort option.', 'cspml'),
				'options' => array(
					'yes' => esc_attr__( 'Enable', 'cspml' ),
					'no' => esc_attr__( 'Disable', 'cspml' ),
				),
				'default' => 'yes',
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_sort_options',
				'name' => esc_attr__('Default Sort Options', 'cspml'),				
				'type' => 'pw_multiselect',
				'desc' => esc_attr__('Select the sort options to use from the available options.', 'cspml'),
				'options' => array(
					'default|Default|init' => esc_attr__( 'Default', 'cspml' ),
					'data-date-created|Date (Newest first)|desc' => esc_attr__( 'Date (Newest first)', 'cspml' ),
					'data-date-created|Date (Oldest first)|asc' => esc_attr__( 'Date (Oldest first)', 'cspml' ),
					'data-title|Title A to Z|asc' => esc_attr__( 'Title A to Z', 'cspml' ),
					'data-title|Title Z to A|desc' => esc_attr__( 'Title Z to A', 'cspml' )
				),
				'default' => array(),
				'attributes' => array(
					'placeholder' => 'Select'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_sort_options_order',
				'name' => esc_attr__( 'Sort options display order', 'cspml' ),
				'desc' => esc_attr__( 'Change the display order of the sort options.', 'cspml' ),				
				'type' => 'order',
				//'inline' => true,
				'options' => array(
					'default' => esc_attr__('Default sort option', 'cspml'),
					'custom' => esc_attr__('Custom sort options', 'cspml'),					
				),
			);
						
			$fields[] = array(
				'id' => $this->metafield_prefix . '_custom_sort_options',
				'name' => 'Custom Sort Options', 
				'desc' => 'Add your custom sort options. You can sort list items by custom fields.<br />',
				'type' => 'group',
				'repeatable'  => true,
				'options'     => array(
					'group_title'   => esc_attr__( 'Custom field {#}', 'cspml' ),
					'add_button'    => esc_attr__( 'Add New Custom Field', 'cspml' ),
					'remove_button' => esc_attr__( 'Remove Custom Field', 'cspml' ),
					'sortable'      => true,
					'closed'     => true,
				),
				'fields' => array(	
					array(
						'id' => 'sort_options_name',
						'name' => 'Custom field name', 
						'desc' => 'The name of the custom field to sort with. e.g. "property_price"',
						'type' => 'text',
						'default' => '',
					),	
					array(
						'id' => 'sort_meta_type',
						'name' => 'Custom field type', 
						'desc' => 'Select the custom field type.',
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
						)
					),
					array(
						'id' => 'sort_options_label',
						'name' => 'Label', 
						'desc' => 'The label to use to describe the sort option. e.g. "Price"',
						'type' => 'text',
						'default' => '',	
						'attributes'  => array(
							'data-group-title' => 'text'
						)																
					),			
					array(
						'id' => 'sort_options_order',
						'name' => 'Sort order', 
						'desc' => 'Select the sort order.',
						'type' => 'multicheck',
						'default' => array('desc', 'asc'),
						'options' => array(
							'desc' => 'Descending <sup>e.g. (Highest first)</sup>',
							'asc' => 'Ascending <sup>e.g. (Lowest first)</sup>'
						),
						'select_all_button' => false,
					),
					array(
						'id' => 'sort_options_desc_suffix',
						'name' => 'Descending suffix', 
						'desc' => 'Enter the suffix to add to the end of the label for the descending order. e.g. "(Highest first)"',
						'type' => 'text',
						'default' => '(Highest first)',
					),
					array(
						'id' => 'sort_options_asc_suffix',
						'name' => 'Ascending suffix', 
						'desc' => 'Enter the suffix to add to the end of the label for the ascending order. e.g. "(Lowest first)"',
						'type' => 'text',
						'default' => '(Lowest first)',
					),
					array(
						'id' => 'sort_options_visibilty',
						'name' => 'Visibilty', 
						'desc' => 'Show/hide the custom sort option from being displayed. Default "Show"',
						'type' => 'radio',
						'default' => 'yes',
						'options' => array(
							'yes' => 'Show',
							'no' => 'Hide'
						)
					)
				)
			);
		
			return $fields;
			
		}
		
		
		/**
		 * Pagination settings Fields
		 *
		 * @since 1.0 
		 */
		function cspml_pagination_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Pagination Settings',
				'desc' => '',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_pagination_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
			
			$fields[] = array(
				'id'   => $this->metafield_prefix . '_posts_per_page',
				'name' => esc_attr__( 'Number of posts per page', 'cspml' ),				
				'desc' => esc_attr__( 'Enter the number of the posts per page. Defaults to the number selected in "Settings / Reading / Blog pages show at most".', 'cspml' ),
				'type' => 'text',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
					'min' => '0',
				),				
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_pagination_position',
				'name' => esc_attr__('Pagination position', 'cspml'),				
				'type' => 'radio',
				'desc' => esc_attr__('Select the position of the pagination in the page. Defaults to "Bottom".', 'cspml'),
				'options' => array(
					'top' => esc_attr__( 'Top', 'cspml' ),
					'bottom' => esc_attr__( 'Bottom', 'cspml' ),
					'both' => esc_attr__( 'Both', 'cspml' ),
				),
				'default' => 'bottom',
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_pagination_align',
				'name' => esc_attr__('Pagination alignment', 'cspml'),				
				'type' => 'radio',
				'desc' => esc_attr__('Select the alignment of the pagination in the page. Defaults to "Center".', 'cspml'),
				'options' => array(
					'left' => esc_attr__( 'Left', 'cspml' ),
					'center' => esc_attr__( 'Center', 'cspml' ),
					'right' => esc_attr__( 'Right', 'cspml' ),
				),
				'default' => 'center',
			);

			$fields[] = array(
				'id'   => $this->metafield_prefix . '_prev_page_text',
				'name' => esc_attr__( 'Previous page text', 'cspml' ),				
				'desc' => esc_attr__( 'Enter the text that appears in the pagination button "Previous". Defaults to "&laquo;".', 'cspml' ),
				'type' => 'text',
				'default' => '&laquo;',
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_next_page_text',
				'name' => esc_attr__( 'Next page text', 'cspml' ),				
				'desc' => esc_attr__( 'Enter the text that appears in the pagination button "Next". Defaults to "&raquo;".', 'cspml' ),
				'type' => 'text',
				'default' => '&raquo;',
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_show_all',
				'name' => esc_attr__('Show all pages', 'cspml'),				
				'type' => 'radio',
				'desc' => esc_attr__('Select True to show all of the pages in the pagination instead of a short list of pages near the current page. Defaults to "False".', 'cspml'),
				'options' => array(
					'true' => esc_attr__( 'Yes', 'cspml' ),
					'false' => esc_attr__( 'No', 'cspml' ),
				),
				'default' => 'false',
			);

			return $fields;
			
		}
		
		
		/**
		 * Search Filter settings Fields
		 *
		 * @since 1.0 
		 */
		function cspml_search_filter_fields(){
			
			$fields = array();
			
			$fields[] = array(
				'name' => 'Search Filter Settings',
				'desc' => '',
				'type' => 'title',
				'id'   => $this->metafield_prefix . '_search_filter_settings',
				'attributes' => array(
					'style' => 'font-size:20px; color:#008fed; font-weight:400;'
				),
			);
						
			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_faceted_search_option',
				'name' => esc_attr__('Search Filter option', 'cspml'),				
				'type' => 'radio',
				'desc' => esc_attr__('Select "Yes" to enable this option in the plugin. Defaults to "No".', 'cspml'),
				'options' => array(
					'yes' => esc_attr__( 'Yes', 'cspml' ),
					'no' => esc_attr__( 'No', 'cspml' ),
				),
				'default' => 'yes',
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_faceted_search_display_option',
				'name' => esc_attr__('Search Filter display option', 'cspml'),				
				'type' => 'radio',
				'desc' => esc_attr__('Choose whether you want to hide/show the filter on page load. Defaults to "Show on page load".', 'cspml'),
				'options' => array(
					'show' => esc_attr__( 'Show on page load', 'cspml' ),
					'hide' => esc_attr__( 'Hide on page load', 'cspml' ),
				),
				'default' => 'show',
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_filter_btns_position',
				'name' => esc_attr__('Filter & Reset buttons position', 'cspml'), 
				'desc' => 'Select the position of the filter buttons in the page. Defaults to "Bottom".',
				'type' => 'radio',
				'default' => 'bottom',
				'options' => array(
					'top' => 'Top',
					'bottom' => 'Bottom',
					'both' => 'Both',
				)
			);	
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_filter_fields_order',
				'name' => esc_attr__( 'Filter fields display order', 'cspml' ),
				'desc' => esc_attr__( 'Change the display order of the filter fields.', 'cspml' ),				
				'type' => 'order',
				//'inline' => true,
				'options' => array(
					'keyword' => esc_attr__('keyword', 'cspml'),
					'date_filter' => esc_attr__('Posts publish date', 'cspml'),
					'taxonomies' => esc_attr__('Taxonomies', 'cspml'),
					'custom_fields' => esc_attr__('Custom Fields', 'cspml'),
				),
			);
			
			/**
			 * Keyword search
			 * @since 2.2 */
			 	
			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_keyword_param_section',
				'name' => 'Keyword filter Parameters',
				'desc' => 'Filter posts based on a keyword.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),				
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_keyword_filter_option',
				'name' => 'Keyword filter', 
				'desc' => 'This will add a text field in the filter that allows to filter posts based on a keyword. Defaults to "No".',
				'type' => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No',
				),
				'before_row' => str_replace('[title]', 'Keyword filter Parameters', $this->toggle_before_row),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_keyword_display_status',
				'name' => 'Display status',
				'desc' => 'Choose whether to open this field container on page load or to close it. Defaults to "Open".',
				'type' => 'radio',
				'default' => 'open',
				'options' => array(
					'open' => 'Open',
					'close' => 'Close'
				)
			);
						
			$fields[] = array(
				'id' => $this->metafield_prefix . '_keyword_filter_label',
				'name' => 'Title', 
				'desc' => 'Enter a Title to display in the filter. Defaults to "Keyword".',
				'type' => 'text',
				'default' => 'Keyword'												
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_keyword_filter_placeholder',
				'name' => 'Text field placeholder', 
				'desc' => 'Enter a text to display as the placehoder of the text field. Defaults to "Keyword".',
				'type' => 'text',
				'default' => 'Keyword'												
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_keyword_filter_description',
				'name' => 'Description', 
				'desc' => 'The description will be displayed above the field.',
				'type' => 'textarea',
				'default' => '',
				'attributes' => array(
					'rows' => 3,
				),
				'after_row' => $this->toggle_after_row,											
			);
			
			/**
			 * Date Parameters 
			 * @since 2.1 */
			 	
			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_date_param_section',
				'name' => 'Posts Publish Date Parameters',
				'desc' => 'Filter posts by date period.',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_date_filter_option',
				'name' => 'Filter posts by publish date', 
				'desc' => 'This will add a Datepicker field in the filter that allows to filter posts based on the publish date. Defaults to "No".',
				'type' => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => 'Yes',
					'no' => 'No',
				),
				'before_row' => str_replace('[title]', 'Posts publish date parameters', $this->toggle_before_row),
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_date_filter_display_status',
				'name' => 'Display status',
				'desc' => 'Choose whether to open this field container on page load or to close it. Defaults to "Open".',
				'type' => 'radio',
				'default' => 'open',
				'options' => array(
					'open' => 'Open',
					'close' => 'Close'
				)
			);
						
			$fields[] = array(
				'id' => $this->metafield_prefix . '_date_filter_param',
				'name' => 'Posts publish date parameters', 
				'desc' => 'Choose the parameters of your datepicker field',
				'type' => 'group',
				'default' => '',
				'repeatable' => false,
				'options'     => array(
					'group_title'   => esc_attr__( 'Datepicker parameters', 'cspml' ),
					'closed'     => true,
				),				
				'fields' => array(
					array(
						'id' => 'date_filter_label',
						'name' => 'Title', 
						'desc' => 'Enter a Title to display in the filter. Defaults to "Posts publish date".',
						'type' => 'text',
						'default' => 'Posts publish date'												
					),
					array(
						'id' => 'date_filter_description',
						'name' => 'Description', 
						'desc' => 'The description will be displayed above the field.',
						'type' => 'textarea',
						'default' => '',
						'attributes' => array(
							'rows' => 3,
						)											
					),
					array(
						'id' => 'date_filter_type',
						'name' => 'Filter by', 
						'desc' => 'Choose how you want to get posts. Defaults to "Exact date".',
						'type' => 'radio',
						'default' => 'exact_date',
						'options' => array(
							'exact_date' => 'Exact date <sup>(Show posts published in the selected date)</sup>',							
							'after_date' => 'After date <sup>(Show posts published after selected date)<sup>',
							'before_date' => 'Before date <sup>(Show posts published before selected date)<sup>',
							'date_range' => 'Between two dates <sup>(Show posts published between two dates)</sup>',
						)
					),
					array(
						'id' => 'date_format',
						'name' => 'Date format', 
						'desc' => 'Choose the date format. Defaults to "Year, Month, Day".',
						'type' => 'radio',
						'default' => 'ymd',
						'options' => array(
							'ymd' => 'Year, Month, Day',
							'dmy' => 'Day, Month, Year',
						)
					),
					array(
						'id' => 'exclude_date_atts',
						'name' => 'Exclude date attributes', 
						'desc' => 'You can exclude up to two attributes from the date ("Day", "Month" and/or "Year").',
						'type' => 'pw_multiselect',
						'default' => '',
						'options' => array(
							'y' => 'Year',
							'm' => 'Month',
							'd' => 'Day',
						),
						'attributes' => array(
							'placeholder' => 'Select date attribute',
							'data-maximum-selection-length' => '2',
						),
					),
					array(
						'id' => 'start_date',
						'name'   => 'Start date (Year/Month/Day)',
						'desc' => 'The start view date. All the dates before this date will be disabled.',
						'type' => 'text_date',
						'date_format' => 'Y/m/d',
					),
					array(
						'id' => 'end_date',
						'name'   => 'End date (Year/Month/Day)',
						'desc' => 'The end view date. All the dates after this date will be disabled.',
						'type' => 'text_date',
						'date_format' => 'Y/m/d',
					), 
					array(
						'id' => 'start_view',
						'name' => 'Start View', 
						'desc' => 'Select the start view when the datepicker is loaded. Defaults to "Days".',
						'type' => 'radio',
						'default' => 'days',
						'options' => array(
							'days' => 'Days',
							'months' => 'Months',
							'years' => 'Years',
						)
					), 
					array(
						'id' => 'week_start',
						'name' => 'Week start',
						'desc' => 'Select the start day of the week. Defaults to "Days".',
						'type' => 'radio',
						'default' => 'sunday',
						'options' => array(
							'sunday' => 'Sunday',
							'monday' => 'Monday',
							'tuesday' => 'Tuesday',
							'wednesday' => 'Wednesday',
							'thursday' => 'Thursday',
							'friday' => 'Friday',
							'saturday' => 'Saturday',
						)
					),
					array(
						'id' => 'year_first',
						'name' => 'Year first', 
						'desc' => 'Show year before month on the datepicker header. Defaults to "No".',
						'type' => 'radio',
						'default' => 'no',
						'options' => array(							
							'yes' => 'Yes',
							'no' => 'No',
						)
					),
					array(
						'id' => 'year_suffix',
						'name' => 'Year suffix', 
						'desc' => 'Add suffix to year on the datepicker header.',
						'type' => 'text',
						'default' => '',
					),
					array(
						'id' => 'language',
						'name' => 'Language',
						'desc' => 'Select datepicker language. Defaults to "English".',
						'type' => 'select',
						'default' => 'en-US',
						'options' => array(
							'ca-ES' => 'Catalan', 
							'zh-CN' => 'Chinese - China',
							'nl-NL' => 'Dutch - The Netherlands',
							'en-US' => 'English - United States',
							'fr-FR' => 'French - France',
							'de-DE' => 'German - Germany', 
							'pt-BR' => 'Portuguese - Brazil',  
							'es-ES' => 'Spanish - Spain', 
							'sv-SE' => 'Swedish - Sweden', 
							'tr-TR' => 'Turkish - Turkey', 
						)
					),
					array(
						'id' => 'auto_show',
						'name' => 'Auto show', 
						'desc' => 'Show the datepicker automatically when initialized. Defaults to "No".',
						'type' => 'radio',
						'default' => 'no',
						'options' => array(							
							'yes' => 'Yes',
							'no' => 'No',
						)
					),
					array(
						'id' => 'auto_hide',
						'name' => 'Auto hide', 
						'desc' => 'Hide the datepicker automatically when picked. Defaults to "No".',
						'type' => 'radio',
						'default' => 'no',
						'options' => array(							
							'yes' => 'Yes',
							'no' => 'No',
						)
					),
					array(
						'id' => 'inline',
						'name' => 'Inline', 
						'desc' => 'Enable inline mode. When set to "Yes", datepicker will be appended in the filter container. Defaults to "No".',
						'type' => 'radio',
						'default' => 'no',
						'options' => array(							
							'yes' => 'Yes',
							'no' => 'No',
						)
					),
				),
				'after_group_row' => $this->toggle_after_row,	
			);
			
			/**
			 * Taxonomies */
			 			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_taxonomies_section',
				'name' => 'Taxonomies Parameters',
				'desc' => 'Filter posts by taxonomies',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);
			
			/**
			 * [@post_type_taxonomy_options] : Takes the list of all taxonomies related to the post type selected in "Query settings" */
			
			$post_type_taxonomy_options	= $this->cspml_get_post_type_taxonomies($this->selected_cpt);		
				unset($post_type_taxonomy_options['post_format']);
				
			reset($post_type_taxonomy_options); // Set the cursor to 0
			
			$group_id = $this->metafield_prefix . '_cslf_taxonomies';

			$taxonomy_fields = array();
			
			foreach($post_type_taxonomy_options as $cpt_taxonomy_slug => $cpt_taxonomy_title){
	
				$tax_name = $cpt_taxonomy_slug;
				$tax_label = $cpt_taxonomy_title;
				
				$taxonomy_fields[] = array(
					'id' => 'taxonomy_exclude_terms_'.$tax_name,
					'name' => 'Exclude terms',
					'desc' => 'Select the terms that you want to exclude from the filter.',
					'type' => 'pw_multiselect',
					'options' => $this->cspml_get_term_options($tax_name),
					'attributes' => array(
						'placeholder' => 'Select Term(s)',
						'data-conditional-id' => wp_json_encode( array( $group_id, 'taxonomy_name' ) ),
						'data-conditional-value' => $tax_name,																			
					),					
				);
				
				$taxonomy_fields[] = array(
					'id' => 'taxonomy_include_terms_'.$tax_name,
					'name' => 'Include terms',
					'desc' => 'Select the terms that you want to include in the filter. Defaults to all terms.',
					'type' => 'pw_multiselect',
					'options' => $this->cspml_get_term_options($tax_name),
					'attributes' => array(
						'placeholder' => 'Select Term(s)',
						'data-conditional-id' => wp_json_encode( array( $group_id, 'taxonomy_name' ) ),
						'data-conditional-value' => $tax_name,																									
					),
				);
				
			}				

			$fields[] = array(
				'id' => $group_id,
				'name' => 'Taxonomies', 
				'desc' => 'Add the taxonomies to use in the faceted search form.<br />
						   <span style="color:red;">Please note that you can\'t use the same taxonomy twice!</span>',
				'type' => 'group',
				'default' => '',
				'repeatable'  => true,
				'options'     => array(
					'group_title'   => esc_attr__( 'Taxonomy {#}', 'cspml' ),
					'add_button'    => esc_attr__( 'Add New Taxonomy', 'cspml' ),
					'remove_button' => esc_attr__( 'Remove Taxonomy', 'cspml' ),
					'sortable'      => true,
					'closed'     => true,
				),	
				'fields' => array_merge(
					array(	
						array(
							'id' => 'taxonomy_label',
							'name' => 'Label/Title', 
							'desc' => 'Enter a label/Title to describe the taxonomy. e.g. "Category".',
							'type' => 'text',
							'default' => '',
							'attributes'  => array(
								'data-group-title' => 'text'
							)						
						),
						array(
							'id' => 'taxonomy_name',
							'name' => 'Taxonomy name', 
							'desc' => 'Select the taxonomy name. e.g "category".',
							'type' => 'select',
							'options' => $post_type_taxonomy_options,
							'default' => '',					
						),	
						array(
							'id' => 'taxonomy_description',
							'name' => 'Description', 
							'desc' => 'The description will be displayed above the field.',
							'type' => 'textarea',
							'default' => '',
							'attributes' => array(
								'rows' => 3,
							)											
						),
						array(
							'id' => 'taxonomy_display_type',
							'name' => 'Display type', 
							'desc' => 'Select the display type for this taxonomy. Default "checkbox".',
							'type' => 'radio',
							'default' => 'checkbox',
							'options' => array(
								'checkbox' => 'Checkbox',
								'radio' => 'Radio',
								'select' => 'Select',
								'number' => 'Number <sup>(To be used with dicimals only!)</sup>',
								'double_slider' => 'Double range slider <sup>(To be used with dicimals only!)</sup>',
								'single_slider' => 'Single range slider <sup>(To be used with dicimals only!)</sup>',
							)
						),
					),
					
					$taxonomy_fields,
					
					array(
						array(
							'id' => 'taxonomy_operator_param',
							'name' => '"Operator" parameter', 
							'desc' => 'Operator to test with when filtering the posts. Possible values are "IN", "NOT IN", "AND".<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters" target="_blank" class="cspm_blank_link">More</a>',
							'type' => 'radio',
							'default' => 'IN',
							'options' => array(
								'AND' => 'AND',
								'IN' => 'IN',
								'NOT IN' => 'NOT IN',
							)
						),
						array(
							'id' => 'taxonomy_orderby_param',
							'name' => 'Orderby parameter', 
							'desc' => 'Order terms by a parameter. Defaults to "name".',
							'type' => 'select',
							'default' => 'name',
							'options' => array(
								'name' => 'Order by name',
								'term_group' => 'Order by term group',
								'term_id' => 'Order by term ID',
								'description' => 'Order by term description',
								'count' => 'Order by term count',
								'include' => 'Order by "Include term IDs"',
								'none' => 'No order',
							)
						),							
						array(
							'id' => 'taxonomy_order_param',
							'name' => 'Order parameter', 
							'desc' => 'Whether to order terms in ascending or descending order Defaults to "ASC".',
							'type' => 'radio',
							'default' => 'ASC',
							'options' => array(
								'ASC' => 'Ascending order from lowest to highest values',
								'DESC' => 'Descending order from highest to lowest values'
							)
						),							
						array(
							'id' => 'taxonomy_hide_empty',
							'name' => 'Hide empty terms', 
							'desc' => 'Hide the terms with no posts assigned to them. Default "Yes"',
							'type' => 'radio',
							'default' => 'yes',
							'options' => array(
								'yes' => 'Yes',
								'no' => 'No'
							)
						),
						array(
							'id' => 'taxonomy_search_all_option',
							'name' => '"Search All" Option', 
							'desc' => 'Select "Yes" to enable this option for this taxonomy. Default "No"',
							'type' => 'radio',
							'default' => 'no',
							'options' => array(
								'yes' => 'Yes',
								'no' => 'No'
							)
						),
						array(
							'id' => 'taxonomy_search_all_text',
							'name' => '"Search All" Label', 
							'desc' => 'Enter the label for the "Search all" option.',
							'type' => 'text',
							'default' => 'All',
						),
						array(
							'id' => 'taxonomy_symbol',
							'name' => 'Symbol', 
							'desc' => 'The symbol is the indicator of the taxonomy, like currency symbol ($) or surface unit (m²).',
							'type' => 'text',
							'default' => '',
						),
						array(
							'id' => 'taxonomy_symbol_position',
							'name' => 'Symbol position', 
							'desc' => 'Select in which side of the taxonomy the symbol will be displayed. Default (Before).',
							'type' => 'radio',
							'default' => 'before',
							'options' => array(
								'before' => 'Before',
								'after' => 'After'
							)
						),				
						array(
							'id' => 'taxonomy_show_count',
							'name' => 'Show count', 
							'desc' => 'Select "Yes" to show the number of posts assigned to each taxonomy term. Default "No".',
							'type' => 'radio',
							'default' => 'no',
							'options' => array(
								'yes' => 'Yes',
								'no' => 'No'
							)
						),						
						array(
							'id' => 'taxonomy_display_status',
							'name' => 'Display status',
							'desc' => 'Choose whether to open this field container on page load or to close it. Defaults to "Open".',
							'type' => 'radio',
							'default' => 'open',
							'options' => array(
								'open' => 'Open',
								'close' => 'Close'
							)
						),
						array(
							'id' => 'taxonomy_visibilty',
							'name' => 'Visibilty', 
							'desc' => 'Show/hide the taxonomy from being displayed in the filter form. Default "Show".',
							'type' => 'radio',
							'default' => 'yes',
							'options' => array(
								'yes' => 'Show',
								'no' => 'Hide'
							)
						)
					)
				)
			);
				
			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_taxonomy_relation_param',
				'name' => '"Relation" parameter', 
				'desc' => 'Select the relationship between multiple taxonomies. Defaults to "AND". <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters" target="_blank" class="cspm_blank_link">More</a>',
				'type' => 'radio',
				'default' => 'AND',
				'options' => array(
					'AND' => 'AND',
					'OR' => 'OR',
				),
			);
			
			/**
			 * Custom fields */
			 	
			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_custom_fields_section',
				'name' => 'Custom Fields Parameters',
				'desc' => 'Filter posts by custom fields',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				)
			);
			
			$group_id = $this->metafield_prefix . '_cslf_custom_fields';
			
			$fields[] = array(
				'id' => $group_id,
				'name' => 'Custom fields', 
				'desc' => 'Add the custom fields to use in the faceted search form.<br />',
				'type' => 'group',
				'default' => '',
				'repeatable' => true,
				'options'     => array(
					'group_title'   => esc_attr__( 'Custom field {#}', 'cspml' ),
					'add_button'    => esc_attr__( 'Add New Custom Field', 'cspml' ),
					'remove_button' => esc_attr__( 'Remove Custom Field', 'cspml' ),
					'sortable'      => true,
					'closed'     => true,
				),
				'fields' => array(
					array(
						'id' => 'custom_field_label',
						'name' => 'Label/Title', 
						'desc' => 'Enter a label/Title to describe the custom field. e.g. "Price".',
						'type' => 'text',
						'default' => '',
						'attributes'  => array(
							'data-group-title' => 'text'
						)							
					),
					array(
						'id' => 'custom_field_name',
						'name' => 'Custom field name',
						'desc' => 'Enter the name of custom field. e.g. "property_price"',
						'type' => 'text',
						'default' => '',
					),
					array(
						'id' => 'custom_field_description',
						'name' => 'Description', 
						'desc' => 'The description will be displayed above the field.',
						'type' => 'textarea',
						'default' => '',
						'attributes' => array(
							'rows' => 3,
						)											
					),
					array(
						'id' => 'custom_field_display_type',
						'name' => 'Display type', 
						'desc' => 'Select the display type of the custom field. Default "checkbox".',
						'type' => 'radio',
						'default' => 'checkbox',
						'options' => array(
							'text' => 'Text input',
							'min_max_text' => 'Range text input (Min & Max)',
							'price' => 'Price input',
							'min_max_price' => 'Range Price input (Min & Max)',							
							'number' => 'Number <sup>(To be used with dicimals only!)</sup>',																				
							'checkbox' => 'Checkbox',
							'radio' => 'Radio',
							'select' => 'Select',
							'double_slider' => 'Double range slider <sup>(To be used with dicimals only!)</sup>',
							'single_slider' => 'Single range slider <sup>(To be used with dicimals only!)</sup>',
							'datepicker' => 'Datepicker', //@since 2.4
						),
						'after_row' => str_replace('[title]', 'Field parameters', $this->toggle_before_row),
					),	
					array(
						'id' => 'custom_field_symbol',
						'name' => 'Symbol', 
						'desc' => 'The symbol is the indicator of the custom field, like currency symbol ($) or surface unit (m²).',
						'type' => 'text',
						'default' => '',													
					),
					array(
						'id' => 'custom_field_symbol_position',
						'name' => 'Symbol position', 
						'desc' => 'Select in which side of the custom field the symbol will be displayed. Default (Before).',
						'type' => 'radio',
						'default' => 'before',
						'options' => array(
							'before' => 'Before',
							'after' => 'After'
						),
					),
					
					/**
					 * Text parameters
					 * @since 3.2*/
					
					array(
						'id' => 'custom_field_mask',
						'name' => 'Text Mask', 
						'desc' => 'Specify the mask of the text field. <br />
								  The mask could be a price format (e.g. "000.000.000,00"), a date format (e.g. "00/00/0000"), 
								  a phone number (e.g. "(000) 000-0000"), date & time format (e.g. "00/00/0000 00:00:00"), etc...',
						'type' => 'text',
						'default' => '',						
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => wp_json_encode( array('text', 'min_max_text') ),																									
						),
					),
					array(
						'id' => 'custom_field_clear_mask',
						'name' => 'Clear if not match the mask?', 
						'desc' => 'Clear the text field if the user entres a value that doesn\'t match the mask. Defaults to "Yes".',
						'type' => 'radio',
						'default' => 'true',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No',
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => wp_json_encode( array('text', 'min_max_text') ),																									
						),													
					),
					array(
						'id' => 'custom_field_placeholder',
						'name' => 'Text placeholder', 
						'desc' => 'Specify the placeholder of the text field. e.g. "__/__/____"',
						'type' => 'text',
						'default' => '',						
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => wp_json_encode( array('text', 'min_max_text') ),																									
						),
					),
					
					/**
					 * == End text parameters== */
					 										
					/**
					 * Price field parameters/settings 
					 * Visible only when the "Display type" is "Price"!
					 * @since 3.2 */
					
					array(
						'id' => 'cf_cents_separator',
						'name' => 'Cents separator', 
						'desc' => 'Specify the cents separator. Defaults to "Dot (.)".',
						'type' => 'text',
						'default' => '.',
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => wp_json_encode( array('price', 'min_max_price') ),																									
						),	
					),
					array(
						'id' => 'cf_thousands_separator',
						'name' => 'Thousands separator', 
						'desc' => 'Specify the thousands separator. Defaults to "Comma (,)".',
						'type' => 'text',
						'default' => ',',						
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => wp_json_encode( array('price', 'min_max_price') ),																									
						),
					),
					array(
						'id' => 'cf_price_limit',
						'name' => 'Price limit', 
						'desc' => 'Specify the price limit (including cents!). Set to "0" for unlimited price. Defaults to "0".',
						'type' => 'text',
						'default' => '0',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '0',
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => wp_json_encode( array('price', 'min_max_price') ),																									
						),
					),
					array(
						'id' => 'cf_price_cents_limit',
						'name' => 'Cents limit', 
						'desc' => 'Specify the price cents limit. Set to "0" for unlimited cents. Defaults to "0".',
						'type' => 'text',
						'default' => '0',
						'attributes' => array(
							'type' => 'number',
							'pattern' => '\d*',
							'min' => '0',
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => wp_json_encode( array('price', 'min_max_price') ),																									
						),
					),
					array(
						'id' => 'cf_allow_negative_price',
						'name' => 'Allow negative price?', 
						'desc' => 'Choose whether to allow negative price (e.g. "US$ -1,500.00") or not. Defaults to "No".',
						'type' => 'radio',
						'default' => 'false',
						'options' => array(
							'true' => 'Yes',
							'false' => 'No'
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => wp_json_encode( array('price', 'min_max_price') ),																									
						),
					),
					
					/**
					 * == End Price field parameters == */

					/**
					 * Range Datepicker parameters/settings 
					 * Visible only when the "Display type" is "Datepicker"!
					 * @since 2.4 */

					array(
						'id' => 'cf_datepicker_date_format',
						'name' => 'Custom field date format', 
						'desc' => 'Specify the custom field date format. Defaults to "Year, Month, Day".',
						'type' => 'select',
						'default' => 'YYYY/MM/DD',
						'options' => array(
							'YYYY/MM/DD' => 'YYYY/MM/DD (e.g. 2017/09/12)',							
							'YY/MM/DD' => 'YY/MM/DD (e.g. 17/09/12)',
							'DD/MM/YYYY' => 'DD/MM/YYYY (e.g. 09/12/2017)',
							'DD/MM/YY' => 'DD/MM/YY (e.g. 09/12/17)',
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => 'datepicker',																									
						),													
					),
					array(
						'id' => 'cf_datepicker_date_separator',
						'name' => 'Date components separator', 
						'desc' => 'Specify the separator of the date components. Defaults to "Slash (/)".',
						'type' => 'select',
						'default' => 'slash',
						'options' => array(
							'slash' => 'Slash (/)',							
							'dash' => 'Dash (-)',
							'dot' => 'Dot (.)',
							'space' => 'Space',
							'none' => 'No separator',
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => 'datepicker',																									
						),													
					),
					array(
						'id' => 'cf_datepicker_start_date',
						'name'   => 'Start date',
						'desc' => 'Select the datepicker start view date. All the dates before this date will be disabled.',
						'type' => 'text_date',
						'date_format' => 'Y/m/d',
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => 'datepicker',	
						),																			
					),
					array(
						'id' => 'cf_datepicker_end_date',
						'name'   => 'End date',
						'desc' => 'Select the datepicker end view date. All the dates after this date will be disabled.',
						'type' => 'text_date',
						'date_format' => 'Y/m/d',
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => 'datepicker',
						),																			
					), 
					array(
						'id' => 'cf_datepicker_week_start',
						'name' => 'Week start',
						'desc' => 'Select the start day of the week. Defaults to "Sunday".',
						'type' => 'radio',
						'default' => 'sunday',
						'options' => array(
							'sunday' => 'Sunday',
							'monday' => 'Monday',
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => 'datepicker',																									
						),													
					),
					
					/**
					 * End range datepicker parameters */
								
					array(
						'id' => 'custom_field_search_all_option',
						'name' => '"Search All" Option', 
						'desc' => 'Enable the search all option. Default "No"',
						'type' => 'radio',
						'default' => 'no',
						'options' => array(
							'yes' => 'Yes',
							'no' => 'No'
						),
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => wp_json_encode(array(
								'checkbox',
								'radio',
								'select',
							)),																									
						),
					),
					array(
						'id' => 'custom_field_search_all_text',
						'name' => '"Search All" Label', 
						'desc' => 'Enter your custom label for the "search all" option.',
						'type' => 'text',
						'default' => 'All',
						'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_display_type' ) ),
							'data-conditional-value' => wp_json_encode(array(
								'checkbox',
								'radio',
								'select',
							)),																									
						),
					),
					array(
						'id' => 'custom_field_show_count',
						'name' => 'Show count', 
						'desc' => 'Select "Yes" to show the number of posts assigned to the custom field. Default "No".',
						'type' => 'radio',
						'default' => 'no',
						'options' => array(
							'yes' => 'Yes',
							'no' => 'No'
						)
					),
                    array(
						'id' => 'custom_field_serialized',
						'name' => 'Are custom field values serialized?', 
						'desc' => 'If you are saving the custom field values as serialized data, select the option "Yes". Default "No". <br />
                                   <span style="color:red;">Note: With serialized custom fields, the compare parameter of the meta query will always be set to "LIKE"!</span>',
						'type' => 'radio',
						'default' => 'no',
						'options' => array(
							'yes' => 'Yes',
							'no' => 'No'
						),
						'before_row' => $this->toggle_after_row . str_replace('[title]', 'Query parameters', $this->toggle_before_row),							                        
					),
					array(
						'id' => 'custom_field_compare_param',
						'name' => '"Compare" parameter', 
						'desc' => 'Operator to test with when filtering the posts. Use it carefully. 
                                   <span style="color: red;">For instance, if you choose the compare parameter "BETWEEN" or "Not Between", then your field <u>"Display type"</u> should be a double range field/slider (with min & max inputs)!</span><br />
                                   More about compare, please visit. <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters" target="_blank" class="cspm_blank_link">More</a>',
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
							//'REGEXP' => 'REGEXP',
							//'NOT REGEXP' => 'NOT REGEXP',
							//'RLIKE' => 'RLIKE',					
						),
                        'attributes' => array(
							'data-conditional-id' => wp_json_encode( array( $group_id, 'custom_field_serialized' ) ),
							'data-conditional-value' => wp_json_encode(array('no')),																									
						),
					),	
					array(
						'id' => 'custom_field_orderby_param',
						'name' => 'Orderby parameter', 
						'desc' => 'Sort the custom field values by a parameter. Defaults to "date".',
						'type' => 'select',
						'default' => 'meta_value',
						'options' => array(
							'pm.meta_value' => 'Order by custom field value',
							'p.ID' => 'Order by related post id',
							'p.post_title' => 'Order by related post title',
							'p.post_name' => 'Order by related post name (post slug)',
							'p.post_date' => 'Order by related post date',
						)
					),
					array(
						'id' => 'custom_field_order_param',
						'name' => 'Order parameter', 
						'desc' => 'Designates the ascending or descending order of the "orderby" parameter. Defaults to "DESC".',
						'type' => 'radio',
						'default' => 'DESC',
						'options' => array(
							'ASC' => 'Ascending order from lowest to highest values (1,2,3 | A,B,C)',
							'DESC' => 'Descending order from highest to lowest values (3,2,1 | C,B,A)'
						),
						'after_row' => $this->toggle_after_row,
					),
					array(
						'id' => 'custom_field_display_status',
						'name' => 'Display status',
						'desc' => 'Choose whether to open this field container on page load or to close it. Defaults to "Open".',
						'type' => 'radio',
						'default' => 'open',
						'options' => array(
							'open' => 'Open',
							'close' => 'Close'
						)
					),					
					array(
						'id' => 'custom_field_visibilty',
						'name' => 'Visibilty', 
						'desc' => 'Show/hide the custom field from being displayed in the filter form. Default "Yes"',
						'type' => 'radio',
						'default' => 'yes',
						'options' => array(
							'yes' => 'Show',
							'no' => 'Hide'
						)
					)
				)
			);
			
			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_custom_field_relation_param',
				'name' => '"Relation" parameter', 
				'desc' => 'Select the relationship between multiple custom fields. Defaults to "AND". <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters" target="_blank" class="cspm_blank_link">More</a>',
				'type' => 'radio',
				'default' => 'AND',
				'options' => array(
					'AND' => 'AND',
					'OR' => 'OR',
				),
			);
					
			/**
			 * Filter customiztion */
			 	
			$fields[] = array(
				'id' => $this->metafield_prefix . '_customization_section',
				'name' => 'Custmizations',
				'desc' => '',
				'type' => 'title',
				'attributes' => array(
					'style' => 'font-size:15px; color:#ff6600; font-weight:600;'
				),
			);

			$fields[] = array(
				'id' => $this->metafield_prefix . '_cslf_filter_btn_text',
				'name' => 'Filter button text', 
				'desc' => 'Enter your customized text for the "Filter" button.',
				'type' => 'text',
				'default' => 'Filter',
			);

			return $fields;
			
		}
		
		
		/**		 
		 * Get all Taxonomies related to a given post type
		 * 
		 * Since 1.0
		 */
		function cspml_get_post_type_taxonomies($post_type){
			
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
		 * Get taxonomy terms and displays them as options
		 *
		 * @since 1.0
		 */
		function cspml_get_term_options($tax_name){

			$terms = get_terms($tax_name, "hide_empty=0");
			
			$term_options = array();
						
			if(count($terms) > 0){	
				
				foreach($terms as $term){			   											
					$term_options[$term->term_id] = $term->name;
				}
				
			}
						
			return $term_options;
			
		}
				
	}
	
}	
		
