<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/user-registration/myaccount/navigation.php.
 *
 * HOWEVER, on occasion UserRegistration will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.wpuserregistration.com/docs/how-to-edit-user-registration-template-files-such-as-login-form/
 * @package UserRegistration/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Action to fire before the rendering of user registration account navigation.
 */
do_action( 'user_registration_before_account_navigation' );
?>

<nav class="user-registration-MyAccount-navigation">
	<ul>
	   <?php 
	    $array_menu = ur_get_account_menu_items();
	    
	    $index = array_search('edit-profile', array_keys($array_menu)) + 1;
	    if ( current_user_can( 'shop_manager' ) ) {
        $array_menu = array_slice($array_menu, 0, $index, true) +
                array('view-restaurant' => 'Restaurants') +
                array_slice($array_menu, $index, count($array_menu) - $index, true);
	    }
	    if ( current_user_can( 'subscriber' ) ) {
        $array_menu = array_slice($array_menu, 0, $index, true) +
                array('favorites-restaurant' => 'Favorites Restaurant') +
                array_slice($array_menu, $index, count($array_menu) - $index, true);
	    }
                
	    ?>
		<?php foreach ( $array_menu as $endpoint => $label ) : ?>
			<?php $label = ur_string_translation( 0, 'user_registration_' . $endpoint . '_label', $label ); ?>
			<?php $endpoint = ur_string_translation( 0, 'user_registration_' . $endpoint . '_slug', $endpoint ); ?>
			<li class="<?php echo esc_attr( ur_get_account_menu_item_classes( $endpoint ) ); ?>">
				<a href="<?php echo esc_url( ur_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php
/**
 * Action to fire after the rendering of user registration account navigation.
 */
do_action( 'user_registration_after_account_navigation' ); ?>
