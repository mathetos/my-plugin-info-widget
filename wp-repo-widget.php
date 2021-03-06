<?php
/**
 * Plugin Name: 	WP Plugin Ratings and Reviews Widget
 * Plugin URI: 		https://www.mattcromwell.com/products/wp-plugin-ratings-and-reviews
 * Description: 	Adds a widget and shortcode to display ratings and reviews about WordPress plugins directly from the WordPress.org Plugin Directory. Ideal for plugin authors to display on their websites.
 * Version: 		1.0
 * Author: 			Matt Cromwell
 * Author URI: 		https://www.mattcromwell.com
 * License:      	GNU General Public License v2 or later
 * License URI:  	http://www.gnu.org/licenses/gpl-2.0.html
 * Textdomain: wppluginratings
 */

// Globals
define( 'WPRRW_SLUG', 'wppluginratings' );
define( 'WPRRW_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPRRW_URL', plugin_dir_url( __FILE__ ) );
define( 'WPRRW_VERSION', '1.0' );

class WP_Ratings_Widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 **/

	function __construct() {

	    $widget_ops = array( 'classname' => 'wp_ratings_widget', 'description' => 'WP Repo widget' );
	    
	    parent::__construct( 'wp_repo_widget', 'WP Ratings Widget', $widget_ops );

		add_action( 'admin_enqueue_scripts', array($this, 'wprrw_load_admin_scripts') );

	}

  /**
   * Outputs the HTML for this widget.
   *
   * @param array  An array of standard parameters for widgets in this theme
   * @param array  An array of settings for this widget instance
   * @return void Echoes it's output
   **/

	function widget( $args, $instance ) {

	    wp_enqueue_style( 'dashicons' );

		extract( $args, EXTR_SKIP );
		
		echo $before_widget;

		global $slug;
		$slug = esc_attr($instance['wprrw_slug']);

		if ( isset( $instance[ 'wprrw_slug' ] ) ) {
		  $slug = $instance[ 'wprrw_slug' ];
		}
        $cached = '<p>Yes, cached!</p>';

        // Get any existing copy of our transient data.
        if ( false === ( $cachedresults = get_transient( 'wp-plugin-repo-data-' . $slug ) ) ) {
            // It wasn't there, so regenerate the data and save the transient.
            //$cached = '<p>Nope, not cached</p>';

            $cachedresults = $this->wprrw_callapi($slug);
            set_transient( 'wp-plugin-repo-data-' . $slug, $cachedresults, 12 * HOUR_IN_SECONDS );

        } else {

            $cachedresults = get_transient( 'wp-plugin-repo-data-' . $slug );

        }

		//Check for Errors & Display the results
		if ( is_wp_error( $cachedresults ) ) {
				echo '<pre>' . print_r( $cachedresults->get_error_message(), true ) . '</pre>';

		//** If no errors and no cached results, display the results based on a new API call **//
        } else {
            //echo $cached;
			$template = self::wprrw_template_loader();

			include($template);
		}


		echo $after_widget;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array  An array of the current settings for this widget
	 * @return void Echoes it's output
	 **/

	function form( $instance ) {

		$defaults = array(
			'wprrw_title'			=>	'',
			'wprrw_slug' 			=> 	'hello-dolly',
			'wprrw_customize' 		=> 	'off',
			'wprrw_installs' 		=> 	'on',
			'wprrw_ratings' 		=>	'on',
			'wprrw_stars'			=>	'on',
			'wprrw_fivestars'		=>	'on',
			'wprrw_translate'		=>	'on',
			'wprrw_download'		=>	'on',
			'wprrw_badge'			=>	'on',
			'wprrw_reviewsenable'	=> 'off',
			'wprrw_reviewnum'		=> 3,
			'wprrw_reviewsstyle'	=> 'Basic',
			'wprrw_authorinfo'		=> 'on',
			'wprrw_authoravatar'	=> 'on',
			'wprrw_reviewlink'		=> 'on',
            'widget_id'             => $this->id
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$installs = esc_attr($instance['wprrw_installs']);

		if ( isset( $instance[ 'wprrw_slug' ] ) ) {
		  $slug = $instance[ 'wprrw_slug' ];
		}

		if ( isset( $instance[ 'wprrw_reviewnum' ] ) ) {
		  $reviewnum = $instance[ 'wprrw_reviewnum' ];
		}

		echo '<p><label for="' . $this->get_field_id( 'wprrw_title' ) . '">Title: <input class="widefat" id="' . $this->get_field_id( 'wprrw_title' ) .'" name="' . $this->get_field_name( 'wprrw_title' ) . '" value="' . esc_attr( $instance['wprrw_title'] ) . '" /></label></p>';
		?>
		<p>
		  	<label for="<?php echo $this->get_field_id( 'wprrw_slug' ); ?>">
			  	<strong><?php _e( 'Your Plugin Slug' ); ?></strong>
			 	<input class="widefat" id="<?php echo $this->get_field_id( 'wprrw_slug' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_slug' ); ?>" type="text" value="<?php echo esc_attr( $slug ); ?>" />
			</label>
		</p>
        
        <?php $widgetid = $instance['widget_id']; ?>

		<script>
		 	jQuery(document).ready( function( $ ) {
				$( 'div[id*="<?php echo $widgetid; ?>"] a[href="#"]' ).click( function(e) {
					e.preventDefault();
				} );

                if( $('div[id*="<?php echo $widgetid; ?>"] input.checkbox.reviewsenable').is(':checked')) {
                    $('div[id*="<?php echo $widgetid; ?>"] ul.wprrw_reviews_settings').show();
                } else {
                    $('div[id*="<?php echo $widgetid; ?>"] ul.wprrw_reviews_settings').hide();
                }

                if( $('div[id*="<?php echo $widgetid; ?>"] input.checkbox.customize').is(':checked')) {
                    $('div[id*="<?php echo $widgetid; ?>"] ul.wprrw_stats_settings').show();
                } else {
                    $('div[id*="<?php echo $widgetid; ?>"] ul.wprrw_stats_settings').hide();
                }

				$('div[id*="<?php echo $widgetid; ?>"] .wprrw-tabs-menu a.stats-click').click(function( event ) {
							 			
				  $('div[id*="<?php echo $widgetid; ?>"] .show-reviews').removeClass('active');
				  $('div[id*="<?php echo $widgetid; ?>"] .wprrw-tabs-menu a.reviews-click').removeClass('highlight');
				  $('div[id*="<?php echo $widgetid; ?>"] .show-stats').addClass('active');
				  $('div[id*="<?php echo $widgetid; ?>"] .wprrw-tabs-menu a.stats-click').addClass('highlight');
				});

				$('div[id*="<?php echo $widgetid; ?>"] .wprrw-tabs-menu a.reviews-click').click(function( event) {
		 			
				  $('div[id*="<?php echo $widgetid; ?>"] .show-stats').removeClass('active');
				  $('div[id*="<?php echo $widgetid; ?>"] .wprrw-tabs-menu a.stats-click').removeClass('highlight');
				  $('div[id*="<?php echo $widgetid; ?>"] .show-reviews').addClass('active');
				  $('div[id*="<?php echo $widgetid; ?>"] .wprrw-tabs-menu a.reviews-click').addClass('highlight');
				});

                $('div[id*="<?php echo $widgetid; ?>"] .checkbox.customize').click(function() {
                    if( $(this).is(':checked')) {
                        $('div[id*="<?php echo $widgetid; ?>"] ul.wprrw_stats_settings').slideDown("slow");
                    } else {
                        $('div[id*="<?php echo $widgetid; ?>"] ul.wprrw_stats_settings').slideUp("slow");
                    }
                });

                $('div[id*="<?php echo $widgetid; ?>"] .checkbox.reviewsenable').click(function() {
                    if( $(this).is(':checked')) {
                        $('div[id*="<?php echo $widgetid; ?>"] ul.wprrw_reviews_settings').slideDown("slow");
                    } else {
                        $('div[id*="<?php echo $widgetid; ?>"] ul.wprrw_reviews_settings').slideUp("slow");
                    }
                });
            });
		</script>

		<!-- Start Widget Settings HTML Output -->
		<div id="wprrw_tabs_container">

				<!-- Widget Settings Tabbed Menu -->
				<ul id="wprrw-tabs-menu" class="wprrw-tabs-menu">
					<li><a href="#" class="stats-click highlight">Stats</a></li>
					<li><a href="#" class="reviews-click">Reviews</a></li>
				</ul>
					
				<!-- Widget Settings "Stats" Tab -->
				<div class="wprrw-tab show-stats active">
					
					<!-- Checkbox to Enable Widget Reviews -->
					<input class="checkbox customize" type="checkbox" <?php checked( $instance[ 'wprrw_customize' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprrw_customize' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_customize' ); ?>" />
					<label for="<?php echo $this->get_field_id( 'wprrw_customize' ); ?>"><?php echo __('Enable Plugin Stats?', 'wppluginratings' ); ?></label>
					<a class="hint--top  hint--medium" data-hint="<?php echo __('By default, WP Repo Plugin Widget will output &#10;all the data based only on your plugin slug. &#10;If you\'d like to customize which elements &#10;are shown for this widget click here:', 'wppluginratings'); ?>">
					<span class="dashicons dashicons-info"></span></a>
					
					<!-- Settings to Customize Widget Stats -->
					<ul class="wprrw_stats_settings">
						<li>
							<input class="checkbox" type="checkbox" <?php checked( $instance[ 'wprrw_installs' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprrw_installs' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_installs' ); ?>" />
		    				<label for="<?php echo $this->get_field_id( 'wprrw_installs' ); ?>"><?php echo __('Enable Active Installs', 'wppluginratings' ); ?></label>
						</li>
						<li>
							<input class="checkbox ratings" type="checkbox" <?php checked( $instance[ 'wprrw_ratings' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprrw_ratings' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_ratings' ); ?>" />
		    				<label for="<?php echo $this->get_field_id( 'wprrw_ratings' ); ?>"><?php echo __('Enable Overall Rating Score', 'wppluginratings' ); ?></label><a class=" hint--top  hint--medium" data-hint="<?php echo __('e.g. Rated 4.8 out of 5 stars from 25 total ratings.', 'wppluginratings'); ?>" ><span class="dashicons dashicons-info"></span></a>
						</li>
						<li>
							<input class="checkbox stars" type="checkbox" <?php checked( $instance[ 'wprrw_stars' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprrw_stars' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_stars' ); ?>" />
		    				<label for="<?php echo $this->get_field_id( 'wprrw_stars' ); ?>"><?php echo __('Enable Stars', 'wppluginratings' ); ?></label>
								<a class=" hint--top  hint--medium" data-hint="<?php echo __('Display stars to represent the total rating for your plugin just like on WP.org.', 'wppluginratings'); ?>" ><span class="dashicons dashicons-info"></span></a>
						</li>
						<li>
							<input class="checkbox fivestars" type="checkbox" <?php checked( $instance[ 'wprrw_fivestars' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprrw_fivestars' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_fivestars' ); ?>" />
		    				<label for="<?php echo $this->get_field_id( 'wprrw_fivestars' ); ?>"><?php echo __('Show number of 5 star ratings', 'wppluginratings' ); ?></label><a class=" hint--top  hint--medium" data-hint="<?php echo __('e.g. 10 five-star reviews out of 11 total.', 'wppluginratings'); ?>" ><span class="dashicons dashicons-info"></span></a>
						</li>
						<li>
							<input class="checkbox download" type="checkbox" <?php checked( $instance[ 'wprrw_download' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprrw_download' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_download' ); ?>" />
		    				<label for="<?php echo $this->get_field_id( 'wprrw_download' ); ?>"><?php echo __('Display a direct link to your plugins download file', 'wppluginratings' ); ?></label><a class=" hint--top  hint--medium" data-hint="<?php echo __('Visitors will be able to download a zip file &#10;of your plugin directly from your website &#10;rather than going to WP.org.', 'wppluginratings'); ?>" ><span class="dashicons dashicons-info"></span></a>
						</li>
						<li>
							<input class="checkbox translate" type="checkbox" <?php checked( $instance[ 'wprrw_translate' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprrw_translate' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_translate' ); ?>" />
		    				<label for="<?php echo $this->get_field_id( 'wprrw_translate' ); ?>"><?php echo __('Link to your plugins Glotpress translation contribution page', 'wppluginratings' ); ?></label>
						</li>
						<li>
							<input class="checkbox badge" type="checkbox" <?php checked( $instance[ 'wprrw_badge' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprrw_badge' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_badge' ); ?>" />
		    				<label for="<?php echo $this->get_field_id( 'wprrw_badge' ); ?>"><?php echo __('Display a "Hosted on WordPress.org" badge that links to your plugin page.', 'wppluginratings' ); ?></label><a class=" hint--top  hint--medium" data-hint="<?php echo __('Special thanks to Stanislav Kromov for his &#10;WordPress Badge generator (Click to visit it).', 'wppluginratings'); ?>" href="https://khromov.github.io/wordpress-badge-generator/" target="_blank"><span class="dashicons dashicons-info"></span></a>
						</li>
					</ul>
				</div><!-- end .wprrw-tab-content -->

				<!-- Widget Settings "Reviews" Tab -->
				<div class="wprrw-tab show-reviews">

					<!-- Checkbox to Enable Reviews Section -->
					<input class="checkbox reviewsenable" type="checkbox" <?php checked( $instance[ 'wprrw_reviewsenable' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprrw_reviewsenable' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_reviewsenable' ); ?>" />

					<label for="<?php echo $this->get_field_id( 'wprrw_reviewsenable' ); ?>">
						<?php echo __('Enable Reviews?', 'wppluginratings' ); ?></label>
					<a class=" hint--top  hint--medium" data-hint="<?php echo __('By default the first 3 5-star reviews &#10;will be output to the page. You can customize &#10;some elements of that here.', 'wppluginratings'); ?>" target="_blank">
					<span class="dashicons dashicons-info"></span></a>
					
					<!-- Settings to Customize Widget Reviews -->
					<ul class="wprrw_reviews_settings">
						<li>
							<label for="<?php echo $this->get_field_id( 'wprrw_reviewnum' ); ?>"><strong><?php _e( 'Number of Reviews to display' ); ?></strong><br />
						  	<input  id="<?php echo $this->get_field_id( 'wprrw_reviewnum' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_reviewnum' ); ?>" type="number" value="<?php echo esc_attr( $reviewnum ); ?>" /></label>
						</li>

                        <li>
                            <input class="checkbox autoplay" type="checkbox" <?php checked( $instance[ 'wprrw_autoplay' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprrw_autoplay' ); ?>" name="<?php echo $this->get_field_name( 'wprrw_autoplay' ); ?>" />
                            <label for="<?php echo $this->get_field_id( 'wprrw_autoplay' ); ?>"><?php echo __('Enable Autoplay?', 'wppluginratings' ); ?></label><a class=" hint--top  hint--medium" data-hint="<?php echo __('Check this box to enable autoplay of the Reviews Carousel.', 'wppluginratings'); ?>" ><span class="dashicons dashicons-info"></span></a>
                        </li>
					</ul>
				</div><!-- end .wprrw-tab-content -->
			</div><!-- end #wprrw_tabs_container -->
	<?php

	//var_dump($instance);
	}

	// Saving/Updating the Widget fields
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
		$instance['wprrw_title'] = ( ! empty( $new_instance['wprrw_title'] ) ) ? strip_tags( $new_instance['wprrw_title'] ) : '';

		$instance['wprrw_slug'] = ( ! empty( $new_instance['wprrw_slug'] ) ) ?  $new_instance['wprrw_slug']  : '';

		$instance['wprrw_customize'] = ( ! checked( $new_instance['wprrw_customize'] ) ) ?  $new_instance['wprrw_customize']  : '';

		$instance['wprrw_installs'] = ( ! checked( $new_instance['wprrw_installs'] ) ) ?  $new_instance['wprrw_installs']  : '';

		$instance['wprrw_ratings'] = ( ! checked( $new_instance['wprrw_ratings'] ) ) ?  $new_instance['wprrw_ratings']  : '';

		$instance['wprrw_stars'] = ( ! checked( $new_instance['wprrw_stars'] ) ) ?  $new_instance['wprrw_stars']  : '';

		$instance['wprrw_fivestars'] = ( ! checked( $new_instance['wprrw_fivestars'] ) ) ?  $new_instance['wprrw_fivestars']  : '';

		$instance['wprrw_translate'] = ( ! checked( $new_instance['wprrw_translate'] ) ) ?  $new_instance['wprrw_translate']  : '';

		$instance['wprrw_download'] = ( ! checked( $new_instance['wprrw_download'] ) ) ?  $new_instance['wprrw_download']  : '';

		$instance['wprrw_badge'] = ( ! checked( $new_instance['wprrw_badge'] ) ) ?  $new_instance['wprrw_badge']  : '';

		$instance['wprrw_reviewsenable'] = ( ! checked( $new_instance['wprrw_reviewsenable'] ) ) ?  $new_instance['wprrw_reviewsenable']  : '';

		$instance['wprrw_reviewnum'] = ( ! empty( $new_instance['wprrw_reviewnum'] ) ) ?  $new_instance['wprrw_reviewnum']  : '';

        $instance['wprrw_autoplay'] = ( ! checked( $new_instance['wprrw_autoplay'] ) ) ?  $new_instance['wprrw_autoplay']  : '';

		return $instance;

	}

	public function wprrw_template_loader() {

        $template = WPRRW_PATH . '/views/default-view.php';

		return apply_filters('wprrw_template', $template);
	}

	public function wprrw_load_admin_scripts($hook) {

		if( $hook != 'widgets.php' )
			return;

		// Enqueue our Admin scripts
		wp_enqueue_style( 'wprrw-css', WPRRW_URL . 'css/wprrw-admin.css' );
	}

	public function wprrw_callapi($slug) {

		// Require plugin-install.php to query the Repo correctly
		if( !function_exists( 'plugins_api' ) ) {
			require_once(ABSPATH . 'wp-admin/includes/plugin-install.php' );
		}

		// Only get the information we really want from the Repo API
		$call_api = plugins_api( 'plugin_information',
			array(
				'slug' => $slug,
				'fields' => array(
					'active_installs' => true,
					'sections' => false,
					'reviews' => true,
                    'ratings' => true,
					'homepage' => false,
					'added' => false,
					'last_updated' => false,
					'downloaded' => false,
					'compatibility' => false,
					'tested' => false,
					'requires' => false,
					'tags' => false,
					'donate_link' => false ) ) );

		// Plugin/Theme authors can customize this API call if they want
		apply_filters('wprrw-callapi', $call_api);

		return $call_api;

	}

	/**
	 *	The following functions each call parts of the Basic
	 *  Widget template with conditional logic and filters
	 *  for easy extenstion by developers.
	 *  @since 1.0
	 *  @var $instance
	 */

 	// function used to pass widget variables to template functions
 	public function variables( $instance ) {
		
		$slug = $this->get_the_slug($instance);
		
		$cachedresults = get_transient( 'wp-plugin-repo-data-' . $slug );

		if ( $cachedresults ) {
			$call_api = $cachedresults;
			$iscached = 'Not Cached';
		} else {
			$iscached = 'Results Cached';
			$call_api = $this->wprrw_callapi($slug=$instance['wprrw_slug']);
		}

		$var = array(
            'slug' => $this->get_the_slug($instance),
            'rating' => $call_api->rating,
            'ratings' => $call_api->ratings,
            'installs' => $call_api->active_installs,
            'fivestars' => $call_api->ratings[5],
            'numratings' => $call_api->num_ratings,
            'repolink' => 'https://wordpress.org/plugins/' . $slug,
            'download' => $call_api->download_link,
            'name' => $call_api->name
		  );

		return $var;
	}

	/** 
	 *  GET THE SLUG
	 *  A helper function to get the plugin slug more easily
	 *
	 *  @since 1.0
	 *	@var $instance
	 **/
	
	
	public function get_the_slug($instance) {
		
		// A helper function to get the plugin slug more easily
		$slug = $instance['wprrw_slug'];
		$cleanslug = esc_attr($slug);
		return $cleanslug;
	}
	
	/** 
	 *  GET THE REVIEWS
	 *  Our main function for getting the reviews
	 *  and splittting them up into individual reviews
	 *  and their parts
	 *
	 *  @since 1.0
	 *	@var $instance
	 **/
	
    public function wprrw_split_reviews($instance) {

        // Create DOM from reviews output string
        $wprepo = new WP_Ratings_Widget;
        $xpath = $wprepo->wprrw_get_xpath($instance);

        // Target the class of the reviews element
        $reviewclass = './/div[contains(concat(" ", normalize-space(@class), " "), " review ")]';

        // Loop through all review elements
        // and output the markup accordingly
        $i = 0;

        foreach ( $xpath->evaluate($reviewclass) as $div ) {

            // Save each review as an XML instance
            $raw_review = $div->ownerDocument->saveXML( $div );

            // Grab all "a" elements from each review
            $linkhrefs = array();
            $linkTags  = $div->getElementsByTagName( 'a' );

            // Grab the links from each "a" element
            // and define the Author according to the
            // text inside the "a" element
            foreach ( $linkTags as $tag ) {
                $linkhrefs[] = $tag->getAttribute( 'href' );
                $author = trim( strip_tags( $tag->ownerDocument->saveXML( $tag ) ));
            }

            // If a reviewer added any links into their
            // review, it will return as their author link
            // So we'll grab their author url instead and
            // trim that to be their name
            if ( strpos( $author,'http' ) !== false ) {

                $profile = str_replace("//profiles.wordpress.org/", "", $linkhrefs[0]);

                $author = $profile;
            } else {
                $author = $author;
            }

            // Grab and define each element of the review
            $gettitle = $this->wprrw_getElementsByClass($div, 'div', 'review-title-section');
            $title = substr($gettitle[0]->textContent, 0, -13);

            $getstars = $this->wprrw_getElementsByClass($div, 'div', 'star-rating');
            $stars = $getstars[0]->textContent;

            $getrevdate = $this->wprrw_getElementsByClass($div, 'span', 'review-date');
            $revdate = $getrevdate[0]->textContent;

            $getcontent = $this->wprrw_getElementsByClass($div, 'div', 'review-body');
            $content = $getcontent[0]->textContent;
            $trimmedcontent = wp_trim_words($content, 50);

            $starnum = substr($stars, 0, -9);

            //Include our Reviews Template
            if ( $starnum >= 4 ) {
                include(WPRRW_PATH . 'views/default-reviews.php');
            }

            // Get number of reviews to show and break
            $num = $instance['wprrw_reviewnum'];
            if (++$i == $num) break;

            // For testing you can always echo $raw_review
            // to see the original output that the plugins_api returns

            //echo $raw_review;

        }

    }


	/** 
	 *  GET REVIEW ELEMENTS BY CLASS NAMES
	 *  Helper function to get elements of the review
	 *  according to their class names from the XHTML output
	 *
	 *  @since 1.0
	 *	@var $parentNode
	 *  @var $tagName
	 *  @var $className
	 * 
	 **/

	public function wprrw_getElementsByClass($parentNode, $tagName, $className) {
		$nodes=array();

		$childNodeList = $parentNode->getElementsByTagName($tagName);

		for ($i = 0; $i < $childNodeList->length; $i++) {
			$temp = $childNodeList->item($i);

			if (stripos($temp->getAttribute('class'), $className) !== false) {
			  $nodes[]=$temp;
			}
		}

	  return $nodes;
	}

	/** 
	 *  GET REVIEWS AS XPATH
	 *  Helper function to get reviews as xpath format
	 *
	 *  @since 1.0
	 *	@var $parentNode
	 *  @var $tagName
	 *  @var $className
	 * 
	 **/
	
	public function wprrw_get_xpath($instance) {

        // Require plugin-install.php to query the Repo correctly.
        if ( ! function_exists( 'plugins_api' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
        }

	  	$call_api = plugins_api( 'plugin_information',
			array(
			  	'slug' => $this->get_the_slug($instance),
			  	'fields' => array(
				'reviews' => true) 
			) );

	  	$doc = new DOMDocument();

	  	global $reviews;
	  	$reviews = $call_api->sections['reviews'];
	  	
	  	if (!empty($reviews)) {

	  		$doc->loadHTML($reviews);
	  	}

		$doc->saveHTML();

		$xpath = new DOMXpath($doc);

		return $xpath;
	}

} // end of WP_Ratings_Widget class

// Register the WP Ratings Widget
function register_wp_repo_widget() {
	register_widget('WP_Ratings_Widget');
}

add_action( 'widgets_init', 'register_wp_repo_widget' );
