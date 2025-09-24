<?php

/**
 * Footer
 * Estilo “glass + dark” com faixa roxa superior.
 */
?>
<section class="flex flex-col items-center justify-center w-full bg-[#0b0b0f]">
   <div class="h-1 w-full bg-gradient-to-r from-purple-600 via-fuchsia-500 to-cyan-400"></div>
   <footer class="bg-[#0b0b0f] text-gray-300 w-full margin-site ">
      <!-- Faixa superior -->


      <!-- Conteúdo principal -->
      <div class="margin-site py-12 grid grid-cols-1 md:grid-cols-12 gap-10">

         <!-- Coluna: logo + contato -->
         <div class="md:col-span-4">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block">
               <img
                  class="h-[200px] w-[200px] object-fit"
                  src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-branca.png"
                  alt="<?php bloginfo('name'); ?>">
            </a>

            <div class="mt-6 space-y-3 text-sm">
               <a href="mailto:contato@rossettostudio.com.br" class="flex items-center gap-3 hover:text-white transition">
                  <i class="fa-regular fa-envelope text-white/60"></i>
                  <span>contato@rossettostudio.com.br</span>
               </a>
               <a href="tel:+550000000000" class="flex items-center gap-3 hover:text-white transition">
                  <i class="fa-solid fa-phone text-white/60"></i>
                  <span>(00) 0000-0000</span>
               </a>
               <div class="flex items-start gap-3">
                  <i class="fa-solid fa-location-dot text-white/60 mt-0.5"></i>
                  <span>25 Sandwich Street, Plymouth, 02560</span>
               </div>
            </div>
         </div>

         <!-- Coluna 2 -->
         <div class="md:col-span-3">
            <h4 class="text-white font-semibold tracking-wide mb-4">Produtos</h4>
            <ul class="space-y-2 text-sm">
               <li><a class="hover:text-white transition" href="#">Lançamentos</a></li>
               <li><a class="hover:text-white transition" href="#">Figures</a></li>
               <li><a class="hover:text-white transition" href="#">Vasos</a></li>
               <li><a class="hover:text-white transition" href="#">Abajures</a></li>
               <li><a class="hover:text-white transition" href="#">Kits & Combos</a></li>
            </ul>
         </div>

         <!-- Coluna 3 -->
         <div class="md:col-span-3">
            <h4 class="text-white font-semibold tracking-wide mb-4">Serviços</h4>
            <ul class="space-y-2 text-sm">
               <li><a class="hover:text-white transition" href="#">Impressão sob medida</a></li>
               <li><a class="hover:text-white transition" href="#">Pintura personalizada</a></li>
               <li><a class="hover:text-white transition" href="#">Reposição de peças</a></li>
               <li><a class="hover:text-white transition" href="#">Envio & prazos</a></li>
               <li><a class="hover:text-white transition" href="#">Garantia</a></li>
            </ul>
         </div>

         <!-- Coluna 4 -->
         <div class="md:col-span-2">
            <h4 class="text-white font-semibold tracking-wide mb-4">Empresa</h4>
            <ul class="space-y-2 text-sm">
               <li><a class="hover:text-white transition" href="#">Sobre nós</a></li>
               <li><a class="hover:text-white transition" href="#">Contato</a></li>
               <li><a class="hover:text-white transition" href="#">Afiliados</a></li>
               <li><a class="hover:text-white transition" href="#">Recursos</a></li>
               <li><a class="hover:text-white transition" href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">Minha conta</a></li>
            </ul>
         </div>
      </div>

      <!-- Barra inferior -->
      <div class="border-t border-white/10">
         <div class="margin-site py-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Rede sociais -->
            <div class="flex items-center gap-3">
               <a href="#" class="h-9 w-9 grid place-items-center rounded-full ring-1 ring-white/15 hover:ring-white/30 hover:bg-white/5 transition">
                  <i class="fa-brands fa-instagram text-white/80"></i>
               </a>
               <a href="#" class="h-9 w-9 grid place-items-center rounded-full ring-1 ring-white/15 hover:ring-white/30 hover:bg-white/5 transition">
                  <i class="fa-brands fa-facebook-f text-white/80"></i>
               </a>
               <a href="#" class="h-9 w-9 grid place-items-center rounded-full ring-1 ring-white/15 hover:ring-white/30 hover:bg-white/5 transition">
                  <i class="fa-brands fa-x-twitter text-white/80"></i>
               </a>
               <a href="#" class="h-9 w-9 grid place-items-center rounded-full ring-1 ring-white/15 hover:ring-white/30 hover:bg-white/5 transition">
                  <i class="fa-brands fa-youtube text-white/80"></i>
               </a>
            </div>

            <!-- Copyright -->
            <p class="text-xs text-gray-400">
               © <?php echo date('Y'); ?> Rossetto Studio. All rights reserved.
            </p>
         </div>
      </div>


   </footer>
</section>


<script>
   (function() {
      // Toast minimalista
      function showToast(msg) {
         const wrap = document.createElement('div');
         wrap.style.position = 'fixed';
         wrap.style.right = '16px';
         wrap.style.top = '16px';
         wrap.style.zIndex = '9999';
         wrap.style.padding = '12px 14px';
         wrap.style.borderRadius = '12px';
         wrap.style.background = 'rgba(106,13,173,0.95)'; // roxo
         wrap.style.color = '#fff';
         wrap.style.fontWeight = '600';
         wrap.style.boxShadow = '0 12px 30px rgba(0,0,0,.18)';
         wrap.textContent = msg || 'Produto adicionado ao carrinho!';
         document.body.appendChild(wrap);
         setTimeout(() => {
            wrap.style.transition = 'opacity .25s ease';
            wrap.style.opacity = '0';
            setTimeout(() => wrap.remove(), 250);
         }, 1500);
      }

      // WooCommerce: evento disparado após add-to-cart AJAX
      // Requer a classe 'ajax_add_to_cart' no botão (você já coloca quando suporta AJAX)
      jQuery(document.body).on('added_to_cart', function(e, fragments, cart_hash, $button) {
         // showToast('Produto adicionado ao carrinho!');
      });




      // (Opcional) feedback quando não for AJAX e houver redirect (não dispara o evento acima)
   })();
</script>
<?php wp_footer(); ?>
</body>

</html>