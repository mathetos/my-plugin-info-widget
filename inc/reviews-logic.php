<?php

function wprws_split_reviews() {
  // Create DOM from reviews output string
  $xpath = wprws_get_xpath();

  // Target the class of the reviews element
  $reviewclass = './/div[contains(concat(" ", normalize-space(@class), " "), " review ")]';

  //Begin Review Wrapper Div
  ?>
  <h4>Reviews</h4>
  <div class="reviews_nav">
    <span class="prevArrow"><?php echo __('Previous Review', 'wprepowidget'); ?></span>
    <span class="nextArrow"><?php echo __('Next Review', 'wprepowidget'); ?></span>
  </div>
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
    $gettitle = wprws_getElementsByClass($div, 'div', 'review-title-section');
    $title = substr($gettitle[0]->textContent, 0, -13);

    $getstars = wprws_getElementsByClass($div, 'div', 'star-rating');
    $stars = $getstars[0]->textContent;

    $getrevdate = wprws_getElementsByClass($div, 'span', 'review-date');
    $revdate = $getrevdate[0]->textContent;

    $getcontent = wprws_getElementsByClass($div, 'div', 'review-body');
    $content = $getcontent[0]->textContent;
    $trimmedcontent = wp_trim_words($content, 50);

    $starnum = substr($stars, 0, -9);
    $i = 0;
    if ( $starnum >= 4 ) {
      $i++;
      if ( $i++ <= 4 ) {
        include(WPRWS_PATH . 'views/default-reviews.php');
      }
    }
    // For testing you can always echo
    // $raw_review to see the original output
    // that the plugins_api returns
    //echo $raw_review;

	}
  // End Reviews Wrapper Div
  echo '</div>';
}

// Output the reviews function
wprws_split_reviews();

// Gets elements of the review by their class names
function wprws_getElementsByClass($parentNode, $tagName, $className) {
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

// Creates the DOM and XPath out of the review string
// returned by the plugins_api
function wprws_get_xpath() {
  $call_api = plugins_api( 'plugin_information',
    array(
      'slug' => 'foogallery-owl-carousel-template',
      'fields' => array(
        'reviews' => true) ) );

  $reviews = $call_api->sections['reviews'];

  $doc = new DOMDocument();
  $doc->loadHTML($reviews);
  $doc->saveHTML();

  $xpath = new DOMXpath($doc);

  return $xpath;
}
