<?php
/**
 * Plugin Name: CMB2 File Field
 * Plugin URI: 
 * Description: 
 * Version: 1.0
 * Author: Hicham Radi (CodeSpacing)
 * Author URI: http://www.codespacing.com/
 */
 
/**
 * Class CS_CMB2_file_Field */

if( !class_exists( 'CS_CMB2_file_Field' ) ) {
    
	class CS_CMB2_file_Field {
		
	
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
				
			add_filter('cmb2_render_cs_file', array($this, 'cs_render_file_field'), 10, 5);
			add_filter('cmb2_sanitize_cs_file', array($this, 'cs_sanitize_file_field'), 10, 4);
	
		}
		
	
		/**
		 * Render field
		 *
		 * @since 1.0
		 */
		public function cs_render_file_field( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
			
			$output = '';
			
			/**
			 * Get the field options */
			
			$field_options = $field->args('options');
	
			/**
			 * Field options */
			
			$multiple = (isset($field_options['multiple']) && $field_options['multiple']) ? 'multiple' : '';
			$accept = (isset($field_options['accept']) && !empty($field_options['accept'])) ? 'accept="'.esc_attr($field_options['accept']).'"' : '';
			$max_files = (isset($field_options['max_files']) && $field_options['max_files']) ? $field_options['max_files'] : 1;
			$max_upload_size = (isset($field_options['max_upload_size']) && $field_options['max_upload_size']) ? $field_options['max_upload_size'] : wp_max_upload_size();
			$upload_file_text = (isset($field_options['upload_file_text']) && $field_options['upload_file_text']) ? $field_options['upload_file_text'] : esc_attr__('Select or Drag a file', 'cspmsl');
			$required = (isset($field_options['required']) && $field_options['required'] !== false) ? 'required="required"' : '';
			
			$field_name = (isset($field_options['multiple']) && $field_options['multiple']) ? $field->args('_name').'[]' : $field->args('_name');
			
			$output .= '<div class="cs_file_input_container">';
				$output .= '<div class="cs_open_browser" id="'.$field->args('_name').'">'.esc_attr($upload_file_text).'</div>';		
				$output .= '<input type="file" name="'.$field_name.'" id="'.$field->args('_name').'" class="cs_file_input" '.$multiple.' '.$accept.' data-max-files="'.$max_files.'" data-max-upload-size="'.apply_filters('cs_file_max_upload_size', $max_upload_size).'" '.$required.' />';
				$output .= '<input type="hidden" name="'.$field->args('_name').'_id" id="'.$field->args('_name').'_id" class="cs_file_id" data-multiple="'.$multiple.'" value="" />';
			$output .= '</div>';
			
			$output .= '<div class="cs_files_preview_area" id="'.$field->args('_name').'"></div>';
			
			$output .= '<p class="cmb2-metabox-description" style="margin-top:10px;">'.$field->args('desc').'</p>';
			
			$this->cs_enqueue_scripts();
		
			echo $output;		
		
		}
		
	
		/**
		 * Save uploaded files in custom fields
		 *
		 * @since 1.0
		 */
		public function cs_sanitize_file_field($override_value, $value, $object_id, $field_args){
			
			if($object_id === 0)
				return;
	
			$field_options = $field_args['options'];
			
			$file_name = $field_args['_name'];
			
			$is_featured_img = (isset($field_options['is_featured_img']) && is_bool($field_options['is_featured_img'])) ? (bool) $field_options['is_featured_img'] : false;
			
			/*echo '<pre>';
			echo $file_name.' value: ';
			print_r($value);
			//echo 'object_id: '.$object_id;
			//print_r($field_args);
			//print_r($_FILES);
			echo '</pre>';*/
					 
			/*if(isset($_FILES[$file_name])){
				
				if(!function_exists('wp_generate_attachment_metadata')){
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					require_once(ABSPATH . 'wp-admin/includes/file.php');
					require_once(ABSPATH . 'wp-admin/includes/media.php');
				}
				
				/**
				 * Upload multiple files * /
				
				if(is_array($_FILES[$file_name]['name'])){
						
					//$multiple_files_array[$file_name] = $this->cs_rearrange_FILES($_FILES[$file_name]);
					$multiple_files_array = $this->cs_rearrange_FILES($_FILES[$file_name]);
	
					foreach($multiple_files_array as $file_name => $files_arrays){
						
						$post_meta_files_array = array();
						
						$files_arrays_size = count($files_arrays);
						
						for($i = 0; $i < $files_arrays_size; $i++){							
								
							$_FILES = array($file_name => $files_arrays[$i]);
							
							if($_FILES[$file_name]['error'] === UPLOAD_ERR_OK){							
								
								$img_id = media_handle_upload($file_name, $object_id);
						
								$img_url = wp_get_attachment_url($img_id);
									
								$post_meta_files_array[$img_id] = esc_url($img_url);
								
							}
								
							/**
							 * Save the images IDs and URLs * /
														
							if($i == ($files_arrays_size - 1)){							
								update_post_meta($object_id, $file_name, $post_meta_files_array);							
										
								/*echo '<pre>';
								echo 'multiple_files_array: ';
								print_r($multiple_files_array[$file_name]);
								echo 'post_meta_files_array: ';
								print_r($post_meta_files_array);
								echo '</pre>';* /
					 
							}
							
						}
						
					}
				
				/**
				 * Upload single file * /
	
				}else{
					
					if($_FILES[$file_name]['error'] === UPLOAD_ERR_OK){							
									
						/**
						 * Upload and resize file (if image) * /
						
						$img_id = media_handle_upload($file_name, $object_id);
						
						/**
						 * Save the image ID and URL * /
												
						update_post_meta($object_id, $file_name, wp_get_attachment_url($img_id));
						update_post_meta($object_id, $file_name.'_id', $img_id);								
					
						/**
						 * Set the uploaded featured image as the post thumbnail * /
				
						if($img_id && $is_featured_img)
							set_post_thumbnail($object_id, $img_id);
						
						/*echo '<pre>';
						echo 'file_name: '.$file_name;
						print_r($_FILES[$file_name]);
						echo '<br>object_id: '.$object_id;
						echo '<br>img_id: '.$img_id;
						echo '<br>is_featured_img: '.$is_featured_img;
						echo '<br>wp_get_attachment_url: '.$img_url;
						echo '</pre>';* /
												
					}
					
				}
				
			}*/		
									
			return $value;
			
		}
			
			
		/**
		 * This will rearrange $_FILES array
		 *
		 * @since 1.0 
		 
		public function cs_rearrange_FILES($file_post){
			
			$file_array = array();
			$file_count = count($file_post['name']);
			$file_keys = array_keys($file_post);
		
			for($i = 0; $i < $file_count; $i++){
				foreach($file_keys as $key){
					$file_array[$i][$key] = $file_post[$key][$i];
				}
			}
		
			return $file_array;
			
		}*/
	
			
		/**
		 * This will resize an image
		 *
		 * @since 1.0
		 
		public function cs_resize_uploaded_image($img_id){
			
			if(empty($img_id))
				return false;
				
			$full_size_img_path = get_attached_file($img_id);
			
			if(file_exists($full_size_img_path))
				$metadata = wp_generate_attachment_metadata($img_id, $full_size_img_path);
				
			if(!empty($metadata))
				wp_update_attachment_metadata($img_id, $metadata);
			
		}*/
		
		/**
		 * Enqueue scripts and styles
		 *
		 * @since 1.0
		 */
		public function cs_enqueue_scripts($atts = array()){
			
			extract( wp_parse_args( $atts, array()));
				
			$wp_localize_script_args = array(
				'exceed_max_files_error' => apply_filters( '__exceed_max_files_error', esc_attr__('You are not allowed to upload more than %s files!', 'cspmsl')),
				'invalid_file_types_error' => apply_filters( '__invalid_file_types_error', esc_attr__('Your files contains one or more invalid file type. Update your selection!', 'cspmsl')),
				'invalid_file_type_error' => apply_filters( '__invalid_file_type_error', esc_attr__('Invalid file type. Update your selection!', 'cspmsl')),
				'invalid_file_type_msg' => apply_filters( '__invalid_file_type_msg', esc_attr__('Invalid file type!', 'cspmsl')),
				'exceed_max_upload_size_error' => apply_filters( '__exceed_max_upload_size_error', esc_attr__('The file exceeds the maximum upload size', 'cspmsl')),
				'exceed_max_upload_size_error_multiple' => apply_filters( '__exceed_max_upload_size_error_multiple', esc_attr__('One or multiple files exceeds the maximum upload size', 'cspmsl')),
			);
			
			/**
			 * Custom CSS */
								
			wp_enqueue_style('cs-file-input-style', plugins_url( 'css/style.css', __FILE__ ), array(), self::VERSION);
				
			/**
			 * Custom JS */
								
			wp_enqueue_script('cs-file-input-script', plugins_url( 'js/script.js', __FILE__ ), array('jquery'), self::VERSION);
				
			wp_localize_script('cs-file-input-script', 'cs_file_field_vars', $wp_localize_script_args);
	
		}
				
	}
	$CS_CMB2_file_Field = new CS_CMB2_file_Field();

}