<?php

/*
 *  Default Widget view
 */

wp_enqueue_style( 'wprws-default-css', WPRWS_URL . 'css/wprws-widget-default.css', false );
wp_enqueue_style( 'wprws-slick-css', WPRWS_URL . 'vendor/slick/slick.css', false );
wp_enqueue_style( 'wprws-slick-theme-css', WPRWS_URL . 'vendor/slick/slick-theme.css', false );
wp_enqueue_script( 'wprws-slick-js', WPRWS_URL . 'vendor/slick/slick.min.js', '', '', true );

if (isset($instance['wprws_title']))
 echo $before_title . esc_attr( $instance['wprws_title'] ) . $after_title;

$wprepo = new WP_Repo_Widget;
$var = $wprepo->variables($instance);
//var_dump($var);

$slug = $var['slug'];

?>
<script>
jQuery(document).ready(function( $ ) {
  $('.wpwrs_reviews_wrap').slick({
    fade: true,
    autoplay: true,
	autoplaySpeed: 3500,
	prevArrow: $('.prevArrow'),
	nextArrow: $('.nextArrow'),
	adaptiveHeight: true
  })
});
</script>
<div class="wprws_plugin_data_wrapper plugin-<?php echo $slug; ?>">
  <?php

    if ( $instance['wprws_installs'] == true ) {
      echo '<p class="active-installs">' . $var['installs'] . '<span>' . __('Active Installs', 'wprepowidget') . '</span></p>';
    }

    if( $instance['wprws_ratings'] == true ) {
	    echo '<div class="plugin-ratings">';
      echo '<p class="total-rating">' . $var['rating']/100*5;
      echo sprintf(__(' Stars %1$s out of %2$s total reviews', 'wprepowidget'), '<span>', $var['numratings']) . '</span></p>';

    if ( $instance['wprws_stars'] == true )
      include( WPRWS_PATH . '/inc/star-rating.php');

	    echo '</div>';
    }

    if ( $instance['wprws_download'] == 'on' ) {
	    echo '<a href="' . $var['download'] . '" class="download_link"><span class="dashicons dashicons-download"></span>';
      echo sprintf(__('Download "%1$s" here', 'wprepowidget'), $var['name']);
      echo '</a>';
    }

    if ( $instance['wprws_translate'] == 'on') {
	    echo '<a href="https://translate.wordpress.org/projects/wp-plugins/' . $var['slug'] . '" target="_blank" class="translate"><span class="dashicons dashicons-translation"></span> ' . __('Contribute by Translating this plugin', 'wprepowidget') . '</a>';
    }

    if ( $instance['wprws_badge'] == 'on') {
      echo '<a href="' . $var['repolink'] . '" target="_blank" class="badge">
      <img src="https://khromov.github.io/wordpress-badge-generator/images/wp-button-small.png"/>
      </a>';
    }
    //var_dump($call_api);

    include_once( WPRWS_PATH . '/inc/reviews-logic.php');
	?>
	<div class="reviews_nav">
		<span class="prevArrow"><?php echo __('Previous Review', 'wprepowidget'); ?></span>
		<span class="nextArrow"><?php echo __('Next Review', 'wprepowidget'); ?></span>
	</div>
	<div class="reviews_all">
		<a href="https://wordpress.org/support/view/plugin-reviews/<?php echo $slug; ?>" target="_blank"><?php echo __('See all reviews', 'wprepowidget'); ?></a>
	</div>

</div>
<?php
// End of Wrapper Div
