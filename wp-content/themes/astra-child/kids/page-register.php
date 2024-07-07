<?php get_header(); ?>

<section class="register">
    <div class="container-xl">
        <div class="row">
            <div class="register_form">
                <?php echo do_shortcode('[woocommerce_registration_form]'); ?>
                <p>Already Have an Account Click <a href="<?php echo get_home_url(); ?>/login/">Login</a></p>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.register_form form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission
        
        // Get email and password input values
        var email = document.getElementById('reg_email').value;
        var password = document.getElementById('reg_password').value;

        // Validate email and password
        if (!isValidEmail(email)) {
            swal("Error", "Please enter a valid email address", "error");
            return;
        }

        if (!isValidPassword(password)) {
            swal("Error", "Please enter a password with at least 6 characters", "error");
            return;
        }

        // If validation passes, submit the form
        this.submit();
    });

    // Function to validate email
    function isValidEmail(email) {
        var re = /\S+@\S+\.\S+/;
        return re.test(email);
    }

    // Function to validate password
    function isValidPassword(password) {
        return password.length >= 6;
    }
});
</script>
