<?php

/**
 * Theme functions
 */

/* ==============================
   Suporte básico do tema
============================== */
add_action('after_setup_theme', function () {
   add_theme_support('woocommerce');
   add_theme_support('title-tag');
   add_theme_support('post-thumbnails');
   add_theme_support('wc-product-gallery-zoom');
   add_theme_support('wc-product-gallery-lightbox');
   add_theme_support('wc-product-gallery-slider');
});

/* ==============================
   Enqueue de assets
============================== */
add_action('wp_enqueue_scripts', function () {
   $dir = get_stylesheet_directory();
   $uri = get_stylesheet_directory_uri();

   if (is_checkout()) {
      // scripts padrão do WooCommerce (mantidos)
      wp_enqueue_script('wc-checkout');
      wp_enqueue_script('wc-country-select');
      wp_enqueue_script('wc-address-i18n');
   }

   if (is_cart()) {
      $ship_js = $dir . '/assets/js/cart-shipping.js';
      if (file_exists($ship_js)) {
         wp_enqueue_script('rs-cart-shipping', $uri . '/assets/js/cart-shipping.js', ['jquery'], filemtime($ship_js), true);
         wp_localize_script('rs-cart-shipping', 'WooShip', [
            'ajax_url' => admin_url('admin-ajax.php'),
         ]);
      }
   }

   if (class_exists('WooCommerce')) {
      wp_enqueue_script('wc-cart-fragments');
   }

   // Tailwind
   $css_path = $dir . '/assets/build/app.css';
   if (file_exists($css_path)) {
      wp_enqueue_style('tailwind', $uri . '/assets/build/app.css', [], filemtime($css_path));
   }

   // tema de cores
   $theme_css = $dir . '/assets/css/theme.css';
   if (file_exists($theme_css)) {
      wp_enqueue_style('theme-colors', $uri . '/assets/css/theme.css', [], filemtime($theme_css));
   }

   // FontAwesome
   wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css', [], null);

   // Busca
   $search_js = $dir . '/assets/js/search.js';
   if (file_exists($search_js)) {
      wp_enqueue_script('product-search', $uri . '/assets/js/search.js', [], filemtime($search_js), true);
      wp_localize_script('product-search', 'ProductSearch', [
         'ajax_url' => admin_url('admin-ajax.php'),
         'nonce'    => wp_create_nonce('search_products_nonce'),
      ]);
   }

   // Galeria produto
   if (is_product()) {
      $pg_js = $dir . '/assets/js/product-gallery.js';
      if (file_exists($pg_js)) {
         wp_enqueue_script('product-gallery', $uri . '/assets/js/product-gallery.js', [], filemtime($pg_js), true);
      }
   }
});

/* ==============================
   AJAX: busca de produtos
============================== */
add_action('wp_ajax_search_products', 'landing_tailwind_search_products');
add_action('wp_ajax_nopriv_search_products', 'landing_tailwind_search_products');

function landing_tailwind_search_products()
{
   $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
   if (! wp_verify_nonce($nonce, 'search_products_nonce')) {
      wp_send_json_error(['message' => 'Invalid nonce'], 403);
   }

   $term = isset($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';
   if (strlen($term) < 2) {
      wp_send_json_success(['items' => []]);
   }

   $q = new WP_Query([
      'post_type'      => 'product',
      'post_status'    => 'publish',
      's'              => $term,
      'posts_per_page' => 8,
      'orderby'        => 'relevance',
   ]);

   $items = [];
   if ($q->have_posts()) {
      foreach ($q->posts as $p) {
         $product = wc_get_product($p->ID);
         if (! $product) continue;

         $img = get_the_post_thumbnail_url($p->ID, 'woocommerce_thumbnail') ?: wc_placeholder_img_src('woocommerce_thumbnail');

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

/* ==============================
   Conta / cadastro
============================== */
add_filter('woocommerce_registration_generate_password', '__return_false', 99);

add_action('woocommerce_register_form', function () {
   if ('yes' === get_option('woocommerce_registration_generate_password')) return;

   $posted = function ($k) {
      return isset($_POST[$k]) ? wc_clean(wp_unslash($_POST[$k])) : '';
   };

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

   woocommerce_form_field('password2', [
      'type' => 'password',
      'required' => true,
      'label' => __('Confirmar senha', 'woocommerce'),
      'autocomplete' => 'new-password',
      'class' => ['form-row-last'],
      'custom_attributes' => ['minlength' => 6],
   ], $posted('password2'));
});

add_action('woocommerce_register_post', function ($username, $email, $errors) {
   if ('yes' === get_option('woocommerce_registration_generate_password')) return;

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
      update_user_meta($customer_id, 'last_name',  $last);
      update_user_meta($customer_id, 'billing_last_name',  $last);
   }

   foreach (['billing_phone', 'billing_postcode', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_country'] as $k) {
      $v = $get($k);
      if ($v !== '') update_user_meta($customer_id, $k, $v);
   }
});

/* ==============================
   Ajustes de conta / menus / estilos
============================== */
add_filter('woocommerce_account_menu_items', function ($items) {
   unset($items['downloads']);
   return [
      'dashboard'       => 'Painel',
      'orders'          => 'Pedidos',
      'edit-address'    => 'Endereços',
      'edit-account'    => 'Detalhes da conta',
      'payment-methods' => 'Pagamentos',
      'customer-logout' => 'Sair',
   ];
});

add_filter('woocommerce_form_field_args', function ($args, $key, $value) {
   if (! is_account_page()) return $args;
   $args['label_class'][] = 'rs-label';
   $args['input_class'][] = 'rs-input';
   $args['class'][]       = 'rs-row';
   if (empty($args['class']) || (!in_array('form-row-first', $args['class'], true) && !in_array('form-row-last', $args['class'], true))) {
      $args['class'][] = 'form-row-wide';
   }
   if (empty($args['placeholder']) && !empty($args['label'])) {
      $args['placeholder'] = wp_strip_all_tags($args['label']);
   }
   return $args;
}, 10, 3);

add_filter('woocommerce_enqueue_styles', function ($styles) {
   if (is_account_page()) return [];
   return $styles;
}, 99);

/* ==============================
   Ícones gateways (exemplo)
============================== */
add_filter('woocommerce_gateway_icon', function ($icon, $gateway_id) {
   $svg_barcode = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="2" height="14" fill="#6b21a8"/><rect x="7" y="5" width="1" height="14" fill="#6b21a8"/><rect x="10" y="5" width="2" height="14" fill="#6b21a8"/><rect x="14" y="5" width="1" height="14" fill="#6b21a8"/><rect x="17" y="5" width="2" height="14" fill="#6b21a8"/></svg>';
   $svg_card    = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none"><rect x="2" y="5" width="20" height="14" rx="3" stroke="#6b21a8" stroke-width="2"/><rect x="3" y="9" width="18" height="2" fill="#6b21a8"/></svg>';
   $svg_pix     = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M7 12l5-5 5 5-5 5-5-5z" fill="#10b981"/></svg>';
   $svg_wallet  = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none"><rect x="3" y="6" width="18" height="12" rx="3" stroke="#6b21a8" stroke-width="2"/><circle cx="16" cy="12" r="1.8" fill="#6b21a8"/></svg>';

   $map = [
      'woo-mercado-pago-ticket' => $svg_barcode,
      'woo-mercado-pago-basic'  => $svg_barcode,
      'woo-mercado-pago-custom' => $svg_card,
      'woo-mercado-pago-pix'    => $svg_pix,
      'woo-mercado-pago-wallet' => $svg_wallet,
      'pix'                     => $svg_pix,
   ];
   return isset($map[$gateway_id]) ? '<span class="rs-gw-icon">' . $map[$gateway_id] . '</span>' : $icon;
}, 10, 2);

add_filter('woocommerce_coupons_enabled', '__return_false');

/* ==============================
   Debug frete no carrinho (apenas admin)
============================== */
add_action('wp_footer', function () {
   if (is_cart() && current_user_can('manage_woocommerce')) {
      $packages = WC()->shipping()->get_packages();
      echo '<pre style="background:#111;color:#0f0;padding:12px;white-space:pre-wrap;z-index:99999;position:fixed;bottom:0;left:0;right:0;max-height:40vh;overflow:auto">';
      echo "DEBUG FRETE\n\n";
      print_r($packages);
      echo '</pre>';
   }
});

add_action('woocommerce_after_shipping_rate', function ($rate) {
   error_log(sprintf('[FRETE] %s | %s | R$ %s', $rate->id, $rate->label, $rate->cost));
});

/* ==============================
   Loja / loop / filtros
============================== */
add_filter('woocommerce_registration_enabled', '__return_true');

add_filter('template_include', function ($template) {
   if (is_shop() || is_post_type_archive('product') || is_tax(['product_cat', 'product_tag'])) {
      $custom = get_stylesheet_directory() . '/woocommerce/archive-product.php';
      if (file_exists($custom)) return $custom;
   }
   return $template;
}, 50);

add_filter('loop_shop_per_page', function ($n) {
   return 10;
}, 20);

add_action('pre_get_posts', function ($q) {
   if (is_admin() || ! $q->is_main_query()) return;

   if (is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag()) {
      if (isset($_GET['rs_cats'])) {
         $val   = $_GET['rs_cats'];
         $slugs = is_array($val) ? array_filter($val) : array_filter(explode(',', (string)$val));
         $slugs = array_unique(array_map('sanitize_title', $slugs));
         if (! empty($slugs)) {
            $tax = (array) $q->get('tax_query');
            $tax[] = ['taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $slugs, 'operator' => 'IN'];
            $q->set('tax_query', $tax);
            $q->set('paged', 1);
         }
      }

      // remove auto-esconder "fora de estoque" em dev
      $tax_query = (array) $q->get('tax_query');
      foreach ($tax_query as $i => $tx) {
         if (isset($tx['taxonomy'], $tx['terms']) && $tx['taxonomy'] === 'product_visibility' && is_array($tx['terms'])) {
            $tx['terms'] = array_diff($tx['terms'], ['outofstock']);
            $tax_query[$i] = $tx;
         }
      }
      $q->set('tax_query', $tax_query);
   }
});

add_filter('woocommerce_blocks_use_cart_checkout_blocks', '__return_false');

/* ==============================
   Checkout – visual & placeholders (mantido)
============================== */
add_filter('woocommerce_checkout_fields', function ($fields) {
   $groups = ['billing', 'shipping', 'account', 'order'];
   foreach ($groups as $group) {
      if (empty($fields[$group])) continue;
      foreach ($fields[$group] as $key => &$f) {
         $f['class'][]       = 'rs-row';
         $f['label_class'][] = 'rs-label';
         $f['input_class'][] = 'rs-input';
         if ($key === 'billing_address_1') $f['placeholder'] = $f['placeholder'] ?? 'Nome da rua';
         if ($key === 'billing_address_2') {
            $f['placeholder'] = $f['placeholder'] ?? 'Apartamento, sala, etc. (opcional)';
            $f['required'] = false;
         }
      }
   }
   if (isset($fields['billing']['billing_country'])) {
      $fields['billing']['billing_country']['default'] = 'BR';
      $fields['billing']['billing_country']['class'][] = 'rs-country-compact';
   }
   foreach (['billing_address_1', 'billing_address_2', 'billing_email', 'order_comments'] as $full) {
      if (isset($fields['billing'][$full])) $fields['billing'][$full]['class'][] = 'rs-span-2';
   }
   $b = &$fields['billing'];
   $prio = 10;
   $want = [
      'billing_first_name',
      'billing_last_name',
      'billing_persontype',
      'billing_cpf',
      'billing_postcode',
      'billing_country',
      'billing_address_1',
      'billing_number',
      'billing_neighborhood',
      'billing_city',
      'billing_state',
      'billing_phone',
      'billing_email',
      'billing_address_2',
   ];
   foreach ($want as $k) if (isset($b[$k])) $b[$k]['priority'] = $prio += 10;

   return $fields;
});

add_filter('woocommerce_form_field_args', function ($args, $key, $value) {
   if (! is_checkout()) return $args;
   $args['label_class'][] = 'rs-label';
   $args['input_class'][] = 'rs-input';
   $args['class'][]       = 'rs-row';
   return $args;
}, 10, 3);

/* Esconde as opções de frete no checkout (sem mudar a seleção do Woo) */
add_action('wp_head', function () {
   if (!is_checkout()) return;
   echo '<style>
      .woocommerce-checkout-review-order .shipping ul#shipping_method,
      .woocommerce-checkout-review-order .shipping .wc_shipping_rates { display: none !important; }
   </style>';
});

/* ==============================
   FRETE NO CARRINHO – AJAX
============================== */

/** Calcula fretes para CEP e retorna opções (rate_id/label/cost) */
add_action('wp_ajax_calculate_shipping', 'rs_ajax_calculate_shipping');
add_action('wp_ajax_nopriv_calculate_shipping', 'rs_ajax_calculate_shipping');
function rs_ajax_calculate_shipping()
{
   if (null === WC()->cart) {
      wc_load_cart();
   }

   $cep = isset($_POST['cep']) ? preg_replace('/\D+/', '', (string) $_POST['cep']) : '';
   if (strlen($cep) !== 8) {
      wp_send_json_error(['message' => 'CEP inválido. Use 8 dígitos.'], 400);
   }

   // Fixar país BR e CEP de entrega
   WC()->customer->set_billing_country('BR');
   WC()->customer->set_shipping_country('BR');
   WC()->customer->set_billing_postcode($cep);
   WC()->customer->set_shipping_postcode($cep);
   WC()->customer->save();

   // Recalcular fretes/totais
   WC()->cart->calculate_shipping();
   WC()->cart->calculate_totals();

   $packages = WC()->shipping()->get_packages();
   $pkg = $packages[0] ?? null;
   $rates = $pkg['rates'] ?? [];

   $options = [];
   foreach ($rates as $rate) {
      /** @var WC_Shipping_Rate $rate */
      $label = $rate->get_label();
      $cost  = (float) $rate->get_cost();
      $taxes = array_sum((array) $rate->get_taxes());
      $cost_total = $cost + (float) $taxes;

      $options[] = [
         'id'    => $rate->get_id(), // ex: "flat_rate:3"
         'label' => sprintf('%s — %s', $label, wc_price($cost_total)),
         'cost'  => $cost_total,
      ];
   }

   wp_send_json_success(['options' => $options]);
}

/** Define o método de frete escolhido e recalcula totais */
add_action('wp_ajax_set_shipping_method', 'rs_ajax_set_shipping_method');
add_action('wp_ajax_nopriv_set_shipping_method', 'rs_ajax_set_shipping_method');
function rs_ajax_set_shipping_method()
{
   if (null === WC()->cart) {
      wc_load_cart();
   }

   $rate_id = isset($_POST['rate_id']) ? wc_clean(wp_unslash($_POST['rate_id'])) : '';
   if (!$rate_id) wp_send_json_error(['message' => 'rate_id ausente'], 400);

   $chosen = WC()->session->get('chosen_shipping_methods', []);
   $chosen[0] = $rate_id; // único pacote
   WC()->session->set('chosen_shipping_methods', $chosen);

   WC()->cart->calculate_totals();
   wp_send_json_success(['ok' => true]);
}

/** Limpa método escolhido */
add_action('wp_ajax_clear_shipping_method', 'rs_ajax_clear_shipping_method');
add_action('wp_ajax_nopriv_clear_shipping_method', 'rs_ajax_clear_shipping_method');
function rs_ajax_clear_shipping_method()
{
   if (null === WC()->cart) {
      wc_load_cart();
   }
   WC()->session->__unset('chosen_shipping_methods');
   WC()->cart->calculate_totals();
   wp_send_json_success(['ok' => true]);
}

/* ==============================
   BOTÃO DE TESTE (+R$ 10,00)
============================== */
add_action('woocommerce_cart_calculate_fees', function ($cart) {
   if (is_admin() && !defined('DOING_AJAX')) return;
   if (!WC()->session) return;
   $flag = WC()->session->get('rs_add_ten_fee');
   if (!empty($flag)) {
      $cart->add_fee('Taxa de teste', 10, false);
   }
}, 20, 1);

add_action('wp_ajax_rs_add_ten_fee', 'rs_ajax_add_ten_fee');
add_action('wp_ajax_nopriv_rs_add_ten_fee', 'rs_ajax_add_ten_fee');
function rs_ajax_add_ten_fee()
{
   if (null === WC()->cart) {
      wc_load_cart();
   }
   WC()->session->set('rs_add_ten_fee', 1);
   WC()->cart->calculate_totals();
   wp_send_json_success(['ok' => true]);
}

add_action('wp_ajax_rs_clear_ten_fee', 'rs_ajax_clear_ten_fee');
add_action('wp_ajax_nopriv_rs_clear_ten_fee', 'rs_ajax_clear_ten_fee');
function rs_ajax_clear_ten_fee()
{
   if (null === WC()->cart) {
      wc_load_cart();
   }
   WC()->session->__unset('rs_add_ten_fee');
   WC()->cart->calculate_totals();
   wp_send_json_success(['ok' => true]);
}

/* ==============================
   Fragmento AJAX para atualizar o box de totais do carrinho
   (ÚNICA definição + registro)
============================== */
if (! function_exists('rs_ajax_cart_totals_fragment')) {
   add_action('wp_ajax_tail_cart_totals', 'rs_ajax_cart_totals_fragment');
   add_action('wp_ajax_nopriv_tail_cart_totals', 'rs_ajax_cart_totals_fragment');

   function rs_ajax_cart_totals_fragment()
   {
      if (null === WC()->cart) {
         wc_load_cart();
      }
      ob_start();
      wc_get_template('cart/cart-totals.php');
      $html = ob_get_clean();
      wp_send_json_success(['html' => $html]);
   }
}
