<?php
/* Template genérico do WooCommerce */
get_header();
if (function_exists('woocommerce_content')) {
   woocommerce_content();
} else {
   while (have_posts()) : the_post();
      the_content();
   endwhile;
}
get_footer();
