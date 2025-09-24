<?php
defined('ABSPATH') || exit;

/** @var string $load_address */
/** @var array  $address */

$page_title = ($load_address === 'billing')
   ? __('Editar endereço de cobrança', 'woocommerce')
   : __('Editar endereço de entrega', 'woocommerce');

do_action('woocommerce_before_edit_account_address_form');
?>

<div class="relative">
   <!-- glow suave -->
   <div class="pointer-events-none absolute -inset-2 rounded-3xl bg-gradient-to-br from-purple-500/15 via-fuchsia-400/10 to-cyan-400/10 blur-xl"></div>

   <div class="relative rounded-3xl bg-white/80 backdrop-blur-xl ring-1 ring-black/5 shadow-[0_20px_60px_-20px_rgba(17,24,39,0.25)] p-6 md:p-8">
      <h2 class="text-2xl font-extrabold text-gray-900 leading-tight mb-6">
         <?php echo esc_html($page_title); ?>
      </h2>

      <form method="post" class="rs-form">
         <div class="form-grid">
            <?php
            // Placeholders simpáticos (ajuste se quiser)
            $ph = [
               'first_name'       => 'Nome',
               'last_name'        => 'Sobrenome',
               'company'          => 'Empresa (opcional)',
               'country'          => 'País',
               'postcode'         => 'CEP',
               'address_1'        => 'Nome da rua e número da casa',
               'address_2'        => 'Apartamento, suíte, sala, etc. (opcional)',
               'city'             => 'Cidade',
               'state'            => 'Estado',
               'phone'            => 'Telefone',
               'email'            => 'E-mail',
            ];

            foreach ($address as $key => $field) {
               // classes padronizadas (iguais às do login/cadastro)
               $field['class'][]        = 'form-row-wide'; // deixa o grid decidir, Woo usa first/last/wide
               $field['label_class'][]  = 'rs-label';
               $field['input_class'][]  = 'rs-input';

               // select com a mesma cara do input
               if (isset($field['type']) && $field['type'] === 'select') {
                  $field['input_class'][] = 'rs-select';
               }

               // placeholder amigável
               $base = str_replace(['billing_', 'shipping_'], '', $key);
               if (empty($field['placeholder']) && isset($ph[$base])) {
                  $field['placeholder'] = $ph[$base];
               }

               // render
               woocommerce_form_field(
                  $key,
                  $field,
                  wc_get_post_data_by_key($key, get_user_meta(get_current_user_id(), $key, true))
               );
            }
            ?>
         </div>

         <div class="mt-6">
            <?php wp_nonce_field('woocommerce-edit_address', 'woocommerce-edit-address-nonce'); ?>
            <input type="hidden" name="action" value="edit_address" />
            <button type="submit"
               class="rounded-2xl bg-purple-700 px-6 py-3 text-white font-semibold tracking-tight
                 shadow-[0_10px_30px_-10px_rgba(109,40,217,.7)] hover:bg-purple-800 transition">
               <?php esc_html_e('Salvar endereço', 'woocommerce'); ?>
            </button>
         </div>
      </form>
   </div>
</div>

<?php do_action('woocommerce_after_edit_account_address_form'); ?>