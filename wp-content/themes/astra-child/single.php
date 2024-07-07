<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package YourThemeName
 * @subpackage YourThemeName
 * @since 1.0
 * @version 1.0
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                while (have_posts()) :
                    the_post();
                    
                    $post_id = get_the_ID();
                    
                    $data = get_post_meta($post_id);
                    
                    $data_image = unserialize($data['post_images'][0]);
                    
                    // foreach($data_image as $data_image){
                    //     $attachment_id = $data_image;
                    //     $image_url = wp_get_attachment_url( $attachment_id );
                        
                      
                    // }
                    
                    $featured_image_id = get_post_thumbnail_id($post_id);
                    if ($featured_image_id) {
                        $featured_image_url = wp_get_attachment_url($featured_image_id);
                    }
                    
                    
                   
                    
                    $location = get_post_meta($post_id , 'codespacing_progress_map_addres', true) ? : 'No Location Found';
                    $google_rating = get_post_meta($post_id , 'google_reviews', true) ? : '0';
                    $start_time = get_post_meta($post_id , 'start_time', true) ? : '0:00';
                    $end_time = get_post_meta($post_id , 'end_time', true) ? : '0:00';
                    $restaurant_likes = get_post_meta($post_id, 'restaurant_customer_likes', true) ? : array();
                    
        
                    $ratingInput = $google_rating;
                    $rating = floatval($ratingInput);
                    $fullimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/rate.png';
                    $emptyimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/empty.png';
                    $halfimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/half-1.png';
                    for ($i = 1; $i <= 5; $i++) {
                        $star = 'star' . $i;
                        if ($i <= floor($rating)) {
                            // Full star
                           $src_val = $fullimg;
                        } else if ($i === ceil($rating) && $rating % 1 !== 0) {
                            // Half star
                           $src_val = $halfimg;
                        } else {
                            // Empty star
                           $src_val = $emptyimg;
                        }
                    }


                    
                    ?>
                    <div class="card-content">
                        <div class="card-title">
                            <div class="row">
                            <div class="col-md-6">
                            <h2 class="d-flex"><?php the_title(); ?></h2>
                            </div>
                           <?php
                            $ratingInput = $google_rating;
                            $rating = floatval($ratingInput);
                            $fullimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/rate.png';
                            $emptyimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/empty.png';
                            $halfimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/half-1.png';
                            ?>
                            
                            <div class="col-md-6 star-rating-cus">
                                <div class="container m-0 p-0">
                                    <?php for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            // Full star
                                            $src_val = $fullimg;
                                        } else if ($i - 0.5 == $rating) {
                                            // Half star
                                            $src_val = $halfimg;
                                        } else {
                                            // Empty star
                                            $src_val = $emptyimg;
                                        }
                                    ?>
                                        <span><img id="star<?php echo $i; ?>" onload="toggleStar(<?php echo $i; ?>)" class="star" src="<?php echo $src_val; ?>" alt="kids"/></span>
                                    <?php } ?>
                                    <span class="ml-2">Rating: <?php echo $google_rating; ?></span>
                                </div>
                            </div>

                            </div>
                        </div>
                         <div class="container mt-2 mb-4">
                          <div class="row">
                             <div class="mr-2 col-3">
                                 <div class="col-sm text-left p-0">
                                    <small>
                                    <span><img src="<?php echo home_url('/wp-content/uploads/2024/03/Location.png'); ?>" alt="Restaurant"/><?php echo $location; ?></span></small></div>
                             </div>
                            <div class="mr-2 col-2">
                                <div class="col-sm p-0"><small><?php echo $start_time; ?> - <?php echo $end_time; ?></small></div>
                            </div>
                           <div class="mr-2 col-3">
                                <?php
                                $taxonomy = 'category'; 
                                $categories = wp_get_post_terms($post_id, $taxonomy);
                                if (!empty($categories) && !is_wp_error($categories)) {
                                    foreach ($categories as $category) {
                                        echo $category->name . '<br>';
                                    }
                                } 
                                ?>
                            </div>
                            <div class="mr-2 col-3">
                                <?php
                                $taxonomy = 'facilities'; 
                                $categories = wp_get_post_terms($post_id, $taxonomy);
                                if (!empty($categories) && !is_wp_error($categories)) {
                                    foreach ($categories as $category) {
                                        echo $category->name . '<br>';
                                    }
                                } 
                                ?>
                            </div>
                        </div>
                      <div class="card-text"> 
                        <img src="<?php echo $featured_image_url; ?>" alt="restaurant" style="width: 100%;margin-bottom: 30px;">
                        <div class="row">
                            <?php 
                            foreach($data_image as $data_image){
                                $attachment_id = $data_image;
                                $image_url = wp_get_attachment_url( $attachment_id );
                                ?>
                                <div class="col-4"><img src="<?php echo $image_url; ?>" alt="restaurant" style="width: 100%;margin-bottom: 30px;"></div>
                                <?php
                            }
                            ?>
                            
                        </div>
                         <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                      </div>
                    </div>
                    <?php
                    

                endwhile; // End of the loop.
                ?>
            </div><!-- .col-md-12 -->
        </div><!-- .row -->
    </div><!-- .container -->
</main><!-- #main -->
<style>
nav.navigation.post-navigation {
    display: none;
}
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
.col-md-6.star-rating-cus {
    text-align: right;
}
.col-md-6.star-rating-cus img {
    width: 20px;
}
</style>
<script>
    //  // Function to toggle the star color and calculate the rating
    //   function toggleStar(starNumber) {
    //     var ratingInput = 
    //     ratingInput.value = starNumber;
    //     updateStars();
    //   }
    
    //   // Function to update the appearance of the stars based on the input rating
    //   function updateStars() {
    //     var ratingInput =
    //     var rating = parseFloat(ratingInput.value);
    //     var fullimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/rate.png';
    //     var emptyimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/empty.png';
    //     var halfimg = 'https://kidsfriendly.world/wp-content/uploads/2024/04/half-1.png';
    //     for (var i = 1; i <= 5; i++) {
    //       var star = document.getElementById('star' + i);
    //       if (i <= Math.floor(rating)) {
    //         // Full star
    //         star.classList.add('checked');
    //         star.classList.remove('half'); 
    //         star.setAttribute('src', fullimg);
    //       } else if (i === Math.ceil(rating) && rating % 1 !== 0) {
    //         // Half star
    //         star.classList.add('checked');
    //         star.classList.add('half');
    //         star.setAttribute('src', halfimg);
    //       } else {
    //         // Empty star
    //         star.classList.remove('checked');
    //         star.classList.remove('half'); 
    //         star.setAttribute('src', emptyimg);
    //       }
    //     }
    //   }
</script>
<?php
get_footer();
