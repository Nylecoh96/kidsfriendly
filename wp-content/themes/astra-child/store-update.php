<?php
if(isset($_GET['u'])){
    $get_data = get_post_meta($_GET['u']);
    $post_id = $_GET['u'];
    $post_data = get_post_meta($post_id);
    
   
    $category = 'category';
    $category_terms = wp_get_post_terms($post_id, $category, array('fields' => 'ids'));
    
    $facilities = 'facilities';
    $facilities_terms = wp_get_post_terms($post_id, $facilities, array('fields' => 'ids'));
    $address = get_post_meta($post_id, 'codespacing_progress_map_address', true) ? : '';
    $address_explode = explode(',',$address);

    // echo '<pre>';
    // print_r(get_post_meta($post_id, 'post_images', true));
    // echo '</pre>';
}

?>
<form id="update-form" method="post"  enctype="multipart/form-data">
     <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="post_title">Wählen Sie Restauranttyp</label>
                <select class="form-select" id="category" name="category" required>
                    <option selected disabled value="">Wählen Kategorie</option>
            				<?php
            					$taxonomy = 'category';
            					$terms = get_terms( array(
            						'taxonomy' => $taxonomy,
            						'hide_empty' => false, // Set to true if you want to hide empty terms
            					) );
            					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            						foreach ( $terms as $term ) { ?>
            						    <option value="<?php echo $term->term_id; ?>" <?php echo in_array($term->term_id,$category_terms) ? 'selected' : ''?>><?php echo $term->name; ?></option>
            					<?php }
            					}
            
            			?>
                  </select>
            </div>
        </div>
        
         <div class="col-md-6">
            <div class="form-group">
                <label for="post_title">W채hlen Sie Einrichtungen</label>
                <select class="form-select" id="facilities" name="facilities" required>
                    <option selected disabled value="">W채hlen Sie Einrichtungen</option>
            				<?php
            					$taxonomy = 'facilities'; // Replace 'category' with the name of the taxonomy you want to retrieve terms from
            
            					$terms = get_terms( array(
            						'taxonomy' => $taxonomy,
            						'hide_empty' => false, // Set to true if you want to hide empty terms
            					) );
            
            					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            						foreach ( $terms as $term ) { ?>
            							<option value="<?php echo $term->term_id; ?>" <?php echo in_array($term->term_id,$facilities_terms) ? 'selected' : ''?>><?php echo $term->name; ?></option>
            					<?php }
            					}
            
            			?>
                  </select>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label for="post_title">Wie lautet Ihr Name?</label>
                <input type="text" class="form-control" id="owner_name" name="owner_name" value="<?php echo get_post_meta($post_id, 'owner_name', true); ?>">
            </div>
        </div>
        
         <div class="col-md-4"> 
                <div class="form-group">
                    <label for="post_title">Wie lautet der Name Ihres Geschäfts? *</label>
                    <input type="text" class="form-control" id="post_title" name="post_title" required value="<?php echo get_the_title($post_id); ?>">
                </div>
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label for="post_title">Restaurantfiliale?</label>
                    <input type="text" class="form-control" id="branch" name="branch" value="<?php echo get_post_meta($post_id, 'branch', true); ?>">
                </div>
            </div>
            
             <div class="col-md-4">
                <div class="form-group">
                    <label for="post_title">Wie lautet die Adresse ihres Geschäfts?</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo $address_explode[0]; ?>">
                </div>
            </div>
             <div class="col-md-2">
                <div class="form-group">
                    <label for="post_title">City</label>
                    <input type="text" class="form-control" id="city" name="city" value="<?php echo $address_explode[1]; ?>">
                </div>
            </div>
             <div class="col-md-2">
                <div class="form-group">
                    <label for="post_title">State</label>
                    <input type="text" class="form-control" id="state" name="state" value="<?php echo $address_explode[2] ?>">
                </div>
            </div>
             <div class="col-md-2">
                <div class="form-group">
                    <label for="post_title">Code</label>
                    <input type="text" class="form-control" id="code" name="zipcode" value="<?php echo explode(' ',$address_explode[2])[3]; ?>">
                </div>
            </div>
             <div class="col-md-2">
                <div class="form-group">
                    <label for="post_title">Country</label>
                    <select name="country"  class="form-control">
                        <?php foreach (get_countries() as $code => $name) { ?>
                            <option value="<?php echo $code; ?>" <?php echo $code == $address_explode[3] ? 'selected="selected"' : ''; ?> <?php echo $post_data['zipcode'] == $code ? 'selected' : ''; ?>><?php echo $name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
             <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Wie lautet Ihre Telefonnummer?</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo get_post_meta($post_id, 'telephone', true); ?>">
                </div>
            </div>
             <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Auf welcher Email-Adresse können die Gäste Sie erreichen?</label>
                    <input type="text" class="form-control" id="emailadd" name="emailadd" value="<?php echo get_post_meta($post_id, 'emailadd', true); ?>">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Falls vorhanden, verlinken Sie die Accounts Ihrer sozialen Medien hier.</label>
                    <input type="url" class="form-control" id="facebook" name="facebook" placeholder="Facebook Url" value="<?php echo get_post_meta($post_id, 'facebook', true); ?>"><br>
                    <input type="url" class="form-control" id="instagram" name="instagram" placeholder="Instagram Url" value="<?php echo get_post_meta($post_id, 'instagram', true); ?>"><br>
                    <input type="url" class="form-control" id="tiktok" name="tiktok" placeholder="Tiktok Url" value="<?php echo get_post_meta($post_id, 'tiktok', true); ?>"><br>
                </div>
            </div>
            <!--Upload Image-->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Laden Sie nun hier Bilder Ihres Geschäfts hoch.</label>
                    <label for="post_images">Upload Images (up to 4):</label><br>
                    <input type="file" id="post_images" name="post_images[]" accept="image/*" multiple value="<?php echo get_post_meta($post_id, 'post_images', true) ? implode(',', get_post_meta($post_id, 'post_images', true)): ''; ?>"><br><br>
                    <div id="selected_images"></div>
                </div>
            </div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('post_images').addEventListener('change', handleFileSelect);
    });

    function handleFileSelect(event) {
        var files = event.target.files;
        var output = document.getElementById('selected_images');

        if (files.length > 4) {
            alert('You can only upload up to 4 images.');
            event.target.value = ''; // Clear the file input
            return;
        }

        output.innerHTML = ''; // Clear previous selections

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (!file.type.match('image.*')) {
                continue;
            }
            var reader = new FileReader();
            reader.onload = (function(file) {
                return function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.width = 100; // Adjust as needed
                    img.height = 100; // Adjust as needed
                    output.appendChild(img);
                };
            })(file);
            reader.readAsDataURL(file);
        }
    }
</script>
            
           <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">In einem Satz, was ist das __(name of restaurant should be taken here automatically from above)___ ( first field, one sentence desc.) Beschreiben Sie Ihr Konzept. Was macht Ihr Geschäft aus? (Here they should be able to write a short summary allow two fields one only for one sentence, the other longer)</label>
                    <textarea name="post_content" id="post_content" required><?php echo get_post_field('post_content', $post_id);; ?></textarea>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Wie viel kostet das günstigste Hauptgericht bei Ihnen? (Cheapest Maincourse)</label>
                    <input type="text" class="form-control" id="cheap_course" name="cheap_course" value="<?php echo get_post_meta($post_id, 'cheap_course', true); ?>">
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Wie viel kostet das teuerste Hauptgericht bei Ihnen? (most expensive Maincourse)</label>
                    <input type="text" class="form-control" id="expensive_course" name="expensive_course" value="<?php echo get_post_meta($post_id, 'expensive_course', true); ?>">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Verfügen Sie über Kinderhochstühle?</label>
                    <br>
                    <?php
                    $babychairs = get_post_meta($post_id, 'babychairs', true) ? : array();
                    ?>
                     <label for="babychairs1">
                        <input type="checkbox" id="babychairs1" name="babychairs" value="yes" <?php echo in_array("yes", $babychairs) ? 'checked' : ''; ?>>
                        Ja
                    </label><br>
                    <label for="babychairs2">
                        <input type="checkbox" id="babychairs2" name="babychairs" value="no" <?php echo in_array("no", $babychairs) ? 'checked' : ''; ?>>
                        Nein 
                    </label><br>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Bieten Sie spezielle Kindermenus an?</label>
                    <br>
                     <?php
                    $childmenu = get_post_meta($post_id, 'childmenu', true) ? : array();
                    ?>
                     <label for="childmenu1">
                        <input type="checkbox" id="childmenu1" name="childmenu" value="yes" <?php echo in_array("yes", $childmenu) ? 'checked' : ''; ?>>
                        Ja
                    </label><br>
                    <label for="childmenu2">
                        <input type="checkbox" id="childmenu2" name="childmenu" value="no" <?php echo in_array("no", $childmenu) ? 'checked' : ''; ?>>
                        Nein 
                    </label><br>
                </div>
            </div>
            
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Sind Ihre Räumlichkeiten Barrierefrei oder bieten Sie Ihren Gästen Hilfe beim tragen des Kinderwagens an?</label>
                    <br>
                     <?php
                    $barier_free_access = get_post_meta($post_id, 'barier_free_access', true) ? : array();
                    ?>
                     <label for="barier_free_access1">
                        <input type="checkbox" id="barier_free_access1" name="barier_free_access" value="yes" <?php echo in_array("yes", $barier_free_access) ? 'checked' : ''; ?>>
                        Ja
                    </label><br>
                    <label for="barier_free_access2">
                        <input type="checkbox" id="barier_free_access2" name="barier_free_access" value="no" <?php echo in_array("no", $barier_free_access) ? 'checked' : ''; ?>>
                        Nein 
                    </label><br>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Sind mehr als zwei Stufen bis zum Gastraum zu bewältigen? Falls ja  bieten Sie Ihren Gästen Hilfe beim tragen des Kinderwagens an? </label>
                    <br>
                    <?php
                    $carry_stroller = get_post_meta($post_id, 'carry_stroller', true) ? : array();
                    ?>
                     <label for="carry_stroller1">
                        <input type="checkbox" id="carry_stroller1" name="carry_stroller" value="yes"  <?php echo in_array("yes", $carry_stroller) ? 'checked' : ''; ?>>
                        Ja
                    </label><br>
                    <label for="carry_stroller2">
                        <input type="checkbox" id="carry_stroller2" name="carry_stroller" value="no"  <?php echo in_array("no", $carry_stroller) ? 'checked' : ''; ?>>
                        Nein 
                    </label><br>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Verfügen Ihre Räumlichkeiten über eine Wickelstation?</label>
                    <br>
                    <?php
                    $baby_station = get_post_meta($post_id, 'baby_station', true) ? : array();
                    ?>
                     <label for="baby_station1">
                        <input type="checkbox" id="baby_station1" name="baby_station" value="yes" <?php echo in_array("yes", $baby_station) ? 'checked' : ''; ?>>
                        Ja
                    </label><br>
                    <label for="baby_station2">
                        <input type="checkbox" id="baby_station2" name="baby_station" value="no" <?php echo in_array("no", $baby_station) ? 'checked' : ''; ?>>
                        Nein 
                    </label><br>
                </div>
            </div>
            
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Bieten Sie Malutensilien für Kinder an?</label>
                    <br>
                    <?php
                    $painting_materials = get_post_meta($post_id, 'painting_materials', true) ? : array();
                    ?>
                     <label for="painting_materials1">
                        <input type="checkbox" id="painting_materials1" name="painting_materials" value="yes" <?php echo in_array("yes", $painting_materials) ? 'checked' : ''; ?>>
                        Ja
                    </label><br>
                    <label for="painting_materials2">
                        <input type="checkbox" id="painting_materials2" name="painting_materials" value="no" <?php echo in_array("no", $painting_materials) ? 'checked' : ''; ?>>
                        Nein 
                    </label><br>
                </div>
            </div>
            
            
             <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Gibt es Kinderspielzeug, mit denen die kleinen Gäste vor Ort spielen können?</label>
                    <br>
                    <?php
                    $provide_toys = get_post_meta($post_id, 'provide_toys', true) ? : array();
                    ?>
                     <label for="provide_toys1">
                        <input type="checkbox" id="provide_toys1" name="provide_toys" value="yes" <?php echo in_array("yes", $provide_toys) ? 'checked' : ''; ?>>
                        Ja
                    </label><br>
                    <label for="provide_toys2">
                        <input type="checkbox" id="provide_toys2" name="provide_toys" value="no"  <?php echo in_array("no", $provide_toys) ? 'checked' : ''; ?>>
                        Nein 
                    </label><br>
                </div>
            </div>
            
             <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Bieten Sie eine Indoor Spielecke an?</label>
                    <br>
                    <?php
                    $indoor_play = get_post_meta($post_id, 'indoor_play', true) ? : array();
                    ?>
                     <label for="indoor_play1">
                        <input type="checkbox" id="indoor_play1" name="indoor_play" value="yes" <?php echo in_array("yes", $indoor_play) ? 'checked' : ''; ?>>
                        Ja
                    </label><br>
                    <label for="indoor_play2">
                        <input type="checkbox" id="indoor_play2" name="indoor_play" value="no" <?php echo in_array("no", $indoor_play) ? 'checked' : ''; ?>>
                        Nein 
                    </label><br>
                </div>
            </div>
            
            
             <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Bieten Sie eine Outdoor Spielecke an?</label>
                    <br>
                    <?php
                    $outdoor_play = get_post_meta($post_id, 'outdoor_play', true) ? : array();
                    ?>
                     <label for="outdoor_play1">
                        <input type="checkbox" id="outdoor_play1" name="outdoor_play" value="yes" <?php echo in_array("yes", $outdoor_play) ? 'checked' : ''; ?>>
                        Ja
                    </label><br>
                    <label for="outdoor_play2">
                        <input type="checkbox" id="outdoor_play2" name="outdoor_play" value="no" <?php echo in_array("no", $outdoor_play) ? 'checked' : ''; ?>>
                        Nein 
                    </label><br>
                </div>
            </div>
            
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Gibt es einen Spielplatz auf dem Gelände oder in Sichtweite des Geländes?</label>
                    <br>
                    <?php
                    $playground_prov = get_post_meta($post_id, 'playground_prov', true) ? : array();
                    ?>
                     <label for="playground_prov1">
                        <input type="checkbox" id="playground_prov1" name="playground_prov" value="yes" <?php echo in_array("yes", $playground_prov) ? 'checked' : ''; ?>>
                        Ja
                    </label><br>
                    <label for="playground_prov2">
                        <input type="checkbox" id="playground_prov2" name="playground_prov" value="no" <?php echo in_array("no", $playground_prov) ? 'checked' : ''; ?>>
                        Nein 
                    </label><br>
                </div>
            </div>
            <?php 
            $star_rating = get_post_meta($post_id, 'google_reviews', true) ? : '0';
            ?>
               <div class="col-md-12 star-rating-cus">
                <div class="container m-0 p-0">
                  <h6 class="m-0">Star Rating</h6>
                    <span><img id="star1" onclick="toggleStar(1)" class="star <?php echo $star_rating >= 1 ? 'checked'  : ''; ?>" src="<?php echo home_url('/wp-content/uploads/2024/04/empty.png'); ?>" alt="kids"/></span>
                    <span><img id="star2" onclick="toggleStar(2)" class="star <?php echo $star_rating >= 2 ? 'checked'  : ''; ?>" src="<?php echo home_url('/wp-content/uploads/2024/04/empty.png'); ?>" alt="kids"/></span>
                    <span><img id="star3" onclick="toggleStar(3)" class="star <?php echo $star_rating >= 3 ? 'checked'  : ''; ?>" src="<?php echo home_url('/wp-content/uploads/2024/04/empty.png'); ?>" alt="kids"/></span>
                    <span><img id="star4" onclick="toggleStar(4)" class="star <?php echo $star_rating >= 4 ? 'checked'  : ''; ?>" src="<?php echo home_url('/wp-content/uploads/2024/04/empty.png'); ?>" alt="kids"/></span>
                    <span><img id="star5" onclick="toggleStar(5)" class="star <?php echo $star_rating >= 5 ? 'checked'  : ''; ?>" src="<?php echo home_url('/wp-content/uploads/2024/04/empty.png'); ?>" alt="kids"/></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="post_title">Google Reviews Rating(Total)</label>
                    <input type="number" class="form-control" id="ratingInput" name="google_reviews" min="0" max="5" step="0.5" onchange="updateStars()" value="<?php echo get_post_meta($post_id, 'google_reviews', true) ? : '0'; ?>">
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label for="post_title">Google Review Link</label>
                    <input type="url" class="form-control" id="google_url" name="google_url" value="<?php echo get_post_meta($post_id, 'google_url', true) ? : '0'; ?>">
                </div>
            </div>
            
        </div>
    <input type="hidden" name="post_id" value="<?php echo $_GET['u']; ?>"/>
    <input type="submit" name="update" class="mt-3 w-100" value="Update Store">
</div>
</form>
<style>
.star-rating-cus .fa-star.checked {
    color: orange !important;
}
.star-rating-cus .half {
  background: linear-gradient(90deg, orange 50%, #e5e5e5 50%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.star .half{
     background: linear-gradient(90deg, orange 50%, #e5e5e5 50%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
}
.star-rating-cus .fa.fa-star {
    font-size: 30px;
    color: #e5e5e5;
}
svg {
    fill: #c7c7c7;
}
.star-rating-cus .star.checked {
    fill: orange !important;
}
.col-md-12.star-rating-cus img {
    width: 30px;
}
</style>
<script>
    // Function to toggle the star color and calculate the rating
      function toggleStar(starNumber) {
        var ratingInput = document.getElementById("ratingInput");
        ratingInput.value = starNumber;
        updateStars();
      }
    
      // Function to update the appearance of the stars based on the input rating
      function updateStars() {
        var ratingInput = document.getElementById("ratingInput");
        var rating = parseFloat(ratingInput.value);
        var fullimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/rate.png';
        var emptyimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/empty.png';
        var halfimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/half-1.png';
        for (var i = 1; i <= 5; i++) {
          var star = document.getElementById('star' + i);
          if (i <= Math.floor(rating)) {
            // Full star
            star.classList.add('checked');
            star.classList.remove('half'); 
            star.setAttribute('src', fullimg);
          } else if (i === Math.ceil(rating) && rating % 1 !== 0) {
            // Half star
            star.classList.add('checked');
            star.classList.add('half');
            star.setAttribute('src', halfimg);
          } else {
            // Empty star
            star.classList.remove('checked');
            star.classList.remove('half'); 
            star.setAttribute('src', emptyimg);
          }
        }
      }
      
      
      
    jQuery(document).ready(function($){
        
        
        $('#custom_images').change(function() {
            previewImages(this);
        });

        function previewImages(input) {
            $('.image-preview').empty();
            // Check if the number of selected files is greater than 4
            if (input.files.length > 4) {
                alert("Please select only 4 images.");
                $('#custom_images').val(''); // Clear the file input
                return;
            }
            // Display preview for each selected image
            if (input.files && input.files.length > 0) {
                for (var i = 0; i < input.files.length; i++) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('.image-preview').append('<img src="' + e.target.result + '" style="max-width: 300px;">');
                    }
                    reader.readAsDataURL(input.files[i]);
                }
            }
            // Display preview at the bottom
            $('.image-preview-bottom').empty();
            if (input.files && input.files.length > 0) {
                for (var i = 0; i < input.files.length; i++) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('.image-preview-bottom').append('<img src="' + e.target.result + '" style="max-width: 100px; margin-right: 10px;">');
                    }
                    reader.readAsDataURL(input.files[i]);
                }
            }
        }
    });
</script>