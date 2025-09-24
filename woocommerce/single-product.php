<?php
defined('ABSPATH') || exit;
get_header('shop'); ?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
   <?php while (have_posts()) : the_post(); ?>
      <?php wc_get_template_part('content', 'single-product'); ?>
   <?php endwhile; ?>
</div>

<?php get_footer('shop'); ?>