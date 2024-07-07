<form id="registration-form" method="post"  enctype="multipart/form-data">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    
    <div class="spacer mt-2 mb-2"></div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="post_title">Wählen Sie Restauranttyp</label>
                <select class="form-select" id="category" name="category" required>
                    <option selected disabled value="">Wählen Kategorie</option>
            				<?php
            					$taxonomy = 'category'; // Replace 'category' with the name of the taxonomy you want to retrieve terms from
            
            					$terms = get_terms( array(
            						'taxonomy' => $taxonomy,
            						'hide_empty' => false, // Set to true if you want to hide empty terms
            					) );
            
            					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            						foreach ( $terms as $term ) {
            							echo '<option value="' .$term->term_id. '">' . esc_html( $term->name ) . '</option>';
            						}
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
            						foreach ( $terms as $term ) {
            							echo '<option value="' .$term->term_id. '">' . esc_html( $term->name ) . '</option>';
            						}
            					}
            
            			?>
                  </select>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label for="post_title">Wie lautet Ihr Name?</label>
                <input type="text" class="form-control" id="owner_name" name="owner_name" >
            </div>
        </div>
        
         <div class="col-md-4"> 
                <div class="form-group">
                    <label for="post_title">Wie lautet der Name Ihres Geschäfts? *</label>
                    <input type="text" class="form-control" id="post_title" name="post_title" required>
                </div>
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label for="post_title">Restaurantfiliale?</label>
                    <input type="text" class="form-control" id="branch" name="branch" >
                </div>
            </div>
            
             <div class="col-md-4">
                <div class="form-group">
                    <label for="post_title">Wie lautet die Adresse ihres Geschäfts?</label>
                    <input type="text" class="form-control" id="address" name="address" >
                </div>
            </div>
             <div class="col-md-2">
                <div class="form-group">
                    <label for="post_title">City</label>
                    <input type="text" class="form-control" id="city" name="city" >
                </div>
            </div>
             <div class="col-md-2">
                <div class="form-group">
                    <label for="post_title">State</label>
                    <input type="text" class="form-control" id="state" name="state" >
                </div>
            </div>
             <div class="col-md-2">
                <div class="form-group">
                    <label for="post_title">Code</label>
                    <input type="text" class="form-control" id="code" name="zipcode" >
                </div>
            </div>
             <div class="col-md-2">
                <div class="form-group">
                    <label for="post_title">Country</label>
                    <select name="country"  class="form-control">
                        <?php foreach (get_countries() as $code => $name) { ?>
                            <option value="<?php echo $code; ?>" <?php echo $code == "DE" ? 'selected="selected"' : ''; ?>><?php echo $name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
             <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Wie lautet Ihre Telefonnummer?</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" >
                </div>
            </div>
             <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Auf welcher Email-Adresse können die Gäste Sie erreichen?</label>
                    <input type="text" class="form-control" id="emailadd" name="emailadd" >
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Falls vorhanden, verlinken Sie die Accounts Ihrer sozialen Medien hier.</label>
                    <input type="url" class="form-control" id="facebook" name="facebook" placeholder="Facebook Url"><br>
                    <input type="url" class="form-control" id="instagram" name="instagram" placeholder="Instagram Url"><br>
                    <input type="url" class="form-control" id="tiktok" name="tiktok" placeholder="Tiktok Url"><br>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Laden Sie nun hier Bilder Ihres Geschäfts hoch.</label>
                    <label for="post_images">Upload Images (up to 4):</label><br>
                    <input type="file" id="post_images" name="post_images[]" accept="image/*" multiple><br><br>
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
                    <textarea name="post_content" id="post_content" required></textarea>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Wie viel kostet das günstigste Hauptgericht bei Ihnen? (Cheapest Maincourse)</label>
                    <input type="text" class="form-control" id="cheap_course" name="cheap_course" >
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Wie viel kostet das teuerste Hauptgericht bei Ihnen? (most expensive Maincourse)</label>
                    <input type="text" class="form-control" id="expensive_course" name="expensive_course" >
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Startzeit</label>
                    <input type="text" class="form-control" id="start_time" name="start_time" >
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Endzeit</label>
                    <input type="text" class="form-control" id="end_time" name="end_time" >
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Verfügen Sie über Kinderhochstühle?</label>
                    <br>
                     <label for="babychairs1">
                        <input type="checkbox" id="babychairs1" name="babychairs[]" value="yes">
                        Ja
                    </label><br>
                    <label for="babychairs2">
                        <input type="checkbox" id="babychairs2" name="babychairs[]" value="no">
                        Nein 
                    </label><br>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Bieten Sie spezielle Kindermenus an?</label>
                    <br>
                     <label for="childmenu1">
                        <input type="checkbox" id="childmenu1" name="childmenu[]" value="yes">
                        Ja
                    </label><br>
                    <label for="childmenu2">
                        <input type="checkbox" id="childmenu2" name="childmenu[]" value="no">
                        Nein 
                    </label><br>
                </div>
            </div>
            
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Sind Ihre Räumlichkeiten Barrierefrei oder bieten Sie Ihren Gästen Hilfe beim tragen des Kinderwagens an?</label>
                    <br>
                     <label for="barier_free_access1">
                        <input type="checkbox" id="barier_free_access1" name="barier_free_access[]" value="yes">
                        Ja
                    </label><br>
                    <label for="barier_free_access2">
                        <input type="checkbox" id="barier_free_access2" name="barier_free_access[]" value="no">
                        Nein 
                    </label><br>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Sind mehr als zwei Stufen bis zum Gastraum zu bewältigen? Falls ja  bieten Sie Ihren Gästen Hilfe beim tragen des Kinderwagens an? </label>
                    <br>
                     <label for="carry_stroller1">
                        <input type="checkbox" id="carry_stroller1" name="carry_stroller[]" value="yes">
                        Ja
                    </label><br>
                    <label for="carry_stroller2">
                        <input type="checkbox" id="carry_stroller2" name="carry_stroller[]" value="no">
                        Nein 
                    </label><br>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Verfügen Ihre Räumlichkeiten über eine Wickelstation?</label>
                    <br>
                     <label for="baby_station1">
                        <input type="checkbox" id="baby_station1" name="baby_station[]" value="yes">
                        Ja
                    </label><br>
                    <label for="baby_station2">
                        <input type="checkbox" id="baby_station2" name="baby_station[]" value="no">
                        Nein 
                    </label><br>
                </div>
            </div>
            
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Bieten Sie Malutensilien für Kinder an?</label>
                    <br>
                     <label for="painting_materials1">
                        <input type="checkbox" id="painting_materials1" name="painting_materials[]" value="yes">
                        Ja
                    </label><br>
                    <label for="painting_materials2">
                        <input type="checkbox" id="painting_materials2" name="painting_materials[]" value="no">
                        Nein 
                    </label><br>
                </div>
            </div>
            
            
             <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Gibt es Kinderspielzeug, mit denen die kleinen Gäste vor Ort spielen können?</label>
                    <br>
                     <label for="provide_toys1">
                        <input type="checkbox" id="provide_toys1" name="provide_toys[]" value="yes">
                        Ja
                    </label><br>
                    <label for="provide_toys2">
                        <input type="checkbox" id="provide_toys2" name="provide_toys[]" value="no">
                        Nein 
                    </label><br>
                </div>
            </div>
            
             <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Bieten Sie eine Indoor Spielecke an?</label>
                    <br>
                     <label for="indoor_play1">
                        <input type="checkbox" id="indoor_play1" name="indoor_play[]" value="yes">
                        Ja
                    </label><br>
                    <label for="indoor_play2">
                        <input type="checkbox" id="indoor_play2" name="indoor_play[]" value="no">
                        Nein 
                    </label><br>
                </div>
            </div>
            
            
             <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Bieten Sie eine Outdoor Spielecke an?</label>
                    <br>
                     <label for="outdoor_play1">
                        <input type="checkbox" id="outdoor_play1" name="outdoor_play[]" value="yes">
                        Ja
                    </label><br>
                    <label for="outdoor_play2">
                        <input type="checkbox" id="outdoor_play2" name="outdoor_play[]" value="no">
                        Nein 
                    </label><br>
                </div>
            </div>
            
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="post_title">Gibt es einen Spielplatz auf dem Gelände oder in Sichtweite des Geländes?</label>
                    <br>
                     <label for="playground_prov1">
                        <input type="checkbox" id="playground_prov1" name="playground_prov[]" value="yes">
                        Ja
                    </label><br>
                    <label for="playground_prov2">
                        <input type="checkbox" id="playground_prov2" name="playground_prov[]" value="no">
                        Nein 
                    </label><br>
                </div>
            </div>
            
            <div class="col-md-12 star-rating-cus">
                <div class="container m-0 p-0">
                  <h6 class="m-0">Star Rating</h6>
                    <span><img id="star1" onclick="toggleStar(1)" class="star" src="<?php echo home_url('/wp-content/uploads/2024/04/empty.png'); ?>" alt="kids"/></span>
                    <span><img id="star2" onclick="toggleStar(2)" class="star" src="<?php echo home_url('/wp-content/uploads/2024/04/empty.png'); ?>" alt="kids"/></span>
                    <span><img id="star3" onclick="toggleStar(3)" class="star" src="<?php echo home_url('/wp-content/uploads/2024/04/empty.png'); ?>" alt="kids"/></span>
                    <span><img id="star4" onclick="toggleStar(4)" class="star" src="<?php echo home_url('/wp-content/uploads/2024/04/empty.png'); ?>" alt="kids"/></span>
                    <span><img id="star5" onclick="toggleStar(5)" class="star" src="<?php echo home_url('/wp-content/uploads/2024/04/empty.png'); ?>" alt="kids"/></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="post_title">Google Reviews Rating(Total)</label>
                    <input type="number" class="form-control" id="ratingInput" name="google_reviews" min="0" max="5" step="0.5" onchange="updateStars()" value="0">
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label for="post_title">Google Review Link</label>
                    <input type="url" class="form-control" id="google_url" name="google_url" >
                </div>
            </div>
            
            
            
        </div>
    <input type="submit" name="submit" class="mt-3 w-100" value="Register and Answer Questionnaire">
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


