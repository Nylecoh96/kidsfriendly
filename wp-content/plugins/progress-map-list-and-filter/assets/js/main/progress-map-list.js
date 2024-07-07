/* @Version 3.7.3 */

/**
 * Ajax Pagination
 *
 * @since 1.0
 */
function cspml_ajax_pagination(){
	
    jQuery(document).on('click', 'div[class^=cspml_pagination_] ul li a', function(e){ // @edited 3.5.3 | check when pagination link is clicked and stop its action.         
		
		e.preventDefault(); /* Prevent going to href link */
		
		var map_id = jQuery(this).closest('li').attr('data-map-id');	
		
		/**
		 * Get initial post IDs and the count
		 * @since 3.4.3 */
		 
		var init_post_ids = jQuery('div.cspml_listings_area_'+map_id+' input[name=init_post_ids]').val(); //@since 3.4.3
		var count_init_post_ids = jQuery('div.cspml_listings_area_'+map_id+' input[name=count_init_post_ids]').val(); //@since 3.4.3

		/**
		 * Get the current view to start loading listings from it */
		 
		var current_view = jQuery('div[id^='+map_id+'_listing_item_]').attr('data-view');

		/**
		 * Get the sort value to start sorting listings from there */
		 
		var current_sort_html = jQuery('ul.cspml_sort_list[data-map-id='+map_id+'] li.cspml_active').html();
		var current_sort = jQuery('ul.cspml_sort_list[data-map-id='+map_id+'] li.cspml_active').attr('data-sort');
		var current_sort_order = jQuery('ul.cspml_sort_list[data-map-id='+map_id+'] li.cspml_active').attr('data-order');
		
		var link = jQuery(this).attr('href'); 
		
		if(link.indexOf('paginate=true') === -1){
			link = link + '?paginate=true';
		} // @since 3.5 | Make sure to add "paginate" attribute to the pagination URLs especially the button "1". This will fix an issue where pagination is ignored when returning to first page.
		
		var wrapper_selector = 'div.cspml_listings_area_'+map_id;
		
		if(jQuery(wrapper_selector).length){
						
			jQuery('html').animate({ //@edited 3.5
				scrollTop: jQuery('div.cspml_list_and_filter_container[data-map-id='+map_id+']').offset().top-200 //@edited 3.5
			},function(){		
				var $listing_items_container = jQuery('.cspml_listing_items_container[data-map-id='+map_id+']'); //@since 3.5
				if($listing_items_container.length && typeof $listing_items_container.mCustomScrollbar === 'function'){
					$listing_items_container.mCustomScrollbar('scrollTo', 'top');
				} //@since 3.5
				cspml_show_listings_animation(map_id);
			});	
			
		}else cspml_show_listings_animation(map_id);
		
		jQuery(wrapper_selector).load(link + ' ' + wrapper_selector, function(response, status, xhr){	
				
			/** 
			 * Rewrite the initial post IDs and the count hidden fields to fix an issue that prevents reseting ...
			 * ... the list items when navigating throught pages using the pagination.
			 * Issue: After paginating, the initial post IDs variable [$init_post_ids] at "cspml-list-filter.php" gets ... 
			 * ... rewriting by the value of the variable [$post_ids]
			 * @since 3.4.3 */
			 
			jQuery('div.cspml_listings_area_'+map_id+' input[name=init_post_ids]').val(init_post_ids); //@since 3.4.3
			jQuery('div.cspml_listings_area_'+map_id+' input[name=count_init_post_ids]').val(count_init_post_ids); //@since 3.4.3
			
			/**
			 * Loading the results */
			 
			if(status == "error"){
				
				var msg = "An error has been occurred. Please try again later: ";
				
				alert(msg + xhr.status + " " + xhr.statusText);
			
			}else if(status == "success"){
				
				/**
				 * Remove the parents of the set of matched elements from the DOM, leaving the matched elements in their place */
				 
				jQuery(wrapper_selector).children(wrapper_selector).unwrap();
								
				if(current_view == "grid")
					cspml_grid_view_classes(map_id);
				else cspml_list_view_classes(map_id);
							
				jQuery('div.cspml_sort_list_container[data-map-id='+map_id+'] span.cspml_sort_val').empty().html(current_sort_html);
				
				jQuery('li[data-map-id='+map_id+'][data-sort='+current_sort+'][data-order='+current_sort_order+']').addClass('cspml_active');
				
				cspml_hide_listings_animation(map_id);
				
			}
			
			return false;
			
		});
		
	});	
		
}

function cspml_filter_listings(map_id, posts_to_retrieve, save_session, init_posts_count, reset_list, request_type){

	var shortcode_page_id = jQuery('div.cspml_listings_area_'+map_id+' input[name=shortcode_page_id]').val();
	var page_template = jQuery('div.cspml_listings_area_'+map_id+' input[name=page_template]').val();
	var template_tax_query = jQuery('div.cspml_listings_area_'+map_id+' input[name=template_tax_query]').val();
	var init_post_ids = jQuery('div.cspml_listings_area_'+map_id+' input[name=init_post_ids]').val();
	var divider = jQuery('div.cspml_listings_area_'+map_id+' input[name=divider]').val();
	var show_listings = jQuery('div.cspml_listings_area_'+map_id+' input[name=show_listings]').val();
	var optional_latlng = jQuery('div.cspml_listings_area_'+map_id+' input[name=optional_latlng]').val();
	var current_view = jQuery('div[id^='+map_id+'_listing_item_]').attr('data-view');
	var paginate_position = jQuery('div.cspml_listings_area_'+map_id).attr('data-paginate-position');
	var paginate_align = jQuery('div.cspml_listings_area_'+map_id).attr('data-paginate-align');
	var posts_per_page = jQuery('div.cspml_listings_area_'+map_id).attr('data-posts-per-page');
	
	var map_type = jQuery('div.cspml_listings_area_'+map_id).attr('data-map-type'); //@since 2.3
	var shortcode_page_link = jQuery('div.cspml_listings_area_'+map_id+' input[name=shortcode_page_link]').val(); //@since 2.7

	/**
	 * Get the sort value to start sorting listings from it */
	 
	var current_sort_html = jQuery('ul.cspml_sort_list[data-map-id='+map_id+'] li.cspml_active').html();
	var current_sort = jQuery('ul.cspml_sort_list[data-map-id='+map_id+'] li.cspml_active').attr('data-sort');
	var current_sort_order = jQuery('ul.cspml_sort_list[data-map-id='+map_id+'] li.cspml_active').attr('data-order');
		
	var request_object = {
		action: 'cspml_listings_html',
		map_id: map_id,
		shortcode_page_id: shortcode_page_id,
		page_template: page_template,
		template_tax_query: template_tax_query,
		divider: divider,
		ajax_call: true,
		current_view: current_view,
		paginate_position: paginate_position,
		paginate_align: paginate_align,
		posts_per_page: posts_per_page,
		optional_latlng: optional_latlng,
		map_settings: progress_map_vars.map_script_args[map_id]['map_settings'], //@since 2.0	
		request_type: request_type, //@since 2.3	
		shortcode_page_link: shortcode_page_link, //@since 2.7
		init_post_ids: init_post_ids,
		post_ids: posts_to_retrieve,
	};
						  
	if(save_session) request_object.save_session = true;
	if(reset_list) request_object.reset_list = true;

	jQuery.post(
		cspml_vars.ajax_url,
		request_object,
		function(data){

			if(init_posts_count == ''){
				
				var nbr_of_items = (init_post_ids != '') ? posts_to_retrieve.length : '0';
			
			/**
			 * Make sure to set the posts count to 0 on when reset ...
			 * ... but only when it's a search map
			 * @since 2.3 */
			 
			}else if(reset_list && map_type != 'normal_map'){
				
				var nbr_of_items = '0';
				
			}else var nbr_of_items = init_posts_count;
			
			if(show_listings == 'true'){
								
				jQuery('div.cspml_listings_area_'+map_id).html(data);
				jQuery('span.cspml_the_count_'+map_id).empty().html(nbr_of_items);					
				jQuery('div.cspml_sort_list_container[data-map-id='+map_id+'] span.cspml_sort_val').empty().html(current_sort_html);
				jQuery('li[data-map-id='+map_id+'][data-sort='+current_sort+'][data-order='+current_sort_order+']').addClass('cspml_active');			
				if(current_view == "grid") cspml_grid_view_classes(map_id);
				cspml_hide_listings_animation(map_id);	
			
			}
			
		}
	);	
				
}

function cspml_show_listings_animation(map_id){
	
	jQuery('div#cspml_loading_container_'+map_id).fadeIn();
	
	jQuery('div.cspml_listings_area_'+map_id+' div.cspml_item_holder').removeClass('cspm_animated fadeIn').animate({'opacity':'0.5'});
	
	jQuery('div.cspml_transparent_layer_'+map_id).show();
	
}

function cspml_hide_listings_animation(map_id){
	
	jQuery('div.cspml_listings_area_'+map_id+' div.cspml_item_holder').addClass('cspm_animated fadeIn');
	
	jQuery('div#cspml_loading_container_'+map_id).fadeOut();
	
	jQuery('div.cspml_transparent_layer_'+map_id).hide();
	
	/**
	 * Adjust the height of the listings content to the image height (list view only) */
	
	setTimeout(function(){
		cspml_adjust_list_content_height(map_id);
	}, 500);
	
}	

/**
 * Prepare all the CSS classes the list view will need ...
 * ... and replace grid view classes with list view classes
 *
 * @since 1.0
 * @updated 2.6
 */
function cspml_list_view_classes(map_id){
			
	jQuery('div.cspml_options_bar_'+map_id+' div.list_view a').removeClass('enabled');
	
	jQuery('div.cspml_options_bar_'+map_id+' div.grid_view a').addClass('enabled');
		
	var grid_cols = jQuery('div#cspml_listings_container[data-map-id='+map_id+']').attr('data-grid-cols');	//cspml_vars.grid_cols;
	
	if(grid_cols == 'cols1'){
		
		var grid_classes = 'cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12';
		
	}else if(grid_cols == 'cols2'){
		
		var grid_classes = 'cspm-col-lg-6 cspm-col-md-6 cspm-col-sm-6 cspm-col-xs-12';
		
	}else if(grid_cols == 'cols4'){
		
		var grid_classes = 'cspm-col-lg-3 cspm-col-md-3 cspm-col-sm-6 cspm-col-xs-12';
	
	}else if(grid_cols == 'cols6'){
		
		var grid_classes = 'cspm-col-lg-2 cspm-col-md-2 cspm-col-sm-6 cspm-col-xs-12';
	
	}else var grid_classes = 'cspm-col-lg-4 cspm-col-md-4 cspm-col-sm-6 cspm-col-xs-12';
			
	jQuery('div[id^='+map_id+'_listing_item_]').addClass('cspm_animated fadeIn');
	
	jQuery('div[id^='+map_id+'_listing_item_]').attr('data-view', 'list');
	
	jQuery('div[id^='+map_id+'_listing_item_]').removeClass(grid_classes).addClass('cspm-row');
	
	jQuery('div#list_view_holder_'+map_id).addClass('cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12');
	
	if(jQuery('div[id^='+map_id+'_listing_item_] .cspml_thumb_container').is(':visible')){ //@since 2.6
		jQuery('div[id^='+map_id+'_listing_item_] .cspml_thumb_container').removeClass('cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12').addClass('cspm-col-lg-5 cspm-col-md-5 cspm-col-sm-5 cspm-col-xs-12');
	}
	
	if(jQuery('div[id^='+map_id+'_listing_item_] .cspml_thumb_container').is(':visible')){ //@since 2.6
		jQuery('div[id^='+map_id+'_listing_item_] div.cspml_details_container').removeClass('cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12').addClass('cspm-col-lg-7 cspm-col-md-7 cspm-col-sm-7 cspm-col-xs-12').attr('data-view', 'list');
	}else jQuery('div[id^='+map_id+'_listing_item_] div.cspml_details_container').attr('data-view', 'list');
	
	jQuery('div[id^='+map_id+'_listing_item_] div.cspml_details_container .cspml_details_content').removeClass('grid');
	
	setTimeout(function(){
		jQuery('div[id^='+map_id+'_listing_item_]').removeClass('cspm_animated fadeIn');	
	}, 2000);
	
}

/**
 * Prepare all the CSS classes the grid view will need ...
 * ... and replace list view classes with grid view classes
 *
 * @since 1.0
 * @updated 2.6
 */
function cspml_grid_view_classes(map_id){
			
	jQuery('div.cspml_options_bar_'+map_id+' div.grid_view a').removeClass('enabled');
	
	jQuery('div.cspml_options_bar_'+map_id+' div.list_view a').addClass('enabled');
		
	var grid_cols = jQuery('div#cspml_listings_container[data-map-id='+map_id+']').attr('data-grid-cols');	//cspml_vars.grid_cols;
	
	if(grid_cols == 'cols1'){
		
		var grid_classes = 'cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12';
		
	}else if(grid_cols == 'cols2'){
		
		var grid_classes = 'cspm-col-lg-6 cspm-col-md-6 cspm-col-sm-6 cspm-col-xs-12';
		
	}else if(grid_cols == 'cols4'){
		
		var grid_classes = 'cspm-col-lg-3 cspm-col-md-3 cspm-col-sm-6 cspm-col-xs-12';
	
	}else if(grid_cols == 'cols6'){
		
		var grid_classes = 'cspm-col-lg-2 cspm-col-md-2 cspm-col-sm-6 cspm-col-xs-12';
	
	}else var grid_classes = 'cspm-col-lg-4 cspm-col-md-4 cspm-col-sm-6 cspm-col-xs-12';
			
	jQuery('div[id^='+map_id+'_listing_item_]').addClass('cspm_animated fadeIn');
	
	jQuery('div[id^='+map_id+'_listing_item_]').attr('data-view', 'grid');		
	
	jQuery('div[id^='+map_id+'_listing_item_]').removeClass('cspm-row').addClass(grid_classes);
	
	jQuery('div#list_view_holder_'+map_id).removeClass('cspm-col-lg-12 cspm-col-xs-12 cspm-col-sm-12 cspm-col-md-12');
	
	if(jQuery('div[id^='+map_id+'_listing_item_] .cspml_thumb_container').is(':visible')){ //@since 2.6
		jQuery('div[id^='+map_id+'_listing_item_] .cspml_thumb_container').removeClass('cspm-col-lg-5 cspm-col-md-5 cspm-col-sm-5 cspm-col-xs-12').addClass('cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12');
	}
			
	if(jQuery('div[id^='+map_id+'_listing_item_] .cspml_thumb_container').is(':visible')){ //@since 2.6		
		jQuery('div[id^='+map_id+'_listing_item_] div.cspml_details_container').removeClass('cspm-col-lg-7 cspm-col-md-7 cspm-col-sm-7 cspm-col-xs-12').addClass('cspm-col-lg-12 cspm-col-md-12 cspm-col-sm-12 cspm-col-xs-12').attr('data-view', 'grid');
	}else jQuery('div[id^='+map_id+'_listing_item_] div.cspml_details_container').attr('data-view', 'grid');
	
	jQuery('div[id^='+map_id+'_listing_item_] div.cspml_details_container .cspml_details_content').addClass('grid');
	
	setTimeout(function(){
		jQuery('div[id^='+map_id+'_listing_item_]').removeClass('cspm_animated fadeIn');
	}, 2000);
		
}

		
/**
 * Make sure to adjust the hight of the listings content to the image height (list view only) 
 *
 * @since 1.0
 * @updated 2.6 | 3.5
 */		
function cspml_adjust_list_content_height(map_id){

	if(map_id !== null){
		
		if(jQuery('div.cspml_thumb_container[data-map-id='+map_id+']').is(':visible')){
			
			var thumb_height = jQuery('div.cspml_thumb_container[data-map-id='+map_id+']').innerHeight();
			var title_container_height = jQuery('div.cspml_details_title[data-map-id='+map_id+']').innerHeight();
			var content_height = thumb_height - title_container_height - 40;
			
			var data_view = jQuery('div.cspml_details_container[data-map-id='+map_id+']').attr('data-view');
			
			if(data_view == 'list'){
				jQuery('div.cspml_details_container[data-map-id='+map_id+'][data-view=list]').attr('style', 'height:'+thumb_height+'px;');	
				jQuery('div.cspml_details_container[data-map-id='+map_id+'][data-view=list] .cspml_details_content').attr('style', 'height:'+content_height+'px;');
			}else{
				jQuery('div.cspml_details_container[data-map-id='+map_id+']').removeAttr('style');
				jQuery('div.cspml_details_container[data-map-id='+map_id+'] .cspml_details_content').removeAttr('style');
			}
			
		}
		
	}else{
		
		jQuery('div[class^=cspml_listings_area_]').each(function(index){
			
			var map_id = jQuery(this).attr('data-map-id');

			if(jQuery('div.cspml_thumb_container[data-map-id='+map_id+']').is(':visible')){
				
				var thumb_height = jQuery('div.cspml_thumb_container[data-map-id='+map_id+']').innerHeight();
				var title_container_height = jQuery('div.cspml_details_title[data-map-id='+map_id+']').innerHeight();
				var content_height = thumb_height - title_container_height - 40;
				
				var data_view = jQuery('div.cspml_details_container[data-map-id='+map_id+']').attr('data-view');

				if(data_view == 'list'){
					jQuery('div.cspml_details_container[data-map-id='+map_id+'][data-view=list]').attr('style', 'height:'+thumb_height+'px;');	
					jQuery('div.cspml_details_container[data-map-id='+map_id+'][data-view=list] .cspml_details_content').attr('style', 'height:'+content_height+'px;');
				}else{
					jQuery('div.cspml_details_container[data-map-id='+map_id+']').removeAttr('style');
					jQuery('div.cspml_details_container[data-map-id='+map_id+'] .cspml_details_content').removeAttr('style');
				}
			
			}
			
		});
		
	}

}


/**
 * This will add hover/active style to an item in the list
 *
 * @since 1.0
 * @updated 2.2 [added option to disable "scrollTO"]
 * @updated 3.5
 */
function cspml_animate_list_item(map_id, post_id){

	var list_item_class_name = jQuery('div.cspml_item_holder[data-map-id='+map_id+'][data-post-id='+post_id+']'); //@since 3.5
	var list_item_target = jQuery(list_item_class_name);
	
	var scroll_to_item = list_item_target.attr('data-attr-scrollto'); //@since 2.2
	
	jQuery('div.cspml_item_holder[data-map-id='+map_id+'] .cspml_item').removeClass('cspml_active_item');
		
	if(scroll_to_item == 'yes'){
		
		if(list_item_target.length){

			var $listing_items_container = jQuery('.cspml_listing_items_container[data-map-id='+map_id+']');

			if($listing_items_container.length 
			&& typeof $listing_items_container.mCustomScrollbar === 'function'
			&& $listing_items_container.attr('data-scrollable') == 'yes'){
				
				var list_layout = $listing_items_container.attr('data-list-layout');
				
				if(list_layout == 'vertical'){
					$listing_items_container.mCustomScrollbar('scrollTo', list_item_class_name);		
					setTimeout(function(){
						jQuery('html').animate({
							scrollTop: jQuery('div.cspml_list_and_filter_container[data-map-id='+map_id+']').offset().top-200						
						},function(){		
							jQuery('html').animate({
								scrollTop: list_item_target.offset().top
							},function(){	
								setTimeout(function(){
									jQuery('div.cspml_item_holder[data-map-id='+map_id+'][data-post-id='+post_id+'] .cspml_item').addClass('cspml_active_item');
								}, 100);
							});
						});	
					}, 500);
				}else{
					jQuery('html').animate({
						scrollTop: jQuery('div.cspml_list_and_filter_container[data-map-id='+map_id+']').offset().top-200						
					},function(){
						$listing_items_container.mCustomScrollbar('scrollTo', list_item_class_name);		
						setTimeout(function(){
							jQuery('div.cspml_item_holder[data-map-id='+map_id+'][data-post-id='+post_id+'] .cspml_item').addClass('cspml_active_item');
						}, 100);
					});	
				}

			}else{
						
				jQuery('html').animate({
					scrollTop: list_item_target.offset().top-100
				},function(){		
					setTimeout(function(){
						jQuery('div.cspml_item_holder[data-map-id='+map_id+'][data-post-id='+post_id+'] .cspml_item').addClass('cspml_active_item');
					}, 100);
				});	
			
			}
			
		}
	
	}else jQuery('div.cspml_item_holder[data-map-id='+map_id+'][data-post-id='+post_id+'] .cspml_item').addClass('cspml_active_item');
	
}


/**
 * Reset filter form input
 *
 * @since 1.0
 * @updated 2.4
 */
function cspml_reset_filter_fields(map_id, $select){
	
	/**
	 * Reset Checkboxes and Radios */
	 	 	 
	jQuery('form.cspml_filter_form[data-map-id='+map_id+'] input[type=radio]:checked, form.cspml_filter_form[data-map-id='+map_id+'] input[type=checkbox]:checked').each(function() {
		jQuery(this).prop("checked", false).trigger("change");
		jQuery(this).trigger("stateChanged");	
	});
	
	/**
	 * Reset spinners */
	 
	jQuery('form.cspml_filter_form[data-map-id='+map_id+'] input[type=text]').each(function() {
		jQuery(this).val('');
	});
	
	/**
	 * Reset Sliders
	 * @edited 3.4.7 */
	 
	var cspml_filter_slider = jQuery('form.cspml_filter_form[data-map-id='+map_id+'] input.cspml_fs_slider_range');
	if(typeof cspml_filter_slider !== 'undefined' && cspml_filter_slider.length > 0){
		var cspml_slider_data = cspml_filter_slider.data('ionRangeSlider');
		if(typeof cspml_slider_data.reset === 'function')
			cspml_slider_data.reset();
	}
	
	/**
	 * Reset Select boxes (selectize) */
	
	var $select = jQuery('select.cspml_fs_selectize[data-map-id='+map_id+']');
	 
	for(var i=0; i<$select.length; i++){
		var current = $select[i].selectize;
		current.clear();
	}
	
	/**
	 * Clear Range datepicker
	 * @since 2.4 */
	
	jQuery('span[id^=clear_date_range_][data-map-id='+map_id+']').trigger('click');
			
}

(function($){	
    
    'use strict';
	
	/**
	 * Prevent conflict with other Datepicker plugins
	 * @since 2.1 */
	
	if(typeof $.fn.datepicker !== 'undefined'){
		$.fn.cspmSyncDatepicker = $.fn.datepicker.noConflict();
	}
		
	//if (!$.fn.cspmlDatepicker && $.fn.datepicker && $.fn.datepicker.noConflict) {
		//var datepicker = $.fn.datepicker.noConflict();
		//$.fn.cspmlDatepicker = datepicker;
	//}else $.fn.cspmlDatepicker = $.fn.datepicker.noConflict();
		
	/**
	 * Customize Checkboxes and Radios button */
	 
	$("div.cspml_fs_item_container input[type='radio'], div.cspml_fs_item_container input[type='checkbox']").ionCheckRadio();
	
	
	/**
	 * Customize the select box */
	
	$('select.cspml_fs_selectize').selectize();
	
	
	/**
	 * Customize the slider text box
	 * @edited 3.5 */
	 
	var faceted_search_slider = $('input.cspml_fs_slider_range').ionRangeSlider({
		grid: false,
		skin: 'round', //@since 3.5
		prettify_enabled: true,
		keyboard: true,
		decorate_both: false,
		force_edges: true,
	});


	/**
	 * Custom filter & list scrollbar
	 * @since 3.5 */
	
	var mCustomScrollbar_options = {
		autoHideScrollbar: false,
		contentTouchScroll: true,
		live: true,
		mouseWheel:{
			enable: true,
			preventDefault: true,
		},
		keyboard:{ 
			enable: true,
		},
		theme: 'dark-2'
	}
	
	$('div.cspml_fs_container').each(function(index){	
		var map_id = $(this).attr('data-map-id');
		var scrollable = $(this).attr('data-scrollable');
		if(scrollable == 'yes'){
			$(this).mCustomScrollbar("destroy");
			$(this).mCustomScrollbar(mCustomScrollbar_options);	
		}
	});
	
	$('div.cspml_listing_items_container').each(function(index){
		var map_id = $(this).attr('data-map-id');
		var scrollable = $(this).attr('data-scrollable');
		if(scrollable == 'yes'){
			$(this).mCustomScrollbar("destroy");
			$(this).mCustomScrollbar(mCustomScrollbar_options);		
		}
	});

	
	/**
	 * Customize the text box to the number field */
	 
	$('input[type=text].cspml_fs_number_field').spinner();
	
	setTimeout(function(){
		$.each($('a.cspml_reset_spinner'), function(){	
			$(this).trigger('click');
		});
	}, 100);
	
	
	/**
	 * Call pagination function */
	 
	cspml_ajax_pagination();


	/**
	 * Adjust the height of the listings content to the image height (list view only) */
	
	cspml_adjust_list_content_height(null);
	
	
	/**
	 * Toggle the options list in the listings faceted search */
	 
    $(document).on('click', 'div.cspml_fs_label span.cspml_toggle_btn', function(){ //@edited 3.5.3
		
		var map_id = $(this).attr('data-map-id');
		var field_name = $(this).attr('data-field-name');
		var display_location = $(this).attr('data-display-location');

		$('div.cspml_fs_options_list[data-map-id='+map_id+'][data-field-name='+field_name+'][data-display-location='+display_location+']').slideToggle('fast');
		
	});
	
	
	/**
	 * Set the value of the field "spinner" to ANY */
	 	
    $(document).on('click', 'a.cspml_reset_spinner', function(){ //@edited 3.5.3
		
		var map_id = $(this).attr('data-map-id');
		var field_name = $(this).attr('data-field-name');
		var display_location = $(this).attr('data-display-location');
		
		$('div.cspml_fs_options_list[data-map-id='+map_id+'][data-display-location='+display_location+'] div.cspml_input_container input[type=text][name='+field_name+']').val('');	
		
	});
	
	
	/**
	 * Set the padding of the field based on its related symbol width
	 * @since 3.2 */
	 
	$.each($('.cspml_input_symbol_after'), function(){
		var map_id = $(this).attr('data-map-id');
		var field_name = $(this).attr('data-field-name');
		var width = $(this).innerWidth();
		$('div.cspml_input_container').find('input[type=text][data-map-id='+map_id+'][name="'+field_name+'"]').attr('style', 'padding-right: '+ (width + 13) + 'px !important;');
	});
	
	$.each($('.cspml_input_symbol_before'), function(){
		var map_id = $(this).attr('data-map-id');
		var field_name = $(this).attr('data-field-name');
		var width = $(this).innerWidth();
		$('div.cspml_input_container').find('input[type=text][data-map-id='+map_id+'][name="'+field_name+'"]').attr('style', 'padding-left: '+ (width + 13) + 'px !important;');
	});	
		

	/**
	 * Open the "Sort by" options list */
	 
    $(document).on('click', 'div.cspml_sort_list_container', function(){ //@edited 3.5.3
		
		var map_id = $(this).attr('data-map-id');
		
		$('ul.cspml_sort_list[data-map-id='+map_id+']').addClass('cspm_animated fadeIn').slideToggle(function(){
			$('ul.cspml_sort_list[data-map-id='+map_id+']').mCustomScrollbar("destroy");
			$('ul.cspml_sort_list[data-map-id='+map_id+']').mCustomScrollbar({
				autoHideScrollbar:true,
				mouseWheel:{
					enable: true,
					preventDefault: true,
				},
				theme: 'dark-2'
			});
		});
		
	});
	
	
	/**
	 * By default add the class "active" to the first element in the list on page load */
	 
	$('ul.cspml_sort_list').each(function(index){
		
		var map_id = $(this).attr('data-map-id');
		
		$('ul.cspml_sort_list[data-map-id='+map_id+'] li').first().addClass('cspml_active');
	  
	});
	

	/**
	 * Sort the listings
	 * 
	 * @updated 2.0 [added @data_type] */
	 
    $(document).on('click', 'ul.cspml_sort_list li', function(){ //@edited 3.5.3
		
		var data_sort = $(this).attr('data-sort');
		var data_order = $(this).attr('data-order');
		var data_type = $(this).attr('data-type'); //@since 2.0	
		var map_id = $(this).attr('data-map-id');
		
		var map_type = $('div#cspml_listings_container[data-map-id='+map_id+']').attr('data-map-type'); //@since 2.3
		var is_first_load = $('div#cspml_listings_container[data-map-id='+map_id+']').attr('data-first-load'); //@since 2.3
		
		if(map_type != 'normal_map' && is_first_load == 'true')
			return false;

		/**
		 * Update the default list value */
		 
		$('ul.cspml_sort_list[data-map-id='+map_id+'] li').removeClass('cspml_active');
		$(this).addClass('cspml_active');
		$('ul.cspml_sort_list[data-map-id='+map_id+']').fadeOut('fast');
		
		var current_sort_html = $(this).html();
		$('div.cspml_sort_list_container[data-map-id='+map_id+'] span.cspml_sort_val').empty().html(current_sort_html);
			
		cspml_show_listings_animation(map_id);
		
		var init_post_ids = $('div.cspml_listings_area_'+map_id+' input[name=init_post_ids]').val();
		var post_ids = $('div.cspml_listings_area_'+map_id+' input[name=post_ids]').val();
		var shortcode_page_id = $('div.cspml_listings_area_'+map_id+' input[name=shortcode_page_id]').val();
		var divider = $('div.cspml_listings_area_'+map_id+' input[name=divider]').val();
		var current_view = $('div[id^='+map_id+'_listing_item_]').attr('data-view');
		var page_template = $('div.cspml_listings_area_'+map_id+' input[name=page_template]').val();
		var template_tax_query = $('div.cspml_listings_area_'+map_id+' input[name=template_tax_query]').val();
		var optional_latlng = jQuery('div.cspml_listings_area_'+map_id+' input[name=optional_latlng]').val();
		var paginate_position = $('div.cspml_listings_area_'+map_id).attr('data-paginate-position');
		var paginate_align = $('div.cspml_listings_area_'+map_id).attr('data-paginate-align');
		var posts_per_page = jQuery('div.cspml_listings_area_'+map_id).attr('data-posts-per-page');
		var shortcode_page_link = jQuery('div.cspml_listings_area_'+map_id+' input[name=shortcode_page_link]').val(); //@since 2.7

		var $listing_items_container = jQuery('.cspml_listing_items_container[data-map-id='+map_id+']'); //@since 3.5
		if($listing_items_container.length && typeof $listing_items_container.mCustomScrollbar === 'function'){
			$listing_items_container.mCustomScrollbar('scrollTo', 'top');
		} //@since 3.5

		/**
		 * Send the ajax request */
		 
		jQuery.post(
			cspml_vars.ajax_url,
			{
				action: 'cspml_listings_html',
				map_id: map_id,
				data_sort: data_sort,
				data_order: data_order,
				data_type: data_type, //@since 2.0
				shortcode_page_id: shortcode_page_id,
				post_ids: post_ids,
				init_post_ids: init_post_ids,
				divider: divider,
				sort_call: true,
				current_view: current_view,
				page_template: page_template,
				template_tax_query: template_tax_query,
				paginate_position: paginate_position,
				paginate_align: paginate_align,
				posts_per_page: posts_per_page,
				optional_latlng: optional_latlng,
				map_settings: progress_map_vars.map_script_args[map_id]['map_settings'], //@since 2.0
				request_type: 'sort', //@since 2.3		
				shortcode_page_link: shortcode_page_link, //@since 2.7				
			},
			function(data){
		
				jQuery('html').animate({ //@edited 3.5
					scrollTop: jQuery('div.cspml_list_and_filter_container[data-map-id='+map_id+']').offset().top-200 //@edited 3.5
				},function(){
								
					$('div.cspml_listings_area_'+map_id).html(data);
					
					if(current_view == "grid")
						cspml_grid_view_classes(map_id);
						
					$('div.cspml_sort_list_container[data-map-id='+map_id+'] span.cspml_sort_val').empty().html(current_sort_html);
					
					$('li[data-map-id='+map_id+'][data-sort='+data_sort+'][data-order='+data_order+']').addClass('cspml_active');
					
					cspml_hide_listings_animation(map_id);
				
				});	//@edited 3.5
				
			}
		);	
		
	});	
	
	
	/**
	 * Switch the listing's view */
	 
    $(document).on('click', 'div.view_option a', function(){ //@edited 3.5.3
		
		var map_id = $(this).attr('data-map-id');
		var current_view = $(this).attr('data-current-view');
		var next_view = $(this).attr('data-next-view');
		
		/**
		 * Change current view to next view and vise versa */
				
		$(this).attr('data-current-view', next_view);
		$(this).attr('data-next-view', current_view);
		
		/**
		 * Change link title */
		 
		var title = $('div.view_option[data-map-id='+map_id+'] a').attr('title');
		$('div.view_option[data-map-id='+map_id+'] a').attr('title', title.replace(next_view, current_view));			
		
		/**
		 * Set the current view */
		 
		if(next_view == 'grid'){
			
			cspml_grid_view_classes(map_id);
		
			/**
			 * Change view icon */
				
			$('div.view_option[data-map-id='+map_id+'] a svg.cspml_list_view').hide();
			$('div.view_option[data-map-id='+map_id+'] a svg.cspml_grid_view').show();			
			
		}else if(next_view == 'list'){
			
			cspml_list_view_classes(map_id);
		
			/**
			 * Change view icon */
			
			$('div.view_option[data-map-id='+map_id+'] a svg.cspml_grid_view').hide();
			$('div.view_option[data-map-id='+map_id+'] a svg.cspml_list_view').show();			
			
		}

		/**
		 * Adjust the height of the listings content to the image height (list view only) */
		
		cspml_adjust_list_content_height(map_id);
		
	});

		
	/**
	 * Filter the listings from the "Listings filter form" OR the "Map filter form" */
	 
    $(document).on('click', 'a.cspml_submit_listings_filter, a.cspml_mfs_submit_listings_filter', function(e){ //@edited 3.5.3
		
		var map_id = $(this).attr('data-map-id');
		var display_location = $(this).attr('data-display-location'); // Listings OR Map
		var request_type = $(this).attr('data-request-type');
		var init_post_ids = $('div.cspml_listings_area_'+map_id+' input[name=init_post_ids]').val();		
		var current_view = $('div[id^='+map_id+'_listing_item_]').attr('data-view');
		var shortcode_page_id = $('div.cspml_listings_area_'+map_id+' input[name=shortcode_page_id]').val();
		var page_template = $('div.cspml_listings_area_'+map_id+' input[name=page_template]').val();
		var template_tax_query = $('div.cspml_listings_area_'+map_id+' input[name=template_tax_query]').val();
		var show_listings = $('div.cspml_listings_area_'+map_id+' input[name=show_listings]').val();
		var shortcode_page_link = jQuery('div.cspml_listings_area_'+map_id+' input[name=shortcode_page_link]').val(); //@since 2.7
		
		if(display_location != 'map'){
								
			$('html').animate({ //@edited 3.5
				scrollTop: jQuery('div.cspml_list_and_filter_container[data-map-id='+map_id+']').offset().top-200 //@edited 3.5
			},function(){				
				var $listing_items_container = jQuery('.cspml_listing_items_container[data-map-id='+map_id+']'); //@since 3.5
				if($listing_items_container.length && typeof $listing_items_container.mCustomScrollbar === 'function'){
					$listing_items_container.mCustomScrollbar('scrollTo', 'top');
				} //@since 3.5
				cspml_show_listings_animation(map_id);
			});	
			
		}else{
			
			if(typeof NProgress !== 'undefined'){
				NProgress.configure({
				  parent: 'div#codespacing_progress_map_div_'+map_id,
				  showSpinner: true
				});				
				NProgress.start();
			}
			
			if(show_listings == 'true')
				cspml_show_listings_animation(map_id);
		}
			
		var filter_form_fields_val = {};
		var multi_input_values = [];
		var filter_field_data = [];
		
		var posts_to_retrieve = {};
		posts_to_retrieve[map_id] = [];

		$('form#cspml_'+display_location+'_filter_form[data-map-id='+map_id+'] input[type=text], form#cspml_'+display_location+'_filter_form[data-map-id='+map_id+'] input[type=radio]:checked, form#cspml_'+display_location+'_filter_form[data-map-id='+map_id+'] input[type=checkbox]:checked, form#cspml_'+display_location+'_filter_form[data-map-id='+map_id+'] select').each(function() {
			
			var this_field_name = (typeof $(this).attr('name') != 'undefined') 
				? $(this).attr('name').replace('[]', '') 
				: false;
		
			if(this_field_name){

				var filter_type = $('form#cspml_'+display_location+'_filter_form[data-map-id='+map_id+'] input[name='+this_field_name+'_filter_type]').val(); //$(this).attr('data-filter-type');
				filter_field_data = [];
				filter_field_data.push(filter_type);
				
				if($(this).attr('name').indexOf('[]') > -1){
					
					var name_array = $(this).attr('name').split('[]');
					var name = name_array[0];
							
					/**
					 * Clear the array */
					 
					if(!(name in filter_form_fields_val))
						multi_input_values = [];
						
					if(name_array.length == 2){
																			
						if(typeof $(this).attr('value') != 'undefined' && $(this).attr('value').indexOf(',') > -1 && $(this).hasClass('cspml_show_all')){
							var myArray = $(this).attr('value').split(',');
							for(var i = 0; i < myArray.length; i = i + 1){
								multi_input_values.push(myArray[i]);
							}	
						}else{
							multi_input_values.push($(this).val());
						}
						
						filter_form_fields_val[name] = jQuery.merge(filter_field_data, multi_input_values);
					
					}else{
						
						filter_field_data.push($(this).val());
						filter_form_fields_val[$(this).attr('name')] = filter_field_data;
						
					}
					
				}else{
					
					var name = $(this).attr('name');			
					if(typeof $(this).attr('value') != 'undefined' && $(this).attr('value').indexOf(',') > -1 && $(this).hasClass('cspml_show_all')){
						var myArray = $(this).attr('value').split(',');
						for(var i = 0; i < myArray.length; i = i + 1){
							multi_input_values.push(myArray[i]);
						}	
						filter_form_fields_val[name] = jQuery.merge(filter_field_data, multi_input_values);
					}else{
						if($(this).val() != ''){
							filter_field_data.push($(this).val());
							filter_form_fields_val[name] = filter_field_data;
						}
					}
					
				}
			
			}
			
		});
					
		/**
		 * Send the ajax request */

		jQuery.post(
			cspml_vars.ajax_url,
			{
				action: 'cspml_faceted_search_query',
				map_id: map_id,
				init_post_ids: init_post_ids,
				filter_args: filter_form_fields_val,
				filter_form_call: true,
				current_view: current_view,
				shortcode_page_id: shortcode_page_id,
				page_template: page_template,
				template_tax_query: template_tax_query,
				map_settings: progress_map_vars.map_script_args[map_id]['map_settings'], //@since 2.0	
				request_type: request_type, //@since 2.3	
				shortcode_page_link: shortcode_page_link, //@since 2.7				
			},
			function(data){

				posts_to_retrieve[map_id] = JSON.parse(data);

				var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
				
				if(plugin_map.length != 0){
					
					/**
					 * Hide all markers */
					 
					cspm_hide_all_markers(plugin_map).done(function(){
	
						/**
						 * Show markers within the selected filter query */
						 
						cspm_set_markers_visibility(plugin_map, map_id, null, 0, post_ids_and_categories[map_id], posts_to_retrieve[map_id], true);
						cspm_simple_clustering(plugin_map, map_id);						
						
						if(posts_to_retrieve[map_id].length == 0){
							cspm_recenter_map(plugin_map, map_id); //@since 2.5
						}else{
							plugin_map.gmap3({
								autofit:{}
							});
						}
							
						if(progress_map_vars.map_script_args[map_id]['show_posts_count'] == "yes")
							$('span.the_count_'+map_id).empty().html(posts_to_retrieve[map_id].length);
	
						if(typeof NProgress !== 'undefined')
							NProgress.done();
						
					});
					
				}
					
				/**
				 * Filter HTML listings */
				 
				if(show_listings == 'true'){
					
					cspml_filter_listings(map_id, posts_to_retrieve[map_id], false, '', false, request_type);					
					
					$('div#cspml_listings_container[data-map-id='+map_id+']').attr('data-first-load', 'false'); //@since 2.3
						
				}
								
			}
			
		);
		
	});


	/**
	 * Reset the listings from the "Listings filter form" OR the "Map filter form" 
	 *
	 * @updated 2.3 */
	 
    $(document).on('click', 'a.cspml_reset_lsitings_filter, a.cspml_mfs_reset_lsitings_filter', function(){ //@edited 3.5.3

		var map_id = $(this).attr('data-map-id');
		var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
		var display_location = $(this).attr('data-display-location');
		var request_type = $(this).attr('data-request-type');
		//var required_custom_fields = $('div.cspml_listings_area_'+map_id+' input[name=required_custom_fields]').val();
		//var post_type = jQuery('div.cspml_listings_area_'+map_id+' input[name=post_type]').val();
		var init_post_ids = $('div.cspml_listings_area_'+map_id+' input[name=init_post_ids]').val();
		var count_init_post_ids = $('div.cspml_listings_area_'+map_id+' input[name=count_init_post_ids]').val();
		var shortcode_page_id = $('div.cspml_listings_area_'+map_id+' input[name=shortcode_page_id]').val();
		var page_template = $('div.cspml_listings_area_'+map_id+' input[name=page_template]').val();
		var template_tax_query = $('div.cspml_listings_area_'+map_id+' input[name=template_tax_query]').val();
		var show_listings = $('div.cspml_listings_area_'+map_id+' input[name=show_listings]').val();
								 
		var is_first_load = $('div#cspml_listings_container[data-map-id='+map_id+']').attr('data-first-load'); //@since 2.3
		var shortcode_page_link = jQuery('div.cspml_listings_area_'+map_id+' input[name=shortcode_page_link]').val(); //@since 2.7
		
		if(is_first_load == 'true')
			return false; 
			
		if(display_location != 'map'){

			$('html').animate({ //@edited 3.5
				scrollTop: jQuery('div.cspml_list_and_filter_container[data-map-id='+map_id+']').offset().top-200 //@edited 3.5
			},function(){
				var $listing_items_container = jQuery('.cspml_listing_items_container[data-map-id='+map_id+']'); //@since 3.5
				if($listing_items_container.length && typeof $listing_items_container.mCustomScrollbar === 'function'){
					$listing_items_container.mCustomScrollbar('scrollTo', 'top');
				} //@since 3.5
				cspml_show_listings_animation(map_id);
			});	
		
		}else cspml_show_listings_animation(map_id);
		
		var posts_to_retrieve = {};
		posts_to_retrieve[map_id] = [];				
		
		if(plugin_map.length != 0){
		
			if(progress_map_vars.map_script_args[map_id]['map_type'] == 'search_map'){
				
				/** 
				 * Hide all marker on reset when using a search map type
				 * @since 2.3 */
				 
				cspm_hide_all_markers(plugin_map);
				cspm_simple_clustering(plugin_map, map_id);
					
			}else{
						
				cspm_set_markers_visibility(plugin_map, map_id, null, 0, post_ids_and_categories[map_id], posts_to_retrieve[map_id], false);
				cspm_simple_clustering(plugin_map, map_id);
							
				plugin_map.gmap3({
					autofit:{}
				});
							
				if(progress_map_vars.map_script_args[map_id]['show_posts_count'] == "yes")
					$('span.the_count_'+map_id).empty().html(posts_to_retrieve[map_id].length);
					
			}

		}
		
		var filter_form_fields_val = [];//posts_to_retrieve[map_id];

		/**
		 * Send the ajax request */

		jQuery.post(
			cspml_vars.ajax_url,
			{
				action: 'cspml_faceted_search_query',
				map_id: map_id,
				//post_type: post_type,
				init_post_ids: init_post_ids,
				post_ids: posts_to_retrieve[map_id],				
				filter_args: filter_form_fields_val,
				//required_custom_fields: required_custom_fields,				
				filter_form_call: true,	
				shortcode_page_id: shortcode_page_id,
				page_template: page_template,	
				template_tax_query: template_tax_query,
				map_settings: progress_map_vars.map_script_args[map_id]['map_settings'], //@since 2.0	
				request_type: request_type, //@since 2.3
				shortcode_page_link: shortcode_page_link, //@since 2.7						
			},
			function(data){
			
				/**
				 * Reset filter form input */
				 
				cspml_reset_filter_fields(map_id);
			
				/**
				 * Filter HTML listings */

				if(show_listings == 'true'){
					
					cspml_filter_listings(map_id, posts_to_retrieve[map_id], true, count_init_post_ids, true, request_type);	
					
					$('div#cspml_listings_container[data-map-id='+map_id+']').attr('data-first-load', 'true'); //@since 2.3
					
				}
						
			}
		);
		
		/**
		 * Reset "Progress Map" search form */
		 
		if($('form#search_form_'+map_id).is(':visible')){		
			$('form#search_form_'+map_id+' input#cspm_address_'+map_id).val('').trigger('focus'); //@edited 3.5.3
			$('a.cspm_reset_search_form_'+map_id).removeClass('fadeIn').hide('fast'); //@edited 3.4.8
		}
		
	});	
	
		
	/**
	 * The event handler of the listings items.
	 * Show the location of the item in the map */
	
    $(document).on('click', '.cspml_fire_pinpoint', function(){ //@edited 3.5.3

		var map_id = $(this).attr('data-map-id'); 
		var post_id = $(this).attr('data-post-id'); 
		var plugin_map = $('div#codespacing_progress_map_div_'+map_id);

		if(plugin_map.length > 0){
			
			var map = plugin_map.gmap3("get");
			var marker_position = $(this).attr('data-coords').split('_');
	
			$('html').animate({
				scrollTop: $('div#codespacing_progress_map_div_'+map_id).offset().top-100
			}, 500, function(){
				cspm_center_map_at_point(plugin_map, map_id, marker_position[0], marker_position[1], 'zoom');
				setTimeout(function(){cspm_animate_marker(plugin_map, map_id, post_id);}, 200);
				if(progress_map_vars.infowindow_type != 'content_style'){		
					// Add overlay active style (used only for bubble infowindow style) 
					$('div.marker_holder div.pin_overlay_content').removeClass('pin_overlay_content-active');
					$('div#bubble_'+post_id+'_'+map_id+' div.pin_overlay_content').addClass('pin_overlay_content-active');	
				}
			});	
			
		}
		
	}).css('cursor','pointer');
	
    /**
     * Center the map on the item's position when hovering over it for more than 1 second
     * @since 3.6 */
    
    var item_timer;
    
    $(document).on('mouseenter', '.cspml_item_holder', function(){
            
        var map_id = $(this).attr('data-map-id'); 
        var post_id = $(this).attr('data-post-id'); 
        var hover_fire = $(this).attr('data-hover-fire'); 
        var plugin_map = $('div#codespacing_progress_map_div_'+map_id);
        var map = plugin_map.gmap3("get");
        var marker_position = $(this).attr('data-coords').split('_');

        if(hover_fire == 'yes' && plugin_map.length > 0 && marker_position.length == 2){
            item_timer = setTimeout(function(){                               
                cspm_center_map_at_point(plugin_map, map_id, marker_position[0], marker_position[1], 'center');
                setTimeout(function(){cspm_animate_marker(plugin_map, map_id, post_id);}, 200);
                if(progress_map_vars.infowindow_type != 'content_style'){		
                    // Add overlay active style (used only for bubble infowindow style) 
                    $('div.marker_holder div.pin_overlay_content').removeClass('pin_overlay_content-active');
                    $('div#bubble_'+post_id+'_'+map_id+' div.pin_overlay_content').addClass('pin_overlay_content-active');	
                }                	
            }, 500);
        }
		
	}).on('mouseleave', '.cspml_item_holder', function() {
        clearTimeout(item_timer);
    });
    
	/**
	 * @Event handler */
	
	
	/**
	 * This code will filter the listings when there's a faceted search request from "Progress Map".
	 * It'll get the result of the post_ids to display from the global var 'cspm_global_object'. */
	
    $(document).on('ifChanged', 'form.faceted_search_form input', function(){ //@edited 3.5.3
						
		var map_id = $(this).attr('data-map-id');
		var carousel = $(this).attr('data-show-carousel');		
		var ext_name = $('form#faceted_search_form_'+map_id).attr('data-ext'); // Check if "Progress" Map uses another extension/addon and return it name		
		
		if(carousel == 'no' && ext_name == 'cspml_list'){
			if(typeof cspml_show_listings_animation == 'function')
				cspml_show_listings_animation(map_id);
		}

		setTimeout(function(){
				
			/**
			 * Loop throught checkboxes/radios and count the one(s) selected.
			 * If the result equals 0, then, consider the action as resetting ...
			 * ... else, it a filter request.
			 * @since 2.3 */
			
			var num_checked = 0; //@since 2.3
					
			$('div.faceted_search_container_'+map_id+' form.faceted_search_form input').each(function(){
				if($(this).prop('checked') == true) 
					num_checked++;
			}); //@since 2.3
			
			var reset_list = (num_checked == 0) ? true : false; //@since 2.3
			var request_type = (num_checked == 0) ? 'reset' : 'filter'; //@since 2.3
			
			/**
			 * Filter/Reset listings */
			 				
			if(carousel == 'no' && ext_name == 'cspml_list'){
				if(typeof cspml_filter_listings == 'function' && typeof window.cspm_global_object.posts_to_retrieve[map_id] !== 'undefined')
					cspml_filter_listings(map_id, window.cspm_global_object.posts_to_retrieve[map_id], true, '', reset_list, request_type);
			}else{
				if(typeof cspml_hide_listings_animation == 'function')
					cspml_hide_listings_animation(map_id);	
			}
			
		}, 1000);
					
	});
	
	
	/**
	 * This code will filter the listings when there's a search request from "Progress Map"
	 * It'll get the result of the post_ids to display from the global var 'cspm_global_object' */
	 
	$(document).on('click', 'a.cspm_search_in_ext_data, a.cspm_reset_ext_data', function(e){ //@edited 3.6.1		
	
		e.preventDefault(); //@since 3.4.8
			
		var map_id = $(this).attr('data-map-id');
		var carousel = $(this).attr('data-show-carousel');
		var ext_name = $(this).attr('data-ext'); // This data detects if the Progress Map is used with another extension/addon
		
		var request_type = 'filter'; //@since 2.3
		$('div#cspml_listings_container[data-map-id='+map_id+']').attr('data-first-load', 'false'); //@since 2.3
		
		if(carousel == 'no' && ext_name == 'cspml_list'){
			
			if(typeof cspml_show_listings_animation == 'function')
				cspml_show_listings_animation(map_id);
			
			/**
			 * Reset filter form input */
				 
			if(typeof cspml_reset_filter_fields == 'function')
				cspml_reset_filter_fields(map_id);
				
		}
		
		var reset_list = $(this).hasClass('cspm_reset_ext_data'); //@edited 3.6.1

		if(reset_list){
			
			request_type = 'reset'; //@since 2.3
			
			$('div#cspml_listings_container[data-map-id='+map_id+']').attr('data-first-load', 'true'); //@since 2.3
			
		}
		
		setTimeout(function(){
			if(carousel == 'no' && ext_name == 'cspml_list'){
				if(typeof cspml_filter_listings == 'function' && typeof window.cspm_global_object.posts_to_retrieve[map_id] !== 'undefined')
					cspml_filter_listings(map_id, window.cspm_global_object.posts_to_retrieve[map_id], true, '', reset_list, request_type);
				else if(typeof cspml_hide_listings_animation == 'function')
					cspml_hide_listings_animation(map_id);	
			}else{
				if(typeof cspml_hide_listings_animation == 'function')
					cspml_hide_listings_animation(map_id);	
			}
		}, 1000);
					
	});
	
	/**
	 * Show & Hide the listing's filter form
	 * @updated 2.6 | 3.4.7 */
	
    $(document).on('click', 'span.cspml_close_fs', function(){ //@edited 3.5.3
		
		var map_id = $(this).attr('data-map-id');
		var filter_width = $(this).attr('data-filter-width'); //@since 3.4.7
		
		var map_cols = $('.cspml_listing_items_container[data-map-id='+map_id+']').attr('data-map-cols'); //@edited 3.5
		var list_layout = $('.cspml_listing_items_container[data-map-id='+map_id+']').attr('data-list-layout'); //@edited 3.5
		
		var listing_items_container_cols = 'cspm-col-lg-' + (12 - filter_width) + ' cspm-col-md-'+(12 - filter_width); //@edited 3.5
			
		if($('.cspml_fs_container[data-map-id='+ map_id +']').is(':visible')){
			
			$('.cspml_listing_items_container[data-map-id='+map_id+']').removeClass(listing_items_container_cols).addClass('cspm-col-lg-12 cspm-col-md-12');
		
		}else $('.cspml_listing_items_container[data-map-id='+map_id+']').removeClass('cspm-col-lg-12 cspm-col-md-12').addClass(listing_items_container_cols);
		
		$('.cspml_fs_container[data-map-id='+ map_id +']').toggle();
	
		/**
		 * Adjust the height of the listings content to the image height (list view only) */
		
		cspml_adjust_list_content_height(map_id);
		
	});
	
	
	/**
	 * Detect when a user opens "Progress Map" search form and/or faceted search and/or countries list and resize the map accordingly */
	 
    $(document).on('click', '.cspm_expand_map_btn', function(){ //@edited 3.5.3
		
		var map_id = $(this).attr('data-map-id');
			
		var map_height = $('div.cspml_resize_map[data-map-id='+map_id+']').attr('data-map-height');
		
		var $map_div = $('div#codespacing_progress_map_div_'+map_id);
		 
		var $resize_img_selector = $('div.cspml_resize_map[data-map-id="'+map_id+'"] img');
		
		var resize_img_src = $resize_img_selector.attr('src');
		
		if(typeof map_height !== 'undefined' && typeof resize_img_src !== 'undefined'){
			
			if($map_div.innerHeight() != map_height.replace('px', '')){
				
				$map_div.animate({height: map_height}, 600, function(){
				
					$resize_img_selector.attr('src', resize_img_src.replace('expand.svg', 'collapse.svg'));	
				
				});
				
			}
			
		}
		
	});
	
	/**
	 * Resize the Map vertically */
	
    $(document).on('click', 'div.cspml_resize_map', function(){ //@edited 3.5.3
		
		var map_id = $(this).attr('data-map-id');
		
		var map_height = $(this).attr('data-map-height');
		 
		var $resize_img_selector = $('div.cspml_resize_map[data-map-id="'+map_id+'"] img');
		
		var resize_img_src = $resize_img_selector.attr('src');
		
		var $map_div = $('div#codespacing_progress_map_div_'+map_id);
		
		if(typeof map_height !== 'undefined' && typeof resize_img_src !== 'undefined'){
			
			if($map_div.innerHeight() != map_height.replace('px', '')){
				
				$map_div.animate({height: map_height}, 600, function(){
				
					$resize_img_selector.attr('src', resize_img_src.replace('expand.svg', 'collapse.svg'));	
				
				});
				
			}else{
				
				$map_div.animate({height: "100px"}, 600, function(){
							
					$resize_img_selector.attr('src', resize_img_src.replace('collapse.svg', 'expand.svg'));	
					
					/**
					 * Hide "Progress Map" search form */
					 
					if($('div.search_form_container_'+map_id).is(':visible')){
						$('div.search_form_container_'+map_id).removeClass('fadeInUp').addClass('cspm_animated slideOutLeft');
						setTimeout(function(){
							$('div.search_form_container_'+map_id).css({'display':'none'});							
						},200);
					}		
					
					/**
					 * Hide "Progress Map" Faceted search form */
					 
					if($('div.faceted_search_container_'+map_id).is(':visible')){	
						$('div.faceted_search_container_'+map_id).removeClass('slideInLeft').addClass('cspm_animated slideOutLeft');
						setTimeout(function(){$('div.faceted_search_container_'+map_id).css({'display':'none'});},200);
					}
			
					/**
					 * Hide the Countries list & Remove hover style on Countries list */
					
					if($('div.countries_container_'+map_id).is(':visible')){
						
						$('div.countries_container_'+map_id).removeClass('slideInLeft').addClass('cspm_animated slideOutLeft');
						
						setTimeout(function(){$('div.countries_container_'+map_id).css({'display':'none'});},200);
					
						$('li.cspm_country_name[data-map-id='+map_id+']').removeClass('selected');
					
					}
			
				});
				
			}
			
		}
		
	});
	
	/**
	 * Prevent form submit on Enter		
	 * @since 2.5 */
	 
	$('input.cspml_keyword').keypress(function(e){
		if(e.keyCode == 13){
			e.preventDefault();
			var map_id = $(this).closest('div.cspml_fs_options_list').attr('data-map-id');
			if($(this).val() != '' && !$('div#cspml_loading_container_'+map_id).is(':visible')){
				$('a.cspml_submit_listings_filter[data-map-id='+map_id+']').eq(0).trigger('click'); // Note: [eq(0)] Will prevent submitting filter twice when there's two filter buttons!
			}
		}
	});	

})(jQuery);
