<?php
/* Template Name: Meu Checkout */
defined('ABSPATH') || exit;
get_header(); ?>

<main class="container mx-auto max-w-6xl px-4 py-8">
   <h1 class="text-2xl font-semibold mb-6">Finalizar compra</h1>

   <?php
   // Garante que o carrinho/checkout do Woo rode aqui:
   if (function_exists('woocommerce_checkout')) {
      woocommerce_checkout(); // equivalente ao [woocommerce_checkout]
   } else {
      echo '<p>WooCommerce não encontrado.</p>';
   }
   ?>
</main>

<?php get_footer();
