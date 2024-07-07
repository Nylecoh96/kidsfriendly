<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.8/slick-theme.min.css">


<div class="row row-cols-1 row-cols-md-3 g-4">
     <?php
    $user_id = get_current_user_id();
    $query = new WP_Query(array(
        'author'         => $user_id, 
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => array('publish','pending'),
    ));
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post(); 
            $post_id = get_the_ID();
            $location = get_post_meta($post_id , 'location', true);
            $start_time = get_post_meta($post_id , 'start_time', true);
            $end_time = get_post_meta($post_id , 'end_time', true) ? : array();
            $restaurant_likes = get_post_meta($post_id, 'restaurant_customer_likes', true) ? : array();
            $restaurant_list = $restaurant_likes[$post_id] ? : array();
            $counted_values = array_count_values($restaurant_list);
            $post_status = get_post_status($post_id);

            $number_likes = $counted_values[1] ? $counted_values[1]  : '0';
            
            if(isset($restaurant_likes[$post_id]) && is_array($restaurant_likes[$post_id]) && in_array($user_id, $restaurant_likes[$post_id])) {
                $image = home_url('/wp-content/uploads/2024/03/Mark.png');
                $status = 'mark';
                $message = "<span style='color: green;'>You liked this restaurant!</span>";
            } else {
                $image = home_url('/wp-content/uploads/2024/03/Unmark.png');
                $status = 'unmark';
                $message = "<span style='color: red;'>You haven't liked this restaurant yet.</span>";
            }
            echo '<div id="notification" class="alert-message" style="display: none;">' . $message . '</div>';
        ?>
    
          <div class="col <?php echo $post_status == 'pending' ? 'pending-disabled' : ''; ?>">
            <?php
            if($post_status == 'pending'){
                echo '<span class="pending-status">Waiting for Approval</span>';
            }
            ?>
            <div class="likes-number"><?php echo $number_likes; ?><br>Likes</div>
            <div class="card">
              <?php
                 $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                 echo '<img decoding="async"  class="card-img-top" src="' . $featured_image_url . '" alt="' . get_the_title() . '">';
                ?>
                <a href="<?php echo home_url('/edit-store/?u='.$post_id.''); ?>"><span id="edit-store-btn">Edit</span></a>
              <div class="card-body">
                  <?php if($user_id) { ?>
                        <span class="bookmark-resto like-btn fav" data-status="<?php echo $status; ?>" data-post="<?php echo $post_id; ?>" data-user="<?php echo $user_id; ?>">
                            <img src="<?php echo $image; ?>" alt="Restaurant"/>
                            <!--<span class="spinner-grow spinner-grow-sm" style="color:#679F6E;position:absolute;top: 10px;left: 10.4px;display:none"></span>-->
                        </span>
                    <?php }else{ ?>
                        <a href="<?php echo home_url('/register/customer/'); ?>">
                        <span class="bookmark-resto like-btn fav">
                            <img src="<?php echo home_url('/wp-content/uploads/2024/03/Unmark.png'); ?>" alt="Restaurant"/>
                        </span>
                        </a>
                    <?php } ?>
                <h5 class="card-title"><?php echo the_title(); ?></h5>
                <p class="card-text">
					<?php
					$content = get_the_content();
					$trimmed_content = wp_trim_words( $content, 18 ); // Trims the content to 20 words
					echo $trimmed_content;
					?>
				</p>
                <br>
                <div class="container mt-2 mb-4">
                  <div class="row">
                    <div class="col-12 p-0"><small><span><img src="<?php echo home_url('/wp-content/uploads/2024/03/Location.png'); ?>" alt="Restaurant"/><?php //echo $location['street_name'] ? : ''; ?></span></small></div>
                    <div class="col-12 p-0"><small><?php echo $start_time; ?> - <?php echo $end_time; ?></small></div>
                  </div>
                </div>
                <a href="<?php the_permalink(); ?>" class="read-more posts">Read More</a>
              </div>
            </div>
          </div>
        
        
        <?php
            }
            wp_reset_postdata();
        } else {
            echo 'No posts found';
        }
        ?>
  
</div>


<style>
h5.card-title {
    text-align: left;
}
.card-body p {
    text-align: justify;
}
small {
    color: black;
    font-weight: 500;
}
span.pending-status {
    font-weight: 700;
    color: red;
}
span#edit-store-btn {
    color: red;
    font-size: 16px;
    text-align: right;
    position: absolute;
    z-index: 99999999999999999999;
    font-weight: 700;
    text-decoration: underline;
    left: 183px;
    top: 121px;
    background: #00000059;
    width: 47px;
    text-align: center;
}
.likes-number {
    background: #679f6e;
    color: white;
    font-weight: 700;
    width: 50px;
    height: 50px;
    border-radius: 45px;
    font-size: 10px;
    padding: 10px;
    position: absolute;
    z-index: 999999999999;
    margin: -12px;
}
.slick-list.draggable {
    padding: 0 !important;
}
.col-sm.text-left.p-0 img {
    width: 10%;
    float: left;
    position: relative;
    top: 4px;
}
.pending-disabled {
    pointer-events: none;
    opacity: 0.5;
}
span.bookmark-resto.fav, span.unbookmark-resto.fav {
    float: right;
    width: 100%;
    position: absolute;
    left: 40%;
}
.card-title h2 span.fav img {
    width: 85%;
    float: right;
    position: relative;
}
span.bookmark-resto img {
    width: 10%;
    cursor: pointer;
}
.card {
    height: 460px;
}
.card-title h2 {
    font-size: 24px;
    margin-bottom: 5px;
}
.card-title h2 {
    font-size: 24px;
}
.card-footer {
    background: unset;
    border: 0;
    width: 100%;
    padding: 12px 0;
    margin-bottom: 2px;
}
.card-text p {
    font-size: 12px;
}
.card-header {
    border:0;
     padding: 0;
}
.read-more {
    color: white;
    background-color: #679F6E;
    text-decoration: unset !important;
    font-size: 12px;
    padding: 8px 28px;
    border-radius: 8px;
    font-weight: 500;
    width: 100%;
}
.wrapper{
  width:100%;
  padding-top: 20px;
  text-align:center;
}
h2{
  font-family:sans-serif;
  color:#fff;
}
.carousel {
    width: 100%;
    margin: 0px auto;
}
.slick-slide{
  margin:10px;
}
.slick-slide img{
  width:100%;
}
.slick-prev, .slick-next{
  background: #000;
  border-radius: 15px;
  border-color: transparent;
}
.card{
  border: 2px solid #fff;
  box-shadow: 1px 1px 15px #ccc;
}
.card-body{
  background: #fff;
  width: 100%;
  vertical-align: top;
}
.card-content{
  text-align: left;
  color: #333;
  padding: 15px 0;
}
.card-text{
  font-size: 14px;
  font-weight: 300;
}
</style>