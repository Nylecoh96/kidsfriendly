<?php
if (!function_exists('kids')):
    function kids()
    {
        add_theme_support('post-thumbnails');
        add_theme_support('post-formats', array('aside', 'gallery', 'quote', 'image', 'video'));
        register_nav_menus(
            array(
                'primary' => __('Primary Menu', 'kidsNavMenu')
            ));
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }
endif;
add_action('after_setup_theme', 'kids');
function get_excerpt($limit, $source = null)
{
    $excerpt = $source == "content" ? get_the_content() : get_the_excerpt();
    $excerpt = preg_replace(" (\[.*?\])", '', $excerpt);
    $excerpt = strip_shortcodes($excerpt);
    $excerpt = strip_tags($excerpt);
    $excerpt = substr($excerpt, 0, $limit);
    $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
    $excerpt = trim(preg_replace('/\s+/', ' ', $excerpt));
    return $excerpt;
}

function custom_registration_form() {
    if (is_user_logged_in()) {
        return '<p>You are already logged in.</p>';
    } else {
        ob_start();
        ?>
        <div class="woocommerce">
            <?php wc_print_notices(); // Display WooCommerce error/success messages ?>
            <form method="post" class="woocommerce-form woocommerce-form-register register" action="<?php echo esc_url(site_url('wp-login.php?action=register', 'login_post')); ?>">
                <?php do_action('woocommerce_register_form_start'); ?>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_email"><?php esc_html_e('Email Address', 'woocommerce'); ?><span class="required">*</span></label>
                    <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php if (!empty($_POST['email'])) echo esc_attr($_POST['email']); ?>" required>
                </p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="reg_password"><?php esc_html_e('Password', 'woocommerce'); ?><span class="required">*</span></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required>
                </p>
                <?php do_action('woocommerce_register_form'); ?>
                <p class="woocommerce-FormRow form-row">
                    <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
                    <button type="submit" class="woocommerce-Button button" name="register" value="<?php esc_attr_e('Register', 'woocommerce'); ?>"><?php esc_html_e('Register', 'woocommerce'); ?></button>
                </p>
                <?php do_action('woocommerce_register_form_end'); ?>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode('woocommerce_registration_form', 'custom_registration_form');



//Log in Form
function custom_login_form_shortcode()
{
    if (is_user_logged_in()) {
        // If the user is already logged in, display a message or redirect as needed
        return 'You are already logged in.';
    } else {
        // If the user is not logged in, display the custom login form with placeholders and "Remember Me" checkbox
        ob_start();
        ?>
        <form method="post" class="custom-login-form" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>">
            <p>
                <label for="user_login">Username:</label>
                <input type="text" name="log" id="user_login" placeholder="Email" required />
            </p>
            <p>
                <label for="user_pass">Password:</label>
                <input type="password" name="pwd" id="user_pass" placeholder="Password" required />
            </p>
            <p class="login-remember">
                <label>
                    <input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember Me
                </label>
            </p>
            <p>
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="Log In" />
            </p>
        </form>
        <?php
        return ob_get_clean();
    }
}
add_shortcode('custom_login_form', 'custom_login_form_shortcode');


add_filter('yith_wcwl_is_wishlist_responsive', '__return_false');



/* Add Plus and Minus Button for Quantity Input */
add_action('woocommerce_before_quantity_input_field', 'custom_display_quantity_minus');
add_action('woocommerce_after_quantity_input_field', 'custom_display_quantity_plus');

function custom_display_quantity_minus()
{
    echo '<button type="button" class="minus">-</button>';
}

function custom_display_quantity_plus()
{
    echo '<button type="button" class="plus">+</button>';
}

/* Trigger update quantity script */
add_action('wp_footer', 'custom_add_cart_quantity_plus_minus');

function custom_add_cart_quantity_plus_minus()
{
    if (!is_product() && !is_cart())
        return;

    wc_enqueue_js("
      jQuery(document).on('click', 'button.plus, button.minus', function() {

         var qty = jQuery(this).parent('.quantity').find('.qty');
         var val = parseFloat(qty.val());
         var max = parseFloat(qty.attr('max'));
         var min = parseFloat(qty.attr('min'));
         var step = parseFloat(qty.attr('step'));

         if (jQuery(this).is('.plus')) {
            if (max && (max <= val)) {
               qty.val(max).change();
            } else {
               qty.val(val + step).change();
            }
         } else {
            if (min && (min >= val)) {
               qty.val(min).change();
            } else if (val > 1) {
               qty.val(val - step).change();
            }
         }

      });
    ");
}

/* Trigger cart update when quantity changes */
add_action('wp_footer', 'cart_refresh_update_qty');

function cart_refresh_update_qty()
{
    if (is_cart()) {
        ?>
        <script type="text/javascript">
            let timeout;
            jQuery('div.woocommerce').on('change', 'input.qty', function () {
                if (timeout !== undefined) {
                    clearTimeout(timeout);
                }
                timeout = setTimeout(function () {
                    jQuery("[name='update_cart']").trigger("click"); // trigger cart update
                }, 1000); // 1 second
            });

            jQuery('div.woocommerce').on('click', 'button.minus, button.plus', function () {
                jQuery("[name='update_cart']").trigger("click");
            });
        </script>
        <?php
    }
}




remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
add_action('woocommerce_checkout_payment_hook', 'woocommerce_checkout_payment', 10);

// Product Menu 





function enqueue_custom_admin_css()
{
    wp_enqueue_style('custom-admin-style', get_stylesheet_directory_uri() . '/style.css');
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_css');