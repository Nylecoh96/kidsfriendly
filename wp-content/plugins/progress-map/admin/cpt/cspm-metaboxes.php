<?php
 
if(!defined('ABSPATH')){
    exit; // Exit if accessed directly
}

if( !class_exists( 'CspmMetaboxes' ) ){
	
	class CspmMetaboxes{
		
		private $plugin_path;
		private $plugin_url;
		
		private static $_this;	
		
		public $plugin_settings = array();
		
		/**
		 * The name of the post to which we'll add the metaboxes
		 * @since 1.0 */
		 
		public $object_type;
		
        /**
         * Constructor
         * 
         * @plugin_path string path to metaboxes library
         */
        function __construct($atts = array()){
			
			extract( wp_parse_args( $atts, array(
				'plugin_path' => '', 
				'plugin_url' => '',
				'plugin_settings' => array(),
				'metafield_prefix' => '',
				'object_type' => '',
			)));
             
			self::$_this = $this;       
				           
			$this->plugin_path = $plugin_path;
			$this->plugin_url = $plugin_url;
			
			$this->plugin_settings = $plugin_settings;
			
			$this->object_type = $object_type;

			$this->metafield_prefix = $metafield_prefix;
				
			/**
			 * Load & Initialize Metaboxes */
			 
			$this->cspm_metaboxes();
			
			/**
			 * Call .js and .css files */
			 
			add_filter('cmb2_enqueue_js', array($this, 'cspm_register_scripts'));
			
			/**
			 * Injects the JS script that will change the group title */
			 
			add_action('admin_footer', array($this, 'cspm_change_group_titles_script') );

        }
	
	
		static function this() {
			
			return self::$_this;
		
		}
		
		
		function cspm_register_scripts(){
			
			global $typenow;

			/**
			 * Our custom metaboxes JS & CSS file must be loaded only on our CPT page */
			
			/**
			 * Our custom metaboxes CSS */
			
			wp_register_style('cspm-metabox-css', $this->plugin_url . 'admin/cpt/css/cspm-metabox-style.css');
	
		}
	
	
		/**
		 * Intialize metaboxes classes
		 *
		 * @since 1.0
		 */
		function cspm_metaboxes() {
	
			/**
			 * Include all metaboxes files */
			 
			$metaboxes_path = array(
				'cspm_map_settings_metabox' => 'admin/cpt/metaboxes/cspm-map-settings-metabox.php',
				'cspm_add_locations_metabox' => 'admin/cpt/metaboxes/cspm-add-locations-metabox.php', //@since 3.5
			);
				
				foreach($metaboxes_path as $metabox_file_path){
					if(file_exists($this->plugin_path . $metabox_file_path))
						require_once $this->plugin_path . $metabox_file_path;
				}
				
			/**
			 * Display "Progress Map" Metabox */

			if(class_exists('CspmMetabox')){
				
				$CspmMetabox = new CspmMetabox(array(
					'plugin_path' => $this->plugin_path, 
					'plugin_url' => $this->plugin_url,
					'object_type' => $this->object_type,
					'plugin_settings' => $this->plugin_settings,
					'metafield_prefix' => $this->metafield_prefix,
				));
				
			}
				
			/**
			 * Display "Progress Map: Add locations" Metabox */

			if(class_exists('CspmAddLocationsMetabox')){
				
				$CspmAddLocationsMetabox = new CspmAddLocationsMetabox(array(
					'plugin_path' => $this->plugin_path, 
					'plugin_url' => $this->plugin_url,
					'object_type' => array('post'),
					'plugin_settings' => $this->plugin_settings,
					'metafield_prefix' => $this->metafield_prefix,
				));
				
			}

		}
		
		
		/**
		 * This contains the JS script that will change the group titles to the value of ...
		 * ... the field that has the attribute [data-group-title] 
		 *
		 * @since 1.0 		 
		 */
		function cspm_change_group_titles_script(){ 
						
			global $typenow;
			
			if($typenow === $this->object_type){ ?>
        
				<script type="text/javascript">
	
                    jQuery(document).ready(function($){ 
						
						var metaPrefix = '<?php echo $this->metafield_prefix; ?>';
						
                        var metaboxes = [
							metaPrefix+'_pm_metabox', 
							metaPrefix+'_pmlf_metabox', 
							metaPrefix+'_pmst_metabox',
							metaPrefix+'_pmsl_metabox',
						];
                        
                        for(var i=0; i<metaboxes.length; i++) {
                        
                            var $box = $( document.getElementById( metaboxes[i] ) );
                            
                            var replaceTitles = function() {
                                $box.find( '.cmb-group-title' ).each( function() {
                                    var $this = $( this );
                                    var fieldType = $this.next().find('[data-group-title]').prop('type');
                                    if(fieldType == 'text'){
                                        var txt = $this.next().find( '[data-group-title]' ).val();
                                    }else if(fieldType == 'select-one'){
                                        var selectOptions = $this.next().find( '[data-group-title]' );
                                        var txt = selectOptions.find(':selected').text();								
                                    }
                                    
                                    if ( txt ) {
                                        $this.text( txt );
                                    }
                                });
                            };

                            $box.on( 'cmb2_add_row cmb2_shift_rows_complete', function( evt ) {
                                replaceTitles();
                            });
							
                            replaceTitles();
                            
                        }
		
						/**
						 * by Codespacing | Custom Fix
						 * 
						 * Fix an issue in the CMB extension "Conditionals" that doesn't display ...
						 * ... the first field in the group "Marker categories settings => Marker Image #" when clicking ...
						 * ... on the button "Add new marker image".
						 * ... This code will trigger the selected taxonomy in "Marker categories settings => Taxonomies" ...
						 * ... which will allow "Conditionals" to research for all group fields to display based on the selected ...
						 * ... taxonomy and which will also show the first field ("{taxonomy_name}") in the group that was mistakenly hidden */
						 
						$('.cmb-add-group-row').on('click', function(evt){
							
							var $checked_taxonomy = $("input[name=<?php echo $this->metafield_prefix; ?>_marker_categories_taxonomy]:checked");
							
							var value = $checked_taxonomy.val();
							
							var id = $checked_taxonomy.attr('id');
							
							setTimeout(function(){
								$('input[id='+id+']').trigger('change');
							}, 50);
							
						});
						
						/**
						 * Copy shortcode to clipboard */
						
						$('pre.cspm_copy_to_clipboar').on('click', function(){
							var $temp = $("<input>");
							$("body").append($temp);
							$temp.val($(this).text()).select();
							document.execCommand("copy");
							$temp.remove();
							alert('Copied to clipboard');
						}).css({
							'cursor': 'pointer',
							'font-size': '11px',
							'background': '#f1f1f1',
							'padding': '10px',
							'margin': '10px',
						}).attr('title', 'Click to copy to clipboard');
						
                    });
                    
                </script><?php
				
				/**
		 		 * CMB2 Auto-scroll to new group */
		 
				$this->cspm_cmb_group_autoscroll_js();
				
				/**
				 * Heatmap color gradient generator
				 * @since 5.3 */
				
				$this->cspm_heatmap_gradient_preview();
				
			}
			
		}
			
			
		/**
		 * Re-activate CMB2 Auto-scroll to new group
		 * Note: This feature was removed in CMB2 2.0.3
		 * https://github.com/CMB2/CMB2-Snippet-Library/blob/master/javascript/cmb2-auto-scroll-to-new-group.php
		 *
		 * @since 3.0
		 */
		function cspm_cmb_group_autoscroll_js() {
			
			// If not cmb2 scripts on this page, bail
			if ( ! wp_script_is( 'cmb2-scripts', 'enqueued' ) ) {
				return;
			}
			?>
			<script type="text/javascript">
				window.CMB2 = window.CMB2 || {};
				(function(window, document, $, cmb, undefined){
					'use strict';
					// We'll keep it in the CMB2 object namespace
					cmb.initAutoScrollGroup = function(){
						if(typeof cmb.metabox === 'function'){
							cmb.metabox().find('.cmb-repeatable-group').on( 'cmb2_add_row', cmb.autoScrollGroup );
						}
					};
					cmb.autoScrollGroup = function( evt, row ) {
						var $focus = $(row).find('input:not([type="button"]), textarea, select').first();
						if ( $focus.length ) {
							$( 'html, body' ).animate({
								scrollTop: Math.round( $focus.offset().top - 150 )
							}, 1000);
							$focus.focus();
						}
					};
					$(document).ready( cmb.initAutoScrollGroup );
				})(window, document, jQuery, CMB2);
			</script>
			<?php
			
		}	
		
		
		/**
		 * Generate the heatmap color gardient and preview it 
		 *
		 * @since 5.3
		 */
		function cspm_heatmap_gradient_preview(){
			
			// If not cmb2 scripts on this page, bail
			if ( ! wp_script_is( 'cmb2-scripts', 'enqueued' ) ) {
				return;
			}			
			?>
			<script type="text/javascript">            
            
            jQuery(document).ready(function($){ 
            
				/**
				 * This will generate the heatmap color gradient */
             
				var cspm_preview_gradient = function(color_array, stops){
					
					color_array = color_array.filter(function(el){						
						if(el != '' && el != '#' && el)
							return el;
					});

					if(color_array.length <= 1)
						return;
						
					jQuery('.cspm_heatmap_gradient_preview').empty();	
							
					var custom_gradient = gradstop({
						stops: parseInt(stops),
						inputFormat: 'hex',
						colorArray: color_array
					});	
					
					var container_width = jQuery('.cspm_heatmap_gradient_preview').innerWidth(); 							
					var count_gradient = custom_gradient.length;   
					
					setTimeout(function(){
						jQuery.each(custom_gradient, function(index, color){								
							var style  = 'width:' + (container_width/count_gradient) + 'px;';
								style += 'float:left;';
								style += 'height:100%;';
								style += 'background:' + color + ';';
							jQuery('.cspm_heatmap_gradient_preview').append('<div style="'+style.replace(/\s+/g, '')+'"></div>');
						});
					}, 100);
					
				}           
				 
                setTimeout(function(){
                    
                    /**
                     * Preview on page laod */
                     
                    var color_array = [];
                    
                    var $start_color = jQuery('input[type=text]#_cspm_heatmap_colors_0_start_color');
                    var $middle_color = jQuery('input[type=text]#_cspm_heatmap_colors_0_middle_color');
                    var $end_color = jQuery('input[type=text]#_cspm_heatmap_colors_0_end_color');
                    var $stops = jQuery('input[type=number]#_cspm_heatmap_colors_0_stops');
                    
                    color_array.push($start_color.val());
                        
                    var middle_color_val = $middle_color.val();
                        if(middle_color_val != '') color_array.push(middle_color_val);
                        
                    color_array.push($end_color.val());
                    
                    var stops = jQuery('input[type=number]#_cspm_heatmap_colors_0_stops').val();								
                    
                    cspm_preview_gradient(color_array, stops);
                    
                    /**
                     * Preview on value changed */
                    
					jQuery('input[id^=_cspm_heatmap_colors]').on('input', function(){
							
						var hex_regex = /^#([0-9A-F]{3}){1,2}$/i;
						
						if(jQuery(this).attr('type') == 'text' && hex_regex.test(jQuery(this).val()) === false)
							return;
							
                        color_array = [];
                        
                        var stops = $stops.val();
                        
                        if(stops < 2)
                            return;
                        
                        color_array.push($start_color.val());
                            
                        if(stops > 2){
                            var middle_color_val = $middle_color.val();
                                if(middle_color_val != '') color_array.push(middle_color_val);
                        }
                    
                        color_array.push($end_color.val());
                        
                        cspm_preview_gradient(color_array, stops);

                    });
                    
                    /**
                     * Preview on colorpicker click */
                    
					var colorpicker_btn_handler = 'div[id^=cmb-group-_cspm_heatmap_colors] button.wp-color-result';
					var colorpicker_palette_handler = 'div[id^=cmb-group-_cspm_heatmap_colors] div.iris-palette-container a.iris-palette';
					
                    jQuery(colorpicker_btn_handler + ',' + colorpicker_palette_handler).click(function(){
						
						setTimeout(function(){
													
							color_array = [];
							
							var stops = $stops.val();
							
							if(stops < 2)
								return;
							
							color_array.push($start_color.val());
								
							if(stops > 2){
								var middle_color_val = $middle_color.val();
									if(middle_color_val != '') color_array.push(middle_color_val);
							}
						
							color_array.push($end_color.val());
							
                        	cspm_preview_gradient(color_array, stops);
							
						}, 100);

                    });
                    
                }, 1000);
            
            });
            </script>
            <?php            
			
		}
		
	}
	
}
		
