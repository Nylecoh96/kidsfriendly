<?php
 /**
   * Template name: Favorites Restaurant
   */

 get_header();
 
 ?>

<div class="elementor-element elementor-element-621865b e-flex e-con-boxed e-con e-parent" data-id="621865b" data-element_type="container" data-core-v316-plus="true">
  <div class="e-con-inner">
    <div class="elementor-element elementor-element-a751967 elementor-widget elementor-widget-shortcode" data-id="a751967" data-element_type="widget" data-widget_type="shortcode.default">
      <div class="elementor-widget-container">
        <div class="elementor-shortcode">
          <div id="user-registration" class="user-registration vertical">
            <nav class="user-registration-MyAccount-navigation">
              <ul>
                <li class="user-registration-MyAccount-navigation-link user-registration-MyAccount-navigation-link--dashboard">
                  <a href="<?php echo home_url('/my-account/'); ?>">Dashboard</a>
                </li>
                <li class="user-registration-MyAccount-navigation-link user-registration-MyAccount-navigation-link--edit-profile">
                  <a href="<?php echo home_url('/my-account/edit-profile/'); ?>">Profile Details</a>
                </li>
                <li class="user-registration-MyAccount-navigation-link user-registration-MyAccount-navigation-link--view-restaurant is-active">
                  <a href="<?php echo home_url('/my-account/favorites-restaurant/'); ?>">Favorites Restaurant</a>
                </li>
                <li class="user-registration-MyAccount-navigation-link user-registration-MyAccount-navigation-link--edit-password">
                  <a href="<?php echo home_url('/my-account/edit-password/'); ?>">Change Password</a>
                </li>
                <li class="user-registration-MyAccount-navigation-link user-registration-MyAccount-navigation-link--user-logout">
                  <a href="<?php echo home_url('/my-account/user-logout/'); ?>">Logout</a>
                </li>
              </ul>
            </nav>
            <div class="user-registration-MyAccount-content">
              <div class="ur-frontend-form login ur-edit-profile" id="ur-frontend-form">
                <h2 class="text-dark mb-0">List of Favorites Restaurant</h2>
                    <?php
                    $user_id = get_current_user_id();
                    $image_id = get_user_meta($user_id,'user_registration_profile_pic_url', true);
                    $image_url = wp_get_attachment_url( $image_id );
                    echo '<img src="' . $image_url . '" alt="User Avatar" class="user-avatar">';
                    ?>
                    <div class="container">
                      <div class="row">
                        <div class="col-12">
                          <table class="table table-bordered">
                            <thead>
                              <tr>
                                <th scope="col"></th>
                                <th scope="col">Restaurant Title</th>
                                <th scope="col"></th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                            $user_id = get_current_user_id();
                            $category = 'restaurant';
                            $query = new WP_Query(array(
                                'category_name' => $category, 
                                'post_type' => 'post',
                                'posts_per_page' => -1,
                            ));
                            if ($query->have_posts()) {
                                while ($query->have_posts()) {
                                    $query->the_post(); 
                                    $post_id = get_the_ID();
                                    $location = get_post_meta($post_id , 'location', true);
                                    $start_time = get_post_meta($post_id , 'start_time', true);
                                    $end_time = get_post_meta($post_id , 'end_time', true) ? : array();
                                    $restaurant_likes = get_post_meta($post_id, 'restaurant_customer_likes', true) ? : array();
                                    $restaurant_list = $restaurant_likes[$post_id];
                                    
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
                                    
                                    if(in_array($user_id,$restaurant_list)){
                            ?>
                              <tr>
                                <th scope="row"><img src="https://kidsfriendly.world/wp-content/uploads/woocommerce-placeholder.png" width="50" height="50"/></th>
                                <td class="favorites-title"><?php echo  get_the_title(); ?></td>
                                <td>
                                  <a href="<?php echo get_permalink(); ?>" class="btn btn-primary view-resto">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
  <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
  <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
</svg>
                                  </a>
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
                                </td>
                              </tr>
                             
                            <?php
                                    }
                                }
                                wp_reset_postdata();
                            } else {
                                echo 'No posts found';
                            }
                            ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.elementor-element.elementor-element-621865b.e-flex.e-con-boxed.e-con.e-parent {
    padding: 100px 200px 50px;
}
.container {
  padding: 2rem 0rem;
}
div#ur-frontend-form h2 {
    text-align: left;
}
img.user-avatar {
    position: absolute;
    float: right;
    right: 25px;
    bottom: 193px;
    border-radius: 87px;
    width: 80px;
    height: 80px;
    /* box-shadow: 5px 10px #888888; */
    box-shadow: 0px 1px 1px 0px rgba(0, 0, 0, 0.5);
}
h4 {
  margin: 2rem 0rem 1rem;
}

.table-image {
  td, th {
    vertical-align: middle;
  }
}
div#add-resto {
    background: #5e487e;
    color: white;
    padding: 10px;
    font-weight: 700;
}
td.favorites-title, .view-resto {
    text-align: center;
    vertical-align: middle;
}

</style>
 
 <?php 
 get_footer(); ?>