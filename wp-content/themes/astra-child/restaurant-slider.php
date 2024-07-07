<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/freeps2/a7rarpress@main/swiper-bundle.min.css">
<div class="slide-container swiper">
  <div class="slide-content">
    <div class="card-wrapper swiper-wrapper">
    <?php
    $user_id = get_current_user_id();
    $category = '';
    $query = new WP_Query(array(
        'category_name' => $category, 
        'post_type' => 'post',
        'posts_per_page' => -1,
    ));
    if ($query->have_posts()) {
        while ($query->have_posts()) {
             $location = '';
             $start_time = '0:00';
             $restaurant_likes = 0;
             $end_time = '0:00';
            $query->the_post(); 
            $post_id = get_the_ID();
            $location = get_post_meta($post_id , '_cspm_location', true) ? : array();
            $start_time = get_post_meta($post_id , 'start_time', true) ? : '0:00';
            $end_time = get_post_meta($post_id , 'end_time', true) ? : '0:00';
            $restaurant_likes = get_post_meta($post_id, 'restaurant_customer_likes', true) ? : '0';
          
    
            
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
          <div class="swiper-slide">
              <div class="card">
                  <div class="card-body p-3">
                    <div class="card-header">
                        <?php
                         $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                         echo '<img decoding="async" src="' . $featured_image_url . '" alt="' . get_the_title() . '">';
                        ?>
                    </div>
                    <div class="card-content">
                        <div class="card-title">
                            <?php if($user_id && is_user_logged_in()) { ?>
                                <span class="bookmark-resto like-btn fav" data-status="<?php echo $status; ?>" data-post="<?php echo $post_id; ?>" data-user="<?php echo $user_id; ?>">
                                    <img src="<?php echo $image; ?>" alt="Restaurant" class="likes-icon-id"/>
                                    <span class="spinner-grow spinner-grow-sm" style="color:#679F6E;position:absolute;top: 10px;left: 10.4px;display:none"></span>
                                </span>
                            <?php }else{ ?>
                                <span class="bookmark-resto not-fav">
                                     <a href="<?php echo home_url('/my-account/'); ?>"><img class="likes-icon-id" src="<?php echo home_url('/wp-content/uploads/2024/03/Unmark.png'); ?>" alt="Restaurant"/> </a>
                                </span>
                               
                            <?php } ?>
                            <h2><?php the_title(); ?></h2>
                        </div>
                      <div class="card-text"> 
                         <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                        <div class="container mt-2 mb-4">
                          <div class="row">
                            <div class="col-sm text-left p-0 marker"><small><span style="font-size:12px;"><img src="<?php echo home_url('/wp-content/uploads/2024/03/Location.png'); ?>" alt="Restaurant"/><?php echo $location['codespacing_progress_map_address'] ? $location['codespacing_progress_map_address'] : ''; ?></span></small></div>
                            <div class="col-sm p-0" style="text-align: right;margin-left:4px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 8C119 8 8 119 8 256S119 504 256 504 504 393 504 256 393 8 256 8zm92.5 313h0l-20 25a16 16 0 0 1 -22.5 2.5h0l-67-49.7a40 40 0 0 1 -15-31.2V112a16 16 0 0 1 16-16h32a16 16 0 0 1 16 16V256l58 42.5A16 16 0 0 1 348.5 321z"/></svg><small style="font-size:12px;"><?php echo $start_time; ?> - <?php echo $end_time; ?></small></div>
                          </div>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="read-more posts">Read More</a>
                    </div>
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
  </div>
  <div class="swiper-button-next swiper-navBtn"></div>
  <div class="swiper-button-prev swiper-navBtn"></div>
  <div class="swiper-pagination"></div>
</div>

<script src="//cdn.jsdelivr.net/gh/freeps2/a7rarpress@main/swiper-bundle.min.js"></script>
<script src="//cdn.jsdelivr.net/gh/freeps2/a7rarpress@main/script.js"></script>

<style>
.slide-container{
  max-width: 1120px;
  width: 100%;
  padding: 40px 0;
}
.slide-content{
  margin: 0 40px;
  overflow: hidden;
  border-radius: 25px;
}
.image-content,
.card-content{
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 10px 14px;
}
.image-content{
  position: relative;
  row-gap: 5px;
  padding: 25px 0;
}
.overlay{
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 100%;
  background-color: #4070F4;
  border-radius: 25px 25px 0 25px;
}
span.swiper-pagination-bullet {
    background: #679f6e;
}
.overlay::before,
.overlay::after{
  content: '';
  position: absolute;
  right: 0;
  bottom: -40px;
  height: 40px;
  width: 40px;
  background-color: #4070F4;
}
.overlay::after{
  border-radius: 0 25px 0 0;
  background-color: #FFF;
}
.card-image{
  position: relative;
  height: 150px;
  width: 150px;
  border-radius: 50%;
  background: #FFF;
  padding: 3px;
}
.card-image .card-img{
  height: 100%;
  width: 100%;
  object-fit: cover;
  border-radius: 50%;
  border: 4px solid #4070F4;
}


.button:hover{
  background: #265DF2;
}

.swiper-navBtn{
  color: #6E93f7;
  transition: color 0.3s ease;
}
.swiper-navBtn:hover{
  color: #4070F4;
}
.swiper-navBtn::before,
.swiper-navBtn::after{
  font-size: 38px;
  color: #679f6e;
}
.swiper-button-next{
  right: 0;
}
.swiper-button-prev{
  left: 0;
}
.swiper-pagination-bullet{
  background-color: #6E93f7;
  opacity: 1;
}
.swiper-pagination-bullet-active{
  background-color: #4070F4;
}
small {
    color: black;
    font-weight: 500;
}
.container.mt-2.mb-4 {
    padding: 0 30px;
}
.slick-list.draggable {
   margin-left: 30px;
}
.col-sm.text-left.p-0 img {
    width: 15%;
    float: left;
    position: relative;
    top: 4px;
}
/*span.bookmark-resto.fav, span.unbookmark-resto.fav,*/
/*span.bookmark-resto.not-fav, span.unbookmark-resto.not-fav {*/
/*    float: right;s*/
/*    width: 100%;*/
/*    position: absolute;*/
/*    left: 87%;*/
/*}*/
.card-title h2 span.fav img {
    width: 85%;
    float: left;
    position: relative;
}
span.bookmark-resto img {
    width: 10%;
    cursor: pointer;
}
.card-title h2 {
    font-size: 24px;
    margin-bottom: 5px;
    position: relative;
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
.card-footer a {
    color: white;
    background-color: #679F6E;
    text-decoration: unset !important;
    font-size: 16px;
    padding: 12px 105px;
    border-radius: 8px;
    font-weight: 500;
    width:100%;
}
.wrapper{
  width:100%;
  padding-top: 20px;
  text-align:center;
}
svg {
    width: 15px;
    margin-right: 2px;
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
.card {
    border: 2px solid #fff;
    border-radius: 14px;
}
.card-body{
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
@media screen and (max-width: 768px) {
  .slide-content{
    margin: 0 10px;
  }
  .swiper-navBtn{
    display: none;
  }
  .card-footer .container.mt-2.mb-4 {
    padding: 0 20px;
}
svg {
    width: 12px;
    margin-right: 2px;
}
.card-footer .row {
    display: block;
    text-align: left;
}
.card-footer  .col-sm.p-0 {
    text-align: left !important;
}
.card-body {
    box-shadow: 0px 10px 15px -3px rgba(0, 0, 0, 0.1);
}
.col-sm.text-left.p-0 img {
    width: 6%;
    float: left;
    position: relative;
    top: 4px;
}
}
@media screen and (max-width: 414px) {
.col-sm.text-left.p-0 img {
    width: 5%;
    float: left;
    position: relative;
    top: 4px;
}

}

</style>
<script>
 
var swiper = new Swiper(".slide-content", {
    slidesPerView: 3,
    spaceBetween: 25,
    loop: true,
    centerSlide: 'true',
    fade: 'true',
    grabCursor: 'true',
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
      dynamicBullets: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },

    breakpoints:{
        0: {
            slidesPerView: 1,
        },
        520: {
            slidesPerView: 2,
        },
        950: {
            slidesPerView: 3,
        },
    },
  });

</script>