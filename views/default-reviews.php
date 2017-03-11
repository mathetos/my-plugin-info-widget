<?php ?>
<div class="single-review">

  <header>

    <h3><?php echo $title; ?></h3>

    <span><i class="dashicons dashicons-admin-users"></i>
        <?php echo $author; ?></span>
    <span><i class="dashicons dashicons-star-filled"></i>
        <?php echo number_format( $stars, 0, '', '' ); ?> Stars</span>

  </header>

  <p><?php echo $trimmedcontent; ?></p>

</div>
<?php
