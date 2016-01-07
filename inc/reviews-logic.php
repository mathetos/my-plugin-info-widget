<?php



// Creates the DOM and XPath out of the review string
// returned by the plugins_api




// Last but not least, Output the reviews function
$wprepo = new WP_Repo_Widget();
$showreviews = $wprepo->wprws_split_reviews($instance);
echo $showreviews;
