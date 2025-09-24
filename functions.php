<?php
// Endpoint AJAX para limpar o frete
function landing_tailwind_clear_shipping_cost()
{
   if (! function_exists('WC') || ! WC()->session) {
      wp_send_json_error(['message' => 'Sessão WC indisponível'], 500);
   }

   // remove a flag da sessão
   WC()->session->__unset('custom_shipping_cost');

   // remove fees atualmente aplicadas e recalcula
   if (WC()->cart) {
      if (method_exists(WC()->cart, 'fees_api') && method_exists(WC()->cart->fees_api(), 'remove_all_fees')) {
         WC()->cart->fees_api()->remove_all_fees();
      } else {
         // fallback (versões antigas)
         WC()->cart->fees = [];
      }
      WC()->cart->calculate_totals();
   }

   wp_send_json_success(['cleared' => true]);
}
add_action('wp_ajax_clear_shipping_cost', 'landing_tailwind_clear_shipping_cost');
add_action('wp_ajax_nopriv_clear_shipping_cost', 'landing_tailwind_clear_shipping_cost');
// ====== Suporte básico do tema ======
add_action('after_setup_theme', function () {
   add_theme_support('woocommerce');
   add_theme_support('title-tag');
   add_theme_support('post-thumbnails');
   add_theme_support('wc-product-gallery-zoom');
   add_theme_support('wc-product-gallery-lightbox');
   add_theme_support('wc-product-gallery-slider');
});

// ====== Enqueue de assets (CSS + JS da busca) ======
add_action('wp_enqueue_scripts', function () {
   if (class_exists('WooCommerce')) {
      wp_enqueue_script('wc-cart-fragments');
   }
   $dir = get_stylesheet_directory();
   $uri = get_stylesheet_directory_uri();

   // Tailwind compilado
   $css_path = $dir . '/assets/build/app.css';
   if (file_exists($css_path)) {
      wp_enqueue_style(
         'tailwind',
         $uri . '/assets/build/app.css',
         [],
         filemtime($css_path) // bust cache
      );
   }

   // Enfileirar o tema de cores
   $theme_css = $dir . '/assets/css/theme.css';
   if (file_exists($theme_css)) {
      wp_enqueue_style(
         'theme-colors',
         $uri . '/assets/css/theme.css',
         [],
         filemtime($theme_css) // bust cache
      );
   }

   // Enfileirar a CDN do FontAwesome
   wp_enqueue_style(
      'font-awesome',
      'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css',
      [],
      null // Sem versão para evitar cache
   );

   // JS da busca (carrega globalmente)
   $search_js = $dir . '/assets/js/search.js';
   if (file_exists($search_js)) {
      wp_enqueue_script(
         'product-search',
         $uri . '/assets/js/search.js',
         [],
         filemtime($search_js),
         true // footer
      );
      wp_localize_script('product-search', 'ProductSearch', [
         'ajax_url' => admin_url('admin-ajax.php'),
         'nonce'    => wp_create_nonce('search_products_nonce'),
      ]);
   }

   // Enfileirar o script de cálculo de frete
   $shipping_js = $dir . '/assets/js/shipping.js';
   if (file_exists($shipping_js)) {
      wp_enqueue_script(
         'shipping-calculator',
         $uri . '/assets/js/shipping.js',
         [], // sem dependências pra não travar se 'product-search' não existir
         filemtime($shipping_js),
         true // footer
      );

      // Passa o ajax_url direto pro shipping.js
      wp_localize_script('shipping-calculator', 'WooShip', [
         'ajax_url' => admin_url('admin-ajax.php'),
      ]);
   }
   if (is_product()) {
      $pg_js = $dir . '/assets/js/product-gallery.js';
      if (file_exists($pg_js)) {
         wp_enqueue_script('product-gallery', $uri . '/assets/js/product-gallery.js', [], filemtime($pg_js), true);
      }
   }
});

// ====== AJAX: busca de produtos para o typeahead ======
add_action('wp_ajax_search_products', 'landing_tailwind_search_products');
add_action('wp_ajax_nopriv_search_products', 'landing_tailwind_search_products');

function landing_tailwind_search_products()
{
   // valida nonce
   $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
   if (! wp_verify_nonce($nonce, 'search_products_nonce')) {
      wp_send_json_error(['message' => 'Invalid nonce'], 403);
   }

   // termo de busca
   $term  = isset($_POST['q'])     ? sanitize_text_field(wp_unslash($_POST['q']))     : '';
   if (strlen($term) < 2) {
      wp_send_json_success(['items' => []]);
   }

   // query de produtos
   $q = new WP_Query([
      'post_type'      => 'product',
      'post_status'    => 'publish',
      's'              => $term,
      'posts_per_page' => 8,
      'orderby'        => 'relevance', // funciona melhor quando há 's'
   ]);

   $items = [];
   if ($q->have_posts()) {
      foreach ($q->posts as $p) {
         $product = wc_get_product($p->ID);
         if (! $product) continue;

         $img = get_the_post_thumbnail_url($p->ID, 'woocommerce_thumbnail');
         if (! $img) {
            $img = wc_placeholder_img_src('woocommerce_thumbnail');
         }

         $items[] = [
            'id'         => $p->ID,
            'title'      => html_entity_decode(get_the_title($p->ID)),
            'url'        => get_permalink($p->ID),
            'price_html' => $product->get_price_html(),
            'image'      => esc_url_raw($img),
         ];
      }
   }

   wp_send_json_success(['items' => $items]);
}

// Endpoint AJAX para calcular frete
add_action('wp_ajax_calculate_shipping', 'landing_tailwind_calculate_shipping');
add_action('wp_ajax_nopriv_calculate_shipping', 'landing_tailwind_calculate_shipping');

function landing_tailwind_calculate_shipping()
{
   $cep = isset($_POST['cep']) ? sanitize_text_field($_POST['cep']) : '';

   if (empty($cep)) {
      wp_send_json_error(['message' => 'CEP inválido'], 400);
   }

   // Validação do formato do CEP
   if (!preg_match('/^\d{8}$/', $cep)) {
      wp_send_json_error(['message' => 'Por favor, insira um CEP válido no formato correto (8 dígitos).'], 400);
   }


   // Simulação de cálculo de frete
   $shipping_options = [
      [
         'id' => 'sedex',
         'label' => 'Correios Sedex - Uma média de 3 dias úteis',
         'cost' => 25.00,
      ],
      [
         'id' => 'pac',
         'label' => 'Correios Pac - Uma média de 7 dias úteis',
         'cost' => 15.00,
      ],
   ];

   wp_send_json_success(['options' => $shipping_options]);
}

// Salva custo do frete na sessão quando enviado por POST
add_action('init', function () {
   if (function_exists('WC') && WC()->session) {
      WC()->session->__unset('custom_shipping_cost');     // tua flag
      WC()->session->__unset('chosen_shipping_methods');  // força recálculo
      WC()->session->__unset('shipping_for_package_0');
   }
}, 1);

// 2. Remove QUALQUER fee (inclui "Frete") que tenha sido adicionado antes
add_action('woocommerce_cart_calculate_fees', function ($cart) {
   if (! $cart) return;
   if (method_exists($cart, 'fees_api') && method_exists($cart->fees_api(), 'remove_all_fees')) {
      $cart->fees_api()->remove_all_fees();
   } else {
      // fallback versões antigas
      $cart->fees = [];
   }
}, 9999);




function landing_tailwind_set_shipping_cost()
{
   $cost = isset($_POST['cost']) ? floatval(wp_unslash($_POST['cost'])) : null;
   if (! is_numeric($cost) || $cost < 0) {
      wp_send_json_error(['message' => 'Custo inválido'], 400);
   }

   if (! function_exists('WC') || ! WC()->session) {
      wp_send_json_error(['message' => 'Sessão WC indisponível'], 500);
   }

   // salva na sessão e recalcula agora
   WC()->session->set('custom_shipping_cost', $cost);
   if (WC()->cart) {
      WC()->cart->calculate_totals();
   }

   wp_send_json_success(['saved' => true, 'cost' => $cost]);
}
add_action('wp_ajax_set_shipping_cost', 'landing_tailwind_set_shipping_cost');
add_action('wp_ajax_nopriv_set_shipping_cost', 'landing_tailwind_set_shipping_cost');




// === AJAX: retorna o HTML do box "Resumo" ===
// === AJAX: retorna o HTML do box "Resumo" ===
function landing_tailwind_cart_totals_fragment()
{
   if (! function_exists('WC') || ! WC()->cart) {
      wp_send_json_error(['message' => 'Carrinho indisponível'], 500);
   }

   // garante totais atualizados
   WC()->cart->calculate_totals();

   ob_start(); ?>
   <div class="rounded-xl ring-1 ring-purple-300 shadow-md bg-white p-6 cart_totals">
      <h2 class="text-xl font-bold text-purple-700 mb-4">Resumo</h2>
      <ul class="space-y-2 text-sm">
         <li class="flex justify-between">
            <span class="text-gray-700">Subtotal</span>
            <span class="font-semibold">
               <?php wc_cart_totals_subtotal_html(); ?>
            </span>
         </li>

         <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <li class="flex justify-between">
               <span class="text-gray-700"><?php echo esc_html($fee->name); ?></span>
               <span class="font-semibold"><?php wc_cart_totals_fee_html($fee); ?></span>
            </li>
         <?php endforeach; ?>

         <?php if (wc_coupons_enabled()) : ?>
            <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
               <li class="flex justify-between">
                  <span class="text-gray-700"><?php wc_cart_totals_coupon_label($coupon); ?></span>
                  <span class="font-semibold"><?php wc_cart_totals_coupon_html($coupon); ?></span>
               </li>
            <?php endforeach; ?>
         <?php endif; ?>

         <li class="flex justify-between text-base pt-2 border-t border-purple-200">
            <span class="text-gray-900 font-bold">Total</span>
            <span class="text-2xl font-bold text-black order-total">
               <?php wc_cart_totals_order_total_html(); ?>
            </span>
         </li>
      </ul>

      <p class="cart-proceed mt-6">
         <a class="button checkout wc-forward block w-full text-center px-4 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600 transition"
            href="<?php echo esc_url(wc_get_checkout_url()); ?>">
            Finalizar compra
         </a>
      </p>
   </div>
<?php
   $html = ob_get_clean();
   wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_tail_cart_totals', 'landing_tailwind_cart_totals_fragment');
add_action('wp_ajax_nopriv_tail_cart_totals', 'landing_tailwind_cart_totals_fragment');


// Garante que o Woo NÃO gere senha sozinho (exibe o campo "Senha" padrão)
add_filter('woocommerce_registration_generate_password', '__return_false', 99);

// ---- 1) CAMPOS EXTRAS + CONFIRMAR SENHA NO FORM DE CADASTRO ----
add_action('woocommerce_register_form', function () {
   // Se por acaso voltar a opção de gerar senha automática, não desenha nada extra
   if ('yes' === get_option('woocommerce_registration_generate_password')) return;

   $posted = function ($k) {
      return isset($_POST[$k]) ? wc_clean(wp_unslash($_POST[$k])) : '';
   };

   // Dados pessoais / endereço
   $fields = [
      'billing_first_name' => ['type' => 'text', 'label' => 'Nome', 'required' => true, 'class' => ['form-row-first']],
      'billing_last_name'  => ['type' => 'text', 'label' => 'Sobrenome', 'required' => true, 'class' => ['form-row-last']],
      'billing_phone'      => ['type' => 'tel', 'label' => 'Telefone', 'required' => true, 'class' => ['form-row-wide'], 'custom_attributes' => ['inputmode' => 'tel']],
      'billing_postcode'   => ['type' => 'text', 'label' => 'CEP', 'required' => true, 'class' => ['form-row-first'], 'custom_attributes' => ['inputmode' => 'numeric']],
      'billing_city'       => ['type' => 'text', 'label' => 'Cidade', 'required' => true, 'class' => ['form-row-last']],
      'billing_address_1'  => ['type' => 'text', 'label' => 'Endereço (Rua/Av.)', 'required' => true, 'class' => ['form-row-first']],
      'billing_address_2'  => ['type' => 'text', 'label' => 'Complemento (opcional)', 'class' => ['form-row-last']],
      'billing_state'      => ['type' => 'text', 'label' => 'Estado (UF)', 'required' => true, 'class' => ['form-row-first']],
   ];
   foreach ($fields as $key => $args) {
      woocommerce_form_field($key, $args, $posted($key));
   }
   echo '<input type="hidden" name="billing_country" value="BR" />';

   // IMPORTANTE: não desenhamos o campo "password" para não duplicar.
   // O Woo já mostra um campo "Senha". Aqui adicionamos apenas a CONFIRMAÇÃO:
   woocommerce_form_field('password2', [
      'type'              => 'password',
      'required'          => true,
      'label'             => __('Confirmar senha', 'woocommerce'),
      'autocomplete'      => 'new-password',
      'class'             => ['form-row-last'],
      'custom_attributes' => ['minlength' => 6],
   ], $posted('password2'));
});

// ---- 2) VALIDAÇÃO DOS CAMPOS (endereço + confirmação de senha) ----
add_action('woocommerce_register_post', function ($username, $email, $errors) {
   if ('yes' === get_option('woocommerce_registration_generate_password')) return;

   // obrigatórios
   $req = [
      'billing_first_name' => 'Informe seu nome.',
      'billing_last_name'  => 'Informe seu sobrenome.',
      'billing_phone'      => 'Informe seu telefone.',
      'billing_postcode'   => 'Informe seu CEP.',
      'billing_address_1'  => 'Informe seu endereço.',
      'billing_city'       => 'Informe sua cidade.',
      'billing_state'      => 'Informe seu estado (UF).',
   ];
   foreach ($req as $key => $msg) {
      if (empty($_POST[$key]) || trim((string)$_POST[$key]) === '') {
         $errors->add($key . '_error', '<strong>Erro:</strong> ' . $msg);
      }
   }

   // senha (campo padrão do Woo) + confirmação
   $pass  = isset($_POST['password'])  ? (string) wp_unslash($_POST['password'])  : '';
   $pass2 = isset($_POST['password2']) ? (string) wp_unslash($_POST['password2']) : '';

   if ($pass === '') {
      $errors->add('password_error', '<strong>Erro:</strong> Informe uma senha.');
   } elseif (strlen($pass) < 6) {
      $errors->add('password_error', '<strong>Erro:</strong> A senha deve ter pelo menos 6 caracteres.');
   }
   if ($pass2 === '') {
      $errors->add('password2_error', '<strong>Erro:</strong> Confirme sua senha.');
   } elseif ($pass && $pass !== $pass2) {
      $errors->add('password2_error', '<strong>Erro:</strong> As senhas não coincidem.');
   }
}, 10, 3);

// ---- 3) SALVAR METADADOS DE ENDEREÇO NO USUÁRIO ----
add_action('woocommerce_created_customer', function ($customer_id) {
   $get = function ($k) {
      return isset($_POST[$k]) ? wc_clean(wp_unslash($_POST[$k])) : '';
   };

   $first = $get('billing_first_name');
   $last  = $get('billing_last_name');
   if ($first) {
      update_user_meta($customer_id, 'first_name', $first);
      update_user_meta($customer_id, 'billing_first_name', $first);
   }
   if ($last) {
      update_user_meta($customer_id, 'last_name', $last);
      update_user_meta($customer_id, 'billing_last_name', $last);
   }

   foreach (['billing_phone', 'billing_postcode', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_country'] as $k) {
      $v = $get($k);
      if ($v !== '') update_user_meta($customer_id, $k, $v);
   }
});


add_action('woocommerce_api_mp_ping', function () {
   // Só para ver se o ngrok recebe
   error_log('Webhook test body: ' . file_get_contents('php://input'));
   status_header(200);
   echo 'ok';
   exit;
});


// Renomeia e reordena
add_filter('woocommerce_account_menu_items', function ($items) {
   // remove se quiser
   unset($items['downloads']); // exemplo

   // define a ordem/labels que você quer
   $items = [
      'dashboard'       => 'Painel',
      'orders'          => 'Pedidos',
      'edit-address'    => 'Endereços',
      'edit-account'    => 'Detalhes da conta',
      'payment-methods' => 'Pagamentos',
      'customer-logout' => 'Sair',
   ];
   return $items;
});


// Padroniza campos do Woo (conta) p/ bater com o seu visual
add_filter('woocommerce_form_field_args', function ($args, $key, $value) {
   if (! is_account_page()) return $args;

   // classes de label/input que vamos estilizar no CSS
   $args['label_class'][] = 'rs-label';
   $args['input_class'][] = 'rs-input';
   $args['class'][]       = 'rs-row';

   // ocupa 2 colunas por padrão; Woo já marca first/last/wide onde precisa
   if (empty($args['class']) || !in_array('form-row-first', $args['class'], true) && !in_array('form-row-last', $args['class'], true)) {
      $args['class'][] = 'form-row-wide';
   }

   // melhora UX com placeholder
   if (empty($args['placeholder']) && !empty($args['label'])) {
      $args['placeholder'] = wp_strip_all_tags($args['label']);
   }

   return $args;
}, 10, 3);

add_filter('woocommerce_enqueue_styles', function ($styles) {
   if (is_account_page()) return []; // só nessa página
   return $styles;
}, 99);

add_action('woocommerce_api_mp_debug_ping', function () {
   status_header(200);
   header('Content-Type: application/json; charset=UTF-8');
   echo json_encode(['ok' => true, 'time' => gmdate('c')]);
   exit;
});


// Opção A) Manter só "Retirar na loja" (sem custo) e escolher por padrão
add_filter('woocommerce_package_rates', function ($rates) {
   foreach ($rates as $id => $rate) {
      if ($rate->method_id !== 'local_pickup') {
         unset($rates[$id]);
      } else {
         $rates[$id]->cost = 0;
         if (isset($rates[$id]->taxes) && is_array($rates[$id]->taxes)) {
            foreach ($rates[$id]->taxes as $k => $v) $rates[$id]->taxes[$k] = 0;
         }
      }
   }
   return $rates;
}, 9999);

add_filter('woocommerce_shipping_chosen_method', function ($m, $avail) {
   foreach ($avail as $rate_id => $rate) {
      if ($rate->method_id === 'local_pickup') return $rate_id;
   }
   return $m;
}, 10, 2);

add_filter('woocommerce_cart_needs_shipping_address', '__return_false');
add_filter('woocommerce_shipping_show_shipping_calculator', '__return_false');


// Força usar o template do tema para a Loja/Categorias de produto
add_filter('template_include', function ($template) {
   if (is_shop() || is_post_type_archive('product') || is_tax(['product_cat', 'product_tag'])) {
      $custom = get_stylesheet_directory() . '/woocommerce/archive-product.php';
      if (file_exists($custom)) {
         return $custom;
      }
   }
   return $template;
}, 50);


// 10 produtos por página na loja/categorias/tags
add_filter('loop_shop_per_page', function ($n) {
   return 10;
}, 20);



/**
 * Filtra a loja por múltiplas categorias via ?rs_cats=slug1,slug2 ou rs_cats[]=slug
 */
add_action('pre_get_posts', function ($q) {
   if (is_admin() || !$q->is_main_query()) return;

   // só na loja e arquivos de produto
   if (!(is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag())) return;

   if (!isset($_GET['rs_cats'])) return;

   $val = $_GET['rs_cats'];
   $slugs = is_array($val) ? array_filter($val) : array_filter(explode(',', (string)$val));
   $slugs = array_map('sanitize_title', $slugs);
   $slugs = array_unique($slugs);

   if (empty($slugs)) return;

   $tax = (array) $q->get('tax_query');
   $tax[] = [
      'taxonomy' => 'product_cat',
      'field'    => 'slug',
      'terms'    => $slugs,
      'operator' => 'IN',
   ];
   $q->set('tax_query', $tax);
});
