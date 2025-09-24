<?php

/**
 * Tailwind Cart (Custom Layout)
 * Path: yourtheme/woocommerce/cart/cart.php
 * Requer a página do carrinho usando [woocommerce_cart] (não Blocks).
 */
defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
   <h1 class="text-3xl font-bold text-purple-700 mb-6">Carrinho (<?php echo WC()->cart->get_cart_contents_count(); ?>)</h1>

   <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      <!-- Main -->
      <div class="lg:col-span-8">
         <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
            <div class="overflow-hidden rounded-xl ring-1 ring-purple-300 shadow-md bg-white">
               <table class="w-full text-left">
                  <thead class="bg-purple-50 text-xs uppercase tracking-wider text-purple-700">
                     <tr>
                        <th class="p-4">Item</th>
                        <th class="p-4">Quantidade</th>
                        <th class="p-4">Subtotal</th>
                        <th class="p-4"></th>
                     </tr>
                  </thead>

                  <tbody class="divide-y divide-purple-200">
                     <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                        $_product   = $cart_item['data'];
                        $product_id = $cart_item['product_id'];

                        if ($_product && $_product->exists() && $cart_item['quantity'] > 0) :
                           $product_permalink = $_product->is_visible() ? $_product->get_permalink($cart_item) : '';
                           $product_name      = $_product->get_name();
                           $thumbnail         = $_product->get_image('woocommerce_thumbnail', ['class' => 'w-16 h-16 object-cover rounded-lg']);
                     ?>
                           <tr>
                              <!-- Item -->
                              <td class="p-4">
                                 <div class="flex items-center gap-4">
                                    <?php echo $thumbnail; ?>
                                    <div>
                                       <div class="text-purple-700 font-bold">
                                          <?php echo $product_name; ?>
                                       </div>
                                       <div class="text-sm text-gray-500">
                                          <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                                       </div>
                                    </div>
                                 </div>
                              </td>

                              <!-- Quantidade -->
                              <td class="p-4">
                                 <?php
                                 echo woocommerce_quantity_input([
                                    'input_name'   => "cart[{$cart_item_key}][qty]",
                                    'input_value'  => $cart_item['quantity'],
                                    'classes'      => ['w-20 rounded-lg border-purple-300'],
                                 ], $_product, false);
                                 ?>
                              </td>

                              <!-- Subtotal -->
                              <td class="p-4">
                                 <span class="text-purple-700 font-bold">
                                    <?php echo WC()->cart->get_product_subtotal($_product, $cart_item['quantity']); ?>
                                 </span>
                              </td>

                              <!-- Remove -->
                              <td class="p-4">
                                 <a href="<?php echo wc_get_cart_remove_url($cart_item_key); ?>" class="text-purple-700 hover:text-red-600 transition">
                                    <i class="fa-solid fa-trash"></i>
                                 </a>
                              </td>
                           </tr>
                     <?php endif;
                     endforeach; ?>
                  </tbody>
               </table>
            </div>
         </form>

         <!-- Calcular Frete -->
         <div class="mt-10">
            <div class="rounded-xl ring-1 ring-purple-300 shadow-md bg-white p-6">
               <h2 class="text-xl font-bold text-purple-700 mb-4">Calcular frete</h2>

               <form id="shipping-form"
                  class="grid grid-cols-1 md:grid-cols-[1fr_auto_auto] gap-3"
                  method="post"
                  action="<?php echo esc_url(wc_get_cart_url()); ?>">
                  <input id="calculadora-frete" name="cep" type="text" inputmode="numeric"
                     placeholder="Digite seu CEP"
                     class="h-11 w-full rounded-xl border-2 border-purple-200 px-4
                    focus:border-purple-500 focus:ring-0 placeholder:text-gray-400" />

                  <button id="btn-calcular-frete" type="submit"
                     class="h-11 px-5 rounded-xl bg-purple-700 text-white font-semibold
                     hover:bg-purple-800 transition">
                     CALCULAR
                  </button>

                  <!-- <button id="limpar-frete" type="button"
                     class="h-11 px-5 rounded-xl bg-gray-100 text-purple-700 font-semibold
                     hover:bg-gray-200 transition">
                     Limpar frete
                  </button> -->
               </form>

               <!-- Opções de envio -->
               <div id="shipping-options"
                  role="radiogroup"
                  class="mt-5 space-y-3"></div>
            </div>
         </div>
      </div>

      <!-- Sidebar -->
      <aside class="lg:col-span-4">
         <div class="rounded-xl ring-1 ring-purple-300 shadow-md bg-white p-6 cart_totals">
            <h2 class="text-xl font-bold text-purple-700 mb-4">Resumo</h2>
            <ul class="space-y-2 text-sm">
               <li class="flex justify-between">
                  <span class="text-gray-700">Subtotal</span>
                  <span class="font-semibold">
                     <?php wc_cart_totals_subtotal_html(); ?>
                  </span>
               </li>

               <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                  <li class="flex justify-between">
                     <span class="text-gray-700"><?php echo esc_html($fee->name); ?></span>
                     <span class="font-semibold">
                        <?php wc_cart_totals_fee_html($fee); ?>
                     </span>
                  </li>
               <?php endforeach; ?>

               <?php if (wc_coupons_enabled()) : ?>
                  <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                     <li class="flex justify-between">
                        <span class="text-gray-700"><?php wc_cart_totals_coupon_label($coupon); ?></span>
                        <span class="font-semibold"><?php wc_cart_totals_coupon_html($coupon); ?></span>
                     </li>
                  <?php endforeach; ?>
               <?php endif; ?>

               <li class="flex justify-between text-base pt-2 border-t border-purple-200">
                  <span class="text-gray-900 font-bold">Total</span>
                  <span class="text-2xl font-bold text-black order-total">
                     <?php wc_cart_totals_order_total_html(); ?>
                  </span>
               </li>
            </ul>

            <p class="cart-proceed">
               <a class="button checkout wc-forward" href="<?php echo esc_url(wc_get_checkout_url()); ?>">
                  Finalizar compra
               </a>
            </p>
         </div>
      </aside>
   </div>
</div>

<?php do_action('woocommerce_after_cart'); ?>