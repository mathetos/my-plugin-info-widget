<?php

/*
 *  Default Widget view
 */

$wprepo = new WP_Ratings_Widget;
$var = $wprepo->variables($instance);
// var_dump($wprepo);

wp_enqueue_style( 'wprrw-default-css', WPRRW_URL . 'css/wprrw-widget-default.css', false );
wp_enqueue_style( 'wprrw-slick-css', WPRRW_URL . 'vendor/slick/slick.css', false );
wp_enqueue_style( 'wprrw-slick-theme-css', WPRRW_URL . 'vendor/slick/slick-theme.css', false );
wp_enqueue_script( 'wprrw-slick-js', WPRRW_URL . 'vendor/slick/slick.min.js', '', '', true );

// Get Title
if (isset($instance['wprrw_title']))
 echo $before_title . esc_attr( $instance['wprrw_title'] ) . $after_title;

// Get Slug
if (isset($var['slug']))
  $slug = $var['slug'];

// Get Active Installs
if ( !empty( $var['installs'] ) ) {
  
  $installs = $var['installs'];
  
  } else {
  
  $installs = '<p>This Plugin has no Active Installs</p>';
}

// Get ratings and total reviews
if ( !empty( $var['rating'] ) ) {
    $rating = $var['rating']/100*5;
    $ratingtext = sprintf(__(' Stars %1$s out of %2$s total reviews', 'wppluginratings'), '<span>', $var['numratings']) . '</span>';
  } else {
    $rating = '';
    $ratingtext = '';
  }

// Get Whether Stars are Enabled or not
$stars = $instance['wprrw_stars'];



  //Loads the default template
  // Looks in Parent, then Child theme directory, then finally plugin
  if (file_exists( get_template_directory() . '/wprrw/custom-view.php')) {
    $template = get_template_directory() . '/wprrw/custom-view.php';
  } elseif(file_exists( get_stylesheet_directory() . '/wprrw/custom-view.php')) {
    $template = get_stylesheet_directory() . '/wprrw/custom-view.php';
  } else {
    $template = WPRRW_PATH . '/views/widget-template.php';
  } 

  include( $template );

?>


<?php
// End of Wrapper Div

add_action( 'wp_footer', 'print_wprrw_slick_script' );

function print_wprrw_slick_script() {
  if ( wp_script_is( 'wprrw-slick-js', 'done' ) ) {
    ob_start(); ?>
      <script>
        jQuery(document).ready(function( $ ) {
          $('.wprrw_reviews_wrap').slick({
            fade: true,
            autoplay: true,
          autoplaySpeed: 3500,
          prevArrow: $('.prevArrow'),
          nextArrow: $('.nextArrow'),
          })
        });
      </script>
    <?php
  
    $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;
    }
}

