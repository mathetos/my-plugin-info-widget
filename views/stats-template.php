<?php
/**
 * The Template for displaying the WP Ratings Widget output.
 *
 * Override this template by copying it to yourtheme/wprrw/custom-view.php
 *
 * @package       WPRRW/Templates
 * @version       1.0
 */

	if ( !empty($installs) && $instance['wprrw_installs'] == 'on' ) :
		echo '<p class="active-installs">'
			. number_format( $installs, 0, '', ',' )
            . '+ <span>'
            . __( 'Active Installs', 'wppluginratings' )
            . '</span></p>';
	endif;


    if ( !empty($rating) && $instance['wprrw_ratings'] == 'on' ) :
    	echo '<div class="plugin-ratings">';
  			echo '<p class="total-rating">'
                . $rating
                . $ratingtext
                . '</p>';
  		echo '</div><!-- end plugin-ratings -->';
  	endif;

    if ( !empty($fivestars[5]) && $instance['wprrw_fivestars'] == 'on' ) :
  	    echo '<p class="5stars">' . ucwords($plugin_name) . ' has ' . $fivestars[5] . ' 5-star reviews.</p>';
    endif;

  	if ( $stars == 'on' )
      include( WPRRW_PATH . '/views/star-rating.php' );

    if ( $instance['wprrw_download'] == 'on' ) {
        echo '<a href="'
            . $var['download']
            . '" class="download_link"><span class="dashicons dashicons-download"></span>';

        echo sprintf(__('Download "%1$s" here', 'wppluginratings'), $var['name']);

        echo '</a>';
    }

    if ( $instance['wprrw_translate'] == 'on' ) {
        echo '<a href="https://translate.wordpress.org/projects/wp-plugins/'
            . $var['slug']
            . '" target="_blank" class="translate"><span class="dashicons dashicons-translation"></span> '
            . __('Contribute by Translating this plugin', 'wppluginratings')
            . '</a>';
    }

    if ( $instance['wprrw_badge'] == 'on' ) {
        echo '<a href="'
            . $var['repolink']
            . '" target="_blank" class="badge"><img src="https://khromov.github.io/wordpress-badge-generator/images/wp-button-small.png"/>
            </a>';
    }