<?php

/**
 * Cart Totals (Custom fragment for AJAX refresh)
 * Path: yourtheme/woocommerce/cart/cart-totals.php
 */
defined('ABSPATH') || exit;

$cart = WC()->cart;
?>

<div class="rounded-xl ring-1 ring-purple-300 shadow-md bg-white p-6 cart_totals">
   <h2 class="text-xl font-bold text-purple-700 mb-4">Resumo</h2>

   <ul class="space-y-2 text-sm">
      <!-- Subtotal -->
      <li class="flex justify-between">
         <span class="text-gray-700"><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
         <span class="font-semibold">
            <?php wc_cart_totals_subtotal_html(); ?>
         </span>
      </li>

      <!-- Frete / mensagens de frete -->
      <?php if ($cart->needs_shipping()) : ?>
         <?php if ($cart->show_shipping()) : ?>
            <li class="flex justify-between">
               <span class="text-gray-700"><?php esc_html_e('Frete', 'woocommerce'); ?></span>
               <span class="font-semibold">
                  <?php
                  // Exibe o método escolhido e o valor (formatação padrão do Woo)
                  // Se quiser apenas o valor, use wc_cart_totals_shipping_html(); já imprime label+preço.
                  wc_cart_totals_shipping_html();
                  ?>
               </span>
            </li>
         <?php else : ?>
            <li class="text-sm text-gray-500 pt-2">
               <?php esc_html_e('Calcule o frete acima pelo CEP para ver as opções.', 'woocommerce'); ?>
            </li>
         <?php endif; ?>
      <?php endif; ?>

      <!-- Cupons (se habilitado) -->
      <?php if (wc_coupons_enabled()) : ?>
         <?php foreach ($cart->get_coupons() as $code => $coupon) : ?>
            <li class="flex justify-between">
               <span class="text-gray-700"><?php wc_cart_totals_coupon_label($coupon); ?></span>
               <span class="font-semibold"><?php wc_cart_totals_coupon_html($coupon); ?></span>
            </li>
         <?php endforeach; ?>
      <?php endif; ?>

      <!-- Fees -->
      <?php foreach ($cart->get_fees() as $fee) : ?>
         <li class="flex justify-between">
            <span class="text-gray-700"><?php echo esc_html($fee->name); ?></span>
            <span class="font-semibold"><?php wc_cart_totals_fee_html($fee); ?></span>
         </li>
      <?php endforeach; ?>

      <!-- Impostos (se não incluídos nos preços) -->
      <?php if (wc_tax_enabled() && ! wc_prices_include_tax()) : ?>
         <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
            <?php foreach ($cart->get_tax_totals() as $code => $tax) : ?>
               <li class="flex justify-between">
                  <span class="text-gray-700"><?php echo esc_html($tax->label); ?></span>
                  <span class="font-semibold"><?php echo wp_kses_post($tax->formatted_amount); ?></span>
               </li>
            <?php endforeach; ?>
         <?php else : ?>
            <li class="flex justify-between">
               <span class="text-gray-700"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></span>
               <span class="font-semibold"><?php wc_cart_totals_taxes_total_html(); ?></span>
            </li>
         <?php endif; ?>
      <?php endif; ?>

      <!-- Total -->
      <li class="flex justify-between text-base pt-2 border-t border-purple-200">
         <span class="text-gray-900 font-bold"><?php esc_html_e('Total', 'woocommerce'); ?></span>
         <span class="text-2xl font-bold text-black order-total">
            <?php wc_cart_totals_order_total_html(); ?>
         </span>
      </li>
   </ul>

   <p class="cart-proceed mt-6">
      <a class="button checkout wc-forward block w-full text-center px-4 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600 transition"
         href="<?php echo esc_url(wc_get_checkout_url()); ?>">
         <?php esc_html_e('Finalizar compra', 'woocommerce'); ?>
      </a>
   </p>
</div>