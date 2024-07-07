/**
 * Plugin Name: CMB2 File Field
 * Version: 1.1
 * Author: Hicham Radi (CodeSpacing)
 * Author URI: https://www.codespacing.com/
 */
	
(function($){
	
	'use strict';

	/**
	 * Hide all file inputs */
	
	$('.cs_file_input').each(function(){
		
		var $file_input = $(this)[0];
		
		$file_input.style.visibility = 'hidden';
		$file_input.style.height = '0px';
		
	});

	/**
	 * Open the browser modal */
	
	$('.cs_open_browser').on('click', function(){ //@edited 1.1
		
		var id = $(this).attr('id');
		
		$('.cs_file_input[id='+id+']').trigger('click');
		
	});
	
	/**
	 * Add drag and drop options to file inputs
	 *
	 * @since 1.0
	 */
	
	$('.cs_open_browser').on('dragenter, dragover', function(event){ //@edited 1.1
		
		event.stopPropagation();
		event.preventDefault();
		
	}).on('drop', function(event){ //@edited 1.1
	
		event.stopPropagation();
		event.preventDefault();
	
		/**
		 * Display the uploaded files in a list */

		var id = $(this).attr('id');
		var max_files = $('.cs_file_input[id='+id+']').attr('data-max-files');
		var max_upload_size = $('.cs_file_input[id='+id+']').attr('data-max-upload-size');
		var accepted_file_types = $('.cs_file_input[id='+id+']').attr('accept').split(',');		
		
		var $file_input = $('.cs_file_input[id='+id+']');
		var $files_preview_area = $('.cs_files_preview_area[id='+id+']');
		
		if(typeof event.originalEvent.dataTransfer.files !== 'undefined'){
			var uploaded_files = event.originalEvent.dataTransfer.files;
			$file_input[0].files = uploaded_files;
			cs_handle_files($file_input, uploaded_files, $files_preview_area, accepted_file_types, max_files, max_upload_size);
		}
		
	});
	
	/**
	 * Display the uploaded files in a list */
	
	$('.cs_file_input').on('change', function(){ //@edited 1.1

		var id = $(this).attr('id');
		var max_files = $(this).attr('data-max-files');
		var max_upload_size = $(this).attr('data-max-upload-size');
		var accepted_file_types = $(this).attr('accept').split(',');
		
		var $file_input = $(this); 		
		var $files_preview_area = $('.cs_files_preview_area[id='+id+']');

		if(typeof $(this)[0].files !== 'undefined'){
			var uploaded_files = $(this)[0].files;
			cs_handle_files($file_input, uploaded_files, $files_preview_area, accepted_file_types, max_files, max_upload_size);
		}

	});	
	
})(jQuery);


/**
 * This will validate the file(s) attributes (type, size and length) and display the uploaded file(s)
 * in a list.
 *
 * @since 1.0
 */ 
function cs_handle_files(file_input, uploaded_files, files_preview_area, accepted_file_types, max_files, max_upload_size) {
	
	var iziToast_settings = {		
		title: '',
		transitionIn: 'fadeIn',
		position: 'center',
		timeout: 6000,
		maxWidth: null,
		toastOnce: true,
	};	
	
	/**
	 * Make sure not to upload more than max allowed file */
	
	if(uploaded_files.length > max_files){
				
		var message = cs_file_field_vars.exceed_max_files_error.replace('%s', max_files);
		
		(typeof iziToast === 'undefined') ? alert(message) : iziToast.error( jQuery.extend( iziToast_settings, {id: 'cs_error1', message: message} ) );
						
		cs_reset_file_input(file_input);
	
	/**
	 * Make sure all uploaded files are valid types */
	 
	}else if(cs_contains_invalid_file_types(uploaded_files, accepted_file_types)){
				
		var message = (uploaded_files.length > 1) ? cs_file_field_vars.invalid_file_types_error : cs_file_field_vars.invalid_file_type_error;
		
		(typeof iziToast === 'undefined') ? alert(message) : iziToast.error( jQuery.extend( iziToast_settings, {id: 'cs_error2', message: message} ) );
						
		cs_reset_file_input(file_input);
	
	/**
	 * Make sure file size do not exceed "max_upload_size" */
	 
	}else if(cs_check_file_size(uploaded_files, max_upload_size)){
				
		var message = (uploaded_files.length > 1) ? cs_file_field_vars.exceed_max_upload_size_error_multiple : cs_file_field_vars.exceed_max_upload_size_error;
			message += ' (' + cs_file_size(max_upload_size) + ')!';
		
		(typeof iziToast === 'undefined') ? alert(message) : iziToast.error( jQuery.extend( iziToast_settings, {id: 'cs_error3', message: message} ) );
						
		cs_reset_file_input(file_input);
	
	/**
	 * Display the list of all uploaded files */
	 
	}else{
  	
		var output = '';
		
		if(uploaded_files.length === 0){
			
			files_preview_area.html(output);
			
		}else{
			
			output += '<ol>';
				
				for(var i = 0; i < uploaded_files.length; i++){
	
					output += '<li>';
					
						if(cs_validate_file_type(uploaded_files[i], accepted_file_types)){
	
							if((uploaded_files[i].type.split('/')[0] === 'image'))								
								output += '<img src="'+window.URL.createObjectURL(uploaded_files[i])+'" />';
							
							output += '<p>';								
								output += uploaded_files[i].name;
								output += ' (' + cs_file_size(uploaded_files[i].size) + ')';
							output += '</p>';
						
						}else output += '<p>' + uploaded_files[i].name + ': ' + cs_file_field_vars.invalid_file_type_msg + '</p>';
					
					output += '</li>';
					
				}
			
			output += '</ol>';
			
			files_preview_area.html(output);
	
		}

	}
		
}


/**
 * Check all uploaded files types.
 *
 * Return TRUE if all file types are valid!
 * Return FALSE if the uploaded files contains one or more invalid type!
 *
 * @since 1.0
 */
function cs_contains_invalid_file_types(files, accepted_file_types){
	
	var uploaded_files = files;
	
	var invalid_file_types = 0;
	
	for(var i = 0; i < uploaded_files.length; i++){
		if(!cs_validate_file_type(uploaded_files[i], accepted_file_types))
			invalid_file_types++;
	}
	
	return (invalid_file_types > 0) ? true : false;
	
}


/**
 * Validate file type 
 *
 * @since 1.0
 */
function cs_validate_file_type(file, accepted_file_types){

	for(var i = 0; i < accepted_file_types.length; i++) {
		if(file.type === accepted_file_types[i])
			return true;
	}
	
	return false;
  
}


/**
 * Check all uploaded files sizes.
 *
 * Return TRUE if all file sizes are valid!
 * Return FALSE if the uploaded files contains one or more invalid size!
 *
 * @since 1.0
 */
function cs_check_file_size(files, max_upload_size){
	
	var uploaded_files = files;
	
	var invalid_file_sizes = 0;
	
	for(var i = 0; i < uploaded_files.length; i++){
		if(uploaded_files[i].size > max_upload_size)
			invalid_file_sizes++;
	}
	
	return (invalid_file_sizes > 0) ? true : false;
	
}


/**
 * Clear/Reset the file input
 *
 * @since 1.0
 */
function cs_reset_file_input(file_input){

	file_input.wrap('<form>').closest('form').get(0).reset();
	file_input.unwrap();
	
}


/**
 * Convert file size to humain readable value 
 *
 * @since 1.0 
 */
function cs_file_size(number){
	
	if(number <= 1024){
		
		return number + 'bytes';
		
	}else if(number > 1024 && number <= 1048576){
		
		return (number/1024).toFixed(1) + 'KB';
		
	}else if(number > 1048576){
		
		return (number/1048576).toFixed(1) + 'MB';
		
	}
	
}
