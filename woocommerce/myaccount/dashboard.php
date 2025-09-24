<?php
defined('ABSPATH') || exit;

$current_user = wp_get_current_user();
?>
<h1 class="text-2xl font-bold text-gray-900 mb-3">Olá, <?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?> 👋</h1>
<p class="text-gray-600 mb-6">
   A partir do painel, veja seus pedidos recentes, gerencie seus endereços
   e detalhes de conta.
</p>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
   <a href="<?php echo esc_url(wc_get_endpoint_url('orders')); ?>"
      class="rounded-xl p-4 ring-1 ring-gray-200 bg-white hover:bg-gray-50 shadow-sm">
      <div class="text-lg font-semibold text-gray-900">Pedidos</div>
      <div class="text-sm text-gray-600">Histórico e status</div>
   </a>

   <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address')); ?>"
      class="rounded-xl p-4 ring-1 ring-gray-200 bg-white hover:bg-gray-50 shadow-sm">
      <div class="text-lg font-semibold text-gray-900">Endereços</div>
      <div class="text-sm text-gray-600">Entrega e cobrança</div>
   </a>

   <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account')); ?>"
      class="rounded-xl p-4 ring-1 ring-gray-200 bg-white hover:bg-gray-50 shadow-sm">
      <div class="text-lg font-semibold text-gray-900">Detalhes da conta</div>
      <div class="text-sm text-gray-600">Nome, e-mail e senha</div>
   </a>
</div>

<div class="mt-6">
   <a href="<?php echo esc_url(wc_logout_url()); ?>"
      class="inline-flex items-center rounded-xl bg-gray-900 text-white px-4 py-2 text-sm hover:bg-black">
      Sair
   </a>
</div>