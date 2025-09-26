<?php
defined('ABSPATH') || exit;
$cart = WC()->cart;
?>
<div class="rounded-xl ring-1 ring-purple-300 shadow-md bg-white p-6 cart_totals">
   <h2 class="text-xl font-bold text-purple-700 mb-4">Resumo do pedido</h2>

   <ul class="divide-y divide-purple-100">
      <?php foreach ($cart->get_cart() as $cart_item_key => $cart_item) :
         $_product = $cart_item['data'];
         if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0) continue;

         $product_name = $_product->get_name();
         $thumbnail    = $_product->get_image('woocommerce_thumbnail', ['class' => 'w-12 h-12 object-cover rounded-lg']);
      ?>
         <li class="py-3 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
               <?php echo $thumbnail; ?>
               <div class="text-sm">
                  <div class="font-semibold text-gray-900"><?php echo esc_html($product_name); ?></div>
                  <div class="text-gray-500"><?php echo wc_get_formatted_cart_item_data($cart_item); ?></div>
                  <div class="text-gray-500">× <?php echo (int) $cart_item['quantity']; ?></div>
               </div>
            </div>
            <div class="text-sm font-semibold text-purple-700">
               <?php echo $cart->get_product_subtotal($_product, $cart_item['quantity']); ?>
            </div>
         </li>
      <?php endforeach; ?>
   </ul>

   <div class="mt-4 space-y-2 text-sm">
      <div class="flex justify-between">
         <span class="text-gray-700">Subtotal</span>
         <span class="font-semibold"><?php wc_cart_totals_subtotal_html(); ?></span>
      </div>

      <?php foreach ($cart->get_coupons() as $code => $coupon) : ?>
         <div class="flex justify-between">
            <span class="text-gray-700"><?php wc_cart_totals_coupon_label($coupon); ?></span>
            <span class="font-semibold"><?php wc_cart_totals_coupon_html($coupon); ?></span>
         </div>
      <?php endforeach; ?>

      <?php
      // ===== FRETE COMO TEXTO (sem rádios) =====
      if ($cart->needs_shipping()) :
         if ($cart->show_shipping()) :
            $packages  = WC()->shipping()->get_packages();
            $pkg       = $packages[0] ?? null;
            $rates     = $pkg['rates'] ?? [];
            $chosen    = (array) WC()->session->get('chosen_shipping_methods', []);
            $chosen_id = $chosen[0] ?? '';
            $rate      = $rates[$chosen_id] ?? null;
      ?>
            <div class="flex justify-between">
               <span class="text-gray-700"><?php esc_html_e('Entrega', 'woocommerce'); ?></span>
               <span class="font-semibold">
                  <?php
                  if ($rate instanceof WC_Shipping_Rate) {
                     $label = $rate->get_label();
                     $cost  = (float) $rate->get_cost();
                     $taxes = array_sum((array) $rate->get_taxes());
                     echo esc_html($label) . ': ' . wc_price($cost + $taxes);
                  } else {
                     esc_html_e('Selecione o frete no carrinho.', 'woocommerce');
                  }
                  ?>
               </span>
            </div>
         <?php else : ?>
            <div class="text-gray-500">
               <?php esc_html_e('Calcule e escolha o frete no carrinho.', 'woocommerce'); ?>
            </div>
      <?php
         endif;
      endif;
      ?>

      <?php foreach ($cart->get_fees() as $fee) : ?>
         <div class="flex justify-between">
            <span class="text-gray-700"><?php echo esc_html($fee->name); ?></span>
            <span class="font-semibold"><?php wc_cart_totals_fee_html($fee); ?></span>
         </div>
      <?php endforeach; ?>

      <?php if (wc_tax_enabled() && ! wc_prices_include_tax()) : ?>
         <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
            <?php foreach ($cart->get_tax_totals() as $code => $tax) : ?>
               <div class="flex justify-between">
                  <span class="text-gray-700"><?php echo esc_html($tax->label); ?></span>
                  <span class="font-semibold"><?php echo wp_kses_post($tax->formatted_amount); ?></span>
               </div>
            <?php endforeach; ?>
         <?php else : ?>
            <div class="flex justify-between">
               <span class="text-gray-700"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></span>
               <span class="font-semibold"><?php wc_cart_totals_taxes_total_html(); ?></span>
            </div>
         <?php endif; ?>
      <?php endif; ?>

      <div class="flex justify-between text-base pt-2 border-t border-purple-200">
         <span class="text-gray-900 font-bold">Total</span>
         <span class="text-2xl font-bold text-black">
            <?php wc_cart_totals_order_total_html(); ?>
         </span>
      </div>
   </div>
</div>