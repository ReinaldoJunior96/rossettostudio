<?php

/**
 * Front Page - Landing com produtos (Tailwind)
 * Requer WooCommerce ativo e página configurada como Front Page.
 */
defined('ABSPATH') || exit;

get_header();
?>




<section class="flex justify-center">
   <div class="margin-site">
      <img class="w-full h-[400px] object-cover rounded-lg mt-3" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/22.jpg" alt="">
   </div>

</section>


<!-- ==================== PRODUTOS EM DESTAQUE (Glass Cards 2.0) ==================== -->
<section class="relative py-14 flex flex-col justify-center items-center ">
   <!-- fundo com glow suave (igual vibe da seção de Sobre) -->
   <!-- <div class="pointer-events-none absolute inset-0 overflow-hidden">
      <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-gradient-to-br from-purple-600 via-fuchsia-500 to-sky-400 blur-3xl opacity-20 rounded-full"></div>

   </div> -->

   <div class="relative margin-site grid grid-cols-6 gap-8 items-start">
      <!-- ===== Banner (alinhado na grade) ===== -->
      <div class="col-span-3">
         <div class="relative rounded-3xl overflow-hidden shadow-xl ring-1 ring-black/5">
            <div class="relative  pt-[78.5%]"> <!-- controle de proporção do banner -->
               <img
                  class="absolute inset-0 h-full w-full object-cover"
                  src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/robo.jpg"
                  alt="Lançamento"
                  loading="lazy">
            </div>
         </div>
      </div>
      <div class="flex  justify-center  col-span-3 gap-8">
         <!-- ===== 2 cards da primeira fileira ===== -->
         <?php
         $args_first_row = [
            'post_type'      => 'product',
            'posts_per_page' => 2,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
         ];
         $q_first_row = new WP_Query($args_first_row);

         if ($q_first_row->have_posts()) :
            while ($q_first_row->have_posts()) : $q_first_row->the_post();
               $product = wc_get_product(get_the_ID());
               if (!$product) continue;

               $permalink    = $product->get_permalink();
               $title        = $product->get_name();
               $price_html   = $product->get_price_html();
               $is_on_sale   = $product->is_on_sale();
               $supports_ajax = $product->is_purchasable() && $product->is_in_stock() && $product->supports('ajax_add_to_cart');
               $add_url      = $product->add_to_cart_url();
               $buy_now      = add_query_arg('add-to-cart', $product->get_id(), wc_get_checkout_url());
         ?>
               <article class="group rounded-3xl bg-white/70 backdrop-blur-sm ring-1  ring-black/5 shadow-[0_10px_30px_-10px_rgba(17,24,39,0.2)] 
            hover:shadow-[0_20px_40px_-12px_rgba(17,24,39,0.25)] transition">
                  <!-- mídia -->
                  <div class="relative p-6">
                     <?php if ($is_on_sale) : ?>
                        <span class="absolute left-4 top-3 z-10 inline-flex items-center rounded-full bg-purple-600 px-2.5 py-1 text-xs font-semibold text-white shadow">
                           Promoção
                        </span>
                     <?php endif; ?>
                     <div class=" h-[250px] w-[250px] object-contain ">
                        <?php echo $product->get_image('woocommerce_thumbnail', ['class' => 'h-full w-full']); ?>
                     </div>
                  </div>

                  <!-- conteúdo -->
                  <div class="px-6 pb-6 flex flex-col gap-3 ">
                     <a href="<?php echo esc_url($permalink); ?>" class="flex h-[50px]">
                        <h3 class="text-lg font-semibold text-gray-800 leading-snug line-clamp-2">
                           <?php echo esc_html($title); ?>
                        </h3>
                     </a>
                     <div class="flex  flex-col gap-3 justify-end ">
                        <div class="text-lg font-semibold text-purple-default roboto ">
                           <s class="text-gray-400 text-[14px]">
                              <?php echo wc_price($product->get_regular_price()); ?>
                           </s>
                           <br>
                           <ins class="no-underline text-[22px] font-extrabold text-purple-default flex items-center gap-1">
                              <?php echo wc_price($product->get_sale_price()); ?>
                              <?php
                              // classes base do seu botão (estilo)
                              $btn_classes  = 'inline-flex items-center justify-center bg-purple-900 p-1 rounded-md';
                              $btn_classes .= $supports_ajax ? ' add_to_cart_button ajax_add_to_cart' : ''; // ativa AJAX quando puder
                              ?>
                              <a href="<?php echo esc_url($add_url); ?>"
                                 class="<?php echo esc_attr($btn_classes); ?>"
                                 data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                                 data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                                 data-quantity="1"
                                 rel="nofollow"
                                 aria-label="Adicionar <?php echo esc_attr($product->get_name()); ?> ao carrinho">
                                 <i class="fa-solid fa-cart-shopping text-white text-[14px]"></i>
                                 <span class="sr-only">Adicionar ao carrinho</span>
                              </a>
                           </ins>
                        </div>

                        <div class="flex items-end  bg-red-300 gap-3 w-full">

                           <a href="<?php echo esc_url($buy_now); ?>"
                              class="inline-flex items-center w-full justify-center font-bold rounded-md bg-indigo-600 text-white px-3 py-2 text-sm hover:bg-indigo-700 transition">
                              Comprar agora
                           </a>
                        </div>
                     </div>

                  </div>
               </article>
         <?php
            endwhile;
            wp_reset_postdata();
         endif;
         ?>
      </div>
   </div>

   <!-- ===== Segunda fileira: 4 cards ===== -->
   <div class="relative margin-site mt-8 grid grid-cols-1 md:grid-cols-4 gap-8">
      <?php
      $args_second_row = [
         'post_type'      => 'product',
         'posts_per_page' => 4,
         'post_status'    => 'publish',
         'orderby'        => 'date',
         'order'          => 'DESC',
         'offset'         => 2,
      ];
      $q_second_row = new WP_Query($args_second_row);

      if ($q_second_row->have_posts()) :
         while ($q_second_row->have_posts()) : $q_second_row->the_post();
            $product = wc_get_product(get_the_ID());
            if (!$product) continue;

            $permalink    = $product->get_permalink();
            $title        = $product->get_name();
            $price_html   = $product->get_price_html();
            $is_on_sale   = $product->is_on_sale();
            $supports_ajax = $product->is_purchasable() && $product->is_in_stock() && $product->supports('ajax_add_to_cart');
            $add_url      = $product->add_to_cart_url();
            $buy_now      = add_query_arg('add-to-cart', $product->get_id(), wc_get_checkout_url());
      ?>
            <article class="group rounded-3xl bg-white/70 backdrop-blur-sm ring-1  ring-black/5 shadow-[0_10px_30px_-10px_rgba(17,24,39,0.2)] 
            hover:shadow-[0_20px_40px_-12px_rgba(17,24,39,0.25)] transition">
               <!-- mídia -->
               <div class="relative p-6">
                  <?php if ($is_on_sale) : ?>
                     <span class="absolute left-4 top-3 z-10 inline-flex items-center rounded-full bg-purple-600 px-2.5 py-1 text-xs font-semibold text-white shadow">
                        Promoção
                     </span>
                  <?php endif; ?>
                  <div class=" h-[250px] w-[250px] object-contain ">
                     <?php echo $product->get_image('woocommerce_thumbnail', ['class' => 'h-full w-full']); ?>
                  </div>
               </div>

               <!-- conteúdo -->
               <div class="px-6 pb-6 flex flex-col gap-3 ">
                  <a href="<?php echo esc_url($permalink); ?>" class="flex h-[50px]">
                     <h3 class="text-lg font-semibold text-gray-800 leading-snug line-clamp-2">
                        <?php echo esc_html($title); ?>
                     </h3>
                  </a>
                  <div class="flex  flex-col gap-3 justify-end ">
                     <div class="text-lg font-semibold text-purple-default roboto ">
                        <s class="text-gray-400 text-[14px]">
                           <?php echo wc_price($product->get_regular_price()); ?>
                        </s>
                        <br>
                        <ins class="no-underline text-[22px] font-extrabold text-purple-default flex items-center gap-1">
                           <?php echo wc_price($product->get_sale_price()); ?>
                           <?php
                           // classes base do seu botão (estilo)
                           $btn_classes  = 'inline-flex items-center justify-center bg-purple-900 p-1 rounded-md';
                           $btn_classes .= $supports_ajax ? ' add_to_cart_button ajax_add_to_cart' : ''; // ativa AJAX quando puder
                           ?>
                           <a href="<?php echo esc_url($add_url); ?>"
                              class="<?php echo esc_attr($btn_classes); ?>"
                              data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                              data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                              data-quantity="1"
                              rel="nofollow"
                              aria-label="Adicionar <?php echo esc_attr($product->get_name()); ?> ao carrinho">
                              <i class="fa-solid fa-cart-shopping text-white text-[14px]"></i>
                              <span class="sr-only">Adicionar ao carrinho</span>
                           </a>
                        </ins>
                     </div>

                     <div class="flex items-end  bg-red-300 gap-3 w-full">
                        <a href="<?php echo esc_url($buy_now); ?>"
                           class="inline-flex items-center w-full justify-center font-bold rounded-md bg-indigo-600 text-white px-3 py-2 text-sm hover:bg-indigo-700 transition">
                           Comprar agora
                        </a>
                     </div>
                  </div>

               </div>
            </article>
      <?php
         endwhile;
         wp_reset_postdata();
      endif;
      ?>
   </div>
</section>
<!-- ==================== /PRODUTOS EM DESTAQUE ==================== -->



<!-- ======================= SOBRE / SERVIÇOS (CARDS) ======================= -->
<section id="sobre-nos" class="relative bg-white flex justify-center pt-5 pb-16 overflow-hidden roboto">
   <!-- background decor -->
   <!-- <div class="pointer-events-none absolute inset-0 -z-10">
      <div class="absolute -top-24 -left-24 w-96 h-96 bg-gradient-to-br from-purple-600 via-fuchsia-500 to-sky-400 blur-3xl opacity-20 rounded-full"></div>
      <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-gradient-to-br from-cyan-400 via-blue-500 to-indigo-600 blur-3xl opacity-20 rounded-full"></div>
   </div> -->

   <div class="margin-site w-full">
      <!-- título/âncora -->
      <div class="flex items-center gap-2 text-cyan-800 font-bold mb-2">
         <i class="fa-solid fa-list"></i> <span>Sobre nós</span>
      </div>

      <div class="flex  gap-6   w-full">
         <div class="flex flex-col items-stretch justify-between">
            <article class="lg:col-span-2 p-[1px] rounded-3xl bg-gradient-to-br from-white/40 via-white/10 to-white/0">
               <div class="h-full rounded-3xl bg-white/70 backdrop-blur-xl ring-1 ring-white/50 shadow-lg p-6">
                  <h2 class="text-3xl font-extrabold text-purple-default leading-tight">
                     Quem somos nós?
                  </h2>
                  <p class="text-gray-700 mt-4">
                     Somos um estúdio especializado em <strong>impressão 3D</strong>, pintura e
                     acabamento premium. Modelamos, prototipamos e produzimos em pequena escala
                     com controle de qualidade artesanal — do PLA ao resinado.
                  </p>

                  <h3 class="text-xl font-bold text-purple-default mt-6">O que podemos fazer?</h3>
                  <ul class="mt-3 space-y-2 text-gray-700">
                     <li class="flex items-start gap-2">
                        <i class="fa-solid fa-cubes mt-1 text-purple-default"></i>
                        <span>Figures, vasos e luminárias com pintura profissional.</span>
                     </li>
                     <li class="flex items-start gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles mt-1 text-purple-default"></i>
                        <span>Personalizações sob medida e protótipos funcionais.</span>
                     </li>
                     <li class="flex items-start gap-2">
                        <i class="fa-solid fa-truck-fast mt-1 text-purple-default"></i>
                        <span>Envio para todo o Brasil com embalagem segura.</span>
                     </li>
                  </ul>

                  <div class="mt-6 flex flex-wrap gap-2">
                     <span class="px-3 py-1 text-xs rounded-full bg-purple-50 text-purple-700 ring-1 ring-purple-200">PLA+ / ABS / PETG</span>
                     <span class="px-3 py-1 text-xs rounded-full bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200">Resina UV</span>
                     <span class="px-3 py-1 text-xs rounded-full bg-sky-50 text-sky-700 ring-1 ring-sky-200">Pintura automotiva</span>
                  </div>

                  <div class="mt-6 flex gap-3">
                     <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
                        class="inline-flex items-center rounded-2xl bg-indigo-600 text-white px-4 py-2.5 text-sm font-semibold hover:bg-indigo-700 shadow">
                        Ver produtos
                     </a>
                     <a href="<?php echo esc_url(home_url('/contato')); ?>"
                        class="inline-flex items-center rounded-2xl bg-white text-gray-900 px-4 py-2.5 text-sm font-semibold ring-1 ring-gray-200 hover:bg-gray-50">
                        Fale com a gente
                     </a>
                  </div>

               </div>

            </article>
            <article class="">
               <div class=" rounded-3xl bg-white/80 backdrop-blur-xl ring-1 ring-gray-200 shadow-lg p-0 overflow-hidden hover:shadow-2xl hover:-translate-y-0.5 transition-all">
                  <div class="flex items-center justify-between px-5 pt-5">
                     <h3 class="text-lg font-semibold text-gray-900">Preview 3D</h3>
                     <span class="text-xs px-2 py-1 rounded-full bg-purple-50 text-purple-700 ring-1 ring-purple-200">interativo</span>
                  </div>
                  <div id="obj-viewer" class="h-64 md:h-72 w-full"></div>
                  <div class="px-5 pb-5 text-sm text-gray-600">
                     Arraste para girar • Scroll para zoom
                  </div>
               </div>
            </article>
         </div>
         <!-- Card institucional -->


         <!-- Cards de categorias + Preview 3D -->
         <div class="flex flex-col gap-6 w-full ">
            <!-- Figures -->
            <article class="group p-[1px]  rounded-3xl  ">
               <div class="h-[250px] rounded-3xl bg-white/80   p-5">
                  <div class="flex items-start gap-4  h-full">
                     <img src="https://down-br.img.susercontent.com/file/br-11134201-22120-2o63skucwvkv29"
                        alt="Figures"
                        class="w-[250px] h-full object-contain rounded-full shadow mix-blend-multiply" />
                     <div>
                        <h3 class="text-lg font-semibold text-purple-default">Figures</h3>
                        <p class="text-sm text-gray-700">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                     </div>
                  </div>
                  <!-- <div class="mt-4 flex flex-wrap gap-2">
                     <span class="px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700">Escala 1:10/1:6</span>
                     <span class="px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700">Primer P.U.</span>
                  </div> -->
               </div>
            </article>
            <hr>
            <!-- Vasos -->
            <article class="group p-[1px] rounded-3xl">
               <div class="h-[250px] rounded-3xl bg-white/80   p-5">
                  <div class="flex items-start gap-4  h-full">
                     <img src="https://cdn.awsli.com.br/2500x2500/1650/1650789/produto/183551288/9a3ed69cda.jpg"
                        alt="Vasos"
                        class="w-[250px] h-full object-contain rounded-full shadow mix-blend-multiply order-2" />
                     <div>
                        <h3 class="text-lg font-semibold text-purple-default">Vasos</h3>
                        <p class="text-sm text-gray-700">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                     </div>
                  </div>
                  <!-- <div class="mt-4 flex flex-wrap gap-2">
                     <span class="px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700">Impermeabilização</span>
                     <span class="px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</span>
                  </div> -->
               </div>
            </article>
            <hr>
            <!-- Luminárias / Abajures -->
            <article class="group p-[1px] rounded-3xl ">
               <div class="h-[250px] rounded-3xl bg-white/80   p-5]">
                  <div class="flex items-start gap-4  h-full">
                     <img src="https://cdn.awsli.com.br/2500x2500/1650/1650789/produto/183551288/9a3ed69cda.jpg"
                        alt="Abajures"
                        class="w-[250px] h-full object-cover rounded-full shadow mix-blend-multiply" />
                     <div>
                        <h3 class="text-lg font-semibold text-purple-default">Light Box</h3>
                        <p class="text-sm text-gray-700">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                     </div>
                  </div>
                  <!-- <div class="mt-4 flex flex-wrap gap-2">
                     <span class="px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700">LED bivolt</span>
                     <span class="px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700">Acab. acetinado</span>
                  </div> -->
               </div>
            </article>

            <!-- Preview 3D (seu OBJ) -->
            <!-- <article class=" group p-[1px] rounded-3xl bg-gradient-to-br from-fuchsia-200/60 via-white to-fuchsia-100/30
             hover:from-fuchsia-300/80 transition-all">
               <div class="h-full rounded-3xl bg-white/80 backdrop-blur-xl ring-1 ring-gray-200 shadow-lg p-0 overflow-hidden hover:shadow-2xl hover:-translate-y-0.5 transition-all">
                  <div class="flex items-center justify-between px-5 pt-5">
                     <h3 class="text-lg font-semibold text-gray-900">Preview 3D</h3>
                     <span class="text-xs px-2 py-1 rounded-full bg-purple-50 text-purple-700 ring-1 ring-purple-200">interativo</span>
                  </div>
                  <div id="obj-viewer" class="h-64 md:h-72 w-full"></div>
                  <div class="px-5 pb-5 text-sm text-gray-600">
                     Arraste para girar • Scroll para zoom
                  </div>
               </div>
            </article> -->
         </div>
      </div>
   </div>

</section>
<!-- ===================== /SOBRE / SERVIÇOS (CARDS) ===================== -->



<!-- LISTA DE PRODUTOS -->
<!-- <section class="bg-gray-50">
   <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
      <h2 class="text-xl font-semibold text-gray-900 mb-6">Produtos</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
         <?php
         $q = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
         ]);

         // Corrige os erros de sintaxe relacionados ao uso de `endwhile` e `endif`
         if ($q->have_posts()) :
            while ($q->have_posts()) : $q->the_post();
               $product = wc_get_product(get_the_ID());
               if (! $product) {
                  continue;
               }

               $permalink  = $product->get_permalink();
               $title      = $product->get_name();
               $price_html = $product->get_price_html();
               $img        = $product->get_image('woocommerce_thumbnail', ['class' => 'w-full h-48 object-cover rounded-xl']);

               // Botões
               $supports_ajax = $product->is_purchasable() && $product->is_in_stock() && $product->supports('ajax_add_to_cart');
               $add_url       = $product->add_to_cart_url();
               $buy_now       = add_query_arg('add-to-cart', $product->get_id(), wc_get_checkout_url());

               // Se for variável / agrupado / externo, empurramos para detalhes
               $is_simple_like = $product->is_type('simple');
         ?>
               <article class="rounded-2xl bg-white ring-1 ring-gray-200 shadow-sm p-4 flex flex-col">
                  <a href="<?php echo esc_url($permalink); ?>" class="block">
                     <div class="relative">
                        <?php if ($product->is_on_sale()) : ?>
                           <span class="absolute left-2 top-2 z-10 rounded-full bg-rose-600 text-white text-xs px-2 py-1">Promoção</span>
                        <?php endif; ?>
                        <?php echo $img; // phpcs:ignore 
                        ?>
                     </div>
                     <h3 class="mt-3 text-base font-medium text-gray-900 line-clamp-2"><?php echo esc_html($title); ?></h3>
                  </a>

                  <div class="mt-2 text-gray-900 font-semibold"><?php echo wp_kses_post($price_html); ?></div>

                  <div class="mt-4 grid grid-cols-2 gap-2">
                     <?php if ($is_simple_like && $product->is_purchasable() && $product->is_in_stock()) : ?>
                        <a href="<?php echo esc_url($add_url); ?>"
                           data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                           data-quantity="1"
                           class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2 text-sm hover:bg-gray-100 transition <?php echo $supports_ajax ? 'ajax_add_to_cart add_to_cart_button' : ''; ?>">
                           Adicionar
                        </a>
                        <a href="<?php echo esc_url($buy_now); ?>"
                           class="inline-flex items-center justify-center rounded-xl bg-indigo-600 text-white px-3 py-2 text-sm hover:bg-indigo-700 transition">
                           Comprar agora
                        </a>
                     <?php else : ?>
                        <a href="<?php echo esc_url($permalink); ?>"
                           class="col-span-2 inline-flex items-center justify-center rounded-xl bg-gray-900 text-white px-3 py-2 text-sm hover:bg-black transition">
                           Ver detalhes
                        </a>
                     <?php endif; ?>
                  </div>
               </article>
         <?php
            endwhile;
         endif;
         ?>
      </div>
   </div>
</section> -->






<script type="importmap">
   {
  "imports": {
    "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
    "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
  }
}
</script>
<script type="module">
   import * as THREE from 'three';
   import {
      OrbitControls
   } from 'three/addons/controls/OrbitControls.js';
   import {
      OBJLoader
   } from 'three/addons/loaders/OBJLoader.js';

   // --- CAMINHOS (ajuste se mudar a pasta) ---
   const BASE = "<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/3d2/'); ?>";
   const OBJ_URL = BASE + "base.obj";
   const MTL_URL = ""; // sem .mtl -> deixe vazio!

   const wrap = document.getElementById('obj-viewer');

   // Cena
   const scene = new THREE.Scene();
   scene.background = null;

   // Câmera
   const camera = new THREE.PerspectiveCamera(45, wrap.clientWidth / wrap.clientHeight, 0.1, 2000);
   camera.position.set(2, 1.5, 3);

   // Renderer
   const renderer = new THREE.WebGLRenderer({
      antialias: true,
      alpha: true
   });
   renderer.setClearColor(0x000000, 0);
   renderer.setClearAlpha(0);
   renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
   renderer.setSize(wrap.clientWidth, wrap.clientHeight);
   renderer.outputColorSpace = THREE.SRGBColorSpace;
   wrap.appendChild(renderer.domElement);

   // Luz
   scene.add(new THREE.AmbientLight(0xffffff, 0.6));
   const dir = new THREE.DirectionalLight(0xffffff, 0.9);
   dir.position.set(3, 5, 2);
   scene.add(dir);

   // Controles
   const controls = new OrbitControls(camera, renderer.domElement);
   controls.enableDamping = true;

   function fitCameraToObject(object) {
      const box = new THREE.Box3().setFromObject(object);
      const size = new THREE.Vector3();
      const center = new THREE.Vector3();
      box.getSize(size);
      box.getCenter(center);
      object.position.sub(center);
      controls.target.set(0, 0, 0);

      const maxDim = Math.max(size.x, size.y, size.z);
      const fov = camera.fov * (Math.PI / 180);
      let cameraZ = (maxDim / 2) / Math.tan(fov / 2) * 1.3;
      camera.position.set(0, maxDim * 0.3, cameraZ);
      camera.near = Math.max(0.1, maxDim / 1000);
      camera.far = Math.max(2000, maxDim * 1000);
      camera.updateProjectionMatrix();
      controls.update();
   }

   // === Texturas (sem .mtl) ===
   const tex = new THREE.TextureLoader();
   const mapColor = tex.load(BASE + "texture_diffuse.png"); // cor/basecolor/albedo
   const mapMetallic = tex.load(BASE + "texture_metallic.png"); // metalness
   const mapRoughness = tex.load(BASE + "texture_roughness.png"); // roughness
   const mapNormal = tex.load(BASE + "texture_normal.png"); // normal

   // Ajustes recomendados
   mapColor.colorSpace = THREE.SRGBColorSpace;
   // Se as normais parecerem “invertidas”, experimente: mapNormal.flipY = false;

   const materialPBR = new THREE.MeshStandardMaterial({
      map: mapColor,
      metalnessMap: mapMetallic,
      roughnessMap: mapRoughness,
      normalMap: mapNormal,
      metalness: 1.0, // usados caso o map não exista
      roughness: 1.0
   });

   // Carregar OBJ
   const objLoader = new OBJLoader();

   objLoader.load(
      OBJ_URL,
      (obj) => {
         obj.traverse((c) => {
            if (c.isMesh) {
               c.material = materialPBR; // aplica material com as texturas
               c.castShadow = true;
               c.receiveShadow = true;
            }
         });
         scene.add(obj);
         fitCameraToObject(obj);
      },
      (xhr) => {
         // progresso opcional: console.log(`OBJ ${(xhr.loaded/xhr.total*100).toFixed(0)}%`);
      },
      (err) => {
         console.error("Falha ao carregar OBJ:", err);
         const msg = document.createElement('div');
         msg.textContent = 'Não foi possível carregar o modelo 3D.';
         msg.className = 'text-red-600 mt-2';
         wrap.after(msg);
      }
   );

   // Resize
   window.addEventListener('resize', () => {
      const w = wrap.clientWidth,
         h = wrap.clientHeight;
      renderer.setSize(w, h);
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
   });

   // Loop
   (function animate() {
      requestAnimationFrame(animate);
      controls.update();
      renderer.render(scene, camera);
   })();
</script>
<style>
   #obj-viewer canvas {
      background: transparent !important
   }
</style>



<?php get_footer();
