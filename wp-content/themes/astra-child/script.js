jQuery(document).ready(function($) {


        $(window).on('load', function() {
    
  
         $('#select-cat .data').on('click', function(event) {
        var checked_value = $(this).val();
        if($(this).is(":checked")){
            $('.cspml_fs_options_list[data-field-name="category"] input.icr-input[value="'+checked_value+'"]').prop('checked', true);
            $('.cspml_fs_options_list[data-field-name="category"] input.icr-input[value="'+checked_value+'"]').parent().parent().addClass('checked');
        }

        // if($(this).is(":not(:checked)")){
        //     $('.cspml_fs_options_list[data-field-name="category"] input.icr-input[value="'+checked_value+'"]').prop('checked', false);
        //     $('.cspml_fs_options_list[data-field-name="category"] input.icr-input[value="'+checked_value+'"]').parent().parent().removeClass('checked');
        // }

    });
  

    $('input#selectPriceMin').on('input', function(){
        var inputTextMin = $(this).val();
       $('.cspml_input_container .cspml_min_max_container:first-child input').val(inputTextMin);
    });

	$('input#selectPriceMax').on('input', function(){
		var inputTextMax = $(this).val();
       $('.cspml_input_container .cspml_min_max_container:nth-child(3) input').val(inputTextMax);
    });

    $('#select-facilities .data').on('click', function(event) {

        // var checked_value = $(this).val();
        // if($(this).is(":checked")){
        //     $('.cspml_fs_options_list[data-field-name="facilities"] input.icr-input[value="'+checked_value+'"]').prop('checked', true);
        //     $('.cspml_fs_options_list[data-field-name="facilities"] input.icr-input[value="'+checked_value+'"]').parent().parent().addClass('checked');
        // }

        // if($(this).is(":not(:checked)")){
        //     $('.cspml_fs_options_list[data-field-name="facilities"] input.icr-input[value="'+checked_value+'"]').prop('checked', false);
        //     $('.cspml_fs_options_list[data-field-name="facilities"] input.icr-input[value="'+checked_value+'"]').parent().parent().removeClass('checked');
        // }

    });
    
    $("#search-submit").click(function(event){
        event.preventDefault();

        var get_val = $('input#location').val();
        $('input#cspm_address_map1148').val(get_val);
        $('a.cspml_submit_listings_filter.cspml_btn.cspm_bg_hex_hover.cspm_border_shadow.cspm-col-lg-8.cspm-col-md-8.cspm-col-sm-9.cspm-col-xs-9').trigger('click');
    });

      });



    $('#store-post-submission').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: ajax_object.ajaxurl, // WordPress AJAX URL
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response);
                   Swal.fire({
                        title: 'Custom Alert Title',
                        text: 'Custom alert message goes here.',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                // Handle error response here
            }
        });
    });
    
    $('span.bookmark-resto.fav').click(function(e) {
        e.preventDefault();
        var post_id = $(this).attr('data-post');
        var user_id = $(this).attr('data-user');
        var status = $(this).attr('data-status');
        $(this).find('.spinner-grow').css('display','block');
        if(status == 'unmark'){
            $(this).find('img').attr('src', 'https://kidsfriendly.world/wp-content/uploads/2024/03/Mark.png');
            $(this).attr('data-status','mark');
            var status = $(this).attr('data-status');
        }else{
            $(this).find('img').attr('src', 'https://kidsfriendly.world/wp-content/uploads/2024/03/Unmark.png');
             $(this).attr('data-status','unmark');
             var status = $(this).attr('data-status');
        }
        
        $.ajax({
            type: 'POST',
            url: ajax_object.ajaxurl, // WordPress AJAX URL
            data: {
                action: 'bookmarks',
                'post_id' : post_id,
                'user_id' : user_id,
                'status' : status
            },
            success: function(response) {
                console.log(response);
                $(this).find('.spinner-grow').css('display','none');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                // Handle error response here
            }
        });
    
    });
    
    
    setTimeout(function() {
        $('.alert-message').css('display','none');
    }, 9000);
    
    $('#upload_images_button').click(function(e) {
    e.preventDefault(); // Prevents the default action of the button click

    let image_frame = wp.media.frames.file_frame; // Get the media frame

    if (!image_frame) { // If the media frame doesn't exist, create it
        image_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select Images',
            button: {
                text: 'Use these images',
            },
            multiple: true // Allow multiple image selection
        });
    }

    // When images are selected, do the following
    image_frame.on('select', function() {
        const attachment = image_frame.state().get('selection').toJSON(); // Get selected images as JSON

        if (attachment.length > 0) { // If images are selected
            const selected_images = []; // Array to store selected image URLs

            $('#selected_images').empty(); // Clear the container for previously selected images

            // Loop through each selected image
            $.each(attachment, function(index, value) {
                selected_images.push(value.url); // Push the image URL to the array
                $('#selected_images').append('<img src="' + value.url + '" style="max-width:100px; max-height:100px; margin-right:10px;">'); // Display the image as a thumbnail
            });

            // Set the value of the input field with comma-separated URLs
            $('#post_images').val(selected_images.join(','));
        } else {
            console.error('No images selected.'); // Log an error if no images are selected
        }
    });

    // Open the media frame
    image_frame.open();
});

    //  $('#upload_images_button').click(function(e) {
    //     e.preventDefault();

    //     let image_frame = wp.media.frames.file_frame;

    //     if (!image_frame) {
    //         image_frame = wp.media.frames.file_frame = wp.media({
    //             title: 'Select Images',
    //             button: {
    //                 text: 'Use these images',
    //             },
    //             multiple: true
    //         });
    //     }

    //     image_frame.on('select', function() {
    //         const attachment = image_frame.state().get('selection').toJSON();

    //         if (attachment.length > 0) {
    //             const selected_images = [];

    //             $('#selected_images').empty();

    //             $.each(attachment, function(index, value) {
    //                 selected_images.push(value.url);
    //                 $('#selected_images').append('<img src="' + value.url + '" style="max-width:100px; max-height:100px; margin-right:10px;">');
    //             });

    //             // Set the value of the input field with comma-separated URLs
    //             $('#post_images').val(selected_images.join(','));
    //         } else {
    //             console.error('No images selected.');
    //         }
    //     });

    //     image_frame.open();
    // });


});
