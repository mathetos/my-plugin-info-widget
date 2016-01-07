<?php
/**
 * Plugin Name: 	WP Plugin Repo Widget and Shortcode
 * Plugin URI: 		https://www.mattcromwell.com/products/wp-plugin-repo-widget
 * Description: 	Adds a widget and shortcode for displaying information about WordPress plugins directly from the WordPress.org Plugin Directory. Ideal for plugin authors to display on their websites.
 * Version: 		1.0
 * Author: 			Matt Cromwell
 * Author URI: 		https://www.mattcromwell.com
 * License:      	GNU General Public License v2 or later
 * License URI:  	http://www.gnu.org/licenses/gpl-2.0.html
 * Textdomain: wprepowidget
 */

// Globals
define( 'WPRWS_SLUG', 'wprepowidget' );
define( 'WPRWS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPRWS_URL', plugin_dir_url( __FILE__ ) );
define( 'WPRWS_VERSION', '1.0' );

class WP_Repo_Widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 **/

	function __construct() {
	    $widget_ops = array( 'classname' => 'wp_repo_widget', 'description' => 'WP Repo widget' );
	    parent::__construct( 'wp_repo_widget', 'WP Repo widget', $widget_ops );

			add_action( 'admin_enqueue_scripts', array($this, 'wprws_load_admin_scripts') );
	}

  /**
   * Outputs the HTML for this widget.
   *
   * @param array  An array of standard parameters for widgets in this theme
   * @param array  An array of settings for this widget instance
   * @return void Echoes it's output
   **/

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		echo $before_widget;

		global $slug;
		$slug = esc_attr($instance['wprws_slug']);

		$call_api = $this->wprws_callapi($slug='');

		if ( isset( $instance[ 'wprws_slug' ] ) ) {
		  $slug = $instance[ 'wprws_slug' ];
		}

		//Check for Errors & Display the results
		if ( is_wp_error( $call_api ) ) {

				echo '<pre>' . print_r( $call_api->get_error_message(), true ) . '</pre>';

		//** If no errors and no cached results, display the results based on a new API call **//
		} else {
			$template = self::wprws_template_loader();

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
			'wprws_title'			=>	'',
			'wprws_slug' 			=> 	'hello-dolly',
			'wprws_customize' => 	'off',
			'wprws_installs' 	=> 	'on',
			'wprws_ratings' 	=>	'on',
			'wprws_stars'			=>	'on',
			'wprws_fivestars'	=>	'on',
			'wprws_translate'	=>	'on',
			'wprws_download'	=>	'on',
			'wprws_badge'			=>	'on',
			'wprws_reviewsenable'		=> 'off',
			'wprws_reviewnum'				=> 3,
			'wprws_reviewsstyle'		=> 'Basic',
			'wprws_authorinfo'			=> 'on',
			'wprws_authoravatar'		=> 'on',
			'wprws_reviewlink'			=> 'on'
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$installs = esc_attr($instance['wprws_installs']);

		if ( isset( $instance[ 'wprws_slug' ] ) ) {
		  $slug = $instance[ 'wprws_slug' ];
		}

		if ( isset( $instance[ 'wprws_reviewnum' ] ) ) {
		  $reviewnum = $instance[ 'wprws_reviewnum' ];
		}

		echo '<p><label for="' . $this->get_field_id( 'wprws_title' ) . '">Title: <input class="widefat" id="' . $this->get_field_id( 'wprws_title' ) .'" name="' . $this->get_field_name( 'wprws_title' ) . '" value="' . esc_attr( $instance['wprws_title'] ) . '" /></label></p>';
		?>
		<p>
		  <label for="<?php echo $this->get_field_id( 'wprws_slug' ); ?>"><strong><?php _e( 'Your Plugin Slug' ); ?></strong>
		  <input class="widefat" id="<?php echo $this->get_field_id( 'wprws_slug' ); ?>" name="<?php echo $this->get_field_name( 'wprws_slug' ); ?>" type="text" value="<?php echo esc_attr( $slug ); ?>" /></label>
			<hr />
		</p>
		<div class="customize-wrap">
				<label for="<?php echo $this->get_field_id( 'wprws_customize' ); ?>"><?php echo __('Customize Plugin Elements', 'wprepowidget' ); ?><a class="hint--left" data-hint="<?php echo __('By default, WP Repo Plugin Widget will output &#10;all the data based only on your plugin slug. &#10;If you\'d like to customize which elements &#10;are shown for this widget click here:', 'wprepowidget'); ?>"><span class="dashicons dashicons-info"></span></a></label><input class="checkbox customize" type="checkbox" <?php checked( $instance[ 'wprws_customize' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_customize' ); ?>" name="<?php echo $this->get_field_name( 'wprws_customize' ); ?>" />
			<fieldset id="wprws_customize">
				<ul class="wprws_customize">
					<li>
						<input class="checkbox" type="checkbox" <?php checked( $instance[ 'wprws_installs' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_installs' ); ?>" name="<?php echo $this->get_field_name( 'wprws_installs' ); ?>" />
	    			<label for="<?php echo $this->get_field_id( 'wprws_installs' ); ?>"><?php echo __('Enable Active Installs', 'wprepowidget' ); ?></label>
					</li>
					<li>
						<input class="checkbox ratings" type="checkbox" <?php checked( $instance[ 'wprws_ratings' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_ratings' ); ?>" name="<?php echo $this->get_field_name( 'wprws_ratings' ); ?>" />
	    			<label for="<?php echo $this->get_field_id( 'wprws_ratings' ); ?>"><?php echo __('Enable Overall Rating Score', 'wprepowidget' ); ?></label><a class=" hint--left" data-hint="<?php echo __('e.g. Rated 4.8 out of 5 stars from 25 total ratings.', 'wprepowidget'); ?>" ><span class="dashicons dashicons-info"></span></a>
					</li>
					<li>
							<input class="checkbox stars" type="checkbox" <?php checked( $instance[ 'wprws_stars' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_stars' ); ?>" name="<?php echo $this->get_field_name( 'wprws_stars' ); ?>" />
	    				<label for="<?php echo $this->get_field_id( 'wprws_stars' ); ?>"><?php echo __('Enable Stars', 'wprepowidget' ); ?></label>
							<a class=" hint--left" data-hint="<?php echo __('Display stars to represent the total rating for your plugin just like on WP.org.', 'wprepowidget'); ?>" ><span class="dashicons dashicons-info"></span></a>
					</li>
					<li>
						<input class="checkbox fivestars" type="checkbox" <?php checked( $instance[ 'wprws_fivestars' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_fivestars' ); ?>" name="<?php echo $this->get_field_name( 'wprws_fivestars' ); ?>" />
	    			<label for="<?php echo $this->get_field_id( 'wprws_fivestars' ); ?>"><?php echo __('Show number of 5 star ratings', 'wprepowidget' ); ?></label><a class=" hint--left" data-hint="<?php echo __('e.g. 10 five-star reviews out of 11 total.', 'wprepowidget'); ?>" ><span class="dashicons dashicons-info"></span></a>
					</li>
					<li>
						<input class="checkbox download" type="checkbox" <?php checked( $instance[ 'wprws_download' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_download' ); ?>" name="<?php echo $this->get_field_name( 'wprws_download' ); ?>" />
	    			<label for="<?php echo $this->get_field_id( 'wprws_download' ); ?>"><?php echo __('Display a direct link to your plugins download file', 'wprepowidget' ); ?></label><a class=" hint--left" data-hint="<?php echo __('Visitors will be able to download a zip file &#10;of your plugin directly from your website &#10;rather than going to WP.org.', 'wprepowidget'); ?>" ><span class="dashicons dashicons-info"></span></a>
					</li>
					<li>
						<input class="checkbox translate" type="checkbox" <?php checked( $instance[ 'wprws_translate' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_translate' ); ?>" name="<?php echo $this->get_field_name( 'wprws_translate' ); ?>" />
	    			<label for="<?php echo $this->get_field_id( 'wprws_translate' ); ?>"><?php echo __('Link to your plugins Glotpress translation contribution page', 'wprepowidget' ); ?></label>
					</li>
					<li>
						<input class="checkbox badge" type="checkbox" <?php checked( $instance[ 'wprws_badge' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_badge' ); ?>" name="<?php echo $this->get_field_name( 'wprws_badge' ); ?>" />
	    			<label for="<?php echo $this->get_field_id( 'wprws_badge' ); ?>"><?php echo __('Display a "Hosted on WordPress.org" badge that links to your plugin page.', 'wprepowidget' ); ?></label><a class=" hint--left" data-hint="<?php echo __('Special thanks to Stanislav Kromov for his &#10;WordPress Badge generator (Click to visit it).', 'wprepowidget'); ?>" href="https://khromov.github.io/wordpress-badge-generator/" target="_blank"><span class="dashicons dashicons-info"></span></a>
					</li>
				</ul>
			<footer>&nbsp;</footer>
			</fieldset>

			<fieldset id="wprws_reviews">
				<ul>
					<li>
						<input class="checkbox reviewsenable" type="checkbox" <?php checked( $instance[ 'wprws_reviewsenable' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_reviewsenable' ); ?>" name="<?php echo $this->get_field_name( 'wprws_reviewsenable' ); ?>" />
	    			<label for="<?php echo $this->get_field_id( 'wprws_reviewsenable' ); ?>"><?php echo __('Customize Reviews?', 'wprepowidget' ); ?><a class=" hint--left" data-hint="<?php echo __('By default the first 3 5-star reviews &#10;will be output to the page. You can customize &#10;some elements of that here.', 'wprepowidget'); ?>" target="_blank"><span class="dashicons dashicons-info"></span></a></label>
						<ul class="yesenable">
							<li>
								<label for="<?php echo $this->get_field_id( 'wprws_reviewnum' ); ?>"><strong><?php _e( 'Number of Reviews to display' ); ?></strong>
							  <input class="widefat" id="<?php echo $this->get_field_id( 'wprws_reviewnum' ); ?>" name="<?php echo $this->get_field_name( 'wprws_reviewnum' ); ?>" type="number" value="<?php echo esc_attr( $reviewnum ); ?>" /></label>
							</li>
							<li>
								<label for="<?php echo $this->get_field_name( 'wprws_reviewsstyle' ); ?>"><strong><?php echo __('Chooose review presentation style', 'wprepowidget' ); ?></strong></label><br />
								<input class="radio style" type="radio" id="<?php echo $this->get_field_id( 'wprws_reviewsstyle' ); ?>" name="<?php echo $this->get_field_name( 'wprws_reviewsstyle' ); ?>" value="Basic" <?php echo $instance['wprws_reviewsstyle'] == 'Basic' ? 'checked="checked"' : ''; ?> /><?php echo __('Plain text presentation', 'wprepowidget'); ?><br />
								<input class="radio style" type="radio" id="<?php echo $this->get_field_id( 'wprws_reviewsstyle' ); ?>" name="<?php echo $this->get_field_name( 'wprws_reviewsstyle' ); ?>" value="Fade" <?php echo $instance['wprws_reviewsstyle'] == 'Fade' ? 'checked="checked"' : ''; ?> /><?php echo __('Fade 1 review at a time', 'wprepowidget'); ?><br />
								<input class="radio style" type="radio" id="<?php echo $this->get_field_id( 'wprws_reviewsstyle' ); ?>" name="<?php echo $this->get_field_name( 'wprws_reviewsstyle' ); ?>" value="Slider" <?php echo $instance['wprws_reviewsstyle'] == 'Slider' ? 'checked="checked"' : ''; ?> /><?php echo __('Slider with navigation', 'wprepowidget'); ?><br />
							</li>
							<li>
								<input class="checkbox authorinfo" type="checkbox" <?php checked( $instance[ 'wprws_authorinfo' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_authorinfo' ); ?>" name="<?php echo $this->get_field_name( 'wprws_authorinfo' ); ?>" />
			    			<label for="<?php echo $this->get_field_id( 'wprws_authorinfo' ); ?>"><?php echo __('Show author info for each review?', 'wprepowidget' ); ?></label>
							</li>
							<li>
								<input class="checkbox authoravatar" type="checkbox" <?php checked( $instance[ 'wprws_authoravatar' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_authoravatar' ); ?>" name="<?php echo $this->get_field_name( 'wprws_authoravatar' ); ?>" />
			    			<label for="<?php echo $this->get_field_id( 'wprws_authoravatar' ); ?>"><?php echo __('Show author avatar for each review', 'wprepowidget' ); ?></label>
							</li>
							<li>
								<input class="checkbox reviewlink" type="checkbox" <?php checked( $instance[ 'wprws_reviewlink' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'wprws_reviewlink' ); ?>" name="<?php echo $this->get_field_name( 'wprws_reviewlink' ); ?>" />
			    			<label for="<?php echo $this->get_field_id( 'wprws_reviewlink' ); ?>"><?php echo __('Show a link to each review.', 'wprepowidget' ); ?></label>
							</li>
						</ul>
					</li>
				</ul>
				<footer>&nbsp;</footer>
			</fieldset>
		</div>
		<?php

		//var_dump($instance);
	}

	// Saving/Updating the Widget fields
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wprws_title'] = ( ! empty( $new_instance['wprws_title'] ) ) ? strip_tags( $new_instance['wprws_title'] ) : '';

		$instance['wprws_slug'] = ( ! empty( $new_instance['wprws_slug'] ) ) ?  $new_instance['wprws_slug']  : '';

		$instance['wprws_customize'] = ( ! checked( $new_instance['wprws_customize'] ) ) ?  $new_instance['wprws_customize']  : '';

		$instance['wprws_installs'] = ( ! checked( $new_instance['wprws_installs'] ) ) ?  $new_instance['wprws_installs']  : '';

		$instance['wprws_ratings'] = ( ! checked( $new_instance['wprws_ratings'] ) ) ?  $new_instance['wprws_ratings']  : '';

		$instance['wprws_stars'] = ( ! checked( $new_instance['wprws_stars'] ) ) ?  $new_instance['wprws_stars']  : '';

		$instance['wprws_fivestars'] = ( ! checked( $new_instance['wprws_fivestars'] ) ) ?  $new_instance['wprws_fivestars']  : '';

		$instance['wprws_translate'] = ( ! checked( $new_instance['wprws_translate'] ) ) ?  $new_instance['wprws_translate']  : '';

		$instance['wprws_download'] = ( ! checked( $new_instance['wprws_download'] ) ) ?  $new_instance['wprws_download']  : '';

		$instance['wprws_badge'] = ( ! checked( $new_instance['wprws_badge'] ) ) ?  $new_instance['wprws_badge']  : '';

		$instance['wprws_reviewsenable'] = ( ! checked( $new_instance['wprws_reviewsenable'] ) ) ?  $new_instance['wprws_reviewsenable']  : '';

		$instance['wprws_reviewnum'] = ( ! empty( $new_instance['wprws_reviewnum'] ) ) ?  $new_instance['wprws_reviewnum']  : '';

		$instance['wprws_reviewsstyle'] = ( ! checked( $new_instance['wprws_reviewsstyle'] ) ) ?  $new_instance['wprws_reviewsstyle']  : '';

		$instance['wprws_authorinfo'] = ( ! checked( $new_instance['wprws_authorinfo'] ) ) ?  $new_instance['wprws_authorinfo']  : '';

		$instance['wprws_authoravatar'] = ( ! checked( $new_instance['wprws_authoravatar'] ) ) ?  $new_instance['wprws_authoravatar']  : '';

		$instance['wprws_reviewlink'] = ( ! checked( $new_instance['wprws_reviewlink'] ) ) ?  $new_instance['wprws_reviewlink']  : '';

		return $instance;

	}

	public function wprws_template_loader() {

		if (file_exists( get_template_directory() . '/wprws/custom-view.php')) {
			$template = get_template_directory() . '/wprws/custom-view.php';
		} elseif(file_exists( get_stylesheet_directory() . '/wprws/custom-view.php')) {
			$template = get_stylesheet_directory() . '/wprws/custom-view.php';
		} else {
			$template = WPRWS_PATH . '/views/default-view.php';
		}

		return apply_filters('wprws_template', $template);
	}

	public function wprws_load_admin_scripts($hook) {

		if( $hook != 'widgets.php' )
			return;

		wp_enqueue_style( 'wprws-css', WPRWS_URL . 'css/wprws-admin.css' );
	}

	public function wprws_callapi($slug) {

		/** Require plugin-install.php to query the Repo correctly */
		if( ! function_exists( 'plugins_api' ) ) {
			require_once(ABSPATH . 'wp-admin/includes/plugin-install.php' );
		}

		$call_api = plugins_api( 'plugin_information',
			array(
				'slug' => $slug,
				'fields' => array(
					'active_installs' => true,
					'sections' => false,
					'reviews' => true,
					'homepage' => false,
					'added' => false,
					'last_updated' => false,
					'downloaded' => false,
					'compatibility' => false,
					'tested' => false,
					'requires' => false,
					'tags' => false,
					'donate_link' => false ) ) );

		apply_filters('wprws-callapi', $call_api);

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
				$call_api = $this->wprws_callapi($slug=$instance['wprws_slug']);
			}

 		$var = array(
			'slug' => $this->get_the_slug($instance),
 		  'rating' => $call_api->rating,
 		  'installs' => $call_api->active_installs,
 		  'fivestars' => $call_api->ratings[5],
 		  'numratings' => $call_api->num_ratings,
 		  'repolink' => 'https://wordpress.org/plugins/' . $slug,
 		  'download' => $call_api->download_link,
 		  'name' => $call_api->name
 		  );

		return $var;
 	}
	
	public function get_the_slug($instance) {
		$slug = $instance['wprws_slug'];
		$cleanslug = esc_attr($slug);
		return $cleanslug;
	}
	
	/* 
	 *  Getting the Reviews
	 */
	
	public function wprws_split_reviews($instance) {
	  // Create DOM from reviews output string
	  $wprepo = new WP_Repo_Widget;
	  $xpath = $wprepo->wprws_get_xpath($instance);

	  // Target the class of the reviews element
	  $reviewclass = './/div[contains(concat(" ", normalize-space(@class), " "), " review ")]';

	  //Begin Review Wrapper Div
	  ?>
	  <h4 class="wpwrs_reviews_title">Reviews</h4>
	  <div class="wpwrs_reviews_wrap" data-slick=\'{"slidesToShow": 1, "slidesToScroll": 1}\'>';
	  <?php   

	  // Loop through all review elements
	  // and output the markup accordingly

	  foreach ($xpath->evaluate($reviewclass) as $div) {
	  $i = 0;
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
		if (strpos($author,'http') !== false) {
		  $profile = str_replace("//profiles.wordpress.org/", "", $linkhrefs[0]);
		  $author = $profile;
		} else {
		  $author = $author;
		}

		// Define each element of the review
		$gettitle = $this->wprws_getElementsByClass($div, 'div', 'review-title-section');
		$title = substr($gettitle[0]->textContent, 0, -13);

		$getstars = $this->wprws_getElementsByClass($div, 'div', 'star-rating');
		$stars = $getstars[0]->textContent;

		$getrevdate = $this->wprws_getElementsByClass($div, 'span', 'review-date');
		$revdate = $getrevdate[0]->textContent;

		$getcontent = $this->wprws_getElementsByClass($div, 'div', 'review-body');
		$content = $getcontent[0]->textContent;
		$trimmedcontent = wp_trim_words($content, 50);

		$starnum = substr($stars, 0, -9);
		$i = 0;
		if ( $starnum >= 4 ) {
			include(WPRWS_PATH . 'views/default-reviews.php');
		}
		// For testing you can always echo
		// $raw_review to see the original output
		// that the plugins_api returns
		//echo $raw_review;

		}
		// End Reviews Wrapper Div
		echo '</div>';

	}


	// Gets elements of the review by their class names
	public function wprws_getElementsByClass($parentNode, $tagName, $className) {
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
	
	public function wprws_get_xpath($instance) {
	  $call_api = plugins_api( 'plugin_information',
		array(
		  'slug' => $this->get_the_slug($instance),
		  'fields' => array(
			'reviews' => true) ) );

			$reviews = $call_api->sections['reviews'];

	  $doc = new DOMDocument();
	  $doc->loadHTML($reviews);
	  $doc->saveHTML();

	  $xpath = new DOMXpath($doc);

	  return $xpath;
	}

} // end of WP_Repo_Widget class

function register_wp_repo_widget() {
	register_widget('WP_Repo_Widget');
}

add_action( 'widgets_init', 'register_wp_repo_widget' );
