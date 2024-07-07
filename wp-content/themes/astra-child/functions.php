<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );
add_filter('use_block_editor_for_post', '__return_false');

/**
 * Enqueue styles
 */
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 1 );
function child_enqueue_styles() {
    
  wp_enqueue_media();
wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );
wp_enqueue_style( 'astra-child-all', get_stylesheet_directory_uri() . '/css/all.min.css', array(), '4.7.0', 'all' );
wp_enqueue_style( 'astra-child-brand', get_stylesheet_directory_uri() . '/css/brands.min.css', array(), '4.7.0', 'all' );
wp_enqueue_style( 'astra-child-fontawesome', get_stylesheet_directory_uri() . '/css/fontawesome.min.css', array(), '4.7.0', 'all' ); // Fixed handle typo
wp_enqueue_style( 'astra-child-solid', get_stylesheet_directory_uri() . '/css/solid.min.css', array(), '4.7.0', 'all' );
wp_enqueue_style( 'astra-child-regular', get_stylesheet_directory_uri() . '/css/regular.min.css', array(), '4.7.0', 'all' );

wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css');
wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js', array('jquery'), false, true);

wp_enqueue_script('jquery');
wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/script.js', array('jquery'), null, true);
wp_localize_script('custom-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));

    // wp_enqueue_script( 'sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@10', array(), null, true );
}


add_filter('user_registration_success_params', 'custom_registration_redirect', 10, 4);
function custom_registration_redirect($success_params, $valid_form_data, $form_id, $user_id) {
    // Add your custom redirect URL here
    $redirect_url = 'http://example.com/user-profile/?user_id=' . $user_id; // Change the URL as per your requirement
    
    // Add the redirect URL to the success parameters
    $success_params['redirect_to'] = $redirect_url;

    return $success_params;
}
// Allow SVG file uploads
function add_svg_to_upload_mimes( $upload_mimes ) {
    $upload_mimes['svg'] = 'image/svg+xml';
    $upload_mimes['svgz'] = 'image/svg+xml';
    return $upload_mimes;
}
add_filter( 'upload_mimes', 'add_svg_to_upload_mimes', 10, 1 );

add_shortcode('restaurant-content', 'restaurant_page_content');
function restaurant_page_content() {
    ob_start(); 
    include get_stylesheet_directory() . '/kids/page-restaurant-details.php'; 
    $output = ob_get_clean(); 
    return $output;
}

add_shortcode('add-store', 'add_store_kids');
function add_store_kids() {
    ob_start(); 
    include get_stylesheet_directory() . '/add-store.php'; 
    $output = ob_get_clean(); 
    return $output;
}

add_shortcode('search-bar-kids', 'search_bar_kids');
function search_bar_kids() {
    ob_start(); 
    include get_stylesheet_directory() . '/search-bar.php'; 
    $output = ob_get_clean(); 
    return $output;
}


add_shortcode('about-content', 'about_page_content');
function about_page_content() {
    ob_start(); 
    include get_stylesheet_directory() . '/kids/index.php'; 
    $output = ob_get_clean(); 
    return $output;
}

add_shortcode('registration-form-custom', 'registration_form_custom');
function registration_form_custom() {
    ob_start(); 
    include get_stylesheet_directory() . '/registration-form-custom.php'; 
    $output = ob_get_clean(); 
    return $output;
}





add_shortcode('store-update', 'store_submission_form');
function store_submission_form() {
    ob_start(); 
    include get_stylesheet_directory() . '/store-update.php'; 
    $output = ob_get_clean(); 
    return $output;
}


add_shortcode('restaurant-slider', 'restaurant_slider_kids');
function restaurant_slider_kids() {
    ob_start(); 
    include get_stylesheet_directory() . '/restaurant-slider.php'; 
    $output = ob_get_clean(); 
    return $output;
}

add_shortcode('restaurant-card', 'restaurant_card_kids');
function restaurant_card_kids() {
    ob_start(); 
    include get_stylesheet_directory() . '/restaurant-card.php'; 
    $output = ob_get_clean(); 
    return $output;
}

add_shortcode('restaurant-store-card', 'restaurant_store_kids');
function restaurant_store_kids() {
    ob_start(); 
    include get_stylesheet_directory() . '/restaurant-store-card.php'; 
    $output = ob_get_clean(); 
    return $output;
}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');
function my_acf_google_map_api( $api ){
    $api['key'] = 'AIzaSyD9kzmCY9-5cEA3vcUYZPWLSwGlgT5aHmI';
    return $api;
}

add_action( 'init', 'change_shop_manager_role_name' );
function change_shop_manager_role_name() {
    global $wp_roles;

    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();

    // Change role name
    $wp_roles->roles['shop_manager']['name'] = 'Restaurant Owner';
    $wp_roles->role_names['shop_manager'] = 'Restaurant Owner';
}


add_action( 'template_redirect', 'login_page_redirect');
function login_page_redirect() {
    if (is_user_logged_in() && is_page('431') && (current_user_can('shop_manager') || current_user_can('subscriber'))){
        wp_redirect(home_url('/my-account/'));
    }
}
add_action('wp_ajax_bookmarks', 'bookmarks');
add_action('wp_ajax_nopriv_bookmarks', 'bookmarks');
function bookmarks() {

    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : ''; 
    $status = isset($_POST['status']) ? $_POST['status'] : ''; 
    $owner_name= isset($_POST['status']) ? $_POST['status'] : ''; 
    $owner_name = get_post_meta($post_id, 'restaurant_customer_likes', true) ?: array();
    
    echo 'id'.$post_id;
    
    if (!empty($status)) {
        if ($status == 'mark') {
            // Use post ID as array key
            $existing_meta[$post_id][] = $user_id;
            update_post_meta($post_id, 'restaurant_customer_likes', $existing_meta);
        } else {
            // Use post ID as array key
            if (isset($existing_meta[$post_id])) {
                $updated_meta = array_diff($existing_meta[$post_id], array($user_id));
                $existing_meta[$post_id] = $updated_meta;
                update_post_meta($post_id, 'restaurant_customer_likes', $existing_meta);
                update_post_meta($post_id, 'owner_name', $owner_name);
            }
        }
    } else {
        // Use post ID as array key
        $existing_meta[$post_id][] = $user_id;
        update_post_meta($post_id, 'restaurant_customer_likes', $existing_meta);
    }

    
    die();

}


function custom_redirect_after_registration( $success_params, $valid_form_data, $form_id, $user_id ) {
    $redirect_url = 'https://kidsfriendly.world/add-store/';
    $success_params['redirect_to'] = $redirect_url;
    return $success_params;
}
//List of Countries
function get_countries(){
    $countries =
 
array(
"AF" => "Afghanistan",
"AL" => "Albania",
"DZ" => "Algeria",
"AS" => "American Samoa",
"AD" => "Andorra",
"AO" => "Angola",
"AI" => "Anguilla",
"AQ" => "Antarctica",
"AG" => "Antigua and Barbuda",
"AR" => "Argentina",
"AM" => "Armenia",
"AW" => "Aruba",
"AU" => "Australia",
"AT" => "Austria",
"AZ" => "Azerbaijan",
"BS" => "Bahamas",
"BH" => "Bahrain",
"BD" => "Bangladesh",
"BB" => "Barbados",
"BY" => "Belarus",
"BE" => "Belgium",
"BZ" => "Belize",
"BJ" => "Benin",
"BM" => "Bermuda",
"BT" => "Bhutan",
"BO" => "Bolivia",
"BA" => "Bosnia and Herzegovina",
"BW" => "Botswana",
"BV" => "Bouvet Island",
"BR" => "Brazil",
"IO" => "British Indian Ocean Territory",
"BN" => "Brunei Darussalam",
"BG" => "Bulgaria",
"BF" => "Burkina Faso",
"BI" => "Burundi",
"KH" => "Cambodia",
"CM" => "Cameroon",
"CA" => "Canada",
"CV" => "Cape Verde",
"KY" => "Cayman Islands",
"CF" => "Central African Republic",
"TD" => "Chad",
"CL" => "Chile",
"CN" => "China",
"CX" => "Christmas Island",
"CC" => "Cocos (Keeling) Islands",
"CO" => "Colombia",
"KM" => "Comoros",
"CG" => "Congo",
"CD" => "Congo, the Democratic Republic of the",
"CK" => "Cook Islands",
"CR" => "Costa Rica",
"CI" => "Cote D'Ivoire",
"HR" => "Croatia",
"CU" => "Cuba",
"CY" => "Cyprus",
"CZ" => "Czech Republic",
"DK" => "Denmark",
"DJ" => "Djibouti",
"DM" => "Dominica",
"DO" => "Dominican Republic",
"EC" => "Ecuador",
"EG" => "Egypt",
"SV" => "El Salvador",
"GQ" => "Equatorial Guinea",
"ER" => "Eritrea",
"EE" => "Estonia",
"ET" => "Ethiopia",
"FK" => "Falkland Islands (Malvinas)",
"FO" => "Faroe Islands",
"FJ" => "Fiji",
"FI" => "Finland",
"FR" => "France",
"GF" => "French Guiana",
"PF" => "French Polynesia",
"TF" => "French Southern Territories",
"GA" => "Gabon",
"GM" => "Gambia",
"GE" => "Georgia",
"DE" => "Germany",
"GH" => "Ghana",
"GI" => "Gibraltar",
"GR" => "Greece",
"GL" => "Greenland",
"GD" => "Grenada",
"GP" => "Guadeloupe",
"GU" => "Guam",
"GT" => "Guatemala",
"GN" => "Guinea",
"GW" => "Guinea-Bissau",
"GY" => "Guyana",
"HT" => "Haiti",
"HM" => "Heard Island and Mcdonald Islands",
"VA" => "Holy See (Vatican City State)",
"HN" => "Honduras",
"HK" => "Hong Kong",
"HU" => "Hungary",
"IS" => "Iceland",
"IN" => "India",
"ID" => "Indonesia",
"IR" => "Iran, Islamic Republic of",
"IQ" => "Iraq",
"IE" => "Ireland",
"IL" => "Israel",
"IT" => "Italy",
"JM" => "Jamaica",
"JP" => "Japan",
"JO" => "Jordan",
"KZ" => "Kazakhstan",
"KE" => "Kenya",
"KI" => "Kiribati",
"KP" => "Korea, Democratic People's Republic of",
"KR" => "Korea, Republic of",
"KW" => "Kuwait",
"KG" => "Kyrgyzstan",
"LA" => "Lao People's Democratic Republic",
"LV" => "Latvia",
"LB" => "Lebanon",
"LS" => "Lesotho",
"LR" => "Liberia",
"LY" => "Libyan Arab Jamahiriya",
"LI" => "Liechtenstein",
"LT" => "Lithuania",
"LU" => "Luxembourg",
"MO" => "Macao",
"MK" => "Macedonia, the Former Yugoslav Republic of",
"MG" => "Madagascar",
"MW" => "Malawi",
"MY" => "Malaysia",
"MV" => "Maldives",
"ML" => "Mali",
"MT" => "Malta",
"MH" => "Marshall Islands",
"MQ" => "Martinique",
"MR" => "Mauritania",
"MU" => "Mauritius",
"YT" => "Mayotte",
"MX" => "Mexico",
"FM" => "Micronesia, Federated States of",
"MD" => "Moldova, Republic of",
"MC" => "Monaco",
"MN" => "Mongolia",
"MS" => "Montserrat",
"MA" => "Morocco",
"MZ" => "Mozambique",
"MM" => "Myanmar",
"NA" => "Namibia",
"NR" => "Nauru",
"NP" => "Nepal",
"NL" => "Netherlands",
"AN" => "Netherlands Antilles",
"NC" => "New Caledonia",
"NZ" => "New Zealand",
"NI" => "Nicaragua",
"NE" => "Niger",
"NG" => "Nigeria",
"NU" => "Niue",
"NF" => "Norfolk Island",
"MP" => "Northern Mariana Islands",
"NO" => "Norway",
"OM" => "Oman",
"PK" => "Pakistan",
"PW" => "Palau",
"PS" => "Palestinian Territory, Occupied",
"PA" => "Panama",
"PG" => "Papua New Guinea",
"PY" => "Paraguay",
"PE" => "Peru",
"PH" => "Philippines",
"PN" => "Pitcairn",
"PL" => "Poland",
"PT" => "Portugal",
"PR" => "Puerto Rico",
"QA" => "Qatar",
"RE" => "Reunion",
"RO" => "Romania",
"RU" => "Russian Federation",
"RW" => "Rwanda",
"SH" => "Saint Helena",
"KN" => "Saint Kitts and Nevis",
"LC" => "Saint Lucia",
"PM" => "Saint Pierre and Miquelon",
"VC" => "Saint Vincent and the Grenadines",
"WS" => "Samoa",
"SM" => "San Marino",
"ST" => "Sao Tome and Principe",
"SA" => "Saudi Arabia",
"SN" => "Senegal",
"CS" => "Serbia and Montenegro",
"SC" => "Seychelles",
"SL" => "Sierra Leone",
"SG" => "Singapore",
"SK" => "Slovakia",
"SI" => "Slovenia",
"SB" => "Solomon Islands",
"SO" => "Somalia",
"ZA" => "South Africa",
"GS" => "South Georgia and the South Sandwich Islands",
"ES" => "Spain",
"LK" => "Sri Lanka",
"SD" => "Sudan",
"SR" => "Suriname",
"SJ" => "Svalbard and Jan Mayen",
"SZ" => "Swaziland",
"SE" => "Sweden",
"CH" => "Switzerland",
"SY" => "Syrian Arab Republic",
"TW" => "Taiwan, Province of China",
"TJ" => "Tajikistan",
"TZ" => "Tanzania, United Republic of",
"TH" => "Thailand",
"TL" => "Timor-Leste",
"TG" => "Togo",
"TK" => "Tokelau",
"TO" => "Tonga",
"TT" => "Trinidad and Tobago",
"TN" => "Tunisia",
"TR" => "Turkey",
"TM" => "Turkmenistan",
"TC" => "Turks and Caicos Islands",
"TV" => "Tuvalu",
"UG" => "Uganda",
"UA" => "Ukraine",
"AE" => "United Arab Emirates",
"GB" => "United Kingdom",
"US" => "United States",
"UM" => "United States Minor Outlying Islands",
"UY" => "Uruguay",
"UZ" => "Uzbekistan",
"VU" => "Vanuatu",
"VE" => "Venezuela",
"VN" => "Viet Nam",
"VG" => "Virgin Islands, British",
"VI" => "Virgin Islands, U.s.",
"WF" => "Wallis and Futuna",
"EH" => "Western Sahara",
"YE" => "Yemen",
"ZM" => "Zambia",
"ZW" => "Zimbabwe"
);

return $countries;
}

// Define wp_handle_upload() function
function custom_handle_upload($file, $overrides = false, $time = null) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    return wp_handle_upload($file, $overrides, $time);
}

// Hook into init to define wp_handle_upload()
add_action('init', 'define_custom_handle_upload');

function define_custom_handle_upload() {
    if (!function_exists('wp_handle_upload')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
}



add_action('init', 'handle_registration_form');
function handle_registration_form() {
    if (!is_admin() && isset($_POST['submit'])) {
        $username = $_POST['username'] ? $_POST['username'] : '';
        $email = $_POST['email'];
        $post_title = $_POST['post_title'];
        $post_content = $_POST['post_content'];
        $address = sanitize_text_field($_POST['address']);
        $city = sanitize_text_field($_POST['city']);
        $state = sanitize_text_field($_POST['state']);
        $zipcode = sanitize_text_field($_POST['zipcode']);
        $country = sanitize_text_field($_POST['country']);
        $correct_address = $address . ', ' . $city . ', ' . $state . ' ' . $zipcode . ', ' . $country;
        $category = $_POST['category'];
        $facilities = $_POST['facilities'];
        
        $post_data = array(
            'post_title'    => isset($_POST['post_title']) ? sanitize_text_field($_POST['post_title']) : '',
            'branch'    => isset($_POST['branch']) ? sanitize_text_field($_POST['branch']) : '',
            'owner_name'    => isset($_POST['owner_name']) ? sanitize_text_field($_POST['owner_name']) : '',
            'address'       => isset($_POST['address']) ? sanitize_text_field($_POST['address']) : '',
            'telephone'     => isset($_POST['telephone']) ? sanitize_text_field($_POST['telephone']) : '',
            'emailadd'      => isset($_POST['emailadd']) ? sanitize_text_field($_POST['emailadd']) : '',
            'facebook'      => isset($_POST['facebook']) ? esc_url_raw($_POST['facebook']) : '',
            'instagram'     => isset($_POST['instagram']) ? esc_url_raw($_POST['instagram']) : '',
            'tiktok'        => isset($_POST['tiktok']) ? esc_url_raw($_POST['tiktok']) : '',
            'post_content'  => isset($_POST['post_content']) ? sanitize_text_field($_POST['post_content']) : '',
            'cheap_course'  => isset($_POST['cheap_course']) ? sanitize_text_field($_POST['cheap_course']) : '',
            'expensive_course' => isset($_POST['expensive_course']) ? sanitize_text_field($_POST['expensive_course']) : '',
            'babychairs'    => isset($_POST['babychairs']) ? $_POST['babychairs'] : array(),
            'childmenu'     => isset($_POST['childmenu']) ? $_POST['childmenu'] : array(),
            'barier_free_access' => isset($_POST['barier_free_access']) ? $_POST['barier_free_access'] : array(),
            'carry_stroller' => isset($_POST['carry_stroller']) ? $_POST['carry_stroller'] : array(),
            'baby_station'  => isset($_POST['baby_station']) ? $_POST['baby_station'] : array(),
            'painting_materials' => isset($_POST['painting_materials']) ? $_POST['painting_materials'] : array(),
            'provide_toys'  => isset($_POST['provide_toys']) ? $_POST['provide_toys'] : array(),
            'indoor_play'   => isset($_POST['indoor_play']) ? $_POST['indoor_play'] : array(),
            'outdoor_play'  => isset($_POST['outdoor_play']) ? $_POST['outdoor_play'] : array(),
            'playground_prov' => isset($_POST['playground_prov']) ? $_POST['playground_prov'] : array(),
            'start_time'  => isset($_POST['start_time']) ? $_POST['start_time'] : array(),
            'end_time' => isset($_POST['end_time']) ? $_POST['end_time'] : array(),
            'google_reviews' => isset($_POST['google_reviews']) ? $_POST['google_reviews'] : array(),
            'google_url' => isset($_POST['google_url']) ? $_POST['google_url'] : array(),
            //'post_images'  => isset($_POST['post_images']) ? $_POST['post_images'] : array(),
            
        );
        
        // Create user as pending
        if ( !is_user_logged_in() ) {
			$user_data = array(
				'user_login'    => $username,
				'user_email'    => $email,
				'role'          => 'shop_manager' // Set user role to pending
			);
			$user_id = wp_insert_user($user_data);
			wp_update_user(array('ID' => $user_id, 'user_status' => 2));
			 update_user_meta($user_id, 'ur_user_user_status', '0');
        	 update_user_meta($user_id, 'ur_user_status', '0');
        	update_user_meta($user_id, 'wp_capabilities', array('pending' => true));

		}else{
			$user_id = get_current_user_id();
		}
       
        //wp_user_level

        
        if (!is_wp_error($user_id)) {
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'users';
            $wpdb->update(
                $table_name,
                array('user_status' => 1),
                array('ID' => $user_id)
            );
            $user = new WP_User($user_id);
            $user->set_role('shop_manager');
            
            $post_args = array(
                'post_title'    => $post_title,
                'post_content'  => $post_content,
                'post_status'   => is_user_logged_in() ? 'publish' : 'pending', // Set post status to pending for review
                'post_author'   => $user_id,
                'post_type'     => 'post'
            );
            
            $post_id = wp_insert_post($post_args);
        
            
            if (!empty($_FILES['post_images']['name'])) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                
                $files = $_FILES['post_images'];
                $attachment_ids = array();
                $first_image_id = null; // Track the attachment ID of the first image
            
                foreach ($files['name'] as $key => $value) {
                    if ($files['name'][$key]) {
                        $file = array(
                            'name'     => $files['name'][$key],
                            'type'     => $files['type'][$key],
                            'tmp_name' => $files['tmp_name'][$key],
                            'error'    => $files['error'][$key],
                            'size'     => $files['size'][$key]
                        );
            
                        $_FILES = array('upload_file' => $file);
                        $attachment_id = media_handle_upload('upload_file', $post_id);
                        
                        if (is_wp_error($attachment_id)) {
                            // Error handling for file upload
                            echo 'Error uploading file: ' . $attachment_id->get_error_message();
                        } else {
                            // File upload successful, add attachment ID to the array
                            $attachment_ids[] = $attachment_id;
            
                            // Set the first image ID
                            if ($first_image_id === null) {
                                $first_image_id = $attachment_id;
                            }
                        }
                    }
                }
            
                // Set the first uploaded image as the featured image
                if ($first_image_id !== null) {
                    set_post_thumbnail($post_id, $first_image_id);
                }
            
                // Update post meta with the array of attachment IDs
                update_post_meta($post_id, 'post_images', $attachment_ids);
            }


            
            
                if (isset($_POST['category'])) {
                    $category_id = intval($_POST['category']);
                    $taxonomy = 'category';
                    wp_set_object_terms($post_id, $category_id, $taxonomy);
                }
                
                
                if (isset($_POST['facilities'])) {
                    $facilities_id = intval($_POST['facilities']);
                    $taxonomy = 'facilities';
                    wp_set_object_terms($post_id, $facilities_id, $taxonomy);
                }
                
                
                update_post_meta($post_id, 'codespacing_progress_map_address', $correct_address);
            
            
             if ($post_id) {
                // Update post meta with form data
                foreach ($post_data as $key => $value) {
                    if ($key !== 'post_title') {
                        if (is_array($value)) {
                            $value = array_map('sanitize_text_field', $value);
                        } else {
                            $value = sanitize_text_field($value);
                        }
                        update_post_meta($post_id, $key, $value);
                    }
                }
                

        
            } 
            
            if (!is_wp_error($post_id)) {
				
				if(is_user_logged_in()){
					// Post submission successful
                echo '<div class="alert-message">Vielen Dank, dass Sie Ihr Restaurant eingereicht haben! Wir haben Ihre Restaurantdaten erhalten und freuen uns, Sie bald digital begrüßen zu dürfen. <a href="https://kidsfriendly.world/my-account/view-restaurant/">Klicken Sie hier, um es anzuzeigen </a></div>';

                // Send email notification to admin
                $admin_email = get_option('admin_email');
                $subject = 'New Restaurant Submitted Successfully';
                // Plain text content
                $message = "A new registered restaurant submitted successfully.\n\n";
                $message .= "Please review it in the website admin panel.\n\n";
                $message .= "Name: " . $_POST['owner_name'] . "\n";
                $message .= "Establishment: " . $_POST['post_title'] . "\n";
                wp_mail($admin_email, $subject, $message);
				}else{
				// Post submission successful
                echo '<div class="alert-message">Vielen Dank für das Ausfüllen des Fragebogens! Wir haben Ihre Antworten erhalten und unser Team ist schon dabei die Ergebnisse zu evaluieren. Wir freuen uns Die schon bald digital begrüßen zu dürfen.
<a href="https://kidsfriendly.world/my-account/"><u>Hier anmelden</u></a></div>';

                // Send email notification to admin
                $admin_email = get_option('admin_email');
                $subject = 'New Registered User and Questionnaire for Approval';
                // Plain text content
                $message = "A new registered user with questionnaire has been submitted for approval.\n\n";
                $message .= "Please review it in the website admin panel.\n\n";
                $message .= "Name: " . $_POST['owner_name'] . "\n";
                $message .= "Establishment: " . $_POST['post_title'] . "\n";
                wp_mail($admin_email, $subject, $message);
				}
            } else {
                // Error handling for post submission
                echo 'Error submitting post: ' . $post_id->get_error_message();
            }
        } else {
            // Error handling for user registration
            echo '<div class="alert-message">Error registering user: ' . $user_id->get_error_message().'</div>';
        }
    }
}

add_action('wp_head','map_css');
function map_css(){
?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  

<style>
.ast-builder-footer-grid-columns.site-below-footer-inner-wrap.ast-builder-grid-row {
    display: none;
}
.ast-primary-header-bar, .site-below-footer-wrap[data-section="section-below-footer-builder"] {
    background-color: #dabdbd00;
    border: 0;
}
a.ast-header-account-link.ast-header-account-type-text.ast-account-action-link {
    color: white;
    font-weight: 700;
}
.ast-header-account-wrap {
    background: #679f6e;
    padding: 9px 25px;
    border-radius: 10px;
}
.ast-footer-copyright {
    display: none;
}
.cspml_listings_area_map1148.cspm-row,
.cspml_options_bar_map1148.cspm-row{
    display:none;
}
.search_form_container_map1148.cspm_top_element.cspm_border_shadow.cspm_border_radius {
    display: none !important;
}
/*.cspml_list_and_filter_container.cspm-row.no-margin {
    display: none;
}*/
.cspml_resize_map.cspm_bg_hex_hover.cspm_border_top_radius.cspm_border_shadow,
.cspm_heatmap_btn.cspm_map_btn.cspm_active_btn.cspm_bg_rgb_hover.cspm_border_shadow.cspm_border_radius,
.countries_btn.cspm_map_btn.cspm_top_btn.cspm_expand_map_btn.cspm_border_shadow.cspm_border_radius, 
div#map1148,
.cspm_recenter_map_btn.cspm_map_btn.cspm_border_shadow.cspm_border_radius {
    display: none !important;
}
</style>
<?php
}

function require_approval_for_new_users($user_id) {
    $user = new WP_User($user_id);
    $user->set_role('pending'); // Set user role to 'pending' (or any custom role you create)
}
add_action('user_register', 'require_approval_for_new_users');
add_action('init', 'handle_update_restaurants_kids');
function handle_update_restaurants_kids() {

    if (!is_admin() && isset($_POST['update'])) {
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $category = isset($_POST['category']) ? intval($_POST['category']) : 0;
        $facilities = isset($_POST['facilities']) ? intval($_POST['facilities']) : 0;
        $owner_name = isset($_POST['owner_name']) ? sanitize_text_field($_POST['owner_name']) : '';
        $post_title = isset($_POST['post_title']) ? sanitize_text_field($_POST['post_title']) : '';


        if ($post_id) {
            update_post_meta($post_id, 'owner_name', $owner_name);
            update_post_meta($post_id, 'post_title', $post_title);

            // Update categories
            if ($category) {
                wp_set_object_terms($post_id, $category, 'category');
            }

            // Update facilities
            if ($facilities) {
                wp_set_object_terms($post_id, $facilities, 'facilities');
            }
        }

         if (!is_wp_error($post_id)) {
                // Post submission successful
                echo '<div class="alert-message">Sie haben Ihr Restaurant erfolgreich aktualisiert!</div>';

            } else {
                // Error handling for post submission
                echo 'Error submitting post: ' . $post_id->get_error_message();
            }
       
    }

}
function add_custom_footer() {
    ?>
<footer class=" text-white pt-4 pb-4">
    <div class="container custom-footer">
            <div class="row">
                <div class="col-md-4">
                    <div class="footer-logo mb-3">
                       <img src="/wp-content/uploads/2024/03/Group.png"/>
                    </div>
                    <p>Willkommen bei unserer Initiative, die sich der Förderung eines familienfreundlichen kulinarischen Erlebnisses widmet. Entdecken Sie eine kuratierte Auswahl an Restaurants, Cafés und Bars, die Wert darauf legen, eine einladende Atmosphäre für Familien mit Kindern zu schaffen.</p>
                </div>
                <div class="col-md-4">
                    <h5>Kontaktiere uns</h5>
                    <p class="m-0">Angad Strategieberatung für Business Development und Projekte</p>
                    <p class="m-0">Rebgasse 53</p>
                    <p class="m-0">4058 Basel</p>
                    <p class="m-0">Email: <a href="mailto:hi@kidsfriendly.world" class="text-white">hi@kidsfriendly.world</a></p>
                </div>
                <div class="col-md-4">
                    <h5>Melden Sie sich für E-Mail-Updates an</h5>
                    <?php echo do_shortcode('[noptin-form id=700]'); ?>
                    <p class="m-0">Melden Sie sich mit Ihrer E-Mail-Adresse an, um Neuigkeiten und Updates zu erhalten</p>
                </div>
            </div>
            <hr class="bg-white">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">Copyright © 2024 <a href="https://kidsfriendly.world/" style="font-size:14px;">Kids Friendly</a></p>
                </div>
                <div class="col-md-6 text-md-right">
                    <a href="/" class="text-white" style="margin-right: 20px;">Home</a> 
                    <a href="/restaurants/" class="text-white" style="margin-right: 20px;">Restaurants</a> 
                    <a href="/uber-uns/" class="text-white" >Über Uns</a>
                </div>
            </div>
        </div>
</footer>
<style>
a.text-white {
    color: #679f6e !important;
    font-weight: 500 !important;
    font-size: 14px;
}

body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.footer-logo {
    font-size: 2rem;
}
footer .col-md-6.text-md-right {
    float: right;
    text-align: right;
}
a.text-white {
    color: #679f6e !important;
    font-weight: 500 !important;
}
/*footer a, footer a.text-white {*/
/*    color: white;*/
/*    text-decoration: none;*/
/*}*/

footer a:hover {
    text-decoration: underline;
}
footer h5, footer a {
    color: #679f6e !important;
    font-weight: 600;
    font-size: 17px;
}
footer .col-md-6.text-md-right a {
    color: #679f6e !important;
    font-weight: 600;
}
footer input.noptin-form-submit {
    background-color: #679f6e !important;
    font-weight: 700;
}
footer .bg-white {
    background-color: rgb(103 159 110) !important;
}
</style>
    <?php
    // echo '<footer class="custom-footer text-center w-100 p-5">';
    // echo '<p>Copyright © 2024 <a href="https://kidsfriendly.world/">Kids Friendly</a></p>';
    // echo '</footer>';
}
add_action('wp_footer', 'add_custom_footer');

