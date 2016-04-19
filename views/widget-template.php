<?php
/**
 * The Template for displaying the WP Ratings Widget output.
 *
 * Override this template by copying it to yourtheme/wprrw/custom-view.php
 *
 * @package       WPRRW/Templates
 * @version       1.0
 */
?>
<div class="wprrw_plugin_data_wrapper plugin-<?php echo $slug; ?>">

	<?php 
	if (!empty($installs)) :
		echo '<p class="active-installs">' . $installs . '<span>' . __('Active Installs', 'wppluginratings') . '</span></p>'; 
	endif;
	?>

  	<?php

    if (!empty($rating)) :
    	echo '<div class="plugin-ratings">';
  			echo '<p class="total-rating">' . $rating . $ratingtext . '</p>';
  		echo '</div><!-- end plugin-ratings -->';
  	endif;
  	?>

  	<?php 
  	if ($stars == 'on') :
      include( WPRRW_PATH . '/inc/star-rating.php');
    endif;
    ?>

    <?php 
    if ( $instance['wprrw_download'] == 'on' ) {
	    echo '<a href="' . $var['download'] . '" class="download_link"><span class="dashicons dashicons-download"></span>';
      echo sprintf(__('Download "%1$s" here', 'wppluginratings'), $var['name']);
      echo '</a>';
    }

    if ( $instance['wprrw_translate'] == 'on') {
	    echo '<a href="https://translate.wordpress.org/projects/wp-plugins/' . $var['slug'] . '" target="_blank" class="translate"><span class="dashicons dashicons-translation"></span> ' . __('Contribute by Translating this plugin', 'wppluginratings') . '</a>';
    }

    if ( $instance['wprrw_badge'] == 'on') {
      echo '<a href="' . $var['repolink'] . '" target="_blank" class="badge">
      <img src="https://khromov.github.io/wordpress-badge-generator/images/wp-button-small.png"/>
      </a>';
    }
    //var_dump($call_api);

    include_once( WPRRW_PATH . '/inc/reviews-logic.php');
	?>

</div>

<?php 