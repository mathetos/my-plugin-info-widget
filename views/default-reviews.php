<?php ?>
<div class="single-review">
  <header>
    <h3><?php echo $title; ?></h3>
    <span><span class="dashicons dashicons-admin-users"></span><?php echo $author; ?></span> |
    <span><span class="dashicons dashicons-calendar-alt"></span><?php echo $revdate; ?></span> |
    <span><span class="dashicons dashicons-star-filled"></span><?php echo $stars; ?></span>
  </header>
  <p><?php echo $trimmedcontent; ?></p>
</div>
<?php
