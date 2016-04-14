<?php



// Creates the DOM and XPath out of the review string
// returned by the plugins_api




// Last but not least, Output the reviews function
$wprepo = new WP_Repo_Widget(); 
if (!empty($reviews)) : ?>

<div class="wpwrs_reviews_enabled">
	<h4 class="wpwrs_reviews_title">Reviews</h4>
	<div class="wpwrs_reviews_wrap" data-slick=\'{"slidesToShow": 1, "slidesToScroll": 1}\'>';

<?php

	$showreviews = $wprepo->wprws_split_reviews($instance);

	echo $showreviews;


// End Reviews Wrapper Div
echo '</div></div>'; ?>

<div class="reviews_nav">
		<span class="prevArrow"><?php echo __('Previous Review', 'wprepowidget'); ?></span>
		<span class="nextArrow"><?php echo __('Next Review', 'wprepowidget'); ?></span>
	</div>
	<div class="reviews_all">
		<a href="https://wordpress.org/support/view/plugin-reviews/<?php echo $slug; ?>" target="_blank"><?php echo __('See all reviews', 'wprepowidget'); ?></a>
	</div>

<?php else :
	echo '<p class="no-reviews">' . __('No reviews yet!', 'wpreviews');

	echo '&nbsp;<a href="https://wordpress.org/support/view/plugin-reviews/' . $slug . '" target="_blank">' . __('Be the first to review this plugin!', 'wpreviews') . '</a></p>';

	endif;