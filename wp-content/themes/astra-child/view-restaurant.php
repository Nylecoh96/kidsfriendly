<?php
 /**
   * Template name: View Restaurant
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
                  <a href="https://kidsfriendly.world/my-account/">Dashboard</a>
                </li>
                <li class="user-registration-MyAccount-navigation-link user-registration-MyAccount-navigation-link--edit-profile">
                  <a href="https://kidsfriendly.world/my-account/edit-profile/">Profile Details</a>
                </li>
                <li class="user-registration-MyAccount-navigation-link user-registration-MyAccount-navigation-link--view-restaurant is-active">
                  <a href="https://kidsfriendly.world/my-account/view-restaurant/">Restaurants</a>
                </li>
                <li class="user-registration-MyAccount-navigation-link user-registration-MyAccount-navigation-link--edit-password">
                  <a href="https://kidsfriendly.world/my-account/edit-password/">Change Password</a>
                </li>
                <li class="user-registration-MyAccount-navigation-link user-registration-MyAccount-navigation-link--user-logout">
                  <a href="https://kidsfriendly.world/my-account/user-logout/">Logout</a>
                </li>
              </ul>
            </nav>
            <div class="user-registration-MyAccount-content">
              <div class="ur-frontend-form login ur-edit-profile" id="ur-frontend-form">
                <h2 class="text-dark">List of Restaurants</h2>
                <a href="<?php echo home_url('/add-store'); ?>" class="add-resto-here">+ Add Restaurants</a><br>
                <div class="mt-4"><?php echo do_shortcode('[restaurant-store-card]'); ?></div>
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
div#add-resto {
    background: #5e487e;
    color: white;
    padding: 10px;
    font-weight: 700;
}

</style>
 
 <?php 
 get_footer(); ?>