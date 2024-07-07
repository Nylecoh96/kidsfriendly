/**
 * Plugin Name: CMB2 Button Field
 * Version: 1.0
 * Author: Hicham Radi (CodeSpacing)
 * Author URI: https://www.codespacing.com/
 */

jQuery(document).ready(function($){	
	
	$('a.cs_button').on('click', function(e){
		
		e.preventDefault();
		
		var id = $(this).attr('id');
		var action = $(this).attr('data-action');
		var success_msg = $(this).attr('data-success-msg');
		var error_msg = $(this).attr('data-error-msg');
		var confirm_msg = $(this).attr('data-confirm-msg');
		
		var result = confirm(confirm_msg);
		
		if (result == true) {
			
			$('span.cs_loading[id='+id+']').fadeIn();
					
			jQuery.post(
				ajaxurl,
				{
					action: action,
				},
				function(data){	
					$('span.cs_loading[id='+id+']').fadeOut();				
					setTimeout(function(){ 
						alert(success_msg);
					}, 1000);
				}
			);
		
		}else return;
		
	});
				
});
