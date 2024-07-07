<div class="row g-3 needs-validation" id="custom-search-bar">
  <div class="col-md-2">
    <div class="input-group has-validation">

      <span class="input-group-text" id="inputGroupPrepend"><img src="https://kidsfriendly.world/wp-content/uploads/2024/03/WifiHigh.png" alt="search"/></span>
    


      <div class="dropdown form-select" id="select-facilities">
        <button class="dropdown-toggle" onclick="toggleDropdown('dropdown1')">Einrichtungen</button>
        <div class="dropdown-content" id="dropdown1">
           <?php
            $taxonomy = 'facilities'; // Replace 'category' with the name of the taxonomy you want to retrieve terms from

            $terms = get_terms( array(
              'taxonomy' => $taxonomy,
              'hide_empty' => false, // Set to true if you want to hide empty terms
            ) );

            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
              foreach ( $terms as $term ) {
            ?>
            <div class="d-flex cat">
              <input type="checkbox" class="data" id="<?php echo $term->slug; ?>" name="<?php echo $term->slug; ?>" value="<?php echo $term->term_id; ?>">
              <label for="<?php echo $term->slug; ?>"><?php echo $term->name; ?></label><br>
            </div>
            <?php 
                }
            }
          ?>
        </div>
      </div>

    </div>

    
  </div>
  <div class="col-md-2">
    <div class="input-group has-validation">
      <span class="input-group-text" id="inputGroupPrepend"><img src="https://kidsfriendly.world/wp-content/uploads/2024/03/Star.png" alt="search"/></span>
      <select class="form-select" id="validationCustom04" required>
        <option selected disabled value="">Google Ratings</option>
        <option>1 Star</option>
        <option>2 Stars</option>
        <option>3 Stars</option>
        <option>4 Stars</option>
        <option>5 Stars</option>
      </select>
    </div>
  </div>
  <div class="col-md-2">
    <div class="input-group has-validation">
      <span class="input-group-text" id="inputGroupPrepend"><img src="https://kidsfriendly.world/wp-content/uploads/2024/03/CallBell.png" alt="search"/></span>
      


      <div class="dropdown form-select" id="select-cat">
        <button class="dropdown-toggle" onclick="toggleDropdown('dropdown2')">Kategories</button>
        <div class="dropdown-content" id="dropdown2">
           <?php
            $taxonomy = 'category'; // Replace 'category' with the name of the taxonomy you want to retrieve terms from

            $terms = get_terms( array(
              'taxonomy' => $taxonomy,
              'hide_empty' => false, // Set to true if you want to hide empty terms
            ) );

            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
              foreach ( $terms as $term ) {
            ?>
            <div class="d-flex cat">
              <input type="checkbox" class="data" id="<?php echo $term->slug; ?>" name="<?php echo $term->slug; ?>" value="<?php echo $term->term_id; ?>">
              <label for="<?php echo $term->slug; ?>"><?php echo $term->name; ?></label><br>
            </div>
            <?php 
                }
            }
          ?>
        </div>
      </div>

    </div>
  </div>
   <div class="col-md-2">
    <div class="input-group has-validation price-symbol">
      <span class="input-group-text" id="inputGroupPrepend">
        €
      </span>
      <?php
      // $priceFilters = array(
      //     1 => "€1+",
      //     10 => "€10+",
      //     100 => "€100+",
      //     500 => "€500+",
      //   );
       $priceFilters = array(
          '1' => "€1",
          '5' => "€5",
          '10' => "€10",
          '15' => "€15",
          '20' => "€20",
          '25' => "€25",
          '30' => "€30",
          '35' => "€35",
          '40' => "€40",
          '45' => "€45",
          '50' => "€50",
          '55' => "€55",
          '60' => "€60",
          '65' => "€65",
          '70' => "€70",
          '75' => "€75",
          '80' => "€80",
          '85' => "€85",
          '90' => "€90",
          '95' => "€95",
          '100' => "€100+",
       );

      ?>
<!-- 	  <input type="text" class="form-control" id="selectPrice"value="" placeholder="Preis-Segment"/> -->
	  <small class="priceMin">Minimum</small>
	  <input type="number" id="selectPriceMin" name="selectPriceMin" class="form-control price">
	  <span class="to-class">--</span>
	 <small class="priceMax">Maximum</small>
	  <input type="number" id="selectPriceMax" name="selectPriceMax" class="form-control price">
		
    </div>
  </div>
  <div class="col-md-2">
    <div class="input-group has-validation">
      <span class="input-group-text" id="inputGroupPrepend"><img src="https://kidsfriendly.world/wp-content/uploads/2024/03/MapPin.png" alt="search"/></span>
      <input type="text" class="form-control" id="location"value="Germany" placeholder="Enter Location"/>
    </div>
  </div>
  
  <div class="col-1">
     <div class="btn btn-primary" type="submit" id="search-submit" style="background-color: #679F6E;border-color: #679F6E;">
      <img decoding="async" src="https://kidsfriendly.world/wp-content/uploads/2024/03/MagnifyingGlass.png" alt="Search">
</div>
  </div>
</div>
<style>
.price-symbol span#inputGroupPrepend {
    font-size: 25px;
    font-weight: 500;
    color: #7fae86;
}
.price.form-control{
	padding: 6px;
    font-size: 14px;
    font-weight: 600;
    color: #5f5f5f !important;
}
span.to-class {
    padding: 5px;
    margin-top: 4px;
    font-weight: 900;
}
.dropdown-content div {
    line-height: 2;
}
.dropdown-toggle::after {
    display: none;
}
button.dropdown-toggle {
    color: #5f5f5f;
    border: 0;
    position: relative;
    box-shadow: unset;
    width: 150px;
    text-align: left;
    background: transparent;
}
small.priceMin {
    position: absolute;
    left: 40px;
    z-index: 99999999999;
    font-size: 10px;
	top: 2px;
}

small.priceMax {
    position: absolute;
    font-size: 10px;
    left: 133px;
    z-index: 999999999;
	top: 2px;
}
div#search-submit {
    padding: 3px 10px;
}

div#select-cat {
    padding: 1px;
}
.dropdown-content label {
    line-height: 2;
    margin-left: 10px;
}

.dropdown-content input {
    transform: scale(1.5);
}

.dropdown-content {
    width: 295px;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 150px;
  border: 1px solid #ddd;
  padding: 10px;
  z-index: 1;
}
div#select-facilities {
    padding: 1;
}
.dropdown-content label {
  display: block;
}

.dropdown-toggle {
  background-color: white;
  border: 1px solid #ccc;
  padding: 8px 12px;
  cursor: pointer;
}

.dropdown-toggle:hover {
  background-color: white;
}
div#select-facilities {
    padding: 0;
}

button.btn.btn-primary {
    padding: 3px 10px;
}
.col-md-2 {
    width: 18.8%;
}

.col-1 {
    width: 6%;
}
span#inputGroupPrepend {
    padding: 0 5px;
    border-radius: 0;
    border-radius-left: 12px;
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
}
</style>
<script>
function toggleDropdown(dropdownId) {
  var dropdownContent = document.getElementById(dropdownId);
  dropdownContent.style.display === 'none' ? dropdownContent.style.display = 'block' : dropdownContent.style.display = 'none';
}

document.addEventListener('click', function(event) {
  var dropdowns = document.querySelectorAll('.dropdown');
  dropdowns.forEach(function(dropdown) {
    if (!dropdown.contains(event.target)) {
      dropdown.querySelector('.dropdown-content').style.display = 'none';
    }
  });
});

</script>