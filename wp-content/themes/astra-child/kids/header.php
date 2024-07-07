<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?php echo wp_title(); ?>
  </title>
  <?php wp_head() ?>
  <!-- <link rel="icon" href="<?php //echo get_stylesheet_directory_uri();   ?>/assets/img/homepage/logo-icon.png" sizes="32x32" /> -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/inc/css/global.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://kit.fontawesome.com/90ce1d8b78.js" crossorigin="anonymous"></script>
</head>

<body class="bg-secondary <?php body_class();?>">
  <div class="narbar sticky-top">
    <nav class="navbar navbar-expand-md bg-dark-subtle">
      <div class="container-xl">
        <a class="navbar-brand pt-0 pb-0" href="<?php echo get_home_url(); ?>">
          <svg width="60" height="64" viewBox="0 0 60 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M49.6437 44.456L30.0998 63.9999L4.04751 37.9476C-1.34917 32.551 -1.34917 23.8004 4.04751 18.4037C9.44418 13.007 18.1948 13.007 23.5914 18.4037L49.6437 44.456Z"
              fill="#BAA387" />
            <path
              d="M10.6223 44.456L30.1663 63.9999L55.867 38.2992C61.2636 32.9025 61.2636 24.1519 55.867 18.7553C50.4703 13.3586 41.7197 13.3586 36.323 18.7553L10.6223 44.456Z"
              fill="#5E487E" />
            <path
              d="M10.6223 44.456L30.1663 63.9999L39.8765 54.2897C45.2731 48.893 45.2731 40.1424 39.8765 34.7457C34.4798 29.3491 25.7292 29.3491 20.3325 34.7457L10.6223 44.456Z"
              fill="#679F6E" />
            <path
              d="M18.5937 5.67221C18.5937 8.8076 16.0569 11.3444 12.9215 11.3444C9.78608 11.3444 7.24927 8.8076 7.24927 5.67221C7.24927 2.53682 9.78608 0 12.9215 0C16.0569 0 18.5937 2.53682 18.5937 5.67221Z"
              fill="#BAA387" />
            <path
              d="M35.601 12.0475C35.601 15.1828 33.0642 17.7197 29.9288 17.7197C26.7934 17.7197 24.2566 15.1828 24.2566 12.0475C24.2566 8.91206 26.7934 6.37524 29.9288 6.37524C33.0642 6.37524 35.601 8.91206 35.601 12.0475Z"
              fill="#679F6E" />
            <path
              d="M46.9358 11.3444C50.0684 11.3444 52.608 8.80488 52.608 5.67221C52.608 2.53953 50.0684 0 46.9358 0C43.8031 0 41.2635 2.53953 41.2635 5.67221C41.2635 8.80488 43.8031 11.3444 46.9358 11.3444Z"
              fill="#5E487E" />
          </svg>

        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
          <?php wp_nav_menu(array('Primary Menu' => 'Primary', 'menu_class' => 'navMenu navbar-nav me-auto mb-2 mb-lg-0', 'container-xl' => false, )); ?>
          <?php
          if (is_user_logged_in()) {
            $logout_url = wp_logout_url(home_url('/login/')); // Redirect to login page after logout
            echo '<a href="' . esc_url($logout_url) . '" class="login">Logout</a>';
          } else {
            echo '<a href="' . esc_url(home_url('/login/')) . '" target="_blank" rel="noopener noreferrer">Login</a>';
          }
          ?>
        </div>
      </div>
    </nav>
  </div>