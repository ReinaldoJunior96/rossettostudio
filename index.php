<?php

/**
 * Template index básico com integração WooCommerce
 * - Se for página do Woo (checkout/carrinho/loja/produto), renderiza o template do Woo e encerra.
 * - Caso contrário, renderiza a landing com listagem de produtos, carrinho e link para checkout.
 */

// 0) Remover item do carrinho (antes de qualquer saída)
if (isset($_GET['remove_item']) && function_exists('WC') && WC()->cart) {
   WC()->cart->remove_cart_item(sanitize_text_field($_GET['remove_item']));
   wp_safe_redirect(wc_get_cart_url());
   exit;
}

// Se for página do Woo (carrinho/checkout/loja), renderiza conteúdo do Woo e sai
if (function_exists('is_cart') && (is_cart() || is_checkout() || is_account_page() || is_shop())) {
   if (function_exists('woocommerce_content')) {
      // Cabeçalho/opcional
      // get_header();
      woocommerce_content(); // renderiza o template do WooCommerce
      // Rodapé/opcional
      // get_footer();
      return; // impede render da landing
   }
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
   <meta charset="<?php bloginfo('charset'); ?>">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title><?php bloginfo('name'); ?></title>
   <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
   <h1>Landing Page Tailwind 222</h1>

   <div class="product-list">
      <?php
      // 3) Listagem de produtos
      $args = array(
         'post_type' => 'product',
         'posts_per_page' => -1,
      );
      $loop = new WP_Query($args);

      if ($loop->have_posts()) {
         while ($loop->have_posts()) {
            $loop->the_post();
            global $product;
            echo '<div class="product-item">';
            echo '<h2>' . get_the_title() . '</h2>'; // Título do produto
            echo '<div class="product-image">' . woocommerce_get_product_thumbnail() . '</div>'; // Imagem do produto
            echo '<p>' . $product->get_price_html() . '</p>'; // Preço do produto
            echo '<a href="' . esc_url($product->add_to_cart_url()) . '" class="add-to-cart">Adicionar ao carrinho</a>'; // Botão de adicionar ao carrinho
            echo '</div>';
         }
      } else {
         echo '<p>Nenhum produto encontrado.</p>';
      }
      wp_reset_postdata();
      ?>
   </div>

   <div class="cart">
      <h2>Seu Carrinho</h2>
      <?php
      // 4) Render do carrinho simples
      $items = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart() : array();
      if (! empty($items)) :
         foreach ($items as $cart_item_key => $cart_item) :
            $p = wc_get_product($cart_item['product_id']);
            if (! $p) {
               continue;
            }
      ?>
            <div class="cart-item">
               <h3><?php echo esc_html($p->get_name()); ?></h3>
               <p>Quantidade: <?php echo (int) $cart_item['quantity']; ?></p>
               <p>Preço: <?php echo wc_price($cart_item['line_total']); ?></p>
               <a href="<?php echo esc_url(add_query_arg('remove_item', $cart_item_key, home_url(add_query_arg(array(), $_SERVER['REQUEST_URI'])))); ?>"
                  class="remove-item" rel="nofollow">
                  Remover
               </a>
            </div>
      <?php
         endforeach;
      else :
         echo '<p>Seu carrinho está vazio.</p>';
      endif;

      // 5) URL canônica do checkout
      $checkout_url = function_exists('wc_get_page_permalink')
         ? wc_get_page_permalink('checkout')
         : (function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/'));

      // Debug (olhe no código-fonte; remova depois)
      echo '<!-- CHECKOUT_URL: ' . esc_html($checkout_url) . ' -->';

      // DEBUG:
      error_log('IS_CHECKOUT=' . (function_exists('is_checkout') && is_checkout() ? '1' : '0'));
      ?>
      <p>
         <a href="<?php echo esc_url($checkout_url); ?>" class="button checkout-button" rel="nofollow">
            Finalizar Compra
         </a>
      </p>
   </div>

   <?php wp_footer(); ?>
</body>

</html>