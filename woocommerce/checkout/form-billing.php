<?php
defined('ABSPATH') || exit;

$checkout = WC()->checkout();
?>
<div class="rounded-3xl bg-white ring-1 ring-purple-200/70 shadow-sm p-6">
   <h3 class="text-lg font-semibold text-gray-900 mb-4"><?php esc_html_e('Billing details', 'woocommerce'); ?></h3>

   <?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

   <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?php
      $fields = $checkout->get_checkout_fields('billing');
      foreach ($fields as $key => $field) {
         // adiciona classes Tailwind nos inputs/labels
         $field['label_class'][] = 'rs-label';
         $field['input_class'][] = 'rs-input';
         woocommerce_form_field($key, $field, $checkout->get_value($key));
      }
      ?>
   </div>

   <?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>

   <?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>
      <div class="mt-6">
         <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php esc_html_e('Order notes', 'woocommerce'); ?></h3>
         <div>
            <?php
            $fields = $checkout->get_checkout_fields('order');
            foreach ($fields as $key => $field) {
               $field['label_class'][] = 'rs-label';
               $field['input_class'][] = 'rs-input';
               woocommerce_form_field($key, $field, $checkout->get_value($key));
            }
            ?>
         </div>
      </div>
   <?php endif; ?>
</div>