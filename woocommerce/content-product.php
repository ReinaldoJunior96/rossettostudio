<?php

/**
 * Loop product card – “compact premium”
 * Coloque em: yourtheme/woocommerce/content-product.php
 */
defined('ABSPATH') || exit;

global $product;
if (empty($product) || ! $product->is_visible()) return;

$id           = $product->get_id();
$permalink    = $product->get_permalink();
$title        = $product->get_name();
$is_on_sale   = $product->is_on_sale();
$in_stock     = $product->is_in_stock();
$price_html   = $product->get_price_html();
$regular      = $product->get_regular_price();
$sale         = $product->get_sale_price();
$rating       = (float) $product->get_average_rating();
$reviews      = (int) $product->get_review_count();

/* “Badges” (exemplos)
   - “NEW”: produto criado há ≤ 30 dias
   - “Único”: meta custom “rs_unique” = yes
   - “Exclusivo”: destaque/featured
   Ajuste como quiser.
*/
$badges = [];
$created = strtotime(get_post_field('post_date', $id));
if ($created >= strtotime('-30 days'))            $badges[] = ['NEW', 'bg-rose-600'];
if ('yes' === get_post_meta($id, 'rs_unique', true)) $badges[] = ['Único', 'bg-emerald-600'];
if ($product->is_featured())                      $badges[] = ['Exclusivo', 'bg-indigo-600'];

/* botão add-to-cart (com AJAX quando possível) */
$supports_ajax = $product->is_purchasable() && $in_stock && $product->supports('ajax_add_to_cart');
$btn_classes  = 'inline-flex items-center justify-center h-9 px-3 rounded-md font-semibold text-white bg-purple-700 hover:bg-purple-800 transition';
$btn_classes .= $supports_ajax ? ' add_to_cart_button ajax_add_to_cart' : '';
$add_url      = $product->add_to_cart_url();
$buy_now      = add_query_arg('add-to-cart', $id, wc_get_checkout_url());
?>
<article <?php wc_product_class('group rounded-2xl bg-white ring-1 ring-purple-400 p-3 ', $product); ?>>

   <!-- imagem + badges -->
   <a href="<?php echo esc_url($permalink); ?>" class="block relative">
      <?php echo $product->get_image('woocommerce_thumbnail', ['class' => 'h-full w-full object-cover']); ?>
      <!-- <div class="aspect-square w-full rounded-xl overflow-hidden bg-red-300">
       
      </div> -->

      <!-- badges superiores -->
      <?php if ($is_on_sale || !empty($badges)) : ?>
         <div class="absolute top-0  flex flex-wrap gap-1">
            <?php if ($is_on_sale && $regular && $sale && floatval($regular) > 0) :
               $off = round(((float)$regular - (float)$sale) / (float)$regular * 100);
            ?>
               <!-- <span class="px-2 py-0.5 rounded-full text-[11px] font-bold text-white bg-fuchsia-600 shadow">-<?php echo esc_html($off); ?>%</span> -->
               <span class="px-1 py-0.5 rounded-md text-[10px] font-bold text-white bg-red-600 shadow">Novo</span>
               <span class="px-1 py-0.5 rounded-md text-[10px] font-bold text-white bg-green-600 shadow">Limitado</span>
               <span class="px-1 py-0.5 rounded-md text-[10px] font-bold text-white bg-fuchsia-600 shadow">Exclusivo</span>
            <?php endif; ?>
            <?php foreach ($badges as [$label, $bg]) : ?>
               <span class="px-2 py-0.5 rounded-full text-[11px] font-bold text-white <?php echo esc_attr($bg); ?> shadow">
                  <?php echo esc_html($label); ?>
               </span>
            <?php endforeach; ?>
         </div>
      <?php endif; ?>
   </a>
   <!-- rating (opcional) -->
   <!-- <?php if ($reviews > 0) : ?>
      <div class="mt-1 flex items-center gap-1 text-[12px]">
         <?php echo wc_get_rating_html($rating, $reviews); ?>
         <span class="text-gray-600">(<?php echo esc_html($reviews); ?>)</span>
      </div>
   <?php endif; ?> -->
   <!-- título -->
   <a href="<?php echo esc_url($permalink); ?>" class="mt-3 block h-[40px] ">
      <h3 class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2">
         <?php echo esc_html($title); ?>
      </h3>
   </a>



   <!-- preços -->
   <div class="mt-2 h-[70px] ">
      <?php if ($is_on_sale && $regular) : ?>
         <div class="text-[12px] text-gray-500">
            De <s><?php echo wc_price($regular); ?></s>
         </div>
         <div class="text-xl font-extrabold text-purple-800">
            <?php echo wc_price($sale ? $sale : $regular); ?>
         </div>
      <?php else : ?>
         <div class="text-xl font-extrabold text-gray-900">
            <?php echo wp_kses_post($price_html); ?>
         </div>
      <?php endif; ?>
   </div>

   <!-- ações -->
   <div class="mt-3 flex flex-col gap-2">
      <a href="<?php echo esc_url($add_url); ?>"
         class="bg-purple-default inline-flex items-center justify-center h-9 text-[12px] rounded-md font-bold text-white"
         data-product_id="<?php echo esc_attr($id); ?>"
         data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
         data-quantity="1"
         rel="nofollow"
         aria-label="<?php echo esc_attr(sprintf(__('Adicionar “%s” ao carrinho', 'woocommerce'), $title)); ?>">
         Adicionar ao carrinho
      </a>

      <a href="<?php echo esc_url($buy_now); ?>"
         class="inline-flex items-center text-[12px] justify-center h-9  rounded-md font-bold text-white bg-emerald-500 hover:bg-emerald-700 transition">
         Comprar agora
      </a>
   </div>

   <!-- info de estoque (sutil) -->
   <?php if (!$in_stock) : ?>
      <p class="mt-2 text-xs font-medium text-rose-600">Esgotado</p>
   <?php endif; ?>
</article>