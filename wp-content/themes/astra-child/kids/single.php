<?php get_header(); ?>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/inc/css/single.css">
<?php
$theTitle = "";
        // Start the loop.
while (have_posts()) : the_post();

// GATHER DATA (DEFAULT VALUE FROM WORDPRESS)
  $blog_id = get_the_ID();
  $blog_title = get_the_title();
  $blog_link = get_permalink();
  $blog_content = get_the_content();
  $blog_date = get_the_date();

  $singlePosts[] = $post->ID;
?>

<section class="singleBlogs">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-12">
                <div class="header">
                    <h1 class="text-white"><?php echo $blog_title; ?></h1>
                </div>
            </div>
            <div class="col-md-12">
                <div class="singleContent">
                    <div class="featureImage">
                        <?php echo get_the_post_thumbnail(); ?>
                    </div>
                    <?php echo the_content(); ?>
                </div>
                <div class="share">
                    <span>Share</span>
                    <a
                        href="https://www.facebook.com/sharer.php?u=<?php echo the_permalinks(); ?>/lorem-ipsum-dolor-sit/"
                        target="_blank" rel="noopener noreferrer">
                        <img src="https://innovnational.com/aimhigh/wp-content/themes/aimhigh/assets/img/newAndEvents/facebook.png" alt>
                    </a>
                    <a
                        href="https://www.instagram.com/sharer.php?u=<?php echo the_permalinks(); ?>/lorem-ipsum-dolor-sit/"
                        target="_blank" rel="noopener noreferrer">
                        <img src="https://innovnational.com/aimhigh/wp-content/themes/aimhigh/assets/img/newAndEvents/instagram.png" alt>
                    </a>
                    <a
                        href="https://twitter.com/sharer.php?u=<?php echo the_permalinks(); ?>/lorem-ipsum-dolor-sit/"
                        target="_blank" rel="noopener noreferrer">
                        <img src="https://innovnational.com/aimhigh/wp-content/themes/aimhigh/assets/img/newAndEvents/twitter.png" alt>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
    endwhile;
?>

<?php get_footer();?>