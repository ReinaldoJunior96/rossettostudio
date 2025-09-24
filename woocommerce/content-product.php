<?php
defined('ABSPATH') || exit;
global $product;

if (empty($product) || ! $product->is_visible()) return;
?>
<article <?php wc_product_class('group rounded-xl  ring-1 ring-purple-200 p-4 hover:shadow-md transition', $product); ?>>
   <a href="<?php the_permalink(); ?>" class="block">
      <?php echo get_the_post_thumbnail(null, 'woocommerce_thumbnail', [
         'class' => 'w-full aspect-square object-cover rounded-lg mb-3'
      ]); ?>
      <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 mb-1"><?php the_title(); ?></h3>

   </a>

   <div class="text-sm text-gray-700 mb-3"><?php echo $product->get_price_html(); ?></div>

   <?php woocommerce_template_loop_add_to_cart([
      'class' => 'button w-full rounded-lg bg-purple-700 text-white hover:bg-purple-800'
   ]); ?>
</article>