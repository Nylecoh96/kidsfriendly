<?php get_header(); 
/*Template Name: Login*/
?>



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

<?php get_footer(); ?>