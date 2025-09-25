<?php
defined('ABSPATH') || exit;

if (! is_ajax()) {
   do_action('woocommerce_review_order_before_payment');
}
?>
<div id="payment" class="woocommerce-checkout-payment mt-6">
   <?php if (WC()->cart->needs_payment()) : ?>
      <ul class="wc_payment_methods payment_methods methods space-y-3">
         <?php
         if (! empty($available_gateways)) {
            foreach ($available_gateways as $gateway) {
               wc_get_template('checkout/payment-method.php', ['gateway' => $gateway]);
            }
         } else {
            echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info rounded-xl ring-1 ring-purple-200 bg-purple-50 p-4">';
            echo wpautop(wptexturize(apply_filters('woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__('No payment methods available for your state. Please contact us.', 'woocommerce') : esc_html__('Please fill out your details above to see available payment methods.', 'woocommerce'))));
            echo '</li>';
         }
         ?>
      </ul>
   <?php endif; ?>

   <div class="form-row place-order mt-6">
      <?php wc_get_template('checkout/terms.php'); ?>

      <?php do_action('woocommerce_review_order_before_submit'); ?>

      <?php echo apply_filters(
         'woocommerce_order_button_html',
         '<button type="submit" class="button alt w-full h-12 rounded-xl bg-purple-700 text-white font-semibold hover:bg-purple-800 transition"
        name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr(__('Place order', 'woocommerce')) . '" data-value="' . esc_attr(__('Place order', 'woocommerce')) . '">' .
            esc_html__('Finalizar pedido', 'woocommerce') .
            '</button>'
      ); ?>

      <?php do_action('woocommerce_review_order_after_submit'); ?>

      <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
   </div>
</div>
<?php
if (! is_ajax()) {
   do_action('woocommerce_review_order_after_payment');
}
