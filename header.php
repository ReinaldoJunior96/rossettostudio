<!doctype html>
<html <?php language_attributes(); ?>>

<head>
   <meta charset="<?php bloginfo('charset'); ?>">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <?php wp_head(); ?>
</head>

<body <?php body_class('bg-white antialiased'); ?>>

   <!-- AVISO / TOPBAR -->
   <section style="background:#000;color:#fff;">
      <div class=" h-12 flex items-center justify-center text-center text-[17px] font-semibold tracking-tight">
         Frete grátis para todo o Brasil acima de R$ 500,00
      </div>
      <div class="pointer-events-none" style="height:2px;background:linear-gradient(90deg,transparent,var(--color-purple-light,#9B59B6),transparent)"></div>
   </section>

   <!-- HEADER STICKY -->
   <header class="flex justify-center sticky top-0 z-50"
      style="background:var(--color-purple-default,#6A0DAD);border-bottom:1px solid rgba(255,255,255,.08);backdrop-filter:blur(10px) saturate(120%);">
      <div class="margin-site grid grid-cols-12 items-center gap-4 py-4 text-white">

         <!-- LOGO -->
         <div class="col-span-6 md:col-span-2">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center gap-3">
               <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-branca.png"
                  alt="Logo Rossetto Studio" class="h-10 w-auto select-none" />
            </a>
         </div>

         <!-- BUSCA -->
         <div class="col-span-12 md:col-span-7 order-last md:order-none">
            <label for="product-search" class="sr-only">Buscar produtos</label>
            <div class="relative">
               <div class="flex items-center gap-2 rounded-2xl px-4 py-2.5"
                  style="background:rgba(255,255,255,.96);border:1px solid rgba(0,0,0,.08);">
                  <i class="fa-solid fa-magnifying-glass" style="color:#222"></i>
                  <input id="product-search" type="text" placeholder="Busque por nome do produto..."
                     class="w-full bg-transparent px-1 py-1.5 text-sm text-gray-900 placeholder:text-gray-500 focus:outline-none"
                     autocomplete="off" />
                  <kbd class="hidden md:inline-flex items-center rounded-md border border-gray-300 bg-gray-50 px-1.5 text-[11px] text-gray-600">/</kbd>
               </div>

               <!-- dropdown (mantém o seu ID p/ JS) -->
               <div id="product-search-results"
                  class="absolute z-50 mt-2 w-full rounded-2xl overflow-hidden hidden"
                  style="background:#fff;border:1px solid rgba(0,0,0,.12);box-shadow:0 18px 40px rgba(0,0,0,.16);">
               </div>
            </div>
         </div>

         <!-- AÇÕES -->
         <?php
         $my_account_url = wc_get_page_permalink('myaccount');
         $cart_url       = wc_get_cart_url();
         $home_url       = home_url('/');
         $cart_count     = function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
         ?>
         <div class="col-span-6 md:col-span-3 flex items-center justify-end gap-2">

            <!-- Home -->
            <a href="<?php echo esc_url($home_url); ?>"
               class="inline-flex items-center justify-center h-10 w-10 rounded-xl transition"
               style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.14);"
               title="Início" aria-label="Início">
               <i class="fa-solid fa-house text-white/90"></i>
            </a>

            <!-- Conta -->
            <?php if (is_user_logged_in()) : ?>
               <div class="relative group">
                  <a href="<?php echo esc_url($my_account_url); ?>"
                     class="inline-flex items-center justify-center h-10 w-10 rounded-xl transition"
                     style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.14);"
                     title="Minha conta" aria-haspopup="menu" aria-expanded="false">
                     <i class="fa-solid fa-user text-white/90"></i>
                  </a>
                  <div class="absolute right-0 mt-2 hidden group-hover:block min-w-[200px] rounded-2xl overflow-hidden"
                     style="background:#fff;border:1px solid rgba(0,0,0,.12);box-shadow:0 20px 44px rgba(0,0,0,.18);">
                     <a href="<?php echo esc_url(wc_get_endpoint_url('orders', '', $my_account_url)); ?>" class="block px-4 py-2.5 text-sm text-gray-800 hover:bg-gray-50">Meus pedidos</a>
                     <a href="<?php echo esc_url($my_account_url); ?>" class="block px-4 py-2.5 text-sm text-gray-800 hover:bg-gray-50">Minha conta</a>
                     <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">Sair</a>
                  </div>
               </div>
            <?php else : ?>
               <a href="<?php echo esc_url($my_account_url); ?>"
                  class="inline-flex items-center justify-center h-10 w-10 rounded-xl transition"
                  style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.14);"
                  title="Entrar ou criar conta" aria-label="Entrar ou criar conta">
                  <i class="fa-solid fa-user text-white/90"></i>
               </a>
            <?php endif; ?>

            <!-- Carrinho -->
            <a href="<?php echo esc_url($cart_url); ?>"
               class="relative inline-flex items-center justify-center h-10 w-10 rounded-xl transition"
               style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.14);"
               title="Carrinho" aria-label="Carrinho">
               <i class="fa-solid fa-cart-shopping text-white/90"></i>
               <?php if ($cart_count > 0) : ?>
                  <span class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center h-5 min-w-[20px] px-1.5 rounded-full text-white text-[11px] font-semibold leading-none shadow-md"
                     style="background:#ef4444;">
                     <?php echo esc_html($cart_count); ?>
                  </span>
               <?php endif; ?>
            </a>
         </div>
      </div>
   </header>

   <?php if (function_exists('wp_body_open')) {
      wp_body_open();
   } ?>