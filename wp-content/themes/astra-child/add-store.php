<form id="registration-form" method="post"  enctype="multipart/form-data">
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
            
            <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Wie viel kostet das günstigste Hauptgericht bei Ihnen? (Cheapest Maincourse)</label>
                    <input type="text" class="form-control" id="cheap_course" name="cheap_course" >
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <label for="post_title">Wie viel kostet das teuerste Hauptgericht bei Ihnen? (most expensive Maincourse)</label>
                    <input type="text" class="form-control" id="expensive_course" name="expensive_course" >
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
            
        </div>
    <input type="submit" name="submit" class="mt-3 w-100" value="Add Restaurant">
</form>
<style>
.rating {
      unicode-bidi: bidi-override;
      direction: rtl;
      text-align: left;
    }

    .rating>input {
      display: none;
    }

    .rating>label {
      position: relative;
      display: inline-block;
      width: 1.1em;
      font-size: 3em;
      color: #FFD700;
    }

    .rating>label::before {
      content: "\2605";
      position: absolute;
      opacity: 0;
    }

    .rating>label:hover:before,
    .rating>label:hover~label:before {
      opacity: 1 !important;
    }

    .rating>input:checked~label:before {
      opacity: 1;
    }

    .rating:hover>input:checked~label:before {
      opacity: 0.4;
    }
</style>
<script>
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
