<?php
defined('ABSPATH') || exit;

$current_user = wp_get_current_user();
?>
<h2 class="text-xl font-semibold text-gray-900 mb-4">Detalhes da conta</h2>

<form class="grid grid-cols-1 md:grid-cols-2 gap-4" action="" method="post">
   <?php do_action('woocommerce_edit_account_form_start'); ?>

   <p class="col-span-1">
      <label for="account_first_name" class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
      <input type="text" name="account_first_name" id="account_first_name" autocomplete="given-name"
         class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 focus:border-purple-500 focus:ring-0"
         value="<?php echo esc_attr($current_user->first_name); ?>" required />
   </p>

   <p class="col-span-1">
      <label for="account_last_name" class="block text-sm font-medium text-gray-700 mb-1">Sobrenome *</label>
      <input type="text" name="account_last_name" id="account_last_name" autocomplete="family-name"
         class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 focus:border-purple-500 focus:ring-0"
         value="<?php echo esc_attr($current_user->last_name); ?>" required />
   </p>

   <p class="col-span-1">
      <label for="account_display_name" class="block text-sm font-medium text-gray-700 mb-1">Nome de exibição *</label>
      <input type="text" name="account_display_name" id="account_display_name"
         class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 focus:border-purple-500 focus:ring-0"
         value="<?php echo esc_attr($current_user->display_name); ?>" required />
      <span class="text-xs text-gray-500">Assim seu nome aparece nas avaliações, etc.</span>
   </p>

   <p class="col-span-1">
      <label for="account_email" class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
      <input type="email" name="account_email" id="account_email" autocomplete="email"
         class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 focus:border-purple-500 focus:ring-0"
         value="<?php echo esc_attr($current_user->user_email); ?>" required />
   </p>

   <div class="col-span-full border-t border-gray-200 my-2"></div>

   <p class="col-span-1">
      <label for="password_current" class="block text-sm font-medium text-gray-700 mb-1">Senha atual (deixe em branco para não alterar)</label>
      <input type="password" name="password_current" id="password_current" autocomplete="off"
         class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 focus:border-purple-500 focus:ring-0" />
   </p>

   <p class="col-span-1">
      <label for="password_1" class="block text-sm font-medium text-gray-700 mb-1">Nova senha</label>
      <input type="password" name="password_1" id="password_1" autocomplete="off"
         class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 focus:border-purple-500 focus:ring-0" />
   </p>

   <p class="col-span-1">
      <label for="password_2" class="block text-sm font-medium text-gray-700 mb-1">Confirmar nova senha</label>
      <input type="password" name="password_2" id="password_2" autocomplete="off"
         class="w-full rounded-xl border-2 border-gray-200 px-3 py-2 focus:border-purple-500 focus:ring-0" />
   </p>

   <?php do_action('woocommerce_edit_account_form'); ?>

   <p class="col-span-full mt-2">
      <?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
      <button type="submit" class="rounded-xl bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700" name="save_account_details" value="<?php esc_attr_e('Salvar alterações', 'woocommerce'); ?>">
         Salvar alterações
      </button>
      <input type="hidden" name="action" value="save_account_details" />
   </p>

   <?php do_action('woocommerce_edit_account_form_end'); ?>
</form>