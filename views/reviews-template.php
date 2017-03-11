<?php

$wprepo = new WP_Ratings_Widget(); 
?>

	<div class="wprrw_reviews_enabled">
		<h4 class="wprrw_reviews_title">Reviews</h4>
		<div class="wprrw_reviews_wrap" data-slick=\'{"slidesToShow": 1, "slidesToScroll": 1}\'>

			<?php
                $wprepo->wprrw_split_reviews($instance);
			?>

		</div>
	</div>

	<div class="reviews_nav">
		<span class="prevArrow"><?php echo __('Previous Review', 'wppluginratings'); ?></span>
		<span class="nextArrow"><?php echo __('Next Review', 'wppluginratings'); ?></span>
	</div>

	<div class="reviews_all">
		<a href="https://wordpress.org/support/view/plugin-reviews/<?php echo $slug; ?>" target="_blank"><?php echo __('See all reviews', 'wppluginratings'); ?></a>
	</div>

<?php
