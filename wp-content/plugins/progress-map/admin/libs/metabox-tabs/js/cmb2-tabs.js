(function ($) {

	$(document).ready(function () {
						
		/**
		 * Add Read More link to long fields description */
		
		if(typeof $readMoreJS !== 'undefined'){
			$readMoreJS.init({
			   target: 'p.cmb2-metabox-description', // Selector of the element the plugin applies to (any CSS selector, eg: '#', '.'). Default: ''
			   numOfWords: 30, // Number of words to initially display (any number). Default: 50
			   toggle: true, // If true, user can toggle between 'read more' and 'read less'. Default: true
			   moreLink: 'Read more ...', // The text of 'Read more' link. Default: 'read more ...'
			   lessLink: '... Read less', // The text of 'Read less' link. Default: 'read less'
			   linkClass: 'cs-readmore', // The class given to the read more link. Defaul: 'rm-link'
			   containerClass: false // The class given to the div container of the read more link. Default: false
			});	
		}

		/**
		 * JS Cookie - Save current menu */
		
		if(typeof Cookies !== 'undefined'){
			var cmb2_tabs_cookie = Cookies.noConflict();							
			var cookie_name = 'cspm-menu-item';

			setTimeout(function(){
				if(typeof cmb2_tabs_cookie.get(cookie_name) !== 'undefined'){
					var current_cookie = cmb2_tabs_cookie.get(cookie_name);
					if($('ul.tabs-menu li a[href="'+current_cookie+'"]').length){
						$('ul.tabs-menu li a[href="'+current_cookie+'"]').trigger('click');
					}
				}
			}, 500);
		}
		
		/**
		 * Menu Click */
		 
		$("ul.tabs-menu li").click(function(event) {
	
			event.preventDefault();
		
			$this = $(this).find('a');
		
			var metabox_id = $this.attr('data-metabox-id');
			
			$this.parent().addClass("current");
			
			$this.parent().siblings().removeClass("current");
			
			var tab = $this.attr("href");

			$("div#"+metabox_id+" .tab-content").not(tab).css("display", "none");
			
			$(tab).fadeIn();
			
			$('html, body').animate( { scrollTop: $(tab).offset().top-50 }, 500 ); 
			
			/**
			 * Save the current tab */
			
			if(typeof cmb2_tabs_cookie !== 'undefined')
				cmb2_tabs_cookie.set(cookie_name, tab, { path: window.location.pathname });	
						
		});
		
	});
	
})(jQuery);

jQuery.noConflict();	
