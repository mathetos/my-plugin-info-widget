<?php

/*
 *    outputs the star rating html
 */
$stars = $var['rating']/100*5;
?>
<div class="wporg-ratings" title="<?php echo $stars; ?> out of 5 stars" style="color:#e6b800;">
<?php
for ($i=0; $i < 5; $i++) {
    if ($stars <= $i ) {
        echo  '<span class="dashicons dashicons-star-empty"></span>';
    } elseif ($stars <= $i + 0.5) {
        echo  '<span class="dashicons dashicons-star-half"></span>';
    } else {
        echo  '<span class="dashicons dashicons-star-filled"></span>';
    }
}
?>
</div>
