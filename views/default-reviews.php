<?php ?>
<div class="single-review">
  <header>
    <h3><?php echo $title; ?></h3>
    <span><span class="dashicons dashicons-admin-users"></span><a href="<?php echo $linkhrefs[0]?>" target="_blank"><?php echo $author; ?></a></span> 
    <span><span class="dashicons dashicons-calendar-alt"></span><?php echo $revdate; ?></span> 
    <span><span class="dashicons dashicons-star-filled"></span><?php echo $stars; ?></span>
  </header>
  <p><?php echo $trimmedcontent; ?></p>
</div>
<?php
