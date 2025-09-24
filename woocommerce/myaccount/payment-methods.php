<?php
defined('ABSPATH') || exit;

$saved_methods = wc_get_customer_saved_methods_list(get_current_user_id());
$has_methods   = (bool) $saved_methods;
?>
<h2 class="text-xl font-semibold text-gray-900 mb-4">Métodos de pagamento</h2>

<?php if ($has_methods) : ?>
   <div class="rounded-2xl ring-1 ring-gray-200 bg-white shadow-sm">
      <ul class="divide-y divide-gray-100">
         <?php foreach ($saved_methods as $method) : ?>
            <li class="px-4 py-3 flex items-center justify-between text-sm">
               <span class="text-gray-800">
                  <?php echo wp_kses_post($method['method']['brand'] . ' •••• ' . $method['method']['last4']); ?>
                  <?php if (! empty($method['expires'])) : ?>
                     <span class="text-gray-500"> (expira <?php echo esc_html($method['expires']); ?>)</span>
                  <?php endif; ?>
               </span>
               <span>
                  <?php echo wc_get_account_saved_payment_methods_list_item_actions($method); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                  ?>
               </span>
            </li>
         <?php endforeach; ?>
      </ul>
   </div>
<?php else : ?>
   <div class="rounded-xl bg-purple-50 text-purple-800 px-4 py-3">
      Você ainda não salvou nenhum método de pagamento.
   </div>
<?php endif; ?>

<div class="mt-4">
   <a class="inline-flex items-center rounded-xl bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700"
      href="<?php echo esc_url(wc_get_endpoint_url('add-payment-method')); ?>">
      Adicionar método
   </a>
</div>