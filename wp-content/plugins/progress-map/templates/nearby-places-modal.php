<?php

/**
 * Template Name: Nearby Places Modal
 * Template Post Type: page
 *
 * @since 4.6
 */

if(isset($_GET['post_id'])){
	
	$post_id = esc_attr($_GET['post_id']);
	
	wp_head();
	
		if(class_exists('CspmNearbyMap') && shortcode_exists('cs_nearby_map'))
			echo do_shortcode('[cs_nearby_map post_ids="'.$post_id.'"]');
			
	wp_footer();

}