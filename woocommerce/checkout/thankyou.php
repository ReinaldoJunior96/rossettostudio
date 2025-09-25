<?php
defined('ABSPATH') || exit;

if ($order) : ?>
   <?php if ($order->has_status('failed')) : ?>
      <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-10">
         <div class="rounded-xl ring-1 ring-red-300 bg-white p-6 shadow">
            <h1 class="text-2xl font-bold text-red-600 mb-3"><?php esc_html_e('Order failed', 'woocommerce'); ?></h1>
            <p class="text-gray-700 mb-6"><?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'woocommerce'); ?></p>

            <div class="flex flex-wrap gap-3">
               <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="px-4 h-11 inline-flex items-center rounded-xl bg-purple-700 text-white font-semibold hover:bg-purple-800 transition">
                  <?php esc_html_e('Pay', 'woocommerce'); ?>
               </a>
               <?php if (is_user_logged_in()) : ?>
                  <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="px-4 h-11 inline-flex items-center rounded-xl ring-1 ring-gray-200 bg-white text-gray-900 hover:bg-gray-50 transition">
                     <?php esc_html_e('My account', 'woocommerce'); ?>
                  </a>
               <?php endif; ?>
            </div>
         </div>
      </div>
   <?php else : ?>
      <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-10">
         <div class="rounded-xl ring-1 ring-green-300 bg-white p-6 shadow">
            <h1 class="text-2xl font-extrabold text-green-700 mb-2"><?php esc_html_e('Thank you. Your order has been received.', 'woocommerce'); ?></h1>

            <ul class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
               <li class="rounded-lg bg-gray-50 p-3">
                  <span class="text-gray-500 block"><?php esc_html_e('Order number:', 'woocommerce'); ?></span>
                  <span class="font-semibold"><?php echo wp_kses_post($order->get_order_number()); ?></span>
               </li>
               <li class="rounded-lg bg-gray-50 p-3">
                  <span class="text-gray-500 block"><?php esc_html_e('Date:', 'woocommerce'); ?></span>
                  <span class="font-semibold"><?php echo wc_format_datetime($order->get_date_created()); ?></span>
               </li>
               <li class="rounded-lg bg-gray-50 p-3">
                  <span class="text-gray-500 block"><?php esc_html_e('Total:', 'woocommerce'); ?></span>
                  <span class="font-semibold"><?php echo wp_kses_post($order->get_formatted_order_total()); ?></span>
               </li>
               <?php if ($order->get_payment_method_title()) : ?>
                  <li class="rounded-lg bg-gray-50 p-3">
                     <span class="text-gray-500 block"><?php esc_html_e('Payment method:', 'woocommerce'); ?></span>
                     <span class="font-semibold"><?php echo wp_kses_post($order->get_payment_method_title()); ?></span>
                  </li>
               <?php endif; ?>
            </ul>

            <div class="mt-6">
               <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
               <?php do_action('woocommerce_thankyou', $order->get_id()); ?>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
               <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="px-4 h-11 inline-flex items-center rounded-xl bg-purple-700 text-white font-semibold hover:bg-purple-800 transition">
                  <?php esc_html_e('Continue shopping', 'woocommerce'); ?>
               </a>
               <?php if (is_user_logged_in()) : ?>
                  <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="px-4 h-11 inline-flex items-center rounded-xl ring-1 ring-gray-200 bg-white text-gray-900 hover:bg-gray-50 transition">
                     <?php esc_html_e('My account', 'woocommerce'); ?>
                  </a>
               <?php endif; ?>
            </div>
         </div>
      </div>
   <?php endif; ?>
<?php else : ?>
   <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-10">
      <div class="rounded-xl ring-1 ring-purple-300 bg-white p-6 shadow">
         <h1 class="text-2xl font-bold text-purple-700 mb-2"><?php esc_html_e('Thank you. Your order has been received.', 'woocommerce'); ?></h1>
         <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-flex items-center px-4 h-11 rounded-xl bg-purple-700 text-white font-semibold hover:bg-purple-800 transition">
            <?php esc_html_e('Go to shop', 'woocommerce'); ?>
         </a>
      </div>
   </div>
<?php endif; ?>