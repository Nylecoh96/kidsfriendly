<?php
/**
 * Plugin Name: CMB2 Button Field
 * Plugin URI: 
 * Description: Allows runing AJAX functions
 * Version: 1.0
 * Author: Hicham Radi (CodeSpacing)
 * Author URI: http://www.codespacing.com/
 */
 
/**
 * Class CS_CMB2_button_Field */

if( !class_exists( 'CS_CMB2_button_Field' ) ) {
 
	class CS_CMB2_button_Field {
		
	
		/**
		 * Current version number */
		 
		const VERSION = '1.0';
				
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
				
			add_filter('cmb2_render_cs_button', array($this, 'cs_render_button_field'), 10, 5);
			add_filter('cmb2_sanitize_cs_button', array($this, 'cs_sanitize_button_field'), 10, 4);
	
		}
		
	
		/**
		 * Render field
		 *
		 * @since 1.0
		 */
		public function cs_render_button_field( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
			
			$output = '';
			
			/**
			 * Get the field options */
			
			$field_options = $field->args('options');
            
			/**
			 * Field options */
			
			$action = (isset($field_options['action']) && !empty($field_options['action'])) ? esc_attr($field_options['action']) : '';
			
			$messages = (isset($field_options['messages']) && is_array($field_options['messages'])) ? $field_options['messages'] : array();
				$success_msg = (isset($messages['success']) && !empty($messages['success'])) ? esc_attr($messages['success']) : esc_attr__('Done', 'cs_button');
				$error_msg = (isset($messages['error']) && !empty($messages['error'])) ? esc_attr($messages['error']) : esc_attr__('An error has been occured! Please try again later.', 'cs_button');
				$confirm_msg = (isset($messages['confirm']) && !empty($messages['confirm'])) ? esc_attr($messages['confirm']) : esc_attr__('Please confirm this operation!', 'cs_button');
				$loading_msg = (isset($messages['loading']) && !empty($messages['loading'])) ? esc_attr($messages['loading']) : esc_attr__('Loading...', 'cs_button');
			
			$field_name = $field->args('name');
			
			if(!empty($action)){
					
				$output .= '<div class="cs_button_container">';
					$output .= '<a href="#" id="'.$field->args('_name').'" class="cs_button" data-action="'.$action.'" data-success-msg="'.$success_msg.'" data-error-msg="'.$error_msg.'" data-confirm-msg="'.$confirm_msg.'">'.$field_name.'</a>';
				$output .= '</div>';
				
				$output .= '<p class="cmb2-metabox-description" style="margin-top:10px;">';
					$output .= '<span class="cs_loading" id="'.$field->args('_name').'">'.$loading_msg.'<br /></span>';
					$output .= $field->args('desc');
				$output .= '</p>';
			
				$this->cs_enqueue_scripts();
				
			}else $output .= '<strong style="color:red;">You must provide the action name!</strong>';
		
			echo $output;		
		
		}
		
	
		/**
		 * Save uploaded files in custom fields
		 *
		 * @since 1.0
		 */
		public function cs_sanitize_button_field($override_value, $value, $object_id, $field_args){
			return '';
		}
	
		
		/**
		 * Enqueue scripts and styles
		 *
		 * @since 1.0
		 */
		public function cs_enqueue_scripts($atts = array()){
			
			extract( wp_parse_args( $atts, array()));
            
			/**
			 * Custom CSS */
								
			wp_enqueue_style('cs-button-style', $this->plugin_url.'css/style.css', array(), self::VERSION);
				
			/**
			 * Custom JS */
								
			wp_enqueue_script('cs-button-script', $this->plugin_url.'js/script.js', array('jquery'), self::VERSION);
	
		}
		
	}
	$CS_CMB2_button_Field = new CS_CMB2_button_Field();

}