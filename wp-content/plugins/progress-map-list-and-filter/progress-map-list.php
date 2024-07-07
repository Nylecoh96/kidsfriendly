<?php
/**
 * Plugin Name: Progress Map, List & Filter
 * Plugin URI: https://codecanyon.net/item/progress-map-list-filter-wordpress-plugin/16134134
 * Description: <strong>Progress Map List & Filter</strong> is an extension of <strong>Progress Map Wordpress plugin</strong>. This extension allows to switch "Progress Map" carousel to list & to filter the list items using an advanced search filter tool.
 * Version: 3.7.3
 * Author: Hicham Radi (CodeSpacing)
 * Author URI: https://www.codespacing.com/
 * Text Domain: cspml
 * Domain Path: /languages
 */
 
if(!defined('ABSPATH')){
    exit; // Exit if accessed directly
}

if( !class_exists( 'ProgressMapList' ) ){
	
	class ProgressMapList{
		
		private static $_this;	
		
		public $plugin_version = '3.7.3';
		
		private $plugin_path;		
		private $plugin_url;
			
		public $cspml_plugin_path;
		public $cspml_plugin_url;
		
		public $metafield_prefix; //@since 2.0
		
		public $plugin_settings;
		
		/**
		 * The name of the post to which we'll add the metaboxes
		 * @since 1.0 */
		 
		public $object_type;
				
		function __construct(){	

			if (!class_exists('CSProgressMap'))
				return; 
				
			$CSProgressMap = CSProgressMap::this();
			
			self::$_this = $this;       
			
			$this->plugin_path = $this->cspml_plugin_path = plugin_dir_path( __FILE__ );
			$this->plugin_url = $this->cspml_plugin_url = plugin_dir_url( __FILE__ );

			$this->plugin_settings = $CSProgressMap->shared_plugin_settings;
			
			$this->metafield_prefix = $CSProgressMap->metafield_prefix;
			
			$this->object_type = $CSProgressMap->object_type;
							
			/**
			 * Include and setup our custom post type */
			
			if(file_exists($this->plugin_path . 'cspml-list-filter.php')){
				
				require_once( $this->plugin_path . 'cspml-list-filter.php' );
				
				if(class_exists('CspmListFilter')){
					
					$CspmListFilter = new CspmListFilter(array(
						'init' => true, 
						'plugin_settings' => $this->plugin_settings,
						'metafield_prefix' => $this->metafield_prefix,
					));
					
				}
			
			}
            
		}
		
		
		static function this() {
			
			return self::$_this;
		
		}
		
		
		function cspml_hooks(){
			
			/**
			 * Load plugin textdomain.
			 * @since 2.8 */
			 
			add_action('init', array($this, 'cspml_load_textdomain')); 
			
			/**
			 * Start new Session
			 * Note: This must be called exactely in this place not inside is_admin()! */
			 	
			add_action('init', array($this, 'cspml_start_session'), 1);
			
			if(is_admin()){
			
				add_action( 'admin_notices', array($this, 'cspml_required_plugin_notice') );

				/**
				 * Add custom links to plugin instalation area */
				 
				add_filter( 'plugin_row_meta', array($this, 'cspml_plugin_meta_links'), 10, 2 );
		
				/**
				 * Make sure that this plugin always runs after the main plugin */
				 
				add_action('activated_plugin', array($this, 'cspml_run_this_plugin_last'));

				/**
				 * Include all metaboxes files */
				 
				$metaboxes_path = array(
					'cspml_metabox' => 'metaboxes/cspml-metabox.php',
				);
					
				foreach($metaboxes_path as $metabox_file_path){
					if(file_exists($this->plugin_path . $metabox_file_path))
						require_once $this->plugin_path . $metabox_file_path;
				}
				
				/**
				 * Load the plugin metabox */
	
				add_filter('cmb2_admin_init', array($this, 'cspml_metaboxes'));
				
				/**
				 * Display the plugin metabox within the allowed metaboxes of "Progress Map". */
				
				$type = $this->object_type;
				$metafield_prefix = $this->metafield_prefix;
				
				add_action('current_screen', function() use($type, $metafield_prefix){
					
					if(get_current_screen()->id === $type){
						
						/**
						 * Update the list of allowed metaboxes in "PM" by adding the ID of the plugin metabox to that list */
							 
						add_filter('cspm_normal_high_metaboxes', function($allowed_metaboxes) use($metafield_prefix){
							
							$allowed_metaboxes[] = $metafield_prefix.'_pmlf_metabox';
							
							return $allowed_metaboxes;
							
						});
						
					}
					
				}, PHP_INT_MAX );
				
			}else{
				
				/**
				 * Call .js and .css files */
				 
				add_action('wp_enqueue_scripts', array($this, 'cspml_register_styles'));
				add_action('wp_enqueue_scripts', array($this, 'cspml_register_scripts'));
				
				/**
				 * Declare the name of this extension */
				 
				add_filter('cspm_ext_name', array($this, 'cspml_this_ext_name'), 10, 2);			

			}
		
			/**
			 * Add Images Size */
			 
			if(function_exists('add_image_size'))
				add_image_size( 'cspml-listings-thumbnail', 650, 415, true );
			
			/**
			 * Excecute the below class hooks */
			 
			if(class_exists('CspmListFilter')){
				$CspmListFilter = CspmListFilter::this();
				$CspmListFilter->cspml_hooks();
			}

		}
	
	
		/**
		 * Display "List & Filter" Metabox
		 *
		 * @since 1.0
		 */
		function cspml_metaboxes() {
				
			if(class_exists('CspmlMetabox')){
				
				$CspmlMetabox = new CspmlMetabox(array(
					'plugin_path' => $this->plugin_path, 
					'plugin_url' => $this->plugin_url,
					'object_type' => $this->object_type,
					'plugin_settings' => $this->plugin_settings,
					'metafield_prefix' => $this->metafield_prefix,
				));

				$CspmlMetabox->cspml_list_and_filter_metabox();
				
			}

		}
						
		
		/**
		 * Load plugin text domain
		 *
		 * @since 1.1
		 */
		function cspml_load_textdomain(){
			
			/**
			 * To translate the plugin, create a new folder in "wp-content/languages" ...
			 * ... and name it "cs-progress-map". Inside "cs-progress-map", paste your .mo & .po files.
			 * The plugin will detect the language of your website and display the appropriate language. */
			 
			$domain = 'cspml';
			
			$locale = apply_filters('plugin_locale', get_locale(), $domain);
		
			load_textdomain($domain, WP_LANG_DIR.'/cs-progress-map/'.$domain.'-'.$locale.'.mo');
	
			load_plugin_textdomain($domain, FALSE, $this->plugin_path.'languages/');
			
		}

		
		/**
		 * This will display an admin notice if the main plugin "Progress Map" is not installed 
		 *
		 * @since 1.0
		 * @updated 3.5.1		 
		 */		 
		function cspml_required_plugin_notice() {
			
			$required_version = '5.6.6';
			
			if(!class_exists('CSProgressMap')){
				
				echo '<div class="notice notice-warning"><p>';
					echo 'The add-on <strong>"Progress Map List & Filter"</strong> requires the plugin <strong>"Progress Map" (version '.$required_version.' or upper)</strong>! Please navigate to your downloads page on Codecanyon, download the plugin <strong>"Progress Map Wordpress Plugin"</strong>, then, install and activate it. If you did not yet purchased this plugin, you can <a href="http://codecanyon.net/item/progress-map-wordpress-plugin/5581719?ref=codespacing" target="_blank">buy it from here</a>.';
				echo '</p></div>';
			
			}elseif(class_exists('CSProgressMap')){
											
				$CSProgressMap = CSProgressMap::this();
				
				$reflect = new ReflectionClass($CSProgressMap);
				
				$plugin_version = $reflect->getProperty('plugin_version');
				
				if(($plugin_version->isPublic() && version_compare($CSProgressMap->plugin_version, $required_version, '<')) || !$plugin_version->isPublic()){
				
					echo '<div class="notice notice-warning"><p>';
						echo 'The version <strong>'.$this->plugin_version.'</strong> of <strong>"Progress Map List & Filter"</strong> requires <strong>"Progress Map"</strong> version <strong>'.$required_version.' or upper</strong>. Please navigate to your downloads page on <strong>Codecanyon</strong> and download the latest version of <strong>"Progress Map"</strong>!';						
					echo '</p></div>';
			
				}
				
			}
            
		}
		
		
		/**
		 * With this function, we'll make sure that this plugin always runs after the main plugin
		 *
		 * @since 1.0 
		 */			 
		function cspml_run_this_plugin_last() {
			
			$wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', $this->plugin_path."/$2", __FILE__);
			
			$this_plugin = plugin_basename(trim($wp_path_to_this_file));
			
			$active_plugins = get_option('active_plugins');
			
			$this_plugin_key = array_search($this_plugin, $active_plugins);
			
			if ($this_plugin_key !== false) {
				
				array_splice($active_plugins, $this_plugin_key, 1);
				
				$active_plugins[] = $this_plugin;
				
				update_option('active_plugins', $active_plugins);
			}
			
		}
			
	
		/**
		 * Add plugin site link to plugin instalation area
		 *
		 * @since 1.0
		 */
		function cspml_plugin_meta_links($links, $file){
		 
			$plugin = plugin_basename(__FILE__);
		 
			/**
			 * Created by link */
			 
			if ( $file == $plugin ) {
				return array_merge(
					(array) $links,
					array(
						'documentation' => '<a target="_blank" href="https://www.docs.progress-map.com/list-and-filter-user-guide/">'.esc_html__('Documentation', 'cspml').'</a>'
					)
				);
			}
			
			return $links;
		 
		}
		
		
		/**
		 * Start Session
		 *
		 * @since 1.0
         * @updated 3.5.4 | 3.6
		 */
		function cspml_start_session() {			
            
			if(!session_id()) 
                @session_start();
            
            /**
             * Close the session to fix an issue with REST API
             * @since 3.5.4 
             * @edited 3.6 */
            
            if(!wp_doing_ajax() || isset($_GET['paginate']))
                session_write_close();

		}
	

		/**
		 * Register CSS files
		 * 
		 * @since 1.0
		 */
		function cspml_register_styles(){

			if (!class_exists('CSProgressMap'))
				return; 
			
			$CSProgressMap = CSProgressMap::this();
			
			do_action('cspml_before_register_style');
			
		$min_prefix = '';
        if (isset($this->plugin_settings['combine_files']) && $this->plugin_settings['combine_files'] == 'separate') {
            $min_prefix = '';
        } else {
            $min_prefix = '.min';
        }


			/**
			 * Selectize
			 * @since 1.0 */
			 
			wp_register_style('jquery-selectize', $this->plugin_url .'assets/css/selectize/selectize'.$min_prefix.'.css', array(), $this->plugin_version);	
			wp_register_style('jquery-selectize-skin', $this->plugin_url .'assets/css/selectize/selectize.bootstrap3'.$min_prefix.'.css', array(), $this->plugin_version);	
						
			/**
			 * ion-Check Radio
			 * @since 1.0 */
			 
			wp_register_style('jquery-ion-check-radio', $this->plugin_url .'assets/css/ion.checkRadio/ion.checkRadio'.$min_prefix.'.css', array(), $this->plugin_version);	
			wp_register_style('jquery-ion-check-radio-skin', $this->plugin_url .'assets/css/ion.checkRadio/ion.checkRadio.html5'.$min_prefix.'.css', array(), $this->plugin_version);	
			
			/**
			 * Spinner
			 * @since 1.0 */
			 
			wp_register_style('jquery-input-spinner', $this->plugin_url .'assets/css/bootstrap-spinner/bootstrap-spinner'.$min_prefix.'.css', array(), $this->plugin_version);		
			
			/**
			 * Hover
			 * @since 1.0 */
			 
			wp_register_style('hover', $this->plugin_url .'assets/css/hover/hover'.$min_prefix.'.css', array(), $this->plugin_version);			
			
			/**
			 * Datepicker 
			 * @since 2.1 */
			
			wp_register_style('jquery-fengyuan-datepicker', $this->plugin_url .'assets/css/datepicker/datepicker'.$min_prefix.'.css', array(), $this->plugin_version);
			
			/**
			 * Date Range Picker 
			 * @since 2.4 */
			
			wp_register_style('jquery-liu-daterangepicker', $this->plugin_url .'assets/css/dateRangePicker/daterangepicker'.$min_prefix.'.css', array(), $this->plugin_version);
			
			/**
			 * Progress Map List Style
			 * @since 1.0 */
						
			wp_register_style('cspml-style', $this->plugin_url .'assets/css/main/style'.$min_prefix.'.css', array(), $this->plugin_version);		
			
			do_action('cspml_after_register_style');
						
		}	
		
		
		/**
		 * Register JS files
		 *
		 * @since 1.0
		 */
		function cspml_register_scripts(){		

			if (!class_exists('CSProgressMap'))
				return; 
			
			$CSProgressMap = CSProgressMap::this();
			
			$wp_localize_script_args = array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'plugin_url' => $this->plugin_url,
			);
			
			do_action('cspml_before_register_script');
								
			$min_prefix = '';
            if (isset($this->plugin_settings['combine_files']) && $this->plugin_settings['combine_files'] == 'separate') {
                $min_prefix = '';
            } else {
                $min_prefix = '.min';
            }



			/**
			 * Selectize
			 * @since 1.0 */
			 
			wp_register_script('jquery-selectize', $this->plugin_url .'assets/js/selectize/selectize'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);				
		
			/**
			 * ion-Check Radio
			 * @since 1.0 */
			 
			wp_register_script('jquery-ion-check-radio', $this->plugin_url .'assets/js/ion.checkRadio/ion.checkRadio'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);		
			
			/**
			 * Spinner
			 * @since 1.0 */
			 
			wp_register_script('jquery-input-spinner', $this->plugin_url .'assets/js/bootstrap-spinner/jquery.spinner'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);
			
			/**
			 * Datepicker 
			 * @since 2.1 */
			
			wp_register_script('jquery-fengyuan-datepicker', $this->plugin_url .'assets/js/datepicker/datepicker'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);
			
			$datepicker_ISO_languages = array(
				'ca-ES', 
				'de-DE', 
				'en-GB', 
				'en-US', 
				'es-ES', 
				'fr-FR', 
				'nl-NL', 
				'pt-BR', 
				'sv-SE', 
				'tr-TR', 
				'zh-CN'
			);
			
			foreach($datepicker_ISO_languages as $ISO_language)
				wp_register_script('jquery-fengyuan-datepicker_i18n_'.$ISO_language, $this->plugin_url .'assets/js/datepicker/datepicker-i18n/datepicker.'.$ISO_language.'.js', array( 'jquery' ), $this->plugin_version, true);
			
			/**
			 * Moment.js
			 * @since 2.4 */
			
			wp_register_script('moment-js', $this->plugin_url .'assets/js/moment/moment'.$min_prefix.'.js', array(), $this->plugin_version, true);
			
			/**
			 * Date Range Picker 
			 * @since 2.4 */
			
			wp_register_script('jquery-liu-daterangepicker', $this->plugin_url .'assets/js/dateRangePicker/jquery.daterangepicker'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);

			/**
			 * jQuery Mask
			 * @since 3.2  */
			
			wp_register_script('jquery-mask', $this->plugin_url .'assets/js/jquery-mask/jquery.mask'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);
			
			/**
			 * Price format
			 * @since 3.2 */
			
			wp_register_script('jquery-priceformat', $this->plugin_url .'assets/js/price-format/jquery.priceformat'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);			
			
			/**
			 * Progress Map List Custom Functions
			 * @since 1.0 */
		 
			wp_register_script('cspml-script', $this->plugin_url .'assets/js/main/progress-map-list'.$min_prefix.'.js', array( 'jquery' ), $this->plugin_version, true);			
						
			wp_localize_script('cspml-script', 'cspml_vars', $wp_localize_script_args);
			
			do_action('cspml_after_register_script');
									
		}
	
				
		/**
		 * Declare the name of this extension 
		 * 
		 * @since 1.0		 
		 */				 
		function cspml_this_ext_name($val, $atts = array()){
			
			extract( wp_parse_args( $atts, array() ) );
			
			if(isset($list_ext) && $list_ext == 'yes')
				return 'cspml_list';
				
			else return $val;
				
		}

	}
	
}	

if(class_exists('ProgressMapList')){
	
	$ProgressMapList = new ProgressMapList();
	$ProgressMapList->cspml_hooks();
	
}
