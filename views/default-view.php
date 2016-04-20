<?php

/*
 *  Widget Template Loader
 */

$wprepo = new WP_Ratings_Widget;
$var = $wprepo->variables($instance);

//Enqueue necessary styles and scripts for the widget
wp_enqueue_style( 'wprrw-default-css', WPRRW_URL . 'css/wprrw-widget-default.css', false );
wp_enqueue_style( 'wprrw-slick-css', WPRRW_URL . 'vendor/slick/slick.css', false );
wp_enqueue_style( 'wprrw-slick-theme-css', WPRRW_URL . 'vendor/slick/slick-theme.css', false );
wp_enqueue_script( 'wprrw-slick-js', WPRRW_URL . 'vendor/slick/slick.min.js', '', '', true );

// Get the Widget Title
if (isset($instance['wprrw_title']))
 echo $before_title . esc_attr( $instance['wprrw_title'] ) . $after_title;

// Get Plugin Slug
if (isset($var['slug']))
  $slug = $var['slug'];
?>

<div class="wprrw_plugin_data_wrapper plugin-<?php echo $slug; ?>">

<?php 
// Get the Plugin Active Installs
if ( !empty( $var['installs'] ) ) {
  
  $installs = $var['installs'];
  
  } else {
  
  $installs = '<p>This Plugin has no Active Installs</p>';
}

// Get the Plugin's ratings and total reviews
if ( !empty( $var['rating'] ) ) {
    $rating = $var['rating']/100*5;
    $ratingtext = sprintf(__(' Stars %1$s out of %2$s total reviews', 'wppluginratings'), '<span>', $var['numratings']) . '</span>';
  } else {
    $rating = '';
    $ratingtext = '';
  }

// Get Whether this Widget has enabled Stars or not
$stars = $instance['wprrw_stars'];

// Loads the Stats Template and then the Reviews template
// IF they are enabled for this widget.

$parentstatfile = get_template_directory() . '/wprrw/stats-template.php';
$childstatfile = get_stylesheet_directory() . '/wprrw/stats-template.php';

if ($instance['wprrw_customize'] == 'on') {

  if ( !file_exists( $parentstatfile) && !file_exists($childstatfile)) {
    $statstemplate = WPRRW_PATH . '/views/stats-template.php';
  } elseif( file_exists( $childstatfile )) {
    $statstemplate = $childstatfile;
  } else {
    $statstemplate = $parentstatfile;
  } 

  include( apply_filters('wprrw_stats_template', $statstemplate ) );
}

if ($instance['wprrw_reviewsenable'] == 'on') {
  
  $parentrevfile = get_template_directory() . '/wprrw/reviews-template.php';
  $childrevfile = get_stylesheet_directory() . '/wprrw/reviews-template.php';

  if ( !file_exists( $parentrevfile) && !file_exists($childrevfile)) {
    $reviewstemplate = WPRRW_PATH . '/views/reviews-template.php';
  } elseif( file_exists( $childrevfile )) {
    $reviewstemplate = $childrevfile;
  } else {
    $reviewstemplate = $parentrevfile;
  } 

  include( apply_filters('wprrw_reviews_template', $reviewstemplate ) );
}
?>
</div><!-- End widget wrapper div -->
<?php 

add_action( 'wp_footer', 'print_wprrw_slick_script' );

function print_wprrw_slick_script() {
  ?>
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
  
}

