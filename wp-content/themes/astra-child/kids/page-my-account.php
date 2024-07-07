<?php get_header();
/*Template Name: My Account*/
$imgPath = get_stylesheet_directory_uri() . '/assets/img/homepage/';
if (is_user_logged_in()): ?>
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/inc/css/desktop/my-account.css">
  <section class="my-account">
    <div class="container-xl">
      <div class="row">
        <div class="col-md-12">
          <div class="content">
            <div class="content-container">
              <?php echo do_shortcode('[woocommerce_my_account]'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php else: ?>
  <section class="login">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <?php
          if (is_user_logged_in()) {
            echo do_shortcode('[custom_login_form]');
          } else { ?>
            <div class="card">
              <div class="card-body">
                <?php echo do_shortcode('[woocommerce_my_account]'); ?>
                <p>No Account? Click <a href="<?php echo get_home_url(); ?>/register/" target="_blank"
                    rel="noopener noreferrer">Register</a></p>
              </div>
            </div>
          <?php }
          ?>
        </div>
      </div>
    </div>
  </section>
  <?php
  // header('Location: ' . get_home_url() . '/register');
endif; ?>
<?php
get_footer();
?>
<script>
  $(document).ready(function () {
    $("#togglePassword").on("click", function () {
      const passwordField = $("#user_pass");
      const toggleIcon = $(this);
      if (passwordField.attr("type") === "password") {
        passwordField.attr("type", "text");
        toggleIcon.removeClass("fa fa-eye").addClass("fa fa-eye-slash");
      } else {
        passwordField.attr("type", "password");
        toggleIcon.removeClass("fa fa-eye-slash").addClass("fa fa-eye");
      }
    });
  });
</script>