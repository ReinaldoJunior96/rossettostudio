<?php

/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

if (! defined('ABSPATH')) {
   exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in()) {
   echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
   return;
}

?>
<section class=" flex justify-center pt-10">
   <form name="checkout" method="post" class="checkout woocommerce-checkout margin-site" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__('Checkout', 'woocommerce'); ?>">

      <?php if ($checkout->get_checkout_fields()) : ?>

         <?php do_action('woocommerce_checkout_before_customer_details'); ?>

         <div class="grid grid-cols-12 gap-8  " id="customer_details">
            <div class="col-span-6">
               <?php do_action('woocommerce_checkout_billing'); ?>
            </div>

            <div id="order_review" class="woocommerce-checkout-review-order col-span-6">
               <?php do_action('woocommerce_checkout_order_review'); ?>
            </div>
         </div>

         <!-- <?php do_action('woocommerce_checkout_shipping'); ?> -->
         <?php do_action('woocommerce_checkout_after_customer_details'); ?>

      <?php endif; ?>

      <!-- <?php do_action('woocommerce_checkout_before_order_review_heading'); ?> -->



      <!-- <?php do_action('woocommerce_checkout_before_order_review'); ?> -->



      <?php do_action('woocommerce_checkout_after_order_review'); ?>

   </form>
</section>


<?php do_action('woocommerce_after_checkout_form', $checkout); ?>


<script>
   (function() {
      try {
         const cep = sessionStorage.getItem('woo_ship_cep') || '';
         const inp = document.querySelector('#billing_postcode');
         if (inp && cep && !inp.value) {
            inp.value = cep;
         }
         if (inp) {
            inp.setAttribute('readonly', 'readonly');
         }
      } catch (e) {}
   })();
   document.addEventListener('DOMContentLoaded', () => {
      /* === 1) Garante visual consistente em TODOS inputs === */
      document.querySelectorAll(
         '.woocommerce-checkout .form-row input, .woocommerce-checkout .form-row select, .woocommerce-checkout .form-row textarea'
      ).forEach(el => el.classList.add('rs-input'));

      /* === 2) País => vira “pill” de leitura (altura idêntica aos inputs) === */
      const wrap = document.querySelector('#billing_country_field');
      if (wrap) {
         wrap.classList.add('rs-country-compact'); // garante a classe no wrapper
         const sel = wrap.querySelector('select');
         if (sel) {
            const w = sel.closest('.woocommerce-input-wrapper') || wrap;
            if (!w.querySelector('.rs-country-pill')) {
               const pill = document.createElement('div');
               pill.className = 'rs-country-pill';
               const label = sel.options[sel.selectedIndex]?.text || 'Brasil';
               pill.textContent = label;
               w.appendChild(pill);
               sel.style.display = 'none'; // escondemos o select (apenas leitura)
            }
         }
      }
   });
</script>

<script>
   (function() {
      function d(s) {
         return String(s || '').replace(/\D+/g, '');
      }

      function cpf(v) {
         v = d(v).slice(0, 11);
         return v.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
      }

      function cnpj(v) {
         v = d(v).slice(0, 14);
         return v.replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3').replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d{1,2})$/, '$1-$2');
      }

      function type() {
         const s = document.querySelector('#billing_persontype');
         return s ? parseInt(s.value || '1', 10) : 1;
      }

      function mask() {
         const i = document.querySelector('#billing_document');
         if (!i) return;
         const t = type();
         i.value = t === 2 ? cnpj(i.value) : cpf(i.value);
         i.placeholder = t === 2 ? '00.000.000/0000-00' : '000.000.000-00';
      }
      document.addEventListener('input', e => {
         if (e.target?.id === 'billing_document') mask();
      });
      document.querySelector('#billing_persontype')?.addEventListener('change', mask);
      mask();
   })();
</script>