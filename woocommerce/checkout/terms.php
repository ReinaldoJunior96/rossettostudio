<?php
defined('ABSPATH') || exit;

if (wc_get_page_id('terms') > 0 && apply_filters('woocommerce_checkout_show_terms', true)) : ?>
   <p class="form-row validate-required mb-4">
      <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox flex items-start gap-3 text-sm text-gray-700">
         <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox h-4 w-4 rounded border-gray-300 text-purple-700 focus:ring-purple-600"
            name="terms" <?php checked(apply_filters('woocommerce_terms_is_checked_default', isset($_POST['terms']))); ?> id="terms" />
         <span class="woocommerce-terms-and-conditions-checkbox-text">
            <?php
            printf(
               wp_kses_post(__('I have read and agree to the website %s', 'woocommerce')),
               '<a href="' . esc_url(get_permalink(wc_get_page_id('terms'))) . '" class="text-purple-700 underline" target="_blank" rel="noopener">' . esc_html__('terms and conditions', 'woocommerce') . '</a>'
            );
            ?>
         </span>
         <span class="required text-red-500">*</span>
      </label>
      <input type="hidden" name="terms-field" value="1" />
   </p>
<?php endif; ?>