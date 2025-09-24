<?php
defined('ABSPATH') || exit;

$customer_id = get_current_user_id();
$addresses   = apply_filters(
   'woocommerce_my_account_get_addresses',
   array(
      'billing'  => __('Endereço de cobrança', 'woocommerce'),
      'shipping' => __('Endereço de entrega', 'woocommerce'),
   ),
   $customer_id
);
?>

<h2 class="text-xl font-semibold text-gray-900 mb-6">Meus endereços</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
   <?php foreach ($addresses as $name => $title) : ?>
      <div class="rounded-2xl ring-1 ring-gray-200 bg-white shadow-sm p-6">
         <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold text-gray-900"><?php echo esc_html($title); ?></h3>
            <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', $name)); ?>"
               class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium ring-1 ring-gray-300 hover:bg-gray-50">
               Editar
            </a>
         </div>
         <div class="text-sm text-gray-700">
            <?php
            $address = wc_get_account_formatted_address($name);
            echo $address ? wp_kses_post($address) : '<span class="text-gray-500">Você ainda não definiu este endereço.</span>';
            ?>
         </div>
      </div>
   <?php endforeach; ?>
</div>